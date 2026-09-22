<?php
/**
 * File containing the expPhar class.
 *
 * Packages the engine -- the kernel and library class files -- into a phar,
 * so the runtime can load them from one archive instead of from a thousand
 * separate files.
 *
 * Nothing leaves the disk. The phar is an addition, not a replacement: the
 * same files stay where they are, and design, extension, settings and var are
 * untouched and still read from disk exactly as before. The only thing that
 * changes is where the autoloader reads a class from, which is what makes the
 * change safe to try and easy to undo -- delete the phar, or unset one
 * environment variable, and the installation is back to what it was.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 * @package   kernel
 */

class expPhar
{
    /** Directories packaged into the engine phar, relative to the root. */
    const ENGINE_DIRS = 'kernel,lib,autoload';

    /** Where built artifacts land. Gitignored; nothing here is source. */
    const DIST_DIR = 'dist';

    /**
     * Make the phar wrapper available for the duration of one call.
     *
     * The bootstrap unregisters the wrapper on every request and every command,
     * deliberately: it is what lets a path reach inside an uploaded file. This
     * class is the one place that legitimately needs it back, because Phar
     * extends RecursiveDirectoryIterator and cannot even be constructed without
     * it.
     *
     * So it is restored around the work and put back exactly as it was found.
     * Narrow and visible, rather than an exception written into the bootstrap
     * that every other caller would inherit too.
     *
     * @param callable $work
     * @return mixed whatever $work returns
     */
    protected static function withPharWrapper( $work )
    {
        $had = in_array( 'phar', stream_get_wrappers() );
        if ( !$had )
            stream_wrapper_restore( 'phar' );

        try
        {
            return call_user_func( $work );
        }
        finally
        {
            if ( !$had && in_array( 'phar', stream_get_wrappers() ) )
                stream_wrapper_unregister( 'phar' );
        }
    }

    /**
     * Root of the installation this class was loaded from.
     */
    public static function root()
    {
        return realpath( dirname( dirname( __DIR__ ) ) );
    }

    /**
     * A version string for the artifact: what the repository says, plus the
     * commit, plus a dirty marker when the tree does not match the commit.
     *
     * The marker matters more than it looks. A build is allowed from a dirty
     * tree -- refusing would make iterating painful -- so the name is what
     * stops the artifact claiming to be a commit it is not.
     */
    public static function version()
    {
        $root = self::root();
        $sha = trim( (string)@shell_exec( 'git -C ' . escapeshellarg( $root ) . ' rev-parse --short HEAD 2>/dev/null' ) );
        $dirty = trim( (string)@shell_exec( 'git -C ' . escapeshellarg( $root ) . ' status --porcelain 2>/dev/null' ) );

        $base = 'unknown';
        if ( class_exists( 'eZPublishSDK' ) )
            $base = eZPublishSDK::version();

        $parts = array( $base );
        if ( $sha !== '' ) $parts[] = $sha;
        if ( $dirty !== '' ) $parts[] = 'dirty';

        return implode( '-', $parts );
    }

    /**
     * Absolute path of the engine phar this installation would use, whether
     * or not it exists yet.
     */
    public static function enginePath()
    {
        return self::root() . '/' . self::DIST_DIR . '/engine.phar';
    }

