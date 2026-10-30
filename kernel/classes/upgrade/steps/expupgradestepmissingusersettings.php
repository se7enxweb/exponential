<?php
/**
 * File containing the expUpgradeStepMissingUserSettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Accounts without an ezuser_setting row get one, through expUserSettingRepair as
 * update/common/scripts/6.0/createmissingusersettings.php: enabled for a published account without an activation key,
 * disabled otherwise; an account whose object is not published is left alone.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMissingUserSettings extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-missing-user-settings';
    }

    public function description()
    {
        return 'Every account has its ezuser_setting row (update/common/scripts/6.0/createmissingusersettings.php)';
    }

    /** @return array id => 1|0, the rows to create */
    protected function rows()
    {
        return array_filter( expUserSettingRepair::plan( expUserSettingRepair::missing(), true ), function ( $v ) { return $v !== null; } );
    }

    protected function changes( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( $this->rows() as $id => $enabled )
            $lines[] = sprintf( 'user %d: create a%s row', $id, $enabled ? 'n enabled' : ' disabled' );
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $created = expUserSettingRepair::apply( $this->rows() );
        return array( count( $created ) . ' row(s) created' );
    }
}
