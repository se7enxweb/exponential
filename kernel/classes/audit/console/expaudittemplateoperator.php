<?php
/**
 * The template operator audit_label (doc/bc/6.0/audit.md, "Template operator and fetch"): the human label of an
 * audit event name from the taxonomy registry, translated in the context kernel/audit.
 *
 *   {$e.name|audit_label|wash}      content.node.move -> "Node move"
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditTemplateOperator
{
    public function operatorList()
    {
        return array( 'audit_label' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'audit_label' => array() );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $name = is_scalar( $operatorValue ) ? (string)$operatorValue : '';
        if ( $name === '' )
        {
            $operatorValue = '';
            return;
        }
        $operatorValue = class_exists( 'expAuditConsole' ) ? expAuditConsole::label( $name ) : $name;
    }
}
