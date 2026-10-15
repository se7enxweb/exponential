<?php
/**
 * The code of kernel/user/hygiene.php. The file kernel/user/hygiene.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/hygiene, "Check user accounts": the user accounts of the installation, or those below a user group, narrowed
 * by one filter and a search (GET filter, days, group, q), ordered (sort, order) and paged (limit, (offset)); see
 * expUserHygiene. Three bulk actions on the ticked accounts (UserIDArray[]): DisableButton, EnableButton and
 * UnlockButton show a confirmation page with the counts, whose ConfirmButton (HygieneAction) does the action, each
 * account decided again just before (expUserHygiene::decide()). ExportButton sends the CSV of the whole filtered list.
 * With site.ini [UserSettings] DeactivateAndMove=enabled and the function user/deactivate, two more buttons send the
 * ticked accounts to user/deactivate (Deactivate and move, Reactivate and move back), which confirms and does them.
 *
 * Every change and the CSV take a POST (with the form token where ezformtoken is active). The view opens with the
 * function user/hygiene; each action also needs the right to edit the account's object, and never touches the
 * anonymous user, the administrator account or the user who acts. E-mail addresses are shown, searched, sorted
 * and exported only for a user who may assign roles (role/assign), as the E-mail column of the sub-items table.
 * Guide: doc/features/6.0/user-hygiene.md
 */
class Hygiene extends \Exponential\Runnable\ModuleView
{
    /** The most accounts one action takes: the largest page */
    const MAX_IDS = 100;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $tpl = \eZTemplate::factory();
        $currentUser = \eZUser::currentUser();
        $canSeeEmail = self::canSeeEmail( $currentUser );

        $filters = \expUserHygiene::normaliseFilters( $_GET, $canSeeEmail );
        $groupPath = null;
        if ( $filters['group'] > 0 )
        {
            $groupNode = \eZContentObjectTreeNode::fetch( $filters['group'] );
            $groupPath = $groupNode instanceof \eZContentObjectTreeNode ? \expUserHygiene::normalisePath( $groupNode->attribute( 'path_string' ) ) : null;
            if ( $groupPath === null )
                $filters['group'] = 0;
        }
        $query = \expUserHygiene::queryString( $filters );
        $hygiene = \expUserHygiene::fromDatabase();

        $isPost = \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';
        if ( $hygiene !== null && $isPost && $http->hasPostVariable( 'ExportButton' ) )
            return $this->export( $hygiene, $filters, $canSeeEmail, $groupPath );

        $action = null;
        $confirmed = false;
        if ( $isPost )
        {
            foreach ( array( 'DisableButton' => 'disable', 'EnableButton' => 'enable', 'UnlockButton' => 'unlock' ) as $button => $name )
            {
                if ( $http->hasPostVariable( $button ) )
                    $action = $name;
            }
            if ( $http->hasPostVariable( 'ConfirmButton' ) && $http->hasPostVariable( 'HygieneAction' ) )
            {
                $action = \expUserHygiene::normaliseAction( $http->postVariable( 'HygieneAction' ) );
                $confirmed = $action !== null;
            }
        }
        $ids = $http->hasPostVariable( 'UserIDArray' ) ? \expUnactivatedUsers::normaliseIDs( $http->postVariable( 'UserIDArray' ) ) : array();
        $ids = array_slice( $ids, 0, self::MAX_IDS );

