<?php
/**
 * The content/cache event gives its listeners ( $nodeIDs, $objectIDs ) from every sender (L36): a listener such as
 * a search engine or an HTTP cache that purges by object can rely on the object ids being there.
 *
 *  CE-01 - every ezpEvent notify( 'content/cache', ... ) in kernel/, lib/, cronjobs/, bin/ and the kernel's own
 *          extensions (ezjscore, ezoe) passes an argument list of two: the node ids and the object ids
 *  CE-02 - the senders this was made for are among them: the cache manager, the URL aliases view (its three
 *          actions), the hide subtree job and the content operations
 *  CE-03 - a listener that takes only the node ids keeps working: the event calls it with both, PHP drops the second
 *
 * CE-01 and CE-02 read the PHP sources with PHP's tokenizer (no file is run); CE-03 sends the event on an event
 * instance of the test. No database. The hide cronjob's event is tested on a live installation in
 * HideCronjobSearchIndexTest.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 3298b53584 and 89a5a73877), Copyright (C)
 * Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZContentCacheEventSendersTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZContentCacheEventSendersTest extends PHPUnit\Framework\TestCase
{
    const DIRECTORIES = array( 'kernel', 'lib', 'cronjobs', 'bin', 'extension/ezjscore', 'extension/ezoe' );

    /** @var array[]|null file => list of argument counts of its content/cache notifications */
    private static $senders = null;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /** The number of top-level elements of the array( ... ) or [ ... ] starting at token $i, or -1 */
    private static function arrayLength( array $tokens, $i )
    {
        $n = count( $tokens );
        while ( $i < $n && is_array( $tokens[$i] ) && $tokens[$i][0] === T_WHITESPACE )
            $i++;
        if ( $i < $n && is_array( $tokens[$i] ) && $tokens[$i][0] === T_ARRAY )
        {
            $i++;
            while ( $i < $n && is_array( $tokens[$i] ) && $tokens[$i][0] === T_WHITESPACE )
                $i++;
        }
        if ( $i >= $n || !in_array( $tokens[$i], array( '(', '[' ), true ) )
            return -1;
        $depth = 0;
        $elements = 0;
        $content = false;
        for ( ; $i < $n; $i++ )
        {
            $t = $tokens[$i];
            if ( in_array( $t, array( '(', '[', '{' ), true ) || ( is_array( $t ) && in_array( $t[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) )
            {
                if ( $depth === 1 )
                    $content = true;
                $depth++;
                continue;
            }
            if ( in_array( $t, array( ')', ']', '}' ), true ) )
            {
                $depth--;
                if ( $depth === 0 )
                    return $elements + ( $content ? 1 : 0 );
                continue;
            }
            if ( $depth === 1 )
            {
                if ( $t === ',' )
                {
                    $elements++;
                    $content = false;
                }
                else if ( !is_array( $t ) || !in_array( $t[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) )
                    $content = true;
            }
        }
        return -1;
    }

    private static function senders()
    {
        if ( self::$senders !== null )
            return self::$senders;
        self::$senders = array();
        foreach ( self::DIRECTORIES as $directory )
        {
            if ( !is_dir( $directory ) )
                continue;
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.php' )
                    continue;
                $source = file_get_contents( $file->getPathname() );
                if ( strpos( $source, 'content/cache' ) === false )
                    continue;
                $tokens = token_get_all( $source );
                foreach ( $tokens as $i => $t )
                {
                    if ( !is_array( $t ) || $t[0] !== T_STRING || strtolower( $t[1] ) !== 'notify' )
                        continue;
                    $j = $i + 1;
                    while ( isset( $tokens[$j] ) && is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        $j++;
                    if ( !isset( $tokens[$j] ) || $tokens[$j] !== '(' )
                        continue;
                    $j++;
                    while ( isset( $tokens[$j] ) && is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        $j++;
                    if ( !isset( $tokens[$j] ) || !is_array( $tokens[$j] ) || $tokens[$j][0] !== T_CONSTANT_ENCAPSED_STRING ||
                         trim( $tokens[$j][1], '\'"' ) !== 'content/cache' )
                        continue;
                    $j++;
                    while ( isset( $tokens[$j] ) && ( $tokens[$j] === ',' || ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE ) ) )
                        $j++;
                    self::$senders[$file->getPathname()][] = array( 'line' => $tokens[$j][2] ?? $t[2], 'arguments' => self::arrayLength( $tokens, $j ) );
                }
            }
        }
        ksort( self::$senders );
        return self::$senders;
    }

    /** CE-01 */
    public function testEverySenderGivesNodeAndObjectIds()
    {
        $senders = self::senders();
        $this->assertNotSame( array(), $senders );
        foreach ( $senders as $file => $calls )
        {
            foreach ( $calls as $call )
                $this->assertSame( 2, $call['arguments'], "$file:{$call['line']} notifies content/cache with ( \$nodeIDs, \$objectIDs )" );
        }
    }

    /** CE-02 */
    public function testTheSendersOfL36AreFound()
    {
        $senders = self::senders();
        $this->assertCount( 3, $senders['kernel/private/classes/views/content/urlalias.php'] ?? array(), 'the URL aliases view: remove all, remove, new alias' );
        foreach ( array( 'kernel/classes/ezcontentcachemanager.php', 'kernel/classes/contentjob/expcontentjobhidesubtree.php',
                         'kernel/content/ezcontentoperationcollection.php' ) as $file )
            $this->assertArrayHasKey( $file, $senders );
    }

    /** CE-03 */
    public function testAListenerOfTheNodeIdsOnlyKeepsWorking()
    {
        $event = new ezpEvent( false );
        $seen = array();
        $event->attach( 'content/cache', function ( $nodeIDs ) use ( &$seen )
        {
            $seen[] = array( 'nodes' => $nodeIDs );
        } );
        $event->attach( 'content/cache', function ( $nodeIDs, $objectIDs ) use ( &$seen )
        {
            $seen[] = array( 'nodes' => $nodeIDs, 'objects' => $objectIDs );
        } );
        $event->notify( 'content/cache', array( array( 54 ), array( 52 ) ) );
        $this->assertSame( array( array( 'nodes' => array( 54 ) ), array( 'nodes' => array( 54 ), 'objects' => array( 52 ) ) ), $seen );
    }
}
