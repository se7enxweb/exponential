<?php
/**
 * eZMailTransport::send( $mail, $transportType ): the given transport is used for that one message only; without
 * one (null or '') the transport of site.ini [MailSettings] Transport is used as before.
 *
 * No mail leaves the machine: settings/override sets Transport=sendmail and outranks the siteaccess settings, so
 * the tests set [MailSettings] in-process, to recording transports of their own and to the file transport, which
 * writes into a throwaway directory below var/tmp. The mail gate's log goes there too.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZMailTransportSendTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit df07cb1ef5), Copyright (C) Mugo Web, GPL v2
 */

class eZMailTransportSendTestDefaultTransport extends eZMailTransport
{
    public static $sent = array();

    function sendMail( eZMail $mail )
    {
        static::$sent[] = $mail->subject();
        return true;
    }
}

class eZMailTransportSendTestOtherTransport extends eZMailTransport
{
    public static $sent = array();

    function sendMail( eZMail $mail )
    {
        static::$sent[] = $mail->subject();
        return true;
    }
}

class eZMailTransportSendTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $saved = array();

    protected function setUp(): void
    {
        $this->dir = eZSys::rootDir() . '/var/tmp/ezmailtransport_send_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
        if ( class_exists( 'expMailGate' ) )
            expMailGate::setLogFileForTest( $this->dir . '/mailgate.jsonl' );

        $ini = eZINI::instance();
        foreach ( array( 'Transport', 'TransportAlias', 'FileTransportDirectory' ) as $name )
            $this->saved[$name] = $ini->hasVariable( 'MailSettings', $name ) ? $ini->variable( 'MailSettings', $name ) : null;

        $ini->setVariable( 'MailSettings', 'Transport', 'recorddefault' );
        $ini->setVariable( 'MailSettings', 'TransportAlias', array(
            'file' => 'eZFileTransport',
            'recorddefault' => 'eZMailTransportSendTestDefaultTransport',
            'recordother' => 'eZMailTransportSendTestOtherTransport',
        ) );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $this->dir . '/mail' );

        eZMailTransportSendTestDefaultTransport::$sent = array();
        eZMailTransportSendTestOtherTransport::$sent = array();
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'MailSettings', $name );
            else
                $ini->setVariable( 'MailSettings', $name, $value );
        }
        if ( class_exists( 'expMailGate' ) )
            expMailGate::setLogFileForTest( null );
        $this->removeDir( $this->dir );
    }

    private function mail( $subject )
    {
        $mail = new eZMail();
        $mail->setSender( 'sender@example.com' );
        $mail->setReceiver( 'receiver@example.com' );
        $mail->setSubject( $subject );
        $mail->setBody( 'Body of ' . $subject );
        return $mail;
    }

    public function testWithoutATransportTheConfiguredOneIsUsed()
    {
        $this->assertTrue( eZMailTransport::send( $this->mail( 'one' ) ) );
        $this->assertTrue( eZMailTransport::send( $this->mail( 'two' ), '' ) );
        $this->assertSame( array( 'one', 'two' ), eZMailTransportSendTestDefaultTransport::$sent );
        $this->assertSame( array(), eZMailTransportSendTestOtherTransport::$sent );
    }

    public function testTheGivenTransportIsUsedForThatMessageOnly()
    {
        $this->assertTrue( eZMailTransport::send( $this->mail( 'before' ) ) );
        $this->assertTrue( eZMailTransport::send( $this->mail( 'chosen' ), 'recordother' ) );
        $this->assertTrue( eZMailTransport::send( $this->mail( 'after' ) ) );

        $this->assertSame( array( 'chosen' ), eZMailTransportSendTestOtherTransport::$sent );
        $this->assertSame( array( 'before', 'after' ), eZMailTransportSendTestDefaultTransport::$sent );
        $this->assertSame( 'recorddefault', eZINI::instance()->variable( 'MailSettings', 'Transport' ) );
    }

    public function testTheNameIsCaseInsensitiveAndTrimmedAsTheSettingIs()
    {
        $this->assertTrue( eZMailTransport::send( $this->mail( 'upper' ), ' RecordOther ' ) );
        $this->assertSame( array( 'upper' ), eZMailTransportSendTestOtherTransport::$sent );
    }

    public function testTheFileTransportWritesTheMessageToItsDirectory()
    {
        $this->assertTrue( (bool)eZMailTransport::send( $this->mail( 'to a file' ), 'file' ) );
        $files = glob( $this->dir . '/mail/*.mail' );
        $this->assertCount( 1, $files );
        $this->assertStringContainsString( 'to a file', file_get_contents( $files[0] ) );
        $this->assertSame( array(), eZMailTransportSendTestDefaultTransport::$sent );
    }

    public function testAnUnknownTransportSendsNothing()
    {
        $this->assertFalse( @eZMailTransport::send( $this->mail( 'nowhere' ), 'nosuchtransport' ) );
        $this->assertSame( array(), eZMailTransportSendTestDefaultTransport::$sent );
        $this->assertSame( array(), eZMailTransportSendTestOtherTransport::$sent );
    }

    private function removeDir( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            if ( is_dir( $path ) )
                $this->removeDir( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }
}
