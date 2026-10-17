<?php
/**
 * The GitHub workflows (.github/workflows) give a pull request, also one from a fork,
 * the same run as a push to main.
 *
 * Read from the files, no database:
 * - PHPUnit has no paths filter: the tests read templates, stylesheets, scripts,
 *   translations, INI files and documentation, so a change to those alone has to run them;
 * - every workflow resolves the root package as dev-main (a pull request is checked out as
 *   a detached merge commit, where Composer finds no branch and every install failed);
 * - no workflow runs on pull_request_target or asks for write permissions, so the code of
 *   a fork never runs with a token that can change the repository.
 *
 * Run: php vendor/bin/phpunit tests/tests/bin/CiWorkflowPullRequestTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class CiWorkflowPullRequestTest extends PHPUnit\Framework\TestCase
{
    const WORKFLOWS = array( 'phpunit.yml', 'quality.yml', 'performance.yml' );

    /**
     * Returns the text of a workflow file
     *
     * @param string $name
     * @return string
     */
    private static function workflow( $name )
    {
        $path = dirname( __DIR__, 3 ) . '/.github/workflows/' . $name;
        if ( !is_file( $path ) )
        {
            self::fail( "missing workflow $path" );
        }
        return file_get_contents( $path );
    }

    /**
     * Returns the "on:" block of a workflow, up to the next top-level key
     *
     * @param string $text
     * @return string
     */
    private static function triggers( $text )
    {
        if ( !preg_match( '/^on:\s*\n((?:[ \t]+.*\n|\s*\n)+)/m', $text, $match ) )
        {
            self::fail( 'the workflow has no on: block' );
        }
        return $match[1];
    }

    public function testPhpunitRunsForEveryPushAndPullRequest()
    {
        $triggers = self::triggers( self::workflow( 'phpunit.yml' ) );
        $this->assertMatchesRegularExpression( '/^  push:/m', $triggers );
        $this->assertMatchesRegularExpression( '/^  pull_request:/m', $triggers );
        $this->assertDoesNotMatchRegularExpression( '/^\s+paths(-ignore)?:/m', $triggers,
            'a paths filter skips the tests for changes to templates, translations, INI files and documentation' );
    }

    public function testEveryWorkflowResolvesTheRootPackageAsMain()
    {
        foreach ( self::WORKFLOWS as $name )
        {
            $this->assertMatchesRegularExpression( '/^\s+COMPOSER_ROOT_VERSION:\s*dev-main\s*$/m', self::workflow( $name ),
                "$name: a pull request's merge commit needs COMPOSER_ROOT_VERSION=dev-main for its installs" );
        }
    }

    public function testNoWorkflowGivesForkCodeAWriteToken()
    {
        foreach ( self::WORKFLOWS as $name )
        {
            $text = self::workflow( $name );
            $this->assertStringNotContainsString( 'pull_request_target', $text, "$name runs fork code with the base repository's token" );
            $this->assertMatchesRegularExpression( '/^permissions:\s*\n\s+contents:\s*read\s*$/m', $text, "$name: permissions contents: read" );
            $this->assertDoesNotMatchRegularExpression( '/:\s*write\s*$/m', $text, "$name asks for a write permission" );
        }
    }

    /**
     * Returns the text of one step of a workflow, from its "- name:" line to the next step
     *
     * @param string $text
     * @param string $name
     * @return string
     */
    private static function step( $text, $name )
    {
        if ( !preg_match( '/^(\s+)- name: ' . preg_quote( $name, '/' ) . '\s*\n(.*?)(?=^\1- name:|\z)/ms', $text, $match ) )
        {
            self::fail( "no step '$name'" );
        }
        return $match[2];
    }

    /**
     * The manifest check is a warning on a pull request (the person merging refreshes the
     * manifest on main) and stays blocking on a push to main.
     */
    public function testManifestWarnsOnPullRequestsAndBlocksOnPush()
    {
        $step = self::step( self::workflow( 'quality.yml' ), 'File manifest (share/filelist.md5)' );
        $this->assertStringNotContainsString( 'continue-on-error', $step, 'a push to main must stay blocking' );
        $this->assertMatchesRegularExpression(
            "/MANIFEST_BLOCKING:\s*\\$\{\{\s*github\.event_name == 'pull_request' && '0' \|\| '1'\s*\}\}/", $step,
            'the warning mode is chosen by the event: pull_request only' );
        // a mismatch ends in exit 1 unless the step is in warning mode
        $this->assertMatchesRegularExpression(
            '/if \[ "\$MANIFEST_BLOCKING" = 0 \]; then\s*\n\s*echo "::warning::[^\n]*\n\s*exit 0\s*\n\s*fi\s*\n\s*exit 1\s*\n/', $step,
            'a mismatch on push must exit 1' );
        $this->assertStringContainsString( 'php bin/php/checkmanifest.php --all', $step );
    }
}
