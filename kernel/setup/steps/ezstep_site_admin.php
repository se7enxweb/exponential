<?php
/**
 * File containing the eZStepSiteAdmin class.
 *
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
     * Passwords a kickstart file must not install: none at all, and the ones
     * printed in every example and tutorial.
     *
     * @param mixed $password
     * @return bool
     */
    public static function isWeakDefaultPassword( $password )
    {
        if ( !is_string( $password ) || trim( $password ) === '' )
            return true;
        return in_array( strtolower( trim( $password ) ),
                         array( 'publish', 'admin', 'password', 'changeme', 'change-me', 'secret', 'exponential', 'demo', '123456' ),
                         true );
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
            @mkdir( dirname( $file ), 0770, true );
        $old = umask( 0077 );
        $written = @file_put_contents( $file,
            "Exponential administrator login: admin\n" .
            "Password: $password\n" .
            "Generated " . date( 'c' ) . " because the kickstart file set no password or a well-known one.\n" .
            "Log in, change it, then delete this file.\n" );
        umask( $old );
        if ( $written !== false )
            @chmod( $file, 0600 );

        if ( class_exists( 'eZCLI', false ) && PHP_SAPI === 'cli' )
        {
            eZCLI::instance()->warning( "The administrator password was not set or is a well-known one; generated a random one." );
            eZCLI::instance()->output( "  admin password: $password" );
            eZCLI::instance()->output( "  (also written to $file - change the password after the first login and delete that file)" );
        }
        eZDebug::writeNotice( "Generated a random administrator password (********), recorded in $file", __METHOD__ );
        return $password;
    }

    function processPostData()
    {
        $user = array();

        $user['first_name'] = $this->Http->postVariable( 'eZSetup_site_templates_first_name' );
        $user['last_name'] = $this->Http->postVariable( 'eZSetup_site_templates_last_name' );
        $user['email'] = $this->Http->postVariable( 'eZSetup_site_templates_email' );
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
        if ( strlen( trim( $this->Http->postVariable( 'eZSetup_site_templates_password1' ) ) ) == 0 )
        {
            $this->Error[] = self::PASSWORD_MISSING;
        }
        else if ( $this->Http->postVariable( 'eZSetup_site_templates_password1' ) != $this->Http->postVariable( 'eZSetup_site_templates_password2' ) )
        {
            $this->Error[] = self::PASSWORD_MISSMATCH;
        }
        else if ( !eZUser::validatePassword( trim( $this->Http->postVariable( 'eZSetup_site_templates_password1' ) ) ) )
        {
            $this->Error[] = self::PASSWORD_TOO_SHORT;
        }
        else
        {
            $user['password'] = $this->Http->postVariable( 'eZSetup_site_templates_password1' );
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

            // No password, or one everybody knows: the new site would open
            // with a login anyone can guess. Give it a random one instead.
            // (A step can be initialised again later in the same setup; keep
            // the one already generated rather than making another.)
            if ( self::isWeakDefaultPassword( $adminUser['password'] ) )
            {
                $previous = $this->PersistenceList['admin']['password'] ?? false;
                $adminUser['password'] = self::isWeakDefaultPassword( $previous )
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
            }
        }

        $this->Tpl->setVariable( 'has_errors', count( $this->Error ) > 0 );

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
