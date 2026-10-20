{* The start of the API access page (apikey/list) in admin4: the page itself is design/standard's apikey/list.tpl,
   framed here like every other admin4 page (context block, .context-title, .a4-intro). Its styles take admin4's
   tokens (--a4-*) through stylesheets/apikey.css. Closed by parts/page_end.tpl.
   Variables: title, intro (or false). *}
{include uri='design:apikey/parts/style.tpl'}
<div class="context-block a4-page">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$title|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if first_set( $intro, false() )}
<p class="a4-intro">{$intro|wash}</p>
{/if}
<p class="a4-meta"><a href={'user/edit'|ezurl}>{'My account'|i18n( 'design/standard/apikey' )}</a></p>

<div class="ak">
