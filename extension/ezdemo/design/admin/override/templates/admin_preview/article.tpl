{* Article - Admin preview *}
{def $default_attributes=array( 'title', 'author', 'image', 'intro', 'body', 'enable_comments' )}
<div class="content-view-full">
    <div class="class-article">

        <h1>{$node.data_map.title.content|wash()}</h1>

        {* Author. *}
        {if $node.data_map.author.has_content}
            <div class="attribute-byline">
                <p class="author">{attribute_view_gui attribute=$node.data_map.author}</p>
                <p class="date">
                    {if $node.object.data_map.publish_date.has_content}
                        {$node.object.data_map.publish_date.content.timestamp|l10n(date)}
                    {else}
                        {$node.object.published|l10n(date)}
                    {/if}
                </p>
                <div class="break"></div>
            </div>
        {/if}

        {* Image. *}
        {if $node.data_map.image.has_content}
            <div class="attribute-image">
                {attribute_view_gui attribute=$node.data_map.image align=right}
            </div>
        {/if}

        {* Intro. *}
        {if $node.data_map.intro.has_content}
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.intro}
            </div>
        {/if}

        {* Body. *}
        {if $node.data_map.body.has_content}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.body}
            </div>
        {/if}

        {if not( $node.data_map.star_rating.data_int )}
            <div class="attribute-short">
                {if $node.data_map.star_rating.has_content}
                    {"Rating: %current_rating/5"|i18n( 'extension/ezstarrating/datatype', '', hash( '%current_rating', $node.data_map.star_rating.content.rating ) )}
                    ({"%rating_count votes cast"|i18n( 'extension/ezstarrating/datatype', '', hash( '%rating_count', $node.data_map.star_rating.content.rating_count ) )})
                {/if}
            </div>
        {/if}

        {if $node.data_map.tags.has_content}
            <div class="attribute-short">
                <label>{$node.data_map.tags.contentclass_attribute.name|wash}:</label>
                {attribute_view_gui attribute=$node.data_map.tags}
            </div>
        {/if}

        {if $node.data_map.location.has_content}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.location}
            </div>
        {/if}

        {* Comments. *}
        <div class="content-control">
            <h6>{'Comments allowed'|i18n( 'design/admin/preview/article' )}:</h6>
            {if $node.data_map.comments.content.enable_comment  }
                <p>{'Yes'|i18n( 'design/admin/preview/article' )}</p>
            {else}
                <p>{'No'|i18n( 'design/admin/preview/article' )}</p>
            {/if}
        </div>

    </div>
</div>
