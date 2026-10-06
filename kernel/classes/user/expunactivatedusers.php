<?php
/**
 * File containing the expUnactivatedUsers class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The users who registered and never activated their account, for the page user/unactivated: how many there are,
 * a page of them searched by login, e-mail or name and ordered by registration date, login, e-mail or name, the age of
 * each registration, and sending the activation mail again.
 *
 * A user is unactivated when the account is disabled (ezuser_setting.is_enabled = 0) and it has an account key, the
 * key of its activation link (ezuser_account_key). The list and the count use the same condition, so the count says
 * how many the list holds. Removing and activating are only done to such users.
 *
 * The search, the order and the age are plain functions and are tested without a database. resend() takes the
 * function that sends the mail, so a test can catch the mail instead of sending it.
 */
class expUnactivatedUsers
{
    /** The orders the page offers: the name the address carries => the column */
    const SORTS = array( 'time' => 'time', 'login' => 'login', 'email' => 'email', 'name' => 'name' );

    /** A registration this many days old is marked old: its owner is unlikely to activate it */
    const OLD_DAYS = 30;

    /** The translation context of the texts of this class */
    const CONTEXT = 'design/admin/user/unactivated';

    /**
     * The order: one of SORTS, by registration date by default.
     *
     * @param mixed $field
     * @return string
     */
    public static function normaliseSort( $field )
    {
        return is_string( $field ) && array_key_exists( $field, self::SORTS ) ? $field : 'time';
    }

    /**
     * @param mixed $order
     * @return string 'asc' or 'desc'
     */
    public static function normaliseOrder( $order )
    {
        return is_string( $order ) && strtolower( $order ) === 'desc' ? 'desc' : 'asc';
    }

    /**
     * The search as the field and the address carry it: no control characters, no slashes or brackets (the address
     * of a view is split at them), spaces folded, at most 100 characters.
     *
     * @param mixed $search
     * @return string
     */
    public static function normaliseSearch( $search )
    {
        if ( !is_scalar( $search ) )
            return '';
        $search = preg_replace( '#[\x00-\x1F\x7F/()]+#u', ' ', (string)$search );
        if ( $search === null )
            return '';
        $search = trim( preg_replace( '#\s+#u', ' ', $search ) );
        return function_exists( 'mb_substr' ) ? mb_substr( $search, 0, 100, 'UTF-8' ) : substr( $search, 0, 100 );
    }

    /**
     * The ids of a form's selection: positive integers, each once.
     *
     * @param mixed $ids
     * @return int[]
     */
    public static function normaliseIDs( $ids )
    {
        $clean = array();
        foreach ( is_array( $ids ) ? $ids : array( $ids ) as $id )
        {
            if ( is_scalar( $id ) && ctype_digit( (string)$id ) && (int)$id > 0 )
                $clean[(int)$id] = (int)$id;
        }
        return array_values( $clean );
    }

    /**
     * How old a registration is: whole days, whole hours below a day, and whether it counts as old.
     *
     * @param int $time when the account key was made
     * @param int $now
     * @return array days, hours, is_old, unknown (no time stored)
     */
    public static function age( $time, $now )
    {
        $time = (int)$time;
        if ( $time <= 0 )
            return array( 'days' => 0, 'hours' => 0, 'is_old' => false, 'unknown' => true );
        $seconds = max( 0, (int)$now - $time );
        $days = (int)floor( $seconds / 86400 );
        return array( 'days' => $days, 'hours' => (int)floor( $seconds / 3600 ), 'is_old' => $days >= self::OLD_DAYS, 'unknown' => false );
    }

    /**
     * The LIKE pattern of a search, written with ESCAPE '!' (see eZRole::assignmentFilterLikePattern()).
     *
     * @param string $search
     * @return string
     */
    public static function likePattern( $search )
    {
        return '%' . strtr( self::lower( (string)$search ), array( '!' => '!!', '%' => '!%', '_' => '!_' ) ) . '%';
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
    }

    /**
     * The address of the activation link: the site's address (the default siteaccess, not the administration's),
     * then user/activate/<hash>/<main node>.
     *
     * @param string $base the site address without a trailing slash, as eZSys::serverURL() . eZSys::wwwDir() give it
     * @param string $hash
     * @param int $nodeID
     * @return string
     */
    public static function activationURL( $base, $hash, $nodeID )
    {
        return rtrim( (string)$base, '/' ) . '/user/activate/' . rawurlencode( (string)$hash ) . '/' . (int)$nodeID;
    }

    /**
     * A new activation hash: 32 hexadecimal characters from a cryptographic source, as user/register makes them.
     *
     * @return string
     */
    public static function newHash()
    {
        return bin2hex( random_bytes( 16 ) );
    }

