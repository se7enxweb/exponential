{def $node = $block.valid_nodes[0]
     $video_path = concat( '/content/download/', $node.data_map.file.contentobject_id, '/', $node.data_map.file.id, '/', $node.data_map.file.content.original_filename )|ezurl( 'no', 'full' )}

{ezcss_require( 'video.css' )}
{ezscript_require( 'video.js' )}
<script>
    _V_.options.flash.swf = "{'flash/video-js.swf'|ezdesign( 'no' )}"
</script>

<div class="block-type-video">
    <div class="attribute-header">
        <h2>{$block.name|wash()}</h2>
    </div>
    <article>
        <div class="attribute-video">
            <video id="video_{$block.id}" class="video-js vjs-default-skin" controls preload="auto" width="330" height="264" poster="" data-setup="{ldelim}{rdelim}">
              <source src="{$video_path}" type="video/mp4" />
            </video>
        </div>

        <div class="attribute-header">
            <h3>{$node.name|wash()}</h3>
        </div>

        <div class="attribute-short">
            {attribute_view_gui attribute=$node.data_map.caption}
        </div>
    </article>
</div>
{undef $node $video_path}
