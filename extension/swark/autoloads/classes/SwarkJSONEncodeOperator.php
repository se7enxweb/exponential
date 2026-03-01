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

class SwarkJSONEncodeOperator extends SwarkOperator
{
    function __construct()
    {
        parent::__construct( 'json_encode', 'value' );
    }

    static function execute( $operatorValue, $namedParameters )
    {
        if ( $operatorValue === NULL && $namedParameters['value'] !== NULL )
        {
            $operatorValue = $namedParameters['value'];
        }

        if ( function_exists( 'json_encode' ) )
        {
            return json_encode( $operatorValue );
        }
        else
        {
            return SwarkJSONEncodeOperator::encode( $operatorValue );
        }
    }

    private static function encodeString( $string )
    {
        if ( !isset( $GLOBALS['json_codec'] ) || !$GLOBALS['json_codec'] )
        {
            $httpCharset = eZTextCodec::httpCharset();
            $GLOBALS['json_codec'] = eZTextCodec::instance( $httpCharset, 'unicode' );
        }

        $map = array(
            0x08 => '\b',
            0x09 => '\t',
            0x0a => '\n',
            0x0c => '\f',
            0x0d => '\r',
            0x22 => '\042',
            0x27 => '\047',
            // 0x2f => '',
            0x5c => "\\\\",
        );

        $jsonString = '';
        $stringArray = $GLOBALS['json_codec']->convertString( $string );
        foreach ( $stringArray as $character )
        {
            if ( isset( $map[$character] ) )
            {
                $jsonString .= $map[$character];
            }
            else if ( $character < 128 )
            {
                $jsonString .= chr( $character );
            }
            else
            {
                $jsonString .= '\u' . str_pad( dechex( $character ), 4, '0000', STR_PAD_LEFT );
            }
        }

        return $jsonString;
    }

    private static function encode( $variable )
    {
        switch( gettype( $variable ) )
        {
            case 'boolean':
            {
                return $variable? 'true': 'false';
            } break;

            case 'integer':
            {
                return sprintf( '%d', $variable );
            } break;

            case 'double':
            case 'float':
            {
                return sprintf( '%f', $variable );
            } break;

            case 'string':
            {
                return '"' . SwarkJSONEncodeOperator::encodeString( $variable ) . '"';
            } break;

            case 'array':
            case 'object':
            {
                if ( is_object( $variable ) )
                {
                    $variable = get_object_vars( $variable );
                }

                $result = array();
                $isDict = array_keys( $variable ) !== range( 0, count( $variable ) - 1 );

                foreach( $variable as $key => $value )
                {
                    $value = SwarkJSONEncodeOperator::encode( $value );
                    if ( $isDict )
                    {
                        $result[] = '"' . SwarkJSONEncodeOperator::encodeString( $key ) . '":' . $value;
                    }
                    else
                    {
                        $result[] = $value;
                    }
                }

                if ( $isDict )
                {
                    return '{' . implode( $result, ',' ) . '}';
                }
                else
                {
                    return '[' . implode( $result, ',' ) . ']';
                }
            } break;

            case 'NULL':
            {
                return 'null';
            } break;
        }

        return false;            
    }
}

?>
