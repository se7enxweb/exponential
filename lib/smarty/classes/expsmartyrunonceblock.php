<?php
/**
 * {exprunonce id=N}...{/exprunonce}: the template system's {run-once}: the body is output the first
 * time the block runs in a request only.
 *
 * @package lib
 */
class expSmartyRunOnceBlock implements \Smarty\BlockHandler\BlockHandlerInterface
{
    public function handle( $params, $content, \Smarty\Template $template, &$repeat )
    {
        $key = 'stpl:' . $template->getSource()->getResourceName() . ':' . ( isset( $params['id'] ) ? $params['id'] : '0' );
        if ( $content === null )
        {
            if ( isset( $GLOBALS['eZTemplateRunOnceKeys'][$key] ) )
            {
                $repeat = false;
                return '';
            }
            $GLOBALS['eZTemplateRunOnceKeys'][$key] = true;
            return '';
        }
        return $content;
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