    /**
     * The FROM and WHERE of the list and the count.
     *
     * @param eZDBInterface $db
     * @param string $search
     * @return string
     */
    protected static function fromWhere( $db, $search )
    {
        $sql = ' FROM ezuser u
                 INNER JOIN ezuser_accountkey k ON k.user_id = u.contentobject_id
                 INNER JOIN ezuser_setting s ON s.user_id = u.contentobject_id
                 LEFT JOIN ezcontentobject o ON o.id = u.contentobject_id
                 WHERE s.is_enabled = 0';
        if ( $search !== '' )
        {
            $like = "'" . $db->escapeString( self::likePattern( $search ) ) . "'";
            $sql .= " AND ( LOWER( u.login ) LIKE $like ESCAPE '!' OR LOWER( u.email ) LIKE $like ESCAPE '!'"
                  . " OR LOWER( o.name ) LIKE $like ESCAPE '!' )";
        }
        return $sql;
    }

    /**
     * How many users are unactivated, or match the search.
     *
     * @param string $search
     * @return int
     */
    public static function count( $search = '' )
    {
        $db = eZDB::instance();
        $search = self::normaliseSearch( $search );
        if ( $db->databaseName() === 'mongo' )
            return $search === '' ? count( (array)eZUser::fetchUnactivated( false, false, 0 ) ) : 0;
        $rows = $db->arrayQuery( 'SELECT COUNT( DISTINCT u.contentobject_id ) AS c' . self::fromWhere( $db, $search ) );
        return isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
    }

    /**
     * One page of unactivated users: contentobject_id, login, email, name, time (of the newest account key), main
     * node id, and the age (see age()).
     *
     * @param string $search
     * @param string $sort one of SORTS
     * @param string $order asc or desc
     * @param int $limit
     * @param int $offset
     * @param int|null $now
     * @return array
     */
    public static function page( $search, $sort, $order, $limit, $offset, $now = null )
    {
        $db = eZDB::instance();
        $search = self::normaliseSearch( $search );
        $sort = self::normaliseSort( $sort );
        $order = self::normaliseOrder( $order );
        $now = $now === null ? time() : (int)$now;
        $rows = array();
        if ( $db->databaseName() === 'mongo' )
        {
            foreach ( (array)eZUser::fetchUnactivated( array( $sort === 'name' ? 'login' : $sort => $order ), (int)$limit, (int)$offset ) as $user )
            {
                $key = eZUserAccountKey::fetchByUserID( $user->attribute( 'contentobject_id' ) );
                $object = eZContentObject::fetch( $user->attribute( 'contentobject_id' ) );
                $rows[] = array( 'contentobject_id' => (int)$user->attribute( 'contentobject_id' ),
                                 'login' => (string)$user->attribute( 'login' ), 'email' => (string)$user->attribute( 'email' ),
                                 'name' => $object ? (string)$object->attribute( 'name' ) : '',
                                 'time' => $key ? (int)$key->attribute( 'time' ) : 0 );
            }
        }
        else
        {
            $column = array( 'time' => 'time', 'login' => 'LOWER( u.login )', 'email' => 'LOWER( u.email )', 'name' => 'LOWER( o.name )' );
            $sql = 'SELECT u.contentobject_id, u.login, u.email, o.name, MAX( k.time ) AS time' . self::fromWhere( $db, $search )
                 . ' GROUP BY u.contentobject_id, u.login, u.email, o.name'
                 . ' ORDER BY ' . $column[$sort] . ' ' . $order . ', u.contentobject_id ASC';
            $rows = (array)$db->arrayQuery( $sql, array( 'offset' => max( 0, (int)$offset ), 'limit' => max( 1, (int)$limit ) ) );
        }

        $ids = array();
        foreach ( $rows as $row )
            $ids[] = (int)$row['contentobject_id'];
        $mainNodes = array();
        if ( $ids && $db->databaseName() !== 'mongo' )
        {
            $nodes = $db->arrayQuery( 'SELECT contentobject_id, main_node_id FROM ezcontentobject_tree WHERE node_id = main_node_id AND '
                                      . $db->generateSQLINStatement( $ids, 'contentobject_id', false, true, 'int' ) );
            foreach ( (array)$nodes as $node )
                $mainNodes[(int)$node['contentobject_id']] = (int)$node['main_node_id'];
        }
        $page = array();
        foreach ( $rows as $row )
        {
            $id = (int)$row['contentobject_id'];
            $page[] = array( 'contentobject_id' => $id,
                             'login' => (string)$row['login'],
                             'email' => (string)$row['email'],
                             'name' => (string)$row['name'],
                             'time' => (int)$row['time'],
                             'main_node_id' => isset( $mainNodes[$id] ) ? $mainNodes[$id] : 0,
                             'age' => self::age( $row['time'], $now ) );
        }
        return $page;
    }

