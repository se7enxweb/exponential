<?php
/**
 * The administrator password of exp:install: what is accepted, what is generated, and how the summary reads
 * back the password the installation really set. Files are written under var/tmp/install-password-tests/ only.
 *
 *  IP-01 - well-known passwords (publish, admin) are a problem; so are ones shorter than 10 characters
 *  IP-02 - an acceptable password (10+ characters, not well-known) has no problem and is kept as given
 *  IP-03 - a generated password is 24 characters of the bcrypt alphabet and differs per call
 *  IP-04 - the recorded password is read back from the file the setup writes, and only when this run wrote it
 *  IP-05 - the file written by exp:install itself reads back the same, owner-only
 *  IP-06 - insertAfter puts the note right below the password row
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group runnable
 */

class InstallPasswordTest extends PHPUnit\Framework\TestCase
{
    private static function dir()
    {
        $dir = dirname( __DIR__, 5 ) . '/var/tmp/install-password-tests';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        return $dir;
    }

    public function testWellKnownAndShortPasswordsAreProblems()
    {
        $class = '\Exponential\Command\Kernel\Install';
        $this->assertSame( 'well-known', $class::passwordProblem( 'publish' ) );
        $this->assertSame( 'well-known', $class::passwordProblem( 'admin' ) );
        $this->assertNotNull( $class::passwordProblem( 'short1' ) );
        $this->assertStringContainsString( 'shorter', $class::passwordProblem( 'abc123xyz' ) );
    }

    public function testAcceptablePasswordIsKept()
    {
        $class = '\Exponential\Command\Kernel\Install';
        $this->assertNull( $class::passwordProblem( 'publishing' ) );
        $this->assertNull( $class::passwordProblem( 'Tr0ub4dor&3xyz' ) );
    }

    public function testGeneratedPassword()
    {
        $class = '\Exponential\Command\Kernel\Install';
        $a = $class::generatePassword();
        $this->assertMatchesRegularExpression( '#^[./A-Za-z0-9]{24}$#', $a );
        $this->assertNotSame( $a, $class::generatePassword() );
        $this->assertNull( $class::passwordProblem( $a ) );
    }

    public function testRecordedPasswordIsReadBackOnlyWhenWrittenByThisRun()
    {
        $class = '\Exponential\Command\Kernel\Install';
        $file = self::dir() . '/setup-format.txt';
        file_put_contents( $file, "Exponential administrator login: admin\nPassword: s3cretValue9X\nGenerated now because the kickstart file set no password or a well-known one.\n" );
        $this->assertSame( 's3cretValue9X', $class::readRecordedPassword( $file, filemtime( $file ) ) );
        $this->assertNull( $class::readRecordedPassword( $file, filemtime( $file ) + 10 ), 'an older file is not this run\'s' );
        $this->assertNull( $class::readRecordedPassword( self::dir() . '/missing.txt', 0 ) );
    }

    public function testFileWrittenByTheInstallerReadsBack()
    {
        $class = '\Exponential\Command\Kernel\Install';
        $file = self::dir() . '/installer-format.txt';
        $this->assertTrue( $class::writeRecordedPassword( $file, 'abcDEF123./abcDEF123./', 'test' ) );
        $this->assertSame( 'abcDEF123./abcDEF123./', $class::readRecordedPassword( $file, filemtime( $file ) ) );
        $this->assertSame( '0600', substr( sprintf( '%o', fileperms( $file ) ), -4 ) );
    }

    public function testInsertAfter()
    {
        $class = '\Exponential\Command\Kernel\Install';
        $out = $class::insertAfter( array( 'A' => 1, 'Password' => 2, 'B' => 3 ), 'Password', 'Note', 'x' );
        $this->assertSame( array( 'A', 'Password', 'Note', 'B' ), array_keys( $out ) );
    }
}
