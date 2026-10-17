<?php
/**
 * Shared lookup of the node binary for the design tests that run admin4 JavaScript in node
 * (tests/tests/design/js/). Not a test itself: the suite collects *Test.php only, and each user
 * requires this file.
 *
 * Without node a test is skipped on a developer machine. With CI=1 (GitHub Actions sets it) a missing node
 * FAILS instead: a skip there is a check that silently stopped running (admin4 review 15.10.2026, 19 sites
 * called markTestSkipped on their own).
 *
 * @license GPL-2.0-or-later
 */
class AdminNodeBinary
{
    /** Path of the node binary, or '' when there is none. */
    public static function path()
    {
        return trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
    }

    /**
     * The node binary. Skips the calling test without node, unless CI is set: then it fails.
     *
     * @return string
     */
    public static function require()
    {
        $node = self::path();
        if ( $node === '' )
        {
            if ( self::inCi() )
            {
                \PHPUnit\Framework\Assert::fail( 'node is not installed, but CI=1: the design tests that run admin4 JavaScript must not be skipped there' );
            }
            throw new \PHPUnit\Framework\SkippedWithMessageException( 'node is not installed' );
        }
        return $node;
    }

    public static function inCi()
    {
        $ci = getenv( 'CI' );
        return $ci !== false && $ci !== '' && $ci !== '0' && strtolower( $ci ) !== 'false';
    }
}
