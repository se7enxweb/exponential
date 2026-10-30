<?php
/**
 * The contentcheck/brokenrelations view (doc/eng-us/features/6.0/content-checks.md, "Broken relations"): visible,
 * published content below a node (contentcheck/brokenrelations/<node id>, the content tree's top node without one) that refers
 * to deleted, trashed, hidden or unpublished content, page by page. Read only. Needs the policy
 * contentcheck/brokenrelations; answers on administration siteaccesses only. With the target "Missing files"
 * ((kind)/missing_file) it also lists the content whose files are missing from the storage and the images with
 * missing or stale aliases (expContentCheckFiles, doc "Missing files"), paged on their own ((file_offset) in the address).
 * The options are view parameters only: (exclude), (kind), (relation) (comma lists, "none" for none ticked), (after),
 * (problem), (version), (selectall)/1, (file_offset); a query string is not read. The filter form is posted and
 * answered with a 303 to that address.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Brokenrelations extends \Exponential\Runnable\ModuleView
{
    /** The fields of the filter form (posted, then answered with a 303 to the view parameters) */
    const FORM_FIELDS = array( 'NodeID', 'Exclude', 'Kind', 'Relation', 'KindChoice', 'RelationChoice',
                               'Problem', 'Version', 'ProblemChoice', 'VersionChoice' );

    /** The preference that holds the chosen page size (its position in the sizes) */
    const LIMIT_PREFERENCE = 'admin_contentcheck_broken_limit';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'contentcheck/brokenrelations', self::LIMIT_PREFERENCE );
        // the options are view parameters (/(exclude)/43/(kind)/deleted,trash/(relation)/.../(after)/120, and those of
        // the missing files: (problem), (version), (selectall)); the filter form is posted and answered with a 303 to
        // that address (expViewParameters::redirect(), which does not carry a query string over)
        $posted = \expViewParameters::redirectsPost( $_POST, self::FORM_FIELDS );
        $input = $posted ? \expContentCheck::postedInput( $_POST, self::FORM_FIELDS ) : self::viewInput( $Params );
        if ( $posted )
            unset( $input['After'] );
        // the form's node, else the address's, else the user's last choice, which it becomes (expContentCheckScope)
        $input['NodeID'] = \expContentCheckScope::resolve( isset( $Params['NodeID'] ) ? $Params['NodeID'] : null,
                                                          $posted && isset( $input['NodeID'] ) ? $input['NodeID'] : null );
        // "Select all" / "Select none" of the states and the references (KindChoice, RelationChoice; (kind)/none in the
        // address); none ticked checks every one, as no box ticked does
        $none = array();
        foreach ( array( 'Kind' => \expContentCheckBrokenRelations::KINDS, 'Relation' => \expContentCheckBrokenRelations::RELATIONS ) as $group => $all )
        {
            $choice = isset( $input[$group . 'Choice'] ) && is_string( $input[$group . 'Choice'] ) ? $input[$group . 'Choice'] : '';
            if ( $choice === 'all' )
                $input[$group] = $group === 'Kind' && class_exists( 'expContentCheckFiles' ) ? array_merge( $all, array( \expContentCheckFiles::TARGET ) ) : $all;
            else if ( $choice === 'none' )
            {
                unset( $input[$group] );
                $none[] = $group;
            }
        }
        $input['Limit'] = $limit;
        // without a node: the content tree's top node, which a user with a limited content/read may read as well
        $params = \expContentCheckBrokenRelations::parameters( $input, Unreferenced::rootNodeID() );

        // the target "Missing files" (Kind[]=missing_file, expContentCheckFiles): the files of the content below the
        // node that are not in the storage; ticked alone, no relation is checked
        $files = method_exists( 'expContentCheckFiles', 'section' ) ? \expContentCheckFiles::target( isset( $input['Kind'] ) ? $input['Kind'] : array() )
                                                                   : array( 'enabled' => false, 'only' => false );
        if ( $files['only'] )
            $params['kinds'] = array();

        if ( $posted )
        {
            $suffix = self::querySuffix( $params, $none );
            if ( $files['enabled'] )
                $suffix = \expContentCheckFiles::querySuffix( $suffix, $params['kinds'], \expContentCheckFiles::filterParameters( $input, (int)$params['node_id'], $limit ) );
            \expViewParameters::redirect( '/contentcheck/brokenrelations/' . (int)$params['node_id'] . $suffix );
        }

        $result = $files['only'] ? \expContentCheckFiles::noBrokenRelations( $params['node_id'], $params['after'] ) : \expContentCheckBrokenRelations::scan( $params );
        if ( $result['error'] === 'not_found' )
            return $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        $filterSuffix = self::querySuffix( $params, $none );
        $missingFiles = array( 'enabled' => false, 'only' => false, 'storage_dir' => method_exists( 'expContentCheckFiles', 'section' ) ? \eZSys::storageDirectory() : '' );
        if ( $files['enabled'] )
        {
            $missingFiles = \expContentCheckFiles::section( $input,(int)$params['node_id'], $limit, \expAdminPagination::offset( $Params, 'file_offset' ) ) + $files;
            $filterSuffix = \expContentCheckFiles::querySuffix( $filterSuffix, $params['kinds'], $missingFiles['params'] );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'result', $result );
        $tpl->setVariable( 'params', $params );
        $tpl->setVariable( 'scope', \expContentCheckScope::forTemplate( (int)$params['node_id'], 'brokenrelations' ) );
        $tpl->setVariable( 'kinds', \expContentCheckBrokenRelations::KINDS );
        $tpl->setVariable( 'kinds_shown', in_array( 'Kind', $none, true ) ? array() : $params['kinds'] );
        $tpl->setVariable( 'relations_shown', in_array( 'Relation', $none, true ) ? array() : $params['relations'] );
        $tpl->setVariable( 'relations', \expContentCheckBrokenRelations::RELATIONS );
        $tpl->setVariable( 'page_uri', '/contentcheck/brokenrelations/' . (int)$params['node_id'] );
        $tpl->setVariable( 'filter_suffix', $filterSuffix );
        $tpl->setVariable( 'missing_files', $missingFiles );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_preference', self::LIMIT_PREFERENCE );

        return array( 'content' => $tpl->fetch( 'design:contentcheck/brokenrelations.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Broken relations' ), 'url' => false ) ) );
    }

    /**
     * The view parameters of the filters, without the position ((after)), which the pages add.
     *
     * @param array $params from expContentCheckBrokenRelations::parameters()
     * @param string[] $none the groups whose "Select none" was pressed ('Kind', 'Relation'): "none" in the address
     * @return string empty without a filter, else "/(exclude)/..."
     */
    public static function querySuffix( array $params, array $none = array() )
    {
        $options = array();
        if ( $params['exclude'] )
            $options['exclude'] = $params['exclude'];
        if ( in_array( 'Kind', $none, true ) )
            $options['kind'] = 'none';
        else if ( count( $params['kinds'] ) < count( \expContentCheckBrokenRelations::KINDS ) )
            $options['kind'] = $params['kinds'];
        if ( in_array( 'Relation', $none, true ) )
            $options['relation'] = 'none';
        else if ( count( $params['relations'] ) < count( \expContentCheckBrokenRelations::RELATIONS ) )
            $options['relation'] = $params['relations'];
        return \expContentCheck::viewSuffix( $options );
    }

    /**
     * The options of the address as the fields of the filter form: Exclude, Kind, Relation, After, and the missing
     * files' Problem, Version, SelectAll ("none" of a list as <Field>Choice=none).
     *
     * @param array $Params the view's $Params
     * @return array
     */
    public static function viewInput( array $Params )
    {
        return \expContentCheck::viewInput( $Params, array( 'Exclude' => array( 'exclude', false ), 'Kind' => array( 'kind', true ),
                                                            'Relation' => array( 'relation', true ), 'After' => array( 'after', false ),
                                                            'Problem' => array( 'problem', true ), 'Version' => array( 'version', true ),
                                                            'SelectAll' => array( 'selectall', false ) ) );
    }
}

}
