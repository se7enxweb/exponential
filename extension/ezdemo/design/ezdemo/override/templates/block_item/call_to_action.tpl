<article class="clearfix">
    <h3>{$node.name|wash()}</h3>

    <a href="{$node.url_alias|ezurl( 'no' )}" title="{$node.name|wash()}" class="btn btn-warning pull-right">{if $node.data_map.action_button_label.has_content}{$node.data_map.action_button_label.data_text|wash( xhtml )}{else}{"Download!"|i18n("design/ezdemo/block_item/call_to_action")}{/if}</a>
</article>
