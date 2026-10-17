{* The header of a result list of the content checks (shared by every check): its title and "X to Y of N".

     {include uri='design:contentcheck/results_head.tpl' title='Objects'|i18n( 'kernel/contentcheck' ) title_id='cc-list-title'
              offset=$params.offset limit=$limit count=$count}

   Variables: title, title_id (the h2's id, which the section's aria-labelledby names), offset, limit, count (-1: a
   list without a total, e.g. one paged by position; then count_text is shown if given), count_text. The same file is
   in design/admin and design/admin4. *}
{default count_text=''}
<div class="a4-section-head">
    <h2 class="a4-h2" id="{$title_id|wash}">{$title|wash}</h2>
    {if $count|gt( 0 )}
    <span class="a4-meta">{'%from to %to of %count'|i18n( 'kernel/contentcheck',, hash( '%from', sum( $offset, 1 ), '%to', min( sum( $offset, $limit ), $count ), '%count', $count ) )}</span>
    {elseif $count_text|ne( '' )}
    <span class="a4-meta">{$count_text|wash}</span>
    {/if}
</div>
{/default}
