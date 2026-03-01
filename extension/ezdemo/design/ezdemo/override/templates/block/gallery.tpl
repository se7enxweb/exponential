{def $valid_nodes = $block.valid_nodes
     $first = $valid_nodes.0
     $big_image_class = 'gallery'
     $render_image_once = false()}
<!-- BLOCK: START -->
<div class="block-type-gallery">

    <div class="block-gallery">
        <div class="gallery-viewer">
            {node_view_gui view='gallery_viewer' big_class=$big_image_class total_items=$valid_nodes|count content_node=$first}
            {foreach $valid_nodes as $gallery_item offset 1}
                {if eq( $gallery_item.class_identifier, 'video' )}
                    {node_view_gui view='gallery_viewer' is_hidden=true() big_class=$big_image_class total_items=$valid_nodes|count content_node=$gallery_item}
                {/if}
                {if and( eq( $gallery_item.class_identifier, 'image' ), $render_image_once|not() )}
                    {node_view_gui view='gallery_viewer' is_hidden=true() big_class=$big_image_class total_items=$valid_nodes|count content_node=$gallery_item}
                    {set $render_image_once = true()}
                {/if}
            {/foreach}
        </div>
        <div class="gallery-navigator">
            <a href="#" class="navig prev" style="opacity:0;"><span class="hide">&lt;</span></a>
            <a href="#" class="navig next"><span class="hide">&gt;</span></a>

            <img src={'fg-selected.png'|ezimage} alt="Selected indicator" class="cursor" />
            <ul class="images">
            {foreach $valid_nodes as $gallery_item}
                <li>{node_view_gui view='gallery_item' thumb_class='gallerythumbnail' big_class=$big_image_class content_node=$gallery_item}</li>
            {/foreach}
            </ul>
        </div>

    </div>
    {run-once}
    <script type="text/javascript">
    {literal}

    YUI(YUI3_config).use('ezgallery', 'event', function (Y) {
        Y.on('domready', function () {
            Y.all('.block-type-gallery').each(function () {
                var g = new Y.eZ.Gallery({
                    title: 'h3 a',
                    caption: 'figcaption div',
                    autoFixSizes: false,
                    autoScrollOnSelect: false,
                    navigator: {
                        gallery: this.one('.block-gallery')
                    },
                    initFunc: function () {
                        var imgs = this.navigator.getImages();

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
    });
    
    {/literal}
    </script>
    {/run-once}
{undef $valid_nodes $first $big_image_class $render_image_once}
</div>
<!-- BLOCK: END -->
