<?php
/**
 * The datatable/connection view (doc/eng-us/features/6.0/data-tables.md, "Connections"): makes a new connection
 * (/new), or shows, changes, tests and removes one. A declared connection (datatable.ini) and the site's own database
 * are shown read-only and can be tested. The password is the name of a secret; a password typed here is used for a
 * test only, or stored in the secret store under a name the connection then keeps (never in the connection).
 * Policy datatable/connections; storing a secret also needs secrets/manage.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Connection extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $identifier = isset( $Params['Identifier'] ) ? (string)$Params['Identifier'] : 'new';
        $isNew = $identifier === 'new';
        if ( !$isNew && !preg_match( \expDataConnections::IDENTIFIER_PATTERN, $identifier ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $existing = $isNew ? null : \expDataConnections::get( $identifier );
        if ( !$isNew && $existing === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $editable = $isNew || ( $existing !== null && $existing['origin'] === 'admin' );
        $installed = \expDataConnections::installed( true );
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';

        $connection = $existing ?: \expDataConnections::normalize( array( 'engine' => 'sqlite' ) );
        $problems = array();
        $test = null;
        $messages = array();

        if ( $isPost && $http->hasPostVariable( 'CancelButton' ) )
            return $Module->redirectTo( 'datatable/connections' );

        if ( $isPost && $editable && ( $http->hasPostVariable( 'SaveButton' ) || $http->hasPostVariable( 'TestButton' ) ) )
        {
            $input = self::input( $http, $connection );
            $password = $http->hasPostVariable( 'Password' ) ? (string)$http->postVariable( 'Password' ) : '';
            $storeSecret = $http->hasPostVariable( 'StoreSecret' ) && $password !== '';
            if ( $storeSecret && $input['secret'] === '' && $input['identifier'] !== '' )
                $input['secret'] = 'datatable.' . $input['identifier'];
            $connection = \expDataConnections::normalize( $input + array( 'origin' => 'admin' ) );
            if ( $http->hasPostVariable( 'TestButton' ) )
            {
                $testProblems = \expDataConnections::problems( $connection, false );
                if ( $testProblems )
                    $problems = $testProblems;
                else
                    $test = \expDataConnections::test( $connection, $password !== '' ? $password : null );
            }
            else
            {
                if ( !$installed )
                    $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'The table exp_data_connection is missing: create it on the Data tables page first.' );
                if ( $storeSecret && !self::mayStoreSecrets() )
                    $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'Storing a password needs the policy secrets/manage.' );
                if ( !$problems && $storeSecret )
                {
                    if ( !\expSecrets::installed() || !\expSecrets::set( $connection['secret'], $password, 'datatable', 'The password of the data table connection ' . $connection['identifier'] ) )
                        $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'The password could not be stored in the secret store (Setup > Secrets says why).' );
                }
                if ( !$problems )
                {
                    $row = \expDataConnections::save( $connection, $existing !== null ? $existing['id'] : 0, $problems );
                    if ( $row instanceof \expDataConnection )
                        return $Module->redirectTo( 'datatable/connection/' . $row->attribute( 'identifier' ) . '/(saved)/1' );
                }
            }
        }
        elseif ( $isPost && $http->hasPostVariable( 'TestButton' ) && $existing !== null )
        {
            $test = \expDataConnections::test( $existing['identifier'] );
        }
        elseif ( $isPost && $http->hasPostVariable( 'RemoveButton' ) && $editable && !$isNew )
        {
            if ( $http->hasPostVariable( 'ConfirmRemove' ) )
            {
                \expDataConnections::remove( $identifier );
                return $Module->redirectTo( 'datatable/connections' );
            }
            $messages[] = array( 'kind' => 'warn', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Tick "Yes, remove it" to remove the connection. Classes and tools that name it then fail until it is made again.' ) );
        }
        if ( isset( $Params['UserParameters']['saved'] ) )
            $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'The connection was saved.' ) );

        $engines = array();
        foreach ( \expDataConnections::engines() as $key => $engine )
            $engines[] = $engine + array( 'key' => $key );
        $secretNames = array();
        foreach ( \expSecrets::names() as $name => $info )
            $secretNames[] = $name;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'connection', $connection );
        $tpl->setVariable( 'is_new', $isNew );
        $tpl->setVariable( 'editable', $editable );
        $tpl->setVariable( 'installed', $installed );
        $tpl->setVariable( 'problems', $problems );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'test', $test );
        $tpl->setVariable( 'engines', $engines );
        $tpl->setVariable( 'secret_names', $secretNames );
        $tpl->setVariable( 'secret_set', $connection['secret'] === '' || \expSecrets::has( $connection['secret'] ) );
        $tpl->setVariable( 'may_store_secrets', self::mayStoreSecrets() && \expSecrets::installed() );
        $tpl->setVariable( 'sqlite_directories', \expDataConnections::sqliteDirectories() );
        $tpl->setVariable( 'classes', self::classesOn( $connection['identifier'] ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/connection.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => 'datatable/connections', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Connections' ) ),
                                 array( 'url' => false, 'text' => $isNew ? \ezpI18n::tr( 'design/admin/datatable', 'New connection' ) : $connection['name'] ) );
        return $Result;
    }

    /**
     * The connection from the form.
     *
     * @param \eZHTTPTool $http
     * @param array $current
     * @return array
     */
    protected static function input( $http, array $current )
    {
        $get = function ( $name, $default = '' ) use ( $http ) {
            return $http->hasPostVariable( $name ) && is_scalar( $http->postVariable( $name ) ) ? (string)$http->postVariable( $name ) : $default;
        };
        $options = array();
        foreach ( array( 'charset' => 'OptionCharset', 'socket' => 'OptionSocket', 'driver' => 'OptionDriver', 'dsn' => 'OptionDSN' ) as $key => $field )
            if ( trim( $get( $field ) ) !== '' )
                $options[$key] = trim( $get( $field ) );
        return array(
            'identifier' => $current['identifier'] !== '' ? $current['identifier'] : strtolower( trim( $get( 'Identifier' ) ) ),
            'name' => $get( 'Name' ),
            'engine' => $get( 'Engine', $current['engine'] ),
            'server' => $get( 'Server' ),
            'port' => $get( 'Port', '0' ),
            'database' => $get( 'Database' ),
            'user' => $get( 'User' ),
            'secret' => trim( $get( 'Secret' ) ),
            'description' => $get( 'Description' ),
            'read_only' => $http->hasPostVariable( 'ReadOnly' ),
            'options' => $options,
        );
    }

    /** @return bool */
    protected static function mayStoreSecrets()
    {
        $access = \eZUser::currentUser()->hasAccessTo( 'secrets', 'manage' );
        return $access['accessWord'] !== 'no';
    }

    /**
     * The persistent classes that use a connection.
     *
     * @param string $identifier
     * @return string[]
     */
    protected static function classesOn( $identifier )
    {
        if ( $identifier === '' )
            return array();
        $classes = array();
        foreach ( \expDataTableCatalogue::instance()->classes() as $class => $entry )
            if ( $entry['connection'] === $identifier )
                $classes[] = $class;
        return $classes;
    }
}

}
?>
