<?php
/**
 * Help maintained as content, the parts that finish the plan (doc/eng-us/features/6.0/help-content.md): the review
 * workflow, the style guide rules added to the validator, "Was this helpful?", the checks of an EPUB, the history in
 * git, the tables, the REST routes of context help and the public node templates. No database (the SQLite update runs
 * on a file of its own in var/tmp).
 *
 *  HWF-01  plain language: sentences and paragraphs for visitors and members, longer sentences for editors; topics
 *          made from the Markdown help are not checked
 *  HWF-02  one verb per platform: click and tap in one topic; "press Enter" is a key, not a control
 *  HWF-03  review date further away than 12 months (6 for privacy and account); version_to without a successor
 *  HWF-04  a released help_id renamed, reused, gone
 *  HWF-05  the workflow's steps from each state; the author does not release; a step the roles refuse is left out
 *  HWF-06  "Was this helpful?": answers, reasons, the summary, the least helpful topics, the day; the redirect stays
 *          on the site
 *  HWF-07  epubcheck and Ace: skipped when not set, pass and fail by exit code, Ace's report, required refuses
 *  HWF-08  the history in git: the author setting; init, commit, "No change."
 *  HWF-09  the tables in share/db_schema.dba, the kernel schemas and the update files; the SQLite update runs twice
 *  HWF-10  the module: the views state and feedback, the function feedback, the opt-in Anonymous policy; the operation
 *          refuses the author's release and records released help_ids
 *  HWF-11  the REST provider: routes, scope, never cached
 *  HWF-12  the templates: admin and admin4 alike, no inline CSS; the extension exphelpsite's rules name its templates
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentWorkflowTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1792281600; // 2026-10-18

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private function validator( array $options = array() )
    {
        return new expHelpContentValidator( $options + array( 'now' => self::NOW, 'areas' => expHelpContentValidator::DEFAULT_AREAS,
                                                              'public_audiences' => array( 'Visitors' ), 'used_help_ids' => array(),
                                                              'plain_audiences' => array( 'Visitors', 'Members' ),
                                                              'sentence_words_public' => 20, 'sentence_words_technical' => 25,
                                                              'paragraph_sentences_public' => 4, 'review_months' => 12,
                                                              'short_review_months' => 6, 'short_review_areas' => array( 'privacy', 'account' ) ) );
    }

    private static function topic( array $over = array() )
    {
        return array_replace( array(
            'object_id' => 10, 'class' => 'help_concept', 'help_id' => 'content.versions', 'title' => 'Drafts and versions',
            'shortdesc' => 'Every change to content is saved as a new version; readers only see the published one.',
            'audience' => array( 'Editors' ), 'platform' => array( 'Admin' ), 'version_from' => '6.0.0', 'version_to' => '',
            'review_date' => self::NOW + 86400 * 300, 'owner' => 14, 'related' => array(), 'figures' => array(),
            'fields' => array( 'body' => 'When you edit an object, Exponential creates a draft.' ), 'remote_id' => 'help-topic-content-versions',
            'section' => 'help_internal', 'state' => 'released', 'node_id' => 1, 'modified' => self::NOW, 'language' => 'eng-US',
        ), $over );
    }

    private static function codes( array $findings, $level = null )
    {
        $codes = array();
        foreach ( $findings as $f )
        {
            if ( $level === null || $f['level'] === $level )
                $codes[] = $f['code'];
        }
        return $codes;
    }

    /** HWF-01 */
    public function testPlainLanguage()
    {
        $long = 'When you open the folder in the administration you see the list of every object that it contains and the buttons above it.';
        $this->assertSame( 23, expHelpContentValidator::wordCount( $long ) );
        // 23 words: too long for members (20), fine for editors (25)
        $codes = self::codes( $this->validator()->validate( array( self::topic( array( 'audience' => array( 'Members' ), 'fields' => array( 'body' => $long ) ) ) ) ) );
        $this->assertContains( 'language.sentence-too-long', $codes );
        $codes = self::codes( $this->validator()->validate( array( self::topic( array( 'fields' => array( 'body' => $long ) ) ) ) ) );
        $this->assertNotContains( 'language.sentence-too-long', $codes );
        // a NOTICE: a hint for the review
        $findings = $this->validator()->validate( array( self::topic( array( 'audience' => array( 'Visitors' ), 'section' => 'help_public', 'fields' => array( 'body' => $long ) ) ) ) );
        $this->assertSame( array( 'language.sentence-too-long' ), self::codes( $findings, 'NOTICE' ) );
        // five sentences in one paragraph for visitors; a list item is a paragraph of its own, code and tables are not text
        $five = 'Open it. Read it. Close it. Save it. Leave it.';
        $codes = self::codes( $this->validator()->validate( array( self::topic( array( 'audience' => array( 'Visitors' ), 'section' => 'help_public', 'fields' => array( 'body' => $five ) ) ) ) ) );
        $this->assertContains( 'language.paragraph-too-long', $codes );
        $this->assertSame( array( 'Open it.', 'Read it.' ), expHelpContentValidator::proseParagraphs( "1. Open it.\n2. Read it." ) );
        $this->assertSame( array( 'See code here.' ), expHelpContentValidator::proseParagraphs( "| a | b |\n|---|---|\n\n```\nlong code line with many words in it that is not prose at all and never counted\n```\n\nSee `x --y` here." ) );
        // the Markdown help's topics are checked where they are written
        $codes = self::codes( $this->validator()->validate( array( self::topic( array( 'remote_id' => 'help-gen-topic-x', 'audience' => array( 'Members' ),
                                                                                         'fields' => array( 'body' => $long . ' ' . $five ) ) ) ) ) );
        $this->assertNotContains( 'language.sentence-too-long', $codes );
        $this->assertCount( 2, expHelpContentValidator::sentences( 'First one. Second one.' ) );
        $this->assertCount( 1, expHelpContentValidator::sentences( 'Use a folder, e.g. Media, for it.' ), '"e.g." ends no sentence' );
    }

    /** HWF-02 */
    public function testOneVerbPerPlatform()
    {
        $mixed = self::topic( array( 'class' => 'help_task', 'help_id' => 'content.publish-article',
                                     'fields' => array( 'steps' => "1. Click **Create here**.\n2. Tap **Send for publishing**.", 'context' => '', 'prereq' => '', 'result' => '', 'postreq' => '' ) ) );
        $this->assertContains( 'verb.mix', self::codes( $this->validator()->validate( array( $mixed ) ) ) );
        $keys = self::topic( array( 'class' => 'help_task', 'help_id' => 'content.publish-article',
                                    'fields' => array( 'steps' => "1. Click **Search**.\n2. Press Enter.", 'context' => '', 'prereq' => '', 'result' => '', 'postreq' => '' ) ) );
        $this->assertNotContains( 'verb.mix', self::codes( $this->validator()->validate( array( $keys ) ) ) );
        // a bold label is quoted from the interface and not counted
        $label = self::topic( array( 'fields' => array( 'body' => 'Click **Tap to edit** to change it.' ) ) );
        $this->assertNotContains( 'verb.mix', self::codes( $this->validator()->validate( array( $label ) ) ) );
    }

    /** HWF-03 */
    public function testReviewDateAndSuccessor()
    {
        $far = self::topic( array( 'review_date' => self::NOW + 86400 * 500 ) );
        $this->assertContains( 'review-date.too-far', self::codes( $this->validator()->validate( array( $far ) ) ) );
        $year = self::topic( array( 'review_date' => self::NOW + 86400 * 365 ) );
        $this->assertNotContains( 'review-date.too-far', self::codes( $this->validator()->validate( array( $year ) ) ) );
        // account and privacy: six months
        $account = self::topic( array( 'help_id' => 'account.log-in', 'review_date' => self::NOW + 86400 * 300 ) );
        $this->assertContains( 'review-date.too-far', self::codes( $this->validator()->validate( array( $account ) ) ) );
        $account = self::topic( array( 'help_id' => 'account.log-in', 'review_date' => self::NOW + 86400 * 180 ) );
        $this->assertNotContains( 'review-date.too-far', self::codes( $this->validator()->validate( array( $account ) ) ) );
        // the seeder's review date passes the rule
        $seeded = self::topic( array( 'help_id' => 'privacy.delete-data', 'review_date' => expHelpContentSeeder::reviewDate( 'privacy.delete-data', self::NOW ) ) );
        $this->assertNotContains( 'review-date.too-far', self::codes( $this->validator()->validate( array( $seeded ) ) ) );

        $old = self::topic( array( 'version_to' => '6.0.14' ) );
        $this->assertContains( 'version.successor', self::codes( $this->validator()->validate( array( $old ) ) ) );
        $old = self::topic( array( 'version_to' => '6.0.14', 'related' => array( 11 ) ) );
        $new = self::topic( array( 'object_id' => 11, 'help_id' => 'content.versions-new' ) );
        $this->assertNotContains( 'version.successor', self::codes( $this->validator()->validate( array( $old, $new ) ) ) );
    }

    /** HWF-04 */
    public function testReleasedHelpIds()
    {
        $topic = self::topic();
        $this->assertSame( array(), self::codes( $this->validator( array( 'released_ids' => array( 'content.versions' => 10 ) ) )->validate( array( $topic ) ), 'ERROR' ) );
        $renamed = self::codes( $this->validator( array( 'released_ids' => array( 'content.drafts' => 10 ) ) )->validate( array( $topic ) ) );
        $this->assertContains( 'help-id.renamed', $renamed );
        $reused = self::codes( $this->validator( array( 'released_ids' => array( 'content.versions' => 99 ) ) )->validate( array( $topic ) ) );
        $this->assertContains( 'help-id.reused', $reused );
        $this->assertContains( 'help-id.gone', $reused, 'object 99 has no topic any more' );
        $this->assertTrue( expHelpContentHelpIds::isRecorded( $topic ) );
        $this->assertFalse( expHelpContentHelpIds::isRecorded( array( 'help_id' => 'doc.help.manual.x', 'remote_id' => 'help-gen-topic-1' ) ) );
        $this->assertFalse( expHelpContentHelpIds::isRecorded( array( 'help_id' => 'Not valid', 'remote_id' => '' ) ) );
    }

    /** HWF-05 */
    public function testWorkflowSteps()
    {
        $all = array( 'draft', 'review', 'released' );
        $this->assertSame( array( array( 'state' => 'review', 'allowed' => true, 'reason' => '' ) ), expHelpContentWorkflow::steps( 'draft', $all, false ) );
        $this->assertSame( array( array( 'state' => 'released', 'allowed' => true, 'reason' => '' ),
                                  array( 'state' => 'draft', 'allowed' => true, 'reason' => '' ) ), expHelpContentWorkflow::steps( 'review', $all, false ) );
        $this->assertSame( array( array( 'state' => 'review', 'allowed' => true, 'reason' => '' ) ), expHelpContentWorkflow::steps( 'released', $all, false ) );
        // the author of the current version does not release it
        $steps = expHelpContentWorkflow::steps( 'review', $all, true );
        $this->assertSame( array( 'state' => 'released', 'allowed' => false, 'reason' => 'author' ), $steps[0] );
        $this->assertTrue( $steps[1]['allowed'], 'back to draft stays possible' );
        // the help editor: Draft to Review only (state/assign NewState review)
        $steps = expHelpContentWorkflow::steps( 'review', array( 'review' ), false );
        $this->assertSame( 'policy', $steps[0]['reason'] );
        $this->assertSame( array(), expHelpContentWorkflow::steps( 'unknown', $all, false ) );
        $this->assertTrue( expHelpContentWorkflow::isRefusedAuthor( 14, 14, false ) );
        $this->assertFalse( expHelpContentWorkflow::isRefusedAuthor( 14, 15, false ) );
        $this->assertFalse( expHelpContentWorkflow::isRefusedAuthor( 14, 14, true ), 'ReleaseByAuthor=enabled' );
        $this->assertFalse( expHelpContentWorkflow::isRefusedAuthor( 0, 0, false ) );
        $this->assertSame( 'disabled', (string)eZINI::instance( 'exphelp.ini' )->variable( 'ContentSettings', 'ReleaseByAuthor' ) );
    }

    /** HWF-06 */
    public function testFeedback()
    {
        $this->assertSame( array( 'helpful' => 1, 'reason' => '' ), expHelpContentFeedback::normalise( 'yes', 'wrong' ) );
        $this->assertSame( array( 'helpful' => 0, 'reason' => 'wrong' ), expHelpContentFeedback::normalise( 'no', 'wrong' ) );
        $this->assertSame( array( 'helpful' => 0, 'reason' => '' ), expHelpContentFeedback::normalise( 'no', '<script>' ) );
        $this->assertNull( expHelpContentFeedback::normalise( 'maybe', '' ) );
        $this->assertNull( expHelpContentFeedback::normalise( array( 'yes' ), '' ) );
        $this->assertSame( 1792281600, expHelpContentFeedback::day( 1792281600 + 3600 * 13 ) );
        $summary = expHelpContentFeedback::summarise( array(
            array( 'object_id' => 10, 'helpful' => 1, 'reason' => '', 'help_id' => 'content.versions', 'c' => 3 ),
            array( 'object_id' => 10, 'helpful' => 0, 'reason' => 'unclear', 'help_id' => 'content.versions', 'c' => 1 ),
            array( 'object_id' => 11, 'helpful' => 0, 'reason' => 'outdated', 'help_id' => 'content.not-visible', 'c' => 4 ),
            array( 'object_id' => 11, 'helpful' => 0, 'reason' => '', 'help_id' => 'content.not-visible', 'c' => 1 ),
            array( 'object_id' => 11, 'helpful' => 1, 'reason' => '', 'help_id' => 'content.not-visible', 'c' => 1 ),
        ) );
        $this->assertSame( array( 'yes' => 3, 'no' => 1, 'reasons' => array( 'unclear' => 1 ), 'help_id' => 'content.versions' ), $summary[10] );
        $this->assertSame( array( 'outdated' => 4, 'none' => 1 ), $summary[11]['reasons'] );
        $unhelpful = expHelpContentFeedback::unhelpful( $summary );
        $this->assertSame( array( 11 ), array_keys( $unhelpful ), 'more no than yes, at least 3 answers' );
        $this->assertEqualsWithDelta( 5 / 6, $unhelpful[11]['share'], 0.0001 );
        $this->assertSame( array( 'outdated', 'unclear', 'incomplete', 'wrong', 'other' ), expHelpContentFeedback::REASONS );

        $redirect = array( '\Exponential\View\Kernel\Exphelp\Feedback', 'redirectTarget' );
        $this->assertSame( '/Help/Log-in', call_user_func( $redirect, '/Help/Log-in', 'x' ) );
        foreach ( array( 'https://evil.example/', '//evil.example/a', '/\\evil', 'Help/Log-in', '/a?b=c', "/a\nb", '/a/(helpful)/thanks', array( '/a' ), '' ) as $bad )
            $this->assertSame( 'exphelp/topic/x', call_user_func( $redirect, $bad, 'exphelp/topic/x' ), json_encode( $bad ) );
    }

    /** HWF-07 */
    public function testBookChecks()
    {
        $dir = self::root() . '/var/tmp/hwf-' . getmypid();
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/book.epub';
        file_put_contents( $file, 'x' );

        $none = new expHelpContentBookCheck( array( 'epubcheck' => '', 'ace' => '', 'mode' => 'required' ) );
        $results = $none->check( $file );
        $this->assertSame( array( 'skipped', 'skipped' ), array_column( $results, 'status' ) );
        $this->assertFalse( $none->refuses( $results ), 'a check that is not set does not refuse a book' );

        if ( is_executable( '/bin/true' ) && is_executable( '/bin/false' ) )
        {
            $pass = new expHelpContentBookCheck( array( 'epubcheck' => '/bin/true', 'ace' => '', 'mode' => 'required' ) );
            $this->assertSame( 'pass', $pass->check( $file )[0]['status'] );
            $fail = new expHelpContentBookCheck( array( 'epubcheck' => '/bin/false', 'ace' => '', 'mode' => 'required' ) );
            $results = $fail->check( $file );
            $this->assertSame( 'fail', $results[0]['status'] );
            $this->assertTrue( $fail->refuses( $results ) );
            $report = new expHelpContentBookCheck( array( 'epubcheck' => '/bin/false', 'ace' => '', 'mode' => 'report' ) );
            $this->assertFalse( $report->refuses( $report->check( $file ) ), 'report mode stores the book and reports' );
            $off = new expHelpContentBookCheck( array( 'epubcheck' => '/bin/false', 'ace' => '/bin/false', 'mode' => 'disabled' ) );
            $this->assertSame( array( 'skipped', 'skipped' ), array_column( $off->check( $file ), 'status' ) );
        }
        // Ace: the failed leaf assertions of report.json
        $ace = array( 'assertions' => array(
            array( 'earl:result' => array( 'earl:outcome' => 'fail' ), 'assertions' => array(
                array( 'earl:result' => array( 'earl:outcome' => 'fail' ) ),
                array( 'earl:result' => array( 'earl:outcome' => 'pass' ) ),
                array( 'earl:result' => array( 'earl:outcome' => 'fail' ) ) ) ),
            array( 'earl:result' => array( 'earl:outcome' => 'pass' ), 'assertions' => array() ) ) );
        $this->assertSame( 2, expHelpContentBookCheck::aceFailures( $ace ) );
        $this->assertSame( 0, expHelpContentBookCheck::aceFailures( array( 'assertions' => array() ) ) );
        $this->assertSame( array( 'epubcheck PASS    no errors' ), expHelpContentBookCheck::lines( array( array( 'tool' => 'epubcheck', 'status' => 'pass', 'message' => 'no errors' ) ) ) );
        @unlink( $file );
        @rmdir( $dir );
    }

    /** HWF-08 */
    public function testDitaHistory()
    {
        $this->assertSame( array( 'Help Bot', 'help@example.invalid' ), expHelpContentDitaHistory::author( 'Help Bot <help@example.invalid>' ) );
        $this->assertNull( expHelpContentDitaHistory::author( 'not an author' ) );
        $this->assertNull( expHelpContentDitaHistory::author( '' ) );
        $git = trim( (string)shell_exec( 'command -v git 2>/dev/null' ) );
        if ( $git === '' )
            $this->markTestSkipped( 'no git' );
        $dir = self::root() . '/var/tmp/hwf-git-' . getmypid();
        mkdir( $dir . '/topics', 0777, true );
        file_put_contents( $dir . '/topics/a.dita', '<concept id="a"/>' );
        $history = new expHelpContentDitaHistory( $dir );
        $this->assertSame( 'not-a-repository', $history->commit( 'x', false )['status'] );
        $this->assertSame( 'would-commit', $history->commit( 'x', true, true )['status'] );
        $this->assertFalse( $history->isRepository(), 'a dry run changes nothing' );
        putenv( 'GIT_AUTHOR_NAME=Help' );
        putenv( 'GIT_AUTHOR_EMAIL=help@example.invalid' );
        putenv( 'GIT_COMMITTER_NAME=Help' );
        putenv( 'GIT_COMMITTER_EMAIL=help@example.invalid' );
        $first = $history->commit( 'first', true );
        $this->assertSame( 'committed', $first['status'], $first['message'] );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{7,}$/', $first['commit'] );
        $this->assertSame( 'no-change', $history->commit( 'second', true )['status'] );
        file_put_contents( $dir . '/topics/a.dita', '<concept id="a">changed</concept>' );
        $this->assertSame( 'committed', $history->commit( 'third', true )['status'] );
        putenv( 'GIT_AUTHOR_NAME' );
        putenv( 'GIT_AUTHOR_EMAIL' );
        putenv( 'GIT_COMMITTER_NAME' );
        putenv( 'GIT_COMMITTER_EMAIL' );
        eZDir::recursiveDelete( $dir );
        $this->assertDirectoryDoesNotExist( $dir );
    }

    /** HWF-09 */
    public function testTables()
    {
        $schema = null;
        include self::root() . '/share/db_schema.dba';
        $this->assertSame( array( 'help_id', 'object_id', 'released' ), array_keys( $schema['exphelp_help_id']['fields'] ) );
        $this->assertSame( array( 'help_id' ), $schema['exphelp_help_id']['indexes']['PRIMARY']['fields'] );
        $this->assertSame( array( 'created', 'help_id', 'helpful', 'id', 'language', 'object_id', 'reason' ), array_keys( $schema['exphelp_feedback']['fields'] ) );
        $this->assertSame( 'auto_increment', $schema['exphelp_feedback']['fields']['id']['type'] );
        // nothing about who answered
        foreach ( array( 'user_id', 'ip', 'session', 'address', 'comment', 'text' ) as $personal )
            $this->assertArrayNotHasKey( $personal, $schema['exphelp_feedback']['fields'] );
        foreach ( array( 'kernel/sql/mysql/kernel_schema.sql', 'kernel/sql/postgresql/kernel_schema.sql', 'kernel/sql/sqlite/schema.sql',
                         'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql', 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql',
                         'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) as $file )
        {
            $sql = (string)file_get_contents( self::root() . '/' . $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE (IF NOT EXISTS )?`?exphelp_help_id`?/', $sql, $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE (IF NOT EXISTS )?`?exphelp_feedback`?/', $sql, $file );
            if ( strpos( $file, 'update/' ) === 0 )
                $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS exphelp_feedback', $sql, $file );
        }
        $this->assertStringContainsString( "setval('exphelp_feedback_id_seq'", (string)file_get_contents( self::root() . '/kernel/sql/postgresql/setval.sql' ) );
        $this->assertSame( array( 'exphelp_help_id', 'exphelp_feedback' ), expHelpContentTables::tables() );

        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/help-tables-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = (string)file_get_contents( self::root() . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $section = substr( $sql, strpos( $sql, '-- Help maintained as content (doc/eng-us/features/6.0/help-content.md): exphelp_help_id' ) );
            foreach ( preg_split( '/;\s*$/m', preg_replace( '/^--.*$/m', '', $section ) ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $pdo->exec( "INSERT INTO exphelp_help_id ( help_id, object_id, released ) VALUES ( 'content.versions', 10, 1 )" );
        $pdo->exec( "INSERT INTO exphelp_feedback ( object_id, help_id, helpful, reason, created ) VALUES ( 10, 'content.versions', 0, 'unclear', 1 )" );
        $apply();
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM exphelp_feedback' )->fetchColumn() );
        $refused = false;
        try { $pdo->exec( "INSERT INTO exphelp_help_id ( help_id, object_id ) VALUES ( 'content.versions', 11 )" ); }
        catch ( PDOException $e ) { $refused = true; }
        $this->assertTrue( $refused, 'a help_id is recorded once' );
        $pdo = null;
        @unlink( $file );
    }

    /** HWF-10 */
    public function testModuleAndOperation()
    {
        $read = static function ( $file ) {
            $Module = $ViewList = $FunctionList = null;
            include $file;
            return array( 'views' => $ViewList, 'functions' => $FunctionList );
        };
        $m = $read( self::root() . '/kernel/exphelp/module.php' );
        $this->assertSame( array( 'read' ), $m['views']['state']['functions'] );
        $this->assertSame( 'read or feedback', $m['views']['feedback']['functions'] );
        $this->assertArrayHasKey( 'feedback', $m['functions'] );
        $this->assertSame( array( 'step' => 'Step', 'helpful' => 'Helpful' ), $m['views']['topic']['unordered_params'] );
        foreach ( array( 'state', 'feedback' ) as $view )
            $this->assertFileExists( self::root() . '/kernel/exphelp/' . $view . '.php' );
        // help editors open the JSON of a topic and make API keys with the scope help
        $this->assertContains( array( 'exphelp', 'context', array() ), expHelpContentSchema::roles()[expHelpContentSchema::ROLE_EDITOR] );
        if ( expHelpContentSchema::setting( 'AnonymousFeedback', 'disabled' ) !== 'enabled' )
        {
            $roles = expHelpContentSchema::roles();
            foreach ( $roles['Anonymous'] as $policy )
                $this->assertNotSame( array( 'exphelp', 'feedback' ), array( $policy[0], $policy[1] ) );
        }
        $operation = (string)file_get_contents( self::root() . '/kernel/content/ezcontentoperationcollection.php' );
        $refuse = strpos( $operation, 'expHelpContentWorkflow::refusedStates(' );
        $assign = strpos( $operation, '$object->assignState( $state );', (int)$refuse );
        $this->assertNotFalse( $refuse );
        $this->assertNotFalse( $assign, 'the author is refused before the states are assigned' );
        $this->assertStringContainsString( 'expHelpContentWorkflow::afterStateAssign(', $operation );
        $this->assertStringContainsString( 'expHelpContentWorkflow::recordHelpId(', (string)file_get_contents( self::root() . '/kernel/classes/help/content/exphelpcontentseeder.php' ) );
    }

    /** HWF-11 */
    public function testRestProvider()
    {
        $ini = eZINI::instance( 'rest.ini' );
        $providers = $ini->variable( 'ApiProvider', 'ProviderClass' );
        $this->assertSame( 'expHelpRestProvider', $providers['help'] );
        $this->assertSame( 'exphelp/context', $ini->variable( 'ApiKeySettings', 'Scopes' )['help'] );
        $scopes = $ini->variable( 'ApiKeySettings', 'RouteScopes' );
        $this->assertSame( 'help', $scopes['expHelpRestController_topic'] );
        $this->assertSame( 'help', $scopes['expHelpRestController_manual'] );
        $this->assertSame( 'disabled', $ini->variable( 'expHelpRestController_CacheSettings', 'ApplicationCache' ) );
        $routes = ( new expHelpRestProvider() )->getRoutes();
        $this->assertSame( array( 'expHelpTopic', 'expHelpManual' ), array_keys( $routes ) );
        $this->assertInstanceOf( 'ezpRestViewControllerInterface', ( new expHelpRestProvider() )->getViewController() );
        $this->assertTrue( method_exists( 'expHelpRestController', 'doTopic' ) && method_exists( 'expHelpRestController', 'doManual' ) );
    }

    /** HWF-12 */
    public function testTemplates()
    {
        $root = self::root();
        foreach ( array( 'exphelp/topic.tpl', 'exphelp/parts/topic_workflow.tpl', 'exphelp/parts/topic_feedback.tpl' ) as $template )
        {
            $admin4 = (string)file_get_contents( "$root/design/admin4/templates/$template" );
            $this->assertSame( $admin4, (string)file_get_contents( "$root/design/admin/templates/$template" ), "$template is the same in admin and admin4" );
            $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle="/i', $admin4, $template );
        }
        $this->assertStringContainsString( "design:exphelp/parts/topic_workflow.tpl", (string)file_get_contents( "$root/design/admin4/templates/exphelp/topic.tpl" ) );
        $this->assertFileExists( "$root/design/standard/templates/exphelp/parts/feedback_reason.tpl" );

        $ext = "$root/extension/exphelpsite";
        $ini = (string)file_get_contents( "$ext/settings/override.ini.append.php" );
        preg_match_all( '/^MatchFile=(\S+)$/m', $ini, $m );
        $this->assertNotEmpty( $m[1] );
        foreach ( array_unique( $m[1] ) as $file )
            $this->assertFileExists( "$ext/design/standard/override/templates/$file" );
        foreach ( array_merge( expHelpContentSchema::topicClasses(), array( 'help_manual', 'help_section' ) ) as $class )
        {
            $this->assertStringContainsString( "[full_$class]", $ini );
            $this->assertStringContainsString( "[line_$class]", $ini );
        }
        $this->assertStringContainsString( 'DesignExtensions[]=exphelpsite', (string)file_get_contents( "$ext/settings/design.ini.append.php" ) );
        $xml = simplexml_load_file( "$ext/extension.xml" );
        $this->assertSame( '6.0.15', (string)$xml->metadata->version );
        foreach ( glob( "$ext/design/standard/override/templates/help/*.tpl" ) as $file )
        {
            $text = (string)file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle="/i', $text, $file );
            if ( strpos( $text, '<form' ) !== false )
                $this->assertStringContainsString( 'exphelp/feedback/', $text );
        }
    }
}

?>
