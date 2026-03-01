{def $latest_news = fetch( 'content', 'tree', hash( 'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
                                                    'limit', '7',
                                                    'class_filter_type', 'include',
                                                    'class_filter_array', array( 'article' ),
                                                    'attribute_filter', array( array( 'name', '!=', ezini( 'FooterSettings', 'ContentObjectName', 'content.ini' ) ) ),
                                                    'main_node_only', true(),
                                                    'sort_by', array( 'published', false() ) ) )}
<h3>{'Latest News'|i18n('design/ezdemo/footer/latest_news')}</h3>
<ul>
    {foreach $latest_news as $news}
        <li><a href="{$news.url_alias|ezurl( 'no' )}">{$news.name|wash()}</a></li>
    {/foreach}
</ul>
{undef $latest_news}
