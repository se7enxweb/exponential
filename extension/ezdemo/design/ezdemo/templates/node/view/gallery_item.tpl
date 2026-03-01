{def $big = $node.data_map.image.content[$big_class]
     $thumb = $node.data_map.image.content[$thumb_class]}
<!-- BOX CONTENT: START -->
<figure class="attribute-image" data-gallery-src={$big.url|ezroot} data-gallery-height="{$big.height}" data-gallery-width="{$big.width}" data-gallery-node-url={$node.url_alias|ezurl()} data-gallery-node-id="{$node.node_id}" data-gallery-item="image" title="{$node.name|wash}">
    <a href={$node.url_alias|ezurl()}><img src={$thumb.url|ezroot}
         width="{$thumb.width}" height="{$thumb.height}"
         alt="{$node.name|wash}" /></a>
    <figcaption class="attribute-caption">
        {if $node.data_map.caption.has_content}
            {attribute_view_gui attribute=$node.data_map.caption}
        {/if}
    </figcaption>
</figure>
<!-- BOX CONTENT: END -->
{undef $big $thumb}
