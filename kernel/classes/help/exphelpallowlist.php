<?php
/**
 * File containing the expHelpAllowlist class.
 *
 * The files the help system may show (doc/eng-us/features/6.0/help-system.md, "Which files are shown"). Only files that are
 * tracked by git below a configured documentation root are listed, and nothing else is ever read or served:
 *
 * - where the list comes from (exphelp.ini [IndexSettings] AllowlistSources[], first one that answers wins):
 *   gitindex   the repository's index file (.git/index, versions 2 to 4), read in PHP: no program is started, so it
 *              works under PHP-FPM and Velocity alike;
 *   gitcommand "git ls-files" through proc_open, for an index this class cannot read;
 *   manifest   share/filelist.md5 (the checksum list that ships with every release), for an installation without .git;
 * - a root is doc/ (Roots[]) or the doc/ directory of an extension: of every active extension (ActiveExtensionDocs,
 *   enabled by default) and of each one named in ExtensionDocDirs[], with the repository the root is in (an extension
 *   usually has its own). Of such an extension the help also lists the files at the top of its directory whose names
 *   match ExtensionTopFiles[] (README*, CHANGELOG*, LICENSE* ...), and nothing else outside its doc/ directory; a
 *   doc/ file of the same name wins over a top file;
 * - a text file of an extension without a suffix (LICENSE, CHANGELOG) is left out when it starts like a program
 *   ("#!" or "<?"), so a script without a suffix is never shown as a page;
 * - a listed path is kept only when it has no empty, "." or ".." segment and no backslash, matches no
 *   ExcludePatterns[] (doc/eng-us/doxygen/ and HTML by default), has a document or image suffix, is a regular file that is not
 *   a symbolic link (nor is any directory above it, up to the root), lies inside the root after realpath(), and is no
 *   larger than MaxFileSize;
 * - the project files (expHelpProjectFiles, exphelp.ini [ProjectFiles]): tracked Markdown files of the installation
 *   root's own repository outside the roots (README.md, SECURITY.md, bin/php/README.md ...), from the same sources,
 *   with the root '' in their entry; a file .gitignore ignores is refused even when it is tracked.
 *
 * A file on disk that git does not track (a local note, a file written by someone else) is therefore not in the list,
 * and every view answers "not found" for it. safePath() repeats the file checks at the time a file is read, so a file
 * replaced by a symbolic link after the list was built is refused too.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpAllowlist
{
    /** The defaults of exphelp.ini [IndexSettings] */
    const DEFAULTS = array(
        'roots'               => array( 'doc' ),
        'extensionDocDirs'    => array(),
        // the active extensions whose documentation is shown (fromIni() fills it when ActiveExtensionDocs is enabled)
        'activeExtensions'    => array(),
        // the files at the top of an extension's directory that are shown besides its doc/ (case-insensitive patterns)
        'extensionTopFiles'   => array( 'README*', 'CHANGELOG*', 'LICENSE*', 'INSTALL*', 'USAGE*', 'COPYING*' ),
        'sources'          => array( 'gitindex', 'gitcommand', 'manifest' ),
        'documentSuffixes'    => array( 'md', 'markdown', 'txt', 'rst' ),
        'plainTextWithoutSuffix' => true,
        'assetSuffixes'       => array( 'png', 'jpg', 'jpeg', 'gif', 'webp' ),
        'excludePatterns'     => array( 'doc/eng-us/doxygen/*', 'doc/README.md', '*.html', '*.htm', '*.svg', '*.php' ),
        'maxFileSize'         => 2097152,
        // the project files outside doc/ (expHelpProjectFiles configuration, exphelp.ini [ProjectFiles]); null: none
        'projectFiles'        => null,
        // the tracked paths (relative to the installation root) to use instead of the sources: tests and tools only
        'tracked'             => null,
    );

    /** The manifest of a repository, relative to its top directory */
    const MANIFEST = 'share/filelist.md5';

    /** @var string the installation root, without a trailing slash */
    protected $rootDir;

    /** @var array the configuration (DEFAULTS keys) */
    protected $config;

    /** @var array|null path => entry, see files() */
    protected $files = null;

    /** @var array root => the source that listed it */
    protected $usedSources = array();

    /** @var array "<source>\0<repository top>" => the paths it listed (null: it did not answer), within this object */
    protected $listed = array();

    /** Option names also accepted by the constructor => DEFAULTS key */
    const OPTION_ALIASES = array( 'suffixes' => 'documentSuffixes', 'asset_suffixes' => 'assetSuffixes', 'exclude' => 'excludePatterns',
                                  'max_size' => 'maxFileSize', 'extension_doc_dirs' => 'extensionDocDirs',
                                  'plain_text_without_suffix' => 'plainTextWithoutSuffix', 'project_files' => 'projectFiles',
                                  'active_extensions' => 'activeExtensions', 'extension_top_files' => 'extensionTopFiles' );

    /** @var expHelpProjectFiles|null|false the project files, false until made */
    protected $projectFiles = false;

    /**
     * new expHelpAllowlist( '/path/to/root', array( 'roots' => ... ) ), or new expHelpAllowlist( array( 'root' => ..., ... ) )
     * with the option names root, roots, sources, suffixes, asset_suffixes, exclude, max_size, extension_doc_dirs,
     * tracked (or the DEFAULTS keys).
     *
     * @param string|array|null $rootDir the installation root (default expHelp::installRoot(), else the current
     *                                   directory), or the options
     * @param array $config DEFAULTS keys; missing ones take the default
     */
    public function __construct( $rootDir = null, array $config = array() )
    {
        if ( is_array( $rootDir ) )
        {
            $config = $rootDir + $config;
            $rootDir = isset( $config['root'] ) ? $config['root'] : null;
        }
        unset( $config['root'] );
        foreach ( self::OPTION_ALIASES as $alias => $key )
            if ( array_key_exists( $alias, $config ) )
            {
                $config[$key] = $config[$alias];
                unset( $config[$alias] );
            }
        if ( $rootDir === null )
            $rootDir = class_exists( 'expHelp' ) && class_exists( 'eZSys' ) ? expHelp::installRoot() : getcwd();
        $this->rootDir = rtrim( str_replace( '\\', '/', (string)$rootDir ), '/' );
        $this->config = array_merge( self::DEFAULTS, array_intersect_key( $config, self::DEFAULTS ) );
    }

    /**
     * The allowlist of the installation, configured by exphelp.ini [IndexSettings].
     *
     * @param string|null $rootDir the installation root (default expHelp::installRoot())
     * @return expHelpAllowlist
     */
    public static function fromIni( $rootDir = null )
    {
        return new self( $rootDir, self::iniConfig() );
    }

    /**
     * The configuration from exphelp.ini [IndexSettings]; the defaults where it is missing.
     *
     * @return array
     */
    public static function iniConfig()
    {
        $config = array();
        if ( !class_exists( 'eZINI' ) || !eZINI::exists( 'exphelp.ini' ) )
            return $config;
        $ini = eZINI::instance( 'exphelp.ini' );
        $map = array( 'Roots' => 'roots', 'ExtensionDocDirs' => 'extensionDocDirs', 'AllowlistSources' => 'sources',
                      'DocumentSuffixes' => 'documentSuffixes', 'AssetSuffixes' => 'assetSuffixes',
                      'ExcludePatterns' => 'excludePatterns', 'ExtensionTopFiles' => 'extensionTopFiles' );
        foreach ( $map as $name => $key )
            if ( $ini->hasVariable( 'IndexSettings', $name ) )
                $config[$key] = self::listSetting( $ini->variable( 'IndexSettings', $name ) );
        if ( $ini->hasVariable( 'IndexSettings', 'PlainTextWithoutSuffix' ) )
            $config['plainTextWithoutSuffix'] = $ini->variable( 'IndexSettings', 'PlainTextWithoutSuffix' ) === 'enabled';
        if ( $ini->hasVariable( 'IndexSettings', 'MaxFileSize' ) )
            $config['maxFileSize'] = max( 1024, (int)$ini->variable( 'IndexSettings', 'MaxFileSize' ) );
        // every active extension's own documentation, found when the help looks (an extension switched on or off
        // changes the list, and with it the stamp)
        $active = $ini->hasVariable( 'IndexSettings', 'ActiveExtensionDocs' ) ? $ini->variable( 'IndexSettings', 'ActiveExtensionDocs' ) : 'enabled';
        if ( in_array( strtolower( (string)$active ), array( 'enabled', 'true' ), true ) && class_exists( 'eZExtension' ) )
            $config['activeExtensions'] = array_values( array_unique( array_map( 'strval', (array)eZExtension::activeExtensions() ) ) );
        // the project files ([ProjectFiles]); a server process started before the class existed has none
        if ( class_exists( 'expHelpProjectFiles' ) )
            $config['projectFiles'] = expHelpProjectFiles::iniConfig();
        return $config;
    }

    /**
     * The project files of the help (README.md, SECURITY.md ... outside doc/), or null when there are none.
     *
     * @return expHelpProjectFiles|null
     */
    public function projectFiles()
    {
        if ( $this->projectFiles === false )
        {
            $config = $this->config['projectFiles'];
            $this->projectFiles = is_array( $config ) && class_exists( 'expHelpProjectFiles' ) ? new expHelpProjectFiles( $config ) : null;
            if ( $this->projectFiles !== null && !$this->projectFiles->isEnabled() )
                $this->projectFiles = null;
        }
        return $this->projectFiles;
    }

    /**
     * Whether a path relative to the installation root has the name of a project file (expHelpProjectFiles::isCandidate():
     * enabled, matched by Include[] and none of the paths that never are). Whether it is listed is contains()'s question.
     *
     * @param string $path
     * @return bool
     */
    public function isProjectPath( $path )
    {
        $projectFiles = $this->projectFiles();
        return $projectFiles !== null && $projectFiles->isCandidate( $path );
    }

    /**
     * An INI list whose items may also be comma separated ("md,txt" or "md" and "txt").
     *
     * @param mixed $value
     * @return string[]
     */
    public static function listSetting( $value )
    {
        $items = array();
        foreach ( (array)$value as $item )
            foreach ( explode( ',', (string)$item ) as $part )
                if ( ( $part = trim( $part ) ) !== '' )
                    $items[] = $part;
        return array_values( array_unique( $items ) );
    }

    /**
     * @return string the installation root
     */
    public function rootDir()
    {
        return $this->rootDir;
    }

    /**
     * @return array the configuration in use
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * The documentation roots, relative to the installation root: Roots[] and, for each extension (extensions()),
     * extension/<name>/doc.
     *
     * @return string[]
     */
    public function roots()
    {
        $roots = array();
        foreach ( $this->config['roots'] as $root )
            if ( ( $root = self::normalize( $root ) ) !== null )
                $roots[] = $root;
        foreach ( $this->extensions() as $extension )
            $roots[] = 'extension/' . $extension . '/doc';
        return array_values( array_unique( $roots ) );
    }

    /**
     * The extensions whose documentation the help shows: the active ones (ActiveExtensionDocs) and those named in
     * ExtensionDocDirs[], in that order, each once; a name that is no plain directory name is left out.
     *
     * @return string[]
     */
    public function extensions()
    {
        $names = array();
        foreach ( array_merge( (array)$this->config['activeExtensions'], (array)$this->config['extensionDocDirs'] ) as $extension )
            if ( is_string( $extension ) && preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $extension ) && strpos( $extension, '..' ) === false )
                $names[$extension] = true;
        return array_keys( $names );
    }

    /**
     * Whether a file name is one of ExtensionTopFiles[] (README.md, CHANGELOG, LICENSE.txt ...), compared without case.
     *
     * @param string $name a file name without directory
     * @return bool
     */
    public function isExtensionTopFile( $name )
    {
        $name = (string)$name;
        if ( $name === '' || $name[0] === '.' || strpos( $name, '/' ) !== false )
            return false;
        foreach ( (array)$this->config['extensionTopFiles'] as $pattern )
            if ( is_string( $pattern ) && $pattern !== '' && fnmatch( $pattern, $name, FNM_CASEFOLD ) )
                return true;
        return false;
    }

    /**
     * A path relative to the installation root in canonical form, or null when it cannot be one: no NUL, no
     * backslash, no empty, "." or ".." segment, no leading slash after trimming, at most 512 bytes.
     *
     * @param string $path
     * @return string|null
     */
    public static function normalize( $path )
    {
        $path = (string)$path;
        if ( $path === '' || strlen( $path ) > 512 || strpbrk( $path, "\0\\\r\n" ) !== false )
            return null;
        $path = trim( $path, '/' );
        if ( $path === '' )
            return null;
        foreach ( explode( '/', $path ) as $segment )
            if ( $segment === '' || $segment === '.' || $segment === '..' )
                return null;
        return $path;
    }

    /**
     * The files of the help: path relative to the installation root => array( 'path', 'root', 'kind' => document|asset,
     * 'format' => markdown|text|image, 'mtime', 'size' ), sorted by path.
     *
     * @return array
     */
    public function entries()
    {
        if ( $this->files !== null )
            return $this->files;
        $files = array();
        foreach ( $this->roots() as $root )
        {
            $rootReal = realpath( $this->rootDir . '/' . $root );
            if ( $rootReal === false || !is_dir( $rootReal ) || is_link( $this->rootDir . '/' . $root ) )
                continue;
            foreach ( $this->trackedBelow( $root ) as $path )
            {
                $entry = $this->entry( $path, $root, $rootReal );
                if ( $entry !== null )
                    $files[$path] = $entry;
            }
        }
        // the README, CHANGELOG, LICENSE ... at the top of each extension's directory: its root is the extension's
        // directory, but only files directly in it whose names match ExtensionTopFiles[] are taken
        foreach ( $this->extensions() as $extension )
        {
            $root = 'extension/' . $extension;
            $rootReal = realpath( $this->rootDir . '/' . $root );
            if ( $rootReal === false || !is_dir( $rootReal ) || is_link( $this->rootDir . '/' . $root ) )
                continue;
            foreach ( $this->trackedBelow( $root ) as $path )
            {
                $name = substr( $path, strlen( $root ) + 1 );
                if ( !$this->isExtensionTopFile( $name ) || isset( $files[$root . '/doc/' . $name] ) )
                    continue;
                $entry = $this->entry( $path, $root, $rootReal );
                if ( $entry !== null && $entry['kind'] === 'document' )
                    $files[$path] = $entry;
            }
        }
        foreach ( $this->projectEntries() as $path => $entry )
            if ( !isset( $files[$path] ) )
                $files[$path] = $entry;
        ksort( $files, SORT_STRING );
        return $this->files = $files;
    }

    /**
     * The project files (expHelpProjectFiles): the tracked files of the installation root's own repository whose
     * name Include[] matches, that no ignore rule of git ignores, and that pass the file checks. Their entry's root
     * is '' (the installation root). Only the git index, "git ls-files" and the release manifest list them, never
     * the directory, so a file that is not committed is never one, whatever the settings say.
     *
     * @return array path => entry
     */
    protected function projectEntries()
    {
        $projectFiles = $this->projectFiles();
        if ( $projectFiles === null )
            return array();
        $rootReal = realpath( $this->rootDir );
        if ( $rootReal === false )
            return array();
        $gitdir = is_array( $this->config['tracked'] ) ? null : self::gitDir( $this->rootDir );
        $entries = array();
        foreach ( $this->trackedInRoot() as $path )
        {
            if ( !$projectFiles->isCandidate( $path ) || $this->excluded( $path ) || $projectFiles->isIgnored( $this->rootDir, $path, $gitdir ) )
                continue;
            if ( $this->kindOf( $path ) !== array( 'document', 'markdown' ) || !$this->checkFile( $path, '', $rootReal ) )
                continue;
            $size = filesize( $this->rootDir . '/' . $path );
            if ( $size === false || $size > $this->config['maxFileSize'] )
                continue;
            $entries[$path] = array( 'path' => $path, 'root' => '', 'kind' => 'document', 'format' => 'markdown',
                                     'mtime' => (int)filemtime( $this->rootDir . '/' . $path ), 'size' => (int)$size, 'project' => true );
        }
        return $entries;
    }

    /**
     * The tracked files of the repository whose top is the installation root (the 'tracked' option, else the first
     * source that answers); empty when the installation root is not the top of a repository or a release.
     *
     * @return string[]
     */
    protected function trackedInRoot()
    {
        if ( is_array( $this->config['tracked'] ) )
        {
            $this->usedSources['project files'] = 'given';
            return array_values( array_filter( array_map( 'strval', $this->config['tracked'] ), 'strlen' ) );
        }
        $this->usedSources['project files'] = 'none';
        $gitdir = self::gitDir( $this->rootDir );
        foreach ( $this->config['sources'] as $source )
        {
            $paths = null;
            if ( $source === 'gitindex' && $gitdir !== null )
                $paths = self::readGitIndex( $gitdir . '/index', self::hashLength( $gitdir ) );
            elseif ( $source === 'gitcommand' && $gitdir !== null )
                $paths = self::runGitLsFiles( $this->rootDir );
            elseif ( $source === 'manifest' )
                $paths = self::readManifest( $this->rootDir . '/' . self::MANIFEST );
            if ( is_array( $paths ) )
            {
                $this->usedSources['project files'] = $source;
                return $paths;
            }
        }
        return array();
    }

    /**
     * The paths of the help's files, relative to the installation root (doc/...), sorted.
     *
     * @return string[]
     */
    public function files()
    {
        return array_keys( $this->entries() );
    }

    /**
     * Whether a path relative to the installation root is in the list and still passes the file checks.
     *
     * @param string $repoPath
     * @return bool
     */
    public function allows( $repoPath )
    {
        return $this->safePath( $repoPath ) !== null;
    }

    /**
     * Whether a path is in the list.
     *
     * @param string $path
     * @return bool
     */
    public function contains( $path )
    {
        $path = self::normalize( $path );
        if ( $path === null )
            return false;
        $files = $this->entries();
        return isset( $files[$path] );
    }

    /**
     * The absolute file name of a listed path, checked again now (still a regular file, no symbolic link on the way,
     * still inside its root); null otherwise.
     *
     * @param string $path
     * @param array|null $files the entries to check against (a cached copy), default entries()
     * @return string|null
     */
    public function safePath( $path, ?array $files = null )
    {
        $path = self::normalize( $path );
        if ( $files === null )
            $files = $this->entries();
        if ( $path === null || !isset( $files[$path] ) )
            return null;
        $root = $files[$path]['root'];
        $rootReal = realpath( $this->rootDir . '/' . $root );
        if ( $rootReal === false )
            return null;
        return $this->checkFile( $path, $root, $rootReal ) ? $this->rootDir . '/' . $path : null;
    }

    /**
     * Identifies the state of the sources: the index or manifest files with their size and time, and the
     * configuration. It changes when a file is added to or removed from git, or the settings change.
     *
     * @return string
     */
    public function stamp()
    {
        $parts = array( json_encode( $this->config ), json_encode( $this->roots() ) );
        $tops = array();
        foreach ( $this->roots() as $root )
        {
            $repo = $this->repositoryOf( $root );
            if ( $repo !== null )
                $tops[$repo['top']] = $repo['gitdir'];
        }
        // the project files come from the installation root's repository, and its ignore rules
        if ( $this->projectFiles() !== null )
        {
            $gitdir = self::gitDir( $this->rootDir );
            $tops[$this->rootDir] = $gitdir;
            if ( is_file( $this->rootDir . '/.gitignore' ) )
                $parts[] = self::fileState( $this->rootDir . '/.gitignore' );
        }
        foreach ( $tops as $top => $gitdir )
            foreach ( array( $gitdir ? $gitdir . '/index' : null, $top . '/' . self::MANIFEST ) as $file )
                if ( $file !== null && is_file( $file ) )
                    $parts[] = self::fileState( $file );
        return sha1( implode( "\n", $parts ) );
    }

    /**
     * A file's size and time, and its content's hash while the time is too recent to tell two versions apart.
     *
     * A time is whole seconds, and the git index keeps its size when only a page changes (the blob hash in it is
     * replaced): a commit in the same second as the last look left size and time as they were, so the change went
     * unseen until something else changed. The same answer git gives its own "racy" index entries: a file changed
     * within the last two seconds is identified by its content as well. Once it is older, size and time are enough
     * again (a later write gives a later time); the stamp then changes once, which only makes the next look fresh.
     *
     * @param string $file
     * @return string
     */
    protected static function fileState( $file )
    {
        clearstatcache( true, $file );
        $mtime = (int)filemtime( $file );
        $state = $file . ':' . (int)filesize( $file ) . ':' . $mtime;
        if ( time() - $mtime < 2 )
        {
            $hash = @sha1_file( $file );
            $state .= ':' . ( is_string( $hash ) ? $hash : 'unreadable' );
        }
        return $state;
    }

    /**
     * Which source listed each root, after files() (root => gitindex|gitcommand|manifest|none).
     *
     * @return array
     */
    public function usedSources()
    {
        return $this->usedSources;
    }

    /**
     * The tracked paths below a root, relative to the installation root, from the first source that answers.
     *
     * @param string $root
     * @return string[]
     */
    protected function trackedBelow( $root )
    {
        if ( is_array( $this->config['tracked'] ) )
        {
            $this->usedSources[$root] = 'given';
            return array_values( array_filter( $this->config['tracked'], function ( $path ) use ( $root ) {
                return strncmp( (string)$path, $root . '/', strlen( $root ) + 1 ) === 0;
            } ) );
        }
        $this->usedSources[$root] = 'none';
        $repo = $this->repositoryOf( $root );
        if ( $repo === null )
            return array();
        foreach ( $this->config['sources'] as $source )
        {
            $paths = null;
            // one read per repository and source: the doc/ and the top files of the extensions in one repository,
            // and doc/, share it
            $key = $source . "\0" . $repo['top'];
            if ( array_key_exists( $key, $this->listed ) )
                $paths = $this->listed[$key];
            else
            {
                if ( $source === 'gitindex' && $repo['gitdir'] !== null )
                    $paths = self::readGitIndex( $repo['gitdir'] . '/index', self::hashLength( $repo['gitdir'] ) );
                elseif ( $source === 'gitcommand' && $repo['gitdir'] !== null )
                    $paths = self::runGitLsFiles( $repo['top'] );
                elseif ( $source === 'manifest' )
                    $paths = self::readManifest( $repo['top'] . '/' . self::MANIFEST );
                $this->listed[$key] = $paths;
            }
            if ( !is_array( $paths ) )
                continue;
            $this->usedSources[$root] = $source;
            $below = array();
            $prefix = $repo['prefix'];
            // '' when the root is the top of its repository (an extension's directory): every path is below it
            $rootInRepo = $prefix === '' ? $root : (string)substr( $root, strlen( $prefix ) + 1 );
            foreach ( $paths as $path )
                if ( $rootInRepo === '' || strncmp( $path, $rootInRepo . '/', strlen( $rootInRepo ) + 1 ) === 0 )
                    $below[] = $prefix === '' ? $path : $prefix . '/' . $path;
            return $below;
        }
        return array();
    }

    /**
     * The repository a root belongs to: the nearest directory at or above it, up to the installation root, that has
     * a .git (directory, or file pointing to one) or a share/filelist.md5.
     *
     * @param string $root
     * @return array|null array( 'top' => absolute directory, 'prefix' => its path relative to the installation root,
     *                    'gitdir' => absolute git directory or null )
     */
    protected function repositoryOf( $root )
    {
        $segments = explode( '/', $root );
        for ( $n = count( $segments ); $n >= 0; --$n )
        {
            $prefix = implode( '/', array_slice( $segments, 0, $n ) );
            $top = $prefix === '' ? $this->rootDir : $this->rootDir . '/' . $prefix;
            $gitdir = self::gitDir( $top );
            if ( $gitdir !== null || is_file( $top . '/' . self::MANIFEST ) )
                return array( 'top' => $top, 'prefix' => $prefix, 'gitdir' => $gitdir );
        }
        return null;
    }

    /**
     * The git directory of a work tree: <top>/.git, or the directory a .git file points to (a linked work tree).
     *
     * @param string $top
     * @return string|null
     */
    public static function gitDir( $top )
    {
        $dotGit = $top . '/.git';
        if ( is_dir( $dotGit ) )
            return is_file( $dotGit . '/index' ) ? $dotGit : null;
        if ( is_file( $dotGit ) && filesize( $dotGit ) < 4096 )
        {
            $content = (string)file_get_contents( $dotGit );
            if ( preg_match( '/^gitdir:\s*(.+?)\s*$/m', $content, $m ) )
            {
                $dir = $m[1][0] === '/' ? $m[1] : $top . '/' . $m[1];
                return is_file( $dir . '/index' ) ? rtrim( $dir, '/' ) : null;
            }
        }
        return null;
    }

    /**
     * 20 bytes for SHA-1 repositories, 32 for those with extensions.objectformat = sha256.
     *
     * @param string $gitdir
     * @return int
     */
    protected static function hashLength( $gitdir )
    {
        $configFile = $gitdir . '/config';
        if ( !is_file( $configFile ) && is_file( $gitdir . '/commondir' ) )
            $configFile = $gitdir . '/' . trim( (string)file_get_contents( $gitdir . '/commondir' ) ) . '/config';
        if ( is_file( $configFile ) && preg_match( '/objectformat\s*=\s*sha256/i', (string)file_get_contents( $configFile ) ) )
            return 32;
        return 20;
    }

    /**
     * The paths of the regular files in a git index file (versions 2, 3 and 4; symbolic links, submodules, sparse
     * directory entries, intent-to-add entries and unmerged duplicates left out). null when the file cannot be read.
     *
     * @param string $file
     * @param int $hashLength 20 (SHA-1) or 32 (SHA-256)
     * @return string[]|null
     */
    public static function readGitIndex( $file, $hashLength = 20 )
    {
        if ( !is_file( $file ) || !is_readable( $file ) )
            return null;
        $data = @file_get_contents( $file );
        if ( !is_string( $data ) || strlen( $data ) < 12 || substr( $data, 0, 4 ) !== 'DIRC' )
            return null;
        $header = unpack( 'Nversion/Ncount', substr( $data, 4, 8 ) );
        $version = $header['version'];
        if ( $version < 2 || $version > 4 )
            return null;
        $length = strlen( $data );
        $offset = 12;
        $previous = '';
        $paths = array();
        for ( $i = 0; $i < $header['count']; ++$i )
        {
            $start = $offset;
            if ( $offset + 40 + $hashLength + 2 > $length )
                return null;
            $mode = unpack( 'N', substr( $data, $offset + 24, 4 ) )[1];
            $offset += 40 + $hashLength;
            $flags = unpack( 'n', substr( $data, $offset, 2 ) )[1];
            $offset += 2;
            $extended = 0;
            if ( $flags & 0x4000 )
            {
                if ( $version < 3 || $offset + 2 > $length )
                    return null;
                $extended = unpack( 'n', substr( $data, $offset, 2 ) )[1];
                $offset += 2;
            }
            if ( $version === 4 )
            {
                // the path is the previous one, less N bytes at its end, plus a NUL terminated suffix
                // (git's offset varint: each continuation byte adds one before shifting)
                if ( $offset >= $length )
                    return null;
                $byte = ord( $data[$offset++] );
                $strip = $byte & 0x7f;
                while ( $byte & 0x80 )
                {
                    if ( $offset >= $length || $strip > 0xffffff )
                        return null;
                    $byte = ord( $data[$offset++] );
                    $strip = ( ( $strip + 1 ) << 7 ) | ( $byte & 0x7f );
                }
                $end = strpos( $data, "\0", $offset );
                if ( $end === false || $strip > strlen( $previous ) )
                    return null;
                $path = substr( $previous, 0, strlen( $previous ) - $strip ) . substr( $data, $offset, $end - $offset );
                $offset = $end + 1;
            }
            else
            {
                $end = strpos( $data, "\0", $offset );
                if ( $end === false )
                    return null;
                $path = substr( $data, $offset, $end - $offset );
                // entries are padded with 1 to 8 NUL bytes to a multiple of 8 from their start
                $entryLength = $end - $start + 1;
                $offset = $start + ( ( $entryLength + 7 ) & ~7 );
            }
            $previous = $path;
            $type = $mode & 0170000;
            if ( $type !== 0100000 || ( $extended & 0x2000 ) )
                continue;
            $paths[$path] = true;
        }
        return array_keys( $paths );
    }

    /**
     * The tracked files of a work tree from "git ls-files -z"; null when git cannot be run.
     *
     * @param string $top
     * @return string[]|null
     */
    public static function runGitLsFiles( $top )
    {
        if ( !function_exists( 'proc_open' ) )
            return null;
        $command = array( 'git', '-c', 'safe.directory=' . $top, '-C', $top, 'ls-files', '-z', '--', '.' );
        $process = @proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return null;
        $output = stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        if ( proc_close( $process ) !== 0 || !is_string( $output ) )
            return null;
        return array_values( array_filter( explode( "\0", $output ), 'strlen' ) );
    }

    /**
     * When each file below the roots was last changed by a commit: path relative to the installation root => Unix
     * time. One "git log" per repository; empty when git cannot be run (no .git, no proc_open).
     *
     * @return array
     */
    public function lastChanged()
    {
        return $this->history()['changed'];
    }

    /**
     * The commit history of the roots, from one "git log" per repository (empty without git):
     *   'changed' => path => Unix time of the last commit that changed it,
     *   'recent'  => the latest changes, newest first, one per path: list of array( 'path', 'time', 'subject' ).
     *
     * @param int $recentLimit how many recent changes to keep
     * @return array
     */
    public function history( $recentLimit = 200 )
    {
        $changed = array();
        $recent = array();
        if ( is_array( $this->config['tracked'] ) || !function_exists( 'proc_open' ) )
            return array( 'changed' => $changed, 'recent' => $recent );
        $logs = array();
        foreach ( $this->roots() as $root )
        {
            // an active extension without a doc/ directory has nothing to look up
            if ( !is_dir( $this->rootDir . '/' . $root ) )
                continue;
            $repo = $this->repositoryOf( $root );
            if ( $repo === null || $repo['gitdir'] === null )
                continue;
            $logs[] = array( $repo, array( $repo['prefix'] === '' ? $root : substr( $root, strlen( $repo['prefix'] ) + 1 ) ) );
        }
        // the project files: one log of the installation root's repository for exactly those files
        $projectPaths = array();
        foreach ( $this->entries() as $path => $entry )
            if ( $entry['root'] === '' )
                $projectPaths[] = $path;
        if ( $projectPaths && count( $projectPaths ) <= 500 && self::gitDir( $this->rootDir ) !== null )
            $logs[] = array( array( 'top' => $this->rootDir, 'prefix' => '', 'gitdir' => self::gitDir( $this->rootDir ) ),
                             array_map( function ( $path ) { return ':(literal)' . $path; }, $projectPaths ) );
        foreach ( $logs as $log )
        {
            list( $repo, $pathspecs ) = $log;
            $command = array_merge( array( 'git', '-c', 'safe.directory=' . $repo['top'], '-C', $repo['top'], 'log', '--no-renames',
                                           '--format=%x01%ct%x02%s', '--name-only', '--' ), $pathspecs );
            $process = @proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
            if ( !is_resource( $process ) )
                continue;
            $output = stream_get_contents( $pipes[1] );
            fclose( $pipes[1] );
            fclose( $pipes[2] );
            if ( proc_close( $process ) !== 0 || !is_string( $output ) )
                continue;
            $time = 0;
            $subject = '';
            foreach ( explode( "\n", $output ) as $line )
            {
                if ( $line === '' )
                    continue;
                if ( $line[0] === "\x01" )
                {
                    list( $time, $subject ) = array_pad( explode( "\x02", substr( $line, 1 ), 2 ), 2, '' );
                    $time = (int)$time;
                    continue;
                }
                $path = $repo['prefix'] === '' ? $line : $repo['prefix'] . '/' . $line;
                if ( isset( $changed[$path] ) )
                    continue;
                $changed[$path] = $time;
                if ( count( $recent ) < $recentLimit )
                    $recent[] = array( 'path' => $path, 'time' => $time, 'subject' => self::cleanSubject( $subject ) );
            }
        }
        usort( $recent, function ( $a, $b ) { return $b['time'] <=> $a['time']; } );
        return array( 'changed' => $changed, 'recent' => $recent );
    }

    /**
     * A commit subject as shown on the dashboard: one line, no control characters, at most 160 characters.
     *
     * @param string $subject
     * @return string
     */
    protected static function cleanSubject( $subject )
    {
        $subject = trim( (string)preg_replace( '/[\x00-\x1f\x7f]+/', ' ', (string)$subject ) );
        if ( !preg_match( '//u', $subject ) )
            $subject = (string)mb_convert_encoding( $subject, 'UTF-8', 'ISO-8859-1' );
        return mb_strlen( $subject, 'UTF-8' ) > 160 ? mb_substr( $subject, 0, 159, 'UTF-8' ) . "\u{2026}" : $subject;
    }

    /**
     * The paths of a checksum list ("<md5>  <path>" per line); null without one.
     *
     * @param string $file
     * @return string[]|null
     */
    public static function readManifest( $file )
    {
        if ( !is_file( $file ) || !is_readable( $file ) )
            return null;
        $paths = array();
        foreach ( file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line )
            if ( preg_match( '/^[0-9a-f]{32}\s+\*?(.+)$/', $line, $m ) )
                $paths[] = $m[1];
        return $paths;
    }

    /**
     * The list entry of a tracked path, or null when it is not shown.
     *
     * @param string $path
     * @param string $root
     * @param string $rootReal
     * @return array|null
     */
    protected function entry( $path, $root, $rootReal )
    {
        if ( self::normalize( $path ) !== $path || $this->excluded( $path ) || preg_match( '#(^|/)\.#', $path ) )
            return null; // hidden files and directories (.git, .htaccess, .foo.md) are never shown
        $kind = $this->kindOf( $path );
        if ( $kind === null || !$this->checkFile( $path, $root, $rootReal ) )
            return null;
        $size = filesize( $this->rootDir . '/' . $path );
        if ( $size === false || $size > $this->config['maxFileSize'] )
            return null;
        // an extension's text file without a real suffix that is a program (#!/usr/bin/env php, <?php) is no page
        if ( $kind[1] === 'text' && strncmp( $path, 'extension/', 10 ) === 0 && self::looksLikeProgram( $this->rootDir . '/' . $path ) )
            return null;
        return array( 'path' => $path, 'root' => $root, 'kind' => $kind[0], 'format' => $kind[1],
                      'mtime' => (int)filemtime( $this->rootDir . '/' . $path ), 'size' => (int)$size );
    }

    /**
     * Whether a file starts like a program or a source file: "#!", "<?" (PHP, XML processing instruction) after an
     * optional byte order mark and white space.
     *
     * @param string $file
     * @return bool
     */
    public static function looksLikeProgram( $file )
    {
        $head = @file_get_contents( $file, false, null, 0, 64 );
        if ( !is_string( $head ) )
            return true;
        $head = ltrim( preg_replace( '/^\xEF\xBB\xBF/', '', $head ) );
        return strncmp( $head, '#!', 2 ) === 0 || strncmp( $head, '<?', 2 ) === 0;
    }

    /**
     * Whether a path matches one of ExcludePatterns[] (fnmatch on the path relative to the installation root; a
     * pattern without "/" also matches the file name alone).
     *
     * @param string $path
     * @return bool
     */
    public function excluded( $path )
    {
        $base = basename( $path );
        foreach ( $this->config['excludePatterns'] as $pattern )
        {
            if ( fnmatch( $pattern, $path, FNM_PATHNAME ) || fnmatch( $pattern, $path ) )
                return true;
            if ( strpos( $pattern, '/' ) === false && fnmatch( $pattern, $base ) )
                return true;
        }
        return false;
    }

    /**
     * document/markdown, document/text, asset/image, or null for a file the help does not show.
     *
     * @param string $path
     * @return string[]|null
     */
    public function kindOf( $path )
    {
        $base = basename( $path );
        $dot = strrpos( $base, '.' );
        $suffix = $dot === false || $dot === 0 ? '' : strtolower( substr( $base, $dot + 1 ) );
        $documents = array_map( 'strtolower', $this->config['documentSuffixes'] );
        if ( $suffix !== '' && in_array( $suffix, $documents, true ) )
            return array( 'document', in_array( $suffix, array( 'md', 'markdown' ), true ) ? 'markdown' : 'text' );
        if ( $suffix !== '' && in_array( $suffix, array_map( 'strtolower', $this->config['assetSuffixes'] ), true ) )
            return array( 'asset', 'image' );
        // CHANGELOG-3.2-1, CHANGELOG-4.0.0-to-4.1.0alpha1, release-overview: plain text without a real suffix
        if ( $this->config['plainTextWithoutSuffix'] && $base[0] !== '.' && ( $suffix === '' || preg_match( '/^[0-9][0-9a-z-]*$/', $suffix ) ) )
            return array( 'document', 'text' );
        return null;
    }

    /**
     * Whether a path is a regular file inside its root with no symbolic link from the root down to it.
     *
     * @param string $path
     * @param string $root
     * @param string $rootReal
     * @return bool
     */
    protected function checkFile( $path, $root, $rootReal )
    {
        // root '': a project file, relative to the installation root itself
        if ( $root === '' )
        {
            if ( self::normalize( $path ) !== $path || !$this->isProjectPath( $path ) )
                return false;
            $current = $this->rootDir;
            $rest = $path;
        }
        elseif ( strncmp( $path, $root . '/', strlen( $root ) + 1 ) !== 0 )
            return false;
        else
        {
            $current = $this->rootDir . '/' . $root;
            $rest = substr( $path, strlen( $root ) + 1 );
        }
        foreach ( explode( '/', $rest ) as $segment )
        {
            $current .= '/' . $segment;
            if ( is_link( $current ) )
                return false;
        }
        $real = realpath( $current );
        return $real !== false && is_file( $real ) && strncmp( $real, $rootReal . '/', strlen( $rootReal ) + 1 ) === 0;
    }
}
