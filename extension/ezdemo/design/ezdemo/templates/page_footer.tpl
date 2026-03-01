<!-- Footer area: START -->
{def $footer_node = fetch( 'content', 'node', hash( 'node_id', ezini( 'FooterSettings', 'NodeID', 'content.ini' ) ) )}
<footer>
    <div class="claim-wrapper">
        <div class="container">
            <div class="nav-collapse">
                Powered by <a href="http://ez.no/ezpublish" title="eZ Publish&#8482; CMS Open Source Web Content Management">eZ Publish&#8482; CMS Open Source Web Content Management</a>.
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="span4">
                {include uri='design:footer/address.tpl' node=$footer_node}
            </div>
            <div class="span4 nav-collapse">
                {include uri='design:footer/latest_news.tpl'}
            </div>
            <div class="span4 nav-collapse">
                {include uri='design:footer/links.tpl' node=$footer_node}
            </div>
        </div>
    </div>
</footer>
<!-- Footer area: END -->
{undef $footer_node}
