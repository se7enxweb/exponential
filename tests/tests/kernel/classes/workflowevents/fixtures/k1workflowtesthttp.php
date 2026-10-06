<?php
/**
 * Stands in for eZHTTPTool in the workflow event tests: POST and session variables from plain arrays, no request
 * and no session.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class k1WorkflowTestHTTP
{
    public $post;
    public $session;

    public function __construct( array $post = array(), array $session = array() )
    {
        $this->post = $post;
        $this->session = $session;
    }

    function hasPostVariable( $name )
    {
        return array_key_exists( $name, $this->post );
    }

    function postVariable( $name, $fallback = null )
    {
        return array_key_exists( $name, $this->post ) ? $this->post[$name] : $fallback;
    }

    function hasSessionVariable( $name, $forceStart = true )
    {
        return array_key_exists( $name, $this->session );
    }

    function sessionVariable( $name, $fallback = null, $forceStart = true )
    {
        return array_key_exists( $name, $this->session ) ? $this->session[$name] : $fallback;
    }

    function setSessionVariable( $name, $value )
    {
        $this->session[$name] = $value;
    }

    function removeSessionVariable( $name )
    {
        unset( $this->session[$name] );
    }
}
