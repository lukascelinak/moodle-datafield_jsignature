<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The Hearth Rating externallib api functions.
 *
 * @package     mod_hearthrating
 * @author      Lukas Celinak <lukascelinak@gmail.com>
 * @copyright   2022 Lukas Celinak, Edumood <lukascelinak@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class mod_hearthrating_external extends external_api{
    /**
     * Returns description of add_rating parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function add_rating_parameters() {
        return new external_function_parameters (
            array(
                'itemid'        => new external_value(PARAM_INT, 'associated id'),
                'promptitemid'  => new external_value(PARAM_INT, 'associated id'),
                'rating'        => new external_value(PARAM_INT, 'user rating')
            )
        );
    }

    /**
     * @param $value
     * @param $itemid
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function add_rating($itemid, $promptitemid, $rating) {
        global $DB,$USER;
        $params = array(
            'itemid'   => $itemid,
            'promptitemid'   => $promptitemid,
            'rating' => $rating
        );

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::add_rating_parameters(), $params);

        if($hearthrating=$DB->get_record('hearthrating_ratings',['itemid'=>$itemid,'promptitemid'=>$promptitemid,'userid'=>$USER->id])){
            $hearthrating->rating=$rating;
            $hearthrating->timemodified=time();
            $DB->update_record('hearthrating_ratings',$hearthrating);
        }else{
            $params['userid']=$USER->id;
            $params['timemodified']=time();
            $DB->insert_record('hearthrating_ratings',$params);
        }

        $returndata = array(
            'itemid'   => $itemid,
            'promptitemid'   => $promptitemid,
            'rating' => $rating
        );

        return $returndata;
    }

    /**
     * Returns description of add_rating result values.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function add_rating_returns() {
        return new external_warnings();
    }

    /**
     * Returns description of add_rating parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function add_comment_parameters() {
        return new external_function_parameters (
            array(
                'itemid'        => new external_value(PARAM_INT, 'associated id'),
                'submission'        => new external_value(PARAM_TEXT, 'user comment')
            )
        );
    }

    /**
     * @param $comment
     * @param $promptitemid
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function add_comment($itemid, $submission) {
        global $USER,$DB;
        $params = array(
            'submission' => $submission,
            'itemid' => $itemid
        );

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::add_comment_parameters(), $params);

        if($hearthrating=$DB->get_record('hearthrating_comments',['itemid'=>$itemid,'userid'=>$USER->id])){
            $hearthrating->submission=$submission;
            $hearthrating->timemodified=time();
            $DB->update_record('hearthrating_comments',$hearthrating);
        }else{
            $params['userid']=$USER->id;
            $params['timemodified']=time();
            $DB->insert_record('hearthrating_comments',$params);
        }

        $returndata = array(
            'submission' => $submission,
            'itemid' => $itemid
        );

        return $returndata;
    }

    /**
     * Returns description of add_rating result values.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function add_comment_returns() {
        return new external_warnings();
    }
}
