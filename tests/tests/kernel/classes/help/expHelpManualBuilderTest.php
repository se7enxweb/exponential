<?php
/**
 * The manuals made from the Markdown help (doc/eng-us/features/6.0/help-content.md, "Manuals from the Markdown help"),
 * without a database: the settings, the identifiers, a page as a topic, the plan of a manual and its translation, and
 * what the synchronisation compares and removes.
 *
 *  HMB-01  [ManualSettings] Manuals[]: the lines, the shipped list (52 manuals, existing folders, unique keys)
 *  HMB-02  remote_ids: the prefix help-gen-, stable for the same page or folder, different for another
 *  HMB-03  help_ids: doc.<path>, valid, at most 80 characters, a hash for a long one and for pages that would clash
 *  HMB-04  links: a page that is a topic -> help:<help_id>, other files -> /doc/..., images, anchors, code untouched,
 *          a translation's links resolved from its own file
 *  HMB-05  the topic type: front matter topic_type, reference pages, "How to" tasks, a task without one list is a concept
 *  HMB-06  the short description: one sentence, at most 250 characters, a full stop, no markup
 *  HMB-07  the plan: manual, chapters from subfolders, README first, order, parents before children, audiences, sections
 *  HMB-08  translations: translated pages pair with the same topics; chapters and manuals only with a translated topic
 *  HMB-09  idempotence: the same pages give the same plan; an unchanged stored value is no difference
 *  HMB-10  removal: only generated objects missing from the plan, the deepest first, only below the manual given
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpManualBuilderTestSource
{
    /** @var array folder => node */
    public $folders = array();
    /** @var array id => page */
    public $pages = array();
    /** @var string doc directory of the language */
    public $dir;
    /** @var array translated path below the directory => id */
    public $paths = array();

    public function __construct( $dir = 'eng-us' )
    {
        $this->dir = $dir;
    }

    public function tree( $folder )
    {
        return isset( $this->folders[$folder] ) ? $this->folders[$folder]
             : array( 'name' => basename( $folder ), 'path' => $folder, 'title' => ucfirst( basename( $folder ) ), 'index' => null,
                      'order' => 1000, 'children' => array(), 'documents' => array() );
    }

    public function has( $id )
    {
        return isset( $this->pages[$id] );
    }

    public function page( $id )
    {
        return isset( $this->pages[$id] ) ? $this->pages[$id] : null;
    }

    public function idOf( $path )
    {
        $prefix = 'doc/' . $this->dir . '/';
        if ( strpos( $path, $prefix ) !== 0 )
            return null;
        $rest = substr( $path, strlen( $prefix ) );
        return isset( $this->paths[$rest] ) ? $this->paths[$rest] : $rest;
    }

    /** Adds a page: id, title, body, audience, order, front matter, its path below the language directory */
    public function add( $id, $title, $body, array $audience = array( 'editor' ), $order = 1000, array $meta = array(), $type = 'help', $path = null )
    {
        $path = $path === null ? $id : $path;
        $this->paths[$path] = $id;
        $this->pages[$id] = array(
            'doc' => array( 'id' => $id, 'path' => 'doc/' . $this->dir . '/' . $path, 'title' => $title, 'summary' => isset( $meta['summary'] ) ? $meta['summary'] : '',
                            'audience' => $audience, 'type' => $type, 'order' => $order, 'format' => 'markdown' ),
            'meta' => $meta,
            'body' => "# $title\n\n" . $body,
        );
        return array( 'id' => $id, 'path' => 'doc/' . $this->dir . '/' . $path, 'title' => $title, 'order' => $order, 'type' => $type, 'audience' => $audience );
    }

    public static function node( $path, $title, $index, array $documents, array $children = array(), $order = 1000 )
    {
        return array( 'name' => basename( $path ), 'path' => $path, 'title' => $title, 'index' => $index, 'index_id' => $index,
                      'order' => $order, 'children' => $children, 'documents' => $documents );
    }
}

class expHelpManualBuilderTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function options()
    {
        return array(
            'audiences' => array( 'Visitors', 'Members', 'Editors', 'Administrators', 'Developers', 'Partners' ),
            'audience_map' => array( 'visitors' => 'user', 'members' => 'user', 'editors' => 'editor', 'administrators' => 'admin',
                                     'developers' => 'developer', 'partners' => 'partner' ),
            'public_audiences' => array( 'Visitors' ),
            'edition' => '6.0.15',
            'license' => 'GNU Free Documentation License 1.3',
            'translated_titles' => array( 'start' => array( 'ger-DE' => 'Erste Schritte' ) ),
        );
    }

    /** A help folder "help/editors" with a README, two pages, a subfolder with a README and a page, a deeper subfolder */
    private static function source()
    {
        $s = new expHelpManualBuilderTestSource();
        $readme = $s->add( 'help/editors/README.md', 'Help for editors', "Start here.\n\nSee [publishing](publishing.md).", array( 'editor' ), 1,
                           array( 'summary' => 'Everything editors do. And more.' ) );
        $publishing = $s->add( 'help/editors/publishing.md', 'Publishing', "Text with [the README](README.md#start) and ![a shot](../images/x.png).", array( 'editor', 'user' ), 20 );
        $versions = $s->add( 'help/editors/versions.md', 'Versions', 'About versions.', array( 'editor' ), 10 );
        $mediaReadme = $s->add( 'help/editors/media/README.md', 'Media', 'Images and files.', array( 'editor' ), 1 );
        $upload = $s->add( 'help/editors/media/upload.md', 'How to upload a file', "Before.\n\n1. Open the folder.\n2. Click **Upload**.\n\nThe file is there.", array( 'editor' ), 10 );
        $deep = $s->add( 'help/editors/media/deep/one.md', 'Deep page', 'Deep text.', array( 'partner' ), 10 );
        $s->folders['help/editors'] = expHelpManualBuilderTestSource::node( 'help/editors', 'Help for editors', 'help/editors/README.md',
            array( $publishing, $versions ),
            array( expHelpManualBuilderTestSource::node( 'help/editors/media', 'Media', 'help/editors/media/README.md', array( $upload ),
                       array( expHelpManualBuilderTestSource::node( 'help/editors/media/deep', 'Deep', null, array( $deep ), array(), 1000 ) ), 15 ),
                   expHelpManualBuilderTestSource::node( 'help/editors/images', 'Images', null, array() ) ) );
        $loose = $s->add( 'help/getting-started.md', 'Getting started', 'Begin.', array( 'user' ), 5 );
        $s->folders['help'] = expHelpManualBuilderTestSource::node( 'help', 'Help', null, array( $loose ), array( $s->folders['help/editors'] ) );
        return $s;
    }

    private static function manuals()
    {
        return array( expHelpManualBuilder::parseManualLine( 'editors;help/editors' ), expHelpManualBuilder::parseManualLine( 'start;help/*.md;Getting started' ) );
    }

    // ---------------------------------------------------------------- HMB-01

    public function testManualLines()
    {
        $this->assertSame( array( 'key' => 'editors', 'folder' => 'help/editors', 'loose' => false, 'title' => '' ), expHelpManualBuilder::parseManualLine( 'editors;help/editors' ) );
        $this->assertSame( array( 'key' => 'start', 'folder' => 'help', 'loose' => true, 'title' => 'Getting started' ), expHelpManualBuilder::parseManualLine( 'start;help/*.md;Getting started' ) );
        $this->assertSame( 'bc', expHelpManualBuilder::parseManualLine( 'contributor-notes;bc;Contributor notes' )['folder'] );
        foreach ( array( 'Editors;help/editors', 'editors', 'editors;../etc', 'editors;help/../x', 'editors;', 'a;b;c;d' ) as $bad )
            $this->assertNull( expHelpManualBuilder::parseManualLine( $bad ), $bad );
        $parsed = expHelpManualBuilder::manualsFromSettings( array( 'a;help/a', 'a;help/b', 'bad line', '' ) );
        $this->assertCount( 1, $parsed['manuals'] );
        $this->assertCount( 2, $parsed['errors'] );
    }

    public function testShippedManualList()
    {
        $ini = (string)file_get_contents( self::root() . '/settings/exphelp.ini' );
        $this->assertSame( 1, preg_match( '/^\[ManualSettings\]\n(.*?)(?:^\[|\z)/ms', $ini, $m ) );
        preg_match_all( '/^Manuals\[\]=(.+)$/m', $m[1], $lines );
        $parsed = expHelpManualBuilder::manualsFromSettings( $lines[1] );
        $this->assertSame( array(), $parsed['errors'] );
        $this->assertCount( 52, $parsed['manuals'], 'the scope the owner approved: 45 help folders, Getting started, 6 reference manuals' );
        $help = 0;
        foreach ( $parsed['manuals'] as $manual )
        {
            $this->assertDirectoryExists( self::root() . '/doc/eng-us/' . $manual['folder'], $manual['key'] );
            if ( strpos( $manual['folder'], 'help/' ) === 0 )
                $help++;
        }
        $this->assertSame( 45, $help );
        // every help folder with pages has its manual
        $folders = array();
        foreach ( glob( self::root() . '/doc/eng-us/help/*', GLOB_ONLYDIR ) as $dir )
            if ( basename( $dir ) !== 'images' )
                $folders[] = 'help/' . basename( $dir );
        $listed = array_map( function ( $manual ) { return $manual['folder']; }, $parsed['manuals'] );
        $this->assertSame( array(), array_values( array_diff( $folders, $listed ) ) );
        $this->assertNotContains( 'history', $listed );
        $this->assertNotContains( 'examples', $listed );
        // every help audience has an audience option
        preg_match_all( '/^AudienceNames\[([^\]]+)\]=/m', $ini, $names );
        preg_match_all( '/^AudienceMap\[[^\]]+\]=(.+)$/m', $ini, $mapped );
        $this->assertSame( array(), array_values( array_diff( $names[1], $mapped[1] ) ) );
    }

    // ---------------------------------------------------------------- HMB-02, HMB-03

    public function testRemoteIds()
    {
        $a = expHelpManualBuilder::topicRemoteId( 'help/editors/publishing.md' );
        $this->assertSame( $a, expHelpManualBuilder::topicRemoteId( 'help/editors/publishing.md' ) );
        $this->assertNotSame( $a, expHelpManualBuilder::topicRemoteId( 'help/editors/versions.md' ) );
        $this->assertNotSame( expHelpManualBuilder::chapterRemoteId( 'help/editors/media' ), expHelpManualBuilder::chapterRemoteId( 'help/editors/deep' ) );
        foreach ( array( $a, expHelpManualBuilder::chapterRemoteId( 'help/editors/media' ), expHelpManualBuilder::manualRemoteId( 'editors' ) ) as $remoteId )
        {
            $this->assertTrue( expHelpManualBuilder::isGenerated( $remoteId ) );
            $this->assertLessThanOrEqual( 100, strlen( $remoteId ) );
        }
        $this->assertFalse( expHelpManualBuilder::isGenerated( 'help-manual-editors' ) );
        $this->assertFalse( expHelpManualBuilder::isGenerated( 'help-topic-content.versions' ) );
    }

    public function testHelpIds()
    {
        $this->assertSame( 'doc.help.editors.publishing', expHelpManualBuilder::helpIdCandidate( 'help/editors/publishing.md' ) );
        $this->assertSame( 'doc.bc.6-0.velocity', expHelpManualBuilder::helpIdCandidate( 'bc/6.0/Velocity.md' ) );
        $long = 'help/' . str_repeat( 'very-long-folder-name/', 5 ) . 'page.md';
        $clashA = 'help/a/Some_Page.md';
        $clashB = 'help/a/some-page.md';
        $ids = expHelpManualBuilder::helpIds( array( 'help/editors/publishing.md', $long, $clashA, $clashB ) );
        $this->assertSame( 'doc.help.editors.publishing', $ids['help/editors/publishing.md'] );
        $this->assertNotSame( $ids[$clashA], $ids[$clashB] );
        foreach ( $ids as $id )
            $this->assertTrue( expHelpContentValidator::isValidHelpId( $id ), $id );
        // the same on every run, whichever other pages there are
        $this->assertSame( $ids[$long], expHelpManualBuilder::helpIds( array( $long ) )[$long] );
    }

    // ---------------------------------------------------------------- HMB-04

    public function testLinks()
    {
        $s = self::source();
        $helpIds = array( 'help/editors/README.md' => 'doc.help.editors.readme', 'help/editors/publishing.md' => 'doc.help.editors.publishing' );
        $md = "See [README](README.md#start), [guide](../../guides/caches.md#x), [web](https://example.org/a.md),\n"
            . "[top](#here), ![shot](../images/x.png) and `[code](README.md)`.\n\n```\n[fenced](README.md)\n```\n\n[ref]: publishing.md\n";
        $out = expHelpManualBuilder::rewriteLinks( $md, 'doc/eng-us/help/editors/versions.md', array( $s, 'idOf' ), $helpIds );
        $this->assertStringContainsString( '[README](help:doc.help.editors.readme#start)', $out );
        $this->assertStringContainsString( '[guide](/doc/eng-us/guides/caches.md#x)', $out );
        $this->assertStringContainsString( '[web](https://example.org/a.md)', $out );
        $this->assertStringContainsString( '[top](#here)', $out );
        $this->assertStringContainsString( '![shot](/doc/eng-us/help/images/x.png)', $out );
        $this->assertStringContainsString( '`[code](README.md)`', $out );
        $this->assertStringContainsString( '[fenced](README.md)', $out );
        $this->assertStringContainsString( '[ref]: help:doc.help.editors.publishing', $out );
        // the topic of the help renders help: links as links to the topic
        $this->assertStringContainsString( '](content/doc.help.editors.readme.md#start)', expHelpContentProvider::linksRewritten( $out ) );

        // a translation: its links are read from its own file, and pair with the same pages
        $de = new expHelpManualBuilderTestSource( 'ger-de' );
        $de->paths['hilfe/redakteure/README.md'] = 'help/editors/README.md';
        $out = expHelpManualBuilder::rewriteLinks( '[Start](README.md) [Bild](../bilder/y.png)', 'doc/ger-de/hilfe/redakteure/versionen.md', array( $de, 'idOf' ), $helpIds );
        $this->assertSame( '[Start](help:doc.help.editors.readme) [Bild](/doc/ger-de/hilfe/bilder/y.png)', $out );
    }

    // ---------------------------------------------------------------- HMB-05

    public function testTopicType()
    {
        $task = "Before.\n\n1. One.\n2. Two.\n   Result of two.\n\nDone.";
        $this->assertSame( 'help_task', expHelpManualBuilder::topicClass( array(), array( 'type' => 'help', 'title' => 'How to do it' ), $task ) );
        $this->assertSame( array( 'context' => 'Before.', 'steps' => "1. One.\n2. Two.\n   Result of two.", 'result' => 'Done.' ), expHelpManualBuilder::topicFields( 'help_task', $task ) );
        $this->assertNull( expHelpManualBuilder::topicFields( 'help_task', "1. a\n\nText.\n\n1. b" ) );
        $this->assertSame( 'help_concept', expHelpManualBuilder::topicClass( array(), array( 'type' => 'help', 'title' => 'How to do it' ), "1. a\n\nText.\n\n1. b" ) );
        $this->assertSame( 'help_concept', expHelpManualBuilder::topicClass( array(), array( 'type' => 'help', 'title' => 'Doing it' ), $task ) );
        $this->assertSame( 'help_reference', expHelpManualBuilder::topicClass( array(), array( 'type' => 'catalogue', 'title' => 'Operators' ), 'x' ) );
        $this->assertSame( 'help_task', expHelpManualBuilder::topicClass( array( 'topic_type' => 'task' ), array( 'type' => 'help', 'title' => 'X' ), 'x' ) );
        $trouble = "Intro.\n\n## Symptom\n\nIt fails.\n\n## Cause\n\nA cache.\n\n### Detail\n\nMore.\n\n## Solution\n\nClear it.";
        $this->assertSame( array( 'condition' => 'It fails.', 'cause' => "A cache.\n\n### Detail\n\nMore.", 'remedy' => 'Clear it.' ),
                           expHelpManualBuilder::topicFields( 'help_troubleshooting', $trouble ) );
        $this->assertNull( expHelpManualBuilder::topicFields( 'help_troubleshooting', "## Cause\n\nNo condition." ) );
        $this->assertSame( "Text.\n", expHelpManualBuilder::withoutTitle( "\n# Title\n\nText.\n" ) );
        $this->assertSame( "Text.\n", expHelpManualBuilder::withoutTitle( "Title\n=====\n\nText." ) );
        $this->assertSame( "## Not a title\n", expHelpManualBuilder::withoutTitle( "## Not a title\n" ) );
    }

    // ---------------------------------------------------------------- HMB-06

    public function testShortdesc()
    {
        $this->assertSame( 'Everything editors do.', expHelpManualBuilder::shortdesc( 'Everything **editors** do. And [more](x.md).', 'T' ) );
        $this->assertSame( 'Use e.g. the cache.', expHelpManualBuilder::shortdesc( 'Use e.g. the cache. Then more.', 'T' ) );
        $this->assertSame( 'Publishing.', expHelpManualBuilder::shortdesc( '', 'Publishing' ) );
        $long = expHelpManualBuilder::shortdesc( str_repeat( 'word ', 80 ) . '…', 'T' );
        $this->assertLessThanOrEqual( 250, mb_strlen( $long ) );
        $this->assertStringEndsWith( '.', $long );
        foreach ( array( $long, expHelpManualBuilder::shortdesc( 'One. Two. Three.', 'T' ) ) as $text )
            $this->assertSame( 1, expHelpContentValidator::sentenceCount( $text ) );
    }

    // ---------------------------------------------------------------- HMB-07

    public function testPlan()
    {
        $builder = new expHelpManualBuilder( self::source(), array(), self::options() );
        $plan = $builder->plan( self::manuals() );
        $items = $plan['items'];
        $manual = $items['help-gen-manual-editors'];
        $this->assertSame( 'Help for editors', $manual['attributes']['title'] );
        $this->assertSame( 'Everything editors do.', $manual['attributes']['shortdesc'] );
        $this->assertSame( 'Editors', $manual['attributes']['audience'] );
        $this->assertSame( '6.0.15', $manual['attributes']['edition'] );
        $this->assertSame( 'help_internal', $manual['section'] );
        $media = expHelpManualBuilder::chapterRemoteId( 'help/editors/media' );
        $deep = expHelpManualBuilder::chapterRemoteId( 'help/editors/media/deep' );
        $t = function ( $id ) { return expHelpManualBuilder::topicRemoteId( $id ); };
        // README first, then by order: versions (10), the chapter Media (15), publishing (20); the empty images folder is no chapter
        $this->assertSame( array( $t( 'help/editors/README.md' ), $t( 'help/editors/versions.md' ), $media, $t( 'help/editors/publishing.md' ) ), $manual['refs'] );
        $this->assertSame( array( $t( 'help/editors/media/README.md' ), $t( 'help/editors/media/upload.md' ), $deep ), $items[$media]['refs'] );
        $this->assertSame( 'help-gen-manual-editors', $items[$media]['parent'] );
        $this->assertSame( $media, $items[$deep]['parent'] );
        $this->assertSame( $deep, $items[$t( 'help/editors/media/deep/one.md' )]['parent'] );
        // parents before their children
        $position = array_flip( array_keys( $items ) );
        foreach ( $items as $remoteId => $item )
            if ( $item['parent'] !== '' )
                $this->assertLessThan( $position[$remoteId], $position[$item['parent']], $remoteId );
        // topics
        $publishing = $items[$t( 'help/editors/publishing.md' )];
        $this->assertSame( 'help_concept', $publishing['class'] );
        $this->assertSame( 'doc.help.editors.publishing', $publishing['attributes']['help_id'] );
        $this->assertSame( 'Visitors|Editors', $publishing['attributes']['audience'] );
        $this->assertSame( 'help_public', $publishing['section'] );
        $this->assertSame( 'Text with [the README](help:doc.help.editors.readme#start) and ![a shot](/doc/eng-us/help/images/x.png).', $publishing['attributes']['body'] );
        $upload = $items[$t( 'help/editors/media/upload.md' )];
        $this->assertSame( 'help_task', $upload['class'] );
        $this->assertSame( "1. Open the folder.\n2. Click **Upload**.", $upload['attributes']['steps'] );
        $this->assertSame( 'Partners', $items[$t( 'help/editors/media/deep/one.md' )]['attributes']['audience'] );
        // every planned topic passes the validator's checks of its fields without ERROR
        $topics = array();
        foreach ( $items as $item )
            if ( $item['kind'] === 'topic' )
            {
                $fields = $item['attributes'];
                $topics[] = array( 'object_id' => count( $topics ) + 1, 'class' => $item['class'], 'help_id' => $fields['help_id'], 'title' => $fields['title'],
                                   'shortdesc' => $fields['shortdesc'], 'audience' => explode( '|', $fields['audience'] ), 'platform' => array(),
                                   'version_from' => '', 'version_to' => '', 'review_date' => 0, 'owner' => 0, 'related' => array(), 'figures' => array(),
                                   'fields' => array_intersect_key( $fields, expHelpContentSchema::textFields( $item['class'] ) ),
                                   'section' => $item['section'], 'state' => 'released', 'node_id' => 1, 'modified' => 0, 'language' => 'eng-US' );
            }
        $validator = new expHelpContentValidator( array( 'now' => 1792281600, 'areas' => array( 'doc' ), 'public_audiences' => array( 'Visitors' ), 'used_help_ids' => array() ) );
        $errors = array_filter( $validator->validate( $topics, array(), array(), array() ), function ( $f ) { return $f['level'] === 'ERROR'; } );
        $this->assertSame( array(), array_values( $errors ) );

        // the loose manual: only the pages directly in help/, no chapters; public, because its page is for users
        $start = $items['help-gen-manual-start'];
        $this->assertSame( 'Getting started', $start['attributes']['title'] );
        $this->assertSame( array( $t( 'help/getting-started.md' ) ), $start['refs'] );
        $this->assertSame( 'help_public', $start['section'] );
    }

    public function testChaptersOfAPublicManualArePublic()
    {
        $s = self::source();
        $s->pages['help/editors/README.md']['doc']['audience'] = array( 'user' );
        $plan = ( new expHelpManualBuilder( $s, array(), self::options() ) )->plan( array( expHelpManualBuilder::parseManualLine( 'editors;help/editors' ) ) );
        $this->assertSame( 'help_public', $plan['items']['help-gen-manual-editors']['section'] );
        $this->assertSame( 'help_public', $plan['items'][expHelpManualBuilder::chapterRemoteId( 'help/editors/media/deep' )]['section'] );
        // a topic keeps the section of its own audience
        $this->assertSame( 'help_internal', $plan['items'][expHelpManualBuilder::topicRemoteId( 'help/editors/versions.md' )]['section'] );
    }

    public function testStartPageTitle()
    {
        $this->assertSame( 'Adopters', expHelpManualBuilder::folderTitle( array( 'index' => null, 'title' => 'Adopters x' ),
                                                                         array( 'doc' => array( 'title' => 'Adopters: start here' ) ) ) );
        $this->assertSame( 'Behörden', expHelpManualBuilder::folderTitle( array( 'index' => null, 'title' => 'Government' ),
                                                                         array( 'doc' => array( 'title' => 'Behörden: Hier beginnen' ) ) ) );
        $this->assertSame( 'From README', expHelpManualBuilder::folderTitle( array( 'index' => 'x/README.md', 'title' => 'From README' ), null ) );
    }

    // ---------------------------------------------------------------- HMB-08

    public function testTranslations()
    {
        $de = new expHelpManualBuilderTestSource( 'ger-de' );
        $readme = $de->add( 'help/editors/README.md', 'Hilfe für Redakteure', "Hier anfangen.\n\nSiehe [Veröffentlichen](veroeffentlichen.md).", array( 'editor' ), 1,
                            array( 'summary' => 'Alles, was Redakteure tun.' ), 'help', 'hilfe/redakteure/README.md' );
        $de->add( 'help/editors/publishing.md', 'Veröffentlichen', 'Text.', array( 'editor', 'user' ), 20, array(), 'help', 'hilfe/redakteure/veroeffentlichen.md' );
        // a German task whose steps are not one list: not translated, reported
        $de->add( 'help/editors/media/upload.md', 'Eine Datei hochladen', "1. a\n\nText.\n\n1. b", array( 'editor' ), 10, array(), 'help', 'hilfe/redakteure/medien/hochladen.md' );
        $de->folders['help/editors'] = expHelpManualBuilderTestSource::node( 'help/editors', 'Hilfe für Redakteure', 'help/editors/README.md', array() );
        $builder = new expHelpManualBuilder( self::source(), array( 'ger-DE' => $de ), self::options() );
        $plan = $builder->plan( self::manuals() );
        $items = $plan['items'];
        $t = function ( $id ) { return expHelpManualBuilder::topicRemoteId( $id ); };
        $readmeTopic = $items[$t( 'help/editors/README.md' )];
        $this->assertSame( 'Hilfe für Redakteure', $readmeTopic['translations']['ger-DE']['title'] );
        $this->assertSame( 'Alles, was Redakteure tun.', $readmeTopic['translations']['ger-DE']['shortdesc'] );
        $this->assertSame( "Hier anfangen.\n\nSiehe [Veröffentlichen](help:doc.help.editors.publishing).", $readmeTopic['translations']['ger-DE']['body'] );
        $this->assertArrayNotHasKey( 'help_id', $readmeTopic['translations']['ger-DE'], 'help_id and audience are not translatable' );
        $this->assertArrayNotHasKey( 'ger-DE', $items[$t( 'help/editors/versions.md' )]['translations'] );
        $this->assertArrayNotHasKey( 'ger-DE', $items[$t( 'help/editors/media/upload.md' )]['translations'] );
        $this->assertNotEmpty( array_filter( $builder->notices(), function ( $n ) { return strpos( $n, 'upload.md (ger-DE)' ) !== false; } ) );
        $this->assertSame( 'Hilfe für Redakteure', $items['help-gen-manual-editors']['translations']['ger-DE']['title'] );
        // the chapter Media contains no translated topic: no translation
        $this->assertArrayNotHasKey( 'ger-DE', $items[expHelpManualBuilder::chapterRemoteId( 'help/editors/media' )]['translations'] );
        $this->assertArrayNotHasKey( 'ger-DE', $items['help-gen-manual-start']['translations'] );
    }

    // ---------------------------------------------------------------- HMB-09

    public function testIdempotence()
    {
        $a = ( new expHelpManualBuilder( self::source(), array(), self::options() ) )->plan( self::manuals() );
        $b = ( new expHelpManualBuilder( self::source(), array(), self::options() ) )->plan( self::manuals() );
        $this->assertSame( $a, $b );

        $sync = new expHelpManualSync( array( 'user_id' => 14, 'batch_size' => 10, 'language' => 'eng-US' ) );
        $sync->setClasses( array( 'help_concept' => array( 'id' => 92, 'attributes' => array(
            'title' => array( 'id' => 1, 'type' => 'ezstring', 'options' => array() ),
            'audience' => array( 'id' => 2, 'type' => 'ezselection', 'options' => array( 'Visitors' => '0', 'Editors' => '2' ) ),
            'body' => array( 'id' => 3, 'type' => 'eztext', 'options' => array() ),
            'related' => array( 'id' => 4, 'type' => 'ezobjectrelationlist', 'options' => array() ),
        ) ) ) );
        $stored = array( 'title' => 'Publishing', 'audience' => '2-0', 'body' => 'Text.' );
        $this->assertSame( array(), $sync->differing( 'help_concept', $stored, array( 'title' => 'Publishing', 'audience' => 'Visitors|Editors', 'body' => 'Text.' ) ) );
        $this->assertSame( array( 'body' => 'New.' ), $sync->differing( 'help_concept', $stored, array( 'title' => 'Publishing', 'body' => 'New.' ) ) );
        $this->assertSame( array( 'audience' => 'Editors' ), $sync->differing( 'help_concept', $stored, array( 'audience' => 'Editors' ) ) );
        $this->assertSame( array( 'title' => 'T' ), $sync->differing( 'help_concept', null, array( 'title' => 'T', 'unknown' => 'x' ) ) );
        $xml = '<?xml version="1.0"?><related-objects><relation-list><relation-item priority="2" contentobject-id="7"/>'
             . '<relation-item contentobject-id="5" priority="1"/></relation-list></related-objects>';
        $this->assertSame( array( 5, 7 ), expHelpManualSync::relationIds( $xml ) );
        $this->assertTrue( expHelpManualSync::sameValue( array( 'type' => 'ezobjectrelationlist', 'options' => array() ), $xml, array( 5, 7 ) ) );
        $this->assertFalse( expHelpManualSync::sameValue( array( 'type' => 'ezobjectrelationlist', 'options' => array() ), $xml, array( 7, 5 ) ) );
    }

    // ---------------------------------------------------------------- HMB-10

    public function testRemoval()
    {
        $stored = array(
            'help-gen-topic-aaaa' => array( 'path_string' => '/1/43/100/200/300/' ),
            'help-gen-chapter-bbbb' => array( 'path_string' => '/1/43/100/200/' ),
            'help-gen-topic-kept' => array( 'path_string' => '/1/43/100/201/' ),
            'help-topic-content.versions' => array( 'path_string' => '/1/43/90/' ),
            'help-manual-editors' => array( 'path_string' => '/1/43/91/' ),
            'help-gen-topic-other' => array( 'path_string' => '/1/43/500/501/' ),
        );
        $items = array( 'help-gen-topic-kept' => array() );
        $gone = expHelpManualSync::gone( $stored, $items );
        $this->assertSame( array( 'help-gen-topic-aaaa', 'help-gen-chapter-bbbb', 'help-gen-topic-other' ), array_keys( $gone ) );
        $this->assertSame( array( 'help-gen-topic-aaaa', 'help-gen-chapter-bbbb' ), array_keys( expHelpManualSync::gone( $stored, $items, '/100/' ) ) );
    }
}

?>
