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

use phparia\Events\PlaybackFinished;
use phparia\Resources\Playback;

class PlaybackList extends \ArrayObject
{
    /**
     * @var Phparia
     */
    protected $phparia;

    /**
     * @var Playback[]
     */
    protected $playbacks = [];

    public function __construct(Phparia $phparia)
    {
        $this->phparia = $phparia;
        parent::__construct($this->playbacks, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param mixed $offset
     * @param Playback $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Playback) {
            throw new \InvalidArgumentException("Value must be of type Playback");
        }
        parent::offsetSet($offset, $value);

        // Remove playbacks when they are done playing
        $value->oncePlaybackFinished(function (PlaybackFinished $playbackFinished) {
            $key = array_search($playbackFinished->getPlayback(), $this->playbacks);
            if ($key !== false) {
                unset($this->playbacks[$key]);
            }
        });
    }

    /**
     * Stop all the playbacks
     */
    public function stop()
    {
        foreach (array_reverse($this->playbacks) as $playback) {
            try {
                $this->phparia->playbacks()->stopPlayback($playback);
            } catch (\Exception $ignore) {

            }
        }
    }
}