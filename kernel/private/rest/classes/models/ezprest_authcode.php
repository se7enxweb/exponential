<?php
/**
 * File containing the ezpRestToken class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Data class ezprest_tokens.
 * Class to be used with eZ Components PersistentObject.
 */
class ezpRestAuthcode implements ezcPersistentObject
{
    /**
     * id
     *
     * @var string
     */
    public $id;
    /**
     * expirytime
     *
     * @var int
     */
    public $expirytime;
    /**
     * client_id
     *
     * @var string
     */
    public $client_id;
    /**
     * user_id
     *
     * @var int
     */
    public $user_id;
    /**
     * scope
     *
     * @var string
     */
    public $scope;
    /**
     * PKCE code_challenge (RFC 7636) of the authorization request, null without PKCE
     *
     * @var string|null
     */
    public $code_challenge;
    /**
     * PKCE code_challenge_method: S256 or plain, null without PKCE
     *
     * @var string|null
     */
    public $code_challenge_method;

    /**
     * Set the PersistentObject state.
     *
     * @param array(string=>mixed) $state The state to set.
     * @return void
     */
     public function setState( array $state )
     {
         foreach ( $state as $attribute => $value )
         {
             $this->$attribute = $value;
         }
     }

    /**
     * Get the PersistentObject state.
     *
     * @return array(string=>mixed) The state of the object.
     */
     public function getState()
     {
         return array(
             'id' => $this->id,
             'expirytime' => $this->expirytime,
             'client_id' => $this->client_id,
             'user_id' => $this->user_id,
             'scope' => $this->scope,
             'code_challenge' => $this->code_challenge,
             'code_challenge_method' => $this->code_challenge_method,
         );
     }
}
?>
