<?php
/*
 * Copyright 2015 Brian Smith <wormling@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace phparia\Client;


use Devristo\Phpws\Client\WebSocket;
use phparia\Events\Event;
use React\EventLoop\LoopInterface;
use Zend\Log\LoggerInterface;

class Phparia
{
    /**
     * @var WebSocket
     */
    protected $wsClient;

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AriClient
     */
    protected $ariClient;

    /**
     * @var AmiClient
     */
    protected $amiClient;

    /**
     * @var string
     */
    protected $stasisApplicationName;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Connect to ARI and optionally AMI
     *
     * @param string $ariAddress
     * @param string|null $amiAddress
     */
    public function connect($ariAddress, $amiAddress = null)
    {
        $this->eventLoop = \React\EventLoop\Factory::create();
        $this->ariClient = new AriClient($this->eventLoop, $this->logger);
        $this->ariClient->connect($ariAddress);
        $this->wsClient = $this->ariClient->getWsClient();
        $this->stasisApplicationName = $this->ariClient->getStasisApplicationName();

        if ($amiAddress !== null) {
            $this->amiClient = new AmiClient($this->ariClient->getWsClient(), $this->eventLoop, $this->logger);
            $this->amiClient
                ->connect($amiAddress)
                ->done();
        }
    }

    /**
     * Connect and start the event loop
     */
    public function run()
    {
        $this->wsClient->open();
        $this->eventLoop->run();
    }

    /**
     * @param callable|callable $callback
     */
    public function onStasisStart(callable $callback)
    {
        $this->wsClient->on(Event::STASIS_START, $callback);
    }

    /**
     * @param callable|callable $callback
     */
    public function onStasisEnd(callable $callback)
    {
        $this->wsClient->on(Event::STASIS_END, $callback);
    }

    /**
     * @return WebSocket
     */
    public function getWsClient()
    {
        return $this->wsClient;
    }

    /**
     * @return LoopInterface
     */
    public function getEventLoop()
    {
        return $this->eventLoop;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return AriClient
     */
    public function getAriClient()
    {
        return $this->ariClient;
    }

    /**
     * @return AmiClient
     */
    public function getAmiClient()
    {
        return $this->amiClient;
    }

    /**
     * @return string
     */
    public function getStasisApplicationName()
    {
        return $this->stasisApplicationName;
    }

    /**
     * @return Applications
     */
    public function applications()
    {
        return $this->ariClient->applications();
    }

    /**
     * @return Asterisk
     */
    public function asterisk()
    {
        return $this->ariClient->asterisk();
    }

    /**
     * @return Bridges
     */
    public function bridges()
    {
        return $this->ariClient->bridges();
    }

    /**
     * @return Channels
     */
    public function channels()
    {
        return $this->ariClient->channels();
    }

    /**
     * @return DeviceStates
     */
    public function deviceStates()
    {
        return $this->ariClient->deviceStates();
    }

    /**
     * @return Endpoints
     */
    public function endPoints()
    {
        return $this->ariClient->endPoints();
    }

    /**
     * @return Events
     */
    public function events()
    {
        return $this->ariClient->events();
    }

    /**
     * @return Mailboxes
     */
    public function mailboxes()
    {
        return $this->ariClient->mailboxes();
    }

    /**
     * @return Playbacks
     */
    public function playbacks()
    {
        return $this->ariClient->playbacks();
    }

    /**
     * @return Recordings
     */
    public function recordings()
    {
        return $this->ariClient->recordings();
    }

    /**
     * @return Sounds
     */
    public function sounds()
    {
        return $this->ariClient->sounds();
    }

}