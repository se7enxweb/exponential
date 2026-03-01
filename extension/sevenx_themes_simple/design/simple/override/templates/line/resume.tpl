{* Article - Line view *}

<div class="content-view-line">
    <div class="class-article float-break">

    <h2><a href={$object.main_node.url_alias|ezurl}>{$object.main_node.data_map.title.content|wash}</a></h2>

    {section show=$object.main_node.data_map.image.has_content}
        <div class="attribute-image">
            {attribute_view_gui image_class=articlethumbnail href=$object.main_node.url_alias|ezurl attribute=$object.main_node.data_map.image}
        </div>
    {/section}

    {section show=$object.main_node.data_map.intro.content.is_empty|not}
    <div class="attribute-short">
        {attribute_view_gui attribute=$object.main_node.data_map.intro}
    </div>
    {/section}

    {section show=$object.main_node.data_map.body.content.is_empty|not}
    <div class="attribute-long">
        {attribute_view_gui attribute=$object.main_node.data_map.body}
    </div>
    {/section}

    </div>
</div>