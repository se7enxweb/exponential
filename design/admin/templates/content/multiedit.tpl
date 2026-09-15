{* Editing several objects at once.

   The attribute rows are drawn by the same include the single object editor
   uses, so a datatype looks and behaves here exactly as it does there. What
   this template adds is the shape around them: one section per content class,
   one panel per object, and a report of what happened to each when publishing
   several of them did not all go the same way. *}
{literal}<style>
.multiedit-note { color: #555; margin: 0 0 1rem; }
.multiedit-group { margin-bottom: 1.5rem; }
.multiedit-group > h2 { font-size: 1.1em; margin: 0 0 .5rem; }
.multiedit-object { border: 1px solid #d8d8d8; border-radius: 4px; margin-bottom: .75rem; background: #fff; }
.multiedit-object > summary { padding: .6rem .75rem; cursor: pointer; font-weight: bold; list-style: revert; }
.multiedit-object > summary small { font-weight: normal; color: #777; margin-left: .5rem; }
.multiedit-object[open] > summary { border-bottom: 1px solid #e8e8e8; }
.multiedit-body { padding: .75rem; }
.multiedit-invalid { border-color: #c0392b; }
.multiedit-invalid > summary { background: #fdf0ee; color: #a5281b; }
.multiedit-report { border: 1px solid #d8d8d8; border-radius: 4px; padding: .75rem; margin-bottom: 1rem; background: #fafafa; }
.multiedit-report li { margin: .2rem 0; }
.multiedit-state-published { color: #2e7d32; }
.multiedit-state-pending { color: #b26a00; }
.multiedit-state-failed { color: #c0392b; }
.multiedit-refused { border: 1px solid #e0cf9a; background: #fdf8e6; border-radius: 4px; padding: .6rem .75rem; margin-bottom: 1rem; }
.multiedit-controls { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
.multiedit-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.multiedit-toolbar .multiedit-note { margin: 0; }
.multiedit-toggles { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
.multiedit-autosave { font-style: normal; font-size: .9em; color: #777; min-width: 12em; }
.multiedit-autosave.as-saving { color: #b26a00; }
.multiedit-autosave.as-success { color: #2e7d32; }
.multiedit-autosave.as-error { color: #c0392b; }
@media (max-width: 700px) {
    .multiedit-body .block label { display: block; }
    .multiedit-controls .button { flex: 1 1 auto; }
    .multiedit-toolbar { justify-content: flex-start; }
    .multiedit-toggles { width: 100%; }
    .multiedit-toggles .button { flex: 1 1 auto; }
}
</style>{/literal}

<form method="post" action={"content/multiedit"|ezurl} enctype="multipart/form-data" name="multiedit">

<div class="box-header">
<h1 class="context-title">{'Edit several items'|i18n('design/admin/content/multiedit')}</h1>
<div class="header-mainline"></div>
</div>

<div class="box-content">
<div class="context-attributes">

{* ── Making several new ones ───────────────────────────────────────────── *}
{if $multiedit_create_classes}
    {if $multiedit_error|begins_with('create-')}
    <div class="multiedit-refused">
        {if $multiedit_error|eq('create-permission')}{'You may not create content of that type here.'|i18n('design/admin/content/multiedit')}
        {elseif $multiedit_error|eq('create-count')}{'Choose how many to create.'|i18n('design/admin/content/multiedit')}
        {elseif $multiedit_error|eq('create-class')}{'Choose what to create.'|i18n('design/admin/content/multiedit')}
        {else}{'They could not be created here.'|i18n('design/admin/content/multiedit')}
        {/if}
    </div>
    {/if}

    <p class="multiedit-note">{'What would you like to create, and how many? They are made as drafts and nothing is published until you say so.'|i18n('design/admin/content/multiedit')}</p>

    <div class="block">
        <label class="inline" for="multiedit-create-class">{'Type'|i18n('design/admin/content/multiedit')}:</label>
        <select id="multiedit-create-class" name="MultiEditCreateClass">
        {foreach $multiedit_create_classes as $creatable}
            <option value="{$creatable.id}">{$creatable.name|wash}</option>
        {/foreach}
        </select>

        <label class="inline" for="multiedit-create-count">{'How many'|i18n('design/admin/content/multiedit')}:</label>
        <input type="number" id="multiedit-create-count" name="MultiEditCreateCount" value="3" min="1" max="{$multiedit_max}" size="4" title="{'At most %max at a time.'|i18n('design/admin/content/multiedit','',hash('%max',$multiedit_max))|wash}" />
    </div>

    <input type="hidden" name="MultiEditCreateParent" value="{$multiedit_create_parent}" />
    <input type="hidden" name="MultiEditReturnURI" value="{$multiedit_return_uri|wash}" />

{else}

{* ── Nothing to do ─────────────────────────────────────────────────────── *}
{if $multiedit_error|eq('nothing-selected')}
    <p class="multiedit-note">{'Nothing was selected. Tick some items in a list or a search result and choose "Edit selected" from the More actions menu.'|i18n('design/admin/content/multiedit')}</p>
{elseif $multiedit_error|eq('nothing-editable')}
    <p class="multiedit-note">{'None of the selected items can be edited.'|i18n('design/admin/content/multiedit')}</p>
    {* The reasons are worked out either way; when everything is refused they
       are the only thing on the page worth reading, so they are shown here as
       well as beside a form that did open. *}
    {if $multiedit_refused|count|gt(0)}
    <div class="multiedit-refused">
        <ul>
        {foreach $multiedit_refused as $refusedID => $refused}
            <li>{$refused.name|wash} &mdash;
                {if $refused.reason|eq('no-permission')}{'you may not edit this item'|i18n('design/admin/content/multiedit')}
                {elseif $refused.reason|eq('gone')}{'no longer exists'|i18n('design/admin/content/multiedit')}
                {elseif $refused.reason|eq('no-draft')}{'a draft could not be opened, somebody may be editing it'|i18n('design/admin/content/multiedit')}
                {else}{'cannot be edited'|i18n('design/admin/content/multiedit')}
                {/if}
            </li>
        {/foreach}
        </ul>
    </div>
    {/if}
{else}

{* ── What happened when publishing ─────────────────────────────────────── *}
{if $multiedit_published}
<div class="multiedit-report">
    <h2>{'Result'|i18n('design/admin/content/multiedit')}</h2>
    <ul>
    {foreach $multiedit_published as $result}
        <li class="multiedit-state-{$result.state}">
            {$result.name|wash}
            {if $result.state|eq('published')}&mdash; {'published'|i18n('design/admin/content/multiedit')}
            {elseif $result.state|eq('pending')}&mdash; {'waiting for approval'|i18n('design/admin/content/multiedit')}
            {else}&mdash; {'could not be published'|i18n('design/admin/content/multiedit')}
            {/if}
        </li>
    {/foreach}
    </ul>
    <p class="multiedit-note">{'Items waiting for approval have been sent into their workflow and are not published yet.'|i18n('design/admin/content/multiedit')}</p>
</div>
{/if}

{* ── What could not be opened ──────────────────────────────────────────── *}
{if $multiedit_refused|count|gt(0)}
<div class="multiedit-refused">
    <strong>{'Left out of this form'|i18n('design/admin/content/multiedit')}:</strong>
    <ul>
    {foreach $multiedit_refused as $refusedID => $refused}
        <li>{$refused.name|wash} &mdash;
            {if $refused.reason|eq('no-permission')}{'you may not edit this item'|i18n('design/admin/content/multiedit')}
            {elseif $refused.reason|eq('gone')}{'no longer exists'|i18n('design/admin/content/multiedit')}
            {elseif $refused.reason|eq('no-draft')}{'a draft could not be opened, somebody may be editing it'|i18n('design/admin/content/multiedit')}
            {else}{'cannot be edited'|i18n('design/admin/content/multiedit')}
            {/if}
        </li>
    {/foreach}
    </ul>
</div>
{/if}

{* ── Not everything validated ──────────────────────────────────────────── *}
{if $multiedit_all_valid|not}
<div class="multiedit-refused">
    <strong>{'Nothing was published'|i18n('design/admin/content/multiedit')}:</strong>
    {'some items have fields that need attention. They are marked below; everything typed has been kept as a draft.'|i18n('design/admin/content/multiedit')}
</div>
{/if}

<div class="multiedit-toolbar">
    <p class="multiedit-note">
        {'%count items, in %language. Each one is published separately when you press Publish.'|i18n('design/admin/content/multiedit','',hash('%count',$multiedit_count,'%language',$multiedit_language))}
    </p>
    {* Several items open at once is a wall; several closed is a list you can
       find your way around. Nothing here changes what is submitted - a closed
       panel still posts its fields. *}
    <div class="multiedit-toggles">
        {if $multiedit_autosave_interval|gt(0)}<em id="multiedit-autosave" class="multiedit-autosave"></em>{/if}
        <input class="button" type="button" id="multiedit-expand" value="{'Expand all'|i18n('design/admin/content/multiedit')}" title="{'Open every item.'|i18n('design/admin/content/multiedit')|wash}" />
        <input class="button" type="button" id="multiedit-collapse" value="{'Collapse all'|i18n('design/admin/content/multiedit')}" title="{'Close every item. Closed items are still saved and published; nothing is left out.'|i18n('design/admin/content/multiedit')|wash}" />
    </div>
</div>

{* ── The objects, grouped by class ─────────────────────────────────────── *}
{foreach $multiedit_groups as $group}
<div class="multiedit-group">
    <h2>{$group.name|wash} <small>({$group.objects|count})</small></h2>

    {foreach $group.objects as $entry}
    <details class="multiedit-object{if $multiedit_validation[$entry.object_id]} multiedit-invalid{/if}"{if or($multiedit_validation[$entry.object_id],$group.objects|count|le(3))} open="open"{/if}>
        <summary>
            {$entry.object.name|wash}
            <small>{$entry.language|wash} &middot; {'version'|i18n('design/admin/content/multiedit')} {$entry.version.version}</small>
        </summary>
        <div class="multiedit-body">
            {* Which fields stopped this one, said here rather than only marked.
               With several objects on the page, "something is wrong somewhere"
               is not a message anybody can act on. *}
            {if $multiedit_validation[$entry.object_id]}
            <div class="multiedit-refused">
                <strong>{'Needs attention before this item can be published'|i18n('design/admin/content/multiedit')}:</strong>
                <ul>
                {foreach $multiedit_validation[$entry.object_id].attributes as $invalid}
                    <li>{$invalid.name|wash}{if $invalid.description} &mdash; {$invalid.description|wash}{/if}</li>
                {/foreach}
                </ul>
            </div>
            {/if}

            {* The same include the single object editor uses, given the same
               variables content/attribute_edit gives it. The grouped data map
               is what the admin override draws from; the flat list is passed
               too, for the standard design and for override templates that
               expect it. *}
            {include uri='design:content/edit_attribute.tpl'
                     content_attributes=$entry.attributes
                     content_attributes_grouped_data_map=$entry.grouped
                     from_content_attributes_grouped_data_map=array()
                     is_translating_content=false()
                     content_language=$entry.language
                     object=$entry.object
                     attribute_base=$attribute_base
                     view_parameters=array()}
        </div>
    </details>
    {/foreach}
</div>
{/foreach}

{* The drafts this form is working on, carried back so that pressing a button
   twice does not stack a new version each time. *}
{foreach $multiedit_drafts as $draft}
<input type="hidden" name="MultiEditDraft[{$draft.object_id}]" value="{$draft.version}" />
<input type="hidden" name="MultiEditObjectIDArray[]" value="{$draft.object_id}" />
{/foreach}
<input type="hidden" name="MultiEditReturnURI" value="{$multiedit_return_uri|wash}" />

{literal}<script type="text/javascript">
(function () {
    function panels()
    {
        return Array.prototype.slice.call(
            document.querySelectorAll( 'details.multiedit-object' ) );
    }

    function setAll( open )
    {
        panels().forEach( function ( panel ) { panel.open = open; } );
    }

    var expand   = document.getElementById( 'multiedit-expand' ),
        collapse = document.getElementById( 'multiedit-collapse' );

    if ( expand )   expand.onclick   = function () { setAll( true ); };
    if ( collapse ) collapse.onclick = function () { setAll( false ); };

    // A form that has come back with something to fix opens the items that
    // need it, whatever the reader last collapsed: being told there is a
    // problem and then having to hunt for it is the worst of both.
    panels().forEach( function ( panel ) {
        if ( panel.className.indexOf( 'multiedit-invalid' ) !== -1 )
            panel.open = true;
    } );
})();
</script>{/literal}

{if $multiedit_autosave_interval|gt(0)}
{* The settings first: the script below reads them as it runs. *}
<script type="text/javascript">
var MULTIEDIT_AUTOSAVE_INTERVAL = {$multiedit_autosave_interval};
var MULTIEDIT_AUTOSAVE_TRACK = {if $multiedit_autosave_track}true{else}false{/if};
var MULTIEDIT_AUTOSAVE_TEXTS = {ldelim}
    saving: "{'Saving drafts...'|i18n('design/admin/content/multiedit')|wash('javascript')}",
    saved:  "{'Drafts saved at %time'|i18n('design/admin/content/multiedit')|wash('javascript')}",
    error:  "{'Could not save the drafts'|i18n('design/admin/content/multiedit')|wash('javascript')}"
{rdelim};
</script>
<script type="text/javascript">
{literal}
// Autosave.
//
// ezautosave's own AutoSubmit posts to an endpoint naming one object and one
// version, which cannot say what this form holds. So the form is posted back
// to its own Save drafts action instead: one request, every draft stored, by
// the same code the button uses. The interval and the track-input preference
// come from autosave.ini, so an installation that has turned autosave off has
// it off here too.
(function () {
    var form     = document.forms.multiedit,
        place    = document.getElementById( 'multiedit-autosave' ),
        interval = MULTIEDIT_AUTOSAVE_INTERVAL,
        track    = MULTIEDIT_AUTOSAVE_TRACK,
        texts    = MULTIEDIT_AUTOSAVE_TEXTS;

    if ( !form || !place || !window.FormData || !window.fetch )
        return;

    var dirty = false, saving = false, debounce = null;

    function say( state, text )
    {
        place.className = 'multiedit-autosave as-' + state;
        place.textContent = text;
    }

    function stamp()
    {
        var now = new Date();
        return ( '0' + now.getHours() ).slice( -2 ) + ':' + ( '0' + now.getMinutes() ).slice( -2 );
    }

    function save()
    {
        if ( saving || !dirty )
            return;

        saving = true;
        say( 'saving', texts.saving );

        var data = new FormData();

        // Files are left out on purpose. A file field cannot be re-sent
        // meaningfully every few minutes, and a datatype that receives no
        // upload keeps what it already has - which is what is wanted. The
        // buttons are left out too, except the one that means "store".
        Array.prototype.forEach.call( form.elements, function ( field ) {
            if ( !field.name || field.disabled || field.type === 'file' )
                return;
            if ( field.type === 'submit' || field.type === 'button' )
                return;
            if ( ( field.type === 'checkbox' || field.type === 'radio' ) && !field.checked )
                return;
            if ( field.multiple && field.options )
            {
                Array.prototype.forEach.call( field.options, function ( option ) {
                    if ( option.selected ) data.append( field.name, option.value );
                } );
                return;
            }
            data.append( field.name, field.value );
        } );

        data.append( 'MultiStoreButton', '1' );

        fetch( form.getAttribute( 'action' ), {
            method: 'POST',
            body: data,
            credentials: 'same-origin'
        } ).then( function ( response ) {
            saving = false;
            if ( !response.ok )
                throw new Error( response.status );
            // Only now: anything typed while the request was in flight has
            // set this again, and must not be thrown away.
            dirty = false;
            say( 'success', texts.saved.replace( '%time', stamp() ) );
        } ).catch( function () {
            saving = false;
            say( 'error', texts.error );
        } );
    }

    form.addEventListener( 'input', function () { dirty = true; } );
    form.addEventListener( 'change', function () {
        dirty = true;
        if ( !track )
            return;
        // Leaving a field saves, but not on every keystroke.
        if ( debounce ) clearTimeout( debounce );
        debounce = setTimeout( save, 2000 );
    } );

    setInterval( save, interval * 1000 );

    // Pressing a real button is a save of its own; do not race it.
    Array.prototype.forEach.call( form.elements, function ( field ) {
        if ( field.type === 'submit' )
            field.addEventListener( 'click', function () { dirty = false; } );
    } );
})();
{/literal}
</script>
{/if}

{/if}
{/if}
</div>
</div>

<div class="controlbar">
<div class="block multiedit-controls">
    {if $multiedit_create_classes}
    {* Still choosing what to make: only one button makes sense yet. *}
    <input class="button" type="submit" name="MultiEditCreateButton" value="{'Create them'|i18n('design/admin/content/multiedit')}" title="{'Make this many drafts and open them all for editing.'|i18n('design/admin/content/multiedit')|wash}" />
    {elseif $multiedit_error|not}
    <input class="button" type="submit" name="MultiPublishButton" value="{'Publish all'|i18n('design/admin/content/multiedit')}" title="{'Publish every item in this form. Each is published separately.'|i18n('design/admin/content/multiedit')|wash}" />
    <input class="button" type="submit" name="MultiStoreButton" value="{'Save drafts'|i18n('design/admin/content/multiedit')}" title="{'Keep what has been typed without publishing anything.'|i18n('design/admin/content/multiedit')|wash}" />
    {/if}
    <input class="button" type="submit" name="MultiDiscardButton" value="{'Discard'|i18n('design/admin/content/multiedit')}" title="{'Throw away the drafts this form opened and go back.'|i18n('design/admin/content/multiedit')|wash}" />
</div>
</div>

</form>
