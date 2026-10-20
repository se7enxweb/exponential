<?php
/**
 * @description The help system on the command line: build and describe the index of doc/, search it, show a page's metadata, check its links and images
 * @alias exp:help
 *
 * File containing the exp:help command (doc/en/us/features/6.0/help-system.md, "On the command line").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exphelp extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "The help system: the index of the documentation, its search and its link check\n" .
                                 "\n" .
                                 "./console exp:help index                     build the index (if it is not current) and describe it\n" .
                                 "./console exp:help index --rebuild           build it again, whatever is cached\n" .
                                 "./console exp:help index --list              one line per page: id, type, category, audiences, title\n" .
                                 "./console exp:help search \"clear cache\"      search the pages (--audience, --category, --type, --limit)\n" .
                                 "./console exp:help show guides/caches.md     the metadata and headings of one page\n" .
                                 "./console exp:help check                     check the links and images of every page (--below=guides/)\n" .
                                 "./console exp:help pdf help/searching.md      the page as a PDF file (--out=file.pdf, --base-url=https://host)\n" .
                                 "./console exp:help maps --language=de        check doc/de/.paths.json and .dirnames.json; --fix writes the\n" .
                                 "                                             union of both sides of a git conflict (and of --paths=, --dirnames=)\n" .
                                 "\n" .
                                 "Only git-tracked files below doc/ (and the doc/ of the extensions in exphelp.ini ExtensionDocDirs[]) are\n" .
                                 "pages; nothing is changed except the cache in var/<site>/cache/exphelp. --json prints one JSON document.\n" .
                                 "index builds the index of every language of exphelp.ini [DocLanguages] (doc/en/us/, doc/de/ ...);\n" .
                                 "--language=de limits it to one, and makes search, show, check and pdf read that language.\n" .
                                 "Exit code: 0 fine, 1 check found errors (with --strict also warnings) or nothing was found, 2 error.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[rebuild][list][json][audience:][category:][type:][limit:][below:][all][strict][out:][base-url:][language:][fix][paths:][dirnames:]', '', array(
            'fix'      => 'maps: write .paths.json and .dirnames.json as the union of their entries, sorted',
            'paths'    => 'maps --fix: also merge the entries of this JSON file into .paths.json',
            'dirnames' => 'maps --fix: also merge the entries of this JSON file into .dirnames.json',
            'language' => 'the documentation language: a directory of exphelp.ini [DocLanguages] Directories[] (en/us, de)',
            'rebuild'  => 'index: build the index again even when the cached one is current',
            'list'     => 'index: list every page',
            'json'     => 'Print one JSON document instead of the text',
            'audience' => 'search, index --list: only pages for this audience (editor, admin, developer, user)',
            'category' => 'search, index --list: only pages of this category',
            'type'     => 'search, index --list: only pages of this type (guide, feature, bc, changelog ...)',
            'limit'    => 'search: the number of results (default 10)',
            'below'    => 'check, index --list: only the pages whose id starts with this (e.g. guides/)',
            'all'      => 'check: also list the notices (links to files that are not help pages)',
            'strict'   => 'check: exit 1 on warnings too',
            'out'      => 'pdf: the file to write (default: the page title as file name, in the current directory)',
            'base-url' => 'pdf: scheme and host for the links and the page address (https://www.example.com)',
        ) );

        $arguments = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $arguments ? strtolower( (string)array_shift( $arguments ) ) : 'index';
        $json = !empty( $options['json'] );
        try
        {
            $languages = \expHelpLanguage::fromIni();
            $language = $this->languageOption( $options, $languages );
            if ( $language === false )
            {
                $this->cli()->error( 'Unknown --language: one of ' . implode( ', ', $languages->directories() ) );
                $this->shutdown( 2 );
                return;
            }
            // search, show, check and pdf read the index of the language asked for
            if ( $language !== null && $action !== 'index' )
                \expHelpDocumentIndex::setInstance( \expHelpDocumentIndex::forLanguage( $language, $languages ) );
            switch ( $action )
            {
                case 'index':
                    $code = $this->index( $options, $json );
                    break;
                case 'search':
                    $code = $this->search( implode( ' ', $arguments ), $options, $json );
                    break;
                case 'show':
                    $code = $this->show( $arguments ? (string)$arguments[0] : '', $json );
                    break;
                case 'check':
                    $code = $this->check( $options, $json );
                    break;
                case 'pdf':
                    $code = $this->pdf( $arguments ? (string)$arguments[0] : '', $options, $json );
                    break;
                case 'maps':
                    $code = $this->maps( $options, $language, $languages, $json );
                    break;
                default:
                    $cli->error( "Unknown action \"$action\": index, search, show, check, pdf or maps (./console exp:help --help)" );
                    $code = 2;
            }
        }
        catch ( \Exception $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $code = 2;
        }
        $this->shutdown( $code );
    }

    /**
     * The --language option: a configured directory, null when it is not given, false when it names none.
     *
     * @param array $options
     * @param \expHelpLanguage $languages
     * @return string|null|false
     */
    protected function languageOption( array $options, $languages )
    {
        if ( !isset( $options['language'] ) || $options['language'] === '' || $options['language'] === null )
            return null;
        $directory = \expHelpLanguage::cleanDirectory( (string)$options['language'] );
        return $directory !== null && $languages->isDirectory( $directory ) ? $directory : false;
    }

    /**
     * The report of exp:help index --language=<a translation>.
     *
     * @param array $reports directory => row
     * @param bool $json
     * @return int
     */
    protected function languageReport( array $reports, $json )
    {
        if ( $json )
        {
            $this->printJson( array( 'languages' => $reports ) );
            return 0;
        }
        foreach ( $reports as $directory => $row )
            $this->cli()->output( sprintf( '%s: %d pages, %d translated, %d shown in the original; cache %s', $directory,
                                           $row['pages'], $row['translated'], $row['fallback'], (string)$row['cache'] ) );
        return 0;
    }

    /**
     * exp:help index [--rebuild] [--list]
     *
     * @param array $options
     * @param bool $json
     * @return int
     */
    protected function index( array $options, $json )
    {
        // every language of the documentation has its own index ([DocLanguages] Directories[]); --language one of them
        $languages = \expHelpLanguage::fromIni();
        $wanted = $this->languageOption( $options, $languages );
        $languageReports = array();
        foreach ( $languages->directories() as $directory )
        {
            if ( $directory === $languages->defaultDirectory() || ( $wanted !== null && $wanted !== $directory ) )
                continue;
            $other = \expHelpDocumentIndex::forLanguage( $directory, $languages );
            $otherStats = !empty( $options['rebuild'] ) ? $other->rebuild() : null;
            $otherDocuments = $other->documents();
            $translated = count( array_filter( $otherDocuments, function ( $d ) { return empty( $d['fallback'] ); } ) );
            $otherSearch = method_exists( 'expHelpSearch', 'rebuild' ) ? ( new \expHelpSearch( $other ) )->rebuild() : null;
            $languageReports[$directory] = array( 'pages' => count( $otherDocuments ), 'translated' => $translated,
                                                  'fallback' => count( $otherDocuments ) - $translated,
                                                  'rebuilt' => $otherStats !== null, 'cache' => $other->cacheDir(),
                                                  'search' => is_array( $otherSearch ) ? $otherSearch : null );
        }
        $index = \expHelpDocumentIndex::forLanguage( $languages->defaultDirectory(), $languages );
        if ( $wanted !== null && $wanted !== $languages->defaultDirectory() )
            return $this->languageReport( $languageReports, $json );
        $stats = null;
        $start = microtime( true );
        if ( !empty( $options['rebuild'] ) )
            $stats = $index->rebuild();
        $documents = $index->documents();
        $seconds = round( microtime( true ) - $start, 3 );
        // the search index too, so the first search on the web does not build it
        $searchStats = null;
        if ( method_exists( 'expHelpSearch', 'rebuild' ) )
        {
            $search = new \expHelpSearch( $index );
            $searchStats = $search->rebuild();
        }
        $report = array(
            'signature'  => $index->signature(),
            'cache'      => $index->cacheDir(),
            'rebuilt'    => $stats !== null,
            'seconds'    => $stats !== null ? $stats['seconds'] : $seconds,
            'sources'    => $index->sources(),
            'roots'      => $index->allowlist()->roots(),
            'pages'      => count( $documents ),
            'images'     => count( $index->assets() ),
            'hidden'     => count( $documents ) - count( $index->visibleDocuments() ),
            'types'      => array_map( function ( $t ) { return $t['count']; }, $index->types() ),
            'categories' => array_map( function ( $c ) { return $c['count']; }, $index->categories() ),
            'audiences'  => array_map( function ( $a ) { return $a['count']; }, $index->audiences() ),
            'search'     => is_array( $searchStats ) ? $searchStats : null,
            'language'   => $index->language(),
            'languages'  => $languageReports,
        );
        $list = array();
        if ( !empty( $options['list'] ) )
            foreach ( $this->filter( $documents, $options ) as $id => $document )
                $list[] = array( 'id' => $id, 'type' => $document['type'], 'category' => $document['category'],
                                 'audience' => $document['audience'], 'title' => $document['title'] );

        if ( $json )
        {
            $this->printJson( $report + ( $list || !empty( $options['list'] ) ? array( 'list' => $list ) : array() ) );
            return $report['pages'] > 0 ? 0 : 1;
        }
        $cli = $this->cli();
        if ( $list )
        {
            foreach ( $list as $row )
                $cli->output( sprintf( '%-60s %-13s %-16s %-26s %s', $row['id'], $row['type'], $row['category'],
                                       implode( ',', $row['audience'] ), $row['title'] ) );
            $cli->output( '' );
        }
        $cli->output( sprintf( '%s: %d pages, %d images (%s), %s s, signature %s',
                               $report['rebuilt'] ? 'Built' : 'Index', $report['pages'], $report['images'],
                               implode( ', ', array_map( function ( $root, $source ) { return "$root from $source"; },
                                                         array_keys( $report['sources'] ), $report['sources'] ) ),
                               $report['seconds'], substr( $report['signature'], 0, 12 ) ) );
        $cli->output( 'Cache: ' . ( $report['cache'] === null ? '(none)' : $report['cache'] ) );
        if ( is_array( $report['search'] ) )
            $cli->output( 'Search index: ' . implode( ', ', array_map( function ( $k, $v ) { return $k . ' ' . ( is_scalar( $v ) ? $v : json_encode( $v ) ); },
                                                                      array_keys( $report['search'] ), $report['search'] ) ) );
        foreach ( array( 'types', 'categories', 'audiences' ) as $group )
            $cli->output( sprintf( '%-11s %s', ucfirst( $group ) . ':', implode( ', ', array_map( function ( $k, $n ) { return "$k $n"; },
                                   array_keys( array_filter( $report[$group] ) ), array_filter( $report[$group] ) ) ) ) );
        $cli->output( sprintf( '%-11s %s (the original)', 'Language:', $report['language'] ) );
        foreach ( $languageReports as $directory => $row )
            $cli->output( sprintf( '%-11s %s: %d pages, %d translated, %d shown in %s', 'Language:', $directory, $row['pages'],
                                   $row['translated'], $row['fallback'], $report['language'] ) );
        if ( $report['pages'] === 0 )
        {
            $cli->error( 'No pages: is doc/ there and tracked by git, or listed in share/filelist.md5?' );
            return 1;
        }
        return 0;
    }

    /**
     * exp:help search <words>
     *
     * @param string $query
     * @param array $options
     * @param bool $json
     * @return int
     */
    protected function search( $query, array $options, $json )
    {
        $cli = $this->cli();
        if ( trim( $query ) === '' )
        {
            $cli->error( 'Usage: ./console exp:help search "<words>" [--audience=editor] [--category=...] [--type=...] [--limit=10]' );
            return 2;
        }
        $filters = array();
        foreach ( array( 'audience', 'category', 'type' ) as $key )
            if ( !empty( $options[$key] ) )
                $filters[$key] = (string)$options[$key];
        $limit = !empty( $options['limit'] ) ? max( 1, min( 200, (int)$options['limit'] ) ) : 10;
        $search = new \expHelpSearch( \expHelpDocumentIndex::instance() );
        $result = $search->search( $query, $filters, $limit, 0 );
        $hits = isset( $result['hits'] ) ? $result['hits'] : array();
        if ( $json )
        {
            $this->printJson( $result );
            return $hits ? 0 : 1;
        }
        if ( !$hits )
        {
            $cli->output( 'No page matches "' . $query . '".' );
            return 1;
        }
        foreach ( $hits as $n => $hit )
        {
            $cli->output( sprintf( '%2d. %s  (%s%s)', $n + 1, $hit['title'], $hit['id'], !empty( $hit['anchor'] ) ? '#' . $hit['anchor'] : '' ) );
            if ( !empty( $hit['snippet'] ) )
                $cli->output( '    ' . trim( (string)preg_replace( '/\s+/', ' ', strip_tags( $hit['snippet'] ) ) ) );
        }
        $cli->output( sprintf( '%d of %d results', count( $hits ), isset( $result['total'] ) ? (int)$result['total'] : count( $hits ) ) );
        return 0;
    }

    /**
     * exp:help show <id>
     *
     * @param string $id
     * @param bool $json
     * @return int
     */
    protected function show( $id, $json )
    {
        $cli = $this->cli();
        $index = \expHelpDocumentIndex::instance();
        // a file name (doc/en/us/<id>, doc/<language>/<translated path>) names its page
        $id = trim( $id ) === '' ? '' : \expHelpLanguage::fromIni()->idOfValue( trim( $id ) );
        $document = $id === '' ? null : $index->document( $id );
        if ( $document === null )
        {
            $cli->error( $id === '' ? 'Usage: ./console exp:help show <id>, e.g. guides/caches.md'
                                    : "Not a help page: $id (unknown, not tracked by git, or excluded)" );
            return $id === '' ? 2 : 1;
        }
        $neighbours = $index->neighbours( $id );
        if ( $json )
        {
            $this->printJson( $document + array( 'prev' => $neighbours['prev'] ? $neighbours['prev']['id'] : null,
                                                 'next' => $neighbours['next'] ? $neighbours['next']['id'] : null ) );
            return 0;
        }
        foreach ( array( 'id', 'path', 'title', 'type', 'category', 'format', 'order', 'summary' ) as $key )
            $cli->output( sprintf( '%-10s %s', $key . ':', (string)$document[$key] ) );
        $cli->output( sprintf( '%-10s %s', 'audience:', implode( ', ', $document['audience'] ) ) );
        if ( $document['keywords'] )
            $cli->output( sprintf( '%-10s %s', 'keywords:', implode( ', ', $document['keywords'] ) ) );
        if ( $document['context'] )
            $cli->output( sprintf( '%-10s %s', 'context:', implode( ', ', $document['context'] ) ) );
        $cli->output( sprintf( '%-10s %s, %d bytes', 'changed:', date( 'Y-m-d H:i', $document['changed'] ), $document['size'] ) );
        $cli->output( sprintf( '%-10s %s', 'prev:', $neighbours['prev'] ? $neighbours['prev']['id'] : '-' ) );
        $cli->output( sprintf( '%-10s %s', 'next:', $neighbours['next'] ? $neighbours['next']['id'] : '-' ) );
        if ( $document['headings'] )
        {
            $cli->output( 'headings:' );
            foreach ( $document['headings'] as $heading )
                $cli->output( str_repeat( '  ', $heading['level'] ) . $heading['text'] . '  #' . $heading['anchor'] );
        }
        return 0;
    }

    /**
     * exp:help pdf <id> [--out=file.pdf] [--base-url=https://host]: the page as a PDF (expHelpPdf, the same file the
     * view exphelp/pdf serves, from the same cache)
     *
     * @param string $id
     * @param array $options
     * @param bool $json
     * @return int
     */
    protected function pdf( $id, array $options, $json )
    {
        $cli = $this->cli();
        // a file name (doc/en/us/<id>, doc/<language>/<translated path>) names its page
        $id = trim( $id ) === '' ? '' : \expHelpLanguage::fromIni()->idOfValue( trim( $id ) );
        if ( $id === '' )
        {
            $cli->error( 'Usage: ./console exp:help pdf <id> [--out=file.pdf] [--base-url=https://www.example.com]' );
            return 2;
        }
        $baseUrl = isset( $options['base-url'] ) && is_string( $options['base-url'] ) ? trim( $options['base-url'] ) : '';
        if ( $baseUrl !== '' && preg_match( '#^https?://[A-Za-z0-9.-]+(:[0-9]+)?$#', rtrim( $baseUrl, '/' ) ) !== 1 )
        {
            $cli->error( "--base-url takes a scheme and host, e.g. https://www.example.com: $baseUrl" );
            return 2;
        }
        $pdf = \expHelpPdf::forPage( $id, $baseUrl );
        if ( $pdf === null )
        {
            $cli->error( "Not a help page: $id (unknown, not tracked by git, or excluded)" );
            return 1;
        }
        $out = isset( $options['out'] ) && is_string( $options['out'] ) && $options['out'] !== '' ? $options['out'] : $pdf['filename'];
        $data = $pdf['file'] !== null ? (string)file_get_contents( $pdf['file'] ) : (string)$pdf['data'];
        if ( @file_put_contents( $out, $data ) !== strlen( $data ) )
        {
            $cli->error( "FAIL: could not write $out" );
            return 2;
        }
        if ( $json )
            $this->printJson( array( 'id' => $id, 'file' => $out, 'size' => strlen( $data ), 'cached' => $pdf['cached'] ) );
        else
            $cli->output( sprintf( 'PASS %s -> %s (%d bytes%s)', $id, $out, strlen( $data ), $pdf['cached'] ? ', from the cache' : '' ) );
        return 0;
    }

    /**
     * exp:help check [--below=] [--all] [--strict]
     *
     * @param array $options
     * @param bool $json
     * @return int
     */
    protected function check( array $options, $json )
    {
        $checker = new \expHelpLinkCheck( \expHelpDocumentIndex::instance() );
        $problems = $checker->check( !empty( $options['below'] ) ? preg_replace( '#^doc/#', '', (string)$options['below'] ) : null );
        $stats = $checker->stats();
        if ( empty( $options['all'] ) )
            $problems = array_values( array_filter( $problems, function ( $p ) { return $p['severity'] !== 'notice'; } ) );
        $failed = $stats['error'] > 0 || ( !empty( $options['strict'] ) && $stats['warning'] > 0 );
        if ( $json )
        {
            $this->printJson( array( 'stats' => $stats, 'problems' => $problems ) );
            return $failed ? 1 : 0;
        }
        $cli = $this->cli();
        foreach ( $problems as $problem )
            $cli->output( sprintf( '%-7s %s:%d  %s %s  [%s]%s', strtoupper( $problem['severity'] ), $problem['id'], $problem['line'],
                                   $problem['kind'], $problem['target'], $problem['problem'],
                                   $problem['resolved'] !== '' && $problem['resolved'] !== $problem['target'] ? ' -> ' . $problem['resolved'] : '' ) );
        $cli->output( sprintf( '%s: %d pages, %d links, %d images, %d external (not fetched); %d errors, %d warnings, %d notices',
                               $failed ? 'FAIL' : 'PASS', $stats['pages'], $stats['links'], $stats['images'], $stats['external'],
                               $stats['error'], $stats['warning'], $stats['notice'] ) );
        return $failed ? 1 : 0;
    }

    /**
     * maps: the translated file names of a language (doc/<language>/.paths.json) and its directory names
     * (.dirnames.json, English name => translated name). Checks them: JSON objects of clean relative paths, no page
     * under two names, written sorted, each translated file present and its original too, every directory named as
     * .dirnames.json says. --fix writes them back as the union of every entry: of both sides of a git conflict in the
     * file, and of the maps --paths= and --dirnames= name (a translator's own list); a conflict keeps the file's entry
     * and is reported.
     *
     * @param array $options
     * @param string|null $language
     * @param \expHelpLanguage $languages
     * @param bool $json
     * @return int
     */
    protected function maps( array $options, $language, $languages, $json )
    {
        $cli = $this->cli();
        if ( $language === null || $language === $languages->defaultDirectory() )
        {
            $cli->error( 'maps needs --language= with a translation: ' . implode( ', ', array_slice( $languages->directories(), 1 ) ) );
            return 2;
        }
        $root = \expHelp::installRoot();
        $dir = ( $root === '' ? '' : rtrim( $root, '/' ) . '/' ) . \expHelpLanguage::DOC_ROOT . '/' . $language;
        $result = array( 'language' => $language, 'files' => array(), 'problems' => array(), 'conflicts' => array(), 'written' => array() );
        $merged = array();
        foreach ( array( 'paths' => \expHelpLanguage::PATH_MAP_FILE, 'dirnames' => \expHelpLanguage::DIRNAMES_FILE ) as $kind => $name )
        {
            $file = $dir . '/' . $name;
            $text = is_file( $file ) ? (string)file_get_contents( $file ) : '';
            $sides = $text === '' ? array() : \expHelpLanguage::decodeMapSides( $text );
            foreach ( $sides as $i => $side )
                if ( $side === null )
                    $result['problems'][] = sprintf( '%s: %s is not a JSON object', $name, count( $sides ) > 1 ? ( $i === 0 ? 'our side of the conflict' : 'their side of the conflict' ) : 'the file' );
            if ( count( $sides ) > 1 )
                $result['problems'][] = sprintf( '%s has git conflict markers (--fix merges both sides)', $name );
            $extra = isset( $options[$kind] ) && is_string( $options[$kind] ) && $options[$kind] !== '' ? (string)$options[$kind] : '';
            if ( $extra !== '' )
            {
                $data = is_file( $extra ) ? json_decode( (string)file_get_contents( $extra ), true ) : null;
                if ( !is_array( $data ) )
                {
                    $cli->error( "--$kind=$extra is not a readable JSON object" );
                    return 2;
                }
                $sides[] = $data;
            }
            $merge = \expHelpLanguage::mergePathMaps( array_values( array_filter( $sides, 'is_array' ) ) );
            $merged[$kind] = $merge['map'];
            foreach ( $merge['invalid'] as $message )
                $result['problems'][] = $name . ': ' . $message;
            foreach ( $merge['conflicts'] as $message )
                $result['conflicts'][] = $name . ': ' . $message;
            $encoded = \expHelpLanguage::encodePathMap( $merge['map'] );
            $result['files'][$name] = count( $merge['map'] );
            if ( $text !== '' && $text !== $encoded && count( $sides ) === 1 && !$merge['invalid'] )
                $result['problems'][] = sprintf( '%s is not written sorted, one entry per line (--fix writes it so)', $name );
            if ( !empty( $options['fix'] ) && ( $text !== '' || $merge['map'] ) && $text !== $encoded )
            {
                if ( @file_put_contents( $file, $encoded ) === false )
                {
                    $cli->error( "FAIL: $file could not be written" );
                    return 2;
                }
                $result['written'][] = $name;
            }
        }
        $defaultDir = ( $root === '' ? '' : rtrim( $root, '/' ) . '/' ) . \expHelpLanguage::DOC_ROOT . '/' . $languages->defaultDirectory();
        foreach ( $merged['paths'] as $translated => $original )
        {
            if ( !is_file( $dir . '/' . $translated ) )
                $result['problems'][] = sprintf( '%s: "%s" is not a file of doc/%s/', \expHelpLanguage::PATH_MAP_FILE, $translated, $language );
            if ( !is_file( $defaultDir . '/' . $original ) )
                $result['problems'][] = sprintf( '%s: "%s" translates "%s", which is not a page of doc/%s/', \expHelpLanguage::PATH_MAP_FILE, $translated, $original, $languages->defaultDirectory() );
        }
        foreach ( \expHelpLanguage::pathsAgainstDirnames( $merged['paths'], $merged['dirnames'] ) as $message )
            $result['problems'][] = \expHelpLanguage::PATH_MAP_FILE . ': ' . $message;
        // after --fix the conflict markers and the order are gone: what is left is a problem of the entries
        if ( $result['written'] )
            $result['problems'] = array_values( array_filter( $result['problems'], function ( $p ) {
                return strpos( $p, 'conflict markers' ) === false && strpos( $p, 'not written sorted' ) === false;
            } ) );
        $failed = $result['problems'] || $result['conflicts'];
        if ( $json )
        {
            $this->printJson( $result );
            return $failed ? 1 : 0;
        }
        foreach ( $result['conflicts'] as $message )
            $cli->output( 'CONFLICT ' . $message );
        foreach ( $result['problems'] as $message )
            $cli->output( 'ERROR    ' . $message );
        foreach ( $result['written'] as $name )
            $cli->output( "written  doc/$language/$name" );
        $cli->output( sprintf( '%s: doc/%s/ %s', $failed ? 'FAIL' : 'PASS', $language,
                               implode( ', ', array_map( function ( $name, $count ) { return "$name $count entries"; }, array_keys( $result['files'] ), $result['files'] ) ) ) );
        return $failed ? 1 : 0;
    }

    /**
     * The documents that pass --audience, --category, --type and --below.
     *
     * @param array $documents
     * @param array $options
     * @return array
     */
    protected function filter( array $documents, array $options )
    {
        return array_filter( $documents, function ( $document ) use ( $options ) {
            if ( !empty( $options['audience'] ) && !in_array( $options['audience'], $document['audience'], true ) )
                return false;
            if ( !empty( $options['category'] ) && $document['category'] !== $options['category'] )
                return false;
            if ( !empty( $options['type'] ) && $document['type'] !== $options['type'] )
                return false;
            if ( !empty( $options['below'] ) && strncmp( $document['id'], preg_replace( '#^doc/#', '', $options['below'] ), strlen( preg_replace( '#^doc/#', '', $options['below'] ) ) ) !== 0 )
                return false;
            return true;
        } );
    }

    /**
     * @param mixed $data
     */
    protected function printJson( $data )
    {
        $this->cli()->output( (string)json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE ) );
    }
}

}
