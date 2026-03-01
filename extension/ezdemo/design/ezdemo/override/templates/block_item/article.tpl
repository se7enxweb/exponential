<article>

    <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link">
    {if $node.data_map.image.has_content}
        <div class="attribute-image">
            {attribute_view_gui attribute=$node.data_map.image image_class=$image_class}
        </div>
    {/if}
        <div class="attribute-header">
            <h3>{$node.name|wash()}</h3>
        </div>
    </a>

    <div class="attribute-short">
        {attribute_view_gui attribute=$node.data_map.intro}
    </div>

</article>
