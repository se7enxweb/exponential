{* The description at the top of the content browser when a section is assigned (section/assign).

   The same file is in design/admin and design/admin4. Guide: doc/eng-us/guides/sections.md *}
{def $section = fetch( 'section', 'object', hash( 'section_id', $browse.content.section_id ) )}

<div class="context-block a4-page">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Choose start location for the <%section_name> section'|i18n( 'design/admin/section/browse_assign',, hash( '%section_name', $section.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-notice is-info">
<p>{'Use the radio buttons to select an item that should have the <%section_name> section assigned.'|i18n( 'design/admin/section/browse_assign',, hash( '%section_name', $section.name ) )|wash}</p>
</div>

{* which objects below the chosen item get the section too (eZSection::SCOPE_*); the fields belong to the browse
   form below (content/browse.tpl, id browse-form), page/section_browse_assign.js keeps the choice while browsing *}
<fieldset class="section-assign-scope" data-section-id="{$browse.content.section_id|wash}">
<legend>{'Which objects get the section'|i18n( 'design/admin/section/browse_assign' )}</legend>
<p><label><input type="radio" name="SectionScope" value="subtree" form="browse-form" checked="checked" />
    {'The chosen item and every object below it'|i18n( 'design/admin/section/browse_assign' )}</label></p>
<p><label><input type="radio" name="SectionScope" value="inherited" form="browse-form" />
    {'The chosen item and the objects below it that have its section; branches in another section keep theirs'|i18n( 'design/admin/section/browse_assign' )}</label></p>
<p><label><input type="radio" name="SectionScope" value="object" form="browse-form" />
    {'The chosen item only'|i18n( 'design/admin/section/browse_assign' )}</label></p>
</fieldset>

<div class="a4-notice is-info">
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/section/browse_assign' )}</p>
</div>

</div></div></div>
</div>

{ezscript( array( 'page/section_browse_assign.js' ) )}

{undef $section}
