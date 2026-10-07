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
     * Whether the dialogs of the editor open for $object: for whoever may read the object, and for whoever may edit
     * the version being edited. Someone who edits a draft of an object that was never published can not read the
     * object yet (it has no location), and an extension may let others edit a version (filter content/edit/access,
     * as for uploads and custom tags); both need the dialogs of the editor they work in.
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
        $version = $object->version( (int)$versionNumber );
        return $version instanceof \eZContentObjectVersion && (bool)$object->editAccess( $version );
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
