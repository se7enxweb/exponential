{def $global_layout_object = fetch( 'content', 'tree', hash( 'parent_node_id', 1,
                                                             'limit', 1,
                                                             'class_filter_type', include,
                                                             'class_filter_array', array( 'global_layout' ) ) )}

<!-- ZONE CONTENT: START -->

{if $global_layout_object}
<aside>
    {attribute_view_gui attribute=$global_layout_object[0].data_map.page}
</aside>
{/if}

<!-- ZONE CONTENT: END -->
