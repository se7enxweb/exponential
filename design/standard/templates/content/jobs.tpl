{* The content jobs list (content/jobs): the subtree removes and copies running or run in the background,
   the current user's own, or everybody's for a user with the content/jobs policy. Filters, sorting and paging
   are query parameters (state, type, user, sort, order, offset). *}
{def $state_names = hash( 'queued',    'Waiting'|i18n( 'design/admin/content/job' ),
                          'running',   'Running'|i18n( 'design/admin/content/job' ),
                          'done',      'Done'|i18n( 'design/admin/content/job' ),
                          'failed',    'Failed'|i18n( 'design/admin/content/job' ),
                          'cancelled', 'Cancelled'|i18n( 'design/admin/content/job' ) )
     $sort_url = concat( $base_url, '?', cond( $filter.state, concat( 'state=', $filter.state, '&' ), '' ), cond( $filter.type, concat( 'type=', $filter.type, '&' ), '' ), cond( $filter.user, concat( 'user=', $filter.user, '&' ), '' ) )
     $flip = cond( eq( $filter.order, 'desc' ), 'asc', 'desc' )}
<style type="text/css">
{literal}
#exp-contentjobs .cj-state { display: inline-block; padding: .1em .5em; border-radius: 4px; font-size: .8em; font-weight: bold; text-transform: uppercase; background: #e8e8e8; color: #555; white-space: nowrap; }
#exp-contentjobs .cj-state.running, #exp-contentjobs .cj-state.queued { background: #fde7d9; color: #b84a0e; }
#exp-contentjobs .cj-state.done { background: #e1f1e2; color: #2e7d32; }
#exp-contentjobs .cj-state.failed { background: #fbe3e6; color: #b00020; }
#exp-contentjobs .cj-bar { display: block; height: 8px; min-width: 4em; background: #e8e8e8; border-radius: 4px; overflow: hidden; margin-bottom: .2em; }
#exp-contentjobs .cj-bar span { display: block; height: 100%; background: #f26a21; }
#exp-contentjobs .cj-bar.done span { background: #2e7d32; }
#exp-contentjobs .cj-bar.failed span { background: #b00020; }
#exp-contentjobs .cj-table { width: 100%; overflow-x: auto; }
#exp-contentjobs td { overflow-wrap: anywhere; vertical-align: top; }
#exp-contentjobs td.cj-actions, #exp-contentjobs td.cj-user { white-space: nowrap; }
#exp-contentjobs td.cj-num { text-align: right; white-space: nowrap; }
#exp-contentjobs .cj-scope { margin: 0 0 .6em; }
#exp-contentjobs .cj-scope .current { font-weight: bold; }
#exp-contentjobs .cj-filters { display: flex; flex-wrap: wrap; gap: .5em 1em; align-items: flex-end; margin: .4em 0 .8em; }
#exp-contentjobs .cj-filters label { display: flex; flex-direction: column; font-size: .9em; }
#exp-contentjobs .cj-totals { display: flex; flex-wrap: wrap; gap: .4em .8em; margin: 0 0 .8em; padding: 0; list-style: none; }
#exp-contentjobs .cj-totals a { text-decoration: none; }
#exp-contentjobs .cj-empty { padding: 1.2em; text-align: center; color: #555; }
#exp-contentjobs .cj-pages { margin: .6em 0; display: flex; gap: 1em; align-items: center; }
#exp-contentjobs th a { white-space: nowrap; }
{/literal}
</style>

<div class="context-block" id="exp-contentjobs">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Content jobs (%count)'|i18n( 'design/admin/content/job',, hash( '%count', $count ) )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $error}
<div class="message-error"><h2>{$error|wash}</h2></div>
{/if}

<div class="block">
<p>{'Large subtree removes and copies run in the background, in batches. A job keeps going when you leave its page.'|i18n( 'design/admin/content/job' )}</p>
{if $can_see_all}
<p class="cj-scope">
    {if $show_all}<a href={'content/jobs'|ezurl}>{'My jobs'|i18n( 'design/admin/content/job' )}</a> | <span class="current">{'All users'|i18n( 'design/admin/content/job' )}</span>
    {else}<span class="current">{'My jobs'|i18n( 'design/admin/content/job' )}</span> | <a href={'content/jobs/(all)/1'|ezurl}>{'All users'|i18n( 'design/admin/content/job' )}</a>{/if}
</p>
{/if}

<ul class="cj-totals" aria-label="{'Jobs per state'|i18n( 'design/admin/content/job' )|wash}">
{foreach $states as $st}
    <li><a href={concat( $base_url, '?state=', $st )|ezurl}><span class="cj-state {$st}">{$state_names[$st]|wash}</span> {$totals[$st]}</a></li>
{/foreach}
</ul>

