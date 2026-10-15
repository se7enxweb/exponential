{def $liclass='unselected '
     $item_mega=''}
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
            <a href={$menu_item.url|ezurl} class="{$liclass}{$menu_item.position} {$menu_item.navigationpart_identifier}" title="{$menu_item.tooltip}"{if eq( $liclass, 'selected ' )} aria-current="page"{/if}>{$menu_item.name|wash}</a>
        {else}
            <a href='#' class="disabled" title="{$menu_item.tooltip}" aria-disabled="true" tabindex="-1">{$menu_item.name|wash}</a>
        {/if}
        {if $item_mega}
            {* The mega menu (prototype; doc/features/6.0/admin4-design.md, "Mega menu"): the WAI-ARIA disclosure
               navigation pattern, not a menu. The tab stays a link; this button beside it opens and closes the
               panel (aria-expanded, aria-controls). The panel's groups and links come from user/megamenu/<tab>,
               loaded by a4_mega_menu.js when it is first opened; until then it holds a status line and the link to
               the tab's page, which also stays when the links cannot be loaded. *}
            <button type="button" class="a4-mega-toggle" aria-expanded="false" aria-controls="a4-mega-{$item_mega|wash}"
                    aria-label="{'%tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}"
                    title="{'%tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}"
                    data-a4-mega-src={concat( 'user/megamenu/', $item_mega )|ezurl}><span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span></button>
            <div id="a4-mega-{$item_mega|wash}" class="a4-mega-panel" hidden
                 data-a4-mega-error="{'The %tab menu could not be loaded.'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}">
                <div class="a4-mega-body" data-a4-mega-body>
                    <p class="a4-mega-status" role="status">{'Loading the %tab menu'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}</p>
                </div>
                <p class="a4-mega-all"><a href={$menu_item.url|ezurl}>{'Go to %tab'|i18n( 'design/admin/pagelayout',, hash( '%tab', $menu_item.name ) )|wash}<span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span></a></p>
            </div>
        {/if}
    </li>
{undef $liclass $item_mega}
