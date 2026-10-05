<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Page class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * What the views of the mailpreferences module share: the variables of the including function, rendering a page of
 * design:mailpreferences/, the answer when the e-mail preferences are not installed.
 */
abstract class Page extends \Exponential\Runnable\ModuleView
{
    /** @var array the view's parameters ($Params) */
    protected $params = array();

    /** @var \eZModule */
    protected $module;

    /** @var \eZHTTPTool */
    protected $http;

    public function run( array $scope )
    {
        $this->params = isset( $scope['Params'] ) ? $scope['Params'] : array();
        $this->module = isset( $this->params['Module'] ) ? $this->params['Module'] : ( isset( $scope['Module'] ) ? $scope['Module'] : null );
        $this->http = \eZHTTPTool::instance();
        if ( !MailPreferencesPage::available() )
            return $this->viewResult( null, $this->module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        return $this->viewResult( null, $this->page() );
    }

    /**
     * The view itself.
     *
     * @return mixed the view's result
     */
    abstract protected function page();

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed a parameter of the view
     */
    protected function param( $name, $default = null )
    {
        return isset( $this->params[$name] ) && $this->params[$name] !== '' ? $this->params[$name] : $default;
    }

    /**
     * Renders a template of design:mailpreferences/ as the view's result.
     *
     * @param string $template e.g. 'settings.tpl', 'admin/status.tpl'
     * @param array $variables
     * @param string $title the last element of the path
     * @param bool $private the page belongs to one person (no caching, no referrer, not indexed)
     * @return array $Result
     */
    public function render( $template, array $variables, $title, $private = true )
    {
        if ( $private )
            MailPreferencesPage::privateHeaders();
        $tpl = \eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        return array( 'content' => $tpl->fetch( 'design:mailpreferences/' . $template ),
                      'path' => array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/mailpreferences', 'E-mail preferences' ) ),
                                       array( 'url' => false, 'text' => $title ) ) );
    }

    /** @return bool the current user may use the administrator's pages */
    protected static function canAdministrate()
    {
        $access = \eZUser::currentUser()->hasAccessTo( 'mailpreferences', 'administrate' );
        return $access['accessWord'] !== 'no';
    }

    /** @return bool the request is a POST */
    protected static function isPost()
    {
        return isset( $_SERVER['REQUEST_METHOD'] ) && strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST';
    }
}

}
