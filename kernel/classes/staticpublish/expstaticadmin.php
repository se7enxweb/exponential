<?php
/**
 * File containing the expStaticAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the administration of static publishing (Setup > Static publishing, module staticpublish) shares: the policies
 * with their Target limitation, the wizard's settings and their write through expIniEditor into
 * settings/override/staticcache.ini.append.php, the preview diff with sizes, the limits check, the signed preview
 * route, the deploy key and the dashboard block.
 *
 * Policies: staticpublish/read (see the targets, their builds, previews, history and logs), build (build and throw a
 * build away), publish (publish and roll back), setup (make and change targets, their deploy key, switch the feature
 * on or off). Each takes the limitation Target, so an editor can be allowed one target. Publishing, rolling back,
 * saving a target, the deploy key and the switch ask "Verify it's you" (site.ini [ReauthGroup_system]).
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticAdmin
{
    /** The policy functions of the module */
    const FUNCTIONS = array( 'read', 'build', 'publish', 'setup' );

    /** GitHub Pages: a site, a file (the defaults of MaxSiteBytes and MaxFileBytes) */
    const GITHUB_SITE_BYTES = 1000000000;
    const GITHUB_FILE_BYTES = 100000000;

    /** The settings the wizard writes, in the order of its steps */
    const WIZARD_SETTINGS = array( 'Name', 'SiteAccess', 'SiteAccessList', 'DefaultSiteAccess', 'Kind', 'Credential', 'Remote', 'Branch', 'Folder', 'Delivery', 'Links', 'BasePath',
                                   'PublicURL', 'CustomDomain', 'Assets', 'Extras', 'PostProcess', 'Search', 'Forms', 'ExcludePaths',
                                   'ExcludeSections', 'CrawlURL', 'LiveURL', 'OutsideLinks', 'AdminLinks', 'MaxPages', 'Triggers', 'Schedule', 'Notify', 'Webhook', 'License' );

    /** The longest a preview link lives, in seconds */
    const PREVIEW_TTL = 3600;

    /** @var string|null a fixed signing key (tests) */
    protected static $testKey = null;

    // ------------------------------------------------------------------ policies

    /**
     * Whether the user may do a function, for one target or (without one) for any.
     *
     * @param string $function read, build, publish or setup
     * @param string|null $targetId
     * @param eZUser|null $user
     * @return bool
     */
    public static function canDo( $function, $targetId = null, $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        $access = $user->hasAccessTo( 'staticpublish', (string)$function );
        return self::allows( $access, $targetId );
    }

    /**
     * Whether an access result (eZUser::hasAccessTo()) allows a target. Pure.
     *
     * @param array $access accessWord yes, no or limited; policies (each a limitation array) for limited
     * @param string|null $targetId null: any target
     * @return bool
     */
    public static function allows( array $access, $targetId = null )
    {
        $word = isset( $access['accessWord'] ) ? $access['accessWord'] : 'no';
        if ( $word === 'yes' )
            return true;
        if ( $word !== 'limited' )
            return false;
        if ( $targetId === null )
            return true;
        foreach ( isset( $access['policies'] ) ? (array)$access['policies'] : array() as $limitations )
        {
            if ( !is_array( $limitations ) || !isset( $limitations['Target'] ) )
                return true;
            if ( in_array( (string)$targetId, array_map( 'strval', (array)$limitations['Target'] ), true ) )
                return true;
        }
        return false;
    }

    /**
     * Whether the user may do a function without a Target limitation (making a new target, the switch).
     *
     * @param string $function
     * @param eZUser|null $user
     * @return bool
     */
    public static function canDoAll( $function, $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        $access = $user->hasAccessTo( 'staticpublish', (string)$function );
        if ( isset( $access['accessWord'] ) && $access['accessWord'] === 'yes' )
            return true;
        if ( !isset( $access['accessWord'] ) || $access['accessWord'] !== 'limited' )
            return false;
        foreach ( isset( $access['policies'] ) ? (array)$access['policies'] : array() as $limitations )
            if ( !is_array( $limitations ) || !isset( $limitations['Target'] ) )
                return true;
        return false;
    }

    /**
     * The values of the limitation Target (module.php): every target of TargetList[].
     *
     * @return array[] name, id
     */
    public static function limitationList()
    {
        $out = array();
        foreach ( expStaticTarget::all() as $target )
            $out[] = array( 'name' => $target->name() !== '' ? $target->name() . ' (' . $target->id() . ')' : $target->id(), 'id' => $target->id() );
        return $out;
    }

    /**
     * What the user may do with a target.
     *
     * @param string $targetId
     * @return array read, build, publish, setup (bool each)
     */
    public static function permissions( $targetId )
    {
        $out = array();
        foreach ( self::FUNCTIONS as $function )
            $out[$function] = self::canDo( $function, $targetId );
        return $out;
    }

    // ------------------------------------------------------------------ targets and their state

    /** @return expStaticPublisher the publisher of a target, rooted at the installation */
    public static function publisher( expStaticTarget $target, array $options = array() )
    {
        $db = null;
        try
        {
            $db = eZDB::instance();
        }
        catch ( Throwable $e )
        {
        }
        return new expStaticPublisher( $target, $options + array( 'root' => rtrim( (string)eZSys::rootDir(), '/' ), 'db' => $db, 'notify' => true ) );
    }

    /**
     * A target as the dashboard and the target pages show it: its settings, the last publish, the stale pages, the
     * build not published, the job going on, the size against the limits.
     *
     * @param expStaticTarget $target
     * @return array
     */
    public static function overview( expStaticTarget $target )
    {
        $publisher = self::publisher( $target );
        $db = null;
        try
        {
            $db = eZDB::instance();
        }
        catch ( Throwable $e )
        {
        }
        $runs = expStaticStatus::runs( $db, $target->id(), 1 );
        $stale = 0;
        foreach ( expStaticStatus::pages( $db, $target->id() ) as $page )
            if ( !empty( $page['stale'] ) )
                $stale++;
        $state = array( 'repository' => false, 'uncommitted' => 0, 'last_tag' => '', 'last_tag_commit' => '', 'unpushed' => null );
        try
        {
            $state = $publisher->repository()->state() + $state;
        }
        catch ( Throwable $e )
        {
        }
        $built = expStaticManifest::load( rtrim( $publisher->checkoutDirectory() . '/' . $target->folder(), '/' ) );
        $context = expStaticTarget::siteAccessContext();
        $validation = $target->validate( $context );
        return array(
            'id' => $target->id(),
            'name' => $target->name() !== '' ? $target->name() : $target->id(),
            'siteaccess' => $target->siteAccess(),
            'siteaccesses' => self::memberList( $target, $context ),
            'kind' => (string)$target->setting( 'Kind' ),
            'remote' => (string)$target->setting( 'Remote' ),
            'branch' => (string)$target->setting( 'Branch' ),
            'public_url' => $target->publicUrl(),
            'credential' => (string)$target->setting( 'Credential' ),
            'last_run' => $runs ? $runs[0] : null,
            'stale' => $stale,
            'pending_build' => (int)$state['uncommitted'] > 0 || ( empty( $state['repository'] ) && $built !== null ),
            'last_tag' => (string)$state['last_tag'],
            'version' => self::versionNumber( (string)$state['last_tag'], $target->id() ),
            'commit' => (string)$state['last_tag_commit'],
            'commit_short' => self::shortHash( (string)$state['last_tag_commit'] ),
            'commit_url' => self::commitUrl( (string)$target->setting( 'Remote' ) ),
            'deploy' => self::deployState( $runs ? $runs[0] : null ),
            'unpushed' => $state['unpushed'],
            'bytes' => $built !== null ? $built->bytes() : 0,
            'pages' => $built !== null ? $built->count( 'page' ) : 0,
            'limit_percent' => $built !== null ? self::percent( $built->bytes(), self::siteLimit() ) : 0,
            'job' => expStaticJob::running( $publisher->workDirectory(), $target->id() ),
            'errors' => $validation['errors'],
            'warnings' => $validation['warnings'],
            'permissions' => self::permissions( $target->id() ),
        );
    }

    /**
     * The siteaccesses of a target as the administration lists them: each with its folder in the copy and whether it
     * is the one at the root. Pure.
     *
     * @param expStaticTarget $target
     * @param array $context expStaticTarget::siteAccessContext()
     * @return array[] name, folder ('/' for the root, else /ger/), default (bool)
     */
    public static function memberList( expStaticTarget $target, array $context )
    {
        $out = array();
        foreach ( $target->members( $context ) as $member )
            $out[] = array( 'name' => $member['siteaccess'], 'folder' => $member['prefix'] === '' ? '/' : '/' . $member['prefix'] . '/',
                            'default' => (bool)$member['default'] );
        return $out;
    }

    /** @return int MaxSiteBytes */
    public static function siteLimit()
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        return $ini->hasVariable( 'StaticPublishSettings', 'MaxSiteBytes' ) ? (int)$ini->variable( 'StaticPublishSettings', 'MaxSiteBytes' ) : self::GITHUB_SITE_BYTES;
    }

    /** @return int MaxFileBytes */
    public static function fileLimit()
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        return $ini->hasVariable( 'StaticPublishSettings', 'MaxFileBytes' ) ? (int)$ini->variable( 'StaticPublishSettings', 'MaxFileBytes' ) : self::GITHUB_FILE_BYTES;
    }

    /** @return int a share in percent, 0 to 100. Pure. */
    public static function percent( $part, $whole )
    {
        return $whole > 0 ? (int)min( 100, floor( max( 0, $part ) * 100 / $whole ) ) : 0;
    }

    /**
     * The version number of a publish: the n of its tag static/<target>/<n>. Pure.
     *
     * @param string $tagName
     * @param string $targetId
     * @return int 0 when there is no tag or it is not one of the target's
     */
    public static function versionNumber( $tagName, $targetId )
    {
        $tagName = trim( (string)$tagName );
        if ( $tagName === '' || ctype_digit( $tagName ) )
            return 0;
        return expStaticRepository::tagNumber( $tagName, (string)$targetId );
    }

    /**
     * The short form of a commit id, as git and GitHub show it. Pure.
     *
     * @param string $commit
     * @param int $length
     * @return string the first $length hex digits in lower case, '' for something that is no commit id
     */
    public static function shortHash( $commit, $length = 7 )
    {
        $commit = trim( (string)$commit );
        if ( !preg_match( '/^[0-9a-f]{4,64}$/i', $commit ) )
            return '';
        return strtolower( substr( $commit, 0, max( 4, (int)$length ) ) );
    }

    /**
     * The address of a commit on the host, for a remote on GitHub ('' for another host). Pure.
     *
     * @param string $remote
     * @return string https://github.com/<owner>/<repo>/commit/ (the commit id goes after it), or ''
     */
    public static function commitUrl( $remote )
    {
        if ( preg_match( '#^(?:https://github\.com/|git@github\.com:|ssh://git@github\.com/)([A-Za-z0-9_.-]+)/([A-Za-z0-9_.-]+?)(?:\.git)?/?$#', (string)$remote, $m ) )
            return 'https://github.com/' . $m[1] . '/' . $m[2] . '/commit/';
        return '';
    }

    /**
     * The deploy state of a run (a row of exp_static_run) in one word, for the dashboard. Pure.
     *
     * @param array|null $run
     * @return array state (none: no run; failed: the run failed; pushed; pending: the push is to be tried again;
     *         unchanged: nothing to publish; not-pushed: no push, or no remote), time (when it ended, else began),
     *         run_id
     */
    public static function deployState( $run )
    {
        if ( !is_array( $run ) || !isset( $run['run_id'] ) )
            return array( 'state' => 'none', 'time' => 0, 'run_id' => '' );
        $state = isset( $run['state'] ) ? (string)$run['state'] : '';
        $push = isset( $run['push_state'] ) ? (string)$run['push_state'] : '';
        if ( $state === expStaticStatus::FAILED )
            $out = 'failed';
        else if ( $push === expStaticRepository::PUSHED )
            $out = 'pushed';
        else if ( $push === expStaticRepository::PENDING )
            $out = 'pending';
        else if ( $state === expStaticStatus::UNCHANGED )
            $out = 'unchanged';
        else
            $out = 'not-pushed';
        $time = !empty( $run['finished_at'] ) ? (int)$run['finished_at'] : ( isset( $run['started_at'] ) ? (int)$run['started_at'] : 0 );
        return array( 'state' => $out, 'time' => $time, 'run_id' => (string)$run['run_id'] );
    }

    /**
     * A run as the history shows it: the row with its version number (the n of its tag, 0 without one) and the
     * short commit id. Pure.
     *
     * @param array $run a row of exp_static_run
     * @param string $targetId
     * @return array the row with version and commit_short
     */
    public static function runView( array $run, $targetId )
    {
        $run['version'] = self::versionNumber( isset( $run['tag_name'] ) ? $run['tag_name'] : '', $targetId );
        $run['commit_short'] = self::shortHash( isset( $run['commit_id'] ) ? $run['commit_id'] : '' );
        return $run;
    }

    /**
     * The targets the user may read.
     *
     * @return expStaticTarget[]
     */
    public static function readableTargets()
    {
        $out = array();
        foreach ( expStaticTarget::all() as $target )
            if ( self::canDo( 'read', $target->id() ) )
                $out[] = $target;
        return $out;
    }

    /**
     * The fetch function staticpublish/summary, for the dashboard block "staticpublish": the targets the user may read
     * with their state; empty for a user without staticpublish/read. Never throws.
     *
     * @return array result => array( enabled, targets (id, name, stale, last_run, pending_build, job), invite (no target
     *               yet), can_setup, problem )
     */
    public static function fetchSummary()
    {
        if ( !self::canDo( 'read' ) )
            return array( 'result' => array() );
        $summary = array( 'enabled' => false, 'targets' => array(), 'invite' => false, 'can_setup' => false, 'problem' => '' );
        try
        {
            $summary['enabled'] = expStaticTarget::enabled();
            $summary['can_setup'] = self::canDoAll( 'setup' );
            foreach ( self::readableTargets() as $target )
            {
                $o = self::overview( $target );
                $summary['targets'][] = array( 'id' => $o['id'], 'name' => $o['name'], 'stale' => $o['stale'], 'last_run' => $o['last_run'],
                                               'pending_build' => $o['pending_build'], 'job' => $o['job'] );
            }
            $summary['invite'] = !$summary['targets'] && expStaticTarget::ids() === array();
        }
        catch ( Throwable $e )
        {
            $summary['problem'] = $e->getMessage();
        }
        return array( 'result' => $summary );
    }

    // ------------------------------------------------------------------ the wizard

    /**
     * The settings of a new target as the wizard starts it: DEFAULTS with the first front siteaccess.
     *
     * @param array $context expStaticTarget::siteAccessContext()
     * @return array
     */
    public static function newSettings( array $context )
    {
        $settings = expStaticTarget::DEFAULTS;
        foreach ( isset( $context['siteaccesses'] ) ? $context['siteaccesses'] : array() as $name )
            if ( !in_array( $name, isset( $context['admin'] ) ? $context['admin'] : array(), true ) && !expStaticTarget::adminName( $name ) )
            {
                $settings['SiteAccess'] = $name;
                break;
            }
        return $settings;
    }

    /**
     * The settings of the wizard's form. Pure.
     *
     * @param array $post the POST (Target[<setting>]; lists as arrays or, for ExcludePaths and ExcludeSections, one
     *                    value per line; CredentialKind and CredentialToken make Credential)
     * @param string $targetId the target (for the name of its deploy key)
     * @return array the settings of WIZARD_SETTINGS the form sent
     */
    public static function settingsFromPost( array $post, $targetId )
    {
        $in = isset( $post['Target'] ) && is_array( $post['Target'] ) ? $post['Target'] : array();
        $out = array();
        foreach ( self::WIZARD_SETTINGS as $name )
        {
            // a setting the form does not send keeps its value (a list is sent with an empty first value)
            if ( !array_key_exists( $name, $in ) )
                continue;
            $value = $in[$name];
            if ( in_array( $name, expStaticTarget::LIST_SETTINGS, true ) )
            {
                if ( is_string( $value ) )
                    $value = preg_split( '/\r\n|\r|\n|,/', $value );
                $value = array_values( array_unique( array_filter( array_map( 'trim', array_map( 'strval', (array)$value ) ), 'strlen' ) ) );
            }
            else
                $value = is_array( $value ) ? '' : trim( str_replace( array( "\r", "\n", "\0" ), '', (string)$value ) );
            $out[$name] = $value;
        }
        $kind = isset( $post['CredentialKind'] ) ? (string)$post['CredentialKind'] : '';
        $token = isset( $post['CredentialToken'] ) ? trim( (string)$post['CredentialToken'] ) : '';
        if ( $kind === 'token' )
            $out['Credential'] = 'token:' . $token;
        else if ( $kind === 'sshkey' )
            $out['Credential'] = 'sshkey:' . expStaticCredentials::keySecretName( $targetId );
        else if ( $kind === 'app' )
            $out['Credential'] = 'app:' . ( isset( $post['CredentialApp'] ) ? preg_replace( '/\D/', '', (string)$post['CredentialApp'] ) : '' );
        else if ( $kind === 'none' )
            $out['Credential'] = 'none';
        if ( isset( $out['Links'], $out['BasePath'] ) && $out['Links'] === 'relative' && $out['BasePath'] === '/' )
            $out['BasePath'] = '';
        if ( array_key_exists( 'SiteAccessList', $out ) )
            $out = self::siteAccessSettings( expStaticTarget::siteAccessesOf( $out ), isset( $out['DefaultSiteAccess'] ) ? $out['DefaultSiteAccess'] : '' ) + $out;
        return $out;
    }

    /**
     * The settings of the siteaccesses ticked in the wizard, in the form that is written: one siteaccess as SiteAccess
     * alone (as a target of one has always been written), several as SiteAccessList[] with DefaultSiteAccess (empty
     * when the default chosen is not one of them). Pure.
     *
     * @param string[] $names the siteaccesses ticked
     * @param string $default the one chosen for the root of the copy
     * @return array SiteAccess, SiteAccessList, DefaultSiteAccess
     */
    public static function siteAccessSettings( array $names, $default = '' )
    {
        $names = expStaticTarget::siteAccessesOf( array( 'SiteAccessList' => $names ) );
        $default = (string)$default;
        if ( count( $names ) <= 1 )
            return array( 'SiteAccess' => $names ? $names[0] : '', 'SiteAccessList' => array(), 'DefaultSiteAccess' => '' );
        return array( 'SiteAccess' => '', 'SiteAccessList' => $names, 'DefaultSiteAccess' => in_array( $default, $names, true ) ? $default : '' );
    }

    /**
     * The kind and token name of a Credential, for the wizard. Pure.
     *
     * @param string $credential
     * @return array kind (token, sshkey, app, none), token (the secret name of a token), app (the installation of a
     *         GitHub App)
     */
    public static function credentialChoice( $credential )
    {
        $credential = (string)$credential;
        $kind = strpos( $credential, ':' ) === false ? $credential : substr( $credential, 0, strpos( $credential, ':' ) );
        return array( 'kind' => in_array( $kind, expStaticTarget::CREDENTIAL_KINDS, true ) ? $kind : 'none',
                      'token' => $kind === 'token' ? (string)substr( $credential, 6 ) : '',
                      'app' => $kind === 'app' ? (string)substr( $credential, 4 ) : '' );
    }

    /**
     * What the administration shows of a target on GitHub: whether the GitHub steps can be used for it and, when
     * asked, its state there (one short round of API calls).
     *
     * @param expStaticTarget|null $target
     * @param bool $status also ask GitHub for the repository, Pages and the last Pages build
     * @param array $options expStaticGitHub's (http, api, credentials) for tests
     * @return array github (the target is a github-pages one on github.com), available (the integration can be used
     *         here), usable (and with this target's credential), problem (why not), owner, repo, pages_url, status
     *         (expStaticGitHub::statusFor() or null)
     */
    public static function github( $target, $status = false, array $options = array() )
    {
        $out = array( 'github' => false, 'available' => expStaticGitHub::available(), 'usable' => false, 'problem' => expStaticGitHub::unavailable(),
                      'owner' => '', 'repo' => '', 'pages_url' => '', 'status' => null );
        if ( !$target instanceof expStaticTarget || $target->setting( 'Kind' ) !== 'github-pages' )
            return $out;
        $remote = expStaticGitHub::parseRemote( (string)$target->setting( 'Remote' ) );
        if ( $remote === null )
            return $out;
        $out['github'] = true;
        $out['owner'] = $remote['owner'];
        $out['repo'] = $remote['repo'];
        $out['pages_url'] = $target->setting( 'CustomDomain' ) !== '' ? 'https://' . $target->setting( 'CustomDomain' ) . '/' : expStaticGitHub::pagesUrl( $remote['owner'], $remote['repo'] );
        if ( !$out['available'] && empty( $options['http'] ) )
            return $out;
        $kind = expStaticCredentials::parse( (string)$target->setting( 'Credential' ) )[0];
        if ( $kind !== 'token' && $kind !== 'app' )
        {
            $out['problem'] = 'the GitHub steps need a token or a GitHub App as the credential';
            return $out;
        }
        $out['usable'] = true;
        $out['problem'] = '';
        if ( $status )
        {
            $out['status'] = expStaticGitHub::statusFor( $target, $options + array( 'timeout' => 8 ) );
            if ( !$out['status']['available'] )
            {
                $out['usable'] = false;
                $out['problem'] = (string)reset( $out['status']['errors'] );
            }
        }
        return $out;
    }

    /**
     * The checks of a target made in the wizard: expStaticTarget::validate() and the wizard's own (a new id must be
     * free, the deploy key needs an SSH remote).
     *
     * @param string $id
     * @param array $settings
     * @param array $context expStaticTarget::siteAccessContext()
     * @param string[] $existing the target ids there are
     * @param bool $isNew
     * @return array errors, warnings
     */
    public static function check( $id, array $settings, array $context, array $existing, $isNew )
    {
        $target = new expStaticTarget( $id, $settings );
        $result = $target->validate( $context );
        if ( $isNew && in_array( $id, $existing, true ) )
            $result['errors'][] = "There is a target $id already: choose another id.";
        if ( $settings['Remote'] === '' )
            $result['warnings'][] = 'No git address (Remote): publishing commits and tags in the target\'s folder, but pushes nothing.';
        if ( strpos( (string)$settings['Credential'], 'sshkey:' ) === 0 && preg_match( '#^https?://#i', (string)$settings['Remote'] ) )
            $result['errors'][] = 'A deploy key signs in over SSH: the git address must be the SSH one (git@github.com:owner/repo.git).';
        if ( strpos( (string)$settings['Credential'], 'token:' ) === 0 && preg_match( '#^(git@|ssh://)#i', (string)$settings['Remote'] ) )
            $result['errors'][] = 'A token signs in over HTTPS: the git address must be the https:// one.';
        return $result;
    }

    /**
     * The editor of settings/override/staticcache.ini.append.php with a target written into it: its block (each
     * wizard setting; lists as a reset line and their values), the id in TargetList[] and, when asked, Enabled.
     *
     * @param string $id
     * @param array $settings
     * @param bool $enable switch static publishing on
     * @param expIniScope|null $scope another scope (tests)
     * @return expIniEditor
     */
    public static function editor( $id, array $settings, $enable = false, $scope = null )
    {
        $editor = new expIniEditor( $scope ?: expIniEditor::scope( 'global' ), 'staticcache' );
        $block = 'Target_' . $id;
        $ini = eZINI::instance( 'staticcache.ini' );
        foreach ( self::WIZARD_SETTINGS as $name )
        {
            if ( !array_key_exists( $name, $settings ) )
                continue;
            // a target of one siteaccess is written as it always was (SiteAccess alone): the settings of several are
            // only written when they hold something or there is something of them to clear
            if ( ( $name === 'SiteAccessList' || $name === 'DefaultSiteAccess' ) && ( $settings[$name] === array() || $settings[$name] === '' )
                 && !$ini->hasVariable( $block, $name ) )
                continue;
            if ( in_array( $name, expStaticTarget::LIST_SETTINGS, true ) )
            {
                $editor->clearArray( $block, $name );
                foreach ( (array)$settings[$name] as $value )
                    $editor->add( $block, $name, (string)$value );
            }
            else
                $editor->set( $block, $name, (string)$settings[$name] );
        }
        $listed = $ini->hasVariable( 'StaticPublishSettings', 'TargetList' ) ? (array)$ini->variable( 'StaticPublishSettings', 'TargetList' ) : array();
        if ( !in_array( $id, $listed, true ) )
            $editor->add( 'StaticPublishSettings', 'TargetList', $id );
        if ( $enable )
            $editor->set( 'StaticPublishSettings', 'Enabled', 'enabled' );
        return $editor;
    }

    /**
     * Writes a target, audited as system.staticpublish.target.
     *
     * @param string $id
     * @param array $settings
     * @param bool $isNew
     * @param bool $enable
     * @return string '' when written, else why not
     */
    public static function saveTarget( $id, array $settings, $isNew, $enable )
    {
        try
        {
            $editor = self::editor( $id, $settings, $enable );
            if ( $editor->hasChanges() )
                $editor->save( array( 'create' => true ) );
        }
        catch ( Throwable $e )
        {
            expStaticPublisher::audit( 'system.staticpublish.target', array( 'object' => array( 'type' => 'static_target', 'id' => $id ),
                                       'verb' => $isNew ? 'create' : 'update', 'result' => 'failure', 'reason' => $e->getMessage() ) );
            return $e->getMessage();
        }
        self::settingsChanged();
        expStaticPublisher::audit( 'system.staticpublish.target', array( 'object' => array( 'type' => 'static_target', 'id' => $id ),
                                   'verb' => $isNew ? 'create' : 'update', 'result' => 'success',
                                   'x' => array( 'siteaccess' => implode( ',', expStaticTarget::siteAccessesOf( $settings ) ), 'kind' => $settings['Kind'], 'remote' => $settings['Remote'],
                                                 'credential' => $settings['Credential'], 'enabled' => (bool)$enable ) ) );
        return '';
    }

    /**
     * Switches static publishing on or off ([StaticPublishSettings] Enabled in the override).
     *
     * @param bool $on
     * @return string '' when written, else why not
     */
    public static function setEnabled( $on )
    {
        try
        {
            $editor = new expIniEditor( expIniEditor::scope( 'global' ), 'staticcache' );
            $editor->set( 'StaticPublishSettings', 'Enabled', $on ? 'enabled' : 'disabled' );
            if ( $editor->hasChanges() )
                $editor->save( array( 'create' => true ) );
        }
        catch ( Throwable $e )
        {
            return $e->getMessage();
        }
        self::settingsChanged();
        expStaticPublisher::audit( 'system.staticpublish.target', array( 'object' => array( 'type' => 'static_publishing', 'id' => 'settings' ),
                                   'verb' => $on ? 'enable' : 'disable', 'result' => 'success' ) );
        return '';
    }

    /** The INI cache and this request's instances forget staticcache.ini. */
    protected static function settingsChanged()
    {
        try
        {
            if ( class_exists( 'eZCache' ) )
                eZCache::clearByTag( 'ini' );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'INI cache: ' . $e->getMessage(), __METHOD__ );
        }
        eZINI::resetInstance( 'staticcache.ini' );
    }

    /**
     * A link of the site as the target will write it, for the wizard's live preview. Pure.
     *
     * @param string $links relative or base-path
     * @param string $basePath
     * @param string $fromPath the page holding the link (/news/)
     * @param string $toPath the page it links to (/about-us/team)
     * @return string
     */
    public static function linkExample( $links, $basePath, $fromPath, $toPath )
    {
        $from = expStaticLinkRewriter::pageFile( $fromPath );
        $to = expStaticLinkRewriter::pageFile( $toPath );
        if ( $from === null || $to === null )
            return '';
        $target = expStaticLinkRewriter::pageUrl( $to );
        if ( $links === 'base-path' )
            return expStaticLinkRewriter::normaliseBasePath( $basePath ) . $target;
        return expStaticLinkRewriter::relative( $from, $target );
    }

    // ------------------------------------------------------------------ preview

    /**
     * The build not yet published against the last publish: every file added, changed or removed with its size.
     *
     * @param expStaticManifest|null $committed the manifest of the last publish
     * @param expStaticManifest|null $built the manifest of the build in the folder
     * @param int $limit the most rows per list
     * @return array added, changed, removed (rows: path, kind, size, old_size), counts (added, changed, removed,
     *               unchanged), bytes (of the build), bytes_added, bytes_removed, more (rows left out per list)
     */
    public static function diffDetails( $committed, $built, $limit = 500 )
    {
        $out = array( 'added' => array(), 'changed' => array(), 'removed' => array(), 'counts' => array( 'added' => 0, 'changed' => 0, 'removed' => 0, 'unchanged' => 0 ),
                      'bytes' => 0, 'bytes_added' => 0, 'bytes_removed' => 0, 'more' => array( 'added' => 0, 'changed' => 0, 'removed' => 0 ) );
        if ( !$built instanceof expStaticManifest )
            return $out;
        $diff = expStaticManifest::diff( $committed instanceof expStaticManifest ? $committed : null, $built );
        $old = $committed instanceof expStaticManifest ? $committed->entries() : array();
        $new = $built->entries();
        foreach ( array( 'added', 'changed', 'removed' ) as $list )
        {
            $out['counts'][$list] = count( $diff[$list] );
            foreach ( array_slice( $diff[$list], 0, $limit ) as $path )
            {
                $entry = isset( $new[$path] ) ? $new[$path] : $old[$path];
                $out[$list][] = array( 'path' => $path, 'kind' => $entry['kind'], 'size' => isset( $new[$path] ) ? $new[$path]['size'] : 0,
                                       'old_size' => isset( $old[$path] ) ? $old[$path]['size'] : 0 );
            }
            $out['more'][$list] = max( 0, count( $diff[$list] ) - $limit );
        }
        $out['counts']['unchanged'] = $diff['unchanged'];
        $out['bytes'] = $built->bytes();
        $out['bytes_added'] = $diff['bytes_added'];
        $out['bytes_removed'] = $diff['bytes_removed'];
        return $out;
    }

    /**
     * The build against the limits of the host (GitHub Pages: 1 GB a site, 100 MB a file). Pure.
     *
     * @param expStaticManifest|null $built
     * @param int $siteLimit
     * @param int $fileLimit
     * @return array bytes, site_limit, file_limit, percent, meter (its step of the meter classes), state (ok, warn above 90 %, bad above the limit or with
     *               a file above the file limit), too_large (path, size), largest (path, size)
     */
    public static function limits( $built, $siteLimit, $fileLimit )
    {
        $out = array( 'bytes' => 0, 'site_limit' => (int)$siteLimit, 'file_limit' => (int)$fileLimit, 'percent' => 0, 'meter' => 0, 'state' => 'ok',
                      'too_large' => array(), 'largest' => array( 'path' => '', 'size' => 0 ) );
        if ( !$built instanceof expStaticManifest )
            return $out;
        $out['bytes'] = $built->bytes();
        $out['percent'] = self::percent( $out['bytes'], $siteLimit );
        $out['meter'] = expStaticProgress::meterStep( $out['percent'] );
        foreach ( $built->entries() as $path => $entry )
        {
            if ( $entry['size'] > $out['largest']['size'] )
                $out['largest'] = array( 'path' => $path, 'size' => $entry['size'] );
            if ( $fileLimit > 0 && $entry['size'] > $fileLimit )
                $out['too_large'][] = array( 'path' => $path, 'size' => $entry['size'] );
        }
        if ( ( $siteLimit > 0 && $out['bytes'] > $siteLimit ) || $out['too_large'] )
            $out['state'] = 'bad';
        else if ( $siteLimit > 0 && $out['bytes'] > $siteLimit * 0.9 )
            $out['state'] = 'warn';
        return $out;
    }

    /** @return string bytes for people: 512 B, 1.4 KB, 3.2 MB. Pure. */
    public static function bytes( $bytes )
    {
        return expStaticBuild::bytes( (int)$bytes );
    }

    /** @return string the raw signing key of the preview links: derived from the site secret */
    protected static function key()
    {
        if ( self::$testKey !== null )
            return self::$testKey;
        return expMailSecret::derive( 'staticpublish-preview' );
    }

    /**
     * Tests only: a fixed key (raw bytes); null resets.
     *
     * @param string|null $key
     */
    public static function setKeyForTest( $key = null )
    {
        self::$testKey = $key;
    }

    /**
     * The signature of a preview link: the target, the user and the expiry, so a link works for its user only and only
     * for an hour.
     *
     * @param string $targetId
     * @param int $userId
     * @param int $expires a Unix time
     * @return string
     */
    public static function previewSignature( $targetId, $userId, $expires )
    {
        $mac = hash_hmac( 'sha256', 'staticpublish-preview/1|' . $targetId . '|' . (int)$userId . '|' . (int)$expires, self::key(), true );
        return rtrim( strtr( base64_encode( $mac ), '+/', '-_' ), '=' );
    }

    /**
     * Whether a preview link is valid.
     *
     * @param string $targetId
     * @param int $userId
     * @param int|string $expires
     * @param string $signature
     * @param int|null $now
     * @return bool
     */
    public static function previewValid( $targetId, $userId, $expires, $signature, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        if ( !ctype_digit( (string)$expires ) || (int)$expires < $now || (int)$expires > $now + self::PREVIEW_TTL + 60 )
            return false;
        return hash_equals( self::previewSignature( $targetId, $userId, (int)$expires ), (string)$signature );
    }

    /**
     * The path of a preview link (module view staticpublish/serve), ending with / (the front page of the build).
     *
     * @param string $targetId
     * @param int $userId
     * @param int|null $now
     * @return string staticpublish/serve/<target>/<expires>/<signature>/
     */
    public static function previewPath( $targetId, $userId, $now = null )
    {
        $expires = ( $now === null ? time() : (int)$now ) + self::PREVIEW_TTL;
        return 'staticpublish/serve/' . $targetId . '/' . $expires . '/' . self::previewSignature( $targetId, $userId, $expires ) . '/';
    }

    // ------------------------------------------------------------------ the deploy key

    /**
     * The deploy key of a target: the public half kept as the secret's description.
     *
     * @param expStaticTarget $target
     * @return array secret, exists, public_key, fingerprint, store (whether the secret store is set up)
     */
    public static function deployKey( expStaticTarget $target )
    {
        $secret = expStaticCredentials::keySecretName( $target->id() );
        $out = array( 'secret' => $secret, 'exists' => false, 'public_key' => '', 'fingerprint' => '', 'store' => expSecrets::installed() );
        $stored = $out['store'] ? expSecrets::names( expStaticCredentials::SCOPE ) : array();
        if ( isset( $stored[$secret] ) && $stored[$secret]['source'] === 'store' )
        {
            $out['exists'] = true;
            $out['public_key'] = (string)$stored[$secret]['description'];
            $out['fingerprint'] = expStaticCredentials::fingerprint( $out['public_key'] );
        }
        return $out;
    }

    /**
     * Makes a target's deploy key (as exp:static:keys --generate), audited as system.staticpublish.credential.
     *
     * @param expStaticTarget $target
     * @param bool $replace replace an existing key
     * @return array generated (bool), public_key, fingerprint, error
     */
    public static function generateKey( expStaticTarget $target, $replace )
    {
        $current = self::deployKey( $target );
        $out = array( 'generated' => false, 'public_key' => '', 'fingerprint' => '', 'error' => '' );
        if ( !$current['store'] )
            $out['error'] = 'the secret store is not set up (the table exp_secret: ./console exp:upgrade --apply)';
        else if ( $current['exists'] && !$replace )
            $out['error'] = 'the target has a deploy key already';
        else
        {
            $host = preg_replace( '/[^A-Za-z0-9.-]/', '', (string)php_uname( 'n' ) ) ?: 'localhost';
            $key = expStaticCredentials::generateKey( self::publisher( $target )->workDirectory(), 'exponential-static-publish-' . $target->id() . '@' . $host );
            if ( $key['error'] !== '' )
                $out['error'] = $key['error'];
            else if ( !expSecrets::set( $current['secret'], $key['private'], expStaticCredentials::SCOPE, $key['public'] ) )
                $out['error'] = 'the secret ' . $current['secret'] . ' could not be stored (Setup > Secrets: the store\'s key)';
            else
                $out = array( 'generated' => true, 'public_key' => $key['public'], 'fingerprint' => $key['fingerprint'], 'error' => '' );
        }
        expStaticPublisher::audit( 'system.staticpublish.credential', array(
            'object' => array( 'type' => 'static_target', 'id' => $target->id() ), 'verb' => $current['exists'] ? 'replace' : 'generate',
            'result' => $out['generated'] ? 'success' : 'failure', 'reason' => $out['error'],
            'x' => array( 'kind' => 'sshkey', 'secret' => $current['secret'], 'fingerprint' => $out['fingerprint'] ) ) );
        return $out;
    }

    /**
     * The secret names a token credential may name (Setup > Secrets), for the wizard's list.
     *
     * @return string[]
     */
    public static function secretNames()
    {
        try
        {
            return array_keys( expSecrets::names() );
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }
}
