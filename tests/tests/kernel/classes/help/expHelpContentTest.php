<?php
/**
 * Help maintained as content (doc/en/us/features/6.0/help-content.md), without a database: the content model, the rules of
 * the style guide the validator checks, the help_id, the Markdown of a topic and its mapping into the help.
 *
 *  HCT-01  the model: eight classes, the plan's fields, the topic types' required fields, the roles' limitations
 *  HCT-02  help_id: valid and invalid forms, parts
 *  HCT-03  the style guide's own templates pass the validator without ERROR
 *  HCT-04  a task without exactly one numbered list, sub-steps, more than 9 steps
 *  HCT-05  shortdesc: required, one sentence, 250 characters, markup
 *  HCT-06  duplicate and invalid help_id; unknown area
 *  HCT-07  figures: alt text missing / too long (ERROR for a generated figure), "Screenshot of"
 *  HCT-08  section from audience: public / internal / none; figures public through a public topic; glossary internal
 *  HCT-09  glossary: an avoided word is reported, a bold UI label is not
 *  HCT-10  maps: unknown targets, cycles, orphan chapters; chapters of a public manual are public
 *  HCT-11  review date in the past; version format
 *  HCT-12  the topic's Markdown (headings, help: links) and its document in the help (audiences, ids)
 *  HCT-13  the context registry takes "content/<help_id>" only from a page provider, never from a plain one
 *  HCT-14  the Anonymous policy is ONE policy with both limitations; context JSON for Anonymous is opt-in
 *  HCT-15  the JSON endpoint's ETag and If-None-Match
 *  HCT-16  policy signatures are order-independent (idempotent role step)
 *  HCT-17  the shipped seed files are valid and pass the validator
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1792281600; // 2026-10-18

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private function validator()
    {
        return new expHelpContentValidator( array( 'now' => self::NOW, 'areas' => expHelpContentValidator::DEFAULT_AREAS,
                                                   'public_audiences' => array( 'Visitors' ) ) );
    }

    private static function topic( array $over = array() )
    {
        return array_replace( array(
            'object_id' => 10, 'class' => 'help_concept', 'help_id' => 'content.versions', 'title' => 'Drafts and versions',
            'shortdesc' => 'Every change to content is saved as a new version; readers only see the published one.',
            'audience' => array( 'Editors' ), 'platform' => array( 'Admin' ), 'version_from' => '6.0.0', 'version_to' => '',
            'review_date' => self::NOW + 86400 * 300, 'owner' => 14, 'related' => array(), 'figures' => array(),
            'fields' => array( 'body' => 'When you edit an object, Exponential creates a draft.' ),
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

    /** HCT-01 */
    public function testModel()
    {
        $classes = expHelpContentSchema::classes();
        $this->assertSame( array( 'help_manual', 'help_section', 'help_concept', 'help_task', 'help_reference', 'help_troubleshooting',
                                  'help_screenshot', 'help_glossary_entry' ), array_keys( $classes ) );
        foreach ( expHelpContentSchema::topicClasses() as $class )
        {
            foreach ( array( 'title', 'shortdesc', 'help_id', 'audience', 'platform', 'version_from', 'version_to', 'review_date', 'owner', 'related', 'figures' ) as $field )
                $this->assertArrayHasKey( $field, $classes[$class]['attributes'], "$class.$field" );
            foreach ( expHelpContentSchema::requiredFields( $class ) as $field )
                $this->assertTrue( $classes[$class]['attributes'][$field]['required'], "$class.$field required" );
            foreach ( array_keys( expHelpContentSchema::textFields( $class ) ) as $field )
                $this->assertSame( 'eztext', $classes[$class]['attributes'][$field]['type'] );
        }
        $this->assertSame( array( 'context', 'prereq', 'steps', 'result', 'postreq' ), array_keys( expHelpContentSchema::textFields( 'help_task' ) ) );
        $this->assertSame( array( 'condition', 'cause', 'remedy' ), array_keys( expHelpContentSchema::textFields( 'help_troubleshooting' ) ) );
        $this->assertSame( 250, $classes['help_concept']['attributes']['shortdesc']['params']['max_length'] );
        $this->assertTrue( $classes['help_screenshot']['attributes']['alt_text']['required'] );
        $this->assertArrayHasKey( 'avoid', $classes['help_glossary_entry']['attributes'] );
        $this->assertTrue( $classes['help_manual']['container'] );
        $this->assertSame( array( 'help_section', 'help_concept', 'help_task', 'help_reference', 'help_troubleshooting' ),
                           $classes['help_manual']['attributes']['topicrefs']['params']['classes'] );
        $this->assertSame( array( 'draft', 'review', 'released' ), array_keys( expHelpContentSchema::states() ) );
        $tree = expHelpContentSchema::tree();
        foreach ( array( 'help-root', 'help-pool', 'help-manuals', 'help-ebooks', 'help-pool-glossary', 'help-pool-figures' ) as $remoteId )
            $this->assertArrayHasKey( $remoteId, $tree );
        $roles = expHelpContentSchema::roles();
        $editor = array();
        foreach ( $roles['Help editor'] as $policy )
            $editor[$policy[0] . '/' . $policy[1]] = $policy[2];
        $this->assertSame( array( 'draft', 'review' ), $editor['content/edit']['StateGroup_help_status'] );
        $this->assertSame( array( 'draft', 'review' ), $editor['content/remove']['StateGroup_help_status'] );
        $this->assertSame( array( 'review' ), $editor['state/assign']['NewState'] );
        $this->assertSame( array( 'draft' ), $editor['state/assign']['StateGroup_help_status'] );
        $this->assertSame( array( 'help-root' ), $editor['content/create']['Subtree'] );
    }

    /** HCT-02 */
    public function testHelpId()
    {
        foreach ( array( 'account.log-in', 'content.publish-article', 'account.log-in.sso', 'media.upload.mobile', 'a.b', 'setup.cache2' ) as $id )
            $this->assertTrue( expHelpContentValidator::isValidHelpId( $id ), $id );
        foreach ( array( '', 'account', 'Account.log-in', 'account..log-in', 'account.-log-in', 'account.log-', 'account.log_in', 'account.log in',
                         '.account.x', 'account.x.', 'konto.anmelden-ä', str_repeat( 'a', 40 ) . '.' . str_repeat( 'b', 40 ), 'a/b.c', 123 ) as $id )
            $this->assertFalse( expHelpContentValidator::isValidHelpId( $id ), var_export( $id, true ) );
        $this->assertSame( array( 'area' => 'account', 'topic' => 'log-in', 'variant' => 'sso' ), expHelpContentValidator::parseHelpId( 'account.log-in.sso' ) );
        $this->assertSame( '', expHelpContentValidator::parseHelpId( 'account.log-in' )['variant'] );
        $this->assertNull( expHelpContentValidator::parseHelpId( 'nope' ) );
    }

    /** HCT-03 */
    public function testStyleGuideTemplatesPass()
    {
        $topics = array(
            self::topic(),
            self::topic( array( 'object_id' => 11, 'class' => 'help_task', 'help_id' => 'content.publish-article', 'title' => 'Publish an article',
                                'shortdesc' => 'Create an article in a folder and publish it on the site.', 'related' => array( 10, 12 ),
                                'fields' => array( 'context' => 'An article appears on the site as soon as you publish it.', 'prereq' => 'You are logged in.',
                                                   'steps' => "1. Open the folder in which the article belongs.\n2. Select **Article** in the list of content classes.\n3. Click **Create here**.\n   The edit form opens.\n4. Fill in at least the title and the text.\n5. Click **Send for publishing**.\n   The folder opens again and shows the new article.",
                                                   'result' => 'The article is published.', 'postreq' => '' ) ) ),
            self::topic( array( 'object_id' => 12, 'class' => 'help_troubleshooting', 'help_id' => 'content.not-visible', 'title' => 'A published article does not appear on the site',
                                'shortdesc' => 'If an article is published but missing on the site, its section, state or the cache is usually the reason.',
                                'audience' => array( 'Editors', 'Administrators' ),
                                'fields' => array( 'condition' => 'The article is listed as published. Its address shows "Access denied".', 'cause' => 'Anonymous readers may not read it.', 'remedy' => 'Look at **Section** and **Object states**.' ) ) ),
            self::topic( array( 'object_id' => 13, 'class' => 'help_task', 'help_id' => 'account.log-in', 'title' => 'Log in', 'shortdesc' => 'Log in with your user name and your password.',
                                'audience' => array( 'Members', 'Visitors' ), 'section' => 'help_public', 'platform' => array( 'Web' ),
                                'fields' => array( 'prereq' => 'You have an account.', 'steps' => "1. Open the login page.\n2. Enter your user name in **Username**.\n3. Click **Login**.", 'result' => 'You are logged in.' ) ) ),
        );
        $findings = $this->validator()->validate( $topics );
        $this->assertSame( array(), self::codes( $findings, 'ERROR' ), json_encode( $findings ) );
        $this->assertSame( array(), self::codes( $findings, 'WARNING' ), json_encode( $findings ) );
    }

    /** HCT-04 */
    public function testTaskSteps()
    {
        $task = function ( $steps ) {
            return self::topic( array( 'class' => 'help_task', 'fields' => array( 'steps' => $steps ) ) );
        };
        $this->assertContains( 'task.steps-list', self::codes( $this->validator()->validate( array( $task( 'Open the folder and click **Create here**.' ) ) ) ) );
        $this->assertContains( 'task.steps-list', self::codes( $this->validator()->validate( array( $task( "1. One.\n2. Two.\n\nThen:\n\n1. Three." ) ) ) ) );
        $this->assertNotContains( 'task.steps-list', self::codes( $this->validator()->validate( array( $task( "1. One.\n   It opens.\n\n2. Two." ) ) ) ) );
        $this->assertContains( 'task.sub-steps', self::codes( $this->validator()->validate( array( $task( "1. One.\n   1. Sub.\n2. Two." ) ) ) ) );
        $ten = implode( "\n", array_map( function ( $i ) { return "$i. Step $i."; }, range( 1, 10 ) ) );
        $this->assertContains( 'task.too-many-steps', self::codes( $this->validator()->validate( array( $task( $ten ) ) ) ) );
        $missing = self::topic( array( 'class' => 'help_task', 'fields' => array( 'steps' => '' ) ) );
        $this->assertContains( 'field.required', self::codes( $this->validator()->validate( array( $missing ) ), 'ERROR' ) );
        $context = self::topic( array( 'class' => 'help_task', 'fields' => array( 'steps' => "1. A.", 'context' => "1. Do this.\n2. Do that." ) ) );
        $this->assertContains( 'task.steps-elsewhere', self::codes( $this->validator()->validate( array( $context ) ) ) );
        $this->assertSame( 2, count( expHelpContentValidator::numberedLists( "1. a\n2. b\n\ntext\n\n1. c\n```\n1. code\n```" )['lists'] ) );
    }

    /** HCT-04b: the notice levels of the style guide (section 5) */
    public function testNotices()
    {
        $task = function ( array $fields ) {
            return self::codes( $this->validator()->validate( array( self::topic( array( 'class' => 'help_task', 'fields' => $fields + array( 'steps' => '1. A.' ) ) ) ) ) );
        };
        // the style guide's own example passes
        $ok = $task( array( 'steps' => "1. Select the objects.\n2. Click **Remove**.\n3. **Attention:** Without \"Move to trash\" the objects are deleted at once and\n   cannot be restored.\n   Click **OK**." ) );
        foreach ( array( 'notice.position', 'notice.per-step', 'notice.before-action', 'notice.too-long' ) as $code )
            $this->assertNotContains( $code, $ok );
        $this->assertContains( 'notice.position', $task( array( 'context' => '**Attention:** Removed objects cannot be restored.' ) ) );
        $this->assertContains( 'notice.position', $task( array( 'result' => '**Data protection**: The page is now public.' ) ) );
        $this->assertNotContains( 'notice.position', $task( array( 'context' => '**Note:** A draft is kept until you discard it.' ) ) );
        $this->assertContains( 'notice.per-step', $task( array( 'steps' => "1. **Attention:** It is deleted. **Note:** Drafts stay.\n   Click **OK**." ) ) );
        $this->assertContains( 'notice.before-action', $task( array( 'steps' => "1. Click **OK**.\n   **Attention:** The object is deleted at once." ) ) );
        $this->assertNotContains( 'notice.before-action', $task( array( 'steps' => "1. Click **OK**.\n   **Note:** The trash keeps it." ) ) );
        $long = self::codes( $this->validator()->validate( array( self::topic( array( 'fields' => array(
            'body' => "> **Attention:** One. Two. Three. Four.\n\nNext paragraph. More. More. More." ) ) ) ) ) );
        $this->assertContains( 'notice.too-long', $long );
        $this->assertNotContains( 'notice.too-long', self::codes( $this->validator()->validate( array( self::topic( array( 'fields' => array(
            'body' => "**Data protection:** Visitors see the name. Remove it first.\n\nOne. Two. Three. Four." ) ) ) ) ) ) );
        $this->assertSame( array(), expHelpContentValidator::notices( "Text `**Attention:**` and\n```\n**Note:** x\n```" ) );
        $this->assertSame( array( "Open.\nIt opens.", 'Close.' ), expHelpContentValidator::stepItems( "1. Open.\n   It opens.\n2. Close.\n\nAfter." ) );
    }

    /** HCT-05 */
    public function testShortdesc()
    {
        $v = $this->validator();
        $this->assertContains( 'field.required', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => '' ) ) ) ), 'ERROR' ) );
        $this->assertContains( 'shortdesc.sentences', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => 'One sentence. Another one.' ) ) ) ), 'ERROR' ) );
        $this->assertNotContains( 'shortdesc.sentences', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => 'Fields, e.g. Title and Body, are checked.' ) ) ) ) ) );
        $this->assertContains( 'shortdesc.too-long', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => str_repeat( 'word ', 60 ) . 'end.' ) ) ) ), 'ERROR' ) );
        $this->assertContains( 'shortdesc.markup', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => 'Click **Save** to keep it.' ) ) ) ) ) );
        $this->assertContains( 'shortdesc.this-topic', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => 'This topic explains versions.' ) ) ) ) ) );
        $this->assertContains( 'shortdesc.full-stop', self::codes( $v->validate( array( self::topic( array( 'shortdesc' => 'No full stop' ) ) ) ) ) );
        $this->assertSame( 1, expHelpContentValidator::sentenceCount( 'Use e.g. the trash.' ) );
        $this->assertSame( 2, expHelpContentValidator::sentenceCount( 'One. Two.' ) );
    }

    /** HCT-06 */
    public function testHelpIdRules()
    {
        $findings = $this->validator()->validate( array( self::topic(), self::topic( array( 'object_id' => 11 ) ) ) );
        $this->assertContains( 'help-id.duplicate', self::codes( $findings, 'ERROR' ) );
        $this->assertContains( 'help-id.invalid', self::codes( $this->validator()->validate( array( self::topic( array( 'help_id' => 'Content.Versions' ) ) ) ), 'ERROR' ) );
        $this->assertContains( 'help-id.area', self::codes( $this->validator()->validate( array( self::topic( array( 'help_id' => 'shop.basket' ) ) ) ), 'WARNING' ) );
        $this->assertContains( 'help-id.type', self::codes( $this->validator()->validate( array( self::topic( array( 'help_id' => 'account.task-log-in' ) ) ) ) ) );
    }

    /** HCT-07 */
    public function testFigures()
    {
        $v = $this->validator();
        $fig = function ( array $over ) {
            return array_replace( array( 'object_id' => 50, 'alt_text' => 'Edit form with the fields Title and Body.', 'caption' => 'Edit form',
                                         'figure_id' => '', 'has_image' => true, 'section' => 'help_internal' ), $over );
        };
        $this->assertContains( 'figure.alt-missing', self::codes( $v->validate( array(), array( $fig( array( 'alt_text' => '' ) ) ) ), 'ERROR' ) );
        $long = str_repeat( 'x', 151 );
        $this->assertContains( 'figure.alt-too-long', self::codes( $v->validate( array(), array( $fig( array( 'alt_text' => $long ) ) ) ), 'WARNING' ) );
        $this->assertContains( 'figure.alt-too-long', self::codes( $v->validate( array(), array( $fig( array( 'alt_text' => $long, 'figure_id' => 'content.versions/1' ) ) ) ), 'ERROR' ) );
        $this->assertContains( 'figure.alt-image-of', self::codes( $v->validate( array(), array( $fig( array( 'alt_text' => 'Screenshot of the edit form' ) ) ) ) ) );
        $this->assertContains( 'figure.image-missing', self::codes( $v->validate( array(), array( $fig( array( 'has_image' => false ) ) ) ) ) );
        $this->assertContains( 'figures.target', self::codes( $v->validate( array( self::topic( array( 'figures' => array( 99 ) ) ) ) ), 'ERROR' ) );
    }

    /** HCT-08 */
    public function testSections()
    {
        $this->assertSame( 'help_public', expHelpContentSchema::sectionForAudience( array( 'Visitors', 'Editors' ), array( 'Visitors' ) ) );
        $this->assertSame( 'help_internal', expHelpContentSchema::sectionForAudience( array( 'Editors' ), array( 'Visitors' ) ) );
        $this->assertSame( 'help_internal', expHelpContentSchema::sectionForAudience( array(), array( 'Visitors' ) ) );
        $v = $this->validator();
        $findings = $v->validate( array( self::topic( array( 'audience' => array( 'Visitors' ), 'section' => 'help_internal' ) ) ) );
        $mismatch = array_values( array_filter( $findings, function ( $f ) { return $f['code'] === 'section.mismatch'; } ) );
        $this->assertSame( 'help_public', $mismatch[0]['fix'] );
        // an internal topic in the public section is the dangerous case: ERROR with the fix
        $findings = $v->validate( array( self::topic( array( 'audience' => array( 'Editors' ), 'section' => 'help_public' ) ) ) );
        $this->assertContains( 'section.mismatch', self::codes( $findings, 'ERROR' ) );
        $sections = $v->figureSections( array( self::topic( array( 'figures' => array( 5 ) ) ),
                                               self::topic( array( 'object_id' => 11, 'audience' => array( 'Visitors' ), 'figures' => array( 5, 6 ) ) ) ) );
        $this->assertSame( array( 5 => 'help_public', 6 => 'help_public' ), $sections );
        $glossary = array( array( 'object_id' => 70, 'term' => 'object', 'avoid' => array(), 'section' => 'help_public' ) );
        $this->assertContains( 'section.mismatch', self::codes( $v->validate( array(), array(), array(), $glossary ), 'ERROR' ) );
    }

    /** HCT-09 */
    public function testGlossary()
    {
        $glossary = array( array( 'object_id' => 70, 'term' => 'trash', 'avoid' => array( 'recycle bin' ), 'section' => 'help_internal' ) );
        $plain = self::topic( array( 'fields' => array( 'body' => 'Removed objects go to the recycle bin.' ) ) );
        $this->assertContains( 'glossary.avoid', self::codes( $this->validator()->validate( array( $plain ), array(), array(), $glossary ) ) );
        $label = self::topic( array( 'fields' => array( 'body' => 'Click **Empty recycle bin**.' ) ) );
        $this->assertNotContains( 'glossary.avoid', self::codes( $this->validator()->validate( array( $label ), array(), array(), $glossary ) ) );
        $word = self::topic( array( 'fields' => array( 'body' => 'The recycle binder is not meant.' ) ) );
        $this->assertNotContains( 'glossary.avoid', self::codes( $this->validator()->validate( array( $word ), array(), array(), $glossary ) ) );
    }

    /** HCT-10 */
    public function testMaps()
    {
        $topics = array( self::topic() );
        $maps = array(
            array( 'object_id' => 100, 'class' => 'help_manual', 'title' => 'Manual for members', 'shortdesc' => 'All of it.', 'audience' => array( 'Visitors' ),
                   'topicrefs' => array( 101, 10 ), 'remote_id' => 'help-manual-members', 'section' => 'help_public' ),
            array( 'object_id' => 101, 'class' => 'help_section', 'title' => 'Chapter', 'shortdesc' => '', 'audience' => array(),
                   'topicrefs' => array( 10, 102 ), 'remote_id' => 'help-chapter-a', 'section' => 'help_internal' ),
            array( 'object_id' => 102, 'class' => 'help_section', 'title' => 'Loop', 'shortdesc' => '', 'audience' => array(),
                   'topicrefs' => array( 101, 999 ), 'remote_id' => 'help-chapter-b', 'section' => 'help_public' ),
            array( 'object_id' => 103, 'class' => 'help_section', 'title' => 'Orphan', 'shortdesc' => '', 'audience' => array(),
                   'topicrefs' => array(), 'remote_id' => 'help-chapter-c', 'section' => 'help_internal' ),
        );
        $findings = $this->validator()->validate( $topics, array(), $maps );
        $codes = self::codes( $findings );
        $this->assertContains( 'map.target', $codes );
        $this->assertContains( 'map.cycle', $codes );
        $this->assertContains( 'map.orphan-chapter', $codes );
        $this->assertContains( 'map.empty', $codes );
        $sections = $this->validator()->chapterSections( $maps );
        $this->assertSame( 'help_public', $sections[101] );
        $this->assertSame( 'help_public', $sections[102] );
        $this->assertSame( 'help_internal', $sections[103] );
    }

    /** HCT-11 */
    public function testReviewAndVersion()
    {
        $codes = self::codes( $this->validator()->validate( array( self::topic( array( 'review_date' => self::NOW - 86400, 'version_from' => '6.0' ) ) ) ) );
        $this->assertContains( 'review-date.past', $codes );
        $this->assertContains( 'version.format', $codes );
        $codes = self::codes( $this->validator()->validate( array( self::topic( array( 'owner' => 0, 'platform' => array() ) ) ) ), 'NOTICE' );
        $this->assertSame( array( 'field.recommended', 'field.recommended' ), $codes );
        $this->assertSame( (int)mktime( 0, 0, 0, 4, 18, 2027 ), expHelpContentSeeder::reviewDate( 'account.log-in', mktime( 12, 0, 0, 10, 18, 2026 ) ) );
        $this->assertSame( (int)mktime( 0, 0, 0, 10, 18, 2027 ), expHelpContentSeeder::reviewDate( 'content.versions', mktime( 12, 0, 0, 10, 18, 2026 ) ) );
    }

    /** HCT-12 */
    public function testMarkdownAndDocument()
    {
        $task = self::topic( array( 'class' => 'help_task', 'type' => 'task', 'help_id' => 'content.publish-article',
                                    'fields' => array( 'context' => 'Why.', 'prereq' => '', 'steps' => "1. Do it.\n2. See [versions](help:content.versions#history).", 'result' => 'Done.', 'postreq' => '' ) ) );
        $md = expHelpContentProvider::markdown( $task, function ( $t ) { return $t; } );
        $this->assertSame( "Why.\n\n## Steps\n\n1. Do it.\n2. See [versions](content/content.versions.md#history).\n\n## Result\n\nDone.", $md );
        $this->assertSame( '[x](content/a.b.md)', expHelpContentProvider::linksRewritten( '[x](help:a.b)' ) );
        $this->assertSame( '[x](help:A b)', expHelpContentProvider::linksRewritten( '[x](help:A b)' ) );
        $this->assertSame( array( 'user', 'editor', 'admin' ), expHelpContentProvider::helpAudiences( array( 'Visitors', 'Members', 'Editors', 'Administrators', 'Unknown' ) ) );
        $doc = expHelpContentProvider::document( $task + array( 'preview' => true ) );
        $this->assertSame( 'content/content.publish-article', $doc['id'] );
        $this->assertSame( '/exphelp/topic/content.publish-article', $doc['url'] );
        $this->assertSame( array( 'editor' ), $doc['audience'] );
        $this->assertTrue( $doc['preview'] );
        $this->assertTrue( expHelpContentProvider::matchesFilters( $doc, array( 'audience' => 'admin,editor', 'type' => 'task' ) ) );
        $this->assertFalse( expHelpContentProvider::matchesFilters( $doc, array( 'audience' => 'developer' ) ) );
        $this->assertFalse( expHelpContentProvider::matchesFilters( $doc, array( 'category' => 'guides' ) ) );
    }

    /** HCT-13 */
    public function testRegistryContentPages()
    {
        $plain = new class implements expHelpContextProviderInterface {
            public function helpContext( $module, $view, $design ) { return 'content/content.versions'; }
        };
        $page = new class implements expHelpContextProviderInterface, expHelpContextPageInterface {
            public $known = array( 'content/content.versions' => 'Drafts and versions' );
            public function helpContext( $module, $view, $design ) { return $module === 'content' ? 'content/content.versions' : 'content/../etc'; }
            public function contextPage( $id ) { return isset( $this->known[$id] ) ? array( 'url' => '/exphelp/topic/' . substr( $id, 8 ), 'title' => $this->known[$id] ) : null; }
        };
        $evil = new class implements expHelpContextProviderInterface, expHelpContextPageInterface {
            public function helpContext( $module, $view, $design ) { return 'content/a.b'; }
            public function contextPage( $id ) { return array( 'url' => 'https://evil.example.org/', 'title' => 'x' ); }
        };
        $pages = function ( $id ) { return $id === 'help/content.md' ? array( 'title' => 'Content' ) : null; };
        $r = new expHelpContextRegistry( array( 'content' => 'help/content.md' ), array( $plain ), $pages );
        $this->assertSame( 'help/content.md', $r->lookup( 'content', 'edit', '' )['id'] );
        $r = new expHelpContextRegistry( array( 'content' => 'help/content.md' ), array( $page ), $pages );
        $hit = $r->lookup( 'content', 'edit', '' );
        $this->assertSame( 'content/content.versions', $hit['id'] );
        $this->assertSame( '/exphelp/topic/content.versions', $hit['url'] );
        $this->assertSame( 'Drafts and versions', $hit['title'] );
        // a bad answer of the page provider ("content/../etc" is not its pattern) falls back to the map
        $this->assertSame( 'dashboard', $r->lookup( 'setup', '', '' )['level'] );
        $r = new expHelpContextRegistry( array( 'content' => 'help/content.md' ), array( $evil ), $pages );
        $this->assertSame( 'help/content.md', $r->lookup( 'content', '', '' )['id'] );
        // a map entry can never name content/... (only providers can)
        $r = new expHelpContextRegistry( array( 'content' => 'content/content.versions' ), array(), $pages );
        $this->assertSame( 'dashboard', $r->lookup( 'content', '', '' )['level'] );
    }

    /** HCT-14 */
    public function testAnonymousPolicy()
    {
        $roles = expHelpContentSchema::roles();
        $read = array_values( array_filter( $roles['Anonymous'], function ( $p ) { return $p[0] === 'content' && $p[1] === 'read'; } ) );
        $this->assertCount( 1, $read, 'one policy, both limitations' );
        $this->assertSame( array( 'Section' => array( 'help_public' ), 'StateGroup_help_status' => array( 'released' ) ), $read[0][2] );
        // without exphelp.ini AnonymousContextHelp=enabled, Anonymous gets no exphelp policy
        if ( expHelpContentSchema::setting( 'AnonymousContextHelp', 'disabled' ) !== 'enabled' )
        {
            foreach ( $roles['Anonymous'] as $policy )
                $this->assertNotSame( 'exphelp', $policy[0] );
        }
    }

    /** HCT-15 */
    public function testEtag()
    {
        $etag = \Exponential\View\Kernel\Exphelp\Context::etag( '{"a":1}' );
        $this->assertMatchesRegularExpression( '/^"[0-9a-f]{32}"$/', $etag );
        $this->assertTrue( \Exponential\View\Kernel\Exphelp\Context::etagMatches( $etag, $etag ) );
        $this->assertTrue( \Exponential\View\Kernel\Exphelp\Context::etagMatches( '"x", W/' . $etag, $etag ) );
        $this->assertTrue( \Exponential\View\Kernel\Exphelp\Context::etagMatches( '*', $etag ) );
        $this->assertFalse( \Exponential\View\Kernel\Exphelp\Context::etagMatches( '"other"', $etag ) );
        $this->assertFalse( \Exponential\View\Kernel\Exphelp\Context::etagMatches( '', $etag ) );
    }

    /** HCT-16 */
    public function testPolicySignature()
    {
        $a = expHelpContentInstaller::signature( 'content', 'read', array( 'Section' => array( 3 ), 'StateGroup_help_status' => array( 9, 8 ) ) );
        $b = expHelpContentInstaller::signature( 'content', 'read', array( 'StateGroup_help_status' => array( '8', '9' ), 'Section' => array( '3' ) ) );
        $this->assertSame( $a, $b );
        $this->assertNotSame( $a, expHelpContentInstaller::signature( 'content', 'read', array( 'Section' => array( 3 ) ) ) );
        $xml = expHelpContentInstaller::selectionXml( array( 'Visitors', 'Members' ) );
        $this->assertStringContainsString( '<option id="0" name="Visitors"/>', $xml );
        $this->assertStringContainsString( '<option id="1" name="Members"/>', $xml );
        $this->assertStringContainsString( '<option id="5" name="Admins"/>', expHelpContentInstaller::selectionXml( array( 5 => 'Admins' ), true ) );
    }

    /** HCT-17 */
    public function testSeedFiles()
    {
        $data = expHelpContentSeeder::readFile( self::root() . '/share/help/seed-topics.json' );
        $topics = array();
        $i = 0;
        foreach ( $data['topics'] as $t )
        {
            $public = expHelpContentSchema::isPublicAudience( $t['audience'], array( 'Visitors' ) );
            $topics[] = self::topic( array( 'object_id' => ++$i, 'class' => $t['class'], 'help_id' => $t['help_id'], 'title' => $t['title'],
                                            'shortdesc' => $t['shortdesc'], 'audience' => $t['audience'], 'platform' => $t['platform'],
                                            'fields' => $t['fields'] + array_fill_keys( array_keys( expHelpContentSchema::textFields( $t['class'] ) ), '' ),
                                            'section' => $public ? 'help_public' : 'help_internal' ) );
            foreach ( $t['related'] as $rel )
                $this->assertContains( $rel, array_column( $data['topics'], 'help_id' ), "related $rel" );
        }
        $glossary = array();
        foreach ( expHelpContentSeeder::readFile( self::root() . '/share/help/seed-glossary.json' )['entries'] as $e )
            $glossary[] = array( 'object_id' => ++$i, 'term' => $e['term'], 'avoid' => $e['avoid'], 'section' => 'help_internal' );
        $findings = $this->validator()->validate( $topics, array(), array(), $glossary );
        $this->assertSame( array(), self::codes( $findings, 'ERROR' ), json_encode( $findings ) );
        $this->assertSame( array(), self::codes( $findings, 'WARNING' ), json_encode( $findings ) );
        $this->assertSame( 'help-manual-editors', $data['manuals'][0]['remote_id'] );
    }
}

?>
