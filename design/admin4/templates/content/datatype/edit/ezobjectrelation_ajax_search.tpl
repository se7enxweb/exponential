{run-once}
{if ezmodule( 'ezjscore' )}{* Make sure ezjscore is installed before we try to enable the search code *}
<input type="hidden" id="ezobjectrelation-search-published-text" value="{'Yes'|i18n( 'design/standard/content/datatype' )}" />
<p id="ezobjectrelation-search-empty-result-text" class="hide ezobjectrelation-search-empty-result">{'No results were found when searching for "%1"'|i18n("design/standard/content/search",,array( '--search-string--' ))}</p>
<p id="ezobjectrelation-search-failed-text" class="hide">{'The search failed. Try again.'|i18n( 'design/standard/content/search' )|wash}</p>
{* ezajaxrelations_jquery.js runs on Exponential UI (exp::core, exp::io), not on jQuery *}
{ezscript_require( array( 'exp::core::shared', 'exp::io', 'ezajaxrelations_jquery.js' ) )}
{/if}
{/run-once}