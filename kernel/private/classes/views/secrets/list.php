<?php
/**
 * The secrets/list view (doc/eng-us/features/6.0/secret-store.md): the names of the secrets of the kernel's secret
 * store and of secrets.ini, with the tool each belongs to and whether this server can decrypt it; where the key is
 * (EXP_SECRET_KEY or the key file) and the warning that a backup of the database alone cannot decrypt them; setting a
 * value (never shown again), changing a description, removing a secret, rotating the key (file key) or encrypting
 * every secret again with the current key (after EXP_SECRET_KEY was changed). Policy secrets/manage.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Secrets
{

class SecretList extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $messages = array();
        $installed = \expSecrets::installed( true );
        $form = array( 'name' => '', 'scope' => '', 'description' => '' );

        if ( $isPost && $http->hasPostVariable( 'CreateTableButton' ) )
        {
            $log = array();
            $ok = \expSecrets::install( $log );
            $installed = \expSecrets::installed( true );
            $messages[] = array( 'kind' => $ok ? 'ok' : 'bad', 'text' => implode( '; ', $log ) );
        }
        elseif ( $isPost && $http->hasPostVariable( 'SaveSecretButton' ) )
        {
            $name = trim( (string)$http->postVariable( 'SecretName', '' ) );
            $value = (string)$http->postVariable( 'SecretValue', '' );
            $secretScope = strtolower( trim( (string)$http->postVariable( 'SecretScope', '' ) ) );
            $description = (string)$http->postVariable( 'SecretDescription', '' );
            $form = array( 'name' => $name, 'scope' => $secretScope, 'description' => $description );
            if ( !\expSecrets::validName( $name ) || strpos( $name, 'env:' ) === 0 )
                $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'A name is letters, digits and _ . : - (at most 128), and does not start with env:.' ) );
            elseif ( !preg_match( \expSecrets::SCOPE_PATTERN, $secretScope ) )
                $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The tool is lower case letters, digits and _ . - (at most 64).' ) );
            elseif ( $value === '' )
            {
                if ( \expSecrets::describe( $name, $description ) )
                    $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The description of %name was changed; its value was kept.', null, array( '%name' => $name ) ) );
                else
                    $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'Give the value.' ) );
            }
            elseif ( \expSecrets::set( $name, $value, $secretScope, $description ) )
            {
                $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The secret %name was stored, encrypted. Its value is not shown again.', null, array( '%name' => $name ) ) );
                $form = array( 'name' => '', 'scope' => '', 'description' => '' );
            }
            else
                $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The secret could not be stored: see the key above.' ) );
            unset( $value );
        }
        elseif ( $isPost && $http->hasPostVariable( 'RemoveButton' ) )
        {
            $names = array_keys( (array)$http->postVariable( 'RemoveButton' ) );
            $name = isset( $names[0] ) ? (string)$names[0] : '';
            $confirmed = $http->hasPostVariable( 'ConfirmRemove' ) ? (array)$http->postVariable( 'ConfirmRemove' ) : array();
            if ( \expSecrets::validName( $name ) && isset( $confirmed[$name] ) )
            {
                \expSecrets::remove( $name );
                $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The secret %name was removed.', null, array( '%name' => $name ) ) );
            }
            else
                $messages[] = array( 'kind' => 'warn', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'Tick the box beside the secret to remove it. A tool that names it fails until it is set again.' ) );
        }
        elseif ( $isPost && $http->hasPostVariable( 'RotateKeyButton' ) )
        {
            if ( !$http->hasPostVariable( 'ConfirmRotate' ) )
                $messages[] = array( 'kind' => 'warn', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'Tick "Yes, rotate the key" first.' ) );
            else
            {
                $counts = \expSecrets::rotate();
                $messages[] = $counts === false
                    ? array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The key could not be rotated (a key from EXP_SECRET_KEY is rotated by setting a new one and the old one as EXP_SECRET_KEY_PREVIOUS).' ) )
                    : array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'The key was rotated: %done secrets encrypted again, %bad could not be read. Back up the new key file now.', null,
                                                                     array( '%done' => $counts['reencrypted'] + $counts['current'], '%bad' => $counts['unreadable'] ) ) );
            }
        }
        elseif ( $isPost && $http->hasPostVariable( 'ReencryptButton' ) )
        {
            $counts = \expSecrets::reencryptAll();
            $messages[] = array( 'kind' => $counts['unreadable'] ? 'warn' : 'ok',
                                 'text' => \ezpI18n::tr( 'design/admin/secrets', '%done secrets encrypted again with the current key, %current were already, %bad could not be read.', null,
                                                         array( '%done' => $counts['reencrypted'], '%current' => $counts['current'], '%bad' => $counts['unreadable'] ) ) );
        }

        $filter = isset( $Params['Scope'] ) && preg_match( \expSecrets::SCOPE_PATTERN, (string)$Params['Scope'] ) && (string)$Params['Scope'] !== '' ? (string)$Params['Scope'] : null;
        $key = \expSecretKey::info();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'secrets', array_values( \expSecrets::names( $filter ) ) );
        $tpl->setVariable( 'scope', $filter === null ? '' : $filter );
        $tpl->setVariable( 'installed', $installed );
        $tpl->setVariable( 'key', $key );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'stale', self::staleCount( $key['id'] ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:secrets/list.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/secrets', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/secrets', 'Secrets' ) ) );
        return $Result;
    }

    /**
     * The stored secrets that are not under the current key.
     *
     * @param string $keyID
     * @return int
     */
    protected static function staleCount( $keyID )
    {
        if ( !\expSecrets::installed() )
            return 0;
        $stale = 0;
        foreach ( \expSecret::fetchList() as $row )
            if ( $row->attribute( 'key_id' ) !== $keyID )
                $stale++;
        return $stale;
    }
}

}
?>
