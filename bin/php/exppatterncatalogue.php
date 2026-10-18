#!/usr/bin/env php
<?php
/**
 * Generates the catalogue of design patterns and extension points of Exponential (doc/standards/patterns/).
 *
 * It reads the PHP source of the kernel, lib/ and the extensions with PHP's own tokenizer (nothing is loaded or run,
 * no database, no settings), classifies every class, interface, trait, module view, fetch function, template operator,
 * policy function, workflow event, event hook and INI-driven plugin point into pattern categories, and writes one
 * Markdown page per pattern, one per source area, and an index with the statistics.
 *
 *   php bin/php/exppatterncatalogue.php                     report what would change, write nothing
 *   php bin/php/exppatterncatalogue.php --write             write doc/standards/patterns/
 *   php bin/php/exppatterncatalogue.php --check             exit 1 when the pages differ from the source (CI)
 *   php bin/php/exppatterncatalogue.php --extensions=<dir>  also scan the extensions below <dir> that are git
 *                                                           repositories of their own (default: extension)
 *   php bin/php/exppatterncatalogue.php --no-extension-repos  only the files tracked by the installation's repository
 *
 * Sources: the git-tracked PHP and INI files below kernel/, lib/ and extension/ of the installation, plus every
 * extension below the extensions directory that is a git repository of its own whose origin is a repository of one of
 * the owners in OWNERS (minus EXCLUDED). The output only depends on those files: no dates, no commit ids, sorted
 * everywhere, so a second run on the same source writes the same bytes.
 *
 * The classification is heuristic (names, inheritance, method signatures, the registration files of the kernel);
 * doc/standards/patterns/README.md explains each rule.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package bin
 */

if ( PHP_SAPI !== 'cli' )
{
    exit( 1 );
}

chdir( dirname( __DIR__, 2 ) );

/** Owners whose extension repositories are part of the catalogue */
const EXPPC_OWNERS = array( 'se7enxweb' );
/** Extensions never catalogued: private or not part of the distribution */
const EXPPC_EXCLUDED = array( 'expmcp', 'ezmcp', 'expdse', 'sevenx_alpha_settings' );
/** The output directory */
const EXPPC_OUT = 'doc/standards/patterns';
/** Longest description, in characters */
const EXPPC_DESCRIPTION_LENGTH = 150;

$options = array( 'write' => false, 'check' => false, 'extensions' => 'extension', 'repos' => true, 'quiet' => false );
foreach ( array_slice( $argv, 1 ) as $arg )
{
    if ( $arg === '--write' )
        $options['write'] = true;
    elseif ( $arg === '--check' )
        $options['check'] = true;
    elseif ( $arg === '--no-extension-repos' )
        $options['repos'] = false;
    elseif ( $arg === '--quiet' )
        $options['quiet'] = true;
    elseif ( strncmp( $arg, '--extensions=', 13 ) === 0 )
        $options['extensions'] = rtrim( substr( $arg, 13 ), '/' );
    elseif ( $arg === '--help' || $arg === '-h' )
    {
        echo "Usage: php bin/php/exppatterncatalogue.php [--write|--check] [--extensions=<dir>] [--no-extension-repos] [--quiet]\n";
        exit( 0 );
    }
    else
    {
        fwrite( STDERR, "Unknown option $arg (see --help)\n" );
        exit( 2 );
    }
}

$started = microtime( true );
$sources = exppcSources( $options );
$model = exppcScan( $sources );
exppcClassify( $model );
$pages = exppcRender( $model, $sources );
$changed = exppcCompare( $pages );

if ( !$options['quiet'] )
{
    printf( "exppatterncatalogue: %d PHP files, %d INI files, %d classes, %d instances in %d categories, %d pages (%.1f s)\n",
            $model['stats']['php_files'], $model['stats']['ini_files'], count( $model['classes'] ),
            $model['stats']['instances'], count( $model['stats']['per_pattern'] ), count( $pages ), microtime( true ) - $started );
    foreach ( $changed as $path => $state )
        echo "  $state $path\n";
}
if ( $options['write'] )
{
    exppcWrite( $pages );
    if ( !$options['quiet'] )
        echo $changed ? "exppatterncatalogue: written\n" : "exppatterncatalogue: up to date, nothing written\n";
    exit( 0 );
}
if ( $options['check'] )
    exit( $changed ? 1 : 0 );
if ( !$options['quiet'] )
    echo $changed ? "exppatterncatalogue: " . count( $changed ) . " page(s) differ (run with --write)\n" : "exppatterncatalogue: up to date\n";
exit( 0 );

/**
 * The files to read: array( 'php' => array( path => true ), 'ini' => array( path => true ), 'extensions' => array( name => source ) ).
 *
 * @param array $options
 * @return array
 */
function exppcSources( $options )
{
    $php = array();
    $ini = array();
    $extensions = array();
    foreach ( exppcGitFiles( '.', array( 'kernel', 'lib', 'extension', 'settings' ) ) as $path )
    {
        if ( preg_match( '#^(kernel|lib|extension)/.*\.php$#', $path ) && !preg_match( '#(^|/)(tests?|vendor)/#', $path ) && !preg_match( '#\.ini\.append\.php$#', $path ) )
            $php[$path] = true;
        if ( preg_match( '#^(settings/[^/]+\.ini|extension/[^/]+/settings/.+\.ini(\.append(\.php)?)?)$#', $path ) )
            $ini[$path] = true;
        if ( preg_match( '#^extension/([^/]+)/#', $path, $m ) )
            $extensions[$m[1]] = 'bundled';
    }
    if ( $options['repos'] && is_dir( $options['extensions'] ) )
    {
        $names = scandir( $options['extensions'] );
        sort( $names, SORT_STRING );
        foreach ( $names as $name )
        {
            $dir = $options['extensions'] . '/' . $name;
            if ( $name[0] === '.' || isset( $extensions[$name] ) || in_array( $name, EXPPC_EXCLUDED, true ) || !is_dir( $dir ) )
                continue;
            if ( !is_dir( $dir . '/.git' ) && !is_file( $dir . '/.git' ) )
            {
                // a Composer package of one of the owners, installed without its repository: its files on disk
                $composer = @json_decode( (string)@file_get_contents( $dir . '/composer.json' ), true );
                $package = is_array( $composer ) && isset( $composer['name'] ) ? (string)$composer['name'] : '';
                if ( !in_array( strtok( $package, '/' ), EXPPC_OWNERS, true ) )
                    continue;
                $extensions[$name] = 'package';
                foreach ( exppcDiskFiles( $dir ) as $file )
                {
                    $path = 'extension/' . $name . '/' . $file;
                    if ( preg_match( '#\.php$#', $file ) && !preg_match( '#(^|/)(tests?|vendor|var|node_modules)/#', $file ) && !preg_match( '#\.ini\.append\.php$#', $file ) )
                        $php[$path] = $dir . '/' . $file;
                    if ( preg_match( '#^settings/.+\.ini(\.append(\.php)?)?$#', $file ) )
                        $ini[$path] = $dir . '/' . $file;
                }
                continue;
            }
            $origin = trim( (string)shell_exec( 'git -C ' . escapeshellarg( $dir ) . ' config --get remote.origin.url 2>/dev/null' ) );
            $owned = false;
            foreach ( EXPPC_OWNERS as $owner )
                if ( preg_match( '#[:/]' . preg_quote( $owner, '#' ) . '/[^/]+?(\.git)?$#', $origin ) )
                    $owned = true;
            if ( !$owned )
                continue;
            $extensions[$name] = 'repository';
            foreach ( exppcGitFiles( $dir, array() ) as $file )
            {
                $path = 'extension/' . $name . '/' . $file;
                if ( preg_match( '#\.php$#', $file ) && !preg_match( '#(^|/)(tests?|vendor|var|node_modules)/#', $file ) && !preg_match( '#\.ini\.append\.php$#', $file ) )
                    $php[$path] = $dir . '/' . $file;
                if ( preg_match( '#^settings/.+\.ini(\.append(\.php)?)?$#', $file ) )
                    $ini[$path] = $dir . '/' . $file;
            }
        }
    }
    ksort( $php, SORT_STRING );
    ksort( $ini, SORT_STRING );
    ksort( $extensions, SORT_STRING );
    return array( 'php' => $php, 'ini' => $ini, 'extensions' => $extensions );
}

/**
 * The git-tracked files of a repository below some directories.
 *
 * @param string $dir
 * @param string[] $below
 * @return string[]
 */
