<?php
/**
 * expaccount: the account flows of a person: registration and activation, forgotten password and reset,
 * changing one's own password and e-mail, and the self-service reads. The public flows (register, activate,
 * forgotRequest, reset) answer the same whether or not an address or key is known where that would otherwise
 * reveal accounts, and never return a key, hash or password.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expAccountServices extends expUsersBase
{
    public static $services = array();

    /** Seconds a forgotten password or activation key stays usable through the services. */
    const KEY_LIFETIME = 86400;

    protected static function keyOk( $key )
    {
        return is_string( $key ) && preg_match( '/^[a-f0-9]{32}$/', $key );
    }

    protected static function failed( $reason, $user = null )
    {
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.user.password.reset.failed', array( 'object' => array( 'type' => 'user' ), 'result' => 'refused', 'reason' => $reason ) );
    }

    public static function registrationOptions( array $a = array() )
    {
        self::guard( 'registrationOptions' );
        $ini = eZINI::instance();
        return self::ok( array( 'allowed' => self::can( 'user', 'register' ), 'verification' => (string)$ini->variable( 'UserSettings', 'VerifyUserType' ) ?: 'none',
                                'password_in_mail' => $ini->variable( 'UserSettings', 'PasswordInRegistrationEmail' ) === 'enabled',
                                'min_password_length' => (int)$ini->variable( 'UserSettings', 'MinPasswordLength' ),
                                'unique_email' => (bool)eZUser::requireUniqueEmail(), 'user_class' => eZUser::fetchUserClassNames() ) );
    }

    public static function register( array $a = array() )
    {
        self::guard( 'register' );
        if ( eZUser::currentUser()->isRegistered() )
            throw new expServiceException( 'You are signed in; sign out to register another account', 409 );
        if ( !self::can( 'user', 'register' ) )
            throw new expServiceException( 'Registration is not open', 403 );
        $login = trim( self::post( 'login', 'string' ) );
        $email = trim( self::post( 'email', 'string' ) );
        $password = self::post( 'password', 'string' );
        $fields = self::post( 'fields', 'json', array() );
        self::validateNewAccount( $login, $email, $password );
        $ini = eZINI::instance();
        $placement = (int)$ini->variable( 'UserSettings', 'DefaultUserPlacement' );
        $classId = (int)$ini->variable( 'UserSettings', 'UserClassID' );
        $class = eZContentClass::fetch( $classId );
        if ( !$class instanceof eZContentClass || !eZContentObjectTreeNode::fetch( $placement ) )
            throw new expServiceException( 'Registration is not configured', 409 );
        $verify = (string)$ini->variable( 'UserSettings', 'VerifyUserType' );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $attributes = array( 'user_account' => $login . '|' . $email . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|' . ( $verify === '' ? 1 : 0 ) );
        foreach ( (array)$fields as $k => $v )
            if ( $k !== 'user_account' && is_scalar( $v ) )
                $attributes[$k] = (string)$v;
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $placement, 'class_identifier' => $class->attribute( 'identifier' ),
                                                                      'creator_id' => (int)eZUser::anonymousId(), 'attributes' => $attributes ) );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'The account could not be created', 422 );
        $id = (int)$object->attribute( 'id' );
        $mailed = false;
        if ( $verify !== '' )
        {
            eZUserOperationCollection::sendActivationEmail( $id );
            $mailed = true;
        }
        return self::ok( array( 'created' => true, 'needs_activation' => $verify !== '', 'activation_mail_sent' => $mailed ) );
    }

    public static function activationKeyValid( array $a = array() )
    {
        self::guard( 'activationKeyValid' );
        $key = self::arg( $a, 0, 'string' );
        $k = self::keyOk( $key ) ? eZUserAccountKey::fetchByKey( $key ) : null;
        return self::ok( array( 'valid' => $k instanceof eZUserAccountKey && (int)$k->attribute( 'time' ) + self::KEY_LIFETIME > time() ) );
    }

    public static function activate( array $a = array() )
    {
        self::guard( 'activate' );
        $key = self::post( 'key', 'string' );
        $k = self::keyOk( $key ) ? eZUserAccountKey::fetchByKey( $key ) : null;
        if ( !$k instanceof eZUserAccountKey || (int)$k->attribute( 'time' ) + self::KEY_LIFETIME <= time() )
            throw new expServiceException( 'The activation key is not valid', 404 );
        $id = (int)$k->attribute( 'user_id' );
        $r = eZUserOperationCollection::activation( $id, $key, true );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The account could not be activated', 422 );
        return self::ok( array( 'activated' => true ) );
    }

    public static function forgotRequest( array $a = array() )
    {
        self::guard( 'forgotRequest' );
        $email = trim( self::post( 'email', 'string' ) );
        if ( !eZMail::validate( $email ) || preg_match( '/[<>"\'&\\\\]/', $email ) )
            throw new expServiceException( 'The e-mail address is not valid', 422 );
        $users = eZPersistentObject::fetchObjectList( eZUser::definition(), null, array( 'email' => $email ), null, null, true );
        if ( $users )
        {
            $user = $users[0];
            $hash = bin2hex( random_bytes( 16 ) );
            eZForgotPassword::removeByUserID( (int)$user->id() );
            eZUserOperationCollection::forgotpassword( (int)$user->id(), $hash, time() );
            $ini = eZINI::instance();
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'user', $user );
            $tpl->setVariable( 'object', $user->attribute( 'contentobject' ) );
            $tpl->setVariable( 'password', '' );
            $tpl->setVariable( 'link', true );
            $tpl->setVariable( 'hash_key', $hash );
            $body = $tpl->fetch( 'design:user/forgotpasswordmail.tpl' );
            $mail = new eZMail();
            if ( $tpl->hasVariable( 'content_type' ) )
                $mail->setContentType( $tpl->variable( 'content_type' ) );
            $sender = $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' );
            $mail->setSender( $sender );
            $mail->setReceiver( $email );
            $mail->setSubject( $tpl->hasVariable( 'subject' ) ? $tpl->variable( 'subject' ) : ezpI18n::tr( 'kernel/user/register', 'Registration info' ) );
            $mail->setBody( $body );
            $sent = eZMailTransport::send( $mail );
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'access.user.password.reset.request', array( 'object' => expAuditHook::user( $user ), 'after' => array( 'mail_sent' => (bool)$sent ) ) );
        }
        else
            self::failed( 'unknown_email' );
        // the same answer for a known and an unknown address
        return self::ok( array( 'requested' => true ) );
    }

    public static function forgotKeyValid( array $a = array() )
    {
        self::guard( 'forgotKeyValid' );
        $key = self::arg( $a, 0, 'string' );
        $k = self::keyOk( $key ) ? eZForgotPassword::fetchByKey( $key ) : null;
        return self::ok( array( 'valid' => $k instanceof eZForgotPassword && (int)$k->attribute( 'time' ) + self::KEY_LIFETIME > time() ) );
    }

    protected static function resetKey( $key )
    {
        $k = self::keyOk( $key ) ? eZForgotPassword::fetchByKey( $key ) : null;
        if ( !$k instanceof eZForgotPassword || (int)$k->attribute( 'time' ) + self::KEY_LIFETIME <= time() )
        {
            self::failed( 'unknown_key' );
            throw new expServiceException( 'The key is not valid', 404 );
        }
        return $k;
    }

    public static function reset( array $a = array() )
    {
        self::guard( 'reset' );
        $k = self::resetKey( self::post( 'key', 'string' ) );
        $password = self::post( 'password', 'string' );
        if ( !eZUser::validatePassword( $password ) )
            throw new expServiceException( 'The password is too short', 422 );
        $id = (int)$k->attribute( 'user_id' );
        $change = function () use ( $id, $password ) { return eZUserOperationCollection::password( $id, $password ); };
        $r = class_exists( 'expAuditHook' ) ? expAuditHook::muted( 'access.user.password.change', $change ) : $change();
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The password could not be changed', 422 );
        eZForgotPassword::removeByUserID( $id );
        eZUser::removeSessionData( $id );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'access.user.password.reset', array( 'object' => expAuditHook::user( $id ), 'after' => array( 'mail_sent' => false ) ) );
        return self::ok( array( 'reset' => true ) );
    }

    public static function changePassword( array $a = array() )
    {
        self::guard( 'changePassword' );
        $user = eZUser::currentUser();
        $old = self::post( 'old_password', 'string' );
        $new = self::post( 'new_password', 'string' );
        $confirm = self::post( 'confirm_password', 'string', $new );
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( !eZUser::authenticateHash( $user->attribute( 'login' ), $old, $user->site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) )
        {
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'access.user.password.change.failed', array( 'object' => expAuditHook::user( $id ), 'result' => 'refused', 'reason' => 'old_password' ) );
            throw new expServiceException( 'The old password is wrong', 403 );
        }
        if ( $new !== $confirm )
            throw new expServiceException( 'The new password and its confirmation differ', 422 );
        if ( !eZUser::validatePassword( $new ) )
            throw new expServiceException( 'The password is too short', 422 );
        $r = eZUserOperationCollection::password( $id, $new );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The password could not be changed', 422 );
        return self::ok( array( 'changed' => true ) );
    }

    public static function changeEmail( array $a = array() )
    {
        self::guard( 'changeEmail' );
        $user = eZUser::currentUser();
        $password = self::post( 'password', 'string' );
        $email = trim( self::post( 'email', 'string' ) );
        if ( !eZUser::authenticateHash( $user->attribute( 'login' ), $password, $user->site(), $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ) )
            throw new expServiceException( 'The password is wrong', 403 );
        self::validateNewAccount( $user->attribute( 'login' ), $email, null, (int)$user->attribute( 'contentobject_id' ) );
        // a new address takes effect once it is confirmed from the new mailbox (mailpreferences.ini [EmailChangeSettings])
        $change = class_exists( 'expMailAddressChange' ) ? expMailAddressChange::request( $user, $email ) : 'changed';
        if ( $change !== 'changed' )
            return self::ok( array( 'email' => $user->attribute( 'email' ), 'pending' => $change === 'pending_confirmation' ) );
        $user->setAttribute( 'email', $email );
        $user->store();
        return self::ok( array( 'email' => $email ) );
    }

    public static function me( array $a = array() )
    {
        self::guard( 'me' );
        $user = eZUser::currentUser();
        return self::ok( self::exportUser( $user, true ) + array( 'roles' => count( (array)$user->roles() ), 'online' => true ) );
    }

    public static function myRoles( array $a = array() )
    {
        self::guard( 'myRoles' );
        $out = array();
        foreach ( (array)eZUser::currentUser()->roles() as $role )
            $out[] = self::exportRole( $role );
        return self::ok( $out );
    }

    public static function myGroups( array $a = array() )
    {
        self::guard( 'myGroups' );
        $out = array();
        foreach ( eZUser::currentUser()->groups( true ) as $g )
        {
            $n = $g->mainNode();
            if ( $n instanceof eZContentObjectTreeNode )
                $out[] = self::exportGroup( $n );
        }
        return self::ok( $out );
    }

    public static function myLoginInfo( array $a = array() )
    {
        self::guard( 'myLoginInfo' );
        $user = eZUser::currentUser();
        return self::ok( array( 'login_count' => (int)$user->loginCount(), 'last_visit' => self::iso( $user->lastVisit() ), 'failed_login_attempts' => (int)$user->failedLoginAttempts() ) );
    }

    public static function updateMyProfile( array $a = array() )
    {
        self::guard( 'updateMyProfile' );
        $user = eZUser::currentUser();
        $object = $user->contentObject();
        $fields = self::post( 'fields', 'json' );
        if ( !is_array( $fields ) || !$fields )
            throw new expServiceException( 'fields must be a JSON object of attribute identifier => value', 400 );
        if ( !$object->canEdit() && !self::can( 'user', 'selfedit' ) )
            throw new expServiceException( 'You may not edit your profile', 403 );
        $map = $object->dataMap();
        foreach ( $fields as $k => $v )
        {
            if ( !isset( $map[$k] ) )
                throw new expServiceException( "Unknown attribute '$k'", 422 );
            if ( $map[$k]->attribute( 'data_type_string' ) === 'ezuser' )
                throw new expServiceException( 'The account attribute is changed with changePassword and changeEmail', 422 );
            if ( !is_scalar( $v ) )
                throw new expServiceException( "Attribute '$k' needs a text value", 422 );
        }
        if ( !eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $fields ) ) )
            throw new expServiceException( 'The profile could not be updated', 422 );
        return self::ok( array( 'updated' => array_keys( $fields ) ) );
    }

    public static function unactivated( array $a = array() )
    {
        self::guard( 'unactivated' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $db = eZDB::instance();
        $total = (int)$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezuser_accountkey' )[0]['c'];
        $rows = $db->arrayQuery( 'SELECT user_id, time FROM ezuser_accountkey ORDER BY id', array( 'limit' => $limit, 'offset' => $offset ) );
        $out = array();
        foreach ( $rows as $r )
        {
            $u = eZUser::fetch( (int)$r['user_id'] );
            if ( $u instanceof eZUser )
                $out[] = self::exportUser( $u ) + array( 'requested' => self::iso( $r['time'] ) );
        }
        return self::page( $out, $total, $offset, $limit );
    }

    public static function unactivatedCount( array $a = array() )
    {
        self::guard( 'unactivatedCount' );
        return self::ok( array( 'count' => (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezuser_accountkey' )[0]['c'] ) );
    }

    public static function activateUser( array $a = array() )
    {
        self::guard( 'activateUser' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        $r = eZUserOperationCollection::activation( $id, '', true );
        if ( empty( $r['status'] ) )
            throw new expServiceException( 'The account could not be activated', 422 );
        return self::ok( array( 'id' => $id, 'activated' => true ) );
    }

    public static function pendingResets( array $a = array() )
    {
        self::guard( 'pendingResets' );
        return self::ok( array( 'count' => (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezforgot_password' )[0]['c'] ) );
    }

    public static function cancelReset( array $a = array() )
    {
        self::guard( 'cancelReset' );
        $id = self::post( 'id', 'int' );
        self::requireEdit( $id );
        eZForgotPassword::removeByUserID( $id );
        return self::ok( array( 'id' => $id, 'cancelled' => true ) );
    }
}

expAccountServices::$services = expUsersBase::specs( array(
    'registrationOptions' => array( 'Whether registration is open and how it is verified', 'public', 'r', '', 'allowed, verification, rules' ),
    'register' => array( 'Register a new account (POST login, email, password, fields)', 'user/register', 'w', 'login:string,email:string,password:string,fields:json', 'created, needs_activation' ),
    'activationKeyValid' => array( 'Whether an activation key is valid', 'public', 'r', 'key:string', 'valid' ),
    'activate' => array( 'Activate an account with the key from the activation mail (POST key)', 'user/login', 'w', 'key:string', 'activated' ),
    'forgotRequest' => array( 'Mail a password reset link, the same answer for known and unknown addresses (POST email)', 'user/login', 'w', 'email:string', 'requested' ),
    'forgotKeyValid' => array( 'Whether a reset key is valid', 'public', 'r', 'key:string', 'valid' ),
    'reset' => array( 'Set a new password with a reset key (POST key, password)', 'user/login', 'w', 'key:string,password:string', 'reset' ),
    'changePassword' => array( 'Change your password with the old one (POST old_password, new_password, confirm_password)', 'user', 'w', 'old_password:string,new_password:string,confirm_password:string', 'changed' ),
    'changeEmail' => array( 'Change your e-mail address, confirmed with the password (POST password, email)', 'user', 'w', 'password:string,email:string', 'email' ),
    'me' => array( 'Your account: profile data, groups, role count', 'user', 'r', '', 'user' ),
    'myRoles' => array( 'Your roles', 'user', 'r', '', 'roles' ),
    'myGroups' => array( 'Your groups', 'user', 'r', '', 'groups' ),
    'myLoginInfo' => array( 'Your login count, last visit and failed attempts', 'user', 'r', '', 'login info' ),
    'updateMyProfile' => array( 'Update your own profile attributes (POST fields)', 'user', 'w', 'fields:json', 'updated' ),
    'unactivated' => array( 'The accounts waiting for activation, paged', 'role/list', 'r', 'limit:int,offset:int', 'paged users' ),
    'unactivatedCount' => array( 'The number of accounts waiting for activation', 'role/list', 'r', '', 'count' ),
    'activateUser' => array( 'Activate an account by hand (POST id)', 'user', 'w', 'id:int', 'activated' ),
    'pendingResets' => array( 'The number of open password reset requests', 'role/list', 'r', '', 'count' ),
    'cancelReset' => array( 'Cancel the open reset requests of a user (POST id)', 'user', 'w', 'id:int', 'cancelled' ),
) );
