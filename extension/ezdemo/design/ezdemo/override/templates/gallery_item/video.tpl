<!-- BOX CONTENT: START -->
<figure class="attribute-video" data-gallery-src="" data-gallery-height="" data-gallery-width="" data-gallery-node-url={$node.url_alias|ezurl()} data-gallery-node-id="{$node.node_id}" data-gallery-item="video" title="{$node.name|wash}">
    <a href={$node.url_alias|ezurl()}>
        <img src={'video-icon.png'|ezimage} alt="{$node.name|wash}" />
    </a>
    <figcaption class="attribute-caption">
        {if $node.data_map.caption.has_content}
            {attribute_view_gui attribute=$node.data_map.caption}
        {/if}
    </figcaption>
</figure>
<!-- BOX CONTENT: END -->
