<?php
/**
 * File containing the expSysCardSettingsProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exp:info: the INI settings of an allowlist (syscard.ini [Inventory] Settings[]=<file>/<block>/<name>). Only those
 * are shown, and never one whose name looks like a secret (password, key, token, secret, user ...), even when the
 * allowlist names it.
 */
class expSysCardSettingsProvider extends expSysCardBaseProvider
{
    protected $id = 'settings';
    protected $section = 'settings';
    protected $modes = array( expSysCardContext::MODE_INFO );
    protected $priority = 12;
    protected $budget = 1.0;

    /** Setting names that are never shown */
    const SECRET = '/(pass|secret|token|key|salt|user|login|credential|auth|cookie|session|dsn|private)/i';

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        foreach ( (array)$context->ini( 'syscard.ini', 'Inventory', 'Settings', array() ) as $entry )
        {
            $parts = explode( '/', trim( (string)$entry ) );
            if ( count( $parts ) !== 3 || preg_match( self::SECRET, $parts[2] ) || preg_match( self::SECRET, $parts[1] ) )
                continue;
            if ( class_exists( 'expSystemReportMask' ) && expSystemReportMask::isSecretSettingName( $parts[2] ) )
                continue;
            $value = $context->ini( $parts[0], $parts[1], $parts[2], null );
            if ( $value === null )
                continue;
            if ( is_array( $value ) )
                $value = implode( ', ', array_filter( array_map( 'strval', $value ), 'strlen' ) );
            $facts[] = $this->fact( 'ini:' . $parts[0] . '/' . $parts[1] . '/' . $parts[2], $parts[1] . '/' . $parts[2], $value === '' ? '(empty)' : $value );
        }
        return $facts;
    }
}
