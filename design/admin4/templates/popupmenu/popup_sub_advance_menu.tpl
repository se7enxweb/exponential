<!-- Advanced menu -->
<script type="text/javascript">
menuArray['Advanced'] = {ldelim} 'depth': 1 {rdelim};
menuArray['Advanced']['elements'] = {ldelim}{rdelim};
menuArray['Advanced']['elements']['menu-list'] = {ldelim} 'url': {"content/view/sitemap/%nodeID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['reverse-related'] = {ldelim} 'url': {"content/reverserelatedlist/%nodeID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['menu-history'] = {ldelim} 'url': {"content/history/%objectID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['menu-url-alias'] = {ldelim} 'url': {"content/urlalias/%nodeID%"|ezurl} {rdelim};
</script>

{* Export: the item or its subtree as a content package (the package wizard, for a user who may use it and download
   the package: fetch( 'package', 'content_export_access' )) and the entries of extensions (admininterface.ini
   [AdditionalMenuSettings] ExportMenuTemplateArray, e.g. xrowextract's CSV). Every entry opens its tool's form
   filled in for the item; the export is started there. The submenu is shown only when it has an entry.
   Guide: doc/bc/6.0/content-export-menu.md *}
{set-block variable=$export_package_items}
{if fetch( 'package', 'content_export_access' )}
<script type="text/javascript">
menuArray['ExportMenu']['elements']['menu-export-package-node'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%"|ezurl} {rdelim};
menuArray['ExportMenu']['elements']['menu-export-package-subtree'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1"|ezurl} {rdelim};
menuArray['ExportMenu']['elements']['menu-export-package-content'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1/(classes)/0"|ezurl} {rdelim};
menuArray['ExportMenu']['elements']['menu-export-package-versions'] = {ldelim} 'url': {"package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1/(versions)/all"|ezurl} {rdelim};
</script>
    <a id="menu-export-package-node" href="#" onmouseover="ezpopmenu_mouseOver( 'ExportMenu' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'This item as package…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-export-package-subtree" href="#" onmouseover="ezpopmenu_mouseOver( 'ExportMenu' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'This item and its subtree as package…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-export-package-content" href="#" onmouseover="ezpopmenu_mouseOver( 'ExportMenu' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'Subtree as package, without class definitions…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-export-package-versions" href="#" onmouseover="ezpopmenu_mouseOver( 'ExportMenu' )"><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span> {'Subtree as package, with all versions…'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-export-package-wizard" href={'package/create'|ezurl} onmouseover="ezpopmenu_mouseOver( 'ExportMenu' )"><span class="a4-icon a4-bi-magic" aria-hidden="true"></span> {'Open the package wizard…'|i18n( 'design/admin/popupmenu' )}</a>
{/if}
{/set-block}
{set-block variable=$export_extension_items}
{foreach ezini( 'AdditionalMenuSettings', 'ExportMenuTemplateArray', 'admininterface.ini' ) as $template}
    {include uri=concat( 'design:', $template )}
{/foreach}
{/set-block}
{def $has_export_package = $export_package_items|trim|ne( '' )
     $has_export_extension = $export_extension_items|trim|ne( '' )}

<div class="popupmenu" id="Advanced">
    <a id="menu-swap" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-swap' ); return false;">{'Swap with another node'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-hide" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-hide' ); return false;">{'Hide / unhide'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-list" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Sitemap for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="reverse-related" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Reverse related for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-history" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Manage versions'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-url-alias" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Manage URL aliases'|i18n( 'design/admin/popupmenu' )}</a>
{if or( $has_export_package, $has_export_extension )}
    <hr />
    <a id="menu-export" class="more" href="#" onmouseover="ezpopmenu_showSubLevel( event, 'ExportMenu', 'menu-export' ); return false;"><span class="a4-icon a4-bi-download" aria-hidden="true"></span> {'Export'|i18n( 'design/admin/popupmenu' )}</a>
{/if}

    {* Include additional advanced menu items  based on .ini settings *}
    {foreach ezini( 'AdditionalMenuSettings', 'AdvancedMenuTemplateArray', 'admininterface.ini' ) as $template}
        {include uri=concat('design:', $template )}
    {/foreach}
</div>

{if or( $has_export_package, $has_export_extension )}
<script type="text/javascript">
menuArray['ExportMenu'] = {ldelim} 'depth': 2 {rdelim};
menuArray['ExportMenu']['elements'] = {ldelim}{rdelim};
</script>
<div class="popupmenu" id="ExportMenu">
{$export_package_items}
{if and( $has_export_package, $has_export_extension )}
    <hr />
{/if}
{$export_extension_items}
</div>

{* The submenus of extension entries (a level below Export), after it so they are drawn above it *}
{foreach ezini( 'AdditionalMenuSettings', 'ExportSubMenuTemplateArray', 'admininterface.ini' ) as $template}
    {include uri=concat( 'design:', $template )}
{/foreach}
{/if}
{undef $has_export_package $has_export_extension}

{* Hide / unhide: a POST, as it changes the site *}
<form id="menu-form-hide" method="post" action={"/content/hide/%nodeID%"|ezurl}>
</form>

{* Swap node *}
<form id="menu-form-swap" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="TopLevelNode" value="%nodeID%" />
  <input type="hidden" name="ContentNodeID" value="%nodeID%" />
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="SwapNodeButton" value="x" />
</form>
