<?php
/**
 * The content classes the GraphQL schema is generated from: every defined class with its attributes, as plain
 * arrays, filtered by graphql.ini (ClassList[], ExcludedClassList[], HiddenFieldList[], HiddenDatatypeList[]).
 *
 * A class:     array( 'id', 'identifier', 'name', 'description', 'attributes' => array( <attribute>, ... ) )
 * An attribute: array( 'id', 'identifier', 'datatype', 'name', 'description', 'required', 'options' )
 *              (options: the choices of a selection, id => name)
 *
 * fromDatabase() reads the classes; filter() and the rest need no database, so a test builds the schema from a
 * fixture catalogue.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLClassCatalogue
{
    /** @var array class identifier => class */
    protected $classes;

    /**
     * @param array $classes a list of classes (see the class comment)
     */
    public function __construct( array $classes )
    {
        $this->classes = array();
        foreach ( $classes as $class )
            $this->classes[(string)$class['identifier']] = $class;
    }

    /**
     * The defined classes of the database, filtered by graphql.ini.
     *
     * @return self
     */
    public static function fromDatabase()
    {
        $classes = array();
        foreach ( eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
        {
            $attributes = array();
            foreach ( $class->fetchAttributes() as $classAttribute )
            {
                $datatype = (string)$classAttribute->attribute( 'data_type_string' );
                $options = array();
                if ( $datatype === 'ezselection' )
                {
                    $content = $classAttribute->content();
                    if ( is_array( $content ) && isset( $content['options'] ) && is_array( $content['options'] ) )
                    {
                        foreach ( $content['options'] as $option )
                            $options[(int)$option['id']] = (string)$option['name'];
                    }
                }
                $attributes[] = array( 'id' => (int)$classAttribute->attribute( 'id' ),
                                       'identifier' => (string)$classAttribute->attribute( 'identifier' ),
                                       'datatype' => $datatype,
                                       'name' => (string)$classAttribute->attribute( 'name' ),
                                       'description' => (string)$classAttribute->attribute( 'description' ),
                                       'required' => (bool)$classAttribute->attribute( 'is_required' ),
                                       'options' => $options );
            }
            $classes[] = array( 'id' => (int)$class->attribute( 'id' ),
                                'identifier' => (string)$class->attribute( 'identifier' ),
                                'name' => (string)$class->attribute( 'name' ),
                                'description' => (string)$class->attribute( 'description' ),
                                'attributes' => $attributes );
        }
        return self::filtered( $classes, self::settings() );
    }

    /**
     * The classes of fromDatabase(), from a cache file when it is current: one file per siteaccess, filter
     * settings, languages and class change (the expiry class-identifier-cache, set whenever a class is stored). A
     * PHP file that OPcache keeps, so a request reads no class from the database. A new file is written under a new
     * name, never over one a worker may have cached.
     *
     * @return self
     */
    public static function cached()
    {
        $handler = eZExpiryHandler::instance();
        $classTime = $handler->hasTimestamp( 'class-identifier-cache' ) ? (int)$handler->timestamp( 'class-identifier-cache' ) : 0;
        $key = substr( sha1( json_encode( array( 1, expGraphQL::currentSiteAccess(), self::settings(), $classTime,
                                                 array_values( (array)eZContentLanguage::prioritizedLanguageCodes() ) ) ) ), 0, 20 );
        $dir = self::cacheDirectory();
        $file = $dir . '/catalogue-' . $key . '.php';
        if ( is_file( $file ) )
        {
            $classes = include $file;
            if ( is_array( $classes ) )
                return new self( $classes );
        }
        $catalogue = self::fromDatabase();
        eZFile::create( basename( $file ), $dir, "<?php\n// GraphQL class catalogue, generated; removed by ezcache --clear-id=graphql\nreturn " .
                                                 var_export( array_values( $catalogue->classes() ), true ) . ";\n", true );
        return $catalogue;
    }

    /**
     * @return string the directory of the GraphQL caches of the siteaccess (var/<site>/cache/graphql)
     */
    public static function cacheDirectory()
    {
        return eZSys::cacheDirectory() . '/graphql';
    }

    /**
     * The filter settings of graphql.ini.
     *
     * @return array ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList
     */
    public static function settings()
    {
        $settings = array();
        foreach ( array( 'ClassList', 'ExcludedClassList', 'HiddenFieldList', 'HiddenDatatypeList' ) as $name )
            $settings[$name] = expGraphQL::listSetting( $name );
        return $settings;
    }

    /**
     * @param array $classes a list of classes
     * @param array $settings ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList (each a list)
     * @return self the classes the schema shows, with the attributes it shows
     */
    public static function filtered( array $classes, array $settings )
    {
        $settings += array( 'ClassList' => array(), 'ExcludedClassList' => array(), 'HiddenFieldList' => array(), 'HiddenDatatypeList' => array() );
        $kept = array();
        foreach ( $classes as $class )
        {
            if ( !self::isExposedIdentifier( $class['identifier'], $settings ) )
                continue;
            $attributes = array();
            foreach ( $class['attributes'] as $attribute )
            {
                if ( in_array( $attribute['datatype'], $settings['HiddenDatatypeList'], true )
                     || in_array( $class['identifier'] . '/' . $attribute['identifier'], $settings['HiddenFieldList'], true )
                     || in_array( '*/' . $attribute['identifier'], $settings['HiddenFieldList'], true ) )
                    continue;
                $attributes[] = $attribute;
            }
            $class['attributes'] = $attributes;
            $kept[] = $class;
        }
        return new self( $kept );
    }

    /**
     * @param string $identifier
     * @param array $settings ClassList and ExcludedClassList
     * @return bool the class is in the schema
     */
    public static function isExposedIdentifier( $identifier, array $settings )
    {
        if ( !empty( $settings['ClassList'] ) )
            return in_array( $identifier, $settings['ClassList'], true );
        return empty( $settings['ExcludedClassList'] ) || !in_array( $identifier, $settings['ExcludedClassList'], true );
    }

    /**
     * @return array class identifier => class
     */
    public function classes()
    {
        return $this->classes;
    }

    /**
     * @param string $identifier
     * @return array|null
     */
    public function classByIdentifier( $identifier )
    {
        return isset( $this->classes[$identifier] ) ? $this->classes[$identifier] : null;
    }

    /**
     * @return string[] the identifiers of the classes in the schema
     */
    public function identifiers()
    {
        return array_keys( $this->classes );
    }

    /**
     * @return string a short hash of the classes and attributes (the schema changes when it changes)
     */
    public function hash()
    {
        $parts = array();
        foreach ( $this->classes as $identifier => $class )
        {
            $attributes = array();
            foreach ( $class['attributes'] as $attribute )
                $attributes[] = $attribute['identifier'] . ':' . $attribute['datatype'] . ( empty( $attribute['options'] ) ? '' : ':' . implode( ',', array_keys( $attribute['options'] ) ) );
            $parts[] = $identifier . '(' . implode( ' ', $attributes ) . ')';
        }
        sort( $parts );
        return substr( sha1( implode( "\n", $parts ) ), 0, 12 );
    }
}

?>
