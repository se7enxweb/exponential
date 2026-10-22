<?php
/**
 * The datatable/schema view (doc/eng-us/features/6.0/data-tables.md, "Schema"): creates or upgrades data tables on a
 * connection. The schema comes from a persistent class's definition (/(class)/<Class>) or from an extension's
 * share/db_schema.dba (/(extension)/<name>); the connection is chosen (the class's own by default). The page shows
 * what would be done, table by table, with the SQL, and does it on "Apply"; dropping and changing columns or indexes
 * happen only when ticked. The install SQL of every engine is shown too. Policy datatable/manage.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Schema extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $post = function ( $name, $default = '' ) use ( $http ) {
            return $http->hasPostVariable( $name ) && is_scalar( $http->postVariable( $name ) ) ? (string)$http->postVariable( $name ) : $default;
        };

        $class = $isPost ? $post( 'ClassName' ) : ( isset( $Params['ClassName'] ) ? (string)$Params['ClassName'] : '' );
        $extension = $isPost ? $post( 'Extension' ) : ( isset( $Params['Extension'] ) ? (string)$Params['Extension'] : '' );
        $connectionID = $isPost ? $post( 'Connection' ) : ( isset( $Params['Connection'] ) ? (string)$Params['Connection'] : '' );
        $options = array( 'drop' => $isPost && $http->hasPostVariable( 'AllowDrop' ), 'alter' => $isPost && $http->hasPostVariable( 'AllowAlter' ) );

        $schema = array();
        $source = '';
        $problems = array();
        $catalogue = \expDataTableCatalogue::instance();
        if ( $class !== '' )
        {
            $entry = preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $class ) ? $catalogue->entry( $class ) : null;
            if ( $entry === null || $entry['error'] !== '' || !class_exists( $entry['class'] ) )
                $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'There is no persistent class %class.', null, array( '%class' => $class ) );
            else
            {
                $schema = \expDataTableSchema::fromDefinition( call_user_func( array( $entry['class'], 'definition' ) ) );
                $source = $entry['class'];
                if ( $connectionID === '' )
                    $connectionID = $entry['connection'] !== '' ? $entry['connection'] : 'site';
            }
        }
        elseif ( $extension !== '' )
        {
            $files = self::extensionSchemas();
            if ( !isset( $files[$extension] ) )
                $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'The extension %name has no share/db_schema.dba.', null, array( '%name' => $extension ) );
            else
            {
                $schema = \expKernelTableSchema::read( $files[$extension] );
                $source = $extension;
            }
        }
        if ( $connectionID === '' )
            $connectionID = 'site';
        $connection = \expDataConnections::get( $connectionID );
        if ( $connection === null )
            $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'There is no connection %id.', null, array( '%id' => $connectionID ) );
        elseif ( $connection['read_only'] )
            $problems[] = \ezpI18n::tr( 'design/admin/datatable', 'The connection %id is read only.', null, array( '%id' => $connectionID ) );

        $plan = null;
        $result = null;
        if ( $schema && !$problems )
        {
            try
            {
                $db = \expDataConnections::db( $connectionID );
                if ( $isPost && $http->hasPostVariable( 'ApplyButton' ) )
                {
                    $result = \expDataTableSchema::apply( $schema, $db, $options );
                    \expKernelTableSchema::reset();
                }
                $plan = \expDataTableSchema::plan( $schema, $db, $options );
            }
            catch ( \Throwable $e )
            {
                $problems[] = $e->getMessage();
            }
        }
        $tables = array();
        if ( $plan )
            foreach ( $plan['tables'] as $table => $step )
                $tables[] = $step + array( 'table' => $table, 'sql' => implode( ";\n\n", $step['statements'] ) );
        $outcomes = array();
        if ( $result )
            foreach ( $result['tables'] as $table => $outcome )
                $outcomes[] = $outcome + array( 'table' => $table );

        $classes = array();
        foreach ( $catalogue->byOrigin() as $origin => $entries )
            if ( $origin !== 'kernel' )
                foreach ( $entries as $name => $entry )
                    if ( $entry['error'] === '' )
                        $classes[] = array( 'class' => $name, 'origin' => $origin );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'class', $class );
        $tpl->setVariable( 'extension', $extension );
        $tpl->setVariable( 'source', $source );
        $tpl->setVariable( 'connection_id', $connectionID );
        $tpl->setVariable( 'connection', $connection );
        $tpl->setVariable( 'connections', array_values( \expDataConnections::all() ) );
        $tpl->setVariable( 'classes', $classes );
        $tpl->setVariable( 'extensions', array_keys( self::extensionSchemas() ) );
        $tpl->setVariable( 'problems', $problems );
        $tpl->setVariable( 'tables', $tables );
        $tpl->setVariable( 'engine', $plan ? $plan['engine'] : '' );
        $tpl->setVariable( 'outcomes', $outcomes );
        $tpl->setVariable( 'applied', $result !== null );
        $tpl->setVariable( 'applied_ok', $result !== null && $result['ok'] );
        $tpl->setVariable( 'options', $options );
        $installSql = array();
        if ( $schema )
            foreach ( \expDataTableSchema::installSql( $schema ) as $engine => $sql )
                $installSql[] = array( 'engine' => $engine, 'sql' => $sql );
        $tpl->setVariable( 'install_sql', $installSql );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/schema.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Schema' ) ) );
        return $Result;
    }

    /**
     * The active extensions that ship a share/db_schema.dba.
     *
     * @return array name => file
     */
    public static function extensionSchemas()
    {
        $files = array();
        foreach ( \eZExtension::activeExtensions() as $name )
        {
            $file = \eZExtension::baseDirectory() . '/' . $name . '/share/db_schema.dba';
            if ( is_file( $file ) )
                $files[$name] = $file;
        }
        ksort( $files );
        return $files;
    }
}

}
?>
