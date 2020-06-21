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

namespace Ikarus\SPS\Visualizer\Brick;


class VerticalPowerSwitchBrick extends AbstractBrick
{
    protected function renderPath()
    {
        echo "<circle stroke-width=\"4\" cx=\"34.694\" cy=\"33.42\" r=\"45.248\" transform=\"matrix(0.999998, -0.002227, 0.002227, 0.999998, 15.231671, 16.657346)\"></circle>
            <path stroke='black' fill=\"none\" stroke-width=\"2\" d=\"M 47.924 77.283 C 64.476 76.729 78.322 65.342 78.322 49.042 C 78.322 40.134 74.189 32.182 67.741 26.979 C 65.017 24.796 60.971 26.778 60.971 30.269 C 60.971 30.269 60.971 30.269 60.971 30.269 C 60.971 31.553 61.575 32.748 62.576 33.553 C 67.086 37.215 69.974 42.801 69.974 49.042 C 69.974 60.806 59.751 70.23 47.716 68.884 C 38.5 67.859 31.101 60.385 30.151 51.149 C 29.421 44.059 32.422 37.642 37.417 33.566 C 38.424 32.76 39.041 31.565 39.041 30.269 C 39.041 30.269 39.041 30.269 39.041 30.269 C 39.041 26.74 34.926 24.821 32.177 27.061 C 25.131 32.786 20.878 41.801 21.803 51.753 C 23.074 65.418 34.234 76.32 47.924 77.283 Z M 54.164 18.618 C 54.164 18.618 54.164 37.844 54.164 37.844 C 54.164 40.146 52.29 42.021 49.993 42.021 C 47.691 42.021 45.816 40.159 45.816 37.844 C 45.816 37.844 45.816 18.618 45.816 18.618 C 45.816 16.322 47.678 14.447 49.993 14.447 C 52.302 14.447 54.164 16.322 54.164 18.618 Z\"></path>
            <line stroke-width=\"4\" x1=\"50\" y1=\"6\" x2=\"50\" y2=\"0\" ></line>
            <line stroke-width=\"4\" x1=\"50\" y1=\"100\" x2=\"50\" y2=\"94\"></line>";
    }
}