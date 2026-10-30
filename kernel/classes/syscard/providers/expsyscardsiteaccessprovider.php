<?php
/**
 * File containing the expSysCardSiteAccessProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exp:info: how a request finds its siteaccess (site.ini [SiteAccessSettings] MatchOrder: uri, host, port ...) and,
 * for each siteaccess of AvailableSiteAccessList, its design, its language and the host of its SiteURL.
 */
class expSysCardSiteAccessProvider extends expSysCardBaseProvider
{
    protected $id = 'siteaccess';
    protected $section = 'siteaccesses';
    protected $modes = array( expSysCardContext::MODE_INFO );
    protected $priority = 14;
    protected $budget = 2.0;

    /** At most this many siteaccesses are described */
    const LIMIT = 40;

    public function collect( expSysCardContext $context )
    {
        $order = $context->ini( 'site.ini', 'SiteAccessSettings', 'MatchOrder', '' );
        $order = is_array( $order ) ? implode( ';', $order ) : (string)$order;
        $facts = array( $this->fact( 'match', 'URL mode', $order !== '' ? str_replace( ';', ', ', $order ) : 'n/a' ) );
        $list = array_values( array_unique( array_filter( array_map( 'trim', (array)$context->ini( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array() ) ), 'strlen' ) ) );
        foreach ( array_slice( $list, 0, self::LIMIT ) as $siteaccess )
        {
            if ( $context->remaining() < 0.05 )
            {
                $facts[] = $this->fact( 'more', '...', 'n/a (time)' );
                break;
            }
            $details = $context->get( 'siteaccess:' . $siteaccess, function () use ( $siteaccess ) {
                if ( !class_exists( 'eZSiteAccess' ) )
                    return null;
                $ini = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
                if ( !$ini instanceof eZINI )
                    return null;
                $get = function ( $block, $name ) use ( $ini ) { return $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : ''; };
                return array( 'design' => (string)$get( 'DesignSettings', 'SiteDesign' ), 'locale' => (string)$get( 'RegionalSettings', 'Locale' ),
                              'url' => (string)$get( 'SiteSettings', 'SiteURL' ) );
            } );
            $parts = array();
            if ( is_array( $details ) )
            {
                if ( !empty( $details['design'] ) )
                    $parts[] = 'design ' . $details['design'];
                if ( !empty( $details['locale'] ) )
                    $parts[] = $details['locale'];
                $host = self::host( isset( $details['url'] ) ? $details['url'] : '' );
                if ( $host !== '' )
                    $parts[] = $host;
            }
            $facts[] = $this->fact( 'sa:' . $siteaccess, $siteaccess, empty( $parts ) ? 'n/a' : implode( ', ', $parts ) );
        }
        return $facts;
    }

    /**
     * The host (and path) of a SiteURL, without a scheme or credentials.
     *
     * @param string $url
     * @return string
     */
    public static function host( $url )
    {
        $url = trim( (string)$url );
        $url = (string)preg_replace( '#^[a-z][a-z0-9+.-]*://#i', '', $url );
        $at = strpos( $url, '@' );
        if ( $at !== false && ( strpos( $url, '/' ) === false || $at < strpos( $url, '/' ) ) )
            $url = substr( $url, $at + 1 );
        return rtrim( $url, '/' );
    }
}
