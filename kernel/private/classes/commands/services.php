<?php
/**
 * The code of bin/php/services.php: the kernel service container on the command line.
 * @description Compile, list, show and check the kernel service container (verbs compile, list, show, check)
 * @alias exp:services:compile
 * @alias exp:services:list
 * @alias exp:services:show
 * @alias exp:services:check
 * Guide: doc/eng-us/bc/6.0/kernel-services.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Services extends \Exponential\Runnable\Command
{
    const VERBS = array( 'compile', 'list', 'show', 'check' );

    public function run()
    {
        $cli = $this->cli();
        $script = $this->script( array(
            'description' => "Exponential kernel services\n\n"
                           . "The service container of the kernel (settings/kernelservices.ini) and its compiled form\n"
                           . "(var/cache/kernelservices/).\n\n"
                           . "  ./console exp:services:compile [--only=site,admin]\n"
                           . "  ./console exp:services:list [--tag=<tag>] [-s <siteaccess>]\n"
                           . "  ./console exp:services:show <id> [-s <siteaccess>]\n"
                           . "  ./console exp:services:check",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true,
        ) );
        $options = $this->startup(
            '[json][tag:][only:]',
            '[verb][id]',
            array(
                'json' => 'Print the result as JSON.',
                'tag'  => 'list: only the services with this tag.',
                'only' => 'compile: only these siteaccesses (comma separated); the files of the others are kept.',
                'verb' => "compile, list, show or check (default: the one the command was called as, else list)\n"
                        . "compile  record the extension activation of every siteaccess and write the compiled container\n"
                        . "list     the services of the siteaccess (-s), with class, scope and tags\n"
                        . "show     one service: its definition and where the compiled container stands\n"
                        . "check    the definitions of every siteaccess and the compiled files; exit 1 on an error",
                'id'   => 'show: the service id',
            ) );

        // exp:services:compile and the other aliases name the verb; the first argument can too
        $invoked = (string)getenv( 'EXP_CONSOLE_COMMAND' );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $verb = 'list';
        if ( preg_match( '/^exp:services:(\w+)$/', $invoked, $m ) && in_array( $m[1], self::VERBS, true ) )
            $verb = $m[1];
        else if ( isset( $args[0] ) && in_array( $args[0], self::VERBS, true ) )
            $verb = array_shift( $args );
        else if ( isset( $args[0] ) )
            $verb = array_shift( $args );
        if ( isset( $args[0] ) && $args[0] === $verb )
            array_shift( $args );
        $json = !empty( $options['json'] );

        switch ( $verb )
        {
            case 'compile':
                $only = !empty( $options['only'] ) ? array_filter( array_map( 'trim', explode( ',', (string)$options['only'] ) ) ) : array();
                $compileOptions = $only ? array( 'siteaccesses' => $only, 'partial' => true ) : array();
                $result = \expKernelServiceCompiler::compile( $compileOptions );
                break;
            case 'list':
                $result = $this->listServices( isset( $options['tag'] ) ? (string)$options['tag'] : '' );
                break;
            case 'show':
                $result = $this->showService( isset( $args[0] ) ? (string)$args[0] : '' );
                break;
            case 'check':
                $check = \expKernelServiceCompiler::check();
                $lines = array();
                foreach ( $check['problems'] as $p )
                    $lines[] = strtoupper( $p[0] ) . ' ' . $p[1] . ': ' . $p[2];
                $result = array( 'ok' => $check['ok'],
                                 'message' => ( $check['ok'] ? 'PASS' : 'FAIL' ) . ' kernel services: mode ' . $check['data']['mode']
                                            . ', ' . count( $check['data']['compiled'] ) . ' siteaccess(es) compiled'
                                            . ( $check['data']['generation'] ? ' (generation ' . $check['data']['generation'] . ')' : '' )
                                            . ', worker scope ' . ( $check['data']['worker_scope'] ? 'on' : 'off' ),
                                 'data' => $check['data'] + array( 'problems' => $lines ) );
                break;
            default:
                $result = array( 'ok' => false, 'message' => "unknown verb '$verb'; use compile, list, show or check", 'data' => array() );
        }

        if ( $json )
        {
            $cli->output( json_encode( $result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
        }
        else
        {
            $cli->output( ( $result['ok'] ? '  ' : '  ERROR: ' ) . $result['message'] );
            foreach ( $result['data'] as $k => $v )
            {
                if ( $k === 'rows' )
                {
                    foreach ( $v as $row )
                        $cli->output( '    ' . $row );
                    continue;
                }
                if ( is_array( $v ) && !$v )
                {
                    $v = '-';
                }
                else if ( is_array( $v ) )
                {
                    if ( array_keys( $v ) === range( 0, count( $v ) - 1 ) && !is_array( reset( $v ) ) )
                    {
                        if ( in_array( $k, array( 'problems', 'arguments' ), true ) )
                        {
                            $cli->output( sprintf( '    %-12s', $k ) );
                            foreach ( $v as $line )
                                $cli->output( '      ' . $line );
                            continue;
                        }
                        $v = implode( ', ', $v );
                    }
                    else
                        $v = json_encode( $v, JSON_UNESCAPED_SLASHES );
                }
                else if ( is_bool( $v ) )
                    $v = $v ? 'yes' : 'no';
                else if ( $v === null )
                    $v = '-';
                $cli->output( sprintf( '    %-12s %s', $k, $v ) );
            }
        }

        $script->shutdown( $result['ok'] ? 0 : 1 );
    }

    /**
     * The services of the command's siteaccess: compiled when there is a compiled file for it, else as the
     * settings say now.
     */
    protected function listServices( $tag )
    {
        list( $read, $source ) = $this->definitions();
        $rows = array();
        foreach ( $read['definitions'] as $id => $def )
        {
            if ( $tag !== '' && !in_array( $tag, $def['tags'], true ) )
                continue;
            $rows[] = sprintf( '%-24s %-10s %-34s %s', $id, $def['scope'],
                               $def['factory'] !== '' ? $def['factory'] . '()' : $def['class'],
                               $def['tags'] ? '[' . implode( ', ', $def['tags'] ) . ']' : '' );
        }
        $siteAccess = $this->siteAccessName();
        return array( 'ok' => true,
                      'message' => count( $rows ) . ' service(s)' . ( $tag !== '' ? " tagged $tag" : '' ) . " of siteaccess $siteAccess ($source)",
                      'data' => array( 'rows' => $rows ) );
    }

    /**
     * One service.
     */
    protected function showService( $id )
    {
        if ( $id === '' )
            return array( 'ok' => false, 'message' => 'show needs a service id: ./console exp:services:show <id>', 'data' => array() );
        list( $read, $source ) = $this->definitions();
        $iniFile = \expKernelServiceContainer::iniFileName( $id );
        if ( !isset( $read['definitions'][$id] ) && $iniFile === null )
            return array( 'ok' => false, 'message' => "no service '$id' in siteaccess " . $this->siteAccessName(), 'data' => array() );
        $def = isset( $read['definitions'][$id] ) ? $read['definitions'][$id]
             : array( 'class' => 'eZINI', 'factory' => 'eZINI::instance', 'arguments' => array( $iniFile ), 'scope' => 'worker',
                      'lazy' => true, 'tags' => array(), 'description' => "eZINI::instance( '$iniFile' ) (no block needed)" );
        $problems = array();
        foreach ( \expKernelServiceCompiler::validate( array( 'definitions' => array( $id => $def ) + $read['definitions'],
                                                               'tags' => $read['tags'], 'declared_tags' => $read['declared_tags'] ) ) as $p )
        {
            if ( $p[1] === $id )
                $problems[] = strtoupper( $p[0] ) . ': ' . $p[2];
        }
        return array( 'ok' => !$problems || strpos( implode( ' ', $problems ), 'ERROR' ) === false,
                      'message' => "$id ($source, siteaccess " . $this->siteAccessName() . ')',
                      'data' => array( 'description' => $def['description'], 'class' => $def['class'], 'factory' => $def['factory'],
                                       'arguments' => $def['arguments'], 'scope' => $def['scope'], 'lazy' => $def['lazy'],
                                       'tags' => $def['tags'], 'problems' => $problems ) );
    }

    /**
     * array( read definitions, 'compiled' or 'settings' ) for the command's siteaccess.
     */
    protected function definitions()
    {
        $siteAccess = $this->siteAccessName();
        $dir = \expKernelServices::cacheDirectory();
        $index = \expKernelServices::readCompiled( $dir . '/index.php', null );
        if ( $index !== null && \expKernelServices::validSiteAccessName( $siteAccess ) )
        {
            $compiled = \expKernelServices::readCompiled( $dir . '/' . $siteAccess . '.php', $index['generation'] );
            if ( $compiled !== null )
                return array( array( 'definitions' => $compiled['services'], 'tags' => $compiled['tags'],
                                     'declared_tags' => array() ), 'compiled ' . $index['generation'] );
        }
        return array( \expKernelServiceCompiler::definitionsFromINI( \eZINI::instance( 'kernelservices.ini' ) ), 'settings, not compiled' );
    }

    protected function siteAccessName()
    {
        return isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
    }
}

}
