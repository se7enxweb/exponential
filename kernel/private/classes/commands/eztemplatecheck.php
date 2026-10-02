<?php
/**
 * The code of bin/php/eztemplatecheck.php, moved into a class (#207 stage 1). The file bin/php/eztemplatecheck.php is one call to it.
 * @description Check the syntax of the templates of a siteaccess or of a design directory
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/eztemplatecheck.php:
 *
 *
 * File containing the eztemplatecheck.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{


function showResourceData( $resourceData )
{
    $cli = eZCLI::instance();

    if ( isset( $resourceData['errors'] ) )
    {
        $cli->output( 'Errors:' );
        foreach( $resourceData['errors'] as $error )
            showError( $error );
    }
    if ( isset( $resourceData['warnings'] ) )
    {
        $cli->output( 'Warnings:' );
        foreach( $resourceData['warnings'] as $warning )
            showError( $warning );
    }
}

function showError( $error )
{
    $cli = eZCLI::instance();

    $str = $error['name'] . ': ' . $error['text'];
    if( $error['placement'] !== false )
    {
        $p = $error['placement'];
        $str .= ' at ' . $p['templatefile'] . ' LINE ' . $p['start']['line'];
    }
    $cli->output( $str );
}
}

namespace Exponential\Command\Kernel
{

class Eztemplatecheck extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'additionalSiteDesignList', 'baseDir', 'cli', 'design', 'designList', 'file', 'fileList', 'fileRelative', 'filename', 'files', 'ini', 'nbInvalidTemplates', 'options', 'resourceData', 'result', 'script', 'siteDesign', 'standardDesign', 'status', 'sys', 'text', 'tpl' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => ( "Exponential Template Syntax Checker\n" .
                                                                "\n" .
                                                                "./bin/php/eztemplatecheck.php -sadmin\n" .
                                                                "or\n" .
                                                                "./bin/php/eztemplatecheck.php design/" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );

        $script->startup();

        $options = $script->getOptions( "", "[FILE*]", array() );
        $sys = \eZSys::instance();

        $script->initialize();

        $result = true;
        $nbInvalidTemplates = 0;

        if ( count( $options['arguments'] ) > 0 )
        {
            $ini = \eZINI::instance();
            $tpl = \eZTemplate::factory();

            $fileList = array();

            foreach ( $options['arguments'] as $file )
            {
                if ( is_dir( $file ) )
                {
                    $fileList = array_merge( $fileList, \eZDir::recursiveFindRelative( '', $file, "\.tpl" ) );
                }
                else if ( is_file( $file ) )
                {
                    $fileList[] = $file;
                }
            }
            $fileList = array_unique( $fileList );

            $script->setIterationData( '.', '~' );
            $script->setShowVerboseOutput( true );

            $files = array();
            foreach ( $fileList as $file )
            {
                $filename = basename( $file );
                if ( preg_match( "!^.+~$|^/?#.+#$|^\..+$!", $filename ) )
                    continue;
                $files[] = $file;
            }

            $script->resetIteration( count( $files ) );
            foreach ( $files as $file )
            {
                if ( is_dir( $file ) )
                {
                    $script->iterate( $cli, true, "Skipping directory: " . $cli->stylize( 'dir', $file ) );
                }
                else
                {
                    $resourceData = $tpl->validateTemplateFile( $file, true );
                    $status = $resourceData['result'];
                    $text = false;
                    if ( $status )
                        $text = "Template file valid: " . $cli->stylize( 'file', $file );
                    else
                    {
                        $text = "Template file invalid: " . $cli->stylize( 'file', $file );
                        $nbInvalidTemplates++;
                        showResourceData( $resourceData );
                    }
                    if ( !$status )
                        $result = false;
                    $script->iterate( $cli, $status, $text );
                }
            }
        }
        else
        {
            $ini = \eZINI::instance();
            $standardDesign = $ini->variable( "DesignSettings", "StandardDesign" );
            $siteDesign = $ini->variable( "DesignSettings", "SiteDesign" );
            $additionalSiteDesignList = $ini->variable( "DesignSettings", "AdditionalSiteDesignList" );

            $designList = array_merge( array( $standardDesign ), $additionalSiteDesignList, array( $siteDesign ) );

            $tpl = \eZTemplate::factory();

            $script->setIterationData( '.', '~' );
            $script->setShowVerboseOutput( true );

            foreach ( $designList as $design )
            {
                $cli->output( "Validating in design " . $cli->stylize( 'emphasize', $design ) );
                $baseDir = 'design/' . $design;
                $files = \eZDir::recursiveFindRelative( $baseDir, 'templates', "\.tpl" );
                $files = array_merge( $files, \eZDir::recursiveFindRelative( $baseDir, 'override/templates', "\.tpl" ) );
                $script->resetIteration( count( $files ) );
                foreach ( $files as $fileRelative )
                {
                    $file = $baseDir . '/' . $fileRelative;
                    $resourceData = $tpl->validateTemplateFile( $file, true );
                    $status = $resourceData['result'];
                    $text = false;
                    if ( $status )
                        $text = "Template file valid: " . $cli->stylize( 'file', $file );
                    else
                    {
                        $text = "Template file invalid: " . $cli->stylize( 'file', $file );
                        $nbInvalidTemplates++;
                        showResourceData( $resourceData );
                    }
                    if ( !$status )
                        $result = false;
                    $script->iterate( $cli, $status, $text );
                }
            }
        }

        if ( !$result )
        {
            $script->shutdown( 1, "Some templates ($nbInvalidTemplates) did not validate" );
        }
        else
        {
            $script->shutdown();
        }
    }
}

}
