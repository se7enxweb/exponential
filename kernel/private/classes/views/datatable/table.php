<?php
/**
 * The datatable/table view (doc/eng-us/features/6.0/data-tables.md, "Describe"): one persistent class and its table:
 * the definition (fields, keys, sort, function attributes), the connection, the relations to content and other
 * tables, the table as the database has it (columns, rows), how the two differ, and the first rows. Columns whose
 * names say they hold a secret (password, hash, token, secret, key) are not shown. Policy datatable/read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Table extends \Exponential\Runnable\ModuleView
{
    /** Rows shown */
    const SAMPLE = 20;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $class = isset( $Params['ClassName'] ) ? (string)$Params['ClassName'] : '';
        $entry = preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $class ) ? \expDataTableCatalogue::instance()->entry( $class ) : null;
        if ( $entry === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $description = \expDataTableDescriber::describe( $entry );
        $sample = array();
        $hidden = array();
        foreach ( array_keys( $entry['fields'] ) as $field )
            if ( self::isSecretColumn( $field ) )
                $hidden[] = $field;
        if ( $description['exists'] === true && $entry['error'] === '' && class_exists( $entry['class'] ) )
        {
            try
            {
                $definition = call_user_func( array( $entry['class'], 'definition' ) );
                $rows = \eZPersistentObject::fetchObjectList( $definition, null, null, null, array( 'offset' => 0, 'length' => self::SAMPLE ), false );
                foreach ( is_array( $rows ) ? $rows : array() as $row )
                {
                    $line = array();
                    foreach ( array_keys( $entry['fields'] ) as $field )
                    {
                        $value = isset( $row[$field] ) ? $row[$field] : '';
                        $line[] = in_array( $field, $hidden, true ) ? '' : mb_substr( is_scalar( $value ) ? (string)$value : json_encode( $value ), 0, 80 );
                    }
                    $sample[] = $line;
                }
            }
            catch ( \Throwable $e )
            {
                $description['error'] = trim( $description['error'] . ' ' . $e->getMessage() );
            }
        }
        $relations = array();
        foreach ( $entry['relations'] as $relation )
            $relations[] = $relation + array( 'target' => self::targetLabel( $relation ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'entry', $entry );
        $tpl->setVariable( 'description', $description );
        $tpl->setVariable( 'relations', $relations );
        $tpl->setVariable( 'sample', $sample );
        $tpl->setVariable( 'sample_limit', self::SAMPLE );
        $tpl->setVariable( 'hidden_columns', $hidden );
        $tpl->setVariable( 'connection', \expDataConnections::get( $entry['connection'] ) );
        $tpl->setVariable( 'can_manage', Dashboard::may( 'manage' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/table.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => false, 'text' => $entry['class'] ) );
        return $Result;
    }

    /**
     * Whether a column's name says it holds a secret.
     *
     * @param string $name
     * @return bool
     */
    public static function isSecretColumn( $name )
    {
        return (bool)preg_match( '/(password|passwd|hash|token|secret|salt|ciphertext|private_key|api_key|^key$)/i', (string)$name );
    }

    /**
     * @param array $relation
     * @return string
     */
    protected static function targetLabel( array $relation )
    {
        switch ( $relation['kind'] )
        {
            case 'object':
                return \ezpI18n::tr( 'design/admin/datatable', 'Content object' );
            case 'node':
                return \ezpI18n::tr( 'design/admin/datatable', 'Node' );
            case 'class':
                return \ezpI18n::tr( 'design/admin/datatable', 'Content class' );
            case 'user':
                return \ezpI18n::tr( 'design/admin/datatable', 'User' );
            case 'section':
                return \ezpI18n::tr( 'design/admin/datatable', 'Section' );
        }
        return $relation['class'] !== '' ? $relation['class'] : \ezpI18n::tr( 'design/admin/datatable', 'Table' );
    }
}

}
?>
