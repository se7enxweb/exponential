<!-- Advanced menu -->
{* The content checks (doc/features/6.0/content-checks.md) for the subtree, only for a user with their policies *}
{def $contentcheck_unreferenced = fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'unreferenced' ) )
     $contentcheck_broken = fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'brokenrelations' ) )}
<script type="text/javascript">
menuArray['Advanced'] = {ldelim} 'depth': 1 {rdelim};
menuArray['Advanced']['elements'] = {ldelim}{rdelim};
menuArray['Advanced']['elements']['menu-list'] = {ldelim} 'url': {"content/view/sitemap/%nodeID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['reverse-related'] = {ldelim} 'url': {"content/reverserelatedlist/%nodeID%"|ezurl} {rdelim};
{if $contentcheck_unreferenced}
menuArray['Advanced']['elements']['menu-unreferenced'] = {ldelim} 'url': {"contentcheck/unreferenced/%nodeID%"|ezurl} {rdelim};
{/if}
{if $contentcheck_broken}
menuArray['Advanced']['elements']['menu-brokenrelations'] = {ldelim} 'url': {"contentcheck/brokenrelations/%nodeID%"|ezurl} {rdelim};
{/if}
menuArray['Advanced']['elements']['menu-history'] = {ldelim} 'url': {"content/history/%objectID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['menu-url-alias'] = {ldelim} 'url': {"content/urlalias/%nodeID%"|ezurl} {rdelim};
</script>

{* Export: the item or its subtree as a content package (the package wizard) and the export entries of extensions
   (xrowextract's CSV), from popupmenu/popup_export_items.tpl, which the node view's Import/Export menu shares. Every
   entry opens its tool's form filled in for the item; the export is started there. The submenu is shown only when
   it has an entry for the user. Guide: doc/bc/6.0/content-export-menu.md *}
{set-block variable=$export_menu_items}
{include uri='design:popupmenu/popup_export_items.tpl' menu_id='ExportMenu' item_prefix='menu-export' sub_menu_depth=3 with_import=false()}
{/set-block}
{def $has_export_menu = $export_menu_items|trim|ne( '' )}

<div class="popupmenu" id="Advanced">
    <a id="menu-swap" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-swap' ); return false;">{'Swap with another node'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-hide" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-hide' ); return false;">{'Hide / unhide'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-list" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Sitemap for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="reverse-related" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Reverse related for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    {if $contentcheck_unreferenced}<a id="menu-unreferenced" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Unreferenced objects in subtree'|i18n( 'design/admin/popupmenu' )}</a>{/if}
    {if $contentcheck_broken}<a id="menu-brokenrelations" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Broken relations in subtree'|i18n( 'design/admin/popupmenu' )}</a>{/if}
    <hr />
    <a id="menu-history" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Manage versions'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-url-alias" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Manage URL aliases'|i18n( 'design/admin/popupmenu' )}</a>
{if $has_export_menu}
    <hr />
    <a id="menu-export" class="more" href="#" onmouseover="ezpopmenu_showSubLevel( event, 'ExportMenu', 'menu-export' ); return false;"><span class="a4-icon a4-bi-download" aria-hidden="true"></span> {'Export'|i18n( 'design/admin/popupmenu' )}</a>
{/if}

    {* Deactivate and move (user/deactivate) for user accounts: when site.ini [UserSettings] DeactivateAndMove=enabled
       and you have user/deactivate. The menu does not know the class of the node; the confirmation page that
       follows says when the item is no user account or you may not, and nothing is changed before you confirm. *}
    {if and( ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' ), fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'deactivate' ) ) )}
    <hr />
    <a id="menu-deactivate-move" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-deactivate-move' ); return false;">{'Deactivate and move (user accounts)'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-reactivate-move" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-reactivate-move' ); return false;">{'Reactivate and move back (user accounts)'|i18n( 'design/admin/popupmenu' )}</a>
    {/if}

    {* Include additional advanced menu items  based on .ini settings *}
    {foreach ezini( 'AdditionalMenuSettings', 'AdvancedMenuTemplateArray', 'admininterface.ini' ) as $template}
        {include uri=concat('design:', $template )}
    {/foreach}
</div>

{if $has_export_menu}
<script type="text/javascript">
menuArray['ExportMenu'] = {ldelim} 'depth': 2 {rdelim};
menuArray['ExportMenu']['elements'] = {ldelim}{rdelim};
</script>
<div class="popupmenu" id="ExportMenu">
{$export_menu_items}
</div>

{* The submenus of extension entries (a level below Export), after it so they are drawn above it *}
{foreach ezini( 'AdditionalMenuSettings', 'ExportSubMenuTemplateArray', 'admininterface.ini' ) as $template}
    {include uri=concat( 'design:', $template ) menu_id='ExportMenu' item_prefix='menu-export' sub_menu_depth=3}
{/foreach}
{/if}
{undef $has_export_menu $export_menu_items}

{* Hide / unhide: a POST, as it changes the site *}
<form id="menu-form-hide" method="post" action={"/content/hide/%nodeID%"|ezurl}>
</form>

{* Deactivate and move / Reactivate and move back: a POST to the confirmation page *}
{if and( ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' ), fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'deactivate' ) ) )}
<form id="menu-form-deactivate-move" method="post" action={"/user/deactivate"|ezurl}>
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="DeactivateMoveButton" value="x" />
</form>
<form id="menu-form-reactivate-move" method="post" action={"/user/deactivate"|ezurl}>
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="ReactivateMoveButton" value="x" />
</form>
{/if}

{* Swap node *}
<form id="menu-form-swap" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="TopLevelNode" value="%nodeID%" />
  <input type="hidden" name="ContentNodeID" value="%nodeID%" />
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="SwapNodeButton" value="x" />
</form>
{undef $contentcheck_unreferenced $contentcheck_broken}
