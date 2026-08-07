<?php
/**
 * Template operators for exposing the installation name to templates:
 *
 *   {installation_name()}                        -> tap_a
 *   {installation_name( hide_on_prod=true() )}   -> '' on production
 *   {is_production_system()}                     -> true / false
 */

class ExpInstallationOperator
{
    public $Operators = array( 'installation_name', 'is_production_system' );

    function operatorList()
    {
        return $this->Operators;
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'installation_name' => array(
                'hide_on_prod' => array( 'type' => 'boolean',
                                         'required' => false,
                                         'default' => false )
            ),
            'is_production_system' => array()
        );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'installation_name':
            {
                $hideOnProd = isset( $namedParameters['hide_on_prod'] ) ? (bool)$namedParameters['hide_on_prod'] : false;

                if ( $hideOnProd && ExpInstallationDetailsOutputFilter::isProductionSystem() )
                {
                    $operatorValue = '';
                }
                else
                {
                    $operatorValue = ExpInstallationDetailsOutputFilter::installationName();
                }
            } break;

            case 'is_production_system':
            {
                $operatorValue = ExpInstallationDetailsOutputFilter::isProductionSystem();
            } break;
        }
    }
}
