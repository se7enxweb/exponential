{* The description of the browse page when several items of a list are copied at once (content/action, CopyNode with
   more than one node): the names of the items, from $browse.content.name_list. The same file is in design/admin and
   design/admin4. *}
{def $copy_names = $browse.content.name_list|implode( ', ' )}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Choose location for the copy of <%object_name>'|i18n( 'design/admin/content/browse_copy_node',, hash( '%object_name', $copy_names ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="block">
<p>{'Choose a new location for the copy of <%object_name> using the radio buttons then click "Select".'|i18n( 'design/admin/content/browse_copy_node',, hash( '%object_name', $copy_names ) )|wash}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/content/browse_copy_node' )}</p>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>

{undef $copy_names}
