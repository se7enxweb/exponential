{* The push state of a publish or rollback (a row of exp_static_run) as a badge. Variables: run, dash (optional: "-"
   for a run without a push state, else nothing). *}
{switch match=$run.push_state}
{case match='pushed'}<span class="a4-badge is-ok">{'pushed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='pending'}<span class="a4-badge is-warn">{'push pending'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='disabled'}<span class="a4-badge is-info">{'not pushed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='no-remote'}<span class="a4-badge is-info">{'no remote'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case}{if is_set( $dash )}-{/if}{/case}
{/switch}
