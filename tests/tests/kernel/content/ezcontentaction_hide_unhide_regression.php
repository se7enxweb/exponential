<?php
/**
 * File containing eZContentActionHideUnhideRegression class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * @backupGlobals disabled
 */
class eZContentActionHideUnhideRegression extends ezpDatabaseTestCase
{
    private $folder;

    private $article;

    private $previousUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->previousUser = eZUser::currentUser();
        $adminUser = eZUser::fetchByName( 'admin' );
        eZUser::setCurrentlyLoggedInUser( $adminUser, $adminUser->attribute( 'contentobject_id' ) );

        $_POST = array();
        $_GET = array();

        $this->folder = new ezpObject( 'folder', 2 );
        $this->folder->name = 'Hide/unhide test folder';
        $this->folder->publish();

        $this->article = new ezpObject( 'article', $this->folder->main_node_id );
        $this->article->title = 'Hide/unhide test article';
        $this->article->publish();

        eZContentLanguage::expireCache();
    }

    public function tearDown(): void
    {
        if ( $this->article instanceof ezpObject )
        {
            $this->article->remove();
        }

        if ( $this->folder instanceof ezpObject )
        {
            $this->folder->remove();
        }

        $_POST = array();
        $_GET = array();
        eZContentLanguage::expireCache();

        if ( $this->previousUser instanceof eZUser )
        {
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ) );
        }

        parent::tearDown();
    }

    private function runHideAction( $buttonName )
    {
        $http = eZHTTPTool::instance();

        $_POST = array();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_LENGTH'] = 1;

        $http->setPostVariable( 'ViewMode', 'full' );
        $http->setPostVariable( 'ContentNodeID', $this->folder->main_node_id );
        $http->setPostVariable( 'SelectedIDArray', array( $this->article->main_node_id ) );
        $http->setPostVariable( $buttonName, 1 );

        $contentModule = eZModule::findModule( 'content' );
        $contentModule->run( 'action', array() );

        return $contentModule;
    }

    public function testHideSelectedNodeRedirectsAndHidesNode()
    {
        $contentModule = $this->runHideAction( 'HideButton' );

        self::assertEquals( eZModule::STATUS_REDIRECT, $contentModule->ExitStatus );

        $node = eZContentObjectTreeNode::fetch( $this->article->main_node_id );
        self::assertTrue( (bool)$node->attribute( 'is_hidden' ) );
    }

    public function testUnhideSelectedNodeRedirectsAndUnhidesNode()
    {
        eZContentOperationCollection::changeHideStatus( $this->article->main_node_id );

        $contentModule = $this->runHideAction( 'UnhideButton' );

        self::assertEquals( eZModule::STATUS_REDIRECT, $contentModule->ExitStatus );

        $node = eZContentObjectTreeNode::fetch( $this->article->main_node_id );
        self::assertFalse( (bool)$node->attribute( 'is_hidden' ) );
    }
}
?>