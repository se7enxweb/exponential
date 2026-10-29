<?php
/**
 * What the installation knows about its template engines: for each engine (tpl, stpl, twig) whether its library is
 * available, the version, the settings file, whether its security policy is on, the compiled-template directory and
 * the file endings. Used by exp:template:engines and exp:template:check.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateEngineInfo
{
    /**
     * @return array[] engine => array( engine, name, available, version, endings, settings, security, compiled, renderer )
     */
    public static function engines()
    {
        $cache = eZSys::cacheDirectory();
        $engines = array(
            'tpl' => array( 'engine' => 'tpl', 'name' => 'Exponential templates', 'available' => true,
                            'version' => 'Exponential ' . ExponentialSDK::version(),
                            'endings' => array( '.tpl' ), 'settings' => 'settings/site.ini [TemplateSettings]', 'security' => null,
                            'compiled' => $cache . '/template/compiled', 'renderer' => 'eZTemplate' ),
        );
        $endings = array();
        foreach ( expTemplateEngines::suffixes() as $ending => $engine )
            $endings[$engine][] = $ending;
        if ( class_exists( 'expSmartyRenderer' ) )
        {
            $available = expSmartyRenderer::loadLibrary();
            $ini = eZINI::instance( 'smarty.ini' );
            $security = $ini->hasVariable( 'SmartySettings', 'Security' ) && $ini->variable( 'SmartySettings', 'Security' ) === 'enabled';
            $engines['stpl'] = array( 'engine' => 'stpl', 'name' => 'Smarty templates', 'available' => $available,
                                      'version' => $available && defined( '\Smarty\Smarty::SMARTY_VERSION' ) ? 'Smarty ' . \Smarty\Smarty::SMARTY_VERSION : null,
                                      'endings' => isset( $endings['stpl'] ) ? $endings['stpl'] : array( '.stpl' ), 'settings' => 'settings/smarty.ini [SmartySettings]',
                                      'security' => $security, 'compiled' => $cache . '/smarty/' . ( $security ? 'compile-secure' : 'compile' ),
                                      'renderer' => 'expSmartyRenderer' );
        }
        if ( class_exists( 'expTwigRenderer' ) )
        {
            $available = expTwigRenderer::loadLibrary();
            $ini = eZINI::instance( 'twig.ini' );
            $security = !$ini->hasVariable( 'TwigSettings', 'Security' ) || $ini->variable( 'TwigSettings', 'Security' ) === 'enabled';
            $engines['twig'] = array( 'engine' => 'twig', 'name' => 'Twig templates', 'available' => $available,
                                      'version' => $available ? 'Twig ' . \Twig\Environment::VERSION : null,
                                      'endings' => isset( $endings['twig'] ) ? $endings['twig'] : array( '.twig', '.html.twig' ), 'settings' => 'settings/twig.ini [TwigSettings]',
                                      'security' => $security, 'compiled' => $cache . '/twig/' . ( $security ? 'compile-secure' : 'compile' ),
                                      'renderer' => 'expTwigRenderer' );
        }
        return $engines;
    }
}
