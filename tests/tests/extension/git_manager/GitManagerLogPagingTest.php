<?php
/**
 * The commit log's pages (GitManagerLog): the paging maths, the view parameters a link carries and reads back, and
 * the argument lists for git, which never let a value become a command or an option. No database, no git.
 *
 * The same test as git_manager's tests/unit/LogPagingTest.php. Skips when extension/git_manager (2.0.21 or later)
 * is not installed; GIT_MANAGER_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit --testsuite admin-extensions
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class GitManagerLogPagingTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $ext = getenv( 'GIT_MANAGER_EXTENSION_DIR' ) ?: dirname( __DIR__, 4 ) . '/extension/git_manager';
        if ( !class_exists( 'GitManagerLog', false ) && is_file( $ext . '/classes/gitmanagerlog.php' ) )
        {
            require_once $ext . '/classes/gitmanagerlog.php';
        }
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'GitManagerLog', false ) )
        {
            $this->markTestSkipped( 'extension/git_manager 2.0.21 or later is not installed' );
        }
    }

    // ---- paging maths ---------------------------------------------------------------------------------------------

    public function testFirstPageOfMany()
    {
        $p = GitManagerLog::paging( 26911, 0, 25 );
        $this->assertSame( 1, $p['page'] );
        $this->assertSame( 1077, $p['page_count'] );
        $this->assertSame( 1, $p['from'] );
        $this->assertSame( 25, $p['to'] );
        $this->assertFalse( $p['first_offset'] );
        $this->assertFalse( $p['previous_offset'] );
        $this->assertSame( 25, $p['next_offset'] );
        $this->assertSame( 26900, $p['last_offset'] );
        $this->assertTrue( $p['is_first'] );
        $this->assertFalse( $p['is_last'] );
    }

    public function testLastPageIsPartial()
    {
        $p = GitManagerLog::paging( 26911, 26900, 25 );
        $this->assertSame( 1077, $p['page'] );
        $this->assertSame( 26901, $p['from'] );
        $this->assertSame( 26911, $p['to'] );
        $this->assertSame( 0, $p['first_offset'] );
        $this->assertSame( 26875, $p['previous_offset'] );
        $this->assertFalse( $p['next_offset'] );
        $this->assertFalse( $p['last_offset'] );
        $this->assertTrue( $p['is_last'] );
    }

    public function testOffsetMovesToTheStartOfItsPage()
    {
        $p = GitManagerLog::paging( 200, 63, 25 );
        $this->assertSame( 50, $p['offset'] );
        $this->assertSame( 3, $p['page'] );
        $this->assertSame( 51, $p['from'] );
    }

    public function testOffsetPastTheEndShowsTheLastPage()
    {
        $p = GitManagerLog::paging( 120, 999999, 50 );
        $this->assertSame( 100, $p['offset'] );
        $this->assertSame( 3, $p['page'] );
        $this->assertSame( 101, $p['from'] );
        $this->assertSame( 120, $p['to'] );
    }

    public function testExactMultipleHasNoEmptyLastPage()
    {
        $p = GitManagerLog::paging( 100, 0, 50 );
        $this->assertSame( 2, $p['page_count'] );
        $this->assertSame( 50, $p['last_offset'] );
    }

    public function testNothingToShow()
    {
        $p = GitManagerLog::paging( 0, 75, 25 );
        $this->assertSame( 0, $p['offset'] );
        $this->assertSame( 1, $p['page_count'] );
        $this->assertSame( 0, $p['from'] );
        $this->assertSame( 0, $p['to'] );
        $this->assertFalse( $p['next_offset'] );
        $this->assertFalse( $p['previous_offset'] );
    }

    public function testNegativeAndInvalidValuesAreSafe()
    {
        $p = GitManagerLog::paging( -5, -10, 7 );
        $this->assertSame( 0, $p['total'] );
        $this->assertSame( 0, $p['offset'] );
        $this->assertSame( GitManagerLog::DEFAULT_LIMIT, $p['limit'] );
    }

    // ---- page size ------------------------------------------------------------------------------------------------

    public function testLimitIsOneOfTheOfferedSizes()
    {
        foreach ( array( 10, 25, 50, 100 ) as $size )
        {
            $this->assertSame( $size, GitManagerLog::normalizeLimit( $size ) );
            $this->assertSame( $size, GitManagerLog::normalizeLimit( (string)$size ) );
        }
        foreach ( array( 0, 7, 1000, '25abc', '1e2', -25, null, '', array() ) as $bad )
        {
            $this->assertSame( 50, GitManagerLog::normalizeLimit( $bad ), var_export( $bad, true ) );
        }
        $this->assertSame( 10, GitManagerLog::normalizeLimit( 'x', '10' ) );
    }

    // ---- view parameters ------------------------------------------------------------------------------------------

    public function testViewParametersAreRead()
    {
        $s = GitManagerLog::parseViewParameters( array( 'offset' => '50', 'limit' => '25', 'ref' => 'origin~2Fmain',
            'author' => 'Jane~20Doe', 'grep' => 'fix~2Fpath~20100~25', 'since' => '2026-01-01', 'until' => '2026-02-30' ) );
        $this->assertSame( 50, $s['offset'] );
        $this->assertSame( 25, $s['limit'] );
        $this->assertTrue( $s['limit_given'] );
        $this->assertSame( 'origin/main', $s['ref'] );
        $this->assertSame( 'Jane Doe', $s['author'] );
        $this->assertSame( 'fix/path 100%', $s['grep'] );
        $this->assertSame( '2026-01-01', $s['since'] );
        $this->assertSame( '', $s['until'], 'not a real date' );
    }

    public function testPreferenceIsTheDefaultPageSize()
    {
        $this->assertSame( 100, GitManagerLog::parseViewParameters( array(), '100' )['limit'] );
        $this->assertFalse( GitManagerLog::parseViewParameters( array(), '100' )['limit_given'] );
        $this->assertSame( 50, GitManagerLog::parseViewParameters( array(), false )['limit'] );
        $this->assertSame( 50, GitManagerLog::parseViewParameters( array(), '13' )['limit'] );
        $this->assertSame( 10, GitManagerLog::parseViewParameters( array( 'limit' => '10' ), '100' )['limit'] );
    }

    public function testInvalidViewParametersAreDropped()
    {
        $s = GitManagerLog::parseViewParameters( array( 'offset' => '-5', 'limit' => '33', 'ref' => '--output=~2Ftmp~2Fx',
            'since' => 'yesterday', 'author' => array( 'x' ) ) );
        $this->assertSame( 0, $s['offset'] );
        $this->assertFalse( $s['limit_given'] );
        $this->assertSame( '', $s['ref'] );
        $this->assertSame( '', $s['since'] );
        $this->assertSame( '', $s['author'] );
        $this->assertSame( 0, GitManagerLog::parseViewParameters( array( 'offset' => '1e5' ) )['offset'] );
        $this->assertSame( 0, GitManagerLog::parseViewParameters( array( 'offset' => '99999999999' ) )['offset'] );
        $this->assertSame( 0, GitManagerLog::parseViewParameters( 'not an array' )['offset'] );
    }

    public function testTextIsCleanedAndCut()
    {
        $this->assertSame( 'a b', GitManagerLog::cleanText( "  a\x00\nb\t " ) );
        $this->assertSame( 200, mb_strlen( GitManagerLog::cleanText( str_repeat( 'ä', 500 ) ), 'UTF-8' ) );
        $this->assertSame( '', GitManagerLog::cleanText( "\xff\xfe" ) );
    }

    public function testEncodedParametersRoundTrip()
    {
        foreach ( array( 'Jane Doe <jane@example.com>', 'fix/path 100%+more', 'origin/feature/x', "Ünïcödé ~tilde", '$(id);`id`|x' ) as $value )
        {
            $encoded = GitManagerLog::encodeParameter( $value );
            $this->assertMatchesRegularExpression( '/^[A-Za-z0-9._@~-]*$/', $encoded, 'no slash, percent, plus or space in a link' );
            $this->assertSame( $value, GitManagerLog::decodeParameter( $encoded ) );
        }
    }

    public function testLinksReproduceTheView()
    {
        $state = GitManagerLog::parseViewParameters( array( 'limit' => '25', 'author' => 'Jane~20Doe', 'ref' => 'main' ) );
        $path = GitManagerLog::viewParameterPath( $state, array( 'offset' => 75 ) );
        $this->assertSame( '/(offset)/75/(limit)/25/(ref)/main/(author)/Jane~20Doe', $path );

        // what the kernel makes of the path: /(key)/value pairs
        preg_match_all( '#/\(([a-z]+)\)/([^/]*)#', $path, $m );
        $again = GitManagerLog::parseViewParameters( array_combine( $m[1], $m[2] ) );
        $this->assertSame( 75, $again['offset'] );
        $this->assertSame( 25, $again['limit'] );
        $this->assertSame( 'Jane Doe', $again['author'] );
        $this->assertSame( 'main', $again['ref'] );

        $this->assertSame( '/(limit)/50', GitManagerLog::viewParameterPath( array( 'offset' => 0, 'limit' => 50 ) ) );
    }

    public function testNavigatorParametersLeaveOutEmptyFilters()
    {
        $state = GitManagerLog::parseViewParameters( array( 'offset' => '100', 'limit' => '50', 'grep' => 'a~2Fb' ) );
        $this->assertSame( array( 'offset' => 100, 'limit' => 50, 'grep' => 'a~2Fb' ), GitManagerLog::navigatorParameters( $state ) );
    }

    public function testFormBecomesTheFirstPage()
    {
        $s = GitManagerLog::stateFromForm( array( 'ref' => 'main', 'author' => ' jane ', 'grep' => 'login', 'start_date' => '2026-01-01', 'end_date' => 'bad' ), '25' );
        $this->assertSame( 0, $s['offset'] );
        $this->assertSame( 25, $s['limit'] );
        $this->assertSame( 'jane', $s['author'] );
        $this->assertSame( '', $s['until'] );
        $this->assertTrue( GitManagerLog::isFiltered( $s ) );
        $this->assertFalse( GitManagerLog::isFiltered( GitManagerLog::stateFromForm( null, 50 ) ) );
    }

    // ---- refs -----------------------------------------------------------------------------------------------------

    public function testRefsMustBeTheRepositorysOwn()
    {
        $refs = array( 'refs/heads/main', 'refs/heads/feature/x', 'refs/remotes/origin/main', 'refs/tags/v2.0.20' );
        $this->assertSame( 'HEAD', GitManagerLog::resolveRef( '', $refs ) );
        $this->assertSame( 'HEAD', GitManagerLog::resolveRef( 'HEAD', $refs ) );
        $this->assertSame( 'refs/heads/main', GitManagerLog::resolveRef( 'main', $refs ) );
        $this->assertSame( 'refs/heads/feature/x', GitManagerLog::resolveRef( 'feature/x', $refs ) );
        $this->assertSame( 'refs/remotes/origin/main', GitManagerLog::resolveRef( 'origin/main', $refs ) );
        $this->assertSame( 'refs/tags/v2.0.20', GitManagerLog::resolveRef( 'v2.0.20', $refs ) );
        foreach ( array( 'nope', '--all', '-p', 'main..evil', 'main@{1}', 'HEAD~1', 'main;id', 'refs/heads/main', 'main ' ) as $bad )
        {
            $this->assertNull( GitManagerLog::resolveRef( $bad, $refs ), $bad );
        }
    }

    // ---- git arguments --------------------------------------------------------------------------------------------

    public function testLogArgumentsForOnePage()
    {
        $args = GitManagerLog::logArguments( array(), 'HEAD', 50, 25 );
        $this->assertSame( array( 'log', '--no-color', '--skip=50', '--max-count=25', '--format=%H%x1f%an%x1f%cD%x1f%s',
            '--end-of-options', 'HEAD', '--' ), $args );
        $this->assertSame( array( 'rev-list', '--count', '--end-of-options', 'refs/heads/main', '--' ),
            GitManagerLog::countArguments( array(), 'refs/heads/main' ) );
    }

    public function testFiltersAreFixedTextIgnoringCase()
    {
        $args = GitManagerLog::filterArguments( array( 'author' => 'Jane', 'grep' => 'fix.*', 'since' => '2026-01-01', 'until' => '2026-01-31' ) );
        $this->assertSame( array( '--regexp-ignore-case', '--fixed-strings', '--author=Jane', '--grep=fix.*',
            '--since=2026-01-01 00:00:00', '--until=2026-01-31 23:59:59' ), $args );
        $this->assertSame( array(), GitManagerLog::filterArguments( array( 'since' => 'last week', 'author' => '' ) ) );
    }

    /**
     * Hostile values stay one argument each and inside their option: none becomes an option, a revision or a path
     * of its own, and the revision is always HEAD or a full ref name between --end-of-options and --.
     */
    public function testHostileValuesStayInsideTheirArgument()
    {
        $hostile = array( 'author' => "x' ; rm -rf / ; '", 'grep' => '--output=/tmp/pwned $(id) `id`', 'since' => '2026-01-01; id' );
        $args = GitManagerLog::logArguments( $hostile, '--upload-pack=touch /tmp/x', 0, 10 );
        $this->assertContains( "--author=x' ; rm -rf / ; '", $args );
        $this->assertContains( '--grep=--output=/tmp/pwned $(id) `id`', $args );
        foreach ( $args as $arg )
        {
            $this->assertStringStartsNotWith( '--output', $arg );
            $this->assertStringStartsNotWith( '--since', $arg, 'an invalid date is left out' );
            $this->assertStringStartsNotWith( '--upload-pack', $arg );
        }
        $end = array_search( '--end-of-options', $args, true );
        $this->assertSame( array( 'HEAD', '--' ), array_slice( $args, $end + 1 ), 'a ref that is not a full ref name falls back to HEAD' );
        $this->assertSame( 'HEAD', GitManagerLog::logArguments( array(), 'refs/heads/../../x', 0, 10 )[6] );
        $this->assertSame( '--skip=0', GitManagerLog::logArguments( array(), 'HEAD', '-5', 10 )[2] );
        $this->assertSame( '--max-count=50', GitManagerLog::logArguments( array(), 'HEAD', 0, '10; id' )[3] );
    }

    public function testLogOutputIsParsed()
    {
        $sep = GitManagerLog::FIELD_SEPARATOR;
        $out = str_repeat( 'a', 40 ) . $sep . 'Jane Doe' . $sep . 'Fri, 17 Oct 2026 12:00:00 +0000' . $sep . "Title, with | odd ,|. text\n"
            . "garbage line\n" . str_repeat( 'b', 40 ) . $sep . 'X' . $sep . 'D' . "\n";
        $commits = GitManagerLog::parseLog( $out );
        $this->assertCount( 1, $commits );
        $this->assertSame( 'Title, with | odd ,|. text', $commits[0]['title'] );
        $this->assertSame( 'Jane Doe', $commits[0]['author'] );
    }
}
