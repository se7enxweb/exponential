<?php
/**
 * File containing the eZWordtoimageoperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZWordToImageOperator ezwordtoimageoperator.php
  \brief The class eZWordToImageOperator does

*/
class eZWordToImageOperator
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Operators = array( "wordtoimage",
                                  "mimetype_icon", "class_icon", "classgroup_icon", "action_icon", "icon",
                                  "flag_icon", "icon_info" );
        $this->IconInfo = array();
    }

    /*!
      Returns the template operators.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        switch ( $operatorName )
        {
            case "wordtoimage":
            {
                $ini = eZINI::instance("wordtoimage.ini");
                $iconRoot = $ini->variable( 'WordToImageSettings', 'IconRoot' );

                $replaceText = $ini->variable( 'WordToImageSettings', 'ReplaceText' );
                $replaceIcon = $ini->variable( 'WordToImageSettings', 'ReplaceIcon' );

                $wwwDirPrefix = "";
                if ( strlen( eZSys::wwwDir() ) > 0 )
                    $wwwDirPrefix = eZSys::wwwDir() . "/";
                foreach( $replaceIcon as $icon )
                {
                    // Issue 015718, constructing alt text from icon name
                    $aReplaceIconName = explode( '.', $icon );
                    $altText = htmlspecialchars( $aReplaceIconName[0], ENT_COMPAT, 'UTF-8' );
                    $icons[] = '<img src="' . htmlspecialchars( $wwwDirPrefix . $iconRoot .'/' . $icon, ENT_COMPAT, 'UTF-8' ) . '" alt="'.$altText.'"/>';
                }

                $operatorValue = str_replace( $replaceText, $icons, $operatorValue );
            } break;

            // icon_info( <type> ) => array() containing:
            // - repository - Repository path
            // - theme - Theme name
            // - theme_path - Theme path
            // - size_path_list - Associative array of size paths
            // - size_info_list - Associative array of size info (width and height)
            // - icons - Array of icon files, relative to theme and size path
            // - default - Default icon file, relative to theme and size path
            case 'icon_info':
            {
                if ( !isset( $operatorParameters[0] ) )
                {
                    $tpl->missingParameter( $operatorName, 'type' );
                    return;
                }
                $type = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace );

                // Check if we have it cached
                if ( isset( $this->IconInfo[$type] ) )
                {
                    $operatorValue = $this->IconInfo[$type];
                    return;
                }

                $ini = eZINI::instance( 'icon.ini' );
                $defaultRepository = $ini->variable( 'IconSettings', 'Repository' );
                $theme = $ini->variable( 'IconSettings', 'Theme' );
                $standardTheme = $ini->hasVariable( 'IconSettings', 'StandardTheme' ) ? $ini->variable( 'IconSettings', 'StandardTheme' ) : false;
                $extensions = $ini->hasVariable( 'ExtensionSettings', 'IconExtensions' ) ? $ini->variable( 'ExtensionSettings', 'IconExtensions' ) : array();

                $groups = array( 'mimetype' => 'MimeIcons',
                                 'class' => 'ClassIcons',
                                 'classgroup' => 'ClassGroupIcons',
                                 'action' => 'ActionIcons',
                                 'icon' => 'Icons' );
                $configGroup = $groups[$type];
                $mapNames = array( 'mimetype' => 'MimeMap',
                                   'class' => 'ClassMap',
                                   'classgroup' => 'ClassGroupMap',
                                   'action' => 'ActionMap',
                                   'icon' => 'IconMap' );
                $mapName = $mapNames[$type];

                // Check if the specific icon type has a theme setting
                if ( $ini->hasVariable( $configGroup, 'Theme' ) )
                {
                    $theme = $ini->variable( $configGroup, 'Theme' );
                }

                // Build the list of themes to search
                $availableThemes = $ini->hasVariable( 'IconSettings', 'AdditionalThemeList' ) ? $ini->variable( 'IconSettings', 'AdditionalThemeList' ) : array();
                if ( !is_array( $availableThemes ) )
                    $availableThemes = array();
                array_unshift( $availableThemes, $theme );
                if ( $standardTheme )
                    array_push( $availableThemes, $standardTheme );

                // Build the list of repositories to search
                $siteDir = eZSys::siteDir();
                $extensionDirectory = eZExtension::baseDirectory();
                if ( !is_array( $extensions ) )
                    $extensions = array();
                $matches = array( $defaultRepository );
                foreach ( $extensions as $extension )
                {
                    $matches[] = "$extensionDirectory/$extension/icons";
                }

                $iconInfo = false;
                foreach ( $availableThemes as $theme )
                {
                    foreach ( $matches as $repository )
                    {
                        $themePath = $repository . '/' . $theme;
                        if ( !is_dir( $siteDir . $themePath ) )
                            continue;

                        $themeRepositoryINI = eZINI::instance( 'icon.ini', $themePath );

                        $sizes = $themeRepositoryINI->variable( 'IconSettings', 'Sizes' );
                        if ( !is_array( $sizes ) )
                            $sizes = array();
                        if ( $ini->hasVariable( 'IconSettings', 'Sizes' ) )
                        {
                            $sizes = array_merge( $sizes,
                                                  $ini->variable( 'IconSettings', 'Sizes' ) );
                        }

                        $sizePathList = array();
                        $sizeInfoList = array();

                        if ( is_array( $sizes ) )
                        {
                            foreach ( $sizes as $key => $size )
                            {
                                $pathDivider = strpos( $size, ';' );
                                if ( $pathDivider !== false )
                                {
                                    $sizePath = substr( $size, $pathDivider + 1 );
                                    $size = substr( $size, 0, $pathDivider );
                                }
                                else
                                {
                                    $sizePath = $size;
                                }

                                $width = false;
                                $height = false;
                                $xDivider = strpos( $size, 'x' );
                                if ( $xDivider !== false )
                                {
                                    $width = (int)substr( $size, 0, $xDivider );
                                    $height = (int)substr( $size, $xDivider + 1 );
                                }
                                $sizePathList[$key] = $sizePath;
                                $sizeInfoList[$key] = array( $width, $height );
                            }
                        }

                        $map = array();

                        // Load mapping from theme
                        if ( $themeRepositoryINI->hasVariable( $configGroup, $mapName ) )
                        {
                            $map = array_merge( $map,
                                                $themeRepositoryINI->variable( $configGroup, $mapName ) );
                        }
                        // Load override mappings if they exist
                        if ( $ini->hasVariable( $configGroup, $mapName ) )
                        {
                            $map = array_merge( $map,
                                                $ini->variable( $configGroup, $mapName ) );
                        }

                        $default = false;
                        if ( $themeRepositoryINI->hasVariable( $configGroup, 'Default' ) )
                            $default = $themeRepositoryINI->variable( $configGroup, 'Default' );
                        if ( $ini->hasVariable( $configGroup, 'Default' ) )
                            $default = $ini->variable( $configGroup, 'Default' );

                        $iconInfo = array( 'repository' => $repository,
                                           'theme' => $theme,
                                           'theme_path' => $themePath,
                                           'size_path_list' => $sizePathList,
                                           'size_info_list' => $sizeInfoList,
                                           'icons' => $map,
                                           'default' => $default );

                        break 2;
                    }
                }

                // Fallback to the default repository / first theme if no theme dir found
                if ( $iconInfo === false )
                {
                    $theme = reset( $availableThemes );
                    $repository = reset( $matches );
                    $themePath = $repository . '/' . $theme;
                    $themeRepositoryINI = eZINI::instance( 'icon.ini', $themePath );

                    $sizes = $themeRepositoryINI->variable( 'IconSettings', 'Sizes' );
                    if ( !is_array( $sizes ) )
                        $sizes = array();
                    if ( $ini->hasVariable( 'IconSettings', 'Sizes' ) )
                    {
                        $sizes = array_merge( $sizes,
                                              $ini->variable( 'IconSettings', 'Sizes' ) );
                    }

                    $sizePathList = array();
                    $sizeInfoList = array();

                    if ( is_array( $sizes ) )
                    {
                        foreach ( $sizes as $key => $size )
                        {
                            $pathDivider = strpos( $size, ';' );
                            if ( $pathDivider !== false )
                            {
                                $sizePath = substr( $size, $pathDivider + 1 );
                                $size = substr( $size, 0, $pathDivider );
                            }
                            else
                            {
                                $sizePath = $size;
                            }

                            $width = false;
                            $height = false;
                            $xDivider = strpos( $size, 'x' );
                            if ( $xDivider !== false )
                            {
                                $width = (int)substr( $size, 0, $xDivider );
                                $height = (int)substr( $size, $xDivider + 1 );
                            }
                            $sizePathList[$key] = $sizePath;
                            $sizeInfoList[$key] = array( $width, $height );
                        }
                    }

                    $map = array();

                    if ( $themeRepositoryINI->hasVariable( $configGroup, $mapName ) )
                    {
                        $map = array_merge( $map,
                                            $themeRepositoryINI->variable( $configGroup, $mapName ) );
                    }
                    if ( $ini->hasVariable( $configGroup, $mapName ) )
                    {
                        $map = array_merge( $map,
                                            $ini->variable( $configGroup, $mapName ) );
                    }

                    $default = false;
                    if ( $themeRepositoryINI->hasVariable( $configGroup, 'Default' ) )
                        $default = $themeRepositoryINI->variable( $configGroup, 'Default' );
                    if ( $ini->hasVariable( $configGroup, 'Default' ) )
                        $default = $ini->variable( $configGroup, 'Default' );

                    $iconInfo = array( 'repository' => $repository,
                                       'theme' => $theme,
                                       'theme_path' => $themePath,
                                       'size_path_list' => $sizePathList,
                                       'size_info_list' => $sizeInfoList,
                                       'icons' => $map,
                                       'default' => $default );
                }

                $this->IconInfo[$type] = $iconInfo;
                $operatorValue = $iconInfo;
            } break;

            case 'flag_icon':
            {
                $ini = eZINI::instance( 'icon.ini' );
                $defaultRepository = $ini->variable( 'FlagIcons', 'Repository' );
                $theme = $ini->variable( 'FlagIcons', 'Theme' );
                $extensions = $ini->hasVariable( 'ExtensionSettings', 'IconExtensions' ) ? $ini->variable( 'ExtensionSettings', 'IconExtensions' ) : array();

                if ( !is_array( $extensions ) )
                    $extensions = array();

                $siteDir = eZSys::siteDir();
                $extensionDirectory = eZExtension::baseDirectory();

                $matches = array();
                foreach ( $extensions as $extension )
                {
                    $matches[] = "$extensionDirectory/$extension/icons";
                }
                $matches[] = $defaultRepository;

                $iconPath = false;
                $fallbackIconPath = false;
                foreach ( $matches as $repository )
                {
                    if ( !is_dir( $siteDir . $repository . '/' . $theme ) )
                        continue;

                    // Load icon settings from the theme
                    $themeINI = eZINI::instance( 'icon.ini', $repository . '/' . $theme );

                    $iconFormat = $themeINI->variable( 'FlagIcons', 'IconFormat' );
                    if ( $ini->hasVariable( 'FlagIcons', 'IconFormat' ) )
                    {
                        $iconFormat = $ini->variable( 'FlagIcons', 'IconFormat' );
                    }

                    $icon = $operatorValue . '.' . $iconFormat;
                    $candidateIconPath = $repository . '/' . $theme . '/' . $icon;

                    if ( is_readable( $siteDir . $candidateIconPath ) )
                    {
                        $iconPath = $candidateIconPath;
                        break;
                    }

                    $defaultIcon = $themeINI->variable( 'FlagIcons', 'DefaultIcon' );
                    $candidateIconPath = $repository . '/' . $theme . '/' . $defaultIcon . '.' . $iconFormat;
                    if ( $fallbackIconPath === false && is_readable( $siteDir . $candidateIconPath ) )
                    {
                        $fallbackIconPath = $candidateIconPath;
                    }
                }

                if ( $iconPath === false && $fallbackIconPath !== false )
                {
                    $iconPath = $fallbackIconPath;
                }

                if ( $iconPath === false )
                {
                    $iconPath = $defaultRepository . '/' . $theme . '/' . $operatorValue . '.gif';
                }

                if ( strlen( eZSys::wwwDir() ) > 0 )
                    $wwwDirPrefix = htmlspecialchars( eZSys::wwwDir(), ENT_COMPAT, 'UTF-8' ) . '/';
                else
                    $wwwDirPrefix = '/';
                $operatorValue = $wwwDirPrefix . $iconPath;
            } break;

            case 'mimetype_icon':
            case 'class_icon':
            case 'classgroup_icon':
            case 'action_icon':
            case 'icon':
            {
                // Determine whether we should return only the image URI instead of the whole HTML code.
                if ( isset( $operatorParameters[2] ) )
                    $returnURIOnly = $tpl->elementValue( $operatorParameters[2], $rootNamespace, $currentNamespace );
                else
                    $returnURIOnly = false;

                $ini = eZINI::instance( 'icon.ini' );
                $defaultRepository = $ini->variable( 'IconSettings', 'Repository' );
                $theme = $ini->variable( 'IconSettings', 'Theme' );
                $standardTheme = $ini->hasVariable( 'IconSettings', 'StandardTheme' ) ? $ini->variable( 'IconSettings', 'StandardTheme' ) : false;
                $extensions = $ini->hasVariable( 'ExtensionSettings', 'IconExtensions' ) ? $ini->variable( 'ExtensionSettings', 'IconExtensions' ) : array();

                if ( !is_array( $extensions ) )
                    $extensions = array();

                $siteDir = eZSys::siteDir();
                $extensionDirectory = eZExtension::baseDirectory();

                $groups = array( 'mimetype_icon' => 'MimeIcons',
                                 'class_icon' => 'ClassIcons',
                                 'classgroup_icon' => 'ClassGroupIcons',
                                 'action_icon' => 'ActionIcons',
                                 'icon' => 'Icons' );
                $configGroup = $groups[$operatorName];

                // Check if the specific icon type has a theme setting
                if ( $ini->hasVariable( $configGroup, 'Theme' ) )
                {
                    $theme = $ini->variable( $configGroup, 'Theme' );
                }

                // Build the list of themes to search
                $availableThemes = $ini->hasVariable( 'IconSettings', 'AdditionalThemeList' ) ? $ini->variable( 'IconSettings', 'AdditionalThemeList' ) : array();
                if ( !is_array( $availableThemes ) )
                    $availableThemes = array();
                array_unshift( $availableThemes, $theme );
                if ( $standardTheme )
                    array_push( $availableThemes, $standardTheme );

                // Build the list of repositories to search
                $matches = array();
                foreach ( $extensions as $extension )
                {
                    $matches[] = "$extensionDirectory/$extension/icons";
                }
                $matches[] = $defaultRepository;

                $sizeName = isset( $operatorParameters[0] )
                    ? $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace )
                    : $ini->variable( 'IconSettings', 'Size' );
                if ( !isset( $operatorParameters[0] ) && $ini->hasVariable( $configGroup, 'Size' ) )
                {
                    $theme = $ini->variable( $configGroup, 'Size' );
                }

                if ( isset( $operatorParameters[1] ) )
                {
                    $altText = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );
                }
                else
                {
                    $altText = $operatorValue;
                }

                // Default values in case no icon file is found
                $repository = $defaultRepository;
                $theme = reset( $availableThemes );
                $sizePath = $sizeName;
                $icon = 'mimetypes/empty.png';
                $width = false;
                $height = false;

                $iconFileAvailable = false;
                $fallbackIconField = false;
                foreach ( $availableThemes as $theme )
                {
                    foreach ( $matches as $repository )
                    {
                        if ( !is_dir( $siteDir . $repository . '/' . $theme ) )
                            continue;

                        // Load icon settings from the theme
                        $themeINI = eZINI::instance( 'icon.ini', $repository . '/' . $theme );

                        $sizes = $themeINI->variable( 'IconSettings', 'Sizes' );
                        if ( !is_array( $sizes ) )
                            $sizes = array();
                        if ( $ini->hasVariable( 'IconSettings', 'Sizes' ) )
                        {
                            $sizes = array_merge( $sizes,
                                                  $ini->variable( 'IconSettings', 'Sizes' ) );
                        }

                        if ( isset( $sizes[$sizeName] ) )
                        {
                            $size = $sizes[$sizeName];
                        }
                        else
                        {
                            $size = reset( $sizes );
                        }

                        $pathDivider = strpos( $size, ';' );
                        if ( $pathDivider !== false )
                        {
                            $sizePath = substr( $size, $pathDivider + 1 );
                            $size = substr( $size, 0, $pathDivider );
                        }
                        else
                        {
                            $sizePath = $size;
                        }

                        $width = false;
                        $height = false;
                        $xDivider = strpos( $size, 'x' );
                        if ( $xDivider !== false )
                        {
                            $width = (int)substr( $size, 0, $xDivider );
                            $height = (int)substr( $size, $xDivider + 1 );
                        }

                        if ( $operatorName == 'mimetype_icon' )
                        {
                            $icon = $this->iconGroupMapping( $ini, $themeINI,
                                                             'MimeIcons', 'MimeMap',
                                                             strtolower( $operatorValue ) );
                        }
                        else if ( $operatorName == 'class_icon' )
                        {
                            $icon = $this->iconDirectMapping( $ini, $themeINI,
                                                              'ClassIcons', 'ClassMap',
                                                              strtolower( $operatorValue ) );
                        }
                        else if ( $operatorName == 'classgroup_icon' )
                        {
                            $icon = $this->iconDirectMapping( $ini, $themeINI,
                                                              'ClassGroupIcons', 'ClassGroupMap',
                                                              strtolower( $operatorValue ) );
                        }
                        else if ( $operatorName == 'action_icon' )
                        {
                            $icon = $this->iconDirectMapping( $ini, $themeINI,
                                                              'ActionIcons', 'ActionMap',
                                                              strtolower( $operatorValue ) );
                        }
                        else if ( $operatorName == 'icon' )
                        {
                            $icon = $this->iconDirectMapping( $ini, $themeINI,
                                                              'Icons', 'IconMap',
                                                              strtolower( $operatorValue ) );
                        }

                        $filesystemIconPath = $siteDir . $repository . '/' . $theme . '/' . $sizePath . '/' . $icon;

                        $iconField = array( 'repository' => $repository,
                                            'theme' => $theme,
                                            'sizePath' => $sizePath,
                                            'icon' => $icon,
                                            'width' => $width,
                                            'height' => $height );

                        $themeDefault = $themeINI->hasVariable( $configGroup, 'Default' ) ? $themeINI->variable( $configGroup, 'Default' ) : false;
                        $iniDefault = $ini->hasVariable( $configGroup, 'Default' ) ? $ini->variable( $configGroup, 'Default' ) : false;
                        $isDefaultIcon = ( $themeDefault !== false && $icon == $themeDefault ) ||
                                         ( $iniDefault !== false && $icon == $iniDefault );

                        if ( is_file( $filesystemIconPath ) && !$isDefaultIcon )
                        {
                            $iconFileAvailable = true;
                            break;
                        }
                        else if ( $fallbackIconField === false && is_file( $filesystemIconPath ) && $isDefaultIcon )
                        {
                            $fallbackIconField = $iconField;
                        }
                    }

                    if ( $iconFileAvailable )
                    {
                        break;
                    }
                    else if ( $fallbackIconField !== false )
                    {
                        $repository = $fallbackIconField['repository'];
                        $theme = $fallbackIconField['theme'];
                        $sizePath = $fallbackIconField['sizePath'];
                        $icon = $fallbackIconField['icon'];
                        $width = $fallbackIconField['width'];
                        $height = $fallbackIconField['height'];
                    }
                    else
                    {
                        if ( $theme == $standardTheme && $repository == $defaultRepository )
                        {
                            eZDebug::writeError( "Missing icon file for '$operatorValue' with size '$sizeName'", "eZWordToImageOperator, case '$operatorName'" );
                        }
                    }
                }

                $iconPath = '/' . $repository . '/' . $theme;
                $iconPath .= '/' . $sizePath;
                $iconPath .= '/' . $icon;

                $wwwDirPrefix = "";
                if ( strlen( eZSys::wwwDir() ) > 0 )
                    $wwwDirPrefix = eZSys::wwwDir();
                $sizeText = '';
                if ( $width !== false and $height !== false )
                {
                    $sizeText = ' width="' . $width . '" height="' . $height . '"';
                }

                // The class will be detected by ezpngfix.js, which will force alpha blending in IE.
                if ( ( !isset( $sizeName ) || $sizeName == 'normal' || $sizeName == 'original' ) && strstr( strtolower( $iconPath ), ".png" ) )
                {
                    $class = 'class="transparent-png-icon" ';
                }
                else
                {
                    $class = '';
                }

                if ( $returnURIOnly )
                    $operatorValue = $wwwDirPrefix . $iconPath;
                else
                    $operatorValue = '<img ' . $class . 'src="' . htmlspecialchars( $wwwDirPrefix . $iconPath, ENT_COMPAT, 'UTF-8' ) . '"' . $sizeText . ' alt="' .  htmlspecialchars( $altText, ENT_COMPAT, 'UTF-8' ) . '" title="' . htmlspecialchars( $altText ) . '" />';
            } break;

            default:
            {
                eZDebug::writeError( "Unknown operator: $operatorName", "ezwordtoimageoperator.php" );
            }

        }

    }

    /*!
     \private
     Tries to find icon file by considering \a $matchItem as a single value.

     It will first try to match the whole \a $matchItem value in the mapping table.

     \return The relative path to the icon file.

     Example
     \code
     $icon = $this->iconDirectMapping( $ini, $themeINI, 'ClassIcons', 'ClassMap', 'Folder' );
     \endcode

     \sa iconGroupMapping
    */
    function iconDirectMapping( &$ini, &$themeINI, $iniGroup, $mapName, $matchItem )
    {
        $map = array();

        // Load mapping from theme
        if ( $themeINI->hasVariable( $iniGroup, $mapName ) )
        {
            $map = array_merge( $map,
                                $themeINI->variable( $iniGroup, $mapName ) );
        }
        // Load override mappings if they exist
        if ( $ini->hasVariable( $iniGroup, $mapName ) )
        {
            $map = array_merge( $map,
                                $ini->variable( $iniGroup, $mapName ) );
        }

        $icon = false;
        if ( isset( $map[$matchItem] ) )
        {
            $icon = $map[$matchItem];
        }
        if ( $icon === false )
        {
            if ( $themeINI->hasVariable( $iniGroup, 'Default' ) )
                $icon = $themeINI->variable( $iniGroup, 'Default' );
            if ( $ini->hasVariable( $iniGroup, 'Default' ) )
                $icon = $ini->variable( $iniGroup, 'Default' );
        }
        return $icon;
    }

    /*!
     \private
     Tries to find icon file by considering \a $matchItem as a group,
     split into two parts and separated by a slash.

     It will first try to match the whole \a $matchItem value and then
     the group name.

     \return The relative path to the icon file.

     Example
     \code
     $icon = $this->iconGroupMapping( $ini, $themeINI, 'MimeIcons', 'MimeMap', 'image/jpeg' );
     \endcode

     \sa iconDirectMapping
    */
    function iconGroupMapping( &$ini, &$themeINI, $iniGroup, $mapName, $matchItem )
    {
        $map = array();

        // Load mapping from theme
        if ( $themeINI->hasVariable( $iniGroup, $mapName ) )
        {
            $map = array_merge( $map,
                                $themeINI->variable( $iniGroup, $mapName ) );
        }
        // Load override mappings if they exist
        if ( $ini->hasVariable( $iniGroup, $mapName ) )
        {
            $map = array_merge( $map,
                                $ini->variable( $iniGroup, $mapName ) );
        }

        $icon = false;
        // See if we have a match for the whole match item
        if ( isset( $map[$matchItem] ) )
        {
            $icon = $map[$matchItem];
        }
        else
        {
            // If not we have to check the group (first part)
            $pos = strpos( $matchItem, '/' );
            if ( $pos !== false )
            {
                $mimeGroup = substr( $matchItem, 0, $pos );
                if ( isset( $map[$mimeGroup] ) )
                {
                    $icon = $map[$mimeGroup];
                }
            }
        }

        // No icon? If so use default
        if ( $icon === false )
        {
            if ( $themeINI->hasVariable( $iniGroup, 'Default' ) )
                $icon = $themeINI->variable( $iniGroup, 'Default' );
            if ( $ini->hasVariable( $iniGroup, 'Default' ) )
                $icon = $ini->variable( $iniGroup, 'Default' );
        }
        return $icon;
    }

    /// \privatesection
    public $Operators;
    public $IconInfo;
}
?>
