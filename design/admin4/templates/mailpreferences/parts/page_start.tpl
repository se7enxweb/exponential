{* The start of every e-mail preference page in admin4 (and admin4l): the page sits in the admin's content card, so
   it needs no margins of its own; the colours come from the admin's tokens (--a4-*) through parts/style.tpl.
   Variables: title, intro (or false), crumb (hash( url, text ) or false), wide, admin_tab (or false). *}
{include uri='design:mailpreferences/parts/style.tpl'}
<div class="mp mp-admin4{if first_set( $wide, false() )} mp-wide{/if}">
    <div class="mp-head">
{if first_set( $crumb, false() )}
        <p class="mp-crumb"><a href={$crumb.url|ezurl}>{$crumb.text|wash}</a></p>
{/if}
        <h1>{$title|wash}</h1>
{if first_set( $intro, false() )}
        <p>{$intro|wash}</p>
{/if}
    </div>
{if first_set( $admin_tab, false() )}
    {include uri='design:mailpreferences/parts/admin_tabs.tpl' current=$admin_tab}
{/if}
