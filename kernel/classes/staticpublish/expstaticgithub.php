<?php
/**
 * File containing the expStaticGitHub class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The GitHub integration of static publishing: GitHub's REST API for a target whose Kind is github-pages.
 *
 *   - the repository: read it, or create it (public or private) for the signed-in account or an organization
 *   - GitHub Pages: switch it on from the branch (and folder) or from GitHub Actions, the custom domain, HTTPS
 *   - the Pages build status (built, building, errored) and the address the site is served at
 *   - the limits of GitHub Pages, checked before every push (limits())
 *   - a GitHub App installation token for Credential=app:<installation> (appToken(), registered as the provider of
 *     expStaticCredentials), and the OAuth device flow that signs a user in and keeps the token in Setup > Secrets
 *
 * Everything here is behind available(): without curl and openssl, or with [StaticPublishSettings] GitHubAPI=disabled,
 * nothing calls GitHub, and publishing to the branch keeps working without the API.
 *
 * The API signs in with the target's own credential: its token (token:<secret>) or its GitHub App installation
 * (app:<installation>). A deploy key (sshkey:) or none cannot call the API. The token is sent in the Authorization
 * header only: it is never part of an address, a report or a log (redact()).
 *
 * The requests go through request(): curl by default, or the callable of the option http, which the tests pass.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticGitHub
{
    /** GitHub's REST API */
    const API = 'https://api.github.com';

    /** The API version sent with every request */
    const API_VERSION = '2022-11-28';

    /** The addresses GitHub Pages serves an apex domain from (docs: Managing a custom domain for your GitHub Pages site) */
    const PAGES_A = array( '185.199.108.153', '185.199.109.153', '185.199.110.153', '185.199.111.153' );
    const PAGES_AAAA = array( '2606:50c0:8000::153', '2606:50c0:8001::153', '2606:50c0:8002::153', '2606:50c0:8003::153' );

    /** The limits of GitHub Pages (docs: GitHub Pages limits) */
    const SITE_BYTES = 1000000000;
    const FILE_BYTES = 100000000;
    /** GitHub warns about a pushed file above this size */
    const FILE_WARN_BYTES = 50000000;
    const BANDWIDTH = '100 GB a month';

    /** The workflow file the Actions delivery uses, at the root of the branch */
    const WORKFLOW_FILE = '.github/workflows/static-publish-pages.yml';

    /** @var string the token the API is called with */
    private $token;

    /** @var array api (the API address), http (callable( method, url, headers, body ) returning status, headers, body), user_agent */
    private $options;

    /** @var string why the last appToken() gave no token */
    private static $lastProblem = '';

    /** @var array installation => array( token, expires ) of the App tokens of this process */
    private static $appTokens = array();

    /**
     * @param string $token the token the API is called with (a fine-grained or OAuth token, or an App installation token)
     * @param array $options api, http, user_agent, timeout (seconds a request may take; the administration's pages use less)
     */
    public function __construct( $token, array $options = array() )
    {
        $this->token = (string)$token;
        $this->options = $options + array( 'api' => self::setting( 'GitHubAPIURL', self::API ), 'http' => null, 'user_agent' => 'Exponential static publishing',
                                       'timeout' => 30 );
        $this->options['api'] = rtrim( (string)$this->options['api'], '/' );
    }

    /** @return string a setting of [StaticPublishSettings] */
    protected static function setting( $name, $default )
    {
        if ( !class_exists( 'eZINI' ) )
            return $default;
        $ini = eZINI::instance( 'staticcache.ini' );
        return $ini->hasVariable( 'StaticPublishSettings', $name ) && trim( (string)$ini->variable( 'StaticPublishSettings', $name ) ) !== ''
             ? trim( (string)$ini->variable( 'StaticPublishSettings', $name ) ) : $default;
    }

    /**
     * Why the GitHub integration cannot be used here, '' when it can.
     *
     * @return string
     */
    public static function unavailable()
    {
        if ( self::setting( 'GitHubAPI', 'enabled' ) !== 'enabled' )
            return 'the GitHub integration is switched off (staticcache.ini [StaticPublishSettings] GitHubAPI=disabled)';
        if ( !function_exists( 'curl_init' ) )
            return 'the PHP extension curl is missing';
        if ( !function_exists( 'openssl_sign' ) )
            return 'the PHP extension openssl is missing';
        return '';
    }

    /** @return bool whether the GitHub integration can be used here (the feature check) */
    public static function available()
    {
        return self::unavailable() === '';
    }

    /**
     * The owner and repository of a GitHub address. Pure.
     *
     * @param string $remote https://github.com/owner/repo(.git), git@github.com:owner/repo.git, ssh://git@github.com/owner/repo
     * @return array|null owner, repo; null for an address not on github.com
     */
    public static function parseRemote( $remote )
    {
        $remote = trim( (string)$remote );
        if ( !preg_match( '#^(?:https?://(?:[^/@]+@)?github\.com/|ssh://git@github\.com/|git@github\.com:)([A-Za-z0-9](?:[A-Za-z0-9-]{0,38}))/([A-Za-z0-9._-]{1,100}?)(?:\.git)?/?$#i', $remote, $m ) )
            return null;
        return array( 'owner' => $m[1], 'repo' => $m[2] );
    }

    /**
     * The address GitHub Pages serves a repository at, without a custom domain. Pure.
     *
     * @param string $owner
     * @param string $repo
     * @return string https://owner.github.io/repo/, or https://owner.github.io/ for the repository owner.github.io
     */
    public static function pagesUrl( $owner, $repo )
    {
        $host = strtolower( (string)$owner ) . '.github.io';
        return strtolower( (string)$repo ) === $host ? "https://$host/" : "https://$host/$repo/";
    }

    /**
     * The base path GitHub Pages serves a repository below, without a custom domain. Pure.
     *
     * @return string /repo/, or '' for owner.github.io
     */
    public static function pagesBasePath( $owner, $repo )
    {
        return strtolower( (string)$repo ) === strtolower( (string)$owner ) . '.github.io' ? '' : '/' . $repo . '/';
    }

    /**
     * The GitHub client of a target: signed in with its credential. Null, with the reason, when the target cannot
     * call the API.
     *
     * @param expStaticTarget $target
     * @param string $problem why not
     * @param array $options as the constructor's, and credentials (an expStaticCredentials instead of the target's)
     * @return expStaticGitHub|null
     */
    public static function forTarget( expStaticTarget $target, &$problem = '', array $options = array() )
    {
        $problem = self::unavailable();
        if ( $problem !== '' && empty( $options['http'] ) )
            return null;
        $problem = '';
        if ( self::parseRemote( (string)$target->setting( 'Remote' ) ) === null )
        {
            $problem = 'the target\'s Remote is not a GitHub repository (https://github.com/owner/repo.git)';
            return null;
        }
        $credentials = isset( $options['credentials'] ) && $options['credentials'] instanceof expStaticCredentials
                     ? $options['credentials'] : new expStaticCredentials( (string)$target->setting( 'Credential' ) );
        unset( $options['credentials'] );
        if ( !in_array( $credentials->kind(), array( 'token', 'app' ), true ) )
        {
            $problem = 'the GitHub API needs a token or a GitHub App (Credential=token:<secret> or app:<installation>); a deploy key or none only pushes';
            return null;
        }
        $problem = $credentials->problem( $target );
        if ( $problem !== '' )
            return null;
        return new self( $credentials->apiToken(), $options );
    }

    /**
     * Replaces the token in a text. Pure apart from the token.
     *
     * @param string $text
     * @return string
     */
    public function redact( $text )
    {
        $text = (string)$text;
        return $this->token !== '' ? str_replace( $this->token, '***', $text ) : $text;
    }

    /**
     * One API request.
     *
     * @param string $method GET, POST, PUT, PATCH, DELETE
     * @param string $path /repos/owner/repo (or a full https:// address)
     * @param array|null $body sent as JSON
     * @param array $headers more headers (name => value); Authorization is replaced only when given
     * @return array status (0: no answer), data (the decoded JSON, or null), error ('' or why), headers (lower case names)
     */
    public function request( $method, $path, $body = null, array $headers = array() )
    {
        $url = preg_match( '#^https://#i', (string)$path ) ? (string)$path : $this->options['api'] . '/' . ltrim( (string)$path, '/' );
        $headers += array(
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => self::API_VERSION,
            'User-Agent' => $this->options['user_agent'],
        );
        if ( !isset( $headers['Authorization'] ) && $this->token !== '' )
            $headers['Authorization'] = 'Bearer ' . $this->token;
        $json = $body === null ? null : json_encode( $body, JSON_UNESCAPED_SLASHES );
        if ( $json !== null )
            $headers['Content-Type'] = 'application/json';
        $answer = is_callable( $this->options['http'] )
                ? (array)call_user_func( $this->options['http'], strtoupper( $method ), $url, $headers, $json )
                : self::curl( strtoupper( $method ), $url, $headers, $json, (int)$this->options['timeout'] );
        $answer += array( 'status' => 0, 'headers' => array(), 'body' => '', 'error' => '' );
        $data = $answer['body'] !== '' ? json_decode( (string)$answer['body'], true ) : null;
        $error = (string)$answer['error'];
        if ( $error === '' && ( $answer['status'] < 200 || $answer['status'] >= 300 ) )
            $error = 'GitHub answered ' . (int)$answer['status'] . ( is_array( $data ) && isset( $data['message'] ) ? ': ' . $data['message'] : '' );
        return array( 'status' => (int)$answer['status'], 'data' => $data, 'error' => $this->redact( $error ), 'headers' => (array)$answer['headers'] );
    }

    /** A request over curl: status, headers, body, error. */
    protected static function curl( $method, $url, array $headers, $body, $timeout = 30 )
    {
        $lines = array();
        foreach ( $headers as $name => $value )
            $lines[] = $name . ': ' . $value;
        $received = array();
        $ch = curl_init( $url );
        $options = array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $lines,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => min( 10, max( 1, (int)$timeout ) ),
            CURLOPT_TIMEOUT => max( 1, (int)$timeout ),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_HEADERFUNCTION => function ( $handle, $line ) use ( &$received )
            {
                if ( strpos( $line, ':' ) !== false )
                {
                    list( $name, $value ) = explode( ':', $line, 2 );
                    $received[strtolower( trim( $name ) )] = trim( $value );
                }
                return strlen( $line );
            },
        );
        if ( $body !== null )
            $options[CURLOPT_POSTFIELDS] = $body;
        $proxy = class_exists( 'eZINI' ) ? (string)eZINI::instance()->variable( 'ProxySettings', 'ProxyServer' ) : '';
        if ( $proxy !== '' )
            $options[CURLOPT_PROXY] = $proxy;
        curl_setopt_array( $ch, $options );
        $out = curl_exec( $ch );
        $answer = array( 'status' => (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE ), 'headers' => $received,
                         'body' => is_string( $out ) ? $out : '', 'error' => $out === false ? 'no answer from GitHub: ' . curl_error( $ch ) : '' );
        if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
        return $answer;
    }

    /** @return array|null the signed-in account (login, type), null when the token is refused or belongs to an App installation */
    public function account()
    {
        $r = $this->request( 'GET', '/user' );
        return $r['status'] === 200 && is_array( $r['data'] ) ? $r['data'] : null;
    }

    /**
     * A repository.
     *
     * @return array status (200, 404 ...), repo (name, private, visibility, default_branch, html_url, ...) or null, error
     */
    public function repository( $owner, $repo )
    {
        $r = $this->request( 'GET', '/repos/' . rawurlencode( $owner ) . '/' . rawurlencode( $repo ) );
        return array( 'status' => $r['status'], 'repo' => $r['status'] === 200 ? $r['data'] : null, 'error' => $r['error'] );
    }

    /**
     * Creates a repository, empty (no README: the first publish is its first commit), for the signed-in account or,
     * when the owner is not that account, for the organization of that name.
     *
     * @param string $owner
     * @param string $repo
     * @param bool $private
     * @param string $description
     * @return array ok, repo, error
     */
    public function createRepository( $owner, $repo, $private, $description = '' )
    {
        $account = $this->account();
        $path = $account !== null && strcasecmp( (string)$account['login'], (string)$owner ) === 0 ? '/user/repos' : '/orgs/' . rawurlencode( $owner ) . '/repos';
        $r = $this->request( 'POST', $path, array( 'name' => (string)$repo, 'private' => (bool)$private, 'auto_init' => false,
                                                   'description' => (string)$description, 'has_wiki' => false, 'has_projects' => false ) );
        return array( 'ok' => $r['status'] === 201, 'repo' => $r['status'] === 201 ? $r['data'] : null, 'error' => $r['error'] );
    }

    /**
     * The Pages site of a repository.
     *
     * @return array status (200, 404 when Pages is off), pages (build_type, source, cname, https_enforced, html_url, status, ...), error
     */
    public function pages( $owner, $repo )
    {
        $r = $this->request( 'GET', '/repos/' . rawurlencode( $owner ) . '/' . rawurlencode( $repo ) . '/pages' );
        return array( 'status' => $r['status'], 'pages' => $r['status'] === 200 ? $r['data'] : null, 'error' => $r['status'] === 404 ? '' : $r['error'] );
    }

    /**
     * The body that sets the delivery of Pages. Pure.
     *
     * @param string $delivery branch or actions
     * @param string $branch
     * @param string $folder '' or docs
     * @return array
     */
    public static function pagesSource( $delivery, $branch, $folder )
    {
        if ( $delivery === 'actions' )
            return array( 'build_type' => 'workflow' );
        return array( 'build_type' => 'legacy', 'source' => array( 'branch' => (string)$branch, 'path' => trim( (string)$folder, '/' ) === '' ? '/' : '/' . trim( (string)$folder, '/' ) ) );
    }

    /**
     * Switches Pages on, or changes how it is delivered when it is on already.
     *
     * @return array ok, created (bool), error
     */
    public function configurePages( $owner, $repo, $delivery, $branch, $folder )
    {
        $path = '/repos/' . rawurlencode( $owner ) . '/' . rawurlencode( $repo ) . '/pages';
        $body = self::pagesSource( $delivery, $branch, $folder );
        $current = $this->pages( $owner, $repo );
        if ( $current['status'] === 404 )
        {
            $r = $this->request( 'POST', $path, $body );
            return array( 'ok' => $r['status'] === 201, 'created' => true, 'error' => $r['error'] );
        }
        if ( $current['status'] !== 200 )
            return array( 'ok' => false, 'created' => false, 'error' => $current['error'] );
        $r = $this->request( 'PUT', $path, $body );
        return array( 'ok' => $r['status'] === 204, 'created' => false, 'error' => $r['error'] );
    }

    /**
     * Sets the custom domain ('' removes it) and, when asked, HTTPS. GitHub can only enforce HTTPS once its
     * certificate for the domain is issued; until then the answer says so and the setting is left for later.
     *
     * @param string $domain
     * @param bool|null $https null leaves it as it is
     * @return array ok, error, https_pending (bool: the certificate is not there yet)
     */
    public function setDomain( $owner, $repo, $domain, $https = null )
    {
        $path = '/repos/' . rawurlencode( $owner ) . '/' . rawurlencode( $repo ) . '/pages';
        $r = $this->request( 'PUT', $path, array( 'cname' => $domain === '' ? null : (string)$domain ) );
        if ( $r['status'] !== 204 )
            return array( 'ok' => false, 'error' => $r['error'], 'https_pending' => false );
        if ( $https === null )
            return array( 'ok' => true, 'error' => '', 'https_pending' => false );
        $h = $this->request( 'PUT', $path, array( 'https_enforced' => (bool)$https ) );
        if ( $h['status'] === 204 )
            return array( 'ok' => true, 'error' => '', 'https_pending' => false );
        // 404 or 422 while the certificate is being issued
        return array( 'ok' => true, 'error' => '', 'https_pending' => true, 'https_error' => $h['error'] );
    }

    /**
     * The state of the last Pages build: of the branch build, or of the workflow run with Actions.
     *
     * @return array status (built, building, queued, errored, none, unknown), url, commit, error, at
     */
    public function buildStatus( $owner, $repo, $delivery = 'branch' )
    {
        $base = '/repos/' . rawurlencode( $owner ) . '/' . rawurlencode( $repo );
        if ( $delivery === 'actions' )
        {
            $r = $this->request( 'GET', $base . '/actions/workflows/' . rawurlencode( basename( self::WORKFLOW_FILE ) ) . '/runs?per_page=1' );
            if ( $r['status'] === 404 )
                return array( 'status' => 'none', 'url' => '', 'commit' => '', 'error' => '', 'at' => '' );
            if ( $r['status'] !== 200 || !is_array( $r['data'] ) )
                return array( 'status' => 'unknown', 'url' => '', 'commit' => '', 'error' => $r['error'], 'at' => '' );
            $run = isset( $r['data']['workflow_runs'][0] ) ? $r['data']['workflow_runs'][0] : null;
            if ( $run === null )
                return array( 'status' => 'none', 'url' => '', 'commit' => '', 'error' => '', 'at' => '' );
            return array( 'status' => self::runState( (string)$run['status'], isset( $run['conclusion'] ) ? (string)$run['conclusion'] : '' ),
                          'url' => (string)$run['html_url'], 'commit' => (string)$run['head_sha'], 'error' => '', 'at' => (string)$run['updated_at'] );
        }
        $r = $this->request( 'GET', $base . '/pages/builds/latest' );
        if ( $r['status'] === 404 )
            return array( 'status' => 'none', 'url' => '', 'commit' => '', 'error' => '', 'at' => '' );
        if ( $r['status'] !== 200 || !is_array( $r['data'] ) )
            return array( 'status' => 'unknown', 'url' => '', 'commit' => '', 'error' => $r['error'], 'at' => '' );
        $d = $r['data'];
        $status = isset( $d['status'] ) ? (string)$d['status'] : 'unknown';
        return array( 'status' => in_array( $status, array( 'built', 'building', 'queued', 'errored' ), true ) ? $status : 'unknown',
                      'url' => '', 'commit' => isset( $d['commit'] ) ? (string)$d['commit'] : '',
                      'error' => isset( $d['error']['message'] ) && $d['error']['message'] ? (string)$d['error']['message'] : '',
                      'at' => isset( $d['updated_at'] ) ? (string)$d['updated_at'] : '' );
    }

    /** A workflow run's status and conclusion as a Pages build state. Pure. */
    public static function runState( $status, $conclusion )
    {
        if ( $status !== 'completed' )
            return $status === 'queued' || $status === 'waiting' || $status === 'pending' || $status === 'requested' ? 'queued' : 'building';
        return $conclusion === 'success' ? 'built' : 'errored';
    }

    /**
     * Everything the administration and exp:static:github status show about a target on GitHub.
     *
     * @return array remote (owner, repo), repo (exists, private, visibility, html_url, default_branch), pages (enabled,
     *         build_type, branch, folder, cname, https_enforced, certificate, html_url), build (buildStatus()), errors, warnings
     */
    public function status( expStaticTarget $target )
    {
        $out = array( 'remote' => self::parseRemote( (string)$target->setting( 'Remote' ) ), 'repo' => array( 'exists' => false ),
                      'pages' => array( 'enabled' => false ), 'build' => null, 'errors' => array(), 'warnings' => array() );
        if ( $out['remote'] === null )
        {
            $out['errors'][] = 'the target\'s Remote is not a GitHub repository';
            return $out;
        }
        list( $owner, $repo ) = array( $out['remote']['owner'], $out['remote']['repo'] );
        $r = $this->repository( $owner, $repo );
        if ( $r['status'] === 404 )
        {
            $out['warnings'][] = "the repository $owner/$repo does not exist (or the credential cannot see it): exp:static:github setup {$target->id()} --create-repo";
            return $out;
        }
        if ( $r['repo'] === null )
        {
            $out['errors'][] = $r['error'];
            return $out;
        }
        $out['repo'] = array( 'exists' => true, 'private' => !empty( $r['repo']['private'] ),
                              'visibility' => isset( $r['repo']['visibility'] ) ? (string)$r['repo']['visibility'] : ( !empty( $r['repo']['private'] ) ? 'private' : 'public' ),
                              'html_url' => (string)$r['repo']['html_url'], 'default_branch' => (string)$r['repo']['default_branch'] );
        $branchWarning = self::environmentBranchWarning( $target, $out['repo']['default_branch'], "$owner/$repo" );
        if ( $branchWarning !== '' )
            $out['warnings'][] = $branchWarning;
        $p = $this->pages( $owner, $repo );
        if ( $p['pages'] !== null )
        {
            $d = $p['pages'];
            $out['pages'] = array( 'enabled' => true, 'build_type' => isset( $d['build_type'] ) ? (string)$d['build_type'] : 'legacy',
                                   'branch' => isset( $d['source']['branch'] ) ? (string)$d['source']['branch'] : '',
                                   'folder' => isset( $d['source']['path'] ) ? trim( (string)$d['source']['path'], '/' ) : '',
                                   'cname' => isset( $d['cname'] ) ? (string)$d['cname'] : '', 'https_enforced' => !empty( $d['https_enforced'] ),
                                   'certificate' => isset( $d['https_certificate']['state'] ) ? (string)$d['https_certificate']['state'] : '',
                                   'html_url' => isset( $d['html_url'] ) ? (string)$d['html_url'] : '' );
            $out['build'] = $this->buildStatus( $owner, $repo, $out['pages']['build_type'] === 'workflow' ? 'actions' : 'branch' );
            $out['warnings'] = array_merge( $out['warnings'], self::driftWarnings( $target, $out['pages'] ) );
        }
        else if ( $p['error'] !== '' )
            $out['errors'][] = $p['error'];
        else
            $out['warnings'][] = "GitHub Pages is off for $owner/$repo: exp:static:github setup {$target->id()}";
        if ( $out['repo']['private'] )
            $out['warnings'][] = 'the repository is private: GitHub Pages serves a private repository only on a paid plan (GitHub Pro, Team or Enterprise)';
        return $out;
    }

    /**
     * With Delivery=actions the workflow deploys from the target's branch, but the environment github-pages that
     * GitHub makes for Pages lets only the repository's default branch deploy until another branch is added to it:
     * the deployment would be refused. Pure.
     *
     * @param expStaticTarget $target
     * @param string $defaultBranch the repository's default_branch ('' when unknown: no warning)
     * @param string $repository owner/repo, for the text
     * @return string the warning, '' when there is none
     */
    public static function environmentBranchWarning( expStaticTarget $target, $defaultBranch, $repository = '' )
    {
        $branch = (string)$target->setting( 'Branch' );
        $defaultBranch = (string)$defaultBranch;
        if ( $target->setting( 'Delivery' ) !== 'actions' || $defaultBranch === '' || $branch === '' || $branch === $defaultBranch )
            return '';
        return 'Delivery=actions deploys from the branch ' . $branch . ', but the environment github-pages of ' . ( $repository !== '' ? $repository : 'the repository' )
             . ' lets only the default branch ' . $defaultBranch . ' deploy: add ' . $branch . ' in Settings > Environments > github-pages > Deployment branches,'
             . ' or publish to ' . $defaultBranch;
    }

    /**
     * Where GitHub's Pages settings differ from the target's. Pure.
     *
     * @param expStaticTarget $target
     * @param array $pages as status() returns it
     * @return string[]
     */
    public static function driftWarnings( expStaticTarget $target, array $pages )
    {
        $warnings = array();
        $actions = $target->setting( 'Delivery' ) === 'actions';
        if ( $actions !== ( $pages['build_type'] === 'workflow' ) )
            $warnings[] = 'GitHub Pages is delivered ' . ( $pages['build_type'] === 'workflow' ? 'by GitHub Actions' : 'from a branch' )
                        . ', the target says Delivery=' . $target->setting( 'Delivery' ) . ': exp:static:github setup ' . $target->id() . ' sets it';
        else if ( !$actions && ( $pages['branch'] !== $target->setting( 'Branch' ) || $pages['folder'] !== trim( (string)$target->setting( 'Folder' ), '/' ) ) )
            $warnings[] = 'GitHub Pages serves ' . $pages['branch'] . ( $pages['folder'] !== '' ? ' /' . $pages['folder'] : '' ) . ', the target publishes to '
                        . $target->setting( 'Branch' ) . ( $target->setting( 'Folder' ) !== '' ? ' /' . trim( (string)$target->setting( 'Folder' ), '/' ) : '' );
        if ( strcasecmp( (string)$pages['cname'], (string)$target->setting( 'CustomDomain' ) ) !== 0 )
            $warnings[] = 'GitHub Pages has the custom domain "' . $pages['cname'] . '", the target "' . $target->setting( 'CustomDomain' ) . '"';
        return $warnings;
    }

    /**
     * Sets a target up on GitHub: the repository (created when asked and missing), Pages from the branch or Actions,
     * the custom domain and HTTPS. Each step is reported; a step that fails ends the setup.
     *
     * @param expStaticTarget $target
     * @param array $options create (bool: create a missing repository), private (bool), pages (bool, default true),
     *        https (bool|null: enforce HTTPS with a custom domain, default true), dry_run (bool: only say what would be done)
     * @return array steps (list of step, state: done, unchanged, planned, failed, skipped, message), errors, warnings
     */
    public function setup( expStaticTarget $target, array $options = array() )
    {
        $options += array( 'create' => false, 'private' => false, 'pages' => true, 'https' => true, 'dry_run' => false );
        $out = array( 'steps' => array(), 'errors' => array(), 'warnings' => array() );
        $step = function ( $name, $state, $message ) use ( &$out )
        {
            $out['steps'][] = array( 'step' => $name, 'state' => $state, 'message' => $message );
            if ( $state === 'failed' )
                $out['errors'][] = $message;
        };
        $remote = self::parseRemote( (string)$target->setting( 'Remote' ) );
        if ( $remote === null )
        {
            $step( 'repository', 'failed', 'the target\'s Remote is not a GitHub repository (https://github.com/owner/repo.git)' );
            return $out;
        }
        list( $owner, $repo ) = array( $remote['owner'], $remote['repo'] );
        $r = $this->repository( $owner, $repo );
        if ( $r['status'] === 200 )
        {
            $step( 'repository', 'unchanged', "$owner/$repo exists (" . ( !empty( $r['repo']['private'] ) ? 'private' : 'public' ) . ')' );
            if ( !empty( $r['repo']['private'] ) )
                $out['warnings'][] = 'the repository is private: GitHub Pages serves a private repository only on a paid plan';
            $branchWarning = self::environmentBranchWarning( $target, isset( $r['repo']['default_branch'] ) ? (string)$r['repo']['default_branch'] : '', "$owner/$repo" );
            if ( $branchWarning !== '' )
                $out['warnings'][] = $branchWarning;
        }
        else if ( $r['status'] === 404 && $options['create'] )
        {
            if ( $options['dry_run'] )
                $step( 'repository', 'planned', "create $owner/$repo (" . ( $options['private'] ? 'private' : 'public' ) . ')' );
            else
            {
                $c = $this->createRepository( $owner, $repo, $options['private'], 'Static copy of ' . ( $target->setting( 'Name' ) !== '' ? $target->setting( 'Name' ) : $target->id() ) . ', published with Exponential' );
                if ( !$c['ok'] )
                {
                    $step( 'repository', 'failed', "$owner/$repo could not be created: " . $c['error'] );
                    return $out;
                }
                $step( 'repository', 'done', "created $owner/$repo (" . ( $options['private'] ? 'private' : 'public' ) . ')' );
            }
            if ( $options['private'] )
                $out['warnings'][] = 'the repository is private: GitHub Pages serves a private repository only on a paid plan';
        }
        else if ( $r['status'] === 404 )
        {
            $step( 'repository', 'failed', "$owner/$repo does not exist (or the credential cannot see it): create it, or ask for it to be created (--create-repo)" );
            return $out;
        }
        else
        {
            $step( 'repository', 'failed', $r['error'] );
            return $out;
        }

        $delivery = (string)$target->setting( 'Delivery' );
        $branch = (string)$target->setting( 'Branch' );
        $folder = trim( (string)$target->setting( 'Folder' ), '/' );
        $where = $delivery === 'actions' ? 'GitHub Actions' : 'the branch ' . $branch . ( $folder !== '' ? ' /' . $folder : '' );
        if ( !$options['pages'] )
            $step( 'pages', 'skipped', 'GitHub Pages is left as it is' );
        else if ( $options['dry_run'] || $r['status'] === 404 )
            // a repository that is created gets Pages only once its first push made the branch
            $step( 'pages', 'planned', "switch GitHub Pages on from $where" . ( $r['status'] === 404 && !$options['dry_run'] ? ' (after the first publish: run this again)' : '' ) );
        else
        {
            $p = $this->configurePages( $owner, $repo, $delivery, $branch, $folder );
            if ( !$p['ok'] )
            {
                $hint = $delivery === 'branch' ? ' (the branch must exist: publish once, then run this again)' : '';
                $step( 'pages', 'failed', "GitHub Pages could not be switched on from $where: " . $p['error'] . $hint );
                return $out;
            }
            $step( 'pages', 'done', ( $p['created'] ? 'switched GitHub Pages on from ' : 'GitHub Pages is delivered from ' ) . $where );
        }

        $domain = (string)$target->setting( 'CustomDomain' );
        if ( !$options['pages'] || $r['status'] === 404 )
            $step( 'domain', 'skipped', $domain === '' ? 'no custom domain' : "the custom domain $domain is set once Pages is on" );
        else if ( $options['dry_run'] )
            $step( 'domain', 'planned', $domain === '' ? 'no custom domain' : "set the custom domain $domain" . ( $options['https'] ? ' and enforce HTTPS' : '' ) );
        else
        {
            $d = $this->setDomain( $owner, $repo, $domain, $domain !== '' && $options['https'] ? true : null );
            if ( !$d['ok'] )
            {
                $step( 'domain', 'failed', 'the custom domain could not be set: ' . $d['error'] );
                return $out;
            }
            $step( 'domain', 'done', $domain === '' ? 'no custom domain' : "the custom domain is $domain" );
            if ( $domain !== '' && $options['https'] )
                $step( 'https', $d['https_pending'] ? 'planned' : 'done', $d['https_pending']
                       ? 'HTTPS is enforced once GitHub has the certificate for the domain (up to 24 hours after the DNS is right): run this again then'
                       : 'HTTPS is enforced' );
        }
        if ( $delivery === 'actions' && !$target->hasExtra( 'workflow' ) )
            $out['warnings'][] = 'Delivery=actions: the build writes ' . self::WORKFLOW_FILE . ' itself; a token needs the Workflows permission to push it';
        return $out;
    }

    /**
     * setup() for a target, signed in with its credential, audited as system.staticpublish.target (verb github-setup).
     * The command exp:static:github setup and the administration's "Set up on GitHub" both run it.
     *
     * @param expStaticTarget $target
     * @param array $options as setup()'s, and the constructor's (http, api, credentials)
     * @return array as setup() (steps, errors, warnings), with target and available
     */
    public static function setupTarget( expStaticTarget $target, array $options = array() )
    {
        $problem = '';
        $client = self::forTarget( $target, $problem, array_intersect_key( $options, array( 'http' => 1, 'api' => 1, 'credentials' => 1 ) ) );
        if ( $client === null )
            $report = array( 'steps' => array(), 'errors' => array( $problem ), 'warnings' => array(), 'available' => false );
        else
            $report = $client->setup( $target, $options ) + array( 'available' => true );
        if ( empty( $options['dry_run'] ) )
            expStaticPublisher::audit( 'system.staticpublish.target', array(
                'object' => array( 'type' => 'static_target', 'id' => $target->id() ), 'verb' => 'github-setup',
                'result' => $report['errors'] ? 'failure' : 'success', 'reason' => $report['errors'] ? (string)reset( $report['errors'] ) : '',
                'x' => array( 'remote' => (string)$target->setting( 'Remote' ), 'delivery' => (string)$target->setting( 'Delivery' ),
                              'domain' => (string)$target->setting( 'CustomDomain' ),
                              'steps' => array_map( function ( $s ) { return $s['step'] . ':' . $s['state']; }, $report['steps'] ) ) ) );
        return array( 'target' => $target->id() ) + $report;
    }

    /**
     * status() for a target, signed in with its credential; for the administration (history and preview) and
     * exp:static:github status.
     *
     * @param expStaticTarget $target
     * @param array $options the constructor's (http, api, credentials)
     * @return array as status(), with available (bool) and, when the API cannot be used, errors holding why
     */
    public static function statusFor( expStaticTarget $target, array $options = array() )
    {
        $problem = '';
        $client = self::forTarget( $target, $problem, $options );
        if ( $client === null )
            return array( 'target' => $target->id(), 'available' => false, 'remote' => self::parseRemote( (string)$target->setting( 'Remote' ) ),
                          'repo' => array( 'exists' => false ), 'pages' => array( 'enabled' => false ), 'build' => null, 'errors' => array( $problem ), 'warnings' => array() );
        return array( 'target' => $target->id(), 'available' => true ) + $client->status( $target );
    }

    /**
     * The limits of GitHub Pages for a build, checked before every push of a github-pages target. Pure.
     *
     * @param expStaticManifest $manifest the build
     * @param array $options max_site_bytes, max_file_bytes (default GitHub's), private (bool|null: the repository is private)
     * @return array errors (the push is refused), warnings, bytes, too_large (paths)
     */
    public static function limits( expStaticManifest $manifest, array $options = array() )
    {
        $options += array( 'max_site_bytes' => self::SITE_BYTES, 'max_file_bytes' => self::FILE_BYTES, 'private' => null );
        $out = array( 'errors' => array(), 'warnings' => array(), 'bytes' => $manifest->bytes(), 'too_large' => array() );
        $maxSite = (int)$options['max_site_bytes'];
        $maxFile = (int)$options['max_file_bytes'];
        $large = array();
        foreach ( $manifest->entries() as $path => $entry )
        {
            if ( $maxFile > 0 && $entry['size'] > $maxFile )
                $out['too_large'][] = $path;
            else if ( $entry['size'] > self::FILE_WARN_BYTES )
                $large[] = $path;
        }
        if ( $maxSite > 0 && $out['bytes'] > $maxSite )
            $out['errors'][] = sprintf( 'the build is %s, more than the %s GitHub Pages allows a site', expStaticBuild::bytes( $out['bytes'] ), expStaticBuild::bytes( $maxSite ) );
        else if ( $maxSite > 0 && $out['bytes'] > $maxSite * 0.9 )
            $out['warnings'][] = sprintf( 'the build is %s, more than 90 %% of the %s GitHub Pages allows a site', expStaticBuild::bytes( $out['bytes'] ), expStaticBuild::bytes( $maxSite ) );
        if ( $out['too_large'] )
            $out['errors'][] = sprintf( '%d file(s) are larger than the %s GitHub allows a file: %s', count( $out['too_large'] ), expStaticBuild::bytes( $maxFile ),
                                        implode( ', ', array_slice( $out['too_large'], 0, 5 ) ) );
        if ( $large )
            $out['warnings'][] = sprintf( '%d file(s) are larger than 50 MB, which GitHub warns about: %s', count( $large ), implode( ', ', array_slice( $large, 0, 5 ) ) );
        if ( $options['private'] === true )
            $out['warnings'][] = 'the repository is private: GitHub Pages serves a private repository only on a paid plan';
        return $out;
    }

    /**
     * The workflow file of the Actions delivery: it deploys the files of the branch as they are pushed. Pure.
     *
     * @param string $branch
     * @param string $folder the folder of the build in the branch ('' for the root)
     * @return string YAML
     */
    public static function workflow( $branch, $folder = '' )
    {
        $branch = preg_replace( '#[^A-Za-z0-9._/-]#', '', (string)$branch );
        $folder = trim( preg_replace( '#[^A-Za-z0-9._/-]#', '', (string)$folder ), '/' );
        return "# Written by Exponential static publishing: deploys the static copy pushed to the branch\n"
             . "# $branch to GitHub Pages. It builds nothing; the files are published as they are.\n"
             . "# Each publish rewrites this file: change the target's settings instead.\n"
             . "name: Deploy the static site to GitHub Pages\n"
             . "on:\n"
             . "  push:\n"
             . "    branches: [\"$branch\"]\n"
             . "  workflow_dispatch:\n"
             . "permissions:\n"
             . "  contents: read\n"
             . "  pages: write\n"
             . "  id-token: write\n"
             . "concurrency:\n"
             . "  group: pages\n"
             . "  cancel-in-progress: false\n"
             . "jobs:\n"
             . "  deploy:\n"
             . "    environment:\n"
             . "      name: github-pages\n"
             . "      url: \${{ steps.deployment.outputs.page_url }}\n"
             . "    runs-on: ubuntu-latest\n"
             . "    steps:\n"
             . "      - uses: actions/checkout@v4\n"
             . "      - uses: actions/configure-pages@v5\n"
             . "      - uses: actions/upload-pages-artifact@v3\n"
             . "        with:\n"
             . "          path: " . ( $folder === '' ? '.' : $folder ) . "\n"
             . "      - id: deployment\n"
             . "        uses: actions/deploy-pages@v4\n";
    }

    // ---- GitHub App and OAuth ------------------------------------------------------------------------------------

    /** base64url without padding. Pure. */
    public static function base64url( $data )
    {
        return rtrim( strtr( base64_encode( (string)$data ), '+/', '-_' ), '=' );
    }

    /**
     * The JSON Web Token a GitHub App signs its requests for installation tokens with (RS256, 9 minutes).
     *
     * @param string $appId the App's id (or client id)
     * @param string $privateKey the App's private key (PEM)
     * @param int|null $now
     * @return string|null null when the key cannot sign
     */
    public static function appJwt( $appId, $privateKey, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $key = openssl_pkey_get_private( (string)$privateKey );
        if ( $key === false )
            return null;
        $input = self::base64url( json_encode( array( 'alg' => 'RS256', 'typ' => 'JWT' ) ) ) . '.'
               . self::base64url( json_encode( array( 'iat' => $now - 60, 'exp' => $now + 540, 'iss' => (string)$appId ) ) );
        $signature = '';
        if ( !openssl_sign( $input, $signature, $key, OPENSSL_ALGO_SHA256 ) )
            return null;
        return $input . '.' . self::base64url( $signature );
    }

    /**
     * An installation token of the GitHub App ([StaticPublishSettings] GitHubAppId, and the secret named by
     * GitHubAppKey with its private key), kept for the process until a minute before it expires. It is the provider
     * of Credential=app:<installation>.
     *
     * @param string $installation the installation id
     * @param array $options app_id, private_key (instead of the settings), http, api, now (tests)
     * @param string $problem why there is none
     * @return string|null
     */
    public static function appToken( $installation, array $options = array(), &$problem = '' )
    {
        $installation = trim( (string)$installation );
        $now = isset( $options['now'] ) ? (int)$options['now'] : time();
        $problem = '';
        if ( !ctype_digit( $installation ) )
        {
            $problem = "app:$installation is not an installation id (a number: the end of the App's installation address on GitHub)";
            return null;
        }
        if ( isset( self::$appTokens[$installation] ) && self::$appTokens[$installation]['expires'] > $now + 60 && !isset( $options['http'] ) )
            return self::$appTokens[$installation]['token'];
        $appId = isset( $options['app_id'] ) ? (string)$options['app_id'] : self::setting( 'GitHubAppId', '' );
        $keyName = self::setting( 'GitHubAppKey', '' );
        $pem = isset( $options['private_key'] ) ? (string)$options['private_key'] : ( $keyName !== '' && class_exists( 'expSecrets' ) ? (string)expSecrets::get( $keyName ) : '' );
        if ( $appId === '' || $pem === '' )
        {
            $problem = 'the GitHub App is not set up: staticcache.ini [StaticPublishSettings] GitHubAppId and GitHubAppKey (the secret with its private key)';
            return null;
        }
        $jwt = self::appJwt( $appId, $pem, $now );
        if ( $jwt === null )
        {
            $problem = 'the private key of the GitHub App cannot sign (GitHubAppKey must name a secret holding the PEM key GitHub made)';
            return null;
        }
        $client = new self( '', array_intersect_key( $options, array( 'http' => 1, 'api' => 1 ) ) );
        $r = $client->request( 'POST', '/app/installations/' . $installation . '/access_tokens', null, array( 'Authorization' => 'Bearer ' . $jwt ) );
        if ( $r['status'] !== 201 || !isset( $r['data']['token'] ) )
        {
            $problem = 'the GitHub App gave no token for installation ' . $installation . ': ' . str_replace( $jwt, '***', $r['error'] );
            return null;
        }
        $expires = isset( $r['data']['expires_at'] ) ? (int)strtotime( (string)$r['data']['expires_at'] ) : $now + 3600;
        self::$appTokens[$installation] = array( 'token' => (string)$r['data']['token'], 'expires' => $expires );
        return (string)$r['data']['token'];
    }

    /** @return string why the provider registered by register() gave no token the last time ('' when it gave one) */
    public static function lastProblem()
    {
        return self::$lastProblem;
    }

    /** Forgets the App tokens of this process (tests). */
    public static function resetAppTokens()
    {
        self::$appTokens = array();
    }

    /**
     * Registers appToken() as the provider of Credential=app:<installation>, when the integration is available.
     *
     * @return bool whether it was registered
     */
    public static function register()
    {
        if ( !self::available() )
            return false;
        expStaticCredentials::provider( 'app', function ( $installation, $target )
        {
            $problem = '';
            $token = expStaticGitHub::appToken( $installation, array(), $problem );
            self::$lastProblem = $problem;
            if ( $token === null && $problem !== '' && class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( $problem, __METHOD__ );
            return $token;
        } );
        return true;
    }

    /**
     * Starts the OAuth device flow of an OAuth App: the user opens verification_uri and types user_code.
     *
     * @param string $clientId the OAuth App's client id ([StaticPublishSettings] GitHubOAuthClientId)
     * @param array $options http (tests), scope (default repo workflow)
     * @return array device_code, user_code, verification_uri, interval, expires_in, error
     */
    public static function deviceCode( $clientId, array $options = array() )
    {
        $client = new self( '', array_intersect_key( $options, array( 'http' => 1 ) ) );
        $r = $client->request( 'POST', 'https://github.com/login/device/code',
                               array( 'client_id' => (string)$clientId, 'scope' => isset( $options['scope'] ) ? (string)$options['scope'] : 'repo workflow' ),
                               array( 'Accept' => 'application/json' ) );
        $d = is_array( $r['data'] ) ? $r['data'] : array();
        return array( 'device_code' => isset( $d['device_code'] ) ? (string)$d['device_code'] : '', 'user_code' => isset( $d['user_code'] ) ? (string)$d['user_code'] : '',
                      'verification_uri' => isset( $d['verification_uri'] ) ? (string)$d['verification_uri'] : 'https://github.com/login/device',
                      'interval' => isset( $d['interval'] ) ? max( 1, (int)$d['interval'] ) : 5, 'expires_in' => isset( $d['expires_in'] ) ? (int)$d['expires_in'] : 900,
                      'error' => isset( $d['device_code'] ) ? '' : ( isset( $d['error_description'] ) ? (string)$d['error_description'] : $r['error'] ) );
    }

    /**
     * One poll of the device flow.
     *
     * @return array token ('' while the user has not confirmed), state (done, pending, slow_down, failed), error
     */
    public static function deviceToken( $clientId, $deviceCode, array $options = array() )
    {
        $client = new self( '', array_intersect_key( $options, array( 'http' => 1 ) ) );
        $r = $client->request( 'POST', 'https://github.com/login/oauth/access_token',
                               array( 'client_id' => (string)$clientId, 'device_code' => (string)$deviceCode, 'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code' ),
                               array( 'Accept' => 'application/json' ) );
        $d = is_array( $r['data'] ) ? $r['data'] : array();
        if ( isset( $d['access_token'] ) && $d['access_token'] !== '' )
            return array( 'token' => (string)$d['access_token'], 'state' => 'done', 'error' => '' );
        $error = isset( $d['error'] ) ? (string)$d['error'] : '';
        if ( $error === 'authorization_pending' || $error === 'slow_down' )
            return array( 'token' => '', 'state' => $error === 'slow_down' ? 'slow_down' : 'pending', 'error' => '' );
        return array( 'token' => '', 'state' => 'failed', 'error' => isset( $d['error_description'] ) ? (string)$d['error_description'] : ( $error !== '' ? $error : $r['error'] ) );
    }
}
