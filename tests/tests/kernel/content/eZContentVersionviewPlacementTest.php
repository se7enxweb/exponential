<?php
/**
 * The location of the version preview, without the database: the versionview templates of every design mark the
 * shown location with $placement, and content/versionview hands its placement to them.
 *
 *  VP-01 - kernel/content/versionviewframe.php sets the template variable placement from $placementID, as an
 *          integer only (the value may come from the request and design/standard prints it into a form field)
 *  VP-02 - $placementID is the variable \Exponential\View\Kernel\Content\Versionview fills from the node
 *          assignments before it includes the frame
 *  VP-03 - Every versionview template that reads $placement is one this frame renders (content/view/versionview.tpl)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZContentVersionviewPlacementTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $cwd;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        chdir( $this->cwd );
    }

    /** VP-01 */
    public function testTheFrameSetsThePlacement()
    {
        $source = file_get_contents( 'kernel/content/versionviewframe.php' );
        $this->assertSame( 1, preg_match( '/\$tpl->setVariable\(\s*\'placement\',\s*([^;]+);/', $source, $match ),
                           'versionviewframe.php sets the template variable placement' );
        $this->assertStringContainsString( '$placementID', $match[1] );
        $this->assertStringContainsString( 'is_numeric( $placementID )', $match[1], 'only a number' );
        $this->assertStringContainsString( '(int)$placementID', $match[1] );
    }

    /** VP-02 */
    public function testTheViewFillsThePlacementBeforeItIncludesTheFrame()
    {
        $source = file_get_contents( 'kernel/private/classes/views/content/versionview.php' );
        $include = strpos( $source, "include( 'kernel/content/versionviewframe.php' )" );
        $this->assertNotFalse( $include );
        $fromAssignment = strpos( $source, "\$placementID = \$nodeAssignments[0]->attribute( 'id' );" );
        $this->assertNotFalse( $fromAssignment, 'the view takes the placement from the node assignment' );
        $this->assertLessThan( $include, $fromAssignment );
    }

    /** VP-03 */
    public function testTheTemplatesThatReadThePlacementAreVersionviewTemplates()
    {
        $readers = array();
        foreach ( glob( 'design/*/templates/content/view/versionview.tpl' ) as $file )
        {
            if ( strpos( file_get_contents( $file ), '$placement' ) !== false )
            {
                $readers[] = $file;
            }
        }
        $this->assertContains( 'design/admin/templates/content/view/versionview.tpl', $readers );
        $this->assertContains( 'design/standard/templates/content/view/versionview.tpl', $readers );
        $frame = file_get_contents( 'kernel/content/versionviewframe.php' );
        $this->assertStringContainsString( "'design:content/view/versionview.tpl'", $frame );
    }
}
