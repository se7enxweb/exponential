<?php
/**
 * File containing the ezpExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Object representing an eZ Publish extension
 */
class ezpExtension
{
    public $name;

    /**
     * Array of multiton instances (Multiton pattern)
     *
     * @see getInstance
     */
    private static $instances = array();

    /**
     * ezpExtension constructor.
     *
     * @param string $name Name of the extension
     */
    protected function __construct( $name )
    {
        $this->name = $name;
    }

    /**
     * ezpExtension constructor.
     *
     * @see $instances
     *
     * @param string $name Name of the extension
     * @return ezpExtension
     */
    public static function getInstance( $name )
    {
        if (! isset( self::$instances[$name] ) )
            self::$instances[$name] = new self( $name );

        return self::$instances[$name];
    }

    /**
     * Returns the loading order informations from extension.xml
     *
     * @return array array( before => array( a, b ), after => array( c, d ) ) or an empty array if not available
     */
    public function getLoadingOrder()
    {
        $return = array( 'before' => array(), 'after' => array() );

        $extensionPath = eZExtension::extensionPath( $this->name );
        if ( $extensionPath === false )
            return $return;

        if ( is_readable( $XMLDependencyFile = $extensionPath . "/extension.xml" ) )
        {
            $useErrors = libxml_use_internal_errors( true );
            $xml = simplexml_load_file( $XMLDependencyFile );
            $xmlErrors = libxml_get_errors();
            // Mode and error list are process-wide: restore them, or a persistent worker keeps collecting errors
            libxml_clear_errors();
            libxml_use_internal_errors( $useErrors );
            // xml parsing error
            if ( $xml === false )
            {
                eZDebug::writeError( $xmlErrors, "ezpExtension( {$this->name} )::getLoadingOrder()" );
                return null;
            }
            foreach ( $xml->dependencies as $dependenciesNode )
            {
                foreach ( $dependenciesNode as $dependencyType => $dependenciesNode )
                {
                    switch ( $dependencyType )
                    {
                        case 'requires':
                            $relationship = 'after';
                            break;

                        case 'uses':
                            $relationship = 'after';
                            break;

                        case 'extends':
                            $relationship = 'before';
                            break;
                    }

                    foreach ( $dependenciesNode as $dependency )
                    {
                        $return[$relationship][] = (string)$dependency['name'];
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Returns the extension informations
     * Uses the <metadata> of extension.xml by default, then tries ezinfo.php for backwards compatibility.
     * An extension.xml that carries no metadata (only a name, a summary or load order dependencies)
     * does not hide the ezinfo.php next to it.
     *
     * @since 4.4
     * @return array|null array of extension informations, or null if no source exists
     */
    public function getInfo()
    {
        $extensionPath = eZExtension::extensionPath( $this->name );
        if ( $extensionPath === false )
            return null;

        $return = null;
        // try extension.xml first
        if ( is_readable( $XMLFilePath = $extensionPath . "/extension.xml" ) )
        {
            $return = $this->getInfoFromExtensionXml( $XMLFilePath );
            if ( is_array( $return ) && count( $return ) > 0 )
                return $return;
        }

        // then try ezinfo.php, for backwards compatibility
        if ( is_readable( $infoFilePath = $extensionPath . "/ezinfo.php" ) )
        {
            $result = $this->getInfoFromEzInfo( $infoFilePath );
            if ( is_array( $result ) )
                return $result;
        }

        return $return;
    }

    /**
     * Reads the <metadata> of an extension.xml file
     *
     * @param string $XMLFilePath
     * @return array|null the metadata (empty when the file has none), or null when the file does not parse
     */
    protected function getInfoFromExtensionXml( $XMLFilePath )
    {
        $infoFields = array( 'name', 'description', 'version', 'copyright', 'author', 'license', 'info_url' );

        $useErrors = libxml_use_internal_errors( true );
        $xml = simplexml_load_file( $XMLFilePath );
        $xmlErrors = libxml_get_errors();
        // Mode and error list are process-wide: restore them, or a persistent worker keeps collecting errors
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        // xml parsing error
        if ( $xml === false )
        {
            eZDebug::writeError( $xmlErrors, "ezpExtension({$this->name})::getInfo()" );
            return null;
        }
        $return = array();
        $metadataNode = $xml->metadata;

        // standard extension metadata
        foreach ( $infoFields as $field )
        {
            if ( (string)$metadataNode->$field !== '' )
                $return[$field] = (string)$metadataNode->$field;
        }

        // 3rd party software
        if ( !$metadataNode->software->uses )
            return $return;

        $index = 1;
        foreach ( $metadataNode->software->uses as $software )
        {
            $label = "Includes the following third-party software";
            if ( $index > 1 )
                $label .= " (" . $index . ")";

            foreach ( $infoFields as $field )
            {
                if ( (string)$software->$field !== '' )
                    $return[$label][$field] = (string)$software->$field;
            }
            $index++;
        }

        return $return;
    }

    /**
     * Calls the static info() of the <extension>Info class declared in an ezinfo.php file
     *
     * A broken file (a parse error, or an info() that throws) is logged and skipped, so one
     * extension cannot take down the page that lists them all.
     *
     * @param string $infoFilePath
     * @return array|null
     */
    protected function getInfoFromEzInfo( $infoFilePath )
    {
        $className = $this->name . 'Info';
        try
        {
            include_once( $infoFilePath );
            if ( !is_callable( array( $className, 'info' ) ) )
                return null;

            $result = call_user_func_array( array( $className, 'info' ), array() );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeNotice( get_class( $e ) . ': ' . $e->getMessage() . " in $infoFilePath", "ezpExtension({$this->name})::getInfo()" );
            return null;
        }

        return is_array( $result ) ? $result : null;
    }
}
?>
