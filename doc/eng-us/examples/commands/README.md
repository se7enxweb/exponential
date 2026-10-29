---
title: Console commands
audience: developer, admin
category: examples
order: 0
summary: Every ./console command with its help text, options and aliases.
keywords: Console commands, examples, reference
type: generated
generated: exp:help:examples
---
# Console commands

Every command of `./console` (`bin/php/console`): each `bin/php/<name>.php` is `exp:<name>`, a script may add aliases with `@alias`. The help text and the options are read from the code, so they match `--help`. `./console list` shows them on a machine; `./console help <command>` prints one command's help.

| Command | Aliases | What it does |
|---|---|---|
| [exp:accesscheck](accesscheck.md) | exp:access:check | Check what a user may do with a node or object (content/read, edit, ...) and which limitation refuses it |
| [exp:addanonymouspublicpolicies](addanonymouspublicpolicies.md) |  | Give the Anonymous role the public policies it lacks: user/register, rss/feed, tags/view, the newsletter's public views (dry run unless --apply) |
| [exp:adddefaultstates](adddefaultstates.md) |  | Give every content object the default state of each state group it has no state of (dry run unless --apply) |
| [exp:addeditorpolicies](addeditorpolicies.md) |  | Give the Editor role the everyday policies it lacks: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, the image editor, the own account (dry run unless --apply) |
| [exp:addexphelpreadpolicy](addexphelpreadpolicy.md) | exp:help-grant | Grant exphelp/read (the help system) to the Editor role or the roles named (dry run unless --apply) |
| [exp:addmembermanagement](addmembermanagement.md) |  | Set up member management from membermanagement.ini: the role that lets a group (default Editors) manage the accounts of another (default Members), never Administrator users or Editors (dry run unless --apply) |
| [exp:addoauthauthorizepolicy](addoauthauthorizepolicy.md) |  | Grant oauth/authorize to the roles that let users sign in (6.0.15 upgrade; dry run unless --apply) |
| [exp:adduserapproval](adduserapproval.md) |  | Set up the approval of new accounts in a user group (default: new Editors wait for the Administrator users): the workflow event on content/publish before and the role that lets the requesters create such accounts; --mode=section sets up the optional alternative, a section Staff with the plain approval event (both a dry run unless --apply) |
| [exp:audit](audit.md) |  | Audit log: tail, show one event, verify the hash chains, list the channels |
| [exp:benchmark](benchmark.md) | exp:bench | Measure this installation: page timings over HTTP, A/B between servers, kernel probes, regression check |
| [exp:cache](cache.md) |  | Clear and inspect caches by tag, id or all: content, static, HTTP, Velocity, OPcache, APCu |
| [exp:changelog2xmltext](changelog2xmltext.md) |  | Convert a Changelog file to XML text format and print it to standard output |
| [exp:checkclasses](checkclasses.md) |  | Load every declared class and report the ones PHP refuses to load |
| [exp:checkcodestyle](checkcodestyle.md) |  |  |
| [exp:checkdbfiles](checkdbfiles.md) |  | Verify the database update files against the upgrade path; reports missing or stray files |
| [exp:checkmanifest](checkmanifest.md) |  |  |
| [exp:cleanuppolicies](cleanuppolicies.md) |  | Remove policies of modules that no longer exist (dry run unless --apply) |
| [exp:cleanuprss](cleanuprss.md) |  | Trim RSS-imported content, keeping the newest items of each active import (dry run unless --apply) |
| [exp:cleanupversions](cleanupversions.md) |  | Remove archived content versions beyond the VersionManagement limits (dry run unless --apply; -n: do not wait) |
| [exp:clusterize](clusterize.md) |  | Move var files into the database cluster storage (-u moves them back) |
| [exp:clusterpurge](clusterpurge.md) |  | Physically purge the cluster files of the given scopes (--scopes=scope1,scope2; dry run unless --apply) |
| [exp:collaborationsampledata](collaborationsampledata.md) | exp:collaboration:sample-data | Build sample content for the collaboration tool (approvals, comments, groups), or remove it again (dry run unless --apply) |
| [exp:configphp](configphp.md) | exp:config:list, exp:config:get, exp:config:set, exp:config:unset | List, read, set and unset the define() settings of config.php, typed and checked with php -l, documented by config.php-RECOMMENDED (set and unset: dry run unless --apply) |
| [exp:contentcheck](contentcheck.md) | exp:content-check | Content checks: build the reference index, list the unused assets and the broken relations below a node, list the missing files, scan the storage files (report only; --fix, --quarantine and --fix-permissions are a dry run unless --apply) |
| [exp:convertezenumtoezselection](convertezenumtoezselection.md) |  |  |
| [exp:convertprice2multiprice](convertprice2multiprice.md) |  | Convert content objects with 'price' datatype attributes to 'multiprice' |
| [exp:dfscleanup](dfscleanup.md) |  | Check database and DFS files against each other (-S, -B) and delete nonexistent ones (-D) |
| [exp:expbackfillordersiteaccess](expbackfillordersiteaccess.md) |  | Infer the siteaccess of orders placed before ezorder.siteaccess was recorded (dry run unless --apply) |
| [exp:expcontentjob](expcontentjob.md) |  | Run, list, resume and cancel content jobs: large subtree removes and copies in batches |
| [exp:expfetch](expfetch.md) | exp:info, exp:about | The system card: the Exponential "e" next to the facts of this installation, its engine and its machine, with health hints (exp:info adds an inventory, exp:about the project) |
| [exp:exphelp](exphelp.md) | exp:help | The help system on the command line: build and describe the index of doc/, search it, show a page's metadata, check its links and images |
| [exp:exphelpcontent](exphelpcontent.md) | exp:help:content | Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall (the changing actions are a dry run unless --apply) |
| [exp:exphelpexamples](exphelpexamples.md) | exp:help:examples | Generate the reference examples of the help below doc/eng-us/examples/ from the code: template operators and functions, fetch functions, module views, policies, INI settings, datatypes, workflow events, cronjobs, console commands and extension points |
| [exp:explogs](explogs.md) | exp:logs | The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives (dry run unless --apply) |
| [exp:exploremipsumfill](exploremipsumfill.md) | exp:loremipsum | Fill a translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text (dry run unless --apply) |
| [exp:explovestack](explovestack.md) | exp:lovestack | Check an installation coming from LoveStack (read-only), and record its database as Exponential's (migrate) |
| [exp:exppatterncatalogue](exppatterncatalogue.md) |  |  |
| [exp:exprepair](exprepair.md) |  | Repair queue of the missing-libraries error page: --create-key, --disable, --status, --run |
| [exp:ezapplytemplate](ezapplytemplate.md) |  | Apply the template blocks found in files and write the files back (exit code 0, 1 or 2) |
| [exp:ezasynchronouspublisher](ezasynchronouspublisher.md) |  |  |
| [exp:ezcache](ezcache.md) |  | Clear Exponential caches by tag or id (--clear-tag, --clear-id, --clear-all, --list-tags) |
| [exp:ezcheckphptag](ezcheckphptag.md) |  | Check files for characters before the PHP start tag or after the end tag |
| [exp:ezchecktranslation](ezchecktranslation.md) |  | Show statistics for one translation, e.g. ezchecktranslation.php ita-IT |
| [exp:ezcontentcache](ezcontentcache.md) |  | Clear content caches of given nodes or subtrees (--clear-node, --clear-subtree) |
| [exp:ezconvert2isbn13](ezconvert2isbn13.md) |  | Convert ISBN-10 numbers to ISBN-13 |
| [exp:ezconvertdbcharset](ezconvertdbcharset.md) |  | Convert all Exponential database tables to use UTF-8 character set |
| [exp:ezconvertmysqltabletype](ezconvertmysqltabletype.md) |  | Convert the tables of a MySQL database to another table type (--list, --newtype=TYPE) |
| [exp:ezcreatedeactivatedusergroup](ezcreatedeactivatedusergroup.md) |  | Create the user group for deactivated accounts ("Deactivate and move") ahead of first use |
| [exp:ezcsvexport](ezcsvexport.md) |  | Export the subtree below a node to one CSV file per content class (--storage-dir) |
| [exp:ezcsvimport](ezcsvimport.md) |  | Import content objects of a class below a node from a CSV file (--class, --creator) |
| [exp:ezexec](ezexec.md) |  | Run a PHP script with the Exponential bootstrap done: ezexec.php myscript.php |
| [exp:ezflowupgrade](ezflowupgrade.md) |  | Upgrade the installed ezflow packages (--to-version, --package, --url) |
| [exp:ezgeneratetranslationcache](ezgeneratetranslationcache.md) |  | Generate translation cache files (--ts-list limits it to the given translations) |
| [exp:ezimportdbafile](ezimportdbafile.md) |  | Import the dba data of a datatype into the database (--datatype=ezisbn) |
| [exp:ezpgenerateautoloads](ezpgenerateautoloads.md) |  | Generate the class autoload arrays: kernel (-k), kernel overrides (-o), extensions (-e) |
| [exp:ezpublishingbenchmark](ezpublishingbenchmark.md) |  | Benchmark parallel publishing in concurrent batches (--batches-count, -l level) |
| [exp:ezrequestrules](ezrequestrules.md) |  | List and check the request rules of a siteaccess and explain their decision for an address |
| [exp:ezsessiongc](ezsessiongc.md) |  | Remove expired sessions as defined by site.ini [Session] SessionTimeout |
| [exp:ezsqldiff](ezsqldiff.md) |  | Show the differences between two database schemas; the exit code tells if they differ |
| [exp:ezsqldumpisbndata](ezsqldumpisbndata.md) |  | Dump the ISBN group and range tables as SQL to a file or standard output |
| [exp:ezsqldumpschema](ezsqldumpschema.md) |  | Dump the database schema to a file or standard output (--type, --user) |
| [exp:ezsqlinsertschema](ezsqlinsertschema.md) |  | Insert a database schema and data from a .dba file into a database (--type, --user) |
| [exp:ezsubtreecopy](ezsubtreecopy.md) |  | Copy a content subtree to a new location (--src-node-id, --dst-node-id; dry run unless --apply) |
| [exp:ezsubtreeremove](ezsubtreeremove.md) |  | Remove content subtrees by node ID, to the trash unless --ignore-trash is given (dry run unless --apply) |
| [exp:eztc](eztc.md) |  | Compile the templates of a siteaccess into the template cache (--force recompiles all) |
| [exp:eztemplatecheck](eztemplatecheck.md) |  | Check the syntax of the templates of a siteaccess or of a design directory |
| [exp:ezwebincommon](ezwebincommon.md) |  | Helper library of ezwebininstall and ezwebinupgrade, not meant to be run directly |
| [exp:ezwebininstall](ezwebininstall.md) |  | Install the ezwebin site package and configure the site |
| [exp:ezwebinupgrade](ezwebinupgrade.md) |  | Upgrade an existing ezwebin installation to the current package version (--to-version) |
| [exp:flatten](flatten.md) |  | Remove unused data from the database: contentobject, contentclass, workflow, role or all |
| [exp:frankenphp](frankenphp.md) | exp:fp | Control the FrankenPHP engine of the Exponential web server |
| [exp:i18n](i18n.md) |  | The translation fallbacks: per locale the chain of translations it falls back to, and how much of the kernel and of each extension it shows translated; --check for CI |
| [exp:ini](ini.md) |  | Read and change INI settings in every scope: get, set, add, rem, clear, toggle, copy, move, where, list, scopes |
| [exp:install](install.md) | exp:names | One-command installation, SQLite by default, no kickstart.ini needed (a dry run unless --apply) |
| [exp:installcrontab](installcrontab.md) |  | Show, add or remove the cron job of the installation: the crontab lines that run its cronjob parts (--add and --remove: dry run unless --apply) |
| [exp:kickstarter](kickstarter.md) |  | Kickstarter runner: "ini" writes kickstart.ini, "run" installs from it (a dry run unless --apply) |
| [exp:mailbounces](mailbounces.md) | exp:mail:bounces | Read the bounce mailbox: hard bounces and spam complaints go on the suppression list (dry run unless --apply) |
| [exp:mailconsent](mailconsent.md) | exp:mail:consent | The consent log of the e-mail preferences: list, export as CSV, retention cleanup (dry run unless --apply) |
| [exp:mailgate](mailgate.md) | exp:mail:gate | Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like |
| [exp:mailpreferences](mailpreferences.md) | exp:mail:preferences | Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase |
| [exp:mailstatus](mailstatus.md) | exp:mail:status | Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems |
| [exp:mailsuppression](mailsuppression.md) | exp:mail:suppression | The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored) |
| [exp:maintenance](maintenance.md) |  | Take the site offline for maintenance and back: on, off or status |
| [exp:makestaticcache](makestaticcache.md) |  | Crawl the public siteaccesses and generate static pages (--site, --max-pages, --max-depth) |
| [exp:notificationevents](notificationevents.md) | exp:notification:events, exp:notify:events | List the notification events, or remove the old ones (dry run unless --apply) |
| [exp:notificationrun](notificationrun.md) | exp:notification:run, exp:notify:run | Run the notification filter: send the notifications of the pending events and the due digests (dry run unless --apply) |
| [exp:notificationstatus](notificationstatus.md) | exp:notification:status, exp:notify:status | Show the notification system: pending events, digest items, subscriptions, the last runs and problems |
| [exp:notificationsubscriptions](notificationsubscriptions.md) | exp:notification:subscriptions, exp:notify:subscriptions | List the subtree notification subscriptions per user and subtree, or remove those whose content is gone (dry run unless --apply) |
| [exp:phar](phar.md) |  | Build and inspect the engine archive dist/engine.phar (verbs build, check, clean, info) |
| [exp:preload](preload.md) |  | Warm the page caches of a site: request its section pages, then follow its links, and report the broken ones (dry run unless --apply) |
| [exp:preloadjob](preloadjob.md) |  |  |
| [exp:publish_content](publish_content.md) |  |  |
| [exp:resetuserpassword](resetuserpassword.md) |  | Reset a user password with admin authentication or root bypass |
| [exp:services](services.md) | exp:services:compile, exp:services:list, exp:services:show, exp:services:check | Compile, list, show and check the kernel service container (verbs compile, list, show, check) |
| [exp:setupfiles](setupfiles.md) | exp:setup:htaccess, exp:setup:config, exp:setup:status | Copy .htaccess_root (or .htaccess_root_static) to .htaccess and config.php-RECOMMENDED to config.php, with a diff, a backup and --force for an existing file (dry run unless --apply) |
| [exp:solr](solr.md) | exp:search | Control the Solr search server, once it is installed and configured |
| [exp:systeminfo](systeminfo.md) | exp:system:info | The facts and health checks of Setup > System information, as text or JSON (--json, --sizes) |
| [exp:trashpurge](trashpurge.md) |  | Permanently delete all objects in the trash (dry run unless --apply) |
| [exp:updateisbn13](updateisbn13.md) |  | Update the ISBN range data in the database from an XML file (--url is required) |
| [exp:updateniceurls](updateniceurls.md) |  | Import old URL aliases and update the URL aliases of all nodes (--update-nodes) |
| [exp:updatesearchindex](updatesearchindex.md) |  | Reindex all content objects in the search engine |
| [exp:upgrade](upgrade.md) |  | Upgrade the installation in one call: every stage in order (dry run unless --apply) |
| [exp:upgradebackup](upgradebackup.md) | exp:upgrade:backup | Upgrade backup: the backup exp:upgrade makes before it writes, on its own (database dump and settings overrides) |
| [exp:upgradecaptions](upgradecaptions.md) | exp:upgrade:captions | Upgrade captions: the safe points to roll back to (git_manager's and exp:upgrade's), checked with --verify, pruned with --prune |
| [exp:upgradecode](upgradecode.md) | exp:upgrade:code | Upgrade stage 1, code: the git checkouts moved to the target release, fast-forward only, through git_manager (dry run unless --apply) |
| [exp:upgradedatabase](upgradedatabase.md) | exp:upgrade:database | Upgrade stage 3, database: the schema and data steps on every engine (dry run unless --apply) |
| [exp:upgradefinish](upgradefinish.md) | exp:upgrade:finish | Upgrade stage 5, finish: deploy the changes (exp:velocity deploy --kernel) and check the site answers (dry run unless --apply) |
| [exp:upgradelibraries](upgradelibraries.md) | exp:upgrade:libraries | Upgrade stage 2, libraries: Composer install from composer.lock (or update of named packages) with a security audit (dry run unless --apply) |
| [exp:upgraderollback](upgraderollback.md) | exp:upgrade:rollback | Upgrade rollback: put the database, storage, settings, libraries and code back to a caption (dry run unless --apply) |
| [exp:upgradescripts](upgradescripts.md) | exp:upgrade:scripts | Upgrade stage 4, scripts: the PHP upgrade steps, policies and repairs (dry run unless --apply) |
| [exp:upgradestatus](upgradestatus.md) | exp:upgrade:status | Upgrade status: every upgrade step with its state (done, pending, not-needed, failed, unknown) |
| [exp:upgradesteps](upgradesteps.md) | exp:upgrade:steps | Upgrade steps: the registered steps with id, stage, version, engines and description |
| [exp:upgradeverify](upgradeverify.md) | exp:upgrade:verify | Upgrade verify: re-detect every step and report drift; --after: the full check after an upgrade (steps, smoke addresses, tests, error log, autoloads) |
| [exp:userlogincheck](userlogincheck.md) | exp:user-login-check | Unique logins: list the logins more than one user account has; --fix-index adds the unique index on ezuser.login once there are none |
| [exp:userpasswordchangerequired](userpasswordchangerequired.md) | exp:user-password-change-required | Change password at next sign-in: show, set or clear the mark of a user account |
| [exp:velocity-router](velocity-router.md) |  |  |
| [exp:velocity-warmup](velocity-warmup.md) |  |  |
| [exp:velocity](velocity.md) | exp:vc |  |
| [exp:verify_aliases](verify_aliases.md) |  |  |
| [exp:warm](warm.md) |  | Request every published page so no visitor pays for a render (--limit, --concurrency) |
| [exp:webserver](webserver.md) |  | Control the Exponential web server (php, frankenphp or qbix engines) |

