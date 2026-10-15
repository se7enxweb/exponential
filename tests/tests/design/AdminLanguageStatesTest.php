<?php
/**
 * admin4's language badges show the state of a language for one object by their shape, not by a colour per language:
 * the main language filled, a translation outlined, a missing one dashed (a "Translate into" link for who may create
 * it), an outdated one with a mark. expContentLanguageStates works the states out; design:parts/
 * language_state_badge.tpl draws them in the node view and its Translations tab, the sub-items script in the
 * Translations column. doc/features/6.0/admin4-design.md.
 *
 *  LS-01 - states(): main, present and missing in the site's order, the object's other languages after them;
 *          can_translate only for a missing language the user may create
 *  LS-02 - outdated only for a present translation whose last edit and the main language's are both known and older
 *  LS-03 - a list shows the missing languages up to 6 languages of the site, the template uses the same limit
 *  LS-04 - lastEdited() reads all objects with one grouped query (SQL and MongoDB), none without objects
 *  LS-05 - the titles and accessible names are in ger-DE and eng-US and used by the include and the sub-items table
 *  LS-06 - no colour per language and none of the status colours: the state is a class from the state only
 *  LS-07 - the shapes survive forced colours; the fill keeps its contrast (AdminTokenContrastTest)
 *  LS-08 - the sub-items formatter run in node (tests/tests/design/js/language_states.js; skipped without node)
 *
 * No database: the rules take plain arrays, the query goes to a stand-in that records it.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLanguageStatesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminLanguageStatesTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/node/view/full';

    const TEXTS = array( '%language – main language' => '%language – Hauptsprache',
                         '%language – translation exists' => '%language – Übersetzung vorhanden',
                         '%language – missing' => '%language – fehlt',
                         '%language – outdated, older than %main' => '%language – veraltet, älter als %main',
                         'Translate into %language' => 'In %language übersetzen' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function file( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    private static function site()
    {
        return array( 'ger-DE' => 'Deutsch', 'eng-GB' => 'English', 'fre-FR' => 'Français', 'esl-ES' => 'Español' );
    }

    /** LS-01 */
    public function testTheStatesFollowTheSitesOrder()
    {
        $states = expContentLanguageStates::states( self::site(), array( 'ger-DE' => 'Deutsch', 'eng-GB' => 'English', 'nor-NO' => 'Norsk' ),
                                                    'ger-DE', array(), array( 'fre-FR' ) );
        $this->assertSame( array( 'ger-DE', 'eng-GB', 'fre-FR', 'esl-ES', 'nor-NO' ), array_column( $states, 'locale' ) );
        $this->assertSame( array( 'main', 'present', 'missing', 'missing', 'present' ), array_column( $states, 'state' ) );
        $this->assertSame( array( false, false, true, false, false ), array_column( $states, 'can_translate' ),
                           'only a missing language the user may create is a link' );
        $this->assertSame( 'Français', $states[2]['name'] );

        // a creatable language the object already has is no translate link
        $states = expContentLanguageStates::states( self::site(), array( 'eng-GB' => 'English' ), 'eng-GB', array(), array( 'eng-GB' ) );
        $this->assertFalse( $states[1]['can_translate'] );
        $this->assertSame( 'main', $states[1]['state'] );
    }

    /** LS-02 */
    public function testOnlyAKnownOlderTranslationIsOutdated()
    {
        $edited = array( 'ger-DE' => 2000, 'eng-GB' => 1000, 'fre-FR' => 3000 );
        $this->assertTrue( expContentLanguageStates::isOutdated( 'eng-GB', 'ger-DE', $edited ) );
        $this->assertFalse( expContentLanguageStates::isOutdated( 'fre-FR', 'ger-DE', $edited ), 'newer than the main language' );
        $this->assertFalse( expContentLanguageStates::isOutdated( 'ger-DE', 'ger-DE', $edited ), 'the main language itself' );
        $this->assertFalse( expContentLanguageStates::isOutdated( 'esl-ES', 'ger-DE', $edited ), 'no version edited in it: unknown' );
        $this->assertFalse( expContentLanguageStates::isOutdated( 'eng-GB', 'ger-DE', array( 'eng-GB' => 1000 ) ),
                            'the main language\'s edit is unknown (an import, the history limit)' );
        $this->assertFalse( expContentLanguageStates::isOutdated( 'eng-GB', 'ger-DE', array( 'ger-DE' => 1000, 'eng-GB' => 1000 ) ) );

        $site = self::site();
        $states = expContentLanguageStates::states( $site, array( 'ger-DE' => 'Deutsch', 'eng-GB' => 'English', 'fre-FR' => 'Français' ),
                                                    'ger-DE', $edited + array( 'esl-ES' => 1 ) );
        $this->assertSame( array( false, true, false, false ), array_column( $states, 'outdated' ),
                           'only a present translation can be outdated, never the main or a missing one' );
    }

    /** LS-03 */
    public function testListsShowTheMissingLanguagesOnlyForFewLanguages()
    {
        $this->assertTrue( expContentLanguageStates::showMissingInList( 1 ) );
        $this->assertTrue( expContentLanguageStates::showMissingInList( 6 ) );
        $this->assertFalse( expContentLanguageStates::showMissingInList( 7 ) );
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $template = self::file( "design/$design/templates/children_detailed.tpl" );
            $this->assertStringContainsString( "showMissingLanguages: {if le( fetch( 'content', 'prioritized_languages' )|count, "
                                               . expContentLanguageStates::LIST_MISSING_LIMIT . ' )}true{else}false{/if}', $template, $design );
        }
    }

    /** A stand-in for eZDB that records the queries */
    private static function recordingDB( $name, array $rows )
    {
        return new class( $name, $rows )
        {
            public $queries = array();
            private $name;
            private $rows;

            public function __construct( $name, $rows )
            {
                $this->name = $name;
                $this->rows = $rows;
            }

            public function databaseName()
            {
                return $this->name;
            }

            public function generateSQLINStatement( $elements, $column, $not = false, $unique = true, $type = false )
            {
                return $column . ' IN ( ' . implode( ', ', array_map( 'intval', $elements ) ) . ' )';
            }

            public function arrayQuery( $sql )
            {
                $this->queries[] = $sql;
                return $this->rows;
            }

            public function find( $table, $conds, $projection = array() )
            {
                $this->queries[] = array( $table, $conds );
                return $this->rows;
            }
        };
    }

    /** LS-04 */
    public function testAllObjectsAreReadWithOneQuery()
    {
        $db = self::recordingDB( 'mysql', array(
            array( 'contentobject_id' => '7', 'initial_language_id' => '2', 'modified' => '2000' ),
            array( 'contentobject_id' => '7', 'initial_language_id' => '4', 'modified' => '1000' ),
            array( 'contentobject_id' => '9', 'initial_language_id' => '2', 'modified' => '500' ) ) );
        $edited = expContentLanguageStates::lastEdited( range( 1, 50 ), $db );
        $this->assertCount( 1, $db->queries, 'one query for a page of 50 objects' );
        $sql = $db->queries[0];
        $this->assertStringContainsString( 'FROM ezcontentobject_version', $sql );
        $this->assertStringContainsString( 'contentobject_id IN ( 1, 2, 3', $sql );
        $this->assertStringContainsString( 'status IN ( 1, 3 )', $sql, 'published and archived versions' );
        $this->assertStringContainsString( 'GROUP BY contentobject_id, initial_language_id', $sql );
        $this->assertSame( array( 7 => array( 2 => 2000, 4 => 1000 ), 9 => array( 2 => 500 ) ), $edited );

        $db = self::recordingDB( 'mysql', array() );
        $this->assertSame( array(), expContentLanguageStates::lastEdited( array(), $db ) );
        $this->assertSame( array(), expContentLanguageStates::lastEdited( array( 0, '' ), $db ) );
        $this->assertCount( 0, $db->queries, 'no objects, no query' );

        // MongoDB: one find over all objects, the newest version per language kept in PHP
        $db = self::recordingDB( 'mongo', array(
            array( 'contentobject_id' => 7, 'initial_language_id' => 2, 'modified' => 1000 ),
            array( 'contentobject_id' => 7, 'initial_language_id' => 2, 'modified' => 3000 ),
            array( 'contentobject_id' => 7, 'initial_language_id' => 4, 'modified' => 2000 ) ) );
        $this->assertSame( array( 7 => array( 2 => 3000, 4 => 2000 ) ), expContentLanguageStates::lastEdited( array( 7, 8 ), $db ) );
        $this->assertCount( 1, $db->queries );
        $this->assertSame( 'ezcontentobject_version', $db->queries[0][0] );
        $this->assertSame( array( array( 7, 8 ) ), $db->queries[0][1]['contentobject_id'] );
    }

    /** LS-04: the pages hand over what the states need without a query per row */
    public function testThePagesGetTheStatesWithoutAQueryPerRow()
    {
        $definition = self::file( 'kernel/content/function_definition.php' );
        $this->assertMatchesRegularExpression( "/\\\$FunctionList\\['language_states'\\].*?'class' => 'expContentLanguageStates',\\s*'method' => 'fetchForObject'/s", $definition );
        // the sub-items rows carry the main language (from the cached language list), the badges compute the rest
        $this->assertStringContainsString( "\$ret['initial_language']        \t= (string) \$contentObject->attribute( 'initial_language_code' );",
                                           self::file( 'extension/ezjscore/classes/ezjscajaxcontent.php' ) );
        // the node view and its Translations tab fetch the states once for the object
        $this->assertSame( 1, substr_count( self::file( 'design/admin4/templates/node/view/a4_full_body.tpl' ), "fetch( 'content', 'language_states'" ) );
        $tab = self::file( 'design/admin4/templates/translations.tpl' );
        $this->assertSame( 1, substr_count( $tab, "fetch( 'content', 'language_states'" ) );
        $this->assertStringContainsString( "state_object_id=\$node.object.id", $tab, 'the missing ones link to translate' );
        $this->assertStringNotContainsString( 'language_states', self::file( 'design/admin4/javascript/ezajaxsubitems_expdatatable.js' ) );
    }

    /** source => translations of self::CONTEXT in $locale */
    private static function messages( $locale )
    {
        $dom = new DOMDocument();
        self::assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ) );
        $messages = array();
        foreach ( ( new DOMXPath( $dom ) )->query( "//context[name='" . self::CONTEXT . "']/message" ) as $message )
        {
            $translation = $message->getElementsByTagName( 'translation' )->item( 0 );
            $messages[$message->getElementsByTagName( 'source' )->item( 0 )->textContent][] = $translation ? $translation->textContent : '';
        }
        return $messages;
    }

    /** LS-05 */
    public function testTheStateTextsAreTranslatedAndUsed()
    {
        $german = self::messages( 'ger-DE' );
        $english = self::messages( 'eng-US' );
        foreach ( self::TEXTS as $source => $translation )
        {
            $this->assertSame( array( $translation ), isset( $german[$source] ) ? $german[$source] : null, "ger-DE: $source" );
            $this->assertSame( array( $source ), isset( $english[$source] ) ? $english[$source] : null, "eng-US: $source" );
        }
        $include = self::file( 'design/admin4/templates/parts/language_state_badge.tpl' );
        foreach ( array_keys( self::TEXTS ) as $source )
            $this->assertStringContainsString( "'$source'|i18n( '" . self::CONTEXT . "'", $include );
        // a link shows the code and names the state as hidden text; a plain badge carries it as its accessible name
        $this->assertStringContainsString( 'role="img" aria-label="{$a4_ls_label|wash}"', $include );
        $this->assertStringContainsString( '<span class="a4-sr-only"> {$a4_ls_label|wash}</span></a>', $include );
        $this->assertStringContainsString( "concat( 'content/edit/', \$state_object_id, '/f/', \$state_entry.locale )", $include,
                                           'translate takes the GET way of the Edit in menu' );
        foreach ( array( 'main language', 'translation exists', 'missing' ) as $state )
            $this->assertStringContainsString( "'%language – $state'|i18n( '" . self::CONTEXT . "',, hash( '%language', '%language' ) )",
                                               self::file( 'design/admin4/templates/children_detailed.tpl' ) );

        // the include reads only its own parameters and variables (it sees the caller's)
        $code = (string)preg_replace( '/\{\*.*?\*\}/s', '', $include );
        preg_match_all( '/\$([a-z_0-9]+)/', $code, $matches );
        foreach ( array_unique( $matches[1] ) as $name )
            $this->assertMatchesRegularExpression( '/^(state_[a-z_]+|a4_ls_[a-z]+)$/', $name, "the include reads \$$name" );
    }

    /** The .a4-lang rules of components.css (comments removed) */
    private static function badgeRules( $design = 'admin4' )
    {
        $css = (string)preg_replace( '#/\*.*?\*/#s', '', self::file( "design/$design/stylesheets/components.css" ) );
        preg_match_all( '/[^{}]*\.a4-lang[^{]*\{[^}]*\}/', $css, $rules );
        return $rules[0];
    }

    /** LS-06 */
    public function testNoColourPerLanguageAndNoStatusColour()
    {
        $rules = self::badgeRules();
        $this->assertGreaterThanOrEqual( 8, count( $rules ) );
        $all = implode( "\n", $rules );
        foreach ( array( 'is-main', 'is-present', 'is-missing', 'is-outdated' ) as $state )
        {
            if ( $state !== 'is-present' )
                $this->assertStringContainsString( ".a4-lang.$state", $all );
        }
        $this->assertDoesNotMatchRegularExpression( '/--a4-(ok|warn|bad|info)\b|--a4-(ok|warn|bad|info)-|--a4-chart-/', $all,
                                                    'green, amber, red and blue are the visibility\'s and the states\'' );
        $this->assertDoesNotMatchRegularExpression( '/\[lang|:lang\(|\[data-lang|\.a4-lang-[a-z]{2}\b|is-(ger|eng|fre|de|en|fr)\b/i', $all,
                                                    'no selector picks out a language' );
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,6}\b|rgba?\(/i', $all, 'tokens only' );
        $this->assertMatchesRegularExpression( '/\.a4-lang\.is-missing\s*\{[^}]*border-style:\s*dashed/', $all );
        $this->assertMatchesRegularExpression( '/\.a4-lang\.is-main\s*\{[^}]*background-color:\s*var\(--a4-ink\)/', $all );

        // the scripts and templates make the class from the state, never from the locale
        $script = self::file( 'design/admin4/javascript/ezajaxsubitems_expdatatable.js' );
        $this->assertStringContainsString( "cls = 'a4-lang is-' + state", $script );
        $this->assertMatchesRegularExpression( "/state = e === main \\? 'main' : \\(own\\.indexOf\\(e\\) !== -1 \\? 'present' : 'missing'\\)/", $script );
        $include = self::file( 'design/admin4/templates/parts/language_state_badge.tpl' );
        $this->assertSame( 2, substr_count( $include, 'class="a4-lang is-{$state_entry.state|wash}' ) );
        $this->assertStringNotContainsString( 'is-{$state_entry.locale', $include );
        $this->assertSame( array( 'main', 'present', 'missing' ),
                           array( expContentLanguageStates::MAIN, expContentLanguageStates::PRESENT, expContentLanguageStates::MISSING ) );
    }

    /** LS-07 */
    public function testTheShapesSurviveForcedColours()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = self::file( "design/$design/stylesheets/components.css" );
            $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{\s*\.a4-lang \{[^}]*\}\s*\.a4-lang\.is-main \{[^}]*forced-color-adjust: none;[^}]*background-color: CanvasText/', $css, $design );
            $this->assertMatchesRegularExpression( '/\.a4-lang\.is-outdated::after \{ forced-color-adjust: none;[^}]*background-color: CanvasText/', $css, $design );
        }
        require_once __DIR__ . '/AdminTokenContrastTest.php';
        $this->assertContains( array( '--a4-card', '--a4-ink', 4.5 ), AdminTokenContrastTest::pairs(), 'the main language\'s fill' );
        $this->assertContains( array( '--a4-field-line', '--a4-card', 3.0 ), AdminTokenContrastTest::pairs(), 'the outline' );
        $this->assertContains( array( '--a4-muted', '--a4-card', 4.5 ), AdminTokenContrastTest::pairs(), 'a missing language\'s text' );
    }

    /** LS-08 */
    public function testTheSubItemsFormatterDrawsTheStates()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/language_states.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/ezajaxsubitems_expdatatable.js' ) . ' 2>&1' );
        $runs = array();
        foreach ( array_filter( explode( "\n", trim( $out ) ) ) as $line )
        {
            $run = json_decode( $line, true );
            $this->assertIsArray( $run, $out );
            $runs[$run['name']] = $run['html'];
        }
        $this->assertCount( 5, $runs, $out );

        $few = $runs['few languages, read only'];
        $this->assertSame( 4, substr_count( $few, 'class="a4-lang ' ) );
        $this->assertStringContainsString( '<span class="a4-lang is-main" title="Deutsch – main language" role="img" aria-label="Deutsch – main language" translate="no">DE</span>', $few );
        $this->assertStringContainsString( 'class="a4-lang is-present" title="English – translation exists"', $few );
        $this->assertStringContainsString( 'class="a4-lang is-missing" title="Français – missing" role="img" aria-label="Français – missing" translate="no">FR</span>', $few );
        $this->assertLessThan( strpos( $few, '>EN<' ), strpos( $few, '>DE<' ), 'the site\'s order' );

        $many = $runs['many languages'];
        $this->assertSame( 2, substr_count( $many, 'class="a4-lang ' ), 'with many languages only the object\'s own' );
        $this->assertStringNotContainsString( 'is-missing', $many );

        $editable = $runs['editable'];
        $this->assertStringContainsString( '<a class="a4-lang is-main" href="/content/edit/42/f/ger-DE" title="Deutsch – main language. Edit in &#60;Deutsch&#62;."', $editable );
        $this->assertStringContainsString( 'translate="no">DE<span class="a4-sr-only"> Deutsch – main language. Edit in &#60;Deutsch&#62;.</span></a>', $editable );
        $this->assertStringNotContainsString( '/f/fre-FR', $editable, 'a missing language is no link in a list: who may create it is not known per row' );

        $this->assertStringNotContainsString( '<b>', $runs['escaped'] );
        $this->assertStringContainsString( '&#60;b&#62;&#34;x&#34;&#60;/b&#62; – main language', $runs['escaped'] );

        $outside = $runs['a language outside the site list'];
        $this->assertStringContainsString( 'class="a4-lang is-main" title="nor-NO – main language"', $outside );
        $this->assertGreaterThan( strpos( $outside, '>ES<' ), strpos( $outside, 'nor-NO – main' ), 'after the site\'s languages' );
    }
}
