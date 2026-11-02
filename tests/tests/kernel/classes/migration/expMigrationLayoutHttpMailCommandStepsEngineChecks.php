<?php
/**
 * The checks of the step types layout, layout_block, layout_rule, http, mail and command that run on every engine the
 * same way: SQLite in process (expMigrationLayoutHttpMailCommandStepsSqliteTest), MySQL/MariaDB and PostgreSQL in the
 * database job (expMigrationLayoutHttpMailCommandStepsDatabaseTest), MongoDB in the live MongoDB suite
 * (expMigrationLayoutHttpMailCommandStepsMongoDBTest). Each gives a connection with the kernel schema and the clean
 * data.
 *
 *  LE-1 layouts with Exponential Layouts (its classes from the installation, EXP_TEST_EXPLAYOUTS_PATH or extension/,
 *       and its tables made by the test): a shared layout with a block, a layout whose zone takes the shared zone's
 *       blocks, blocks with parameters in their order, rules by node and path; updated, ensured unchanged, a block and
 *       the rules deleted; a create rolled back. Without the extensions: the step fails and says so, missing: skip
 *       passes
 *  LE-2 http, mail and command through the runner (a transport, a mail folder and a process runner of the test): a dry
 *       run sends, mails and runs nothing; a run does each once; a migration with them cannot be rolled back by itself
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationContentStepsEngineChecks.php';

trait expMigrationLayoutHttpMailCommandStepsEngineChecks
{
    use expMigrationContentStepsEngineChecks;

    /** The tables of Exponential Layouts the checks use, children first. */
    protected static $wp10eTables = array( 'explayouts_rule_target', 'explayouts_rule_condition', 'explayouts_rule', 'explayouts_block_parameter',
                                           'explayouts_collection_item', 'explayouts_collection_query', 'explayouts_collection', 'explayouts_block',
                                           'explayouts_zone', 'explayouts_layout' );

    /**
     * @param eZDBInterface $db
     * @param expMigration[] $migrations
     * @return array the report
     */
    protected function wp10eRun( $db, array $migrations )
    {
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        return $report;
    }

    /**
     * @param array $report
     * @return string[]
     */
    protected static function wp10eMessages( array $report )
    {
        $lines = array();
        foreach ( $report['migrations'] as $migration )
            $lines = array_merge( $lines, (array)$migration['messages'] );
        return $lines;
    }

    /**
     * Makes the classes of Exponential Layouts loadable and its tables, when the extensions can be found: the
     * installation's, the folder of EXP_TEST_EXPLAYOUTS_PATH (holding explayouts and explayouts_core), or extension/
     * of the checkout.
     *
     * @param eZDBInterface $db
     * @return bool whether Exponential Layouts is ready
     */
    protected function wp10eLayouts( $db )
    {
        $base = null;
        if ( class_exists( 'expLayoutsLayout' ) && class_exists( 'expLayoutsCoreLayoutService' ) )
            $base = dirname( ( new ReflectionClass( 'expLayoutsLayout' ) )->getFileName(), 3 );
        else
        {
            foreach ( array( (string)getenv( 'EXP_TEST_EXPLAYOUTS_PATH' ), self::runnerRoot() . '/extension' ) as $candidate )
                if ( $candidate !== '' && is_file( "$candidate/explayouts/classes/explayoutslayout.php" ) && is_dir( "$candidate/explayouts_core/classes" ) )
                {
                    $base = $candidate;
                    break;
                }
            if ( $base === null )
                return false;
            $dirs = array( "$base/explayouts/classes", "$base/explayouts_core/classes" );
            spl_autoload_register( function ( $class ) use ( $dirs ) {
                if ( strncasecmp( $class, 'expLayouts', 10 ) !== 0 )
                    return;
                foreach ( $dirs as $dir )
                    if ( is_file( "$dir/" . strtolower( $class ) . '.php' ) )
                    {
                        require_once "$dir/" . strtolower( $class ) . '.php';
                        return;
                    }
            } );
        }
        if ( !is_file( "$base/explayouts/share/db_schema.dba" ) )
            return false;
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array(), $db, "$base/explayouts/share/db_schema.dba", $messages ), 'the tables of explayouts: ' . implode( '; ', $messages ) );
        $this->wp10eEmptyLayouts( $db );
        return expMigrationStepLayoutBase::installed();
    }

    /** @param eZDBInterface $db */
    protected function wp10eEmptyLayouts( $db )
    {
        foreach ( self::$wp10eTables as $table )
            $db->query( "DELETE FROM $table" );
    }

    /**
     * The top-level blocks of a zone of a published layout: name => position.
     *
     * @param string $layout
     * @param string $zone
     * @return array
     */
    protected static function wp10eBlocks( $layout, $zone )
    {
        $published = expLayoutsLayout::fetchByIdentifier( $layout, 2 );
        $row = $published ? expLayoutsZone::fetchByLayoutAndIdentifier( (int)$published->attribute( 'id' ), $zone, 2 ) : null;
        $blocks = array();
        foreach ( $row ? (array)expLayoutsBlock::fetchByZone( (int)$row->attribute( 'id' ), 2 ) : array() as $block )
            $blocks[(string)$block->attribute( 'name' )] = (int)$block->attribute( 'position' );
        asort( $blocks );
        return $blocks;
    }

    /**
     * LE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkLayouts( $db )
    {
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261106%wp10e%'" );
        $this->contentClean( $db, array( "wp10e-folder" ) );
        if ( !$this->wp10eLayouts( $db ) )
        {
            $failing = $this->contentMigrations( 'layouts-missing', array(
                '20261106100000_wp10e_no_layouts.yml' => "# description: A layout without explayouts\n- type: layout\n  mode: create\n  identifier: landing\n" ) );
            $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $failing );
            $this->assertSame( 1, $report['exit'] );
            $this->assertStringContainsString( 'needs Exponential Layouts (the extensions explayouts and explayouts_core), which is not installed here',
                                               implode( '; ', $report['errors'] ) . json_encode( $report['migrations'] ) );
            $skipped = $this->contentMigrations( 'layouts-skipped', array(
                '20261106100100_wp10e_no_layouts_skip.yml' => "# description: Skipped without explayouts\n- type: layout\n  mode: create\n  identifier: landing\n  missing: skip\n" ) );
            $report = $this->wp10eRun( $db, $skipped );
            $this->assertContains( 'Exponential Layouts (explayouts) is not installed: skipped (missing: skip)', self::wp10eMessages( $report ) );
            return;
        }
        $migrations = $this->contentMigrations( 'layouts', array(
            '20261106100000_wp10e_layouts.yml' => <<<'YAML'
# description: A shared layout, a landing page that takes its header, blocks and rules
steps:
    - type: content
      mode: create
      class: folder
      parent: 2
      remote_id: wp10e-folder
      fields: { name: WP10e Folder }
    - type: layout
      mode: create
      identifier: wp10e_header_footer
      name: WP10e Header / Footer
      shared: true
      zones: { header: {}, footer: {} }
    - type: layout_block
      mode: create
      layout: wp10e_header_footer
      zone: header
      name: Logo
      definition: html
      parameters: { content: "<b>WP10e</b>" }
    - type: layout
      mode: ensure
      identifier: wp10e_landing
      name: WP10e Landing
      zones:
          header: { link: "wp10e_header_footer/header" }
          main: {}
      references: { landing: layout_id }
    - type: layout_block
      mode: create
      layout: wp10e_landing
      zone: main
      name: Welcome
      definition: title
      view_type: title
      parameters: { title: Welcome, tag: h1 }
      references: { welcome: block_id }
    - type: layout_block
      mode: ensure
      layout: wp10e_landing
      zone: main
      name: Intro
      definition: text
      position: 0
    - type: layout_rule
      mode: create
      layout: wp10e_landing
      targets: [ { type: node, value: "object-remote:wp10e-folder" } ]
      priority: 70
    - type: layout_rule
      mode: ensure
      layout: wp10e_landing
      targets: [ { type: path_prefix, value: /wp10e } ]
      conditions: [ { type: siteaccess, value: site } ]
YAML
        ) );
        $report = $this->wp10eRun( $db, $migrations );
        $messages = self::wp10eMessages( $report );
        $shared = expLayoutsLayout::fetchByIdentifier( 'wp10e_header_footer', 2 );
        $landing = expLayoutsLayout::fetchByIdentifier( 'wp10e_landing', 2 );
        $this->assertInstanceOf( 'expLayoutsLayout', $shared );
        $this->assertInstanceOf( 'expLayoutsLayout', $landing );
        $this->assertFalse( expLayoutsLayout::fetchByIdentifier( 'wp10e_landing', 1 ), 'no draft is left' );
        $this->assertTrue( $shared->isShared() );
        $header = expLayoutsZone::fetchByLayoutAndIdentifier( (int)$landing->attribute( 'id' ), 'header', 2 );
        $this->assertTrue( $header->isLinked() );
        $this->assertSame( (int)$shared->attribute( 'id' ), (int)$header->attribute( 'linked_layout_id' ) );
        $this->assertSame( 'header', (string)$header->attribute( 'linked_zone_identifier' ) );
        $this->assertSame( array( 'Intro' => 0, 'Welcome' => 1 ), self::wp10eBlocks( 'wp10e_landing', 'main' ) );
        $this->assertSame( array( 'Logo' => 0 ), self::wp10eBlocks( 'wp10e_header_footer', 'header' ) );
        $this->assertContains( 'the zone header takes the blocks of wp10e_header_footer/header', $messages );
        $rules = expLayoutsRule::fetchObjectList( expLayoutsRule::definition(), null, array( 'layout_id' => (int)$landing->attribute( 'id' ) ), array( 'priority' => 'desc' ) );
        $this->assertCount( 2, $rules );
        $this->assertContains( 70, array_map( function ( $rule ) { return (int)$rule->attribute( 'priority' ); }, $rules ) );
        $targets = array();
        foreach ( $rules as $rule )
            foreach ( $rule->targets() as $target )
                $targets[] = $target->attribute( 'target_type' ) . '=' . $target->attribute( 'target_value' );
        sort( $targets );
        $this->assertSame( array( 'node=' . (int)self::contentObject( 'wp10e-folder' )->attribute( 'main_node_id' ), 'path_prefix=/wp10e' ), $targets, 'the node is stored by its id' );
        $welcome = expLayoutsBlock::fetch( self::wp10eBlockId( 'wp10e_landing', 'main', 'Welcome' ) );
        $this->assertInstanceOf( 'expLayoutsBlock', $welcome );
        $params = array();
        foreach ( expLayoutsBlockParameter::fetchByBlock( (int)$welcome->attribute( 'id' ) ) as $parameter )
            $params[(string)$parameter->attribute( 'name' )] = (string)$parameter->attribute( 'value' );
        $this->assertSame( 'Welcome', $params['title'] );
        $this->assertSame( 'h1', $params['tag'] );
        foreach ( array( 'content', 'template-block' ) as $cache )
            $this->assertContains( $cache, $report['caches'] );

        $changes = $this->contentMigrations( 'layouts-change', array(
            '20261106100100_wp10e_layouts_change.yml' => <<<'YAML'
# description: The landing page changed
steps:
    - type: layout
      mode: ensure
      identifier: wp10e_landing
      name: WP10e Landing
      zones: { main: {} }
    - type: layout
      mode: update
      identifier: wp10e_landing
      name: WP10e Landing page
    - type: layout_block
      mode: update
      layout: wp10e_landing
      zone: main
      name: Welcome
      parameters: { title: Hello }
    - type: layout_block
      mode: delete
      layout: wp10e_landing
      zone: main
      name: Intro
    - type: layout_rule
      mode: ensure
      layout: wp10e_landing
      targets: [ { type: path_prefix, value: /wp10e } ]
      conditions: [ { type: siteaccess, value: site } ]
    - type: layout_rule
      mode: delete
      layout: wp10e_landing
      which: all
YAML
        ) );
        $report = $this->wp10eRun( $db, $changes );
        $messages = self::wp10eMessages( $report );
        $this->assertContains( 'the layout wp10e_landing is as the step says: nothing changed', $messages );
        $this->assertContains( 'the rule of the layout wp10e_landing for path_prefix "/wp10e" is as the step says: nothing changed', $messages );
        $landing = expLayoutsLayout::fetchByIdentifier( 'wp10e_landing', 2 );
        $this->assertSame( 'WP10e Landing page', (string)$landing->attribute( 'name' ) );
        $this->assertSame( array( 'Welcome' => 0 ), self::wp10eBlocks( 'wp10e_landing', 'main' ) );
        $this->assertSame( array(), expLayoutsRule::fetchObjectList( expLayoutsRule::definition(), null, array( 'layout_id' => (int)$landing->attribute( 'id' ) ) ) );
        $this->assertTrue( expLayoutsZone::fetchByLayoutAndIdentifier( (int)$landing->attribute( 'id' ), 'header', 2 )->isLinked(), 'the link is kept by a publish' );

        $revert = $this->contentMigrations( 'layouts-revert', array(
            '20261106100200_wp10e_layout_revert.yml' => "# description: A layout rolled back\n- type: layout\n  mode: create\n  identifier: wp10e_temp\n  zones: { main: {} }\n"
                                                      . "- type: layout_block\n  mode: create\n  layout: wp10e_temp\n  zone: main\n  name: Temp\n  definition: title\n" ) );
        $this->assertTrue( $revert[0]->reversible() );
        $this->wp10eRun( $db, $revert );
        $this->assertSame( array( 'Temp' => 0 ), self::wp10eBlocks( 'wp10e_temp', 'main' ) );
        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $revert );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) );
        $this->assertFalse( expLayoutsLayout::fetchByIdentifier( 'wp10e_temp', 2 ) );
        $this->assertFalse( expLayoutsLayout::fetchByIdentifier( 'wp10e_temp', 1 ) );

        $failing = $this->contentMigrations( 'layouts-fail', array(
            '20261106100300_wp10e_layout_fail.yml' => "# description: A link to a layout that is not shared\n- type: layout\n  mode: update\n  identifier: wp10e_landing\n"
                                                    . "  zones: { main: { link: \"wp10e_landing/main\" } }\n" ) );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $failing );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( 'zones.main.link: the layout wp10e_landing is not shared', json_encode( $report['migrations'] ) );
        $this->wp10eEmptyLayouts( $db );
        $this->contentClean( $db, array( 'wp10e-folder' ) );
    }

    /**
     * The id of a block of a zone of a published layout, by its name; 0 when there is none.
     *
     * @param string $layout
     * @param string $zone
     * @param string $name
     * @return int
     */
    protected static function wp10eBlockId( $layout, $zone, $name )
    {
        $published = expLayoutsLayout::fetchByIdentifier( $layout, 2 );
        $row = $published ? expLayoutsZone::fetchByLayoutAndIdentifier( (int)$published->attribute( 'id' ), $zone, 2 ) : null;
        foreach ( $row ? (array)expLayoutsBlock::fetchByZone( (int)$row->attribute( 'id' ), 2 ) : array() as $block )
            if ( (string)$block->attribute( 'name' ) === $name )
                return (int)$block->attribute( 'id' );
        return 0;
    }

    /**
     * LE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkHttpMailCommand( $db )
    {
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261106%wp10e%'" );
        $mailDir = $this->runnerDir() . '/mail';
        mkdir( $mailDir, 0775, true );
        $ini = eZINI::instance();
        $before = $ini->hasVariable( 'MailSettings', 'FileTransportDirectory' ) ? $ini->variable( 'MailSettings', 'FileTransportDirectory' ) : '';
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $mailDir );
        $sent = array();
        $ran = array();
        expMigrationStepHttp::setTransport( function ( $method, $url, array $headers, $body ) use ( &$sent ) {
            $sent[] = "$method $url $body";
            return array( 200, '{"ok":true}', null );
        } );
        expMigrationProcess::setRunner( function ( array $command ) use ( &$ran ) {
            $ran[] = implode( ' ', array_slice( $command, 1 ) );
            return array( 0, 'done' );
        } );
        try
        {
            $migrations = $this->contentMigrations( 'outside', array(
                '20261106110000_wp10e_outside.yml' => <<<'YAML'
# description: Tell the outside world
steps:
    - type: http
      method: POST
      url: https://hooks.example.com/wp10e
      json: { event: migrated }
      references: { hook: status }
    - type: mail
      to: [ editors@example.com ]
      subject: WP10e migrated (${ref.hook})
      body: The migration ran.
      transport: file
    - type: command
      command: exp:services:compile
      arguments: [ --quiet ]
YAML
            ) );
            $this->assertFalse( $migrations[0]->reversible(), 'http, mail and command do not undo themselves' );
            $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
            $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
            $plan = implode( "\n", (array)$dry['migrations'][0]['plan'] );
            $this->assertStringContainsString( 'send POST https://hooks.example.com/wp10e (a JSON body) (not sent in a dry run)', $plan );
            $this->assertStringContainsString( 'run ./console exp:services:compile --quiet (not run in a dry run)', $plan );
            $this->assertSame( array(), $sent, 'a dry run sends nothing' );
            $this->assertSame( array(), $ran, 'a dry run runs nothing' );
            $this->assertSame( array(), glob( "$mailDir/*.mail" ), 'a dry run mails nothing' );

            $report = $this->wp10eRun( $db, $migrations );
            $messages = self::wp10eMessages( $report );
            $this->assertSame( array( 'POST https://hooks.example.com/wp10e {"event":"migrated"}' ), $sent );
            $this->assertSame( 1, count( $ran ) );
            $this->assertStringStartsWith( 'bin/php/console exp:services:compile --quiet', $ran[0] );
            $this->assertContains( 'POST https://hooks.example.com/wp10e answered 200', $messages );
            $this->assertContains( 'sent the mail "WP10e migrated (200)" to 1 address', $messages );
            $this->assertContains( './console exp:services:compile exited with 0', $messages );
            $mails = glob( "$mailDir/*.mail" );
            $this->assertCount( 1, $mails );
            $this->assertStringContainsString( 'WP10e migrated (200)', file_get_contents( $mails[0] ) );
        }
        finally
        {
            expMigrationStepHttp::setTransport( null );
            expMigrationProcess::setRunner( null );
            $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $before );
        }
    }
}
