<?php
/**
 * File containing the expIniCommand class.
 *
 * The exp:ini command without its script: splitting the command line, the help texts, finding the action in
 * the registry, running it with an expIniCommandContext and turning what happened into an exit code and
 * (with --json) one JSON object. Exponential\Command\Kernel\Ini (bin/php/ini.php) is the script around it;
 * tests call dispatch() directly. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniCommand
{
    /** The options of the command, in eZScript::getOptions() form. */
    const OPTION_CONFIG = '[dry-run][json][show-secrets][allow-default][clear-cache][no-clear-cache][backup][no-backup][create][no-create][root:][keep-target][only:][files:][force][create-extension][activate]';

    /**
     * The help of each option, for eZScript and the help text.
     *
     * @return array option => text
     */
    public static function optionHelp()
    {
        return array(
            'dry-run'        => 'show the diff of the change and write nothing',
            'json'           => 'print one JSON object: ok, code, action, message, warnings, data',
            'show-secrets'   => 'show the values of Password, Secret, Token, Salt, ...Key variables (masked otherwise)',
            'allow-default'  => 'allow writing the default scope, the kernel\'s shipped settings/<file>.ini',
            'clear-cache'    => 'clear the ini cache after a write (the default)',
            'no-clear-cache' => 'do not clear the ini cache after a write',
            'backup'         => 'copy the file to var/backup/ini/<timestamp>/ before writing (the default)',
            'no-backup'      => 'write without a backup copy',
            'create'         => 'create a missing file and block (the default for set, add, clear, toggle, copy)',
            'no-create'      => 'refuse to create a missing file (rem never creates one)',
            'root'           => 'work on the settings tree of another installation root (a copy, a checkout); the ini cache is not cleared',
            'keep-target'    => 'move: a variable both blocks have keeps the target\'s value (the source\'s wins by default)',
            'only'           => 'move: only these variables of the block, comma separated',
            'files'          => 'move-all: only these files, comma separated (site,content,...)',
            'force'          => 'move: keep a move although a value in effect changed (it is rolled back otherwise)',
            'create-extension' => 'move: create the target extension (extension.xml, ezinfo.php, settings/) when it does not exist',
            'activate'       => 'move: activate the target extension (ActiveExtensions[] in global, ActiveAccessExtensions[] for a siteaccess target)',
        );
    }

    /**
     * Splits the command line: what eZScript parses, the literal arguments after "--" (so a value may start
     * with "-") and whether help was asked for (--help / -h are taken out, the command answers them itself).
     *
     * @param array $argv the arguments without the program name
     * @return array( array $forOptions, array $literal, bool $wantsHelp )
     */
    public static function splitArguments( array $argv )
    {
        $forOptions = array();
        $literal = array();
        $wantsHelp = false;
        $afterDashes = false;
        foreach ( array_values( $argv ) as $arg )
        {
            if ( $afterDashes )
                $literal[] = $arg;
            else if ( $arg === '--' )
                $afterDashes = true;
            else if ( $arg === '--help' || $arg === '-h' )
                $wantsHelp = true;
            else
                $forOptions[] = $arg;
        }
        // "exp:ini help [<action>]" is --help too
        foreach ( $forOptions as $i => $arg )
        {
            if ( $arg === '' || $arg[0] !== '-' )
            {
                if ( $arg === 'help' )
                {
                    $wantsHelp = true;
                    unset( $forOptions[$i] );
                    $forOptions = array_values( $forOptions );
                }
                break;
            }
        }
        return array( $forOptions, $literal, $wantsHelp );
    }

    /**
     * The options eZScript parsed, as the context takes them.
     *
     * @param array $parsed eZScript::getOptions()
     * @return array
     */
    public static function contextOptions( array $parsed )
    {
        $flag = function ( $name ) use ( $parsed ) { return !empty( $parsed[$name] ); };
        $tri = function ( $on, $off ) use ( $flag ) { return $flag( $off ) ? false : ( $flag( $on ) ? true : null ); };
        return array(
            'dry-run'       => $flag( 'dry-run' ),
            'json'          => $flag( 'json' ),
            'show-secrets'  => $flag( 'show-secrets' ),
            'allow-default' => $flag( 'allow-default' ),
            'clear-cache'   => $tri( 'clear-cache', 'no-clear-cache' ) !== false,
            'backup'        => $tri( 'backup', 'no-backup' ) !== false,
            'create'        => $tri( 'create', 'no-create' ),
            'root'          => isset( $parsed['root'] ) && is_string( $parsed['root'] ) && $parsed['root'] !== '' ? $parsed['root'] : null,
            'keep-target'   => $flag( 'keep-target' ),
            'only'          => isset( $parsed['only'] ) && is_string( $parsed['only'] ) ? $parsed['only'] : null,
            'files'         => isset( $parsed['files'] ) && is_string( $parsed['files'] ) ? $parsed['files'] : null,
            'force'         => $flag( 'force' ),
            'create-extension' => $flag( 'create-extension' ),
            'activate'      => $flag( 'activate' ),
        );
    }

    /**
     * Runs the command.
     *
     * @param array $positional the arguments: the action's name, then its arguments
     * @param array $options contextOptions()
     * @param bool $wantsHelp --help / -h
     * @param callable $out function( string $line ): standard output
     * @param callable $err function( string $line ): error output
     * @param expIniActionRegistry|null $registry
     * @return int exit code
     */
    public static function dispatch( array $positional, array $options, $wantsHelp, $out, $err, $registry = null )
    {
        $registry = $registry !== null ? $registry : expIniActionRegistry::fromSettings();
        $json = !empty( $options['json'] );
        $positional = array_values( $positional );
        $name = isset( $positional[0] ) ? (string)$positional[0] : '';

        if ( $name === 'help' )
        {
            $wantsHelp = true;
            array_shift( $positional );
            $name = isset( $positional[0] ) ? (string)$positional[0] : '';
        }

        if ( $name === '' )
        {
            foreach ( explode( "\n", self::help( $registry ) ) as $l )
                call_user_func( $wantsHelp ? $out : $err, $l );
            return $wantsHelp ? expIniCommandContext::EXIT_OK : expIniCommandContext::EXIT_USAGE;
        }

        $resolved = $registry->resolve( $name );
        if ( $resolved === null )
            return self::fail( $json, $name, expIniCommandContext::EXIT_USAGE,
                               "unknown action \"$name\"; the actions are: " . implode( ', ', $registry->names() )
                               . ' (exp:ini --help)', $out, $err );

        try
        {
            $action = $registry->create( $resolved );
        }
        catch ( RuntimeException $e )
        {
            return self::fail( $json, $resolved, expIniCommandContext::EXIT_USAGE, $e->getMessage(), $out, $err );
        }

        if ( $wantsHelp )
        {
            foreach ( explode( "\n", self::actionHelp( $action, $registry ) ) as $l )
                call_user_func( $out, $l );
            return expIniCommandContext::EXIT_OK;
        }

        if ( !empty( $options['root'] ) )
        {
            if ( !is_dir( $options['root'] . '/settings' ) )
                return self::fail( $json, $resolved, expIniCommandContext::EXIT_USAGE,
                                   '--root ' . $options['root'] . ' has no settings directory', $out, $err );
            self::useRoot( realpath( $options['root'] ) );
        }

        $context = new expIniCommandContext( $resolved, array_slice( $positional, 1 ), $options, $registry );
        $context->setWriter( $out );
        try
        {
            $code = (int)$action->run( $context );
        }
        catch ( expIniException $e )
        {
            $code = in_array( $e->getCode(), array( 1, 2, 3, 4 ), true ) ? $e->getCode() : expIniCommandContext::EXIT_USAGE;
            $context->finish( $code, '' );
            $message = ( $code === expIniCommandContext::EXIT_USAGE ? 'Usage error: ' : ( $code === expIniCommandContext::EXIT_NOT_FOUND
                       ? 'Not found: ' : ( $code === expIniCommandContext::EXIT_REFUSED ? 'Refused: ' : 'Write failed: ' ) ) ) . $e->getMessage();
            if ( $json )
            {
                $result = $context->result( $code );
                $result['message'] = $message;
                call_user_func( $out, self::json( $result ) );
            }
            else
            {
                call_user_func( $err, $message );
                if ( $code === expIniCommandContext::EXIT_USAGE )
                    call_user_func( $err, 'Usage: exp:ini ' . $resolved . ' ' . strtok( (string)$action->usage(), "\n" ) );
            }
            return $code;
        }

        if ( $json )
            call_user_func( $out, self::json( $context->result( $code ) ) );
        return $code;
    }

    /**
     * Works under another installation root, with the extensions that root activates: ActiveExtensions[] of
     * its settings/site.ini and settings/override, and the ActiveAccessExtensions[] of its siteaccesses (an
     * approximation: the editor's chain under another root has one list of active extensions for every
     * siteaccess). Called again after a move activated an extension.
     *
     * @param string $root
     */
    public static function useRoot( $root )
    {
        expIniEditor::setRoot( $root, array() );
        $active = (array)expIniEditor::effectiveValue( 'site', 'ExtensionSettings', 'ActiveExtensions' );
        foreach ( expIniEditor::scopes() as $scope )
        {
            if ( $scope->kind() !== expIniScope::KIND_SITEACCESS || !is_file( $scope->path( 'site' ) ) )
                continue;
            $values = expIniWriter::fromFile( $scope->path( 'site' ) )->values();
            if ( isset( $values['ExtensionSettings']['ActiveAccessExtensions'] ) )
                $active = array_merge( $active, (array)$values['ExtensionSettings']['ActiveAccessExtensions'] );
        }
        $active = array_values( array_unique( array_filter( array_map( 'strval', $active ), 'strlen' ) ) );
        expIniEditor::setRoot( $root, $active );
    }

    /** Reports a failure before an action ran. */
    private static function fail( $json, $action, $code, $message, $out, $err )
    {
        if ( $json )
            call_user_func( $out, self::json( array( 'ok' => false, 'code' => $code, 'action' => $action,
                                                     'message' => $message, 'warnings' => array(), 'data' => array() ) ) );
        else
            call_user_func( $err, $message );
        return $code;
    }

    /** @return string pretty JSON */
    public static function json( array $data )
    {
        return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
    }

    /**
     * The help of one action.
     *
     * @param expIniAction $action
     * @param expIniActionRegistry $registry
     * @return string
     */
    public static function actionHelp( expIniAction $action, expIniActionRegistry $registry )
    {
        $aliases = array_keys( array_filter( $registry->aliases(), function ( $n ) use ( $action ) { return $n === $action->name(); } ) );
        $lines = explode( "\n", (string)$action->usage() );
        $text = 'exp:ini ' . $action->name() . ': ' . $action->description() . "\n\nUsage:\n";
        $examples = false;
        foreach ( $lines as $l )
        {
            if ( $l === '' )
                $examples = true;
            $text .= $examples ? ( $l === '' ? "\n" : "$l\n" ) : '  exp:ini ' . $action->name() . ' ' . $l . "\n";
        }
        if ( $aliases )
            $text .= "\nAlias: " . implode( ', ', $aliases ) . "\n";
        $text .= "\nOptions: --dry-run --json --show-secrets --allow-default --no-clear-cache --no-backup --no-create --root=<dir>\n"
               . 'Exit codes: 0 done, 1 usage error, 2 not found, 3 refused, 4 write failed. Guide: doc/bc/6.0/console-exp-ini.md';
        return $text;
    }

    /**
     * The command's full help.
     *
     * @param expIniActionRegistry $registry
     * @return string
     */
    public static function help( expIniActionRegistry $registry )
    {
        $text = "exp:ini: read and change the settings of every scope from the command line\n\n"
              . "Usage: exp:ini <action> <arguments> [options]          (php bin/php/ini.php ...)\n\n"
              . "Actions:\n";
        foreach ( $registry->actions() as $name => $class )
        {
            try
            {
                $action = $registry->create( $name );
                $first = strtok( (string)$action->usage(), "\n" );
                $text .= sprintf( "  %-8s %s\n", $name, $action->description() );
                if ( $first !== false && $first !== '(no arguments)' )
                    $text .= sprintf( "  %-8s   exp:ini %s %s\n", '', $name, $first );
            }
            catch ( Exception $e )
            {
                $text .= sprintf( "  %-8s BROKEN: %s\n", $name, $e->getMessage() );
            }
        }
        foreach ( $registry->aliases() as $alias => $name )
            $text .= sprintf( "  %-8s = %s\n", $alias, $name );

        $text .= "\nSetting syntax (the .ini suffix is optional):\n"
               . "  site.ini/SiteSettings/SiteName      site.ini:SiteSettings.SiteName      \"site.ini [SiteSettings] SiteName\"\n"
               . "  .../Variable[]        an array (add, rem <value>, clear)\n"
               . "  .../Variable[key]     a hash entry (set, get, rem)\n"
               . "  A value that starts with \"-\": put \"--\" before it; what follows is taken as it is:\n"
               . "    exp:ini set site.ini/MySettings/Offset -- -1 global\n"
               . "\nScopes (exp:ini scopes lists those of this installation):\n"
               . "  global (= override)                  settings/override/<file>.ini.append.php\n"
               . "  siteaccess:<sa> (or bare <sa>)       settings/siteaccess/<sa>/<file>.ini.append.php\n"
               . "  extension:<ext>                      extension/<ext>/settings/<file>.ini.append.php\n"
               . "  extension:<ext>:siteaccess:<sa>      extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php\n"
               . "  default                              settings/<file>.ini, refused unless --allow-default\n"
               . "  and the scopes of providers registered in ini.ini [IniCommandSettings] ScopeProviders[]\n"
               . "\nOptions:\n";
        foreach ( self::optionHelp() as $option => $help )
            $text .= sprintf( "  --%-16s %s\n", $option . ( $option === 'root' ? '=<dir>' : '' ), $help );
        $text .= "  -s <siteaccess>    the siteaccess whose settings are in effect (get, where, list)\n"
               . "  --allow-root-user  needed when run as root, as for every script\n"
               . "\nFiles are written line by line: comments, blank lines and the order stay; a changed file keeps its\n"
               . "owner, group and mode, a new one gets those of its settings directory; the write is atomic and a\n"
               . "backup goes to var/backup/ini/<timestamp>/ first. After a write the ini cache is cleared.\n"
               . "\nExit codes: 0 done (or nothing to change), 1 usage error, 2 not found, 3 refused, 4 write failed\n"
               . "\nExamples:\n"
               . "  exp:ini set site.ini/SiteSettings/SiteName \"My site\" global\n"
               . "  exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin\n"
               . "  exp:ini toggle site.ini/ContentSettings/ViewCaching global          enabled <-> disabled\n"
               . "  exp:ini toggle site.ini/TemplateSettings/Debug siteaccess:admin     true <-> false\n"
               . "  exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] myext global --dry-run\n"
               . "  exp:ini where site.ini/SiteSettings/SiteName\n"
               . "  exp:ini get site.ini/DatabaseSettings/Password global              ******** (--show-secrets)\n"
               . "  exp:ini move site.ini/DebugSettings global extension:mysite --create-extension --activate --dry-run\n"
               . "  exp:ini move-all siteaccess:admin extension:mysite:siteaccess:admin --dry-run\n"
               . "  exp:ini help <action>\n"
               . "\nExtending: an extension adds an action with Actions[<name>]=<class> and a scope provider with\n"
               . "ScopeProviders[]=<class> in extension/<ext>/settings/ini.ini.append.php. Guide: doc/bc/6.0/console-exp-ini.md";
        return $text;
    }
}
