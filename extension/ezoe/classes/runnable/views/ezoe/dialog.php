<?php
/**
 * The code of extension/ezoe/modules/ezoe/dialog.php, moved into a class (#207 stage 1). The file extension/ezoe/modules/ezoe/dialog.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Ezoe\Ezoe
{

class Dialog extends \Exponential\Runnable\ModuleView
{
    /**
     * Whether the dialogs of the editor open for $object: for whoever may read the object, as before, and for whoever
     * may edit the version being edited. Someone who edits a draft of an object that was never published can not read
     * the object yet (it has no location), and needs the dialogs of the editor they work in.
     *
     * "May edit the version" is decided the way content/edit decides it (Edit::findEditVersion() and the check
     * before the edit page): see mayEditVersion(). A version number of someone else's draft, of a published,
     * archived or pending version, or of a version that does not exist opens nothing for who may not read the
     * object.
     *
     * @param \eZContentObject|null $object
     * @param int $versionNumber The version being edited
     * @return bool
     */
    public static function mayOpen( $object, $versionNumber )
    {
        if ( !$object instanceof \eZContentObject )
        {
            return false;
        }
        if ( $object->canRead() )
        {
            return true;
        }
        return self::mayEditVersion( $object, $versionNumber );
    }

    /**
     * Whether the current user may edit version $versionNumber of $object in the editor, decided as content/edit
     * decides it: the version exists, is a draft (a draft, an internal draft or one to be repeated), was made by the
     * current user, the object is not in the trash, and eZContentObject::editAccess() allows it for the version in its
     * own language (so a Language limitation applies, and a listener of the filter content/edit/access has its say).
     *
     * @param \eZContentObject|null $object
     * @param int $versionNumber
     * @return bool
     */
    public static function mayEditVersion( $object, $versionNumber )
    {
        $versionNumber = (int)$versionNumber;
        if ( !$object instanceof \eZContentObject || $versionNumber < 1 )
        {
            return false;
        }
        if ( (int)$object->attribute( 'status' ) === \eZContentObject::STATUS_ARCHIVED )
        {
            return false;
        }
        $version = $object->version( $versionNumber );
        if ( !$version instanceof \eZContentObjectVersion
             || (int)$version->attribute( 'contentobject_id' ) !== (int)$object->attribute( 'id' )
             || (int)$version->attribute( 'version' ) !== $versionNumber )
        {
            return false;
        }
        if ( !in_array( (int)$version->attribute( 'status' ), array( \eZContentObjectVersion::STATUS_DRAFT,
                                                                       \eZContentObjectVersion::STATUS_INTERNAL_DRAFT,
                                                                       \eZContentObjectVersion::STATUS_REPEAT ), true ) )
        {
            return false;
        }
        $userID = (int)\eZUser::currentUserID();
        if ( $userID < 1 ||(int)$version->attribute( 'creator_id' ) !== (int)$userID )
        {
            return false;
        }
        $language = $version->initialLanguageCode();
        return (bool)$object->editAccess( $version, is_string( $language ) && $language !== '' ? $language : false );
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $objectID      = isset( $Params['ObjectID'] ) ? (int) $Params['ObjectID'] : 0;
        $objectVersion = isset( $Params['ObjectVersion'] ) ? (int) $Params['ObjectVersion'] : 0;
        $dialog        = isset( $Params['Dialog'] ) ? trim( $Params['Dialog'] ) : '';

        if ( $objectID === 0  || $objectVersion === 0 )
        {
           echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid or missing parameter: %parameter', null, array( '%parameter' => 'ObjectID/ObjectVersion' ) );
           \eZExecution::cleanExit();
        }

        $object = \eZContentObject::fetch( $objectID );
        if ( !self::mayOpen( $object, $objectVersion ) )
        {
           echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid parameter: %parameter = %value', null, array( '%parameter' => 'ObjectId', '%value' => $objectID ) );
           \eZExecution::cleanExit();
        }


        if ( $dialog === '' )
        {
           echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid or missing parameter: %parameter', null, array( '%parameter' => 'Dialog' ) );
           \eZExecution::cleanExit();
        }





        $ezoeInfo = \eZExtension::extensionInfo( 'ezoe' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'object', $object );
        $tpl->setVariable( 'object_id', $objectID );
        $tpl->setVariable( 'object_version', $objectVersion );

        $tpl->setVariable( 'ezoe_name', $ezoeInfo['name'] );
        $tpl->setVariable( 'ezoe_version', $ezoeInfo['version'] );
        $tpl->setVariable( 'ezoe_copyright', $ezoeInfo['copyright'] );
        $tpl->setVariable( 'ezoe_license', $ezoeInfo['license'] );
        $tpl->setVariable( 'ezoe_info_url', $ezoeInfo['info_url'] );

        // use persistent_variable like content/view does, sending parameters
        // to pagelayout as a hash.
        $tpl->setVariable( 'persistent_variable', array() );




        // run template and return result
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:ezoe/' . $dialog . '.tpl' );
        $Result['pagelayout'] = 'design:ezoe/popup_pagelayout.tpl';
        $Result['persistent_variable'] = $tpl->variable( 'persistent_variable' );
        return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
