<?php
/**
 * The code of kernel/package/list.php, moved into a class (#207 stage 1). The file kernel/package/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md, doc/guides/packages.md
 */
/*
 * The original header of kernel/package/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Package
{

/**
 * package/list[/<RepositoryID>][/(search)/..][/(type)/..][/(state)/..][/(sort)/..][/(dir)/..][/offset/N]
 *
 * The overview of every repository with its counts, and one card per package, searched, filtered, sorted and
 * paged; the removal of selected packages through a confirmation that says what goes. Without a repository the
 * list shows every repository (it showed only "local" before, which hid the installer's own packages).
 *
 * POST names are those of before: ChangeRepositoryButton (RepositoryID), InstallPackageButton, CreatePackageButton,
 * RemovePackageButton, ConfirmRemovePackageButton, CancelRemovePackageButton (PackageSelection[]). The template
 * variables of before are still set: module_action, view_parameters, page_limit, remove_list, repository_id.
 */
class ListView extends \Exponential\Runnable\ModuleView
{
    /** What the last removal did, for the list it redirects to. */
    const FEEDBACK = 'ExpPackageFeedback';
    /** The selection values the removal confirmation offered; only those can be confirmed. */
    const OFFER = 'ExpPackageRemoveOffer';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $repositories = \eZPackage::packageRepositories();

        // The repository: none means every repository. One the storage does not have is not found.
        $repositoryID = isset( $scope['Params']['RepositoryID'] ) && $scope['Params']['RepositoryID'] !== false ? (string)$scope['Params']['RepositoryID'] : '';
        if ( $repositoryID !== '' && !\eZPackageRequestGuard::repository( $repositoryID, $repositories ) )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( $module->isCurrentAction( 'InstallPackage' ) )
            return $this->viewResult( null, $module->redirectToView( 'upload' ) );
        if ( $module->isCurrentAction( 'CreatePackage' ) )
            return $this->viewResult( null, $module->redirectToView( 'create' ) );
        if ( $module->isCurrentAction( 'ChangeRepository' ) )
        {
            $chosen = (string)$module->actionParameter( 'RepositoryID' );
            return $this->viewResult( null, $module->redirectTo( self::listURI( \eZPackageRequestGuard::repository( $chosen, $repositories ) ? $chosen : '' ) ) );
        }

        // The filter form sends GET fields; they are answered with one redirect to the same state as view
        // parameters, so the address bar, the pager and a bookmark all carry the clean form.
        if ( $http->hasGetVariable( 'PackageFilter' ) )
        {
            $get = function ( $name ) use ( $http ) { return $http->hasGetVariable( $name ) ? $http->getVariable( $name ) : ''; };
            $chosen = (string)$get( 'Repository' );
            $target = self::listURI( \eZPackageRequestGuard::repository( $chosen, $repositories ) ? $chosen : '',
                                     array( 'search' => $get( 'SearchText' ), 'type' => $get( 'Type' ), 'state' => $get( 'State' ),
                                            'sort' => $get( 'Sort' ), 'dir' => '' ) );
            \eZURI::transformURI( $target, false, 'full' );
            \eZHTTPTool::redirect( $target, array(), '302 Found', false );
            \eZExecution::cleanExit();
        }

        $scan = \eZPackageCatalog::scan( $repositories );
        $cards = $scan['cards'];
        $canRemove = \eZPackage::canUsePolicyFunction( 'remove' );
        $removePolicy = function ( array $card )
        {
            $package = \eZPackage::create( $card['name'], array( 'type' => $card['type'] ) );
            return $package->canUsePackagePolicyFunction( 'remove' );
        };
        $storagePath = \eZPackage::repositoryPath();

