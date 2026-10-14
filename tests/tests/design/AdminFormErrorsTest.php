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
        $this->assertStringContainsString( ' id="ezcca-attr-{$attribute.id}" tabindex="-1"{if $a4_own_fieldset|not} role="group" aria-labelledby="ezcca-name-{$attribute.id}"{if and( $attribute.has_validation_error, $a4_view_only|not )} aria-describedby="ezcca-error-{$attribute.id}"{/if}{/if}>', $tpl,
            'a named group, described by its message only where one is drawn, and no second name for a grouped input' );
        // a grouped input (fieldset + legend) carries the description on its fieldset
        $this->assertStringContainsString( '<fieldset{if $attribute.has_validation_error} aria-describedby="ezcca-error-{$attribute.id}"{/if}>', $tpl );
        // the name the group points at exists in each of the four exclusive label/legend branches
        $this->assertSame( 4, substr_count( $tpl, '<span id="ezcca-name-{$attribute.id}">' ) );
    }

    public function testRequiredFieldsShowTheMarkAndTheWord()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        $this->assertSame( 3, substr_count( $tpl, '<span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({\'required\'|i18n( \'design/admin/content/edit_attribute\' )})</span>' ) );
        $this->assertStringNotContainsString( '<span class="required">', $tpl, 'the old visible "(required)" is the mark now' );
        // what the mark means: once per page, from the shared template, so every page that includes it says it
        $this->assertStringContainsString( '{if $a4_has_required}{run-once}<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>', $tpl );
        $root = dirname( __DIR__, 3 );
        $callers = array();
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/design/admin4", FilesystemIterator::SKIP_DOTS ) ) as $file )
        {
            if ( substr( (string)$file, -4 ) !== '.tpl' ) continue;
            $text = (string)file_get_contents( (string)$file );
            if ( strpos( $text, "design:content/edit_attribute.tpl" ) !== false )
            {
                $callers[] = substr( (string)$file, strlen( $root ) + 1 );
                $this->assertStringNotContainsString( 'a4-required-hint', $text, "$file: the hint comes from edit_attribute.tpl" );
            }
        }
        sort( $callers );
        $this->assertSame( array( 'design/admin4/override/templates/template_look_edit.tpl', 'design/admin4/templates/content/edit.tpl',
                                  'design/admin4/templates/content/multiedit.tpl', 'design/admin4/templates/user/register.tpl' ), $callers,
            'a new page that includes edit_attribute.tpl gets the hint from it; check it shows the attributes the same way' );
    }

    public function testInvalidFieldsShowTheirMessageUnderTheLabel()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        // one per editable branch: translation, grouped input (legend), single input (label)
        $this->assertSame( 3, substr_count( $tpl, '<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}">' ) );
        $this->assertSame( 3, substr_count( $tpl, '{$attribute.validation_error|explode( \'&lt;\' )|implode( \'<\' )|explode( \'&gt;\' )|implode( \'>\' )|wash}' ) );
    }

    public function testTheScriptMarksTheControlsOfEveryDatatype()
    {
        $tpl = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        foreach ( array( "attr( 'aria-required', 'true' )", "setAttribute( 'aria-invalid', 'true' )", "setAttribute( 'aria-describedby'",
                         "not('.original *, .a4-no-field-mark, .a4-no-field-mark *')", ':not([type=reset]):not([type=file])',
                         '/delete|remove|selection/i', "$('#a4-error-summary').trigger('focus')", '.a4-content-attr.is-invalid',
                         "$('#a4-error-summary a[href^=\"#ezcca-attr-\"]')" ) as $needle )
            $this->assertStringContainsString( $needle, $tpl );
        // the summary's targets are marked before the groups collapse, so a named attribute's group stays open
        $this->assertLessThan( strpos( $tpl, "$('fieldset.ezcca-collapsible').each" ), strpos( $tpl, "$('#a4-error-summary a[href^=" ) );
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

    /**
     * Validation messages are escaped once: some arrive with &lt; and &gt; already in them (the XML text parser's
     * tag names), and some carry what the editor typed (a link, an e-mail address). Each is decoded from those two
     * entities and then washed, in the summary and at the field alike; none is printed raw.
     */
    public function testValidationMessagesAreEscapedOnce()
    {
        $chain = "|explode( '&lt;' )|implode( '<' )|explode( '&gt;' )|implode( '>' )|wash}";
        $summary = self::read( 'design/admin4/templates/content/edit_validation.tpl' );
        foreach ( array( '{$UnvalidatedAttributes.item.description', '{$UnvalidatedPlacements.item.text', '{$UnvalidatedCustomRules.item.text', '{$LogMessages.item' ) as $value )
        {
            $this->assertStringContainsString( $value . $chain, $summary );
            $this->assertStringNotContainsString( $value . '}', $summary, "$value is printed raw" );
        }
        $field = self::read( 'design/admin4/templates/content/edit_attribute.tpl' );
        $this->assertSame( 3, substr_count( $field, '{$attribute.validation_error' . $chain ) );
        $this->assertSame( 3, substr_count( $field, '{$attribute.validation_error' ) );
    }
}
