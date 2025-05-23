<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines metadata for the Message My Teacher block
 *
 * @package    block_messageteacher
 * @author     Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright  2010-2012 Tauntons College, UK. 2012 onwards Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version = 2025041400;
$plugin->requires = 2025041400; // Moodle 5.0.
$plugin->component = 'block_messageteacher';
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '2.7';
$plugin->supported = [500, 500];
