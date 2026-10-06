{* Create or edit a section (section/edit/<id>, 0 for a new one).

   One form: name, identifier and navigation part, each with what it is used for; an error names the field it is
   about; for an existing section, what a change affects. The field names (Name, SectionIdentifier,
   NavigationPartIdentifier, SectionID), the buttons (StoreButton, CancelButton) and the action are those the view
   has always read.

   The same file is in design/admin and design/admin4. This is an edit view, which admin4 draws without its main
   card; .exp-standalone gives the page its own. Works without javascript; the script fills in the identifier of a
   new section from its name until the identifier is typed in. Guide: doc/guides/sections.md *}
{include uri='design:section/exp_style.tpl'}

{def $is_new = first_set( $section_is_new, eq( $section.id, 0 ) )
     $usage = first_set( $section_usage, false() )
     $parts = first_set( $navigation_parts, fetch( 'content', 'navigation_parts' ) )
     $error = first_set( $error_message, false() )
     $form_id = cond( $is_new, 0, $section.id )}

<form method="post" action={concat( '/section/edit/', $form_id, '/' )|ezurl} class="exp-sections exp-standalone" aria-labelledby="section-edit-title" novalidate="novalidate" id="section-edit-form">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" id="section-edit-title">{if $is_new}{'New section'|i18n( 'design/admin/section/edit' )}{else}{'Edit section %name'|i18n( 'design/admin/section/edit',, hash( '%name', $section.name ) )|wash}{/if}</h1>
{if $is_new|not}<span class="exp-meta">{'ID %id'|i18n( 'design/admin/section/edit',, hash( '%id', $section.id ) )}</span>{/if}
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A section groups content for permissions, navigation and designs. Every object belongs to exactly one section.'|i18n( 'design/admin/section/edit' )}</p>

{if $error}
<div class="exp-feedback is-bad" role="alert" id="section-edit-error" tabindex="-1">
    <p><strong>{'The section was not saved.'|i18n( 'design/admin/section/edit' )}</strong> <a href="#SectionIdentifier">{$error|wash}</a></p>
</div>
{/if}

<input type="hidden" name="SectionID" value="{$form_id}" />

<div class="exp-panel">
<div class="exp-form-fields">

    <div class="exp-field">
        <label for="sectionName">{'Name'|i18n( 'design/admin/section/edit' )}</label>
        <input id="sectionName" type="text" name="Name" value="{$section.name|wash}" required="required" maxlength="255" aria-describedby="sectionName-help" autocomplete="off" />
        <span class="exp-help" id="sectionName-help">{'Shown in the section lists, in role limitations and in the section choice of an object.'|i18n( 'design/admin/section/edit' )}</span>
    </div>

    <div class="exp-field">
        <label for="SectionIdentifier">{'Identifier'|i18n( 'design/admin/section/edit' )}</label>
        <input id="SectionIdentifier" type="text" name="SectionIdentifier" value="{$section.identifier|wash}" required="required" maxlength="255"
               pattern="[A-Za-z][A-Za-z0-9_]*" spellcheck="false" autocomplete="off"
               aria-describedby="{if $error}SectionIdentifier-error {/if}SectionIdentifier-help"{if $error} aria-invalid="true"{/if} />
        {if $error}<span class="exp-field-error" id="SectionIdentifier-error">{$error|wash}</span>{/if}
        <span class="exp-help" id="SectionIdentifier-help">{'Letters, digits and _, starting with a letter, unique among the sections. Templates, fetch functions and override conditions (section_identifier) can name the section by it.'|i18n( 'design/admin/section/edit' )}</span>
    </div>

    <div class="exp-field">
        <label for="NavigationPartIdentifier">{'Navigation part'|i18n( 'design/admin/section/edit' )}</label>
        <select id="NavigationPartIdentifier" name="NavigationPartIdentifier" aria-describedby="NavigationPartIdentifier-help">
        {foreach $parts as $part}
            <option value="{$part.identifier|wash}"{if eq( $section.navigation_part_identifier, $part.identifier )} selected="selected"{/if}>{$part.name|wash} ({$part.identifier|wash})</option>
        {/foreach}
        </select>
        <span class="exp-help" id="NavigationPartIdentifier-help">{'The top menu tab of the administration interface that is active while an object of this section is viewed or edited. The parts are listed in menu.ini [NavigationPart].'|i18n( 'design/admin/section/edit' )}</span>
    </div>

