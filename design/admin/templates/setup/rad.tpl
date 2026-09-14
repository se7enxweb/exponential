{* The RAD tools.

   Every point this system can be extended at is listed, whether or not there is
   a tool for it: what it is for, where the code goes, and what registers it -
   which is the part that is otherwise only discoverable by reading the kernel.
   A point with no tool is still worth knowing about. *}

{literal}
<style type="text/css">
.exp-rad {
    --rad-ink: #1c1c1e; --rad-muted: #6a6a72; --rad-line: #e2e2e6;
    --rad-accent: #2d6cdf; --rad-ok: #1f8a4c;
    color: var(--rad-ink);
}
.exp-rad h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-rad .rad-meta { color: var(--rad-muted); font-size: .92em; }
.exp-rad code { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }

.rad-summary {
    display: flex; flex-wrap: wrap; gap: 1.6rem; align-items: baseline;
    padding: 0 0 1.2rem 0;
}
.rad-summary b { font-size: 1.5rem; }

.rad-group { margin: 0 0 1.8rem 0; }
.rad-group > .rad-meta { display: block; padding-bottom: .7rem; }

.rad-point {
    border: 1px solid var(--rad-line); border-radius: 8px; background: #fff;
    padding: .85rem 1rem; margin: 0 0 .6rem 0;
}
.rad-point.is-ready { border-left: 4px solid var(--rad-ok); }
.rad-point-head {
    display: flex; flex-wrap: wrap; align-items: baseline; gap: .6rem;
    white-space: normal;
}
.rad-point-head h3 { margin: 0; font-size: 1rem; }
.rad-point-what { padding: .2rem 0 .5rem 0; white-space: normal; }
.rad-facts { display: flex; flex-direction: column; gap: .25rem; }
.rad-fact { display: flex; flex-wrap: wrap; gap: .5rem; white-space: normal; }
.rad-fact b {
    flex: 0 0 6.5rem; font-weight: 600; color: var(--rad-muted);
    font-size: .85em; text-transform: uppercase; letter-spacing: .03em;
}
.rad-fact span { flex: 1 1 14rem; min-width: 0; }
.rad-tag {
    display: inline-block; padding: .05rem .45rem; border-radius: 4px;
    border: 1px solid var(--rad-line); font-size: .8em; background: #fafafb;
    color: var(--rad-muted); white-space: nowrap;
}
.rad-tag.is-ready { border-color: var(--rad-ok); color: var(--rad-ok); }
.rad-open { font-weight: 600; text-decoration: none; }
.rad-legend { border: 1px solid var(--rad-line); border-radius: 8px; padding: .9rem 1rem; background: #fcfcfd; }
.rad-legend dt { font-weight: 600; padding-top: .5rem; }
.rad-legend dd { margin: 0; color: var(--rad-muted); white-space: normal; }

/* The filter sits on its own line with room under it. The admin toolbar floats
   its contents, so without clearing it the first group heading comes up beside
   the filter instead of below it. */
.rad-survey-note { padding: 0 0 1.2rem 0; }
.rad-survey-link {
    display: flex; gap: .9rem; align-items: center; text-decoration: none; color: inherit;
    border: 1px solid var(--rad-line); border-left: 4px solid var(--rad-accent);
    border-radius: 8px; padding: .8rem 1rem; background: #fcfcfd;
}
.rad-survey-link:hover { border-color: #9a9aa0; border-left-color: var(--rad-accent); }
.rad-survey-link > span { min-width: 0; }
.rad-survey-count { flex: 0 0 auto; font-size: 1.6rem; font-weight: 700; color: var(--rad-accent); line-height: 1; }
.rad-survey-link b { display: block; }
.rad-survey-link .rad-meta { display: block; white-space: normal; }

.rad-filter { display: block; padding: 0 0 .4rem 0; }
.rad-filter:after { content: ""; display: block; clear: both; }
.rad-filter p.table-preferences { margin: 0; }
.rad-groups { clear: both; padding-top: 1.2rem; }
</style>
{/literal}

<div class="context-block exp-rad">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Rapid Application Development Tools'|i18n( 'design/admin/setup/rad' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'Every point this system can be extended at is listed below: what it is for, where the code goes, and what registers it. Where there is a tool it opens from here; where there is not, what is written here is what you would otherwise have to find by reading the kernel.'|i18n( 'design/admin/setup/rad' )}</p>
</div>

<div class="rad-summary">
    <span><b>{$rad_coverage.total}</b> {'extension points written up'|i18n( 'design/admin/setup/rad' )}</span>
    <span><b>{$rad_coverage.covered}</b> {'with a tool'|i18n( 'design/admin/setup/rad' )}</span>
    <span class="rad-meta">{'The rest are documented, and each is a tool waiting to be written.'|i18n( 'design/admin/setup/rad' )}</span>
{if ne( $rad_filter, 'all' )}
    <span class="rad-meta">{'Showing %shown of them.'|i18n( 'design/admin/setup/rad',, hash( '%shown', $rad_shown ) )}</span>
{/if}
</div>

{* The written list is the map. The survey is the territory, and it is a good
   deal larger than the map - which is worth saying on the page rather than
   leaving somebody to think forty odd points is the size of this system. *}
<div class="rad-survey-note">
<a class="rad-survey-link" href={'setup/radsurvey'|ezurl}>
    <span class="rad-survey-count">{$rad_survey.counts.total}</span>
    <span>
        <b>{'Extension point survey'|i18n( 'design/admin/setup/rad' )}</b>
        <span class="rad-meta">{'The list above is written by hand. This one is read off disk on every request: %settings settings that name a class across %ini ini files, %views module views, %repositories directories searched for handlers, and %contracts interfaces waiting to be implemented.'|i18n( 'design/admin/setup/rad',, hash( '%settings', $rad_survey.counts.settings, '%ini', $rad_survey.counts.ini, '%views', $rad_survey.counts.views, '%repositories', $rad_survey.counts.repositories, '%contracts', $rad_survey.counts.contracts ) )}</span>
    </span>
</a>
</div>

{* Which of them to list. Links rather than a script, so a filtered list can be
   linked to, comes back the same, and works with javascript switched off. *}
<div class="context-toolbar rad-filter"><div class="button-left">
<p class="table-preferences">
{foreach $rad_filters as $rad_option}
    {if $rad_option.current}<span class="current">{$rad_option.label|i18n( 'design/admin/setup/rad' )|wash} ({$rad_option.count})</span>
    {else}<a href={$rad_option.url|ezurl}>{$rad_option.label|i18n( 'design/admin/setup/rad' )|wash} ({$rad_option.count})</a>{/if}
{/foreach}
</p>
</div></div>

<div class="rad-groups">

{if $rad_groups|count|eq( 0 )}
<div class="block"><p>{'Nothing matches that.'|i18n( 'design/admin/setup/rad' )}</p></div>
{/if}

{foreach $rad_groups as $rad_group}
<div class="rad-group">
    <h2>{$rad_group.title|i18n( 'design/admin/setup/rad' )|wash}</h2>
    <span class="rad-meta">{$rad_group.description|i18n( 'design/admin/setup/rad' )|wash}</span>

    {foreach $rad_group.points as $rad_point}
    <div class="rad-point{if $rad_point.tool} is-ready{/if}">
        <div class="rad-point-head">
            <h3>{if $rad_point.tool}<a class="rad-open" href={concat( '/', $rad_point.tool )|ezurl}>{$rad_point.title|i18n( 'design/admin/setup/rad' )|wash}</a>{else}{$rad_point.title|i18n( 'design/admin/setup/rad' )|wash}{/if}</h3>
            {if $rad_point.tool}
            <span class="rad-tag is-ready">{'tool available'|i18n( 'design/admin/setup/rad' )}</span>
            {else}
            <span class="rad-tag">{'no tool yet'|i18n( 'design/admin/setup/rad' )}</span>
            {/if}
            <span class="rad-tag">{$rad_point.mechanism|wash}</span>
        </div>

        <div class="rad-point-what">{$rad_point.what|i18n( 'design/admin/setup/rad' )|wash}</div>

        <div class="rad-facts">
            <div class="rad-fact"><b>{'Code'|i18n( 'design/admin/setup/rad' )}</b><span><code>{$rad_point.where|wash}</code></span></div>
            <div class="rad-fact"><b>{'Registered by'|i18n( 'design/admin/setup/rad' )}</b><span>{$rad_point.register|wash}</span></div>
            <div class="rad-fact"><b>{'Contract'|i18n( 'design/admin/setup/rad' )}</b><span><code>{$rad_point.contract|wash}</code></span></div>
            <div class="rad-fact"><b>{'Kernel'|i18n( 'design/admin/setup/rad' )}</b><span class="rad-meta"><code>{$rad_point.source|wash}</code></span></div>
        </div>
    </div>
    {/foreach}
</div>
{/foreach}

</div>

<div class="rad-legend">
<h2>{'How things are registered'|i18n( 'design/admin/setup/rad' )}</h2>
<dl>
{foreach $rad_mechanisms as $rad_key => $rad_text}
    <dt>{$rad_key|wash}</dt>
    <dd>{$rad_text|i18n( 'design/admin/setup/rad' )|wash}</dd>
{/foreach}
</dl>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>
