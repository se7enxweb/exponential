<?php
/**
 * The code of kernel/content/history.php, moved into a class (#207 stage 1). The file kernel/content/history.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
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
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $http = \eZHTTPTool::instance();

        $ObjectID = $Params['ObjectID'];
        $EditVersion = $Params['EditVersion'];

        $Offset = $Params['Offset'];
        $viewParameters = array( 'offset' => $Offset );

        if ( $http->hasPostVariable( 'BackButton' )  )
        {
            $userRedirectURI = '';
            if ( $http->hasPostVariable( 'RedirectURI' ) )
            {
                $redurectURI = $http->postVariable( 'RedirectURI' );
                $http->removeSessionVariable( 'LastAccessesVersionURI' );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $redurectURI ) );
            }
            if ( $http->hasSessionVariable( "LastAccessesURI", false ) )
                $userRedirectURI = $http->sessionVariable( "LastAccessesURI" );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $userRedirectURI ) );
        }

        $object = \eZContentObject::fetch( $ObjectID );

        $editWarning = false;

        $canEdit = false;
        $canRemove = false;

        if ( $object === null )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $canEdit = (bool)$object->editAccess();
        if ( !self::canOpen( $object, $canEdit ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $canRead = (bool)$object->attribute( 'can_read' );
        $currentUserID = (int)\eZUser::currentUserID();
        // Versions whose content the user may see (open, compare, copy): see canSeeVersionContent()
        $contentVersions = array();
        foreach ( $object->versions() as $versionItem )
        {
            if ( self::canSeeVersionContent( $versionItem, $canRead, $canEdit, $currentUserID ) )
                $contentVersions[] = (int)$versionItem->attribute( 'version' );
        }
        // Set below when an action of the page is refused for some versions: array( 'action' => ..., 'versions' => array( ... ) )
        $refused = false;

        $canRemove = true;

        //content/diff functionality
        //Set default values
        $previousVersion = 1;
        $newestVersion = 1;

        //By default, set preselect the previous and most recent version for diffing
        if ( count( $object->versions() ) > 1 )
        {
            $versionArray = $object->versions( false );
            $selectableVersions = array();
            foreach( $versionArray as $versionItem )
            {
                //Only return version numbers of archived or published items, and drafts, the user may see
                if ( in_array( $versionItem['status'], array( \eZContentObjectVersion::STATUS_DRAFT,
                                                              \eZContentObjectVersion::STATUS_PUBLISHED,
                                                              \eZContentObjectVersion::STATUS_ARCHIVED ) ) &&
                     in_array( (int)$versionItem['version'], $contentVersions, true ) )
                {
                    $selectableVersions[] = $versionItem['version'];
                }
            }
            if ( $selectableVersions )
                $newestVersion = array_pop( $selectableVersions );
            if ( $selectableVersions )
                $previousVersion = array_pop( $selectableVersions );
        }

        $tpl->setVariable( 'selectOldVersion', $previousVersion );
        $tpl->setVariable( 'selectNewVersion', $newestVersion );
        $tpl->setVariable( 'module', $Module );

        $diff = array();

        if ( $http->hasPostVariable('DiffButton') && $http->hasPostVariable( 'FromVersion' ) && $http->hasPostVariable( 'ToVersion' ) )
        {
            if ( !$object->attribute( 'can_diff' ) )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

            $lang = false;
            if ( $http->hasPostVariable( 'Language' ) )
            {
                $lang = $http->postVariable( 'Language' );
            }
            $oldVersion = $http->postVariable( 'FromVersion' );
            $newVersion = $http->postVariable( 'ToVersion' );

            $oldObject = is_numeric( $oldVersion ) ? $object->version( (int)$oldVersion ) : null;
            $newObject = is_numeric( $newVersion ) ? $object->version( (int)$newVersion ) : null;
            if ( $oldObject instanceof \eZContentObjectVersion && $newObject instanceof \eZContentObjectVersion &&
                 ( !in_array( (int)$oldVersion, $contentVersions, true ) || !in_array( (int)$newVersion, $contentVersions, true ) ) )
            {
                // A version given in the request whose content the user may not see is not compared
                $refused = array( 'action' => 'diff', 'versions' => array_values( array_diff( array( (int)$oldVersion, (int)$newVersion ), $contentVersions ) ) );
                \eZDebug::writeNotice( 'content/history: comparing versions ' . (int)$oldVersion . ' and ' . (int)$newVersion . ' of object ' . (int)$ObjectID .
                                       ' refused for user ' . $currentUserID, __METHOD__ );
            }
            else if ( $oldObject instanceof \eZContentObjectVersion && $newObject instanceof \eZContentObjectVersion )
            {

                if ( $lang )
                {
                    $oldAttributes = $object->fetchDataMap( $oldVersion, $lang );
                    //Fallback, if desired language not available in version
                    if ( !$oldAttributes )
                    {
                        $oldObjectLang = $oldObject->attribute( 'initial_language' );
                        $oldAttributes = $object->fetchDataMap( $oldVersion, $oldObjectLang->attribute( 'locale' ) );
                    }
                    $newAttributes = $object->fetchDataMap( $newVersion, $lang );
                    //Fallback, if desired language not available in version
                    if ( !$newAttributes )
                    {
                        $newObjectLang = $newObject->attribute( 'initial_language' );
                        $newAttributes = $object->fetchDataMap( $newVersion, $newObjectLang->attribute( 'locale' ) );
                    }

                }
                else
                {
                    $oldAttributes = $oldObject->dataMap();
                    $newAttributes = $newObject->dataMap();
                }

                //Extra options to open up for future extensions of the system.
                $extraOptions = false;
                if ( $http->hasPostVariable( 'ExtraOptions' ) )
                {
                    $extraOptions = $http->postVariable( 'ExtraOptions' );
                }

                //Invoke diff method in the datatype
                foreach ( $oldAttributes as $attribute )
                {
                    $identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
                    if ( !isset( $newAttributes[$identifier] ) )
                        continue;
                    $newAttr = $newAttributes[$identifier];
                    $contentClassAttr = $newAttr->attribute( 'contentclass_attribute' );
                    $diff[$contentClassAttr->attribute( 'id' )] = $contentClassAttr->diff( $attribute, $newAttr, $extraOptions );
                }

                $tpl->setVariable( 'oldVersion', $oldVersion );
                $tpl->setVariable( 'oldVersionObject', $object->version( $oldVersion ) );

                $tpl->setVariable( 'newVersion', $newVersion );
                $tpl->setVariable( 'newVersionObject', $object->version( $newVersion ) );
                $tpl->setVariable( 'diff', $diff );
            }
        }
        //content/diff end

        //content/versions
        if ( $http->hasSessionVariable( 'ExcessVersionHistoryLimit' ) )
        {
            $excessLimit = $http->sessionVariable( 'ExcessVersionHistoryLimit' );
            if ( $excessLimit )
                $editWarning = 3;
            $http->removeSessionVariable( 'ExcessVersionHistoryLimit' );
        }

        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            if ( !$canEdit )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $db = \eZDB::instance();
                $db->begin();

                $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
                $versionArray = array();
                $auditRemoved = array();
                $refusedRemove = array();
                foreach ( is_array( $deleteIDArray ) ? $deleteIDArray : array() as $deleteID )
                {
                    if ( !is_numeric( $deleteID ) )
                        continue;
                    $version = \eZContentObjectVersion::fetch( (int)$deleteID );
                    // Only versions of this object, in a status the page offers for removal
                    if ( !$version instanceof \eZContentObjectVersion ||
                         (int)$version->attribute( 'contentobject_id' ) !== (int)$object->attribute( 'id' ) )
                        continue;
                    $versionArray[] = $version->attribute( 'version' );
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
                    $refused = array( 'action' => 'remove', 'versions' => $refusedRemove );
                    \eZDebug::writeNotice( 'content/history: removing versions ' . implode( ',', $refusedRemove ) . ' of object ' . (int)$ObjectID .
                                           ' refused for user ' . $currentUserID, __METHOD__ );
                }
                // The versions removed are no longer offered
                if ( $auditRemoved )
                {
                    $removedNumbers = array();
                    foreach ( $auditRemoved as $removedItem )
                        $removedNumbers[] = $removedItem['version'];
                    $contentVersions = array_values( array_diff( $contentVersions, $removedNumbers ) );
                }

                // Audit (doc/bc/6.0/audit.md, content.version.remove)
                if ( $auditRemoved && class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'content.version.remove', function () use ( $ObjectID, $auditRemoved ) {
                        $versions = array();
                        foreach ( $auditRemoved as $v )
                            $versions[] = $v['version'];
                        $o = \expAuditHook::object( (int)$ObjectID );
                        return array( 'object' => array( 'type' => 'version', 'id' => implode( ',', $versions ), 'object_id' => (int)$ObjectID,
                                                         'name' => isset( $o['name'] ) ? $o['name'] : null ),
                                      'before' => array( 'versions' => $auditRemoved ) );
                    } );
            }
        }

        $user = \eZUser::currentUser();

        if ( $Module->isCurrentAction( 'Edit' )  )
        {
            if ( !$canEdit )
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

            $versionID = false;

            if ( is_array( $Module->actionParameter( 'VersionKeyArray' ) ) )
            {
                $versionID = array_keys( $Module->actionParameter( 'VersionKeyArray' ) );
                $versionID = $versionID[0];
            }
            else if ( $Module->hasActionParameter( 'VersionID' ) )
                $versionID = $Module->actionParameter( 'VersionID' );

            $version = is_numeric( $versionID ) ? $object->version( (int)$versionID ) : null;
            if ( !$version )
            {
                // No such version of this object: back to the list, as copying does
                $Module->redirectToView( 'history', array( $ObjectID, $object->attribute( 'current_version' ) ) );
                return $this->viewResult( isset( $Result ) ? $Result : null,  \eZModule::HOOK_STATUS_CANCEL_RUN );
            }
            $versionID = (int)$version->attribute( 'version' );

            if ( $versionID !== false and
                 !in_array( $version->attribute( 'status' ), array( \eZContentObjectVersion::STATUS_DRAFT, \eZContentObjectVersion::STATUS_INTERNAL_DRAFT ) ) )
            {
                $editWarning = 1;
                $EditVersion = $versionID;
            }
            else if ( $versionID !== false and
                      $version->attribute( 'creator_id' ) != $user->attribute( 'contentobject_id' ) )
            {
                $editWarning = 2;
                $EditVersion = $versionID;
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'edit', array( $ObjectID, $versionID, $version->initialLanguageCode() ) ) );
            }
        }

        if ( $Module->isCurrentAction( 'CopyVersion' )  )
        {
            if ( !$canEdit )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }

            if ( is_array( $Module->actionParameter( 'VersionKeyArray' ) ) )
            {
                $versionID = array_keys( $Module->actionParameter( 'VersionKeyArray' ) );
                $versionID = $versionID[0];
            }
            else
            {
                $versionID = $Module->actionParameter( 'VersionID' );
            }

            $version = $object->version( $versionID );
            if ( !$version )
                $versionID = false;

            // if we cannot fetch version with given versionID or if fetched version is
            // an internal-draft then just skip copying and redirect back to the history view
            if ( !$versionID or $version->attribute( 'status' ) == \eZContentObjectVersion::STATUS_INTERNAL_DRAFT )
            {
                $currentVersion = $object->attribute( 'current_version' );
                $Module->redirectToView( 'history', array( $ObjectID, $currentVersion ) );
                return $this->viewResult( isset( $Result ) ? $Result : null,  \eZModule::HOOK_STATUS_CANCEL_RUN );
            }

            $versionID = (int)$version->attribute( 'version' );
            $languages = $Module->actionParameter( 'LanguageArray' );
            $language = ( is_array( $languages ) && isset( $languages[$versionID] ) && is_string( $languages[$versionID] ) )
                        ? $languages[$versionID] : $version->initialLanguageCode();
            // The copy starts in one of the version's own translations
            if ( !in_array( $language, self::versionLanguageCodes( $version ), true ) )
                $language = $version->initialLanguageCode();

            if ( !in_array( $versionID, $contentVersions, true ) )
            {
                // A copy shows the content of the version in the editor
                $refused = array( 'action' => 'copy', 'versions' => array( $versionID ) );
                \eZDebug::writeNotice( 'content/history: copying version ' . $versionID . ' of object ' . (int)$ObjectID .
                                       ' refused for user ' . $currentUserID . ' (may not read the version)', __METHOD__ );
            }
            else if ( !$object->editAccess( $version, $language ) )
            {
                $refused = array( 'action' => 'copy-language', 'versions' => array( $versionID ), 'language' => $language );
                \eZDebug::writeNotice( 'content/history: copying version ' . $versionID . ' of object ' . (int)$ObjectID .
                                       ' refused for user ' . $currentUserID . ' (may not edit ' . $language . ')', __METHOD__ );
            }
            else
            {
                // Copying version (versionHistoryLimit is done in eZContentObject createNewVersion() )
                $db = \eZDB::instance();
                $db->begin();
                $newVersionID = $object->copyRevertTo( $versionID, $language );
                $db->commit();

                if ( !$http->hasPostVariable( 'DoNotEditAfterCopy' ) )
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'edit', array( $ObjectID, $newVersionID, $language ) ) );
                }
                // The new draft is the user's own
                if ( $newVersionID )
                    $contentVersions[] = (int)$newVersionID;
            }
        }

        $res = \eZTemplateDesignResource::instance();
        $res->setKeys( array( array( 'object', $object->attribute( 'id' ) ), // Object ID
                              array( 'remote_id', $object->attribute( 'remote_id' ) ),
                              array( 'class', $object->attribute( 'contentclass_id' ) ), // Class ID
                              array( 'class_identifier', $object->attribute( 'class_identifier' ) ), // Class identifier
                              array( 'section_id', $object->attribute( 'section_id' ) ), // Section ID, typo, deprecated
                              array( 'section', $object->attribute( 'section_id' ) ) // Section ID
                              ) ); // Section ID, 0 so far

        $section = \eZSection::fetch( $object->attribute( 'section_id' ) );
        if( $section )
        {
            $res->setKeys( array( array( 'section_identifier', $section->attribute( 'identifier' ) ) ) );
        }

        $versionArray =( isset( $versionArray ) && is_array( $versionArray ) ) ? array_unique( $versionArray, SORT_REGULAR ) : array();
        $LastAccessesVersionURI = $http->hasSessionVariable( 'LastAccessesVersionURI' ) ? $http->sessionVariable( 'LastAccessesVersionURI' ) : null;
        $explodedURI = $LastAccessesVersionURI ? explode ( '/', $LastAccessesVersionURI ) : null;
        if ( $LastAccessesVersionURI and is_array( $versionArray ) and !in_array( $explodedURI[3], $versionArray ) )
          $tpl->setVariable( 'redirect_uri', $http->sessionVariable( 'LastAccessesVersionURI' ) );

        //Fetch newer drafts and count of newer drafts.
        $newerDraftVersionList = \eZPersistentObject::fetchObjectList( \eZContentObjectVersion::definition(),
                                                                      null,
                                                                      array( 'contentobject_id' => $object->attribute( 'id' ),
                                                                             'status' => \eZContentObjectVersion::STATUS_DRAFT,
                                                                             'version' => array( '>', $object->attribute( 'current_version' ) ) ),
                                                                      array( 'modified' => 'asc',
                                                                             'initial_language_id' => 'desc' ),
                                                                      null, true );
        $newerDraftVersionListCount = is_array( $newerDraftVersionList ) ? count( $newerDraftVersionList ) : 0;

        $versions = $object->versions();

        $tpl->setVariable( 'newerDraftVersionList', $newerDraftVersionList );
        $tpl->setVariable( 'newerDraftVersionListCount', $newerDraftVersionListCount );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'object', $object );
        $tpl->setVariable( 'edit_version', $EditVersion );
        $tpl->setVariable( 'versions', $versions );
        $tpl->setVariable( 'edit_warning', $editWarning );
        $tpl->setVariable( 'can_edit', $canEdit );
        // Whether the user may read the object; false for an editor who may edit it only (the page says so)
        $tpl->setVariable( 'can_read', $canRead );
        // The numbers of the versions whose content the user may see (compare, copy, the links to the version view)
        $tpl->setVariable( 'content_versions', $contentVersions );
        // An action of the page refused for some versions, or false
        $tpl->setVariable( 'refused', $refused );
        //$tpl->setVariable( 'can_remove', $canRemove );
        $tpl->setVariable( 'user_id', $user->attribute( 'contentobject_id' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/history.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'History' ),
                                        'url' => false ) );
        if ( $section )
        {
            $Result['navigation_part'] = $section->attribute( 'navigation_part_identifier' );
            $Result['section_id'] = $section->attribute( 'id' );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
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
