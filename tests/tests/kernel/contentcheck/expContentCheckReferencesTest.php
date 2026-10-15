<?php
/**
 * The reference sources of the unreferenced list and the new parts of the broken relations, without a database
 * (doc/features/6.0/content-checks.md, "What counts as a reference"): every extractor, the judging of a candidate
 * against an index, and the asset class condition of the SQL.
 *
 *  CR-01  rich text by id: object_id and node_id of embeds and links, relation lists (contentobject-id, node-id but
 *         not parent-node-id), eznode:// ezobject:// ezcontent:// ezlocation://, exp-remote-location://
 *  CR-02  text by path: content/view/<mode>/<node>, content/download/<object>, storage file paths, internal hrefs
 *  CR-03  fields by datatype: ezobjectrelation, ngenhancedlink (internal: object and node; external: only its URL), ezurl
 *  CR-04  layout block parameters: links (JSON with exp-remote-location://), ids in parameters named for content,
 *         HTML with storage paths, internal paths; numbers in other parameters are not ids
 *  CR-05  layout dynamic collections: parent node, list or tree, class include and exclude, the current location
 *         (anywhere), topic content
 *  CR-06  layout rule targets: node, subtree, content, paths; module targets and "/" are not references
 *  CR-07  settings files: node, root and index settings, content paths in any value, NexusNodeMap remote ids; image
 *         sizes and other numbers are not ids
 *  CR-08  templates: fetch hashes with node_id, parent_node_id, object_id; content/view paths
 *  CR-09  role and policy limitations, newsletter lists: ids of path strings and lists
 *  CR-10  judging: object, node, remote id and path references; a reference from the object itself does not count;
 *         an asset below content that shows it counts as used; dynamic collections by place and class
 *  CR-11  the SQL of the candidates is limited to the asset classes; none configured lists nothing
 *  CR-12  broken relations: targets of relation fields, the hint of each kind
 *  CR-13  paths are kept normalised: no host, no query, lower case; ".." is refused
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentCheckReferencesTest extends PHPUnit\Framework\TestCase
{
    /**
     * @param array $references
     * @return string[] "type:value", sorted
     */
    protected static function keys( array $references )
    {
        $keys = array();
        foreach ( $references as $reference )
            $keys[] = $reference['type'] . ':' . $reference['value'];
        $keys = array_values( array_unique( $keys ) );
        sort( $keys );
        return $keys;
    }

    /** CR-01 */
    public function testRichTextByIdAndScheme()
    {
        $xml = '<section><paragraph><embed view="embed" size="medium" object_id="267"/>' .
               '<link node_id="58">x</link><embed-inline object_id="12"/></paragraph></section>';
        $this->assertSame( array( 'node:58', 'object:12', 'object:267' ), self::keys( expContentCheckReferences::textReferences( $xml ) ) );

        $list = '<related-objects><relation-list><relation-item priority="1" contentobject-id="185" contentobject-version="1"' .
                ' node-id="187" parent-node-id="186" contentclass-id="57"/></relation-list></related-objects>';
        $this->assertSame( array( 'node:187', 'object:185' ), self::keys( expContentCheckReferences::textReferences( $list ) ),
                           'the parent of a related object is not referred to' );

        $schemes = 'a eznode://5 b ezobject://6 c ezcontent://7 d ezlocation://8 e exp-remote-location://media-n-744';
        $this->assertSame( array( 'node:5', 'node:8', 'object:6', 'object:7', 'remote:media-n-744' ),
                           self::keys( expContentCheckReferences::textReferences( $schemes ) ) );
        $this->assertSame( array(), expContentCheckReferences::textReferences( '<p>main_node_id="3" custom_object_id="4" plain text 99</p>' ) );
    }

    /** CR-02 */
    public function testTextByPath()
    {
        $text = '<a href="/content/view/full/131">x</a> <a href="/content/download/44/120/file/a.pdf">y</a>' .
                ' <img src="/var/site/storage/images/media/images/logo/1234-2-eng-GB/logo.png" />' .
                ' <a href="/Media/Images/Logo?x=1">z</a> <a href="https://www.example.com/about">w</a>';
        $keys = self::keys( expContentCheckReferences::textReferences( $text ) );
        $this->assertContains( 'node:131', $keys );
        $this->assertContains( 'object:44', $keys );
        $this->assertContains( 'file:var/site/storage/images/media/images/logo/1234-2-eng-GB/logo.png', $keys );
        $this->assertContains( 'path:/Media/Images/Logo', $keys );
        $this->assertContains( 'path:https://www.example.com/about', $keys, 'an absolute link is kept; its path is compared without the host' );
    }

    /** CR-03 */
    public function testFieldsByDatatype()
    {
        $this->assertSame( array( 'object:73' ), self::keys( expContentCheckReferences::attributeReferences( 'ezobjectrelation', 73, '' ) ) );
        $this->assertSame( array(), expContentCheckReferences::attributeReferences( 'ezobjectrelation', 0, '' ) );
        $this->assertSame( array( 'node:235', 'object:235' ),
                           self::keys( expContentCheckReferences::attributeReferences( 'ngenhancedlink', 235, '{"type":"internal","id":235,"label":"Contact"}' ) ) );
        $this->assertSame( array(), expContentCheckReferences::attributeReferences( 'ngenhancedlink', 0, '{"type":"internal","id":0,"label":"#"}' ) );
        $this->assertSame( array( 'path:https://se7enx.com/about' ),
                           self::keys( expContentCheckReferences::attributeReferences( 'ngenhancedlink', 0, '{"type":"external","url":"https://se7enx.com/about"}' ) ) );
        $this->assertSame( array( 'node:12', 'path:/content/view/full/12' ), self::keys( expContentCheckReferences::attributeReferences( 'ezurl', 0, '/content/view/full/12' ) ) );
    }

    /** CR-03 ids of imported content: remote id and offset patterns, in the order given */
    public function testImportedIdPatterns()
    {
        $this->assertSame( array( array( 'type' => 'remote', 'value' => 'media-o-1014' ), array( 'type' => 'remote', 'value' => 'media-o-238' ),
                                  array( 'type' => 'object', 'value' => 1014 ) ),
                           expContentCheckReferences::importedIdReferences( 238, array( 'media-o-%id+776', 'media-o-%id' ), array( '%id+776' ) ) );
        $this->assertSame( array( array( 'type' => 'object', 'value' => 200 ) ),
                           expContentCheckReferences::importedIdReferences( 210, array( 'no placeholder' ), array( '%id-10', '%id-300' ) ),
                           'a pattern without %id, or one that leaves no positive id, gives nothing' );
        $this->assertSame( array(), expContentCheckReferences::importedIdReferences( 0, array( 'media-o-%id' ), array() ) );
        $this->assertSame( array( 'remote:imported-77' ),
                           self::keys( array_filter( expContentCheckReferences::attributeReferences( 'ngenhancedlink', 0, '{"type":"internal","id":0,"remote_id":"imported-77"}' ),
                                                     function ( $r ) { return $r['type'] === 'remote'; } ) ),
                           'an enhanced link that keeps its target\'s remote id names it by that' );
    }

    /** CR-04 */
    public function testLayoutBlockParameters()
    {
        $link = '{"link_type": "internal", "link": "exp-remote-location://media-n-744", "link_suffix": null, "new_window": false}';
        $this->assertSame( array( 'remote:media-n-744' ), self::keys( expContentCheckReferences::blockParameterReferences( 'link', $link ) ) );
        $this->assertSame( array( 'node:8', 'object:8' ), self::keys( expContentCheckReferences::blockParameterReferences( 'background_image:image', '8' ) ) );
        $this->assertSame( array( 'node:31' ), self::keys( expContentCheckReferences::blockParameterReferences( 'link', 'ezlocation://31' ) ) );
        $this->assertSame( array( 'path:/Running' ), self::keys( expContentCheckReferences::blockParameterReferences( 'link', '/Running' ) ) );
        $this->assertSame( array( 'file:var/site/storage/images/a/1-1-eng-GB/a.jpg' ),
                           self::keys( expContentCheckReferences::blockParameterReferences( 'content', '<p><img src="/var/site/storage/images/a/1-1-eng-GB/a.jpg"></p>' ) ) );
        $this->assertSame( array(), expContentCheckReferences::blockParameterReferences( 'number_of_columns', '4' ) );
        $this->assertSame( array(), expContentCheckReferences::blockParameterReferences( 'vertical_whitespace:top', 'large' ) );
    }

    /** CR-05 */
    public function testLayoutDynamicCollections()
    {
        $tree = expContentCheckReferences::collectionQueryReferences( '{"use_current_location":false,"parent_location_id":130,"query_type":"tree",' .
                                                                     '"filter_by_content_type":true,"content_types":{"0":"ng_article","2":"image"},' .
                                                                     '"content_types_filter":"include","topic_content_id":17}' );
        $this->assertSame( array( 'node:130', 'object:17' ), self::keys( $tree['references'] ) );
        $this->assertSame( array( 130 ), $tree['query']['nodes'] );
        $this->assertSame( 'tree', $tree['query']['depth'] );
        $this->assertSame( array( 'ng_article', 'image' ), $tree['query']['include'] );

        $list = expContentCheckReferences::collectionQueryReferences( '{"parent_location_id":164,"query_type":"list","filter_by_content_type":true,' .
                                                                     '"content_types":["image"],"content_types_filter":"exclude"}' );
        $this->assertSame( 'list', $list['query']['depth'] );
        $this->assertSame( array( 'image' ), $list['query']['exclude'] );

        $current = expContentCheckReferences::collectionQueryReferences( '{"use_current_location":true,"parent_location_id":null,"filter_by_content_type":false}' );
        $this->assertSame( array( 0 ), $current['query']['nodes'], 'a collection of the current location applies anywhere' );
        $this->assertSame( array(), $current['query']['include'] );
        $this->assertNull( expContentCheckReferences::collectionQueryReferences( 'not json' )['query'] );
    }

    /** CR-05 a collection of the current location lists below the nodes its layout's rules target */
    public function testCurrentLocationPlacements()
    {
        $rules = array( array( 'targets' => array( array( 'node', '192' ) ), 'classes' => array() ),
                        array( 'targets' => array( array( 'subtree', '89' ), array( 'module_view', 'content/view' ) ), 'classes' => array() ),
                        array( 'targets' => array( array( 'module', 'user' ) ), 'classes' => array() ),
                        array( 'targets' => array(), 'classes' => array( 'ng_gallery' ) ) );
        $this->assertSame( array( array( 'nodes' => array( 192 ), 'depth' => 'list', 'parent_classes' => array() ),
                                  array( 'nodes' => array( 89 ), 'depth' => 'tree', 'parent_classes' => array() ),
                                  array( 'nodes' => array( 0 ), 'depth' => 'list', 'parent_classes' => array( 'ng_gallery' ) ) ),
                           expContentCheckReferences::currentLocationPlacements( $rules, 'list', false ),
                           'a node target lists its children, a subtree target everything below it, a class condition anywhere below that class; module targets are administration layouts' );
        $this->assertSame( array( array( 'nodes' => array( 0 ), 'depth' => 'list', 'parent_classes' => array() ) ),
                           expContentCheckReferences::currentLocationPlacements( $rules, 'list', true ), 'a shared layout can show up anywhere' );
        $this->assertSame( array( array( 'nodes' => array( 0 ), 'depth' => 'tree', 'parent_classes' => array() ) ),
                           expContentCheckReferences::currentLocationPlacements( array(), 'tree', false ), 'a layout without rules: anywhere' );
        $gallery = array( 'nodes' => array( 0 ), 'depth' => 'list', 'include' => array( 'image' ), 'exclude' => array(), 'parent_classes' => array( 'ng_gallery' ) );
        $this->assertTrue( expContentCheckReferences::queryReaches( $gallery, 'image', 7, array( 1, 7 ), 'ng_gallery' ) );
        $this->assertFalse( expContentCheckReferences::queryReaches( $gallery, 'image', 7, array( 1, 7 ), 'folder' ) );
    }

    /** CR-06 */
    public function testLayoutRuleTargets()
    {
        $this->assertSame( array( 'node:92' ), self::keys( expContentCheckReferences::ruleTargetReferences( 'node', '92' ) ) );
        $this->assertSame( array( 'node:89' ), self::keys( expContentCheckReferences::ruleTargetReferences( 'subtree', '89' ) ) );
        $this->assertSame( array( 'object:5' ), self::keys( expContentCheckReferences::ruleTargetReferences( 'content', '5' ) ) );
        $this->assertSame( array( 'path:/Fit-Healthy/Recipes' ), self::keys( expContentCheckReferences::ruleTargetReferences( 'path_prefix', '/Fit-Healthy/Recipes' ) ) );
        $this->assertSame( array(), expContentCheckReferences::ruleTargetReferences( 'path_info_prefix', '/' ) );
        $this->assertSame( array(), expContentCheckReferences::ruleTargetReferences( 'module_view', 'class/copy' ) );
    }

    /** CR-07 */
    public function testSettingsFiles()
    {
        $ini = "#?ini charset=\"utf-8\"?\n[NodeSettings]\nRootNode=2\nMediaRootNode=43\n[SiteSettings]\nIndexPage=/content/view/full/89/\n" .
               "[NexusNodeMap]\n190=media-n-744\n[MenuContentSettings]\nTopIdentifierList[]=folder\n" .
               "[small]\nFilters[]=geometry/scale=300;300\n[ImageSettings]\nMaxSize=300\n# RootNode=999\n";
        $found = expContentCheckReferences::iniReferences( $ini );
        $keys = self::keys( $found );
        foreach ( array( 'node:2', 'node:43', 'node:89', 'remote:media-n-744' ) as $expected )
            $this->assertContains( $expected, $keys );
        $this->assertNotContains( 'node:300', $keys, 'an image size is not an id' );
        $this->assertNotContains( 'node:190', $keys, 'a NexusNodeMap key is an id of the reference installation; its value names the node' );
        $this->assertNotContains( 'node:999', $keys, 'a comment is not a setting' );
        $this->assertSame( '[NodeSettings] RootNode', $found[0]['setting'] );
    }

    /** CR-08 */
    public function testTemplates()
    {
        $tpl = "{def \$news = fetch( 'content', 'list', hash( 'parent_node_id', 58, 'limit', 3 ) )\n" .
               "     \$logo = fetch( 'content', 'object', hash( 'object_id', 312 ) )\n" .
               "     \$home = fetch( 'content', 'node', hash( 'node_id', \$id ) )}\n" .
               "<a href={'/content/view/full/77'|ezurl}>x</a>";
        $this->assertSame( array( 'node:58', 'node:77', 'object:312' ), self::keys( expContentCheckReferences::templateReferences( $tpl ) ),
                           'an id the template computes ($id) cannot be known' );
    }

    /** CR-09 */
    public function testPathStringsAndLists()
    {
        $this->assertSame( array( 1, 2, 43 ), expContentCheckReferences::idsOf( '/1/2/43/' ) );
        $this->assertSame( array( 58, 61 ), expContentCheckReferences::idsOf( '58;61,58' ) );
        $this->assertSame( array(), expContentCheckReferences::idsOf( '' ) );
    }

    /**
     * @return array an index with one reference of each kind
     */
    protected static function index()
    {
        return array_merge( expContentCheckReferences::emptyIndex(), array(
            'objects' => array( 10 => array( array( 99, 'embed in object 99' ) ), 11 => array( array( 11, 'itself' ) ) ),
            'nodes' => array( 20 => array( array( 0, 'layout block 1, collection item 2' ) ) ),
            'remote' => array( 'img-remote' => array( array( 0, 'layout block 3, parameter link' ) ) ),
            'aliases' => array( 'media/images/used' => array( 0, 'link (ezurl 5)' ) ),
            'queries' => array( array( 'nodes' => array( 50 ), 'depth' => 'list', 'include' => array( 'image' ), 'exclude' => array(), 'label' => 'gallery block' ),
                                array( 'nodes' => array( 60 ), 'depth' => 'tree', 'include' => array(), 'exclude' => array( 'image' ), 'label' => 'no images' ) ) ) );
    }

    /**
     * @return array a candidate image in a folder, nothing referring to it
     */
    protected static function candidate( array $object = array(), array $location = array() )
    {
        return array_merge( array( 'object_id' => 1, 'remote_id' => 'o1', 'class_identifier' => 'image',
                                   'locations' => array( array_merge( array( 'node_id' => 2, 'parent_node_id' => 43, 'path_string' => '/1/43/2/',
                                                                             'remote_id' => 'n2', 'path' => 'media/images/free', 'parent_class' => 'folder' ), $location ) ) ),
                            $object );
    }

    /** CR-10 */
    public function testJudging()
    {
        $index = self::index();
        $storage = array( 'folder' );
        $this->assertNull( expContentCheckReferences::reason( $index, self::candidate(), $storage ) );
        $this->assertSame( 'embed in object 99', expContentCheckReferences::reason( $index, self::candidate( array( 'object_id' => 10 ) ), $storage ) );
        $this->assertNull( expContentCheckReferences::reason( $index, self::candidate( array( 'object_id' => 11 ) ), $storage ), 'a reference from itself' );
        $this->assertSame( 'layout block 1, collection item 2', expContentCheckReferences::reason( $index, self::candidate( array(), array( 'node_id' => 20 ) ), $storage ) );
        $this->assertSame( 'layout block 3, parameter link', expContentCheckReferences::reason( $index, self::candidate( array( 'remote_id' => 'img-remote' ) ), $storage ) );
        $this->assertSame( 'layout block 3, parameter link', expContentCheckReferences::reason( $index, self::candidate( array(), array( 'remote_id' => 'img-remote' ) ), $storage ) );
        $this->assertSame( 'link (ezurl 5)', expContentCheckReferences::reason( $index, self::candidate( array(), array( 'path' => 'Media/Images/Used' ) ), $storage ) );
        $this->assertStringStartsWith( 'shown by its parent (ng_gallery', (string)expContentCheckReferences::reason( $index, self::candidate( array(), array( 'parent_class' => 'ng_gallery' ) ), $storage ) );
        // dynamic collections: the children of 50 of class image; the subtree of 60 but not images
        $this->assertSame( 'gallery block', expContentCheckReferences::reason( $index, self::candidate( array(), array( 'parent_node_id' => 50, 'path_string' => '/1/50/2/' ) ), $storage ) );
        $this->assertNull( expContentCheckReferences::reason( $index, self::candidate( array( 'class_identifier' => 'file' ), array( 'parent_node_id' => 50, 'path_string' => '/1/50/2/' ) ), $storage ) );
        $this->assertNull( expContentCheckReferences::reason( $index, self::candidate( array(), array( 'parent_node_id' => 61, 'path_string' => '/1/60/61/2/' ) ), $storage ) );
        $this->assertSame( 'no images', expContentCheckReferences::reason( $index, self::candidate( array( 'class_identifier' => 'file' ), array( 'parent_node_id' => 61, 'path_string' => '/1/60/61/2/' ) ), $storage ) );
        $this->assertTrue( expContentCheckReferences::queryReaches( array( 'nodes' => array( 0 ), 'depth' => 'tree', 'include' => array(), 'exclude' => array() ), 'image', 7, array( 1, 7 ) ) );
    }

    /** CR-11 */
    public function testCandidatesAreAssetsOnly()
    {
        $params = expContentCheckUnreferenced::parameters( array( 'NodeID' => 2 ), 2 );
        $c = expContentCheckUnreferenced::conditions( $params, '/1/2/%', array( 'from' => '', 'where' => '' ), array( 'image', 'file', "x'; DROP" ) );
        $this->assertStringContainsString( "ezcontentclass.identifier IN ( 'image', 'file' )", $c['where'] );
        $none = expContentCheckUnreferenced::conditions( $params, '/1/2/%', array( 'from' => '', 'where' => '' ), array() );
        $this->assertStringContainsString( "ezcontentclass.identifier IN ( '' )", $none['where'] );
    }

    /** CR-12 */
    public function testBrokenRelationFieldsAndHints()
    {
        $this->assertSame( array( array( 'type' => 'object', 'id' => 7 ) ), expContentCheckBrokenRelations::attributeTargets( 'ezobjectrelation', 7, '' ) );
        $this->assertSame( array( array( 'type' => 'object', 'id' => 185 ), array( 'type' => 'object', 'id' => 190 ) ),
                           expContentCheckBrokenRelations::attributeTargets( 'ezobjectrelationlist', 0,
                               '<relation-item contentobject-id="185" node-id="187"/><relation-item contentobject-id="190" parent-node-id="1"/>' ) );
        $this->assertSame( array( array( 'type' => 'either', 'id' => 738, 'remote' => '' ) ),
                           expContentCheckBrokenRelations::attributeTargets( 'ngenhancedlink', 738, '{"type":"internal","id":738,"target":"modal"}' ) );
        $this->assertSame( array(), expContentCheckBrokenRelations::attributeTargets( 'ngenhancedlink', 0, '{"type":"external","url":"https://x"}' ) );
        $this->assertSame( 'restore', expContentCheckBrokenRelations::hint( 'trash' ) );
        $this->assertSame( 'remove', expContentCheckBrokenRelations::hint( 'deleted' ) );
        $this->assertSame( 'reveal', expContentCheckBrokenRelations::hint( 'hidden' ) );
        $this->assertSame( 'publish', expContentCheckBrokenRelations::hint( 'unpublished' ) );
        $this->assertSame( 'locate', expContentCheckBrokenRelations::hint( 'no_location' ) );
        $this->assertSame( 'ask', expContentCheckBrokenRelations::hint( 'unavailable' ) );
    }

    /** CR-13 */
    public function testPathsAreNormalised()
    {
        $this->assertSame( 'media/images/logo', expContentCheckReferences::normalisePath( 'https://alpha.example.com/Media/Images/Logo/?a=1#x' ) );
        $this->assertSame( 'running', expContentCheckReferences::normalisePath( '/Running' ) );
        $this->assertSame( '', expContentCheckReferences::normalisePath( '/a/../b' ) );
        $this->assertSame( '', expContentCheckReferences::normalisePath( '/' ) );
    }
}
