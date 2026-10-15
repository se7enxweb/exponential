<?php
/**
 * Templates that disable their submit buttons against a second click must do it after the submit event, never in it.
 *
 * A submit button disabled while the submit event is still running is left out of the posted data (the browser builds
 * the form data after the event), so the module never sees its action: user/register without PublishButton shows the
 * empty form again, shop/userregister without StoreButton, shop/confirmorder without ConfirmOrderButton. The safe ways
 * are the upstream onclick="window.setTimeout( disableButtons, 1 )" and a submit listener that calls
 * window.setTimeout( <fn>, 0 ). DB-less: reads the templates of the kernel designs and of every extension on disk.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class FormSubmitButtonDisableTest extends PHPUnit\Framework\TestCase
{
    private static function root(): string
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * @return string[] the templates under design/ and extension/<name>/design/
     */
    private static function templates(): array
    {
        $root = self::root();
        $dirs = array_merge( array( $root . '/design' ), glob( $root . '/extension/*/design', GLOB_ONLYDIR ) ?: array() );
        $files = array();
        foreach ( $dirs as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
                if ( $file->isFile() && substr( $file->getFilename(), -4 ) === '.tpl' )
                    $files[] = $file->getPathname();
        }
        sort( $files );
        return $files;
    }

    /**
     * The functions a template defines that set a submit button's disabled to true.
     *
     * @return string[]
     */
    private static function disablingFunctions( string $source ): array
    {
        $names = array();
        if ( preg_match_all( '/function\s+([A-Za-z_$][\w$]*)\s*\([^)]*\)\s*\{(.*?)\n\s*\}/s', $source, $m, PREG_SET_ORDER ) )
            foreach ( $m as $fn )
                if ( preg_match( '/\.disabled\s*=\s*(true|!0)/', $fn[2] ) && preg_match( '/Button/', $fn[2] ) )
                    $names[] = $fn[1];
        return $names;
    }

    /**
     * The offences of one template's source: a disabling function named as a submit listener or called in onsubmit,
     * not through setTimeout.
     *
     * @return string[]
     */
    public static function offences( string $source ): array
    {
        $out = array();
        foreach ( self::disablingFunctions( $source ) as $name )
        {
            $q = preg_quote( $name, '/' );
            if ( preg_match( '/addEventListener\(\s*[\'"]submit[\'"]\s*,\s*' . $q . '\s*[,)]/', $source ) )
                $out[] = "$name is the submit listener itself";
            if ( preg_match_all( '/onsubmit\s*=\s*"([^"]*)"/i', $source, $s ) )
                foreach ( $s[1] as $handler )
                    if ( preg_match( '/(?<![\w$])' . $q . '\s*\(/', $handler ) && stripos( $handler, 'setTimeout' ) === false )
                        $out[] = "$name is called in onsubmit";
            if ( preg_match_all( '/addEventListener\(\s*[\'"]submit[\'"]\s*,\s*function\s*\([^)]*\)\s*\{(.*?)\}\s*\)/s', $source, $l ) )
                foreach ( $l[1] as $body )
                    if ( preg_match( '/(?<![\w$])' . $q . '\s*\(/', $body ) )
                        $out[] = "$name is called inside a submit listener";
        }
        return $out;
    }

    public function testTheCheckSeesTheBrokenAndTheSafeForms(): void
    {
        $fn = "function disableButtons()\n{\n    document.getElementById( 'PublishButton' ).disabled = true;\n}\n";
        $this->assertNotEmpty( self::offences( $fn . "form.addEventListener( 'submit', disableButtons );" ) );
        $this->assertNotEmpty( self::offences( $fn . "form.addEventListener( 'submit', function() { disableButtons(); } );" ) );
        $this->assertNotEmpty( self::offences( $fn . '<form onsubmit="disableButtons();">' ) );
        $this->assertSame( array(), self::offences( $fn . "form.addEventListener( 'submit', function()\n{\n    window.setTimeout( disableButtons, 0 );\n} );" ) );
        $this->assertSame( array(), self::offences( $fn . '<input type="submit" onclick="window.setTimeout( disableButtons, 1 ); return true;" />' ) );
    }

    public function testNoTemplateDisablesItsSubmitButtonsInsideTheSubmitEvent(): void
    {
        $bad = array();
        $checked = 0;
        foreach ( self::templates() as $file )
        {
            $source = file_get_contents( $file );
            if ( strpos( $source, '.disabled' ) === false )
                continue;
            $checked++;
            foreach ( self::offences( $source ) as $offence )
                $bad[] = substr( $file, strlen( self::root() ) + 1 ) . ': ' . $offence;
        }
        $this->assertGreaterThan( 0, $checked, 'no template with a disabled submit button was found' );
        $this->assertSame( array(), $bad, "A submit button disabled during the submit event is not posted; defer it with window.setTimeout:\n" . implode( "\n", $bad ) );
    }

    public function testTheMediaRegistrationFormDefersDisablingItsButtons(): void
    {
        $file = self::root() . '/extension/sevenx_themes_media/design/media/templates/user/register.tpl';
        if ( !is_file( $file ) )
            $this->markTestSkipped( 'sevenx_themes_media is not installed' );
        $source = file_get_contents( $file );
        $this->assertSame( array(), self::offences( $source ) );
        $this->assertMatchesRegularExpression( '/setTimeout\(\s*disableButtons\s*,\s*0\s*\)/', $source );
    }
}
