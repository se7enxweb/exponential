<?php
/**
 * The interface strings of help maintained as content (doc/eng-us/features/6.0/help-content.md) in every translation
 * catalogue that has the context kernel/exphelp, without a database.
 *
 *  HCL-01  every string of the help content templates and headings is in each such catalogue; translated in all but
 *          eng-US (the source) and untranslated (unfinished); the placeholders are kept
 *  HCL-02  the templates and the topic headings use exactly these strings in the context kernel/exphelp
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentTranslationsTest extends PHPUnit\Framework\TestCase
{
    /** The strings of the DITA download, the stored e-books, the overdue reviews and the topic headings */
    const STRINGS = array(
        'Download DITA', 'Download all as DITA', 'E-books',
        'Built from the released topics each time one of them is released, and kept with their earlier versions.',
        'File', 'Size', 'Updated',
        'Help topics overdue for review',
        'The review date has passed, or the topic has been in Review for more than %days days without a change.',
        'State', 'Overdue', 'Actions', 'Review date %date passed %days days ago', 'In Review for %days days without a change',
        'Edit', 'Set state', '%shown of %count overdue topics are listed. ./console exp:help:content review lists them all.',
        'Before you begin', 'Steps', 'Result', 'What to do next', 'Condition', 'Cause', 'Remedy',
        // the manual pages while the background job builds the manuals from the Markdown help
        'Manuals are being built (%1 of %2)',
        // the review workflow, the style guide check and "Was this helpful?" of a topic (parts/topic_workflow.tpl,
        // parts/topic_feedback.tpl, parts/feedback_reason.tpl, the templates of the extension exphelpsite)
        'Review and release', 'The topic is in Review now.', 'The topic is Released: readers see it now.', 'The topic is a Draft again.',
        'You wrote the current version, so someone else with the release role releases it.',
        'The state was not changed: your role does not allow this step.', 'Review workflow', 'Draft', 'Review', 'Back to Review',
        'Send to Review', 'Release', 'Back to Draft', 'Style guide check', 'The style guide check found nothing.',
        '%errors errors, %warnings warnings, %notices notices. An error keeps ./console exp:help:content check from passing.',
        'Level', 'Rule', 'Field', 'Finding', 'Was this helpful?', 'Readers answered yes %yes times and no %no times.',
        'Reasons given for no:', 'Thank you. Your answer helps us improve the help.', 'Yes', 'If not, what was wrong?',
        'No reason given', 'No', 'Only your answer is kept, not who you are.', 'It is out of date', 'It is hard to understand',
        'Something is missing', 'It is wrong', 'Another reason', 'This topic is not Released; readers do not see it.',
    );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /**
     * @return array language => source => array( translation, unfinished )
     */
    private static function catalogues()
    {
        $list = array();
        foreach ( glob( self::root() . '/share/translations/*/translation.ts' ) as $file )
        {
            $doc = new DOMDocument();
            if ( !@$doc->load( $file, LIBXML_NONET ) )
                continue;
            $xpath = new DOMXPath( $doc );
            $context = $xpath->query( '/TS/context[name="kernel/exphelp"]' );
            if ( !$context->length )
                continue;
            $messages = array();
            foreach ( $xpath->query( 'message', $context->item( 0 ) ) as $message )
            {
                $translation = $xpath->query( 'translation', $message )->item( 0 );
                $messages[$xpath->query( 'source', $message )->item( 0 )->textContent] = array(
                    $translation ? $translation->textContent : '', $translation && $translation->getAttribute( 'type' ) === 'unfinished' );
            }
            $list[basename( dirname( $file ) )] = $messages;
        }
        return $list;
    }

    /** HCL-01 */
    public function testEveryCatalogueHasTheStrings()
    {
        $catalogues = self::catalogues();
        $this->assertGreaterThanOrEqual( 20, count( $catalogues ) );
        foreach ( $catalogues as $language => $messages )
        {
            foreach ( self::STRINGS as $source )
            {
                // isset, not assertArrayHasKey(): PHPUnit 10 exports the value of every
                // assertion for its events, and that was the whole catalogue each time
                $this->assertTrue( isset( $messages[$source] ), "$language: $source" );
                list( $translation, $unfinished ) = $messages[$source];
                if ( $language === 'untranslated' )
                {
                    $this->assertTrue( $unfinished, "$language: $source" );
                    continue;
                }
                $this->assertNotSame( '', $translation, "$language: $source" );
                if ( $language === 'eng-US' )
                    $this->assertSame( $source, $translation );
                preg_match_all( '/%\w+/', $source, $want );
                preg_match_all( '/%\w+/', $translation, $have );
                sort( $want[0] );
                sort( $have[0] );
                $this->assertSame( $want[0], $have[0], "$language: placeholders of $source" );
            }
        }
    }

    /** HCL-02 */
    public function testTheTemplatesUseTheStrings()
    {
        $root = self::root();
        $text = '';
        foreach ( array( 'exphelp/manual.tpl', 'exphelp/parts/dashboard_review.tpl' ) as $template )
            $text .= (string)file_get_contents( "$root/design/admin4/templates/$template" );
        preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'\\|i18n\\( 'kernel\\/exphelp'/", $text, $m );
        $used = array_unique( $m[1] );
        foreach ( array_slice( self::STRINGS, 0, 17 ) as $source )
            $this->assertContains( $source, $used, $source );
        foreach ( array( 'help_task', 'help_troubleshooting' ) as $class )
        {
            foreach ( expHelpContentSchema::textFields( $class ) as $heading )
            {
                if ( $heading !== '' )
                    $this->assertContains( $heading, self::STRINGS, $heading );
            }
        }
    }
}

?>
