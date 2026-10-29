<?php
/**
 * The filter forms of package/list, package/compare and package/view are sent with POST and answered with a
 * 303 to the canonical /(name)/value address, through expViewParameters::redirect(); their options are view
 * parameters only, never GET query parameters. That redirect builds its address with eZURI::transformURI() and
 * sends it in the Location header. transformURI() escapes for HTML unless told not to, and a header is not HTML:
 * an & in the address would arrive as &amp;. The call must pass $htmlEscape = false.
 *
 * The redirect ends with eZExecution::cleanExit(), so the test reads the transformURI() call that is followed by
 * the redirect out of the source and runs it, with its own arguments, on an address with several query
 * parameters.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class PackageViewRedirectTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    public static function views()
    {
        return array(
            'list' => array( 'kernel/private/classes/views/package/list.php' ),
            'compare' => array( 'kernel/private/classes/views/package/compare.php' ),
            'view' => array( 'kernel/private/classes/views/package/view.php' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'views' )]
    public function testTheFilterFormIsAPostAnsweredWithA303AndNoGetIsRead( $file )
    {
        $source = file_get_contents( $file );
        $this->assertStringContainsString( '\\expViewParameters::redirectsPost( $_POST', $source, $file );
        $this->assertStringContainsString( '\\expViewParameters::redirect(', $source, $file );
        foreach ( array( '$_GET', 'hasGetVariable', 'getVariable', '302 Found' ) as $needle )
            $this->assertStringNotContainsString( $needle, $source, "$file: $needle" );
    }

    public function testTheModuleDeclaresTheViewParameters()
    {
        $Module = null;
        $ViewList = array();
        $FunctionList = array();
        include 'kernel/package/module.php';
        foreach ( array( 'offset', 'search', 'type', 'state', 'sort', 'dir' ) as $name )
            $this->assertArrayHasKey( $name, $ViewList['list']['unordered_params'], "list: $name" );
        foreach ( array( 'filter', 'class', 'search', 'sort', 'dir', 'limit', 'offset', 'item' ) as $name )
            $this->assertArrayHasKey( $name, $ViewList['compare']['unordered_params'], "compare: $name" );
        foreach ( array( 'type', 'search', 'limit', 'offset', 'file', 'as' ) as $name )
            $this->assertArrayHasKey( $name, $ViewList['view']['unordered_params'], "view: $name" );
    }

    public function testTheFilterFormsArePosted()
    {
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            foreach ( array( 'package/list.tpl' => 'PackageFilter', 'package/compare.tpl' => 'CompareApply', 'package/view/full.tpl' => 'BrowseApply' ) as $template => $field )
            {
                $source = file_get_contents( "$design/templates/$template" );
                $this->assertStringContainsString( 'name="' . $field . '"', $source, "$design/$template" );
                $this->assertStringNotContainsString( 'method="get"', $source, "$design/$template" );
            }
        }
    }

    public function testTheRedirectAddressIsNotEscapedForHTML()
    {
        $file = 'kernel/classes/expviewparameters.php';
        $source = file_get_contents( $file );
        preg_match_all( '#\\\\?eZURI::transformURI\(\s*\$target\s*,([^;]*)\);\s*\n\s*\\\\?eZHTTPTool::redirect\(\s*\$target\s*,\s*array\(\)\s*,\s*\'303 See Other\'#', $source, $calls );
        $this->assertCount( 1, $calls[1], "$file: one transformURI() call before the redirect" );
        foreach ( $calls[1] as $arguments )
        {
            // The arguments are literals; read them, not run them
            $values = array();
            foreach ( explode( ',', $arguments ) as $argument )
            {
                $argument = trim( $argument );
                if ( $argument === 'true' || $argument === 'false' )
                    $values[] = $argument === 'true';
                else if ( preg_match( "#^'([a-z]*)'$#", $argument, $match ) )
                    $values[] = $match[1];
                else
                    $this->fail( "$file: an argument that is not a literal: $argument" );
            }
            $target = '/package/list?a=1&b=2&c=3';
            eZURI::transformURI( $target, ...$values );
            $this->assertStringNotContainsString( '&amp;', $target, "$file: transformURI( \$target,$arguments )" );
            $this->assertStringEndsWith( '/package/list?a=1&b=2&c=3', $target, $file );
        }
    }
}
