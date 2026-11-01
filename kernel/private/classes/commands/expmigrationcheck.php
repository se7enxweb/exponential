<?php
/**
 * @description Check migration files without running them: names, headers, order, PHP syntax and classes (the lint for CI)
 * @alias exp:migration:check
 *
 * File containing the exp:migration:check command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationcheck extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:check';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Check migration files\n" .
                             "The lint for CI, without a database: every migration of the migration directories (or of --path)\n" .
                             "is checked for a valid file name, a header of known keys, requirements that exist and no cycle,\n" .
                             "active extensions it requires, and, for a PHP migration, its syntax and exactly one class that\n" .
                             "extends expMigrationPhp and loads; for a YAML migration, its YAML and every step against the\n" .
                             "schema of its step type, with its conditions, loops and references. Nothing of a migration is run.\n" .
                             "Exit 1 when something is wrong.\n" .
                             "\n" .
                             "./console exp:migration:check\n" .
                             "./console exp:migration:check --path=extension/expnews/migrations --format=json\n" .
                             "./console exp:migration:check --environment=production",
        ) + self::SCRIPT );
        return (array)$this->startup( '[path:*][environment:][format:]', '', array(
            'path' => 'Migration files or folders, relative to the root or absolute (a comma list or repeated): migrate and dry-run take only the migrations in them, check checks them instead of the migration directories',
            'environment' => 'The environment (default the installation\'s: [AdminEnvironment] Level, or EXP_ENV with Level=auto; check: every environment); only the migrations for it count',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $selection = $this->selection( $options );
        $this->finish( \expMigrationCheck::run( array(
            'paths' => isset( $selection['path'] ) ? $selection['path'] : array(),
            'environment' => isset( $selection['environment'] ) ? $selection['environment'] : null,
        ) ), $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: check' );
        foreach ( $report['directories'] as $directory )
            $lines[] = sprintf( '  %-24s %s%s', $directory['source'], $directory['path'], $directory['exists'] ? '' : ' (none)' );
        if ( $report['migrations'] )
            $lines[] = '';
        foreach ( $report['migrations'] as $m )
        {
            $lines[] = sprintf( '  %-5s %-44s %-4s %s', $m['ok'] ? 'ok' : 'FAIL', $m['id'], $m['type'], implode( ', ', $m['files'] ) );
            foreach ( $m['errors'] as $error )
                $lines[] = '        ' . $error;
        }
        $lines[] = '';
        $lines[] = $report['checked'] . ' migration(s) checked';
        return $lines;
    }
}

}
