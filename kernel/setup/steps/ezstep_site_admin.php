<?php
/**
 * File containing the eZStepSiteAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepSiteAdmin ezstep_site_admin.php
  \brief The class eZStepSiteAdmin does

*/

class eZStepSiteAdmin extends eZStepInstaller
{
    const PASSWORD_MISSMATCH = 1;
    const FIRST_NAME_MISSING = 2;
    const LAST_NAME_MISSING = 3;
    const EMAIL_MISSING = 4;
    const EMAIL_INVALID = 5;
    const PASSWORD_MISSING = 6;
    const PASSWORD_TOO_SHORT = 7;
    const PASSWORD_TOO_COMMON = 8;

    /**
     * Constructor
     *
     * @param eZTemplate $tpl
     * @param eZHTTPTool $http
     * @param eZINI $ini
     * @param array $persistenceList
     */
    public function __construct( $tpl, $http, $ini, &$persistenceList )
    {
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'site_admin', 'Site admin' );
    }

    /**
     * Passwords an installation must not install: none at all, and the well-known and common ones
     * (expWeakPasswordList: the built-in words with their simple variants, site.ini [UserSettings]
     * WeakPasswordList[] and WeakPasswordFile). The installers always check, whatever WeakPasswordCheck says.
     *
     * In demo mode (the environment variable EXP_DEMO_MODE=1, expDemoMode, doc/bc/6.0/demo-mode.md) a well-known
     * password is accepted, so a public demo can install the password it publishes; an empty one never is (the
     * length: isTooShortAdminPassword()). This is the one place every installer asks (setup
     * wizard, kickstarter, exp:install and the demo installers built on them), and it only concerns the
     * administrator password of a new installation.
     *
     * @param mixed $password
     * @return bool
     */
    public static function isWeakDefaultPassword( $password )
    {
        if ( !is_string( $password ) || trim( $password ) === '' )
            return true;
        if ( self::demoModeAcceptsWellKnown() )
            return false;
        return expWeakPasswordList::instance()->isWeak( $password );
    }

    /**
     * @return bool demo mode is on, so the installers accept a well-known administrator password (read on every call)
     */
    public static function demoModeAcceptsWellKnown()
    {
        return class_exists( 'expDemoMode' ) && expDemoMode::enabled();
    }

    /**
     * @return int the shortest administrator password an installation accepts: site.ini [UserSettings]
     *             MinPasswordLength, 10 when it is not set
     */
    public static function minAdminPasswordLength()
    {
        $min = 10;
        if ( class_exists( 'eZINI', false ) )
        {
            $configured = (int)eZINI::instance( 'site.ini' )->variable( 'UserSettings', 'MinPasswordLength' );
            if ( $configured > 0 )
                $min = $configured;
        }
        return $min;
    }

    /**
     * Why a kickstart file's administrator password cannot be installed, null when it can.
     *
     * @param mixed $password
     * @return string|null "empty", "well-known" or "too short"
     */
    public static function kickstartPasswordProblem( $password )
    {
        if ( !is_string( $password ) || trim( $password ) === '' )
            return 'empty';
        if ( self::isWeakDefaultPassword( $password ) )
            return 'well-known';
        if ( self::isTooShortAdminPassword( $password ) )
            return 'too short';
        return null;
    }

    /**
     * Whether an administrator password of a new installation is shorter than minAdminPasswordLength(). In demo mode
     * (EXP_DEMO_MODE=1, doc/bc/6.0/demo-mode.md) no length is too short, so a public demo can install the short
     * password it publishes (publish); an empty password is refused by the callers before they ask.
     *
     * @param mixed $password
     * @return bool
     */
    public static function isTooShortAdminPassword( $password )
    {
        if ( self::demoModeAcceptsWellKnown() )
            return false;
        return strlen( (string)$password ) < self::minAdminPasswordLength();
    }

    /**
     * A random administrator password, recorded once for the person running
     * the installation: printed on the console when there is one, and
     * written to var/log/initial-admin-password (readable by the owner
     * only), which they are told to read and delete. setup.log only notes
     * that it happened.
     *
     * @return string
     */
    public static function generateAdminPassword()
    {
        $alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $password = '';
        for ( $i = 0; $i < 20; $i++ )
            $password .= $alphabet[random_int( 0, strlen( $alphabet ) - 1 )];

        $file = 'var/log/initial-admin-password';
        if ( !is_dir( dirname( $file ) ) )
            @mkdir( dirname( $file ), eZDir::dirMode( 0770 ), true );
        $old = umask( eZFile::creationUmask( 0077 ) );
        $written = @file_put_contents( $file,
            "Exponential administrator login: admin\n" .
            "Password: $password\n" .
            "Generated " . date( 'c' ) . " because the kickstart file set no password, a well-known one or one that is too short.\n" .
            "Log in, change it, then delete this file.\n" );
        umask( $old );
        if ( $written !== false )
            @chmod( $file, eZFile::fileMode( 0600 ) );

        if ( class_exists( 'eZCLI', false ) && PHP_SAPI === 'cli' )
        {
            eZCLI::instance()->warning( "The administrator password was not set, is a well-known one or is shorter than " . self::minAdminPasswordLength() . " characters; generated a random one." );
            eZCLI::instance()->output( "  admin password: $password" );
            eZCLI::instance()->output( "  (also written to $file - change the password after the first login and delete that file)" );
        }
        eZDebug::writeNotice( "Generated a random administrator password (********), recorded in $file", __METHOD__ );
        return $password;
    }

    function processPostData()
    {
        $user = array();

        // A field that was not posted, or was posted as an array (name[]=x), is
        // no answer: trim() refused it with a TypeError and ended the setup
        $posted = array();
        foreach ( array( 'first_name', 'last_name', 'email', 'password1', 'password2' ) as $field )
        {
            $value = $this->Http->postVariable( 'eZSetup_site_templates_' . $field, '' );
            $posted[$field] = is_scalar( $value ) ? (string)$value : '';
        }

        $user['first_name'] = $posted['first_name'];
        $user['last_name'] = $posted['last_name'];
        $user['email'] = $posted['email'];
        if ( strlen( trim( $user['first_name'] ) ) == 0 )
        {
            $this->Error[] = self::FIRST_NAME_MISSING;
        }
        if ( strlen( trim( $user['last_name'] ) ) == 0 )
        {
            $this->Error[] = self::LAST_NAME_MISSING;
        }
        if ( strlen( trim( $user['email'] ) ) == 0 )
        {
            $this->Error[] = self::EMAIL_MISSING;
        }
        else if ( !eZMail::validate( trim( $user['email'] ) ) )
        {
            $this->Error[] = self::EMAIL_INVALID;
        }
        if ( strlen( trim( $posted['password1'] ) ) == 0 )
        {
            $this->Error[] = self::PASSWORD_MISSING;
        }
        else if ( $posted['password1'] !== $posted['password2'] )
        {
            $this->Error[] = self::PASSWORD_MISSMATCH;
        }
        else if ( !self::demoModeAcceptsWellKnown() && !eZUser::validatePassword( trim( $posted['password1'] ) ) )
        {
            // (demo mode, EXP_DEMO_MODE=1: no length is too short for the administrator of a new installation)
            $this->Error[] = self::PASSWORD_TOO_SHORT;
        }
        else if ( self::isWeakDefaultPassword( $posted['password1'] ) )
        {
            // a long word everybody knows is no better than a short one
            $this->Error[] = self::PASSWORD_TOO_COMMON;
        }
        else
        {
            $user['password'] = $posted['password1'];
        }
        if ( !isset( $user['password'] ) )
            $user['password'] = '';
        $this->PersistenceList['admin'] = $user;

        return ( count( $this->Error ) == 0 );
    }

    function init()
    {
        $siteType = $this->chosenSiteType();
        if ( isset( $siteType['existing_database'] ) &&
             $siteType['existing_database'] == eZStepInstaller::DB_DATA_KEEP ) // Keep existing data in database, no need to reset admin user.
        {
            return true;
        }

        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            $adminUser = array( 'first_name' => 'Administrator',
                                'last_name' => 'User',
                                'email' => false,
                                'password' => false );

            if ( isset( $data['FirstName'] ) )
                $adminUser['first_name'] = $data['FirstName'];
            if ( isset( $data['LastName'] ) )
                $adminUser['last_name'] = $data['LastName'];
            if ( isset( $data['Email'] ) )
                $adminUser['email'] = $data['Email'];
            if ( isset( $data['Password'] ) )
                $adminUser['password'] = $data['Password'];

            // No password, one everybody knows or one shorter than
            // MinPasswordLength (the rules of exp:install): the new site would
            // open with a login anyone can guess. Give it a random one instead.
            // (A step can be initialised again later in the same setup; keep
            // the one already generated rather than making another.)
            $problem = self::kickstartPasswordProblem( $adminUser['password'] );
            if ( $problem !== null )
            {
                eZDebug::writeNotice( "The kickstart administrator password was refused ($problem); a random one is used", __METHOD__ );
                $previous = $this->PersistenceList['admin']['password'] ?? false;
                $adminUser['password'] = self::kickstartPasswordProblem( $previous ) !== null
                    ? self::generateAdminPassword()
                    : $previous;
            }

            $this->PersistenceList['admin'] = $adminUser;
            return $this->kickstartContinueNextStep();
        }

        // Set default values for admin user
        if ( !isset( $this->PersistenceList['admin'] ) )
        {
            $adminUser = array( 'first_name' => 'Administrator',
                                'last_name' => 'User',
                                'email' => false,
                                'password' => false );
            $this->PersistenceList['admin'] = $adminUser;
        }

        return false;
    }

    function display()
    {
        $this->Tpl->setVariable( 'first_name_missing', 0 );
        $this->Tpl->setVariable( 'last_name_missing', 0 );
        $this->Tpl->setVariable( 'email_missing', 0 );
        $this->Tpl->setVariable( 'email_invalid', 0 );
        $this->Tpl->setVariable( 'password_missmatch', 0 );
        $this->Tpl->setVariable( 'password_missing', 0 );
        $this->Tpl->setVariable( 'password_too_short', 0 );
        $this->Tpl->setVariable( 'password_too_common', 0 );

        if ( isset( $this->Error[0] ) )
        {
            switch ( $this->Error[0] )
            {
                case self::FIRST_NAME_MISSING:
                {
                    $this->Tpl->setVariable( 'first_name_missing', 1 );
                } break;

                case self::LAST_NAME_MISSING:
                {
                    $this->Tpl->setVariable( 'last_name_missing', 1 );
                } break;

                case self::EMAIL_MISSING:
                {
                    $this->Tpl->setVariable( 'email_missing', 1 );
                } break;

                case self::EMAIL_INVALID:
                {
                    $this->Tpl->setVariable( 'email_invalid', 1 );
                } break;

                case self::PASSWORD_MISSMATCH:
                {
                    $this->Tpl->setVariable( 'password_missmatch', 1 );
                } break;

                case self::PASSWORD_MISSING:
                {
                    $this->Tpl->setVariable( 'password_missing', 1 );
                } break;

                case self::PASSWORD_TOO_SHORT:
                {
                    $this->Tpl->setVariable( 'password_too_short', 1 );
                } break;

                case self::PASSWORD_TOO_COMMON:
                {
                    $this->Tpl->setVariable( 'password_too_common', 1 );
                } break;
            }
        }

        $this->Tpl->setVariable( 'has_errors', count( $this->Error ) > 0 );
        // demo mode (EXP_DEMO_MODE=1): the page says that a well-known password is accepted, and why
        $this->Tpl->setVariable( 'demo_mode', self::demoModeAcceptsWellKnown() );

        $adminUser = array( 'first_name' => false,
                            'last_name' => false,
                            'email' => false,
                            'password' => false );
        if ( isset( $this->PersistenceList['admin'] ) )
            $adminUser = $this->PersistenceList['admin'];

        $this->Tpl->setVariable( 'admin', $adminUser );

        // Return template and data to be shown
        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/site_admin.tpl' );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Site administrator' ),
                                        'url' => false ) );
        return $result;
    }

    public $Error = array();
}

?>
