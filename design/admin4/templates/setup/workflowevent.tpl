{* The workflow event wizard.

   A workflow event is the hardest thing here to write from memory: four
   separate decisions, none of them discoverable from the code you are writing.
   All four are made on this page, and the triggers come from this
   installation's own operation definitions rather than from a list. *}


<form method="post" action={'setup/workflowevent'|ezurl} name="WorkflowEventWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Workflow event wizard'|i18n( 'design/admin/setup/rad/workflowevent' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A workflow event is a step a workflow takes when something is published, moved, removed, registered or bought. Four things have to be decided: where it may be attached, what an editor can set on it, what it answers with, and what it does. The first three are made here; the fourth is left as a method with every option written out beside it.'|i18n( 'design/admin/setup/rad/workflowevent' )}</p>
</div>

{foreach $wizard_feedback as $wfe_note}
<div class="a4-notice {if $wfe_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $wfe_note.ok}&#10003;{else}!{/if}</strong><span>{$wfe_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $wfe_problem}
<div class="a4-notice is-bad"><span>{$wfe_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-ok"><span>{'Written to %target. Switch it on with the lines below, then clear the caches.'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></div>
{/if}

<div class="a4-setup-wz-grid">

{* --------------------------------------------------------- what it is --- *}
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'What it is'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="a4-meta">{'The event name becomes the class, the template names and the value stored against every workflow that uses it. It cannot be changed afterwards without breaking those workflows.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="wfeName">{'Extension name'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_workflow" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="wfeEvent">{'Event name'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeEvent" name="event" value="{$wizard_settings.event|wash}" placeholder="requireapproval" autocomplete="off" />
        <span class="a4-help" data-empty="{'Lower case letters and digits.'|i18n( 'design/admin/setup/rad/workflowevent' )|wash}" data-named="{'Lower case letters and digits. Becomes %class.'|i18n( 'design/admin/setup/rad/workflowevent' )|wash}">{'Lower case letters and digits. Becomes %class.'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%class', $wizard_class ) )}</span>
    </div>
</div>

<div class="a4-field">
    <label for="wfeLabel">{'What editors see it called'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
    <input type="text" id="wfeLabel" name="label" value="{$wizard_settings.label|wash}" placeholder="{'Require approval'|i18n( 'design/admin/setup/rad/workflowevent' )}" />
    <span class="a4-help">{'The name in the list when an event is added to a workflow.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>
</div>

<div class="a4-field">
    <label for="wfeSummary">{'Summary'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
    <input type="text" id="wfeSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="wfeAuthor">{'Author'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="wfeVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="wfeVersion">{'Version'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
        <input type="text" id="wfeVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="wfeLicence">{'Licence'|i18n( 'design/admin/setup/rad/workflowevent' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='wfeLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>
</div>

{* ------------------------------------------------------ when it runs ---- *}
<div class="a4-card">
<h2>{'When it runs'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="a4-meta">{'Every operation on this installation that carries a trigger, read from its own definition. Before runs while the operation is still deciding, so rejecting the event stops the operation; after runs once it has happened, so rejecting it then stops the rest of the workflow but not the thing itself.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

{if $wizard_trigger_sentences|count|gt( 0 )}
<div class="a4-setup-wz-triggers">
{foreach $wizard_trigger_sentences as $wfe_sentence}
    <span class="a4-setup-wz-chip is-accent">{$wfe_sentence|wash}</span>
{/foreach}
</div>
{/if}

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="wfeTriggersNone">{'Tick none'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
    <button type="submit" class="a4-btn a4-btn-small" name="PreviewButton" value="1">{'Refresh preview'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
</div>

{foreach $wizard_matrix as $wfe_module}
<div class="a4-setup-wz-module">
    <h3>{$wfe_module.module|wash} <span class="a4-meta">({$wfe_module.count})</span></h3>
    <table class="a4-setup-wz-matrix">
    <tr>
        <th scope="col">{'Operation'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
        <th scope="col" class="a4-setup-wz-tick" id="wfe-{$wfe_module.module|wash}-before">{'Before'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
        <th scope="col" class="a4-setup-wz-tick" id="wfe-{$wfe_module.module|wash}-after">{'After'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    </tr>
    {foreach $wfe_module.rows as $wfe_row}
    <tr{if or( $wfe_row.cells.before.chosen, $wfe_row.cells.after.chosen )} class="is-chosen"{/if}>
        <td id="wfe-{$wfe_module.module|wash}-{$wfe_row.operation|wash}"><code>{$wfe_module.module|wash}/{$wfe_row.operation|wash}</code></td>
        {foreach array( 'before', 'after' ) as $wfe_point}
        <td class="a4-setup-wz-tick">
        {if $wfe_row.cells[$wfe_point].available}
            <input type="checkbox" name="Triggers[]" aria-labelledby="wfe-{$wfe_module.module|wash}-{$wfe_row.operation|wash} wfe-{$wfe_module.module|wash}-{$wfe_point}" value="{$wfe_row.cells[$wfe_point].key|wash}"{if $wfe_row.cells[$wfe_point].chosen} checked="checked"{/if} />
        {else}
            <span class="a4-setup-wz-none">&mdash;</span>
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
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'What an editor can set'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="a4-meta">{'An event keeps its settings in four integer columns and five text ones, and the kernel gives those columns no meaning. Name them here and the generated class gets a constant per setting, a field in the edit form, and the code that reads it back.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

<table class="a4-setup-wz-attrs">
<tr>
    <th scope="col" id="wfe-attr-name">{'Name'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th scope="col" id="wfe-attr-label">{'Label'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th scope="col" id="wfe-attr-kind">{'Kind'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th scope="col" id="wfe-attr-values">{'Values'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
    <th scope="col">{'Column'|i18n( 'design/admin/setup/rad/workflowevent' )}</th>
</tr>
{foreach $wizard_attributes as $wfe_attribute}
<tr>
    <td><input type="text" aria-labelledby="wfe-attr-name" name="AttributeName[]" value="{$wfe_attribute.name|wash}" /></td>
    <td><input type="text" aria-labelledby="wfe-attr-label" name="AttributeLabel[]" value="{$wfe_attribute.label|wash}" /></td>
    <td>
        <select aria-labelledby="wfe-attr-kind" name="AttributeType[]">
        {foreach $wizard_attribute_types as $wfe_type => $wfe_type_info}
            <option value="{$wfe_type|wash}"{if eq( $wfe_attribute.type, $wfe_type )} selected="selected"{/if}>{$wfe_type_info.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</option>
        {/foreach}
        </select>
    </td>
    <td><input type="text" aria-labelledby="wfe-attr-values" name="AttributeChoices[]" value="{$wfe_attribute.choices|implode( ', ' )|wash}" placeholder="a, b, c" /></td>
    <td class="a4-setup-wz-col-used"><code>{$wfe_attribute.column|wash}</code></td>
</tr>
<tr>
    <td colspan="5"><input type="text" aria-label="{'A line of help shown under the field'|i18n( 'design/admin/setup/rad/workflowevent' )|wash}" name="AttributeHelp[]" value="{$wfe_attribute.help|wash}" placeholder="{'A line of help shown under the field'|i18n( 'design/admin/setup/rad/workflowevent' )}" /></td>
</tr>
{/foreach}

{* Three empty rows, so something can always be added without a round trip. *}
{foreach array( 1, 2, 3 ) as $wfe_spare}
<tr>
    <td><input type="text" aria-labelledby="wfe-attr-name" name="AttributeName[]" value="" placeholder="minwords" /></td>
    <td><input type="text" aria-labelledby="wfe-attr-label" name="AttributeLabel[]" value="" /></td>
    <td>
        <select aria-labelledby="wfe-attr-kind" name="AttributeType[]">
        {foreach $wizard_attribute_types as $wfe_type => $wfe_type_info}
            <option value="{$wfe_type|wash}"{if eq( $wfe_type, 'text' )} selected="selected"{/if}>{$wfe_type_info.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</option>
        {/foreach}
        </select>
    </td>
    <td><input type="text" aria-labelledby="wfe-attr-values" name="AttributeChoices[]" value="" placeholder="a, b, c" /></td>
    <td class="a4-setup-wz-col-used">&mdash;</td>
</tr>
<tr>
    <td colspan="5"><input type="text" aria-label="{'A line of help shown under the field'|i18n( 'design/admin/setup/rad/workflowevent' )|wash}" name="AttributeHelp[]" value="" placeholder="{'A line of help shown under the field'|i18n( 'design/admin/setup/rad/workflowevent' )}" /></td>
</tr>
{/foreach}
</table>

<div class="a4-setup-wz-left">
    <span class="a4-meta">{'%n integer columns left'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%n', $wizard_ints_left ) )}</span>
    <span class="a4-meta">{'%n text columns left'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%n', $wizard_texts_left ) )}</span>
</div>

<div class="a4-meta a4-setup-gap">
{foreach $wizard_attribute_types as $wfe_type => $wfe_type_info}
    <div><b>{$wfe_type_info.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</b> &mdash; {$wfe_type_info.description|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</div>
{/foreach}
</div>
</div>

<div class="a4-card">
<h2>{'What it can answer'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="a4-meta">{'Each of these changes what happens next, and several change what the kernel does rather than only what the workflow does. Tick the ones this event will use; each gets a branch in execute() with this explanation beside it.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>

{foreach $wizard_statuses as $wfe_status}
<label class="a4-setup-wz-status">
    <input type="checkbox" name="Statuses[]" value="{$wfe_status.key|wash}"{if $wfe_status.chosen} checked="checked"{/if}{if $wfe_status.fixed} disabled="disabled"{/if} />
    {if $wfe_status.fixed}<input type="hidden" name="Statuses[]" value="{$wfe_status.key|wash}" />{/if}
    <span>
        <code>{$wfe_status.key|wash}</code>
        <span class="a4-meta">{$wfe_status.what|i18n( 'design/admin/setup/rad/workflowevent' )|wash}{if $wfe_status.fixed} {'Always included: an event with no way to say it is done would hang every workflow it is in.'|i18n( 'design/admin/setup/rad/workflowevent' )}{/if}</span>
    </span>
</label>
{/foreach}
</div>

<div class="a4-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
{foreach $wizard_parts as $wfe_key => $wfe_part}
    <label class="a4-setup-wz-status">
        <input type="checkbox" name="Parts[]" value="{$wfe_key|wash}"{if $wizard_settings.parts[$wfe_key]} checked="checked"{/if} />
        <span>
            <code>{$wfe_part.label|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</code>
            <span class="a4-meta">{$wfe_part.description|i18n( 'design/admin/setup/rad/workflowevent' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="a4-meta">{'Add this to settings/override/site.ini.append.php, then clear the caches. The event then appears when an event is added to a workflow.'|i18n( 'design/admin/setup/rad/workflowevent' )}</span>
<pre>{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{* ---------------------------------------------------------- the files --- *}
{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/workflowevent' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-setup-wz-summary">
    <span><b>{$wizard_file_count}</b><span class="a4-meta">{'files'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
    <span><b>{$wizard_trigger_sentences|count}</b><span class="a4-meta">{'triggers'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
    <span><b>{$wizard_attributes|count}</b><span class="a4-meta">{'settings'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
    <span><b>{$wizard_settings.statuses|count}</b><span class="a4-meta">{'statuses'|i18n( 'design/admin/setup/rad/workflowevent' )}</span></span>
</div>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="wfeOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="wfeCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/workflowevent' )}</button>
</div>

{foreach $wizard_files as $wfe_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$wfe_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/workflowevent',, hash( '%lines', $wfe_file.lines|wash, '%bytes', $wfe_file.bytes|wash ) )}</span>
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
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/workflowevent' )}</a>
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
        if ( divs[i].className.indexOf( 'a4-setup-wz-file' ) === 0 )
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
                hint.textContent = clean === ''
                    ? hint.getAttribute( 'data-empty' )
                    : hint.getAttribute( 'data-named' ).split( '%class' ).join( clean + 'Type' );
        };
    }
} )();
</script>
{/literal}
