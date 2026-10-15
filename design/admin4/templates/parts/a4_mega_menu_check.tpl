{* Checks the mega menu's settings (menu.ini; doc/features/6.0/admin4-design.md, "Mega menu") and names what is ignored
   in the debug report, as a debug message: page_head_script.tpl includes it while [TopAdminMenu] MegaMenuTabs[]
   names a tab. The header and the fragment simply leave out what they cannot use; this says why.
   * a MegaMenuTabs[] value whose [Topmenu_<tab>] has no MegaMenuTemplate or NavigationPartIdentifier
   * a key of the tab's MegaMenuLinks[] (in the section its [Topmenu_<tab>] MegaMenuSection names) that is no link of
     that section (Links[]) and no <group>-<key> of a section its GroupSections[] adds: an unknown key, or the link of
     an extension that is not active here
   * more than 12 keys: only the first 12 links the user may open are shown
   Drawn in the page's cache blocks, so a message shows when the block is built, not on every page. *}
{def $mmc_section = ''
     $mmc_own = hash()
     $mmc_added = hash()
     $mmc_parts = array()
     $mmc_known = false()}
{foreach ezini( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' ) as $mmc_tab}
    {if and( ezini_hasvariable( concat( 'Topmenu_', $mmc_tab ), 'MegaMenuTemplate', 'menu.ini' ),
             ezini_hasvariable( concat( 'Topmenu_', $mmc_tab ), 'NavigationPartIdentifier', 'menu.ini' ) )}
    {else}
        {debug-log msg=concat( 'menu.ini [TopAdminMenu] MegaMenuTabs[]=', $mmc_tab, ': no [Topmenu_', $mmc_tab, '] MegaMenuTemplate and NavigationPartIdentifier, ignored' )}
        {continue}
    {/if}
    {if ezini_hasvariable( concat( 'Topmenu_', $mmc_tab ), 'MegaMenuSection', 'menu.ini' )}{else}{continue}{/if}
    {set $mmc_section = ezini( concat( 'Topmenu_', $mmc_tab ), 'MegaMenuSection', 'menu.ini' )}
    {if ezini_hasvariable( $mmc_section, 'MegaMenuLinks', 'menu.ini' )}{else}{continue}{/if}
    {set $mmc_own = cond( ezini_hasvariable( $mmc_section, 'Links', 'menu.ini' ), ezini( $mmc_section, 'Links', 'menu.ini' ), hash() )
         $mmc_added = cond( ezini_hasvariable( $mmc_section, 'GroupSections', 'menu.ini' ), ezini( $mmc_section, 'GroupSections', 'menu.ini' ), hash() )}
    {foreach ezini( $mmc_section, 'MegaMenuLinks', 'menu.ini' ) as $mmc_key}
        {set $mmc_known = is_set( $mmc_own[ $mmc_key ] )}
        {if $mmc_known|not}
            {set $mmc_parts = $mmc_key|explode( '-' )}
            {if and( $mmc_parts|count|gt( 1 ), is_set( $mmc_added[ $mmc_parts[0] ] ) )}
                {if ezini_hasvariable( $mmc_added[ $mmc_parts[0] ], 'Links', 'menu.ini' )}
                    {set $mmc_known = is_set( ezini( $mmc_added[ $mmc_parts[0] ], 'Links', 'menu.ini' )[ $mmc_parts|extract_right( $mmc_parts|count|dec )|implode( '-' ) ] )}
                {/if}
            {/if}
        {/if}
        {if $mmc_known|not}
            {debug-log msg=concat( 'menu.ini [', $mmc_section, '] MegaMenuLinks[]=', $mmc_key, ': no link of this menu or of a section its GroupSections[] adds (unknown key, or its extension is not active here), ignored' )}
        {/if}
    {/foreach}
    {if ezini( $mmc_section, 'MegaMenuLinks', 'menu.ini' )|count|gt( 12 )}
        {debug-log msg=concat( 'menu.ini [', $mmc_section, '] MegaMenuLinks[]: ', ezini( $mmc_section, 'MegaMenuLinks', 'menu.ini' )|count, ' keys, the mega menu shows the first 12 links the user may open' )}
    {/if}
{/foreach}
{undef $mmc_section $mmc_own $mmc_added $mmc_parts $mmc_known}
