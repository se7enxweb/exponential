<?php
/**
 * File containing the expKernelTransferOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The template operators of the transfer registry (expKernelTransferRegistry; doc/eng-us/features/6.0/transfer-registry.md).
 * Each returns plain arrays, only of the tools that are active here and that the current user may use:
 *
 *   transfer_tools( hash( 'direction', 'import', 'scope', 'subtree', 'format', 'csv', 'query', 'csv spreadsheet',
 *                         'exclude', 'transfer.package' ) )
 *       the tools, each hash( id, name, description, direction, scopes, formats, url, module, help_page, help_url,
 *       round_trip, keywords )
 *   transfer_actions( 'container', hash( '%nodeID%', 42 ) [, 'import'] )
 *       the context entries, each hash( tool, name, context, direction, label, url )
 *   transfer_related( 'transfer.xrowextract' )   (a tool id, its short name, or a module)
 *       the tools a "Looking for…?" box offers, each a tool hash with partner (true for a round-trip partner)
 *   transfer_tool( 'xrowextract' )               one tool hash (also an inactive one, with active false), or false
 *
 * Names, descriptions and labels are translated in the context kernel/transfer.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferOperator
{
    /** @var string[] */
    public $Operators = array( 'transfer_tools', 'transfer_actions', 'transfer_related', 'transfer_tool' );

    /** @var expKernelTransferRegistry|null for tests; the registry of the siteaccess when null */
    public static $registry = null;

    /** @var callable|null for tests; the current user's access when null */
    public static $access = null;

    /**
     * @return string[]
     */
    function operatorList()
    {
        return $this->Operators;
    }

    /**
     * @return bool
     */
    function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * @return array
     */
    function namedParameterList()
    {
        return array(
            'transfer_tools' => array( 'filter' => array( 'type' => 'array', 'required' => false, 'default' => array() ) ),
            'transfer_actions' => array( 'context' => array( 'type' => 'string', 'required' => true, 'default' => '' ),
                                         'values' => array( 'type' => 'array', 'required' => false, 'default' => array() ),
                                         'direction' => array( 'type' => 'string', 'required' => false, 'default' => '' ) ),
            'transfer_related' => array( 'tool' => array( 'type' => 'string', 'required' => true, 'default' => '' ) ),
            'transfer_tool' => array( 'tool' => array( 'type' => 'string', 'required' => true, 'default' => '' ) ),
        );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = self::run( $operatorName, $namedParameters );
    }

    /**
     * What an operator returns for its named parameters (the operators without a template).
     *
     * @param string $operatorName
     * @param array $parameters
     * @return mixed
     */
    public static function run( $operatorName, array $parameters )
    {
        if ( !class_exists( 'expKernelTransferRegistry' ) )
            return $operatorName === 'transfer_tool' ? false : array();
        $registry = self::$registry instanceof expKernelTransferRegistry ? self::$registry : expKernelTransferRegistry::instance();
        $access = self::$access;

        switch ( $operatorName )
        {
            case 'transfer_tools':
                $filter = isset( $parameters['filter'] ) && is_array( $parameters['filter'] ) ? $parameters['filter'] : array();
                $result = array();
                foreach ( $registry->select( $filter, $access ) as $tool )
                    $result[] = self::toolHash( $tool );
                return $result;

            case 'transfer_actions':
                $values = array();
                foreach ( isset( $parameters['values'] ) && is_array( $parameters['values'] ) ? $parameters['values'] : array() as $name => $value )
                {
                    // a placeholder given as itself stays for the menu's script to fill in
                    if ( is_scalar( $value ) && preg_match( '/^%[A-Za-z]+%$/', (string)$name ) )
                        $values[(string)$name] = (string)$value === (string)$name ? (string)$value : rawurlencode( (string)$value );
                }
                $direction = isset( $parameters['direction'] ) ? (string)$parameters['direction'] : '';
                $result = array();
                foreach ( $registry->actions( (string)$parameters['context'], $values, $access, $direction ) as $action )
                {
                    $action['label'] = self::tr( $action['label'] );
                    $action['name'] = self::tr( $action['name'] );
                    $result[] = $action;
                }
                return $result;

            case 'transfer_related':
                $result = array();
                foreach ( $registry->related( (string)$parameters['tool'], $access ) as $entry )
                    $result[] = self::toolHash( $entry['tool'] ) + array( 'partner' => $entry['partner'] );
                return $result;

            case 'transfer_tool':
                $tool = $registry->tool( (string)$parameters['tool'] );
                if ( $tool === null )
                    $tool = $registry->toolOfModule( (string)$parameters['tool'] );
                if ( $tool === null )
                    return false;
                $available = $registry->available( $access );
                return self::toolHash( $tool ) + array( 'active' => isset( $available[$tool->id()] ) );
        }
        return array();
    }

    /**
     * A tool as the templates see it.
     *
     * @param expKernelTransferTool $tool
     * @return array
     */
    public static function toolHash( expKernelTransferTool $tool )
    {
        $help = $tool->get( 'help_page' );
        $anchor = '';
        if ( strpos( $help, '#' ) !== false )
            list( $help, $anchor ) = explode( '#', $help, 2 );
        return array(
            'id' => $tool->id(),
            'name' => self::tr( $tool->get( 'name' ) ),
            'description' => self::tr( $tool->get( 'description' ) ),
            'direction' => $tool->get( 'direction' ),
            'scopes' => $tool->get( 'scopes' ),
            'formats' => $tool->get( 'formats' ),
            'url' => $tool->get( 'entry_url' ),
            'module' => $tool->get( 'module' ),
            'help_page' => $help,
            'help_url' => $help !== '' && class_exists( 'expHelp' ) ? ltrim( expHelp::url( 'viewer', $help, $anchor ), '/' ) : '',
            'round_trip' => $tool->get( 'round_trip' ),
            'keywords' => $tool->get( 'keywords' ),
        );
    }

    /**
     * @param string $text
     * @return string
     */
    protected static function tr( $text )
    {
        $text = (string)$text;
        if ( $text === '' || !class_exists( 'ezpI18n' ) )
            return $text;
        return ezpI18n::tr( 'kernel/transfer', $text );
    }
}
?>
