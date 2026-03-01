{def $valid_nodes = $block.valid_nodes}

<div class="block-type-content-grid block-view-{$block.view}">

    {if ne( $block.name, '' )}
    <div class="attribute-header">
        <h2>{$block.name|wash()}</h2>
    </div>
    {/if}

    <div class="row">
    {foreach $valid_nodes as $valid_node max 4}
        <div class="span4">
        {node_view_gui view='block_item' image_class='contentgrid' content_node=$valid_node}
        </div>

        {delimiter modulo=2}
        </div>
        <div class="row">
        {/delimiter}
    {/foreach}
    </div>

</div>

{undef $valid_nodes}
