<?php
/**
 * The code of kernel/section/edit.php, moved into a class (#207 stage 1). The file kernel/section/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/section/edit.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Section
{

class Edit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $SectionID = $Params["SectionID"];
        $Module = $Params['Module'];
        $tpl = \eZTemplate::factory();

        if ( $SectionID == 0 )
        {
            $section = array( 'id' => 0,
                              'name' => \ezpI18n::tr( 'kernel/section', 'New section' ),
                              'navigation_part_identifier' => 'ezcontentnavigationpart' );
        }
        else
        {
            $section = \eZSection::fetch( $SectionID );
            if( $section === null )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }
        }

        if ( $http->hasPostVariable( "StoreButton" ) )
        {
            if ( $SectionID == 0 )
            {
                $section = new \eZSection( array() );
            }
            $section->setAttribute( 'name', $http->postVariable( 'Name' ) );
            $sectionIdentifier = trim( $http->postVariable( 'SectionIdentifier' ) );
            $errorMessage = '';
            if( $sectionIdentifier === '' )
            {
                $errorMessage = \ezpI18n::tr( 'design/admin/section/edit', 'Identifier can not be empty' );

            }
            else if( preg_match( '/(^[^A-Za-z])|\W/', $sectionIdentifier ) )
            {
                $errorMessage = \ezpI18n::tr( 'design/admin/section/edit', 'Identifier should consist of letters, numbers or \'_\' with letter prefix.' );
            }
            else
            {
                $conditions = array( 'identifier' => $sectionIdentifier,
                                     'id' => array( '!=', !empty( $SectionID ) ? $SectionID : 0 ) );
                $existingSection = \eZSection::fetchFilteredList( $conditions );
                if( count( $existingSection ) > 0 )
                {
                    $errorMessage = \ezpI18n::tr( 'design/admin/section/edit', 'The identifier has been used in another section.' );
                }
            }
            $section->setAttribute( 'identifier', $sectionIdentifier );
            $section->setAttribute( 'navigation_part_identifier', $http->postVariable( 'NavigationPartIdentifier' ) );
            if ( $http->hasPostVariable( 'Locale' ) )
                $section->setAttribute( 'locale', $http->postVariable( 'Locale' ) );
            if( $errorMessage === '' )
            {
                $section->store();
                \eZContentCacheManager::clearContentCacheIfNeededBySectionID( $section->attribute( 'id' ) );
                \ezpEvent::getInstance()->notify( 'content/section/cache', array( $section->attribute( 'id' ) ) );
                $Module->redirectTo( $Module->functionURI( 'list' ) );
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }
            else
            {
                $tpl->setVariable( 'error_message', $errorMessage );
            }
        }

        if ( $http->hasPostVariable( 'CancelButton' )  )
        {
            $Module->redirectTo( $Module->functionURI( 'list' ) );
        }

        $tpl->setVariable( "section", $section );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:section/edit.tpl" );
        $Result['path'] = array( array( 'url' => 'section/list',
                                        'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                                 array( 'url' => false,
                                        'text' => $section instanceof \eZSection ? $section->attribute('name') : $section['name'] ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
