<?php
/**
 * @description What the last static build of a target changed, whether its folder still matches the manifest, and whether every link of it works when served below a path
 * @alias exp:static:diff
 *
 * File containing the exp:static:diff command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticdiff extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:diff';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "What the last static build changed\n" .
                             "Compares the manifest of the target's folder with the one of the build before it: the files added,\n" .
                             "changed and removed. --verify compares the folder with its manifest (files missing, changed since the\n" .
                             "build, or not listed); --check-links checks every link and file as a browser would, served below each\n" .
                             "--mount. Changes nothing; exit 1 when --verify or --check-links finds a problem.\n" .
                             "\n" .
                             "./console exp:static:diff github-public\n" .
                             "./console exp:static:diff github-public --verify\n" .
                             "./console exp:static:diff github-public --check-links --mount=/ --mount=/my-site/",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][verify][check-links][mount:*]', '[target]', array(
            'format' => 'text (default) or json (the machine output exp-static/1)',
            'verify' => 'Compare the folder with its manifest: files missing, changed since the build, or not listed',
            'check-links' => 'Check every link and file of the folder as a browser would, served below each --mount',
            'mount' => 'The path the folder is served at for --check-links (default the target\'s BasePath; a comma list or repeated: /,/repo/)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $mounts = isset( $options['mount'] ) && $options['mount'] !== false ? self::listOption( $options['mount'] ) : array();
        foreach ( $mounts as $mount )
            if ( $mount[0] !== '/' )
                $this->usage( "--mount takes paths starting with /, not $mount" );
        if ( $mounts && empty( $options['check-links'] ) )
            $this->usage( '--mount goes with --check-links' );
        $target = $this->target( $options, $format );
        $ini = \eZINI::instance( 'staticcache.ini' );
        $workDir = $ini->hasVariable( 'StaticPublishSettings', 'WorkDir' ) ? (string)$ini->variable( 'StaticPublishSettings', 'WorkDir' ) : 'static-publish';
        $directory = $target->outputDirectory( \eZSys::varDirectory(), $workDir );
        $report = array( 'mode' => 'diff', 'target' => $target->id(), 'directory' => $directory, 'diff' => null, 'verify' => null, 'link_checks' => array(),
                         'errors' => array(), 'warnings' => array() );
        $manifest = \expStaticManifest::load( $directory );
        if ( $manifest === null )
        {
            $report['errors'][] = "$directory has no manifest: build the target first (exp:static:build {$target->id()}).";
            $this->finish( $report, $format );
            return;
        }
        $previous = \expStaticManifest::load( $directory, \expStaticManifest::PREVIOUS_FILE );
        $diff = \expStaticManifest::diff( $previous, $manifest );
        $meta = $manifest->meta();
        $report['built_at'] = isset( $meta['built_at'] ) ? $meta['built_at'] : null;
        $report['run_id'] = isset( $meta['run_id'] ) ? $meta['run_id'] : null;
        $report['diff'] = $diff + array( 'first_build' => $previous === null );

        if ( !empty( $options['verify'] ) )
        {
            $verify = self::verify( $directory, $manifest );
            $report['verify'] = $verify;
            foreach ( array( 'missing' => 'missing', 'changed' => 'changed since the build', 'unlisted' => 'not listed in the manifest' ) as $key => $what )
                if ( $verify[$key] )
                    $report[$key === 'unlisted' ? 'warnings' : 'errors'][] = sprintf( '%d file(s) %s', count( $verify[$key] ), $what );
        }
        if ( !empty( $options['check-links'] ) )
        {
            $builtFor = isset( $meta['base_path'] ) ? \expStaticLinkRewriter::normaliseBasePath( (string)$meta['base_path'] ) : $target->basePath();
            foreach ( $mounts ?: array( $target->basePath() ) as $mount )
            {
                // 404.html links below the base path it was built for, so it is checked below that path only
                $skip = \expStaticLinkRewriter::normaliseBasePath( $mount ) === $builtFor ? array() : array( '404.html' );
                $check = \expStaticLinkChecker::check( $directory, $mount, array( 'skip' => $skip ) );
                $report['link_checks'][] = $check;
                if ( $check['broken_count'] )
                    $report['errors'][] = sprintf( '%d broken link(s) when served below %s', $check['broken_count'], $check['mount'] );
            }
        }
        $this->finish( $report, $format );
    }

    /**
     * The folder against its manifest: files missing, changed since the build, not listed (outside .git and
     * .static-publish). Reads files only.
     *
     * @param string $directory
     * @param \expStaticManifest $manifest
     * @return array missing, changed, unlisted (paths)
     */
    public static function verify( $directory, \expStaticManifest $manifest )
    {
        $out = array( 'missing' => array(), 'changed' => array(), 'unlisted' => array() );
        foreach ( $manifest->entries() as $path => $entry )
        {
            $file = $directory . '/' . $path;
            if ( !is_file( $file ) )
                $out['missing'][] = $path;
            else if ( hash_file( 'sha256', $file ) !== $entry['sha256'] )
                $out['changed'][] = $path;
        }
        foreach ( \expStaticLinkChecker::files( $directory ) as $path )
            if ( !$manifest->has( $path ) )
                $out['unlisted'][] = $path;
        return $out;
    }

    protected function lines( array $report )
    {
        $lines = array( 'Static publishing: ' . $report['target'] . ' in ' . $report['directory'] );
        if ( empty( $report['diff'] ) )
            return $lines;
        $d = $report['diff'];
        $lines[] = sprintf( 'The build %s (%s) %s: %d added, %d changed, %d removed, %d unchanged', (string)$report['run_id'], (string)$report['built_at'],
                            $d['first_build'] ? 'is the first' : 'against the one before', count( $d['added'] ), count( $d['changed'] ), count( $d['removed'] ), $d['unchanged'] );
        foreach ( array( 'added' => '+', 'changed' => '~', 'removed' => '-' ) as $key => $mark )
        {
            foreach ( array_slice( $d[$key], 0, 50 ) as $path )
                $lines[] = "  $mark $path";
            if ( count( $d[$key] ) > 50 )
                $lines[] = sprintf( '  %s ... and %d more', $mark, count( $d[$key] ) - 50 );
        }
        if ( !empty( $report['verify'] ) )
            foreach ( $report['verify'] as $key => $paths )
                foreach ( array_slice( $paths, 0, 20 ) as $path )
                    $lines[] = "  $key $path";
        foreach ( $report['link_checks'] as $check )
        {
            $lines[] = sprintf( 'Link check below %s: %d links in %d files, %d broken%s', $check['mount'], $check['links'], $check['files'], $check['broken_count'],
                                $check['skipped'] ? ' (' . implode( ', ', $check['skipped'] ) . ' is built for another base path and was not read)' : '' );
            foreach ( array_slice( $check['broken'], 0, 20 ) as $b )
                $lines[] = '  broken ' . $b['link'] . ' in ' . $b['file'] . ' (' . $b['resolved'] . ')';
        }
        return $lines;
    }
}

}
