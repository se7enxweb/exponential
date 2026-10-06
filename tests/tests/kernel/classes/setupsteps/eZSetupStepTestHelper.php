<?php
/**
 * What the tests of the setup wizard's steps share: a step made with a kickstart file of the test's own (written
 * under var/tmp and handed to the step in place of the installation's kickstart.ini, which is put back at once), a
 * template that records what it was given instead of drawing it, posted variables, settings injected for one test,
 * and everything put back afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once dirname( __DIR__ ) . '/radwizards/expRadWizardTestHelper.php';

/**
 * Reaches eZINI's instance table, to hand a step a kickstart file of the test's own.
 */
class eZSetupStepTestINI extends eZINI
{
    public static function swap( $key, $ini )
    {
        $before = self::$instances[$key] ?? null;
        if ( $ini === null )
            unset( self::$instances[$key] );
        else
            self::$instances[$key] = $ini;
        return $before;
    }
}

/**
 * A template that keeps the variables and the template names it is asked for, and draws nothing.
 */
class eZSetupStepTestTemplate extends eZTemplate
{
    public $fetched = array();

    function fetch( $template = false, $extraParameters = false, $returnResourceData = false )
    {
        $this->fetched[] = $template;
        return '[' . $template . ']';
    }
}

class eZSetupStepTestHelper
{
    const KICKSTART_KEY = '.-kickstart.ini-';

    private $post;
    private $injected;
    private $hadAllow;
    private $allow;
    private $scratch = array();

    /** @var eZSetupStepTestTemplate */
    public $tpl;

    public static function bootOnce()
    {
        expRadWizardTestHelper::boot();
        require_once 'kernel/setup/ezsetupcommon.php';
        require_once 'kernel/setup/ezsetuptests.php';
    }

    public function __construct()
    {
        expRadWizardTestHelper::boot();
        $this->post = $_POST;
        $_POST = array();
        $this->injected = expRadWizardTestINI::injected();
        $this->hadAllow = array_key_exists( 'eZStepAllowKickstart', $GLOBALS );
        $this->allow = $this->hadAllow ? $GLOBALS['eZStepAllowKickstart'] : null;
        unset( $GLOBALS['eZStepAllowKickstart'] );
        $this->tpl = new eZSetupStepTestTemplate();
    }

    public function restore()
    {
        $_POST = $this->post;
        eZINI::injectSettings( $this->injected );
        if ( $this->hadAllow )
            $GLOBALS['eZStepAllowKickstart'] = $this->allow;
        else
            unset( $GLOBALS['eZStepAllowKickstart'] );
        foreach ( $this->scratch as $dir )
            expRadWizardTestHelper::removeTree( $dir );
    }

    public function scratch( $label )
    {
        $dir = expRadWizardTestHelper::scratch( $label );
        $this->scratch[] = $dir;
        return $dir;
    }

    public function injectSite( array $blocks )
    {
        expRadWizardTestHelper::injectIni( 'site.ini', $blocks );
    }

    /**
     * A step built by $factory( $tpl, $http, $ini, &$persistenceList ), with the kickstart groups given.
     */
    public function step( $factory, array $persistence = array(), array $kickstart = array() )
    {
        $dir = $this->scratch( 'kickstart' );
        $text = '';
        foreach ( $kickstart as $group => $values )
        {
            $text .= "[$group]\n";
            foreach ( $values as $name => $value )
            {
                if ( is_array( $value ) )
                {
                    $text .= "{$name}[]\n";
                    foreach ( $value as $key => $item )
                        $text .= is_int( $key ) ? "{$name}[]=$item\n" : "{$name}[$key]=$item\n";
                }
                else
                    $text .= "$name=$value\n";
            }
        }
        file_put_contents( $dir . '/kickstart.ini', $text );
        $ini = eZINI::fetchFromFile( substr( $dir, strlen( expRadWizardTestHelper::root() ) + 1 ) . '/kickstart.ini' );

        $before = eZSetupStepTestINI::swap( self::KICKSTART_KEY, $ini );
        try
        {
            $list = $persistence;
            $step = $factory( $this->tpl, eZHTTPTool::instance(), eZINI::instance(), $list );
        }
        finally
        {
            eZSetupStepTestINI::swap( self::KICKSTART_KEY, $before );
        }
        return $step;
    }

    /**
     * The warnings and notices $callable raised.
     */
    public function warningsOf( $callable )
    {
        $warnings = array();
        set_error_handler( function ( $no, $message, $file, $line ) use ( &$warnings ) {
            $warnings[] = "$message ($file:$line)";
            return true;
        }, E_WARNING | E_NOTICE | E_DEPRECATED | E_USER_WARNING | E_USER_NOTICE );
        try
        {
            $callable();
        }
        finally
        {
            restore_error_handler();
        }
        return $warnings;
    }
}
