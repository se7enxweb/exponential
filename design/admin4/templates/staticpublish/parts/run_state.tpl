{* The state of a publish or rollback (a row of exp_static_run) as a badge, and its push state. Variables: run. *}
{switch match=$run.state}
{case match='done'}<span class="a4-badge is-ok">{if eq( $run.kind, 'rollback' )}{'rolled back'|i18n( 'design/admin/staticpublish' )}{else}{'published'|i18n( 'design/admin/staticpublish' )}{/if}</span>{/case}
{case match='unchanged'}<span class="a4-badge is-info">{'nothing changed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='failed'}<span class="a4-badge is-bad">{'failed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case}<span class="a4-badge is-info">{$run.state|wash}</span>{/case}
{/switch}
{switch match=$run.push_state}
{case match='pushed'}<span class="a4-badge is-ok">{'pushed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='pending'}<span class="a4-badge is-warn">{'push pending'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='disabled'}<span class="a4-badge is-info">{'not pushed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='no-remote'}<span class="a4-badge is-info">{'no remote'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case}{/case}
{/switch}
