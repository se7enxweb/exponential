{* The start of every e-mail preference page (standard design: a public site). A design overrides this file and
   parts/page_end.tpl to place the pages in its own frame; the pages themselves are the same everywhere.
   Variables: title, intro (or false), crumb (hash( url, text ) or false), wide (true for the administrator's lists),
   admin_tab (the current tab of the administrator's pages, or false). *}
{include uri='design:mailpreferences/parts/style.tpl'}
<div class="mp mp-public{if first_set( $wide, false() )} mp-wide{/if}">
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
