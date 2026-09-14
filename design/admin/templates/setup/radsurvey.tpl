{* The extension point survey.

   Not a written list: what is actually on this installation, read off disk on
   every request. *}

{literal}
<style type="text/css">
.exp-sv {
    --sv-ink: #1c1c1e; --sv-muted: #6a6a72; --sv-line: #e2e2e6;
    --sv-accent: #2d6cdf; --sv-ok: #1f8a4c; --sv-bad: #b4232c;
    color: var(--sv-ink);
}
.exp-sv h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-sv .sv-meta { color: var(--sv-muted); font-size: .92em; white-space: normal; }
.exp-sv code { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-sv label, .exp-sv label * { white-space: normal; }

.sv-summary { display: flex; flex-wrap: wrap; gap: 1.4rem 2rem; align-items: baseline; padding: 0 0 1.2rem 0; }
.sv-summary > span { display: flex; flex-direction: column; gap: .1rem; }
.sv-summary b { font-size: 1.6rem; line-height: 1.1; }
.sv-summary .sv-label { font-size: .78em; text-transform: uppercase; letter-spacing: .05em; color: var(--sv-muted); font-weight: 600; }

.sv-figure {
    display: flex; flex-direction: column; gap: .1rem; text-decoration: none; color: inherit;
    border-bottom: 2px solid transparent; padding-bottom: .1rem;
}
.sv-figure b { font-size: 1.6rem; line-height: 1.1; }
.sv-figure:hover { border-bottom-color: var(--sv-accent); }
.sv-figure.is-bad b { color: var(--sv-bad); }
.sv-figure.is-bad:hover { border-bottom-color: var(--sv-bad); }

.sv-kinds { display: flex; flex-wrap: wrap; gap: .4rem; padding: 0 0 1rem 0; }
.sv-kind {
    display: inline-flex; gap: .4rem; align-items: baseline; text-decoration: none; color: inherit;
    border: 1px solid var(--sv-line); border-left-width: 4px; border-radius: 6px;
    padding: .3rem .6rem; background: #fff; font-size: .92em;
}
.sv-kind:hover { border-color: #9a9aa0; }
.sv-kind.is-broken { border-left-color: var(--sv-bad); }
.sv-kind.is-odd { border-left-color: var(--sv-ok); }
.sv-kind.is-note { border-left-color: #c9c9ce; }
.sv-kind.is-current { background: #eef4ff; border-color: var(--sv-accent); }
.sv-kind b { font-weight: 700; }

.sv-fix { margin: .35rem 0 .1rem 0; padding: .5rem .7rem; background: #fbfbfc; border: 1px solid var(--sv-line); border-radius: 6px; }
.sv-fix-head { font-size: .78em; text-transform: uppercase; letter-spacing: .05em; color: var(--sv-muted); font-weight: 700; display: block; padding-bottom: .25rem; }
.sv-fix ol { margin: 0; padding-left: 1.1rem; }
.sv-fix li { padding: .1rem 0; white-space: normal; }

.sv-tabs { display: flex; flex-wrap: wrap; gap: .5rem; padding: 0 0 1rem 0; }
.sv-tab {
    display: flex; flex-direction: column; gap: .1rem; flex: 1 1 14rem; min-width: 0;
    border: 1px solid var(--sv-line); border-radius: 8px; padding: .6rem .8rem;
    background: #fff; text-decoration: none; color: inherit;
}
.sv-tab:hover { border-color: #9a9aa0; }
.sv-tab.is-current { border-color: var(--sv-accent); border-width: 2px; padding: calc(.6rem - 1px) calc(.8rem - 1px); }
.sv-tab .sv-tab-title { font-weight: 600; }
.sv-tab .sv-tab-count { color: var(--sv-accent); font-weight: 700; }

.sv-card { border: 1px solid var(--sv-line); border-radius: 8px; background: #fff; padding: 1rem 1.1rem; margin: 0 0 1.4rem 0; min-width: 0; }
.sv-card > .sv-meta { display: block; padding-bottom: .8rem; }

.sv-find { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; padding: 0 0 .9rem 0; }
.sv-find input[type=text] { flex: 1 1 16rem; min-width: 0; border: 1px solid var(--sv-line); border-radius: 6px; padding: .4rem .55rem; font: inherit; box-sizing: border-box; }
.sv-btn { border: 1px solid var(--sv-line); background: #f4f4f5; border-radius: 6px; padding: .35rem .8rem; font: inherit; cursor: pointer; color: inherit; text-decoration: none; }
.sv-btn:hover { border-color: #9a9aa0; }

.sv-scroll { overflow-x: auto; max-width: 100%; }
.sv-table { width: 100%; border-collapse: collapse; font-size: .92em; }
.sv-table th { text-align: left; font-size: .78em; text-transform: uppercase; letter-spacing: .04em; color: var(--sv-muted); border-bottom: 1px solid var(--sv-line); padding: .35rem .5rem; white-space: nowrap; }
.sv-table td { border-bottom: 1px solid #f2f2f4; padding: .4rem .5rem; vertical-align: top; }
.sv-table tr:hover td { background: #f8f9fb; }
.sv-table .sv-value { font-weight: 600; }
.sv-table .sv-where { color: var(--sv-muted); font-size: .9em; }
.sv-dot { display: inline-block; width: .55rem; height: .55rem; border-radius: 50%; background: var(--sv-ok); vertical-align: .05em; }
.sv-dot.is-bad { background: var(--sv-bad); }
.sv-dot.is-empty { background: #c9c9ce; }

.sv-pages { display: flex; flex-wrap: wrap; gap: .35rem; padding: .9rem 0 0 0; clear: both; }
.sv-pages a, .sv-pages span { border: 1px solid var(--sv-line); border-radius: 6px; padding: .2rem .55rem; text-decoration: none; color: inherit; font-size: .9em; }
.sv-pages .is-current { background: var(--sv-accent); border-color: var(--sv-accent); color: #fff; font-weight: 600; }
</style>
{/literal}

<div class="context-block exp-sv">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Extension point survey'|i18n( 'design/admin/setup/rad/survey' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'The RAD tools page lists the points somebody thought to write down. This one lists what is actually here: read off disk on every request, so an extension installed this morning is in it this afternoon. Nothing below is a list kept by hand, and nothing below can go stale.'|i18n( 'design/admin/setup/rad/survey' )}</p>
</div>

<div class="sv-summary">
    <span><b>{$survey_counts.total}</b><span class="sv-label">{'extension points found'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.ini}</b><span class="sv-label">{'ini files read'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.settings}</b><span class="sv-label">{'settings naming a class'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.views}</b><span class="sv-label">{'module views'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.contracts}</b><span class="sv-label">{'contracts to implement'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.aliases}</b><span class="sv-label">{'take an alias instead'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
{if $survey_health.broken|gt( 0 )}
    <a class="sv-figure is-bad" href={$survey_problems_url|ezurl}><b>{$survey_health.broken}</b><span class="sv-label">{'configured and cannot work'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
{if $survey_counts.broken|gt( 0 )}
    <a class="sv-figure is-bad" href={$survey_unknown_url|ezurl}><b>{$survey_counts.broken}</b><span class="sv-label">{'look like a class and are not one'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
</div>

<div class="sv-tabs">
{foreach $survey_tabs as $sv_tab}
    <a class="sv-tab{if $sv_tab.current} is-current{/if}" href={$sv_tab.url|ezurl}>
        <span class="sv-tab-title">{$sv_tab.title|i18n( 'design/admin/setup/rad/survey' )|wash}</span>
        <span class="sv-tab-count">{$sv_tab.count}</span>
    </a>
{/foreach}
</div>

<div class="sv-card">
<h2>{$survey_section.title|i18n( 'design/admin/setup/rad/survey' )|wash}</h2>
<span class="sv-meta">{$survey_section.what|i18n( 'design/admin/setup/rad/survey' )|wash}</span>

<form class="sv-find" method="get" action={concat( '/setup/radsurvey/(show)/', $survey_show )|ezurl}>
    <input type="text" name="find" value="{$survey_find|wash}" placeholder="{'Narrow the list'|i18n( 'design/admin/setup/rad/survey' )}" autocomplete="off" />
    <input class="sv-btn" type="submit" value="{'Find'|i18n( 'design/admin/setup/rad/survey' )}" />
{if ne( $survey_find, '' )}
    <a class="sv-btn" href={$survey_reset|ezurl}>{'Clear'|i18n( 'design/admin/setup/rad/survey' )}</a>
{/if}
    <span class="sv-meta">{'Showing %shown of %total.'|i18n( 'design/admin/setup/rad/survey',, hash( '%shown', $survey_shown, '%total', $survey_total ) )}</span>
</form>

{if eq( $survey_show, 'problems' )}
<div class="sv-kinds">
    <a class="sv-kind{if eq( $survey_kind, '' )} is-current{/if}" href={$survey_problems_url|ezurl}>
        <b>{'Everything'|i18n( 'design/admin/setup/rad/survey' )}</b>
    </a>
{foreach $survey_health_kinds as $sv_kind}
    <a class="sv-kind is-{$sv_kind.severity|wash}{if eq( $survey_kind, $sv_kind.key )} is-current{/if}" href={$sv_kind.url|ezurl}>
        <b>{$sv_kind.count}</b> <span>{$sv_kind.check|i18n( 'design/admin/setup/rad/survey' )|wash}</span>
    </a>
{/foreach}
</div>

<p class="sv-meta">
{if eq( $survey_check, 'classes' )}
    {'Every class this installation declares has been loaded in a child process. That is the only way to find one php refuses, and a class php refuses ends the request that touches it rather than merely failing.'|i18n( 'design/admin/setup/rad/survey' )}
{else}
    <a class="sv-btn" href={$survey_check_url|ezurl}>{'Also load every class'|i18n( 'design/admin/setup/rad/survey' )}</a>
    {'Takes a few seconds. It loads every class this installation declares, in a child process so that one php refuses cannot take this page with it - which is how the last fault of that kind was found.'|i18n( 'design/admin/setup/rad/survey' )}
{/if}
</p>
{/if}

{if $survey_total|eq( 0 )}
<p class="sv-meta">{'Nothing here matches that.'|i18n( 'design/admin/setup/rad/survey' )}</p>
{else}
<div class="sv-scroll">
<table class="sv-table">
{if eq( $survey_show, 'problems' )}
<tr>
    <th>&nbsp;</th>
    <th>{'What is wrong'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'How much'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'Which one'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'What it means'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'Where'|i18n( 'design/admin/setup/rad/survey' )}</th>
</tr>
{else}
<tr>
    <th>&nbsp;</th>
    <th>{'Where'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'Section'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'Setting'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'Value'|i18n( 'design/admin/setup/rad/survey' )}</th>
    <th>{'Declared in'|i18n( 'design/admin/setup/rad/survey' )}</th>
</tr>
{/if}
{foreach $survey_rows as $sv_row}
<tr>
    <td><span class="sv-dot{if eq( $sv_row.state, 'bad' )} is-bad{elseif eq( $sv_row.state, 'empty' )} is-empty{/if}"></span></td>
    <td><code>{$sv_row.one|wash}</code></td>
    <td><code>{$sv_row.two|wash}</code></td>
    <td><code>{$sv_row.three|wash}</code></td>
    <td class="sv-value">{if eq( $survey_show, 'problems' )}{$sv_row.four|wash}{else}<code>{$sv_row.four|wash}</code>{/if}
    {if $sv_row.fix|count|gt( 0 )}
        <div class="sv-fix">
            <span class="sv-fix-head">{'How to fix it'|i18n( 'design/admin/setup/rad/survey' )}</span>
            <ol>
            {foreach $sv_row.fix as $sv_step}<li>{$sv_step|i18n( 'design/admin/setup/rad/survey' )|wash}</li>{/foreach}
            </ol>
        </div>
    {/if}
    </td>
    <td class="sv-where"><code>{$sv_row.note|wash}</code></td>
</tr>
{/foreach}
</table>
</div>

{if $survey_pages|count|gt( 0 )}
<div class="sv-pages">
{foreach $survey_pages as $sv_page}
    {if $sv_page.current}<span class="is-current">{$sv_page.from}&ndash;{$sv_page.to}</span>
    {else}<a href={$sv_page.url|ezurl}>{$sv_page.from}&ndash;{$sv_page.to}</a>{/if}
{/foreach}
</div>
{/if}
{/if}
</div>

<div class="sv-card">
<h2>{'What the dots mean'|i18n( 'design/admin/setup/rad/survey' )}</h2>
<p class="sv-meta">
    <span class="sv-dot"></span>
    {'A class of that name is declared, and the file it is in is shown. This is a point you can replace.'|i18n( 'design/admin/setup/rad/survey' )}
</p>
<p class="sv-meta">
    <span class="sv-dot is-bad"></span>
    {'The value has a capital in it, so it is shaped like a class name, and nothing declares a class of that name. Either the registration is broken and whatever it was meant to switch on has never run, or it is an alias that happens to be capitalised.'|i18n( 'design/admin/setup/rad/survey' )}
</p>
<p class="sv-meta">
    <span class="sv-dot is-empty"></span>
    {'One lower case word, so it is an alias that something else turns into a class - or, in the other lists, nothing is set and nothing implements it yet. An empty repository directory list is normal; an interface nothing implements is a point nobody has taken up.'|i18n( 'design/admin/setup/rad/survey' )}
</p>
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <a class="sv-btn" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/survey' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
