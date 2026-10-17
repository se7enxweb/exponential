<?php
/**
 * File access for the design tests that assert that something is ABSENT. A missing file read as '' or an empty
 * glob() result is a pass that checked nothing (a renamed file or directory, a wrong pattern); these helpers fail
 * instead (admin4 review 15.10.2026: the suite had neither failOnRisky nor a guard).
 * Not a test itself: the suite collects *Test.php only, and each user requires this file.
 *
 * @license GPL-2.0-or-later
 */
class AdminFiles
{
    /**
     * @param string $pattern glob pattern
     * @param int    $flags   glob flags (GLOB_BRACE ...)
     * @return string[] never empty
     */
    public static function files( $pattern, $flags = 0 )
    {
        $files = glob( $pattern, $flags ) ?: array();
        \PHPUnit\Framework\Assert::assertNotEmpty( $files, "no file matches $pattern: a loop over it would check nothing" );
        return $files;
    }

    /**
     * Contents of a file that must exist.
     *
     * @param string $path
     * @return string
     */
    public static function read( $path )
    {
        \PHPUnit\Framework\Assert::assertFileExists( $path );
        return (string)file_get_contents( $path );
    }
}
