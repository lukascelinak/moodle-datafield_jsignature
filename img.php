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
 * @package    datafield
 * @subpackage jSignature
 * @copyright  2021 onwards Andrei Bautu (abautu) {@link https://www.linkedin.com/in/andreibautu/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/classes/jSignature_Tools_Base30.php');
require_once(dirname(__FILE__) . '/classes/jSignature_Tools_SVG.php');

$data = required_param('data', PARAM_ALPHANUMEXT);
error_reporting(E_ERROR);
header('Content-type: image/svg+xml');
header('Cache-Control:public, max-age=31536000');
echo data_field_jsignature::get_svg_image($data);