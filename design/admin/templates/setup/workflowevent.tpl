{* The workflow event wizard.

   A workflow event is the hardest thing here to write from memory: four
   separate decisions, none of them discoverable from the code you are writing.
   All four are made on this page, and the triggers come from this
   installation's own operation definitions rather than from a list. *}

{literal}
<style type="text/css">
.exp-wfe {
    --wfe-ink: #1c1c1e; --wfe-muted: #6a6a72; --wfe-line: #e2e2e6;
    --wfe-accent: #2d6cdf; --wfe-ok: #1f8a4c; --wfe-bad: #b4232c;
    --wfe-gap: 1.6rem;
    color: var(--wfe-ink);
}
.exp-wfe h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-wfe h3 { font-size: .95rem; margin: 0 0 .3rem 0; }
.exp-wfe .wfe-meta { color: var(--wfe-muted); font-size: .92em; white-space: normal; }
.exp-wfe code, .exp-wfe pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-wfe pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-wfe label, .exp-wfe label * { white-space: normal; }

.wfe-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--wfe-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.wfe-note.is-ok { border-left-color: var(--wfe-ok); }
.wfe-note.is-bad { border-left-color: var(--wfe-bad); }
.wfe-note.is-info { border-left-color: var(--wfe-accent); }

.wfe-card {
    border: 1px solid var(--wfe-line); border-radius: 8px; background: #fff;
    padding: 1rem 1.1rem; margin: 0 0 var(--wfe-gap) 0; min-width: 0;
}
.wfe-card > .wfe-meta { display: block; padding-bottom: .9rem; }
.wfe-grid { display: flex; flex-wrap: wrap; gap: var(--wfe-gap); align-items: flex-start; }
.wfe-col { flex: 1 1 24rem; min-width: 0; }

.wfe-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.wfe-field label {
    font-size: .82em; color: var(--wfe-muted); text-transform: uppercase;
    letter-spacing: .04em; font-weight: 600;
}
.wfe-field input[type=text], .wfe-field select {
    border: 1px solid var(--wfe-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.wfe-field .wfe-hint { font-size: .85em; color: var(--wfe-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.wfe-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.wfe-row > .wfe-field { flex: 1 1 9rem; }

/* The trigger matrix. */
.wfe-module { padding-bottom: 1rem; }
.wfe-module h3 { text-transform: capitalize; }
.wfe-matrix { width: 100%; border-collapse: collapse; font-size: .93em; }
.wfe-matrix th, .wfe-matrix td { padding: .3rem .5rem; border-bottom: 1px solid #f0f0f3; text-align: left; }
.wfe-matrix th { color: var(--wfe-muted); font-weight: 600; font-size: .85em; text-transform: uppercase; }
.wfe-matrix td.wfe-tick { width: 5.5rem; text-align: center; }
.wfe-matrix tr.is-chosen { background: #f2f7ff; }
.wfe-matrix .wfe-none { color: var(--wfe-muted); }

/* The statuses. */
.wfe-status { display: flex; gap: .6rem; align-items: flex-start; padding: .5rem .55rem; border-radius: 6px; }
.wfe-status:hover { background: #f6f7f9; }
.wfe-status input { margin-top: .25rem; flex: 0 0 auto; }
.wfe-status > span { flex: 1 1 auto; min-width: 0; }
.wfe-status code { display: block; font-weight: 600; font-size: .9em; }
.wfe-status .wfe-meta { display: block; }

/* The settings rows. */
.wfe-attrs { width: 100%; border-collapse: collapse; font-size: .93em; }
.wfe-attrs th, .wfe-attrs td { padding: .35rem .4rem; border-bottom: 1px solid #f0f0f3; text-align: left; vertical-align: top; }
.wfe-attrs th { color: var(--wfe-muted); font-weight: 600; font-size: .82em; text-transform: uppercase; }
.wfe-attrs input[type=text], .wfe-attrs select {
    border: 1px solid var(--wfe-line); border-radius: 5px; padding: .3rem .4rem;
    font: inherit; width: 100%; box-sizing: border-box; background: #fff;
}
.wfe-attrs .wfe-col-used { color: var(--wfe-muted); white-space: nowrap; font-size: .88em; }
.wfe-left { display: flex; gap: 1.2rem; padding-top: .6rem; }

.wfe-summary { display: flex; flex-wrap: wrap; gap: 1.4rem; padding-bottom: .9rem; }
.wfe-summary b { display: block; font-size: 1.3rem; line-height: 1.2; }
.wfe-triggers { display: flex; flex-wrap: wrap; gap: .35rem; padding-bottom: .6rem; }
.wfe-chip {
    border: 1px solid var(--wfe-accent); color: var(--wfe-accent); border-radius: 4px;
    padding: .05rem .45rem; font-size: .85em; white-space: nowrap;
}

.wfe-file { border: 1px solid var(--wfe-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.wfe-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.wfe-file-head:hover { background: #f6f7f9; }
.wfe-file-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--wfe-line);
    border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1;
}
.wfe-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.wfe-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--wfe-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.wfe-file.is-collapsed pre { display: none; }
.wfe-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.wfe-btn {
    border: 1px solid var(--wfe-line); background: #f4f4f5; border-radius: 6px;
    padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit;
}
.wfe-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/workflowevent'|ezurl} name="WorkflowEventWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-wfe">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Workflow event wizard'|i18n( 'design/admin/setup/rad/workflowevent' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A workflow event is a step a workflow takes when something is published, moved, removed, registered or bought. Four things have to be decided: where it may be attached, what an editor can set on it, what it answers with, and what it does. The first three are made here; the fourth is left as a method with every option written out beside it.'|i18n( 'design/admin/setup/rad/workflowevent' )}</p>
</div>

{foreach $wizard_feedback as $wfe_note}
<div class="wfe-note {if $wfe_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $wfe_note.ok}&#10003;{else}!{/if}</strong><span>{$wfe_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $wfe_problem}
<div class="wfe-note is-bad"><strong>!</strong><span>{$wfe_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="wfe-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, then clear the caches.'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="wfe-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></div>
{/if}

<div class="wfe-grid">

{* --------------------------------------------------------- what it is --- *}
<div class="wfe-col">

<div class="wfe-card">
<h2>{'What it is'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="wfe-meta">{'The event name becomes the class, the template names and the value stored against every workflow that uses it. It cannot be changed afterwards without breaking those workflows.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

<div class="wfe-row">
    <div class="wfe-field">
        <label for="wfeName">{'Extension name'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_workflow" autocomplete="off" />
    </div>
    <div class="wfe-field">
        <label for="wfeEvent">{'Event name'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeEvent" name="event" value="{$wizard_settings.event|wash}" placeholder="requireapproval" autocomplete="off" />
        <span class="wfe-hint">{'Lower case letters and digits. Becomes %class.'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%class', $wizard_class ) )}</span>
    </div>
</div>

<div class="wfe-field">
    <label for="wfeLabel">{'What editors see it called'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
    <input type="text" id="wfeLabel" name="label" value="{$wizard_settings.label|wash}" placeholder="Require approval" />
    <span class="wfe-hint">{'The name in the list when an event is added to a workflow.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>
</div>

<div class="wfe-field">
    <label for="wfeSummary">{'Summary'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
    <input type="text" id="wfeSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="wfe-row">
    <div class="wfe-field">
        <label for="wfeAuthor">{'Author'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="wfe-field">
        <label for="wfeVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="wfe-field">
        <label for="wfeVersion">{'Version'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="wfe-field">
    <label for="wfeLicence">{'Licence'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
    <select id="wfeLicence" name="licence">
    {foreach $wizard_licences as $wfe_key => $wfe_label}
        <option value="{$wfe_key|wash}"{if eq( $wizard_settings.licence, $wfe_key )} selected="selected"{/if}>{$wfe_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

{* ------------------------------------------------------ when it runs ---- *}
<div class="wfe-card">
<h2>{'When it runs'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="wfe-meta">{'Every operation on this installation that carries a trigger, read from its own definition. Before runs while the operation is still deciding, so rejecting the event stops the operation; after runs once it has happened, so rejecting it then stops the rest of the workflow but not the thing itself.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

{if $wizard_trigger_sentences|count|gt( 0 )}
<div class="wfe-triggers">
{foreach $wizard_trigger_sentences as $wfe_sentence}
    <span class="wfe-chip">{$wfe_sentence|wash}</span>
{/foreach}
</div>
{/if}

<div class="wfe-toolbar">
    <button type="button" class="wfe-btn" id="wfeTriggersNone">{'Tick none'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
    <button type="submit" class="wfe-btn" name="PreviewButton" value="1">{'Refresh preview'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
</div>

{foreach $wizard_matrix as $wfe_module}
<div class="wfe-module">
    <h3>{$wfe_module.module|wash} <span class="wfe-meta">({$wfe_module.count})</span></h3>
    <table class="wfe-matrix">
    <tr>
        <th>{'Operation'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
        <th class="wfe-tick">{'Before'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
        <th class="wfe-tick">{'After'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    </tr>
    {foreach $wfe_module.rows as $wfe_row}
    <tr{if or( $wfe_row.cells.before.chosen, $wfe_row.cells.after.chosen )} class="is-chosen"{/if}>
        <td><code>{$wfe_module.module|wash}/{$wfe_row.operation|wash}</code></td>
        {foreach array( 'before', 'after' ) as $wfe_point}
        <td class="wfe-tick">
        {if $wfe_row.cells[$wfe_point].available}
            <input type="checkbox" name="Triggers[]" value="{$wfe_row.cells[$wfe_point].key|wash}"{if $wfe_row.cells[$wfe_point].chosen} checked="checked"{/if} />
        {else}
            <span class="wfe-none">&mdash;</span>
        {/if}
        </td>
        {/foreach}
    </tr>
    {/foreach}
    </table>
</div>
{/foreach}
</div>

</div>

{* --------------------------------------------- settings and statuses ---- *}
<div class="wfe-col">

<div class="wfe-card">
<h2>{'What an editor can set'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="wfe-meta">{'An event keeps its settings in four integer columns and five text ones, and the kernel gives those columns no meaning. Name them here and the generated class gets a constant per setting, a field in the edit form, and the code that reads it back.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

<table class="wfe-attrs">
<tr>
    <th>{'Name'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th>{'Label'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th>{'Kind'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th>{'Values'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th>{'Column'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
</tr>
{foreach $wizard_attributes as $wfe_attribute}
<tr>
    <td><input type="text" name="AttributeName[]" value="{$wfe_attribute.name|wash}" /></td>
    <td><input type="text" name="AttributeLabel[]" value="{$wfe_attribute.label|wash}" /></td>
    <td>
        <select name="AttributeType[]">
        {foreach $wizard_attribute_types as $wfe_type => $wfe_type_info}
            <option value="{$wfe_type|wash}"{if eq( $wfe_attribute.type, $wfe_type )} selected="selected"{/if}>{$wfe_type_info.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</option>
        {/foreach}
        </select>
    </td>
    <td><input type="text" name="AttributeChoices[]" value="{$wfe_attribute.choices|implode( ', ' )|wash}" placeholder="a, b, c" /></td>
    <td class="wfe-col-used"><code>{$wfe_attribute.column|wash}</code></td>
</tr>
<tr>
    <td colspan="5"><input type="text" name="AttributeHelp[]" value="{$wfe_attribute.help|wash}" placeholder="{'A line of help shown under the field'|i18n( 'design/admin/setup/rad/workflowevent' )}" /></td>
</tr>
{/foreach}

{* Three empty rows, so something can always be added without a round trip. *}
{foreach array( 1, 2, 3 ) as $wfe_spare}
<tr>
    <td><input type="text" name="AttributeName[]" value="" placeholder="minwords" /></td>
    <td><input type="text" name="AttributeLabel[]" value="" /></td>
    <td>
        <select name="AttributeType[]">
        {foreach $wizard_attribute_types as $wfe_type => $wfe_type_info}
            <option value="{$wfe_type|wash}"{if eq( $wfe_type, 'text' )} selected="selected"{/if}>{$wfe_type_info.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</option>
        {/foreach}
        </select>
    </td>
    <td><input type="text" name="AttributeChoices[]" value="" placeholder="a, b, c" /></td>
    <td class="wfe-col-used">&mdash;</td>
</tr>
<tr>
    <td colspan="5"><input type="text" name="AttributeHelp[]" value="" placeholder="{'A line of help shown under the field'|i18n( 'design/admin/setup/rad/workflowevent' )}" /></td>
</tr>
{/foreach}
</table>

<div class="wfe-left">
    <span class="wfe-meta">{'%n integer columns left'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%n', $wizard_ints_left ) )}</span>
    <span class="wfe-meta">{'%n text columns left'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%n', $wizard_texts_left ) )}</span>
</div>

<div class="wfe-meta" style="padding-top:.6rem;">
{foreach $wizard_attribute_types as $wfe_type => $wfe_type_info}
    <div><b>{$wfe_type_info.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</b> &mdash; {$wfe_type_info.description|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</div>
{/foreach}
</div>
</div>

<div class="wfe-card">
<h2>{'What it can answer'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="wfe-meta">{'Each of these changes what happens next, and several change what the kernel does rather than only what the workflow does. Tick the ones this event will use; each gets a branch in execute() with this explanation beside it.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

{foreach $wizard_statuses as $wfe_status}
<label class="wfe-status">
    <input type="checkbox" name="Statuses[]" value="{$wfe_status.key|wash}"{if $wfe_status.chosen} checked="checked"{/if}{if $wfe_status.fixed} disabled="disabled"{/if} />
    {if $wfe_status.fixed}<input type="hidden" name="Statuses[]" value="{$wfe_status.key|wash}" />{/if}
    <span>
        <code>{$wfe_status.key|wash}</code>
        <span class="wfe-meta">{$wfe_status.what|i18n( 'design/admin/setup/rad/workflowevent' )|wash}{if $wfe_status.fixed} {'Always included: an event with no way to say it is done would hang every workflow it is in.'|i18n( 'design/admin/setup/rad/workflowevent' )}{/if}</span>
    </span>
</label>
{/foreach}
</div>

<div class="wfe-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
{foreach $wizard_parts as $wfe_key => $wfe_part}
    <label class="wfe-status">
        <input type="checkbox" name="Parts[]" value="{$wfe_key|wash}"{if $wizard_settings.parts[$wfe_key]} checked="checked"{/if} />
        <span>
            <code>{$wfe_part.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</code>
            <span class="wfe-meta">{$wfe_part.description|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="wfe-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="wfe-meta">{'Add this to settings/override/site.ini.append.php, then clear the caches. The event then appears when an event is added to a workflow.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>
<pre>{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{* ---------------------------------------------------------- the files --- *}
{if $wizard_file_count|gt( 0 )}
<div class="wfe-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="wfe-meta">{$wizard_target|wash}</span>

<div class="wfe-summary">
    <span><b>{$wizard_file_count}</b><span class="wfe-meta">{'files'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
    <span><b>{$wizard_trigger_sentences|count}</b><span class="wfe-meta">{'triggers'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
    <span><b>{$wizard_attributes|count}</b><span class="wfe-meta">{'settings'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
    <span><b>{$wizard_settings.statuses|count}</b><span class="wfe-meta">{'statuses'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
</div>

<div class="wfe-toolbar">
    <button type="button" class="wfe-btn" id="wfeOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
    <button type="button" class="wfe-btn" id="wfeCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
</div>

{foreach $wizard_files as $wfe_file}
<div class="wfe-file is-collapsed">
    <div class="wfe-file-head">
        <span class="wfe-file-toggle">+</span>
        <span class="wfe-file-path">{$wfe_file.path|wash}</span>
        <span class="wfe-meta">{$wfe_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/workflowevent' )}, {$wfe_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>
    </div>
    <pre>{$wfe_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/workflowevent' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/workflowevent' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/workflowevent' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/workflowevent' )}" />
    <a class="wfe-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/workflowevent' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
</form>

{literal}
<script type="text/javascript">
( function () {
    function fold( panel, open )
    {
        var collapsed = panel.className.indexOf( 'is-collapsed' ) !== -1;
        if ( open === collapsed )
        {
            panel.className = open
                ? panel.className.replace( / ?is-collapsed/, '' )
                : panel.className + ' is-collapsed';
        }
        var toggle = panel.getElementsByTagName( 'span' )[0];
        if ( toggle ) toggle.innerHTML = open ? '&minus;' : '+';
    }

    var divs = document.getElementsByTagName( 'div' ), files = [], i;
    for ( i = 0; i < divs.length; i++ )
        if ( divs[i].className.indexOf( 'wfe-file' ) === 0 )
            files.push( divs[i] );

    for ( i = 0; i < files.length; i++ )
    {
        ( function ( panel ) {
            var head = panel.getElementsByTagName( 'div' )[0];
            if ( !head ) return;
            head.onclick = function () {
                fold( panel, panel.className.indexOf( 'is-collapsed' ) !== -1 );
                return false;
            };
        } )( files[i] );
    }

    function every( open ) { for ( var j = 0; j < files.length; j++ ) fold( files[j], open ); }

    var openAll = document.getElementById( 'wfeOpenAll' ),
        closeAll = document.getElementById( 'wfeCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };

    var none = document.getElementById( 'wfeTriggersNone' );
    if ( none )
    {
        none.onclick = function () {
            var boxes = document.getElementsByTagName( 'input' ), b;
            for ( b = 0; b < boxes.length; b++ )
                if ( boxes[b].type === 'checkbox' && boxes[b].name === 'Triggers[]' )
                    boxes[b].checked = false;
            return false;
        };
    }

    // The event name drives the class name; show it as it is typed.
    var event = document.getElementById( 'wfeEvent' );
    if ( event )
    {
        event.onkeyup = function () {
            var clean = event.value.toLowerCase().replace( /[^a-z0-9]+/g, '' ),
                hint = event.parentNode.getElementsByTagName( 'span' )[0];
            if ( hint )
                hint.innerHTML = clean === ''
                    ? 'Lower case letters and digits.'
                    : 'Lower case letters and digits. Becomes ' + clean + 'Type.';
        };
    }
} )();
</script>
{/literal}