    /**
     * Every PHP file that would go into the archive, as paths relative to the
     * root, and the non-PHP files alongside them.
     */
    public static function collect()
    {
        $root = self::root();
        $files = array();

        foreach ( explode( ',', self::ENGINE_DIRS ) as $dir )
        {
            $full = $root . '/' . $dir;
            if ( !is_dir( $full ) )
                continue;

            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $full, FilesystemIterator::SKIP_DOTS )
            );
            foreach ( $it as $file )
            {
                if ( !$file->isFile() )
                    continue;
                $files[] = substr( $file->getPathname(), strlen( $root ) + 1 );
            }
        }

        sort( $files );
        return $files;
    }

    /**
     * Build the engine phar.
     *
     * Every PHP file is parsed before anything is written. A packaged file
     * that does not compile is worse than an unpackaged one: it fails at the
     * point of use, inside an archive, where the error names a phar:// path
     * nobody can open in an editor.
     *
     * @param array $options 'output' => path, 'quiet' => bool
     * @return array ok / message / data
     */
    public static function build( array $options = array() )
    {
        if ( ini_get( 'phar.readonly' ) )
        {
            return self::fail(
                'phar.readonly is on, so nothing can be written. Re-run with: '
                . 'php -d phar.readonly=0 bin/php/console exp:phar build' );
        }

        $root = self::root();
        $output = isset( $options['output'] ) && $options['output'] !== ''
                ? $options['output'] : self::enginePath();

        $distDir = dirname( $output );
        if ( !is_dir( $distDir ) && !@mkdir( $distDir, 0755, true ) )
            return self::fail( "could not create $distDir" );

        $files = self::collect();
        if ( !$files )
            return self::fail( 'nothing to package: no engine directories found' );

        // Parse first, write second.
        $bad = array();
        foreach ( $files as $rel )
        {
            if ( substr( $rel, -4 ) !== '.php' )
                continue;
            $out = array(); $code = 0;
            @exec( 'php -l ' . escapeshellarg( $root . '/' . $rel ) . ' 2>&1', $out, $code );
            if ( $code !== 0 )
                $bad[] = $rel;
        }
        if ( $bad )
        {
            return self::fail(
                count( $bad ) . ' file(s) do not parse, nothing written',
                array( 'unparsable' => array_slice( $bad, 0, 20 ) ) );
        }

        @unlink( $output );

        return self::withPharWrapper( function () use ( $output, $root, $files ) {
        $phar = new Phar( $output, 0, 'engine.phar' );
        $phar->startBuffering();

        foreach ( $files as $rel )
            $phar->addFile( $root . '/' . $rel, $rel );

        $version = self::version();
        $phar->addFromString( 'ENGINE_VERSION', $version . "\n" );

        // The autoloader has to decide, for every class it resolves, whether
        // that file is in here or on disk. Asking the archive per class is a
        // stat per class; reading one generated list once is not. The keys are
        // the paths exactly as the class map holds them, so the lookup is a
        // single isset().
        $set = array();
        foreach ( $files as $rel )
            $set[$rel] = true;
        $phar->addFromString( 'MANIFEST.php', "<?php return " . var_export( $set, true ) . ";\n" );

        // The archive is only ever read through the autoloader, never executed,
        // so the stub exists to satisfy the format and to say so if anyone runs
        // it by hand.
        $phar->setStub(
            "<?php\n"
            . "// Exponential engine archive. Read by the autoloader; not a program.\n"
            . "Phar::mapPhar('engine.phar');\n"
            . "if (PHP_SAPI === 'cli') { fwrite(STDERR, \"engine.phar $version\\n\"); }\n"
            . "__HALT_COMPILER();\n" );

        $phar->stopBuffering();
        unset( $phar );

        return self::ok(
            'built ' . basename( $output ),
            array(
                'path'    => $output,
                'version' => $version,
                'files'   => count( $files ),
                'bytes'   => filesize( $output ),
            ) );
        } );
    }

    /**
     * Remove built artifacts.
     */
    public static function clean()
    {
        $path = self::enginePath();
        if ( !file_exists( $path ) )
            return self::ok( 'nothing to remove' );
        if ( !@unlink( $path ) )
            return self::fail( "could not remove $path" );
        return self::ok( 'removed ' . basename( $path ) );
    }

    /**
     * What is on disk, and whether the runtime is using it.
     */
    public static function info()
    {
        $path = self::enginePath();
        $exists = file_exists( $path );

        $data = array(
            'path'      => $path,
            'exists'    => $exists,
            'bytes'     => $exists ? filesize( $path ) : 0,
            'built'     => $exists ? date( 'Y-m-d H:i:s', filemtime( $path ) ) : '',
            'version'   => '',
            'in_use'    => defined( 'EXP_ENGINE_PHAR' ),
            'repo'      => self::version(),
        );

        if ( $exists )
        {
            $v = self::withPharWrapper( function () use ( $path ) {
                return @file_get_contents( 'phar://' . $path . '/ENGINE_VERSION' );
            } );
            $data['version'] = $v === false ? '(unreadable)' : trim( $v );
        }

        return self::ok( $exists ? 'engine phar present' : 'no engine phar built', $data );
    }

    protected static function ok( $message, array $data = array() )
    {
        return array( 'ok' => true, 'message' => $message, 'data' => $data );
    }

    protected static function fail( $message, array $data = array() )
    {
        return array( 'ok' => false, 'message' => $message, 'data' => $data );
    }
}