        $removeList = array();
        $removePlan = false;
        $removeProblem = null;
        if ( $module->isCurrentAction( 'CancelRemovePackage' ) )
        {
            $http->removeSessionVariable( self::OFFER );
            $http->setSessionVariable( self::FEEDBACK, array( 'type' => 'cancelled' ) );
            return $this->viewResult( null, $module->redirectTo( self::listURI( $repositoryID ) ) );
        }
        if ( $module->isCurrentAction( 'RemovePackage' ) || $module->isCurrentAction( 'ConfirmRemovePackage' ) )
        {
            if ( !$canRemove )
                return $this->viewResult( null, $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            $selection = $module->hasActionParameter( 'PackageSelection' ) ? $module->actionParameter( 'PackageSelection' ) : array();
            $removePlan = \eZPackageRemovalPlan::build( $selection, $repositoryID, $cards, $removePolicy, $storagePath );
            if ( !$removePlan['items'] && !$removePlan['refused'] )
            {
                $http->setSessionVariable( self::FEEDBACK, array( 'type' => 'none_selected' ) );
                return $this->viewResult( null, $module->redirectTo( self::listURI( $repositoryID ) ) );
            }

            if ( $module->isCurrentAction( 'ConfirmRemovePackage' ) )
            {
                $offered = $http->hasSessionVariable( self::OFFER ) ? (array)$http->sessionVariable( self::OFFER ) : array();
                $removeProblem = \eZPackageRemovalPlan::confirmationProblem( $offered, $removePlan, $http->hasPostVariable( 'ConfirmInstallerSourceRemoval' ) );
                if ( $removeProblem === null && $removePlan['items'] )
                {
                    $removed = self::remove( $removePlan, $repositories );
                    $http->removeSessionVariable( self::OFFER );
                    $http->setSessionVariable( self::FEEDBACK, array( 'type' => 'removed', 'names' => $removed ) );
                    return $this->viewResult( null, $module->redirectTo( self::listURI( $repositoryID ) ) );
                }
            }
            else
            {
                $http->setSessionVariable( self::OFFER, \eZPackageRemovalPlan::values( $removePlan ) );
            }

            // the old template variable: the packages offered for removal
            foreach ( $removePlan['items'] as $card )
            {
                $package = \eZPackage::fetch( $card['name'], self::repositoryPath( $card['repository_id'], $repositories ), false, false );
                if ( $package )
                    $removeList[] = $package;
            }
        }

        // The list
        $userParameters = isset( $scope['Params']['UserParameters'] ) && is_array( $scope['Params']['UserParameters'] ) ? $scope['Params']['UserParameters'] : array();
        if ( isset( $userParameters['search'] ) )
            $userParameters['search'] = rawurldecode( $userParameters['search'] );
        $types = \eZPackageCatalog::types( $cards );
        $query = \eZPackageCatalog::normaliseQuery( $userParameters, $types );
        $filtered = \eZPackageCatalog::sort( \eZPackageCatalog::filter( $cards, $repositoryID, $query ), $query['sort'], $query['dir'] );

        list( $limit, $limitChoice, $limitChoices ) = self::pageSize();
        $total = count( $filtered );
        $offset = \expAdminPagination::offset( $scope['Params'] );
        if ( $offset >= $total && $total > 0 )
            $offset = (int)( floor( ( $total - 1 ) / $limit ) * $limit );
        $page = array_slice( $filtered, $offset, $limit, true );

        $tpl = \eZTemplate::factory();
        // the variables of before
        $viewParameters = array( 'offset' => $offset );
        foreach ( array( 'search', 'type', 'state', 'sort', 'dir' ) as $key )
            $viewParameters[$key] = $query[$key];
        $tpl->setVariable( 'module_action', $module->currentAction() );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'page_limit', $limit );
        $tpl->setVariable( 'remove_list', $removeList );
        $tpl->setVariable( 'repository_id', $repositoryID );
        // the overview, the cards and their controls
        $tpl->setVariable( 'package_repositories', $scan['repositories'] );
        $tpl->setVariable( 'package_totals', \eZPackageCatalog::totals( $scan['repositories'] ) );
        $tpl->setVariable( 'package_problems', $scan['problems'] );
        $tpl->setVariable( 'package_cards', array_values( $page ) );
        $tpl->setVariable( 'package_count', $total );
        $tpl->setVariable( 'package_all_count', count( $cards ) );
        $tpl->setVariable( 'package_query', $query );
        $tpl->setVariable( 'package_types', $types );
        $tpl->setVariable( 'package_states', \eZPackageCatalog::$states );
        $tpl->setVariable( 'package_sort_keys', array_keys( \eZPackageCatalog::$sortKeys ) );
        $tpl->setVariable( 'package_pager', self::pager( $repositoryID, $query, $offset, $limit, $total ) );
        $tpl->setVariable( 'package_list_uri', self::listURI( $repositoryID, $query ) );
        $tpl->setVariable( 'package_clear_uri', self::listURI( $repositoryID ) );
        $tpl->setVariable( 'package_reverse_uri', self::listURI( $repositoryID, array( 'dir' => $query['dir'] === 'asc' ? 'desc' : 'asc' ) + $query ) );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'package_can_remove', $canRemove );
        $tpl->setVariable( 'package_can_import', \eZPackage::canUsePolicyFunction( 'import' ) );
        $tpl->setVariable( 'package_can_create', \eZPackage::canUsePolicyFunction( 'create' ) );
        $tpl->setVariable( 'package_vendor', \eZPackageCatalog::vendorRepository() );
        $tpl->setVariable( 'package_storage_path', $storagePath );
        $tpl->setVariable( 'remove_plan', $removePlan );
        $tpl->setVariable( 'remove_problem', $removeProblem );
        $tpl->setVariable( 'package_feedback', ( $removePlan === false ) ? self::takeFeedback( $http ) : false );

        $Result = array();
        $Result['content'] = $tpl->fetch( $removePlan !== false ? 'design:package/confirmremove.tpl' : 'design:package/list.tpl' );
        $path = array( array( 'url' => $repositoryID !== '' || $removePlan !== false ? 'package/list' : false,
                              'text' => \ezpI18n::tr( 'kernel/package', 'Packages' ) ) );
        if ( $repositoryID !== '' )
            $path[] = array( 'url' => $removePlan !== false ? self::listURI( $repositoryID ) : false, 'text' => $repositoryID );
        if ( $removePlan !== false )
            $path[] = array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/package', 'Remove packages' ) );
        $Result['path'] = $path;

        return $this->viewResult( $Result, null );
    }

    /**
     * Removes the packages of a plan; each is read again by its own repository's path first.
     *
     * @return string[] the names removed, "name (repository)"
     */
    private static function remove( array $plan, array $repositories )
    {
        $removed = array();
        foreach ( $plan['items'] as $card )
        {
            $path = self::repositoryPath( $card['repository_id'], $repositories );
            $package = $path ? \eZPackage::fetch( $card['name'], $path, false, true ) : false;
            if ( !$package || (string)$package->attribute( 'name' ) !== $card['name'] )
                continue;
            $repository = \eZPackageRequestGuard::repository( $card['repository_id'], $repositories );
            $package->setCurrentRepositoryInformation( $repository );
            if ( class_exists( 'expAuditHook' ) )
                \expAuditHook::emit( 'system.package.remove', array( 'object' => $package->auditObject(),
                    'before' => array( 'name' => $card['name'], 'version' => $card['version'], 'repository' => $card['repository_id'],
                                       'files' => $card['files'], 'bytes' => $card['bytes'], 'installed' => $card['installed'],
                                       'installer_source' => $card['installer_source'] ) ) );
            $package->remove();
            $removed[] = $card['name'] . ' (' . $card['repository_id'] . ')';
        }
        return $removed;
    }

    private static function repositoryPath( $repositoryID, array $repositories )
    {
        $repository = \eZPackageRequestGuard::repository( $repositoryID, $repositories );
        return $repository ? $repository['path'] : false;
    }

    /**
     * The address of the list for a repository and a state; only what differs from the defaults is written. The
     * search is encoded twice: the kernel decodes the whole path once before it splits it.
     *
     * @return string without the siteaccess, e.g. /package/list/7x/(search)/demo%2520content/(sort)/size
     */
    static function listURI( $repositoryID, array $query = array(), $offset = 0 )
    {
        $uri = '/package/list' . ( (string)$repositoryID !== '' ? '/' . $repositoryID : '' );
        $search = isset( $query['search'] ) ? trim( (string)$query['search'] ) : '';
        if ( $search !== '' )
            $uri .= '/(search)/' . rawurlencode( rawurlencode( $search ) );
        foreach ( array( 'type', 'state' ) as $key )
            if ( isset( $query[$key] ) && (string)$query[$key] !== '' && preg_match( '/^[A-Za-z0-9_\-]+$/', (string)$query[$key] ) )
                $uri .= '/(' . $key . ')/' . $query[$key];
        $sort = isset( $query['sort'] ) && isset( \eZPackageCatalog::$sortKeys[$query['sort']] ) ? $query['sort'] : 'name';
        if ( $sort !== 'name' )
            $uri .= '/(sort)/' . $sort;
        if ( isset( $query['dir'] ) && ( $query['dir'] === 'asc' || $query['dir'] === 'desc' ) && $query['dir'] !== \eZPackageCatalog::$sortKeys[$sort] )
            $uri .= '/(dir)/' . $query['dir'];
        if ( $offset > 0 )
            $uri .= '/offset/' . (int)$offset;
        return $uri;
    }

    /**
     * The links of the pager: first, previous, the pages around the current one, next, last.
     *
     * @return array( 'pages' => int, 'page' => int, 'first' => uri|false, 'prev', 'next', 'last', 'items' => list of
     *                array( 'number', 'uri', 'current' ), 'from' => int, 'to' => int )
     */
    static function pager( $repositoryID, array $query, $offset, $limit, $total )
    {
        $limit = max( 1, (int)$limit );
        $pages = max( 1, (int)ceil( $total / $limit ) );
        $page = (int)floor( $offset / $limit ) + 1;
        $items = array();
        for ( $n = max( 1, $page - 3 ); $n <= min( $pages, $page + 3 ); $n++ )
            $items[] = array( 'number' => $n, 'uri' => self::listURI( $repositoryID, $query, ( $n - 1 ) * $limit ), 'current' => $n === $page );
        return array(
            'pages' => $pages,
            'page' => $page,
            'first' => $page > 1 ? self::listURI( $repositoryID, $query, 0 ) : false,
            'prev' => $page > 1 ? self::listURI( $repositoryID, $query, ( $page - 2 ) * $limit ) : false,
            'next' => $page < $pages ? self::listURI( $repositoryID, $query, $page * $limit ) : false,
            'last' => $page < $pages ? self::listURI( $repositoryID, $query, ( $pages - 1 ) * $limit ) : false,
            'items' => $items,
            // the first and last page are linked on their own when the window around the current one leaves them out
            'gap_start' => $items && $items[0]['number'] > 1,
            'gap_end' => $items && $items[count( $items ) - 1]['number'] < $pages,
            'from' => $total ? $offset + 1 : 0,
            'to' => min( $total, $offset + $limit ),
        );
    }

    /**
     * The page size: the sizes on offer are admininterface.ini [PaginationSettings] ItemsPerPageList_package_list
     * (10, 25, 50, 100 when not set); the viewer's choice is the preference admin_package_list_limit, and without
     * one the size of ItemsPerPage[package/list] when it is on offer.
     *
     * @return array( int $limit, int $choice (from 1), int[] $sizes )
     */
    static function pageSize()
    {
        $sizes = \expAdminPagination::sizes( 'package/list', array( 10, 25, 50, 100 ) );
        $choice = (int)\eZPreferences::value( 'admin_package_list_limit' );
        if ( $choice < 1 || $choice > count( $sizes ) )
        {
            $position = array_search( (int)\expAdminPagination::limit( 'package/list' ), $sizes, true );
            $choice = $position === false ? 1 : $position + 1;
        }
        return array( $sizes[$choice - 1], $choice, $sizes );
    }

    /**
     * What the last removal left for the list, read once.
     *
     * @return array|false
     */
    public static function takeFeedback( \eZHTTPTool $http )
    {
        if ( !$http->hasSessionVariable( self::FEEDBACK ) )
            return false;
        $feedback = $http->sessionVariable( self::FEEDBACK );
        $http->removeSessionVariable( self::FEEDBACK );
        return is_array( $feedback ) && isset( $feedback['type'] ) ? $feedback : false;
    }
}

}
