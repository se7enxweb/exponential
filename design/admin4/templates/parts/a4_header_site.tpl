{* The header's site link and site menu (page_topmenu.tpl, admin4l's explayouts/block/admin_theme_switch.tpl).

   The link: the front page of the site this administration belongs to (siteaccess_url(): FrontSiteAccess, else
   DefaultAccess), with the scheme, host, port and siteaccess path the settings and this request call for. The first
   of the header's tools, before light/dark, the user menu and logout, and a round icon button as they are; it opens
   in the same window and stays a link on edit pages.

   The menu (site.ini [AdminSiteAccessMenu], signed-in users only): the same globe as a disclosure button (aria-expanded,
   aria-controls), whose list opens the site first and then the other siteaccesses, grouped into sites and
   administrations, each with its name (SiteName), identifier, locale and address (the operator siteaccess_menu, worked out by
   ezpSiteAccessURL as the link is); the siteaccess in use is marked with aria-current. One button in the place of the
   link, so the header takes no more room. admin4.js shows the button and hides the link; without the script the
   link stays, so the site is one click away either way. The header's cache blocks are keyed by siteaccess_menu_key()
   (pagelayout.tpl): the list is the installation's, the addresses the request's. *}
{def $a4_site_label = 'Open the site'|i18n( 'design/admin/pagelayout' )
     $a4_site_menu = hash( 'enabled', false() )}
{if $current_user.is_logged_in}{set $a4_site_menu = siteaccess_menu()}{/if}
<div id="header-site" class="header-site">
    <a class="site-preview a4-header-btn" href="{siteaccess_url()|wash}" title="{$a4_site_label|wash}" aria-label="{$a4_site_label|wash}">
        <span class="a4-icon a4-bi-globe2" aria-hidden="true"></span>
    </a>
{if $a4_site_menu.enabled}
    {def $a4_site_menu_label = 'Open the site or another siteaccess'|i18n( 'design/admin/pagelayout' )}
    <div id="a4-site-menu-wrap" class="a4-site-menu-wrap" hidden>
        <button type="button" id="a4-site-toggle" class="a4-header-btn a4-site-toggle" aria-expanded="false" aria-controls="a4-site-menu" title="{$a4_site_menu_label|wash}">
            <span class="a4-icon a4-bi-globe2" aria-hidden="true"></span>
            <span class="a4-sr-only">{$a4_site_menu_label|wash}</span>
        </button>
        <div id="a4-site-menu" class="a4-site-menu" hidden>
            <a class="a4-site-menu-front" href="{$a4_site_menu.front.url|wash}">
                <span class="a4-site-menu-name">{$a4_site_label|wash}</span>
                <span class="a4-site-menu-meta"><span{if $a4_site_menu.front.lang} lang="{$a4_site_menu.front.lang|wash}"{/if}>{$a4_site_menu.front.name|wash}</span> · <span class="a4-site-menu-url" translate="no">{$a4_site_menu.front.display_url|wash}</span></span>
            </a>
            {foreach $a4_site_menu.groups as $a4_site_group}
            <p class="a4-site-menu-heading" id="a4-site-menu-{$a4_site_group.identifier}">{if eq( $a4_site_group.identifier, 'admin' )}{'Administration'|i18n( 'design/admin/pagelayout' )|wash}{else}{'Sites'|i18n( 'design/admin/pagelayout' )|wash}{/if}</p>
            <ul class="a4-site-menu-list" aria-labelledby="a4-site-menu-{$a4_site_group.identifier}">
                {foreach $a4_site_group.items as $a4_site_item}
                <li><a href="{$a4_site_item.url|wash}"{if $a4_site_item.current} aria-current="true"{/if}>
                    <span class="a4-site-menu-name"{if $a4_site_item.lang} lang="{$a4_site_item.lang|wash}"{/if}>{$a4_site_item.name|wash}</span>
                    <span class="a4-site-menu-meta"><span translate="no">{$a4_site_item.identifier|wash}</span>{if $a4_site_item.locale} · <span translate="no">{$a4_site_item.locale|wash}</span>{/if}{if $a4_site_item.current} · <span class="a4-site-menu-here">{'in use'|i18n( 'design/admin/pagelayout' )|wash}</span>{/if}</span>
                    <span class="a4-site-menu-url" translate="no">{$a4_site_item.display_url|wash}</span>
                </a></li>
                {/foreach}
            </ul>
            {/foreach}
        </div>
    </div>
    {undef $a4_site_menu_label}
{/if}
</div>
{undef $a4_site_label $a4_site_menu}
