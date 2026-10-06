{* Assign a role with a section limitation, first step: the section (role/assign/<role>/section).

   One choice per section; OK (AssignSectionID) goes on to choosing the users and groups in the content browser,
   Cancel (AssignSectionCancelButton) back to the role. Every name is the one the view has always read (SectionID,
   AssignSectionID, AssignSectionCancelButton). The same file is in design/admin and design/admin4.
   Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

<form method="post" action={concat( '/role/assign/', $role_id, '/', $limit_ident|wash( url ) )|ezurl} class="exp-roles">
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if is_set( $role )}{'Assign <%role_name> in one section'|i18n( 'design/admin/role/assign_limited_section',, hash( '%role_name', $role.name ) )|wash}{else}{'Select section'|i18n( 'design/admin/role/assign_limited_section' )}{/if}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<ol class="exp-steps" aria-label="{'Steps'|i18n( 'design/admin/role/assign_limited_section' )}">
    <li class="is-current" aria-current="step">{'Select section'|i18n( 'design/admin/role/assign_limited_section' )}</li>
    <li>{'Users and groups'|i18n( 'design/admin/role/assign_limited_section' )}</li>
</ol>

<p class="exp-intro">{'The role will apply to the users and groups you choose next only for content in this section.'|i18n( 'design/admin/role/assign_limited_section' )}</p>

{if $section_array}
<fieldset class="exp-field">
<legend class="exp-sr">{'Select section'|i18n( 'design/admin/role/assign_limited_section' )}</legend>
<ul class="exp-radios">
{foreach $section_array as $index => $section}
    <li><label><input type="radio" name="SectionID" value="{$section.id}"{if eq( $index, 0 )} checked="checked"{/if} /> <span>{$section.name|wash} <span class="exp-muted">({'ID'|i18n( 'design/admin/role/assign_limited_section' )} {$section.id})</span></span></label></li>
{/foreach}
</ul>
</fieldset>
{else}
<p class="exp-empty">{'There are no sections on the system.'|i18n( 'design/admin/role/assign_limited_section' )}</p>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="AssignSectionID" value="1"{if not( $section_array )} disabled="disabled"{/if}>{'OK'|i18n( 'design/admin/role/assign_limited_section' )}</button>
        <button class="exp-btn" type="submit" name="AssignSectionCancelButton" value="1">{'Cancel'|i18n( 'design/admin/role/assign_limited_section' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