function exppcGitFiles( $dir, $below )
{
    $command = 'git -C ' . escapeshellarg( $dir ) . ' ls-files -z';
    if ( $below )
        $command .= ' -- ' . implode( ' ', array_map( 'escapeshellarg', $below ) );
    $out = (string)shell_exec( $command . ' 2>/dev/null' );
    $files = array_filter( explode( "\0", $out ), 'strlen' );
    sort( $files, SORT_STRING );
    return $files;
}

/**
 * The files below a directory (relative, sorted), without hidden directories and symbolic links to directories.
 *
 * @param string $dir
 * @return string[]
 */
function exppcDiskFiles( $dir )
{
    $files = array();
    $todo = array( '' );
    while ( $todo )
    {
        $sub = array_shift( $todo );
        $names = @scandir( $dir . ( $sub === '' ? '' : '/' . $sub ) );
        if ( !is_array( $names ) )
            continue;
        foreach ( $names as $name )
        {
            if ( $name === '' || $name[0] === '.' )
                continue;
            $relative = $sub === '' ? $name : $sub . '/' . $name;
            $full = $dir . '/' . $relative;
            if ( is_link( $full ) && is_dir( $full ) )
                continue;
            if ( is_dir( $full ) )
                $todo[] = $relative;
            elseif ( is_file( $full ) )
                $files[] = $relative;
        }
    }
    sort( $files, SORT_STRING );
    return $files;
}

/**
 * The file a source path is read from.
 *
 * @param string $path
 * @param mixed $source true (the installation's own file) or the path of the file in an extension repository
 * @return string
 */
function exppcFile( $path, $source )
{
    return $source === true ? $path : $source;
}

/**
 * Reads every source file.
 *
 * @param array $sources
 * @return array the model: classes, views, policies, fetch, operators, functions, cronjobs, hooks, ini, stats
 */
function exppcScan( $sources )
{
    $model = array( 'classes' => array(), 'views' => array(), 'policies' => array(), 'fetch' => array(), 'operators' => array(),
                    'tplfunctions' => array(), 'cronjobs' => array(), 'hooks' => array(), 'ini' => array(),
                    'stats' => array( 'php_files' => count( $sources['php'] ), 'ini_files' => count( $sources['ini'] ) ) );
    foreach ( $sources['php'] as $path => $source )
    {
        $code = @file_get_contents( exppcFile( $path, $source ) );
        if ( !is_string( $code ) )
            continue;
        foreach ( exppcParseClasses( $code ) as $class )
        {
            $class['file'] = $path;
            $class['area'] = exppcArea( $path );
            $key = strtolower( $class['name'] );
            if ( !isset( $model['classes'][$key] ) )
                $model['classes'][$key] = $class;
        }
        $base = basename( $path );
        if ( $base === 'module.php' )
            exppcParseModule( $path, $code, $model );
        elseif ( $base === 'function_definition.php' )
            exppcParseFetchFunctions( $path, $code, $model );
        elseif ( $base === 'eztemplateautoload.php' )
            exppcParseTemplateAutoload( $path, $code, $model );
        if ( preg_match( '#/cronjobs/[^/]+\.php$#', $path ) && !preg_match( '#^kernel/private/classes/cronjobs/#', $path ) )
            $model['cronjobs'][] = array( 'name' => preg_replace( '/\.php$/', '', $base ), 'file' => $path, 'area' => exppcArea( $path ),
                                          'description' => exppcDescription( exppcFirstDocBlock( $code ) ) );
        if ( preg_match_all( '/ezpEvent::getInstance\(\s*\)\s*->\s*(notify|filter)\(\s*[\'"]([a-z0-9_\/.-]+)[\'"]/i', $code, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
            foreach ( $m as $hit )
                $model['hooks'][] = array( 'event' => $hit[2][0], 'kind' => strtolower( $hit[1][0] ), 'file' => $path, 'area' => exppcArea( $path ),
                                           'line' => substr_count( $code, "\n", 0, $hit[0][1] ) + 1 );
    }
    foreach ( $sources['ini'] as $path => $source )
    {
        $text = @file_get_contents( exppcFile( $path, $source ) );
        if ( is_string( $text ) )
            exppcParseIni( $path, $text, $model );
    }
    ksort( $model['classes'], SORT_STRING );
    return $model;
}

/**
 * The classes, interfaces, traits and enums of a PHP file.
 *
 * @param string $code
 * @return array list of array( name, kind, abstract, final, extends, implements, methods, doc, line, signals )
 */
function exppcParseClasses( $code )
{
    $tokens = @token_get_all( $code );
    $classes = array();
    $namespace = '';
    $doc = '';
    $modifiers = array();
    $count = count( $tokens );
    $depth = 0;
    $current = null;
    $currentDepth = -1;
    $pendingMethod = null;
    for ( $i = 0; $i < $count; ++$i )
    {
        $token = $tokens[$i];
        if ( !is_array( $token ) )
        {
            if ( $token === '{' )
                ++$depth;
            elseif ( $token === '}' )
            {
                --$depth;
                if ( $current !== null && $depth === $currentDepth )
                {
                    $classes[] = $current;
                    $current = null;
                }
            }
            elseif ( $token === ';' || $token === '=' )
                $modifiers = array();
            continue;
        }
        $id = $token[0];
        if ( $id === T_CURLY_OPEN || $id === T_DOLLAR_OPEN_CURLY_BRACES )
        {
            ++$depth;
            continue;
        }
        if ( $id === T_DOC_COMMENT )
        {
            $doc = $token[1];
            continue;
        }
        if ( $id === T_NAMESPACE && $current === null )
        {
            $namespace = exppcReadName( $tokens, $i + 1 );
            continue;
        }
        if ( in_array( $id, array( T_ABSTRACT, T_FINAL, T_STATIC, T_PUBLIC, T_PROTECTED, T_PRIVATE ), true ) )
        {
            $modifiers[$id] = true;
            continue;
        }
        $isEnum = defined( 'T_ENUM' ) && $id === constant( 'T_ENUM' );
        if ( ( $id === T_CLASS || $id === T_INTERFACE || $id === T_TRAIT || $isEnum ) && $current === null )
        {
            // "Foo::class" and "new class" are no declarations
            $prev = exppcPrevious( $tokens, $i );
            if ( $prev === T_DOUBLE_COLON || $prev === T_NEW )
                continue;
            $name = exppcReadName( $tokens, $i + 1 );
            if ( $name === '' )
                continue;
            $kind = $id === T_CLASS ? 'class' : ( $id === T_INTERFACE ? 'interface' : ( $id === T_TRAIT ? 'trait' : 'enum' ) );
            $extends = array();
            $implements = array();
            $mode = null;
            for ( $j = $i + 2; $j < $count && $tokens[$j] !== '{'; ++$j )
            {
                if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_EXTENDS )
                    $mode = 'extends';
                elseif ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_IMPLEMENTS )
                    $mode = 'implements';
                elseif ( $mode !== null && is_array( $tokens[$j] ) && exppcIsNameToken( $tokens[$j][0] ) )
                {
                    $parent = exppcReadName( $tokens, $j );
                    while ( $j + 1 < $count && is_array( $tokens[$j + 1] ) && exppcIsNameToken( $tokens[$j + 1][0] ) )
                        ++$j;
                    if ( $mode === 'extends' )
                        $extends[] = exppcShortName( $parent );
                    else
                        $implements[] = exppcShortName( $parent );
                }
            }
            $current = array( 'name' => ( $namespace !== '' ? $namespace . '\\' : '' ) . $name, 'short' => $name, 'kind' => $kind,
                              'abstract' => isset( $modifiers[T_ABSTRACT] ), 'final' => isset( $modifiers[T_FINAL] ),
                              'extends' => $extends, 'implements' => $implements, 'methods' => array(), 'staticProps' => array(),
                              'doc' => $doc, 'line' => $token[2], 'signals' => array() );
            $currentDepth = $depth;
            $doc = '';
            $modifiers = array();
            continue;
        }
        if ( $current !== null && $depth === $currentDepth + 1 )
        {
            if ( $id === T_FUNCTION )
            {
                $name = exppcReadName( $tokens, $i + 1 );
                if ( $name !== '' )
                    $current['methods'][strtolower( $name )] = array( 'name' => $name, 'static' => isset( $modifiers[T_STATIC] ),
                                                                     'abstract' => isset( $modifiers[T_ABSTRACT] ) || $current['kind'] === 'interface',
                                                                     'private' => isset( $modifiers[T_PRIVATE] ) );
                $modifiers = array();
                $doc = '';
                continue;
            }
            if ( $id === T_VARIABLE && isset( $modifiers[T_STATIC] ) )
                $current['staticProps'][] = strtolower( substr( $token[1], 1 ) );
            if ( $id === T_VARIABLE || $id === T_CONST )
                $modifiers = array();
        }
        if ( $current !== null && $depth > $currentDepth )
        {
            if ( $id === T_NEW )
            {
                $next = exppcNext( $tokens, $i );
                if ( $next !== null && is_array( $tokens[$next] ) && in_array( strtolower( $tokens[$next][1] ), array( 'self', 'static' ), true ) )
                    $current['signals']['new_self'] = true;
            }
            if ( $id === T_VARIABLE && $token[1] === '$GLOBALS' )
                $current['signals']['globals'] = true;
        }
    }
    return $classes;
}

