{def $video_path = concat( '/content/download/', $node.data_map.file.contentobject_id, '/', $node.data_map.file.id, '/', $node.data_map.file.content.original_filename )|ezurl( 'no', 'full' )}

{ezcss_require( 'video.css' )}
{ezscript_require( 'video.js' )}
<script>
    _V_.options.flash.swf = "{'flash/video-js.swf'|ezdesign( 'no' )}"
</script>

<section class="content-view-full">
    <article class="class-video">
        <div class="attribute-header">
            <h1>{$node.name|wash()}</h1>
        </div>

        <div class="attribute-video">
            <video id="video_{$node.contentobject_id}" class="video-js vjs-default-skin" controls preload="auto" width="770" height="318" poster="" data-setup="{ldelim}{rdelim}">
              <source src="{$video_path}" type="video/mp4" />
            </video>
        </div>

        <div class="attribute-download">
            <p><a class="btn btn-mini btn-warning pull-right" href="{$video_path|ezurl( 'no' )}">{'Download'|i18n( 'design/ezdemo/full/video' )}</a></p>
        </div>

        <div class="attribute-long">
            {attribute_view_gui attribute=$node.data_map.caption}
        </div>

        <div class="attribute-star-rating">
            {attribute_view_gui attribute=$node.data_map.star_rating}
        </div>

        <div class="attribute-socialize">
            {include uri='design:parts/social_buttons.tpl'}
        </div>
    </article>
</section>
{undef $video_path}
