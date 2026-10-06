<?php
/**
 * File containing the oauthadmin/view view definition
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

// Who authorized the application and how many of its tokens are still valid.
$now = time();
$db = eZDB::instance();
$authorizations = array();
$tokens = 0;
if ( $db->databaseName() !== 'mongo' )
{
    foreach ( (array)$db->arrayQuery( 'SELECT user_id, created FROM ezprest_authorized_clients WHERE rest_client_id = ' . (int)$application->id . ' ORDER BY created DESC', array( 'limit' => 50 ) ) as $row )
    {
        $object = eZContentObject::fetch( (int)$row['user_id'] );
        $authorizations[] = array( 'user_id' => (int)$row['user_id'],
                                   'name' => $object ? $object->attribute( 'name' ) : '',
                                   'created' => (int)$row['created'] );
    }
    $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezprest_token WHERE client_id = '" . $db->escapeString( (string)$application->client_id ) . "' AND ( expirytime = 0 OR expirytime > $now )" );
    $tokens = $rows ? (int)$rows[0]['c'] : 0;
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'application', $application );
$tpl->setVariable( 'authorizations', $authorizations );
$tpl->setVariable( 'active_tokens', $tokens );
$Result['path'] = array( array( 'url' => 'oauthadmin/list',
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'REST application: %application_name%', null,
                                    array( '%application_name%' => $application->name ) ) ),
);

$Result['content'] = $tpl->fetch( 'design:oauthadmin/view.tpl' );
return $Result;
?>
