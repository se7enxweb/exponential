{def $image = $node.data_map.image.content[$image_class]}
<!-- BOX CONTENT: START -->
<figure class="attribute-image">
    <img src={$image.url|ezroot} width="{$image.width}" height="{$image.height}" alt="{$node.name|wash}" />
    <figcaption class="attribute-caption">
        {if $node.data_map.intro.has_content}
            {attribute_view_gui attribute=$node.data_map.intro}
        {/if}
        <h3><a href={$node.url_alias|ezurl()}>{$node.name|wash()}</a></h3>
    </figcaption>
</figure>
<!-- BOX CONTENT: END -->
{undef $image}
