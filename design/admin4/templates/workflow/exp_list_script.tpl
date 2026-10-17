{* The search, the filter and the selection of the redesigned lists (rss/list, workflow/grouplist,
   class/grouplist, trigger/list), in one place per module so that each page keeps working on its own.

   Works on what the page marks: the lists (data-list) with one card per row (data-search, data-<flag>="1|0"), a
   search field (input[type=search]), filter chips (input[data-filter], value "" for all, "flag" for data-flag="1",
   "!flag" for data-flag="0"), "select all" boxes (data-select-all="<checkbox name>") and selection counts
   (.a4-workflow-selected-count[data-for="<checkbox name>"]). Without javascript none of the controls are shown, every
   card is, and every button still submits its form.

   The code is page/exp_list_script.js, configured by the hidden element below.

   Parameters: text_shown ("%shown of %count ... shown"), text_all ("%count ..."), text_selected ("%count selected."). *}
{default text_shown='' text_all='' text_selected=''}
<div hidden data-exp-list data-exp-list-scope=".a4-workflow-lists" data-exp-list-prefix="a4-workflow" data-exp-list-filter="1" data-text-shown="{$text_shown|wash}" data-text-all="{$text_all|wash}" data-text-selected="{$text_selected|wash}"></div>
{ezscript( array( 'page/exp_list_script.js' ) )}
