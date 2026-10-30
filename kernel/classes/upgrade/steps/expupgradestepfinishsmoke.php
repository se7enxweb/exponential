<?php
/**
 * File containing the expUpgradeStepFinishSmoke class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * After the deploy: the site answers. Each address of the option url (--url=, a comma list) or, without it, the
 * front page of [SiteSettings] SiteURL is requested once; a status from 200 to 399 passes, anything else (or no
 * answer in 20 seconds) fails the run, with the backup named in the report as the way back. Without an address the
 * step is not applicable.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepFinishSmoke extends expUpgradeFinishStep
{
    public function id()
    {
        return '6.0.15-finish-smoke';
    }

    public function description()
    {
        return 'The site answers after the upgrade: the front page (or every --url) gives 200 to 399';
    }

    public function after()
    {
        return array( '6.0.15-finish-deploy' );
    }

    /**
     * The addresses to request.
     *
     * @param expUpgradeContext $context
     * @return string[]
     */
    public static function urls( expUpgradeContext $context )
    {
        $urls = expUpgrade::listOption( $context->option( 'url', array() ) );
        if ( $urls )
            return $urls;
        $site = trim( (string)eZINI::instance()->variable( 'SiteSettings', 'SiteURL' ) );
        if ( $site === '' )
            return array();
        return array( ( preg_match( '#^https?://#', $site ) ? '' : 'https://' ) . rtrim( $site, '/' ) . '/' );
    }

    public function detect( expUpgradeContext $context )
    {
        $effect = parent::detect( $context );
        if ( $effect['effect'] === self::MISSING && !self::urls( $context ) )
            return self::effect( self::NOT_APPLICABLE, 'no address: [SiteSettings] SiteURL is empty and no --url was given' );
        return $effect;
    }

    public function plan( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( self::urls( $context ) as $url )
            $lines[] = "request $url, expect 200 to 399";
        return $lines;
    }

    /**
     * The HTTP status of an address, 0 when there is no answer.
     *
     * @param string $url
     * @return int
     */
    public static function status( $url )
    {
        $context = stream_context_create( array( 'http' => array( 'method' => 'GET', 'timeout' => 20, 'ignore_errors' => true,
                                                                  'follow_location' => 0, 'header' => "User-Agent: exp-upgrade-smoke\r\n" ) ) );
        $handle = @fopen( $url, 'r', false, $context );
        if ( !$handle )
            return 0;
        $meta = stream_get_meta_data( $handle );
        fclose( $handle );
        foreach ( (array)( isset( $meta['wrapper_data'] ) ? $meta['wrapper_data'] : array() ) as $header )
            if ( preg_match( '#^HTTP/\S+\s+(\d{3})#', $header, $m ) )
                return (int)$m[1];
        return 0;
    }

    protected function work( expUpgradeContext $context )
    {
        $done = array();
        $bad = array();
        foreach ( self::urls( $context ) as $url )
        {
            $code = self::status( $url );
            $done[] = "$url: " . ( $code ? $code : 'no answer' );
            if ( $code < 200 || $code > 399 )
                $bad[] = "$url answered " . ( $code ? $code : 'nothing' );
        }
        if ( $bad )
            throw new RuntimeException( 'the site does not answer as it should: ' . implode( '; ', $bad ) );
        return $done;
    }
}
