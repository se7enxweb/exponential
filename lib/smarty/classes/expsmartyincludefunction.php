<?php
/**
 * {expinclude uri='design:parts/x.stpl' name=... var=value ...}: the template system's {include}.
 *
 * Works as {include} does in a .tpl: the URI goes through the design resource and the override
 * rules, the included template may be a .tpl or a .stpl, and the parameters become its
 * variables. Without name=, the included template shares the namespace of the including one:
 * it sees all of its variables, and what it defines is visible after the include, as with
 * {include} in a .tpl. With name=, it runs in a namespace of that name below.
 *
 * @package lib
 */
class expSmartyIncludeFunction implements \Smarty\FunctionHandler\FunctionHandlerInterface
{
    public function handle( $params, \Smarty\Template $template )
    {
        if ( !isset( $params['uri'] ) || (string)$params['uri'] === '' )
            throw new \Smarty\Exception( "expinclude: missing parameter 'uri'" );
        $uri = (string)$params['uri'];
        $name = isset( $params['name'] ) ? (string)$params['name'] : '';
        unset( $params['uri'], $params['name'] );

        list( $tpl, $rootNamespace, $currentNamespace ) = expSmartyRenderer::context() ?: array( eZTemplate::factory(), '', '' );
        $namespace = $currentNamespace;
        if ( $name !== '' )
            $namespace = $namespace !== '' ? "$namespace:$name" : $name;
        $shared = $name === '' && $namespace === $rootNamespace;

        // A .stpl in the shared namespace runs as a Smarty subtemplate of this one: the same file the template
        // system would load (the design resource, its override rules and keys), the same variables, without the
        // detour through the template system, its stub and a second render.
        if ( $shared )
        {
            $file = expSmartyRenderer::resolveURI( $tpl, $uri, $matchedName );
            if ( $file !== null && expSmartyRenderer::isSmartyTemplate( $file ) )
                return expSmartyRenderer::renderSubtemplate( $template, $file, $params, $uri, $matchedName );
        }

        if ( $shared )
            expSmartyRenderer::exportVariables( $template, $tpl, $namespace );

        // As the include function: the parameters replace variables of the same name for the
        // included template only.
        $unset = array();
        $replaced = array();
        foreach ( $params as $key => $value )
        {
            if ( $tpl->hasVariable( $key, $namespace ) )
                $replaced[$key] = $tpl->variable( $key, $namespace );
            else
                $unset[] = $key;
            $tpl->setVariable( $key, expSmartyRenderer::unwrap( $value ), $namespace );
        }

        $textElements = array();
        $extraParameters = array();
        $tpl->processURI( $uri, true, $extraParameters, $textElements, $namespace, $namespace );

        foreach ( $unset as $key )
            $tpl->unsetVariable( $key, $namespace );
        foreach ( $replaced as $key => $value )
            $tpl->setVariable( $key, $value, $namespace );

        if ( $shared )
            expSmartyRenderer::importVariables( $template, $tpl, $namespace );

        return implode( '', $textElements );
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
