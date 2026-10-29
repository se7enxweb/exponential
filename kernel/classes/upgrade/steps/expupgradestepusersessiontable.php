<?php
/**
 * File containing the expUpgradeStepUserSessionTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The signed-in sessions of the users, "Your devices" (expuser_session), as
 * update/common/scripts/6.0/createusersessiontable.php.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepUserSessionTable extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-user-session-table';
    }

    public function description()
    {
        return 'The signed-in sessions of the users, Your devices (table expuser_session)';
    }

    public function tables()
    {
        return array( expUserSession::TABLE );
    }
}
