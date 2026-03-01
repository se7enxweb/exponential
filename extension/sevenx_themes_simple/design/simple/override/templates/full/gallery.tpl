<h1 class="name">{$node.name|wash()}</h1>
{def $children=fetch( content , list , hash( parent_node_id , $node.node_id ,
                                             class_filter_type , 'include' ,
                                             class_filter_array , array( 'image', 'video' ),
                                             sort_by, array( array( 'priority' , true() ), array( 'published' , true() ) ) ))}
<div id="myGallery{$node.node_id}" class="myGallery">
    {foreach $children as $image_node}
        {node_view_gui content_node=$image_node view='image_gallery' width="320" height="240"} 
    {/foreach}
</div>