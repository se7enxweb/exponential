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
 * update/common/scripts/6.0/createusersessiontable.php, and what is known of each session's device
 * (expuser_session_info), which the step left out before.
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
        return 'The signed-in sessions of the users, Your devices (tables expuser_session, expuser_session_info)';
    }

    public function tables()
    {
        return array( expUserSession::TABLE, expUserSessionInfo::TABLE );
    }
}
