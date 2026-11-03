# Extensions of a new installation (6.0.15)

This page is for anyone who installs Exponential 6.0.15 and wants one list of the extensions that come with it:
which ones are bundled, which ones a `composer install` puts on disk, which ones a new installation activates, and
where their database tables come from. It is the canonical list. Other pages link here and do not repeat it.

The list was read from the code: the kernel repository's `extension/` folder, the root `composer.json`, the
`exponential-php-floor` package (lines 1.1, 1.2 and 1.3), the setup step `kernel/setup/steps/ezstep_create_sites.php`
with `kernel/classes/expinstalldefaultextensions.php`, and the `extension_list` of every site package installer.

## How an extension gets on disk

- **Kernel tree**: kept in the kernel repository itself (`extension/` of the release).
- **Root composer.json**: required by the root `composer.json`, installed by `composer install`.
- **php-floor**: required by `se7enxweb/exponential-php-floor`, the package the root `composer.json` requires for
  each supported PHP line. `composer install` installs it the same way.

No site package copies an extension's files any more. Since 6.0.15 the extension packages `ezwt_extension`,
`ezstarrating_extension` and `ezgmaplocation_extension` are no longer required by any site package (see
[Site packages no longer install extension packages](../../bc/6.0/site-packages-no-extension-packages.md)). A package
copy used to be written over the Composer copy.

## Who activates it

- **Kernel (every installation)**: the setup step activates it whatever the site package is, when the extension is on
  disk.
- **Admin (access)**: switched on only in the administration siteaccess (`ActiveAccessExtensions[]`). The editor and
  Admin UI siteaccesses are made from the administration's settings.
- **Site packages**: the `extension_list` of the installer. The site packages are named here as follows. MS:
  `sevenx_multisite` and `sevenx_multisite_clean`. CJW: `cjw_multisite`. SITE: `sevenx_site` and `sevenx_site_clean`.
  WEBIN: `ezwebin_site` and `ezwebin_site_clean`. FLOW: `ezflow_site` and `ezflow_site_clean`. DEMO: `ezdemo_site` and
  `ezdemo_site_clean`. `plain_site` activates only what the kernel activates.

## The list

| Extension | Purpose | On disk from | Activated on a new installation | Tables |
|---|---|---|---|---|
| ezjscore | Ajax server functions, script and stylesheet packing, jQuery 4 | kernel tree | kernel | |
| ezoe | the online rich text editor | kernel tree | kernel | |
| ezformtoken | form tokens against cross-site request forgery | kernel tree | kernel | |
| expimageeditor | the image editor of the administration | kernel tree | kernel | |
| expservices | remote services (portal, partner) | kernel tree | MS, CJW, SITE | yes |
| exphelpsite | the help site design | kernel tree | not by default | |
| ezodf | OpenDocument import and export | php-floor | kernel | |
| ezprestapi | REST API v1 and v2 | root composer.json | kernel | |
| ezmultiupload | upload several files at once | root composer.json | kernel | |
| eztags | tags and the tags datatype | php-floor | kernel | yes |
| ezautosave | automatic saving of drafts | root composer.json | kernel | |
| ezownerchange | change the owner of content | php-floor | kernel | |
| sevenx_authentication_2fa | two-step sign-in | php-floor | kernel | |
| ezoracle | the Oracle database driver | root composer.json | kernel, only on Oracle; MS, CJW, SITE | |
| sqliimport | content imports | php-floor | kernel: admin (access) | yes |
| expui | the jQuery 4 interface API of the designs | root composer.json | MS, CJW, SITE; kernel: admin (access) where it is not global | |
| expqueue | the job queue | php-floor | MS, CJW, SITE; kernel: admin (access) where it is not global | yes |
| exp_adminui | the Admin UI look of the administration | php-floor | kernel: admin, editor and adminui (access) | |
| expai | the writing assistant (switched off by its own `Enabled=false`) | php-floor | MS, CJW, SITE: admin (access) | yes |
| expwt | the website sidebar, which replaces ezwt | see below | MS, CJW, SITE, WEBIN, FLOW, DEMO (see below) | |
| ezwt | the website toolbar, replaced by expwt | root composer.json | none (see below) | |
| ezstarrating | the star rating datatype | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | yes |
| ezgmaplocation | the map location datatype | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | yes |
| ezwebin | the website interface design | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| ezie | the classic image editor | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| ezpaypal | PayPal payments | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| xrowmetadata | the Meta Data datatype (title, description, keywords) | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| xrowextract | content export | php-floor | MS, CJW, SITE, WEBIN, FLOW, DEMO | yes |
| bccie | collected information export | php-floor | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| bcgooglesitemaps | sitemaps | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| bcwebsitestatistics | web statistics | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| owsimpleoperator | extra template operators | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| swark | extra template operators | root composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| ezflow | page layouts with blocks (classic) | root composer.json | FLOW, DEMO | yes |
| ezdemo | the demo design | root composer.json | DEMO | |
| sevenx_themes_simple | the simple design | root composer.json | MS, CJW, SITE | |
| sevenx_themes_media | the media design | php-floor | MS, CJW | yes |
| cjw_themes_jumper, cjw_tmv | the CJW designs | php-floor | CJW | |
| explayouts, its explayouts_* parts and expquery_translator | Exponential Layouts | php-floor | MS, CJW | yes |
| expsite_api, expsite_core, expsite_app | the site API of the media design | php-floor | MS, CJW | |
| ezurlmultisite | addresses across sites | root composer.json | MS, CJW | |
| cjw_newsletter | newsletters | php-floor | MS, CJW, SITE | yes |
| syndication | content syndication | php-floor | MS, CJW, SITE | yes |
| sevenx_dse | the data storage engine | php-floor | MS, CJW, SITE | |
| git_manager | Git checkouts and captions from the administration | php-floor | MS, CJW, SITE | |
| ezupdate | updates from the administration | root composer.json | MS, CJW, SITE | |
| exp_enhanced_link, expchangeclass, ngclasslist, powercontent | link datatype, class change, class list, bulk content | php-floor, root composer.json | MS, CJW, SITE | |
| birthday, enhancedezbinaryfile, enhancedselection2 | datatypes | root composer.json | MS, CJW, SITE | enhancedselection2: yes |
| hcaptcha, recaptcha | anti-spam | root composer.json | MS, CJW, SITE | |
| ezmbpaex | password expiry | root composer.json | not by default | yes |
| expsite_data_media, expsite_installer | the media site's data and installer | php-floor | not activated | |

