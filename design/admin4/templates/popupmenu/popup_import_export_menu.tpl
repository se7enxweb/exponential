{* The menu of the node view's Import/Export button (node/view/import_export_button.tpl): the export entries of the
   context menus' Advanced > Export (popupmenu/popup_export_items.tpl, one definition for both) and the import and
   job entries of extensions (xrowextract: Import into this item, Jobs, History, Schedules). Each entry opens its
   tool's form filled in for the item. A top-level menu, opened by the button with %nodeID% and %canImport% (1 for a
   container below which the user may create content). Nothing when the user has no entry.
   Guide: doc/eng-us/bc/6.0/content-export-menu.md *}
{set-block variable=$import_export_items}
{include uri='design:popupmenu/popup_export_items.tpl' menu_id='ImportExportMenu' item_prefix='node-export' sub_menu_depth=1 with_import=true()}
{/set-block}
{if $import_export_items|trim|ne( '' )}
<div class="popupmenu" id="ImportExportMenu" data-menu-depth="0" data-menu-header="node-export-header">
    <div class="popupmenuheader"><h3 id="node-export-header">{'Import/Export'|i18n( 'design/admin/popupmenu' )}</h3>
        <div class="break"></div>
    </div>
{$import_export_items}
</div>
{foreach ezini( 'AdditionalMenuSettings', 'ExportSubMenuTemplateArray', 'admininterface.ini' ) as $template}
    {include uri=concat( 'design:', $template ) menu_id='ImportExportMenu' item_prefix='node-export' sub_menu_depth=1}
{/foreach}
{/if}
{undef $import_export_items}
