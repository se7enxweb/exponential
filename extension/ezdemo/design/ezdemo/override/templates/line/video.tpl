{def $video_path = concat( '/content/download/', $node.data_map.file.contentobject_id, '/', $node.data_map.file.id, '/', $node.data_map.file.content.original_filename )|ezurl( 'no', 'full' )}

{ezcss_require( 'video.css' )}
{ezscript_require( 'video.js' )}

<div class="content-view-line">
    <article class="class-video row">
        <div class="span2">
            <div class="attribute-video">
                <video id="video_{$node.contentobject_id}" class="video-js vjs-default-skin" controls preload="auto" width="100%" height="113" poster="" data-setup="">
                  <source src="{$video_path}" type="video/mp4" />
                </video>
            </div>
        </div>
        <div class="span6">
            <div class="attribute-header">
                <h2>
                    <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link">{$node.name|wash()}</a>
                </h2>
            </div>
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.caption}
            </div>
        </div>
    </article>
</div>
{undef $video_path}
