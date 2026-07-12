<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$parameters = $Params["Parameters"];

$overrideKeys = array( 'nodeID' => $Params['NodeID'],
                       'objectID' => $Params['ObjectID'],
                       'classID' => $Params['ClassID'] );


$ini = eZINI::instance();
$tpl = eZTemplate::factory();

// Todo: read from siteaccess settings
$siteAccess = $Params['SiteAccess'];
if( $siteAccess )
    $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $siteAccess );
else
    $siteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );

$siteBase = $siteAccess;

$siteINI = eZINI::instance( 'site.ini', 'settings', null, null, true );
$siteINI->prependOverrideDir( "siteaccess/$siteAccess", false, 'siteaccess' );
$siteINI->loadCache();
$siteDesign = $siteINI->variable( "DesignSettings", "SiteDesign" );

$template = "";
foreach ( $parameters as $param )
{
    $template .= "/$param";
}


// If the requested template is not a base source, try to find the source
// template that it overrides and redirect to the source template view.
$overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );
if ( !isset( $overrideArray[$template] ) ||
     !isset( $overrideArray[$template]['base_dir'] ) ||
     !isset( $overrideArray[$template]['template'] ) )
{
    $templateFile = ltrim( $template, '/' );
    $sourceTemplate = false;
    foreach ( $overrideArray as $overrideSource => $overrideSetting )
    {
        if ( isset( $overrideSetting['custom_match'] ) )
        {
            foreach ( $overrideSetting['custom_match'] as $customMatch )
            {
                if ( !empty( $customMatch['match_file'] ) &&
                     substr( $customMatch['match_file'], -strlen( $templateFile ) ) === $templateFile )
                {
                    $sourceTemplate = $overrideSource;
                    break 2;
                }
            }
        }
    }

    if ( $sourceTemplate !== false )
    {
        $module->redirectTo( '/visual/templateview' . $sourceTemplate );
        return eZModule::HOOK_STATUS_CANCEL_RUN;
    }
}

$templateType = 'default';
if ( strpos( $template, "node/view" ) )
{
    $templateType = 'node_view';
}
else if ( strpos( $template, "content/view" ) )
{
    $templateType = 'object_view';
}
else if ( strpos( $template, "content/edit" ) )
{
    $templateType = 'object_view';
}
else if ( strpos( $template, "pagelayout.tpl" ) )
{
    $templateType = 'pagelayout';
}

$error = false;
$templateName = false;
$designExtension = '';

$designINI = eZINI::instance( 'design.ini' );
$designExtensionList = $designINI->variable( 'ExtensionSettings', 'DesignExtensions' );
if ( $designExtensionList !== array() )
{
    $designExtension = $designExtensionList[0];
}

