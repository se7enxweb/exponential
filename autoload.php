<?php
/**
 * Autoloader definition for eZ Publish
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
// The installation root, as an absolute path on disk.
//
// Kernel code that needs to find settings, extensions or var has always
// derived them from its own file's location. That works while every file is
// on disk and stops working the moment any of them is read out of an archive,
// because __DIR__ then names a path inside the archive. Publishing the root
// once, from the one file that is always on disk, gives that code something
// true to ask instead.
if ( !defined( 'EXP_ROOT_DIR' ) && strncmp( __FILE__, 'phar://', 7 ) !== 0 )
{
    define( 'EXP_ROOT_DIR', __DIR__ );
}

// An engine archive, if this installation is running from one.
//
// Resolved before anything else because the phar wrapper check below needs the
// answer: a runtime read through the wrapper must not unregister it. The
// environment variable is the switch -- set it and the kernel and library
// classes come from the archive, leave it unset and they come from disk, and
// nothing else about the installation differs either way. That is deliberate:
// the change has to be revertible by one variable, not by a reinstall.
if ( !defined( 'EXP_ENGINE_PHAR' ) )
{
    $ezpEnginePhar = getenv( 'EXP_ENGINE_PHAR' );
    if ( is_string( $ezpEnginePhar ) && $ezpEnginePhar !== '' && file_exists( $ezpEnginePhar ) )
    {
        define( 'EXP_ENGINE_PHAR', $ezpEnginePhar );
    }
    unset( $ezpEnginePhar );
}

// Disable the PHAR stream wrapper as it is insecure.
//
// The vector this was written for in 2018 -- phar metadata being unserialized
// by an ordinary file operation, turning any influenced path into object
// instantiation -- is closed. PHP 8 no longer deserializes metadata that way;
// measured on 8.5.10, a file_exists() on a phar:// path instantiates nothing.
//
// The line is not obsolete, because the wrapper also does something PHP 8 did
// not change: it lets any path-taking function reach *inside* an archive. A
// file that is a valid image and a valid phar at the same time is trivial to
// produce, and this application accepts image uploads by design. Measured both
// ways on this installation: with the wrapper registered, include() on
// phar://<uploaded>.jpg/payload.php executes the PHP inside the image; with it
// unregistered, the same call reaches nothing at all.
//
// What was wrong was the condition. PHP_SAPI !== 'cli' stood in for "is this a
// web request", and a persistent-worker server answers that wrongly: it serves
// public traffic under the CLI SAPI. So this protection was absent on exactly
// the requests it exists for, and present only on the CLI, where an attacker
// has no path in. The SAPI is not consulted any more.
//
// One case must keep the wrapper: a runtime that is itself read through it.
// An engine packaged as a phar, or a tool such as phpunit.phar as the entry
// point, unloads itself mid-run if the wrapper goes. Detected by looking for a
// phar:// path among the files already included, which covers this file being
// in one, the entry point being one, and an engine phar that included a copy
// of this file from disk. EXP_ENGINE_PHAR is the explicit hook for the last
// case, set by a packaged engine's bootstrap before it reaches here.
//
// The answer cannot change within one process, so it is decided once. That
// matters under a persistent worker, where this file is re-entered on every
// request and the included-file list grows with the worker's life.
if ( !defined( 'EXP_RUNTIME_IS_PHAR' ) )
{
    $ezpRuntimeIsPhar = strncmp( __FILE__, 'phar://', 7 ) === 0
                     || defined( 'EXP_ENGINE_PHAR' );

    if ( !$ezpRuntimeIsPhar )
    {
        foreach ( get_included_files() as $ezpIncludedFile )
        {
            if ( strncmp( $ezpIncludedFile, 'phar://', 7 ) === 0 )
            {
                $ezpRuntimeIsPhar = true;
                break;
            }
        }
    }

    define( 'EXP_RUNTIME_IS_PHAR', $ezpRuntimeIsPhar );
    unset( $ezpRuntimeIsPhar, $ezpIncludedFile );
}

if ( !EXP_RUNTIME_IS_PHAR && in_array( 'phar', stream_get_wrappers() ) )
{
    stream_wrapper_unregister( 'phar' );
}

// config.php can set the components path like:
// ini_set( 'include_path', ini_get( 'include_path' ). ':../ezcomponents/trunk' );
// It is also possible to push a custom autoload method to the autoload
// function stack. Remember to check for class prefixes in such a method, if it
// will not serve classes from eZ Publish and eZ Components

if ( file_exists( __DIR__ . '/config.php' ) )
{
    require_once __DIR__ . '/config.php';
}

// Check for EZCBASE_ENABLED, if set we can skip autoloading Zeta Components
if ( !defined( 'EZCBASE_ENABLED' ) )
{
    // Start by setting EZCBASE_ENABLED to avoid recursion
    define( 'EZCBASE_ENABLED', false );

    // If composer autoloader is already present we can skip trying to load it
    if ( class_exists( 'Composer\Autoload\ClassLoader', false ) )
    {
        // do nothing
    }
    // Composer if in eZ Platform context
    else if ( file_exists( __DIR__ . "/../vendor/autoload.php" ) )
    {
        require_once __DIR__ . "/../vendor/autoload.php";
    }
    // Composer if in eZ Publish legacy context
    else if ( file_exists( __DIR__ . "/vendor/autoload.php" ) )
    {
        require_once __DIR__ . "/vendor/autoload.php";
    }
}

// Check if ezpAutoloader exists because it can be already declared if running in the Symfony context (e.g. CLI scripts)
if ( !class_exists( 'ezpAutoloader', false ) )
{
    /**
     * Provides the native autoload functionality for eZ Publish
     *
     * @package kernel
     */
    class ezpAutoloader
    {
        protected static $ezpClasses = null;

        /** null = not looked at yet, false = no archive, array = what it holds */
        protected static $ezpPharFiles = null;

        public static function autoload( $className )
        {
            if ( self::$ezpClasses === null )
            {
                $ezpKernelClasses = require __DIR__ . '/autoload/ezp_kernel.php';
                $ezpExtensionClasses = false;
                $ezpTestClasses = false;

                if ( file_exists( __DIR__ . '/var/autoload/ezp_extension.php' ) )
                {
                    $ezpExtensionClasses = require __DIR__ . '/var/autoload/ezp_extension.php';
                }

                if ( file_exists( __DIR__ . '/var/autoload/ezp_tests.php' ) )
                {
                    $ezpTestClasses = require __DIR__ . '/var/autoload/ezp_tests.php';
                }

                if ( $ezpExtensionClasses and $ezpTestClasses )
                {
                    self::$ezpClasses = $ezpTestClasses + $ezpExtensionClasses + $ezpKernelClasses;
                }
                else if ( $ezpExtensionClasses )
                {
                    self::$ezpClasses = $ezpExtensionClasses + $ezpKernelClasses;
                }
                else if ( $ezpTestClasses )
                {
                    self::$ezpClasses = $ezpTestClasses + $ezpKernelClasses;
                }
                else
                {
                    self::$ezpClasses = $ezpKernelClasses;
                }

                if ( defined( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE' ) and EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE )
                {
                    // won't work, as eZDebug isn't initialized yet at that time
                    // eZDebug::writeError( "Kernel override is enabled, but var/autoload/ezp_override.php has not been generated\nUse bin/php/ezpgenerateautoloads.php -o", 'autoload.php' );
                    $ezpKernelOverridePath = __DIR__ . '/var/autoload/ezp_override.php';
                    if ( file_exists( $ezpKernelOverridePath ) && $ezpKernelOverrideClasses = include $ezpKernelOverridePath )
                    {
                        self::$ezpClasses = array_merge( self::$ezpClasses, $ezpKernelOverrideClasses );
                    }
                }
            }

            if ( isset( self::$ezpClasses[$className] ) )
            {
                $ezpRelativePath = self::$ezpClasses[$className];

                // Read the archive's own list of what it holds, once. Asking
                // the archive per class would be a stat per class, which is
                // the cost this is meant to remove rather than add.
                if ( self::$ezpPharFiles === null )
                {
                    self::$ezpPharFiles = false;
                    if ( defined( 'EXP_ENGINE_PHAR' ) )
                    {
                        $ezpManifest = 'phar://' . EXP_ENGINE_PHAR . '/MANIFEST.php';
                        if ( file_exists( $ezpManifest ) )
                        {
                            self::$ezpPharFiles = require $ezpManifest;
                        }
                    }
                }

                // Only what the archive actually carries comes from it. A file
                // added to the kernel after the archive was built is still on
                // disk and is still found, so a stale archive degrades to the
                // old behaviour for that class instead of failing.
                if ( self::$ezpPharFiles !== false && isset( self::$ezpPharFiles[$ezpRelativePath] ) )
                {
                    require( 'phar://' . EXP_ENGINE_PHAR . '/' . $ezpRelativePath );
                }
                else
                {
                    require( __DIR__ . "/" . $ezpRelativePath );
                }
            }
        }

        /**
         * Resets the local, in-memory autoload cache.
         *
         * If the autoload arrays are extended during a requests lifetime, this
         * method must be called, to make them available.
         *
         * @return void
         */
        public static function reset()
        {
            self::$ezpClasses = null;
            self::$ezpPharFiles = null;
        }

        public static function updateExtensionAutoloadArray()
        {
            $autoloadGenerator = new eZAutoloadGenerator();
            try
            {
                $autoloadGenerator->buildAutoloadArrays();

                self::reset();
            }
            catch ( Exception $e )
            {
                echo $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            }
        }
    }

    spl_autoload_register( array( 'ezpAutoloader', 'autoload' ) );
}

if ( EZCBASE_ENABLED )
{
    spl_autoload_register( array( 'ezcBase', 'autoload' ) );
}

?>
