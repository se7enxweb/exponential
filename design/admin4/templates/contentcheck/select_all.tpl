{* The master box of a result list of the content checks (shared: unreferenced, storage, missingfiles), to put in the
   first header cell of an a4-table that carries data-a4-select="1". Each row box carries data-a4-select-item="1"
   (and checked="checked" when select_all). The box is hidden without JavaScript (contentcheck_select.js shows it);
   then select_count.tpl's links do the same on the server.

     <th scope="col" class="tight">{include uri='design:contentcheck/select_all.tpl' select_all=$select_all}</th>

   Variables: select_all (bool: every box of the page ticked, (selectall)/1). The same file is in design/admin and
   design/admin4. *}
<label class="a4-sr-only" for="cc-select-all">{'Select all on this page'|i18n( 'kernel/contentcheck' )}</label><input type="checkbox" id="cc-select-all" data-a4-select-all="1" hidden="hidden"{if $select_all} checked="checked"{/if} />
