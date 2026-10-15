{* The entries of the export menus, one definition for both: Advanced > Export of the context menus
   (popupmenu/popup_sub_advance_menu.tpl) and the node view's Import/Export menu (popupmenu/popup_import_export_menu.tpl,
   node/view/import_export_button.tpl). Each entry opens its tool's form filled in for the item (%nodeID%); nothing is
   exported or imported from a menu. Guide: doc/bc/6.0/content-export-menu.md
   Parameters: $menu_id (the menu the items are in, whose menuArray entry exists before), $item_prefix (of the item
   ids), $sub_menu_depth (the depth of the items' submenus), $with_import (also the import and job entries).
   - the package entries: fetch( 'package', 'content_export_access' ) (package/create with the content object wizard,
     package/export);
   - extensions: admininterface.ini [AdditionalMenuSettings] ExportMenuTemplateArray[] (export entries, both menus)
     and ImportExportMenuTemplateArray[] (import and job entries, the Import/Export menu only), given the same
     parameters; each checks its own policies. Nothing is output when there is no entry for the user. *}
{set-block variable=$export_package_items}
{if fetch( 'package', 'content_export_access' )}
<script type="text/javascript">
menuArray['{$menu_id}']['elements']['{$item_prefix}-package-node'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%"|ezurl} {rdelim};
menuArray['{$menu_id}']['elements']['{$item_prefix}-package-subtree'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1"|ezurl} {rdelim};
menuArray['{$menu_id}']['elements']['{$item_prefix}-package-content'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1/(classes)/0"|ezurl} {rdelim};
menuArray['{$menu_id}']['elements']['{$item_prefix}-package-versions'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1/(versions)/all"|ezurl} {rdelim};
</script>
    <a id="{$item_prefix}-package-node" href="#" onmouseover="ezpopmenu_mouseOver( '{$menu_id}' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'This item as package…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-subtree" href="#" onmouseover="ezpopmenu_mouseOver( '{$menu_id}' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'This item and its subtree as package…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-content" href="#" onmouseover="ezpopmenu_mouseOver( '{$menu_id}' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'Subtree as package, without class definitions…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-versions" href="#" onmouseover="ezpopmenu_mouseOver( '{$menu_id}' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'Subtree as package, with all versions…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-wizard" href={'package/create'|ezurl} onmouseover="ezpopmenu_mouseOver( '{$menu_id}' )"><span class="a4-icon a4-bi-magic" aria-hidden="true"></span> {'Open the package wizard…'|i18n( 'design/admin/popupmenu' )}</a>
{/if}
{/set-block}
{set-block variable=$export_extension_items}
{foreach ezini( 'AdditionalMenuSettings', 'ExportMenuTemplateArray', 'admininterface.ini' ) as $template}
    {include uri=concat( 'design:', $template ) menu_id=$menu_id item_prefix=$item_prefix sub_menu_depth=$sub_menu_depth}
{/foreach}
{/set-block}
{set-block variable=$import_extension_items}
{if $with_import}
{foreach ezini( 'AdditionalMenuSettings', 'ImportExportMenuTemplateArray', 'admininterface.ini' ) as $template}
    {include uri=concat( 'design:', $template ) menu_id=$menu_id item_prefix=$item_prefix sub_menu_depth=$sub_menu_depth}
{/foreach}
{/if}
{/set-block}
{def $export_groups = array()}
{foreach array( $export_package_items, $export_extension_items, $import_extension_items ) as $export_group}
    {if $export_group|trim|ne( '' )}{set $export_groups = $export_groups|append( $export_group )}{/if}
{/foreach}
{foreach $export_groups as $export_index => $export_group}
{if $export_index|gt( 0 )}
    <hr />
{/if}
{$export_group}
{/foreach}
{undef $export_groups}
{undef $export_package_items $export_extension_items $import_extension_items}
