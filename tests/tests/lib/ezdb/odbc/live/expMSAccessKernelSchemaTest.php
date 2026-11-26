<?php
/**
 * How far a minimal Exponential site gets on MS Access (Windows with the Access Database Engine; skipped without
 * EXP_TEST_MSACCESS_KERNEL_FILE, an empty writable .accdb used for nothing else). It measures, it does not promise:
 * every step is recorded as it went, written to var/tmp/msaccess-kernel-probe.json and printed, and the test fails
 * only when the driver itself breaks (not one kernel table could be made).
 *
 *  AK-01 - The kernel schema (share/db_schema.dba): every table and index made through expMSAccessDialect
 *  AK-02 - The kernel data (share/db_data.dba): every row written, ids and all
 *  AK-03 - Kernel reads with Access as the site's database: an object, a node, a subtree page, a user, a class, the
 *          languages, a URL alias, a count with the language mask (bitAnd)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expODBCTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('odbc')]
#[\PHPUnit\Framework\Attributes\Group('msaccess-live')]
class expMSAccessKernelSchemaTest extends PHPUnit\Framework\TestCase
{
    /**
     * @param callable $step
     * @return array ok, ms, error
     */
    private static function probe( callable $step )
    {
        $started = microtime( true );
        try
        {
            $value = $step();
            return array( 'ok' => $value !== false && $value !== null, 'ms' => (int)round( ( microtime( true ) - $started ) * 1000 ),
                          'error' => $value === false || $value === null ? 'no result' : '', 'value' => is_scalar( $value ) ? $value : ( is_array( $value ) ? count( $value ) . ' rows' : get_class( $value ) ) );
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'ms' => (int)round( ( microtime( true ) - $started ) * 1000 ), 'error' => get_class( $e ) . ': ' . mb_substr( $e->getMessage(), 0, 400 ), 'value' => null );
        }
    }

    public function testKernelOnAccess()
    {
        $file = (string)getenv( 'EXP_TEST_MSACCESS_KERNEL_FILE' );
        if ( $file === '' || !is_file( $file ) )
            $this->markTestSkipped( 'No Access file for the kernel schema: set EXP_TEST_MSACCESS_KERNEL_FILE' );
        $saved = expODBCTestKit::quietDebug();
        $root = dirname( __DIR__, 6 );
        $driver = (string)getenv( 'EXP_TEST_MSACCESS_DRIVER' );
        $db = new expMSAccessDB( array( 'database' => $file, 'connect_retries' => 0, 'msaccess' => $driver !== '' ? array( 'Driver' => $driver ) : array() ) );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $report = array( 'file' => basename( $file ), 'layer' => $db->layer(), 'ansi92' => $db->ansi92(), 'schema' => array(), 'data' => array(), 'kernel' => array() );

        // AK-01
        include $root . '/share/db_schema.dba';
        /** @var array $schema */
        unset( $schema['_info'] );
        $made = 0;
        foreach ( $schema as $table => $definition )
        {
            $errors = array();
            foreach ( expMSAccessDialect::createTable( $table, $definition, $db->ansi92() ) as $sql )
            {
                $result = self::probe( function () use ( $db, $sql ) { return $db->query( $sql ); } );
                if ( !$result['ok'] )
                    $errors[] = $result['error'] . ' [' . mb_substr( $sql, 0, 160 ) . ']';
            }
            $report['schema'][$table] = $errors;
            if ( !$errors || strpos( $errors[0], 'CREATE TABLE' ) === false )
                $made++;
        }

        // AK-02
        include $root . '/share/db_data.dba';
        /** @var array $data */
        foreach ( $data as $table => $content )
        {
            $columns = implode( ', ', array_map( array( 'expMSAccessDialect', 'quoteName' ), $content['fields'] ) );
            $written = 0;
            $failed = array();
            foreach ( $content['rows'] as $row )
            {
                $values = array();
                foreach ( $row as $value )
                    $values[] = $value === null ? 'NULL' : ( preg_match( '/^-?\d+$/', (string)$value ) && strlen( (string)$value ) < 10 ? (string)$value : $db->sqlLiteral( (string)$value ) );
                $sql = 'INSERT INTO ' . expMSAccessDialect::quoteName( $table ) . " ($columns) VALUES (" . implode( ', ', $values ) . ')';
                $result = self::probe( function () use ( $db, $sql ) { return $db->query( $sql ); } );
                if ( $result['ok'] )
                    $written++;
                elseif ( count( $failed ) < 3 )
                    $failed[] = $result['error'];
            }
            $report['data'][$table] = array( 'rows' => count( $content['rows'] ), 'written' => $written, 'errors' => $failed );
        }

        // AK-03
        $previous = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        eZDB::setInstance( $db );
        $steps = array(
            'eZContentObject::fetch( 1 )' => function () { return eZContentObject::fetch( 1 ); },
            'eZContentObjectTreeNode::fetch( 2 )' => function () { return eZContentObjectTreeNode::fetch( 2 ); },
            'subTreeByNodeID( Limit 5, Offset 1 ) of node 1' => function () { return eZContentObjectTreeNode::subTreeByNodeID( array( 'Limit' => 5, 'Offset' => 1, 'SortBy' => array( 'node_id', true ) ), 1 ); },
            'subTreeCountByNodeID() of node 1' => function () { return eZContentObjectTreeNode::subTreeCountByNodeID( array(), 1 ); },
            "eZUser::fetchByName( 'admin' )" => function () { return eZUser::fetchByName( 'admin' ); },
            "eZContentClass::fetchByIdentifier( 'folder' )" => function () { return eZContentClass::fetchByIdentifier( 'folder' ); },
            'eZContentLanguage::fetchList()' => function () { return eZContentLanguage::fetchList( true ); },
            "eZURLAliasML::fetchByPath( 'users' )" => function () { return eZURLAliasML::fetchByPath( 'users' ); },
            'objects with language mask & 2 (bitAnd)' => function () use ( $db ) {
                return $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE ' . $db->bitAnd( 'language_mask', '2' ) . ' > 0', array( 'column' => 'c' ) );
            },
            'eZPersistentObject::fetchObjectList( ezsection, limit 2 offset 1 )' => function () {
                return eZPersistentObject::fetchObjectList( eZSection::definition(), null, null, array( 'id' => 'asc' ), array( 'offset' => 1, 'length' => 2 ) );
            },
        );
        foreach ( $steps as $name => $step )
            $report['kernel'][$name] = self::probe( $step );
        if ( $previous === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $previous[0];
        $db->close();
        expODBCTestKit::restoreDebug( $saved );

        $out = $root . '/var/tmp/msaccess-kernel-probe.json';
        if ( !is_dir( dirname( $out ) ) )
            mkdir( dirname( $out ), 0775, true );
        file_put_contents( $out, json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        $schemaFailed = array_filter( $report['schema'] );
        $rows = array_sum( array_column( $report['data'], 'rows' ) );
        $written = array_sum( array_column( $report['data'], 'written' ) );
        $lines = array( sprintf( 'Kernel schema: %d tables, %d made, %d with an error', count( $schema ), $made, count( $schemaFailed ) ),
                        sprintf( 'Kernel data: %d rows, %d written', $rows, $written ) );
        foreach ( array_slice( $schemaFailed, 0, 15, true ) as $table => $errors )
            $lines[] = "  schema $table: " . $errors[0];
        foreach ( $report['data'] as $table => $result )
            if ( $result['errors'] )
                $lines[] = "  data $table: {$result['written']}/{$result['rows']} " . $result['errors'][0];
        foreach ( $report['kernel'] as $name => $result )
            $lines[] = sprintf( '  %-4s %s (%d ms)%s', $result['ok'] ? 'PASS' : 'FAIL', $name, $result['ms'], $result['ok'] ? '' : ': ' . $result['error'] );
        fwrite( STDERR, "\n" . implode( "\n", $lines ) . "\n" );
        $this->assertGreaterThan( 0, $made, 'not one kernel table could be made on Access' );
    }
}
?>
