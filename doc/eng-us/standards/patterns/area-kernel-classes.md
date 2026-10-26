---
title: Patterns in kernel/classes
audience: developer
category: standards
order: 484
summary: Every class, view, fetch function, operator and plugin point of kernel/classes with its design patterns.
keywords: kernel/classes, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (217)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expAdminPagination` | class | [Utility (static helper)](utility.md) | `kernel/classes/expadminpagination.php:19` | One place to ask how many rows a list in the administration interface shows. |
| `expAnonymousPublicPolicies` | class | [Utility (static helper)](utility.md) | `kernel/classes/expanonymouspublicpolicies.php:29` | The policies a visitor who is not signed in needs on a public site, beyond reading content: registering, reading the RSS feeds, the tag pages of ezta… |
| `expBenchmark` | class | [Utility (static helper)](utility.md) | `kernel/classes/expbenchmark.php:28` | The statistics, the report formats and the regression check of exp:benchmark. |
| `expBenchmarkHttp` | class | [Utility (static helper)](utility.md) | `kernel/classes/expbenchmarkhttp.php:20` | The HTTP side of exp:benchmark: requests a list of URLs, several at a time, with PHP's own curl (curl_multi), so no external program is needed. |
| `expBenchmarkKernel` | class | [Utility (static helper)](utility.md) | `kernel/classes/expbenchmarkkernel.php:29` | The in-process side of exp:benchmark: what the kernel spends on the parts of a page, without HTTP, a web server or a response cache in between. |
| `expBenchmarkMicro` | class | [Utility (static helper)](utility.md) | `kernel/classes/expbenchmarkmicro.php:33` | The micro mode of exp:benchmark: pure-PHP hot paths of the kernel, timed in this process without HTTP, a web server or a database, so it runs on a CI… |
| `expBookmarkPage` | class | [Utility (static helper)](utility.md) | `kernel/classes/expbookmarkpage.php:18` | What the bookmark page (content/bookmark) shows, worked out from plain arrays: the folders with their counts, which bookmarks a folder, a search and… |
| `expBookmarkView` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/expbookmarkview.php:39` | A bookmark of a system page: a page of a module view (setup/systemupgrade, exphelp/dashboard, audit/console/(user)/14 ...) instead of a node. |
| `expCacheCatalogue` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expcachecatalogue.php:22` | The caches of Setup › Caches, described for a person: what each holds, its id and tags, its directory, its size and number of files, when it was last… |
| `expCacheManager` | class | [Facade / service](facade.md) | `kernel/classes/expcachemanager.php:30` | Everything Setup › Cache (kernel/setup/cache.php) can do, as functions that return what happened instead of setting template variables, so the admini… |
| `expCacheWarm` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcachewarm.php:28` | Keeps the response cache full, so that no visitor is the one who pays for a page to be rendered. |
| `expCleanupRSS` | class | – | `kernel/classes/expcleanuprss.php:28` | Trims the content an RSS import has created, keeping the newest items of each feed and removing the rest. |
| `expCollaborationGroupManager` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/expcollaborationgroupmanager.php:14` | Creates, renames and deletes the collaboration groups of one user and moves items between them. |
| `expCollaborationInbox` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcollaborationinbox.php:15` | The collaboration summary as an inbox: every item of the current user with its state (waiting, approved, denied), its role for the user (approver or… |
| `expCollaborationSampleData` | class | – | `kernel/classes/expcollaborationsampledata.php:19` | Builds, reports and removes a self-contained set of sample content for the collaboration tool: a user group with three editors, an approval workflow… |
| `expCollaborationSampleModule` | class | – | `kernel/classes/expcollaborationsampledata.php:856` | The module the approval handler redirects with; here nothing is redirected. |
| `expContentDraftList` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcontentdraftlist.php:20` | What the drafts page (content/draft, "My drafts") lists, worked out from plain arrays: the filters of the address (translation, class, age, search, o… |
| `expContentHistoryList` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcontenthistorylist.php:22` | What the versions page of an object (content/history) lists, worked out from plain arrays: the filters of the address (status, translation, creator,… |
| `expContentLanguageStates` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcontentlanguagestates.php:26` | The state of each language of one content object, for the language badges of admin4 (design:parts/ language_state_badge.tpl): the main language (the… |
| `expContentPendingList` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcontentpendinglist.php:21` | What the pending page (content/pendinglist, "My pending items") lists, worked out from plain arrays: the versions the user sent for publishing that a… |
| `expCronjobPause` | class | [Utility (static helper)](utility.md) | `kernel/classes/expcronjobpause.php:27` | Pauses cronjob parts: one part, or all of them, until someone resumes them. |
| `expCrontabSetup` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expcrontabsetup.php:47` | The cron job of an installation: the crontab lines that run its cronjob parts (runcronjobs.php), written by the installers and by bin/php/installcron… |
| `expDemoMode` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/expdemomode.php:38` | Demo mode: the environment variable EXP_DEMO_MODE=1 marks an installation as a public demo. |
| `expEditorPolicies` | class | [Utility (static helper)](utility.md) | `kernel/classes/expeditorpolicies.php:39` | The policies an editor needs for everyday work and the Editor role of a new installation lacks: hiding and revealing, the trash (looking into it and… |
| `expErrorHeaderList` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/experrorheaderlist.php:25` | The response headers of an error page, from error.ini [HTTPError-]: |
| `expExtensionCatalogue` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expextensioncatalogue.php:20` | What Setup › Extensions knows about each extension: its metadata (expInfo), its declared dependencies (extension.xml ), the designs and settings file… |
| `expExtensionChangePlan` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expextensionchangeplan.php:33` | The change planner of Setup › Extensions: what a list of active extensions becomes when an administrator moves, activates or deactivates one, what di… |
| `expImageUploadValidator` | class | [Utility (static helper)](utility.md) | `kernel/classes/expimageuploadvalidator.php:35` | Decides whether an uploaded file is an image the site accepts, by what the file contains rather than by its name. |
| `ExpInstallationDetailsOutputFilter` | class | [Decorator / filter](decorator.md), [Utility (static helper)](utility.md) | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Installation name output filter. |
| `ExpInstallationOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/classes/expinstallationoperator.php:10` | Template operators for exposing the installation name to templates: |
| `expInstallDefaultExtensions` | class | [Utility (static helper)](utility.md) | `kernel/classes/expinstalldefaultextensions.php:33` | The extensions every new installation gets set up, whatever site package it is made from: two-step sign-in (sevenx_authentication_2fa) and imports (s… |
| `expJsonSchemaValidator` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expjsonschemavalidator.php:20` | Checks decoded JSON (json_decode( ..., true )) against a JSON Schema (draft 2020-12), for the keywords the kernel's own schemas use: type, enum, cons… |
| `expKernelExtensionOverrideList` | class | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/expkernelextensionoverridelist.php:21` | The service "extensions.overrides": what the two extension activation passes of the web kernel (eZExtension::activateExtensions() 'default' and 'acce… |
| `expKernelServiceCompiler` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/expkernelservicecompiler.php:33` | Builds, checks and removes the compiled kernel service container (var/cache/kernelservices/). |
| `expKernelServiceContainer` | class | – | `kernel/classes/expkernelservicecontainer.php:35` | The service container of the kernel: shared objects by name, made on first use, in their scope. |
| `expKernelServices` | class | [Utility (static helper)](utility.md) | `kernel/classes/expkernelservices.php:44` | The kernel service container: the static facade, and the compiled boot of the web kernel. |
| `expKernelServiceScope` | class | – | `kernel/classes/expkernelservicescope.php:31` | The scopes of the kernel service container (expKernelServices), and the store of the worker scope. |
| `expKickstarter` | class | – | `kernel/classes/expkickstarter.php:12` | – |
| `expKickstarterIni` | class | – | `kernel/classes/expkickstarterini.php:19` | – |
| `expLinkCheck` | class | – | `kernel/classes/explinkcheck.php:15` | Decides whether a link of published content works, for the link check cronjob (linkcheck.php) and the link list (url/list). |
| `expLoveStack` | class | – | `kernel/classes/explovestack.php:29` | Finds what a site coming from LoveStack (mugoweb/Exponential-legacy) brings along that Exponential handles differently: the markers of its database (… |
| `expMaintenance` | class | [Utility (static helper)](utility.md) | `kernel/classes/expmaintenance.php:29` | Maintenance mode: while var/maintenance.json exists, every request to the front controllers is answered with a maintenance page (503, Retry-After, ne… |
| `expManagedConfiguration` | class | [Utility (static helper)](utility.md) | `kernel/classes/expmanagedconfiguration.php:25` | site.ini [ConfigurationSettings] ManagedByDeployment=enabled: settings, templates and code come from the deployment only, and the admin interface doe… |
| `expMemberManagement` | class | [Utility (static helper)](utility.md) | `kernel/classes/expmembermanagement.php:27` | Member management: user groups that manage the accounts of other user groups, set up from membermanagement.ini (doc/eng-us/bc/6.0/member-management.m… |
| `expNamePatternResolverInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/expnamepatternresolverinterface.php:27` | A PHP class that makes the name of a content object, or the name its URL alias is made from, instead of the attribute tokens of the class's name patt… |
| `expNodeQuickPick` | class | [Utility (static helper)](utility.md) | `kernel/classes/expnodequickpick.php:18` | Quick location buttons (doc/eng-us/features/6.0/quick-location-buttons.md): a few well-known places of the content tree, found from a short key (Webs… |
| `expNotificationJob` | class | [Utility (static helper)](utility.md) | `kernel/classes/expnotificationjob.php:17` | A notification run for the status page ("Run now"), in the background. |
| `expNotificationService` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `kernel/classes/expnotificationservice.php:26` | The one place that knows how the notification system is run and looked at: the cronjob part, the exp:notification:* commands, the status page and its… |
| `expPackageArchive` | class | [Utility (static helper)](utility.md) | `kernel/classes/exppackagearchive.php:41` | Opens a package archive (.ezpkg: a gzip compressed tar archive) for reading, after a bounded scan of its tar headers. |
| `expPackageArchiveModes` | class extends ezcArchiveCallback | – | `kernel/classes/exppackagearchivemodes.php:20` | The extract callback of a package archive (expPackageArchive::open()): every file it extracts gets 0644, every directory 0755, each within the limits… |
| `expPackageConflictReport` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/exppackageconflictreport.php:22` | What installing a package would meet on this installation, by remote id, without writing anything: for every content object of the package whether an… |
| `expPackageJson` | class | [Utility (static helper)](utility.md) | `kernel/classes/exppackagejson.php:34` | The JSON serialization of a package (doc/eng-us/bc/6.0/package-json-format.md). |
| `expPasswordPolicy` | class | [Singleton / instance()](singleton.md) | `kernel/classes/exppasswordpolicy.php:24` | The rules a new password must meet, and what happens after a password was changed. |
| `expPhar` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/expphar.php:21` | Packages the engine -- the kernel and library class files -- into a phar, so the runtime can load them from one archive instead of from a thousand se… |
| `expPolicyOmitList` | class | [Utility (static helper)](utility.md) | `kernel/classes/exppolicyomitlist.php:25` | The modules and views that skip the policy check: site.ini [RoleSettings] PolicyOmitList[] without the entries of [RoleSettings] PolicyOmitListExclud… |
| `expPreloadAddress` | class | [Factory and static factory methods](factory.md) | `kernel/classes/exppreloadaddress.php:26` | Where the cache preloader finds a siteaccess: the address of its site, the url prefix that selects it on that host, and the pages a run starts from. |
| `expPreloadHistory` | class | – | `kernel/classes/exppreloadhistory.php:22` | The cache preloader's runs: one status file per run, .json, next to its events (.jsonl) in var//preload. |
| `expPreloadJob` | class | [Utility (static helper)](utility.md) | `kernel/classes/exppreloadjob.php:27` | A cache preload run: started in the background from Setup › Preload, or in the foreground from the shell (bin/php/preload.php), with one lock, one st… |
| `expPreloadLock` | class | – | `kernel/classes/exppreloadlock.php:21` | One cache preload at a time. |
| `expProcessTools` | class | [Utility (static helper)](utility.md) | `kernel/classes/expprocesstools.php:22` | Finds the programs a background job needs: the PHP command line and setsid. |
| `expScriptStatus` | class | [Singleton / instance()](singleton.md) | `kernel/classes/expscriptstatus.php:18` | expScriptStatus - lightweight CLI progress/status reporter. |
| `expSecretRule` | class | [Utility (static helper)](utility.md) | `kernel/classes/expsecretrule.php:29` | The one rule of what is a secret, shared by everything that shows or records settings and values: exp:ini and expIniEditor, the audit log (through ex… |
| `expSetupLog` | class | [Utility (static helper)](utility.md) | `kernel/classes/expsetuplog.php:31` | var/log/setup.log: one readable record per installation run. |
| `expSetupWizardGuard` | class | [Utility (static helper)](utility.md) | `kernel/classes/expsetupwizardguard.php:26` | Decides what a request to the web setup wizard (setup/init) may do, before the wizard shows a page or touches maintenance mode. |
| `expSiteAccessOverview` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/expsiteaccessoverview.php:31` | Every siteaccess of the installation, how a request reaches it and where it is: the siteaccess part of Setup › System information (#552). |
| `expSystemReport` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expsystemreport.php:29` | The facts of Setup › System information and of exp:system:info, in sections, with health checks. |
| `expSystemReportMask` | class | [Utility (static helper)](utility.md) | `kernel/classes/expsystemreportmask.php:29` | What Setup › System information, its text report and exp:system:info may show of a value. |
| `expTestLanguage` | class | [Utility (static helper)](utility.md) | `kernel/classes/exptestlanguage.php:17` | Test languages: locales marked [LanguageSettings] TestLanguage=true in share/locale (the shipped one is Lorem Ipsum, qlo-ZZ), and the lorem ipsum tex… |
| `expUserLoginIntegrity` | class | [Utility (static helper)](utility.md) | `kernel/classes/expuserloginintegrity.php:35` | One account per login: finds the logins more than one ezuser row has, adds the unique index on ezuser.login once there are none, and stores an accoun… |
| `expVelocity` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expvelocity.php:38` | Drives the bundled application server as a service. |
| `expVelocityConfig` | class | – | `kernel/classes/expvelocityconfig.php:36` | Read and write the settings Exponential Velocity actually runs on. |
| `expVelocityConfigLayout` | class | – | `kernel/classes/expvelocityconfiglayout.php:41` | The server's configuration as a Debian Apache-style tree, and the move to it. |
| `expVelocityDeploy` | class | – | `kernel/classes/expvelocitydeploy.php:52` | Everything a PHP code change needs before it is live, in the one order that works: exp:velocity deploy. |
| `expVelocityFrankenPHP` | class extends expVelocity | – | `kernel/classes/expvelocityfrankenphp.php:34` | Velocity's second engine: FrankenPHP, the Caddy web server with PHP built in. |
| `expVelocityFrankenPHPInstaller` | class | – | `kernel/classes/expvelocityfrankenphpinstaller.php:28` | Puts the FrankenPHP binary in place for the frankenphp engine: `exp:velocity install`, and start's AutoInstall. |
| `expVelocityMarkers` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expvelocitymarkers.php:30` | The small state files that say why an engine is not running. |
| `expVelocityPHPServer` | class extends expVelocity | – | `kernel/classes/expvelocityphpserver.php:29` | Velocity's third engine: PHP's own built-in web server (php -S). |
| `expVelocityRestartUnit` | class | [Factory and static factory methods](factory.md) | `kernel/classes/expvelocityrestartunit.php:34` | Runs exp:velocity restart as one detached unit: the stop and the start in a process that does not belong to the caller. |
| `expVelocityWatchdog` | class | – | `kernel/classes/expvelocitywatchdog.php:49` | A safety net that starts the default engine again when it is down and nobody meant it to be: exp:velocity watchdog. |
| `expViewAccess` | class | [Utility (static helper)](utility.md) | `kernel/classes/expviewaccess.php:25` | Whether a user can open an address of this siteaccess, decided the way the kernel decides a request (ezpKernelWeb): the URL alias (and wildcard) tran… |
| `expViewParameters` | class | [Utility (static helper)](utility.md) | `kernel/classes/expviewparameters.php:25` | The options of an administration view or endpoint are view parameters, /(name)/value, never GET query parameters (as the help center's, doc/eng-us/fe… |
| `expWeakPasswordList` | class | [Singleton / instance()](singleton.md) | `kernel/classes/expweakpasswordlist.php:35` | The one list of well-known and common passwords, and the check every place that sets a new password uses: the setup wizard, the kickstarter and exp:i… |
| `eZAudit` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezaudit.php:20` | The 4.x audit API, kept so every existing call keeps working: eZAudit::writeAudit( $name, $attributes ) is recorded by expAudit (kernel/classes/audit… |
| `eZBasket` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezbasket.php:20` | – |
| `eZBinaryFileHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezbinaryfilehandler.php:23` | – |
| `eZCache` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezcache.php:22` | eZCache ezcache.php |
| `eZCacheTrash` | class | [Registry](registry.md), [Utility (static helper)](utility.md) | `kernel/classes/ezcachetrash.php:38` | Removes a cache directory by renaming it out of the way first. |
| `eZClusterFileFailure` | class | – | `kernel/classes/ezclusterfilefailure.php:20` | – |
| `eZClusterFileHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `kernel/classes/ezclusterfilehandler.php:11` | – |
| `eZCodeTemplate` | class | – | `kernel/classes/ezcodetemplate.php:18` | – |
| `eZCollaborationGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationgroup.php:18` | – |
| `eZCollaborationItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationitem.php:18` | – |
| `eZCollaborationItemGroupLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationitemgrouplink.php:18` | – |
| `eZCollaborationItemHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/ezcollaborationitemhandler.php:18` | – |
| `eZCollaborationItemMessageLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationitemmessagelink.php:18` | – |
| `eZCollaborationItemParticipantLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationitemparticipantlink.php:18` | – |
| `eZCollaborationItemStatus` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationitemstatus.php:18` | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md), [Utility (static helper)](utility.md) | `kernel/classes/ezcollaborationprofile.php:18` | – |
| `eZCollaborationSimpleMessage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcollaborationsimplemessage.php:18` | – |
| `eZCollaborationViewHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezcollaborationviewhandler.php:18` | – |
| `eZConfirmOrderHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezconfirmorderhandler.php:18` | – |
| `eZContentBrowse` | class | – | `kernel/classes/ezcontentbrowse.php:33` | – |
| `eZContentBrowseBookmark` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentbrowsebookmark.php:35` | – |
| `eZContentBrowseBookmarkFolder` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcontentbrowsebookmarkfolder.php:32` | – |
| `eZContentBrowseRecent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcontentbrowserecent.php:34` | – |
| `eZContentCache` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezcontentcache.php:18` | – |
| `eZContentCacheManager` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `kernel/classes/ezcontentcachemanager.php:26` | – |
| `eZContentClass` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentclass.php:20` | – |
| `eZContentClassAttribute` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentclassattribute.php:19` | – |
| `eZContentClassClassGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcontentclassclassgroup.php:18` | – |
| `eZContentClassEditHandler` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezcontentclassedithandler.php:15` | Handler for content class editing. |
| `eZContentClassGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcontentclassgroup.php:18` | – |
| `eZContentClassName` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Utility (static helper)](utility.md) | `kernel/classes/ezcontentclassname.php:12` | – |
| `eZContentClassNameList` | class extends eZSerializedObjectNameList | – | `kernel/classes/ezcontentclassnamelist.php:12` | – |
| `eZContentClassOperations` | class | – | `kernel/classes/ezcontentclassoperations.php:20` | – |
| `eZContentFunctions` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezcontentfunctions.php:12` | – |
| `eZContentLanguage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentlanguage.php:12` | – |
| `eZContentObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentobject.php:17` | Encapsulates data about and methods to work with content objects |
| `eZContentObjectAssignmentHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/ezcontentobjectassignmenthandler.php:18` | – |
| `eZContentObjectAttribute` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentobjectattribute.php:20` | – |
| `eZContentObjectEditHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/ezcontentobjectedithandler.php:19` | – |
| `eZContentObjectOperations` | class | – | `kernel/classes/ezcontentobjectoperations.php:20` | – |
| `eZContentObjectTranslation` | class | – | `kernel/classes/ezcontentobjecttranslation.php:20` | – |
| `eZContentObjectTrashNode` | class extends eZContentObjectTreeNode | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezcontentobjecttrashnode.php:15` | Encapsulates data about and methods to work with content objects which reside in the trash |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentobjecttreenode.php:17` | Encapsulates data about and methods to work with content objects inside the content object tree |
| `eZContentObjectTreeNodeOperations` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezcontentobjecttreenodeoperations.php:22` | – |
| `eZContentObjectVersion` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezcontentobjectversion.php:19` | – |
| `eZContentUpload` | class | – | `kernel/classes/ezcontentupload.php:51` | – |
| `eZContentUploadHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/ezcontentuploadhandler.php:28` | – |
| `eZDataType` | class | [Factory and static factory methods](factory.md), [Registry](registry.md) | `kernel/classes/ezdatatype.php:46` | – |
| `eZDBGarbageCollector` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezdbgarbagecollector.php:33` | – |
| `eZDiscount` | class | – | `kernel/classes/ezdiscount.php:12` | – |
| `eZDiscountRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezdiscountrule.php:18` | – |
| `eZDiscountSubRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezdiscountsubrule.php:18` | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezdiscountsubrulevalue.php:17` | – |
| `eZError` | class | – | `kernel/classes/ezerror.php:16` | – |
| `eZHTTPHeader` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezhttpheader.php:18` | – |
| `eZInformationCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezinformationcollection.php:22` | – |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezinformationcollectionattribute.php:19` | – |
| `eZNamePatternResolver` | class | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/eznamepatternresolver.php:40` | eZNamePatternResolver is a utility class for resolving object name and url alias patterns. |
| `eZNavigationPart` | class | [Utility (static helper)](utility.md) | `kernel/classes/eznavigationpart.php:32` | – |
| `eZNodeAssignment` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/eznodeassignment.php:17` | – |
| `eZNodeviewfunctions` | class | [Utility (static helper)](utility.md) | `kernel/classes/eznodeviewfunctions.php:18` | – |
| `eZOrder` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezorder.php:20` | – |
| `eZOrderItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezorderitem.php:27` | – |
| `eZOrderStatus` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezorderstatus.php:47` | – |
| `eZOrderStatusHistory` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezorderstatushistory.php:35` | – |
| `eZPackage` | class | [Factory and static factory methods](factory.md) | `kernel/classes/ezpackage.php:20` | – |
| `eZPackageCatalog` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagecatalog.php:23` | What the package pages say about the packages on disk: every repository with its counts, one card per package (name, version, type, summary, maintain… |
| `eZPackageComparison` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezpackagecomparison.php:36` | Compares what a package carries with what the site has, for package/compare/. |
| `eZPackageComparisonDiff` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagecomparisondiff.php:20` | A word level difference of two texts, for the package comparison viewer (package/compare, eZPackageComparison): which words were removed from the fir… |
| `eZPackageComparisonFileCollector` | class | – | `kernel/classes/ezpackagecomparisonfilecollector.php:18` | Stands in for an eZPackage when eZPackageComparison asks a datatype to serialize one of the site's own attributes, so that the site's value comes out… |
| `eZPackageComparisonImport` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagecomparisonimport.php:34` | Imports what a package brings for single items of its comparison with the site (eZPackageComparison, package/compare): a new object is created where… |
| `eZPackageCreationHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezpackagecreationhandler.php:21` | – |
| `eZPackageDownload` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagedownload.php:25` | Sends a file of the package module (an exported archive, a file out of a package) to the browser in pieces. |
| `eZPackageFileBrowser` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagefilebrowser.php:17` | Read-only support for the package contents browser on package/view/full/: every file a package's own directory carries, paginated and filtered, and o… |
| `eZPackageHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/ezpackagehandler.php:18` | – |
| `eZPackageInstallationHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezpackageinstallationhandler.php:19` | – |
| `eZPackageLicense` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagelicense.php:28` | – |
| `eZPackageRemovalPlan` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezpackageremovalplan.php:20` | What removing a selection of packages would do, before it is done: per package its directory, files and size, whether it is installed (its install re… |
| `eZPackageRequestGuard` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackagerequestguard.php:16` | The checks every package view applies to what a request names before anything is read from disk: a package name, a repository, a view mode and a file… |
| `eZPackageUploadInspector` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackageuploadinspector.php:33` | Looks at a package archive before anything of it is written into a repository. |
| `eZPackageVersion` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpackageversion.php:55` | – |
| `eZPathElement` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezpathelement.php:23` | – |
| `eZPDFExport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezpdfexport.php:19` | – |
| `eZPendingActions` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Utility (static helper)](utility.md) | `kernel/classes/ezpendingactions.php:12` | – |
| `eZPersistentObject` | class | – | `kernel/classes/ezpersistentobject.php:38` | Allows for object persistence in a database |
| `eZPolicy` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezpolicy.php:19` | – |
| `eZPolicyLimitation` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezpolicylimitation.php:18` | – |
| `eZPolicyLimitationValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezpolicylimitationvalue.php:18` | – |
| `eZPreferences` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezpreferences.php:22` | – |
| `eZProductCategory` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezproductcategory.php:18` | – |
| `eZProductCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezproductcollection.php:16` | eZProductCollection is a container class which handles groups of products |
| `eZProductCollectionItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezproductcollectionitem.php:20` | – |
| `eZProductCollectionItemOption` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezproductcollectionitemoption.php:12` | – |
| `eZRedirectManager` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `kernel/classes/ezredirectmanager.php:22` | – |
| `eZRole` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezrole.php:35` | – |
| `eZRSSExport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezrssexport.php:19` | – |
| `eZRSSExportItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezrssexportitem.php:19` | – |
| `eZRSSExportOPMLItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezrssexportopmlitem.php:26` | One line of an OPML export: a feed that the export lists. |
| `eZRSSImport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezrssimport.php:19` | – |
| `eZRunCronjobs` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezruncronjobs.php:15` | – |
| `eZScript` | class | [Singleton / instance()](singleton.md) | `kernel/classes/ezscript.php:49` | – |
| `eZSearch` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezsearch.php:19` | – |
| `eZSearchLog` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezsearchlog.php:18` | – |
| `eZSection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezsection.php:18` | – |
| `eZSerializedObjectNameList` | class | – | `kernel/classes/ezserializedobjectnamelist.php:12` | – |
| `eZShippingManager` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `kernel/classes/ezshippingmanager.php:17` | – |
| `eZShopAccountHandler` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ezshopaccounthandler.php:12` | – |
| `eZSiteAccess` | class | [Front Controller](front-controller.md), [Utility (static helper)](utility.md) | `kernel/classes/ezsiteaccess.php:17` | Provides functions for siteaccess handling |
| `eZSiteData` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezsitedata.php:21` | eZPersistentObject implementation for ezsite_data table. |
| `eZSiteInstaller` | class | [Factory and static factory methods](factory.md) | `kernel/classes/ezsiteinstaller.php:23` | – |
| `eZSSLZone` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezsslzone.php:27` | – |
| `eZStaticCache` | class implements ezpStaticCache | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezstaticcache.php:30` | The eZStaticCache class manages the static cache system. |
| `eZSubtreeCache` | class | [Utility (static helper)](utility.md) | `kernel/classes/ezsubtreecache.php:18` | – |
| `eZTextInputParser` | class | – | `kernel/classes/eztextinputparser.php:17` | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/eztipafriendcounter.php:18` | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/eztipafriendrequest.php:17` | – |
| `eZTrigger` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/eztrigger.php:17` | – |
| `eZURLAliasFilter` | abstract class | [Decorator / filter](decorator.md), [Template Method / abstract base class](template-method.md) | `kernel/classes/ezurlaliasfilter.php:22` | The eZURLAliasFilter class the interface for all url alias filters, the filter implementation must implement the process method. |
| `eZURLAliasML` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezurlaliasml.php:58` | – |
| `eZURLAliasQuery` | class | [Factory and static factory methods](factory.md) | `kernel/classes/ezurlaliasquery.php:34` | – |
| `eZURLWildcard` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezurlwildcard.php:19` | – |
| `eZUserDiscountRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezuserdiscountrule.php:18` | – |
| `eZVATManager` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `kernel/classes/ezvatmanager.php:18` | – |
| `eZVatRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezvatrule.php:18` | – |
| `eZVatType` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezvattype.php:19` | – |
| `eZViewCounter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezviewcounter.php:12` | – |
| `eZWishList` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezwishlist.php:20` | – |
| `eZWorkflow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ezworkflow.php:18` | – |
| `eZWorkflowEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezworkflowevent.php:18` | – |
| `eZWorkflowEventType` | class extends eZWorkflowType | [Registry](registry.md) | `kernel/classes/ezworkfloweventtype.php:18` | – |
| `eZWorkflowGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezworkflowgroup.php:18` | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/ezworkflowgrouplink.php:18` | – |
| `eZWorkflowGroupType` | class extends eZWorkflowType | [Registry](registry.md) | `kernel/classes/ezworkflowgrouptype.php:18` | – |
| `eZWorkflowProcess` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/ezworkflowprocess.php:19` | – |
| `eZWorkflowType` | class | [Factory and static factory methods](factory.md), [Registry](registry.md) | `kernel/classes/ezworkflowtype.php:18` | – |

## Event hooks (11)

| Event | Kind | Called in |
|---|---|---|
| `image/invalidateAliases` | notify | `kernel/classes/ezcache.php:715` |
| `content/class/cache/all` | notify | `kernel/classes/ezcache.php:862` |
| `user/cache/all` | notify | `kernel/classes/ezcache.php:885` |
| `content/cache/all` | notify | `kernel/classes/ezcache.php:898` |
| `content/state/cache/all` | notify | `kernel/classes/ezcache.php:968` |
| `content/cache` | notify | `kernel/classes/ezcontentcachemanager.php:818` |
| `content/cache/all` | notify | `kernel/classes/ezcontentcachemanager.php:826` |
| `content/cache/all` | notify | `kernel/classes/ezcontentcachemanager.php:1182` |
| `content/edit/access` | filter | `kernel/classes/ezcontentobject.php:6375` |
| `content/move` | notify | `kernel/classes/ezcontentobjecttreenode.php:5415` |
| `content/view/cachekeys` | filter | `kernel/classes/eznodeviewfunctions.php:414` |
