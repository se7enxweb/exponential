<?php
/**
 * Stand-ins shared by the XML text tests in this directory:
 *
 *  - eZXMLTextTestInputParser: the simplified XML input parser with the registration of external links in the
 *    ezurl table replaced by a counter, so external links can be parsed without a database.
 *  - eZXMLTextTestCase: the base class. It puts a schema built from fixed settings in place of the shared one
 *    (custom tags, classes and custom attributes of this file, not of the installation's content.ini, which
 *    differs between sites and CI), and keeps the no-database guard of the datatype tests.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once dirname( __DIR__ ) . '/datatypes/eZDatatypeTestFixtures.php';

class eZXMLTextTestInputParser extends eZSimplifiedXMLInputParser
{
    /** @var string[] the external URLs that would have been registered, by the id given out for each */
    public $registeredURLs = array();

    function convertHrefToID( $href )
    {
        $href = str_replace( "&amp;", "&", $href );
        $urlID = array_search( $href, $this->registeredURLs, true );
        if ( $urlID === false )
        {
            $urlID = 9000 + count( $this->registeredURLs );
            $this->registeredURLs[$urlID] = $href;
        }
        if ( !in_array( $urlID, $this->urlIDArray ) )
            $this->urlIDArray[] = $urlID;
        return $urlID;
    }
}

abstract class eZXMLTextTestCase extends eZDatatypeTestCase
{
    private $savedSchema;
    private $hadSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadSchema = array_key_exists( 'eZXMLSchemaGlobalInstance', $GLOBALS );
        $this->savedSchema = $this->hadSchema ? $GLOBALS['eZXMLSchemaGlobalInstance'] : null;
        $GLOBALS['eZXMLSchemaGlobalInstance'] = self::fixedSchema();
    }

    protected function tearDown(): void
    {
        if ( $this->hadSchema )
            $GLOBALS['eZXMLSchemaGlobalInstance'] = $this->savedSchema;
        else
            unset( $GLOBALS['eZXMLSchemaGlobalInstance'] );
        parent::tearDown();
    }

    /**
     * The schema with the settings the tests are written for.
     *
     * @return eZXMLSchema
     */
    protected static function fixedSchema()
    {
        $schema = new eZXMLSchema();
        foreach ( array_keys( $schema->Schema ) as $tagName )
        {
            $schema->Schema[$tagName]['classesList'] = array();
            if ( $tagName !== 'custom' )
                $schema->Schema[$tagName]['customAttributes'] = array();
        }
        $schema->Schema['custom']['tagList'] = array( 'factbox', 'quote', 'strike', 'sup' );
        $schema->Schema['custom']['isInline'] = array( 'strike' => 'true', 'sup' => 'true', 'quote' => 'false' );
        $schema->Schema['custom']['customAttributes'] = array( 'factbox' => array( 'title' ), 'quote' => array( 'author' ),
                                                               'strike' => array(), 'sup' => array() );
        $schema->Schema['paragraph']['classesList'] = array( 'lead' );
        $schema->Schema['literal']['classesList'] = array( 'html' );
        $schema->Schema['table']['classesList'] = array( 'list', 'cols' );
        $schema->Schema['embed']['classesList'] = array( 'itemized' );
        $schema->Schema['paragraph']['customAttributes'] = array( 'note' );
        $schema->Schema['paragraph']['childrenRequired'] = true;
        return $schema;
    }

    /**
     * A parser with fixed settings (trimmed spaces, no multiple spaces, no numeric entities, no strict headers).
     *
     * @return eZXMLTextTestInputParser
     */
    protected function parser( $contentObjectID = 5, $parseLineBreaks = false, $validateErrorLevel = eZXMLInputParser::ERROR_NONE, $removeDefaultAttrs = false )
    {
        $parser = new eZXMLTextTestInputParser( $contentObjectID, $validateErrorLevel, eZXMLInputParser::ERROR_ALL, $parseLineBreaks, $removeDefaultAttrs );
        $parser->TrimSpaces = true;
        $parser->AllowMultipleSpaces = false;
        $parser->AllowNumericEntities = false;
        $parser->StrictHeaders = false;
        return $parser;
    }

    /**
     * The children of the root section as XML, without the namespace declarations libxml repeats on them.
     */
    protected static function body( $document )
    {
        if ( !$document instanceof DOMDocument )
            return false;
        $xml = '';
        foreach ( $document->documentElement->childNodes as $child )
            $xml .= $document->saveXML( $child );
        return preg_replace( '# xmlns:(tmp|image|xhtml|custom)="[^"]*"#', '', $xml );
    }

    /**
     * Parses the input and returns array( body XML, messages, valid ).
     */
    protected function parse( $input, $parser = null )
    {
        $parser = $parser ?: $this->parser();
        $document = $parser->process( $input );
        return array( self::body( $document ), $parser->getMessages(), $parser->isValid() );
    }
}
