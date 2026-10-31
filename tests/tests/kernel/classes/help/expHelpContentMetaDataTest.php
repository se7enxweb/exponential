<?php
/**
 * The help classes as every other content class (expHelpContentMetaData), without a database:
 *
 *  HMD-01  title fields: the names that hold a title, and the attributes of the object name pattern
 *  HMD-02  the schema: no title field of a help class is limited below 255 characters
 *  HMD-03  changes(): the attribute added, completed, left alone; title fields raised; nothing when in place
 *  HMD-04  changes() without xrowmetadata: only the title fields
 *  HMD-05  the provider: xrowmetadata only when it can be limited to the help classes (1.4.24), never all classes
 *  HMD-06  the upgrade step is registered in the scripts stage, after the help read policy
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentMetaDataTestOldProvider
{
    public static function addToEveryClass( $license = 'GPL-2.0-or-later' )
    {
        return array();
    }
}

class expHelpContentMetaDataTestProvider
{
    public static function addToEveryClass( $license = 'GPL-2.0-or-later', $identifiers = null )
    {
        return array();
    }
}

class expHelpContentMetaDataTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function entry( array $titles, $metadata, $exists = true, $pattern = '<title>' )
    {
        return array( 'exists' => $exists, 'name_pattern' => $pattern, 'metadata' => $metadata, 'titles' => $titles );
    }

    private static function meta( $datatype = 'xrowmetadata', $category = 'meta', $license = 'GPL-2.0-or-later' )
    {
        return array( 'datatype' => $datatype, 'category' => $category, 'license' => $license,
                      'allow_object_license' => true, 'og_source' => 'object' );
    }

    /** HMD-01 */
    public function testTitleFields()
    {
        foreach ( array( 'title', 'subtitle', 'name', 'headline', 'teaser_title', 'short_title', 'eyebrow_title' ) as $identifier )
            $this->assertTrue( expHelpContentMetaData::isTitleField( $identifier ), $identifier );
        foreach ( array( 'shortdesc', 'help_id', 'version_from', 'alt_text', 'caption', 'titles' ) as $identifier )
            $this->assertFalse( expHelpContentMetaData::isTitleField( $identifier ), $identifier );
        $this->assertTrue( expHelpContentMetaData::isTitleField( 'term', '<term>' ), 'the glossary entry is named by its term' );
        $this->assertTrue( expHelpContentMetaData::isTitleField( 'term', '<term|short_name>' ) );
        $this->assertFalse( expHelpContentMetaData::isTitleField( 'figure_id', '<caption|figure_id|alt_text>' ), 'a fallback of the name is not a title' );
        $this->assertFalse( expHelpContentMetaData::isTitleField( 'term', '<title>' ) );
        $this->assertFalse( expHelpContentMetaData::isTitleField( 'term', '<terms>' ) );
    }

    /** HMD-02 */
    public function testSchemaHasNoShortTitleField()
    {
        $limited = array();
        foreach ( expHelpContentSchema::classes() as $class => $definition )
        {
            foreach ( $definition['attributes'] as $identifier => $attribute )
            {
                if ( $attribute['type'] !== 'ezstring' || !expHelpContentMetaData::isTitleField( $identifier, $definition['name_pattern'] ) )
                    continue;
                $max = isset( $attribute['params']['max_length'] ) ? (int)$attribute['params']['max_length'] : 0;
                if ( $max > 0 && $max < expHelpContentMetaData::TITLE_MIN_LENGTH )
                    $limited[] = "$class.$identifier=$max";
            }
        }
        $this->assertSame( array(), $limited );
        $classes = expHelpContentSchema::classes();
        $this->assertSame( 255, $classes['help_glossary_entry']['attributes']['term']['params']['max_length'] );
    }

    /** HMD-03 */
    public function testChanges()
    {
        $state = array(
            'help_manual' => self::entry( array( 'title' => 255, 'subtitle' => 255 ), null ),
            'help_section' => self::entry( array( 'title' => 255 ), self::meta( 'xrowmetadata', '', '' ) ),
            'help_concept' => self::entry( array( 'title' => 255 ), self::meta( 'ezstring' ) ),
            'help_task' => self::entry( array( 'title' => 255 ), self::meta() ),
            'help_glossary_entry' => self::entry( array( 'term' => 100 ), self::meta(), true, '<term>' ),
            'help_screenshot' => self::entry( array( 'title' => 0 ), self::meta() ),
            'help_reference' => self::entry( array(), null, false ),
        );
        $this->assertSame( array(
            'class help_manual: add the attribute metadata (xrowmetadata, category meta, GPL-2.0-or-later)',
            'class help_section: complete the attribute metadata: category meta, default license GPL-2.0-or-later',
            'class help_glossary_entry: title field term from at most 100 characters to 255',
        ), expHelpContentMetaData::changes( $state, true ) );

        $inPlace = array( 'help_task' => self::entry( array( 'title' => 255 ), self::meta() ) );
        $this->assertSame( array(), expHelpContentMetaData::changes( $inPlace, true ) );
    }

    /** HMD-04 */
    public function testChangesWithoutXrowMetaData()
    {
        $state = array(
            'help_manual' => self::entry( array( 'title' => 255 ), null ),
            'help_glossary_entry' => self::entry( array( 'term' => 100 ), null, true, '<term>' ),
        );
        $this->assertSame( array( 'class help_glossary_entry: title field term from at most 100 characters to 255' ),
                           expHelpContentMetaData::changes( $state, false ) );
    }

    /** HMD-05 */
    public function testProvider()
    {
        list( $class, $reason ) = expHelpContentMetaData::providerFor( 'stdClass' );
        $this->assertNull( $class );
        $this->assertStringContainsString( '1.4.23', $reason );

        list( $class, $reason ) = expHelpContentMetaData::providerFor( 'expHelpContentMetaDataTestOldProvider' );
        $this->assertNull( $class, 'an addToEveryClass() that cannot be limited would change every class of the site' );
        $this->assertStringContainsString( '1.4.24', $reason );

        list( $class, $reason ) = expHelpContentMetaData::providerFor( 'expHelpContentMetaDataTestProvider' );
        $this->assertSame( 'expHelpContentMetaDataTestProvider', $class );
        $this->assertSame( '', $reason );

        list( $class, ) = expHelpContentMetaData::providerFor( 'NoSuchClassForHelpMetaData' );
        $this->assertNull( $class );
    }

    /** HMD-06 */
    public function testUpgradeStepRegistered()
    {
        $ini = file_get_contents( self::root() . '/settings/upgrade.ini' );
        $this->assertSame( 1, preg_match( '/^StepList\[\]=expUpgradeStepHelpReadPolicy\nStepList\[\]=expUpgradeStepHelpClassMetaData$/m', $ini ) );
        $step = new expUpgradeStepHelpClassMetaData();
        $this->assertSame( '6.0.15-help-class-metadata', $step->id() );
        $this->assertSame( 'scripts', $step->stage() );
        $this->assertSame( '6.0.15', $step->version() );
    }
}
