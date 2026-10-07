<?php
/**
 * The code of kernel/content/draft.php, moved into a class (#207 stage 1). The file kernel/content/draft.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * The drafts page of the current user ("My drafts"). run() takes the request in steps: removing ticked drafts, one
 * draft, every draft, or the drafts older than a number of days, then the list. Only the user's own drafts are ever
 * listed or removed: every version is checked again at the moment it is removed (expContentDraftList::removable()).
 * The list (filters, search, order, paging, figures) is worked out by expContentDraftList, which needs no database.
 * Guide: doc/guides/drafts-and-pending.md
 */
/*
 * The original header of kernel/content/draft.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Draft extends \Exponential\Runnable\ModuleView
{
    /** The view name of the page sizes (admininterface.ini [PaginationSettings]) and the preference of the chosen one */
    const LIST_VIEW = 'content/draft';
    const LIMIT_PREFERENCE = 'admin_draft_list_limit';

    /** @var \eZHTTPTool */
    private $http;
    private $userID = 0;
    /** What a removal did: array( 'removed' => n, 'refused' => n, 'kind' => ... ), or false */
    private $feedback = false;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $this->http = \eZHTTPTool::instance();

        $user = \eZUser::currentUser();
        if ( !$user->isRegistered() )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        $this->userID = (int)$user->id();

        $filters = \expContentDraftList::filters( isset( $scope['Params']['UserParameters'] ) ? (array)$scope['Params']['UserParameters'] : array(),
                                                  $this->http->hasGetVariable( 'q' ) ? $this->http->getVariable( 'q' ) : '' );
        $offset = \expAdminPagination::offset( $scope['Params'] );

        $this->removeSelected();
        $this->removeOne();
        $this->removeAll();
        $this->removeOld();

        $tpl = \eZTemplate::factory();
        $this->listVariables( $tpl, $filters, $offset );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $tpl->setVariable( 'draft_feedback', $this->feedback );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/draft.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'My drafts' ), 'url' => false ) );
        return $this->viewResult( $Result, null );
    }

    /** Remove selected (RemoveButton, DeleteIDArray[]) */
    protected function removeSelected()
    {
        if ( !$this->http->hasPostVariable( 'RemoveButton' ) )
            return;
        $ids = $this->http->hasPostVariable( 'DeleteIDArray' ) ? $this->http->postVariable( 'DeleteIDArray' ) : array();
        $this->remove( is_array( $ids ) ? $ids : array(), 'selected' );
    }

    /** The Remove button of one draft (RemoveDraftButton[<version id>]) */
    protected function removeOne()
    {
        $button = $this->http->hasPostVariable( 'RemoveDraftButton' ) ? $this->http->postVariable( 'RemoveDraftButton' ) : null;
        if ( is_array( $button ) && $button )
            $this->remove( array( key( $button ) ), 'one' );
    }

    /** Remove all (EmptyButton): every draft of the user */
    protected function removeAll()
    {
        if ( !$this->http->hasPostVariable( 'EmptyButton' ) )
            return;
        $ids = array();
        foreach ( \eZContentObjectVersion::fetchForUser( $this->userID ) as $version )
            $ids[] = (int)$version->attribute( 'id' );
        $this->remove( $ids, 'all' );
    }

    /** Remove the drafts not modified for some days (RemoveOldButton, OldDraftDays) */
    protected function removeOld()
    {
        if ( !$this->http->hasPostVariable( 'RemoveOldButton' ) )
            return;
        $days = \expContentDraftList::age( $this->http->hasPostVariable( 'OldDraftDays' ) ? $this->http->postVariable( 'OldDraftDays' ) : '' );
        if ( !$days )
        {
            $this->feedback = array( 'kind' => 'old', 'removed' => 0, 'refused' => 0, 'days' => 0 );
            return;
        }
        $now = time();
        $rows = array();
        foreach ( \eZContentObjectVersion::fetchForUser( $this->userID ) as $version )
            $rows[] = \expContentDraftList::row( array( 'id' => $version->attribute( 'id' ), 'modified' => $version->attribute( 'modified' ) ), $now );
        $this->remove( \expContentDraftList::olderThan( $rows, $days ), 'old' );
        $this->feedback['days'] = $days;
    }

    /**
     * Removes the versions $ids that are the user's own drafts, each checked as it is at the moment it is removed;
     * every other id (another user's version, a published version, an unknown id) is left and counted.
     *
     * @param array $ids version ids
     * @param string $kind selected, one, all or old, for the message
     */
    protected function remove( array $ids, $kind )
    {
        $removed = 0;
        $refused = 0;
        $anonymousID = (int)\eZUser::anonymousId();
        $db = \eZDB::instance();
        foreach ( array_unique( $ids ) as $id )
        {
            if ( !is_scalar( $id ) || !ctype_digit( (string)$id ) )
            {
                $refused++;
                continue;
            }
            $db->begin();
            $version = \eZContentObjectVersion::fetch( (int)$id );
            if ( !$version instanceof \eZContentObjectVersion ||
                 !\expContentDraftList::removable( array( 'creator_id' => $version->attribute( 'creator_id' ), 'status' => $version->attribute( 'status' ) ),
                                                   $this->userID, $anonymousID ) )
            {
                $db->commit();
                $refused++;
                continue;
            }
            $version->removeThis();
            $db->commit();
            $removed++;
        }
        if ( $refused )
            \eZDebug::writeNotice( "content/draft: $refused of the versions asked for are not drafts of user {$this->userID} and were not removed", __METHOD__ );
        $this->feedback = array( 'kind' => $kind, 'removed' => $removed, 'refused' => $refused );
    }

    /** The list: every draft of the user as a row, the figures, the filter choices and links, one page of rows */
    protected function listVariables( $tpl, array $filters, $offset )
    {
        $now = time();
        $all = array();
        $versions = array();
        $names = array();
        foreach ( \eZContentObjectVersion::fetchForUser( $this->userID ) as $version )
        {
            $object = $version->attribute( 'contentobject' );
            if ( !$object instanceof \eZContentObject )
                continue;
            $class = $object->attribute( 'content_class' );
            $locale = (string)$version->initialLanguageCode();
            $nodeID = (int)$object->attribute( 'main_node_id' );
            $parentID = $nodeID ? (int)$object->attribute( 'main_parent_node_id' ) : (int)$version->attribute( 'main_parent_node_id' );
            $all[] = \expContentDraftList::row( array( 'id' => $version->attribute( 'id' ), 'version' => $version->attribute( 'version' ),
                                                       'object_id' => $object->attribute( 'id' ), 'name' => $version->versionName(),
                                                       'class_identifier' => $class ? $class->attribute( 'identifier' ) : '',
                                                       'class_name' => $class ? $class->attribute( 'name' ) : '',
                                                       'language' => $locale, 'language_name' => self::cached( $names, 'l' . $locale, function () use ( $locale ) {
                                                           $language = \eZContentLanguage::fetchByLocale( $locale );
                                                           return $language ? $language->attribute( 'name' ) : $locale; } ),
                                                       'created' => $version->attribute( 'created' ), 'modified' => $version->attribute( 'modified' ),
                                                       'status' => $version->attribute( 'status' ),
                                                       'is_new' => (int)$object->attribute( 'status' ) === \eZContentObject::STATUS_DRAFT,
                                                       'node_id' => $nodeID, 'location' => self::cached( $names, 'n' . $parentID, function () use ( $parentID ) {
                                                           $node = $parentID ? \eZContentObjectTreeNode::fetch( $parentID ) : null;
                                                           return $node ? (string)$node->attribute( 'name' ) : ''; } ) ), $now );
            $versions[(int)$version->attribute( 'id' )] = $version;
        }
        $choices = \expContentDraftList::choices( $all );
        $oldDays = $filters['age'] ? $filters['age'] : \expContentDraftList::AGES[0];
        $selected = \expContentDraftList::select( $all, $filters );
        $count = count( $selected );
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( self::LIST_VIEW, self::LIMIT_PREFERENCE );
        if ( $offset >= $count )
            $offset = 0;
        $rows = array();
        foreach ( \expAdminPagination::page( $selected, $offset, $limit ) as $row )
        {
            $row['can_versionread'] = (bool)$versions[$row['id']]->attribute( 'can_read' );
            $row['object'] = $versions[$row['id']];
            $rows[] = $row;
        }
        $tpl->setVariable( 'draft_rows', $rows );
        $tpl->setVariable( 'draft_count', $count );
        $tpl->setVariable( 'draft_offset', $offset );
        $tpl->setVariable( 'draft_limit', $limit );
        $tpl->setVariable( 'draft_limit_choices', $limitChoices );
        $tpl->setVariable( 'draft_filters', $filters );
        $tpl->setVariable( 'draft_suffix', \expContentDraftList::suffix( $filters ) );
        $tpl->setVariable( 'draft_search_suffix', $filters['search'] !== '' ? '?q=' . rawurlencode( $filters['search'] ) : '' );
        $tpl->setVariable( 'draft_links', \expContentDraftList::links( $filters, $choices ) );
        $tpl->setVariable( 'draft_choices', $choices );
        $tpl->setVariable( 'draft_overview', \expContentDraftList::overview( $all, $oldDays ) );
        $tpl->setVariable( 'draft_ages', \expContentDraftList::AGES );
        $tpl->setVariable( 'draft_old_counts', self::oldCounts( $all ) );
    }

    /** @return array days => the number of drafts not modified for that many days, for the removal of old drafts */
    private static function oldCounts( array $rows )
    {
        $counts = array();
        foreach ( \expContentDraftList::AGES as $days )
            $counts[$days] = count( \expContentDraftList::olderThan( $rows, $days ) );
        ksort( $counts );
        return $counts;
    }

    /** A value remembered in $cache for the request */
    private static function cached( array &$cache, $key, $make )
    {
        if ( !array_key_exists( $key, $cache ) )
            $cache[$key] = $make();
        return $cache[$key];
    }
}

}