if ( $module->isCurrentAction( 'CreateOverride' ) )
{
    $templateName = trim( $http->postVariable( 'TemplateName' ) );
    if ( $http->hasPostVariable( 'DesignExtension' ) )
    {
        $designExtension = trim( $http->postVariable( 'DesignExtension' ) );
    }

    if ( !isset( $overrideArray[$template] ) ||
         !isset( $overrideArray[$template]['base_dir'] ) ||
         !isset( $overrideArray[$template]['template'] ) )
    {
        $error = "invalid_template";
        eZDebug::writeError( "Cannot create override for non-existent or non-source template: $template", "Template override" );
    }
    else if ( preg_match( "#^[0-9a-z_]+(/[0-9a-z_]+)*$#", $templateName ) )
    {
        $templateName = trim( $http->postVariable( 'TemplateName' ) );
        // The override.ini group name cannot contain slashes, so derive a safe
        // identifier from the filename while preserving the existing group for
        // paths such as full/frontpage -> full_frontpage.
        $overrideName = preg_replace( "#[^0-9a-z_]+#", "_", $templateName );
        $overrideName = trim( $overrideName, "_" );

        $filePath = "design/$siteDesign/override/templates";
        if ( $designExtension !== '' )
        {
            $filePath = eZExtension::baseDirectory() . "/" . $designExtension . "/" . $filePath;
        }
        $fileName = $filePath . "/" . $templateName . ".tpl";

        $templateCode = "";
        switch ( $templateType )
        {
            case "node_view":
            {
                $templateCode = generateNodeViewTemplate( $http, $template, $fileName );
            }break;

            case "object_view":
            {
                $templateCode = generateObjectViewTemplate( $http, $template, $fileName );
            }break;

            case "pagelayout":
            {
                $templateCode = generatePagelayoutTemplate( $http, $template, $fileName );
            }break;

            default:
            {
                $templateCode = generateDefaultTemplate( $http, $template, $fileName );
            }break;
        }

        $fileDir = dirname( $fileName );
        if ( !file_exists( $fileDir ) )
        {
            eZDir::mkdir( $fileDir, false, true );
        }


        $fp = fopen( $fileName, "w+" );
        if ( $fp )
        {
            $filePermission = $ini->variable( 'FileSettings', 'StorageFilePermissions' );
            $oldumask = umask( 0 );
            fwrite( $fp, $templateCode );
            fclose( $fp );
            chmod( $fileName, octdec( $filePermission ) );
            umask( $oldumask );

            // Store override.ini.append file
            // Clear stale INI cache first so the newly created group is merged
            // with the current override list and saved with the top priority.
            eZCache::clearByID( array( 'global_ini', 'template-override' ) );

            $overrideINI = eZINI::instance( 'override.ini', 'settings', null, null, true );
            $overrideINI->prependOverrideDir( "siteaccess/$siteAccess", false, 'siteaccess' );
            $overrideINI->loadCache();

            $templateFile = preg_replace( "#^/(.*)$#", "\\1", $template );

            $overrideINI->setVariable( $overrideName, 'Source', $templateFile );
            $overrideINI->setVariable( $overrideName, 'MatchFile', $templateName . ".tpl" );
            $overrideINI->setVariable( $overrideName, 'Subdir', "templates" );

            if ( $http->hasPostVariable( 'Match' ) )
            {
                $matchArray = $http->postVariable( 'Match' );

                foreach ( array_keys( $matchArray ) as $matchKey )
                {
                    if ( $matchArray[$matchKey] == -1 or trim( $matchArray[$matchKey] ) == "" )
                        unset( $matchArray[$matchKey] );
                }
                $overrideINI->setVariable( $overrideName, 'Match', $matchArray );
            }

            // New overrides are inserted with the highest priority so they take
            // effect immediately without requiring a manual reorder.
            $overrideINI->setVariable( $overrideName, 'Priority', 0 );

            $oldumask = umask( 0 );
            $overrideINI->save( "siteaccess/$siteAccess/override.ini.append" );
            $overridePath = "settings/siteaccess/$siteAccess/override.ini.append.php";
            if ( file_exists( $overridePath ) )
            {
                $s = stat($overridePath);
                $mode = $s["mode"] & 0777; // get only the last 9 bits.
                if ($mode & $filePermission != $filePermission ) // filePermission wrong?
                {
                    chmod( $overridePath, octdec( $filePermission ) );
                }
            }
            umask( $oldumask );

            // Expire content view cache
            eZContentCacheManager::clearAllContentCache();

            // Clear global INI cache and template override cache so the new
            // override.ini group is picked up in the same request.
            eZCache::clearByID( array( 'global_ini', 'template-override' ) );
        }
        else
        {
            $error = "permission_denied";
            eZDebug::writeError( "Could not create override template, check permissions on $fileName", "Template override" );
        }
    }
    else
    {
        $error = "invalid_name";
    }

    if ( $error == false )
    {
        $module->redirectTo( '/visual/templateview'. $template );
        return eZModule::HOOK_STATUS_CANCEL_RUN;
    }
}
else if( $module->isCurrentAction( 'CancelOverride' ) )
{
   $module->redirectTo( '/visual/templateview'. $template );
}


