<?php
/**
 * The checks of URL map import, export and undo that run on every engine the same way: SQLite in process
 * (expUrlMapImportSqliteTest), MySQL/MariaDB and PostgreSQL in the database job (expUrlMapImportDatabaseTest). Each is
 * given a connection with the kernel schema and the clean data (nodes 2, 5 Users, 43 Media), which it may write to.
 *
 *  UE-1 the upgrade step 6.0.15-url-map: missing without the tables, makes all three, present afterwards; without
 *       them an import is refused and the redirects are empty
 *  UE-2 import: a 301 redirect becomes a URL alias that redirects, an alias one that does not, wildcards forward,
 *       forward temporary (302) and direct, redirects with a query and temporary ones in exp_url_redirect; a broken
 *       target and a page's own address are not imported; the batch and its items; the audit event
 *  UE-3 resolve: the alias redirects (eZURLAliasML::translate), the wildcards answer 301 and 302
 *       (eZURLWildcard::translate, lastRedirectStatus()), the redirects answer the request with its query in any
 *       order (expUrlRedirect::forRequest) and only that query
 *  UE-4 export: every imported entry comes back, and planning the export again finds each one unchanged
 *       (import(export(x)) == x)
 *  UE-5 overwrite and undo: a second import with on_conflict overwrite changes an alias, a wildcard and a redirect;
 *       its undo puts them back; the first import's undo removes everything, so the URL alias table is as before
 *  UE-6 an entry changed after the import is kept by the undo (partly-undone), force undoes it too; an undone batch
 *       cannot be undone again
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expUrlMapEngineChecks
{
    /** @var array[] audit events: name, data */
    protected $urlMapAudits = array();

    /** Public for the closure. */
    public function urlMapAudit( $name, $data )
    {
        $this->urlMapAudits[] = array( $name, $data );
    }

    /** @return expUrlMapImporter */
    protected function urlMapImporter( $db )
    {
        $test = $this;
        return new expUrlMapImporter( array( 'db' => $db, 'user_id' => 14, 'audit' => function ( $name, $data ) use ( $test ) { $test->urlMapAudit( $name, $data ); } ) );
    }

    /**
     * @param array $rows lists of the row's constructor arguments
     * @param array $options
     * @return expUrlMapPlan
     */
    protected function urlMapPlan( array $rows, array $options = array() )
    {
        $objects = array();
        foreach ( $rows as $i => $args )
        {
            $args += array( 2 => '', 3 => '', 4 => '', 5 => '', 6 => $i + 1, 7 => '' );
            ksort( $args );
            $objects[] = new expUrlMapRow( ...$args );
        }
        return ( new expUrlMapPlanner() )->plan( $objects, $options + array( 'siteaccess' => self::urlMapSiteaccess(), 'language' => self::urlMapLanguage(), 'source_name' => 'test.csv', 'format' => 'csv' ) );
    }

    /** @return string the siteaccess bare paths belong to: the installation's default one */
    protected static function urlMapSiteaccess()
    {
        $environment = new expUrlMapKernelEnvironment();
        $list = $environment->siteaccesses();
        return in_array( $environment->defaultSiteaccess(), $list, true ) || !$list ? $environment->defaultSiteaccess() : (string)reset( $list );
    }

    /** @return string the language of the clean data (eng-US) */
    protected static function urlMapLanguage()
    {
        $languages = eZContentLanguage::fetchList();
        return $languages ? (string)reset( $languages )->attribute( 'locale' ) : 'eng-GB';
    }

    /** @return int */
    protected static function urlMapCount( $db, $table, $where = '' )
    {
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . $table . ( $where !== '' ? " WHERE $where" : '' ) );
        return (int)( isset( $rows[0]['n'] ) ? $rows[0]['n'] : ( $rows ? reset( $rows[0] ) : 0 ) );
    }

    /** @return array[] the rows of ezurlalias_ml, sorted, to compare before and after */
    protected static function urlMapAliasTable( $db )
    {
        $rows = $db->arrayQuery( 'SELECT * FROM ezurlalias_ml' );
        $rows = array_map( function ( $row ) { $row = array_map( 'strval', array_change_key_case( (array)$row, CASE_LOWER ) ); ksort( $row ); return $row; }, $rows );
        usort( $rows, function ( $a, $b ) { return strcmp( $a['parent'] . ':' . $a['text_md5'], $b['parent'] . ':' . $b['text_md5'] ); } );
        return $rows;
    }

    /** Drops the tables of the feature (when they exist). */
    protected function dropUrlMapTables( $db )
    {
        foreach ( array( expUrlRedirect::TABLE, expUrlMapBatch::TABLE, expUrlMapBatch::ITEM_TABLE ) as $table )
        {
            expKernelTableSchema::reset();
            if ( expKernelTableSchema::exists( $table, $db, true ) === true )
                $db->query( 'DROP TABLE ' . $table );
        }
        // PostgreSQL: the sequences go with the tables only when they were made by the tables (SERIAL); here they
        // were made first, so they are dropped too, or the step's CREATE SEQUENCE would find them
        if ( expKernelTableSchema::type( $db ) === 'postgresql' )
            foreach ( array( expUrlRedirect::TABLE, expUrlMapBatch::TABLE, expUrlMapBatch::ITEM_TABLE ) as $table )
                $db->query( 'DROP SEQUENCE IF EXISTS ' . $table . '_id_seq' );
        expKernelTableSchema::reset();
        expUrlRedirect::resetRequest();
    }

    /** UE-1 */
    protected function checkUrlMapTables( $db, $root )
    {
        $this->dropUrlMapTables( $db );
        $this->assertFalse( expUrlMapBatch::tablesExist( $db ) );
        $this->assertSame( array(), expUrlRedirect::buildIndex() );
        $plan = $this->urlMapPlan( array( array( '/x?a=1', 'node:2' ) ) );
        try
        {
            $this->urlMapImporter( $db )->apply( $plan );
            $this->fail( 'an import without the tables' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( '6.0.15-url-map', $e->getMessage() );
        }
        $context = new expUpgradeContext( $db, true, array(), $root . '/share/db_schema.dba' );
        $step = new expUpgradeStepUrlMapTables();
        $this->assertSame( '6.0.15-url-map', $step->id() );
        $effect = $step->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $effect['effect'], $effect['detail'] );
        $this->assertSame( array( 'created table exp_url_redirect', 'created table exp_urlmap_batch', 'created table exp_urlmap_batch_item' ), $step->apply( $context ) );
        expKernelTableSchema::reset();
        expUrlRedirect::resetRequest();
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $context )['effect'] );
        $this->assertTrue( expUrlMapBatch::tablesExist( $db ) );
        $this->assertTrue( expUrlRedirect::tableExists( $db ) );
    }

    /**
     * UE-2 to UE-6
     *
     * @param eZDBInterface $db
     */
    protected function checkUrlMapImportResolveExportUndo( $db )
    {
        $aliasesBefore = self::urlMapAliasTable( $db );
        $wildcardsBefore = self::urlMapCount( $db, 'ezurlwildcard' );
        $rows = array(
            array( '/old-home.html', 'node:2' ),                                  // 0 alias, redirecting
            array( '/about-old', '/users', 'alias' ),                             // 1 alias, not redirecting
            array( '/legacy/*', '/users/{1}' ),                                    // 2 wildcard forward
            array( '/tmp/*', '/media/{1}', 'wildcard-forward-temporary' ),         // 3 wildcard 302
            array( '/print/*', '/{1}', 'wildcard-direct' ),                        // 4 wildcard direct
            array( '/index.php?id=12&lang=en', 'node:43' ),                        // 5 redirect with a query
            array( '/promo', '/content/search', 'redirect-temporary', '', '', '', 0, '307' ), // 6 redirect 307
            array( '/missing', '/no/such/page' ),                                  // 7 broken target
            array( '/users', 'node:2' ),                                           // 8 conflict: a page
            array( '/deep/old/path.html', '/media' ),                              // 9 alias with new parents
        );
        $plan = $this->urlMapPlan( $rows );
        $this->assertSame( array( 'new', 'new', 'new', 'new', 'new', 'new', 'new', 'broken-target', 'conflict', 'new' ),
                           array_column( $plan->items(), 'state' ), json_encode( array_column( $plan->items(), 'messages' ) ) );
        $this->assertSame( array( 'alias', 'alias', 'wildcard', 'wildcard', 'wildcard', 'redirect', 'redirect', '', '', 'alias' ), array_column( $plan->items(), 'store' ) );

        // a dry run writes nothing
        $dry = $this->urlMapImporter( $db )->apply( $plan, array( 'dry_run' => true ) );
        $this->assertSame( array( 'created' => 8, 'updated' => 0, 'skipped' => 2, 'failed' => 0 ), $dry['counts'] );
        $this->assertSame( $aliasesBefore, self::urlMapAliasTable( $db ) );
        $this->assertSame( 0, self::urlMapCount( $db, expUrlMapBatch::TABLE ) );

        // UE-2
        $this->urlMapAudits = array();
        $result = $this->urlMapImporter( $db )->apply( $plan );
        $this->assertSame( array( 'created' => 8, 'updated' => 0, 'skipped' => 2, 'failed' => 0 ), $result['counts'], json_encode( $result['items'] ) );
        $batchId = $result['batch_id'];
        $this->assertGreaterThan( 0, $batchId );
        $batch = expUrlMapBatch::fetch( $batchId );
        $this->assertSame( array( 'applied', 'test.csv', 'csv', self::urlMapSiteaccess(), 14, 10, 8 ),
                           array( $batch->attribute( 'state' ), $batch->attribute( 'source_name' ), $batch->attribute( 'format' ), $batch->attribute( 'siteaccess' ),
                                  (int)$batch->attribute( 'user_id' ), (int)$batch->attribute( 'rows_total' ), (int)$batch->attribute( 'created_count' ) ) );
        $this->assertSame( 10, $batch->summaryArray()['plan']['rows'] );
        $items = $batch->items();
        $this->assertGreaterThanOrEqual( 10, count( $items ), 'deep/old/path.html writes three elements' );
        $this->assertSame( array( 'ezurlalias_ml', 'ezurlwildcard', 'exp_url_redirect' ), array_values( array_unique( array_map( function ( $i ) { return $i->attribute( 'target_table' ); }, $items ) ) ) );
        foreach ( $items as $item )
        {
            $this->assertSame( 'created', $item->attribute( 'action' ) );
            $this->assertNull( $item->before() );
            $this->assertNotNull( $item->after() );
        }
        $this->assertSame( 'content.urlalias.import', $this->urlMapAudits[0][0] );
        $this->assertSame( 8, $this->urlMapAudits[0][1]['after']['created'] );

        // UE-3: the alias
        $uri = 'old-home.html';
        $this->assertIsString( eZURLAliasML::translate( $uri ), 'a redirecting alias answers with the address to go to' );
        $uri = 'about-old';
        $this->assertTrue( eZURLAliasML::translate( $uri ) );
        $this->assertSame( 'content/view/full/5', $uri );
        $uri = 'deep/old/path.html';
        $this->assertIsString( eZURLAliasML::translate( $uri ) );
        // the wildcards
        eZURLWildcard::expireCache();
        $uri = 'legacy/editors';
        $this->assertSame( 'users/editors', eZURLWildcard::translate( $uri ) );
        $this->assertSame( 301, eZURLWildcard::lastRedirectStatus() );
        $uri = 'tmp/images';
        $this->assertSame( 'media/images', eZURLWildcard::translate( $uri ) );
        $this->assertSame( 302, eZURLWildcard::lastRedirectStatus() );
        // the redirects: the query in any order, only that query
        expUrlRedirect::resetRequest();
        $this->assertSame( array( 'location' => 'content/view/full/43', 'status' => 301 ),
                           array_intersect_key( (array)expUrlRedirect::forRequest( self::urlMapSiteaccess(), '/index.php?lang=en&id=12', '', 'lang=en&id=12' ), array( 'location' => 1, 'status' => 1 ) ) );
        $this->assertNull( expUrlRedirect::forRequest( self::urlMapSiteaccess(), '/index.php?id=12', '', 'id=12' ) );
        $this->assertNull( expUrlRedirect::forRequest( 'no_such_siteaccess', '/index.php?lang=en&id=12', '', 'lang=en&id=12' ), 'only in its siteaccess' );
        $this->assertSame( array( 'location' => 'content/search', 'status' => 307 ),
                           array_intersect_key( (array)expUrlRedirect::forRequest( self::urlMapSiteaccess(), '/promo', 'promo', '' ), array( 'location' => 1, 'status' => 1 ) ) );

        // UE-4: export, and the export planned again is unchanged
        $exported = ( new expUrlMapExporter() )->export( array( 'kinds' => array( 'alias', 'wildcard', 'redirect' ), 'include_node_aliases' => true ) );
        $byOld = array();
        foreach ( $exported as $row )
            $byOld[$row->oldUrl()] = $row;
        foreach ( array( '/old-home.html', '/about-old', '/legacy/*', '/tmp/*', '/print/*', '/index.php?id=12&lang=en', '/promo', '/deep/old/path.html' ) as $old )
            $this->assertArrayHasKey( $old, $byOld, "$old is exported" );
        $this->assertSame( 'wildcard-forward-temporary', $byOld['/tmp/*']->kind() );
        $this->assertSame( 307, $byOld['/promo']->redirectStatus() );
        $this->assertSame( self::urlMapSiteaccess(), $byOld['/promo']->siteaccess() );
        $this->assertSame( 'alias', $byOld['/about-old']->kind() );
        $mine = array_values( array_filter( $exported, function ( $row ) {
            return in_array( $row->oldUrl(), array( '/old-home.html', '/about-old', '/legacy/*', '/tmp/*', '/print/*', '/index.php?id=12&lang=en', '/promo', '/deep/old/path.html' ), true );
        } ) );
        foreach ( array( 'csv', 'json', 'apache' ) as $format )
        {
            $back = expUrlMapFormats::reader( $format )->readString( expUrlMapFormats::writer( $format )->writeString( $mine ) );
            $again = ( new expUrlMapPlanner() )->plan( $back, array( 'siteaccess' => self::urlMapSiteaccess(), 'language' => self::urlMapLanguage() ) );
            $this->assertSame( array_fill( 0, count( $mine ), 'unchanged' ), array_column( $again->items(), 'state' ),
                               $format . ': ' . json_encode( array_column( $again->items(), 'messages' ) ) );
        }

        // UE-5: overwrite and undo
        // (a second later: the wildcard cache counts a file written in the second of its expiry as current)
        sleep( 1 );
        $plan2 = $this->urlMapPlan( array(
            array( '/old-home.html', '/media' ),
            array( '/legacy/*', '/media/{1}' ),
            array( '/index.php?id=12&lang=en', 'node:5', 'redirect-temporary' ),
            array( '/brand-new', '/users' ),
        ), array( 'on_conflict' => 'overwrite' ) );
        $this->assertSame( array( 'changed', 'changed', 'changed', 'new' ), array_column( $plan2->items(), 'state' ) );
        $result2 = $this->urlMapImporter( $db )->apply( $plan2 );
        $this->assertSame( array( 'created' => 1, 'updated' => 3, 'skipped' => 0, 'failed' => 0 ), $result2['counts'] );
        eZURLWildcard::expireCache();
        $uri = 'legacy/x';
        $this->assertSame( 'media/x', eZURLWildcard::translate( $uri ) );
        expUrlRedirect::resetRequest();
        $this->assertSame( 302, expUrlRedirect::forRequest( self::urlMapSiteaccess(), '/index.php', '', 'id=12&lang=en' )['status'] );
        sleep( 1 );
        $undo2 = $this->urlMapImporter( $db )->undo( $result2['batch_id'] );
        $this->assertSame( 'undone', $undo2['state'] );
        $this->assertSame( 0, $undo2['counts']['kept'] );
        eZURLWildcard::expireCache();
        $uri = 'legacy/x';
        $this->assertSame( 'users/x', eZURLWildcard::translate( $uri ) );
        expUrlRedirect::resetRequest();
        $this->assertSame( array( 'content/view/full/43', 301 ), array_values( array_intersect_key( expUrlRedirect::forRequest( self::urlMapSiteaccess(), '/index.php', '', 'id=12&lang=en' ),
                                                                                                   array( 'location' => 1, 'status' => 1 ) ) ) );
        $uri = 'brand-new';
        $this->assertFalse( eZURLAliasML::translate( $uri ) );
        $this->assertSame( 'content.urlalias.import_undo', end( $this->urlMapAudits )[0] );
        try
        {
            $this->urlMapImporter( $db )->undo( $result2['batch_id'] );
            $this->fail( 'a batch undone twice' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'undone already', $e->getMessage() );
        }

        // UE-6: an entry changed after the import is kept, force undoes it
        $db->query( "UPDATE ezurlwildcard SET destination_url = 'changed/{1}' WHERE source_url = 'print/*'" );
        $undo1 = $this->urlMapImporter( $db )->undo( $batchId );
        $this->assertSame( 'partly-undone', $undo1['state'] );
        $this->assertSame( 1, $undo1['counts']['kept'] );
        $this->assertSame( 1, self::urlMapCount( $db, 'ezurlwildcard', "source_url = 'print/*'" ) );
        $undo1 = $this->urlMapImporter( $db )->undo( $batchId, array( 'force' => true ) );
        $this->assertSame( 'undone', $undo1['state'] );
        $this->assertSame( 1, $undo1['counts']['undone'], 'force undoes the item the first undo kept' );

        // everything is gone again
        $this->assertSame( $aliasesBefore, self::urlMapAliasTable( $db ) );
        $this->assertSame( $wildcardsBefore, self::urlMapCount( $db, 'ezurlwildcard' ) );
        $this->assertSame( 0, self::urlMapCount( $db, expUrlRedirect::TABLE ) );
        expUrlRedirect::resetRequest();
        $this->assertNull( expUrlRedirect::forRequest( self::urlMapSiteaccess(), '/promo', 'promo', '' ) );
        $this->assertSame( array( 'undone', 'undone' ), array_map( function ( $b ) { return $b->attribute( 'state' ); }, expUrlMapBatch::fetchList() ) );
    }
}
