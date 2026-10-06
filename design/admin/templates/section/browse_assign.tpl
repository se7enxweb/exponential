{* The description at the top of the content browser when a section is assigned (section/assign).

   The same file is in design/admin and design/admin4. Guide: doc/guides/sections.md *}
{include uri='design:section/exp_style.tpl'}
{def $section = fetch( 'section', 'object', hash( 'section_id', $browse.content.section_id ) )}

<div class="context-block exp-sections">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Choose start location for the <%section_name> section'|i18n( 'design/admin/section/browse_assign',, hash( '%section_name', $section.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-info">
<p>{'Use the radio buttons to select an item that should have the <%section_name> section assigned.'|i18n( 'design/admin/section/browse_assign',, hash( '%section_name', $section.name ) )|wash}</p>
<p>{'Note that the section assignment of the sub items will also be changed.'|i18n( 'design/admin/section/browse_assign' )}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/section/browse_assign' )}</p>
</div>

</div></div></div>
</div>

{undef $section}
