<?php
/**
 * The code of kernel/content/history.php, moved into a class (#207 stage 1). The file kernel/content/history.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * The versions page of an object. run() takes the request in steps: the Back button, who may open the page, the
 * comparison, the removal, the edit and the copy of a version, then the list. The list (filters, order, paging,
 * figures, and the actions each version offers) is worked out by expContentHistoryList, which needs no database;
 * who may open the page and whose content they see is decided here (canOpen(), canSeeVersionContent()).
 * Guides: doc/bc/6.0/draft-edit-access.md ("The versions of such an object"), doc/guides/content-history.md
 */
/*
 * The original header of kernel/content/history.php:
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

class History extends \Exponential\Runnable\ModuleView
{
    /** The view name of the page sizes (admininterface.ini [PaginationSettings]) and the preference of the chosen one */
    const LIST_VIEW = 'content/history';
    const LIMIT_PREFERENCE = 'admin_history_list_limit';

    /** @var \eZTemplate */
    private $tpl;
    /** @var \eZHTTPTool */
    private $http;
    /** @var \eZModule */
    private $module;
    /** @var \eZContentObject */
    private $object;
    private $canEdit = false;
    private $canRead = false;
    private $userID = 0;
    /** @var int[] the numbers of the versions whose content the user may see */
    private $contentVersions = array();
    /** An action refused for some versions: array( 'action' => ..., 'versions' => array( ... ) ), or false */
    private $refused = false;
    /** What an action did: array( 'type' => 'removed'|'copied', ... ), or false */
    private $feedback = false;
    private $editWarning = false;
    private $editVersion;
    /** Where the Back button goes, see originURI() */
    private $origin = '/content/dashboard';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $this->tpl = \eZTemplate::factory();
        $this->http = \eZHTTPTool::instance();
        $this->module = $scope['Params']['Module'];
        $this->editVersion = $scope['Params']['EditVersion'];

        $object = \eZContentObject::fetch( $scope['Params']['ObjectID'] );
        // Where the Back button goes (originURI()): taken when the page is opened and carried in the form as
        // RedirectURI, so the actions of the page keep it; without an origin, the object's own location
        $this->origin = self::originURI( $object ? (int)$object->attribute( 'id' ) : 0, $object ? (int)$object->attribute( 'main_node_id' ) : 0,
                                         self::originCandidates( $this->http ), \eZSys::indexDir() );
        if ( $this->http->hasPostVariable( 'BackButton' ) )
            return $this->viewResult( null, $this->module->redirectTo( $this->origin ) );

        if ( $object === null )
            return $this->viewResult( null, $this->module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        $this->object = $object;

        $this->canEdit = (bool)$object->editAccess();
        if ( !self::canOpen( $object, $this->canEdit ) )
            return $this->viewResult( null, $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        $this->canRead = (bool)$object->attribute( 'can_read' );
        $this->userID = (int)\eZUser::currentUserID();
        $this->contentVersions = $this->seenVersions();

        $filters = \expContentHistoryList::filters( isset( $scope['Params']['UserParameters'] ) ? (array)$scope['Params']['UserParameters'] : array() );
        $offset = \expAdminPagination::offset( $scope['Params'] );

        // The actions of the page, each with its own check; a step that ends the request returns its result
        foreach ( array( 'compare', 'versionLimitWarning', 'removeVersions', 'editAction', 'copyAction' ) as $step )
        {
            $returned = $this->$step();
            if ( $returned !== null )
                return $this->viewResult( null, $returned );
        }
        // what the actions changed is listed as it is now
        $this->contentVersions = $this->seenVersions();

        $section = $this->designKeys();
        $this->listVariables( $filters, $offset );

        $tpl = $this->tpl;
        $tpl->setVariable( 'module', $this->module );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $tpl->setVariable( 'object', $object );
        $tpl->setVariable( 'edit_version', $this->editVersion );
        $tpl->setVariable( 'versions', $object->versions() );
        $tpl->setVariable( 'edit_warning', $this->editWarning );
        $tpl->setVariable( 'can_edit', $this->canEdit );
        // Whether the user may read the object; false for an editor who may edit it only (the page says so)
        $tpl->setVariable( 'can_read', $this->canRead );
        // The numbers of the versions whose content the user may see (compare, copy, the links to the version view)
        $tpl->setVariable( 'content_versions', $this->contentVersions );
        // An action of the page refused for some versions, or false
        $tpl->setVariable( 'refused', $this->refused );
        $tpl->setVariable( 'history_feedback', $this->feedback );
        $tpl->setVariable( 'user_id', $this->userID );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/history.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'History' ), 'url' => false ) );
        if ( $section )
        {
            $Result['navigation_part'] = $section->attribute( 'navigation_part_identifier' );
            $Result['section_id'] = $section->attribute( 'id' );
        }
        return $this->viewResult( $Result, null );
    }

    /** @return int[] the numbers of the versions whose content the user may see (canSeeVersionContent()) */
    protected function seenVersions()
    {
        $seen = array();
        foreach ( $this->object->versions() as $version )
        {
            if ( self::canSeeVersionContent( $version, $this->canRead, $this->canEdit, $this->userID ) )
                $seen[] = (int)$version->attribute( 'version' );
        }
        return $seen;
    }

    /** The comparison of two versions (DiffButton, FromVersion, ToVersion, Language, ExtraOptions) */
    protected function compare()
    {
        $http = $this->http;
        $object = $this->object;
        $this->preselectComparison();
        // CompareButton[n] on a version compares it with the current version (or the newest one the user may see)
        $compareWith = $http->hasPostVariable( 'CompareButton' ) && is_array( $http->postVariable( 'CompareButton' ) )
                       ? array_keys( $http->postVariable( 'CompareButton' ) ) : array();
        if ( !$compareWith && ( !$http->hasPostVariable( 'DiffButton' ) || !$http->hasPostVariable( 'FromVersion' ) || !$http->hasPostVariable( 'ToVersion' ) ) )
            return null;
        if ( !$object->attribute( 'can_diff' ) )
            return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $lang = $http->hasPostVariable( 'Language' ) ? $http->postVariable( 'Language' ) : false;
        if ( $compareWith )
        {
            $oldVersion = $compareWith[0];
            $newVersion = self::comparedWith( (int)$oldVersion, (int)$object->attribute( 'current_version' ), $this->contentVersions );
            $lang = false;
        }
        else
        {
            $oldVersion = $http->postVariable( 'FromVersion' );
            $newVersion = $http->postVariable( 'ToVersion' );
        }
        $oldObject = is_numeric( $oldVersion ) ? $object->version( (int)$oldVersion ) : null;
        $newObject = is_numeric( $newVersion ) ? $object->version( (int)$newVersion ) : null;
        if ( !$oldObject instanceof \eZContentObjectVersion || !$newObject instanceof \eZContentObjectVersion )
            return null;
        if ( !in_array( (int)$oldVersion, $this->contentVersions, true ) || !in_array( (int)$newVersion, $this->contentVersions, true ) )
        {
            // A version given in the request whose content the user may not see is not compared
            $this->refused = array( 'action' => 'diff', 'versions' => array_values( array_diff( array( (int)$oldVersion, (int)$newVersion ), $this->contentVersions ) ) );
            \eZDebug::writeNotice( 'content/history: comparing versions ' . (int)$oldVersion . ' and ' . (int)$newVersion . ' of object ' .
                                   (int)$object->attribute( 'id' ) . ' refused for user ' . $this->userID, __METHOD__ );
            return null;
        }

        if ( $lang )
        {
            $oldAttributes = $object->fetchDataMap( $oldVersion, $lang );
            // Fallback, if the language asked for is not in the version
            if ( !$oldAttributes )
                $oldAttributes = $object->fetchDataMap( $oldVersion, $oldObject->attribute( 'initial_language' )->attribute( 'locale' ) );
            $newAttributes = $object->fetchDataMap( $newVersion, $lang );
            if ( !$newAttributes )
                $newAttributes = $object->fetchDataMap( $newVersion, $newObject->attribute( 'initial_language' )->attribute( 'locale' ) );
        }
        else
        {
            $oldAttributes = $oldObject->dataMap();
            $newAttributes = $newObject->dataMap();
        }

        // Extra options to open up for future extensions of the system.
        $extraOptions = $http->hasPostVariable( 'ExtraOptions' ) ? $http->postVariable( 'ExtraOptions' ) : false;

        // The diff method of each datatype
        $diff = array();
        foreach ( $oldAttributes as $attribute )
        {
            $identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
            if ( !isset( $newAttributes[$identifier] ) )
                continue;
            $newAttr = $newAttributes[$identifier];
            $contentClassAttr = $newAttr->attribute( 'contentclass_attribute' );
            $diff[$contentClassAttr->attribute( 'id' )] = $contentClassAttr->diff( $attribute, $newAttr, $extraOptions );
        }

        $this->tpl->setVariable( 'oldVersion', $oldVersion );
        $this->tpl->setVariable( 'oldVersionObject', $object->version( $oldVersion ) );
        $this->tpl->setVariable( 'newVersion', $newVersion );
        $this->tpl->setVariable( 'newVersionObject', $object->version( $newVersion ) );
        $this->tpl->setVariable( 'diff', $diff );
        $this->tpl->setVariable( 'diff_language', $lang );
        $this->tpl->setVariable( 'selectOldVersion', (int)$oldVersion );
        $this->tpl->setVariable( 'selectNewVersion', (int)$newVersion );
        return null;
    }

    /** The two versions the comparison offers first: the newest and the one before it, of those the user may see */
    protected function preselectComparison()
    {
        $previousVersion = 1;
        $newestVersion = 1;
        $selectable = array();
        foreach ( $this->object->versions( false ) as $versionItem )
        {
            // archived, published and drafts the user may see
            if ( in_array( (int)$versionItem['status'], array( \eZContentObjectVersion::STATUS_DRAFT, \eZContentObjectVersion::STATUS_PUBLISHED,
                                                               \eZContentObjectVersion::STATUS_ARCHIVED ), true ) &&
                 in_array( (int)$versionItem['version'], $this->contentVersions, true ) )
                $selectable[] = (int)$versionItem['version'];
        }
        if ( $selectable && count( $this->object->versions( false ) ) > 1 )
        {
            $newestVersion = array_pop( $selectable );
            if ( $selectable )
                $previousVersion = array_pop( $selectable );
        }
        $this->tpl->setVariable( 'selectOldVersion', $previousVersion );
        $this->tpl->setVariable( 'selectNewVersion', $newestVersion );
    }

    /** The warning that the version history limit was reached (set by content/edit in the session) */
    protected function versionLimitWarning()
    {
        if ( $this->http->hasSessionVariable( 'ExcessVersionHistoryLimit' ) )
        {
            if ( $this->http->sessionVariable( 'ExcessVersionHistoryLimit' ) )
                $this->editWarning = 3;
            $this->http->removeSessionVariable( 'ExcessVersionHistoryLimit' );
        }
        return null;
    }

    /** Removing versions (RemoveButton, DeleteIDArray[]): only versions of this object, in a status the page offers */
    protected function removeVersions()
    {
        $http = $this->http;
        if ( !$http->hasPostVariable( 'RemoveButton' ) )
            return null;
        if ( !$this->canEdit )
            return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        if ( !$http->hasPostVariable( 'DeleteIDArray' ) )
        {
            $this->refused = array( 'action' => 'remove-none', 'versions' => array() );
            return null;
        }
        $objectID = (int)$this->object->attribute( 'id' );
        $db = \eZDB::instance();
        $db->begin();
        $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
        $auditRemoved = array();
        $refusedRemove = array();
        foreach ( is_array( $deleteIDArray ) ? $deleteIDArray : array() as $deleteID )
        {
            if ( !is_numeric( $deleteID ) )
                continue;
            $version = \eZContentObjectVersion::fetch( (int)$deleteID );
            if ( !$version instanceof \eZContentObjectVersion || (int)$version->attribute( 'contentobject_id' ) !== $objectID )
                continue;
            if ( !self::isRemovableStatus( $version->attribute( 'status' ) ) || !$version->attribute( 'can_remove' ) )
            {
                $refusedRemove[] = (int)$version->attribute( 'version' );
                continue;
            }
            $auditRemoved[] = array( 'version' => (int)$version->attribute( 'version' ), 'status' => (int)$version->attribute( 'status' ),
                                     'language' => (string)$version->initialLanguageCode() );
            $version->removeThis();
        }
        $db->commit();

        if ( $refusedRemove )
        {
            $this->refused = array( 'action' => 'remove', 'versions' => $refusedRemove );
            \eZDebug::writeNotice( 'content/history: removing versions ' . implode( ',', $refusedRemove ) . ' of object ' . $objectID .
                                   ' refused for user ' . $this->userID, __METHOD__ );
        }
        if ( $auditRemoved )
        {
            $removedNumbers = array();
            foreach ( $auditRemoved as $removedItem )
                $removedNumbers[] = $removedItem['version'];
            $this->feedback = array( 'type' => 'removed', 'versions' => $removedNumbers );
        }

        // Audit (doc/bc/6.0/audit.md, content.version.remove)
        if ( $auditRemoved && class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'content.version.remove', function () use ( $objectID, $auditRemoved ) {
                $versions = array();
                foreach ( $auditRemoved as $v )
                    $versions[] = $v['version'];
                $o = \expAuditHook::object( $objectID );
                return array( 'object' => array( 'type' => 'version', 'id' => implode( ',', $versions ), 'object_id' => $objectID,
                                                 'name' => isset( $o['name'] ) ? $o['name'] : null ),
                              'before' => array( 'versions' => $auditRemoved ) );
            } );
        return null;
    }

    /** @return int|false the version an action names: HistoryEditButton[n] / HistoryCopyVersionButton[n], else RevertToVersionID */
    protected function actionVersion()
    {
        if ( is_array( $this->module->actionParameter( 'VersionKeyArray' ) ) )
        {
            $keys = array_keys( $this->module->actionParameter( 'VersionKeyArray' ) );
            return isset( $keys[0] ) ? $keys[0] : false;
        }
        return $this->module->hasActionParameter( 'VersionID' ) ? $this->module->actionParameter( 'VersionID' ) : false;
    }

    /** Back to the list, when an action names no version of this object */
    protected function backToList()
    {
        $this->module->redirectToView( 'history', array( $this->object->attribute( 'id' ), $this->object->attribute( 'current_version' ) ) );
        return \eZModule::HOOK_STATUS_CANCEL_RUN;
    }

    /** Editing a version (HistoryEditButton[n]): one's own draft in place; any other version has to be copied first */
    protected function editAction()
    {
        if ( !$this->module->isCurrentAction( 'Edit' ) )
            return null;
        if ( !$this->canEdit )
            return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $versionID = $this->actionVersion();
        $version = is_numeric( $versionID ) ? $this->object->version( (int)$versionID ) : null;
        if ( !$version )
            return $this->backToList();
        $versionID = (int)$version->attribute( 'version' );
        if ( !in_array( $version->attribute( 'status' ), array( \eZContentObjectVersion::STATUS_DRAFT, \eZContentObjectVersion::STATUS_INTERNAL_DRAFT ) ) )
        {
            $this->editWarning = 1;
            $this->editVersion = $versionID;
            return null;
        }
        if ( $version->attribute( 'creator_id' ) != $this->userID )
        {
            $this->editWarning = 2;
            $this->editVersion = $versionID;
            return null;
        }
        return $this->module->redirectToView( 'edit', array( $this->object->attribute( 'id' ), $versionID, $version->initialLanguageCode() ) );
    }

    /** A new draft from a version (HistoryCopyVersionButton[n], CopyVersionLanguage[n], DoNotEditAfterCopy) */
    protected function copyAction()
    {
        if ( !$this->module->isCurrentAction( 'CopyVersion' ) )
            return null;
        if ( !$this->canEdit )
            return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $object = $this->object;
        $versionID = $this->actionVersion();
        $version = is_numeric( $versionID ) ? $object->version( (int)$versionID ) : null;
        // no such version, or an untouched draft (nothing to copy): back to the list
        if ( !$version || $version->attribute( 'status' ) == \eZContentObjectVersion::STATUS_INTERNAL_DRAFT )
            return $this->backToList();

        $versionID = (int)$version->attribute( 'version' );
        $languages = $this->module->actionParameter( 'LanguageArray' );
        $language = ( is_array( $languages ) && isset( $languages[$versionID] ) && is_string( $languages[$versionID] ) )
                    ? $languages[$versionID] : $version->initialLanguageCode();
        // The copy starts in one of the version's own translations
        if ( !in_array( $language, self::versionLanguageCodes( $version ), true ) )
            $language = $version->initialLanguageCode();

        if ( !in_array( $versionID, $this->contentVersions, true ) )
        {
            // A copy shows the content of the version in the editor
            $this->refused = array( 'action' => 'copy', 'versions' => array( $versionID ) );
            \eZDebug::writeNotice( 'content/history: copying version ' . $versionID . ' of object ' . (int)$object->attribute( 'id' ) .
                                   ' refused for user ' . $this->userID . ' (may not read the version)', __METHOD__ );
            return null;
        }
        if ( !$object->editAccess( $version, $language ) )
        {
            $this->refused = array( 'action' => 'copy-language', 'versions' => array( $versionID ), 'language' => $language );
            \eZDebug::writeNotice( 'content/history: copying version ' . $versionID . ' of object ' . (int)$object->attribute( 'id' ) .
                                   ' refused for user ' . $this->userID . ' (may not edit ' . $language . ')', __METHOD__ );
            return null;
        }
        // Copying version (versionHistoryLimit is done in eZContentObject createNewVersion() )
        $db = \eZDB::instance();
        $db->begin();
        $newVersionID = $object->copyRevertTo( $versionID, $language );
        $db->commit();

        if ( !$this->http->hasPostVariable( 'DoNotEditAfterCopy' ) )
            return $this->module->redirectToView( 'edit', array( $object->attribute( 'id' ), $newVersionID, $language ) );
        $this->feedback = array( 'type' => 'copied', 'from' => $versionID, 'to' => (int)$newVersionID, 'language' => $language );
        return null;
    }

    /** The keys of the design resource for template overrides; @return \eZSection|null the object's section */
    protected function designKeys()
    {
        $object = $this->object;
        $res = \eZTemplateDesignResource::instance();
        $res->setKeys( array( array( 'object', $object->attribute( 'id' ) ),
                              array( 'remote_id', $object->attribute( 'remote_id' ) ),
                              array( 'class', $object->attribute( 'contentclass_id' ) ),
                              array( 'class_identifier', $object->attribute( 'class_identifier' ) ),
                              array( 'section_id', $object->attribute( 'section_id' ) ), // typo, deprecated
                              array( 'section', $object->attribute( 'section_id' ) ) ) );
        $section = \eZSection::fetch( $object->attribute( 'section_id' ) );
        if ( $section )
            $res->setKeys( array( array( 'section_identifier', $section->attribute( 'identifier' ) ) ) );
        return $section ? $section : null;
    }

    /**
     * The list: every version as a row, the figures, the filters' choices, the page of rows that match the
     * filters with the actions of each, the newer drafts and the address of the Back button.
     */
    protected function listVariables( array $filters, $offset )
    {
        $tpl = $this->tpl;
        $object = $this->object;
        $all = array();
        $byNumber = array();
        $languageNames = array();
        foreach ( $object->versions() as $version )
        {
            $locale = (string)$version->initialLanguageCode();
            $creator = $version->attribute( 'creator' );
            $all[] = \expContentHistoryList::row( array( 'id' => $version->attribute( 'id' ), 'version' => $version->attribute( 'version' ),
                                                         'status' => $version->attribute( 'status' ), 'language' => $locale,
                                                         'language_name' => self::languageName( $locale, $languageNames ),
                                                         'creator_id' => $version->attribute( 'creator_id' ),
                                                         'creator_name' => $creator ? (string)$creator->attribute( 'name' ) : '',
                                                         'created' => $version->attribute( 'created' ), 'modified' => $version->attribute( 'modified' ) ) );
            $byNumber[(int)$version->attribute( 'version' )] = $version;
        }
        $selected = \expContentHistoryList::select( $all, $filters );
        $count = count( $selected );
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( self::LIST_VIEW, self::LIMIT_PREFERENCE );
        if ( $offset >= $count )
            $offset = 0;

        $page = \expAdminPagination::page( $selected, $offset, $limit );
        // The translations of the versions on this page the user may edit (and so start a new draft in): the same
        // check the copy makes (editAccess() in that language), which also allows a translation the object lacks
        $editLanguages = array();
        $languagesOf = array();
        foreach ( $page as $row )
        {
            $languagesOf[$row['version']] = self::versionLanguageCodes( $byNumber[$row['version']] );
            foreach ( $languagesOf[$row['version']] as $locale )
            {
                if ( $this->canEdit && !in_array( $locale, $editLanguages, true ) && $object->editAccess( null, $locale ) )
                    $editLanguages[] = $locale;
            }
        }
        $context = array( 'can_edit' => $this->canEdit, 'content_versions' => $this->contentVersions, 'edit_languages' => $editLanguages,
                          'user_id' => $this->userID );
        $rows = array();
        foreach ( $page as $row )
        {
            $version = $byNumber[$row['version']];
            $row['languages'] = array();
            foreach ( $languagesOf[$row['version']] as $locale )
                $row['languages'][$locale] = self::languageName( $locale, $languageNames );
            $row['can_versionread'] = (bool)$version->attribute( 'can_read' );
            $row['can_remove'] = (bool)$version->attribute( 'can_remove' );
            $row['actions'] = \expContentHistoryList::actions( $row, $context );
            $row['object'] = $version;
            $rows[] = $row;
        }

        $tpl->setVariable( 'history_rows', $rows );
        $tpl->setVariable( 'history_count', $count );
        $tpl->setVariable( 'history_offset', $offset );
        $tpl->setVariable( 'history_limit', $limit );
        $tpl->setVariable( 'history_limit_choices', $limitChoices );
        $tpl->setVariable( 'history_filters', $filters );
        $tpl->setVariable( 'history_suffix', \expContentHistoryList::suffix( $filters ) );
        $overview = \expContentHistoryList::overview( $all, $this->userID );
        $choices = \expContentHistoryList::choices( $all );
        $tpl->setVariable( 'history_overview', $overview );
        $tpl->setVariable( 'history_choices', $choices );
        $tpl->setVariable( 'history_links', \expContentHistoryList::links( $filters, $overview['statuses'], $choices ) );
        $tpl->setVariable( 'history_status_names', \expContentHistoryList::STATUSES );
        $tpl->setVariable( 'history_edit_languages', $editLanguages );

        // The drafts newer than the current version, as before
        $newerDraftVersionList = \eZPersistentObject::fetchObjectList( \eZContentObjectVersion::definition(), null,
                                                                      array( 'contentobject_id' => $object->attribute( 'id' ),
                                                                             'status' => \eZContentObjectVersion::STATUS_DRAFT,
                                                                             'version' => array( '>', $object->attribute( 'current_version' ) ) ),
                                                                      array( 'modified' => 'asc', 'initial_language_id' => 'desc' ), null, true );
        $tpl->setVariable( 'newerDraftVersionList', $newerDraftVersionList );
        $tpl->setVariable( 'newerDraftVersionListCount', is_array( $newerDraftVersionList ) ? count( $newerDraftVersionList ) : 0 );

        // The Back button: an edit of a version that was just removed is no longer there to go back to
        $removed = ( $this->feedback && $this->feedback['type'] === 'removed' ) ? $this->feedback['versions'] : array();
        if ( $removed && preg_match( '#^/content/edit/\d+/(\d+)(/|$)#', $this->origin, $originMatch ) && in_array( (int)$originMatch[1], $removed, true ) )
            $this->origin = self::originURI( (int)$object->attribute( 'id' ), (int)$object->attribute( 'main_node_id' ), array(), \eZSys::indexDir() );
        $tpl->setVariable( 'redirect_uri', $this->origin );
    }

    /**
     * The version a version's own Compare button compares it with: the current version, or when that is the version
     * itself or one the user may not see, the newest other version the user may see.
     *
     * @param int $version
     * @param int $current the object's current version
     * @param int[] $seen the versions whose content the user may see
     * @return int the version itself when there is no other
     */
    public static function comparedWith( $version, $current, array $seen )
    {
        if ( $current !== $version && in_array( $current, $seen, true ) )
            return $current;
        rsort( $seen );
        foreach ( $seen as $other )
        {
            if ( $other !== $version )
                return $other;
        }
        return $version;
    }

    /** The name of a language by its locale, remembered in $names for the request */
    private static function languageName( $locale, array &$names )
    {
        if ( !isset( $names[$locale] ) )
        {
            $language = $locale !== '' ? \eZContentLanguage::fetchByLocale( $locale ) : false;
            $names[$locale] = $language ? (string)$language->attribute( 'name' ) : $locale;
        }
        return $names[$locale];
    }

    /**
     * Whether the current user may open the versions of $object: who may edit it (eZContentObject::editAccess(),
     * so also a further editor an extension lets in through the filter content/edit/access), and who may read it
     * and has a content/edit policy for some content, as before. The editors of a draft that was never published
     * (readable for its owner only) can so make their next version from a rejected one. A reader without any edit
     * policy (the anonymous user) stays out, as the view's functions read and edit kept them out before.
     *
     * @param \eZContentObject $object
     * @param bool|null $canEdit $object->editAccess(), when the caller has it already
     * @param bool|null $mayEditSomewhere Whether the user has content/edit for some content; asked when null
     * @return bool
     */
    public static function canOpen( $object, $canEdit = null, $mayEditSomewhere = null )
    {
        if ( $canEdit === null )
        {
            $canEdit = $object->editAccess();
        }
        if ( $canEdit )
        {
            return true;
        }
        if ( !$object->attribute( 'can_read' ) )
        {
            return false;
        }
        if ( $mayEditSomewhere === null )
        {
            $user = \eZUser::currentUser();
            $access = $user instanceof \eZUser ? $user->hasAccessTo( 'content', 'edit' ) : array( 'accessWord' => 'no' );
            $mayEditSomewhere = $access['accessWord'] !== 'no';
        }
        return (bool)$mayEditSomewhere;
    }

    /**
     * Whether the user may see the content of $version on the history page: compare it, copy it into a new draft
     * (which shows it in the editor) and follow the link to the version view (which asks content/versionread
     * itself). The content of the published and archived versions is what who may read or edit the object sees
     * anyway (an editor gets the published content in the editor, and restoring an archived version is what the
     * page is for). A rejected version was sent back by a workflow to be made again, so who may edit the object
     * sees it, to make the next version from it. Someone's draft or pending version (and an untouched, queued or
     * repeated one) needs content/versionread for it; one's own versions are always visible, except for the
     * anonymous user, whose versions belong to every visitor.
     *
     * @param \eZContentObjectVersion|null $version
     * @param bool $canRead Whether the user may read the object
     * @param bool $canEdit Whether the user may edit the object
     * @param int $userID The current user's ID
     * @return bool
     */
    public static function canSeeVersionContent( $version, $canRead, $canEdit, $userID )
    {
        if ( !$version instanceof \eZContentObjectVersion )
        {
            return false;
        }
        if ( (int)$version->attribute( 'creator_id' ) === (int)$userID && (int)$userID !== (int)\eZUser::anonymousId() )
        {
            return true;
        }
        $status = (int)$version->attribute( 'status' );
        if ( ( $canRead || $canEdit ) &&
             in_array( $status, array( \eZContentObjectVersion::STATUS_PUBLISHED, \eZContentObjectVersion::STATUS_ARCHIVED ), true ) )
        {
            return true;
        }
        if ( $canEdit && $status === \eZContentObjectVersion::STATUS_REJECTED )
        {
            return true;
        }
        return (bool)$version->attribute( 'can_read' );
    }

    /**
     * The statuses the page offers removal for: draft, archived, rejected, untouched draft. The published version
     * cannot be removed, and a pending, queued or repeated version belongs to a workflow that is still running.
     *
     * @param int $status
     * @return bool
     */
    public static function isRemovableStatus( $status )
    {
        return in_array( (int)$status, array( \eZContentObjectVersion::STATUS_DRAFT, \eZContentObjectVersion::STATUS_ARCHIVED,
                                              \eZContentObjectVersion::STATUS_REJECTED, \eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true );
    }

    /**
     * The pages the Back button may return to, best first: the one the page's form carries (RedirectURI), the edit
     * page that opened it with "Manage versions" (the session's LastAccessesVersionURI, taken once so that a later
     * visit does not find it again) and the page the browser came from (the Referer header).
     *
     * @param \eZHTTPTool $http
     * @return array
     */
    protected static function originCandidates( $http )
    {
        $candidates = array();
        if ( $http->hasPostVariable( 'RedirectURI' ) )
            $candidates[] = $http->postVariable( 'RedirectURI' );
        if ( $http->hasSessionVariable( 'LastAccessesVersionURI' ) )
        {
            $candidates[] = $http->sessionVariable( 'LastAccessesVersionURI' );
            $http->removeSessionVariable( 'LastAccessesVersionURI' );
        }
        $candidates[] = \eZSys::serverVariable( 'HTTP_REFERER', true );
        return $candidates;
    }

    /**
     * Where the Back button of the versions page goes: the first of $candidates that originCandidate() takes, else
     * the object's own location in the full view, else (an object never published) the content dashboard. Never
     * an edit that would make a new draft.
     *
     * @param int $objectID
     * @param int $mainNodeID 0 when the object has no location
     * @param array $candidates see originCandidates()
     * @param string $prefix the siteaccess prefix of the addresses ("/admin" with URI matching), see eZSys::indexDir()
     * @param array|null $allowedHosts, $currentHost as for eZRedirectManager::safeURI()
     * @return string a path of this site without the siteaccess prefix, such as "/content/view/full/2"
     */
    public static function originURI( $objectID, $mainNodeID, array $candidates, $prefix = '', $allowedHosts = null, $currentHost = null )
    {
        foreach ( $candidates as $candidate )
        {
            $uri = self::originCandidate( $candidate, $objectID, $prefix, $allowedHosts, $currentHost );
            if ( $uri !== false )
                return $uri;
        }
        return (int)$mainNodeID > 0 ? '/content/view/full/' . (int)$mainNodeID : '/content/dashboard';
    }

    /**
     * One page the Back button may return to, as a path of this site without the siteaccess prefix, or false: an
     * address that eZRedirectManager::safeURI() refuses (another host, a script, an encoded host ...), the versions
     * page itself, a view a return never goes to (site.ini [SiteSettings] DisallowedReturnViews), and an edit that
     * is not of a version of this object (content/edit/<object> without a version makes a new draft).
     *
     * @param mixed $uri
     * @param int $objectID
     * @param string $prefix
     * @param array|null $allowedHosts
     * @param string|null $currentHost
     * @return string|false
     */
    public static function originCandidate( $uri, $objectID, $prefix = '', $allowedHosts = null, $currentHost = null )
    {
        if ( !is_string( $uri ) || trim( $uri ) === '' || \eZRedirectManager::safeURI( $uri, $allowedHosts, $currentHost ) === false )
            return false;
        $uri = trim( $uri );
        if ( preg_match( '#^(?:https?:)?//#i', $uri ) )
        {
            // an address of this site with its host (the Referer header): its path and query
            $parts = parse_url( preg_match( '#^//#', $uri ) ? 'https:' . $uri : $uri );
            if ( !is_array( $parts ) )
                return false;
            $uri = ( isset( $parts['path'] ) ? $parts['path'] : '/' ) . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
        }
        $path = '/' . ltrim( $uri, '/' );
        $prefix = rtrim( (string)$prefix, '/' );
        if ( $prefix !== '' && ( $path === $prefix || strpos( $path, $prefix . '/' ) === 0 || strpos( $path, $prefix . '?' ) === 0 ) )
            $path = '/' . ltrim( substr( $path, strlen( $prefix ) ), '/' );
        if ( \eZRedirectManager::safeURI( $path, $allowedHosts, $currentHost ) === false )
            return false;

        $view = \eZRedirectManager::moduleView( $path, '' );
        if ( $view === 'content/history' )
            return false;
        if ( $view !== false && in_array( $view, array_map( 'strtolower', \eZRedirectManager::disallowedReturnViews() ), true ) )
            return false;
        if ( $view === 'content/edit' &&
             ( !preg_match( '#^/content/edit/(\d+)/(\d+)(?:[/?]|$)#i', $path, $m ) || (int)$m[1] !== (int)$objectID ) )
            return false;
        return $path;
    }

    /**
     * The language codes of the translations $version has.
     *
     * @param \eZContentObjectVersion $version
     * @return string[]
     */
    private static function versionLanguageCodes( $version )
    {
        $codes = array( $version->initialLanguageCode() );
        $translations = $version->translations( false );
        if ( is_array( $translations ) )
        {
            foreach ( $translations as $code )
            {
                if ( is_string( $code ) )
                    $codes[] = $code;
            }
        }
        return array_values( array_unique( $codes ) );
    }
}

}
