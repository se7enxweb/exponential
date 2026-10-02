<?php
/**
 * The code of cronjobs/linkcheck.php, moved into a class (#207 stage 1). The file cronjobs/linkcheck.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/linkcheck.php:
 *
 *
 * @description Check all internal and external links in published content for broken URLs
 *
 * File containing the linkcheck.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Linkcheck extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $cli->output( "Checking link ..." );

        $cronjobIni = \eZINI::instance( 'cronjob.ini' );
        $siteURLs = $cronjobIni->variable( 'linkCheckSettings', 'SiteURL' );
        $linkList = \eZURL::fetchList( array( 'only_published' => true ) );
        foreach ( $linkList as $link )
        {
            $linkID = $link->attribute( 'id' );
            $url = $link->attribute( 'url' );
            $isValid = $link->attribute( 'is_valid' );

            $cli->output( "check-" . $cli->stylize( 'emphasize', $url ) . " ", false );
            if ( preg_match("/^(http:)/i", $url ) or
                 preg_match("/^(ftp:)/i", $url ) or
                 preg_match("/^(https:)/i", $url ) or
                 preg_match("/^(file:)/i", $url ) or
                 preg_match("/^(mailto:)/i", $url ) )
            {
                if ( preg_match("/^(mailto:)/i", $url))
                {
                    if ( \eZSys::osType() != 'win32' )
                    {
                        $url = trim( preg_replace("/^mailto:(.+)/i", "\\1", $url));
                        list($userName, $host) = explode( '@', $url );
                        list($host, $junk) = explode( '?', $host );
                        $dnsCheck = checkdnsrr( $host,"MX" );
                        if ( !$dnsCheck )
                        {
                            if ( $isValid )
                                \eZURL::setIsValid( $linkID, false );
                            $cli->output( $cli->stylize( 'warning', "invalid" ) );
                        }
                        else
                        {
                            if ( !$isValid )
                                \eZURL::setIsValid( $linkID, true );
                            $cli->output( $cli->stylize( 'success', "valid" ) );
                        }
                    }
                }
                else if ( preg_match("/^(http:)/i", $url ) or
                          preg_match("/^(file:)/i", $url ) or
                          preg_match("/^(ftp:)/i", $url ) )
                {
                    if ( !\eZHTTPTool::getDataByURL( $url, true, 'Exponential Link Validator' ) )
                    {
                        if ( $isValid )
                            \eZURL::setIsValid( $linkID, false );
                        $cli->output( $cli->stylize( 'warning', "invalid" ) );
                    }
                    else
                    {
                        if ( !$isValid )
                            \eZURL::setIsValid( $linkID, true );
                        $cli->output( $cli->stylize( 'success', "valid" ) );
                    }
                }
                else
                {
                    $cli->output( "HTTPS protocol is not supported by linkcheck" );
                }
            }
            else
            {
                $translateResult = \eZURLAliasML::translate( $url );

                if ( !$translateResult )
                {
                      $isInternal = false;
                      // Check if it is a valid internal link.
                      foreach ( $siteURLs as $siteURL )
                      {
                          $siteURL = preg_replace("/\/$/", "", $siteURL );
                          $fp = @fopen( $siteURL . "/". $url, "r" );
                          if ( !$fp )
                          {
                              // do nothing
                          }
                          else
                          {
                              $isInternal = true;
                              fclose($fp);
                          }
                      }
                      $translateResult = $isInternal;
                }
                if ( $translateResult )
                {
                    if ( !$isValid )
                        \eZURL::setIsValid( $linkID, true );
                    $cli->output( $cli->stylize( 'success', "valid" ) );
                }
                else
                {
                    if ( $isValid )
                        \eZURL::setIsValid( $linkID, false );
                    $cli->output( $cli->stylize( 'warning', "invalid" ) );
                }
            }
            \eZURL::setLastChecked( $linkID );
        }

        $cli->output( "All links have been checked!" );
    }
}

}
