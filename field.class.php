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

class data_field_jsignature extends data_field_base {

    var $type = 'jsignature';

    /**
     * This field just sets up a default field object
     *
     * @return bool
     */
    function define_default_field() {
        parent::define_default_field();
        $this->field->param1 = '#000080';
        $this->field->param2 = '#FFFFFF';
        return true;
    }
    /**
     * Print the relevant form element in the ADD template for this field
     *
     * @global object
     * @param int $recordid
     * @return string
     */
    public function display_add_field($recordid = 0, $formdata = null)
    {
        global $PAGE;
        $PAGE->requires->js_call_amd('datafield_jsignature/jsignature', 'init', array(
            'field_' . $this->field->id,
            $this->field->param1,
            $this->field->param2,
        ));
        $str = parent::display_add_field($recordid, $formdata);
        return $str;
    }

    /**
     * Generates the image URL of a signature
     */
    function get_image_url($data) {
        global $CFG;
        return $CFG->wwwroot . '/mod/data/field/jsignature/img.php?data=' . $data;
    }

    /**
     * Returns a SVG image created from the a jSignature base30 encoded data.
     *
     * @param $data jSignature base30 data
     * @return string
     */
    public static function get_svg_image($data) {
        require_once(dirname(__FILE__) . '/classes/jSignature_Tools_Base30.php');
        require_once(dirname(__FILE__) . '/classes/jSignature_Tools_SVG.php');
        $signatureParser = new jSignature_Tools_Base30();
        $svgGenerator = new jSignature_Tools_SVG();
        $svg = $svgGenerator->NativeToSVG($signatureParser->Base64ToNative($data));
        return $svg;
    }

    public static function get_raster_image($data, $format = 'png8') {
        $svg = self::get_svg_image($data);
        $img = new Imagick();
        $img->readImageBlob($svg);
        $img->setImageFormat($format);
        $img->paintTransparentImage("rgb(255,255,255)", 0, 0);
        $img->trimImage(0);
        return $img->getImageBlob();
    }

    /**
     * Display the content of the field in browse mode
     *
     * @global object
     * @param int $recordid
     * @param object $template
     * @return bool|string
     */
    function display_browse_field($recordid, $template) {
        global $DB;

        $content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
        if (empty($content->content)) {
            return false;
        }
        $str = $this->get_image_url($content->content);
        return "<img class=\"jsignaturefield_img\" src=\"$str\">";
    }

    function display_search_field($value = '') {
        return '<label class="accesshide" for="f_' . $this->field->id . '">'. $this->field->name.'</label>' .
            '<select id="f_'.$this->field->id.'" name="f_'.$this->field->id.'">' .
                '<option></option>' .
                '<option value="0" '.($value===0 ? "selected":"").'>'. get_string('no', 'core') . '</option>' .
                '<option value="1" '.($value===1 ? "selected":"").'>'. get_string('yes', 'core') . '</option>' .
            '</select>';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_BOOL);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $sql = "{$tablealias}.fieldid = {$this->field->id}";
        if ($value === 0) {
            $sql .= " AND {$tablealias}.content=''";
        }
        if ($value === 1) {
            $sql .= " AND {$tablealias}.content<>''";
        }
        return array(" ($sql) ", array());
    }

    /**
     * Per default, return the record's text value only from the "content" field.
     * Override this in fields class if necesarry.
     *
     * @param string $record
     * @return string
     */
    function export_text_value($record) {
        if ($this->text_export_supported()) {
            return $this->get_image_url($record->content);
        }
    }
}


