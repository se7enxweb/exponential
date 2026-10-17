{* The bulk-action bar of a result list of the content checks (shared by every check that acts on rows): the count of
   ticked rows with "Select all on this page" / "Select none" (select_count.tpl), the action buttons, and the help
   line. Placed inside the list's POST form, after the table and before the pager. The view builds its buttons with
   {set-block} so that each check keeps its own actions with the same layout:

     {set-block variable=cc_actions}<button type="submit" class="a4-btn" name="..." value="...">...</button>{/set-block}
     {include uri='design:contentcheck/bulkbar.tpl' select_all=$select_all total=$tickable_count
              page_url=concat( $page_uri, '/(offset)/', $params.offset, $page_uri_suffix ) actions=$cc_actions
              help=$help_text help_id='cc-action-help'}

   Variables: select_all, total, page_url (see select_count.tpl), actions (HTML of the buttons), help (text), help_id
   (the id the buttons' aria-describedby names). The same file is in design/admin and design/admin4. *}
<div class="a4-toolbar is-bottom">
    {include uri='design:contentcheck/select_count.tpl' select_all=$select_all total=$total page_url=$page_url}
    <div class="a4-actions">{$actions}</div>
    <p class="a4-meta" id="{$help_id|wash}">{$help|wash}</p>
</div>
