{* What an operation on a subtree touches, shown before it happens. Variables: $job_summary
   (ContentJobDetails::subtreeSummary()), optional $operation ('remove', 'copy', 'move', 'state', 'addlocation',
   'removelocation': the last two work on the selected locations only, not on what is below them). *}
{default operation='remove'}
<style type="text/css">
{literal}
.exp-jobsummary { margin: .6em 0 1em; }
.exp-jobsummary dl { display: grid; grid-template-columns: max-content 1fr; gap: .2em 1.2em; margin: .3em 0; }
.exp-jobsummary dt { color: var(--a4-muted, #5d6573); }
.exp-jobsummary dd { margin: 0; overflow-wrap: anywhere; }
.exp-jobsummary ul { margin: .2em 0; padding-left: 1.2em; }
{/literal}
</style>
<div class="block exp-jobsummary">
<h2>{'What this touches'|i18n( 'design/admin/content/job' )}</h2>
<dl>
    {if or( eq( $operation, 'addlocation' ), eq( $operation, 'removelocation' ) )}
    <dt>{'Selected locations'|i18n( 'design/admin/content/job' )}</dt><dd>{$job_summary.locations}</dd>
    {else}
    <dt>{'Locations'|i18n( 'design/admin/content/job' )}</dt><dd>{$job_summary.locations}</dd>
    {/if}
    <dt>{'Objects'|i18n( 'design/admin/content/job' )}</dt><dd>{$job_summary.objects}</dd>
    {if or( eq( $operation, 'remove' ), eq( $operation, 'removelocation' ) )}
    <dt>{'Objects with other locations'|i18n( 'design/admin/content/job' )}</dt>
    <dd>{$job_summary.outside}{if $job_summary.outside} - {'only these locations go; the objects keep existing at their other locations'|i18n( 'design/admin/content/job' )}{/if}</dd>
    {/if}
    <dt>{'Classes'|i18n( 'design/admin/content/job' )}</dt>
    <dd>{foreach $job_summary.classes as $c}{$c.name|wash} ({$c.count}){delimiter}, {/delimiter}{/foreach}</dd>
    {foreach $job_summary.roots as $r max 10}
    <dt>{'Last modified'|i18n( 'design/admin/content/job' )}</dt>
    <dd>{$r.name|wash}: {$r.modified|l10n( 'shortdatetime' )}{if $r.modifier} - {$r.modifier|wash}{/if}</dd>
    {/foreach}
    {if $job_summary.roots|count|gt( 10 )}
    <dt></dt><dd>{'and %count more'|i18n( 'design/admin/content/job',, hash( '%count', sub( $job_summary.roots|count, 10 ) ) )}</dd>
    {/if}
</dl>
{if $job_summary.jobs|count}
<div class="message-warning">
    <h2>{'Background jobs are working on this part of the tree'|i18n( 'design/admin/content/job' )}</h2>
    <ul>{foreach $job_summary.jobs as $j}<li><a href={concat( 'content/job/', $j.id )|ezurl}>{$j.title|wash}</a> - {$j.percent}%</li>{/foreach}</ul>
    <p>{'An operation that overlaps them is refused until they are done.'|i18n( 'design/admin/content/job' )}</p>
</div>
{/if}
</div>
{/default}
