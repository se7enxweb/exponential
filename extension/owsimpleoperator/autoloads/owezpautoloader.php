<?php

/*!
 * Provides an access to the native autoload functionality for eZ Publish
 */
class OWEzpAutoloader extends ezpAutoloader
{
    /*!
     * Return the file path for a class name
     * Return null if the class is not found
     */
    public static function get_class_file_path( $className )
    {
        $path = null;
        
        if ( self::$ezpClasses === null )
        {
            self::autoload( $className );
        }

        if ( isset( self::$ezpClasses[$className] ) )
        {
            $path = self::$ezpClasses[$className];
        }
        
        return $path;
    }
    /*!
     * Return the folder path for a class name
     * Return null if the class is not found
     */
    public static function get_class_folder_path( $className )
    {
        return dirname( self::get_class_file_path( $className ) );
    }
}
        
?>