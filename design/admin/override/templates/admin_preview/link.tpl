{* Link - Admin preview *}
<div class="content-view-full">
    <div class="class-link">

    <h1>{$node.name|wash}</h1>

    {* Description. *}
    {if $node.data_map.description.has_content}
        <div class="attribute-long">
            {attribute_view_gui attribute=$node.data_map.description}
        </div>
    {/if}

    {* URL/Link. *}
    {if $node.data_map.location.has_content}
        {* A link only to http(s), mailto or an address without a scheme; anything else is shown as text *}
        {def $link_url = $node.data_map.location.content|trim
             $link_scheme = $link_url|downcase}
        <div class="attribute-link">
            {if or( $link_scheme|begins_with( 'http://' ), $link_scheme|begins_with( 'https://' ), $link_scheme|begins_with( 'mailto:' ), $link_scheme|contains( ':' )|not )}
            <p><a href="{$link_url|wash}">{if $node.data_map.location.data_text}{$node.data_map.location.data_text|wash}{else}{$link_url|wash}{/if}</a></p>
            {else}
            <p>{if $node.data_map.location.data_text}{$node.data_map.location.data_text|wash} ({$link_url|wash}){else}{$link_url|wash}{/if}</p>
            {/if}
        </div>
        {undef $link_url $link_scheme}
    {/if}

    </div>
</div>
