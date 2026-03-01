{def $image = $node.data_map.image.content[$big_class]}
<figure class="gallery-viewer-image{if $is_hidden} hidden{else} visible{/if}">
    <img src={$image.url|ezroot} alt="{$node.name|wash()}" height="{$image.height}" width="{$image.width}" />
    <figcaption>
        <h3>
            <span class="counter"><span>1</span>/{$total_items}</span>
            <a href={$node.url_alias|ezurl}>{$node.name|wash()}</a>
        </h3>
        <div>
        {if $node.data_map.caption.has_content}
            {attribute_view_gui attribute=$node.data_map.caption}
        {/if}
        </div>
    </figcaption>
</figure>
{undef $image}
