{* The main area of admin3 run as its own design (SiteDesign=admin3): the demo mode banner (EXP_DEMO_MODE=1,
   doc/eng-us/bc/6.0/demo-mode.md) above the page's content, then what design/standard draws. The pagelayout includes
   this file outside its cached parts, so the banner follows the environment of each request. The banner is the
   partial of design/admin (admin3 falls back to it); when admin4 comes first, its own main area draws the banner. *}
{include uri='design:parts/exp_demo_mode_banner.tpl'}
{include uri="design:page_warning.tpl"}

{$module_result.content}
