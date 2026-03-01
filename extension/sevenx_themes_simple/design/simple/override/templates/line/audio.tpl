<div class="imageElement">
    <h4>
        <a href={$node.url_alias|ezurl()}>{$node.name|wash()}</a>
    </h4>
    <figure>
        {attribute_view_gui attribute=$node.data_map.file width="620" height="440" poster="favicon/favicon-banner.png"}
        <figcaption>{if $node.data_map.description.has_content}{attribute_view_gui attribute=$node.data_map.description}{/if}</figcaption>
    </figure>
        {*attribute_view_gui attribute=$node.data_map.rating *}
</div>
