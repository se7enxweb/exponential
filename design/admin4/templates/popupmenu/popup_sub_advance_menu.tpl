<!-- Advanced menu -->
<script type="text/javascript">
menuArray['Advanced'] = {ldelim} 'depth': 1 {rdelim};
menuArray['Advanced']['elements'] = {ldelim}{rdelim};
menuArray['Advanced']['elements']['menu-list'] = {ldelim} 'url': {"content/view/sitemap/%nodeID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['reverse-related'] = {ldelim} 'url': {"content/reverserelatedlist/%nodeID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['menu-history'] = {ldelim} 'url': {"content/history/%objectID%"|ezurl} {rdelim};
menuArray['Advanced']['elements']['menu-url-alias'] = {ldelim} 'url': {"content/urlalias/%nodeID%"|ezurl} {rdelim};
</script>

<div class="popupmenu" id="Advanced">
    <a id="menu-swap" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-swap' ); return false;">{'Swap with another node'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-hide" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )" onclick="ezpopmenu_submitForm( 'menu-form-hide' ); return false;">{'Hide / unhide'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-list" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Sitemap for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="reverse-related" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Reverse related for subtree'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-history" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Manage versions'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="menu-url-alias" href="#" onmouseover="ezpopmenu_mouseOver( 'Advanced' )">{'Manage URL aliases'|i18n( 'design/admin/popupmenu' )}</a>

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