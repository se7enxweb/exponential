<?php
/**
 * The export of the URL map (content/urlexport), policy content/urltranslator: a form (format, what, siteaccess,
 * language) whose ExportButton answers with the file, written by expUrlMapExporter and expUrlMapFormats (CSV, JSON,
 * .xlsx, .ods, Apache or nginx rules). Nothing is read from the query string; the form posts.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @since 6.0.15
 */

namespace Exponential\View\Kernel\Content
{

class UrlExport extends \Exponential\Runnable\ModuleView
{
    /**
     * The export's options from a posted form, checked. Pure.
     *
     * @param array $post
     * @param string[] $siteaccesses
     * @param string[] $locales
     * @return array format, kinds, siteaccess, language, include_node_aliases
     */
    public static function options( array $post, array $siteaccesses, array $locales )
    {
        $format = isset( $post['Format'] ) && is_string( $post['Format'] ) && in_array( $post['Format'], \expUrlMapFormats::writable(), true ) ? $post['Format'] : \expUrlMapFormats::CSV;
        $kinds = array_values( array_intersect( \expUrlMapExporter::KINDS, isset( $post['Kinds'] ) ? array_filter( (array)$post['Kinds'], 'is_string' ) : array() ) );
        $siteaccess = isset( $post['SiteAccess'] ) && is_string( $post['SiteAccess'] ) && in_array( $post['SiteAccess'], $siteaccesses, true ) ? $post['SiteAccess'] : '';
        $language = isset( $post['Language'] ) && is_string( $post['Language'] ) && in_array( $post['Language'], $locales, true ) ? $post['Language'] : '';
        return array( 'format' => $format, 'kinds' => $kinds, 'siteaccess' => $siteaccess, 'language' => $language,
                      'include_node_aliases' => !empty( $post['IncludeNodeAliases'] ) );
    }

    /**
     * The name of the download. Pure.
     *
     * @param string $format
     * @param int $time
     * @return string
     */
    public static function fileName( $format, $time )
    {
        $definitions = \expUrlMapFormats::definitions();
        $extension = isset( $definitions[$format] ) ? $definitions[$format]['extensions'][0] : 'txt';
        return 'url-map-' . date( 'Y-m-d', (int)$time ) . '.' . $extension;
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];

        $Module = $Params['Module'];
        $environment = new \expUrlMapKernelEnvironment();
        $siteaccesses = $environment->siteaccesses();
        $languages = array();
        foreach ( \eZContentLanguage::fetchList() as $language )
            $languages[(string)$language->attribute( 'locale' )] = (string)$language->attribute( 'name' );
        $error = '';
        $form = array( 'format' => \expUrlMapFormats::CSV, 'kinds' => \expUrlMapExporter::KINDS, 'siteaccess' => '', 'language' => '', 'include_node_aliases' => false );

        if ( \expViewParameters::isPost() && isset( $_POST['ExportButton'] ) )
        {
            $form = self::options( $_POST, $siteaccesses, array_keys( $languages ) );
            if ( !$form['kinds'] )
                $error = 'error-no-kind';
            else
            {
                $rows = ( new \expUrlMapExporter() )->export( array( 'kinds' => $form['kinds'], 'siteaccess' => $form['siteaccess'], 'language' => $form['language'],
                                                                   'include_node_aliases' => $form['include_node_aliases'] ) );
                $definitions = \expUrlMapFormats::definitions();
                $name = self::fileName( $form['format'], time() );
                $dir = \eZSys::cacheDirectory() . '/urlexport';
                \eZDir::mkdir( $dir, false, true );
                $path = $dir . '/' . bin2hex( random_bytes( 8 ) ) . '-' . $name;
                $written = true;
                try
                {
                    \expUrlMapFormats::writeFile( $path, $rows, $form['format'] );
                }
                catch ( \expUrlMapFormatException $e )
                {
                    $error = 'error-write';
                    $written = false;
                }
                if ( $written )
                {
                    $body = (string)file_get_contents( $path );
                    @unlink( $path );
                    if ( class_exists( 'expAuditHook' ) )
                        \expAuditHook::emit( 'data.export.csv', array( 'object' => array( 'type' => 'urlmap', 'id' => 'export' ), 'verb' => 'export',
                            'after' => array( 'rows' => count( $rows ), 'format' => $form['format'], 'kinds' => $form['kinds'], 'file' => $name ) ) );
                    while ( @ob_end_clean() );
                    header( 'Content-Type: ' . $definitions[$form['format']]['mime'] . ( in_array( $form['format'], array( 'xlsx', 'ods' ), true ) ? '' : '; charset=utf-8' ) );
                    header( 'Content-Disposition: attachment; filename="' . $name . '"' );
                    header( 'Content-Length: ' . strlen( $body ) );
                    header( 'Cache-Control: private, no-store, max-age=0' );
                    header( 'X-Content-Type-Options: nosniff' );
                    echo $body;
                    // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
                    \eZExecution::cleanExit();
                }
            }
        }

        $formats = array();
        foreach ( \expUrlMapFormats::definitions() as $name => $definition )
            if ( $definition['writer'] !== null )
                $formats[] = array( 'name' => $name, 'label' => $definition['label'] );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'export_form', $form );
        $tpl->setVariable( 'export_formats', $formats );
        $tpl->setVariable( 'export_siteaccesses', $siteaccesses );
        $tpl->setVariable( 'export_languages', $languages );
        $tpl->setVariable( 'export_error', $error );
        $tpl->setVariable( 'export_can_import', \eZUser::currentUser()->hasAccessTo( 'content', 'urltranslator_import' )['accessWord'] !== 'no' );

        $title = \ezpI18n::tr( 'design/admin/content/urlimport', 'Export the URL map' );
        $Module->setTitle( $title );
        return $this->viewResult( array( 'content' => $tpl->fetch( 'design:content/urlexport.tpl' ),
                                         'path' => array( array( 'url' => '/content/urltranslator', 'text' => \ezpI18n::tr( 'kernel/content/urlalias_global', 'Global URL aliases' ) ),
                                                          array( 'url' => false, 'text' => $title ) ) ), null );
    }
}

}
