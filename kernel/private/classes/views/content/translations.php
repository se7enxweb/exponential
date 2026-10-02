<?php
/**
 * The code of kernel/content/translations.php, moved into a class (#207 stage 1). The file kernel/content/translations.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/translations.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Translations extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];

        $tpl->setVariable( 'module', $Module );


        if ( $Module->isCurrentAction( 'New' ) /*or
             $Module->isCurrentAction( 'Edit' )*/ )
        {
            $tpl->setVariable( 'is_edit', $Module->isCurrentAction( 'Edit' ) );
            $Result['content'] = $tpl->fetch( 'design:content/translationnew.tpl' );
            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Translation' ),
                                            'url' => false ),
                                     array( 'text' => 'New',
                                            'url' => false ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $Module->isCurrentAction( 'StoreNew' ) /* || $http->hasPostVariable( 'StoreButton' ) */ )
        {
            $localeID = $Module->actionParameter( 'LocaleID' );
            $translationName = '';
            $translationLocale = '';
            \eZDebug::writeDebug( $localeID, 'localeID' );
            if ( $localeID != '' and
                 $localeID != -1 )
            {
                $translationLocale = $localeID;
                $localeInstance = \eZLocale::instance( $translationLocale );
                $translationName = $localeInstance->internationalLanguageName();
            }
            else
            {
                $translationName = $Module->actionParameter( 'TranslationName' );
                $translationLocale = $Module->actionParameter( 'TranslationLocale' );
                \eZDebug::writeDebug( $translationName, 'translationName' );
                \eZDebug::writeDebug( $translationLocale, 'translationLocale' );
            }

            // Make sure the locale string is valid, if not we try to extract a valid part of it
            if ( !preg_match( "/^" . \eZLocale::localeRegexp( false, false ) . "$/", $translationLocale ) )
            {
                if ( preg_match( "/(" . \eZLocale::localeRegexp( false, false ) . ")/", $translationLocale, $matches ) )
                {
                    $translationLocale = $matches[1];
                }
                else
                {
                    // The locale cannot be used so we show the edit page again.
                    $tpl->setVariable( 'is_edit', $Module->isCurrentAction( 'Edit' ) );
                    $Result['content'] = $tpl->fetch( 'design:content/translationnew.tpl' );
                    $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Translation' ),
                                                    'url' => false ),
                                             array( 'text' => 'New',
                                                    'url' => false ) );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                }
            }

            if ( !\eZContentLanguage::fetchByLocale( $translationLocale ) )
            {
                $locale = \eZLocale::instance( $translationLocale );
                if ( $locale->isValid() )
                {
                    $translation = \eZContentLanguage::addLanguage( $locale->localeCode(), $translationName );
                    \ezpEvent::getInstance()->notify( 'content/translations/cache', array( $translation->attribute( 'id' ) ) );
                }
                else
                {
                    // The locale cannot be used so we show the edit page again.
                    $tpl->setVariable( 'is_edit', $Module->isCurrentAction( 'Edit' ) );
                    $Result['content'] = $tpl->fetch( 'design:content/translationnew.tpl' );
                    $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Translation' ),
                                                    'url' => false ),
                                             array( 'text' => 'New',
                                                    'url' => false ) );
                    return $this->viewResult( isset( $Result ) ? $Result : null, null );
                }
            }
        }

        if ( $Module->isCurrentAction( 'Remove' ) )
        {
            $seletedIDList = $Module->actionParameter( 'SelectedTranslationList' );

            $db = \eZDB::instance();

            $db->begin();
            foreach ( $seletedIDList as $translationID )
            {
                \eZContentLanguage::removeLanguage( $translationID );
            }
            $db->commit();
            \ezpEvent::getInstance()->notify( 'content/translations/cache', array( $seletedIDList ) );
        }


        if ( $Params['TranslationID'] )
        {
            $translation = \eZContentLanguage::fetch( $Params['TranslationID'] );

            if( !$translation )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }

            $tpl->setVariable( 'translation',  $translation );

            $Result['content'] = $tpl->fetch( 'design:content/translationview.tpl' );
            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Content translations' ),
                                            'url' => 'content/translations' ),
                                     array( 'text' => $translation->attribute( 'name' ),
                                            'url' => false ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $availableTranslations = \eZContentLanguage::fetchList();

        // Paged. The whole list was read and every row of it drawn.
        $pageCount  = count( $availableTranslations );
        $pageLimit  = \expAdminPagination::limit( 'content/translations' );
        $pageOffset = \expAdminPagination::offset( $Params );
        $availableTranslations = \expAdminPagination::page( $availableTranslations, $pageOffset, $pageLimit );

        $tpl->setVariable( 'available_translations', $availableTranslations );
        $tpl->setVariable( 'translation_count', $pageCount );
        $tpl->setVariable( 'limit', $pageLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

        $Result['content'] = $tpl->fetch( 'design:content/translations.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Languages' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
