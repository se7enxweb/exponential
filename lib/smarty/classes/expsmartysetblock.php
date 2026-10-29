<?php
/**
 * {expsetblock variable=x scope=... name=...}...{/expsetblock}: {set-block} with a scope (global) or
 * a namespace. {expappendblock ...}: {append-block}, which appends the output to an array.
 * {set-block} into the template's own variables is Smarty's {capture assign=x}.
 *
 * @package lib
 */
class expSmartySetBlock implements \Smarty\BlockHandler\BlockHandlerInterface
{
    /** @var bool */
    private $append;

    public function __construct( $append )
    {
        $this->append = $append;
    }

    public function handle( $params, $content, \Smarty\Template $template, &$repeat )
    {
        if ( $content === null )
            return '';
        if ( !isset( $params['variable'] ) || (string)$params['variable'] === '' )
            throw new \Smarty\Exception( "expsetblock: missing parameter 'variable'" );
        $variable = (string)$params['variable'];
        $scope = isset( $params['scope'] ) ? (string)$params['scope'] : 'relative';
        $namespace = isset( $params['name'] ) ? (string)$params['name'] : '';
        $value = $content;
        if ( $this->append )
        {
            $current = expSmartyRenderer::scopedValue( $template, $variable, $scope, $namespace );
            $value = is_array( $current ) ? $current : array();
            $value[] = $content;
        }
        expSmartyRenderer::setScoped( $template, $variable, $value, $scope, $namespace );
        return '';
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
