{* The demo mode banner (admin4, and admin4l through its fallback): shown on every page of the administration while
   the environment variable EXP_DEMO_MODE=1 marks the installation as a public demo (exp_demo_mode(), expDemoMode).
   It is included outside every cache-block (page_mainarea.tpl, the main zones of admin4l), so it follows the
   environment of each request, above the content and below the fixed header. The shared
   component .a4-notice.is-warn of components.css, no CSS of its own. The same file is in design/admin.
   Guide: doc/eng-us/bc/6.0/demo-mode.md. *}
{if exp_demo_mode()}
<div class="a4-notice is-warn" id="exp-demo-mode-banner" role="note" aria-labelledby="exp-demo-mode-banner-title">
    <p><strong id="exp-demo-mode-banner-title">{'Demo mode'|i18n( 'design/admin/pagelayout' )}</strong></p>
    <p>{'This installation is a public demo: anyone may know its administrator password, and administrator passwords cannot be changed or reset. Do not enter anything private here.'|i18n( 'design/admin/pagelayout' )}</p>
</div>
{/if}
