<?php
/**
 * The view infocollector/export/<ObjectID>: everything one object (a form, a poll, a feedback page) has collected,
 * as a CSV download: the collection's ID, when it was sent and changed, the ID of the user who sent it, then one
 * column per information collector attribute of the object's class, in the class's order.
 *
 * Needs the same policy as the other infocollector views (infocollector/read). Cells that a spreadsheet would
 * read as a formula start with an apostrophe. At most EXPORT_LIMIT collections, newest first. The export is
 * recorded in the audit as data.export.csv with its row count, never the values.
 * User guide: doc/guides/collected-information.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Infocollector
{

class Export extends \Exponential\Runnable\ModuleView
{
    /** the most collections one export holds */
    const EXPORT_LIMIT = 50000;

    /** collections whose values are read in one query */
    const BATCH = 500;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $scope['Params']['Module'];
        $objectID = isset( $scope['Params']['ObjectID'] ) && ctype_digit( (string)$scope['Params']['ObjectID'] ) ? (int)$scope['Params']['ObjectID'] : 0;
        $object = $objectID > 0 ? \eZContentObject::fetch( $objectID ) : null;
        if ( !$object instanceof \eZContentObject )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $columns = self::columns( (int)$object->attribute( 'contentclass_id' ) );
        $fileName = \expSubitemsCSVExport::fileName( $object->attribute( 'name' ) . '-collected' );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );

        while ( @ob_end_clean() );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $out = fopen( 'php://output', 'w' );
        fwrite( $out, \expSubitemsCSVExport::BOM );
        $header = array( \ezpI18n::tr( 'kernel/infocollector', 'Collection ID' ), \ezpI18n::tr( 'kernel/infocollector', 'Sent' ),
                         \ezpI18n::tr( 'kernel/infocollector', 'Changed' ), \ezpI18n::tr( 'kernel/infocollector', 'User ID' ) );
        foreach ( $columns as $column )
            $header[] = $column['name'];
        \expSubitemsCSVExport::writeLine( $out, $header );

        $rows = 0;
        for ( $offset = 0; $offset < self::EXPORT_LIMIT; $offset += self::BATCH )
        {
            $collections = \eZInformationCollection::fetchCollectionsList( $objectID, false, false,
                                                                          array( 'limit' => self::BATCH, 'offset' => $offset ),
                                                                          array( 'created', false ), false );
            if ( !$collections )
                break;
            $values = self::values( $collections );
            foreach ( $collections as $collection )
            {
                $id = (int)$collection['id'];
                \expSubitemsCSVExport::writeLine( $out, self::line( $collection, $columns, isset( $values[$id] ) ? $values[$id] : array() ) );
                $rows++;
            }
            if ( count( $collections ) < self::BATCH )
                break;
        }
        fclose( $out );

        // Audit (doc/bc/6.0/audit.md, data.export.csv): what left the system, never the values
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.export.csv', array( 'object' => \expAuditHook::object( $objectID ), 'verb' => 'export',
                'after' => array( 'object' => $objectID, 'rows' => $rows, 'columns' => count( $columns ), 'file' => (string)$fileName,
                                  'source' => 'infocollector' ) ) );

        \eZExecution::cleanExit();

        return $this->viewResult( null, null );
    }

    /**
     * The information collector attributes of a class, in its order.
     *
     * @return array of array( id, identifier, name )
     */
    public static function columns( $classID )
    {
        $attributes = \eZContentClassAttribute::fetchFilteredList( array( 'contentclass_id' => (int)$classID,
                                                                          'version' => \eZContentClass::VERSION_STATUS_DEFINED,
                                                                          'is_information_collector' => 1 ) );
        usort( $attributes, function ( $a, $b ) { return (int)$a->attribute( 'placement' ) <=> (int)$b->attribute( 'placement' ); } );
        $columns = array();
        foreach ( $attributes as $attribute )
            $columns[] = array( 'id' => (int)$attribute->attribute( 'id' ), 'identifier' => (string)$attribute->attribute( 'identifier' ),
                                'name' => (string)$attribute->attribute( 'name' ) );
        return $columns;
    }

    /**
     * The collected values of some collections.
     *
     * @param array $collections rows with 'id'
     * @return array collection id => class attribute id => value
     */
    public static function values( array $collections )
    {
        $ids = array();
        foreach ( $collections as $collection )
            $ids[] = (int)$collection['id'];
        if ( !$ids )
            return array();
        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = array();
            foreach ( $ids as $id )
            {
                $collection = \eZInformationCollection::fetch( $id );
                if ( $collection )
                    $rows = array_merge( $rows, $collection->informationCollectionAttributes( false ) );
            }
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT informationcollection_id, contentclass_attribute_id, data_text, data_int, data_float
                                      FROM ezinfocollection_attribute
                                      WHERE informationcollection_id IN ( ' . implode( ', ', $ids ) . ' )' );
        }
        $result = array();
        foreach ( (array)$rows as $row )
            $result[(int)$row['informationcollection_id']][(int)$row['contentclass_attribute_id']] = self::value( $row );
        return $result;
    }

    /**
     * The text of one collected value: the text if there is one, else the number.
     */
    public static function value( array $row )
    {
        $text = isset( $row['data_text'] ) ? (string)$row['data_text'] : '';
        if ( $text !== '' )
            return $text;
        $float = isset( $row['data_float'] ) ? (float)$row['data_float'] : 0.0;
        if ( $float != 0.0 )
            return (string)$float;
        $int = isset( $row['data_int'] ) ? $row['data_int'] : null;
        return $int === null || $int === '' ? '' : (string)(int)$int;
    }

    /**
     * One CSV line.
     *
     * @param array $collection id, created, modified, creator_id
     * @param array $columns from columns()
     * @param array $values class attribute id => value
     * Dates are UTC, as "2026-10-06 14:05:00 UTC", so a spreadsheet in any locale reads them alike.
     * @return string[]
     */
    public static function line( array $collection, array $columns, array $values )
    {
        $date = function ( $time ) {
            $time = (int)$time;
            if ( $time <= 0 )
                return '';
            return gmdate( 'Y-m-d H:i:s', $time ) . ' UTC';
        };
        $line = array( (string)(int)$collection['id'], $date( $collection['created'] ),
                       $date( isset( $collection['modified'] ) ? $collection['modified'] : 0 ),
                       (string)(int)( isset( $collection['creator_id'] ) ? $collection['creator_id'] : 0 ) );
        foreach ( $columns as $column )
            $line[] = isset( $values[$column['id']] ) ? (string)$values[$column['id']] : '';
        return $line;
    }
}

}
