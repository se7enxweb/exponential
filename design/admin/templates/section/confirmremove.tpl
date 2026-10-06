{* The confirmation before sections are removed (section/list, Remove selected).

   Lists the sections that will be removed with what removal means, and the ones that cannot be removed with the
   reason for each (its objects, the policies and role assignments that name it) and what to do about it. Only
   ConfirmRemoveSectionButton removes; Cancel and Back return to the list. The form, its action and the button
   names are those the view has always read.

   The same file is in design/admin and design/admin4. The reasons come from the view (section_overview); without
   it the page says only that a section is in use. Guide: doc/guides/sections.md *}
{include uri='design:section/exp_style.tpl'}

{def $allowed_sections_count = $allowed_sections|count()
     $unallowed_count = $unallowed_sections|count()
     $overview = first_set( $section_overview, false() )}

<div class="context-block exp-sections">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $allowed_sections_count}{'Remove sections?'|i18n( 'design/admin/section/confirmremove' )}{else}{'The sections cannot be removed'|i18n( 'design/admin/section/confirmremove' )}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A section can only be removed when no object is in it, drafts and archived objects included, and no policy or role assignment is limited to it.'|i18n( 'design/admin/section/confirmremove' )}</p>

{if $allowed_sections_count}
<section class="exp-section" aria-labelledby="remove-allowed-title">
<div class="exp-feedback is-bad">
    <h2 class="exp-h2" id="remove-allowed-title">{'The following sections will be removed'|i18n( 'design/admin/section/confirmremove' )} ({$allowed_sections_count})</h2>
    <ul>
    {foreach $allowed_sections as $section}
        <li><a href={concat( 'section/view/', $section.id )|ezurl}>{$section.name|wash}</a>{if $section.identifier|ne( '' )} <code>{$section.identifier|wash}</code>{/if} &middot; {'ID %id'|i18n( 'design/admin/section/confirmremove',, hash( '%id', $section.id ) )}</li>
    {/foreach}
    </ul>
    <p><strong>{'Warning'|i18n( 'design/admin/section/confirmremove' )}:</strong> {'Removal cannot be undone. Templates, override conditions and settings that name a removed section by its ID or identifier stop matching, and a section/assign policy that offers it (NewSection) keeps a value that points nowhere.'|i18n( 'design/admin/section/confirmremove' )}</p>
</div>
</section>
{/if}

{if $unallowed_count}
<section class="exp-section" aria-labelledby="remove-blocked-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="remove-blocked-title">{if $allowed_sections_count}{'The following sections cannot be removed because they are either assigned to objects or used in role and policy limitations'|i18n( 'design/admin/section/confirmremove' )}{else}{'None of the selected sections can be removed because they are either assigned to objects or used in role and policy limitations'|i18n( 'design/admin/section/confirmremove' )}{/if}</h2>
</div>
<ul class="exp-secs">
{foreach $unallowed_sections as $section}
    {def $info = cond( $overview, first_set( $overview[$section.id], false() ), false() )}
    <li class="exp-sec">
        <div class="exp-sec-title">
            <h3><a href={concat( 'section/view/', $section.id )|ezurl}>{$section.name|wash}</a></h3>
            {if $section.identifier|ne( '' )}<code class="exp-title-key">{$section.identifier|wash}</code>{/if}
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/section/confirmremove',, hash( '%id', $section.id ) )}</span>
        </div>
        {if $info}
        <ul class="exp-plain exp-reasons">
            {if $info.objects|gt(0)}<li>{'Holds %objects objects (%published published, %drafts drafts, %archived archived). Move them to another section with Assign content first.'|i18n( 'design/admin/section/confirmremove',, hash( '%objects', $info.objects, '%published', $info.published, '%drafts', $info.drafts, '%archived', $info.archived ) )}</li>{/if}
            {if $info.policy_count|gt(0)}<li>{'%count policies are limited to it. Remove the section from their limitations in these roles first:'|i18n( 'design/admin/section/confirmremove',, hash( '%count', $info.policy_count ) )} {foreach $info.roles as $role}<a href={concat( '/role/view/', $role.id )|ezurl}>{$role.name|wash}</a>{delimiter}, {/delimiter}{/foreach}</li>{/if}
            {if $info.assignment_count|gt(0)}<li>{'%count role assignments are limited to it. Remove those assignments first.'|i18n( 'design/admin/section/confirmremove',, hash( '%count', $info.assignment_count ) )}</li>{/if}
        </ul>
        {/if}
    </li>
    {undef $info}
{/foreach}
</ul>
</section>
{/if}

<form action={$module.functions.list.uri|ezurl} method="post" name="SectionRemove">
<div class="exp-bottombar">
    <div class="exp-actions">
    {if $allowed_sections_count}
        <button type="submit" class="exp-btn exp-btn-danger" name="ConfirmRemoveSectionButton" value="1">{'Remove these sections'|i18n( 'design/admin/section/confirmremove' )}</button>
        <button type="submit" class="exp-btn" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/section/confirmremove' )}</button>
    {else}
        <button type="submit" class="exp-btn exp-btn-primary" name="BackButton" value="1">{'Back to the sections'|i18n( 'design/admin/section/confirmremove' )}</button>
    {/if}
    </div>
    {if $allowed_sections_count}<p class="exp-meta">{'Cancel keeps every section.'|i18n( 'design/admin/section/confirmremove' )}</p>{/if}
</div>
</form>

</div></div></div>
</div>
