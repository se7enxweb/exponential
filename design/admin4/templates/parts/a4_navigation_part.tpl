{* The navigation part the admin frame treats as current, printed as its identifier and nothing else: the page's own
   ($navigation_part, else $module_result.navigation_part), or ezsetupnavigationpart for a page of a top tab that the
   Setup menu stands in for (menu.ini [Leftmenu_setup] GroupTabs[]) while that tab is not in the top menu (left out of
   [TopAdminMenu] Tabs[], named in HiddenTabs[], or named in SetupTabs[] for a user who may open setup/menu). Then
   the Setup tab is marked and the Setup menu, which holds the tab's links in its group, is drawn: no page marks a
   tab that is not there or draws a menu no tab leads to.
   page_topmenu.tpl and page_leftmenu.tpl read it with {set-block}. doc/features/6.0/admin4-design.md,
   "Moving a tab into Setup". *}
{def $a4np_part = first_set( $navigation_part.identifier, '' )}
{if $a4np_part|eq( '' )}{set $a4np_part = first_set( $module_result.navigation_part, '' )}{/if}
{if and( $a4np_part|ne( '' ), $a4np_part|ne( 'ezsetupnavigationpart' ), ezini_hasvariable( 'Leftmenu_setup', 'GroupTabs', 'menu.ini' ) )}
    {def $a4np_tabs = cond( ezini_hasvariable( 'TopAdminMenu', 'Tabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'Tabs', 'menu.ini' ), array() )
         $a4np_hidden = cond( ezini_hasvariable( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' ), array() )
         $a4np_setup = cond( ezini_hasvariable( 'TopAdminMenu', 'SetupTabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'SetupTabs', 'menu.ini' ), array() )}
    {* SetupTabs[] leave the top menu for a user who may open the Setup overview (the topmenu operator) *}
    {if and( $a4np_setup|count, fetch( 'user', 'can_open', hash( 'uri', 'setup/menu' ) ) )}
        {set $a4np_hidden = $a4np_hidden|merge( $a4np_setup )}
    {/if}
    {foreach ezini( 'Leftmenu_setup', 'GroupTabs', 'menu.ini' ) as $a4np_tab}
        {if and( or( $a4np_tabs|contains( $a4np_tab )|not, $a4np_hidden|contains( $a4np_tab ) ),
                 ezini_hasvariable( concat( 'Topmenu_', $a4np_tab ), 'NavigationPartIdentifier', 'menu.ini' ),
                 eq( ezini( concat( 'Topmenu_', $a4np_tab ), 'NavigationPartIdentifier', 'menu.ini' ), $a4np_part ) )}
            {set $a4np_part = 'ezsetupnavigationpart'}
            {break}
        {/if}
    {/foreach}
    {undef $a4np_tabs $a4np_hidden $a4np_setup}
{/if}
{$a4np_part}
{undef $a4np_part}
