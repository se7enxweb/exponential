<!-- Advanced menu -->
{* The content checks (doc/en/us/features/6.0/content-checks.md) for the subtree, only for a user with their policies *}
{def $contentcheck_unreferenced = fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'unreferenced' ) )
     $contentcheck_broken = fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'brokenrelations' ) )}

{* Export: the item or its subtree as a content package (the package wizard) and the export entries of extensions
   (xrowextract's CSV), from popupmenu/popup_export_items.tpl, which the node view's Import/Export menu shares. Every
   entry opens its tool's form filled in for the item; the export is started there. The submenu is shown only when
   it has an entry for the user. Guide: doc/en/us/bc/6.0/content-export-menu.md *}
{set-block variable=$export_menu_items}
{include uri='design:popupmenu/popup_export_items.tpl' menu_id='ExportMenu' item_prefix='menu-export' sub_menu_depth=3 with_import=false()}
{/set-block}
{def $has_export_menu = $export_menu_items|trim|ne( '' )}

<div class="popupmenu" id="Advanced" data-menu-depth="1">
    <a id="menu-swap" href="#" data-menu-form="menu-form-swap">{'Swap with another node'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-hide" href="#" data-menu-form="menu-form-hide">{'Hide / unhide'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-list" data-menu-url={"content/view/sitemap/%nodeID%"|ezurl} href="#">{'Sitemap for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="reverse-related" data-menu-url={"content/reverserelatedlist/%nodeID%"|ezurl} href="#">{'Reverse related for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    {if $contentcheck_unreferenced}<a id="menu-unreferenced" data-menu-url={"contentcheck/unreferenced/%nodeID%"|ezurl} href="#">{'Unreferenced objects in subtree'|i18n( 'design/admin/popupmenu' )}</a>{/if}
    {if $contentcheck_broken}<a id="menu-brokenrelations" data-menu-url={"contentcheck/brokenrelations/%nodeID%"|ezurl} href="#">{'Broken relations in subtree'|i18n( 'design/admin/popupmenu' )}</a>{/if}
    <hr />
    <a id="menu-history" data-menu-url={"content/history/%objectID%"|ezurl} href="#">{'Manage versions'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-url-alias" data-menu-url={"content/urlalias/%nodeID%"|ezurl} href="#">{'Manage URL aliases'|i18n( 'design/admin/popupmenu' )}</a>
{if $has_export_menu}
    <hr />
    <a id="menu-export" class="more" href="#" data-menu-submenu="ExportMenu"><span class="a4-icon a4-bi-download" aria-hidden="true"></span> {'Export'|i18n( 'design/admin/popupmenu' )}</a>
{/if}

    {* Deactivate and move (user/deactivate) for user accounts: when site.ini [UserSettings] DeactivateAndMove=enabled
       and you have user/deactivate. The menu does not know the class of the node; the confirmation page that
       follows says when the item is no user account or you may not, and nothing is changed before you confirm. *}
    {if and( ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' ), fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'deactivate' ) ) )}
    <hr />
    <a id="menu-deactivate-move" href="#" data-menu-form="menu-form-deactivate-move">{'Deactivate and move (user accounts)'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-reactivate-move" href="#" data-menu-form="menu-form-reactivate-move">{'Reactivate and move back (user accounts)'|i18n( 'design/admin/popupmenu' )}</a>
    {/if}

    {* Include additional advanced menu items  based on .ini settings *}
    {foreach ezini( 'AdditionalMenuSettings', 'AdvancedMenuTemplateArray', 'admininterface.ini' ) as $template}
        {include uri=concat('design:', $template )}
    {/foreach}
</div>

{if $has_export_menu}
<div class="popupmenu" id="ExportMenu" data-menu-depth="2">
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