## Shell scripts

`bin/shell/<name>.sh` runs as `shell:<name>`:

| Command | File | What it does |
|---|---|---|
| `shell:checkcalltimeref` | `bin/shell/checkcalltimeref.sh` | Check PHP source files for deprecated call-time pass-by-reference usage |
| `shell:checkcode` | `bin/shell/checkcode.sh` | Check PHP source files for short open tag coding standard violations |
| `shell:checkdbschema` | `bin/shell/checkdbschema.sh` | Verify database schema files against the expected baseline schema |
| `shell:checkdbupdate` | `bin/shell/checkdbupdate.sh` | Check database update files for consistency with the current version |
| `shell:checkforeach` | `bin/shell/checkforeach.sh` | Check PHP foreach loop syntax for Exponential coding standard compliance |
| `shell:checkini` | `bin/shell/checkini.sh` | Find INI configuration files that do not conform to valid PHP parse syntax |
| `shell:checkprint` | `bin/shell/checkprint.sh` | Check PHP source files for print() statement usage |
| `shell:checkversionnumbers` | `bin/shell/checkversionnumbers.sh` | Check version number consistency across source files (deprecated since 5.0) |
| `shell:clearcache` | `bin/shell/clearcache.sh` | Deprecated — clear site caches using bin/php/ezcache.php instead |
| `shell:common` | `bin/shell/common.sh` |  |
| `shell:creatediff` | `bin/shell/creatediff.sh` | Create a patch/diff archive between two Exponential release versions |
| `shell:distcommon` | `bin/shell/distcommon.sh` | Shared functions library sourced by distribution build scripts (not executable) |
| `shell:extensionscommon` | `bin/shell/extensionscommon.sh` | Shared functions library sourced by extension packaging scripts (not executable) |
| `shell:fixlicense` | `bin/shell/fixlicense.sh` | Update legacy eZ Systems license URLs in PHP source files |
| `shell:generatefilelist` | `bin/shell/generatefilelist.sh` | Generate share/filelist.md5 file integrity checksums for the current release |
| `shell:i18ncontrol` | `bin/shell/i18ncontrol.sh` | Control and manage i18n translation source files |
| `shell:i18nstats` | `bin/shell/i18nstats.sh` | Display translation coverage statistics for specified locales |
| `shell:makeaddonpackages` | `bin/shell/makeaddonpackages.sh` | Build add-on packages for inclusion in a distribution release |
| `shell:makedist` | `bin/shell/makedist.sh` | Build a full Exponential release distribution package (deprecated since 5.0) |
| `shell:makedoc` | `bin/shell/makedoc.sh` | Dump the current running site as static HTML for offline documentation |
| `shell:makei18ndist` | `bin/shell/makei18ndist.sh` | Build i18n translation distribution packages for specified locales |
| `shell:makestylepackages` | `bin/shell/makestylepackages.sh` | Build style and design distribution packages |
| `shell:packagescommon` | `bin/shell/packagescommon.sh` | Shared functions library sourced by package build scripts (not executable) |
| `shell:packext` | `bin/shell/packext.sh` | Package an extension directory into a distributable archive |
| `shell:phpcheck` | `bin/shell/phpcheck.sh` | Find PHP source files with syntax errors and print their paths |
| `shell:redumpall` | `bin/shell/redumpall.sh` | Re-dump all SQL database schemas and seed data to SQL files |
| `shell:setdistribution` | `bin/shell/setdistribution.sh` | Set the distribution name and version properties for a release build |
| `shell:setupdist` | `bin/shell/setupdist.sh` | Set up a distribution build environment and staging directory structure |
| `shell:sqlcommon` | `bin/shell/sqlcommon.sh` | Shared database connection variables sourced by SQL scripts (not executable) |
| `shell:sqlredump` | `bin/shell/sqlredump.sh` | Re-dump a specific SQL schema or data set to a named output file |
| `shell:stats` | `bin/shell/stats.sh` | Display code statistics including line counts and file totals for the repository |
| `shell:updateisbn13` | `bin/shell/updateisbn13.sh` | Download updated ISBN-13 publisher prefix data and import it into the database |
| `shell:updatelicense` | `bin/shell/updatelicense.sh` | Update license header blocks across all source files |
| `shell:updatetranslations` | `bin/shell/updatetranslations.sh` | Download and update translation files for all configured locales |
| `shell:verifyfiles` | `bin/shell/verifyfiles.sh` | Verify repository file integrity against share/filelist.md5 checksums |

- [All examples](../README.md)

---

Generated by `./console exp:help:examples` from `bin/php/console`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
