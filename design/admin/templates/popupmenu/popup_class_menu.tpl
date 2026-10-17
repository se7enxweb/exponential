<!-- Class popup menu -->

<div class="popupmenu" id="ClassMenu" data-menu-depth="0" data-menu-header="class-header">
    <div class="popupmenuheader"><h3 id="class-header">XXX</h3>
{*        <div class="window-close" onclick="ezpopmenu_hide( 'ClassMenu' )"><p>X</p></div> *}
        <div class="break"></div>
    </div>
    <a id="class-view" data-menu-url={"/class/view/%classID%"|ezurl} href="#">{"View class"|i18n("design/admin/popupmenu")}</a>
{if $multilingual_site}
    <a id="class-edit-in" class="more" href="#" data-menu-submenu="EditClassSubmenu">{'Edit class in'|i18n( 'design/admin/popupmenu' )}</a>
{else}
    <a id="class-edit" data-menu-url={"/class/edit/%classID%"|ezurl} href="#">{"Edit class"|i18n("design/admin/popupmenu")}</a>
{/if}

    <hr />
    <a id="view-cache-delete" data-menu-url={"%currentURL%"|ezurl} href="#" data-menu-form="menu-form-view-cache-delete">{"Delete view cache"|i18n("design/admin/popupmenu")}</a>
<!--
    <a id="template-cache-delete" href="#">{"Delete template cache"|i18n("design/admin/popupmenu")}</a>
-->
    <a id="recursive-view-cache-delete" data-menu-url={"%currentURL%"|ezurl} href="#" data-menu-form="menu-form-recursive-view-cache-delete">{"Delete view cache from here"|i18n("design/admin/popupmenu")}</a>
    <hr />
    <a id="class-createnodefeed" data-menu-url={"/content/view/full/%nodeID%"|ezurl} data-menu-disabled-class="menu-item-disabled" data-menu-disabled-for="class-createnodefeed" href="#" data-menu-form="menu-form-createnodefeed">{"Create RSS/ATOM feed"|i18n("design/admin/popupmenu")}</a>
    <a id="class-removenodefeed" data-menu-url={"/content/view/full/%nodeID%"|ezurl} data-menu-disabled-class="menu-item-disabled" data-menu-disabled-for="class-removenodefeed" href="#" data-menu-form="menu-form-removenodefeed">{"Remove RSS/ATOM feed"|i18n("design/admin/popupmenu")}</a>
{if fetch( 'user', 'can_open', hash( 'uri', 'visual/templatecreate' ) )}
    <hr />
    <a id="override-view" class="more" href="#" data-menu-submenu="OverrideSiteAccess" data-menu-hide="OverrideByClassSiteAccess OverrideByNodeSiteAccess">{"Template overrides"|i18n("design/admin/popupmenu")}</a>
    <a id="override-by-class-view" class="more" href="#" data-menu-submenu="OverrideByClassSiteAccess" data-menu-hide="OverrideSiteAccess OverrideByNodeSiteAccess">{"New class override"|i18n("design/admin/popupmenu")}</a>
    <a id="override-by-node-view" class="more" href="#" data-menu-submenu="OverrideByNodeSiteAccess" data-menu-hide="OverrideSiteAccess OverrideByClassSiteAccess">{"New node override"|i18n("design/admin/popupmenu")}</a>
{/if}
    <hr />
    <a id="class-history" data-menu-url={"content/history/%objectID%"|ezurl} data-menu-disabled-class="menu-item-disabled" data-menu-disabled-for="class-history" href="#">{'Manage versions'|i18n( 'design/admin/popupmenu' )}</a>
    <a id="url-alias" data-menu-url={"content/urlalias/%nodeID%"|ezurl} data-menu-disabled-class="menu-item-disabled" data-menu-disabled-for="url-alias" href="#">{'Manage URL aliases'|i18n( 'design/admin/popupmenu' )}</a>
    
    {* Include additional class menu items  based on .ini settings *}
    {foreach ezini( 'AdditionalMenuSettings', 'ClassMenuTemplateArray', 'admininterface.ini' ) as $template}
        {include uri=concat('design:', $template )}
    {/foreach}
</div>

{* Add node feed. *}
<form id="menu-form-createnodefeed" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="NodeID" value="%nodeID%" />
  <input type="hidden" name="CreateNodeFeed" value="x" />
</form>

{* Remove node feed *}
<form id="menu-form-removenodefeed" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="NodeID" value="%nodeID%" />
  <input type="hidden" name="RemoveNodeFeed" value="x" />
</form>

{* Delete view cache for node. *}
<form id="menu-form-view-cache-delete" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="NodeID" value="%nodeID%" />
  <input type="hidden" name="ObjectID" value="%objectID%" />
  <input type="hidden" name="CurrentURL" value="%currentURL%" />
  <input type="hidden" name="ClearViewCacheButton" value="x" />
</form>

{* Delete view cache for subtree. *}
<form id="menu-form-recursive-view-cache-delete" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="NodeID" value="%nodeID%" />
  <input type="hidden" name="ObjectID" value="%objectID%" />
  <input type="hidden" name="CurrentURL" value="%currentURL%" />
  <input type="hidden" name="ClearViewCacheSubtreeButton" value="x" />
</form>