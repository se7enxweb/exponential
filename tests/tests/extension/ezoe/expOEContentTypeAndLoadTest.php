<?php
/**
 * The content type of the upload and embed dialogs and the EmbedID of ezoe/load, without the database:
 * Exponential\View\Extension\Ezoe\Ezoe\Dialog::contentTypes() and isContentType(), Relations::parseEmbedId().
 *
 *  CT-01 - The content types are the shipped ones (objects, images, files) and the relation groups of content.ini
 *  CT-02 - Anything else, a path, a dot, another case or "auto" itself, is no content type
 *  CT-03 - ezoe/upload and ezoe/relations check the content type before it goes into the path of a template;
 *          ezoe/relations checks it after "auto" was resolved
 *  LO-01 - ezoe/load reads the EmbedID through Relations::parseEmbedId() and starts without an embedded object
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/dialog.php';
require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/relations.php';

use Exponential\View\Extension\Ezoe\Ezoe\Dialog;

class expOEContentTypeAndLoadTest extends PHPUnit\Framework\TestCase
{
    private $groups;
    private $hadGroups;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'content.ini' );
        $this->hadGroups = $ini->hasVariable( 'RelationGroupSettings', 'Groups' );
        $this->groups = $this->hadGroups ? $ini->variable( 'RelationGroupSettings', 'Groups' ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'content.ini' );
        if ( $this->hadGroups )
            $ini->setVariable( 'RelationGroupSettings', 'Groups', $this->groups );
        else
            $ini->removeSetting( 'RelationGroupSettings', 'Groups' );
    }

    private function source( $view )
    {
        return file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/' . $view . '.php' );
    }

    /** CT-01 */
    public function testTheContentTypesAreTheShippedOnesAndTheRelationGroups()
    {
        foreach ( array( 'objects', 'images', 'files' ) as $type )
            $this->assertTrue( Dialog::isContentType( $type ), $type );

        $ini = eZINI::instance( 'content.ini' );
        $ini->setVariable( 'RelationGroupSettings', 'Groups', array( 'images', 'files', 'media', '../bad' ) );
        $this->assertTrue( Dialog::isContentType( 'media' ), 'a relation group of content.ini' );
        $this->assertFalse( Dialog::isContentType( '../bad' ), 'a group that is no template name' );
        $this->assertNotContains( '../bad', Dialog::contentTypes() );
    }

    /** CT-02 */
    public function testAnythingElseIsNoContentType()
    {
        foreach ( array( '', 'auto', 'Objects', 'object', 'objects/../x', '../../content/edit', 'objects.tpl', 'a/b',
                         "objects\n", 'objects ', 'unknown', str_repeat( 'a', 101 ), null, 12, array( 'objects' ) ) as $type )
            $this->assertFalse( Dialog::isContentType( $type ), var_export( $type, true ) );
    }

    /** CT-03 */
    public function testTheViewsCheckTheContentTypeBeforeTheTemplatePath()
    {
        $upload = $this->source( 'upload' );
        $check = strpos( $upload, 'if ( !Dialog::isContentType( $contentType ) )' );
        $this->assertNotFalse( $check );
        $this->assertLessThan( strpos( $upload, "'design:ezoe/upload_' . \$contentType" ), $check );
        $this->assertLessThan( strpos( $upload, '$http->hasPostVariable( \'uploadButton\' )' ), $check );

        $relations = $this->source( 'relations' );
        $check = strpos( $relations, 'if ( !Dialog::isContentType( $contentType ) )' );
        $this->assertNotFalse( $check );
        $this->assertGreaterThan( strpos( $relations, '$contentType = \eZOEXMLInput::embedTagContentType(' ), $check, 'after auto' );
        $this->assertLessThan( strpos( $relations, "'design:ezoe/tag_embed_' . \$contentType" ), $check );
    }

    /** LO-01 */
    public function testLoadReadsTheEmbedIdThroughTheParser()
    {
        $load = $this->source( 'load' );
        $start = strpos( $load, '$embedObject = false;' );
        $parse = strpos( $load, 'Relations::parseEmbedId( isset( $Params[\'EmbedID\'] ) ? $Params[\'EmbedID\'] : null )' );
        $this->assertNotFalse( $start );
        $this->assertNotFalse( $parse );
        $this->assertLessThan( $parse, $start );
        $this->assertLessThan( strpos( $load, 'if ( !$embedObject instanceof \eZContentObject' ), $parse );
        $this->assertStringNotContainsString( 'explode(', $load );
        // what ezoe/load is sent: ezobject_12 and eznode_34 in lower case, too
        $this->assertSame( array( 'eZObject', 12 ), Exponential\View\Extension\Ezoe\Ezoe\Relations::parseEmbedId( 'ezobject_12' ) );
        $this->assertSame( array( 'eZNode', 34 ), Exponential\View\Extension\Ezoe\Ezoe\Relations::parseEmbedId( 'eznode_34' ) );
        $this->assertFalse( Exponential\View\Extension\Ezoe\Ezoe\Relations::parseEmbedId( 'eZNode_' ) );
    }
}
