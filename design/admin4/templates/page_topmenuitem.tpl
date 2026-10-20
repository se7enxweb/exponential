{def $liclass='unselected '
     $item_mega=''
     $item_icon=''}
    {* menu.ini [Topmenu_<tab>] Icon: drawn before the name when design.ini [AdminDesignSettings] IconList[] knows it and
       TopMenuIcons is not disabled *}
    {if and( is_set( $menu_item.icon ), $menu_item.icon|ne( '' ), ezini_hasvariable( 'AdminDesignSettings', 'IconList', 'design.ini' ), ezini( 'AdminDesignSettings', 'TopMenuIcons', 'design.ini' )|ne( 'disabled' ) )}
        {if ezini( 'AdminDesignSettings', 'IconList', 'design.ini' )|contains( $menu_item.icon )}
            {set $item_icon=$menu_item.icon}
        {/if}
    {/if}
    {if eq( $navigationpart_identifier, $menu_item.navigationpart_identifier ) }
        {set $liclass='selected '}
    {/if}
    {* $a4_mega_tabs (page_topmenu.tpl): a hash of the navigation part of each tab that gets a mega menu to the tab *}
    {if is_set( $a4_mega_tabs )}
        {if and( is_set( $a4_mega_tabs[ $menu_item.navigationpart_identifier ] ), $menu_item.enabled, or( is_unset( $menu_item.access ), $menu_item.access ) )}
            {set $item_mega=$a4_mega_tabs[ $menu_item.navigationpart_identifier ]}
        {/if}
    {/if}
    <li{if $item_mega} class="a4-mega-item"{/if}>
        {if and( $menu_item.enabled, or(is_unset( $menu_item.access ), $menu_item.access ) )}
            <a href={$menu_item.url|ezurl} class="{$liclass}{$menu_item.position} {$menu_item.navigationpart_identifier}" title="{$menu_item.tooltip}"{if eq( $liclass, 'selected ' )} aria-current="page"{/if}>{if $item_icon}<span class="a4-icon a4-bi-{$item_icon|wash}" aria-hidden="true"></span>{/if}{$menu_item.name|wash}</a>
        {else}
            <a href='#' class="disabled" title="{$menu_item.tooltip}" aria-disabled="true" tabindex="-1">{if $item_icon}<span class="a4-icon a4-bi-{$item_icon|wash}" aria-hidden="true"></span>{/if}{$menu_item.name|wash}</a>
        {/if}
        {if $item_mega}
            {* The mega menu (prototype; doc/eng-us/features/6.0/admin4-design.md, "Mega menu"): the WAI-ARIA disclosure
               navigation pattern, not a menu. The tab stays a link; this button beside it opens and closes the
               panel (aria-expanded, aria-controls). The panel's groups and links come from user/megamenu/<tab>,
               loaded by a4_mega_menu.js when it is first opened; until then it holds a status line and the link to
               the tab's page, which also stays when the links cannot be loaded. *}
            <button type="button" class="a4-mega-toggle" aria-expanded="false" aria-controls="a4-mega-{$item_mega|wash}"
                    aria-label="{'%tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}"
                    title="{'%tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}"
                    data-a4-mega-src={concat( 'user/megamenu/', $item_mega )|ezurl}><span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span></button>
            {* [Topmenu_<tab>] MegaMenuLayout=wide: the groups in up to four balanced columns (a long choice, the Dashboard) *}
            <div id="a4-mega-{$item_mega|wash}" class="a4-mega-panel{if ezini_hasvariable( concat( 'Topmenu_', $item_mega ), 'MegaMenuLayout', 'menu.ini' )}{if eq( ezini( concat( 'Topmenu_', $item_mega ), 'MegaMenuLayout', 'menu.ini' ), 'wide' )} a4-mega-wide{/if}{/if}" hidden
                 data-a4-mega-error="{'The %tab menu could not be loaded.'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}"
                 data-a4-mega-retry="{'Try again'|i18n( 'design/admin/pagelayout' )|wash}"
                 data-a4-mega-signed-out="{'You are signed out. Sign in again to see the %tab menu.'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}"
                 data-a4-mega-signin="{'Sign in'|i18n( 'design/admin/pagelayout' )|wash}"
                 data-a4-mega-login={'user/login'|ezurl}>
                <div class="a4-mega-body" data-a4-mega-body>
                    <p class="a4-mega-status" role="status">{'Loading the %tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}</p>
                </div>
                <p class="a4-mega-all"><a href={$menu_item.url|ezurl}>{'Go to %tab'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}<span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span></a></p>
            </div>
        {/if}
    </li>
{undef $liclass $item_mega $item_icon}