        $Result = array();
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => \ezpI18n::tr( \expUserHygiene::CONTEXT, 'Check user accounts' ), 'url' => 'user/hygiene' ),
        );
        $tpl->setVariable( 'hygiene_filters', $filters );
        $tpl->setVariable( 'hygiene_query', $query );
        $links = \expUserHygiene::links( $filters, $canSeeEmail );
        $tpl->setVariable( 'hygiene_filter_queries', $links['filters'] );
        $tpl->setVariable( 'hygiene_sort_queries', $links['sorts'] );
        $tpl->setVariable( 'hygiene_clear_query', $links['clear'] );
        $tpl->setVariable( 'hygiene_can_see_email', $canSeeEmail );
        $tpl->setVariable( 'hygiene_supported', $hygiene !== null );
        // Deactivate and move (user/deactivate, expUserDeactivation): offered when switched on and allowed; the
        // group's node marks the accounts already moved there
        $deactivate = \expUserDeactivation::isEnabled() && self::canDeactivate( $currentUser );
        $deactivateGroup = $deactivate ? \expUserDeactivation::groupNode() : null;
        $tpl->setVariable( 'hygiene_deactivate', $deactivate );
        $tpl->setVariable( 'hygiene_deactivate_group_id', $deactivateGroup ? (int)$deactivateGroup->attribute( 'node_id' ) : 0 );

        if ( $hygiene !== null && $action !== null )
        {
            if ( !$ids )
            {
                $tpl->setVariable( 'hygiene_nothing_selected', true );
            }
            else
            {
                $protected = \expUserHygiene::protectedIDs( $currentUser->attribute( 'contentobject_id' ) );
                $result = \expUserHygiene::applyWith( $action, $ids, array( 'expUserHygiene', 'accountState' ),
                                                      array( 'expUserHygiene', 'perform' ), $protected, !$confirmed );
                if ( !$confirmed )
                {
                    $tpl->setVariable( 'hygiene_confirm', array( 'action' => $action, 'ids' => $ids, 'result' => $result,
                                                         'names' => self::names( array_slice( $result['done'], 0, 20 ) ) ) );
                    $Result['content'] = $tpl->fetch( 'design:user/hygiene.tpl' );
                    return $this->viewResult( $Result, $Result );
                }
                if ( $result['done'] )
                    \eZContentObject::clearCache( $result['done'] );
                $tpl->setVariable( 'hygiene_action_result', array( 'action' => $action, 'result' => $result ) );
            }
        }

        $offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $count = 0;
        $accounts = array();
        $filterCounts = array();
        $groups = array();
        $accountless = 0;
        if ( $hygiene !== null )
        {
            $count = $hygiene->count( $filters, $canSeeEmail, $groupPath );
            if ( $offset >= $count )
                $offset = $count > 0 ? (int)( floor( ( $count - 1 ) / $filters['limit'] ) * $filters['limit'] ) : 0;
            $accounts = $count > 0 ? $hygiene->page( $filters, $canSeeEmail, $groupPath, $offset ) : array();
            foreach ( \expUserHygiene::filters( $canSeeEmail ) as $filter )
                $filterCounts[$filter] = $filter === $filters['filter'] ? $count : $hygiene->count( array( 'filter' => $filter ) + $filters, $canSeeEmail, $groupPath );
            $groups = $hygiene->groups( (int)\eZINI::instance()->variable( 'UserSettings', 'UserGroupClassID' ) );
            $accountless = $hygiene->accountlessObjectCount();
        }

        $protected = \expUserHygiene::protectedIDs( $currentUser->attribute( 'contentobject_id' ) );
        $tpl->setVariable( 'hygiene_accounts', $accounts );
        $tpl->setVariable( 'hygiene_account_count', $count );
        $tpl->setVariable( 'hygiene_filter_counts', $filterCounts );
        $tpl->setVariable( 'hygiene_groups', $groups );
        $tpl->setVariable( 'hygiene_accountless_objects', $accountless );
        $tpl->setVariable( 'hygiene_protected_ids', $protected );
        $tpl->setVariable( 'hygiene_max_failed_logins', (int)\eZUser::maxNumberOfFailedLogin() );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $tpl->setVariable( 'module', $Module );
        $Result['content'] = $tpl->fetch( 'design:user/hygiene.tpl' );
        return $this->viewResult( $Result, $Result );
    }

    /**
     * Whether the user may see e-mail addresses on the page and in the CSV: role/assign, as the E-mail column of the
     * sub-items table (settings/subitemscolumns.ini [Column_useremail]).
     *
     * @param \eZUser $user
     * @return bool
     */
    public static function canSeeEmail( $user )
    {
        $access = $user->hasAccessTo( 'role', 'assign' );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * Whether the user may use Deactivate and move: the function user/deactivate.
     *
     * @param \eZUser $user
     * @return bool
     */
    public static function canDeactivate( $user )
    {
        $access = $user->hasAccessTo( 'user', 'deactivate' );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The names of some accounts for the confirmation page, in one query.
     *
     * @param int[] $ids
     * @return string[] id => name
     */
    protected static function names( array $ids )
    {
        $names = array();
        if ( !$ids )
            return $names;
        $db = \eZDB::instance();
        foreach ( (array)$db->arrayQuery( 'SELECT u.contentobject_id, u.login, o.name FROM ezuser u LEFT JOIN ezcontentobject o ON o.id = u.contentobject_id WHERE '
                                          . $db->generateSQLINStatement( $ids, 'u.contentobject_id', false, true, 'int' ) ) as $row )
            $names[(int)$row['contentobject_id']] = (string)$row['name'] !== '' ? (string)$row['name'] : (string)$row['login'];
        return $names;
    }

    /**
     * Sends the CSV of the filtered list and ends the request. Records data.export.csv with the number of rows,
     * never the values.
     */
    protected function export( \expUserHygiene $hygiene, array $filters, $canSeeEmail, $groupPath )
    {
        $fileName = 'user-accounts-' . gmdate( 'Ymd-His' ) . '.csv';
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );
        while ( @ob_end_clean() );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $out = fopen( 'php://output', 'w' );
        $rows = $hygiene->writeCSV( $out, $filters, $canSeeEmail, $groupPath, true, function ( $source )
        {
            return \ezpI18n::tr( \expUserHygiene::CONTEXT, $source );
        } );
        fclose( $out );

        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.export.csv', array( 'object' => array( 'type' => 'view', 'id' => 'user/hygiene' ), 'verb' => 'export',
                'after' => array( 'rows' => $rows, 'filter' => $filters['filter'], 'group' => $filters['group'], 'search' => $filters['q'] !== '',
                                  'email' => (bool)$canSeeEmail, 'file' => $fileName, 'source' => 'user_hygiene' ) ) );

        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }
}

}
