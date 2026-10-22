{* The header items of the extensions (design.ini [AdminHeaderSettings] HeaderItems[]: templates, e.g.
   sqliimport/header_bell.tpl), after Help and before the user menu, each a round 36px tool (parts/a4_header_item.tpl
   describes the markup). Included by admin4's page_topmenu.tpl and admin4l's tools block. They are drawn inside the
   header's cache block, which is kept per user and page part, so an item that shows live data (a count) names a
   feed in data-a4-header-feed and admin4.js loads it. *}
{if ezini_hasvariable( 'AdminHeaderSettings', 'HeaderItems', 'design.ini' )}{if ezini( 'AdminHeaderSettings', 'HeaderItems', 'design.ini' )|count|gt( 0 )}
<div id="header-items" class="header-items">
    {foreach ezini( 'AdminHeaderSettings', 'HeaderItems', 'design.ini' ) as $a4_header_item}{include uri=concat( 'design:', $a4_header_item )}{/foreach}
</div>
{/if}{/if}
