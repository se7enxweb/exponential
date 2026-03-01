{* Gallery - Embed view *}
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $object.main_node.node_id,
                                                 'class_filter_type', 'include',
                                                 'class_filter_array', array( 'image', 'video' ),
                                                 'sort_by', $object.main_node.sort_array ) )
     $children_count = $children|count
     $first = $children.0
     $big_image_class = 'galleryfull'
     $render_image_once = false()}
<div class="embed-gallery">
    <div class="gallery-viewer">
        {node_view_gui view='gallery_viewer' big_class=$big_image_class total_items=$children_count content_node=$first}
        {foreach $children as $gallery_item offset 1}
            {if eq( $gallery_item.class_identifier, 'video' )}
                {node_view_gui view='gallery_viewer' is_hidden=true() big_class=$big_image_class total_items=$children_count content_node=$gallery_item}
            {/if}
            {if and( eq( $gallery_item.class_identifier, 'image' ), $render_image_once|not() )}
                {node_view_gui view='gallery_viewer' is_hidden=true() big_class=$big_image_class total_items=$children_count content_node=$gallery_item}
                {set $render_image_once = true()}
            {/if}
        {/foreach}
    </div>
    <div class="gallery-navigator">
        <a href="#" class="navig prev" style="opacity:0;"><span class="hide">&lt;</span></a>
        <a href="#" class="navig next"><span class="hide">&gt;</span></a>

        <img src={'fg-selected.png'|ezimage} alt="Selected indicator" class="cursor" />
        <ul class="images">
        {foreach $children as $gallery_item}
            <li>{node_view_gui view='gallery_item' thumb_class='gallerythumbnail' big_class=$big_image_class content_node=$gallery_item}</li>
        {/foreach}
        </ul>
    </div>
</div>
<script type="text/javascript">
{literal}

YUI(YUI3_config).use('ezgallery', 'event', function (Y) {
    Y.on('domready', function () {
        var g = new Y.eZ.Gallery({
            title: 'h3 a',
            caption: 'figcaption div',
            autoFixSizes: false,
            navigator: {
                gallery: '.embed-gallery'
            },
            initFunc: function () {
                var imgs = this.navigator.getImages();

                // make the browser caches images
                setTimeout(function () {
                    imgs.each(function(elem) {
                        (new Image).src = elem.getAttribute('data-gallery-src');
                    });
                }, 0);
            },
            updateFunc: function (item) {
                var node = item.imageNode,
                    img = this.container.one('.gallery-viewer-image').one('> img');

                if ( node.getAttribute('data-gallery-item') == 'image' ) {
                    this.container.one('.visible').replaceClass('visible', 'hidden');
                    this.container.one('.gallery-viewer-image').replaceClass('hidden', 'visible');
                } else if ( node.getAttribute('data-gallery-item') == 'video' ) {
                    this.container.one('.gallery-viewer-image').replaceClass('visible', 'hidden');
                    this.container.one('#gallery-viewer-video-' + node.getAttribute('data-gallery-node-id')).replaceClass('hidden', 'visible');
                }

                if ( img ) {
                    img.setAttribute('src', node.getAttribute('data-gallery-src'));
                    img.setAttribute('height', node.getAttribute('data-gallery-height'));
                    img.setAttribute('width', node.getAttribute('data-gallery-width'));
                    img.setAttribute('alt', node.get('title'));
                }

                var t = this.container.one('.visible ' + this.conf.title),
                    cap = this.container.one('.visible ' + this.conf.caption),
                    c = this.container.one('.visible ' + this.conf.counter);

                t.setContent(node.get('title'));
                t.setAttribute('href', node.getAttribute('data-gallery-node-url'));
                c.setContent(item.index + 1);
                cap.setContent(node.one('figcaption').getContent());
            }
        });
    });
});

{/literal}
</script>
{undef $children $first $big_image_class $render_image_once}