    /**
     * Whether the user is unactivated: disabled, with an account key.
     *
     * @param int $userID
     * @return bool
     */
    public static function isUnactivated( $userID )
    {
        $userID = (int)$userID;
        if ( $userID <= 0 )
            return false;
        $setting = eZUserSetting::fetch( $userID );
        if ( !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0 )
            return false;
        return eZUserAccountKey::fetchByUserID( $userID ) instanceof eZUserAccountKey;
    }

    /** How many users "Remove all" checks and removes per batch */
    const REMOVE_BATCH = 50;

    /**
     * Whether one user may be removed by "Remove all", decided again at the moment of removal: 'remove', or why not:
     * 'gone' (removed meanwhile), 'protected' (the anonymous user, the administrator account, the user removing),
     * 'activated' (enabled meanwhile, or without an account key).
     *
     * @param array|null $user exists, is_enabled (int), has_key (bool); null when the user no longer exists
     * @param int $userID
     * @param int[] $protected
     * @return string
     */
    public static function removalDecision( $user, $userID, array $protected )
    {
        if ( in_array( (int)$userID, array_map( 'intval', $protected ), true ) )
            return 'protected';
        if ( !is_array( $user ) || empty( $user['exists'] ) )
            return 'gone';
        if ( !isset( $user['is_enabled'] ) || (int)$user['is_enabled'] !== 0 || empty( $user['has_key'] ) )
            return 'activated';
        return 'remove';
    }

    /**
     * Removes every user matched by the candidates, batch by batch, deciding for each user again just before
     * (removalDecision()). The database work is passed in, so the batching and the decisions are tested without one.
     *
     * @param callable $candidates ( int $afterID, int $limit ) => int[] the next ids in ascending order
     * @param callable $state ( int $id ) => array|null see removalDecision()
     * @param callable $remove ( int $id ) => bool
     * @param int[] $protected
     * @param int $batchSize
     * @param callable|null $afterBatch ( int $batchNumber ) called after each batch (caches, time limit)
     * @return array removed (int), skipped (reason => count), batches (int)
     */
    public static function removeAllWith( $candidates, $state, $remove, array $protected, $batchSize = self::REMOVE_BATCH, $afterBatch = null )
    {
        $result = array( 'removed' => 0, 'skipped' => array(), 'batches' => 0 );
        $after = 0;
        $batchSize = max( 1, (int)$batchSize );
        while ( true )
        {
            $ids = array_map( 'intval', (array)call_user_func( $candidates, $after, $batchSize ) );
            if ( !$ids )
                break;
            $result['batches']++;
            foreach ( $ids as $id )
            {
                $after = max( $after, $id );
                $decision = self::removalDecision( call_user_func( $state, $id ), $id, $protected );
                if ( $decision === 'remove' && !call_user_func( $remove, $id ) )
                    $decision = 'failed';
                if ( $decision === 'remove' )
                    $result['removed']++;
                else
                    $result['skipped'][$decision] = isset( $result['skipped'][$decision] ) ? $result['skipped'][$decision] + 1 : 1;
            }
            if ( is_callable( $afterBatch ) )
                call_user_func( $afterBatch, $result['batches'] );
            // an id that did not move the start on would loop for ever
            if ( count( $ids ) < $batchSize )
                break;
        }
        return $result;
    }

