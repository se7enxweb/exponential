{* The "Import and export" list of a left menu (the transfer registry, doc/eng-us/features/6.0/transfer-registry.md):
   every import and export tool the user may use, the current one marked, and "Which tool do I need?". The Setup menu
   includes it; an extension's left menu includes it after its own links. The markup of parts/ini_menu.tpl's boxes
   (box-header, leftmenu-items), so it looks like every other left menu; no CSS of its own.
   Nothing when the user may use no tool, or only the current one.
   Parameters: optional $current (the current tool's id). *}
{def $transfer_menu_tools = transfer_tools()
     $transfer_menu_current = first_set( $current, '' )}
{if or( $transfer_menu_tools|count|gt( 1 ), and( $transfer_menu_tools|count|eq( 1 ), $transfer_menu_tools[0].id|ne( $transfer_menu_current ) ) )}
<div class="box-header" data-transfer="left-menu"><div class="box-ml">
<h4 id="leftmenu-title-transfer">{'Import and export'|i18n( 'design/admin/transfer' )}</h4>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">
<ul class="leftmenu-items" aria-labelledby="leftmenu-title-transfer">
{foreach $transfer_menu_tools as $transfer_menu_tool}
    {if eq( $transfer_menu_tool.id, $transfer_menu_current )}
    <li class="current"><div><a href={$transfer_menu_tool.url|ezurl} aria-current="true">{$transfer_menu_tool.name|wash}</a></div></li>
    {else}
    <li><div><a href={$transfer_menu_tool.url|ezurl} title="{$transfer_menu_tool.description|wash}">{$transfer_menu_tool.name|wash}</a></div></li>
    {/if}
{/foreach}
    <li><div><a href={'exphelp/viewer/help/import-export-tools.md'|ezurl}>{'Which tool do I need?'|i18n( 'design/admin/transfer' )}</a></div></li>
</ul>
</div></div></div>
{/if}
{undef $transfer_menu_tools $transfer_menu_current}
