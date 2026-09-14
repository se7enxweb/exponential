<?php
/**
 * Loading every class this installation declares, and saying which php refuses.
 *
 * A class that cannot be loaded is not a quiet problem: the fatal takes the
 * whole request with it, so the page somebody was looking at is gone rather
 * than merely wrong. It is also invisible until something happens to touch
 * that class, which can be months.
 *
 * The loading is done in child processes, which is the only way to survive
 * finding one. bin/php/checkclasses.php is the command that drives this from a
 * terminal, and is also the child: it is invoked with --load to read names from
 * stdin, and with --why to report on one class without the kernel's error
 * handler printing over what php was going to say.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The parts of the check worth having in one place.
 */
class eZCheckClasses
{
    /**
     * The script that is both the command and the child process.
     *
     * Held here rather than taken from __FILE__, because __FILE__ is this
     * library and the thing that has to be run is the command beside it.
     */
    const COMMAND = 'bin/php/checkclasses.php';

    /**
     * The value of a --name=value option, or false.
     *
     * Read out of $argv directly because this runs before eZScript does, for
     * the child modes that must not have the kernel's error handler in the way.
     *
     * @param array $argv
     * @param string $name
     * @return string|false
     */
    public static function optionValue( array $argv, $name )
    {
        foreach ( $argv as $argument )
            if ( strpos( $argument, $name . '=' ) === 0 )
                return substr( $argument, strlen( $name ) + 1 );

        return false;
    }

    /**
     * Every class name this installation declares.
     *
     * @param bool $withKernel
     * @return array of string
     */
    public static function classNames( $withKernel = false, $withTests = false )
    {
        $maps = array( 'var/autoload/ezp_extension.php', 'var/autoload/ezp_override.php' );

        if ( $withKernel )
            $maps[] = 'autoload/ezp_kernel.php';

        $names = array();

        foreach ( $maps as $map )
        {
            if ( !is_file( $map ) )
                continue;

            $loaded = include $map;

            if ( !is_array( $loaded ) )
                continue;

            foreach ( $loaded as $name => $path )
            {
                // A test class that will not load breaks nothing anybody is
                // looking at, and there are enough of them written against an
                // old phpunit to bury the faults that matter.
                if ( !$withTests && self::isTest( (string) $path ) )
                    continue;

                $names[(string) $name] = true;
            }
        }

        return array_keys( $names );
    }

    /**
     * Whether a path is a test rather than something the site runs.
     *
     * @param string $path
     * @return bool
     */
    public static function isTest( $path )
    {
        return preg_match( '#(^|/)tests?(/|$)#i', $path ) === 1;
    }

    /**
     * Load them all, and say which ones php refused.
     *
     * One child process for the lot, and one more each time a class kills it.
     * A run with nothing wrong is one process; a run with ten faults is eleven.
     *
     * @param array $classes
     * @param int $limit how many faults to find before giving up, so that a
     *        thoroughly broken installation cannot spawn processes for ever
     * @return array with keys checked and bad
     */
    public static function check( array $classes, $limit = 200 )
    {
        $todo    = array_values( $classes );
        $bad     = array();
        $checked = 0;

        while ( count( $todo ) && count( $bad ) < $limit )
        {
            $batch = self::tempFile( 'list' );
            file_put_contents( $batch, implode( "\n", $todo ) );

            $output = shell_exec( self::php() . ' ' . escapeshellarg( self::COMMAND )
                                . ' --load --allow-root-user < ' . escapeshellarg( $batch ) . ' 2>/dev/null' );

            @unlink( $batch );

            $lastTried = null;
            $finished  = false;

            foreach ( explode( "\n", (string) $output ) as $line )
            {
                if ( strpos( $line, 'OK:' ) === 0 )
                    $checked++;
                else if ( strpos( $line, 'TRY:' ) === 0 )
                    $lastTried = substr( $line, 4 );
                else if ( $line === 'DONE' )
                    $finished = true;
            }

            if ( $finished || $lastTried === null )
                break;

            // The last name it announced is the one that killed it. Carry on
            // from after it rather than stopping here.
            $bad[] = $lastTried;

            $at   = array_search( $lastTried, $todo, true );
            $todo = $at === false ? array() : array_slice( $todo, $at + 1 );
        }

        return array( 'checked' => $checked, 'bad' => $bad );
    }

    /**
     * Why one class cannot be loaded, in php's own words.
     *
     * @param string $class
     * @return array with keys kind and message
     */
    public static function reason( $class )
    {
        $output = shell_exec( self::php() . ' -d display_errors=1 ' . escapeshellarg( self::COMMAND )
                            . ' --why=' . escapeshellarg( $class ) . ' 2>&1' );

        $message = '';

        if ( preg_match( '/(?:Fatal error|Uncaught Error|Error|Warning):\s*(.+)/', (string) $output, $found ) )
            $message = trim( preg_replace( '/\s+/', ' ', $found[1] ) );

        return array( 'kind' => self::kindOf( $message ), 'message' => $message );
    }

    /**
     * What sort of fault a message describes.
     *
     * Three kinds, and they want different things doing about them, which is
     * why they are told apart rather than counted together.
     *
     * @param string $message
     * @return string
     */
    public static function kindOf( $message )
    {
        if ( strpos( $message, 'must be compatible with' ) !== false )
            return 'Incompatible declaration: this class has to be changed.';

        if ( preg_match( '/Class ["\']?([^"\' ]+)["\']? not found/', $message ) )
            return 'A parent or interface is missing: usually an extension that needs another one.';

        if ( strpos( $message, 'cannot be called statically' ) !== false )
            return 'Called statically at load time and not declared static: a php 8 incompatibility.';

        if ( strpos( $message, 'Cannot redeclare' ) !== false )
            return 'Declared twice: two files claim the same name, or one is included by hand as well as autoloaded.';

        if ( $message === '' )
            return 'php refused it and said nothing this script could read.';

        return 'php refused it.';
    }

    /**
     * The php binary running this.
     *
     * @return string
     */
    protected static function php()
    {
        return escapeshellarg( PHP_BINARY !== '' ? PHP_BINARY : 'php' );
    }

    /**
     * A temporary file, in var/ rather than anywhere shared.
     *
     * @param string $what
     * @return string
     */
    protected static function tempFile( $what )
    {
        $directory = 'var/cache/classcheck';

        if ( !is_dir( $directory ) )
            @mkdir( $directory, 0775, true );

        return $directory . '/' . $what . '-' . getmypid() . '-' . mt_rand() . '.txt';
    }
}

?>
