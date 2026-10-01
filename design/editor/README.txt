The editor design
=================

The design of the editor siteaccess: the administration for content editing,
for people who edit content but do not administer the site. It is admin3 in
every way - the siteaccess sets SiteDesign=editor with admin3, admin2 and admin
after it in AdditionalSiteDesignList, so every template it does not have comes
from admin3 and the designs admin3 falls back to.

What makes it an editor's administration is in the siteaccess settings, not in
templates (settings/siteaccess/editor, written by the installer):

  menu.ini            [TopAdminMenu] Tabs: Dashboard, Content structure, Media,
                      Users, Store, Tags, Newsletter - no Layouts, Setup,
                      Design, Git, Export or CIE tab
  site.ini            [SiteAccessRules]: the modules behind those tabs (setup,
                      visual, explayouts_ui, explayouts_ui_api, git_manager,
                      xrowextract, bccie) answer 404 in this siteaccess
                      ExtensionSettingsSiteAccess=admin: extensions' admin
                      settings apply here too
  toolbar.ini         no developer toolbar (Clear cache, Quick settings)
  admininterface.ini  the node view tabs without Layouts

The convention is to serve it on its own host, edit.<your domain>, beside the
site and the admin.

Templates go into templates/ or override/templates/ only where the editor's
administration has to look or behave differently from admin3; keep them few.
