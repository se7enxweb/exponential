{* The description at the top of the content browser when a source of an RSS export is chosen (rss/edit_export, SourceBrowse_<n>).

   The same file is in design/admin and design/admin4, in the look of the RSS pages. Choosing an item and OK hand it
   back to the edit form; Cancel returns to it unchanged. Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}

<div class="context-block exp-lists exp-rss">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Choose source for RSS export'|i18n( 'design/admin/rss/browse_source' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-info">
<p>{'Use the radio buttons to choose the item that you want to export using RSS then click "OK".'|i18n( 'design/admin/rss/browse_source' )}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/rss/browse_source' )}</p>
<p>{'The feed lists the newest objects of the chosen class published below this location; with Subnodes ticked, also those deeper down.'|i18n( 'design/admin/rss/browse_source' )}</p>
</div>

</div></div></div>
</div>
