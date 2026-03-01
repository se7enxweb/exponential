<?php
/// ###exp_feature_g1013_ez2014.11### collaboration/item erweitern um zusätzliche Abfrage für ein "ezapprove2/superadmin"-Recht ///
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$ViewMode = $Params['ViewMode'];
$ItemID = $Params['ItemID'];

$Offset = $Params['Offset'];
if ( !is_numeric( $Offset ) )
    $Offset = 0;

/** @var eZCollaborationItem $collabItem */
$collabItem = eZCollaborationItem::fetch( $ItemID );

/// ###exp_feature_g1013_ez2014.11### ///
// If the user is no superadmin and no participant, we're going to throw an
// access denied error here.
if ( !($collabItem instanceof eZCollaborationItem) )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel', array() );
}

/// ###exp_feature_g1013_ez2014.11###
$contentObjectID = $collabItem->contentAttribute( 'content_object_id' );
$contentObjectVersion = $collabItem->contentAttribute( 'content_object_version' );
//$contentObject = eZContentObject::fetch( $contentObjectID );
/** @var eZContentObjectVersion $versionObject */
$versionObject = eZContentObjectVersion::fetchVersion( $contentObjectVersion, $contentObjectID );

/// ###exp_feature_g1013_ez2014.11### ///
$superadminAccess = eZUser::currentUser()->hasAccessTo( 'ezapprove2', 'superadmin' );
$isSuperAdmin     = $superadminAccess['accessWord'] === 'yes';
$isParticipant    = $collabItem->userIsParticipant( eZUser::currentUser() );
// Check if the user is approver for this collaboration item by doing a count
// fetch on the participant links for this collaboration limited to the current
// user's ID and the approver "participant_role".
$isApprover       = !!eZPersistentObject::count(
    eZCollaborationItemParticipantLink::definition(),
    [
        "collaboration_id" => $collabItem->ID,
        "participant_id"   => eZUser::currentUserID(),
        "participant_role" => eZCollaborationItemParticipantLink::ROLE_APPROVER,
    ]
);

/// ###exp_feature_g1013_ez2014.11### ///
// If the user is no superadmin and no participant, we're going to throw an
// access denied error here.
if ( !($isSuperAdmin && $versionObject->canVersionRead()) && !$isParticipant )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel', array() );
}

$collabHandler = $collabItem->handler();
$collabItem->handleView( $ViewMode );
$template = $collabHandler->template( $ViewMode );
$collabTitle = $collabItem->title();

$viewParameters = array( 'offset' => $Offset );

$tpl = eZTemplate::factory();

$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'collab_item', $collabItem );

/// ###exp_feature_g1013_ez2014.11###
// Additional variables for right checking in template ...
$tpl->setVariable( 'is_superadmin', $isSuperAdmin );
$tpl->setVariable( 'is_participant', $isParticipant );
$tpl->setVariable( 'is_approver', $isApprover );

$Result = array();
$Result['content'] = $tpl->fetch( $template );

$collabHandler->readItem( $collabItem );

$Result['path'] = array( array( 'url' => 'collaboration/view/summary',
                                'text' => ezpI18n::tr( 'kernel/collaboration', 'Collaboration' ) ),
                         array( 'url' => false,
                                'text' => $collabTitle ) );

?>
