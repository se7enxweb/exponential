<?php
/**
 * File containing the expStaticRepository class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The git checkout of a static publishing target, modelled on expMigrationRepository. The target's folder
 * (var/<site>/static-publish/<target>/) is a git repository of its own, never inside the site's repository; with
 * Folder=docs the build is in <checkout>/docs.
 *
 *   prepare()   makes the folder a repository on the target's Branch: git init, the remote origin (the target's
 *               Remote), and when the remote has the branch already its history is taken over (fetch, then the
 *               index set to it), so the first publish is a commit on top of what is there. Files of that history
 *               that no build wrote (a README) are kept.
 *   commit()    stages only the build's files: every file the manifest lists, the manifest itself, and the files the
 *               last committed manifest listed that this one does not (removed). Anything else in the checkout is
 *               left alone. One commit, tagged static/<target>/<n> (n counts on from the highest tag; a tag is never
 *               moved or replaced).
 *   push()      the branch and the target's tags to origin, never forced (no --force, no + refspec: a guard refuses
 *               them). States: pushed, pending (failed: tried again by exp:static:push and the cronjob part
 *               staticpublish), disabled (Push=disabled or --no-push), no-remote (Remote empty).
 *   rollback()  a new commit with the files of an earlier tag (a revert, the history stays), tagged with the next
 *               number and pushed like a publish.
 *
 * git runs without a shell: through git_manager's GitManager (expUpgradeGit::git()) for the commands that stay in
 * the checkout when git_manager is active, and through the same kind of process call with the credential's
 * environment (expStaticCredentials) for the commands that talk to the remote, since GitManager takes no
 * environment. Paths are passed literally (--literal-pathspecs), in batches (Windows limits a command line to 32K).
 * A checkout that belongs to another system user than the process is left alone.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticRepository
{
    const PUSHED = 'pushed';
    const PENDING = 'pending';
    const DISABLED = 'disabled';
    const NO_REMOTE = 'no-remote';
    const NONE = 'none';

    /** The name of the remote in the checkout */
    const REMOTE = 'origin';

    /** The prefix of the tags: static/<target>/<n> */
    const TAG_PREFIX = 'static/';

    /** Paths per git call */
    const BATCH = 200;

    /** @var expStaticTarget */
    protected $target;

    /** @var string the checkout */
    protected $checkout;

    /** @var expStaticCredentials */
    protected $credentials;

    /** @var array */
    protected $options;

    /**
     * @param expStaticTarget $target
     * @param string $checkout the target's folder (expStaticTarget::directory())
     * @param expStaticCredentials $credentials
     * @param array $options push (bool, default true), commit_name, commit_email, git_manager (bool: use
     *        expUpgradeGit when active, default true), max_site_bytes, max_file_bytes (the limits of a github-pages target
     *        checked before every push; default [StaticPublishSettings] MaxSiteBytes and MaxFileBytes)
     */
    public function __construct( expStaticTarget $target, $checkout, expStaticCredentials $credentials, array $options = array() )
    {
        $this->target = $target;
        $this->checkout = rtrim( str_replace( '\\', '/', (string)$checkout ), '/' );
        $this->credentials = $credentials;
        $this->options = $options + array( 'push' => true, 'commit_name' => 'Exponential static publishing', 'commit_email' => '', 'git_manager' => true,
                                           'max_site_bytes' => null, 'max_file_bytes' => null );
    }

    /** @return string the checkout */
    public function checkout()
    {
        return $this->checkout;
    }

    /** @return string the branch the target publishes to */
    public function branch()
    {
        $branch = (string)$this->target->setting( 'Branch' );
        return $branch !== '' ? $branch : 'gh-pages';
    }

    /** @return string '' or the folder of the branch the site is in, with a slash (docs/) */
    public function prefix()
    {
        $folder = trim( str_replace( '\\', '/', (string)$this->target->setting( 'Folder' ) ), '/' );
        return $folder === '' ? '' : $folder . '/';
    }

    /**
     * The tag of a publish. Pure.
     *
     * @param string $targetId
     * @param int $number
     * @return string static/<target>/<n>
     */
    public static function tagName( $targetId, $number )
    {
        return self::TAG_PREFIX . $targetId . '/' . (int)$number;
    }

    /**
     * The number of a target's tag, 0 when the name is not one. Pure.
     *
     * @param string $name static/<target>/<n>, or <n>
     * @param string $targetId
     * @return int
     */
    public static function tagNumber( $name, $targetId )
    {
        $name = trim( (string)$name );
        if ( ctype_digit( $name ) )
            return (int)$name;
        $prefix = self::TAG_PREFIX . $targetId . '/';
        if ( strpos( $name, $prefix ) !== 0 || !ctype_digit( substr( $name, strlen( $prefix ) ) ) )
            return 0;
        return (int)substr( $name, strlen( $prefix ) );
    }

    /**
     * The next number from a list of tag names. Pure.
     *
     * @param string[] $tags
     * @param string $targetId
     * @return int
     */
    public static function nextNumber( array $tags, $targetId )
    {
        $max = 0;
        foreach ( $tags as $tag )
            $max = max( $max, self::tagNumber( $tag, $targetId ) );
        return $max + 1;
    }

    /**
     * Refuses what would rewrite the remote's history. Pure.
     *
     * @param string[] $arguments a git call's arguments
     * @throws LogicException for push with --force, -f, --force-with-lease, --mirror, --delete or a + refspec
     */
    public static function guard( array $arguments )
    {
        $i = array_search( 'push', $arguments, true );
        if ( $i === false )
            return;
        foreach ( array_slice( $arguments, $i + 1 ) as $argument )
        {
            $argument = (string)$argument;
            if ( in_array( $argument, array( '-f', '--force', '--mirror', '--delete', '-d', '--prune' ), true )
                 || strpos( $argument, '--force' ) === 0 || ( $argument !== '' && $argument[0] === '+' ) || ( $argument !== '' && $argument[0] === ':' ) )
                throw new LogicException( "static publishing never forces a push or deletes on the remote: $argument" );
        }
    }

    /**
     * Why git cannot be used in the checkout, '' when it can.
     *
     * @return string
     */
    public function checkoutProblem()
    {
        if ( !is_dir( $this->checkout ) )
            return "{$this->checkout} does not exist: build the target first";
        foreach ( array( $this->checkout . '/.git', $this->checkout ) as $path )
            if ( file_exists( $path ) && function_exists( 'posix_geteuid' ) && ( $owner = fileowner( $path ) ) !== false && $owner !== posix_geteuid() )
            {
                $name = function_exists( 'posix_getpwuid' ) && ( $pw = posix_getpwuid( $owner ) ) ? $pw['name'] : (string)$owner;
                return "$path belongs to $name: run static publishing as $name (sudo -u $name), files written by another user break the next build";
            }
        $version = $this->git( array( '--version' ) );
        if ( !$version['ok'] )
            return 'git is not installed or cannot be run';
        return '';
    }

    /**
     * Makes the checkout a repository on the target's branch, with origin set to the target's Remote.
     *
     * @param array $result messages, warnings, problems are added here
     * @return bool whether the checkout can be committed to
     */
    public function prepare( array &$result )
    {
        $result += array( 'messages' => array(), 'warnings' => array(), 'problems' => array() );
        $problem = $this->checkoutProblem();
        if ( $problem !== '' )
        {
            $result['problems'][] = $problem;
            return false;
        }
        $branch = $this->branch();
        $remote = (string)$this->target->setting( 'Remote' );
        if ( !file_exists( $this->checkout . '/.git' ) )
        {
            $init = $this->git( array( 'init', '--quiet' ) );
            if ( !$init['ok'] )
            {
                $result['problems'][] = "git init failed in {$this->checkout}: " . trim( $init['error'] );
                return false;
            }
            $this->git( array( 'symbolic-ref', 'HEAD', 'refs/heads/' . $branch ) );
            $this->exclude();
            $result['messages'][] = "made {$this->checkout} a git repository on the branch $branch";
            if ( $remote !== '' )
            {
                $this->git( array( 'remote', 'add', self::REMOTE, $remote ) );
                $this->adopt( $branch, $result );
            }
        }
        else
        {
            $current = $this->git( array( 'symbolic-ref', '--quiet', '--short', 'HEAD' ) );
            $current = $current['ok'] ? trim( $current['output'] ) : '';
            if ( $current !== $branch )
            {
                $result['problems'][] = $current === '' ? "{$this->checkout} is on no branch: check out $branch there"
                                                        : "{$this->checkout} is on the branch $current, not $branch (the target's Branch)";
                return false;
            }
            $this->exclude();
            if ( $this->syncRemote() )
                $result['messages'][] = 'the remote origin now is the target\'s Remote';
        }
        $staged = $this->git( array( 'diff', '--cached', '--quiet' ) );
        if ( !$staged['ok'] && $staged['exit'] === 1 && $this->hasHead() )
        {
            $result['problems'][] = "{$this->checkout} has staged changes that are not the build's: commit or unstage them there first";
            return false;
        }
        return true;
    }

    /**
     * Sets origin to the target's Remote (the setting is what counts; it never holds a credential).
     *
     * @return bool whether origin was changed
     */
    protected function syncRemote()
    {
        $remote = (string)$this->target->setting( 'Remote' );
        if ( $remote === '' )
            return false;
        $url = $this->git( array( 'remote', 'get-url', self::REMOTE ) );
        if ( !$url['ok'] )
            return $this->git( array( 'remote', 'add', self::REMOTE, $remote ) )['ok'];
        if ( trim( $url['output'] ) === $remote )
            return false;
        return $this->git( array( 'remote', 'set-url', self::REMOTE, $remote ) )['ok'];
    }

    /** Keeps the local files of static publishing out of the repository (.git/info/exclude). */
    protected function exclude()
    {
        $file = $this->checkout . '/.git/info/exclude';
        $line = basename( expStaticManifest::PREVIOUS_FILE );
        $text = is_file( $file ) ? (string)file_get_contents( $file ) : '';
        if ( strpos( $text, $line ) !== false )
            return;
        if ( !is_dir( dirname( $file ) ) )
            @mkdir( dirname( $file ), eZDir::dirMode( 0775 ), true );
        file_put_contents( $file, rtrim( $text ) . ( $text !== '' ? "\n" : '' ) . "# static publishing: the manifest of the build before, kept locally\n.static-publish/$line\n" );
    }

    /**
     * Takes over the branch of the remote when it has it: fetched with the target's tags, the index set to it, and
     * the files of it that no build wrote and that are not in the folder brought back.
     */
    protected function adopt( $branch, array &$result )
    {
        $heads = $this->git( array( 'ls-remote', '--heads', self::REMOTE, 'refs/heads/' . $branch ), true );
        if ( !$heads['ok'] )
        {
            $result['warnings'][] = 'the remote cannot be read (' . $this->firstLine( $heads ) . '): the publish starts a new history; its push waits until the remote is reachable';
            return;
        }
        if ( trim( $heads['output'] ) === '' )
        {
            $result['messages'][] = "the remote has no branch $branch yet: the first push makes it";
            return;
        }
        $id = $this->target->id();
        $fetch = $this->git( array( 'fetch', '--quiet', '--no-tags', self::REMOTE, "refs/heads/$branch:refs/remotes/origin/$branch",
                                    "refs/tags/static/$id/*:refs/tags/static/$id/*" ), true, 600 );
        if ( !$fetch['ok'] )
        {
            $result['warnings'][] = 'the branch of the remote could not be fetched (' . $this->firstLine( $fetch ) . '): the publish starts a new history';
            return;
        }
        $this->git( array( 'reset', '--quiet', "refs/remotes/origin/$branch" ) );
        $owned = $this->manifestPaths( 'HEAD' );
        $restore = array();
        foreach ( $this->lines( $this->git( array( 'ls-files', '--deleted', '-z' ) ), "\0" ) as $path )
            if ( !isset( $owned[$path] ) )
                $restore[] = $path;
        foreach ( array_chunk( $restore, self::BATCH ) as $chunk )
            $this->git( array_merge( array( 'checkout', '--quiet', '--' ), $chunk ) );
        $result['messages'][] = "took over the branch $branch of the remote" . ( $restore ? ' (' . count( $restore ) . ' file(s) of it that no build wrote kept)' : '' );
    }

    /**
     * Commits the build in the checkout and tags it.
     *
     * @param string $message the commit message
     * @param array $result messages, warnings, problems; commit_id, tag_name, tag_number, added, changed, removed are set
     * @return bool whether a commit was made (false: nothing changed, or a problem)
     */
    public function commit( $message, array &$result )
    {
        $result += array( 'messages' => array(), 'warnings' => array(), 'problems' => array(), 'commit_id' => '', 'tag_name' => '', 'tag_number' => 0,
                          'added' => 0, 'changed' => 0, 'removed' => 0 );
        $prefix = $this->prefix();
        $manifest = expStaticManifest::load( $this->checkout . '/' . $prefix );
        if ( $manifest === null )
        {
            $result['problems'][] = "{$this->checkout}/{$prefix} holds no build (no manifest): run exp:static:build {$this->target->id()} first";
            return false;
        }
        $current = array();
        foreach ( array_merge( array_keys( $manifest->entries() ), array( expStaticManifest::FILE ) ) as $path )
            $current[$prefix . $path] = true;
        $missing = array();
        $add = array();
        foreach ( array_keys( $current ) as $path )
            if ( is_file( $this->checkout . '/' . $path ) )
                $add[] = $path;
            else
                $missing[] = $path;
        if ( $missing )
            $result['warnings'][] = sprintf( '%d file(s) of the manifest are missing from the folder and are not committed: %s', count( $missing ), implode( ', ', array_slice( $missing, 0, 5 ) ) );
        $removed = array_keys( array_diff_key( $this->hasHead() ? $this->manifestPaths( 'HEAD' ) : array(), $current ) );

        foreach ( array_chunk( $add, self::BATCH ) as $chunk )
        {
            $r = $this->git( array_merge( array( 'add', '--all', '--' ), $chunk ) );
            if ( !$r['ok'] )
            {
                $result['problems'][] = 'git add failed: ' . $this->firstLine( $r );
                return false;
            }
        }
        foreach ( array_chunk( $removed, self::BATCH ) as $chunk )
            $this->git( array_merge( array( 'rm', '--quiet', '--cached', '--ignore-unmatch', '--' ), $chunk ) );

        $status = $this->git( array( 'diff', '--cached', '--name-status', '--no-renames' ) );
        foreach ( $this->lines( $status ) as $line )
        {
            $code = substr( $line, 0, 1 );
            if ( $code === 'A' )
                $result['added']++;
            else if ( $code === 'D' )
                $result['removed']++;
            else if ( $code !== '' )
                $result['changed']++;
        }
        if ( $result['added'] + $result['changed'] + $result['removed'] === 0 )
        {
            $result['messages'][] = 'nothing to commit: the checkout holds this build already';
            return false;
        }
        $commit = $this->git( array_merge( $this->identity(), array( 'commit', '--quiet', '--no-verify', '-m', (string)$message ) ) );
        if ( !$commit['ok'] )
        {
            $result['problems'][] = 'git commit failed: ' . $this->firstLine( $commit );
            return false;
        }
        $result['commit_id'] = $this->head();
        $result['messages'][] = sprintf( 'committed %s on %s: %d added, %d changed, %d removed', substr( $result['commit_id'], 0, 10 ), $this->branch(),
                                         $result['added'], $result['changed'], $result['removed'] );
        $this->tag( strtok( (string)$message, "\n" ), $result );
        return true;
    }

    /** Tags HEAD with the next number. */
    protected function tag( $subject, array &$result )
    {
        $number = self::nextNumber( $this->tags(), $this->target->id() );
        $name = self::tagName( $this->target->id(), $number );
        for ( $i = 0; $i < 100 && $this->git( array( 'rev-parse', '--verify', '--quiet', 'refs/tags/' . $name ) )['ok']; $i++ )
            $name = self::tagName( $this->target->id(), ++$number );
        $tag = $this->git( array_merge( $this->identity(), array( 'tag', '-a', $name, '-m', (string)$subject, $result['commit_id'] ) ) );
        if ( !$tag['ok'] )
        {
            $result['problems'][] = "git tag $name failed: " . $this->firstLine( $tag );
            return;
        }
        $result['tag_name'] = $name;
        $result['tag_number'] = $number;
        $result['messages'][] = "tagged $name";
    }

    /** @return string[] the target's tags in the checkout */
    public function tags()
    {
        return $this->lines( $this->git( array( 'tag', '--list', self::TAG_PREFIX . $this->target->id() . '/*' ) ) );
    }

    /**
     * Pushes the branch and the target's tags to origin. Never forced.
     *
     * @param array $result messages, problems are added here
     * @return string pushed, pending, disabled or no-remote
     */
    public function push( array &$result )
    {
        $result += array( 'messages' => array(), 'warnings' => array(), 'problems' => array() );
        if ( !$this->options['push'] )
        {
            $result['messages'][] = 'not pushed (--no-push or [StaticPublishSettings] Push=disabled)';
            return self::DISABLED;
        }
        if ( (string)$this->target->setting( 'Remote' ) === '' )
        {
            $result['messages'][] = 'not pushed: the target has no Remote';
            return self::NO_REMOTE;
        }
        $problem = $this->credentials->problem( $this->target );
        if ( $problem !== '' )
        {
            $result['problems'][] = "not pushed: $problem; exp:static:push tries again";
            return self::PENDING;
        }
        $limits = $this->checkLimits();
        if ( $limits !== null )
        {
            $result['limits'] = $limits;
            $result['warnings'] = array_merge( $result['warnings'], $limits['warnings'] );
            if ( $limits['errors'] )
            {
                foreach ( $limits['errors'] as $error )
                    $result['problems'][] = "not pushed: $error; make the build smaller (Assets, ExcludePaths) and publish again";
                return self::PENDING;
            }
        }
        $branch = $this->branch();
        $id = $this->target->id();
        $this->syncRemote();
        try
        {
            $push = $this->git( array( 'push', '--quiet', self::REMOTE, "refs/heads/$branch:refs/heads/$branch",
                                       "refs/tags/static/$id/*:refs/tags/static/$id/*" ), true, 900 );
        }
        finally
        {
            $this->credentials->cleanup();
        }
        if ( $push['ok'] )
        {
            $result['messages'][] = "pushed $branch and the tags static/$id/* to " . $this->target->setting( 'Remote' );
            return self::PUSHED;
        }
        $result['problems'][] = 'the push failed (' . $this->firstLine( $push ) . '): the commit stays local and exp:static:push or the cronjob part staticpublish tries again';
        return self::PENDING;
    }

    /**
     * The limits of GitHub Pages for the build in the checkout (expStaticGitHub::limits()), checked before every
     * push of a github-pages target: a push over them is refused, as GitHub would refuse it or not serve it.
     *
     * @return array|null null for another kind of target or a checkout without a manifest
     */
    public function checkLimits()
    {
        if ( $this->target->setting( 'Kind' ) !== 'github-pages' || !class_exists( 'expStaticGitHub' ) )
            return null;
        $manifest = expStaticManifest::load( $this->checkout . '/' . $this->prefix() );
        if ( $manifest === null )
            return null;
        $setting = function ( $name, $default )
        {
            if ( !class_exists( 'eZINI' ) )
                return $default;
            $ini = eZINI::instance( 'staticcache.ini' );
            return $ini->hasVariable( 'StaticPublishSettings', $name ) ? (int)$ini->variable( 'StaticPublishSettings', $name ) : $default;
        };
        return expStaticGitHub::limits( $manifest, array(
            'max_site_bytes' => $this->options['max_site_bytes'] !== null ? (int)$this->options['max_site_bytes'] : $setting( 'MaxSiteBytes', expStaticGitHub::SITE_BYTES ),
            'max_file_bytes' => $this->options['max_file_bytes'] !== null ? (int)$this->options['max_file_bytes'] : $setting( 'MaxFileBytes', expStaticGitHub::FILE_BYTES ),
        ) );
    }

    /**
     * Restores the files of an earlier publish as a new commit (the history stays), tagged with the next number.
     *
     * @param string $to a tag static/<target>/<n> or its number
     * @param bool $apply false: only report what would change
     * @param string $message
     * @param array $result messages, problems; from_tag, commit_id, tag_name, tag_number, added, changed, removed
     * @return bool whether a commit was made
     */
    public function rollback( $to, $apply, $message, array &$result )
    {
        $result += array( 'messages' => array(), 'warnings' => array(), 'problems' => array(), 'from_tag' => '', 'commit_id' => '', 'tag_name' => '',
                          'tag_number' => 0, 'added' => 0, 'changed' => 0, 'removed' => 0, 'files' => array() );
        $number = self::tagNumber( $to, $this->target->id() );
        if ( $number < 1 )
        {
            $result['problems'][] = "$to is not a publish of {$this->target->id()}: name its number or its tag static/{$this->target->id()}/<n>";
            return false;
        }
        $tag = self::tagName( $this->target->id(), $number );
        $result['from_tag'] = $tag;
        $commit = $this->git( array( 'rev-parse', '--verify', '--quiet', "refs/tags/$tag^{commit}" ) );
        if ( !$commit['ok'] || !$this->hasHead() )
        {
            $result['problems'][] = "there is no tag $tag in {$this->checkout} (exp:static:status {$this->target->id()} lists them)";
            return false;
        }
        $diff = $this->git( array( 'diff', '--name-status', '--no-renames', 'HEAD', trim( $commit['output'] ) ) );
        foreach ( $this->lines( $diff ) as $line )
        {
            $code = substr( $line, 0, 1 );
            $result[$code === 'A' ? 'added' : ( $code === 'D' ? 'removed' : 'changed' )]++;
            if ( count( $result['files'] ) < 200 )
                $result['files'][] = $line;
        }
        if ( $result['added'] + $result['changed'] + $result['removed'] === 0 )
        {
            $result['messages'][] = "the checkout holds the files of $tag already: nothing to roll back";
            return false;
        }
        if ( !$apply )
        {
            $result['messages'][] = sprintf( 'rolling back to %s would add %d, change %d and remove %d file(s); run again with --apply', $tag,
                                             $result['added'], $result['changed'], $result['removed'] );
            return false;
        }
        $dirty = $this->git( array( 'status', '--porcelain', '--untracked-files=no' ) );
        if ( !$dirty['ok'] || trim( $dirty['output'] ) !== '' )
        {
            $result['problems'][] = "{$this->checkout} has changes that are not committed (a build not published yet?): publish it or build again before a rollback";
            return false;
        }
        $head = $this->head();
        $tree = $this->git( array( 'rev-parse', "refs/tags/$tag^{tree}" ) );
        $new = $this->git( array_merge( $this->identity(), array( 'commit-tree', trim( $tree['output'] ), '-p', $head, '-m', (string)$message ) ) );
        if ( !$tree['ok'] || !$new['ok'] || trim( $new['output'] ) === '' )
        {
            $result['problems'][] = 'the rollback commit could not be made: ' . $this->firstLine( $new );
            return false;
        }
        $update = $this->git( array( 'update-ref', '-m', 'static publishing rollback to ' . $tag, 'refs/heads/' . $this->branch(), trim( $new['output'] ), $head ) );
        if ( !$update['ok'] )
        {
            $result['problems'][] = 'the branch could not be moved to the rollback commit: ' . $this->firstLine( $update );
            return false;
        }
        $this->git( array( 'reset', '--quiet', '--hard', 'HEAD' ) );
        $result['commit_id'] = $this->head();
        $result['messages'][] = sprintf( 'committed %s: the files of %s (%d added, %d changed, %d removed)', substr( $result['commit_id'], 0, 10 ), $tag,
                                         $result['added'], $result['changed'], $result['removed'] );
        $this->tag( strtok( (string)$message, "\n" ), $result );
        return true;
    }

    /**
     * The manifest of the last commit (the build as last published), or null before the first publish.
     *
     * @return expStaticManifest|null
     */
    public function committedManifest()
    {
        if ( !file_exists( $this->checkout . '/.git' ) || !$this->hasHead() )
            return null;
        $show = $this->git( array( 'show', 'HEAD:' . $this->prefix() . expStaticManifest::FILE ) );
        $data = $show['ok'] ? json_decode( $show['output'], true ) : null;
        if ( !is_array( $data ) )
            return null;
        try
        {
            return expStaticManifest::fromArray( $data );
        }
        catch ( InvalidArgumentException $e )
        {
            return null;
        }
    }

    /**
     * Throws a build away that was not published: the build's files go back to what the last commit holds (the files
     * the build added are removed, the ones it changed or removed restored); before the first publish every file
     * of the build is removed. Files no build wrote are not touched.
     *
     * @param array $result messages, problems are added here; removed and restored (counts) are set
     * @return bool whether the checkout now holds the last publish
     */
    public function discard( array &$result )
    {
        $result += array( 'messages' => array(), 'warnings' => array(), 'problems' => array(), 'removed' => 0, 'restored' => 0 );
        $problem = $this->checkoutProblem();
        if ( $problem !== '' )
        {
            $result['problems'][] = $problem;
            return false;
        }
        $dir = rtrim( $this->checkout . '/' . $this->prefix(), '/' );
        $built = expStaticManifest::load( $dir );
        $committed = $this->committedManifest();
        $keep = $committed !== null ? $committed->entries() : array();
        foreach ( $built !== null ? array_keys( $built->entries() ) : array() as $path )
        {
            if ( isset( $keep[$path] ) || !is_file( $dir . '/' . $path ) || !@unlink( $dir . '/' . $path ) )
                continue;
            $result['removed']++;
            $parent = dirname( $dir . '/' . $path );
            while ( strlen( $parent ) > strlen( $dir ) && is_dir( $parent ) && count( (array)scandir( $parent ) ) === 2 && @rmdir( $parent ) )
                $parent = dirname( $parent );
        }
        if ( $committed !== null )
        {
            $paths = array( $this->prefix() . expStaticManifest::FILE );
            foreach ( array_keys( $keep ) as $path )
                $paths[] = $this->prefix() . $path;
            foreach ( array_chunk( $paths, self::BATCH ) as $chunk )
            {
                $checkout = $this->git( array_merge( array( 'checkout', '--quiet', 'HEAD', '--' ), $chunk ) );
                if ( !$checkout['ok'] )
                {
                    $result['problems'][] = 'the files of the last publish could not be restored: ' . $this->firstLine( $checkout );
                    return false;
                }
            }
            $result['restored'] = count( $paths );
            $result['messages'][] = sprintf( 'the build was thrown away: %d file(s) of it removed, the files of the last publish restored', $result['removed'] );
        }
        else
        {
            if ( is_file( $dir . '/' . expStaticManifest::FILE ) )
                @unlink( $dir . '/' . expStaticManifest::FILE );
            $result['messages'][] = sprintf( 'the build was thrown away: %d file(s) removed (nothing was published yet)', $result['removed'] );
        }
        if ( is_file( $dir . '/' . expStaticManifest::PREVIOUS_FILE ) )
            @unlink( $dir . '/' . expStaticManifest::PREVIOUS_FILE );
        return true;
    }

    /**
     * The target's tags, newest first: name, number, commit, time (Unix), subject.
     *
     * @return array[]
     */
    public function tagList()
    {
        $out = array();
        $refs = $this->git( array( 'for-each-ref', '--format=%(refname:short)%09%(objectname)%09%(*objectname)%09%(creatordate:unix)%09%(subject)',
                                   'refs/tags/' . self::TAG_PREFIX . $this->target->id() . '/' ) );
        foreach ( $this->lines( $refs ) as $line )
        {
            $parts = explode( "\t", $line, 5 ) + array( '', '', '', '0', '' );
            $number = self::tagNumber( $parts[0], $this->target->id() );
            if ( $number < 1 )
                continue;
            $out[] = array( 'name' => $parts[0], 'number' => $number, 'commit' => $parts[2] !== '' ? $parts[2] : $parts[1],
                            'time' => (int)$parts[3], 'subject' => $parts[4] );
        }
        usort( $out, function ( $a, $b ) { return $b['number'] <=> $a['number']; } );
        return $out;
    }

    /**
     * The last commits of the branch, newest first: commit, time (Unix), author, subject.
     *
     * @param int $limit
     * @return array[]
     */
    public function commits( $limit = 30 )
    {
        if ( !file_exists( $this->checkout . '/.git' ) || !$this->hasHead() )
            return array();
        $out = array();
        foreach ( $this->lines( $this->git( array( 'log', '-n', (string)max( 1, (int)$limit ), '--format=%H%x09%ct%x09%an%x09%s', 'HEAD' ) ) ) as $line )
        {
            $parts = explode( "\t", $line, 4 ) + array( '', '0', '', '' );
            $out[] = array( 'commit' => $parts[0], 'time' => (int)$parts[1], 'author' => $parts[2], 'subject' => $parts[3] );
        }
        return $out;
    }

    /**
     * The state of the checkout for exp:static:status: branch, head, subject, tags, last tag, changes not committed,
     * commits not pushed, remote.
     *
     * @return array
     */
    public function state()
    {
        $state = array( 'repository' => file_exists( $this->checkout . '/.git' ), 'branch' => '', 'head' => '', 'subject' => '', 'tags' => 0,
                        'last_tag' => '', 'last_tag_commit' => '', 'uncommitted' => 0, 'unpushed' => null, 'remote' => '' );
        if ( !$state['repository'] )
            return $state;
        $branch = $this->git( array( 'symbolic-ref', '--quiet', '--short', 'HEAD' ) );
        $state['branch'] = $branch['ok'] ? trim( $branch['output'] ) : '';
        if ( $this->hasHead() )
        {
            $state['head'] = $this->head();
            $state['subject'] = trim( $this->git( array( 'log', '-1', '--format=%s', 'HEAD' ) )['output'] );
        }
        $tags = $this->tags();
        $state['tags'] = count( $tags );
        if ( $tags )
        {
            $state['last_tag'] = self::tagName( $this->target->id(), self::nextNumber( $tags, $this->target->id() ) - 1 );
            $commit = $this->git( array( 'rev-parse', '--verify', '--quiet', 'refs/tags/' . $state['last_tag'] . '^{commit}' ) );
            $state['last_tag_commit'] = $commit['ok'] ? trim( $commit['output'] ) : '';
        }
        $state['uncommitted'] = count( $this->lines( $this->git( array( 'status', '--porcelain', '--untracked-files=all', '--', $this->prefix() !== '' ? $this->prefix() : '.' ) ) ) );
        $url = $this->git( array( 'remote', 'get-url', self::REMOTE ) );
        $state['remote'] = $url['ok'] ? $this->credentials->redact( trim( $url['output'] ) ) : '';
        if ( $state['head'] !== '' && $state['remote'] !== '' )
        {
            $tracking = 'refs/remotes/origin/' . $this->branch();
            $known = $this->git( array( 'rev-parse', '--verify', '--quiet', $tracking ) )['ok'];
            $count = $this->git( array( 'rev-list', '--count', $known ? "$tracking..HEAD" : 'HEAD' ) );
            $state['unpushed'] = $count['ok'] ? (int)trim( $count['output'] ) : null;
        }
        return $state;
    }

    /** @return bool whether the branch has a commit */
    public function hasHead()
    {
        return $this->git( array( 'rev-parse', '--verify', '--quiet', 'HEAD' ) )['ok'];
    }

    /** @return string the commit of HEAD */
    public function head()
    {
        $head = $this->git( array( 'rev-parse', '--verify', 'HEAD' ) );
        return $head['ok'] ? trim( $head['output'] ) : '';
    }

    /**
     * The paths a committed manifest lists (with the folder prefix), and the manifest itself.
     *
     * @param string $revision
     * @return array path => true
     */
    protected function manifestPaths( $revision )
    {
        $prefix = $this->prefix();
        $show = $this->git( array( 'show', $revision . ':' . $prefix . expStaticManifest::FILE ) );
        if ( !$show['ok'] )
            return array();
        $data = json_decode( $show['output'], true );
        if ( !is_array( $data ) )
            return array();
        $manifest = expStaticManifest::fromArray( $data );
        $paths = array( $prefix . expStaticManifest::FILE => true );
        foreach ( array_keys( $manifest->entries() ) as $path )
            $paths[$prefix . $path] = true;
        return $paths;
    }

    /** @return string[] the -c options of the committer (CommitName, CommitEmail) unless the repository's own config names one */
    protected function identity()
    {
        $email = $this->git( array( 'config', '--local', 'user.email' ) );
        if ( $email['ok'] && trim( $email['output'] ) !== '' )
            return array();
        $address = trim( (string)$this->options['commit_email'] );
        if ( $address === '' )
            $address = 'static-publish@' . ( preg_replace( '/[^A-Za-z0-9.-]/', '', (string)php_uname( 'n' ) ) ?: 'localhost' );
        return array( '-c', 'user.name=' . $this->options['commit_name'], '-c', 'user.email=' . $address );
    }

    /** @return string the first line of a failed call's error, redacted */
    protected function firstLine( array $r )
    {
        $text = trim( isset( $r['error'] ) && trim( $r['error'] ) !== '' ? $r['error'] : $r['output'] );
        $line = $text === '' ? 'exit ' . $r['exit'] : strtok( $text, "\n" );
        return $this->credentials->redact( preg_replace( '#^(fatal|error): #', '', $line ) );
    }

    /** @return string[] the non-empty lines of a call's output */
    protected function lines( array $r, $separator = "\n" )
    {
        if ( !$r['ok'] )
            return array();
        return array_values( array_filter( explode( $separator, rtrim( $r['output'], "\n" ) ), 'strlen' ) );
    }

    /**
     * git in the checkout.
     *
     * @param string[] $arguments
     * @param bool $remote whether it talks to the remote: then with the credential's environment
     * @param int $seconds
     * @return array ok, exit, output, error (both redacted)
     */
    protected function git( array $arguments, $remote = false, $seconds = 120 )
    {
        self::guard( $arguments );
        $env = array();
        $before = array( '--literal-pathspecs' );
        if ( $remote )
        {
            $g = $this->credentials->gitEnvironment();
            $env = $g['env'];
            $before = array_merge( $before, $g['options'] );
        }
        $arguments = array_merge( $before, array_map( 'strval', $arguments ) );
        $cwd = is_dir( $this->checkout ) ? $this->checkout : (string)getcwd();
        if ( !$remote && $this->options['git_manager'] && class_exists( 'expUpgradeGit' ) && expUpgradeGit::available() )
            $r = expUpgradeGit::git( $cwd, $arguments, $seconds ) + array( 'error' => '' );
        else
            $r = $this->run( $cwd, $arguments, $env, $seconds );
        $r['output'] = $this->credentials->redact( $r['output'] );
        $r['error'] = $this->credentials->redact( $r['error'] );
        return $r;
    }

    /**
     * Runs git without a shell, with an environment of its own: the essentials of the process's (PATH, HOME, the
     * Windows system folders, an SSH agent) and $env. A time limit through timeout(1) where there is one.
     *
     * @param string $cwd
     * @param string[] $arguments
     * @param array $env
     * @param int $seconds
     * @return array ok, exit, output, error
     */
    protected function run( $cwd, array $arguments, array $env, $seconds )
    {
        $command = array_merge( array( 'git' ), $arguments );
        foreach ( array( '/usr/bin/timeout', '/bin/timeout' ) as $timeout )
            if ( is_executable( $timeout ) )
            {
                $command = array_merge( array( $timeout, (string)(int)$seconds ), $command );
                break;
            }
        $base = array( 'GIT_TERMINAL_PROMPT' => '0', 'LC_ALL' => 'C', 'PATH' => getenv( 'PATH' ) ?: '/usr/local/bin:/usr/bin:/bin' );
        foreach ( array( 'HOME', 'SSH_AUTH_SOCK', 'SystemRoot', 'SYSTEMROOT', 'TEMP', 'TMP', 'USERPROFILE', 'APPDATA', 'LOCALAPPDATA', 'ProgramData', 'ComSpec', 'PATHEXT' ) as $name )
            if ( ( $value = getenv( $name ) ) !== false && $value !== '' )
                $base[$name] = $value;
        $pipes = array();
        $process = @proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $cwd, $env + $base );
        if ( !is_resource( $process ) )
            return array( 'ok' => false, 'exit' => 127, 'output' => '', 'error' => 'git cannot be started' );
        fclose( $pipes[0] );
        $output = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        $error = (string)stream_get_contents( $pipes[2] );
        fclose( $pipes[2] );
        $exit = proc_close( $process );
        return array( 'ok' => $exit === 0 || ( $exit === -1 && trim( $error ) === '' ), 'exit' => $exit, 'output' => $output, 'error' => $error );
    }
}
