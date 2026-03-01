{* Gallery - Line view *}
{def $content_size = '8'}

<div class="content-view-line">
    <article class="class-gallery row">
    {if $node.data_map.image.has_content}
    <div class="span2">
        <div class="attribute-image">
            {attribute_view_gui image_class=medium href=$node.url_alias|ezurl attribute=$node.data_map.image.content.data_map.image}
        </div>
    </div>
        {set $content_size = '6'}
    {/if}
    <div class="span{$content_size}">
        <div class="attribute-header">
            <h2>
                <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link">{$node.name|wash()}</a>
            </h2>
        </div>
        {if $node.data_map.short_description.has_content}
        <div class="attribute-short">
            {attribute_view_gui attribute=$node.data_map.short_description}
        </div>
        {/if}
    </div>
    </article>
</div>

{undef $content_size}