    /**
     * The users "Remove all" never removes: the anonymous user (site.ini [UserSettings] AnonymousUserID), the user
     * who removes, and the account with the login "admin".
     *
     * @param int $currentUserID
     * @return int[]
     */
    public static function protectedIDs( $currentUserID )
    {
        $ids = array( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ), (int)$currentUserID );
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            $ids[] = (int)$admin->attribute( 'contentobject_id' );
        return array_values( array_unique( array_filter( $ids ) ) );
    }

    /**
     * Removes every unactivated user, or every one matching $search, in batches of REMOVE_BATCH, each checked again at
     * removal, and records one audit event access.user.remove with the counts (each removed user is recorded by the
     * kernel as well). Not for MongoDB (the candidates are read with SQL): nothing is removed there.
     *
     * @param string $search
     * @param int $currentUserID
     * @return array see removeAllWith(), plus search (bool)
     */
    public static function removeAll( $search, $currentUserID )
    {
        $db = eZDB::instance();
        $search = self::normaliseSearch( $search );
        $empty = array( 'removed' => 0, 'skipped' => array(), 'batches' => 0, 'search' => $search !== '', 'skipped_total' => 0 );
        if ( $db->databaseName() === 'mongo' )
            return $empty;
        $protected = self::protectedIDs( $currentUserID );
        $audit = class_exists( 'expAuditHook' ) ? expAuditHook::begin( 'access.user.remove', array(
            'object' => array( 'type' => 'unactivated_users', 'id' => 0, 'name' => 'unactivated users' ),
            'before' => array( 'search' => $search !== '', 'matching' => self::count( $search ) ) ) ) : null;

        $result = self::removeAllWith(
            function ( $after, $limit ) use ( $db, $search )
            {
                $rows = $db->arrayQuery( 'SELECT DISTINCT u.contentobject_id' . self::fromWhere( $db, $search )
                                         . ' AND u.contentobject_id > ' . (int)$after . ' ORDER BY u.contentobject_id ASC',
                                         array( 'offset' => 0, 'limit' => (int)$limit ) );
                $ids = array();
                foreach ( (array)$rows as $row )
                    $ids[] = (int)$row['contentobject_id'];
                return $ids;
            },
            function ( $id )
            {
                $object = eZContentObject::fetch( $id );
                $setting = eZUserSetting::fetch( $id );
                if ( !$object instanceof eZContentObject || !$setting )
                    return null;
                return array( 'exists' => true, 'is_enabled' => (int)$setting->attribute( 'is_enabled' ),
                              'has_key' => eZUserAccountKey::fetchByUserID( $id ) instanceof eZUserAccountKey );
            },
            function ( $id )
            {
                $object = eZContentObject::fetch( $id );
                if ( !$object instanceof eZContentObject )
                    return false;
                $object->purge();
                eZUserAccountKey::removeByUserID( $id );
                return true;
            },
            $protected,
            self::REMOVE_BATCH,
            function ( $batch )
            {
                // keep memory and time in bounds for thousands of users
                eZContentObject::clearCache();
                if ( function_exists( 'set_time_limit' ) )
                    @set_time_limit( 60 );
            }
        );
        $result['search'] = $search !== '';
        $result['skipped_total'] = array_sum( $result['skipped'] );
        if ( $audit !== null )
            expAuditHook::end( $audit, array( 'after' => array( 'removed' => $result['removed'], 'skipped' => $result['skipped'] ) ) );
        return $result;
    }

    /**
     * Sends the activation mail again, with a new link: the old key is replaced, so the link of the first mail no
     * longer works. Only for an unactivated user with an e-mail address.
     *
     * @param int $userID
     * @param callable|null $sender ( eZMail $mail ) => bool; null sends with eZMailTransport::send()
     * @param string|null $base the site address for the link; null is eZSys::serverURL() . eZSys::wwwDir()
     * @return string 'sent', or why not: 'not_unactivated', 'no_email', 'not_sent'
     */
    public static function resend( $userID, $sender = null, $base = null )
    {
        $userID = (int)$userID;
        if ( !self::isUnactivated( $userID ) )
            return 'not_unactivated';
        $user = eZUser::fetch( $userID );
        $object = eZContentObject::fetch( $userID );
        if ( !$user || !$object )
            return 'not_unactivated';
        $email = trim( (string)$user->attribute( 'email' ) );
        if ( $email === '' || !eZMail::validate( $email ) )
            return 'no_email';

        $hash = self::newHash();
        $db = eZDB::instance();
        $db->begin();
        eZUserAccountKey::removeByUserID( $userID );
        $key = eZUserAccountKey::createNew( $userID, $hash, time() );
        $key->store();
        $db->commit();

        if ( $base === null )
            $base = eZSys::serverURL() . eZSys::wwwDir();
        $ini = eZINI::instance();
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'user', $user );
        $tpl->setVariable( 'object', $object );
        $tpl->setVariable( 'hash', $hash );
        $tpl->setVariable( 'activation_url', self::activationURL( $base, $hash, (int)$object->attribute( 'main_node_id' ) ) );
        $tpl->setVariable( 'site_url', $ini->variable( 'SiteSettings', 'SiteURL' ) );
        $body = $tpl->fetch( 'design:user/unactivated_resend_mail.tpl' );
        $subject = $tpl->hasVariable( 'subject' ) ? (string)$tpl->variable( 'subject' )
                 : ezpI18n::tr( self::CONTEXT, 'Activate your account' );

        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );

        $mail = new eZMail();
        $mail->setSender( $emailSender );
        $mail->setContentType( $ini->variable( 'MailSettings', 'ContentType' ) );
        $mail->setReceiver( $email );
        $mail->setSubject( trim( $subject ) );
        $mail->setBody( $body );
        // The category of the registration mail: essential, the mail gate never holds it back
        $mail->setCategory( 'security' );

        $sent = is_callable( $sender ) ? call_user_func( $sender, $mail ) : eZMailTransport::send( $mail );
        return $sent ? 'sent' : 'not_sent';
    }
}
