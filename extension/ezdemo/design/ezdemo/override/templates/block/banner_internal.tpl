{def $image_node = fetch( 'content', 'node', hash( 'node_id', $block.custom_attributes.image_node_id ) )
     $image = $image_node.data_map.image.content['banner']
     $has_url = and( is_set( $block.custom_attributes.url ), ne( $block.custom_attributes.url, '' ) )}

<div class="block-type-banner-internal">
    {if $has_url}<a href="{$block.custom_attributes.url}" title="{$image_node.name|wash}">{/if}<img src={$image.url|ezroot} width="{$image.width}" height="{$image.height}" alt="{$image_node.name|wash}" />{if $has_url}</a>{/if}
</div>
{undef $image_node $image $has_url}
