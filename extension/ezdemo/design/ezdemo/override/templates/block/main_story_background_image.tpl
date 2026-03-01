{def $valid_node = $block.valid_nodes[0]}
<article class="block-type-mainstory full-size">
    <a href="{$valid_node.url_alias|ezurl(no)}">
        <div class="attribute-image">
            {attribute_view_gui attribute=$valid_node.data_map.image image_class='backgroundimage'}
        </div>
        <div class="overlay">
        {if $valid_node.data_map.author.content.is_empty|not()}
            <div class="attribute-author">
                {attribute_view_gui attribute=$valid_node.data_map.author}
            </div>
        {/if}
            <div class="attribute-header">
                <h2>{$valid_node.name|wash()}</h2>
            </div>
        </div>
    </a>
</article>
{undef $valid_node}
