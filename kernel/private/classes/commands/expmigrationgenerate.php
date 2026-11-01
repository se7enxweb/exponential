<?php
/**
 * @description Write a new migration file: --skeleton writes an empty PHP migration with its header, plan(), up() and down(), or with --type=yml an empty YAML migration; a dry run unless --apply
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
                             "\n" .
                             "./console exp:migration:generate --skeleton add_news_class\n" .
                             "./console exp:migration:generate --skeleton news_folder --type=yml --apply\n" .
                             "./console exp:migration:generate --skeleton add_news_class --description=\"The news class\" --apply\n" .
                             "./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[skeleton][source:][description:][author:][type:][apply][dry-run][format:]', '[name]', array(
            'skeleton' => 'An empty migration with its header: PHP with plan(), up() and down(), or YAML without steps',
            'source' => 'The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id)',
            'description' => 'The description in the header of the new migration',
            'author' => 'The author in the header of the new migration',
            'type' => 'The type of the new migration: php (the default) or yml',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        if ( empty( $options['skeleton'] ) )
            $this->usage( 'say what to generate: --skeleton writes an empty PHP migration (--type=yml: a YAML migration)' );
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
