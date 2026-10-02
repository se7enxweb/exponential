{* What an operation on a subtree touches, shown before it happens. Variables: $job_summary
   (ContentJobDetails::subtreeSummary()), optional $operation ('remove' or 'copy'). *}
{default operation='remove'}
<style type="text/css">
{literal}
.exp-jobsummary { margin: .6em 0 1em; }
.exp-jobsummary dl { display: grid; grid-template-columns: max-content 1fr; gap: .2em 1.2em; margin: .3em 0; }
.exp-jobsummary dt { color: #555; }
.exp-jobsummary dd { margin: 0; overflow-wrap: anywhere; }
.exp-jobsummary ul { margin: .2em 0; padding-left: 1.2em; }
{/literal}
</style>
<div class="block exp-jobsummary">
<h2>{'What this touches'|i18n( 'design/admin/content/job' )}</h2>
<dl>
    <dt>{'Locations'|i18n( 'design/admin/content/job' )}</dt><dd>{$job_summary.locations}</dd>
    <dt>{'Objects'|i18n( 'design/admin/content/job' )}</dt><dd>{$job_summary.objects}</dd>
    {if eq( $operation, 'remove' )}
    <dt>{'Objects with other locations'|i18n( 'design/admin/content/job' )}</dt>
    <dd>{$job_summary.outside}{if $job_summary.outside} - {'only these locations go; the objects keep existing at their other locations'|i18n( 'design/admin/content/job' )}{/if}</dd>
    {/if}
    <dt>{'Classes'|i18n( 'design/admin/content/job' )}</dt>
    <dd>{foreach $job_summary.classes as $c}{$c.name|wash} ({$c.count}){delimiter}, {/delimiter}{/foreach}</dd>
    {foreach $job_summary.roots as $r}
    <dt>{'Last modified'|i18n( 'design/admin/content/job' )}</dt>
    <dd>{$r.name|wash}: {$r.modified|l10n( 'shortdatetime' )}{if $r.modifier} - {$r.modifier|wash}{/if}</dd>
    {/foreach}
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
