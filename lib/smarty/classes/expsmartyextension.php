<?php
/**
 * Smarty extension that makes Exponential's template operators and functions available in
 * .stpl templates. Smarty's own tags and modifiers always come first.
 *
 *  - Modifiers: any Exponential operator, e.g. {$node.name|wash}, {'/content/view'|ezurl:'no'},
 *    {'Read more'|i18n:'design/standard/node'}. The piped value is the operator's input,
 *    the modifier parameters are the operator parameters. Without an input, as a function:
 *    {concat('a', $b)}, {fetch('content', 'node', ['node_id' => 2])}.
 *  - exp_<operator>: the operator, also where Smarty has a modifier of the same name
 *    ({$list|exp_count}, {$text|exp_explode:','}).
 *  - Helpers of the converted templates: expattr, expvar, expvoid, expsequence, expslice, exprange,
 *    expsectionloop, expcolumn (see doc/eng-us/guides/smarty-templates-operators.md).
 *  - Functions: every template function of the template system that has no children
 *    ({attribute_view_gui attribute=$node.data_map.body}, {node_view_gui ...}), plus the tags of
 *    this extension: {expinclude}, {expset}, {expundefall}; blocks {expsetblock}, {expappendblock},
 *    {expcacheblock}, {exprunonce}; {expfetch assign=name module=... function=... <params>}.
 *
 * Callbacks are looked up when a compiled Smarty template runs, never stored in it.
 *
 * @package lib
 */
class expSmartyExtension extends \Smarty\Extension\Base
{
    /** Template functions the extension never bridges as tags: they are template structure. */
    const NOT_BRIDGED = array(
        'if', 'elseif', 'else', 'foreach', 'for', 'while', 'do', 'def', 'undef', 'set', 'let', 'default',
        'section', 'section-else', 'section-exclude', 'section-include', 'delimiter', 'switch', 'case',
        'set-block', 'append-block', 'cache-block', 'run-once', 'include', 'literal', 'ldelim', 'rdelim',
        'break', 'continue', 'skip',
    );

    /**
     * @var array[] what the lookups below found, by kind and name. A compiled template asks for its modifiers,
     * functions and blocks every time it runs one (thousands of times on a page); the answer for a name does not
     * change while the process runs, so it is found once.
     */
    private static $found = array( 'modifier' => array(), 'function' => array(), 'block' => array() );

    public function getModifierCallback( string $modifierName )
    {
        if ( !array_key_exists( $modifierName, self::$found['modifier'] ) )
            self::$found['modifier'][$modifierName] = $this->findModifierCallback( $modifierName );
        return self::$found['modifier'][$modifierName];
    }

    public function getFunctionHandler( string $functionName ): ?\Smarty\FunctionHandler\FunctionHandlerInterface
    {
        if ( !array_key_exists( $functionName, self::$found['function'] ) )
            self::$found['function'][$functionName] = $this->findFunctionHandler( $functionName );
        return self::$found['function'][$functionName];
    }

    public function getBlockHandler( string $blockTagName ): ?\Smarty\BlockHandler\BlockHandlerInterface
    {
        if ( !array_key_exists( $blockTagName, self::$found['block'] ) )
            self::$found['block'][$blockTagName] = $this->findBlockHandler( $blockTagName );
        return self::$found['block'][$blockTagName];
    }

    private function findModifierCallback( string $modifierName )
    {
        switch ( $modifierName )
        {
            case 'fetch':
                return function ( $module = null, ...$parameters )
                {
                    return expSmartyRenderer::callOperator( 'fetch', $module, $parameters );
                };
            case 'expattr':
                return array( 'expSmartyRenderer', 'attributeOf' );
            case 'expvar':
                return array( 'expSmartyRenderer', 'globalVariable' );
            case 'expvoid':
                return array( 'expSmartyRenderer', 'void' );
            case 'expsequence':
                return function ( $sequence, $index = 0 )
                {
                    $sequence = is_array( $sequence ) ? array_values( $sequence ) : array( $sequence );
                    return $sequence ? $sequence[( (int)$index ) % count( $sequence )] : null;
                };
            case 'expslice':
                return array( 'expSmartyRenderer', 'loopSlice' );
            case 'expsectionloop':
                return array( 'expSmartyRenderer', 'sectionLoop' );
            case 'exprange':
                return function ( $from, $to )
                {
                    return range( (int)$from, (int)$to );
                };
            case 'expcolumn':
                return function ( $array, $key )
                {
                    $values = array();
                    foreach ( (array)$array as $item )
                        $values[] = expSmartyRenderer::attributeOf( $item, $key );
                    return $values;
                };
        }
        // an operator whose own name begins with exp_ (exp_can_login_to_siteaccess) is called by that name; for any
        // other exp_<name>, the prefix only steps past a Smarty modifier of the same name
        if ( strncmp( $modifierName, 'exp_', 4 ) === 0 && strlen( $modifierName ) > 4 && !expSmartyRenderer::hasOperator( $modifierName ) )
        {
            $operatorName = substr( $modifierName, 4 );
            return function ( $input = null, ...$parameters ) use ( $operatorName )
            {
                return expSmartyRenderer::callOperator( $operatorName, $input, $parameters );
            };
        }
        if ( expSmartyRenderer::hasOperator( $modifierName ) )
        {
            return function ( $input = null, ...$parameters ) use ( $modifierName )
            {
                return expSmartyRenderer::callOperator( $modifierName, $input, $parameters );
            };
        }
        return null;
    }

    private function findFunctionHandler( string $functionName ): ?\Smarty\FunctionHandler\FunctionHandlerInterface
    {
        switch ( $functionName )
        {
            case 'expfetch':
                return new expSmartyFetchFunction();
            case 'expinclude':
                return new expSmartyIncludeFunction();
            case 'expset':
                return new expSmartySetFunction();
            case 'expundefall':
                return new expSmartyUndefAllFunction();
        }
        $functions = eZINI::instance( 'smarty.ini' )->variable( 'SmartySettings', 'FunctionList' );
        if ( is_array( $functions ) && in_array( $functionName, $functions, true ) )
            return new expSmartyFunction( $functionName );
        if ( !in_array( $functionName, self::NOT_BRIDGED, true ) && expSmartyRenderer::hasFunction( $functionName ) &&
             !expSmartyRenderer::isBlockFunction( $functionName ) )
            return new expSmartyFunction( $functionName );
        return null;
    }

    private function findBlockHandler( string $blockTagName ): ?\Smarty\BlockHandler\BlockHandlerInterface
    {
        switch ( $blockTagName )
        {
            case 'expsetblock':
                return new expSmartySetBlock( false );
            case 'expappendblock':
                return new expSmartySetBlock( true );
            case 'expcacheblock':
                return new expSmartyCacheBlock();
            case 'exprunonce':
                return new expSmartyRunOnceBlock();
        }
        if ( !in_array( $blockTagName, self::NOT_BRIDGED, true ) && expSmartyRenderer::hasFunction( $blockTagName ) )
            return new expSmartyBlockFunction( $blockTagName );
        return null;
    }
}
