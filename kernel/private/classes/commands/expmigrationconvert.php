<?php
/**
 * @description Convert YAML migrations of the migration bundle of the Symfony based platform into YAML migrations of Exponential: a file or every .yml file of a folder, step by step, what does not convert listed at the top of the new file; a dry run unless --apply
 * @alias exp:migration:convert
 *
 * File containing the exp:migration:convert command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationconvert extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:convert';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Convert migrations of the migration bundle of the Symfony based platform\n" .
                             "Reads a YAML migration of the bundle (a list of steps with type, mode, match, attributes and\n" .
                             "references), or every .yml and .yaml file of a folder, and writes a YAML migration of Exponential\n" .
                             "for each: content, content_type, content_type_group, location, role, section, user, user_group,\n" .
                             "language, tag, sql, reference and trash steps convert; \"reference:<name>\" becomes \${ref.<name>}.\n" .
                             "What does not convert (PHP and service calls, HTTP, mail, processes, loops, files, keys without a\n" .
                             "counterpart) is never guessed: it is listed at the top of the new file, to be done by hand. Every\n" .
                             "converted step is checked by its step type. The new file keeps the date and time of the old name and\n" .
                             "goes into the site's migrations directory or, with --source=extension:<name>, the extension's. Without\n" .
                             "--apply it shows the files and writes nothing; an existing file is never replaced.\n" .
                             "\n" .
                             "./console exp:migration:convert var/import/20240115093000_create_news_folder.yml\n" .
                             "./console exp:migration:convert var/import/MigrationVersions --apply\n" .
                             "./console exp:migration:convert var/import/MigrationVersions --source=extension:expnews --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][source:][author:]', '[file]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'source' => 'The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id)',
            'author' => 'The author in the header of the new migration',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $path = $this->argument( $options );
        if ( $path === '' )
            $this->usage( 'name the file or folder to convert, e.g. ./console exp:migration:convert var/import/MigrationVersions' );
        $report = \expMigrationConverter::run( array( 'path' => $path, 'apply' => $apply, 'source' => isset( $options['source'] ) ? (string)$options['source'] : '',
                                                      'author' => isset( $options['author'] ) ? (string)$options['author'] : '' ) );
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'convert' ? 'convert' : 'convert, dry run (--apply writes the files)' ) );
        foreach ( $report['files'] as $file )
        {
            $lines[] = $file['from'] . ( $file['path'] !== '' ? ' -> ' . $file['path'] . ( $file['written'] ? ' (written)' : '' ) : '' )
                     . ': ' . (int)$file['converted'] . ' step(s) converted, ' . (int)$file['skipped'] . ' left to do by hand';
            foreach ( $file['manual'] as $line )
                $lines[] = '    by hand: ' . $line;
            if ( !$file['written'] && $file['content'] !== '' && $report['mode'] !== 'convert' )
                foreach ( explode( "\n", rtrim( $file['content'] ) ) as $line )
                    $lines[] = '    | ' . $line;
        }
        $lines[] = 'Converted ' . (int)$report['converted'] . ' step(s), ' . (int)$report['skipped'] . ' to do by hand';
        return $lines;
    }
}

}
