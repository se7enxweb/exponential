<?php
/**
 * The code of kernel/collaboration/action.php, moved into a class (#207 stage 1). The file kernel/collaboration/action.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/action.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Collaboration
{

class Action extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $http = \eZHTTPTool::instance();

        if ( $Module->isCurrentAction( 'Custom' ) )
        {
            $typeIdentifier = $Module->actionParameter( 'TypeIdentifer' );
            $itemID = $Module->actionParameter( 'ItemID' );
            $collaborationItem = is_numeric( $itemID ) ? \eZCollaborationItem::fetch( (int)$itemID ) : null;
            // Only someone who may open the item acts on it (filter collaboration/item/access); what each action
            // needs beyond that (the approver role to approve) is still decided by the handler
            $denied = self::access( $collaborationItem, $typeIdentifier, \eZUser::currentUser() );
            if ( $denied !== null )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( $denied, 'kernel' ) );
            }
            $handler = $collaborationItem->handler();
            if ( !$handler instanceof \eZCollaborationItemHandler )
            {
                return $this->viewResult( $scope['Result'] ?? null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }
            return $this->viewResult( $scope['Result'] ?? null, $handler->handleCustomAction( $Module, $collaborationItem ) );
        }

        $Result = array();
        $Result['content'] = false;
        $Result['path'] = array( array( 'url' => false,
                                        \ezpI18n::tr( 'kernel/collaboration', 'Collaboration custom action' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * Whether $user may act on $collabItem: null when they may, otherwise the error to answer with. The item must
     * exist and be of the type the form names (not available otherwise), and the user must be allowed to open it
     * (Item::access(), the participants and whoever the filter collaboration/item/access lets in).
     *
     * @param \eZCollaborationItem|null $collabItem
     * @param string $typeIdentifier The type the form names
     * @param \eZUser $user
     * @return int|null
     */
    public static function access( $collabItem, $typeIdentifier, $user )
    {
        if ( !$collabItem instanceof \eZCollaborationItem ||
             (string)$collabItem->attribute( 'type_identifier' ) !== (string)$typeIdentifier )
        {
            return \eZError::KERNEL_NOT_AVAILABLE;
        }
        return Item::access( $collabItem, $user ) ? null : \eZError::KERNEL_ACCESS_DENIED;
    }
}

}
