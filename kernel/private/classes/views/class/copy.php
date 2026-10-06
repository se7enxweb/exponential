<?php
/**
 * The code of kernel/class/copy.php, moved into a class (#207 stage 1). The file kernel/class/copy.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/copy.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Class
{

class Copy extends \Exponential\Runnable\ModuleView
{
    /** The buttons that copy: CopyClassButton on the class lists, ConfirmCopyButton on the confirmation page. */
    const COPY_BUTTONS = array( 'CopyClassButton', 'ConfirmCopyButton' );

    /**
     * Whether this request makes the copy: a POST carrying one of the copy buttons. Anything else only shows the
     * confirmation.
     *
     * @param \eZHTTPTool|object $http anything with hasPostVariable()
     * @param string $method the request method
     * @return bool
     */
    public static function isCopyRequest( $http, $method )
    {
        if ( strtoupper( (string)$method ) !== 'POST' )
            return false;
        foreach ( self::COPY_BUTTONS as $button )
            if ( $http->hasPostVariable( $button ) )
                return true;
        return false;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $ClassID = null;
        if ( isset( $Params["ClassID"] ) )
            $ClassID = $Params["ClassID"];
        $class = \eZContentClass::fetch( $ClassID, true, 0 );
        if ( !$class )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE ) );

        // A copy is made only by a form: a POST (which the form token protects) with one of the copy buttons. A plain
        // link - an old bookmark, a template that still links here, a page that sends the browser here - only shows
        // what would be copied, with a Copy button that posts.
        if ( !self::isCopyRequest( \eZHTTPTool::instance(), isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET' ) )
        {
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'module', $Module );
            $tpl->setVariable( 'class', $class );
            $tpl->setVariable( 'copy_redirect', strtolower( trim( \eZINI::instance( 'content.ini' )->variable( 'CopySettings', 'ClassRedirect' ) ) ) );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:class/copy_confirm.tpl' );
            $Result['path'] = array( array( 'url' => '/class/grouplist/', 'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ),
                                     array( 'url' => '/class/view/' . $class->attribute( 'id' ), 'text' => (string)$class->attribute( 'name' ) ),
                                     array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/class/copy', 'Copy' ) ) );
            return $this->viewResult( $Result, null );
        }

        $classCopy = clone $class;
        $classCopy->initializeCopy( $class );
        $classCopy->setAttribute( 'version', \eZContentClass::VERSION_STATUS_MODIFIED );
        $classCopy->store();

        // Audit (doc/bc/6.0/audit.md, content.class.copy)
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'content.class.copy', function () use ( $class, $classCopy ) {
                return array( 'object' => \expAuditHook::contentClass( $class ),
                              'target' => array( 'type' => 'class', 'id' => (int)$classCopy->attribute( 'id' ) ),
                              'after' => array( 'identifier' => (string)$classCopy->attribute( 'identifier' ) ) );
            } );

        $mainGroupID = false;
        $classGroups = \eZContentClassClassGroup::fetchGroupList( $class->attribute( 'id' ),
                                                                  $class->attribute( 'version' ) );
        for ( $i = 0; $i < count( $classGroups ); ++$i )
        {
            $classGroup =& $classGroups[$i];
            $classGroup->setAttribute( 'contentclass_id', $classCopy->attribute( 'id' ) );
            $classGroup->setAttribute( 'contentclass_version', $classCopy->attribute( 'version' ) );
            $classGroup->store();
            if ( $mainGroupID === false )
                $mainGroupID = $classGroup->attribute( 'group_id' );
        }

        $classAttributeCopies = array();
        $classAttributes = $class->fetchAttributes();
        foreach ( array_keys( $classAttributes ) as $classAttributeKey )
        {
            $classAttribute =& $classAttributes[$classAttributeKey];
            $classAttributeCopy = clone $classAttribute;

            if ( $datatype = $classAttributeCopy->dataType() ) //avoiding fatal error if datatype not exist (was removed).
            {
                $datatype->cloneClassAttribute( $classAttribute, $classAttributeCopy );
            }
            else
            {
                continue;
            }

            $classAttributeCopy->setAttribute( 'contentclass_id', $classCopy->attribute( 'id' ) );
            $classAttributeCopy->setAttribute( 'version', \eZContentClass::VERSION_STATUS_MODIFIED );
            $classAttributeCopy->store();
            $classAttributeCopies[] =& $classAttributeCopy;
            unset( $classAttributeCopy );
        }

        $ini = \eZINI::instance( 'content.ini' );
        $classRedirect = strtolower( trim( $ini->variable( 'CopySettings', 'ClassRedirect' ) ) );

        switch ( $classRedirect )
        {
            case 'grouplist':
            {
                $classCopy->storeDefined( $classAttributeCopies );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'grouplist', array() ) );
            } break;

            case 'classlist':
            {
                $classCopy->storeDefined( $classAttributeCopies );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'classlist', array( $mainGroupID ) ) );
            } break;

            case 'classview':
            {
                $classCopy->storeDefined( $classAttributeCopies );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( $classCopy->attribute( 'id' ) ) ) );
            } break;

            default:
            {
                \eZDebug::writeWarning( "Invalid ClassRedirect value '$classRedirect', use one of: grouplist, classlist, classedit or classview" );
            }

            case 'classedit':
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'edit', array( $classCopy->attribute( 'id' ) ) ) );
            } break;
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
