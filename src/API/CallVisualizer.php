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

namespace Ikarus\SPS\Visualizer\API;


use Ikarus\SPS\Client\AbstractClient;
use Ikarus\SPS\Client\ClientInterface;
use Ikarus\SPS\Client\Command\Command;

class CallVisualizer
{
    /** @var ClientInterface */
    private $client;

    /**
     * CallVisualizer constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Fetches all values in a visual domain
     *
     * @param string $domain
     * @return array
     */
    public function fetchValues(string $domain) {
        return unserialize( $this->_send("fetch $domain") );
    }

    /**
     * Sends a command to the sps
     * Please note that this method returns true, if the command was posted successfully.
     * If the command really was performed you can not detect using this method!
     *
     * @param $cmd
     * @param string $toBrickID
     * @return bool
     */
    public function sendCommand($cmd, string $toBrickID): bool {
        return unserialize( $this->_send("putc $toBrickID " . serialize($cmd)) ) ? true : false;
    }

    /**
     * Puts a value into the sps
     *
     * @param $value
     * @param $key
     * @param $domain
     * @return bool
     */
    public function putValue($value, $key, $domain): bool {
        return unserialize( $this->_send("putc $domain $key " . serialize($value)) ) ? true : false;
    }


    private function _send($cmd) {
        $c = $this->getClient();
        if($c instanceof AbstractClient)
            return $c->sendCommandNamed($cmd);

        $cmd = new Command($cmd);
        if($c->sendCommand($cmd) != $c::STATUS_OK)
            return NULL;
        return  $cmd->getResponse() ;
    }
}