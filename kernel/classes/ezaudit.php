<?php
/**
 * File containing the eZAudit class.
 *
 * The 4.x audit API, kept so every existing call keeps working: eZAudit::writeAudit( $name, $attributes ) is
 * recorded by expAudit (kernel/classes/audit/) as the new taxonomy name ([AuditCompatSettings] Map[] of
 * audit.ini, or system.legacy.<name>), in the hash-chained JSON lines channels. Guide: doc/bc/6.0/audit.md
 * ("The eZAudit::writeAudit() compatibility path").
 *
 * The results are no longer cached in $GLOBALS: a persistent Velocity worker kept them across requests, so a
 * changed audit.ini was not seen until the worker restarted.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZAudit
{
    const DEFAULT_LOG_DIR = 'log/audit';

    /**
     * Returns an associative array of all names of audit and the log files used by this class,
     * Will be fetched from ini settings. The old file names are aliases of the new names (filters by
     * old file name resolve through the mapping).
     *
     * @return array
     */
    static function fetchAuditNameSettings()
    {
        $ini = eZINI::instance( 'audit.ini' );

        $auditNames = $ini->hasVariable( 'AuditSettings', 'AuditFileNames' )
                      ? $ini->variable( 'AuditSettings', 'AuditFileNames' )
                      : array();
        // [AuditSettings] LogDir inside VarDir, or as it is when absolute: the directory expAudit writes its records
        // to (expAuditConfig::path()). The audit trail stays with the storage of the site; site.ini [FileSettings]
        // LogDir, LogVarDir and UseGlobalLogDir do not move it.
        $logDir = expAuditConfig::path( $ini->hasVariable( 'AuditSettings', 'LogDir' ) ? $ini->variable( 'AuditSettings', 'LogDir' ) : self::DEFAULT_LOG_DIR );

        $resultArray = array();
        foreach ( array_keys( $auditNames ) as $auditNameKey )
        {
            $auditNameValue = $auditNames[$auditNameKey];
            $resultArray[$auditNameKey] = array( 'dir' => $logDir,
                                                 'file_name' => $auditNameValue );
        }
        return $resultArray;
    }

    /**
     * Records $auditName with $auditAttributes: the 4.x name is mapped to its taxonomy name and written by
     * expAudit. Attributes on the deny list (HashKey, Password, ...) and secrets are never recorded.
     *
     * @param string $auditName
     * @param array $auditAttributes
     * @return bool true when recorded
     */
    static function writeAudit( $auditName, $auditAttributes = array() )
    {
        if ( !class_exists( 'expAudit' ) )
            return false;
        return expAudit::legacy( $auditName, $auditAttributes ) !== null;
    }

    /**
     * Writes the old text file of a 4.x name (when [AuditCompatSettings] LegacyFiles=enabled), in the old format
     * and with the old rotation, outside the hash chain. Attributes on the deny list are left out here too.
     *
     * @param string $auditName
     * @param array $auditAttributes
     * @return bool
     */
    static function writeLegacyFile( $auditName, $auditAttributes = array() )
    {
        $auditNameSettings = eZAudit::auditNameSettings();
        if ( !isset( $auditNameSettings[$auditName] ) )
            return false;

        $ip = eZSys::clientIP();
        if ( !$ip )
            $ip = eZSys::serverVariable( 'HOSTNAME', true );

        $user = eZUser::currentUser();
        $userID = $user->attribute( 'contentobject_id' );
        $userLogin = $user->attribute( 'login' );

        $message = "[$ip] [$userLogin:$userID]\n";

        $never = eZINI::instance( 'audit.ini' )->hasVariable( 'AuditPrivacySettings', 'NeverRecord' )
                 ? (array)eZINI::instance( 'audit.ini' )->variable( 'AuditPrivacySettings', 'NeverRecord' ) : array();
        foreach ( (array)$auditAttributes as $attributeKey => $attributeValue )
        {
            if ( class_exists( 'expAuditPrivacy' ) && ( expAuditPrivacy::isNeverRecorded( (string)$attributeKey, $never ) || expAuditPrivacy::isSecretName( (string)$attributeKey ) ) )
                continue;
            if ( is_array( $attributeValue ) )
                $attributeValue = implode( ',', $attributeValue );
            $message .= "$attributeKey: $attributeValue\n";
        }

        eZLog::write( $message, $auditNameSettings[$auditName]['file_name'], $auditNameSettings[$auditName]['dir'] );
        return true;
    }

    /**
     * Returns true if audit should be enabled ([AuditSettings] Audit=enabled, the shipped default).
     *
     * @return boolean
     */
    static function isAuditEnabled()
    {
        if ( class_exists( 'expAudit' ) )
            return expAudit::isEnabled();
        return eZAudit::fetchAuditEnabled();
    }

    /**
     * Returns true if audit should be enabled.
     * Will fetch from ini setting.
     *
     * @return bool
     */
    static function fetchAuditEnabled()
    {
        $ini = eZINI::instance( 'audit.ini' );
        $auditEnabled = $ini->hasVariable( 'AuditSettings', 'Audit' )
                      ? $ini->variable( 'AuditSettings', 'Audit' )
                      : 'disabled';
        $enabled = $auditEnabled == 'enabled';
        return $enabled;
    }

    /**
     * Returns an associative array of all names of audit and the log files used by this class
     *
     * @return array
     */
    static function auditNameSettings()
    {
        return eZAudit::fetchAuditNameSettings();
    }
}
?>
