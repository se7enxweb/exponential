{set scope=global persistent_variable=hash('top_menu', false(),
                                           'show_path', false(),
                                           'left_menu', false(),
                                           'extra_menu', false())}
<section class="content-view-full">
    <article class="class-call-to-action">
        <div class="attribute-header">
            <h1>{$node.name|wash()}</h1>
        </div>

        <div class="attribute-description">
            {attribute_view_gui attribute=$node.data_map.description}
        </div>
    </article>
</section>
