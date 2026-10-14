<?php
/**
 * Every form control of admin4's templates has an accessible name: a <label for> on its id, a <label> around it,
 * aria-label or aria-labelledby. A title or a placeholder is not a name. Hidden fields, buttons with a value and
 * controls hidden from assistive technology (aria-hidden="true") need none.
 * doc/bc/6.0/admin4-styling.md (review checklist).
 *
 * The check reads the template source, not the rendered page: a <label for> must name the id in the same file, and
 * a control inside an include is named where it is included only when listed in INCLUDED below.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminFormLabelsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminFormLabelsTest extends PHPUnit\Framework\TestCase
{
    /** Where admin4's templates are: the design's own and its overrides */
    const TEMPLATE_DIRS = array( 'design/admin4/templates', 'design/admin4/override/templates' );

    /**
     * Includes whose control takes its id from a parameter: every template that includes them puts a
     * <label for> on that id, checked in testIncludedControlsAreNamedWhereTheyAreIncluded().
     */
    const INCLUDED = array(
        'design/admin4/templates/setup/rad_licence_select.tpl' => array( 'design:setup/rad_licence_select.tpl', 'select_id' ),
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** Every template of admin4 and its overrides, path relative to the root => source */
    private static function templates()
    {
        $all = array();
        foreach ( self::TEMPLATE_DIRS as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.tpl' )
                    $all[substr( $file->getPathname(), strlen( self::root() ) + 1 )] = (string)file_get_contents( $file->getPathname() );
            }
        }
        ksort( $all );
        return $all;
    }

    /**
     * The form controls of a template source that have no accessible name, as "line: tag".
     * Comments ({* *} and <!-- -->) are blanked first, keeping the line numbers.
     */
    public static function unnamedControls( $source )
    {
        $blank = function ( $m ) { return preg_replace( '/[^\n]/', ' ', $m[0] ); };
        $code = preg_replace_callback( '/\{\*.*?\*\}/s', $blank, $source );
        $code = preg_replace_callback( '/<!--.*?-->/s', $blank, $code );
        preg_match_all( '/\bfor="([^"]*)"/', $code, $forMatches );
        $labelled = array_flip( $forMatches[1] );

        $unnamed = array();
        preg_match_all( '/<(input|select|textarea)\b([^>]*)>/i', $code, $controls, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
        foreach ( $controls as $control )
        {
            $tag = strtolower( $control[1][0] );
            $attributes = $control[2][0];
            $type = preg_match( '/\btype="([^"]*)"/i', $attributes, $t ) ? strtolower( $t[1] ) : 'text';
            if ( $tag === 'input' && $type === 'hidden' )
                continue;
            if ( $tag === 'input' && in_array( $type, array( 'submit', 'button', 'reset' ), true ) && preg_match( '/\bvalue="[^"]+"/', $attributes ) )
                continue;
            if ( $tag === 'input' && $type === 'image' && preg_match( '/\balt="[^"]+"/', $attributes ) )
                continue;
            if ( preg_match( '/\baria-label(ledby)?="[^"]+"/', $attributes ) || preg_match( '/\baria-hidden="true"/', $attributes ) )
                continue;
            if ( preg_match( '/\bid="([^"]*)"/', $attributes, $id ) && isset( $labelled[$id[1]] ) )
                continue;
            $before = strtolower( substr( $code, 0, $control[0][1] ) );
            if ( substr_count( $before, '<label' ) > substr_count( $before, '</label>' ) )
                continue;
            $unnamed[] = ( substr_count( $before, "\n" ) + 1 ) . ': ' . $tag . ( $tag === 'input' ? "[$type]" : '' );
        }
        return $unnamed;
    }

    public function testEveryFormControlOfAdmin4HasAName()
    {
        $problems = array();
        foreach ( self::templates() as $path => $source )
        {
            if ( isset( self::INCLUDED[$path] ) )
                continue;
            foreach ( self::unnamedControls( $source ) as $control )
                $problems[] = $path . ':' . $control;
        }
        $this->assertSame( array(), $problems,
            "Form controls without an accessible name. Give each a <label for> on its id, a <label> around it, or\n" .
            "aria-labelledby on visible text (a column header, the row's name); aria-label only where no text is shown.\n" .
            "A title or a placeholder is not a name. Around plain text use <label class=\"a4-inline-label\">." );
    }

    public function testIncludedControlsAreNamedWhereTheyAreIncluded()
    {
        $templates = self::templates();
        foreach ( self::INCLUDED as $path => $include )
        {
            list( $uri, $parameter ) = $include;
            $callers = 0;
            foreach ( $templates as $callerPath => $source )
            {
                if ( !preg_match_all( "/\\{include uri='" . preg_quote( $uri, '/' ) . "'[^}]*\\b" . $parameter . "='([^']+)'/", $source, $m ) )
                    continue;
                foreach ( $m[1] as $id )
                {
                    $callers++;
                    $this->assertStringContainsString( 'for="' . $id . '"', $source, "$callerPath includes $uri with $parameter=$id but has no <label for> on it" );
                }
            }
            $this->assertGreaterThan( 0, $callers, "$uri is included nowhere; remove it from INCLUDED" );
            $unnamed = self::unnamedControls( $templates[$path] );
            $this->assertCount( 1, $unnamed, "$path should hold only its parameter-named control" );
            $this->assertStringContainsString( 'id="{$' . $parameter . '|wash}"', $templates[$path] );
        }
    }

    /** The checker itself: what counts as a name and what does not */
    public function testTheCheckerTellsNamedFromUnnamedControls()
    {
        $this->assertSame( array(), self::unnamedControls(
            '<label for="a">A</label><input id="a" type="text" />' .
            '<label class="a4-inline-label"><input type="checkbox" /> B</label>' .
            '<select aria-labelledby="h"></select><textarea aria-label="C"></textarea>' .
            '<input type="hidden" name="x" /><input type="submit" value="OK" /><input type="submit" aria-hidden="true" value="" />' .
            '{* <input type="text" /> *}<!-- <select></select> -->'
        ) );
        $this->assertSame( array( '1: input[text]', '2: select', '3: textarea', '4: input[checkbox]' ), self::unnamedControls(
            "<label>Name</label><input type=\"text\" title=\"Name\" />\n<select name=\"s\"></select>\n" .
            "<textarea placeholder=\"Text\"></textarea>\n<label for=\"other\">X</label><input type=\"checkbox\" id=\"mine\" />"
        ) );
    }
}
