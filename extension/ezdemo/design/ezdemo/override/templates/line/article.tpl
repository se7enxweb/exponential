{* Article - Line view *}
{def $content_size = '8'}

<div class="content-view-line">
    <article class="class-article row">

    {if $node.data_map.image.has_content}
    <div class="span2">
        <div class="attribute-image">
            {attribute_view_gui image_class=articlethumbnail href=$node.url_alias|ezurl attribute=$node.data_map.image}
        </div>
    </div>
        {set $content_size = '6'}
    {/if}

    <div class="span{$content_size}">
        <div class="attribute-header">
            <h2>
                <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link">{$node.data_map.title.content|wash()}</a>
            </h2>
        </div>

        <div class="attribute-byline with-comments">
            <span class="date">
                {$node.object.published|l10n(shortdatetime)}
            </span>
        {if $node.data_map.author.content.is_empty|not()}
            <span class="author">
                {attribute_view_gui attribute=$node.data_map.author}
            </span>
        {/if}
        </div>

        {if $node.data_map.intro.content.is_empty|not}
        <div class="attribute-short">
            {attribute_view_gui attribute=$node.data_map.intro}
        </div>
        {/if}
    </div>

    </article>
</div>

{undef $content_size}
