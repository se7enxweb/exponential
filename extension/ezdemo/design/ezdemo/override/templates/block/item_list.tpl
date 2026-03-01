{def $valid_nodes = $block.valid_nodes}

<div class="block-type-itemlist item-list">

    <div class="attribute-header">
        <h2>{$block.name|wash()}</h2>
    </div>

    <article>
        <ul>
        {foreach $valid_nodes as $valid_node}
           <li><a href={$valid_node.url_alias|ezurl()}>{$valid_node.name|wash()}</a></li>
        {/foreach}
        </ul>
    </article>

</div>

{undef $valid_nodes}
