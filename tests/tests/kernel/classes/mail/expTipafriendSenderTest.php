<?php
/**
 * content/tipafriend without [TipAFriend] FromEmail sends as the site, not as the visitor:
 * Tipafriend::siteSender() is the first valid address of [MailSettings] EmailSender and AdminEmail ("address" or
 * "Name <address>"), null when neither is one (then the visitor's address is used as before). The visitor's address
 * goes into Reply-To.
 *
 * DB-less; nothing is sent. Fake addresses use the .invalid domain.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

use Exponential\View\Kernel\Content\Tipafriend;

class expTipafriendSenderTest extends PHPUnit\Framework\TestCase
{
    /** @var array */
    private $saved = array();

    protected function setUp(): void
    {
        $ini = eZINI::instance();
        foreach ( array( 'EmailSender', 'AdminEmail' ) as $key )
            $this->saved[$key] = $ini->hasVariable( 'MailSettings', $key ) ? $ini->variable( 'MailSettings', $key ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->saved as $key => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'MailSettings', $key );
            else
                $ini->setVariable( 'MailSettings', $key, $value );
        }
    }

    private static function sender( $emailSender, $adminEmail )
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'EmailSender', $emailSender );
        $ini->setVariable( 'MailSettings', 'AdminEmail', $adminEmail );
        return Tipafriend::siteSender( $ini );
    }

    public function testTheSiteSender()
    {
        $this->assertSame( 'site@example.invalid', self::sender( 'site@example.invalid', 'admin@example.invalid' ) );
        $this->assertSame( 'site@example.invalid', self::sender( 'Portal <site@example.invalid>', 'admin@example.invalid' ) );
        $this->assertSame( 'admin@example.invalid', self::sender( '', 'admin@example.invalid' ) );
        $this->assertSame( 'admin@example.invalid', self::sender( 'not an address', 'admin@example.invalid' ) );
        $this->assertNull( self::sender( '', '' ) );
    }

    /**
     * Sent with the site's address, the display name is the site's, never the visitor's: "Bank support" would
     * otherwise have arrived as "Bank support <site address>" from the site's own domain.
     */
    public function testTheSiteSenderNameIsTheSiteName()
    {
        $ini = eZINI::instance();
        $saved = $ini->hasVariable( 'SiteSettings', 'SiteName' ) ? $ini->variable( 'SiteSettings', 'SiteName' ) : null;
        try
        {
            $ini->setVariable( 'SiteSettings', 'SiteName', 'Example "Site"' . "\r\nBcc: x@example.invalid" );
            $this->assertSame( 'Example  Site Bcc: x@example.invalid', Tipafriend::siteSenderName( $ini ) );
            $ini->setVariable( 'SiteSettings', 'SiteName', 'Example Site' );
            $this->assertSame( 'Example Site', Tipafriend::siteSenderName( $ini ) );
        }
        finally
        {
            if ( $saved === null )
                $ini->removeSetting( 'SiteSettings', 'SiteName' );
            else
                $ini->setVariable( 'SiteSettings', 'SiteName', $saved );
        }
    }
}