function generateDefaultCopyCode( $http, $template, $classIdentifier = '' )
{
    $templateCode = "";
    $siteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );
    $overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );
    $bases = eZTemplateDesignResource::allDesignBases( $siteAccess );
    $templateName = trim( $http->postVariable( 'TemplateName' ) );
    $templateFileName = $templateName . ".tpl";

    $sourceFileName = false;

    // If a specific source template was selected in the GUI, use it.
    if ( $http->hasPostVariable( 'TemplateSource' ) )
    {
        $selectedSource = trim( $http->postVariable( 'TemplateSource' ) );
        if ( $selectedSource !== '' && file_exists( $selectedSource ) )
        {
            $sourceFileName = $selectedSource;
        }
    }

    // Otherwise, for node/object view templates, try the class-specific
    // override template by convention (e.g. full/frontpage.tpl for class "frontpage").
    if ( $sourceFileName === false && $classIdentifier !== '' )
    {
        $classTemplateFileName = "full/" . $classIdentifier . ".tpl";

        if ( isset( $overrideArray[$template]['custom_match'] ) )
        {
            foreach ( $overrideArray[$template]['custom_match'] as $customMatch )
            {
                if ( !empty( $customMatch['match_file'] ) &&
                     substr( $customMatch['match_file'], -strlen( $classTemplateFileName ) ) === $classTemplateFileName )
                {
                    $sourceFileName = $customMatch['match_file'];
                    break;
                }
            }
        }

        if ( $sourceFileName === false )
        {
            $triedFiles = array();
            $fileInfo = eZTemplateDesignResource::fileMatch( $bases, 'override/templates', $classTemplateFileName, $triedFiles );
            if ( is_array( $fileInfo ) && isset( $fileInfo['path'] ) )
            {
                $sourceFileName = $fileInfo['path'];
            }
        }
    }

    // If the new filename matches an existing override for the same source,
    // copy the existing override contents (e.g. full/frontpage.tpl).
    if ( $sourceFileName === false && isset( $overrideArray[$template]['custom_match'] ) )
    {
        foreach ( $overrideArray[$template]['custom_match'] as $customMatch )
        {
            if ( !empty( $customMatch['match_file'] ) &&
                 substr( $customMatch['match_file'], -strlen( $templateFileName ) ) === $templateFileName )
            {
                $sourceFileName = $customMatch['match_file'];
                break;
            }
        }
    }

    // Finally, fall back to the actual default template for the source.
    if ( $sourceFileName === false )
    {
        if ( isset( $overrideArray[$template] ) &&
             !empty( $overrideArray[$template]['base_dir'] ) &&
             !empty( $overrideArray[$template]['template'] ) )
        {
            $sourceFileName = $overrideArray[$template]['base_dir'] . $overrideArray[$template]['template'];
        }
        else
        {
            $templatePath = ltrim( $template, '/' );
            $triedFiles = array();
            $fileInfo = eZTemplateDesignResource::fileMatch( $bases, 'templates', $templatePath, $triedFiles );
            if ( is_array( $fileInfo ) && isset( $fileInfo['path'] ) )
            {
                $sourceFileName = $fileInfo['path'];
            }
        }
    }

    if ( $sourceFileName !== false && file_exists( $sourceFileName ) )
    {
        $fp = fopen( $sourceFileName, 'rb' );
        if ( $fp )
        {
            $fileSize = @filesize( $sourceFileName );
            if ( $fileSize !== false && $fileSize > 0 )
            {
                $codeFromFile = fread( $fp, $fileSize );
                $templateCode = preg_replace( '@^{\*\s*DO\sNOT\sEDIT.*?\*}\n(.*)@s', '$1', $codeFromFile );
            }
            fclose( $fp );
        }
        else
        {
            eZDebug::writeError( "Could not open file $sourceFileName, check read permissions" );
        }
    }
    else
    {
        eZDebug::writeError( "Template source not found for $templateFileName", __FUNCTION__ );
    }

    return $templateCode;
}

