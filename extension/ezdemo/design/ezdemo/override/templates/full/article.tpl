{* Article - Full view *}
{set scope=global persistent_variable=hash('left_menu', false(),
                                           'extra_menu', false())}

<section class="content-view-full">
    <article class="class-article row">
        <div class="span8">
            <div class="attribute-header">
                <h1>{$node.data_map.title.content|wash()}</h1>
            </div>

            <div class="attribute-byline">
                <span class="date">
                    {$node.object.published|l10n(shortdatetime)}
                </span>
            {if $node.data_map.author.content.is_empty|not()}
                <span class="author">
                    {attribute_view_gui attribute=$node.data_map.author}
                </span>
            {/if}
            </div>

        {if eq( ezini( 'article', 'ImageInFullView', 'content.ini' ), 'enabled' )}
            {if $node.data_map.image.has_content}
                <div class="attribute-image full-head">
                    {attribute_view_gui attribute=$node.data_map.image image_class=articleimage}

                    {if $node.data_map.caption.has_content}
                        <div class="attribute-caption">
                            {attribute_view_gui attribute=$node.data_map.caption}
                        </div>
                    {/if}
                </div>
            {/if}
        {/if}

        {if eq( ezini( 'article', 'SummaryInFullView', 'content.ini' ), 'enabled' )}
            {if $node.data_map.intro.content.is_empty|not}
                <div class="attribute-short">
                    {attribute_view_gui attribute=$node.data_map.intro}
                </div>
            {/if}
        {/if}

        {if $node.data_map.body.content.is_empty|not}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.body}
            </div>
        {/if}

            <div class="attribute-tags">
                {attribute_view_gui attribute=$node.data_map.tags}
            </div>

            <div class="attribute-star-rating">
                {attribute_view_gui attribute=$node.data_map.star_rating}
            </div>

            <div class="attribute-socialize">
                {include uri='design:parts/social_buttons.tpl'}
            </div>

        {def $tipafriend_access=fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'tipafriend' ) )}
        {if and( ezmodule( 'content/tipafriend' ), $tipafriend_access )}
            <div class="attribute-tipafriend">
                <p><a href={concat( "/content/tipafriend/", $node.node_id )|ezurl} title="{'Tip a friend'|i18n( 'design/ezdemo/full/article' )}">{'Tip a friend'|i18n( 'design/ezdemo/full/article' )}</a></p>
            </div>
        {/if}
        </div>
        <div class="span4">
            <aside>
                <section class="content-view-aside">
                    {if $node.data_map.location.has_content}
                    <h2>{'Location'|i18n( 'design/ezdemo/full/article' )}</h2>
                    <article>
                        <div class="attribute-location">
                            {attribute_view_gui attribute=$node.data_map.location}
                        </div>
                    </article>
                    {/if}

                    {include uri='design:parts/related_content.tpl'}
                </section>
            </aside>
        </div>
    </article>
</section>

