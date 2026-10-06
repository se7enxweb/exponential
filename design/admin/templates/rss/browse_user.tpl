{* The description at the top of the content browser when the owner of the objects an RSS import creates is chosen (rss/edit_import, UserBrowse).

   The same file is in design/admin and design/admin4, in the look of the RSS pages. Choosing an item and OK hand it
   back to the edit form; Cancel returns to it unchanged. Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}

<div class="context-block exp-lists exp-rss">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Choose owner for RSS imported objects'|i18n( 'design/admin/rss/browse_user' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-info">
<p>{'Use the radio buttons to choose a user then click "OK". The user will become the owner of the objects that were imported using RSS.'|i18n( 'design/admin/rss/browse_user' )}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/rss/browse_user' )}</p>
<p>{'The owner needs no login of their own; the import creates the objects on their behalf.'|i18n( 'design/admin/rss/browse_user' )}</p>
</div>

</div></div></div>
</div>
