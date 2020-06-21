<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Ikarus\SPS\Visualizer\Plugin\Cyclic;

use Ikarus\SPS\Alert\AlertInterface;
use Ikarus\SPS\Alert\AlertRecoveryInterface;
use Ikarus\SPS\Alert\NoticeAlert;
use Ikarus\SPS\Alert\WarningAlert;
use Ikarus\SPS\EngineInterface;
use Ikarus\SPS\Plugin\Alert\AlertPluginInterface;
use Ikarus\SPS\Plugin\EngineDependentPluginInterface;
use Ikarus\SPS\Plugin\Management\CyclicPluginManagementInterface;
use Ikarus\SPS\Plugin\Management\PluginManagementInterface;
use Ikarus\SPS\Plugin\PluginInterface;
use Ikarus\SPS\Visualizer\Plugin\ControllablePluginInterface;
use Ikarus\SPS\Server\Cyclic\ServerPlugin;
use Ikarus\SPS\Visualizer\Plugin\VisualizerPluginInterface;

/**
 * Use the visualizer plugin to communicate over API tcp/ip calls with the SPS.
 * @package Ikarus\SPS\Visualizer\Plugin\Cyclic
 */
class VisualizerPlugin extends ServerPlugin implements VisualizerPluginInterface, EngineDependentPluginInterface, AlertPluginInterface
{
    /** @var EngineInterface */
    private $engine;
    /** @var AlertInterface[] */
    private $alerts = [];

    public $formats = [
        'date' => 'd.m.Y',
        'time' => 'G:i'
    ];

    public function setEngine(?EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function handleAlert(AlertInterface $alert)
    {
        if(!($alert instanceof NoticeAlert)) {
            $this->alerts[ uniqid() ] = $alert;
        }
        return false;
    }

    /**
     * VisualizerPlugin constructor.
     * @param string $address
     * @param int|NULL $port
     * @param string|null $identifier
     */
    public function __construct(string $address, int $port = NULL, string $identifier = NULL)
    {
        parent::__construct($address, $port, $identifier, '');
    }

    protected function doCommand($command, PluginManagementInterface $management): string
    {
        if($management instanceof CyclicPluginManagementInterface) {
            if(preg_match("/^fetch\s+(.+)\s*$/i", $command, $ms)) {
                $domain = $ms[1];
                $data = $management->fetchValue($domain);

                if($this->alerts) {
                    $alerts = $this->alerts;
                    uasort($alerts, function(AlertInterface $a, AlertInterface $b) {
                        return $a->getTimeStamp() <=> $b->getTimeStamp();
                    });


                    $data["alerts"] = [];
                    foreach($alerts as $uid => $alert) {
                        array_unshift($data["alerts"], [
                            'uid' => $uid,
                            'level' => (function($alert) {
                                if($alert instanceof WarningAlert)
                                    return 1;
                                return 2;
                            })($alert),
                            'code' => $alert->getCode(),
                            'message' => $alert->getMessage(),
                            'brick' => $alert->getAffectedPlugin() instanceof PluginInterface ? $alert->getAffectedPlugin()->getIdentifier() : $alert->getAffectedPlugin(),
                            'date' => isset($this->formats['date']) ? date( $this->formats['date'] ) : time(),
                            'time' => isset($this->formats['time']) ? date( $this->formats['time'] ) : NULL,
                        ]);
                    }
                }

                return serialize( $data );
            }

            if(preg_match("/^putv\s+(\S+)\s+(\S+)\s+(.+)$/i", $command, $ms)) {
                $management->putValue( unserialize( $ms[3]) , $ms[2], $ms[1]);
                return serialize(true);
            }

            if(preg_match("/^putc\s+(\S+)\s+(.+)$/i", $command, $ms)) {
                $management->putCommand( $ms[1], unserialize( $ms[2]));
                return serialize(true);
            }

            if(preg_match("/^quit\s+(\S+)$/i", $command, $ms)) {
                $alert = $this->alerts[ $ms[1] ] ?? NULL;
                if($alert) {
                    if($alert instanceof AlertRecoveryInterface)
                        $alert->resume();

                    unset( $this->alerts[ $ms[1] ] );

                    return serialize(true);
                }
                return serialize(false);
            }

            if(preg_match("/^ctl\s+(\S+)\s+(\S+)\s*$/i", $command, $ms)) {
                if($this->engine) {
                    /** @var PluginInterface $plugin */
                    foreach($this->engine->getPlugins() as $plugin) {
                        if($plugin->getIdentifier() == $ms[1]) {
                            if($plugin instanceof ControllablePluginInterface) {
                                if($this->applyControlCommand($ms[2], $plugin))
                                    return serialize(true);
                                return serialize(false);
                            }
                            trigger_error("Plugin " . $plugin->getIdentifier() . " is not controllable", E_USER_WARNING);
                            break;
                        }
                    }
                }
                return serialize(false);
            }
        }
        return serialize(false);
    }

    protected function applyControlCommand($cmd, ControllablePluginInterface $plugin): bool {
        switch (strtolower($cmd)) {
            case 'on':
                $plugin->setManualStatus( $plugin::STATUS_ON | $plugin::STATUS_MANUAL );
                break;
            case 'off':
                $plugin->setManualStatus( $plugin::STATUS_OFF | $plugin::STATUS_MANUAL );
                break;
            case 'auto':
                $plugin->resignManualStatus();
                break;
            default:
                trigger_error("Can not assign $cmd to plugin " . $plugin->getIdentifier(), E_USER_WARNING);
                return false;
        }
        return true;
    }
}