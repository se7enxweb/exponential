<?php
/**
 * The settings of the audit index and console (audit.ini [AuditIndexSettings], [AuditConsoleSettings] and
 * [AuditPrivacySettings] PseudonymiseAfterDays), read once per request like expAuditConfig, with the shipped
 * defaults when a value is missing. Tests replace them with setOverride().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditIndexSettings
{
    /** @var array|null */
    protected static $override = null;

    /** @var array|null */
    protected static $snapshot = null;

    /** @var string|null */
    protected static $request = null;

    /** @return array 'Block/Variable' => default */
    public static function defaults()
    {
        return array(
            'AuditIndexSettings/Index' => 'enabled',
            'AuditIndexSettings/BatchSize' => '2000',
            'AuditIndexSettings/IndexReads' => 'disabled',
            'AuditIndexSettings/FullText' => 'enabled',
            'AuditIndexSettings/KeepDays' => '730',
            'AuditConsoleSettings/PageSize' => '50',
            'AuditConsoleSettings/ReauthForManage' => 'disabled',
            'AuditConsoleSettings/ReauthMinutes' => '10',
            'AuditConsoleSettings/MaxExportRecords' => '100000',
            'AuditPrivacySettings/PseudonymiseAfterDays' => '90',
        );
    }

    /** @param array|null $settings 'Block/Variable' => value; null reads audit.ini again */
    public static function setOverride( ?array $settings = null )
    {
        self::$override = $settings;
        self::$snapshot = null;
    }

    /**
     * @return array index (bool), batchSize, indexReads (bool), fullText (bool), keepDays, pageSize, reauth (bool),
     *               reauthMinutes, maxExport, pseudonymiseAfterDays
     */
    public static function get()
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( self::$snapshot !== null && self::$request === $request )
            return self::$snapshot;
        self::$request = $request;

        $v = self::defaults();
        if ( self::$override !== null )
            $v = array_merge( $v, self::$override );
        elseif ( class_exists( 'eZINI' ) )
        {
            try
            {
                $ini = eZINI::instance( 'audit.ini' );
                foreach ( array_keys( $v ) as $key )
                {
                    list( $block, $var ) = explode( '/', $key, 2 );
                    if ( $ini->hasVariable( $block, $var ) )
                        $v[$key] = $ini->variable( $block, $var );
                }
            }
            catch ( Throwable $e )
            {
            }
        }
        $on = function ( $x ) { return strtolower( trim( (string)$x ) ) === 'enabled'; };
        return self::$snapshot = array(
            'index' => $on( $v['AuditIndexSettings/Index'] ),
            'batchSize' => max( 10, (int)$v['AuditIndexSettings/BatchSize'] ),
            'indexReads' => $on( $v['AuditIndexSettings/IndexReads'] ),
            'fullText' => $on( $v['AuditIndexSettings/FullText'] ),
            'keepDays' => max( 1, (int)$v['AuditIndexSettings/KeepDays'] ),
            'pageSize' => min( 500, max( 5, (int)$v['AuditConsoleSettings/PageSize'] ) ),
            'reauth' => $on( $v['AuditConsoleSettings/ReauthForManage'] ),
            'reauthMinutes' => max( 1, (int)$v['AuditConsoleSettings/ReauthMinutes'] ),
            'maxExport' => max( 1, (int)$v['AuditConsoleSettings/MaxExportRecords'] ),
            'pseudonymiseAfterDays' => max( 1, (int)$v['AuditPrivacySettings/PseudonymiseAfterDays'] ),
        );
    }
}
