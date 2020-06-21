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

namespace Ikarus\SPS\Visualizer\Plugin;


use Ikarus\SPS\Visualizer\Plugin\Cyclic\VisualizerPlugin;

trait ControllablePluginTrait
{
    private $status = 0;
    private $mstatus = 0;
    private $manual = false;

    private $lastStatus = 0, $mchan = 0;

    protected $ignoreManualChanges = false;

    public function getStatus(): int
    {
        return $this->isManual() ? $this->mstatus : $this->status;
    }

    public function setStatus(int $status)
    {
        $this->setLastStatus( $this->getStatus() );
        $this->status = $status;
    }

    public function setManualStatus(int $status)
    {
        $this->setLastStatus( $this->getStatus() );

        $this->mstatus = $status;
        $this->manual = true;
        if($this->ignoreManualChanges)
            $this->mchan = 1;
    }

    public function resignManualStatus()
    {
        $this->manual = false;
        $this->mchan = 0;
    }

    /**
     * @return bool
     */
    public function isManual(): bool
    {
        return $this->manual;
    }

    public function hasFlag(int $flag): bool {
        return $this->getStatus() & $flag ? true : false;
    }

    public function hadFlag(int $flag): bool {
        return $this->lastStatus & $flag ? true : false;
    }

    public function requiredFlags(): int {
        $flags = 0;
        if($this->mchan > 1) // Once the changes needs to be applied
            return 0;
        elseif($this->mchan == 1)
            $this->mchan = 2;

        for($e=1;$e<=$this->getStatus();$e<<=1) {
            if($this->getStatus() & $e && ($this->lastStatus & $e) != $e)
                $flags |= $e;
        }
        return $flags;
    }

    /**
     * @return int
     */
    public function getLastStatus(): int
    {
        return $this->lastStatus;
    }

    /**
     * @param int $lastStatus
     */
    public function setLastStatus(int $lastStatus): void
    {
        $this->lastStatus = $lastStatus;
    }

    public function applyStatus(callable $on_status_callback, callable $off_status_callback) {
        $req = $this->requiredFlags();

        if($req & VisualizerPlugin::STATUS_OFF) {
            $off_status_callback($req);
        } elseif( $req & VisualizerPlugin::STATUS_ON ) {
            $on_status_callback($req);
        }
    }
}