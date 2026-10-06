<?php
/**
 * File containing the expApiKeyFunctionCollection class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The fetch functions of the apikey module (kernel/apikey/function_definition.php).
 */
class expApiKeyFunctionCollection
{
    /**
     * @return array result: whether the current user may make API keys
     */
    public static function fetchCanCreate()
    {
        return array( 'result' => expApiKey::userCanCreate( eZUser::currentUser() ) );
    }

    /**
     * The counts of a user's keys, for that user or an administrator of oauthadmin only.
     *
     * @param int $userID 0 = the current user
     * @return array result: the counts of expApiKey::statusCounts(), or false
     */
    public static function fetchCounts( $userID = 0 )
    {
        $current = eZUser::currentUser();
        $userID = (int)$userID ?: (int)$current->attribute( 'contentobject_id' );
        if ( $userID !== (int)$current->attribute( 'contentobject_id' ) )
        {
            $access = $current->hasAccessTo( 'oauthadmin', 'keys' );
            if ( $access['accessWord'] === 'no' )
                return array( 'result' => false );
        }
        elseif ( !$current->isRegistered() )
            return array( 'result' => false );
        return array( 'result' => expApiKey::statusCounts( $userID ) );
    }
}
?>