"Tables" means that the extension ships a `share/db_schema.dba`.

## ezwt and expwt

expwt, the website sidebar, replaces ezwt, the website toolbar. The site package installers of 6.0.15 put `expwt` in
their `extension_list` where `ezwt` was. They no longer require the `ezwt_extension` package. The rebuilt site packages
are published on the package server together with the first expwt release and the designs that use the sidebar.
Until then the published site packages still activate ezwt. The root `composer.json` requires `se7enxweb/ezwt`
until expwt is installed by Composer. expwt's package replaces ezwt (Composer `replace`), so Composer removes
`extension/ezwt` when expwt comes in. An existing installation moves with `./console exp:wt:migrate-from-ezwt --apply`
or the upgrade step `6.0.15-expwt-enable` (see [The website sidebar](website-sidebar.md)). expwt's own default is
`Enabled=disabled` in `expwt.ini` until expwt 1.0, so the sidebar is shown only where `[ExpwtSettings] Enabled=enabled`
is set.

## Where the tables come from

An extension that ships a `share/db_schema.dba` gets its tables from that file, on every engine (MySQL and MariaDB,
PostgreSQL, SQLite, Oracle with ezoracle, MongoDB collections), through the engine's schema handler:

- **New installation**: after the site package, the setup step creates the missing tables of every active extension
  with a `share/db_schema.dba`, global or in the administration
  (`expInstallDefaultExtensions::ensureExtensionTables()`). The maintained site packages create them earlier in their
  own post-install from the same file. The older installers (ezwebin, ezflow, ezdemo) call the same kernel function
  for ezstarrating and ezgmaplocation.
- **Existing installation**: the upgrade step `6.0.15-active-extension-tables` (`./console exp:upgrade`) does the same
  for every extension that is active globally or in a siteaccess. The dry run shows the SQL. See
  [Upgrading with the console](../../guides/upgrading-with-the-console.md).

Missing tables are created. Nothing is dropped or changed, and no `share/db_data.dba` is loaded. A data file holds a
site's seed, such as the layouts of a demo, and it stays with the site package that knows that site. ezstarrating
needs `ezstarrating` and `ezstarrating_data`. ezgmaplocation needs `ezgmaplocation`.

## Related pages

- [What a new installation comes with: the default extension distribution](default-extension-distribution.md) (the
  history of the root `composer.json`)
- [Extensions, themes and packages](extensions/README.md)
- [The extensions sevenx_multisite activates](../../help/site-packages/sevenx-multisite/extensions-it-activates.md)
- [Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
