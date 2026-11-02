<?php
/**
 * @description Set a static publishing target up on GitHub (the repository, GitHub Pages from the branch or Actions, the custom domain, HTTPS), show its state there, or sign in with GitHub
 * @alias exp:static:github
 *
 * File containing the exp:static:github command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticgithub extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:github';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "GitHub for a static publishing target\n" .
                             "setup creates the repository when asked (--create-repo, public unless --private), switches GitHub Pages\n" .
                             "on from the target's branch and folder or from GitHub Actions, and sets the custom domain and HTTPS.\n" .
                             "status shows the repository, GitHub Pages and the state of the last Pages build. Both sign in with the\n" .
                             "target's Credential (token: or app:). signin signs a user in with GitHub (an OAuth App's device flow,\n" .
                             "[StaticPublishSettings] GitHubOAuthClientId) and keeps the token as the secret --secret.\n" .
                             "\n" .
                             "./console exp:static:github setup github-public --create-repo\n" .
                             "./console exp:static:github setup github-public --pages=actions --plan\n" .
                             "./console exp:static:github status github-public\n" .
                             "./console exp:static:github signin --secret=github_pages_token",
        ) + self::SCRIPT );
        return (array)$this->startup( '[create-repo][public][private][pages:][domain:][no-https][plan][client-id:][secret:][format:]', '[action][target]', array(
            'create-repo' => 'setup: create the repository when it does not exist (empty; the first publish fills it)',
            'public' => 'With --create-repo: a public repository (the default; GitHub Pages on a free plan needs one)',
            'private' => 'With --create-repo: a private repository (GitHub Pages serves it only on a paid plan)',
            'pages' => 'setup: branch or actions instead of the target\'s Delivery, or off to leave GitHub Pages as it is',
            'domain' => 'setup: this custom domain instead of the target\'s CustomDomain (none removes it)',
            'no-https' => 'setup: do not enforce HTTPS for the custom domain',
            'plan' => 'setup: only say what would be done, change nothing',
            'client-id' => 'signin: the client id of the OAuth App (default [StaticPublishSettings] GitHubOAuthClientId)',
            'secret' => 'signin: the name of the secret the token is kept as (Setup > Secrets); the target names it as Credential=token:<secret>',
            'format' => 'text (default) or json (the machine output exp-static/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $action = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( !in_array( $action, array( 'setup', 'status', 'signin' ), true ) )
            $this->usage( 'exp:static:github setup|status <target>, or exp:static:github signin --secret=<name>' );
        $unavailable = \expStaticGitHub::unavailable();
        if ( $unavailable !== '' )
            $this->finish( array( 'mode' => 'github-' . $action, 'errors' => array( $unavailable ), 'warnings' => array() ), $format );
        if ( $action === 'signin' )
            $this->signin( $options, $format );

        $options['arguments'] = array_slice( (array)$options['arguments'], 1 );
        $target = $this->target( $options, $format );
        $this->database( $format );
        if ( $action === 'status' )
            $this->finish( array( 'mode' => 'github-status' ) + \expStaticGitHub::statusFor( $target ), $format );

        if ( !empty( $options['public'] ) && !empty( $options['private'] ) )
            $this->usage( '--public or --private, not both' );
        $pages = isset( $options['pages'] ) && $options['pages'] !== false ? (string)$options['pages'] : '';
        if ( $pages !== '' && !in_array( $pages, array( 'branch', 'actions', 'off' ), true ) )
            $this->usage( "--pages must be branch, actions or off, not $pages" );
        $domain = isset( $options['domain'] ) && $options['domain'] !== false ? strtolower( trim( (string)$options['domain'] ) ) : null;
        if ( $domain !== null && $domain !== 'none' && !\expStaticExtras::validDomain( $domain ) )
            $this->usage( "--domain must be a host name (www.example.com) or none, not $domain" );

        $warnings = array();
        $settings = $target->settings();
        if ( $pages === 'branch' || $pages === 'actions' )
        {
            if ( $pages !== $settings['Delivery'] )
                $warnings[] = "--pages=$pages, but the target says Delivery={$settings['Delivery']}: change the target too, or the next publish does not match";
            $settings['Delivery'] = $pages;
        }
        if ( $domain !== null )
        {
            $domain = $domain === 'none' ? '' : $domain;
            if ( $domain !== $settings['CustomDomain'] )
                $warnings[] = '--domain' . ( $domain === '' ? '=none' : "=$domain" ) . ", but the target says CustomDomain={$settings['CustomDomain']}: the build writes the file CNAME from the target's setting";
            $settings['CustomDomain'] = $domain;
        }
        $report = \expStaticGitHub::setupTarget( new \expStaticTarget( $target->id(), $settings ), array(
            'create' => !empty( $options['create-repo'] ), 'private' => !empty( $options['private'] ), 'pages' => $pages !== 'off',
            'https' => empty( $options['no-https'] ), 'dry_run' => !empty( $options['plan'] ) ) );
        $report['warnings'] = array_merge( $warnings, $report['warnings'] );
        $this->finish( array( 'mode' => 'github-setup', 'dry_run' => !empty( $options['plan'] ) ) + $report, $format );
    }

    /** signin: the OAuth device flow; the token goes to the secret store, never to the output. */
    protected function signin( array $options, $format )
    {
        $secret = isset( $options['secret'] ) && $options['secret'] !== false ? trim( (string)$options['secret'] ) : '';
        if ( $secret === '' || !\expSecrets::validName( $secret ) )
            $this->usage( 'signin needs --secret=<name>: the secret the token is kept as (letters, digits, _ . -)' );
        $clientId = isset( $options['client-id'] ) && $options['client-id'] !== false ? trim( (string)$options['client-id'] ) : '';
        if ( $clientId === '' )
        {
            $ini = \eZINI::instance( 'staticcache.ini' );
            $clientId = $ini->hasVariable( 'StaticPublishSettings', 'GitHubOAuthClientId' ) ? trim( (string)$ini->variable( 'StaticPublishSettings', 'GitHubOAuthClientId' ) ) : '';
        }
        $report = array( 'mode' => 'github-signin', 'secret' => $secret, 'credential_setting' => 'token:' . $secret, 'stored' => false, 'errors' => array(), 'warnings' => array() );
        if ( $clientId === '' )
        {
            $report['errors'][] = 'no OAuth App: --client-id, or staticcache.ini [StaticPublishSettings] GitHubOAuthClientId (an OAuth App with the device flow switched on)';
            $this->finish( $report, $format );
        }
        $this->database( $format );
        if ( !\expSecrets::installed() )
        {
            $report['errors'][] = 'the secret store is not set up (the table exp_secret: ./console exp:upgrade --apply)';
            $this->finish( $report, $format );
        }
        $code = \expStaticGitHub::deviceCode( $clientId );
        if ( $code['error'] !== '' )
        {
            $report['errors'][] = 'GitHub did not start the sign-in: ' . $code['error'];
            $this->finish( $report, $format );
        }
        // the code is for the person at the terminal: on stderr, so JSON output stays clean
        $this->error( 'Open ' . $code['verification_uri'] . ' and enter the code ' . $code['user_code'] . ' (valid for ' . (int)round( $code['expires_in'] / 60 ) . ' minutes).' );
        $interval = $code['interval'];
        $until = time() + $code['expires_in'];
        $token = '';
        while ( time() < $until )
        {
            sleep( $interval );
            $poll = \expStaticGitHub::deviceToken( $clientId, $code['device_code'] );
            if ( $poll['state'] === 'done' )
            {
                $token = $poll['token'];
                break;
            }
            if ( $poll['state'] === 'slow_down' )
                $interval += 5;
            else if ( $poll['state'] === 'failed' )
            {
                $report['errors'][] = 'the sign-in failed: ' . $poll['error'];
                break;
            }
        }
        if ( $token === '' && !$report['errors'] )
            $report['errors'][] = 'the code expired before it was entered: run signin again';
        if ( $token !== '' )
        {
            $report['stored'] = (bool)\expSecrets::set( $secret, $token, \expStaticCredentials::SCOPE, 'GitHub sign-in (OAuth) for static publishing' );
            if ( !$report['stored'] )
                $report['errors'][] = "the secret $secret could not be stored (Setup > Secrets: the store's key)";
        }
        \expStaticPublisher::audit( 'system.staticpublish.credential', array(
            'object' => array( 'type' => 'secret', 'id' => $secret ), 'verb' => 'github-signin',
            'result' => $report['stored'] ? 'success' : 'failure', 'reason' => $report['stored'] ? '' : (string)reset( $report['errors'] ),
            'x' => array( 'kind' => 'token', 'secret' => $secret ) ) );
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array();
        if ( $report['mode'] === 'github-signin' )
        {
            if ( !empty( $report['stored'] ) )
                $lines[] = 'Signed in. The token is kept as the secret ' . $report['secret'] . '; set the target\'s Credential=' . $report['credential_setting'];
            return $lines;
        }
        if ( $report['mode'] === 'github-setup' )
        {
            $lines[] = 'GitHub setup of ' . $report['target'] . ( !empty( $report['dry_run'] ) ? ' (dry run: nothing is changed)' : '' );
            foreach ( $report['steps'] as $step )
                $lines[] = sprintf( '  %-10s %-9s %s', $step['step'], $step['state'], $step['message'] );
            return $lines;
        }
        if ( $report['mode'] === 'github-status' && !empty( $report['remote'] ) )
        {
            $lines[] = 'GitHub: ' . $report['remote']['owner'] . '/' . $report['remote']['repo'];
            if ( !empty( $report['repo']['exists'] ) )
                $lines[] = '  repository  ' . $report['repo']['visibility'] . '  ' . $report['repo']['html_url'];
            if ( !empty( $report['pages']['enabled'] ) )
            {
                $p = $report['pages'];
                $lines[] = '  pages       ' . ( $p['build_type'] === 'workflow' ? 'GitHub Actions' : 'branch ' . $p['branch'] . ( $p['folder'] !== '' ? ' /' . $p['folder'] : '' ) )
                         . '  ' . $p['html_url'];
                if ( $p['cname'] !== '' )
                    $lines[] = '  domain      ' . $p['cname'] . '  HTTPS ' . ( $p['https_enforced'] ? 'enforced' : 'not enforced' ) . ( $p['certificate'] !== '' ? ', certificate ' . $p['certificate'] : '' );
            }
            if ( !empty( $report['build'] ) )
                $lines[] = '  last build  ' . $report['build']['status'] . ( $report['build']['at'] !== '' ? '  ' . $report['build']['at'] : '' )
                         . ( $report['build']['error'] !== '' ? '  ' . $report['build']['error'] : '' );
        }
        return $lines;
    }
}

}
