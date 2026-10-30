<?php
/**
 * The shared well-known password check (kernel/classes/expweakpasswordlist.php) and where it is used: the not_common
 * rule of expPasswordPolicy (kernel/classes/exppasswordpolicy.php: user/password, registration, the user edit form,
 * exp:resetuserpassword, the web services) and the installers (kernel/setup/steps/ezstep_site_admin.php: the setup
 * wizard and the kickstarter; exp:install through it).
 *
 * DB-less: the settings are a site.ini of the test's own (expWeakPasswordTestINI), never the installation's. Variants
 * of a word are built at run time from the base word and a rule (an ending, digits, a swap), so the test names no
 * password beyond the base words of the list itself.
 *
 *  WPL-01  Every built-in base word is refused, in any case and with digits or symbols at the end
 *  WPL-02  The variant rules: endings, separators, letter swaps, a word typed twice, digits and symbols at the start
 *  WPL-03  Keyboard runs, alphabet and digit runs, repeated characters and short repeated patterns
 *  WPL-04  Strong and ordinary passphrases pass, as do the passwords the installers generate
 *  WPL-05  WeakPasswordList[] and WeakPasswordFile add words (with their variants); an unreadable file adds nothing
 *  WPL-06  WeakPasswordCheck: enabled when not set, "disabled" switches refuses() off; isWeak() always checks
 *  WPL-07  expPasswordPolicy: not_common is a rule by default, refuses a weak password with its own sentence for both
 *          forms, and is gone with WeakPasswordCheck=disabled
 *  WPL-08  The installers: the wizard refuses a well-known password that is long enough; the kickstarter's
 *          password problem names empty, well-known and too short
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expWeakPasswordTestINI.php';

class expWeakPasswordListTest extends PHPUnit\Framework\TestCase
{
    private static function make( array $userSettings = array(), array $passwordSettings = array() )
    {
        return new expWeakPasswordList( new expWeakPasswordTestINI( array( 'UserSettings' => $userSettings, 'PasswordSettings' => $passwordSettings ) ) );
    }

    /** The base words of the list that are words (not digits) */
    private static function letterWords()
    {
        return array_values( array_filter( expWeakPasswordList::builtInWords(), function ( $w ) { return preg_match( '/^[a-z]+$/', $w ); } ) );
    }

    // ------------------------------------------------------------------ WPL-01

    public function testEveryBuiltInWordIsRefusedInAnyCaseAndWithDigitsAtTheEnd()
    {
        $list = self::make();
        foreach ( expWeakPasswordList::builtInWords() as $w )
        {
            $this->assertTrue( $list->isWeak( $w ), $w );
            $this->assertTrue( $list->isWeak( strtoupper( $w ) ), "upper case of $w" );
            $this->assertTrue( $list->isWeak( ' ' . $w . ' ' ), "spaces around $w" );
        }
        foreach ( self::letterWords() as $w )
        {
            $this->assertTrue( $list->isWeak( ucfirst( $w ) . '2026' ), "$w with a year" );
            $this->assertTrue( $list->isWeak( $w . '!' ), "$w with !" );
            $this->assertTrue( $list->isWeak( ucfirst( $w ) . '123!?' ), "$w with digits and symbols" );
        }
        $this->assertTrue( $list->isWeak( '' ) );
        $this->assertTrue( $list->isWeak( '   ' ) );
        $this->assertTrue( $list->isWeak( null ) );
        $this->assertTrue( $list->isWeak( array( 'x' ) ), 'not a string' );
    }

    // ------------------------------------------------------------------ WPL-02

    public static function variantProvider()
    {
        // the variant rules, applied at run time to base words of the list
        $out = array();
        foreach ( array( 'publish', 'admin', 'password', 'exponential', 'secret', 'welcome', 'demo' ) as $w )
        {
            foreach ( expWeakPasswordList::SUFFIXES as $suffix )
                $out["$w + $suffix"] = array( $w . $suffix );
            $out["$w + ing, capital, digits, symbol"] = array( ucfirst( $w . 'ing' ) . '2026$' );
            $out["$w + er, upper case"] = array( strtoupper( $w . 'er' ) );
            $out["$w twice"] = array( $w . $w );
            $out["$w with digits before"] = array( '2026' . $w );
            $out["$w swapped"] = array( strtr( $w, array( 'a' => '@', 'o' => '0', 'e' => '3', 's' => '$' ) ) . '1' );
        }
        $out['change-me with a space'] = array( 'Change Me' );
        $out['change-me with underscore'] = array( 'change_me!' );
        $out['letmein with digits'] = array( 'letmein123' );
        $out['a season and a year'] = array( 'Summer2026!' );
        return $out;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('variantProvider')]
    public function testVariantsOfAWordAreRefused( $password )
    {
        $this->assertTrue( self::make()->isWeak( $password ) );
    }

    // ------------------------------------------------------------------ WPL-03

    public static function patternProvider()
    {
        return array( array( 'qwertyuiop' ), array( 'QWERTZUIOP' ), array( 'azertyuiop' ), array( 'asdfghjkl' ), array( 'zxcvbnm123' ),
                      array( 'abcdefghij' ), array( 'jihgfedcba' ), array( '1234567890' ), array( '0987654321' ), array( '1111111111' ),
                      array( 'aaaaaaaaaaaa' ), array( 'ababababab' ), array( 'abcabcabcabc' ), array( '!!!!!!!!!!' ), array( 'qwertyuiop2026!' ),
                      array( '1qaz2wsx3edc' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('patternProvider')]
    public function testRunsAndRepeatsAreRefused( $password )
    {
        $this->assertTrue( self::make()->isWeak( $password ) );
    }

    // ------------------------------------------------------------------ WPL-04

    public static function strongProvider()
    {
        return array( array( 'k1e-Strong-Passphrase-9' ), array( 'Tr0ub4dor&3xyz' ), array( 'correct-horse-battery-staple' ),
                      array( 'ExpTest#12345' ), array( 'Reset#Pass456' ), array( 'Brand#New123' ), array( 'a-completely-new-one' ),
                      array( 'has-a-digit-7' ), array( 'no-digits-here' ), array( 'Kx9#mPq2vL7w' ), array( 'äÖ7€-unicode-Wort' ),
                      array( "\xff\xfe-not-utf8-0042" ), array( '193847265' ), array( '7302948516' ),
                      array( 'P@ss-Correct-Horse-Battery' ), array( 'admin-of-the-blue-forest-42' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('strongProvider')]
    public function testOrdinaryPasswordsPass( $password )
    {
        $this->assertFalse( self::make()->isWeak( $password ) );
    }

    public function testGeneratedPasswordsPass()
    {
        $list = self::make();
        $alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ( $i = 0; $i < 200; $i++ )
        {
            $p = '';
            for ( $j = 0; $j < 20; $j++ )
                $p .= $alphabet[random_int( 0, strlen( $alphabet ) - 1 )];
            $this->assertFalse( $list->isWeak( $p ), 'the kickstarter\'s generated passwords' );
            $this->assertFalse( $list->isWeak( \Exponential\Command\Kernel\Install::generatePassword() ), 'exp:install\'s' );
        }
    }

    // ------------------------------------------------------------------ WPL-05

    public function testListAndFileAddWords()
    {
        $extra = 'zorblatt';
        $this->assertFalse( self::make()->isWeak( $extra . 'ing42' ) );
        $list = self::make( array( 'WeakPasswordList' => array( ' Zorblatt ', '', array( 'ignored' ) ) ) );
        $this->assertTrue( $list->isWeak( $extra ) );
        $this->assertTrue( $list->isWeak( ucfirst( $extra ) . 'ing42' ), 'a listed word has its variants too' );
        $this->assertTrue( $list->isWeak( 'admin' ), 'the built-in words stay' );

        $dir = dirname( __DIR__, 5 ) . '/var/tmp/weak-password-list-test-' . getmypid();
        @mkdir( $dir, 0775, true );
        $file = $dir . '/words.txt';
        file_put_contents( $file, "# our own words\nQuuxcorp\n\n  frobnicate  \n" );
        $list = self::make( array( 'WeakPasswordFile' => $file ) );
        $this->assertTrue( $list->isWeak( 'quuxcorp2026' ) );
        $this->assertTrue( $list->isWeak( 'Frobnicate!' ) );
        $this->assertFalse( $list->isWeak( 'our own words' ), 'a comment is no word' );
        $this->assertArrayHasKey( 'quuxcorp', expWeakPasswordList::readFile( $file ) );

        $missing = self::make( array( 'WeakPasswordFile' => $dir . '/missing.txt' ) );
        $this->assertFalse( $missing->isWeak( 'quuxcorp2026' ), 'an unreadable file adds nothing' );
        $this->assertTrue( $missing->isWeak( 'admin' ) );
        @unlink( $file );
        @rmdir( $dir );
    }

    // ------------------------------------------------------------------ WPL-06

    public function testTheSwitch()
    {
        $this->assertTrue( self::make()->enabled(), 'enabled when not set' );
        $this->assertTrue( self::make( array( 'WeakPasswordCheck' => 'enabled' ) )->enabled() );
        $off = self::make( array( 'WeakPasswordCheck' => 'disabled' ) );
        $this->assertFalse( $off->enabled() );
        $this->assertFalse( $off->refuses( 'admin' ), 'switched off for the forms' );
        $this->assertTrue( $off->isWeak( 'admin' ), 'the check itself still answers (the installers use it)' );
        $this->assertTrue( self::make()->refuses( 'admin' ) );
        $this->assertFalse( self::make()->refuses( 'k1e-Strong-Passphrase-9' ) );
    }

    // ------------------------------------------------------------------ WPL-07

    public function testPasswordPolicyRule()
    {
        $ini = new expWeakPasswordTestINI( array( 'UserSettings' => array( 'MinPasswordLength' => '10' ), 'PasswordSettings' => array() ) );
        $policy = new expPasswordPolicy( $ini );
        $this->assertSame( array( 'length', 'not_common' ), array_column( $policy->rules(), 'id' ) );
        $long = 'administrator' . '2026';
        $this->assertSame( array( 'not_common' ), $policy->validate( $long ) );
        $this->assertSame( array( 'length', 'not_common' ), $policy->validate( 'admin' ) );
        $this->assertSame( array(), $policy->validate( 'k1e-Strong-Passphrase-9' ) );
        $this->assertSame( array( 'not_common' ), $policy->validate( 'publish' . 'ing' ), 'the variant of a base word, ten characters long' );
        $this->assertStringContainsString( 'well-known', $policy->errorText( 'not_common' ) );
        $this->assertStringContainsString( 'well-known', $policy->accountErrorText( 'not_common' ) );
        $this->assertSame( array( 'The password is well-known or too common. Choose another one.' ), $policy->accountErrors( $long ) );
        $this->assertSame( 'not_common', $policy->rulesForTemplate( array( 'not_common' ) )[1]['id'] );
        $this->assertTrue( $policy->rulesForTemplate( array( 'not_common' ) )[1]['failed'] );

        $off = new expPasswordPolicy( new expWeakPasswordTestINI( array( 'UserSettings' => array( 'MinPasswordLength' => '10', 'WeakPasswordCheck' => 'disabled' ) ) ) );
        $this->assertSame( array( 'length' ), array_column( $off->rules(), 'id' ), 'the rules of before' );
        $this->assertSame( array(), $off->validate( $long ) );
        $this->assertSame( array(), $off->validate( 'abcdefghij' ), 'ten letters pass, as before' );
    }

    // ------------------------------------------------------------------ WPL-08

    public function testInstallers()
    {
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( 'publish' . 'ing' ), 'long enough, but a variant of the old default' );
        $this->assertTrue( eZStepSiteAdmin::isWeakDefaultPassword( 'Exponential' . '2026!' ) );
        $this->assertFalse( eZStepSiteAdmin::isWeakDefaultPassword( 'k1e-Strong-Passphrase-9' ) );
        $this->assertSame( 'empty', eZStepSiteAdmin::kickstartPasswordProblem( '' ) );
        $this->assertSame( 'empty', eZStepSiteAdmin::kickstartPasswordProblem( null ) );
        $this->assertSame( 'well-known', eZStepSiteAdmin::kickstartPasswordProblem( 'admin' . 'istrator' ) );
        $this->assertSame( 'too short', eZStepSiteAdmin::kickstartPasswordProblem( 'Kx9#mPq' ) );
        $this->assertNull( eZStepSiteAdmin::kickstartPasswordProblem( 'k1e-Strong-Passphrase-9' ) );
        $this->assertGreaterThanOrEqual( 1, eZStepSiteAdmin::minAdminPasswordLength() );
        $this->assertSame( 'well-known', \Exponential\Command\Kernel\Install::passwordProblem( 'publish' . 'ing' ) );
    }
}
