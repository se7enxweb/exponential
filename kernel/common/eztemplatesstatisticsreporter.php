<?php
/**
 * File containing the eZTemplatesStatisticsReporter class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit fb20bee0d2), Copyright (C) Mugo Web, GPL v2
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZTemplatesStatisticsReporter eztemplatesstatisticsreporter.php
  \brief Generates statistics of tempate usage.

*/

class eZTemplatesStatisticsReporter
{
    /*!
     static
    */
    static function generateStatistics( $as_html = true )
    {
        $stats = '';

        if ( !eZTemplate::isTemplatesUsageStatisticsEnabled() )
            return $stats;

        if ( $as_html )
        {
            $stats .= "<h3>Templates used to render the page:</h3>";
            $stats .= ( "<table id='templateusage' class='debug_resource_usage' title='List of used templates'>" .
                   "<tr><th title='Usage count of this particular template'>Usage</th>" .
                   "<th>Requested template</th>" .
                   "<th>Template</th>" .
                   "<th>Template loaded</th>" .
                   "<th>Edit</th>" .
                   "<th>Override</th></tr>" );
        }
        else
        {
            $formatString = "%-40s%-40s%-40s\n";
            $stats .= "Templates usage statistics\n";
            $stats .= sprintf( $formatString, 'Templates', 'Requested template', 'Template loaded' );
        }

        if ( $as_html )
        {
            $iconSizeX = 16;
            $iconSizeY = 16;
            $templateViewFunction = 'visual/templateview';
            eZURI::transformURI( $templateViewFunction );
            $templateEditFunction = 'visual/templateedit';
            eZURI::transformURI( $templateEditFunction );
            $templateOverrideFunction = 'visual/templatecreate';
            eZURI::transformURI( $templateOverrideFunction );

            $std_base = eZTemplateDesignResource::designSetting( 'standard' );
            $wwwDir = htmlspecialchars( (string)eZSys::wwwDir(), ENT_COMPAT, 'UTF-8' );
            $editIconFile = "$wwwDir/design/$std_base/images/edit.gif";
            $overrideIconFile = "$wwwDir/design/$std_base/images/override-template.gif";

            $tdClass = 'used_templates_stats1';
            $j = 0;

            $currentSiteAccess = $GLOBALS['eZCurrentAccess']['name'];

            $editUrlFormat = self::templateEditUrlFormat();
        }

        $templatesUsageStatistics = eZTemplate::templatesUsageStatistics();

        $alreadyListedTemplate = $templateCounts = array();

        //Generate usage count for each unique template first.
        foreach( $templatesUsageStatistics as $templateInfo )
        {
            $actualTemplateName = $templateInfo['actual-template-name'];

            if ( !array_key_exists( $actualTemplateName, $templateCounts ) )
            {
                $templateCounts[$actualTemplateName] = 1;

            }
            else
            {
                ++$templateCounts[$actualTemplateName];
            }
        }

        //Then create the actual listing
        foreach ($templatesUsageStatistics as $templateInfo)
        {
            $actualTemplateName = $templateInfo['actual-template-name'];
            $requestedTemplateName = $templateInfo['requested-template-name'];
            $templateFileName = $templateInfo['template-filename'];

            if ( !in_array( $actualTemplateName, $alreadyListedTemplate ) )
            {
                $alreadyListedTemplate[] = $actualTemplateName;
                if ( $as_html )
                {
                    $requestedTemplateViewURI = $templateViewFunction . '/' . $requestedTemplateName;
                    $actualTemplateViewURI = $templateViewFunction . '/' . $actualTemplateName;

                    $templateEditURI = $templateEditFunction . '/' . $templateFileName;
                    $templateOverrideURI = $templateOverrideFunction . '/' . $actualTemplateName;
                    $templateEditLink = $editUrlFormat === false
                        ? "$templateEditURI/(siteAccess)/$currentSiteAccess"
                        : self::templateEditUrl( $editUrlFormat, $templateEditURI, $currentSiteAccess, $templateFileName );

                    $actualTemplateNameOutput = ( $actualTemplateName == $requestedTemplateName ) ? "<em>&lt;No override&gt;</em>" : $actualTemplateName;

                    $stats .= (
                           "<tr class='data'><td>$templateCounts[$actualTemplateName]</td>" .
                           "<td><a href=\"$requestedTemplateViewURI\">$requestedTemplateName</a></td>" .
                           "<td>$actualTemplateNameOutput</td>" .
                           "<td>$templateFileName</td>" .
                           "<td><a href=\"$templateEditLink\"><img src=\"$editIconFile\" width=\"$iconSizeX\" height=\"$iconSizeY\" alt=\"Edit template\" title=\"Edit template\"></a></td>".
                           "<td><a href=\"$templateOverrideURI/(siteAccess)/$currentSiteAccess\"><img src=\"$overrideIconFile\" width=\"$iconSizeX\" height=\"$iconSizeY\" alt=\"Override template\" title=\"Override template\"></a></td></tr>" );

                    $j++;
                }
                else
                {
                    $stats .= sprintf( $formatString, $requestedTemplateName, $actualTemplateName, $templateFileName );
                }
            }
        }

        $totalTemplatesCount = count( $templatesUsageStatistics );
        $totalUniqueTemplatesCopunt = count( array_keys( $alreadyListedTemplate ) );

        if ( $as_html )
        {
            $stats .= "<tr class='summary'><td colspan=\"6\">Number of times templates used: $totalTemplatesCount<br>Number of unique templates used: $totalUniqueTemplatesCopunt</td></tr>";
            $stats .= "</table>";
        }
        else
        {
            $stats .= "\nTotal templates count: " . $totalTemplatesCount . "\n" . "Total unique templates count: " . $totalUniqueTemplatesCopunt . "\n";
        }

        return $stats;
    }

    /**
     * site.ini [DebugSettings] UsedTemplateEditUrl, the sprintf() format of the "Edit" links, or false when it is
     * empty or not set: then the links are the ones the list always had, <editor>/<file>/(siteAccess)/<siteaccess>.
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit fb20bee0d2), Copyright (C) Mugo Web, GPL v2
     *
     * @return string|false
     */
    static function templateEditUrlFormat()
    {
        $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'DebugSettings', 'UsedTemplateEditUrl' ) )
            return false;
        $format = trim( (string)$ini->variable( 'DebugSettings', 'UsedTemplateEditUrl' ) );
        return $format === '' ? false : $format;
    }

    /**
     * The "Edit" link of a template by the format of site.ini [DebugSettings] UsedTemplateEditUrl, HTML-escaped:
     * %1$s the template editor's address with the template file, %2$s the siteaccess, %3$s the template file.
     * A format sprintf() refuses (a placeholder beyond %3$s, a stray %) gives the link the list always had.
     *
     * @param string $format
     * @param string $templateEditURI the address of visual/templateedit followed by the template file
     * @param string $siteAccess
     * @param string $templateFileName
     * @return string
     */
    static function templateEditUrl( $format, $templateEditURI, $siteAccess, $templateFileName )
    {
        try
        {
            $url = sprintf( $format, $templateEditURI, $siteAccess, $templateFileName );
        }
        catch ( \Throwable $e )
        {
            eZDebug::writeWarning( 'site.ini [DebugSettings] UsedTemplateEditUrl is not a usable sprintf() format: ' . $e->getMessage(), __METHOD__ );
            $url = "$templateEditURI/(siteAccess)/$siteAccess";
        }
        return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
    }
}

?>
