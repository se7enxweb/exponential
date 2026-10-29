<?php
/**
 * {expcacheblock id=N keys=... expiry=... ignore_content_expiry=... subtree_expiry=...}...{/expcacheblock}:
 * the template system's {cache-block}, stored and expired exactly as a .tpl cache-block
 * (template-block cache, the same keys plus the siteaccess, global and subtree expiry).
 * When the block is cached, its body does not run.
 *
 * @package lib
 */
class expSmartyCacheBlock implements \Smarty\BlockHandler\BlockHandlerInterface
{
    /** @var array[] cache handlers of the open blocks */
    private static $open = array();

    public function handle( $params, $content, \Smarty\Template $template, &$repeat )
    {
        if ( eZINI::instance()->variable( 'TemplateSettings', 'TemplateCache' ) != 'enabled' )
            return $content === null ? '' : $content;

        if ( $content === null )
        {
            $accessName = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : false;
            if ( !empty( $GLOBALS['expUISwitchVariantKey'] ) )
                $accessName .= '-' . $GLOBALS['expUISwitchVariantKey'];
            $placement = 'stpl:' . $template->getSource()->getResourceName() . ':' . ( isset( $params['id'] ) ? $params['id'] : '0' );
            $keys = isset( $params['keys'] ) ? expSmartyRenderer::unwrap( $params['keys'] ) : null;
            $keyArray = $keys === null ? array( $placement, $accessName ) : array( $keys, $placement, $accessName );
            $subtreeExpiry = isset( $params['subtree_expiry'] ) ? expSmartyRenderer::unwrap( $params['subtree_expiry'] ) : null;
            $expiry = isset( $params['expiry'] ) ? (int)$params['expiry'] : eZTemplateCacheFunction::DEFAULT_TTL;
            $ignoreContentExpiry = isset( $params['ignore_content_expiry'] ) ? $params['ignore_content_expiry'] === true : false;
            if ( $subtreeExpiry !== null )
                $ignoreContentExpiry = true;

            list( $handler, $cached ) = eZTemplateCacheBlock::retrieve( $keyArray, $subtreeExpiry, $expiry > 0 ? $expiry : 0, !$ignoreContentExpiry );
            if ( !( $cached instanceof eZClusterFileFailure ) )
            {
                $repeat = false;
                return $cached;
            }
            self::$open[] = $handler;
            return '';
        }

        $handler = array_pop( self::$open );
        if ( $handler )
            eZTemplateCacheBlock::store( $handler, $content );
        return $content;
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
