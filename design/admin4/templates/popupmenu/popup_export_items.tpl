{* The entries of the export menus, one definition for both: Advanced > Export of the context menus
   (popupmenu/popup_sub_advance_menu.tpl) and the node view's Import/Export menu (popupmenu/popup_import_export_menu.tpl,
   node/view/import_export_button.tpl). Each entry opens its tool's form filled in for the item (%nodeID%); nothing is
   exported or imported from a menu. Guide: doc/eng-us/bc/6.0/content-export-menu.md
   Parameters: $menu_id (the menu the items are in, whose menuArray entry exists before), $item_prefix (of the item
   ids), $sub_menu_depth (the depth of the items' submenus), $with_import (also the import and job entries).
   - the package entries: fetch( 'package', 'content_export_access' ) (package/create with the content object wizard,
     package/export);
   - extensions: admininterface.ini [AdditionalMenuSettings] ExportMenuTemplateArray[] (export entries, both menus)
     and ImportExportMenuTemplateArray[] (import and job entries, the Import/Export menu only), given the same
     parameters; each checks its own policies. Nothing is output when there is no entry for the user. *}
{set-block variable=$export_package_items}
{if and( fetch( 'package', 'content_export_access' ), fetch( 'user', 'can_open', hash( 'uri', 'package/create' ) ) )}
    <a id="{$item_prefix}-package-node" data-menu-url={"package/create/(creator)/ezcontentobject/(node)/%nodeID%"|ezurl} href="#"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'This item as package…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-subtree" data-menu-url={"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1"|ezurl} href="#"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'This item and its subtree as package…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-content" data-menu-url={"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1/(classes)/0"|ezurl} href="#"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'Subtree as package, without class definitions…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-versions" data-menu-url={"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1/(versions)/all"|ezurl} href="#"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'Subtree as package, with all versions…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="{$item_prefix}-package-wizard" href={'package/create'|ezurl}><span class="a4-icon a4-bi-magic" aria-hidden="true"></span> {'Open the package wizard…'|i18n( 'design/admin/popupmenu' )}</a>
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
{* the transfer registry (doc/eng-us/features/6.0/transfer-registry.md): the "node" entries of every import and export
   tool the user may use, and in the Import/Export menu the "container" entries, shown only for a container below
   which the user may create (%canImport%); the menu's script fills in %nodeID% and %objectID% when it opens *}
{set-block variable=$transfer_registry_items}
{foreach transfer_actions( 'node', hash( '%nodeID%', '%nodeID%', '%objectID%', '%objectID%' ) ) as $transfer_index => $transfer_item}
    <a id="{$item_prefix}-transfer-node-{$transfer_index}" data-menu-url={$transfer_item.url|ezurl} href="#"><span class="a4-icon {if eq( $transfer_item.direction, 'import' )}a4-bi-upload{else}a4-bi-download{/if}" aria-hidden="true"></span> {$transfer_item.label|wash}</a>
{/foreach}
{if $with_import}
{foreach transfer_actions( 'container', hash( '%nodeID%', '%nodeID%', '%objectID%', '%objectID%' ) ) as $transfer_index => $transfer_item}
    <a id="{$item_prefix}-transfer-container-{$transfer_index}" data-menu-url={$transfer_item.url|ezurl} data-menu-shown-if="%canImport%" href="#"><span class="a4-icon {if eq( $transfer_item.direction, 'import' )}a4-bi-upload{else}a4-bi-download{/if}" aria-hidden="true"></span> {$transfer_item.label|wash}</a>
{/foreach}
{/if}
{/set-block}
{def $export_groups = array()}
{foreach array( $export_package_items, $export_extension_items, $import_extension_items, $transfer_registry_items ) as $export_group}
    {if $export_group|trim|ne( '' )}{set $export_groups = $export_groups|append( $export_group )}{/if}
{/foreach}
{* with any entry: the page that answers "Which import or export tool do I need?" *}
{if and( $with_import, $export_groups|count )}
    {set $export_groups = $export_groups|append( concat( '<a id="', $item_prefix|wash, '-transfer-help" href=', 'exphelp/viewer/help/import-export-tools.md'|ezurl, '><span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span> ', 'Which import or export tool do I need?'|i18n( 'design/admin/transfer' )|wash, '</a>' ) )}
{/if}
{undef $transfer_registry_items}
{foreach $export_groups as $export_index => $export_group}
{if $export_index|gt( 0 )}
    <hr />
{/if}
{$export_group}
{/foreach}
{undef $export_groups}
{undef $export_package_items $export_extension_items $import_extension_items}
