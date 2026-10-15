<?php
/**
 * Every template of the administration designs (admin, admin3, admin4, admin4l) closes the <div> frames it opens.
 * A template that closes one too many ends the page's frame early: the rest of the page, the footer with it, lands
 * outside #page, below the page's background, on white (the trash, the URL aliases, About and Copyright did).
 *
 * Template comments are left out, and of an {if} only the first branch counts: the branches of a condition draw the
 * same frame in other words. The package wizard's header and navigator and the e-mail preferences' page start and end
 * are the two halves of one frame, opened in one template and closed in the other: each pair adds up to nothing.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTemplateFramesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminTemplateFramesTest extends PHPUnit\Framework\TestCase
{
    const DESIGNS = array( 'admin', 'admin3', 'admin4', 'admin4l' );

    /** Templates that open a frame another template closes: [opening, closing] */
    const HALVES = array(
        array( 'package/header.tpl', 'package/navigator.tpl' ),
        array( 'package/install_header.tpl', 'package/navigator.tpl' ),
        array( 'mailpreferences/parts/page_start.tpl', 'mailpreferences/parts/page_end.tpl' ),
    );

    /**
     * @return int the <div> frames a template leaves open (negative: closes more than it opens), counting the first
     *             branch of every {if} only
     */
    public static function depth( $source )
    {
        $source = preg_replace( '/\{\*.*?\*\}/s', '', $source );
        preg_match_all( '#<div[\s>]|</div>|\{if[\s}]|\{elseif[\s}]|\{else\}|\{/if\}#', $source, $m );
        $depth = 0;
        // per open {if}: whether the branch being read is skipped
        $skip = array();
        foreach ( $m[0] as $token )
        {
            $skipping = in_array( true, $skip, true );
            if ( strpos( $token, '{if' ) === 0 )
                $skip[] = false;
            elseif ( strpos( $token, '{elseif' ) === 0 || $token === '{else}' )
            {
                if ( $skip )
                    $skip[count( $skip ) - 1] = true;
            }
            elseif ( $token === '{/if}' )
                array_pop( $skip );
            elseif ( !$skipping )
                $depth += $token === '</div>' ? -1 : 1;
        }
        return $depth;
    }

    /** @return array<string, string> relative template path => source, per design */
    private static function templates( $design )
    {
        $root = dirname( __DIR__, 3 ) . "/design/$design/templates/";
        $files = array();
        if ( !is_dir( $root ) )
            return $files;
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
            if ( substr( $file->getFilename(), -4 ) === '.tpl' )
                $files[substr( $file->getPathname(), strlen( $root ) )] = (string)file_get_contents( $file->getPathname() );
        ksort( $files );
        return $files;
    }

    public function testTheCounterReadsTheFirstBranchAndSkipsComments()
    {
        $this->assertSame( 0, self::depth( '<div>{if $a}<div class="x">{else}<div class="y">{/if}</div></div>' ) );
        $this->assertSame( -1, self::depth( "<div>\n</div>\n</div>" ) );
        $this->assertSame( 0, self::depth( "{* <div> *}<div\n  class=\"a\">{if \$a}{if \$b}x{/if}{elseif \$c}<div>{/if}</div>" ) );
    }

    public function testEveryAdminTemplateClosesTheFramesItOpens()
    {
        $halves = array();
        foreach ( self::HALVES as $pair )
            $halves = array_merge( $halves, $pair );
        $checked = 0;
        foreach ( self::DESIGNS as $design )
        {
            $templates = self::templates( $design );
            foreach ( $templates as $path => $source )
            {
                if ( strpos( $source, 'div' ) === false || in_array( $path, $halves, true ) )
                    continue;
                $this->assertSame( 0, self::depth( $source ), "design/$design/templates/$path leaves frames open (positive) or closes too many (negative)" );
                $checked++;
            }
            foreach ( self::HALVES as list( $open, $close ) )
            {
                if ( isset( $templates[$open] ) && isset( $templates[$close] ) )
                    $this->assertSame( 0, self::depth( $templates[$open] ) + self::depth( $templates[$close] ), "design/$design: $open and $close are one frame" );
            }
        }
        $this->assertGreaterThan( 400, $checked );
    }
}
