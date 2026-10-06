{* The start of the API access page (standard design). A design overrides this file and parts/page_end.tpl to
   place the page in its own frame; the page itself is the same everywhere.
   Variables: title, intro (or false). *}
{include uri='design:apikey/parts/style.tpl'}
<div class="ak ak-public">
    <div class="ak-head">
        <p class="ak-crumb"><a href={'user/edit'|ezurl}>{'My account'|i18n( 'design/standard/apikey' )}</a></p>
        <h1>{$title|wash}</h1>
{if first_set( $intro, false() )}
        <p>{$intro|wash}</p>
{/if}
    </div>
