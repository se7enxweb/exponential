<?php
/**
 * @description Write a new migration file: --skeleton writes an empty migration (PHP, or with --type=yml, sql, ini or graphql that kind); --from-recording turns a recording of changes into a YAML migration; --from-content, --from-class, --from-role, --from-section and --from-settings turn what the installation has into one; --from-diff turns what changed since a caption, another installation or a database dump into one; a dry run unless --apply
 * @alias exp:migration:generate
 *
 * File containing the exp:migration:generate command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationgenerate extends ExpmigrationBase
{
    /** The options that generate from what the installation has. */
    const FROM_EXISTING = array( 'from-content', 'from-class', 'from-role', 'from-section', 'from-settings' );

    protected function name()
    {
        return 'exp:migration:generate';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Write a new migration\n" .
                             "--skeleton writes an empty PHP migration named YYYYMMDDHHMMSS_<name>.php after the time of now,\n" .
                             "with the header (description, author), plan(), up() and down(), in the site's migrations directory\n" .
                             "(the first [MigrationSettings] DirectoryList entry) or, with --source=extension:<name>, in the\n" .
                             "extension's. The class is named after the name and the time, in the namespace Site\\Migrations or\n" .
                             "Extension\\<Name>\\Migrations. --type=yml writes YYYYMMDDHHMMSS_<name>.yml instead (the header and an\n" .
                             "empty list of steps), --type=graphql the same with an example of the step graphql, --type=ini an INI\n" .
                             "migration and --type=sql an SQL migration (with --engine=mysql, pgsql, sqlite or oracle the file of one\n" .
                             "engine, with --engine=mongodb the .mongodb.json file). Without --apply it shows the file and writes\n" .
                             "nothing; an existing file is never replaced.\n" .
                             "--from-recording=<id> turns a recording of changes (exp:migration:record:start) into a YAML\n" .
                             "migration: its events folded into one step per thing changed, checked as exp:migration:check checks\n" .
                             "a file. What no step type repeats is listed, and named in the file, to be done by hand.\n" .
                             "--from-content=<node>[,<node>...] (with --depth=<levels below> and --max-objects=<n>), --from-class,\n" .
                             "--from-role, --from-section (identifiers or names, comma-separated) and --from-settings=<file>[,...]\n" .
                             "(of settings/override, or with --siteaccess=<name> of that siteaccess) write a YAML migration that\n" .
                             "makes those things as they are now (mode ensure). Personal data stays out: a user is named by\n" .
                             "secrets, and secret settings are left to be set by hand.\n" .
                             "--from-diff=<caption|address|dump> compares this installation with a saved state: a caption\n" .
                             "(Sicherungspunkt) by id, another installation by its address (https://..., read once through its REST\n" .
                             "route /api/migration/v1/snapshot with an API key of the scope migration:read, --token; never written\n" .
                             "to), or a database dump (a SQLite file, or an SQL dump of this installation's engine, loaded into a\n" .
                             "throwaway database). --compare names what (classes, roles, sections, states, languages, content,\n" .
                             "settings; content with --subtree=<node>). What was added or changed becomes a step that makes it as it\n" .
                             "is now (mode ensure), what was removed a delete step; run on the saved state, the migration makes it\n" .
                             "like this installation. What no step type repeats is listed to be done by hand.\n" .
                             "\n" .
                             "./console exp:migration:generate --skeleton add_news_class\n" .
                             "./console exp:migration:generate --skeleton news_folder --type=yml --apply\n" .
                             "./console exp:migration:generate --skeleton news_index --type=sql --engine=pgsql --apply\n" .
                             "./console exp:migration:generate --skeleton news_settings --type=ini --apply\n" .
                             "./console exp:migration:generate --skeleton add_news_class --description=\"The news class\" --apply\n" .
                             "./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply\n" .
                             "./console exp:migration:generate --from-recording=20261101-120000-a1b2c3 news_section --apply\n" .
                             "./console exp:migration:generate --from-class=news_article --from-role=\"News editor\" news_setup\n" .
                             "./console exp:migration:generate --from-content=path:news --depth=1 news_tree --apply\n" .
                             "./console exp:migration:generate --from-diff=20261102-101500 news_changes\n" .
                             "./console exp:migration:generate --from-diff=var/backup/before.sqlite --compare=classes,roles --apply\n" .
                             "./console exp:migration:generate --from-diff=https://staging.example.com --token=staging_api_key --subtree=path:news",
        ) + self::SCRIPT );
        return (array)$this->startup( '[skeleton][source:][description:][author:][type:][apply][dry-run][format:][from-recording:][engine:][from-content:][depth:][max-objects:][from-class:][from-role:][from-section:][from-settings:][siteaccess:][from-diff:][compare:][subtree:][token:][backup-dir:]', '[name]', array(
            'skeleton' => 'An empty migration with its header: PHP with plan(), up() and down(), or the kind --type names',
            'source' => 'The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id)',
            'description' => 'The description in the header of the new migration',
            'author' => 'The author in the header of the new migration',
            'type' => 'The type of the new migration: php (the default), yml, sql, ini or graphql (a YAML migration with a graphql step to fill in)',
            'engine' => 'With --type=sql: the file of one engine (mysql, pgsql, sqlite, oracle, mongodb) instead of the portable one',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'from-recording' => 'Generate a YAML migration from a recording of changes (its id)',
            'from-content' => 'Generate a YAML migration of content: the nodes (id, remote:<remote id>, path:<url path>), comma-separated',
            'depth' => 'With --from-content: the levels below each node that go in too (default 0: the node alone)',
            'max-objects' => 'With --from-content, or --from-diff with --subtree: at most this many objects (default 500)',
            'from-class' => 'Generate a YAML migration of content classes (identifiers, comma-separated)',
            'from-role' => 'Generate a YAML migration of roles with their policies (names, comma-separated)',
            'from-section' => 'Generate a YAML migration of sections (identifiers, comma-separated)',
            'from-settings' => 'Generate a YAML migration of settings files of settings/override (site.ini ..., comma-separated)',
            'siteaccess' => 'With --from-settings: the files of this siteaccess instead of settings/override',
            'from-diff' => 'Generate a YAML migration of what changed since a saved state: a caption id, the address of another installation (https://...) or a database dump',
            'compare' => 'With --from-diff: what to compare (classes, roles, sections, states, languages, content, settings; comma-separated, default all but content)',
            'subtree' => 'With --from-diff: compare the content of the subtree of this node (id, remote:<remote id>, path:<url path>)',
            'token' => 'With --from-diff=<address>: the API key of the other installation with the scope migration:read, by secret name or env:VARIABLE',
            'backup-dir' => 'Where the captions made without git_manager are (default var/backup/upgrade)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $settings = array();
        foreach ( array( 'source', 'description', 'author', 'type', 'engine' ) as $key )
            if ( isset( $options[$key] ) && $options[$key] !== false && $options[$key] !== '' )
                $settings[$key] = (string)$options[$key];
        $fromRecording = $this->value( $options, 'from-recording' );
        $existing = array();
        foreach ( self::FROM_EXISTING as $key )
            if ( $this->value( $options, $key ) !== '' )
                $existing[$key] = $this->value( $options, $key );
        $fromDiff = $this->value( $options, 'from-diff' );
        $kinds = ( empty( $options['skeleton'] ) ? 0 : 1 ) + ( $fromRecording !== '' ? 1 : 0 ) + ( $existing ? 1 : 0 ) + ( $fromDiff !== '' ? 1 : 0 );
        if ( $kinds > 1 )
            $this->usage( '--skeleton, --from-recording, --from-diff and the options --from-content ... --from-settings exclude each other' );
        if ( $fromDiff === '' )
            foreach ( array( 'compare', 'subtree', 'token' ) as $key )
                if ( $this->value( $options, $key ) !== '' )
                    $this->usage( "--$key goes with --from-diff" );
        if ( $fromDiff !== '' )
        {
            if ( isset( $settings['type'] ) || isset( $settings['engine'] ) )
                $this->usage( 'a generated migration is a YAML migration: --type and --engine go with --skeleton' );
            $settings['name'] = $this->argument( $options );
            $settings['apply'] = $apply;
            foreach ( array( 'compare' => 'compare', 'subtree' => 'subtree', 'token' => 'token', 'backup-dir' => 'backupDir' ) as $option => $key )
                if ( $this->value( $options, $option ) !== '' )
                    $settings[$key] = $this->value( $options, $option );
            if ( $this->value( $options, 'max-objects' ) !== '' )
            {
                if ( !ctype_digit( $this->value( $options, 'max-objects' ) ) )
                    $this->usage( '--max-objects takes a number' );
                $settings['limit'] = (int)$this->value( $options, 'max-objects' );
            }
            $this->database( $format );
            $report = \expMigrationDiff::fromDiff( $fromDiff, $settings );
            $report['mode'] = $apply ? 'generate-from-diff' : 'generate-from-diff-dry-run';
            $this->finish( $report, $format );
        }
        if ( $fromRecording !== '' || $existing )
        {
            if ( isset( $settings['type'] ) || isset( $settings['engine'] ) )
                $this->usage( 'a generated migration is a YAML migration: --type and --engine go with --skeleton' );
            $settings['name'] = $this->argument( $options );
            $settings['apply'] = $apply;
            if ( $fromRecording !== '' )
            {
                if ( !\expMigrationRecording::validId( $fromRecording ) )
                    $this->usage( "--from-recording takes a recording id (<date>-<time>-<6 hex digits>), not $fromRecording" );
                $db = $this->database( $format );
                $report = \expMigrationRecordingGenerator::fromRecording( $db, $fromRecording, $settings );
                $report['mode'] = $apply ? 'generate-from-recording' : 'generate-from-recording-dry-run';
            }
            else
            {
                $this->database( $format );
                $what = array();
                $map = array( 'from-content' => 'content', 'from-class' => 'classes', 'from-role' => 'roles', 'from-section' => 'sections' );
                foreach ( $map as $option => $key )
                    if ( isset( $existing[$option] ) )
                        $what[$key] = self::listOf( $existing[$option] );
                if ( isset( $existing['from-settings'] ) )
                    foreach ( self::listOf( $existing['from-settings'] ) as $file )
                        $what['settings'][] = array( 'file' => $file, 'siteaccess' => $this->value( $options, 'siteaccess' ) );
                foreach ( array( 'depth' => 'depth', 'max-objects' => 'limit' ) as $option => $key )
                    if ( $this->value( $options, $option ) !== '' )
                    {
                        if ( !ctype_digit( $this->value( $options, $option ) ) )
                            $this->usage( "--$option takes a number" );
                        $what[$key] = (int)$this->value( $options, $option );
                    }
                $report = \expMigrationRecordingGenerator::fromExisting( $what, $settings );
                $report['mode'] = $apply ? 'generate-from-existing' : 'generate-from-existing-dry-run';
            }
            $this->finish( $report, $format );
        }
        if ( empty( $options['skeleton'] ) )
            $this->usage( 'say what to generate: --skeleton writes an empty migration, --from-recording=<id> a migration of a recording, '
                        . '--from-content, --from-class, --from-role, --from-section or --from-settings one of what is there, '
                        . '--from-diff=<caption|address|dump> one of what changed since then' );
        $name = $this->argument( $options );
        if ( $name === '' )
            $this->usage( 'name the migration, e.g. ./console exp:migration:generate --skeleton add_news_class' );
        $skeleton = \expMigrationGenerator::skeleton( $name, $settings );
        $report = array( 'mode' => $apply ? 'generate' : 'generate-dry-run', 'generated' => $skeleton, 'errors' => $skeleton['errors'], 'warnings' => array(),
                         'exit' => $skeleton['ok'] ? \expUpgrade::EXIT_OK : \expUpgrade::EXIT_USAGE );
        if ( $skeleton['ok'] && $apply )
        {
            $errors = \expMigrationGenerator::write( $skeleton );
            if ( $errors )
            {
                $report['errors'] = $errors;
                $report['exit'] = \expUpgrade::EXIT_FAILED;
            }
        }
        unset( $report['generated']['errors'] );
        $this->finish( $report, $format );
    }

    /**
     * The value of an option, '' when it is not given.
     *
     * @param array $options
     * @param string $key
     * @return string
     */
    protected function value( array $options, $key )
    {
        return isset( $options[$key] ) && $options[$key] !== false && $options[$key] !== true ? trim( (string)$options[$key] ) : '';
    }

    /**
     * A comma-separated list. Pure.
     *
     * @param string $value
     * @return string[]
     */
    public static function listOf( $value )
    {
        return array_values( array_filter( array_map( 'trim', explode( ',', (string)$value ) ), 'strlen' ) );
    }

    protected function lines( array $report )
    {
        if ( strpos( $report['mode'], 'generate-from-' ) === 0 )
        {
            $dry = substr( $report['mode'], -8 ) === '-dry-run';
            $from = strpos( $report['mode'], 'generate-from-recording' ) === 0 ? 'a recording'
                  : ( strpos( $report['mode'], 'generate-from-diff' ) === 0 ? 'a difference' : 'what the installation has' );
            $lines = array( 'Exponential migrations: generate from ' . $from . ( $dry ? ', dry run (--apply writes the file)' : '' ) );
            if ( !empty( $report['recording'] ) )
                $lines[] = ExpmigrationrecordBase::recordingLine( $report['recording'] );
            if ( !empty( $report['source'] ) )
                $lines[] = 'Compared with the ' . $report['source']['kind'] . ' ' . $report['source']['name'];
            foreach ( isset( $report['diff'] ) ? (array)$report['diff'] : array() as $kind => $c )
                $lines[] = sprintf( '  %-10s %d added, %d changed, %d removed', $kind, $c['added'], $c['changed'], $c['removed'] );
            foreach ( $report['plan'] as $i => $line )
                $lines[] = sprintf( '  %3d  %s', $i + 1, $line );
            if ( !empty( $report['omitted'] ) )
                $lines[] = 'Left out on purpose: ' . implode( ', ', $report['omitted'] );
            if ( !empty( $report['manual'] ) )
            {
                $lines[] = 'Not generated, to be done by hand:';
                foreach ( $report['manual'] as $line )
                    $lines[] = '  - ' . $line;
            }
            if ( !empty( $report['missing_step_types'] ) )
                $lines[] = 'Step types missing: ' . implode( ', ', $report['missing_step_types'] );
            $g = isset( $report['generated'] ) ? $report['generated'] : null;
            if ( $g && $g['id'] !== '' )
            {
                $lines[] = ( !empty( $report['written'] ) ? 'Wrote ' : 'File ' ) . $g['path'];
                if ( $dry )
                {
                    $lines[] = '';
                    foreach ( explode( "\n", rtrim( $g['content'] ) ) as $line )
                        $lines[] = '    ' . $line;
                    $lines[] = '';
                }
                else if ( !empty( $report['written'] ) )
                    $lines[] = 'Next: ./console exp:migration:check and ./console exp:migration:dry-run';
            }
            return $lines;
        }
        $g = isset( $report['generated'] ) ? $report['generated'] : array( 'id' => '' );
        if ( $g['id'] === '' )
            return array( 'Exponential migrations: generate' );
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'generate' ? 'generate' : 'generate, dry run (--apply writes the file)' ),
                        ( $report['mode'] === 'generate' && !$report['errors'] ? 'Wrote ' : 'File ' ) . $g['path'],
                        'Migration ' . $g['id'] . ( $g['class'] !== '' ? ', class \\' . $g['namespace'] . '\\' . $g['class'] : '' ) );
        if ( $report['mode'] !== 'generate' )
        {
            $lines[] = '';
            foreach ( explode( "\n", rtrim( $g['content'] ) ) as $line )
                $lines[] = '    ' . $line;
            $lines[] = '';
        }
        else if ( !$report['errors'] )
            $lines[] = 'Next: ' . ( $g['class'] !== '' ? 'fill in plan() and up()' : 'write the steps' ) . ', then ./console exp:migration:check and ./console exp:migration:dry-run';
        return $lines;
    }
}

}
