{def $valid_nodes = $block.valid_nodes}

<div class="block-type-content-grid block-view-{$block.view}">

    {if ne( $block.name, '' )}
    <div class="attribute-header">
        <h2>{$block.name|wash()}</h2>
    </div>
    {/if}

    {foreach $valid_nodes as $valid_node max 2}
        {node_view_gui view='block_item' image_class='contentgrid' content_node=$valid_node}
    {/foreach}

</div>

{undef $valid_nodes}
