{def $current_node_id=first_set($module_result.node_id, 0)}
<div class="imageElement">
    <h4>
        <a href={$node.url_alias|ezurl} title="{$node.name|wash()}" class="line-clamp">
            {$node.name|wash()}
        </a>
    </h4>
    <figure>
    {if $node.node_id|eq( $current_node_id )}
        {attribute_view_gui attribute=$node.data_map.file width="620" height="440"}
    {else}
        {attribute_view_gui attribute=$node.data_map.file width="240" height="240" poster="favicon/favicon-banner.png"}
    {/if}
        <figcaption>{if $node.data_map.description.has_content}{attribute_view_gui attribute=$node.data_map.description}{/if}</figcaption>
    </figure>
    {attribute_view_gui attribute=$node.data_map.rating}
</div>
