<?php
/**
 * @description Write a new migration file: --skeleton writes an empty PHP migration with its header, plan(), up() and down(), or with --type=yml an empty YAML migration; --from-recording turns a recording of changes into a YAML migration; a dry run unless --apply
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
                             "Extension\\<Name>\\Migrations. With --type=yml it writes YYYYMMDDHHMMSS_<name>.yml instead: the header\n" .
                             "and an empty list of steps. Without --apply it shows the file and writes nothing; an existing file is\n" .
                             "never replaced.\n" .
                             "--from-recording=<id> turns a recording of changes (exp:migration:record:start) into a YAML\n" .
                             "migration: its events folded into one step per thing changed. It needs the YAML step types the\n" .
                             "recording uses (content, location, class, role, user ...); until the installation has them it lists\n" .
                             "the steps and the step types missing, and writes nothing.\n" .
                             "\n" .
                             "./console exp:migration:generate --skeleton add_news_class\n" .
                             "./console exp:migration:generate --skeleton news_folder --type=yml --apply\n" .
                             "./console exp:migration:generate --skeleton add_news_class --description=\"The news class\" --apply\n" .
                             "./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply\n" .
                             "./console exp:migration:generate --from-recording=20261101-120000-a1b2c3 news_section",
        ) + self::SCRIPT );
        return (array)$this->startup( '[skeleton][source:][description:][author:][type:][apply][dry-run][format:][from-recording:]', '[name]', array(
            'skeleton' => 'An empty migration with its header: PHP with plan(), up() and down(), or YAML without steps',
            'source' => 'The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id)',
            'description' => 'The description in the header of the new migration',
            'author' => 'The author in the header of the new migration',
            'type' => 'The type of the new migration: php (the default) or yml',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'from-recording' => 'Generate a YAML migration from a recording of changes (its id); it needs the YAML step types the recording uses',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $fromRecording = isset( $options['from-recording'] ) && $options['from-recording'] !== false ? trim( (string)$options['from-recording'] ) : '';
        if ( $fromRecording !== '' )
        {
            if ( !empty( $options['skeleton'] ) )
                $this->usage( '--skeleton and --from-recording exclude each other' );
            if ( !\expMigrationRecording::validId( $fromRecording ) )
                $this->usage( "--from-recording takes a recording id (<date>-<time>-<6 hex digits>), not $fromRecording" );
            $db = $this->database( $format );
            $report = \expMigrationRecordingGenerator::fromRecording( $db, $fromRecording, array( 'name' => $this->argument( $options ) ) );
            $report['mode'] = $apply ? 'generate-from-recording' : 'generate-from-recording-dry-run';
            $this->finish( $report, $format );
        }
        if ( empty( $options['skeleton'] ) )
            $this->usage( 'say what to generate: --skeleton writes an empty PHP migration (--type=yml: a YAML migration), --from-recording=<id> a migration of a recording' );
        $name = $this->argument( $options );
        if ( $name === '' )
            $this->usage( 'name the migration, e.g. ./console exp:migration:generate --skeleton add_news_class' );
        $settings = array();
        foreach ( array( 'source', 'description', 'author', 'type' ) as $key )
            if ( isset( $options[$key] ) && $options[$key] !== false && $options[$key] !== '' )
                $settings[$key] = (string)$options[$key];
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

    protected function lines( array $report )
    {
        if ( strpos( $report['mode'], 'generate-from-recording' ) === 0 )
        {
            $lines = array( 'Exponential migrations: generate from a recording' );
            if ( !empty( $report['recording'] ) )
                $lines[] = ExpmigrationrecordBase::recordingLine( $report['recording'] );
            foreach ( $report['plan'] as $i => $line )
                $lines[] = sprintf( '  %3d  %s', $i + 1, $line );
            if ( !empty( $report['omitted'] ) )
                $lines[] = 'Left out on purpose: ' . implode( ', ', $report['omitted'] );
            if ( !empty( $report['missing_step_types'] ) )
                $lines[] = 'Step types missing: ' . implode( ', ', $report['missing_step_types'] );
            return $lines;
        }
        $g = $report['generated'];
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
