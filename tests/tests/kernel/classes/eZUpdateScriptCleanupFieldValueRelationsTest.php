<?php
/**
 * update/common/scripts/5.4/cleanupfieldvaluerelations.php with an object relation list attribute whose data_text
 * is NULL or '' (a class attribute added to a class that already has objects leaves data_text NULL). PHP 8's
 * DOMDocument::loadXML() throws a ValueError for an empty string, which ended the upgrade script. The case of
 * LoveStack (mugoweb/ezpublish-legacy) commit 1bd250d4c6.
 *
 * The script runs as soon as it is included, so the test takes processRelationListRow() out of its source and
 * defines it under a name of its own; the function returns before it touches the database for empty data.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group php8
 */

class eZUpdateScriptCleanupFieldValueRelationsTest extends PHPUnit\Framework\TestCase
{
    private static function defineFunction()
    {
        $name = 'eZUpdateScriptCleanupFieldValueRelationsTest_processRelationListRow';
        if ( function_exists( $name ) )
            return $name;
        $file = dirname( __DIR__, 4 ) . '/update/common/scripts/5.4/cleanupfieldvaluerelations.php';
        $tokens = token_get_all( file_get_contents( $file ) );
        $source = '';
        $depth = 0;
        $inside = false;
        foreach ( $tokens as $i => $token )
        {
            $text = is_array( $token ) ? $token[1] : $token;
            if ( !$inside && is_array( $token ) && $token[0] === T_FUNCTION )
            {
                $j = $i + 1;
                while ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                    ++$j;
                if ( is_array( $tokens[$j] ) && $tokens[$j][1] === 'processRelationListRow' )
                    $inside = true;
            }
            if ( !$inside )
                continue;
            $source .= $text;
            if ( $text === '{' || ( is_array( $token ) && in_array( $token[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) )
                ++$depth;
            else if ( $text === '}' && --$depth === 0 )
                break;
        }
        if ( $source === '' )
            self::fail( 'processRelationListRow() not found in ' . $file );
        eval( preg_replace( '/function\s+processRelationListRow\b/', 'function ' . $name, $source, 1 ) );
        return $name;
    }

    public function testEmptyDataTextIsSkipped()
    {
        $function = self::defineFunction();
        foreach ( array( null, '' ) as $dataText )
        {
            $removed = $function( array( 'id' => 1, 'version' => 1, 'contentobject_id' => 1, 'data_text' => $dataText ) );
            $this->assertSame( array(), $removed, var_export( $dataText, true ) );
        }
    }
}