</div>
</div>

{if $usage}
<div class="exp-feedback is-info">
    <p><strong>{'What a change affects'|i18n( 'design/admin/section/edit' )}</strong></p>
    <p>{'This section holds %published published objects (%objects with drafts and archived ones), and %policies policies in %roles roles and %assignments role assignments are limited to it.'|i18n( 'design/admin/section/edit',, hash( '%published', $usage.published, '%objects', $usage.objects, '%policies', $usage.policy_count, '%roles', $usage.role_count, '%assignments', $usage.assignment_count ) )}</p>
    <ul>
        <li>{'Renaming changes no permission: roles name the section by its ID.'|i18n( 'design/admin/section/edit' )}</li>
        <li>{'A new identifier stops templates, fetches and override conditions that use the old one from matching.'|i18n( 'design/admin/section/edit' )}</li>
        <li>{'A new navigation part changes the active top menu tab for all objects of the section.'|i18n( 'design/admin/section/edit' )}</li>
        <li>{'Saving clears the view cache of the objects in the section.'|i18n( 'design/admin/section/edit' )}</li>
    </ul>
</div>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="StoreButton" value="1">{if $is_new}{'Create section'|i18n( 'design/admin/section/edit' )}{else}{'Save section'|i18n( 'design/admin/section/edit' )}{/if}</button>
        <button type="submit" class="exp-btn" name="CancelButton" value="1" formnovalidate="formnovalidate">{'Cancel'|i18n( 'design/admin/section/edit' )}</button>
    </div>
    <p class="exp-meta">{'Cancel goes back to the section list without saving.'|i18n( 'design/admin/section/edit' )}</p>
</div>

</div></div></div>
</div>

</form>

<script type="text/javascript">
var expSectionEditNew = {if $is_new}true{else}false{/if};
var expSectionEditError = {if $error}true{else}false{/if};
var expSectionEditText = {ldelim}
    pattern: '{'Identifier should consist of letters, numbers or \'_\' with letter prefix.'|i18n( 'design/admin/section/edit' )|wash( javascript )}',
    empty: '{'Identifier can not be empty'|i18n( 'design/admin/section/edit' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var nameEl = document.getElementById( 'sectionName' );
    var idEl = document.getElementById( 'SectionIdentifier' );
    if ( !nameEl || !idEl ) return;
    if ( expSectionEditError ) {
        var box = document.getElementById( 'section-edit-error' );
        idEl.focus(); idEl.select();
        if ( box ) box.setAttribute( 'aria-live', 'assertive' );
    } else {
        nameEl.focus(); nameEl.select();
    }
    // A new section's identifier follows its name ("Members area" -> "members_area") until it is typed in.
    var typed = !expSectionEditNew || idEl.value !== '';
    idEl.addEventListener( 'input', function () { typed = idEl.value !== ''; idEl.setCustomValidity( '' ); } );
    nameEl.addEventListener( 'input', function () {
        if ( typed ) return;
        var s = nameEl.value.toLowerCase();
        if ( s.normalize ) s = s.normalize( 'NFD' ).replace( /[̀-ͯ]/g, '' );
        s = s.replace( /ß/g, 'ss' ).replace( /[^a-z0-9_]+/g, '_' ).replace( /^[^a-z]+/, '' ).replace( /_+$/, '' );
        idEl.value = s;
    } );
    // The same checks as the server, before the round trip; the server still has the last word.
    document.getElementById( 'section-edit-form' ).addEventListener( 'submit', function ( e ) {
        var store = e.submitter ? e.submitter.name === 'StoreButton' : true;
        if ( !store ) return;
        var v = idEl.value.trim();
        var msg = v === '' ? expSectionEditText.empty : ( /^[A-Za-z][A-Za-z0-9_]*$/.test( v ) ? '' : expSectionEditText.pattern );
        idEl.setCustomValidity( msg );
        if ( msg ) { e.preventDefault(); idEl.setAttribute( 'aria-invalid', 'true' ); idEl.reportValidity(); idEl.focus(); }
    } );
})();
{/literal}
</script>
