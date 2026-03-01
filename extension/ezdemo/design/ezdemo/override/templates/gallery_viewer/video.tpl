{def $video_path = concat( '/content/download/', $node.data_map.file.contentobject_id, '/', $node.data_map.file.id, '/', $node.data_map.file.content.original_filename )|ezurl( 'no', 'full' )}

{ezcss_require( 'video.css' )}
{ezscript_require( 'video.js' )}
<script>
    _V_.options.flash.swf = "{'flash/video-js.swf'|ezdesign( 'no' )}"
</script>
<figure id="gallery-viewer-video-{$node.node_id}" {if $is_hidden}class="hidden"{else}class="visible"{/if}>
    <video id="video_{$node.node_id}" class="video-js vjs-default-skin" controls preload="auto" width="330" height="264" poster="" data-setup="{ldelim}{rdelim}">
      <source src="{$video_path}" type="video/mp4" />
    </video>
    <figcaption>
        <h3>
            <span class="counter"><span>1</span>/{$total_items}</span>
            <a href={$node.url_alias|ezurl}>{$node.name|wash()}</a>
        </h3>
        <div>
        {if $node.data_map.caption.has_content}
            {attribute_view_gui attribute=$node.data_map.caption}
        {/if}
        </div>
    </figcaption>
</figure>
{undef $video_path}
