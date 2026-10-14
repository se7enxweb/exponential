<?php
/**
 * Every form control of admin4's templates has an accessible name: a <label for> on its id, a <label> around it,
 * aria-label or aria-labelledby. A title or a placeholder is not a name. Hidden fields, buttons with a value and
 * controls hidden from assistive technology (aria-hidden="true") need none. Labels point at ids that exist, an
 * aria-label does not replace a label that has text, and includes that take their id as a parameter get one.
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
     * Includes whose control takes its id from a parameter: every admin4 template that includes them passes the
     * parameter and puts a <label for> on that id, checked in testIncludedControlsAreNamedWhereTheyAreIncluded().
     * Template => array( include uri, parameter ).
     */
    const INCLUDED = array(
        'design/admin4/templates/setup/rad_licence_select.tpl' => array( 'design:setup/rad_licence_select.tpl', 'select_id' ),
        'design/standard/templates/shop/country/edit.tpl' => array( 'design:shop/country/edit.tpl', 'select_id' ),
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
    /** The source with {* *} and <!-- --> comments blanked, line numbers kept */
    private static function code( $source )
    {
        $blank = function ( $m ) { return preg_replace( '/[^\n]/', ' ', $m[0] ); };
        return preg_replace_callback( '/<!--.*?-->/s', $blank, preg_replace_callback( '/\{\*.*?\*\}/s', $blank, $source ) );
    }

    /** The value of attribute $name in $tag, braces of template code respected; null without it */
    private static function attribute( $tag, $name )
    {
        if ( !preg_match( '/\s' . $name . '="/', $tag, $m, PREG_OFFSET_CAPTURE ) )
            return null;
        $start = $m[0][1] + strlen( $m[0][0] );
        for ( $i = $start, $depth = 0, $n = strlen( $tag ); $i < $n; $i++ )
        {
            if ( $tag[$i] === '{' )
                $depth++;
            else if ( $tag[$i] === '}' )
                $depth--;
            else if ( $tag[$i] === '"' && $depth === 0 )
                return substr( $tag, $start, $i - $start );
        }
        return null;
    }

    public static function unnamedControls( $source )
    {
        $code = self::code( $source );
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
                if ( !preg_match_all( "/\\{include uri='" . preg_quote( $uri, '/' ) . "'[^}]*\\}/", self::code( $source ), $includes ) )
                    continue;
                foreach ( $includes[0] as $call )
                {
                    $callers++;
                    $this->assertMatchesRegularExpression( "/\\b" . $parameter . "='[^']+'/", $call, "$callerPath includes $uri without $parameter, so nothing can name its control" );
                    preg_match( "/\\b" . $parameter . "='([^']+)'/", $call, $id );
                    $this->assertStringContainsString( 'for="' . $id[1] . '"', $source, "$callerPath includes $uri with $parameter={$id[1]} but has no <label for> on it" );
                }
            }
            $this->assertGreaterThan( 0, $callers, "$uri is included nowhere in admin4; remove it from INCLUDED" );
            $control = (string)file_get_contents( self::root() . '/' . $path );
            $this->assertCount( 1, self::unnamedControls( $control ), "$path should hold only its parameter-named control" );
            $this->assertMatchesRegularExpression( '/\\bid="\\{\\$' . $parameter . '\\|wash\\}"/', $control, "$path should take its id from $parameter" );
        }
    }

    /**
     * A <label for> and aria-labelledby point at an id of the same template, or at the id an include of it is given
     * (select_id='...'). Ids built from template code ({...}) are left out: they exist only once rendered.
     */
    public function testLabelsPointAtAnIdOfTheSameTemplate()
    {
        $problems = array();
        foreach ( self::templates() as $path => $source )
        {
            $code = self::code( $source );
            preg_match_all( '/\\bid="([^"{]+)"/', $code, $ids );
            preg_match_all( "/\\b(?:[a-z_]*_id|id_name)=['\"]?([A-Za-z][\\w-]*)/", $code, $passed );
            $known = array_flip( array_merge( $ids[1], $passed[1] ) );
            preg_match_all( '/(?<![\\w-])for="([^"]+)"/', $code, $fors );
            preg_match_all( '/(?<![\\w-])aria-labelledby="([^"]+)"/', $code, $labelledby );
            $refs = $fors[1];
            foreach ( $labelledby[1] as $list )
                $refs = array_merge( $refs, preg_split( '/\\s+/', trim( $list ) ) );
            foreach ( $refs as $ref )
            {
                if ( strpos( $ref, '{' ) === false && strpos( $ref, '}' ) === false && !isset( $known[$ref] ) )
                    $problems[] = "$path: $ref";
            }
        }
        $this->assertSame( array(), $problems, 'A label for or aria-labelledby points at no id of its template' );
    }

    /** The words of a label: its content without tags, :, whitespace and |wash, for comparing two names */
    private static function words( $text )
    {
        // tags, and template code that prints nothing ({if}, {else}, {/if}, {def} ...); output ({'...'}, {$...}) stays
        $text = preg_replace( '/\\{\\/?[a-z][^{}]*\\}/', '', preg_replace( '/<[^>]*>/', '', (string)$text ) );
        return trim( str_replace( array( '|wash', ':' ), '', preg_replace( '/\\s+/', '', $text ) ) );
    }

    /**
     * A control with a label that has text (<label for> or a label around it) takes its name from it: an aria-label
     * with other words would replace the label (WCAG 2.5.3 Label in Name). A label around the control without text
     * of its own (a styled tick box) names nothing, so the aria-label is the name there.
     */
    public function testAVisibleLabelIsNotOverriddenByAnAriaLabel()
    {
        $problems = array();
        foreach ( self::templates() as $path => $source )
        {
            $code = self::code( $source );
            preg_match_all( '/\\bfor="([^"]*)"/', $code, $fors );
            $labelled = array_flip( $fors[1] );
            preg_match_all( '/<(input|select|textarea)\\b/i', $code, $starts, PREG_OFFSET_CAPTURE );
            foreach ( $starts[0] as $start )
            {
                $end = $start[1];
                for ( $depth = 0, $n = strlen( $code ); $end < $n; $end++ )
                {
                    if ( $code[$end] === '{' ) $depth++;
                    else if ( $code[$end] === '}' ) $depth--;
                    else if ( $code[$end] === '>' && $depth === 0 ) break;
                }
                $tag = substr( $code, $start[1], $end - $start[1] + 1 );
                if ( self::attribute( $tag, 'aria-label' ) === null )
                    continue;
                $id = self::attribute( $tag, 'id' );
                $before = strtolower( substr( $code, 0, $start[1] ) );
                $label = null;
                if ( $id !== null && isset( $labelled[$id] ) && preg_match( '/<label\\b[^>]*\\bfor="' . preg_quote( $id, '/' ) . '"[^>]*>(.*?)<\\/label>/s', $code, $m ) )
                    $label = $m[1];
                else if ( substr_count( $before, '<label' ) > substr_count( $before, '</label>' ) )
                {
                    $open = strrpos( $before, '<label' );
                    $close = stripos( $code, '</label>', $end );
                    $inside = substr( $code, $open, $close - $open );
                    $label = preg_replace( '/^<label[^>]*>/', '', str_replace( $tag, '', $inside ) );
                }
                if ( $label === null || self::words( $label ) === '' )
                    continue;
                if ( self::words( $label ) !== self::words( self::attribute( $tag, 'aria-label' ) ) )
                    $problems[] = $path . ':' . ( substr_count( $before, "\n" ) + 1 );
            }
        }
        $this->assertSame( array(), $problems, 'A control with a visible label also carries an aria-label, which replaces the label' );
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
