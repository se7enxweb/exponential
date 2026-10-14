{* One siteaccess on the classic menu settings page (visual/menuconfig): its name, whether its pages read the settings
   and why, its design, page layout and current menu, and the link that opens it on the page.
   Parameters: sa (an entry of $menu_siteaccesses). The same file is in design/admin and design/admin4. *}
<li class="a4-visual-sa{if $sa.selected} is-selected{/if}">
    <div class="a4-visual-sa-head">
        <h3 class="a4-h3">{$sa.siteaccess|wash}</h3>
        {switch match=$sa.status}
        {case match='classic'}<span class="a4-badge is-ok">{'Uses these settings'|i18n( 'design/admin/visual/menuconfig' )}</span>{/case}
        {case match='mixed'}<span class="a4-badge is-ok">{'Uses these settings, with Layouts'|i18n( 'design/admin/visual/menuconfig' )}</span>{/case}
        {case match='layouts'}<span class="a4-badge is-info">{'Not used: renders through Layouts'|i18n( 'design/admin/visual/menuconfig' )}</span>{/case}
        {case match='admin'}<span class="a4-badge">{'Administration siteaccess'|i18n( 'design/admin/visual/menuconfig' )}</span>{/case}
        {case match='other'}<span class="a4-badge">{'Not used by its design'|i18n( 'design/admin/visual/menuconfig' )}</span>{/case}
        {case}<span class="a4-badge is-warn">{'No page layout found'|i18n( 'design/admin/visual/menuconfig' )}</span>{/case}
        {/switch}
    </div>
    <p>
    {switch match=$sa.status}
    {case match='classic'}{'Its page layout draws the top and left menus from these settings.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case match='mixed'}{'Its page layout draws menus from these settings and also renders Exponential Layouts.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case match='layouts'}{'Its pages are built with Exponential Layouts; menus are blocks in the layout editor.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case match='admin'}{'The administration interface. Its tabs and side menus are set elsewhere in menu.ini.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case match='other'}{'Its page layout does not read these settings.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case}{'None of its designs has a pagelayout.tpl.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {/switch}
    </p>
    <p>{'Design'|i18n( 'design/admin/visual/menuconfig' )}: <code>{$sa.site_design|wash}</code>{if $sa.pagelayout} &middot; <code>{$sa.pagelayout|wash}</code>{/if}</p>
    <p>{'Current setting'|i18n( 'design/admin/visual/menuconfig' )}: <strong>{if $sa.current_title}{$sa.current_title|wash}{else}{'none'|i18n( 'design/admin/visual/menuconfig' )}{/if}</strong>{if $sa.overridden} &middot; {'fixed by settings/override'|i18n( 'design/admin/visual/menuconfig' )}{/if}</p>
    <div class="a4-actions">
        {if $sa.selected}
        <span class="a4-badge is-info">{'Shown below'|i18n( 'design/admin/visual/menuconfig' )}</span>
        {else}
        <a class="a4-btn a4-btn-small" href={concat( '/visual/menuconfig/(siteaccess)/', $sa.siteaccess )|ezurl}>{'Show its settings'|i18n( 'design/admin/visual/menuconfig' )}<span class="a4-sr-only"> ({$sa.siteaccess|wash})</span></a>
        {/if}
    </div>
</li>
