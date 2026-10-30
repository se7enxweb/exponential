<?php
/**
 * The datatable/rows view (doc/eng-us/features/6.0/data-tables.md, "Rows"): the rows of one persistent class's
 * table, 25 a page, filtered by one field (/(<field>)/<value>, as the content view's "Data tables" tab links), each
 * relation value linked to what it points at. Columns whose names say they hold a secret are not shown. Read-only.
 * Policy datatable/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Rows extends \Exponential\Runnable\ModuleView
{
    const LIMIT = 25;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $class = isset( $Params['ClassName'] ) ? (string)$Params['ClassName'] : '';
        $entry = preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $class ) ? \expDataTableCatalogue::instance()->entry( $class ) : null;
        if ( $entry === null || $entry['error'] !== '' || !class_exists( $entry['class'] ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $definition = call_user_func( array( $entry['class'], 'definition' ) );
        $offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;

        // one filter: a user parameter named after a field
        $filter = null;
        $user = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
        foreach ( $user as $name => $value )
        {
            if ( $name !== 'offset' && isset( $entry['fields'][$name] ) && is_scalar( $value ) && !Table::isSecretColumn( $name ) )
            {
                $filter = array( 'field' => (string)$name, 'value' => (string)$value );
                break;
            }
        }
        $conditions = $filter ? array( $filter['field'] => $filter['value'] ) : null;

        $relations = array();
        foreach ( $entry['relations'] as $relation )
            $relations[$relation['column']] = $relation;
        $fields = array();
        foreach ( array_keys( $entry['fields'] ) as $field )
            if ( !Table::isSecretColumn( $field ) )
                $fields[] = $field;

        $rows = array();
        $count = 0;
        $error = '';
        try
        {
            $exists = \expKernelTableSchema::exists( $entry['table'], \eZPersistentObject::dbFor( $definition ) );
            if ( $exists !== true )
                throw new \RuntimeException( \ezpI18n::tr( 'design/admin/datatable', 'The table %table is not in the database.', null, array( '%table' => $entry['table'] ) ) );
            $count = (int)\eZPersistentObject::count( $definition, $conditions );
            $list = \eZPersistentObject::fetchObjectList( $definition, null, $conditions, null, array( 'offset' => $offset, 'length' => self::LIMIT ), false );
            foreach ( is_array( $list ) ? $list : array() as $row )
            {
                $cells = array();
                foreach ( $fields as $field )
                {
                    $value = isset( $row[$field] ) ? $row[$field] : '';
                    $text = is_scalar( $value ) ? (string)$value : json_encode( $value );
                    $cells[] = array( 'value' => mb_substr( $text, 0, 120 ), 'cut' => mb_strlen( $text ) > 120,
                                      'url' => isset( $relations[$field] ) ? \expDataTableRelation::url( $relations[$field], $value ) : '' );
                }
                $rows[] = $cells;
            }
        }
        catch ( \Throwable $e )
        {
            $error = $e->getMessage();
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'entry', $entry );
        $tpl->setVariable( 'fields', $fields );
        $tpl->setVariable( 'rows', $rows );
        $tpl->setVariable( 'count', $count );
        $tpl->setVariable( 'offset', $offset );
        $tpl->setVariable( 'limit', self::LIMIT );
        $tpl->setVariable( 'filter', $filter );
        $tpl->setVariable( 'error', $error );
        $tpl->setVariable( 'page_uri', 'datatable/rows/' . $entry['class'] . ( $filter ? '/(' . $filter['field'] . ')/' . rawurlencode( $filter['value'] ) : '' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/rows.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => 'datatable/table/' . $entry['class'], 'text' => $entry['class'] ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Rows' ) ) );
        return $Result;
    }
}

}
?>
