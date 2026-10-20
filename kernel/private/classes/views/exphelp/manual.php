<?php
/**
 * exphelp/manual/<remote_id> (doc/en/us/features/6.0/help-content.md): a manual of help maintained as content, its metadata
 * and table of contents (chapters and topics the user may see). Without a remote_id: the list of manuals.
 * Template design:exphelp/manual.tpl.
 *
 * Template variables: help_manual (the manual, or false for the list), help_manual_toc (nested: kind chapter|topic,
 * title, shortdesc, help_id, url, preview, children), help_manuals (visible manuals), help_json_url, help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Manual extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() || !\expHelpContentProvider::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $provider = \expHelpContentProvider::instance();
        $remoteId = \expHelp::requestedId( $Params, $_GET );
        $manual = false;
        $toc = array();
        if ( $remoteId !== '' )
        {
            $manual = $provider->manual( $remoteId );
            if ( $manual === null )
                return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
            $toc = $provider->toc( $manual );
            $format = isset( $_GET['format'] ) && is_string( $_GET['format'] ) ? $_GET['format'] : '';
            if ( $format !== '' )
                return self::download( $Module, $provider, $manual, $format );
        }
        $manuals = array();
        foreach ( $provider->manuals() as $item )
            $manuals[] = array( 'title' => $item['title'], 'subtitle' => $item['subtitle'], 'shortdesc' => $item['shortdesc'],
                                'remote_id' => $item['remote_id'], 'edition' => $item['edition'], 'preview' => $item['preview'] );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_manual', $manual === false ? false : $manual );
        $tpl->setVariable( 'help_manual_toc', self::twoLevels( $toc ) );
        $tpl->setVariable( 'help_manuals', $manuals );
        $tpl->setVariable( 'help_ebook_formats', $manual ? \expHelpContentExport::formats() : array() );
        $tpl->setVariable( 'help_json_url', $manual ? ltrim( \expHelp::url( 'context', 'manual/' . $manual['remote_id'] ), '/' ) : '' );

        $path = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                       array( 'url' => $manual ? 'exphelp/manual' : false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Manuals' ) ) );
        if ( $manual )
            $path[] = array( 'url' => false, 'text' => $manual['title'] );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/manual.tpl' );
        $Result['path'] = $path;
        return $Result;
    }

    /**
     * Sends the e-book of a manual (?format=epub|pdf): the Released topics the user may read, made on request into
     * the help cache. A format this installation cannot write, or a manual without such topics, is a 404.
     *
     * @param \eZModule $Module
     * @param \expHelpContentProvider $provider
     * @param array $manual
     * @param string $format
     * @return array|null
     */
    public static function download( $Module, $provider, array $manual, $format )
    {
        $types = array( 'epub' => 'application/epub+zip', 'pdf' => 'application/pdf' );
        if ( !isset( $types[$format] ) || !in_array( $format, \expHelpContentExport::formats(), true ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $export = new \expHelpContentExport( $provider, $manual );
        if ( !$export->topics() )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        // per user's access: the file name carries the user, so no one gets a book made with another's rights
        $file = rtrim( \expHelp::cacheDirectory(), '/' ) . '/ebooks/' . sha1( $manual['remote_id'] . "\0" . \eZUser::currentUserID() . "\0" . microtime( true ) ) . '.' . $format;
        try
        {
            $result = $format === 'epub' ? $export->writeEpub( $file ) : $export->writePdf( $file );
        }
        catch ( \RuntimeException $e )
        {
            \eZDebug::writeWarning( 'The e-book could not be made: ' . $e->getMessage(), __METHOD__ );
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        }
        $data = (string)file_get_contents( $result['file'] );
        @unlink( $result['file'] );
        $name = preg_replace( '/[^A-Za-z0-9._-]/', '-', $manual['remote_id'] ) . '.' . $format;
        header( 'Content-Type: ' . $types[$format] );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( 'Content-Length: ' . strlen( $data ) );
        header( 'Cache-Control: private, no-store' );
        header( 'X-Content-Type-Options: nosniff' );
        echo $data;
        \eZExecution::cleanExit();
        return null;
    }

    /**
     * The table of contents in two levels for the template (no recursive include): the entries of the manual, each
     * chapter with every entry below it in reading order; a chapter inside a chapter is one of those entries, with
     * the depth it has.
     *
     * @param array $toc expHelpContentProvider::toc()
     * @return array
     */
    public static function twoLevels( array $toc )
    {
        $flat = function ( array $entries, $depth ) use ( &$flat ) {
            $list = array();
            foreach ( $entries as $entry )
            {
                $children = isset( $entry['children'] ) ? $entry['children'] : array();
                $entry['children'] = array();
                $entry['depth'] = $depth;
                $list[] = $entry;
                if ( $children )
                    $list = array_merge( $list, $flat( $children, $depth + 1 ) );
            }
            return $list;
        };
        $out = array();
        foreach ( $toc as $entry )
        {
            if ( $entry['kind'] === 'chapter' )
                $entry['children'] = $flat( $entry['children'], 1 );
            $out[] = $entry;
        }
        return $out;
    }
}

}
