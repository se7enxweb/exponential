<?php
/**
 * File containing the expUpgradeTableInstallerStep class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A table step whose feature has its own installer class (the one its update/common/scripts/6.0/create*.php script
 * calls): detection and the dry run's SQL come from the kernel schema like every schema step, apply() calls the
 * feature's installer, so whatever it does beyond the table (a full-text index, a cache reset) happens too.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expUpgradeTableInstallerStep extends expUpgradeSchemaStep
{
    /**
     * Runs the feature's installer.
     *
     * @param eZDBInterface $db
     * @param string[] $messages
     * @return bool whether the tables exist afterwards
     */
    abstract protected function install( $db, array &$messages );

    public function apply( expUpgradeContext $context )
    {
        $messages = array();
        $ok = $this->install( $context->db(), $messages );
        expKernelTableSchema::reset();
        if ( !$ok )
            throw new RuntimeException( $messages ? implode( '; ', $messages ) : 'the installer did not create ' . implode( ', ', $this->tables() ) );
        $this->afterApply( $context );
        return $messages ? $messages : array( 'created ' . implode( ', ', $this->tables() ) );
    }
}
