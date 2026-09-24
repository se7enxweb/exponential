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
    /** How long a computed version string is reused, in seconds. */
    const VERSION_CACHE_SECONDS = 60;

    public static function version()
    {
        $root = self::root();

        // Remembered for a minute, because working out the "dirty" part costs
        // a full `git status --porcelain` and this tree has thousands of
        // untracked files. Measured here: 280ms per call.
        //
        // The Setup > System information view asks for this on every view, and
        // it was most of that page -- 0.62s inside the module against 0.08s of
        // templates and 0.009s of database for the same request. A version
        // string on a diagnostic panel does not need to be accurate to the
        // second, and a stale "dirty" flag for under a minute is a far smaller
        // problem than a page that takes half a second longer to open.
        static $memory = null;
        $now = time();
        if ( is_array( $memory )
             and $memory['root'] === $root
             and $memory['at'] > $now - self::VERSION_CACHE_SECONDS )
        {
            return $memory['version'];
        }

        $cacheFile = eZSys::cacheDirectory() . '/exp/engine-repo-version.php';
        if ( file_exists( $cacheFile ) )
        {
            $cached = @include( $cacheFile );
            if ( is_array( $cached )
                 and isset( $cached['root'], $cached['version'], $cached['at'] )
                 and $cached['root'] === $root
                 and $cached['at'] > $now - self::VERSION_CACHE_SECONDS )
            {
                $memory = $cached;
                return $cached['version'];
            }
        }

        $sha = trim( (string)@shell_exec( 'git -C ' . escapeshellarg( $root ) . ' rev-parse --short HEAD 2>/dev/null' ) );
        $dirty = trim( (string)@shell_exec( 'git -C ' . escapeshellarg( $root ) . ' status --porcelain 2>/dev/null' ) );

        $base = 'unknown';
        if ( class_exists( 'eZPublishSDK' ) )
            $base = eZPublishSDK::version();

        $parts = array( $base );
        if ( $sha !== '' ) $parts[] = $sha;
        if ( $dirty !== '' ) $parts[] = 'dirty';

        $version = implode( '-', $parts );

        $memory = array( 'root' => $root, 'version' => $version, 'at' => $now );
        $directory = dirname( $cacheFile );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        @file_put_contents( $cacheFile,
            "<?php\nreturn " . var_export( $memory, true ) . ";\n" );

        return $version;
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
            // The PHP running this, not whatever "php" the PATH finds: with no
            // php on the PATH (a minimal container, a service manager's
            // environment) every file "failed to parse", and the build refused.
            @exec( escapeshellarg( PHP_BINARY ) . ' -l ' . escapeshellarg( $root . '/' . $rel ) . ' 2>&1', $out, $code );
            if ( $code !== 0 )
                $bad[] = $rel;
        }
        if ( $bad )
        {
            return self::fail(
                count( $bad ) . ' file(s) do not parse, nothing written',
                array( 'unparsable' => array_slice( $bad, 0, 20 ) ) );
        }

        // Built under a temporary name beside the archive and renamed over it
        // when complete, so there is always a whole archive at $output. This
        // used to delete the archive first and write it in place: every build
        // left a window with none, and two builds at once -- a restart
        // rebuilding a stale archive while someone ran the build by hand --
        // left nothing at all, and the server would not start
        // ("EnginePhar names an archive that does not exist", 2026-09-24).
        // The name must end in .phar for Phar to write it.
        $final = $output;
        $output = dirname( $final ) . '/.' . basename( $final, '.phar' ) . '-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) ) . '.tmp.phar';

        $result = self::withPharWrapper( function () use ( $output, $root, $files ) {
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
            'built engine.phar',
            array(
                'version' => $version,
                'files'   => count( $files ),
                'bytes'   => filesize( $output ),
            ) );
        } );

        if ( empty( $result['ok'] ) || !is_file( $output ) || !@rename( $output, $final ) )
        {
            @unlink( $output );
            return empty( $result['ok'] ) ? $result : self::fail( "could not move the new archive into place at $final" );
        }
        clearstatcache( true, $final );
        $result['data'] = array( 'path' => $final ) + (array)( $result['data'] ?? array() );
        return $result;
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
    /**
     * The version string inside the engine archive, remembered between requests.
     *
     * Reading it means opening the archive, and the archive is 18MB: PHP has to
     * take in and verify its manifest before a single byte of a file inside it
     * can be read. Measured on this installation, 290-412ms per call.
     *
     * The Setup > System information view calls info() on every view, so that
     * was most of a 663ms page -- against 81ms of templates and 9ms of database
     * for the same request -- and all of it to print one line saying which
     * version the archive holds.
     *
     * The answer only changes when the archive is rebuilt, so it is cached
     * against the archive's own size and modification time. A rebuild changes
     * both and the next call reads through again; nothing has to remember to
     * clear anything.
     *
     * @param string $path
     * @param int $bytes
     * @param int $mtime
     * @return string
     */
    protected static function archiveVersion( $path, $bytes, $mtime )
    {
        static $memory = array();

        $stamp = $bytes . '-' . $mtime;
        if ( isset( $memory[$path] ) and $memory[$path]['stamp'] === $stamp )
            return $memory[$path]['version'];

        $cacheFile = eZSys::cacheDirectory() . '/exp/engine-phar-version.php';

        if ( file_exists( $cacheFile ) )
        {
            $cached = @include( $cacheFile );
            if ( is_array( $cached )
                 and isset( $cached['stamp'], $cached['version'], $cached['path'] )
                 and $cached['path'] === $path
                 and $cached['stamp'] === $stamp )
            {
                $memory[$path] = $cached;
                return $cached['version'];
            }
        }

        $v = self::withPharWrapper( function () use ( $path ) {
            return @file_get_contents( 'phar://' . $path . '/ENGINE_VERSION' );
        } );
        $version = $v === false ? '(unreadable)' : trim( $v );

        $entry = array( 'path' => $path, 'stamp' => $stamp, 'version' => $version );
        $memory[$path] = $entry;

        $directory = dirname( $cacheFile );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        @file_put_contents( $cacheFile,
            "<?php\nreturn " . var_export( $entry, true ) . ";\n" );

        return $version;
    }

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
            $data['version'] = self::archiveVersion( $path, $data['bytes'], filemtime( $path ) );
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
