<?php
/**
 * File containing the expUpgradeStepAuditIndexTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The audit index tables (expaudit_event, expaudit_cursor, expaudit_file) and their full-text part, through
 * expAuditIndexSchema::install(), as update/common/scripts/6.0/createaudittables.php does. Filling them from the
 * audit files is step 6.0.15-audit-index-build.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepAuditIndexTables extends expUpgradeTableInstallerStep
{
    public function id()
    {
        return '6.0.15-audit-index-tables';
    }

    public function description()
    {
        return 'The audit index: the tables expaudit_event, expaudit_cursor, expaudit_file and the full-text index';
    }

    public function tables()
    {
        return array( 'expaudit_cursor', 'expaudit_event', 'expaudit_file' );
    }

    public function detect( expUpgradeContext $context )
    {
        $missing = expAuditIndexSchema::missingTables( $context->db() );
        if ( $missing === null )
            return self::effect( self::UNKNOWN, 'cannot tell from the database catalogue' );
        return $missing ? self::effect( self::MISSING, 'missing: table ' . implode( ', table ', $missing ) )
                        : self::effect( self::PRESENT, 'in place: ' . implode( ', ', $this->tables() ) );
    }

    protected function install( $db, array &$messages )
    {
        return expAuditIndexSchema::install( $db, $messages );
    }
}
