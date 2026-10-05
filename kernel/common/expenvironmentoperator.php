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
  \brief The template operator exp_environment() returns the environment name

  The name comes from the EXP_ENV constant in config.env.php, see
  eZINI::environment(). Without a valid environment the operator returns an
  empty string, so a template can test it directly:

  \code
  {if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
  \endcode
*/

class expEnvironmentOperator
{
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct( $name = 'exp_environment' )
    {
        $this->Operators = array( $name );
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
        $environment = eZINI::environment();
        $operatorValue = $environment === false ? '' : $environment;
    }

    /// \privatesection
    public $Operators;
}


?>