/**
 * Whether a token is part of a (qualified) name.
 *
 * @param int $id
 * @return bool
 */
function exppcIsNameToken( $id )
{
    if ( $id === T_STRING || $id === T_NS_SEPARATOR )
        return true;
    foreach ( array( 'T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED', 'T_NAME_RELATIVE' ) as $name )
        if ( defined( $name ) && $id === constant( $name ) )
            return true;
    return false;
}

/**
 * The name that starts at the first non-whitespace token from $i.
 *
 * @param array $tokens
 * @param int $i
 * @return string
 */
function exppcReadName( $tokens, $i )
{
    $count = count( $tokens );
    while ( $i < $count && is_array( $tokens[$i] ) && $tokens[$i][0] === T_WHITESPACE )
        ++$i;
    $name = '';
    while ( $i < $count && is_array( $tokens[$i] ) && exppcIsNameToken( $tokens[$i][0] ) )
    {
        $name .= $tokens[$i][1];
        ++$i;
    }
    if ( $name === '' && $i < $count && is_array( $tokens[$i] ) && $tokens[$i][0] === T_STRING )
        $name = $tokens[$i][1];
    return ltrim( $name, '\\' );
}

/**
 * The id of the previous significant token.
 *
 * @param array $tokens
 * @param int $i
 * @return mixed
 */
