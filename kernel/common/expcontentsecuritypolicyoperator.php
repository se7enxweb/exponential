<?php
/**
 * File containing the expContentSecurityPolicyOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The template operator csp_nonce(): the nonce of the Content Security Policy of this request, for an inline script
 * a template cannot move into a file (expContentSecurityPolicy::templateNonce()).
 *
 * <code>
 * <script nonce="{csp_nonce()}">...</script>
 * </code>
 *
 * An empty string when the policy of the siteaccess carries no nonce. The nonce is new for every request: never print
 * it inside a {cache-block} or a view-cached template, whose cached copy would carry the nonce of another request.
 */
class expContentSecurityPolicyOperator
{
    /** @var array */
    public $Operators;

    public function __construct( $name = 'csp_nonce' )
    {
        $this->Operators = array( $name );
    }

    function operatorList()
    {
        return $this->Operators;
    }

    function namedParameterList()
    {
        return array();
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = class_exists( 'expContentSecurityPolicy' ) ? expContentSecurityPolicy::templateNonce() : '';
    }
}
