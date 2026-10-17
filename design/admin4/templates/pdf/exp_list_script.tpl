{* "Select all on this page" and the selection count of the PDF export list (pdf/list). Without javascript the box
   is not shown and every button still submits the form. The search, the filter and the order are links and a form,
   so they need no script. Parameter: text_selected ("%count selected.").
   design/admin keeps its own copy with the script inline; here the code is page/exp_list_script.js. *}
{default text_selected=''}
<div hidden data-exp-list data-exp-list-scope=".a4-page" data-exp-list-prefix="exp" data-exp-list-filter="0" data-text-selected="{$text_selected|wash}"></div>
{ezscript( array( 'page/exp_list_script.js' ) )}
{/default}