<form class="cj-filters" method="get" action={$base_url|ezurl}>
    <label>{'State'|i18n( 'design/admin/content/job' )}
        <select name="state"><option value="">{'All'|i18n( 'design/admin/content/job' )}</option>
        {foreach $states as $st}<option value="{$st}"{if eq( $filter.state, $st )} selected="selected"{/if}>{$state_names[$st]|wash}</option>{/foreach}</select></label>
    <label>{'Operation'|i18n( 'design/admin/content/job' )}
        <select name="type"><option value="">{'All'|i18n( 'design/admin/content/job' )}</option>
        {foreach $types as $t}<option value="{$t|wash}"{if eq( $filter.type, $t )} selected="selected"{/if}>{$t|wash}</option>{/foreach}</select></label>
    {if $show_all}
    <label>{'User'|i18n( 'design/admin/content/job' )}
        <select name="user"><option value="">{'All'|i18n( 'design/admin/content/job' )}</option>
        {foreach $users as $u}<option value="{$u.id}"{if eq( $filter.user, $u.id )} selected="selected"{/if}>{$u.name|wash}</option>{/foreach}</select></label>
    {/if}
    <input type="hidden" name="sort" value="{$filter.sort|wash}" /><input type="hidden" name="order" value="{$filter.order|wash}" />
    <input class="button" type="submit" value="{'Filter'|i18n( 'design/admin/content/job' )}" />
    {if or( $filter.state, $filter.type, $filter.user )}<a href={$base_url|ezurl}>{'Clear the filters'|i18n( 'design/admin/content/job' )}</a>{/if}
</form>
</div>

{if $jobs|count}
<form method="post" action={'content/jobs'|ezurl}>
{if $show_all}<input type="hidden" name="ShowAll" value="1" />{/if}
<div class="cj-table">
<table class="list" cellspacing="0">
<tr>
    {if $show_all}<th>{'User'|i18n( 'design/admin/content/job' )}</th>{/if}
    <th>{'Operation'|i18n( 'design/admin/content/job' )}</th>
    <th>{'Source and target'|i18n( 'design/admin/content/job' )}</th>
    <th><a href={concat( $sort_url, 'sort=state&order=', $flip )|ezurl}>{'State'|i18n( 'design/admin/content/job' )}</a></th>
    <th><a href={concat( $sort_url, 'sort=items&order=', $flip )|ezurl}>{'Progress and items'|i18n( 'design/admin/content/job' )}</a></th>
    <th><a href={concat( $sort_url, 'sort=started&order=', $flip )|ezurl}>{'Started'|i18n( 'design/admin/content/job' )}</a>, <a href={concat( $sort_url, 'sort=duration&order=', $flip )|ezurl}>{'duration'|i18n( 'design/admin/content/job' )}</a></th>
    <th>{'Server'|i18n( 'design/admin/content/job' )}</th>
    <th>{'Actions'|i18n( 'design/admin/content/job' )}</th>
</tr>
{foreach $jobs as $job sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style} cj-row" data-job-id="{$job.id|wash}" data-state="{$job.state|wash}">
    {if $show_all}<td class="cj-user" title="{$job.user_name|wash}">{$job.user_login|wash}</td>{/if}
    <td><a href={concat( 'content/job/', $job.id )|ezurl}>{$job.operation_short|wash}</a></td>
    <td>{$job.name|wash}{if $job.target_name} &rarr; {$job.target_name|wash}{/if}</td>
    <td><span class="cj-state {$job.state|wash}">{$state_names[$job.state]|wash}</span></td>
    <td><span class="cj-bar {$job.state|wash}" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$job.percent}"><span style="width: {$job.percent}%"></span></span>{'%done of %total'|i18n( 'design/admin/content/job',, hash( '%done', $job.done, '%total', $job.total ) )}</td>
    <td class="cj-when">{$job.when|wash}{if $job.duration}<br /><small>{$job.duration|wash}</small>{/if}</td>
    <td><code>{$job.server|wash}</code></td>
    <td class="cj-actions">
        <a href={concat( 'content/job/', $job.id )|ezurl}>{'Open'|i18n( 'design/admin/content/job' )}</a>
        {if $job.can_cancel}<button class="button" type="submit" name="CancelJobButton" value="{$job.id|wash}">{'Cancel'|i18n( 'design/admin/content/job' )}</button>{/if}
        {if $job.can_resume}<button class="button" type="submit" name="ResumeJobButton" value="{$job.id|wash}">{'Resume'|i18n( 'design/admin/content/job' )}</button>{/if}
    </td>
</tr>
{/foreach}
</table>
</div>
</form>
{if gt( $count, $page )}
<p class="cj-pages">
    {if gt( $offset, 0 )}<a href={concat( $base_url, '?', $query, '&offset=', max( 0, sub( $offset, $page ) ) )|ezurl}>{'Newer'|i18n( 'design/admin/content/job' )}</a>{/if}
    <span>{'%from to %to of %count'|i18n( 'design/admin/content/job',, hash( '%from', inc( $offset ), '%to', min( $count, sum( $offset, $page ) ), '%count', $count ) )}</span>
    {if lt( sum( $offset, $page ), $count )}<a href={concat( $base_url, '?', $query, '&offset=', sum( $offset, $page ) )|ezurl}>{'Older'|i18n( 'design/admin/content/job' )}</a>{/if}
</p>
{/if}
{else}
<div class="block cj-empty">
    {if or( $filter.state, $filter.type, $filter.user )}
    <p>{'No job matches these filters.'|i18n( 'design/admin/content/job' )} <a href={$base_url|ezurl}>{'Clear the filters'|i18n( 'design/admin/content/job' )}</a></p>
    {else}
    <p>{'There are no content jobs.'|i18n( 'design/admin/content/job' )}</p>
    <p>{'A remove or copy of a large subtree appears here when it runs in the background.'|i18n( 'design/admin/content/job' )}</p>
    {/if}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

</div>

{if $has_active}
<script type="text/javascript">
{literal}
(function () { setTimeout(function () { location.reload(); }, 5000); })();
{/literal}
</script>
{/if}
