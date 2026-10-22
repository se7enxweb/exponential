<div class="content-view-thumbnail">
{def $can_create_classes = $node.classes_js_array $disable_createHere_menu = cond( $can_create_classes|eq("''"),"'child-menu-create-here'", '-1' ) }
<a href={$node.url_alias|ezurl} aria-haspopup="menu" aria-expanded="false" data-menu-open="SubitemsContextMenu" data-menu-values="{json_encode( hash( '%nodeID%', $:node.node_id, '%objectID%', $:node.object.id ) )|wash}" data-menu-languages="{$:node.object.language_js_array|wash}" data-menu-classes="{$can_create_classes|wash}" data-menu-title="{$:node.object.name|shorten(18)|wash}" data-menu-disable="{$:node.node_id}" data-menu-disable-item="{$disable_createHere_menu|trim( "'" )|wash}">{$node.class_identifier|class_icon( normal, '[%classname] Click on the icon to display a context-sensitive menu.'|i18n( 'design/admin/node/view/thumbnail',, hash( '%classname', $node.class_name ) ) )}</a>
{undef $can_create_classes $disable_createHere_menu}
</div>
