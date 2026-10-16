<?php
/**
 * The filter redirects of package/list, package/compare and package/view build their address with
 * eZURI::transformURI() and send it in the Location header. transformURI() escapes for HTML unless told not to,
 * and a header is not HTML: an & in the address would arrive as &amp;. Each such call must pass
 * $htmlEscape = false.
 *
 * The views end with eZExecution::cleanExit(), so the test reads every transformURI() call that is followed by
 * a redirect out of the view's source and runs it, with its own arguments, on an address with several query
 * parameters.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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
    public function testTheRedirectAddressIsNotEscapedForHTML( $file )
    {
        $source = file_get_contents( $file );
        preg_match_all( '#\\\\?eZURI::transformURI\(\s*\$target\s*,([^;]*)\);\s*\n\s*\\\\?eZHTTPTool::redirect\(\s*\$target#', $source, $calls );
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
