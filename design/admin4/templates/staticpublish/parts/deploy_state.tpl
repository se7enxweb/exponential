{* The last deploy of a target in one badge, for the dashboard. Variables: deploy (expStaticAdmin::deployState():
   state none, failed, pushed, pending, unchanged or not-pushed). *}
{switch match=$deploy.state}
{case match='pushed'}<span class="a4-badge is-ok">{'pushed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='pending'}<span class="a4-badge is-warn">{'push pending'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='failed'}<span class="a4-badge is-bad">{'failed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='unchanged'}<span class="a4-badge is-info">{'nothing changed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case match='none'}<span class="a4-muted">{'never'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{case}<span class="a4-badge is-info">{'not pushed'|i18n( 'design/admin/staticpublish' )}</span>{/case}
{/switch}
