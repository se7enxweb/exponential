{def $liclass='unselected '
     $item_mega=false()}
    {if eq( $navigationpart_identifier, $menu_item.navigationpart_identifier ) }
        {set $liclass='selected '}
    {/if}
    {* $a4_mega_part (page_topmenu.tpl): the navigation part of the tab that gets a mega menu, empty for none *}
    {if is_set( $a4_mega_part )}
        {if and( $a4_mega_part, eq( $a4_mega_part, $menu_item.navigationpart_identifier ), $menu_item.enabled, or( is_unset( $menu_item.access ), $menu_item.access ) )}
            {set $item_mega=true()}
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
               panel (aria-expanded, aria-controls). The panel's groups and links come from setup/megamenu, loaded by
               a4_mega_menu.js when it is first opened; until then it holds a status line and the link to the
               overview, which also stays when the links cannot be loaded. *}
            <button type="button" class="a4-mega-toggle" aria-expanded="false" aria-controls="a4-mega-setup"
                    aria-label="{'Open the Setup menu'|i18n( 'design/admin/pagelayout' )|wash}"
                    title="{'Open the Setup menu'|i18n( 'design/admin/pagelayout' )|wash}"
                    data-a4-mega-src={'setup/megamenu'|ezurl}><span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span></button>
            <div id="a4-mega-setup" class="a4-mega-panel" hidden
                 data-a4-mega-error="{'The Setup menu could not be loaded.'|i18n( 'design/admin/pagelayout' )|wash}">
                <div class="a4-mega-body" data-a4-mega-body>
                    <p class="a4-mega-status" role="status">{'Loading the Setup menu'|i18n( 'design/admin/pagelayout' )|wash}</p>
                </div>
                <p class="a4-mega-all"><a href={'setup/menu'|ezurl}>{'All Setup functions'|i18n( 'design/admin/pagelayout' )|wash}<span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span></a></p>
            </div>
        {/if}
    </li>
{undef $liclass $item_mega}
