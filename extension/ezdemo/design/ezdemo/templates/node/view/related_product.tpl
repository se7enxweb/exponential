<div class="related-product">
    <h3><a href="{$node.url_alias|ezurl( 'no' )}" title="{$node.name|wash()}">{$node.name|wash()}</a></h3>

    <div class="attribute-image">
        {attribute_view_gui attribute=$node.data_map.image image_class=medium href=$node.url_alias|ezurl('no')}
    </div>
</div>
