{def $sa_menu_siteaccesses = ezini('SiteAccessSettings','RelatedSiteAccessList')|unique}

<!-- Site access for override popup menu -->

<div class="popupmenu" id="OverrideSiteAccess" data-menu-depth="1">
    <div class="popupmenuheader"><h3 class="override-site-access-menu-header">{"Choose siteaccess"|i18n("design/admin/popupmenu")}</h3>
        <div class="break"></div>
    </div>

    {section var=siteAccess loop=$:sa_menu_siteaccesses}
        {let sa_menu_link=concat("visual/templatecreate/node/view/full.tpl/(siteAccess)/",$siteAccess)|ezurl}
            <a id="menu-override-siteAccess-{$siteAccess}" data-menu-redirect={$sa_menu_link}>{$siteAccess}</a>
        {/let}
    {/section}
</div>



<!-- Site access for override by class popup menu -->
<div class="popupmenu" id="OverrideByClassSiteAccess" data-menu-depth="1">
    <div class="popupmenuheader"><h3 class="override-site-access-menu-header">{"Choose siteaccess"|i18n("design/admin/popupmenu")}</h3>
        <div class="break"></div>
    </div>

    {section var=siteAccess loop=$:sa_menu_siteaccesses}
        {let sa_menu_link=concat('visual/templatecreate/node/view/full.tpl/(siteAccess)/', $siteAccess, '/(classID)/%classID%')|ezurl}
            <a id="menu-override-by-class-siteAccess-{$siteAccess}" data-menu-redirect={$sa_menu_link}>{$siteAccess}</a>
        {/let}
    {/section}

</div>



<!-- Site access for override by node popup menu -->
<div class="popupmenu" id="OverrideByNodeSiteAccess" data-menu-depth="1">
    <div class="popupmenuheader"><h3 class="override-site-access-menu-header">{"Choose siteaccess"|i18n("design/admin/popupmenu")}</h3>
        <div class="break"></div>
    </div>

    {section var=siteAccess loop=$:sa_menu_siteaccesses}
        {let sa_menu_link=concat('visual/templatecreate/node/view/full.tpl/(siteAccess)/', $siteAccess, '/(nodeID)/%nodeID%')|ezurl}
            <a id="menu-override-by-node-siteAccess-{$siteAccess}" data-menu-redirect={$sa_menu_link}>{$siteAccess}</a>
        {/let}
    {/section}
</div>{undef $sa_menu_siteaccesses}
