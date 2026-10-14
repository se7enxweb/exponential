{* The extension point survey.

   Not a written list: what is actually on this installation, read off disk on
   every request. *}


<div class="context-block a4-page a4-setup-survey">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Extension point survey'|i18n( 'design/admin/setup/rad/survey' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'The RAD tools page lists the points somebody thought to write down. This one lists what is actually here: read off disk on every request, so an extension installed this morning is in it this afternoon. Nothing below is a list kept by hand, and nothing below can go stale.'|i18n( 'design/admin/setup/rad/survey' )}</p>
</div>

<div class="a4-setup-survey-summary">
    <span><b>{$survey_counts.total}</b><span class="a4-setup-survey-label">{'extension points found'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.ini}</b><span class="a4-setup-survey-label">{'ini files read'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.settings}</b><span class="a4-setup-survey-label">{'settings naming a class'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.views}</b><span class="a4-setup-survey-label">{'module views'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <a class="a4-setup-survey-figure" href={'/setup/radsurvey/(show)/runnables'|ezurl}><b>{$survey_counts.runnables}</b><span class="a4-setup-survey-label">{'commands, cronjob parts and views to re-implement'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
    <a class="a4-setup-survey-figure" href={'/setup/radsurvey/(show)/inicommand'|ezurl}><b>{$survey_counts.ini_actions}</b><span class="a4-setup-survey-label">{'exp:ini actions, and %providers scope providers'|i18n( 'design/admin/setup/rad/survey',, hash( '%providers', $survey_counts.ini_scope_providers ) )}</span></a>
    <a class="a4-setup-survey-figure" href={'/setup/radsurvey/(show)/registries'|ezurl}><b>{$survey_counts.registries}</b><span class="a4-setup-survey-label">{'registry entries: %columns subitems columns, %jobs content job types'|i18n( 'design/admin/setup/rad/survey',, hash( '%columns', $survey_counts.registry_subitemscolumns, '%jobs', $survey_counts.content_job_types ) )}</span></a>
    <span><b>{$survey_counts.contracts}</b><span class="a4-setup-survey-label">{'contracts to implement'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
    <span><b>{$survey_counts.aliases}</b><span class="a4-setup-survey-label">{'take an alias instead'|i18n( 'design/admin/setup/rad/survey' )}</span></span>
{if $survey_health.broken|gt( 0 )}
    <a class="a4-setup-survey-figure is-bad" href={$survey_problems_url|ezurl}><b>{$survey_health.broken}</b><span class="a4-setup-survey-label">{'configured and cannot work'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
{if $survey_counts.runnable_broken|gt( 0 )}
    <a class="a4-setup-survey-figure is-bad" href={'/setup/radsurvey/(show)/runnables'|ezurl}><b>{$survey_counts.runnable_broken}</b><span class="a4-setup-survey-label">{'re-implementations that cannot work'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
{if $survey_counts.ini_command_broken|gt( 0 )}
    <a class="a4-setup-survey-figure is-bad" href={'/setup/radsurvey/(show)/inicommand'|ezurl}><b>{$survey_counts.ini_command_broken}</b><span class="a4-setup-survey-label">{'exp:ini registrations that cannot work'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
{if $survey_counts.registries_broken|gt( 0 )}
    <a class="a4-setup-survey-figure is-bad" href={'/setup/radsurvey/(show)/registries'|ezurl}><b>{$survey_counts.registries_broken}</b><span class="a4-setup-survey-label">{'registry entries that cannot work'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
{if $survey_counts.broken|gt( 0 )}
    <a class="a4-setup-survey-figure is-bad" href={$survey_unknown_url|ezurl}><b>{$survey_counts.broken}</b><span class="a4-setup-survey-label">{'look like a class and are not one'|i18n( 'design/admin/setup/rad/survey' )}</span></a>
{/if}
</div>

<div class="a4-setup-survey-tabs">
{foreach $survey_tabs as $sv_tab}
    <a class="a4-setup-survey-tab{if $sv_tab.current} is-current{/if}" href={$sv_tab.url|ezurl}>
        <span class="a4-setup-survey-tab-title">{$sv_tab.title|i18n( 'design/admin/setup/rad/survey' )|wash}</span>
        <span class="a4-setup-survey-tab-count">{$sv_tab.count}</span>
    </a>
{/foreach}
</div>

<div class="a4-card">
<h2>{$survey_section.title|i18n( 'design/admin/setup/rad/survey' )|wash}</h2>
<span class="a4-meta">{$survey_section.what|i18n( 'design/admin/setup/rad/survey' )|wash}</span>
{if is_set( $survey_section.also )}<span class="a4-meta">{$survey_section.also|i18n( 'design/admin/setup/rad/survey' )|wash}</span>{/if}

<form class="a4-searchrow" method="get" action={concat( '/setup/radsurvey/(show)/', $survey_show )|ezurl}>
    <input type="text" class="a4-input" name="find" value="{$survey_find|wash}" placeholder="{'Narrow the list'|i18n( 'design/admin/setup/rad/survey' )}" autocomplete="off" />
    <input class="a4-btn" type="submit" value="{'Find'|i18n( 'design/admin/setup/rad/survey' )}" />
{if ne( $survey_find, '' )}
    <a class="a4-btn" href={$survey_reset|ezurl}>{'Clear'|i18n( 'design/admin/setup/rad/survey' )}</a>
{/if}
    <span class="a4-meta">{'Showing %shown of %total.'|i18n( 'design/admin/setup/rad/survey',, hash( '%shown', $survey_shown, '%total', $survey_total ) )}</span>
</form>

{if eq( $survey_show, 'problems' )}
<div class="a4-setup-survey-kinds">
    <a class="a4-setup-survey-kind{if eq( $survey_kind, '' )} is-current{/if}" href={$survey_problems_url|ezurl}>
        <b>{'Everything'|i18n( 'design/admin/setup/rad/survey' )}</b>
    </a>
{foreach $survey_health_kinds as $sv_kind}
    <a class="a4-setup-survey-kind is-{$sv_kind.severity|wash}{if eq( $survey_kind, $sv_kind.key )} is-current{/if}" href={$sv_kind.url|ezurl}>
        <b>{$sv_kind.count}</b> <span>{$sv_kind.label|wash}</span>
    </a>
{/foreach}
</div>

<p class="a4-meta">
{if eq( $survey_check, 'classes' )}
    {'Every class this installation declares has been loaded in a child process. That is the only way to find one php refuses, and a class php refuses ends the request that touches it rather than merely failing.'|i18n( 'design/admin/setup/rad/survey' )}
{else}
    <a class="a4-btn" href={$survey_check_url|ezurl}>{'Also load every class'|i18n( 'design/admin/setup/rad/survey' )}</a>
    {'Takes a few seconds. It loads every class this installation declares, in a child process so that one php refuses cannot take this page with it - which is how the last fault of that kind was found.'|i18n( 'design/admin/setup/rad/survey' )}
{/if}
</p>
{/if}

{if $survey_total|eq( 0 )}
<p class="a4-meta">{'Nothing here matches that.'|i18n( 'design/admin/setup/rad/survey' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table is-compact">
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
    <td><span class="a4-dot{if eq( $sv_row.state, 'bad' )} is-bad{elseif eq( $sv_row.state, 'empty' )}{else} is-ok{/if}"></span></td>
    <td><code>{$sv_row.one|wash}</code></td>
    <td><code>{$sv_row.two|wash}</code></td>
    <td><code>{$sv_row.three|wash}</code></td>
    <td class="a4-setup-survey-value">{if eq( $survey_show, 'problems' )}{$sv_row.four|wash}{else}<code>{$sv_row.four|wash}</code>{/if}
    {if $sv_row.fix|count|gt( 0 )}
        <div class="a4-setup-survey-fix">
            <span class="a4-setup-survey-fix-head">{'How to fix it'|i18n( 'design/admin/setup/rad/survey' )}</span>
            <ol>
            {foreach $sv_row.fix as $sv_step}<li>{$sv_step|wash}</li>{/foreach}
            </ol>
        </div>
    {/if}
    </td>
    <td class="a4-setup-survey-where"><code>{$sv_row.note|wash}</code></td>
</tr>
{/foreach}
</table>
</div>

{if $survey_pages|count|gt( 0 )}
<div class="a4-tabs a4-setup-survey-pages">
{foreach $survey_pages as $sv_page}
    {if $sv_page.current}<span class="is-current">{$sv_page.from}&ndash;{$sv_page.to}</span>
    {else}<a href={$sv_page.url|ezurl}>{$sv_page.from}&ndash;{$sv_page.to}</a>{/if}
{/foreach}
</div>
{/if}
{/if}
</div>

<div class="a4-card">
<h2>{'What the dots mean'|i18n( 'design/admin/setup/rad/survey' )}</h2>
<p class="a4-meta">
    <span class="a4-dot is-ok"></span>
    {'A class of that name is declared, and the file it is in is shown. This is a point you can replace.'|i18n( 'design/admin/setup/rad/survey' )}
</p>
<p class="a4-meta">
    <span class="a4-dot is-bad"></span>
    {'The value has a capital in it, so it is shaped like a class name, and nothing declares a class of that name. Either the registration is broken and whatever it was meant to switch on has never run, or it is an alias that happens to be capitalised.'|i18n( 'design/admin/setup/rad/survey' )}
</p>
<p class="a4-meta">
    <span class="a4-dot"></span>
    {'One lower case word, so it is an alias that something else turns into a class - or, in the other lists, nothing is set and nothing implements it yet. An empty repository directory list is normal; an interface nothing implements is a point nobody has taken up.'|i18n( 'design/admin/setup/rad/survey' )}
</p>
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <a class="a4-btn" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/survey' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
