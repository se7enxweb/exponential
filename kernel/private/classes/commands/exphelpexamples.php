<?php
/**
 * @description Generate the reference examples of the help below doc/examples/ from the code: template operators and functions, fetch functions, module views, policies, INI settings, datatypes, workflow events, cronjobs, console commands and extension points
 * @alias exp:help:examples
 *
 * File containing the exp:help:examples command (doc/examples/README.md, "How the generated pages are made").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exphelpexamples extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "The generated reference examples of the help (doc/examples/)\n" .
                                 "\n" .
                                 "./console exp:help:examples                      write the pages of every area (only changed files are written)\n" .
                                 "./console exp:help:examples --area=datatypes     only this area (repeat with a comma: --area=datatypes,cronjobs)\n" .
                                 "./console exp:help:examples --check              compare only: exit 1 when a page is missing, different or stale\n" .
                                 "./console exp:help:examples --list               the areas and how many pages each has\n" .
                                 "\n" .
                                 "The pages are read from the git-tracked sources of this installation (the template autoload files,\n" .
                                 "function_definition.php, module.php, operation_definition.php, the shipped INI files, the datatype and\n" .
                                 "workflow event classes, cronjob.ini and the command classes). The same sources always give the same pages.\n" .
                                 "Only pages marked \"generated: exp:help:examples\" are replaced or removed; nothing else below doc/ is\n" .
                                 "touched. Run \"./console exp:help index\" afterwards to rebuild the help index.\n" .
                                 "Exit code: 0 fine, 1 --check found differences, 2 error.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => false
            )
        );
        $options = $this->startup( '[area:][check][list][out:]', '', array(
            'area'  => 'Only these areas, comma separated (template-operators, template-functions, fetch-functions, module-views, policies, ini-settings, datatypes, workflow-events, cronjobs, commands, extension-points)',
            'check' => 'Compare the generated pages with the files and change nothing; exit 1 on a difference',
            'list'  => 'List the areas with their page counts',
            'out'   => 'The output directory relative to the installation root (default doc/examples)',
        ) );

        $root = defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : getcwd();
        $out = !empty( $options['out'] ) ? trim( (string)$options['out'], '/' ) : 'doc/examples';
        if ( strpos( $out, '..' ) !== false || $out === '' || $out[0] === '/' )
        {
            $cli->error( "--out must be a directory below the installation root" );
            $this->shutdown( 2 );
        }
        $areas = null;
        if ( !empty( $options['area'] ) )
        {
            $areas = array_values( array_filter( array_map( 'trim', explode( ',', (string)$options['area'] ) ), 'strlen' ) );
            foreach ( $areas as $area )
                if ( !isset( \expHelpExamplesGenerator::AREAS[$area] ) )
                {
                    $cli->error( "Unknown area \"$area\": " . implode( ', ', array_keys( \expHelpExamplesGenerator::AREAS ) ) );
                    $this->shutdown( 2 );
                }
        }

        $start = microtime( true );
        $generator = new \expHelpExamplesGenerator( $root, $out );
        $pages = $generator->build( $areas );
        $counts = $generator->counts();
        $seconds = round( microtime( true ) - $start, 2 );

        foreach ( $generator->warnings() as $warning )
            $cli->warning( $warning );

        if ( !empty( $options['list'] ) )
        {
            foreach ( $counts as $area => $count )
                $cli->output( sprintf( '%-20s %5d items  %s', $area, $count, \expHelpExamplesGenerator::AREAS[$area] ) );
            $cli->output( count( $pages ) . " pages in all (with the index pages), built in {$seconds} s" );
            $this->shutdown( 0 );
        }

        if ( !empty( $options['check'] ) )
        {
            $diff = $generator->compare( $pages, $areas );
            $problems = count( $diff['missing'] ) + count( $diff['different'] ) + count( $diff['stale'] );
            foreach ( array( 'missing', 'different', 'stale' ) as $kind )
                foreach ( array_slice( $diff[$kind], 0, 20 ) as $path )
                    $cli->output( "$kind: $path" );
            $cli->output( count( $pages ) . " pages generated in {$seconds} s; " . count( $diff['missing'] ) . ' missing, ' . count( $diff['different'] ) . ' different, ' . count( $diff['stale'] ) . ' stale' );
            $this->shutdown( $problems > 0 ? 1 : 0 );
        }

        $stats = $generator->write( $pages, $areas );
        foreach ( $counts as $area => $count )
            $cli->output( sprintf( '%-20s %5d items', $area, $count ) );
        $cli->output( count( $pages ) . " pages in {$seconds} s: {$stats['written']} written, {$stats['unchanged']} unchanged, {$stats['removed']} removed" );
        $this->shutdown( 0 );
    }
}

}
