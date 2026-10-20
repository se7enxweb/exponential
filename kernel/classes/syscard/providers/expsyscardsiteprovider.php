<?php
/**
 * File containing the expSysCardSiteProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The site: the siteaccesses and the default one, the active extensions, demo mode, debug output and the mail
 * transport (its type only: file, sendmail, smtp; never its server, user or password).
 */
class expSysCardSiteProvider extends expSysCardBaseProvider
{
    protected $id = 'site';
    protected $section = 'site';
    protected $priority = 60;
    protected $budget = 0.5;

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $list = array_values( array_unique( array_filter( array_map( 'trim', (array)$context->ini( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array() ) ), 'strlen' ) ) );
        $default = (string)$context->ini( 'site.ini', 'SiteSettings', 'DefaultAccess', '' );
        $facts[] = $this->fact( 'siteaccesses', 'Siteaccesses', count( $list ) . ( $default !== '' ? ', default ' . $default : '' ) );

        $extensions = (array)$context->get( 'site.extensions', function () { return class_exists( 'eZExtension' ) ? eZExtension::activeExtensions() : array(); }, array() );
        $facts[] = $this->fact( 'extensions', 'Extensions', count( $extensions ) . ' active' );

        $demo = $context->get( 'site.demo', function () { return class_exists( 'expDemoMode' ) ? expDemoMode::enabled() : null; } );
        if ( $demo === null )
            $demo = $context->env( 'EXP_DEMO_MODE' ) === '1';
        $facts[] = $this->fact( 'demo', 'Demo mode', $demo ? 'on' : 'off', $demo ? expSysCardFact::WARN : expSysCardFact::INFO,
                                $demo ? 'Demo mode is on (EXP_DEMO_MODE=1): administrator passwords cannot be changed. Right for a public demo; turn it off on a production site.' : '' );

        $debug = $context->iniEnabled( 'site.ini', 'DebugSettings', 'DebugOutput', 'disabled' );
        $byIp = $context->iniEnabled( 'site.ini', 'DebugSettings', 'DebugByIP', 'disabled' );
        $byUser = $context->iniEnabled( 'site.ini', 'DebugSettings', 'DebugByUser', 'disabled' );
        $public = $debug && !$byIp && !$byUser;
        $facts[] = $this->fact( 'debug', 'Debug', !$debug ? 'off' : ( $public ? 'on, for everyone' : 'on, ' . ( $byIp ? 'by IP' : 'by user' ) ),
                                $public ? expSysCardFact::WARN : expSysCardFact::INFO,
                                $public ? 'Debug output is on for every visitor (site.ini [DebugSettings] DebugOutput=enabled): it shows internals and slows pages down. Limit it with DebugByIP or turn it off.' : '' );

        $transport = strtolower( trim( (string)$context->ini( 'site.ini', 'MailSettings', 'Transport', 'sendmail' ) ) );
        $facts[] = $this->fact( 'mail', 'Mail', $transport !== '' ? $transport : 'sendmail' );
        return $facts;
    }
}
