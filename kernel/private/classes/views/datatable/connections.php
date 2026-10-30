<?php
/**
 * The datatable/connections view (doc/eng-us/features/6.0/data-tables.md, "Connections"): the installation's own
 * database, the connections extensions declare in datatable.ini and the ones made here, with what each engine needs
 * on this server; "Test" opens a connection and reports the server's version, its tables and the time it took (a
 * password never shows: it is the name of a secret). Policy datatable/connections.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Connections extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $tests = array();
        if ( $isPost && $http->hasPostVariable( 'TestButton' ) )
        {
            $ids = (array)$http->postVariable( 'TestButton' );
            foreach ( array_keys( $ids ) as $id )
                if ( preg_match( \expDataConnections::IDENTIFIER_PATTERN, (string)$id ) )
                    $tests[(string)$id] = \expDataConnections::test( (string)$id );
        }
        if ( $isPost && $http->hasPostVariable( 'NewButton' ) )
            return $Module->redirectTo( 'datatable/connection/new' );

        $connections = array();
        foreach ( \expDataConnections::all() as $id => $connection )
        {
            $connection['secret_set'] = $connection['secret'] === '' || \expSecrets::has( $connection['secret'] );
            $connection['test'] = isset( $tests[$id] ) ? $tests[$id] : null;
            $connections[] = $connection;
        }
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'connections', $connections );
        $tpl->setVariable( 'engines', array_values( array_map( function ( $key, $engine ) { return $engine + array( 'key' => $key ); },
                                                               array_keys( \expDataConnections::engines() ), \expDataConnections::engines() ) ) );
        $tpl->setVariable( 'installed', \expDataConnections::installed( true ) );
        $tpl->setVariable( 'sqlite_directories', \expDataConnections::sqliteDirectories() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/connections.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Connections' ) ) );
        return $Result;
    }
}

}
?>
