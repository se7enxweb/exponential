<?php
/**
 * File containing the expHelpContentMetaData class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The help classes as every other content class of an installation: the Meta Data attribute (identifier metadata,
 * datatype xrowmetadata, category meta, class default license GPL-2.0-or-later, license and Open Graph image chosen
 * per object) and no title field limited below 255 characters.
 *
 * The attribute is added by xrowmetadata (xrowMetaDataType::addToEveryClass() with the help class identifiers, from
 * xrowmetadata 1.4.24). The kernel does not require xrowmetadata: without it, or with a version that cannot be
 * limited to these classes, the help classes are left without the attribute and the reason is reported. The title
 * fields are the kernel's own and always raised.
 *
 * Used by expHelpContentInstaller (after the classes are made) and by the upgrade step
 * expUpgradeStepHelpClassMetaData. state() reads, changes() is pure, apply() writes.
 *
 * @since 6.0.15
 * @package kernel
 */
class expHelpContentMetaData
{
    const ATTRIBUTE = 'metadata';
    const DATATYPE = 'xrowmetadata';
    const CATEGORY = 'meta';
    const LICENSE = 'GPL-2.0-or-later';
    /** the smallest maximum length a title field may have (the column a sort key fits in) */
    const TITLE_MIN_LENGTH = 255;

    /**
     * Whether a text line attribute holds a title: title, name, headline, teaser_title, short_title, short_name,
     * sub_title, subtitle, site_name, any *_title, *_headline, *_heading, or the attribute the class's objects are
     * named by first (the first name of a <...> group of the object name pattern: the glossary entry's <term>; not a
     * fallback such as the figure's figure_id in <caption|figure_id|alt_text>).
     *
     * @param string $identifier
     * @param string $namePattern the class's object name pattern, e.g. "<term>"
     * @return bool
     */
    public static function isTitleField( $identifier, $namePattern = '' )
    {
        if ( preg_match( '/^(title|name|headline|heading|header|teaser_title|short_title|short_name|sub_title|subtitle|site_name)$|_title$|_headline$|_heading$/', (string)$identifier ) )
            return true;
        return preg_match( '/<\s*' . preg_quote( (string)$identifier, '/' ) . '\s*[|>]/', (string)$namePattern ) === 1;
    }

    /**
     * The xrowmetadata datatype class when it is active and can add the attribute to a list of classes, else a
     * reason it cannot.
     *
     * @return array array( class name or null, reason )
     */
    public static function provider()
    {
        $dataType = class_exists( 'eZDataType' ) ? eZDataType::create( self::DATATYPE ) : null;
        if ( !$dataType )
            return array( null, 'the datatype xrowmetadata is not available (extension not active)' );
        return self::providerFor( get_class( $dataType ) );
    }

    /**
     * @param string $class a datatype class name
     * @return array array( class name or null, reason )
     */
    public static function providerFor( $class )
    {
        if ( !is_string( $class ) || !class_exists( $class ) || !method_exists( $class, 'addToEveryClass' ) )
            return array( null, 'xrowmetadata cannot add the attribute to a class (before 1.4.23)' );
        $method = new ReflectionMethod( $class, 'addToEveryClass' );
        if ( !$method->isStatic() || $method->getNumberOfParameters() < 2 )
            return array( null, 'xrowmetadata cannot be limited to the help classes (before 1.4.24)' );
        return array( $class, '' );
    }

    /**
     * What the installation holds for the help classes.
     *
     * @param string[]|null $identifiers default: expHelpContentSchema::classIdentifiers()
     * @return array identifier => array( 'exists' => bool, 'name_pattern' => string, 'metadata' => null|array(
     *               'datatype', 'category', 'license', 'allow_object_license', 'og_source' ), 'titles' => array(
     *               attribute identifier => max length ) )
     */
    public static function state( $identifiers = null )
    {
        $identifiers = $identifiers === null ? expHelpContentSchema::classIdentifiers() : $identifiers;
        $state = array();
        foreach ( $identifiers as $identifier )
        {
            $class = eZContentClass::fetchByIdentifier( $identifier, true, eZContentClass::VERSION_STATUS_DEFINED );
            if ( !$class instanceof eZContentClass )
            {
                $state[$identifier] = array( 'exists' => false, 'name_pattern' => '', 'metadata' => null, 'titles' => array() );
                continue;
            }
            $entry = array( 'exists' => true, 'name_pattern' => (string)$class->attribute( 'contentobject_name' ), 'metadata' => null, 'titles' => array() );
            foreach ( $class->fetchAttributes() as $attribute )
            {
                $attributeIdentifier = $attribute->attribute( 'identifier' );
                if ( $attributeIdentifier === self::ATTRIBUTE )
                {
                    $default = @unserialize( (string)$attribute->attribute( 'data_text5' ), array( 'allowed_classes' => false ) );
                    $default = is_array( $default ) ? $default : array();
                    $entry['metadata'] = array(
                        'datatype' => $attribute->attribute( 'data_type_string' ),
                        'category' => (string)$attribute->attribute( 'category' ),
                        'license' => isset( $default['license'] ) ? (string)$default['license'] : '',
                        'allow_object_license' => !isset( $default['allow_object_license'] ) || (int)$default['allow_object_license'] !== 0,
                        'og_source' => (int)$attribute->attribute( 'data_int3' ) === 1 ? 'class' : 'object',
                    );
                }
                elseif ( $attribute->attribute( 'data_type_string' ) === 'ezstring' && self::isTitleField( $attributeIdentifier, $entry['name_pattern'] ) )
                {
                    $entry['titles'][$attributeIdentifier] = (int)$attribute->attribute( 'data_int1' );
                }
            }
            $state[$identifier] = $entry;
        }
        return $state;
    }

