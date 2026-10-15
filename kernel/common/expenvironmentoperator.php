<?php
/**
 * File containing the expEnvironmentOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class expEnvironmentOperator expenvironmentoperator.php
  \brief The template operators exp_environment() and exp_environment_badge()

  The name comes from the EXP_ENV constant in config.env.php, see
  eZINI::environment(). Without a valid environment the operator returns an
  empty string, so a template can test it directly:

  \code
  {if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
  \endcode

  exp_environment_badge() returns the environment badge of the administration
  (site.ini [AdminEnvironment], see badge()) as a hash: level, name, short,
  release, title and key. Without a level, level is an empty string and the
  administration shows no badge.
*/

class expEnvironmentOperator
{
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct( $name = 'exp_environment', $badgeName = 'exp_environment_badge' )
    {
        $this->Operators = array( $name, $badgeName );
        $this->BadgeName = $badgeName;
    }

    /*!
     Returns the operators in this class.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array();
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        if ( $operatorName === $this->BadgeName )
        {
            $operatorValue = self::currentBadge();
            return;
        }
        $environment = eZINI::environment();
        $operatorValue = $environment === false ? '' : $environment;
    }

    /**
     * Returns the environment badge of this request, from site.ini [AdminEnvironment] of the current siteaccess
     *
     * @see badge()
     * @return array
     */
    static function currentBadge()
    {
        $ini = eZINI::instance();
        $settings = array();
        foreach ( array( 'Level', 'Name', 'ShortName', 'Release', 'ShowVersion', 'ShowInTitle' ) as $name )
        {
            if ( $ini->hasVariable( 'AdminEnvironment', $name ) )
                $settings[$name] = $ini->variable( 'AdminEnvironment', $name );
        }
        $version = isset( $settings['ShowVersion'] ) && $settings['ShowVersion'] === 'enabled'
                   ? ExponentialSDK::version( true, false, false ) : '';
        return self::badge( $settings, eZINI::environment(), $version, function ( $text ) {
            return ezpI18n::tr( 'design/admin/pagelayout', $text );
        } );
    }

    /**
     * Returns the environment badge for the settings of site.ini [AdminEnvironment]
     *
     * - Level: development, test, staging or production; "auto" takes it from the environment name (EXP_ENV):
     *   dev and development, test, stage and staging, prod, production and live. Empty (the default), an unknown
     *   level, or "auto" with another or no environment name: no badge.
     * - Name: the badge's text; empty takes the level's name. ShortName: the text where the header is narrow;
     *   empty takes the level's code (DEV, TEST, STAGE, PROD).
     * - Release: free text after the name; ShowVersion=enabled adds the Exponential version ($version).
     * - ShowInTitle: anything but "disabled" puts the name before the page title.
     *
     * The result: level (an empty string for no badge), name, short, release, title (bool) and key, one string of
     * all of them for the keys of the cache-blocks that draw the badge.
     *
     * @param array $settings the settings, name => value
     * @param string|false $environment the environment name, see eZINI::environment()
     * @param string $version the Exponential version, or an empty string
     * @param callable|null $translate translates a default name or code; null keeps the English text
     * @return array
     */
    static function badge( array $settings, $environment, $version = '', $translate = null )
    {
        $badge = array( 'level' => '', 'name' => '', 'short' => '', 'release' => '', 'title' => false, 'key' => '' );
        $level = isset( $settings['Level'] ) && is_scalar( $settings['Level'] ) ? strtolower( trim( (string)$settings['Level'] ) ) : '';
        if ( $level === 'auto' )
        {
            $level = isset( self::$EnvironmentLevels[(string)$environment] ) ? self::$EnvironmentLevels[(string)$environment] : '';
        }
        if ( !isset( self::$Levels[$level] ) )
        {
            return $badge;
        }

        $text = function ( $name ) use ( $settings ) {
            return isset( $settings[$name] ) && is_scalar( $settings[$name] ) ? trim( preg_replace( '/\s+/u', ' ', (string)$settings[$name] ) ) : '';
        };
        $name = $text( 'Name' );
        $short = $text( 'ShortName' );
        if ( $name === '' )
            $name = $translate ? (string)call_user_func( $translate, self::$Levels[$level][0] ) : self::$Levels[$level][0];
        if ( $short === '' )
            $short = $translate ? (string)call_user_func( $translate, self::$Levels[$level][1] ) : self::$Levels[$level][1];
        $release = implode( ' · ', array_filter( array( $text( 'Release' ), trim( (string)$version ) ), 'strlen' ) );

        $badge['level'] = $level;
        $badge['name'] = $name;
        $badge['short'] = $short;
        $badge['release'] = $release;
        $badge['title'] = !isset( $settings['ShowInTitle'] ) || $settings['ShowInTitle'] !== 'disabled';
        $badge['key'] = implode( '|', array( $level, $name, $short, $release, $badge['title'] ? 1 : 0 ) );
        return $badge;
    }

    /// \privatesection
    public $Operators;
    public $BadgeName;

    /**
     * The levels of the badge, each with its default name and code (translated in the context design/admin/pagelayout)
     *
     * @var array
     */
    public static $Levels = array(
        'development' => array( 'Development', 'DEV' ),
        'test' => array( 'Test', 'TEST' ),
        'staging' => array( 'Staging', 'STAGE' ),
        'production' => array( 'Production', 'PROD' ),
    );

    /**
     * The level that Level=auto takes for an environment name (EXP_ENV)
     *
     * @var array
     */
    public static $EnvironmentLevels = array(
        'dev' => 'development', 'development' => 'development',
        'test' => 'test',
        'stage' => 'staging', 'staging' => 'staging',
        'prod' => 'production', 'production' => 'production', 'live' => 'production',
    );
}


?>
