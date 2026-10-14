<?php
/**
 * Content editing in admin4 says which fields are required and which failed, in words and to screen readers:
 * every attribute is a named group (the summary's link target), a required one has the mark and the word, an
 * invalid one its message under the label as the group's description, and the script marks the datatype's
 * controls aria-required / aria-invalid with that message as description. A failed store shows a summary that
 * takes the focus and links to the fields.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminFormErrorsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminFormErrorsTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testEveryAttributeIsANamedGroupThatTheSummaryCanReach()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        $this->assertMatchesRegularExpression( '/<div class="block a4-content-attr [^"]*" id="ezcca-attr-\{\$attribute\.id\}" tabindex="-1" role="group" aria-labelledby="ezcca-name-\{\$attribute\.id\}"\{if \$attribute\.has_validation_error\} aria-describedby="ezcca-error-\{\$attribute\.id\}"\{\/if\}>/', $tpl );
        // the name the group points at exists in each of the four exclusive label/legend branches
        $this->assertSame( 4, substr_count( $tpl, '<span id="ezcca-name-{$attribute.id}">' ) );
    }

    public function testRequiredFieldsShowTheMarkAndTheWord()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        $this->assertSame( 3, substr_count( $tpl, '<span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({\'required\'|i18n( \'design/admin/content/edit_attribute\' )})</span>' ) );
        $this->assertStringNotContainsString( '<span class="required">', $tpl, 'the old visible "(required)" is the mark now' );
        $this->assertStringContainsString( '<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>', self::read( 'design/admin4/templates/content/edit.tpl' ),
            'the form says once what the mark means' );
    }

    public function testInvalidFieldsShowTheirMessageUnderTheLabel()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        // one per editable branch: translation, grouped input (legend), single input (label)
        $this->assertSame( 3, substr_count( $tpl, '<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}">' ) );
        $this->assertSame( 3, substr_count( $tpl, '{$attribute.validation_error|wash}' ) );
    }

    public function testTheScriptMarksTheControlsOfEveryDatatype()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        foreach ( array( "attr( 'aria-required', 'true' )", "setAttribute( 'aria-invalid', 'true' )", "setAttribute( 'aria-describedby'",
                         "not('.original *')", "$('#a4-error-summary').trigger('focus')", '.a4-content-attr.is-invalid' ) as $needle )
            $this->assertStringContainsString( $needle, $tpl );
    }

    public function testTheSummaryIsAnAlertWithLinksToTheFields()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_validation.tpl' );
        $this->assertStringContainsString( '<div class="message-error a4-error-summary" id="a4-error-summary" role="alert" tabindex="-1" aria-labelledby="a4-error-summary-title">', $tpl );
        $this->assertStringContainsString( '<a href="#ezcca-attr-{$UnvalidatedAttributes.item.id|wash}">', $tpl );
        // an entry without an attribute (the request too big) gets no link
        $this->assertStringContainsString( '{if is_set( $UnvalidatedAttributes.item.identifier )}', $tpl );
    }

    public function testTheStylesheetsDrawTheParts()
    {
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $css )
        {
            $source = self::read( $css );
            foreach ( array( '.a4-required-mark', '.a4-required-hint', '.a4-error-summary a', '.a4-bi-x-circle-fill' ) as $selector )
                $this->assertStringContainsString( $selector . ' ', $source, "$css: $selector" );
        }
        $module = self::read( 'design/admin4/stylesheets/modules/content.css' );
        foreach ( array( '.a4-content-attr.is-invalid', '.a4-content-attr-error', '.a4-content-attr input[aria-invalid="true"]' ) as $selector )
            $this->assertStringContainsString( $selector, $module );
    }
}
