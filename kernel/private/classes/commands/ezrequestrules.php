<?php
/**
 * The code of bin/php/ezrequestrules.php, moved into a class (#207 stage 1). The file bin/php/ezrequestrules.php is one call to it.
 * @description List and check the request rules of a siteaccess and explain their decision for an address
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezrequestrules.php:
 *
 *
 * File containing the ezrequestrules.php script.
 *
 * Shows and checks the request rules of a siteaccess (requestrules.ini), and
 * explains what they decide for an address and a user, without sending a
 * request. Guide: doc/bc/6.0/view_full_security.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Ezrequestrules extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'access', 'answer', 'args', 'cli', 'context', 'decided', 'engine', 'exit', 'facts', 'header', 'i', 'ini', 'k', 'key', 'module', 'moduleName', 'moved', 'name', 'option', 'options', 'params', 'parts', 'policy', 'problem', 'problems', 'route', 'row', 'rule', 'rules', 'script', 'siteAccess', 'typed', 'uri', 'user', 'v', 'value', 'view', 'viewParams' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        require_once 'kernel/private/classes/global_functions.php';

        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Exponential request rules\n" .
                                 "Lists and checks the rules of a siteaccess and explains their decision for an address.\n" .
                                 "\n" .
                                 "  php bin/php/ezrequestrules.php -s site --list\n" .
                                 "  php bin/php/ezrequestrules.php -s site --check\n" .
                                 "  php bin/php/ezrequestrules.php -s site --uri=content/view/full/2\n" .
                                 "  php bin/php/ezrequestrules.php -s site --uri=Fit-Healthy --user=admin\n" .
                                 "  php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --header=X-Requested-With:XMLHttpRequest --ip=10.1.2.3",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true,
            )
        );

        $options = $this->startup(
            "[list][check][uri:][user:][method:][host:][ip:][scheme:][header:*]",
            "",
            array( 'list'   => 'List the rules in the order they are asked',
                   'check'  => 'Report problems in the settings; exits 1 when there are any',
                   'uri'    => 'Explain the decision for this address, as typed after the host and siteaccess (e.g. content/view/full/2 or Fit-Healthy)',
                   'user'   => 'The login of the user to explain it for (default: the anonymous user)',
                   'method' => 'HTTP method (default GET)',
                   'host'   => 'Host name the visitor used',
                   'ip'     => 'Client address',
                   'scheme' => 'http or https (default https)',
                   'header' => 'A request header, Name:Value; may be given more than once' ) );

        $engine = \ezpRequestRuleEngine::instance();
        $access = \eZSiteAccess::current();
        $siteAccess = isset( $access['name'] ) ? $access['name'] : '(none)';
        $exit = 0;

        if ( !$options['list'] && !$options['check'] && !$options['uri'] )
            $options['list'] = $options['check'] = true;

        if ( $options['list'] )
        {
            $cli->output( "Request rules of siteaccess $siteAccess: " . ( $engine->isEnabled() ? 'enabled' : 'DISABLED (Enabled=false)' )
                        . ', ' . count( $engine->rules() ) . ' rule(s)' );
            $i = 0;
            foreach ( $engine->rules() as $rule )
            {
                $cli->output( sprintf( "%2d. %s", ++$i, $rule->name ) . ( $rule->description !== '' ? "\n    " . $rule->description : '' ) );
                foreach ( $rule->conditions as $key => $value )
                    $cli->output( "    if   $key = $value" );
                $args = array();
                foreach ( $rule->actionArgs as $k => $v )
                    $args[] = "$k=$v";
                $cli->output( "    then {$rule->action}" . ( $args ? ' (' . implode( ', ', $args ) . ')' : '' )
                            . ( $rule->fallbackAction ? ", else {$rule->fallbackAction}" : '' ) );
            }
            $cli->output();
        }

        if ( $options['check'] )
        {
            $problems = $engine->validate();
            foreach ( $problems as $problem )
                $cli->output( "FAIL $problem" );
            $cli->output( $problems ? 'FAIL ' . count( $problems ) . ' problem(s) in the request rules' : 'PASS the request rules are understood' );
            $exit = $problems ? 1 : 0;
        }

        if ( $options['uri'] )
        {
            $user = \eZUser::fetchByName( $options['user'] ? $options['user'] : '' );
            if ( !$options['user'] )
                $user = \eZUser::fetch( \eZUser::anonymousId() );
            if ( !$user instanceof \eZUser )
            {
                $cli->error( "No user with the login '{$options['user']}'" );
                $script->shutdown( 1 );
            }

            // Translate the address the way the kernel does (ezpKernelWeb::dispatchLoop())
            $ini = \eZINI::instance();
            $uri = \eZURI::instance( '/' . ltrim( $options['uri'], '/' ) );
            $typed = $uri->uriString();
            $route = array( 'typed_uri' => $typed, 'via' => $uri->isEmpty() ? 'index' : 'system',
                            'user_parameters' => $uri->userParameters(), 'rewrites' => 0 );
            if ( $uri->isEmpty() )
                $uri = \eZURI::instance( $ini->variable( 'SiteSettings', 'IndexPage' ) );
            else if ( \eZURLAliasML::urlTranslationEnabledByUri( $uri ) )
            {
                $moved = \eZURLAliasML::translate( $uri );
                if ( is_string( $moved ) )
                {
                    $cli->output( "The address has moved to $moved; the kernel redirects there before any rule is asked" );
                    $script->shutdown( $exit );
                }
                if ( strcasecmp( trim( $uri->uriString(), '/' ), trim( $typed, '/' ) ) !== 0 )
                    $route['via'] = 'alias';
                else if ( $ini->variable( 'URLTranslator', 'WildcardTranslation' ) === 'enabled' )
                {
                    \eZURLWildcard::translate( $uri );
                    if ( strcasecmp( trim( $uri->uriString(), '/' ), trim( $typed, '/' ) ) !== 0 )
                        $route['via'] = 'wildcard';
                }
            }

            $module = null; $moduleName = ''; $view = ''; $params = array();
            if ( !fetchModule( $uri, null, $module, $moduleName, $view, $params ) )
            {
                $cli->output( "No module for the address '$typed': the kernel answers \"not found\" before any rule is asked" );
                $script->shutdown( $exit );
            }

            foreach ( array( 'method' => 'REQUEST_METHOD', 'host' => 'HTTP_HOST' ) as $option => $key )
            {
                if ( $options[$option] )
                    $_SERVER[$key] = $options[$option];
            }
            if ( !$options['method'] )
                $_SERVER['REQUEST_METHOD'] = 'GET';
            foreach ( (array)$options['header'] as $header )
            {
                $parts = explode( ':', $header, 2 );
                if ( count( $parts ) === 2 )
                    $_SERVER['HTTP_' . strtoupper( str_replace( '-', '_', trim( $parts[0] ) ) )] = trim( $parts[1] );
            }

            $context = \ezpRequestRuleKernel::context( $moduleName, $view, $params, $route, $user, $module );
            if ( $options['ip'] )
                $context->define( 'client_ip', $options['ip'] );
            $context->define( 'scheme', $options['scheme'] ? strtolower( $options['scheme'] ) : 'https' );

            $cli->output( "Address  /$typed  ->  " . $context->get( 'resolved_uri' ) . "  (requested via " . $route['via'] . ')' );
            $cli->output( 'User     ' . $user->attribute( 'login' ) . ' (' . ( $user->isAnonymous() ? 'anonymous' : 'logged in' ) . ")  siteaccess $siteAccess" );

            $viewParams = array();
            $policy = $user->hasAccessToView( $module, $view, $viewParams );
            if ( !$policy )
                $cli->output( "Policy   the user's roles do not allow $moduleName/$view: \"access denied\" before any rule is asked" );
            $cli->output();

            $decided = false;
            $rules = $engine->rules();
            foreach ( $engine->explain( $context ) as $row )
            {
                $cli->output( ( $row['decides'] ? 'DECIDES ' : ( $row['matched'] ? 'matched ' : 'no      ' ) ) . $row['rule'] );
                foreach ( $row['conditions'] as $key => $answer )
                    $cli->output( '        ' . ( $answer === true ? 'yes' : ( $answer === false ? 'no ' : $answer ) )
                                . "  $key = " . $rules[$row['rule']]->conditions[$key] );
                if ( $row['matched'] )
                    $cli->output( '        -> ' . ( $row['result'] !== null ? $row['result'] : 'could not act; the next rule is asked' ) );
                if ( $row['varies'] )
                    $cli->output( '        (a condition varies by request: this page is kept out of the shared caches)' );
                if ( $row['decides'] )
                    $decided = true;
            }
            $cli->output();
            $cli->output( $decided ? 'Result   decided by the rule marked DECIDES' : 'Result   no rule decides; the view runs as usual' );

            $facts = array();
            foreach ( $context->resolvedFacts() as $name => $value )
            {
                if ( $value === '(not needed)' || $name === 'user' )
                    continue;
                $facts[] = sprintf( '  %-20s %s', $name, is_array( $value ) ? json_encode( $value, JSON_UNESCAPED_SLASHES ) : var_export( $value, true ) );
            }
            $cli->output( "Facts the rules read:\n" . implode( "\n", $facts ) );
        }

        $script->shutdown( $exit );
    }
}

}
