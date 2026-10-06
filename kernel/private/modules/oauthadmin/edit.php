<?php
/**
 * File containing the oauthadmin/edit view definition
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$session = ezcPersistentSessionInstance::get();

$module = $Params['Module'];

$applicationId = (int)$Params['ApplicationID'];
$application = $applicationId ? $session->loadIfExists( 'ezpRestClient', $applicationId ) : null;
if ( !$application instanceof ezpRestClient )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$errors = array();

if ( $module->isCurrentAction( 'Store') )
{
    $name = trim( (string)$module->actionParameter( 'Name' ) );
    $endPoint = trim( (string)$module->actionParameter( 'EndPointURI' ) );
    $application->name = $name;
    $application->description = (string)$module->actionParameter( 'Description' );
    $application->endpoint_uri = $endPoint;

    if ( $name === '' )
        $errors[] = ezpI18n::tr( 'design/admin/oauthadmin', 'Give the application a name.' );
    if ( $endPoint !== '' && !preg_match( '#^[a-z][a-z0-9+.-]*://\S+$#i', $endPoint ) )
        $errors[] = ezpI18n::tr( 'design/admin/oauthadmin', 'The endpoint URI must be an absolute address, such as https://app.example.com/callback.' );

    if ( !$errors )
    {
        // A new application gets its identifier and secret when it is first stored. They come from random_bytes():
        // the md5 of the name and uniqid() they used to be could be guessed from the name and the time.
        if ( $application->version == ezpRestClient::STATUS_DRAFT )
        {
            $application->client_id = bin2hex( random_bytes( 16 ) );
            $application->client_secret = bin2hex( random_bytes( 32 ) );
        }
        $application->version = ezpRestClient::STATUS_PUBLISHED;
        $application->updated = time();
        $session->update( $application );

        return $module->redirectTo( $module->functionURI( 'view' ) . '/' . $application->id );
    }
}

if ( $module->isCurrentAction( 'Discard' ) )
{
    // if there is a draft, ditch it
    if ( $application->version == ezpRestClient::STATUS_DRAFT )
        $session->delete( $application);
    return $module->redirectTo( $module->functionURI( 'list' ) );
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'application', $application );
$tpl->setVariable( 'errors', $errors );
$tpl->setVariable( 'is_new', $application->version == ezpRestClient::STATUS_DRAFT );
$Result['path'] = array( array( 'url' => 'oauthadmin/list',
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'Edit REST application' ) )
);

$Result['content'] = $tpl->fetch( 'design:oauthadmin/edit.tpl' );
return $Result;
?>
