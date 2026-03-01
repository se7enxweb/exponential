{* Infobox - Full view *}

<div class="content-view-full">
    <div class="class-infobox">

        <div class="attribute-header">
            <h1>{attribute_view_gui attribute=$node.object.data_map.header}</h1>
        </div>
        
        <div class="attribute-image">
            {attribute_view_gui attribute=$node.object.data_map.image image_class='infoboximage'}
        </div>
        
        <div class="attribute-content">
            {attribute_view_gui attribute=$node.object.data_map.content}
        </div>
        
        <div class="attribute-link">
            {attribute_view_gui attribute=$node.object.data_map.url}
        </div>

    </div>
</div>
