{* Editing several objects at once.

   The attribute rows are drawn by the same include the single object editor
   uses, so a datatype looks and behaves here exactly as it does there. What
   this template adds is the shape around them: one section per content class,
   one panel per object, and a report of what happened to each when publishing
   several of them did not all go the same way.

   The frame is the one of the pages content/action leads to (the removal and
   move confirmations): a context block with the title in the box header, the
   content in the box, and the buttons in the control bar under it, the main
   one first and the one that goes back last. The editor's own column is not
   drawn here (ui_context edit), so this template brings the main content box
   itself, as the single object editor does. Everything inside it is built from
   the shared .a4-* components; what only this page needs is in
   stylesheets/modules/content.css (.a4-content-multiedit-*). *}

<form method="post" action={"content/multiedit"|ezurl} enctype="multipart/form-data" name="multiedit">

<div id="maincontent" role="main" tabindex="-1" class="a4-content-multiedit-main"><div id="maincontent-design" class="float-break"><div id="fix">
<div class="context-block a4-content-multiedit">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h2 class="context-title">{'Edit several items'|i18n('design/admin/content/multiedit')}</h2>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{* ── Making several new ones ───────────────────────────────────────────── *}
{if $multiedit_create_classes}
    {if $multiedit_error|begins_with('create-')}
    <div class="a4-notice is-warn" role="alert">
        {if $multiedit_error|eq('create-permission')}{'You may not create content of that type here.'|i18n('design/admin/content/multiedit')}
        {elseif $multiedit_error|eq('create-count')}{'Choose how many to create.'|i18n('design/admin/content/multiedit')}
        {elseif $multiedit_error|eq('create-class')}{'Choose what to create.'|i18n('design/admin/content/multiedit')}
        {else}{'They could not be created here.'|i18n('design/admin/content/multiedit')}
        {/if}
    </div>
    {/if}

    <p class="a4-intro">{'What would you like to create, and how many? They are made as drafts and nothing is published until you say so.'|i18n('design/admin/content/multiedit')}</p>

    <div class="a4-content-multiedit-create">
        <div class="a4-formrow">
            <label for="multiedit-create-class">{'Type'|i18n('design/admin/content/multiedit')}</label>
            <div><select id="multiedit-create-class" class="a4-input is-auto" name="MultiEditCreateClass">
            {foreach $multiedit_create_classes as $creatable}
                <option value="{$creatable.id}">{$creatable.name|wash}</option>
            {/foreach}
            </select></div>
        </div>
        <div class="a4-formrow">
            <label for="multiedit-create-count">{'How many'|i18n('design/admin/content/multiedit')}</label>
            <div><input type="number" id="multiedit-create-count" class="a4-input is-auto" name="MultiEditCreateCount" value="3" min="1" max="{$multiedit_max}" size="4" aria-describedby="multiedit-create-max" />
            <p class="a4-help" id="multiedit-create-max">{'At most %max at a time.'|i18n('design/admin/content/multiedit','',hash('%max',$multiedit_max))|wash}</p></div>
        </div>
    </div>

    <input type="hidden" name="MultiEditCreateParent" value="{$multiedit_create_parent}" />
    <input type="hidden" name="MultiEditReturnURI" value="{$multiedit_return_uri|wash}" />

{else}

{* ── Nothing to do ─────────────────────────────────────────────────────── *}
{if $multiedit_error|eq('nothing-selected')}
    <p class="a4-empty">{'Nothing was selected. Tick some items in a list or a search result and choose "Edit selected" from the More actions menu.'|i18n('design/admin/content/multiedit')}</p>
{elseif $multiedit_error|eq('nothing-editable')}
    <p class="a4-empty">{'None of the selected items can be edited.'|i18n('design/admin/content/multiedit')}</p>
    {* The reasons are worked out either way; when everything is refused they
       are the only thing on the page worth reading, so they are shown here as
       well as beside a form that did open. *}
    {if $multiedit_refused|count|gt(0)}
    {include uri='design:content/multiedit_refused.tpl' refused=$multiedit_refused}
    {/if}
{else}

{* ── What happened when publishing ─────────────────────────────────────── *}
{if $multiedit_published}
<section class="a4-section a4-content-multiedit-report" aria-labelledby="multiedit-result-title">
    <div class="a4-section-head"><h3 class="a4-h2" id="multiedit-result-title">{'Result'|i18n('design/admin/content/multiedit')}</h3>
        <p>{'Items waiting for approval have been sent into their workflow and are not published yet.'|i18n('design/admin/content/multiedit')}</p></div>
    <table class="list" cellspacing="0">
    <tr>
        <th scope="col">{'Item'|i18n('design/admin/content/multiedit')}</th>
        <th scope="col">{'Result'|i18n('design/admin/content/multiedit')}</th>
    </tr>
    {foreach $multiedit_published as $result sequence array( 'bglight', 'bgdark' ) as $row_class}
    <tr class="{$row_class}">
        <td>{$result.name|wash}</td>
        <td>{if $result.state|eq('published')}<span class="a4-badge is-ok">{'published'|i18n('design/admin/content/multiedit')}</span>
            {elseif $result.state|eq('pending')}<span class="a4-badge is-warn">{'waiting for approval'|i18n('design/admin/content/multiedit')}</span>
            {else}<span class="a4-badge is-bad">{'could not be published'|i18n('design/admin/content/multiedit')}</span>
            {/if}</td>
    </tr>
    {/foreach}
    </table>
