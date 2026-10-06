{* The description at the top of the content browser when the destination of an RSS import is chosen (rss/edit_import, DestinationBrowse).

   The same file is in design/admin and design/admin4, in the look of the RSS pages. Choosing an item and OK hand it
   back to the edit form; Cancel returns to it unchanged. Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}

<div class="context-block exp-lists exp-rss">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Choose a destination for RSS import'|i18n( 'design/admin/rss/browse_destination' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-info">
<p>{'Use the radio buttons to choose a destination location for RSS import then click "OK".'|i18n( 'design/admin/rss/browse_destination' )}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/rss/browse_destination' )}</p>
<p>{'Every new item of the feed becomes an object directly below this location.'|i18n( 'design/admin/rss/browse_destination' )}</p>
</div>

</div></div></div>
</div>
