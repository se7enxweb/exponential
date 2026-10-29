<?php
/**
 * The legacy site packages swap the content root with their "Home" page and then remove the old root
 * (eZSiteInstaller::swapNodes(), removeContentObject()). They name the root "eZ Publish", a name the clean data
 * of Exponential 6 no longer uses, and the swap stopped with "Can't fetch node ''". No database.
 *
 *  SR-01 - isContentRootName(): the names the root has carried, without case and blanks; "Home" and others not
 *  SR-02 - objectLookups(): the step's remote id first, then its name (with the class it gives), then the content
 *          root's remote id when the name is a former root name
 *  SR-03 - describeObjectParams(): the message names what the step looked for, never an empty node id
 *  SR-04 - mayPurgeAtDepth(): a top-level node (the content, users and media roots) is never purged
 *  SR-05 - the kernel's ezwebininstall command names the root by its remote id
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SiteInstallerContentRootLookupTest extends PHPUnit\Framework\TestCase
{
    public function testContentRootNames()
    {
        foreach ( array( 'eZ Publish', 'ez publish', ' Exponential ', 'Websites', 'eZ Platform' ) as $name )
            $this->assertTrue( eZSiteInstaller::isContentRootName( $name ), $name );
        foreach ( array( 'Home', 'Media', 'Users', '', 'eZ Publish site', null, array(), 2 ) as $name )
            $this->assertFalse( eZSiteInstaller::isContentRootName( $name ), var_export( $name, true ) );
    }

    public function testLookupOrder()
    {
        $root = eZSiteInstaller::CONTENT_ROOT_REMOTE_ID;
        $this->assertSame( '9459d3c29e15006e45197295722c7ade', $root );

        // the legacy package step: by name, then the root by its remote id
        $this->assertSame( array( array( 'conditions', array( 'name' => 'eZ Publish' ) ), array( 'remote_id', $root ) ),
                           eZSiteInstaller::objectLookups( array( 'name' => 'eZ Publish' ) ) );

        // the rebuilt packages give the remote id: it comes first and is not repeated
        $this->assertSame( array( array( 'remote_id', $root ), array( 'conditions', array( 'name' => 'Websites' ) ) ),
                           eZSiteInstaller::objectLookups( array( 'name' => 'Websites', 'remote_id' => $root ) ) );

        // any other name: by name only, and the class it gives is a condition (it was dropped before)
        $this->assertSame( array( array( 'conditions', array( 'name' => 'Home', 'contentclass_id' => 1 ) ) ),
                           eZSiteInstaller::objectLookups( array( 'name' => 'Home', 'class_id' => 1 ) ) );
        $this->assertSame( array( array( 'conditions', array( 'name' => 'Home' ) ) ),
                           eZSiteInstaller::objectLookups( array( 'name' => 'Home' ) ) );

        // a remote id alone, nothing at all
        $this->assertSame( array( array( 'remote_id', 'abc' ) ), eZSiteInstaller::objectLookups( array( 'remote_id' => 'abc' ) ) );
        $this->assertSame( array(), eZSiteInstaller::objectLookups( array() ) );
        $this->assertSame( array(), eZSiteInstaller::objectLookups( array( 'name' => '' ) ) );
    }

    public function testMessagesNameTheObject()
    {
        $this->assertSame( "the object named 'eZ Publish'", eZSiteInstaller::describeObjectParams( array( 'name' => 'eZ Publish' ) ) );
        $this->assertSame( "the object named 'Websites' or with remote id 'x1'",
                           eZSiteInstaller::describeObjectParams( array( 'name' => 'Websites', 'remote_id' => 'x1' ) ) );
        $this->assertSame( 'an object the step does not name', eZSiteInstaller::describeObjectParams( array() ) );
        $this->assertSame( 'an object the step does not name', eZSiteInstaller::describeObjectParams( null ) );
    }

    public function testTopLevelNodesAreNeverPurged()
    {
        $this->assertFalse( eZSiteInstaller::mayPurgeAtDepth( 1 ) );
        $this->assertFalse( eZSiteInstaller::mayPurgeAtDepth( '1' ) );
        $this->assertFalse( eZSiteInstaller::mayPurgeAtDepth( 0 ) );
        $this->assertFalse( eZSiteInstaller::mayPurgeAtDepth( null ) );
        // the old root after the swap is one level down, where "Home" was
        $this->assertTrue( eZSiteInstaller::mayPurgeAtDepth( 2 ) );
        $this->assertTrue( eZSiteInstaller::mayPurgeAtDepth( '3' ) );
    }

    public function testKernelCommandNamesTheRootByRemoteId()
    {
        $code = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/commands/ezwebininstall.php' );
        $this->assertStringContainsString( "'remote_id' => \\eZSiteInstaller::CONTENT_ROOT_REMOTE_ID", $code );
        $this->assertStringNotContainsString( "array( 'name' => \"Exponential\" )", $code );
    }
}