function exppcPrevious( $tokens, $i )
{
    for ( $j = $i - 1; $j >= 0; --$j )
    {
        if ( is_array( $tokens[$j] ) && in_array( $tokens[$j][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) )
            continue;
        return is_array( $tokens[$j] ) ? $tokens[$j][0] : $tokens[$j];
    }
    return null;
}

/**
 * The index of the next significant token.
 *
 * @param array $tokens
 * @param int $i
 * @return int|null
 */
function exppcNext( $tokens, $i )
{
    $count = count( $tokens );
    for ( $j = $i + 1; $j < $count; ++$j )
        if ( !is_array( $tokens[$j] ) || !in_array( $tokens[$j][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) )
            return $j;
    return null;
}

/**
 * "Foo\Bar\Baz" -> "Baz".
 *
 * @param string $name
 * @return string
 */
function exppcShortName( $name )
{
    $pos = strrpos( $name, '\\' );
    return $pos === false ? $name : substr( $name, $pos + 1 );
}

/**
 * The first doc comment of a file.
 *
 * @param string $code
 * @return string
 */
function exppcFirstDocBlock( $code )
{
    return preg_match( '#/\*\*.*?\*/#s', $code, $m ) ? $m[0] : '';
}

/**
 * One line of description from a doc comment: its first sentence, without tags, boilerplate or the old product name.
 *
 * @param string $doc
 * @return string
 */
function exppcDescription( $doc )
{
    if ( $doc === '' )
        return '';
    $lines = array();
    foreach ( preg_split( '/\R/', $doc ) as $line )
    {
        $line = trim( preg_replace( '#^\s*(/\*\*|\*/|\*)#', '', $line ) );
        $line = trim( preg_replace( '#\*/$#', '', $line ) );
        if ( $line === '' )
        {
            if ( $lines )
                break;
            continue;
        }
        if ( $line[0] === '@' )
            break;
        if ( preg_match( '/^(File containing|Definition of|Created on|\$Id|SOFTWARE|COPYRIGHT|NOTICE|## |Copyright)/i', $line ) || stripos( $line, 'classname' ) !== false )
        {
            if ( $lines )
                break;
            continue;
        }
        $lines[] = $line;
        if ( count( $lines ) >= 4 )
            break;
    }
    $text = implode( ' ', $lines );
    if ( preg_match( '/^(.+?[.!?])(\s|$)/u', $text, $m ) )
        $text = $m[1];
    $text = preg_replace( '/\b[eE][zZ] ?[pP]ublish(?! ?[_\\\\\/]|\.ini)( Platform)?( Legacy)?\b/', 'Exponential', $text );
    $text = preg_replace( '/\s+/u', ' ', strip_tags( (string)$text ) );
    if ( function_exists( 'mb_strlen' ) && mb_strlen( $text, 'UTF-8' ) > EXPPC_DESCRIPTION_LENGTH )
        $text = rtrim( mb_substr( $text, 0, EXPPC_DESCRIPTION_LENGTH - 1, 'UTF-8' ) ) . '…';
    return trim( $text );
}

/**
 * The area of a path: kernel/classes/datatypes, kernel/private, kernel modules, lib/<name>, extension/<name>.
 *
 * @param string $path
 * @return string
 */
function exppcArea( $path )
{
    $parts = explode( '/', $path );
    if ( $parts[0] === 'extension' )
        return 'extension/' . $parts[1];
    if ( $parts[0] === 'lib' )
        return count( $parts ) > 2 ? 'lib/' . $parts[1] : 'lib';
    if ( $parts[0] === 'kernel' )
    {
        if ( $parts[1] === 'classes' )
            return count( $parts ) > 3 ? 'kernel/classes/' . $parts[2] : 'kernel/classes';
        if ( $parts[1] === 'private' )
            return count( $parts ) > 4 && $parts[2] === 'classes' ? 'kernel/private/classes/' . $parts[3] : 'kernel/private';
        if ( $parts[1] === 'common' )
            return 'kernel/common';
        return 'kernel/modules';
    }
    return $parts[0];
}

/**
 * Module views and policy functions of a module.php.
 *
 * @param string $path
 * @param string $code
 * @param array $model
 */
function exppcParseModule( $path, $code, &$model )
{
    $module = basename( dirname( $path ) );
    if ( preg_match( "/\\\$Module\s*=\s*array\(\s*['\"]name['\"]\s*=>\s*['\"]([^'\"]+)/", $code, $m ) )
        $title = $m[1];
    else
        $title = $module;
    foreach ( exppcStatements( $code, '$ViewList[' ) as $statement )
    {
        if ( !preg_match( "/^\\\$ViewList\[\s*['\"]([^'\"]+)['\"]\s*\]/", $statement['text'], $v ) )
            continue;
        $functions = preg_match( "/['\"]functions['\"]\s*=>\s*array\(([^)]*)\)/", $statement['text'], $f ) ? exppcStrings( $f[1] ) : array();
        $script = preg_match( "/['\"]script['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $statement['text'], $s ) ? $s[1] : '';
        $model['views'][] = array( 'module' => $module, 'view' => $v[1], 'script' => $script, 'functions' => $functions,
                                   'file' => $path, 'line' => $statement['line'], 'area' => exppcArea( $path ), 'title' => $title );
    }
    foreach ( exppcStatements( $code, '$FunctionList[' ) as $statement )
    {
        if ( !preg_match( "/^\\\$FunctionList\[\s*['\"]([^'\"]+)['\"]\s*\]\s*=\s*array\((.*)\)\s*;$/s", $statement['text'], $p ) )
            continue;
        $limitations = array();
        if ( preg_match_all( "/['\"]([A-Za-z_]+)['\"]\s*=>\s*\\\$[A-Za-z_]+/", $p[2], $l ) )
            $limitations = $l[1];
        $model['policies'][] = array( 'module' => $module, 'function' => $p[1], 'limitations' => $limitations,
                                      'file' => $path, 'line' => $statement['line'], 'area' => exppcArea( $path ) );
    }
}

/**
 * Fetch functions of a function_definition.php.
 *
 * @param string $path
 * @param string $code
 * @param array $model
 */
function exppcParseFetchFunctions( $path, $code, &$model )
{
    $module = basename( dirname( $path ) );
    foreach ( exppcStatements( $code, '$FunctionList[' ) as $statement )
    {
        if ( !preg_match( "/^\\\$FunctionList\[\s*['\"]([^'\"]+)['\"]\s*\]/", $statement['text'], $f ) )
            continue;
        $class = preg_match( "/['\"]class['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $statement['text'], $c ) ? $c[1] : '';
        $method = preg_match( "/['\"]method['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $statement['text'], $mm ) ? $mm[1] : '';
        $model['fetch'][] = array( 'module' => $module, 'function' => $f[1], 'class' => $class, 'method' => $method,
                                   'file' => $path, 'line' => $statement['line'], 'area' => exppcArea( $path ) );
    }
}

/**
 * Template operators and functions of an eztemplateautoload.php.
 *
 * @param string $path
 * @param string $code
 * @param array $model
 */
function exppcParseTemplateAutoload( $path, $code, &$model )
{
    foreach ( array( '$eZTemplateOperatorArray[' => 'operators', '$eZTemplateFunctionArray[' => 'tplfunctions' ) as $start => $key )
        foreach ( exppcStatements( $code, $start ) as $statement )
        {
            $class = preg_match( "/['\"]class['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $statement['text'], $c ) ? $c[1] : '';
            $names = preg_match( "/['\"](operator|function)_names['\"]\s*=>\s*array\((.*?)\)/s", $statement['text'], $n ) ? exppcStrings( $n[2] ) : array();
            if ( $class === '' )
                continue;
            $model[$key][] = array( 'class' => $class, 'names' => $names, 'file' => $path, 'line' => $statement['line'], 'area' => exppcArea( $path ) );
        }
}

/**
 * The statements of a file that start with a text (up to the ";" that ends them at bracket depth 0).
 *
 * @param string $code
 * @param string $start
 * @return array list of array( 'text', 'line' )
 */
function exppcStatements( $code, $start )
{
    $statements = array();
    $offset = 0;
    while ( ( $pos = strpos( $code, $start, $offset ) ) !== false )
    {
        $lineStart = strrpos( substr( $code, 0, $pos ), "\n" );
        $before = trim( substr( $code, $lineStart === false ? 0 : $lineStart + 1, $pos - ( $lineStart === false ? 0 : $lineStart + 1 ) ) );
        $depth = 0;
        $quote = null;
        $end = strlen( $code );
        for ( $i = $pos, $n = strlen( $code ); $i < $n; ++$i )
        {
            $ch = $code[$i];
            if ( $quote !== null )
            {
                if ( $ch === '\\' )
                    ++$i;
                elseif ( $ch === $quote )
                    $quote = null;
                continue;
            }
            if ( $ch === '"' || $ch === "'" )
                $quote = $ch;
            elseif ( $ch === '(' || $ch === '[' )
                ++$depth;
            elseif ( $ch === ')' || $ch === ']' )
                --$depth;
            elseif ( $ch === ';' && $depth <= 0 )
            {
                $end = $i + 1;
                break;
            }
        }
        if ( $before === '' )
            $statements[] = array( 'text' => substr( $code, $pos, $end - $pos ), 'line' => substr_count( $code, "\n", 0, $pos ) + 1 );
        $offset = max( $end, $pos + 1 );
    }
    return $statements;
}

/**
 * The quoted strings of a PHP fragment.
 *
 * @param string $text
 * @return string[]
 */
function exppcStrings( $text )
{
    preg_match_all( "/['\"]([^'\"]*)['\"]/", $text, $m );
    return array_values( array_filter( $m[1], 'strlen' ) );
}

/**
 * INI settings whose value names a class, and the event listeners of event.ini.
 *
 * @param string $path
 * @param string $text
 * @param array $model
 */
function exppcParseIni( $path, $text, &$model )
{
    $section = '';
    $number = 0;
    foreach ( preg_split( '/\R/', $text ) as $line )
    {
        ++$number;
        $line = trim( $line );
        if ( $line === '' || $line[0] === '#' || $line[0] === ';' || strncmp( $line, '<?php', 5 ) === 0 || strncmp( $line, '*/', 2 ) === 0 || strncmp( $line, '/*', 2 ) === 0 )
            continue;
        if ( preg_match( '/^\[([^\]]+)\]$/', $line, $m ) )
        {
            $section = $m[1];
            continue;
        }
        if ( !preg_match( '/^([A-Za-z0-9_]+)(\[[^\]]*\])?=(.*)$/', $line, $m ) )
            continue;
        $value = trim( $m[3] );
        if ( $value === '' )
            continue;
        $model['ini'][] = array( 'file' => preg_replace( '/\.append(\.php)?$/', '', $path ), 'source' => $path, 'section' => $section,
                                 'key' => $m[1] . ( isset( $m[2] ) ? $m[2] : '' ), 'name' => $m[1], 'value' => $value, 'line' => $number,
                                 'area' => exppcArea( $path ) );
    }
}

/**
 * The pattern categories: slug => array( name, group, summary, rule, extension point ).
 *
 * @return array
 */
function exppcCategories()
{
    return array(
        'active-record' => array( 'Active Record (eZPersistentObject)', 'Data access',
            'A class whose objects are rows of one database table: it describes the table in definition() and loads, stores and removes itself through eZPersistentObject.',
            'Extends eZPersistentObject, directly or through a parent class.',
            'A new table: a class that extends eZPersistentObject with a static definition(); schema in the extension\'s share/db_schema.dba or sql/ENGINE/schema.sql.' ),
        'datatype' => array( 'Datatype (Strategy for content fields)', 'Plugins',
            'Each datatype is a strategy for one kind of content field: how it is edited, validated, stored, indexed, compared and shown.',
            'Extends eZDataType, directly or through a parent class.',
            'content.ini [DataTypeSettings] ExtensionDirectories[] and AvailableDataTypes[]; the class registers itself with eZDataType::register().' ),
        'workflow-event' => array( 'Workflow event type (Command, plugin)', 'Plugins',
            'A step of a workflow that runs when a trigger fires (before or after publishing, reading, adding to the basket ...).',
            'Extends eZWorkflowEventType.',
            'workflow.ini [EventSettings] ExtensionDirectories[] and AvailableEventTypes[]; triggers connect workflows to module operations.' ),
        'template-operator' => array( 'Template operator (Interpreter plugin)', 'Plugins',
            'A class that adds operators to the template language: it lists them in operatorList() and computes them in modify().',
            'Registered in an eztemplateautoload.php ($eZTemplateOperatorArray[]), or has operatorList() and modify().',
            'autoloads/eztemplateautoload.php of an extension, enabled by site.ini/template.ini [TemplateSettings] ExtensionAutoloadPath[].' ),
        'template-function' => array( 'Template function (Interpreter plugin)', 'Plugins',
            'A class that adds a block or function to the template language ({def}, {foreach}, {cache-block} ...).',
            'Registered in an eztemplateautoload.php ($eZTemplateFunctionArray[]), or has functionList() and process().',
            'autoloads/eztemplateautoload.php of an extension.' ),
        'fetch-function' => array( 'Fetch function (Query Object, Facade)', 'Plugins',
            'A named query that templates call with fetch( module, function, hash(...) ); the kernel maps it to a method of a function collection class.',
            'An entry of a module\'s function_definition.php, or a class named *FunctionCollection.',
            'function_definition.php in the module directory of an extension (module.ini ModuleRepositories[]).' ),
        'module-view' => array( 'Module view (Page Controller)', 'Request handling',
            'Each view is a page controller: the front controller dispatches /MODULE/VIEW to the view\'s script, which reads the request and renders a template.',
            'An entry of $ViewList in a module.php, or a class that extends ModuleView or Page (the runnable form of a view).',
            'module.ini [ModuleSettings] ExtensionRepositories[] and ModuleList[]; views declare the policy functions they need.' ),
        'policy-function' => array( 'Policy function (access control)', 'Request handling',
            'A permission a role can grant, optionally limited (by class, section, subtree, siteaccess ...). Views name the functions they need.',
            'An entry of $FunctionList in a module.php.',
            'module.php $FunctionList; limitations are functions that list the allowed values.' ),
        'front-controller' => array( 'Front Controller', 'Request handling',
            'One entry point receives every request, matches the siteaccess and dispatches it to a module view.',
            'The kernel classes ezpKernel, ezpKernelWeb, ezpKernelTreeMenu, ezpKernelRest, eZModule and their kind.',
            'index.php creates ezpKernel with ezpKernelWeb; Velocity and FrankenPHP keep the kernel in memory.' ),
        'command' => array( 'Command (console, scripts, cronjobs)', 'Request handling',
            'A unit of work that runs on its own: a console command (./console exp:...), a CLI script class or a cronjob part.',
            'Extends Command, CronjobPart or Runnable (the runnable console commands and cronjob parts), or a cronjobs/*.php file.',
            'kernel/private/classes/commands/, cronjob.ini [CronjobSettings] ExtensionDirectories[] and CronjobPart-*.' ),
        'singleton' => array( 'Singleton / instance()', 'Creation',
            'A class that hands out one shared instance per key, mostly through a static instance() method (often cached in $GLOBALS).',
            'Has a static instance() or getInstance() method.',
            'Call Class::instance(); many of them take the settings file or database as the key.' ),
        'factory' => array( 'Factory and static factory methods', 'Creation',
            'A class or static method that creates objects for the caller, so the caller does not name the concrete class.',
            'Class name ends in Factory, or has static create*(), from*(), factory(), make*() methods, or creates new self/new static in a static method.',
            'Often the INI setting that names the class to create (see the INI plugin points).' ),
        'builder' => array( 'Builder', 'Creation',
            'Builds a complex object or text step by step.',
            'Class name contains Builder or Generator.',
            '' ),
        'registry' => array( 'Registry', 'Structure',
            'A central place where objects or callbacks are registered by name and looked up later.',
            'Class name contains Registry, or has static register*() methods.',
            '' ),
        'strategy-handler' => array( 'Strategy / handler', 'Behavior',
            'Interchangeable implementations of one job, chosen by a setting: cluster file handlers, image handlers, login handlers, search engines, mail transports, cache backends ...',
            'Class name ends in Handler, Backend, Driver, Transport, Engine or Strategy, or implements an interface whose name does.',
            'The INI setting that names the handler (see the INI plugin points page).' ),
        'template-method' => array( 'Template Method / abstract base class', 'Behavior',
            'An abstract class that fixes the steps of an algorithm and leaves some of them to its subclasses.',
            'An abstract class with both abstract and concrete methods.',
            'Extend the base class and implement its abstract methods.' ),
        'interface' => array( 'Interface (contract)', 'Structure',
            'A contract that implementations follow; code depends on the interface, not on a class.',
            'Every interface.',
            'Implement the interface and register the class where the interface is used.' ),
        'observer' => array( 'Observer / listener / event', 'Behavior',
            'Code that reacts to something that happened elsewhere without the source knowing it: ezpEvent listeners, notification handlers, cache listeners.',
            'Class name contains Listener, Observer or Subscriber, or has attach()/notify()/addListener() methods; every ezpEvent notify()/filter() call (see the hooks page).',
            'event.ini [Event] Listeners[]=EVENT@CALLBACK; notification.ini [NotificationEventHandlerSettings] AvailableNotificationEventTypes[].' ),
        'event-hook' => array( 'Event hook (ezpEvent)', 'Behavior',
            'A named point where the kernel calls ezpEvent::getInstance()->notify() or filter(), so that listeners can react or change a value.',
            'A call of ezpEvent::getInstance()->notify( \'EVENT\' ) or ->filter( \'EVENT\' ).',
            'event.ini [Event] Listeners[]=EVENT@CALLBACK.' ),
        'adapter' => array( 'Adapter / bridge / wrapper', 'Structure',
            'Makes one interface usable through another: wrappers around libraries, bridges between subsystems.',
            'Class name contains Adapter, Bridge, Wrapper or Proxy.',
            '' ),
        'facade' => array( 'Facade / service', 'Structure',
            'One simple entry point in front of a subsystem, or a service class that holds the operations of one domain.',
            'Class name ends in Facade, Service, Manager, API or Api.',
            '' ),
        'decorator' => array( 'Decorator / filter', 'Structure',
            'Wraps an object or a value to add behavior while keeping its interface: output filters, input filters, pre-/post-processors.',
            'Class name contains Decorator or Filter.',
            '' ),
        'chain' => array( 'Chain of Responsibility', 'Behavior',
            'A request is passed along a list of handlers until one of them takes it: login handlers, URL matchers, router routes.',
            'Class name contains LoginHandler, Chain, Pipeline, Middleware, Router or Matcher.',
            'site.ini [UserSettings] LoginHandler[] (tried in order), siteaccess MatchOrder.' ),
        'iterator' => array( 'Iterator', 'Behavior',
            'Walks over a collection without exposing how it is stored.',
            'Implements Iterator, IteratorAggregate or ArrayAccess.',
            '' ),
        'value-object' => array( 'Value object / struct', 'Structure',
            'A small object that carries data and has no identity of its own.',
            'Class name ends in Struct, Value, Result, Item, Info or Definition and the class has few methods, or extends ezcBaseStruct.',
            '' ),
        'exception' => array( 'Exception hierarchy', 'Structure',
            'Errors as classes, so callers catch what they can handle.',
            'Extends Exception, an *Exception class or Error.',
            '' ),
        'trait' => array( 'Trait (mixin)', 'Structure',
            'Shared methods mixed into several classes.',
            'Every trait.',
            '' ),
        'utility' => array( 'Utility (static helper)', 'Structure',
            'A class of static functions grouped by subject; no instances.',
            'A concrete class whose methods are all static (at least two).',
            '' ),
        'ini-plugin-point' => array( 'INI plugin point (extension point)', 'Plugins',
            'A setting that names the class the kernel uses for a job, so an extension or a site can replace or add one without changing code.',
            'A setting of a settings/*.ini file (or an extension\'s) whose value is the name of a class of the catalogue.',
            'Set the setting in an extension\'s settings/FILE.ini.append.php or in settings/override/.' ),
    );
}

/**
 * Classifies every class and counts the instances.
 *
 * @param array $model
 */
function exppcClassify( &$model )
{
    $classes = &$model['classes'];
    $known = array();
    foreach ( $classes as $key => $class )
        $known[strtolower( $class['short'] )] = $key;
    // the INI settings that name a class: the exact (fully qualified) class name, so that a value such as "user" or
    // "Text" is not taken for a class of that short name
    $exact = array();
    foreach ( $classes as $key => $class )
        $exact[$class['name']] = $key;
    $iniByClass = array();
    $iniPoints = array();
    foreach ( $model['ini'] as $setting )
    {
        $value = ltrim( $setting['value'], '\\' );
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_\\\\]*$/', $value ) || !isset( $exact[$value] ) )
            continue;
        $key = $exact[$value];
        $where = $setting['file'] . ' [' . $setting['section'] . '] ' . $setting['key'];
        $iniByClass[$key][$where] = true;
        $iniPoints[] = $setting + array( 'class' => $classes[$key]['name'], 'class_key' => $key );
    }
    $model['ini_points'] = $iniPoints;
    $registered = array();
    foreach ( array( 'operators' => 'template-operator', 'tplfunctions' => 'template-function' ) as $list => $pattern )
        foreach ( $model[$list] as $entry )
            if ( isset( $known[strtolower( $entry['class'] )] ) )
                $registered[$known[strtolower( $entry['class'] )]][$pattern] = true;

    foreach ( $classes as $key => &$class )
    {
        $ancestors = exppcAncestors( $classes, $known, $key );
        $interfaces = exppcInterfaces( $classes, $known, $key );
        $anc = array_map( 'strtolower', $ancestors );
        $ifs = array_map( 'strtolower', $interfaces );
        $short = $class['short'];
        $methods = $class['methods'];
        $patterns = array();
        $isClass = $class['kind'] === 'class';
        if ( $class['kind'] === 'interface' )
            $patterns['interface'] = true;
        if ( $class['kind'] === 'trait' )
            $patterns['trait'] = true;
        if ( $isClass && in_array( 'ezpersistentobject', $anc, true ) )
            $patterns['active-record'] = true;
        if ( $isClass && in_array( 'ezdatatype', $anc, true ) )
            $patterns['datatype'] = true;
        if ( $isClass && in_array( 'ezworkfloweventtype', $anc, true ) )
            $patterns['workflow-event'] = true;
        if ( isset( $registered[$key] ) )
            $patterns += $registered[$key];
        if ( $isClass && isset( $methods['operatorlist'] ) && isset( $methods['modify'] ) )
            $patterns['template-operator'] = true;
        if ( $isClass && isset( $methods['functionlist'] ) && isset( $methods['process'] ) )
            $patterns['template-function'] = true;
        if ( preg_match( '/FunctionCollection$/i', $short ) )
            $patterns['fetch-function'] = true;
        if ( preg_match( '/^(ezpKernel(Web|TreeMenu|Rest)?|eZModule|ezpRestMvcController|ezpMvcRegexpRoute|eZURI|eZSiteAccess|ezpKernelHandler)$/i', $short ) )
            $patterns['front-controller'] = true;
        if ( $isClass && array_intersect( $anc, array( 'moduleview', 'page' ) ) )
            $patterns['module-view'] = true;
        elseif ( $isClass && ( array_intersect( $anc, array( 'command', 'cronjobpart', 'runnable', 'ezscript' ) ) || preg_match( '/Command$/', $short ) ) )
            $patterns['command'] = true;
        foreach ( array( 'instance', 'getinstance' ) as $m )
            if ( isset( $methods[$m] ) && $methods[$m]['static'] )
                $patterns['singleton'] = true;
        $factory = preg_match( '/Factory$/i', $short );
        foreach ( $methods as $name => $method )
            if ( $method['static'] && !$method['private'] && preg_match( '/^(create|from[A-Z_]?|factory|make|newinstance|build)/i', $method['name'] ) && $name !== 'fromstring' )
                $factory = true;
        if ( $factory || !empty( $class['signals']['new_self'] ) && !isset( $patterns['singleton'] ) )
            $patterns['factory'] = true;
        if ( preg_match( '/(Builder|Generator)$/i', $short ) )
            $patterns['builder'] = true;
        $register = false;
        foreach ( $methods as $method )
            if ( $method['static'] && preg_match( '/^register/i', $method['name'] ) )
                $register = true;
        if ( preg_match( '/Registry/i', $short ) || $register )
            $patterns['registry'] = true;
        $handlerName = '/(Handler|Backend|Driver|Transport|Engine|Strategy|Converter|Provider|Resolver|Plugin)$/i';
        $handlerIf = false;
        foreach ( $interfaces as $if )
            if ( preg_match( '/(Handler|Backend|Driver|Transport|Engine|Strategy|Provider|Resolver|Plugin)(Interface)?$/i', $if ) )
                $handlerIf = true;
        if ( $class['kind'] !== 'trait' && ( preg_match( $handlerName, $short ) || $handlerIf ) )
            $patterns['strategy-handler'] = true;
        if ( $isClass && $class['abstract'] )
        {
            $abstract = 0;
            $concrete = 0;
            foreach ( $methods as $method )
                $method['abstract'] ? ++$abstract : ++$concrete;
            if ( $abstract > 0 && $concrete > 0 || $abstract === 0 && $concrete > 0 )
                $patterns['template-method'] = true;
        }
        if ( preg_match( '/(Listener|Observer|Subscriber)/i', $short ) || isset( $methods['attach'] ) && isset( $methods['notify'] ) || isset( $methods['addlistener'] ) )
            $patterns['observer'] = true;
        if ( preg_match( '/(Adapter|Bridge|Wrapper|Proxy)/i', $short ) )
            $patterns['adapter'] = true;
        if ( preg_match( '/(Facade|Service|Manager|API)$/i', $short ) && !preg_match( '/^.*Interface$/', $short ) )
            $patterns['facade'] = true;
        if ( preg_match( '/(Decorator|Filter)/i', $short ) )
            $patterns['decorator'] = true;
        if ( preg_match( '/(LoginHandler|Chain|Pipeline|Middleware|Router|Matcher)/i', $short ) || in_array( 'ezuserloginhandler', $anc, true ) )
            $patterns['chain'] = true;
        if ( array_intersect( $ifs, array( 'iterator', 'iteratoraggregate', 'arrayaccess', 'traversable' ) ) )
            $patterns['iterator'] = true;
        if ( $isClass && ( in_array( 'ezcbasestruct', $anc, true ) || preg_match( '/(Struct|Value|Result|Item|Info|Definition)$/', $short ) && count( $methods ) <= 6 && !isset( $patterns['active-record'] ) ) )
            $patterns['value-object'] = true;
        if ( $isClass && ( preg_match( '/Exception$/', $short ) || array_filter( $ancestors, function ( $a ) { return preg_match( '/^(Exception|Error|\w*Exception)$/', $a ); } ) ) )
            $patterns['exception'] = true;
        if ( $isClass && !$class['abstract'] && count( $methods ) >= 2 && !array_filter( $methods, function ( $m ) { return !$m['static']; } ) )
            $patterns['utility'] = true;
        if ( isset( $iniByClass[$key] ) )
            $patterns['ini-plugin-point'] = true;
        ksort( $patterns, SORT_STRING );
        $class['patterns'] = array_keys( $patterns );
        $class['ancestors'] = $ancestors;
        $class['interfaces_all'] = $interfaces;
        $class['ini'] = isset( $iniByClass[$key] ) ? array_keys( $iniByClass[$key] ) : array();
        sort( $class['ini'], SORT_STRING );
        $class['description'] = exppcDescription( $class['doc'] );
        unset( $class['doc'] );
    }
    unset( $class );

    // statistics
    $per = array();
    foreach ( array_keys( exppcCategories() ) as $slug )
        $per[$slug] = 0;
    foreach ( $classes as $class )
        foreach ( $class['patterns'] as $slug )
            ++$per[$slug];
    $per['module-view'] += count( $model['views'] );
    $per['policy-function'] += count( $model['policies'] );
    $per['fetch-function'] += count( $model['fetch'] );
    $per['event-hook'] += count( $model['hooks'] );
    $per['command'] += count( $model['cronjobs'] );
    $per['ini-plugin-point'] += count( $model['ini_points'] );
    foreach ( $model['operators'] as $entry )
        $per['template-operator'] += count( $entry['names'] );
    foreach ( $model['tplfunctions'] as $entry )
        $per['template-function'] += count( $entry['names'] );
    $model['stats']['per_pattern'] = $per;
    $model['stats']['instances'] = array_sum( $per );
}

/**
 * The parent classes of a class, nearest first (names as written when the parent is not in the catalogue).
 *
 * @param array $classes
 * @param array $known short lower-case name => key
 * @param string $key
 * @return string[]
 */
function exppcAncestors( $classes, $known, $key )
{
    $list = array();
    $seen = array( $key => true );
    $current = $key;
    while ( isset( $classes[$current] ) && $classes[$current]['extends'] && $classes[$current]['kind'] === 'class' )
    {
        $parent = $classes[$current]['extends'][0];
        $list[] = $parent;
        $next = strtolower( $parent );
        if ( !isset( $known[$next] ) || isset( $seen[$known[$next]] ) )
            break;
        $current = $known[$next];
        $seen[$current] = true;
    }
    return $list;
}

/**
 * Every interface a class or interface implements, also through its parents and parent interfaces.
 *
 * @param array $classes
 * @param array $known
 * @param string $key
 * @return string[]
 */
function exppcInterfaces( $classes, $known, $key )
{
    $result = array();
    $todo = array( $key );
    $seen = array();
    while ( $todo )
    {
        $current = array_shift( $todo );
        if ( isset( $seen[$current] ) || !isset( $classes[$current] ) )
            continue;
        $seen[$current] = true;
        $class = $classes[$current];
        $ifs = $class['kind'] === 'interface' ? $class['extends'] : $class['implements'];
        foreach ( $ifs as $if )
        {
            $result[$if] = true;
            if ( isset( $known[strtolower( $if )] ) )
                $todo[] = $known[strtolower( $if )];
        }
        if ( $class['kind'] === 'class' && $class['extends'] && isset( $known[strtolower( $class['extends'][0] )] ) )
            $todo[] = $known[strtolower( $class['extends'][0] )];
    }
    $names = array_keys( $result );
    sort( $names, SORT_STRING );
    return $names;
}

/**
 * Text for a Markdown table cell.
 *
 * @param string $text
 * @return string
 */
function exppcCell( $text )
{
    $text = str_replace( array( "\r", "\n", '|' ), array( ' ', ' ', '\\|' ), (string)$text );
    $text = str_replace( array( '<', '>' ), array( '‹', '›' ), $text );
    return $text === '' ? '–' : $text;
}

/**
 * Code text for a table cell.
 *
 * @param string $text
 * @return string
 */
function exppcCode( $text )
{
    return '`' . str_replace( array( '`', '|' ), array( "'", '\\|' ), (string)$text ) . '`';
}

/**
 * The file name of an area page: kernel/classes/datatypes -> area-kernel-classes-datatypes.md.
 *
 * @param string $area
 * @return string
 */
function exppcAreaFile( $area )
{
    return 'area-' . trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $area ) ), '-' ) . '.md';
}

/**
 * Natural, case-insensitive, stable order of strings.
 *
 * @param string $a
 * @param string $b
 * @return int
 */
function exppcCompareText( $a, $b )
{
    return strnatcasecmp( $a, $b ) ?: strcmp( $a, $b );
}

/**
 * The front matter and title of a generated page.
 *
 * @param string $title
 * @param int $order
 * @param string $summary
 * @param string $keywords
 * @return string
 */
function exppcHead( $title, $order, $summary, $keywords )
{
    return "---\ntitle: $title\naudience: developer\ncategory: standards\norder: $order\nsummary: $summary\nkeywords: $keywords\ntype: catalogue\n---\n# $title\n\n"
         . "> **Note**\n> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:\n"
         . "> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).\n\n";
}

/**
 * Renders every page: path => Markdown.
 *
 * @param array $model
 * @param array $sources
 * @return array
 */
function exppcRender( $model, $sources )
{
    $categories = exppcCategories();
    $pages = array();
    $classes = $model['classes'];
    $byPattern = array();
    $byArea = array();
    foreach ( $classes as $key => $class )
    {
        foreach ( $class['patterns'] as $slug )
            $byPattern[$slug][$class['area']][] = $key;
        $byArea[$class['area']]['classes'][] = $key;
    }
    foreach ( array( 'views', 'policies', 'fetch', 'operators', 'tplfunctions', 'cronjobs', 'hooks', 'ini_points' ) as $list )
        foreach ( $model[$list] as $entry )
            $byArea[$entry['area']][$list][] = $entry;
    uksort( $byArea, 'exppcCompareText' );

    // one page per pattern
    $order = 310;
    foreach ( $categories as $slug => $category )
    {
        list( $name, $group, $summary, $rule, $point ) = $category;
        $count = $model['stats']['per_pattern'][$slug];
        $md = exppcHead( $name, $order++, exppcCell( $summary ) . " $count instances in Exponential.", "design pattern, $slug, " . strtolower( $group ) . ', pattern catalogue' );
        $md .= "$summary\n\n";
        $md .= "| | |\n|---|---|\n| Group | $group |\n| Instances | $count |\n| Recognized by | " . exppcCell( $rule ) . " |\n";
        if ( $point !== '' )
            $md .= '| Extension point | ' . exppcCell( $point ) . " |\n";
        $md .= "\n";
        if ( !empty( $byPattern[$slug] ) )
        {
            $areas = $byPattern[$slug];
            uksort( $areas, 'exppcCompareText' );
            $md .= "## Classes by area\n\n";
            foreach ( $areas as $area => $keys )
            {
                usort( $keys, function ( $a, $b ) use ( $classes ) { return exppcCompareText( $classes[$a]['name'], $classes[$b]['name'] ); } );
                $md .= '### ' . $area . ' (' . count( $keys ) . ")\n\n[All patterns of $area](" . exppcAreaFile( $area ) . ")\n\n";
                $md .= "| Class | Kind | File | Description | Extension point |\n|---|---|---|---|---|\n";
                foreach ( $keys as $key )
                {
                    $class = $classes[$key];
                    $kind = ( $class['abstract'] ? 'abstract ' : '' ) . $class['kind'] . ( $class['extends'] ? ' extends ' . implode( ', ', $class['extends'] ) : '' );
                    $md .= '| ' . exppcCode( $class['name'] ) . ' | ' . exppcCell( $kind ) . ' | ' . exppcCode( $class['file'] . ':' . $class['line'] ) . ' | '
                         . exppcCell( $class['description'] ) . ' | ' . exppcCell( implode( '; ', $class['ini'] ) ) . " |\n";
                }
                $md .= "\n";
            }
        }
        $md .= exppcRenderLists( $slug, $model );
        $pages[EXPPC_OUT . '/' . $slug . '.md'] = rtrim( $md ) . "\n";
    }

    // one page per area
    $order = 400;
    foreach ( $byArea as $area => $lists )
    {
        $classCount = isset( $lists['classes'] ) ? count( $lists['classes'] ) : 0;
        $md = exppcHead( "Patterns in $area", $order++, "Every class, view, fetch function, operator and plugin point of $area with its design patterns.", "$area, design patterns, classes, extension points" );
        $md .= "Every class, interface and trait of `$area` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).\n\n";
        if ( $classCount )
        {
            $keys = $lists['classes'];
            usort( $keys, function ( $a, $b ) use ( $classes ) { return exppcCompareText( $classes[$a]['name'], $classes[$b]['name'] ); } );
            $md .= "## Classes ($classCount)\n\n| Class | Kind | Patterns | File | Description |\n|---|---|---|---|---|\n";
            foreach ( $keys as $key )
            {
                $class = $classes[$key];
                $links = array();
                foreach ( $class['patterns'] as $slug )
                    $links[] = '[' . $categories[$slug][0] . '](' . $slug . '.md)';
                $kind = ( $class['abstract'] ? 'abstract ' : '' ) . $class['kind'] . ( $class['extends'] ? ' extends ' . implode( ', ', $class['extends'] ) : '' )
                      . ( $class['implements'] ? ' implements ' . implode( ', ', $class['implements'] ) : '' );
                $md .= '| ' . exppcCode( $class['name'] ) . ' | ' . exppcCell( $kind ) . ' | ' . ( $links ? implode( ', ', $links ) : '–' ) . ' | '
                     . exppcCode( $class['file'] . ':' . $class['line'] ) . ' | ' . exppcCell( $class['description'] ) . " |\n";
            }
            $md .= "\n";
        }
        $md .= exppcRenderLists( null, $model, $area );
        $pages[EXPPC_OUT . '/' . exppcAreaFile( $area )] = rtrim( $md ) . "\n";
    }

    $pages[EXPPC_OUT . '/README.md'] = exppcRenderIndex( $model, $sources, $byArea );
    ksort( $pages, SORT_STRING );
    return $pages;
}

/**
 * The tables of the non-class instances (views, policies, fetch functions, operators, cronjobs, hooks, INI points) of
 * one pattern page, or of one area.
 *
 * @param string|null $slug the pattern page, or null for an area page
 * @param array $model
 * @param string|null $area
 * @return string
 */
function exppcRenderLists( $slug, $model, $area = null )
{
    $tables = array(
        'views' => array( 'module-view', 'Module views', array( 'View', 'Script', 'Policy functions', 'Defined in' ),
            function ( $e ) { return array( exppcCode( '/' . $e['module'] . '/' . $e['view'] ), exppcCode( $e['script'] ), exppcCell( implode( ', ', $e['functions'] ) ), exppcCode( $e['file'] . ':' . $e['line'] ) ); } ),
        'policies' => array( 'policy-function', 'Policy functions', array( 'Function', 'Limitations', 'Defined in' ),
            function ( $e ) { return array( exppcCode( $e['module'] . '/' . $e['function'] ), exppcCell( implode( ', ', $e['limitations'] ) ), exppcCode( $e['file'] . ':' . $e['line'] ) ); } ),
        'fetch' => array( 'fetch-function', 'Fetch functions', array( 'Fetch', 'Implemented by', 'Defined in' ),
            function ( $e ) { return array( exppcCode( "fetch( '" . $e['module'] . "', '" . $e['function'] . "' )" ), exppcCode( $e['class'] . '::' . $e['method'] . '()' ), exppcCode( $e['file'] . ':' . $e['line'] ) ); } ),
        'operators' => array( 'template-operator', 'Template operators', array( 'Operators', 'Class', 'Registered in' ),
            function ( $e ) { return array( exppcCell( implode( ', ', $e['names'] ) ), exppcCode( $e['class'] ), exppcCode( $e['file'] . ':' . $e['line'] ) ); } ),
        'tplfunctions' => array( 'template-function', 'Template functions', array( 'Functions', 'Class', 'Registered in' ),
            function ( $e ) { return array( exppcCell( implode( ', ', $e['names'] ) ), exppcCode( $e['class'] ), exppcCode( $e['file'] . ':' . $e['line'] ) ); } ),
        'cronjobs' => array( 'command', 'Cronjob parts', array( 'Cronjob', 'File', 'Description' ),
            function ( $e ) { return array( exppcCode( $e['name'] ), exppcCode( $e['file'] ), exppcCell( $e['description'] ) ); } ),
        'hooks' => array( 'event-hook', 'Event hooks', array( 'Event', 'Kind', 'Called in' ),
            function ( $e ) { return array( exppcCode( $e['event'] ), $e['kind'], exppcCode( $e['file'] . ':' . $e['line'] ) ); } ),
        'ini_points' => array( 'ini-plugin-point', 'INI plugin points', array( 'Setting', 'Class', 'Set in' ),
            function ( $e ) { return array( exppcCode( '[' . $e['section'] . '] ' . $e['key'] ), exppcCode( $e['class'] ), exppcCode( $e['source'] . ':' . $e['line'] ) ); } ),
    );
    $md = '';
    foreach ( $tables as $list => $table )
    {
        list( $pattern, $heading, $columns, $row ) = $table;
        if ( $slug !== null && $slug !== $pattern )
            continue;
        $entries = array();
        foreach ( $model[$list] as $entry )
            if ( $area === null || $entry['area'] === $area )
                $entries[] = $entry;
        if ( !$entries )
            continue;
        $grouped = array();
        foreach ( $entries as $entry )
            $grouped[$slug !== null ? $entry['area'] : ''][] = $entry;
        uksort( $grouped, 'exppcCompareText' );
        $md .= "## $heading (" . count( $entries ) . ")\n\n";
        foreach ( $grouped as $group => $items )
        {
            if ( $group !== '' )
                $md .= "### $group (" . count( $items ) . ")\n\n";
            $md .= '| ' . implode( ' | ', $columns ) . " |\n|" . str_repeat( '---|', count( $columns ) ) . "\n";
            foreach ( $items as $item )
                $md .= '| ' . implode( ' | ', $row( $item ) ) . " |\n";
            $md .= "\n";
        }
    }
    return $md;
}

/**
 * The index page with the statistics.
 *
 * @param array $model
 * @param array $sources
 * @param array $byArea
 * @return string
 */
function exppcRenderIndex( $model, $sources, $byArea )
{
    $categories = exppcCategories();
    $stats = $model['stats'];
    $kinds = array();
    foreach ( $model['classes'] as $class )
        $kinds[$class['kind']] = ( isset( $kinds[$class['kind']] ) ? $kinds[$class['kind']] : 0 ) + 1;
    ksort( $kinds, SORT_STRING );
    $classified = 0;
    foreach ( $model['classes'] as $class )
        if ( $class['patterns'] )
            ++$classified;
    $md = "---\ntitle: Design pattern catalogue\naudience: developer\ncategory: standards\norder: 300\n"
        . "summary: Every abstraction and design pattern of the Exponential 6 stack, found in the source code: " . $stats['instances'] . " instances in " . count( $categories ) . " categories across kernel, lib and extensions, with file, class, description and extension point.\n"
        . "keywords: design patterns, pattern catalogue, abstractions, architecture, factory, singleton, registry, strategy, handler, template method, observer, adapter, facade, active record, eZPersistentObject, datatype, template operator, fetch function, module view, policy function, workflow event, extension point, plugin, INI\n"
        . "type: reference\n---\n# Design pattern catalogue\n\n";
    $md .= "This catalogue is for developers who extend Exponential or change its kernel. It lists every abstraction the code uses, where it is, and how a new implementation is plugged in, so that new code follows the patterns that are already there instead of inventing new ones. It is generated from the source code, so it is complete and current for the code it was built from; the rules that recognize each pattern are heuristic and are explained below.\n\n";
    // the examples (doc/examples/) are linked once they are there
    if ( is_file( 'doc/examples/README.md' ) )
        $md .= "Runnable examples of most of these extension points: [Examples](../../examples/README.md)" . ( is_file( 'doc/examples/cookbook/README.md' ) ? " and the [cookbook](../../examples/cookbook/README.md)" : '' ) . ".\n\n";
    $md .= "The rules the code follows: [Standards](../README.md) and [Kernel architecture](../architecture/kernel-architecture.md).\n\n";
    $md .= "## In numbers\n\n| | |\n|---|---|\n";
    $md .= '| Instances (all categories) | ' . $stats['instances'] . " |\n";
    $md .= '| Classes, interfaces, traits and enums | ' . count( $model['classes'] ) . ' (' . implode( ', ', array_map( function ( $k, $n ) { return $n . " " . ( $n === 1 ? $k : ( $k === "class" ? "classes" : $k . "s" ) ); }, array_keys( $kinds ), $kinds ) ) . ") |\n";
    $md .= '| ... with at least one pattern | ' . $classified . " |\n";
    $md .= '| Module views | ' . count( $model['views'] ) . " |\n| Policy functions | " . count( $model['policies'] ) . " |\n| Fetch functions | " . count( $model['fetch'] ) . " |\n";
    $operators = 0;
    foreach ( $model['operators'] as $entry )
        $operators += count( $entry['names'] );
    $md .= "| Template operators | $operators |\n| Cronjob parts | " . count( $model['cronjobs'] ) . " |\n| Event hooks | " . count( $model['hooks'] ) . " |\n| INI plugin points | " . count( $model['ini_points'] ) . " |\n";
    $md .= '| Source files read | ' . $stats['php_files'] . ' PHP, ' . $stats['ini_files'] . " INI |\n| Areas | " . count( $byArea ) . " |\n\n";

    $md .= "## The patterns\n\n| Pattern | Group | Instances | Recognized by |\n|---|---|---|---|\n";
    foreach ( $categories as $slug => $category )
        $md .= '| [' . $category[0] . '](' . $slug . '.md) | ' . $category[1] . ' | ' . $stats['per_pattern'][$slug] . ' | ' . exppcCell( $category[3] ) . " |\n";
    $md .= "\n## The areas\n\nOne page per part of the code base: every class with its patterns, and the views, functions, operators and plugin points defined there.\n\n| Area | Classes | Views | Fetch functions | Operators | Plugin points |\n|---|---|---|---|---|---|\n";
    foreach ( $byArea as $area => $lists )
    {
        $ops = 0;
        if ( isset( $lists['operators'] ) )
            foreach ( $lists['operators'] as $entry )
                $ops += count( $entry['names'] );
        $md .= '| [' . $area . '](' . exppcAreaFile( $area ) . ') | ' . ( isset( $lists['classes'] ) ? count( $lists['classes'] ) : 0 ) . ' | '
             . ( isset( $lists['views'] ) ? count( $lists['views'] ) : 0 ) . ' | ' . ( isset( $lists['fetch'] ) ? count( $lists['fetch'] ) : 0 ) . ' | '
             . $ops . ' | ' . ( isset( $lists['ini_points'] ) ? count( $lists['ini_points'] ) : 0 ) . " |\n";
    }
    $md .= "\n## What was read\n\nThe git-tracked PHP and INI files below `kernel/`, `lib/` and `extension/` of the Exponential repository (tests and `vendor/` directories left out), and these extensions:\n\n| Extension | Source |\n|---|---|\n";
    foreach ( $sources['extensions'] as $name => $source )
        $md .= "| `$name` | " . ( $source === 'bundled' ? 'shipped with the kernel' : ( $source === 'package' ? 'Composer package' : 'its own repository' ) ) . " |\n";
    $md .= "\nAn extension is read when it is a git repository of its own below `extension/` whose origin is a se7enxweb repository, or a Composer package of se7enxweb installed there; private extensions and site settings are never read. A site that has other extensions installed gets a larger catalogue when it runs the generator.\n\n";
    $md .= "## How the catalogue is built\n\nRun in the installation root (no database, nothing is loaded or executed; PHP's tokenizer reads the files):\n\n```bash\n"
         . "php bin/php/exppatterncatalogue.php            # report which pages would change\n"
         . "php bin/php/exppatterncatalogue.php --write    # write doc/standards/patterns/\n"
         . "php bin/php/exppatterncatalogue.php --check    # exit 1 when the pages are out of date\n```\n\n";
    $md .= "The output depends only on the source: no dates, no commit ids, everything sorted, so a second run on the same code writes the same bytes. Commit the pages with the code change that changes them.\n\n";
    $md .= "Each category has a rule (the table above and each pattern's page). A class can match several categories. The rules use the class's name, its parent classes and interfaces (followed through the whole code base), its methods (static, abstract) and the registration files of the kernel: `module.php` (views and policy functions), `function_definition.php` (fetch functions), `eztemplateautoload.php` (template operators and functions), `cronjobs/` directories, `ezpEvent` calls and INI settings whose value is a class name. Names are a heuristic: a class called `...Manager` is listed as a facade or service even when it is something else, and a pattern implemented without the usual names is missed. Descriptions are the first sentence of each class's doc comment.\n\n";
    $md .= "> **Note**\n> The catalogue describes the code as it is, including inherited code. It is not a list of recommendations: for new code, follow [the layering and naming rules](../architecture/layering-and-naming.md).\n";
    return $md;
}

/**
 * Which pages differ from the files: path => created|changed|removed.
 *
 * @param array $pages
 * @return array
 */
function exppcCompare( $pages )
{
    $changed = array();
    foreach ( $pages as $path => $text )
    {
        if ( !is_file( $path ) )
            $changed[$path] = 'created';
        elseif ( file_get_contents( $path ) !== $text )
            $changed[$path] = 'changed';
    }
    foreach ( glob( EXPPC_OUT . '/*.md' ) ?: array() as $path )
        if ( !isset( $pages[$path] ) )
            $changed[$path] = 'removed';
    ksort( $changed, SORT_STRING );
    return $changed;
}

/**
 * Writes the pages and removes the ones that are no longer generated.
 *
 * @param array $pages
 */
function exppcWrite( $pages )
{
    if ( !is_dir( EXPPC_OUT ) )
        mkdir( EXPPC_OUT, 0775, true );
    foreach ( $pages as $path => $text )
        if ( !is_file( $path ) || file_get_contents( $path ) !== $text )
            file_put_contents( $path, $text );
    foreach ( glob( EXPPC_OUT . '/*.md' ) ?: array() as $path )
        if ( !isset( $pages[$path] ) )
            unlink( $path );
}