    /**
     * The changes the help classes need, one line each. Pure: from state().
     *
     * @param array $state state()
     * @param bool $withMetaData whether the Meta Data attribute can be added here (provider() found one)
     * @return string[]
     */
    public static function changes( array $state, $withMetaData )
    {
        $lines = array();
        foreach ( $state as $identifier => $entry )
        {
            if ( empty( $entry['exists'] ) )
                continue;
            foreach ( $entry['titles'] as $attribute => $max )
            {
                if ( $max > 0 && $max < self::TITLE_MIN_LENGTH )
                    $lines[] = "class $identifier: title field $attribute from at most $max characters to " . self::TITLE_MIN_LENGTH;
            }
            if ( !$withMetaData )
                continue;
            $meta = $entry['metadata'];
            if ( $meta === null )
            {
                $lines[] = "class $identifier: add the attribute " . self::ATTRIBUTE . ' (' . self::DATATYPE . ', category ' . self::CATEGORY . ', ' . self::LICENSE . ')';
                continue;
            }
            if ( $meta['datatype'] !== self::DATATYPE )
                continue; // an attribute of that identifier and another datatype is left alone
            $missing = array();
            if ( $meta['category'] !== self::CATEGORY )
                $missing[] = 'category ' . self::CATEGORY;
            if ( $meta['license'] === '' )
                $missing[] = 'default license ' . self::LICENSE;
            if ( $missing )
                $lines[] = "class $identifier: complete the attribute " . self::ATTRIBUTE . ': ' . implode( ', ', $missing );
        }
        return $lines;
    }

    /**
     * Raises the title fields and adds or completes the Meta Data attribute of the help classes.
     *
     * @param string[]|null $identifiers default: expHelpContentSchema::classIdentifiers()
     * @return string[] what was done; the last line says why the attribute was left out, when it was
     */
    public static function apply( $identifiers = null )
    {
        $identifiers = $identifiers === null ? expHelpContentSchema::classIdentifiers() : $identifiers;
        $done = array();
        foreach ( self::state( $identifiers ) as $identifier => $entry )
        {
            if ( empty( $entry['exists'] ) )
                continue;
            foreach ( $entry['titles'] as $attributeIdentifier => $max )
            {
                if ( $max <= 0 || $max >= self::TITLE_MIN_LENGTH )
                    continue;
                $class = eZContentClass::fetchByIdentifier( $identifier, true, eZContentClass::VERSION_STATUS_DEFINED );
                $attribute = $class ? $class->fetchAttributeByIdentifier( $attributeIdentifier ) : null;
                if ( !$attribute )
                    continue;
                $attribute->setAttribute( 'data_int1', self::TITLE_MIN_LENGTH );
                $attribute->store();
                $done[] = "class $identifier: title field $attributeIdentifier now at most " . self::TITLE_MIN_LENGTH . " characters (was $max)";
            }
        }
        list( $provider, $reason ) = self::provider();
        if ( $provider !== null )
        {
            $report = call_user_func( array( $provider, 'addToEveryClass' ), self::LICENSE, array_values( $identifiers ) );
            foreach ( array( 'added' => 'added the attribute ' . self::ATTRIBUTE, 'updated' => 'completed the attribute ' . self::ATTRIBUTE ) as $key => $what )
            {
                foreach ( isset( $report[$key] ) ? (array)$report[$key] : array() as $identifier )
                    $done[] = "class $identifier: $what";
            }
        }
        else
        {
            $done[] = 'Meta Data attribute left out: ' . $reason;
        }
        eZContentClass::expireCache();
        eZContentClassAttribute::expireCache();
        return $done;
    }
}
