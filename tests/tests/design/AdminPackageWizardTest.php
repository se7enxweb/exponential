<?php
/**
 * The package creation wizard (package/create) in the administration designs: design/admin4 (admin4, admin4l) and
 * design/admin carry the same templates; every step draws the shared head, progress and actions; the step list marks
 * the current step; and every name the creators read is still posted (the redesign changed the look, not the form).
 * No inline CSS (AdminStylingRulesTest checks that for every template). DB-less.
 * Guide: doc/features/6.0/package-wizard.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminPackageWizardTest extends PHPUnit\Framework\TestCase
{
    /** step template => the names it must post (besides the shared ones) */
    const STEPS = array(
        'create/info.tpl' => array( 'PackageName', 'PackageSummary', 'PackageDescription', 'PackageVersion', 'PackageFormat', 'PackageLicence', 'PackageHost', 'PackagePackager' ),
        'create/maintainer.tpl' => array( 'PackageMaintainerPerson', 'PackageMaintainerEmail', 'PackageMaintainerRole' ),
        'create/changelog.tpl' => array( 'PackageChangelogPerson', 'PackageChangelogEmail', 'PackageChangelogText' ),
        'create/thumbnail.tpl' => array( 'PackageThumbnail' ),
        'creators/ezcontentclass/class.tpl' => array( 'ClassList[]' ),
        'creators/ezcontentobject/object_select.tpl' => array( 'AddNode', 'AddSubtree', 'RemoveSelected', 'DeleteIDArray[]' ),
        'creators/ezcontentobject/object_limit.tpl' => array( 'IncludeClasses', 'IncludeTemplates', 'SiteAccesses[]', 'VersionExport', 'Languages[]', 'NodeAssignment', 'RelatedObjects' ),
        'creators/ezextension/extension.tpl' => array( 'PackageExtensionNames[]' ),
        'creators/ezstyle/cssfile.tpl' => array( 'PackageSiteCSSFile', 'PackageClassesCSSFile' ),
        'creators/ezstyle/imagefiles.tpl' => array( 'PackageImageFile' ),
    );

    const SHARED = array( 'create.tpl', 'create/wizard_head.tpl', 'create/wizard_progress.tpl', 'create/wizard_actions.tpl',
                          'create/wizard_review.tpl', 'create/wizard_pick_script.tpl',
                          'creators/ezcontentobject/browse_node.tpl', 'creators/ezcontentobject/browse_subtree.tpl' );

    private static function source( $design, $path )
    {
        $file = dirname( __DIR__, 3 ) . "/design/$design/templates/package/$path";
        return is_file( $file ) ? (string)file_get_contents( $file ) : null;
    }

    public function testBothDesignsCarryTheSameWizard()
    {
        foreach ( array_merge( array_keys( self::STEPS ), self::SHARED ) as $path )
        {
            $admin4 = self::source( 'admin4', $path );
            $this->assertNotNull( $admin4, "design/admin4/templates/package/$path" );
            $this->assertSame( $admin4, self::source( 'admin', $path ), "design/admin and design/admin4 differ in package/$path" );
        }
    }

    public function testEveryStepUsesTheSharedPartsAndPostsItsNames()
    {
        foreach ( self::STEPS as $path => $names )
        {
            $source = self::source( 'admin4', $path );
            foreach ( array( 'wizard_head.tpl', 'wizard_progress.tpl', 'wizard_actions.tpl' ) as $part )
                $this->assertStringContainsString( 'design:package/create/' . $part, $source, "$path includes $part" );
            $this->assertMatchesRegularExpression( '#<form [^>]*action=\{\'package/create\'\|ezurl\}#', $source, "$path posts to package/create" );
            foreach ( $names as $name )
                $this->assertStringContainsString( 'name="' . $name . '"', $source, "$path posts $name" );
        }
        foreach ( array( 'creators/ezstyle/cssfile.tpl', 'creators/ezstyle/imagefiles.tpl', 'create/thumbnail.tpl' ) as $path )
            $this->assertStringContainsString( 'enctype="multipart/form-data"', self::source( 'admin4', $path ), "$path uploads files" );
    }

    public function testTheSharedPartsPostTheStepAndMarkTheCurrentOne()
    {
        $head = self::source( 'admin4', 'create/wizard_head.tpl' );
        foreach ( array( 'CreatorItemID', 'CreatorStepID', 'PackageStep' ) as $name )
            $this->assertStringContainsString( 'name="' . $name . '"', $head );
        // Enter goes on: the invisible Continue is the head's only submit button, so it comes before every other one
        $this->assertSame( 1, preg_match_all( '/type="submit"/', $head ) );
        $this->assertStringContainsString( '<input type="submit" name="NextStepButton" value="" tabindex="-1" aria-hidden="true" class="a4-sr-only" />', $head );

        $progress = self::source( 'admin4', 'create/wizard_progress.tpl' );
        $this->assertStringContainsString( '<ol class="a4-steps a4-package-wizard-steps">', $progress );
        $this->assertStringContainsString( 'aria-current="step"', $progress );
        $this->assertStringContainsString( '$creator.current_steps', $progress );

        $actions = self::source( 'admin4', 'create/wizard_actions.tpl' );
        $this->assertStringContainsString( 'name="PreviousStepButton"', $actions );
        $this->assertSame( 2, substr_count( $actions, 'name="NextStepButton"' ), 'Continue and Create package' );
        $this->assertStringContainsString( 'design:package/create/wizard_review.tpl', $actions );

        $choice = self::source( 'admin4', 'create.tpl' );
        $this->assertStringContainsString( 'name="CreatorItemID"', $choice );
        $this->assertStringContainsString( 'name="CreatePackageButton"', $choice );
    }

    public function testNoRequiredAttributeStopsBack()
    {
        foreach ( array_keys( self::STEPS ) as $path )
            $this->assertStringNotContainsString( ' required=', self::source( 'admin4', $path ), "$path: the server checks required fields; a browser check would stop Back" );
    }
}
