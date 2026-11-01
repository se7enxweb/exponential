<?php
/**
 * The staticpublish/keys view (/staticpublish/keys/<target>): the deploy key of a target. Generate (staticpublish/setup,
 * "Verify it's you") makes an ed25519 key pair (expStaticCredentials::generateKey(), as exp:static:keys --generate),
 * keeps the private half in the secret store (Setup > Secrets, staticpublish.<target>.deploy_key) and shows the public
 * half to add to the repository with write access (GitHub: Settings > Deploy keys). The public half stays readable as
 * the secret's description; the private half is never shown. Replacing a key needs the tick "Replace the key".
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Keys extends Page
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $target = self::target( $Params, 'setup' );
        if ( $target === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $messages = array();
        $generated = null;

        if ( self::isPost() && isset( $_POST['GenerateButton'] ) )
        {
            $generated = \expStaticAdmin::generateKey( $target, !empty( $_POST['ReplaceKey'] ) );
            if ( $generated['error'] !== '' )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'No key was made: %problem', array( '%problem' => $generated['error'] ) ) );
            else
                $messages[] = array( 'kind' => 'ok', 'text' => self::t( 'A new deploy key was made. Add its public half to the repository now.' ) );
        }

        $key = \expStaticAdmin::deployKey( $target );
        $credential = (string)$target->setting( 'Credential' );
        return self::page( 'keys.tpl', array(
            'target' => \expStaticAdmin::overview( $target ),
            'messages' => $messages,
            'key' => $key,
            'generated' => $generated && $generated['generated'],
            'uses_key' => $credential === 'sshkey:' . $key['secret'],
            'https_remote' => (bool)preg_match( '#^https?://#i', (string)$target->setting( 'Remote' ) ),
            'view' => 'keys',
        ), self::path( self::t( 'Deploy key' ), $target ) );
    }
}

}
?>