</section>
{/if}

{* ── What could not be opened ──────────────────────────────────────────── *}
{if $multiedit_refused|count|gt(0)}
{include uri='design:content/multiedit_refused.tpl' refused=$multiedit_refused title='Left out of this form'|i18n('design/admin/content/multiedit')}
{/if}

{* ── Not everything validated ──────────────────────────────────────────── *}
{if $multiedit_all_valid|not}
<div class="a4-notice is-warn" role="alert">
    <strong>{'Nothing was published'|i18n('design/admin/content/multiedit')}:</strong>
    {'some items have fields that need attention. They are marked below; everything typed has been kept as a draft.'|i18n('design/admin/content/multiedit')}
</div>
{/if}

<div class="a4-toolbar">
    <p class="a4-meta">
        {'%count items, in %language. Each one is published separately when you press Publish.'|i18n('design/admin/content/multiedit','',hash('%count',$multiedit_count,'%language',$multiedit_language))}
    </p>
    {* Several items open at once is a wall; several closed is a list you can
       find your way around. Nothing here changes what is submitted - a closed
       panel still posts its fields. *}
    <div class="a4-actions">
        {if $multiedit_autosave_interval|gt(0)}<em id="multiedit-autosave" class="a4-content-multiedit-autosave" role="status"></em>{/if}
        <input class="a4-btn a4-btn-small" type="button" id="multiedit-expand" value="{'Expand all'|i18n('design/admin/content/multiedit')}" title="{'Open every item.'|i18n('design/admin/content/multiedit')|wash}" />
        <input class="a4-btn a4-btn-small" type="button" id="multiedit-collapse" value="{'Collapse all'|i18n('design/admin/content/multiedit')}" title="{'Close every item. Closed items are still saved and published; nothing is left out.'|i18n('design/admin/content/multiedit')|wash}" />
    </div>
</div>

{* ── The objects, grouped by class ─────────────────────────────────────── *}
{foreach $multiedit_groups as $group}
<section class="a4-section a4-content-multiedit-group">
    <div class="a4-section-head"><h3 class="a4-h2">{$group.name|wash} <span class="a4-meta">({$group.objects|count})</span></h3></div>

    {foreach $group.objects as $entry}
    <details class="a4-fold a4-content-multiedit-object{if $multiedit_validation[$entry.object_id]} is-bad a4-content-multiedit-invalid{/if}"{if or($multiedit_validation[$entry.object_id],$group.objects|count|le(3))} open="open"{/if}>
        <summary>
            <span class="a4-content-multiedit-name">{$entry.object.name|wash}</span>
            <span class="a4-meta">{$entry.language|wash} &middot; {'version %version'|i18n( 'design/admin/content/multiedit',, hash( '%version', $entry.version.version ) )}</span>
        </summary>
        <div class="a4-fold-body a4-content-multiedit-body">
            {* Which fields stopped this one, said here rather than only marked.
               With several objects on the page, "something is wrong somewhere"
               is not a message anybody can act on. *}
            {if $multiedit_validation[$entry.object_id]}
            <div class="a4-notice is-warn">
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
</section>
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
            document.querySelectorAll( 'details.a4-content-multiedit-object' ) );
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
        if ( panel.className.indexOf( 'a4-content-multiedit-invalid' ) !== -1 )
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
        place.className = 'a4-content-multiedit-autosave as-' + state;
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

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $multiedit_create_classes}
    {* Still choosing what to make: only one button makes sense yet. *}
    <input class="button" type="submit" name="MultiEditCreateButton" value="{'Create them'|i18n('design/admin/content/multiedit')}" title="{'Make this many drafts and open them all for editing.'|i18n('design/admin/content/multiedit')|wash}" />
    {elseif $multiedit_error|not}
    <input class="button" type="submit" name="MultiPublishButton" value="{'Publish all'|i18n('design/admin/content/multiedit')}" title="{'Publish every item in this form. Each is published separately.'|i18n('design/admin/content/multiedit')|wash}" />
    <input class="button" type="submit" name="MultiStoreButton" value="{'Save drafts'|i18n('design/admin/content/multiedit')}" title="{'Keep what has been typed without publishing anything.'|i18n('design/admin/content/multiedit')|wash}" />
    {/if}
    <input class="button" type="submit" name="MultiDiscardButton" value="{'Discard'|i18n('design/admin/content/multiedit')}" title="{'Throw away the drafts this form opened and go back.'|i18n('design/admin/content/multiedit')|wash}" />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
</div></div></div>

</form>
