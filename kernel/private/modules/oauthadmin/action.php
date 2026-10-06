<?php
/**
 * File containing the oauthadmin/action view definition
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$session = ezcPersistentSessionInstance::get();

$module = $Params['Module'];

// new application: create draft, redirect to edit this draft
if ( $module->isCurrentAction( 'NewApplication' ) )
{
    $user = eZUser::currentUser();
    $application = new ezpRestClient();
    $application->name = ezpI18n::tr( 'extension/oauthadmin', 'New REST application' );
    $application->version = ezpRestClient::STATUS_DRAFT;
    $application->owner_id = $user->attribute( 'contentobject_id' );
    $application->created = time();
    $application->updated = 0;
    $application->version = ezpRestClient::STATUS_DRAFT;

    $session->save( $application );

    // The following does not work on PostgreSQL, incorrect id. Probably need refresh from DB.
    return $module->redirectToView( 'edit', array( $application->id ) );
}

// delete several applications
// Used from full view and checkboxes in view list
if ( $module->isCurrentAction( 'DeleteApplicationList' ) )
{
    $applicationList = array();
    $applicationIdList = $module->actionParameter( 'ApplicationIDList' );

    if ( $applicationIdList == null )
    {
        return $module->redirectToView( 'list' );
    }

    // an id that is gone (removed in another window) is left out instead of ending the request
    foreach ( (array)$applicationIdList as $applicationId )
    {
        $application = $session->loadIfExists( 'ezpRestClient', (int)$applicationId );
        if ( $application instanceof ezpRestClient )
            $applicationList[] = $application;
    }
    if ( !$applicationList )
        return $module->redirectToView( 'list' );

    if ( $module->hasActionParameter( 'ConfirmDelete') )
    {
        // confirmed, remove the applications, and with each its authorizations, tokens and codes: they are no use
        // without it, and a token of a removed application must not come back to life if the id is ever reused
        $db = eZDB::instance();
        foreach ( $applicationList as $application )
        {
            if ( $db->databaseName() !== 'mongo' )
            {
                $clientID = $db->escapeString( (string)$application->client_id );
                $db->begin();
                $db->query( 'DELETE FROM ezprest_authorized_clients WHERE rest_client_id = ' . (int)$application->id );
                $db->query( "DELETE FROM ezprest_token WHERE client_id = '$clientID'" );
                $db->query( "DELETE FROM ezprest_authcode WHERE client_id = '$clientID'" );
                $db->commit();
            }
            $session->delete( $application );
        }
        return $module->redirectToView( 'list' );
    }
    else
    {
        // display confirmation request
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'module', $module );
        $tpl->setVariable( 'applications', $applicationList );
        $Result['path'] = array( array( 'url' => 'oauthadmin/list',
                                        'text' => ezpI18n::tr( 'extension/oauthadmin', 'oAuth admin' ) ),
                                 array( 'url' => false,
                                        'text' => ezpI18n::tr( 'extension/oauthadmin', 'Confirm removal' ) )
        );

        $Result['content'] = $tpl->fetch( 'design:oauthadmin/delete_confirmation.tpl' );
        return $Result;
    }
}

return $module->redirectToView( 'list' );
?>
