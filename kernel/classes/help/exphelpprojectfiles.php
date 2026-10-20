<?php
/**
 * File containing the expHelpProjectFiles class.
 *
 * The project files of the help (doc/en/us/features/6.0/help-system.md, "Project files"): the Markdown files of the
 * repository outside doc/ that document the project itself, README.md, INSTALL.md, SECURITY.md, CONTRIBUTING.md,
 * COPYRIGHT.md, LICENSE.md and the README.md files of bin/, design/ and kernel/. They are English only, like
 * doc/changelogs/, and each has the id "project/<path in the repository>" ("project/README.md").
 *
 * Which files: exphelp.ini [ProjectFiles] Include[] and Exclude[] (shell patterns on the path in the repository; "*"
 * stays inside one directory, "**" crosses directories). On top of them, and not to be changed by any setting:
 *
 * - only files git tracks are listed (the allowlist reads them from the git index, "git ls-files" or the release
 *   manifest, never from the directory): a private note in the root that is not committed is never shown, searched
 *   or linked, whatever Include[] says;
 * - a tracked file in a place .gitignore (or .git/info/exclude) ignores is refused too (isIgnored());
 * - only Markdown (.md, .markdown);
 * - never below doc/ (the documentation itself), extension/ (the extensions have their own category, Extensions),
 *   a tests/ directory anywhere, var/, vendor/, settings/override/, settings/siteaccess/, node_modules/, and never a
 *   hidden file or a file in a hidden directory, .github/ included (NEVER, isNever());
 * - the file checks of expHelpAllowlist (regular file, no symbolic link on the way, inside the installation root,
 *   at most MaxFileSize).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpProjectFiles
{
    /** The first segment of every project file's id: project/README.md */
    const ID_PREFIX = 'project';

    /** The defaults of exphelp.ini [ProjectFiles] */
    const DEFAULTS = array(
        'enabled'       => true,
        'include'       => array( '*.md', 'bin/**/README.md', 'design/*/README.md', 'kernel/**/README.md' ),
        // test fixtures and the copies of other projects (a JavaScript library's own README.md)
        'exclude'       => array( '**/fixtures/**', '**/_fixtures/**', '**/javascript/**', '**/third-party/**', '**/thirdparty/**' ),
        'category'      => 'project-files',
        'type'          => 'project',
        'audience'      => array( 'developer', 'admin' ),
        'audienceRules' => array( 'README.md;admin,developer,editor,user,partner,hosting',
                                  'INSTALL.md;admin,developer,hosting',
                                  'SECURITY.md;admin,developer,hosting',
                                  'CONTRIBUTING.md;developer,supporter,advocate,partner',
                                  'CODE_OF_CONDUCT.md;developer,supporter,advocate,partner,trainer,user',
                                  'COPYRIGHT.md;admin,developer,partner,businesses',
                                  'LICENSE.md;admin,developer,partner,businesses' ),
    );

    /**
     * Never a project file, whatever the settings say: shell patterns on the path in the repository (FNM_PATHNAME
     * off, so "*" crosses directories). extension/: the extensions' documentation is the category Extensions;
     * tests/ anywhere (the shell pattern **\/tests/**): the tests and their fixtures are not documentation.
     */
    const NEVER = array( 'doc/*', 'extension/*', 'tests/*', '*/tests/*', 'var/*', 'vendor/*', 'settings/override/*',
                         'settings/siteaccess/*', '*/node_modules/*', 'node_modules/*', '*/vendor/*' );

    /** The only suffixes a project file may have */
    const SUFFIXES = array( 'md', 'markdown' );

    /** @var array the configuration (DEFAULTS keys) */
    protected $config;

    /** @var array gitignore file => list of rules, read once */
    protected $ignoreRules = array();

    /**
     * @param array $config DEFAULTS keys; missing ones take the default
     */
    public function __construct( array $config = array() )
    {
        $config = array_merge( self::DEFAULTS, array_intersect_key( $config, self::DEFAULTS ) );
        $config['enabled'] = (bool)$config['enabled'];
        foreach ( array( 'include', 'exclude', 'audience', 'audienceRules' ) as $key )
            $config[$key] = array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$config[$key] ) ), 'strlen' ) );
        $config['category'] = trim( (string)$config['category'] );
        $config['type'] = trim( (string)$config['type'] );
        $this->config = $config;
    }

    /**
     * The project files as exphelp.ini [ProjectFiles] configures them.
     *
     * @return expHelpProjectFiles
     */
    public static function fromIni()
    {
        return new self( self::iniConfig() );
    }

    /**
     * The configuration from exphelp.ini [ProjectFiles]; the defaults where it is missing.
     *
     * @return array
     */
    public static function iniConfig()
    {
        $config = array();
        if ( !class_exists( 'eZINI' ) || !eZINI::exists( 'exphelp.ini' ) )
            return $config;
        $ini = eZINI::instance( 'exphelp.ini' );
        if ( !$ini->hasGroup( 'ProjectFiles' ) )
            return $config;
        if ( $ini->hasVariable( 'ProjectFiles', 'Enabled' ) )
            $config['enabled'] = in_array( strtolower( trim( (string)$ini->variable( 'ProjectFiles', 'Enabled' ) ) ), array( 'enabled', 'true' ), true );
        foreach ( array( 'Include' => 'include', 'Exclude' => 'exclude', 'Audiences' => 'audience', 'AudienceRules' => 'audienceRules' ) as $name => $key )
            if ( $ini->hasVariable( 'ProjectFiles', $name ) )
                $config[$key] = expHelpAllowlist::listSetting( $ini->variable( 'ProjectFiles', $name ) );
        // AudienceRules[] hold commas themselves ("README.md;admin,developer"): not split
        if ( $ini->hasVariable( 'ProjectFiles', 'AudienceRules' ) )
            $config['audienceRules'] = array_values( array_filter( array_map( 'trim', (array)$ini->variable( 'ProjectFiles', 'AudienceRules' ) ), 'strlen' ) );
        foreach ( array( 'Category' => 'category', 'Type' => 'type' ) as $name => $key )
            if ( $ini->hasVariable( 'ProjectFiles', $name ) )
                $config[$key] = (string)$ini->variable( 'ProjectFiles', $name );
        return $config;
    }

    /**
     * @return array the configuration in use
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @return bool whether the help shows project files at all
     */
    public function isEnabled()
    {
        return $this->config['enabled'];
    }

    /**
     * Whether a path in the repository may be a project file by its name: enabled, Markdown, matched by Include[] and
     * by no Exclude[], and none of the paths that are never project files (NEVER, hidden files and directories).
     * Whether git tracks it, and what .gitignore says, is the allowlist's question (expHelpAllowlist::entries()).
     *
     * @param string $path relative to the installation root
     * @return bool
     */
    public function isCandidate( $path )
    {
        if ( !$this->config['enabled'] )
            return false;
        $path = expHelpAllowlist::normalize( $path );
        if ( $path === null || self::isNever( $path ) )
            return false;
        $base = basename( $path );
        $dot = strrpos( $base, '.' );
        if ( $dot === false || $dot === 0 || !in_array( strtolower( substr( $base, $dot + 1 ) ), self::SUFFIXES, true ) )
            return false;
        $included = false;
        foreach ( $this->config['include'] as $pattern )
            if ( self::globMatch( $pattern, $path ) )
            {
                $included = true;
                break;
            }
        if ( !$included )
            return false;
        foreach ( $this->config['exclude'] as $pattern )
            if ( self::globMatch( $pattern, $path ) )
                return false;
        return true;
    }

    /**
     * Whether a path is one that is never a project file: NEVER, a hidden file or a file in a hidden directory
     * (.github/ included), and anything that is not a clean relative path. Not a setting.
     *
     * @param string $path
     * @return bool
     */
    public static function isNever( $path )
    {
        $path = expHelpAllowlist::normalize( $path );
        if ( $path === null )
            return true;
        foreach ( self::NEVER as $pattern )
            if ( fnmatch( $pattern, $path ) )
                return true;
        foreach ( explode( '/', $path ) as $segment )
            if ( $segment[0] === '.' )
                return true;
        return false;
    }

    /**
     * A shell pattern on a path: "*" and "?" stay inside one directory, "**" crosses directories ("**\/" also
     * matches no directory at all), "[...]" is a character class.
     *
     * @param string $pattern
     * @param string $path
     * @return bool
     */
    public static function globMatch( $pattern, $path )
    {
        static $regexes = array();
        $pattern = (string)$pattern;
        if ( !isset( $regexes[$pattern] ) )
            $regexes[$pattern] = self::globRegex( $pattern );
        return (bool)preg_match( $regexes[$pattern], (string)$path );
    }

    /**
     * The regular expression of a glob pattern (anchored at both ends).
     *
     * @param string $pattern
     * @return string
     */
    public static function globRegex( $pattern )
    {
        $pattern = (string)$pattern;
        $regex = '';
        $length = strlen( $pattern );
        for ( $i = 0; $i < $length; ++$i )
        {
            $c = $pattern[$i];
            if ( $c === '*' )
            {
                if ( $i + 1 < $length && $pattern[$i + 1] === '*' )
                {
                    $i++;
                    if ( $i + 1 < $length && $pattern[$i + 1] === '/' )
                    {
                        // "**/": any number of directories, none included
                        $i++;
                        $regex .= '(?:.*/)?';
                    }
                    else
                        $regex .= '.*';
                }
                else
                    $regex .= '[^/]*';
            }
            elseif ( $c === '?' )
                $regex .= '[^/]';
            elseif ( $c === '[' && ( $end = strpos( $pattern, ']', $i + 2 ) ) !== false )
            {
                $class = substr( $pattern, $i + 1, $end - $i - 1 );
                $negate = $class !== '' && ( $class[0] === '!' || $class[0] === '^' );
                if ( $negate )
                    $class = substr( $class, 1 );
                $regex .= '[' . ( $negate ? '^' : '' ) . str_replace( array( '\\', ']' ), array( '\\\\', '\\]' ), $class ) . ']';
                $i = $end;
            }
            elseif ( $c === '\\' && $i + 1 < $length )
                $regex .= preg_quote( $pattern[++$i], '#' );
            else
                $regex .= preg_quote( $c, '#' );
        }
        return '#^' . $regex . '$#';
    }

    /**
     * Whether a path in a work tree is ignored by git's ignore rules: .git/info/exclude, then the .gitignore of the
     * top directory and of each directory down to the file (the last matching rule wins, "!" re-includes, a rule
     * that ends in "/" matches directories only, and nothing below an ignored directory can be re-included). A
     * .gitignore that cannot be read counts as ignoring everything below it (refused rather than shown).
     *
     * @param string $top the work tree's top directory
     * @param string $path relative to it
     * @param string|null $gitdir the git directory (for info/exclude), or null
     * @return bool
     */
    public function isIgnored( $top, $path, $gitdir = null )
    {
        $path = expHelpAllowlist::normalize( $path );
        if ( $path === null )
            return true;
        $top = rtrim( (string)$top, '/' );
        $segments = explode( '/', $path );
        $sources = array();
        if ( $gitdir !== null )
        {
            $exclude = rtrim( (string)$gitdir, '/' ) . '/info/exclude';
            if ( !is_file( $exclude ) && is_file( $gitdir . '/commondir' ) )
                $exclude = rtrim( (string)$gitdir, '/' ) . '/' . trim( (string)@file_get_contents( $gitdir . '/commondir' ) ) . '/info/exclude';
            $sources[] = array( 'file' => $exclude, 'base' => '' );
        }
        $dir = '';
        // the .gitignore of each directory from the top down to the file's own
        for ( $n = 0; $n < count( $segments ); ++$n )
        {
            $sources[] = array( 'file' => $top . ( $dir === '' ? '' : '/' . $dir ) . '/.gitignore', 'base' => $dir );
            $dir = $dir === '' ? $segments[$n] : $dir . '/' . $segments[$n];
        }
        // each directory on the way, then the file: an ignored directory ignores everything below it
        $current = '';
        foreach ( $segments as $n => $segment )
        {
            $current = $current === '' ? $segment : $current . '/' . $segment;
            $isDir = $n < count( $segments ) - 1;
            $ignored = false;
            foreach ( $sources as $source )
            {
                // a .gitignore applies to the paths below its directory only
                if ( $source['base'] !== '' && strncmp( $current, $source['base'] . '/', strlen( $source['base'] ) + 1 ) !== 0 )
                    continue;
                $rules = $this->ignoreRules( $source['file'] );
                if ( $rules === null )
                    return true;
                $relative = $source['base'] === '' ? $current : substr( $current, strlen( $source['base'] ) + 1 );
                foreach ( $rules as $rule )
                {
                    if ( $rule['dir'] && !$isDir )
                        continue;
                    $subject = $rule['anchored'] ? $relative : basename( $relative );
                    if ( preg_match( $rule['regex'], $subject ) )
                        $ignored = !$rule['negate'];
                }
            }
            if ( $ignored )
                return true;
        }
        return false;
    }

    /**
     * The rules of an ignore file: list of array( 'regex', 'negate', 'dir', 'anchored' ); an empty list when there
     * is no such file, null when it exists but cannot be read (or is a symbolic link).
     *
     * @param string $file
     * @return array|null
     */
    protected function ignoreRules( $file )
    {
        if ( array_key_exists( $file, $this->ignoreRules ) )
            return $this->ignoreRules[$file];
        if ( is_link( $file ) )
            return $this->ignoreRules[$file] = null;
        if ( !file_exists( $file ) )
            return $this->ignoreRules[$file] = array();
        $text = is_file( $file ) && is_readable( $file ) ? @file_get_contents( $file ) : false;
        if ( !is_string( $text ) )
            return $this->ignoreRules[$file] = null;
        return $this->ignoreRules[$file] = self::parseIgnore( $text );
    }

    /**
     * Parses the text of a .gitignore.
     *
     * @param string $text
     * @return array list of array( 'regex', 'negate', 'dir', 'anchored' )
     */
    public static function parseIgnore( $text )
    {
        $rules = array();
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$text ) as $line )
        {
            // trailing spaces are dropped unless escaped
            $line = preg_replace( '/(?<!\\\\)\s+$/', '', $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            $negate = false;
            if ( $line[0] === '!' )
            {
                $negate = true;
                $line = substr( $line, 1 );
            }
            elseif ( $line[0] === '\\' && isset( $line[1] ) && ( $line[1] === '#' || $line[1] === '!' ) )
                $line = substr( $line, 1 );
            $dir = substr( $line, -1 ) === '/';
            $line = rtrim( $line, '/' );
            if ( $line === '' )
                continue;
            // a slash at the start or in the middle anchors the rule at the .gitignore's directory
            $anchored = strpos( $line, '/' ) !== false;
            $line = ltrim( $line, '/' );
            if ( strncmp( $line, '**/', 3 ) === 0 )
                $anchored = true;
            // "foo/**" matches everything inside foo; a rule that matches a directory ignores what is inside it (isIgnored())
            $regex = self::globRegex( $line );
            $rules[] = array( 'regex' => $regex, 'negate' => $negate, 'dir' => $dir, 'anchored' => $anchored );
        }
        return $rules;
    }

    /**
     * The id of a project file's path: project/<path>.
     *
     * @param string $path
     * @return string
     */
    public static function idOf( $path )
    {
        return self::ID_PREFIX . '/' . $path;
    }

    /**
     * The path of a project file's id, or null when the id is not one (it does not start with project/).
     *
     * @param string $id
     * @return string|null
     */
    public static function pathOfId( $id )
    {
        $id = expHelpAllowlist::normalize( $id );
        if ( $id === null || strncmp( $id, self::ID_PREFIX . '/', strlen( self::ID_PREFIX ) + 1 ) !== 0 )
            return null;
        return substr( $id, strlen( self::ID_PREFIX ) + 1 );
    }

    /**
     * A project file's title in the page tree, where it stands at the top next to the folders: its own title (the
     * first heading or the front matter's), with the directory in front when that title says nothing more than
     * "README" ("bin/php README"), so no two entries read the same.
     *
     * @param string $path the path in the repository
     * @param string $title the page's title
     * @return string
     */
    public static function treeTitle( $path, $title )
    {
        $title = trim( (string)$title );
        $dir = dirname( (string)$path );
        if ( $title === '' )
            $title = basename( (string)$path );
        if ( preg_match( '/^readme(\.(md|markdown))?$/i', $title ) && $dir !== '.' && $dir !== '' )
            return $dir . ' ' . $title;
        return $title;
    }

    /**
     * Whether an id names a project file by its form (project/...).
     *
     * @param string $id
     * @return bool
     */
    public static function isProjectId( $id )
    {
        return self::pathOfId( $id ) !== null;
    }

    /**
     * The classification a project file has unless its own front matter says otherwise: array( 'type', 'category',
     * 'audience' => list ) from Type, Category, AudienceRules[] (first matching "<pattern>;<audiences>") and
     * Audiences[].
     *
     * @param string $path
     * @return array
     */
    public function classification( $path )
    {
        $audience = $this->config['audience'];
        foreach ( $this->config['audienceRules'] as $rule )
        {
            $pos = strrpos( $rule, ';' );
            if ( $pos === false )
                continue;
            if ( self::globMatch( trim( substr( $rule, 0, $pos ) ), (string)$path ) )
            {
                $audience = array_values( array_filter( array_map( 'trim', explode( ',', substr( $rule, $pos + 1 ) ) ), 'strlen' ) );
                break;
            }
        }
        return array( 'type' => $this->config['type'], 'category' => $this->config['category'], 'audience' => $audience );
    }
}

?>
