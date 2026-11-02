{* The state of a publish or rollback (a row of exp_static_run) as a badge, and its push state. Variables: run,
   state_only (optional: without the push state). *}
{switch match=$run.state}
{case match='done'}<span class="a4-badge is-ok">{if eq( $run.kind, 'rollback' )}{'rolled back'|i18n( 'design/admin/staticpublish' )}{else}{'published'|i18n( 'design/admin/staticpublish' )}{/if}</span>{/case}
{case match='unchanged'}<span class="a4-badge is-info">{'nothing changed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='failed'}<span class="a4-badge is-bad">{'failed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case}<span class="a4-badge is-info">{$run.state|wash}</span>{/case}
{/switch}
{if is_set( $state_only )|not}{include uri='design:staticpublish/parts/push_state.tpl' run=$run}{/if}
