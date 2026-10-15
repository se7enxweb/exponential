{* The Import/Export button of the node view's actions (node/view/full.tpl of design/admin, node/view/a4_full_body.tpl
   of design/admin4; admin4l finds it through admin4; exp_adminui's "..." menu), after Upload: a menu button that
   opens the Import/Export menu (popupmenu/popup_import_export_menu.tpl) for this node, with the entries of
   popupmenu/popup_export_items.tpl, the same definition as the context menus' Advanced > Export. Shown only when
   that definition has an entry for the user here; published objects only, so a draft or the edit pages never show
   it. Import is offered only for a container below which the user may create content (%canImport%); its tool checks
   that again. Styled by .a4-btn of components.css, the icon from Bootstrap Icons; the keyboard as in every context
   menu (Enter or Space open it, the arrows move, Escape closes it). design/admin4 has an identical copy.
   Optional: $button_class (default a4-btn; exp_adminui's "..." menu gives none) and $in_list (true: an <li> around it). *}
{if and( is_set( $node.node_id ), eq( $node.object.status, 1 ) )}
{set-block variable=$node_export_probe}
{include uri='design:popupmenu/popup_export_items.tpl' menu_id='ImportExportMenu' item_prefix='node-export' sub_menu_depth=1 with_import=true()}
{/set-block}
{if $node_export_probe|trim|ne( '' )}
{def $node_can_import = cond( and( $node.object.content_class.is_container, $node.can_create ), 1, 0 )}
    {if first_set( $in_list, false() )}<li>{/if}<a class="{first_set( $button_class, 'a4-btn' )} a4-node-import-export" id="node-import-export-{$node.node_id}" href="#" role="button" aria-haspopup="menu" aria-expanded="false" title="{'Export this item or import into it.'|i18n( 'design/admin/node/view/full' )|wash}" onclick="ezpopmenu_showTopLevel( event, 'ImportExportMenu', ez_createAArray( new Array( '%nodeID%', {$node.node_id}, '%objectID%', {$node.contentobject_id}, '%canImport%', {$node_can_import} ) ), '{$node.name|wash( javascript )|wash}' ); return false;"><span class="a4-icon a4-bi-arrow-down-up" aria-hidden="true"></span> {'Import/Export'|i18n( 'design/admin/node/view/full' )}</a>{if first_set( $in_list, false() )}</li>{/if}
{undef $node_can_import}
{/if}
{undef $node_export_probe}
{/if}
