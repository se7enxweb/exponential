<?php
/**
 * Installation name output filter.
 *
 * Renders the configured EzInstallationName into the HTML output:
 *   - a <head> HTML comment (always)
 *   - a <title> prefix on non-production installations
 *   - resolves <!--INSTALLATION_NAME--> placeholders
 *
 * Also exposes reusable static helpers for templates:
 *   - ExpInstallationDetailsOutputFilter::installationName()
 *   - ExpInstallationDetailsOutputFilter::isProductionSystem()
 */

class ExpInstallationDetailsOutputFilter
{
    private static $installationName = false;
    private static $installationNameLoaded = false;

    private static $isProduction = false;
    private static $isProductionLoaded = false;

    /**
     * Returns the configured installation name from site.ini[SiteSettings]EzInstallationName.
     */
    public static function installationName()
    {
        if ( !self::$installationNameLoaded )
        {
            $ini = eZINI::instance( 'site.ini' );
            $name = '';
            if ( $ini->hasVariable( 'SiteSettings', 'EzInstallationName' ) )
            {
                $name = $ini->variable( 'SiteSettings', 'EzInstallationName' );
            }
            self::$installationName = trim( $name );
            self::$installationNameLoaded = true;
        }
        return self::$installationName;
    }

    /**
     * Returns true if the installation name is in the ProductionInstallationList.
     */
    public static function isProductionSystem()
    {
        if ( !self::$isProductionLoaded )
        {
            $ini = eZINI::instance( 'site.ini' );
            $name = self::installationName();
            $list = array();
            if ( $ini->hasVariable( 'SiteSettings', 'ProductionInstallationList' ) )
            {
                $list = $ini->variable( 'SiteSettings', 'ProductionInstallationList' );
            }

            if ( !is_array( $list ) )
            {
                $list = array();
            }

            self::$isProduction = ( $name !== '' && in_array( $name, $list ) );
            self::$isProductionLoaded = true;
        }
        return self::$isProduction;
    }

    /**
     * response/output event handler.
     */
    public static function filter( $output )
    {
        $name = self::installationName();
        if ( $name === '' )
        {
            return $output;
        }

        $isProd = self::isProductionSystem();

        // Always add an invisible [I] comment directly after the first <head> tag.
        $headComment = '<!-- [I] ' . htmlspecialchars( $name, ENT_QUOTES ) . ' -->';
        $output = preg_replace( '/<head>/i', '<head>' . $headComment, $output, 1 );

        // On non-production systems, prefix the first <title> with the name.
        if ( !$isProd )
        {
            $output = preg_replace( '/<title>/i', '<title>[' . htmlspecialchars( $name, ENT_QUOTES ) . '] ', $output, 1 );
        }

        // Replace explicit template placeholders; on production they are removed.
        $output = str_replace( '<!--INSTALLATION_NAME-->', $isProd ? '' : htmlspecialchars( $name, ENT_QUOTES ), $output );

        return $output;
    }

    /**
     * Reset cached values (useful for unit tests or re-initialisation).
     */
    public static function resetCache()
    {
        self::$installationName = false;
        self::$installationNameLoaded = false;
        self::$isProduction = false;
        self::$isProductionLoaded = false;
    }
}
