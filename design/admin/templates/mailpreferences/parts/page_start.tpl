{* The start of every e-mail preference page in the old admin design: the title in the admin's context block, the
   page below it. Variables: title, intro (or false), crumb (hash( url, text ) or false), wide, admin_tab (or false). *}
{include uri='design:mailpreferences/parts/style.tpl'}
<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$title|wash}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-content">
<div class="mp mp-wide" style="padding: 8px 10px;">
{if first_set( $crumb, false() )}
    <p class="mp-crumb"><a href={$crumb.url|ezurl}>{$crumb.text|wash}</a></p>
{/if}
{if first_set( $intro, false() )}
    <div class="mp-head"><p>{$intro|wash}</p></div>
{/if}
{if first_set( $admin_tab, false() )}
    {include uri='design:mailpreferences/parts/admin_tabs.tpl' current=$admin_tab}
{/if}
