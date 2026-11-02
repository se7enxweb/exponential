{* page_mainarea admin3 marker *}
{* Demo mode (EXP_DEMO_MODE=1): the banner above the page's content, inside the main area (below the fixed header);
   the pagelayouts include this file outside their cached parts *}
{include uri='design:parts/exp_demo_mode_banner.tpl'}
{* A recording of changes for a migration that records this user (Setup > Migrations > Recording) *}
{include uri='design:parts/exp_migration_recording_bar.tpl'}
{$module_result.content}
