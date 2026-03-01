<div class="imageElement">
    <h4>
        <a href={$node.url_alias|ezurl} title="{$node.name|wash()}" class="line-clamp">
            {$node.name|wash()}
        </a>
    </h4>
    <figure>
        <a 
            href={$node.data_map.image.content.large.full_path|ezroot}
            title="{if $node.data_map.caption.has_content}{$node.data_map.caption.content.output.output_text}{/if}"
            class="open"
        >
            <img src={$node.data_map.image.content.large.full_path|ezroot} class="thumbnail" alt="{$node.data_map.image.content.alternative_text|wash()}" />
        </a>
        <figcaption>{if $node.data_map.caption.has_content}{$node.data_map.caption.content.output.output_text}{/if}</figcaption>
    </figure>
    {attribute_view_gui attribute=$node.data_map.rating}
</div>
