{def $video_path = concat( '/content/download/', $object.main_node.data_map.file.contentobject_id, '/', $object.main_node.data_map.file.id, '/', $object.main_node.data_map.file.content.original_filename )|ezurl( 'no', 'full' )}

{ezcss_require( 'video.css' )}
{ezscript_require( 'video.js' )}
<script>
    _V_.options.flash.swf = "{'flash/video-js.swf'|ezdesign( 'no' )}"
</script>

<section class="content-view-embed">
    <article class="class-video">
        <div class="attribute-video">
            <video id="video_{$object.main_node.contentobject_id}" class="video-js vjs-default-skin" controls preload="auto" width="330" height="318" poster="" data-setup="{ldelim}{rdelim}">
              <source src="{$video_path}" type="video/mp4" />
            </video>
        </div>
        {if $object.main_node.data_map.caption.has_content}
        <div class="attribute-short">
            {attribute_view_gui attribute=$object.main_node.data_map.caption}
        </div>
        {/if}
    </article>
</section>
{undef $video_path}
