<?php
/**
 * File containing the expMigrationStepCommand class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "command": one console command of the installation, from an allowlist.
 *
 *   - type: command
 *     command: exp:services:compile        an exp:* command of ./console that migration.ini allows
 *     arguments: [ --apply ]               its arguments, each one argument (no shell reads them)
 *     timeout: 600                         seconds
 *     expect_exit: 0                       the exit code that passes
 *     references: { compiled: exit }       exit, output
 *
 * Only the exp:* commands of migration.ini [StepSettings] CommandAllowList[] run (a name, or a name ending in * for a
 * group: exp:static:*), never a command of the migrations or of the upgrade (exp:migration:*, exp:upgrade*: they take
 * the lock the run holds), never anything but ./console. There is no shell: the command and its arguments are handed to
 * the PHP that runs the migration as a list (expMigrationProcess), so a ; or a $ in an argument is a character. A dry
 * run runs nothing. Each command is recorded in the audit log (system.migration.command: the command and its exit
 * code; never its arguments or output). A command cannot be taken back: a migration with this step needs a down: or a
 * rollback to its caption.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepCommand extends expMigrationStepTypeBase
{
    /** The most of a command's output kept for references:. */
    const MAX_OUTPUT = 65536;

    /** Commands never run from a migration, whatever the allowlist says. */
    const NEVER = array( 'exp:migration:*', 'exp:upgrade', 'exp:upgrade:*' );

    /** @return string */
    public function name()
    {
        return 'command';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Runs one exp:* console command of the installation that migration.ini allows, without a shell',
            'keys' => array(
                'command' => array( 'type' => 'string', 'required' => true,
                                    'description' => 'an exp:* command of ./console listed in migration.ini [StepSettings] CommandAllowList[]' ),
                'arguments' => array( 'type' => 'list', 'description' => 'its arguments, each one argument' ),
                'timeout' => array( 'type' => 'int', 'default' => 600, 'description' => 'seconds' ),
                'expect_exit' => array( 'type' => 'int', 'default' => 0, 'description' => 'the exit code that passes' ),
            ),
            'values' => array(
                'exit' => 'the exit code',
                'output' => 'what the command printed (at most 64 KB)',
            ),
            'example' => "- type: command\n  command: exp:services:compile\n  references: { compiled: exit }\n",
        );
    }

    /**
     * The allowlist: migration.ini [StepSettings] CommandAllowList[].
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function allowList( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $list = $ini->hasVariable( 'StepSettings', 'CommandAllowList' ) ? (array)$ini->variable( 'StepSettings', 'CommandAllowList' ) : array();
        return array_values( array_filter( array_map( 'trim', array_map( 'strval', $list ) ), 'strlen' ) );
    }

    /**
     * Whether a pattern of the allowlist names a command: the name, or a name ending in *. Pure.
     *
     * @param string $pattern
     * @param string $command
     * @return bool
     */
    public static function matches( $pattern, $command )
    {
        $pattern = (string)$pattern;
        if ( substr( $pattern, -1 ) === '*' )
            return strncmp( $command, substr( $pattern, 0, -1 ), strlen( $pattern ) - 1 ) === 0;
        return $pattern === $command;
    }

    /**
     * Why a command may not run, or ''. Pure but for the allowlist.
     *
     * @param string $command
     * @param string[]|null $allowList
     * @return string
     */
    public static function refusal( $command, $allowList = null )
    {
        $command = (string)$command;
        if ( !preg_match( '/^exp:[a-z0-9][a-z0-9:_-]*$/', $command ) )
            return 'only an exp:* command of ./console runs';
        foreach ( self::NEVER as $never )
            if ( self::matches( $never, $command ) )
                return "$command is never run from a migration (it takes the lock the run holds)";
        foreach ( $allowList === null ? self::allowList() : $allowList as $pattern )
            if ( strpos( (string)$pattern, 'exp:' ) === 0 && self::matches( $pattern, $command ) )
                return '';
        return "$command is not in migration.ini [StepSettings] CommandAllowList[]";
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['command'] ) && !expMigrationReferences::contains( $step['command'] ) )
        {
            $refusal = self::refusal( (string)$step['command'] );
            if ( $refusal !== '' )
                $errors[] = "command: $refusal";
        }
        if ( isset( $step['arguments'] ) && is_array( $step['arguments'] ) )
            foreach ( $step['arguments'] as $i => $argument )
                if ( !is_scalar( $argument ) || preg_match( "/[\r\n\\x00]/", (string)$argument ) )
                    $errors[] = "arguments[$i]: one line of text";
        if ( isset( $step['timeout'] ) && is_int( $step['timeout'] ) && ( $step['timeout'] < 1 || $step['timeout'] > 86400 ) )
            $errors[] = 'timeout: 1 to 86400 seconds';
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        return array( 'run ./console ' . $step['command'] . ( !empty( $step['arguments'] ) ? ' ' . implode( ' ', array_map( 'strval', (array)$step['arguments'] ) ) : '' )
                      . ' (not run in a dry run)' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $command = (string)$step['command'];
        $refusal = self::refusal( $command );
        if ( $refusal !== '' )
            throw new RuntimeException( $refusal );
        $arguments = array_merge( array( $command ), array_map( 'strval', isset( $step['arguments'] ) ? (array)$step['arguments'] : array() ) );
        // a command run by root (the console as root) asks for this, as the run itself did
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 && !in_array( '--allow-root-user', $arguments, true ) )
            $arguments[] = '--allow-root-user';
        list( $exit, $output ) = expMigrationProcess::run( 'bin/php/console', $arguments, expMigrationProcess::root( $context ), (int)$step['timeout'] );
        $passes = $exit === (int)$step['expect_exit'];
        if ( $context instanceof expMigrationContext )
            $context->audit( 'system.migration.command', array( 'object' => array( 'type' => 'command', 'id' => $command ), 'verb' => 'run',
                                                                'result' => $passes ? 'success' : 'failure',
                                                                'x' => array( 'exit' => $exit, 'run_id' => $context->runId() ) ) );
        if ( !$passes )
            throw new RuntimeException( "./console $command exited with $exit (expected " . (int)$step['expect_exit'] . '): ' . expMigrationProcess::tail( $output ) );
        $output = (string)$output;
        return self::result( array( "./console $command exited with $exit" ),
                             array( 'exit' => $exit, 'output' => strlen( $output ) > self::MAX_OUTPUT ? substr( $output, -self::MAX_OUTPUT ) : $output ) );
    }
}
