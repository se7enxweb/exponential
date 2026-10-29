{* The node view's tabs switch at the end of the meta line, after the languages (node/view/a4_full_body.tpl), so
   the tab row below has the box's whole width; window_controls.tpl leaves its own copy out when the including
   template passes tabs_switch_elsewhere. The markup is window_controls.tpl's: node_tabs.js finds every
   a.show-hide-tabs in the page and switches its id between #maincontent-show and #maincontent-hide; the state is the
   admin_navigation_content preference, as there. Drawn in admin4.css (.a4-node-tabs-switch). Spans only: it stands
   inside the meta line's <p>. *}
{def $a4_tabs_pref = ezpreference( 'admin_navigation_content' )
     $a4_tabs_off  = false()}
{if $a4_tabs_pref|is_string}{set $a4_tabs_off = $a4_tabs_pref|not}{/if}
<span class="a4-node-tabs-switch">
{if $a4_tabs_off}
    <a id="maincontent-show" class="show-hide-tabs" href={'/user/preferences/set/admin_navigation_content/1'|ezurl} title="{'Enable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}" role="button" aria-pressed="false" aria-label="{'Show tabs'|i18n( 'design/admin/node/view/full' )|wash}" data-title-show="{'Enable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}" data-title-hide="{'Disable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}">&nbsp;</a>
{else}
    <a id="maincontent-hide" class="show-hide-tabs" href={'/user/preferences/set/admin_navigation_content/0'|ezurl} title="{'Disable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}" role="button" aria-pressed="true" aria-label="{'Show tabs'|i18n( 'design/admin/node/view/full' )|wash}" data-title-show="{'Enable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}" data-title-hide="{'Disable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}">&nbsp;</a>
{/if}
<span class="a4-tabs-switch-label" aria-hidden="true">{'Tabs'|i18n( 'design/admin/parts/my/menu' )}</span>
</span>
{undef $a4_tabs_pref $a4_tabs_off}
