{def $valid_node = $block.valid_nodes[0]}
<article class="block-type-mainstory picture-left">
    <a href="{$valid_node.url_alias|ezurl(no)}" title="{$valid_node.name|wash()}">
        <div class="attribute-image">
            {attribute_view_gui attribute=$valid_node.data_map.image image_class='highlighted'}
        </div>
        <div class="attribute-header">
            <h2>{$valid_node.name|wash()}</h2>
        </div>
        <div class="attribute-short">
            {attribute_view_gui attribute=$valid_node.data_map.intro}
        </div>
    </a>
</article>
{undef $valid_node}
