<?php
/**
 * What the views of the staticpublish module share: the target of the address with the policy check (the limitation
 * Target), the path of the page (Setup > Static publishing > ...), the messages of a job that was not started and of
 * an action, and the template.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

abstract class Page extends \Exponential\Runnable\ModuleView
{
    /** The translation context of the module's pages */
    const I18N = 'design/admin/staticpublish';

    /** @return string a translated text of the module */
    protected static function t( $text, array $values = array() )
    {
        return \ezpI18n::tr( self::I18N, $text, null, $values );
    }

    /**
     * The target named in the address when the user may do a function with it, else null.
     *
     * @param array $Params
     * @param string $function
     * @return \expStaticTarget|null
     */
    protected static function target( array $Params, $function = 'read' )
    {
        $id = isset( $Params['TargetID'] ) ? trim( rawurldecode( (string)$Params['TargetID'] ) ) : '';
        if ( $id === '' || !preg_match( \expStaticTarget::ID_PATTERN, $id ) )
            return null;
        $target = \expStaticTarget::get( $id );
        if ( $target === null || !\expStaticAdmin::canDo( $function, $id ) )
            return null;
        return $target;
    }

    /**
     * The path of a page: Setup > Static publishing > [target >] page.
     *
     * @param string|null $text the page ('' or null for the dashboard)
     * @param \expStaticTarget|null $target
     * @return array
     */
    protected static function path( $text = null, $target = null )
    {
        $path = array( array( 'url' => 'setup/menu', 'text' => self::t( 'Setup' ) ),
                       array( 'url' => $text ? 'staticpublish/dashboard' : false, 'text' => self::t( 'Static publishing' ) ) );
        if ( $target instanceof \expStaticTarget )
            $path[] = array( 'url' => false, 'text' => $target->name() !== '' ? $target->name() : $target->id() );
        if ( $text )
            $path[] = array( 'url' => false, 'text' => $text );
        return $path;
    }

    /**
     * The message of a job that was not started.
     *
     * @param array $started expStaticJob::start()
     * @return string
     */
    public static function refusal( array $started )
    {
        $detail = array( '%detail' => $started['detail'] );
        switch ( $started['refused'] )
        {
            case \expStaticJob::REFUSED_BUSY:
                return self::t( 'A job of this target is still going on (%detail). Wait for it to end.', $detail );
            case \expStaticJob::REFUSED_LOCKED:
                return self::t( 'Another process works on this target: %detail', $detail );
            case \expStaticJob::REFUSED_USER:
                return self::t( 'The job needs a signed-in user.' );
            case \expStaticJob::REFUSED_PHP:
                return self::t( 'No php command line binary was found. Set migration.ini [AdminSettings] PhpCliPath to its full path.' );
            case \expStaticJob::REFUSED_PROC_OPEN:
                return self::t( 'proc_open is disabled, so a job cannot be started here. Use ./console exp:static:build on the server.' );
            case \expStaticJob::REFUSED_DIRECTORY:
                return self::t( 'The folder of the jobs cannot be written: %detail', $detail );
            case \expStaticJob::REFUSED_PID:
                return self::t( 'The job was started but did not report its process id. Check %detail.', $detail );
            case \expStaticJob::REFUSED_TARGET:
                return self::t( 'The job could not be prepared: %detail', $detail );
        }
        return self::t( 'The job could not be started.' );
    }

    /** @return string the login of the signed-in user */
    protected static function login()
    {
        return (string)\eZUser::currentUser()->attribute( 'login' );
    }

    /** @return bool whether the request is a POST */
    protected static function isPost()
    {
        return strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
    }

    /**
     * The result of a page.
     *
     * @param string $template below design:staticpublish/
     * @param array $variables
     * @param array $path
     * @return array
     */
    protected static function page( $template, array $variables, array $path )
    {
        $tpl = \eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:staticpublish/' . $template );
        $Result['path'] = $path;
        return $Result;
    }
}

}
?>
