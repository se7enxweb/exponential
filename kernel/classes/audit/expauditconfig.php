<?php
/**
 * The audit settings (settings/audit.ini, doc/bc/6.0/audit.md "The settings reference") read once into one array.
 *
 * Every value has the shipped default here too, so a missing or partial audit.ini (an old override, a test)
 * still gives a working configuration. The snapshot is kept per request (REQUEST_TIME_FLOAT), so a persistent
 * worker reads it again for each request; the compiled routing of expAuditTaxonomy is keyed by the snapshot's
 * hash and survives requests as long as the settings are the same.
 *
 * Tests replace the settings with setOverride( array( 'Block/Variable' => value, ... ) ): the INI files are then
 * not read at all, and 'logDir' / 'keyDir' / 'root' may point into a throwaway directory.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditConfig
{
    /** @var array|null Test settings: 'Block/Variable' => value, plus 'logDir', 'keyDir', 'root', 'context' */
    protected static $override = null;

    /** @var array|null The snapshot of this request */
    protected static $snapshot = null;

    /** @var string|null The request the snapshot belongs to */
    protected static $request = null;

    /**
     * The shipped defaults, 'Block/Variable' => value, as in settings/audit.ini.
     *
     * @return array
     */
    public static function defaults()
    {
        return array(
            'AuditSettings/Audit' => 'enabled',
            'AuditSettings/LogDir' => 'log/audit',
            'AuditSettings/AuditFileNames' => array(),
            'AuditSettings/OnWriteFailure' => 'continue',
            'AuditEventSettings/Enabled' => array( 'access.*', 'system.*' ),
            'AuditEventSettings/Disabled' => array( 'access.session.regenerate', 'access.session.expire' ),
            'AuditEventSettings/Branches' => array(),
            'AuditEventSettings/MinSeverity' => 'info',
            'AuditChannelSettings/Channels' => array( 'content', 'access', 'system', 'commerce', 'read' ),
            'AuditChannelSettings/Route' => array( 'content.*' => 'content', 'content.node.view' => 'read',
                                                   'content.search.*' => 'read', 'content.object.download' => 'read',
                                                   'access.*' => 'access', 'system.*' => 'system',
                                                   'commerce.*' => 'commerce', 'data.*' => 'commerce' ),
            'AuditChannelSettings/DefaultChannel' => 'system',
            'AuditRotationSettings/MaxFileSize' => '64M',
            'AuditRecordSettings/BeforeAfter' => 'enabled',
            'AuditRecordSettings/MaxValueLength' => '512',
            'AuditRecordSettings/ChildDepth' => '3',
            'AuditRecordSettings/MaxChildren' => '10000',
            'AuditRecordSettings/RequestContext' => 'enabled',
            'AuditRecordSettings/RequestIdHeader' => 'X-Exp-Request-Id',
            'AuditRecordSettings/TrustedRequestIdHeader' => '',
            'AuditRecordSettings/MaxLineBytes' => '262144',
            'AuditPrivacySettings/Field' => array( 'actor.login' => 'full', 'actor.ip' => 'truncate', 'actor.ua' => 'truncate',
                                                   'actor.session' => 'hash', 'actor.cli.os_user' => 'full',
                                                   'request.url' => 'truncate', 'request.host' => 'full',
                                                   'object.name' => 'full', 'email' => 'hash', 'attempted_login' => 'hash' ),
            'AuditPrivacySettings/IPv4Prefix' => '24',
            'AuditPrivacySettings/IPv6Prefix' => '48',
            'AuditPrivacySettings/SecretPathViews' => array( 'user/activate', 'user/forgotpassword', 'userpaex/forgotpassword' ),
            'AuditPrivacySettings/NeverRecord' => array( 'HashKey', 'Hash', 'Password', 'PasswordConfirm', 'password_hash', 'ezxform_token' ),
            'AuditBufferSettings/Buffering' => 'enabled',
            'AuditBufferSettings/ImmediateEvents' => array( 'access.*', 'system.audit.*', 'system.setting.write' ),
            'AuditBufferSettings/MaxEvents' => '500',
            'AuditBufferSettings/MaxBytes' => '1M',
            'AuditBufferSettings/FlushInterval' => '5',
            'AuditChainSettings/Algorithm' => 'sha256',
            'AuditChainSettings/Checkpoints' => 'enabled',
            'AuditKeySettings/GenerateKeys' => 'enabled',
            'AuditReadSettings/Reads' => 'disabled',
            'AuditReadSettings/SampleRate' => '0.01',
            'AuditCompatSettings/Map' => array(
                'user-login' => 'access.session.login',
                'user-failed-login' => 'access.session.login.failed',
                'content-delete' => 'content.node.remove.trash',
                'content-move' => 'content.node.move',
                'content-hide' => 'content.node.hide',
                'role-change' => 'access.role.change',
                'role-assign' => 'access.role.assign',
                'section-assign' => 'content.node.section',
                'state-assign' => 'content.object.state',
                'order-delete' => 'commerce.order.delete',
                'user-password-change' => 'access.user.password.change',
                'user-password-change-self' => 'access.user.password.change',
                'user-password-change-self-fail' => 'access.user.password.change.failed',
                'user-forgotpassword' => 'access.user.password.reset',
                'user-forgotpassword-fail' => 'access.user.password.reset.failed',
            ),
            'AuditCompatSettings/UnmappedAsLegacy' => 'enabled',
            'AuditCompatSettings/LegacyFiles' => 'disabled',
        );
    }

    /**
     * Replaces the settings (tests). null goes back to the INI files.
     *
     * @param array|null $settings 'Block/Variable' => value; 'logDir' (absolute), 'keyDir' (absolute directory
     *                             of the key file), 'context' (fixed request and actor fields)
     */
    public static function setOverride( ?array $settings = null )
    {
        self::$override = $settings;
        self::$snapshot = null;
        self::$request = null;
    }

    /** @return bool Settings come from setOverride() */
    public static function isOverridden()
    {
        return self::$override !== null;
    }

    /** Forgets the snapshot (the next get() reads the settings again). */
    public static function reset()
    {
        self::$snapshot = null;
        self::$request = null;
    }

    /**
     * The settings of this request.
     *
     * @return array
     */
    public static function get()
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( self::$snapshot !== null && self::$request === $request )
            return self::$snapshot;
        self::$request = $request;
        return self::$snapshot = self::build( self::raw() );
    }

    /** @return string The installation root, with a trailing slash */
    public static function root()
    {
        if ( self::$override !== null && isset( self::$override['root'] ) )
            return rtrim( self::$override['root'], '/' ) . '/';
        return dirname( __DIR__, 3 ) . '/';
    }

    /**
     * The raw values, 'Block/Variable' => value: defaults, then audit.ini as eZINI merges it (or the override).
     *
     * @return array
     */
    protected static function raw()
    {
        $values = self::defaults();
        if ( self::$override !== null )
        {
            foreach ( self::$override as $k => $v )
                $values[$k] = $v;
            return $values;
        }
        if ( !class_exists( 'eZINI' ) )
            return $values;
        try
        {
            $ini = eZINI::instance( 'audit.ini' );
            foreach ( array_keys( $values ) as $key )
            {
                list( $block, $var ) = explode( '/', $key, 2 );
                if ( $ini->hasVariable( $block, $var ) )
                    $values[$key] = $ini->variable( $block, $var );
            }
            // the per-channel blocks
            $channels = (array)$values['AuditChannelSettings/Channels'];
            foreach ( $channels as $channel )
            {
                if ( $ini->hasVariable( 'AuditChannel_' . $channel, 'MaxFileSize' ) )
                    $values['AuditChannel_' . $channel . '/MaxFileSize'] = $ini->variable( 'AuditChannel_' . $channel, 'MaxFileSize' );
            }
            foreach ( array( 'InstallationID', 'ActiveSigningKey', 'SigningKey', 'PseudonymKey' ) as $var )
            {
                if ( $ini->hasVariable( 'AuditKeySettings', $var ) )
                    $values['AuditKeySettings/' . $var] = $ini->variable( 'AuditKeySettings', $var );
            }
            $values['varDir'] = eZINI::instance()->variable( 'FileSettings', 'VarDir' );
        }
        catch ( Throwable $e )
        {
        }
        return $values;
    }

    /**
     * The snapshot: typed, with the paths resolved.
     *
     * @param array $v raw values
     * @return array
     */
    protected static function build( array $v )
    {
        $list = function ( $x ) { return array_values( array_filter( array_map( 'trim', (array)$x ), 'strlen' ) ); };
        $hash = function ( $x ) {
            $out = array();
            foreach ( (array)$x as $k => $val )
                if ( is_string( $k ) && trim( $k ) !== '' )
                    $out[trim( $k )] = is_string( $val ) ? trim( $val ) : $val;
            return $out;
        };
        $on = function ( $x ) { return strtolower( trim( (string)$x ) ) === 'enabled'; };
        $root = self::root();

        if ( isset( $v['logDir'] ) )
            $logDir = rtrim( $v['logDir'], '/' );
        else
        {
            $logDir = trim( (string)$v['AuditSettings/LogDir'] ) !== '' ? trim( $v['AuditSettings/LogDir'] ) : 'log/audit';
            if ( $logDir[0] !== '/' )
            {
                $varDir = isset( $v['varDir'] ) && trim( $v['varDir'] ) !== '' ? trim( $v['varDir'], '/ ' ) : 'var';
                $logDir = $root . $varDir . '/' . $logDir;
            }
            $logDir = rtrim( $logDir, '/' );
        }

        $channels = $list( $v['AuditChannelSettings/Channels'] );
        $maxFileSize = array();
        foreach ( $channels as $c )
        {
            $raw = isset( $v['AuditChannel_' . $c . '/MaxFileSize'] ) ? $v['AuditChannel_' . $c . '/MaxFileSize'] : $v['AuditRotationSettings/MaxFileSize'];
            $maxFileSize[$c] = self::bytes( $raw, 64 * 1024 * 1024 );
        }

        $signing = array();
        foreach ( (array)( isset( $v['AuditKeySettings/SigningKey'] ) ? $v['AuditKeySettings/SigningKey'] : array() ) as $id => $key )
            if ( is_string( $id ) && $id !== '' && (string)$key !== '' )
                $signing[$id] = (string)$key;

        $snapshot = array(
            'enabled' => $on( $v['AuditSettings/Audit'] ),
            'logDir' => $logDir,
            'keyDir' => isset( $v['keyDir'] ) ? rtrim( $v['keyDir'], '/' ) : $root . 'settings/override',
            'auditFileNames' => $hash( $v['AuditSettings/AuditFileNames'] ),
            'onWriteFailure' => strtolower( trim( (string)$v['AuditSettings/OnWriteFailure'] ) ) === 'refuse' ? 'refuse' : 'continue',
            'enabledPatterns' => $list( $v['AuditEventSettings/Enabled'] ),
            'disabledPatterns' => $list( $v['AuditEventSettings/Disabled'] ),
            'branches' => $hash( $v['AuditEventSettings/Branches'] ),
            'minSeverity' => strtolower( trim( (string)$v['AuditEventSettings/MinSeverity'] ) ),
            'channels' => $channels,
            'routes' => $hash( $v['AuditChannelSettings/Route'] ),
            'defaultChannel' => trim( (string)$v['AuditChannelSettings/DefaultChannel'] ) ?: 'system',
            'maxFileSize' => $maxFileSize,
            'beforeAfter' => strtolower( trim( (string)$v['AuditRecordSettings/BeforeAfter'] ) ),
            'maxValueLength' => max( 16, (int)$v['AuditRecordSettings/MaxValueLength'] ),
            'childDepth' => max( 0, (int)$v['AuditRecordSettings/ChildDepth'] ),
            'maxChildren' => max( 0, (int)$v['AuditRecordSettings/MaxChildren'] ),
            'requestContext' => $on( $v['AuditRecordSettings/RequestContext'] ),
            'requestIdHeader' => trim( (string)$v['AuditRecordSettings/RequestIdHeader'] ),
            'trustedRequestIdHeader' => trim( (string)$v['AuditRecordSettings/TrustedRequestIdHeader'] ),
            'maxLineBytes' => max( 4096, self::bytes( $v['AuditRecordSettings/MaxLineBytes'], 262144 ) ),
            'privacy' => $hash( $v['AuditPrivacySettings/Field'] ),
            'ipv4Prefix' => min( 32, max( 0, (int)$v['AuditPrivacySettings/IPv4Prefix'] ) ),
            'ipv6Prefix' => min( 128, max( 0, (int)$v['AuditPrivacySettings/IPv6Prefix'] ) ),
            'secretPathViews' => $list( $v['AuditPrivacySettings/SecretPathViews'] ),
            'neverRecord' => $list( $v['AuditPrivacySettings/NeverRecord'] ),
            'buffering' => $on( $v['AuditBufferSettings/Buffering'] ),
            'immediate' => $list( $v['AuditBufferSettings/ImmediateEvents'] ),
            'maxEvents' => max( 1, (int)$v['AuditBufferSettings/MaxEvents'] ),
            'maxBytes' => max( 1024, self::bytes( $v['AuditBufferSettings/MaxBytes'], 1048576 ) ),
            'flushInterval' => max( 0, (int)$v['AuditBufferSettings/FlushInterval'] ),
            'algorithm' => in_array( strtolower( trim( (string)$v['AuditChainSettings/Algorithm'] ) ), array( 'sha256', 'sha512/256', 'sha3-256' ), true )
                           ? strtolower( trim( (string)$v['AuditChainSettings/Algorithm'] ) ) : 'sha256',
            'checkpoints' => $on( $v['AuditChainSettings/Checkpoints'] ),
            'generateKeys' => $on( $v['AuditKeySettings/GenerateKeys'] ),
            'keys' => array(
                'installation' => isset( $v['AuditKeySettings/InstallationID'] ) ? trim( (string)$v['AuditKeySettings/InstallationID'] ) : '',
                'active' => isset( $v['AuditKeySettings/ActiveSigningKey'] ) ? trim( (string)$v['AuditKeySettings/ActiveSigningKey'] ) : '',
                'signing' => $signing,
                'pseudonym' => isset( $v['AuditKeySettings/PseudonymKey'] ) ? trim( (string)$v['AuditKeySettings/PseudonymKey'] ) : '',
            ),
            'reads' => $on( $v['AuditReadSettings/Reads'] ),
            'sampleRate' => min( 1.0, max( 0.0, (float)$v['AuditReadSettings/SampleRate'] ) ),
            'compatMap' => $hash( $v['AuditCompatSettings/Map'] ),
            'unmappedAsLegacy' => $on( $v['AuditCompatSettings/UnmappedAsLegacy'] ),
            'legacyFiles' => $on( $v['AuditCompatSettings/LegacyFiles'] ),
            'context' => isset( $v['context'] ) && is_array( $v['context'] ) ? $v['context'] : null,
            // tests: false keeps eZExecution's handlers and the shutdown function out of the test process
            'handlers' => isset( $v['handlers'] ) ? (bool)$v['handlers'] : true,
        );
        $snapshot['hash'] = md5( serialize( $snapshot ) );
        return $snapshot;
    }

    /**
     * "64M", "1K", "262144" in bytes.
     *
     * @param string|int $value
     * @param int $default
     * @return int
     */
    public static function bytes( $value, $default )
    {
        $value = strtoupper( trim( (string)$value ) );
        if ( !preg_match( '/^(\d+)\s*([KMG]?)B?$/', $value, $m ) )
            return $default;
        $n = (int)$m[1];
        switch ( $m[2] )
        {
            case 'G': return $n * 1073741824;
            case 'M': return $n * 1048576;
            case 'K': return $n * 1024;
        }
        return $n;
    }
}
