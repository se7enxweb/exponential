<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * Area N3 Rendering of cjw_newsletter 4.2.0: the placeholders, the "newsletter condition" of ezxmltext and ezrichtext,
 * the plain text views, the skins, the language per subscriber, interests (the part of the preference page and the
 * block of a skin), the notification card, the list and send form parts, and the views preview_as, interest_list,
 * interest_edit, skin_preview.
 *
 * Live style: the editions, translations, subscribers (nltest-*@example.invalid) and interests are made by the test
 * and removed in tearDown; the list's rendering columns are put back as they were. Mail is only written by the file
 * transport into a directory of the test.
 */
class cjwNewsletterRenderingTest extends cjwNewsletterTestCase
{
    const NAME = '<b>Ann & "Bo"</b>';
    const NAME_ESCAPED = '&lt;b&gt;Ann &amp; &quot;Bo&quot;&lt;/b&gt;';
    const OWN_DOMAIN = 'n3.example.invalid';

    /** @var array the list rows' rendering columns before the test */
    protected $savedListRows = array();
    /** @var int[] interests made by the test */
    protected $createdInterestIds = array();

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'CjwNewsletterRendering' ) )
            $this->markTestSkipped( 'The rendering of 4.2.0 is not installed' );
        $columns = 'contentobject_attribute_id, contentobject_attribute_version, skin_name, skin_name_array_string, main_language, language_array_string, interest_source, personalize_content';
        $this->savedListRows = eZDB::instance()->arrayQuery( "SELECT $columns FROM cjwnl_list WHERE contentobject_id = " . self::LIST_OBJECT_ID );
        CjwNewsletterRendering::clearCache();
        CjwNewsletterInterests::clearCache();
        CjwNewsletterConditions::$active = false;
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null && class_exists( 'CjwNewsletterRendering' ) )
        {
            $db = eZDB::instance();
            foreach ( $this->createdObjectIds as $id )
                foreach ( $db->arrayQuery( 'SELECT id FROM cjwnl_edition_send WHERE edition_contentobject_id = ' . (int)$id ) as $row )
                    $db->query( 'DELETE FROM cjwnl_edition_send_output WHERE edition_send_id = ' . (int)$row['id'] );
            // the subscribers of this test have an address domain of their own, so the sweeps of other suites never meet them
            foreach ( $db->arrayQuery( "SELECT id FROM cjwnl_user WHERE email LIKE 'n3test-%@" . self::OWN_DOMAIN . "'" ) as $row )
            {
                $uid = (int)$row['id'];
                $db->query( 'DELETE FROM cjwnl_user_interest WHERE newsletter_user_id = ' . $uid );
                $db->query( 'DELETE FROM cjwnl_edition_send_item WHERE newsletter_user_id = ' . $uid );
                $db->query( 'DELETE FROM cjwnl_subscription WHERE newsletter_user_id = ' . $uid );
                $db->query( 'DELETE FROM cjwnl_user WHERE id = ' . $uid );
            }
            foreach ( $this->createdInterestIds as $id )
            {
                $db->query( 'DELETE FROM cjwnl_user_interest WHERE interest_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_interest WHERE id = ' . (int)$id );
            }
            $db->query( "DELETE FROM cjwnl_interest WHERE identifier LIKE 'nltest_%'" . $this->ownRows( 'cjwnl_interest' ) );
            $this->createdInterestIds = array();
            foreach ( $this->savedListRows as $row )
                $db->query( "UPDATE cjwnl_list SET skin_name = '" . $db->escapeString( $row['skin_name'] ) . "', skin_name_array_string = '" . $db->escapeString( $row['skin_name_array_string'] )
                    . "', main_language = '" . $db->escapeString( $row['main_language'] ) . "', language_array_string = '" . $db->escapeString( $row['language_array_string'] )
                    . "', interest_source = '" . $db->escapeString( $row['interest_source'] ) . "', personalize_content = " . (int)$row['personalize_content']
                    . ' WHERE contentobject_attribute_id = ' . (int)$row['contentobject_attribute_id'] . ' AND contentobject_attribute_version = ' . (int)$row['contentobject_attribute_version'] );
            CjwNewsletterConditions::$active = false;
            CjwNewsletterRenderingOperators::$baseUrl = '';
            CjwNewsletterRendering::clearCache();
            CjwNewsletterInterests::clearCache();
        }
        parent::tearDown();
    }

    // ------------------------------------------------------------------ helpers

    /** @return string a throwaway address of this suite's own domain */
    protected function newEmail( $label = '' )
    {
        static $n = 0;
        $email = 'n3test-' . getmypid() . '-' . ( ++$n ) . ( $label !== '' ? '-' . $label : '' ) . '@' . self::OWN_DOMAIN;
        $this->extraEmails[] = $email;
        return $email;
    }

    /** Sets rendering columns of the test list (every version), put back in tearDown. */
    protected function setList( array $values )
    {
        $db = eZDB::instance();
        $set = array();
        foreach ( $values as $column => $value )
            $set[] = $column . ' = ' . ( is_int( $value ) ? $value : "'" . $db->escapeString( $value ) . "'" );
        $db->query( 'UPDATE cjwnl_list SET ' . implode( ', ', $set ) . ' WHERE contentobject_id = ' . self::LIST_OBJECT_ID );
        eZContentObject::clearCache();
        CjwNewsletterRendering::clearCache();
    }

    /** @return CjwNewsletterList */
    protected function testList()
    {
        return CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
    }

    /** @return string a locale of the site other than the list's main language */
    protected function otherLanguage()
    {
        $main = CjwNewsletterRendering::mainLanguage( $this->testList() );
        foreach ( array( 'ger-DE', 'eng-GB', 'eng-US' ) as $locale )
            if ( $locale !== $main && eZContentLanguage::fetchByLocale( $locale ) )
                return $locale;
        $this->markTestSkipped( 'The site has only one language' );
    }

    protected function xml( $body )
    {
        return '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">' . $body . '</section>';
    }

    /** An edition with one article whose text has a condition for custom_1 = gold. @return eZContentObject */
    protected function conditionalEdition( $title )
    {
        $edition = $this->newEdition( $title, false );
        $map = $edition->dataMap();
        $map['description']->setAttribute( 'data_text', $this->xml( '<paragraph>Hello [[first_name]].</paragraph><paragraph><custom name="newsletter_condition" custom:field="custom_1" custom:value="gold"><paragraph>NLTEST-GOLD only for gold.</paragraph></custom></paragraph>' ) );
        $map['description']->store();
        $article = $this->newArticle( $edition, 'NLTEST body with <strong>bold</strong> and a list' );
        $amap = $article->dataMap();
        $amap['short_description']->setAttribute( 'data_text', $this->xml( '<paragraph>NLTEST article text &amp; more.</paragraph><header level="1">NLTEST sub heading</header><ul><li><paragraph>one</paragraph></li><li><paragraph>two</paragraph></li></ul><paragraph><custom name="newsletter_condition" custom:language="NLTESTLANG"><paragraph>NLTEST-LANG-ONLY</paragraph></custom></paragraph>' ) );
        $amap['short_description']->store();
        eZContentObject::clearCache();
        return eZContentObject::fetch( $edition->attribute( 'id' ) );
    }

    /**
     * The text fields of the newsletter classes are translatable: the 4.2.0 upgrade step (ext:cjw_newsletter:
     * translatable-fields), which an installation runs once; idempotent, so it stays as it is after the test.
     */
    protected function makeTranslatable()
    {
        CjwNewsletterTranslatableFields::apply();
        $this->assertTrue( CjwNewsletterTranslatableFields::isApplied() );
    }

    /** Publishes a translation of an object with the given texts. */
    protected function translate( eZContentObject $object, $locale, array $texts )
    {
        $initial = $object->attribute( 'initial_language_code' );
        $version = $object->createNewVersionIn( $locale, $initial );
        $map = array();
        foreach ( $version->contentObjectAttributes( $locale ) as $attribute )
            $map[$attribute->attribute( 'contentclass_attribute_identifier' )] = $attribute;
        foreach ( $texts as $identifier => $text )
        {
            $map[$identifier]->setAttribute( 'data_text', $text );
            $map[$identifier]->store();
        }
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => $version->attribute( 'version' ) ) );
        eZContentObject::clearCache();
    }

    /** @return array hash( html, text, subject ) of a mail file, the parts decoded */
    protected function mailParts( $file )
    {
        $raw = file_get_contents( $file );
        $out = array( 'html' => '', 'text' => '', 'subject' => '' );
        if ( preg_match( '/^Subject: (.*)$/mi', preg_replace( "/\r?\n[ \t]+/", ' ', $raw ), $m ) )
            $out['subject'] = iconv_mime_decode( trim( $m[1] ), 0, 'UTF-8' );
        $boundaries = preg_match_all( '/boundary="?([^";\r\n]+)"?/i', $raw, $bm ) ? $bm[1] : array();
        $pattern = $boundaries ? '/\r?\n--(?:' . implode( '|', array_map( function ( $b ) { return preg_quote( $b, '/' ); }, $boundaries ) ) . ')(?:--)?\r?\n/' : '/\r?\n\r?\n(?=Content-Type)/';
        foreach ( preg_split( $pattern, $raw ) as $part )
        {
            $split = preg_split( "/\r?\n\r?\n/", $part, 2 );
            if ( count( $split ) < 2 )
                continue;
            list( $head, $body ) = $split;
            $type = preg_match( '#Content-Type:\s*text/(html|plain)#i', $head, $t ) ? strtolower( $t[1] ) : '';
            if ( $type === '' )
                continue;
            if ( preg_match( '/Content-Transfer-Encoding:\s*quoted-printable/i', $head ) )
                $body = quoted_printable_decode( $body );
            else if ( preg_match( '/Content-Transfer-Encoding:\s*base64/i', $head ) )
                $body = base64_decode( $body );
            $out[$type === 'html' ? 'html' : 'text'] .= $body;
        }
        return $out;
    }

    /** @return string[] the mail files of the test that went to an address */
    protected function mailsTo( $email )
    {
        $out = array();
        foreach ( $this->outbox() as $file )
            if ( strpos( $this->mailText( $file ), $email ) !== false )
                $out[] = $file;
        return $out;
    }

    protected function newInterest( $identifier, $name, $listId = self::LIST_OBJECT_ID, $tagId = 0 )
    {
        $interest = CjwNewsletterInterest::create( array( 'list_contentobject_id' => $listId, 'identifier' => 'nltest_' . $identifier,
            'name' => $name, 'source' => 'topic', 'eztags_id' => $tagId, 'is_active' => 1, 'created' => time(), 'modified' => time() ) );
        $interest->store();
        $this->createdInterestIds[] = (int)$interest->attribute( 'id' );
        return $interest;
    }

    // ------------------------------------------------------------------ placeholders

    public function testPlaceholdersOfASubscriberAreEscapedInHtmlAndPlainInTheText()
    {
        $user = $this->newSubscriber( 'ph' );
        $user->setAttribute( 'first_name', self::NAME );
        $user->setAttribute( 'organisation', 'ACME & Co' );
        $user->setAttribute( 'custom_data_text_2', '<i>vip</i>' );
        $user->store();
        $values = CjwNewsletterPlaceholders::valuesForSubscriber( $user, self::LIST_OBJECT_ID, true );
        foreach ( array( '[[email]]', '[[organisation]]', '[[custom_1]]', '[[custom_4]]', '[[list_name]]', '[[unsubscribe_url]]', '[[configure_url]]', '[[manage_url]]' ) as $key )
            $this->assertArrayHasKey( $key, $values, $key );
        $this->assertSame( $user->attribute( 'email' ), $values['[[email]]'] );
        $this->assertSame( 'ACME & Co', $values['[[organisation]]'] );
        $this->assertStringContainsString( '/newsletter/unsubscribe/' . $this->subscriptionOf( $user )->attribute( 'hash' ), $values['[[unsubscribe_url]]'] );
        $this->assertSame( CjwNewsletterPlaceholders::listName( self::LIST_OBJECT_ID ), $values['[[list_name]]'] );
        $bodies = CjwNewsletterPlaceholders::replaceInBodies( array( 'html' => '<p>[[first_name]] [[organisation]] [[custom_2]]</p>', 'text' => '[[first_name]] [[custom_2]]' ), $values );
        $this->assertSame( '<p>' . self::NAME_ESCAPED . ' ACME &amp; Co &lt;i&gt;vip&lt;/i&gt;</p>', $bodies['html'] );
        $this->assertSame( self::NAME . ' <i>vip</i>', $bodies['text'] );
        $this->assertSame( 'Hi ' . self::NAME_ESCAPED, CjwNewsletterPlaceholders::replaceInText( 'Hi [[first_name]]', $values, true ) );

        $plain = CjwNewsletterPlaceholders::valuesForSubscriber( $user, self::LIST_OBJECT_ID, false );
        $this->assertArrayNotHasKey( '[[first_name]]', $plain, 'no personal fields without personalisation' );
        $this->assertArrayNotHasKey( '[[email]]', $plain );
        $this->assertArrayHasKey( '[[unsubscribe_url]]', $plain, 'the links are always there' );
    }

    public function testPlaceholdersFromTheSettings()
    {
        $user = $this->newSubscriber( 'phini' );
        $this->setIni( 'cjw_newsletter.ini', 'PlaceholderSettings', 'Placeholders', array( 'status_code' => 'status', 'secret' => 'hash' ) );
        $values = CjwNewsletterPlaceholders::valuesForSubscriber( $user, 0, true );
        $this->assertSame( (string)$user->attribute( 'status' ), $values['[[status_code]]'] );
        $this->assertArrayNotHasKey( '[[secret]]', $values, 'the hash of a subscriber is never a placeholder' );
        $this->assertContains( '[[status_code]]', CjwNewsletterPlaceholders::names() );
    }

    // ------------------------------------------------------------------ conditions

    public function testConditionsResolveInEveryMode()
    {
        CjwNewsletterConditions::$active = true;
        $user = $this->newSubscriber( 'cond' );
        $user->setAttribute( 'custom_data_text_1', 'Gold' );
        $user->setAttribute( 'first_name', 'Ann' );
        $text = 'A ' . CjwNewsletterConditions::open( array( 'field' => 'custom_1', 'value' => 'gold' ) ) . 'GOLD'
              . CjwNewsletterConditions::elseMarker() . 'OTHER' . CjwNewsletterConditions::close()
              . ' B ' . CjwNewsletterConditions::open( array( 'language' => 'eng-GB,ger-DE' ) ) . 'LANG '
              . CjwNewsletterConditions::open( array( 'field' => 'first_name', 'operator' => 'starts', 'value' => 'an' ) ) . 'NESTED' . CjwNewsletterConditions::close()
              . CjwNewsletterConditions::close() . ' C';
        $ctx = CjwNewsletterRendering::subscriberContext( $user, self::LIST_OBJECT_ID, 'ger-DE' );
        $this->assertSame( 'A GOLD B LANG NESTED C', CjwNewsletterConditions::resolve( $text, $ctx ) );
        $ctx['language'] = 'eng-US';
        $this->assertSame( 'A GOLD B  C', CjwNewsletterConditions::resolve( $text, $ctx ) );
        $this->assertSame( 'A GOLD B LANG NESTED C', CjwNewsletterConditions::resolve( $text, array( 'mode' => 'all' ) ), 'a preview shows every if-part' );
        $this->assertSame( 'A OTHER B LANG  C', CjwNewsletterConditions::resolve( $text, array( 'mode' => 'anonymous', 'language' => 'ger-DE' ) ),
            'without a subscriber a field condition is false' );

        $list = CjwNewsletterConditions::wrap( array( 'list' => '1,' . self::LIST_OBJECT_ID, 'negate' => 'yes' ), 'NOT-FOR-LIST' );
        $this->assertSame( '', CjwNewsletterConditions::resolve( $list, $ctx ) );
        $this->assertSame( 'NOT-FOR-LIST', CjwNewsletterConditions::resolve( $list, array( 'mode' => 'subscriber', 'user' => $user, 'list_id' => 2 ) ) );
        $damaged = '[[cjwnl:if:abcdef123456:@@]]X[[cjwnl:endif:abcdef123456]] [[cjwnl:else:abcdef999999]]Y';
        $this->assertSame( 'X Y', CjwNewsletterConditions::resolve( $damaged, $ctx ), 'damaged settings: always; stray markers removed' );
        $this->assertSame( 'contains', CjwNewsletterConditions::normalize( array( 'operator' => 'CONTAINS' ) )['operator'] );
        $this->assertSame( array(), CjwNewsletterConditions::normalize( array( 'field' => 'password', 'operator' => 'exec' ) ), 'unknown fields and operators are dropped' );
    }

    public function testConditionTagsLeaveNoMarkersOutsideANewsletter()
    {
        CjwNewsletterConditions::$active = false;
        $this->assertSame( 'X', CjwNewsletterConditions::wrap( array( 'field' => 'email' ), 'X' ), 'on the web site the content stays as it is' );
        $this->assertSame( '', CjwNewsletterConditions::open( array( 'field' => 'email' ) ) );
    }

    // ------------------------------------------------------------------ rich text and plain text

    public function testRichTextBecomesTextAndHtmlWithTheSameConditions()
    {
        CjwNewsletterConditions::$active = true;
        $rich = '<?xml version="1.0" encoding="UTF-8"?><section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" version="5.0-variant ezpublish-1.0">'
              . '<title ezxhtml:level="2">Hello &amp; &lt;welcome&gt;</title><para>Text <emphasis role="strong">bold</emphasis> <link xlink:href="https://example.invalid/a?b=1&amp;c=2">a link</link>.</para>'
              . '<orderedlist><listitem><para>one</para></listitem><listitem><para>two</para></listitem></orderedlist>'
              . '<informaltable><tbody><tr><th>Name</th><th>Price</th></tr><tr><td>Apple</td><td>1.20</td></tr></tbody></informaltable>'
              . '<eztemplate name="newsletter_condition"><ezcontent><para>GOLD-ONLY</para></ezcontent><ezconfig><ezvalue key="field">custom_1</ezvalue><ezvalue key="value">gold</ezvalue></ezconfig></eztemplate>'
              . '<para><link xlink:href="javascript:alert(1)">bad</link></para></section>';
        $text = CjwNewsletterRichText::toText( $rich );
        $html = CjwNewsletterRichText::toHtml( $rich );
        $this->assertStringContainsString( "Hello & <welcome>\n---", $text );
        $this->assertStringContainsString( '*bold* a link (https://example.invalid/a?b=1&c=2).', $text );
        $this->assertStringContainsString( "1. one\n2. two", $text );
        $this->assertStringContainsString( "Name  | Price\n------+------\nApple | 1.20", $text );
        $this->assertStringNotContainsString( 'javascript', $text . $html );
        $this->assertStringContainsString( 'Hello &amp; &lt;welcome&gt;', $html );
        $this->assertStringContainsString( 'href="https://example.invalid/a?b=1&amp;c=2"', $html );
        $this->assertStringContainsString( '<ol ', $html );
        $gold = $this->newSubscriber( 'rtg' );
        $gold->setAttribute( 'custom_data_text_1', 'gold' );
        $other = $this->newSubscriber( 'rto' );
        foreach ( array( $text, $html ) as $part )
        {
            $this->assertStringContainsString( 'GOLD-ONLY', CjwNewsletterConditions::resolve( $part, CjwNewsletterRendering::subscriberContext( $gold, 0, '' ) ) );
            $this->assertStringNotContainsString( 'GOLD-ONLY', CjwNewsletterConditions::resolve( $part, CjwNewsletterRendering::subscriberContext( $other, 0, '' ) ) );
        }
        $this->assertTrue( CjwNewsletterRichText::isRichText( $rich ) );
        $this->assertSame( '', CjwNewsletterRichText::toText( '<not xml' ) );
    }

    public function testPlainTextViewsOfXmlText()
    {
        CjwNewsletterConditions::$active = true;
        $edition = $this->conditionalEdition( 'NLTEST plain' );
        $article = null;
        foreach ( eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => 1, 'DepthOperator' => 'eq' ), $edition->attribute( 'main_node_id' ) ) as $node )
            $article = $node->attribute( 'object' );
        $this->assertNotNull( $article );
        $map = $article->dataMap();
        $text = CjwNewsletterPlainText::xmlText( $map['short_description'] );
        $this->assertStringContainsString( 'NLTEST article text & more.', $text, 'text is not escaped' );
        $this->assertMatchesRegularExpression( "/NLTEST sub heading\n[=-]{3,}/", $text, 'a heading is underlined' );
        $this->assertStringContainsString( "- one\n- two", $text );
        $this->assertStringContainsString( '[[cjwnl:if:', $text, 'the condition tag leaves its markers while a newsletter is rendered' );
        $this->assertStringNotContainsString( '<', $text );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'attribute', $map['title'] );
        $this->assertSame( 'NLTEST article title', trim( $tpl->fetch( 'design:newsletter/rendering/plaintext_attribute.tpl' ) ) );
        $this->assertSame( 'design:content/datatype/view/plaintext/ezxmltext.tpl', CjwNewsletterRenderingOperators::plainTextTemplate( 'ezxmltext' ) );
        $this->assertFalse( CjwNewsletterRenderingOperators::plainTextTemplate( '../../x' ) );
        $this->assertSame( "abc def\nghi", CjwNewsletterPlainText::wrap( 'abc def ghi', 8 ) );
    }

    // ------------------------------------------------------------------ skins

    public function testTheNewSkinsRenderTableHtmlAndARealTextPart()
    {
        $edition = $this->conditionalEdition( 'NLTEST skins' );
        foreach ( array( 'company', 'news', 'shop' ) as $skin )
        {
            $this->assertTrue( CjwNewsletterRenderingHooks::skinTemplateExists( $skin ), $skin );
            $settings = CjwNewsletterRendering::skinSettings( $skin );
            $this->assertSame( 'plain', $settings['text_format'], $skin );
            $html = CjwNewsletterEdition::getOutputRaw( $edition->attribute( 'id' ), 1, 0, 'site', $skin, 0 );
            $text = CjwNewsletterEdition::getOutputRaw( $edition->attribute( 'id' ), 1, 1, 'site', $skin, 0 );
            $this->assertSame( array(), $html['template_errors'], $skin . ' html template errors' );
            $this->assertStringContainsString( 'NLTEST skins', $html['subject'], $skin );
            $h = $html['body']['html'];
            $this->assertStringContainsString( '<table role="presentation"', $h, $skin );
            $this->assertStringContainsString( 'width="600"', $h, $skin );
            $this->assertStringNotContainsString( '<style', $h, $skin . ' styles are inline' );
            $this->assertStringContainsString( 'NLTEST article title', $h, $skin );
            $this->assertStringContainsString( 'href="[[unsubscribe_url]]"', $h, $skin );
            $this->assertStringContainsString( '[[cjwnl:if:', $h, $skin . ' keeps the condition markers in the stored output' );
            $this->assertMatchesRegularExpression( '#src="https?://[^"]+/newsletter/skin/' . $skin . '/logo\.png"#', $h, $skin . ' logo with an absolute address' );
            foreach ( array( $html['body']['text'], $text['body']['text'] ) as $t )
            {
                $this->assertStringNotContainsString( '<p', $t, $skin . ' text part has no markup' );
                $this->assertStringContainsString( 'NLTEST article text & more.', $t, $skin );
                $this->assertStringContainsString( '[[unsubscribe_url]]', $t, $skin );
                $this->assertMatchesRegularExpression( "/- one\n- two/", $t, $skin . ' list as text' );
            }
            $this->assertSame( '', $text['body']['html'], $skin . ' the text format has no HTML part' );
        }
        $preview = CjwNewsletterEdition::getOutput( $edition->attribute( 'id' ), 1, 0, 'site', 'company', 0 );
        $this->assertStringNotContainsString( '[[cjwnl:', $preview['body']['html'], 'a preview resolves the markers' );
        $this->assertStringContainsString( 'NLTEST-GOLD', $preview['body']['html'], 'a preview shows every conditional part' );
        $this->assertSame( array( 'default', 'company', 'news', 'shop' ), array_values( array_intersect( array( 'default', 'company', 'news', 'shop' ), CjwNewsletterRendering::availableSkins() ) ) );
    }

    public function testAllowedSkinsOfAListAndTheSendForm()
    {
        $this->setList( array( 'skin_name' => 'company', 'skin_name_array_string' => ';news;' ) );
        $this->assertSame( array( 'company', 'news' ), CjwNewsletterRendering::allowedSkins( $this->testList() ), 'the list\'s own skin is always allowed' );
        $edition = $this->newEdition( 'NLTEST skin choice' );
        $version = $edition->currentVersion();
        $_POST = array( 'CjwNewsletterRendering_SkinName' => 'shop' );
        $errors = CjwNewsletterRenderingHooks::sendFormValidate( eZHTTPTool::instance(), $version );
        $this->assertCount( 1, $errors, 'a skin the list does not allow is refused' );
        $_POST = array( 'CjwNewsletterRendering_SkinName' => 'news' );
        $this->assertSame( array(), CjwNewsletterRenderingHooks::sendFormValidate( eZHTTPTool::instance(), $version ) );
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() + 3600 );
        CjwNewsletterRenderingHooks::sendFormStored( $send, eZHTTPTool::instance(), $version );
        $send = CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
        $this->assertSame( 'news', $send->attribute( 'skin_name' ) );
        $this->assertStringContainsString( 'skin/news/logo.png', (string)$send->attribute( 'output_xml' ), 'the output was made again in the chosen skin' );
        $_POST = array();
    }

    public function testListAttributePartStoresSkinsLanguagesAndInterests()
    {
        $list = $this->testList();
        $prefix = 'ContentObjectAttribute_CjwNewsletterList_';
        $postfix = '_' . $list->attribute( 'contentobject_attribute_id' );
        $other = $this->otherLanguage();
        $_POST = array( $prefix . 'RenderingPart' . $postfix => '1', $prefix . 'SkinNameArray' . $postfix => array( 'company', 'nosuchskin' ),
                        $prefix . 'LanguageArray' . $postfix => array( $other, 'xxx-XX' ), $prefix . 'MainLanguage' . $postfix => 'eng-GB',
                        $prefix . 'InterestSource' . $postfix => 'topics' );
        $copy = clone $list;
        $copy->setAttribute( 'skin_name', 'company' );
        $errors = CjwNewsletterRenderingHooks::listAttributeInput( $copy, eZHTTPTool::instance(), $prefix, $postfix, null );
        $this->assertSame( array(), $errors );
        $this->assertSame( ';company;', $copy->attribute( 'skin_name_array_string' ) );
        $this->assertSame( 'eng-GB', $copy->attribute( 'main_language' ) );
        $this->assertStringContainsString( ';' . $other . ';', $copy->attribute( 'language_array_string' ) );
        $this->assertStringNotContainsString( 'xxx-XX', $copy->attribute( 'language_array_string' ) );
        $this->assertStringContainsString( ';eng-GB;', $copy->attribute( 'language_array_string' ), 'the main language is one of the languages' );
        $this->assertSame( 'topics', $copy->attribute( 'interest_source' ) );

        $_POST[$prefix . 'MainLanguage' . $postfix] = 'xxx-XX';
        $copy->setAttribute( 'skin_name', 'shop' );
        $this->assertCount( 2, CjwNewsletterRenderingHooks::listAttributeInput( $copy, eZHTTPTool::instance(), $prefix, $postfix, null ),
            'an unknown main language and a list skin that is not allowed' );
        $before = clone $list;
        $_POST = array();
        $this->assertSame( array(), CjwNewsletterRenderingHooks::listAttributeInput( $before, eZHTTPTool::instance(), $prefix, $postfix, null ) );
        $this->assertSame( $list->attribute( 'skin_name_array_string' ), $before->attribute( 'skin_name_array_string' ), 'without the part nothing changes' );
    }

    // ------------------------------------------------------------------ the send: language, conditions, placeholders

    public function testASendGivesEverySubscriberHisLanguageConditionsAndEscapedPlaceholders()
    {
        $main = CjwNewsletterRendering::mainLanguage( $this->testList() );
        $other = $this->otherLanguage();
        $this->setList( array( 'language_array_string' => ';' . $main . ';' . $other . ';', 'personalize_content' => 1, 'skin_name_array_string' => '' ) );
        $this->makeTranslatable();

        $edition = $this->conditionalEdition( 'NLTEST main title' );
        $this->translate( $edition, $other, array( 'title' => 'NLTEST other title', 'short_title' => 'NLTEST other',
            'description' => $this->xml( '<paragraph>OTHER-LANGUAGE intro [[first_name]].</paragraph>' ) ) );

        $translated = $this->newSubscriber( 'lang' );
        $translated->setAttribute( 'language', $other );
        $translated->setAttribute( 'first_name', self::NAME );
        $translated->store();
        $gold = $this->newSubscriber( 'gold' );
        $gold->setAttribute( 'custom_data_text_1', 'gold' );
        $gold->setAttribute( 'language', 'xxx-XX' );
        $gold->store();

        $nodeId = (int)$edition->attribute( 'main_node_id' );
        $r = $this->runView( 'send', array( $nodeId ), array(
            'SendNewsletterButton' => 'Send', 'SendOutConfirmationInput' => '1', 'CjwNewsletterRendering_SkinName' => 'company',
            'CJWNL_datetime_year_noid' => date( 'Y', time() - 3600 ), 'CJWNL_datetime_month_noid' => date( 'n', time() - 3600 ),
            'CJWNL_datetime_day_noid' => date( 'j', time() - 3600 ), 'CJWNL_datetime_hour_noid' => date( 'G', time() - 3600 ),
            'CJWNL_datetime_minute_noid' => date( 'i', time() - 3600 ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'], 'the send was made' );
        $sends = CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) );
        $this->assertCount( 1, $sends );
        $this->assertSame( 'company', $sends[0]->attribute( 'skin_name' ), 'the skin chosen in the send form' );

        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $output = CjwNewsletterEditionSendOutput::fetchByEditionSendIdAndLanguage( $sends[0]->attribute( 'id' ), $other );
        $this->assertNotNull( $output, 'the other language has an output of its own' );
        $this->assertStringContainsString( 'NLTEST other title', (string)$output->attribute( 'output_xml' ) );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );

        $mails = $this->mailsTo( $translated->attribute( 'email' ) );
        $this->assertCount( 1, $mails );
        $m = $this->mailParts( $mails[0] );
        $this->assertStringContainsString( 'NLTEST other title', $m['subject'], 'the subject in his language' );
        $this->assertStringContainsString( 'OTHER-LANGUAGE intro ' . self::NAME_ESCAPED, $m['html'], 'his language, the name escaped in HTML' );
        $this->assertStringContainsString( 'OTHER-LANGUAGE intro ' . self::NAME, $m['text'], 'and plain in the text part' );
        $this->assertStringNotContainsString( 'NLTEST-GOLD', $m['html'] . $m['text'], 'not gold: the condition leaves it out in both parts' );
        $this->assertStringNotContainsString( '[[', $m['html'] . $m['text'], 'every marker and placeholder is resolved' );
        $this->assertStringContainsString( '/newsletter/unsubscribe/' . $this->subscriptionOf( $translated )->attribute( 'hash' ), $m['html'] );
        if ( $other === 'ger-DE' )
            $this->assertStringContainsString( 'Newsletter-Einstellungen', $m['text'], 'the texts of the skin in his language' );

        $mails = $this->mailsTo( $gold->attribute( 'email' ) );
        $this->assertCount( 1, $mails );
        $m = $this->mailParts( $mails[0] );
        $this->assertStringContainsString( 'NLTEST main title', $m['subject'], 'a language the list does not offer: the main language' );
        $this->assertStringContainsString( 'NLTEST-GOLD only for gold.', $m['html'] );
        $this->assertStringContainsString( 'NLTEST-GOLD only for gold.', $m['text'], 'the same condition in the text part' );
        $this->assertStringNotContainsString( 'NLTEST-LANG-ONLY', $m['text'] );

        $items = eZPersistentObject::fetchObjectList( CjwNewsletterEditionSendItem::definition(), null, array( 'edition_send_id' => (int)$sends[0]->attribute( 'id' ) ), null, null, true );
        $languages = array();
        foreach ( $items as $item )
            $languages[(int)$item->attribute( 'newsletter_user_id' )] = (string)$item->attribute( 'language' );
        $this->assertSame( $other, $languages[(int)$translated->attribute( 'id' )], 'the item remembers its language' );
        $this->assertSame( $main, $languages[(int)$gold->attribute( 'id' )] );

        // the preview as a subscriber shows the same mail
        $mail = CjwNewsletterRendering::mailFor( CjwNewsletterEditionSend::fetch( $sends[0]->attribute( 'id' ) ), $translated );
        $this->assertSame( $other, $mail['language'] );
        $this->assertStringContainsString( 'OTHER-LANGUAGE intro ' . self::NAME_ESCAPED, $mail['html'] );
        $before = count( $this->outbox() );
        $r = $this->runView( 'preview_as', array( $nodeId, (int)$gold->attribute( 'id' ), 1 ) );
        $this->assertViewOk( $r, 'preview_as' );
        $this->assertStringContainsString( 'NLTEST-GOLD only for gold.', $r['content'] );
        $this->assertCount( $before, $this->outbox(), 'the preview sends nothing' );
    }

    public function testTheUpgradeStepMakesOnlyTheTextFieldsTranslatable()
    {
        $edition = eZContentClass::fetchByIdentifier( 'cjw_newsletter_edition' );
        $settingsBefore = null;
        foreach ( $edition->fetchAttributes() as $attribute )
            if ( $attribute->attribute( 'identifier' ) === 'newsletter_edition' )
                $settingsBefore = (int)$attribute->attribute( 'can_translate' );
        CjwNewsletterTranslatableFields::apply();
        $again = CjwNewsletterTranslatableFields::apply();
        $this->assertCount( 6, $again );
        foreach ( $again as $row )
            $this->assertSame( 'already', $row['result'], $row['class'] . '/' . $row['attribute'] . ' is idempotent' );
        $this->assertTrue( CjwNewsletterTranslatableFields::isApplied() );
        eZContentClassAttribute::expireCache();
        foreach ( eZContentClass::fetchByIdentifier( 'cjw_newsletter_edition' )->fetchAttributes() as $attribute )
            if ( $attribute->attribute( 'identifier' ) === 'newsletter_edition' )
                $this->assertSame( $settingsBefore, (int)$attribute->attribute( 'can_translate' ), 'the settings field is left as it was' );
    }

    public function testAnSmsSendIsLeftAlone()
    {
        $edition = $this->newEdition( 'NLTEST sms' );
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() + 3600 );
        if ( !$send->hasAttribute( 'channel' ) )
            $this->markTestSkipped( 'no channel column' );
        $send->setAttribute( 'channel', 'sms' );
        $message = new ArrayObject( array( 'subject' => 'S', 'bodies' => array( 'html' => 'H', 'text' => 'T' ), 'values' => array(), 'defer' => false, 'abort' => '' ) );
        CjwNewsletterRenderingHooks::itemBeforeSend( $message, new CjwNewsletterEditionSendItem( array( 'output_format_id' => 0 ) ), $send, $this->newSubscriber( 'sms' ) );
        $this->assertSame( 'H', $message['bodies']['html'] );
        $this->assertTrue( CjwNewsletterRendering::isOtherChannel( $send ) );
    }

    // ------------------------------------------------------------------ interests

    public function testInterestsOnThePreferencePageAndTheKernelHook()
    {
        $this->setList( array( 'interest_source' => 'topics' ) );
        $sport = $this->newInterest( 'sport', 'NLTEST Sport' );
        $music = $this->newInterest( 'music', 'NLTEST Music' );
        $hidden = $this->newInterest( 'hidden', 'NLTEST Hidden' );
        $hidden->setAttribute( 'is_active', 0 );
        $hidden->store();
        $user = $this->newSubscriber( 'int' );
        $recipient = expMailRecipient::fromAddress( $user->attribute( 'email' ), false );
        $category = expMailCategoryRegistry::instance()->get( 'newsletter' );
        $this->assertNotNull( $category );
        $handler = $category->handler();
        $this->assertInstanceOf( 'CjwNewsletterMailCategoryHandler', $handler );
        $this->assertSame( 'design:mailpreferences/category/newsletter.tpl', $handler->partTemplate( $recipient, $category, 'token' ) );
        $vars = $handler->partVariables( $recipient, $category, 'token' );
        $this->assertTrue( $vars['has_interests'] );
        $names = array();
        foreach ( $vars['lists'] as $list )
            foreach ( $list['interests'] as $interest )
                $names[] = $interest['name'];
        $this->assertContains( 'NLTEST Sport', $names );
        $this->assertNotContains( 'NLTEST Hidden', $names, 'an interest that is not offered is not shown' );

        // the kernel page: the part is in the category's row and is stored with the form
        $prefs = expMailPreferences::forRecipient( $recipient );
        $page = \Exponential\Service\MailPreferencesPage::templateVariables( $prefs, 'token', 'mailpreferences/manage/x', false );
        $row = null;
        foreach ( $page['categories'] as $c )
            if ( $c['identifier'] === 'newsletter' )
                $row = $c;
        $this->assertSame( 'design:mailpreferences/category/newsletter.tpl', $row['part'] );
        $this->assertSame( 'newsletter', $row['part_variables']['key'] );

        $_POST = array( 'MailPreferencesForm' => 'categories', 'CategoryShown' => array( 'newsletter' ),
                        'Category' => $prefs->state( 'newsletter' ) !== expMailPreferences::OFF ? array( 'newsletter' => '1' ) : array(),
                        'MailPreferencePart' => array( 'newsletter' => array( 'PartShown' => '1', 'Interest' => array( (string)$sport->attribute( 'id' ), (string)$hidden->attribute( 'id' ) ) ) ) );
        $notices = \Exponential\Service\MailPreferencesPage::handlePost( $prefs, eZHTTPTool::instance(), 'link' );
        $this->assertSame( 'success', $notices[0]['type'], 'a change of the part alone is a change: ' . json_encode( $notices ) );
        $this->assertSame( array( (int)$sport->attribute( 'id' ) ), CjwNewsletterInterests::idsForUser( $user->attribute( 'id' ) ), 'only offered interests are stored' );
        $this->assertContains( 'nltest_sport', CjwNewsletterInterests::keysForUser( $user->attribute( 'id' ) ) );

        $_POST['MailPreferencePart']['newsletter']['Interest'] = array( (string)$music->attribute( 'id' ) );
        $_POST['MailPreferencePart']['newsletter']['Language'] = 'xxx-XX';
        $notices = \Exponential\Service\MailPreferencesPage::handlePost( $prefs, eZHTTPTool::instance(), 'link' );
        $this->assertSame( 'error', $notices[0]['type'], 'a language that is not offered is an error' );
        $this->assertSame( array( (int)$music->attribute( 'id' ) ), CjwNewsletterInterests::idsForUser( $user->attribute( 'id' ) ), 'the interests were still stored' );
        $_POST = array();

        // erasure takes the subscriber with his interests, his language and his subscriptions
        $user->setAttribute( 'language', 'eng-GB' );
        $user->store();
        $handler->erased( $recipient, expConsentContext::system( 'test' ) );
        $this->assertSame( array(), CjwNewsletterInterests::idsForUser( $user->attribute( 'id' ) ) );
        $this->assertFalse( (bool)CjwNewsletterUser::fetch( $user->attribute( 'id' ) ), 'the newsletter user is gone' );
        $this->assertFalse( (bool)$this->subscriptionOf( $user ), 'and his subscription' );
    }

    public function testInterestsBlockIsEmptyWithoutInterestsAndRendersArticles()
    {
        $user = $this->newSubscriber( 'blk' );
        $marker = CjwNewsletterInterestBlock::marker( 3, 'For <you>', '#123456' );
        $this->assertTrue( CjwNewsletterInterestBlock::hasMarker( $marker ) );
        $this->assertSame( 'a  b', CjwNewsletterInterestBlock::resolve( 'a ' . $marker . ' b', true, $user, array( 'list_id' => self::LIST_OBJECT_ID ) ),
            'no interests: no block, not even its heading' );
        $this->assertSame( 'a  b', CjwNewsletterInterestBlock::resolve( 'a ' . $marker . ' b', false, null, array() ), 'no subscriber: nothing' );
        $edition = $this->newEdition( 'NLTEST block' );
        $node = null;
        foreach ( eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => 1, 'DepthOperator' => 'eq' ), $edition->attribute( 'main_node_id' ) ) as $child )
            $node = $child;
        $html = CjwNewsletterInterestBlock::renderHtml( array( $node ), 'For <you>', '#123456' );
        $this->assertStringContainsString( 'For &lt;you&gt;', $html );
        $this->assertStringContainsString( 'NLTEST article title', $html );
        $this->assertStringContainsString( 'border-bottom:2px solid #123456', $html );
        $text = CjwNewsletterInterestBlock::renderText( array( $node ), 'For <you>' );
        $this->assertStringContainsString( "For <you>\n---------", $text );
        $this->assertStringContainsString( '* NLTEST article title', $text );
        $this->assertStringContainsString( 'NLTEST article body line', $text );
        $this->assertContains( (int)$node->attribute( 'contentobject_id' ), CjwNewsletterInterestBlock::editionArticleObjectIds( $edition->attribute( 'id' ) ) );
    }

    public function testInterestAdminViews()
    {
        $r = $this->runView( 'interest_edit', array( 0 ), array( 'StoreButton' => 'Save', 'Interest_name' => 'NLTEST View Interest',
            'Interest_identifier' => 'nltest_view', 'Interest_list_contentobject_id' => (string)self::LIST_OBJECT_ID, 'Interest_source' => 'topic',
            'Interest_priority' => '3', 'Interest_is_active' => '1' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $interest = CjwNewsletterInterest::fetchByListContentobjectIdAndIdentifier( self::LIST_OBJECT_ID, 'nltest_view' );
        $this->assertNotNull( $interest );
        $this->createdInterestIds[] = (int)$interest->attribute( 'id' );
        $r = $this->runView( 'interest_edit', array( 0 ), array( 'StoreButton' => 'Save', 'Interest_name' => 'Again',
            'Interest_identifier' => 'nltest_view', 'Interest_list_contentobject_id' => (string)self::LIST_OBJECT_ID ) );
        $this->assertViewOk( $r, 'a duplicate identifier' );
        $this->assertStringContainsString( 'message-error', $r['content'] );
        $r = $this->runView( 'interest_list' );
        $this->assertViewOk( $r, 'interest_list' );
        $this->assertStringContainsString( 'NLTEST View Interest', $r['content'] );
        $r = $this->runView( 'interest_list', array(), array( 'RemoveButton' => array( $interest->attribute( 'id' ) => 'Remove' ) ) );
        $this->assertStringContainsString( 'nl-confirm', $r['content'] );
        $r = $this->runView( 'interest_list', array(), array( 'ConfirmRemoveButton' => array( $interest->attribute( 'id' ) => 'Yes' ) ) );
        $this->assertNull( CjwNewsletterInterest::fetch( $interest->attribute( 'id' ) ) );
        $this->assertViewClean( $this->runView( 'interest_edit', array( 999999999 ) ), 'a missing interest' );
    }

    public function testSkinPreviewView()
    {
        $edition = $this->newEdition( 'NLTEST skin preview' );
        $r = $this->runView( 'skin_preview' );
        $this->assertViewOk( $r, 'skin_preview' );
        foreach ( array( 'company', 'news', 'shop' ) as $skin )
            $this->assertStringContainsString( 'newsletter/skin_preview/' . $skin, $r['content'] );
        $r = $this->runView( 'skin_preview', array( 'shop', 'text' ), array(), array( 'edition' => $edition->attribute( 'main_node_id' ) ) );
        $this->assertViewOk( $r, 'skin_preview shop text' );
        $this->assertStringContainsString( 'NLTEST article title', $r['content'] );
    }

    // ------------------------------------------------------------------ notification card and dashboard

    public function testNotificationCardAndDashboardBlock()
    {
        if ( !class_exists( 'CjwNewsletterHandler' ) )
            $this->markTestSkipped( 'notification handler not loaded' );
        $handler = new CjwNewsletterHandler();
        $this->assertSame( 'cjwnewsletter', $handler->attribute( 'id_string' ) );
        $this->assertSame( eZNotificationEventHandler::EVENT_SKIPPED, $handler->handle( null ) );
        $this->assertContains( 'design:notification/handler/cjwnewsletter/parts/lists.tpl', $handler->attribute( 'parts' ) );
        $card = $handler->attribute( 'card' );
        $this->assertTrue( $card['available'], 'the signed-in administrator gets the card' );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'handler', $handler );
        $html = $tpl->fetch( 'design:notification/handler/cjwnewsletter/settings/edit.tpl' );
        $this->assertStringContainsString( 'mailpreferences/settings', $html );
        $summary = CjwNewsletterRenderingHooks::dashboardSummary( array() );
        $this->assertContains( 'company', $summary['skins'] );
        $this->assertArrayHasKey( 'lists', $summary );
    }

    public function testSubscriberFieldPointsAreCalled()
    {
        // the points of the user edit and subscribe forms find the parts of the settings
        $this->setIni( 'cjw_newsletter.ini', 'ExtensionPointSettings', 'UserEditParts', array( 'design:newsletter/rendering/list_view_part.tpl' ) );
        $this->assertSame( array( 'design:newsletter/rendering/list_view_part.tpl' ), CjwNewsletterExtensionPoints::templates( 'UserEditParts' ) );
        $this->setIni( 'cjw_newsletter.ini', 'ExtensionPointSettings', 'UserEditParts', array() );
        $user = $this->newSubscriber( 'ue' );
        $r = $this->runView( 'user_edit', array( $user->attribute( 'id' ) ) );
        $this->assertViewOk( $r, 'user_edit' );
        $r = $this->runView( 'user_view', array( $user->attribute( 'id' ) ) );
        $this->assertViewOk( $r, 'user_view' );
    }

    public function testThePreferencePageNamesEachLanguageInItsOwnName()
    {
        $content = CjwNewsletterRendering::contentLanguages();
        $native = CjwNewsletterRendering::nativeContentLanguages();
        $this->assertSame( array_keys( $content ), array_keys( $native ), 'the same languages, in the same order' );
        if ( isset( $native['ger-DE'] ) )
        {
            $this->assertSame( 'Deutsch (Deutschland)', $native['ger-DE'] );
        }
        foreach ( $native as $locale => $name )
        {
            $this->assertNotSame( '', $name, $locale . ' has a name' );
        }
    }
}