function generateNodeViewTemplate( $http, $template, $fileName )
{
    $matchArray = $http->postVariable( 'Match' );

    $templateCode = "";
    $classIdentifier = isset( $matchArray['class_identifier'] ) ? $matchArray['class_identifier'] : '';

    $class = eZContentClass::fetchByIdentifier( $classIdentifier );

    // Check what kind of contents we should create in the template
    switch ( $http->postVariable( 'TemplateContent' ) )
    {
        case 'DefaultCopy' :
        {
            $templateCode = generateDefaultCopyCode( $http, $template, $classIdentifier );
        }break;

        case 'ContainerTemplate' :
        {
            $templateCode = "<h1>{\$node.name}</h1>\n\n";

            // Append attribute view
            if ( $class instanceof eZContentClass )
            {
                $attributes = $class->fetchAttributes();
                foreach ( $attributes as $attribute )
                {
                    $identifier = $attribute->attribute( 'identifier' );
                    $name = $attribute->attribute( 'name' );
                    $templateCode .= "<h2>$name</h2>\n";
                    $templateCode .= "{attribute_view_gui attribute=\$node.object.data_map.$identifier}\n\n";
                }
            }

            $templateCode .= "" .
                 "{let page_limit=20\n" .
                 "    children=fetch('content','list',hash(parent_node_id,\$node.node_id,sort_by,\$node.sort_array,limit,\$page_limit,offset,\$view_parameters.offset))" .
                 "    list_count=fetch('content','list_count',hash(parent_node_id,\$node.node_id))}\n" .
                 "\n" .
                 "{section name=Child loop=\$children sequence=array(bglight,bgdark)}\n" .
                 "{node_view_gui view=line content_node=\$Child:item}\n" .
                 "{/section}\n" .

                 "{include name=navigator\n" .
                 "    uri='design:navigator/google.tpl'\n" .
                 "    page_uri=concat('/content/view','/full/',\$node.node_id)\n" .
                 "    item_count=\$list_count\n" .
                 "    view_parameters=\$view_parameters\n" .
                 "    item_limit=\$page_limit}\n" .
            "{/let}\n";
        }break;

        case 'ViewTemplate' :
        {
            $templateCode = "<h1>{\$node.name}</h1>\n\n";

            // Append attribute view
            if ( $class instanceof eZContentClass )
            {
                $attributes = $class->fetchAttributes();
                foreach ( $attributes as $attribute )
                {
                    $identifier = $attribute->attribute( 'identifier' );
                    $name = $attribute->attribute( 'name' );
                    $templateCode .= "<h2>$name</h2>\n";
                    $templateCode .= "{attribute_view_gui attribute=\$node.object.data_map.$identifier}\n\n";
                }
            }

        }break;

        default:
        case 'EmptyFile' :
        {
        }break;
    }

    return $templateCode;
}


function generateObjectViewTemplate( $http, $template, $fileName )
{
    $matchArray = $http->postVariable( 'Match' );

    $templateCode = "";
    $classIdentifier = isset( $matchArray['class_identifier'] ) ? $matchArray['class_identifier'] : '';

    $class = $classIdentifier ? eZContentClass::fetchByIdentifier( $classIdentifier ) : false;

    // Check what kind of contents we should create in the template
    switch ( $http->postVariable( 'TemplateContent' ) )
    {
        case 'DefaultCopy' :
        {
            $templateCode = generateDefaultCopyCode( $http, $template, $classIdentifier );
        }break;

        case 'ViewTemplate' :
        {
            $templateCode = "<h1>{\$object.name}</h1>\n\n";

            // Append attribute view
            if ( $class instanceof eZContentClass )
            {
                $attributes = $class->fetchAttributes();
                foreach ( $attributes as $attribute )
                {
                    $identifier = $attribute->attribute( 'identifier' );
                    $name = $attribute->attribute( 'name' );
                    $templateCode .= "<h2>$name</h2>\n";
                    $templateCode .= "{attribute_view_gui attribute=\$object.data_map.$identifier}\n\n";
                }
            }

        }break;

        default:
        case 'EmptyFile' :
        {
        }break;
    }
    return $templateCode;
}

function generatePagelayoutTemplate( $http, $template, $fileName )
{
    $templateCode = "";
    $classIdentifier = '';
    // Check what kind of contents we should create in the template
    switch ( $http->postVariable( 'TemplateContent' ) )
    {
        case 'DefaultCopy' :
        {
            $templateCode = generateDefaultCopyCode( $http, $template, $classIdentifier );
        }break;

        default:
        case 'EmptyFile' :
        {
            $templateCode = '{*?template charset=latin1?*}' .
                 '<!DOCTYPE html>' . "\n" .
                 '<html lang="en">' .
                 '<head>' . "\n" .
                 '    <link rel="stylesheet" type="text/css" href={"stylesheets/core.css"|ezdesign} />' . "\n" .
                 '    <link rel="stylesheet" type="text/css" href={"stylesheets/debug.css"|ezdesign} />' . "\n" .
                 '    {include uri="design:page_head.tpl"}' . "\n" .
                 '</head>' . "\n" .
                 '<body>' . "\n" .
                 '{$module_result.content}' . "\n" .
                 '<!--DEBUG_REPORT-->' . "\n" .
                 '</body>' . "\n" .
                 '</html>' . "\n";
        }break;
    }
    return $templateCode;
}

