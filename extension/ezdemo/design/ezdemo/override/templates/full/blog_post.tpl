{* Blog post - Full view *}
{set scope=global persistent_variable=hash('left_menu', false(),
                                           'extra_menu', false())}


<section class="content-view-full">
    <div class="class-blog-post">
        <div class="row">
            <div class="span8">
                <div class="attribute-header">
                    <h1>{$node.data_map.title.content|wash}</h1>
                </div>

                <div class="attribute-byline">
                    <span class="date">{$node.data_map.publication_date.content.timestamp|l10n(shortdatetime)}</span>
                    <span class="author">{$node.object.owner.name}</span>
                </div>

                <div class="attribute-body float-break">
                {attribute_view_gui attribute=$node.data_map.body}
                </div>

                <div class="attribute-tags">
                    {attribute_view_gui attribute=$node.data_map.tags}
                </div>

                {include uri='design:parts/related_content.tpl'}

            </div>
            <div class="span4">
                <aside>
                    <section class="content-view-aside">
                        {include uri='design:parts/blog/extra_info.tpl' used_node=$node.parent}
                    </section>
                </aside>
            </div>
        </div>
    </div>
</section>
