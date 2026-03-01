{def $node = $block.valid_nodes[0]}

<div class="block-type-highlighted-item block-view-{$block.view}">
    <article class="highlighted">
        <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link">
        {if $node.data_map.image.has_content}
            <div class="attribute-image">
                {attribute_view_gui attribute=$node.data_map.image image_class='contentgrid'}
            </div>
        {/if}
            <div class="attribute-header">
                <h3>{$node.name|wash()}</h3>
            </div>
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.intro}
            </div>
        </a>
    </article>
</div>

{undef $node}