function generateDefaultTemplate( $http, $template, $fileName )
{
    $templateCode = "";
    $classIdentifier = '';
    // Check what kind of contents we should create in the template
    switch ( $http->postVariable( 'TemplateContent' ) )
    {
        case 'DefaultCopy' :
        {
            $templateCode = generateDefaultCopyCode( $http, $template, $classIdentifier );
        }break;

        default:
        case 'EmptyFile' :
        {
            $templateCode = '{*?template charset=latin1?*}' .
                 '<!DOCTYPE html>' . "\n" .
                 '<html lang="en">' .
                 '<head>' . "\n" .
                 '    <link rel="stylesheet" type="text/css" href={"stylesheets/core.css"|ezdesign} />' . "\n" .
                 '    <link rel="stylesheet" type="text/css" href={"stylesheets/debug.css"|ezdesign} />' . "\n" .
                 '    {include uri="design:page_head.tpl"}' . "\n" .
                 '</head>' . "\n" .
                 '<body>' . "\n" .
                 '{$module_result.content}' . "\n" .
                 '<!--DEBUG_REPORT-->' . "\n" .
                 '</body>' . "\n" .
                 '</html>' . "\n";
        }break;
    }
    return $templateCode;
}


$tpl->setVariable( 'error', $error );
$tpl->setVariable( 'template', $template );
$tpl->setVariable( 'template_type', $templateType );
$tpl->setVariable( 'template_name', $templateName );
$tpl->setVariable( 'site_base', $siteBase );
$tpl->setVariable( 'site_design', $siteDesign );
$tpl->setVariable( 'override_keys', $overrideKeys );
$tpl->setVariable( 'design_extension', $designExtension );

// Build a list of available source templates for the DefaultCopy option.
$templateSources = array();
$defaultSourcePath = '';
if ( $template !== '' )
{
    $bases = eZTemplateDesignResource::allDesignBases( $siteAccess );
    $overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );
    $templatePath = ltrim( $template, '/' );

    $triedFiles = array();
    $fileInfo = eZTemplateDesignResource::fileMatch( $bases, 'templates', $templatePath, $triedFiles );
    if ( is_array( $fileInfo ) && isset( $fileInfo['path'] ) && file_exists( $fileInfo['path'] ) )
    {
        $defaultSourcePath = $fileInfo['path'];
        $templateSources[] = array( 'path' => $fileInfo['path'], 'label' => 'Source: ' . $templatePath );
    }

    if ( isset( $overrideArray[$template]['custom_match'] ) )
    {
        foreach ( $overrideArray[$template]['custom_match'] as $customMatch )
        {
            if ( !empty( $customMatch['match_file'] ) && file_exists( $customMatch['match_file'] ) )
            {
                $path = $customMatch['match_file'];
                $label = 'Override: ' . $path;
                if ( ( $pos = strpos( $path, 'override/templates/' ) ) !== false )
                {
                    $label = 'Override: ' . substr( $path, $pos + strlen( 'override/templates/' ) );
                }
                else if ( ( $pos = strpos( $path, 'templates/' ) ) !== false )
                {
                    $label = 'Override: ' . substr( $path, $pos + strlen( 'templates/' ) );
                }
                $templateSources[] = array( 'path' => $path, 'label' => $label );
            }
        }
    }
}
$tpl->setVariable( 'default_template_sources', $templateSources );
$tpl->setVariable( 'default_template_source', $defaultSourcePath );

$Result = array();
$Result['content'] = $tpl->fetch( "design:visual/templatecreate.tpl" );
$Result['path'] = array( array( 'url' => "/visual/templatelist/",
                                'text' => ezpI18n::tr( 'kernel/design', 'Template list' ) ),
                         array( 'url' => "/visual/templateview". $template,
                                'text' => ezpI18n::tr( 'kernel/design', 'Template view' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/design', 'Create new template' ) ) );
?>
