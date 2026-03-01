<?php
//
// Swark - extension for eZ Publish
// Author: Jan Kudlicka <jk@seeds.no>
// Copyright (C) 2008 Seeds Consulting AS, http://www.seeds.no/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of version 2.0 of the GNU General
// Public License as published by the Free Software Foundation.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
// MA 02110-1301, USA.
//

class SwarkOperators
{
    static function operators()
    {
        $operators = array(
            'add_view_parameters' => 'SwarkAddViewParametersOperator',
            'array_search' => 'SwarkArraySearchOperator',
            'arsort' => 'SwarkARSortOperator',
            'asort' => 'SwarkASortOperator',
            'charset' => 'SwarkCharsetOperator',
            'clear_object_cache' => 'SwarkClearObjectCacheOperator',
            'cookie' => 'SwarkCookieOperator',
            'current_layout' => 'SwarkCurrentLayoutOperator',
            'current_siteaccess' => 'SwarkCurrentSiteaccessOperator',
            'debug_attributes' => 'SwarkDebugAttributesOperator',
            'debug' => 'SwarkDebugOperator',
            'is_post_request' => 'SwarkIsPostRequestOperator',
            'json_encode' => 'SwarkJSONEncodeOperator',
            'krsort' => 'SwarkKRSortOperator',
            'ksort' => 'SwarkKSortOperator',
            'ltrim' => 'SwarkLTrimOperator',
            'modify_view_parameter' => 'SwarkModifyViewParameterOperator',
            'preg_match' => 'SwarkPregMatchOperator',
            'preg_replace' => 'SwarkPregReplaceOperator',
            'range' => 'SwarkRangeOperator',
            'redirect' => 'SwarkRedirectOperator',
            'remove_array_element' => 'SwarkRemoveArrayElementOperator',
            'return' => 'SwarkReturnOperator',
            'rsort' => 'SwarkRSortOperator',
            'rtrim' => 'SwarkRTrimOperator',
            'server' => 'SwarkServerOperator',
            'set_array_element' => 'SwarkSetArrayElementOperator',
            'shortenw' => 'SwarkShortenWOperator',
            'shuffle' => 'SwarkShuffleOperator',
            'sort' => 'SwarkSortOperator',
            'split_by_length' => 'SwarkSplitByLengthOperator',
            'strpos' => 'SwarkStrPosOperator',
            'str_replace' => 'SwarkStrReplaceOperator',
            'strrpos' => 'SwarkStrRPosOperator',
            'substr' => 'SwarkSubStrOperator',
            'uri_path_segment' => 'SwarkURIPathSegmentOperator',
            'user_id_by_login' => 'SwarkUserIDByLoginOperator',
            'variable_names' => 'SwarkVariableNamesOperator',
        );

        /*
            Please note, that there are also the following operators defined in the INI settings:
                - serialize
                - unserialize
        */

        $eZTemplateOperatorArray = array();
        foreach ( $operators as $operatorName => $operatorClass )
        {
            $eZTemplateOperatorArray[$operatorName] = array( 'class' => $operatorClass,
                                                             'operator_names' => array( $operatorName ) );
        }

        return $eZTemplateOperatorArray;
    }
}

?>