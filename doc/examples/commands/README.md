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
| [exp:adddefaultstates](adddefaultstates.md) |  |  |
| [exp:addexphelpreadpolicy](addexphelpreadpolicy.md) | exp:help-grant | Grant exphelp/read (the help system) to the Editor role or the roles named (--dry-run lists them first) |
| [exp:addoauthauthorizepolicy](addoauthauthorizepolicy.md) |  | Grant oauth/authorize to the roles that let users sign in (6.0.15 upgrade; --dry-run lists them first) |
| [exp:audit](audit.md) |  | Audit log: tail, show one event, verify the hash chains, list the channels |
| [exp:benchmark](benchmark.md) | exp:bench | Measure this installation: page timings over HTTP, A/B between servers, kernel probes, regression check |
| [exp:cache](cache.md) |  | Clear and inspect caches by tag, id or all: content, static, HTTP, Velocity, OPcache, APCu |
| [exp:changelog2xmltext](changelog2xmltext.md) |  | Convert a Changelog file to XML text format and print it to standard output |
| [exp:checkclasses](checkclasses.md) |  | Load every declared class and report the ones PHP refuses to load |
| [exp:checkcodestyle](checkcodestyle.md) |  |  |
| [exp:checkdbfiles](checkdbfiles.md) |  | Verify the database update files against the upgrade path; reports missing or stray files |
| [exp:checkmanifest](checkmanifest.md) |  |  |
| [exp:cleanuppolicies](cleanuppolicies.md) |  | Remove policies of modules that no longer exist (--dry-run lists them first) |
| [exp:cleanuprss](cleanuprss.md) |  | Trim RSS-imported content, keeping the newest items of each active import |
| [exp:cleanupversions](cleanupversions.md) |  | Remove archived content versions beyond the VersionManagement limits (-n: do not wait) |
| [exp:clusterize](clusterize.md) |  | Move var files into the database cluster storage (-u moves them back) |
| [exp:clusterpurge](clusterpurge.md) |  | Physically purge the cluster files of the given scopes (--scopes=scope1,scope2) |
| [exp:collaborationsampledata](collaborationsampledata.md) | exp:collaboration:sample-data | Build sample content for the collaboration tool (approvals, comments, groups), or remove it again |
| [exp:contentcheck](contentcheck.md) | exp:content-check | Content checks: build the reference index, list the unused assets and the broken relations below a node, list the missing files, scan the storage files (report only, unless --fix, --quarantine or --fix-permissions without --dry-run) |
| [exp:convertezenumtoezselection](convertezenumtoezselection.md) |  |  |
| [exp:convertprice2multiprice](convertprice2multiprice.md) |  | Convert content objects with 'price' datatype attributes to 'multiprice' |
| [exp:dfscleanup](dfscleanup.md) |  | Check database and DFS files against each other (-S, -B) and delete nonexistent ones (-D) |
| [exp:expcontentjob](expcontentjob.md) |  | Run, list, resume and cancel content jobs: large subtree removes and copies in batches |
| [exp:exphelp](exphelp.md) | exp:help | The help system on the command line: build and describe the index of doc/, search it, show a page's metadata, check its links and images |
| [exp:exphelpcontent](exphelpcontent.md) | exp:help:content | Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall |
| [exp:exphelpexamples](exphelpexamples.md) | exp:help:examples | Generate the reference examples of the help below doc/examples/ from the code: template operators and functions, fetch functions, module views, policies, INI settings, datatypes, workflow events, cronjobs, console commands and extension points |
| [exp:explogs](explogs.md) | exp:logs | The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives |
| [exp:exploremipsumfill](exploremipsumfill.md) | exp:loremipsum | Fill a translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text |
| [exp:explovestack](explovestack.md) | exp:lovestack | Check an installation coming from LoveStack (read-only), and record its database as Exponential's (migrate) |
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
| [exp:ezsubtreecopy](ezsubtreecopy.md) |  | Copy a content subtree to a new location (--src-node-id, --dst-node-id) |
| [exp:ezsubtreeremove](ezsubtreeremove.md) |  | Remove content subtrees by node ID, to the trash unless --ignore-trash is given |
| [exp:eztc](eztc.md) |  | Compile the templates of a siteaccess into the template cache (--force recompiles all) |
| [exp:eztemplatecheck](eztemplatecheck.md) |  | Check the syntax of the templates of a siteaccess or of a design directory |
| [exp:ezwebincommon](ezwebincommon.md) |  | Helper library of ezwebininstall and ezwebinupgrade, not meant to be run directly |
| [exp:ezwebininstall](ezwebininstall.md) |  | Install the ezwebin site package and configure the site |
| [exp:ezwebinupgrade](ezwebinupgrade.md) |  | Upgrade an existing ezwebin installation to the current package version (--to-version) |
| [exp:flatten](flatten.md) |  | Remove unused data from the database: contentobject, contentclass, workflow, role or all |
| [exp:frankenphp](frankenphp.md) | exp:fp | Control the FrankenPHP engine of the Exponential web server |
| [exp:i18n](i18n.md) |  | The translation fallbacks: per locale the chain of translations it falls back to, and how much of the kernel and of each extension it shows translated; --check for CI |
| [exp:ini](ini.md) |  | Read and change INI settings in every scope: get, set, add, rem, clear, toggle, copy, move, where, list, scopes |
| [exp:install](install.md) | exp:names | One-command installation, SQLite by default, no kickstart.ini needed |
| [exp:kickstarter](kickstarter.md) |  | Kickstarter runner. Supports "ini" generation and full "run" setup subcommands |
| [exp:mailbounces](mailbounces.md) | exp:mail:bounces | Read the bounce mailbox: hard bounces and spam complaints go on the suppression list |
| [exp:mailconsent](mailconsent.md) | exp:mail:consent | The consent log of the e-mail preferences: list, export as CSV, retention cleanup |
| [exp:mailgate](mailgate.md) | exp:mail:gate | Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like |
| [exp:mailpreferences](mailpreferences.md) | exp:mail:preferences | Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase |
| [exp:mailstatus](mailstatus.md) | exp:mail:status | Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems |
| [exp:mailsuppression](mailsuppression.md) | exp:mail:suppression | The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored) |
| [exp:maintenance](maintenance.md) |  | Take the site offline for maintenance and back: on, off or status |
| [exp:makestaticcache](makestaticcache.md) |  | Crawl the public siteaccesses and generate static pages (--site, --max-pages, --max-depth) |
| [exp:notificationevents](notificationevents.md) | exp:notification:events, exp:notify:events | List the notification events, or remove the old ones |
| [exp:notificationrun](notificationrun.md) | exp:notification:run, exp:notify:run | Run the notification filter: send the notifications of the pending events and the due digests (--dry-run: only list them) |
| [exp:notificationstatus](notificationstatus.md) | exp:notification:status, exp:notify:status | Show the notification system: pending events, digest items, subscriptions, the last runs and problems |
| [exp:notificationsubscriptions](notificationsubscriptions.md) | exp:notification:subscriptions, exp:notify:subscriptions | List the subtree notification subscriptions per user and subtree, or remove those whose content is gone |
| [exp:phar](phar.md) |  | Build and inspect the engine archive dist/engine.phar (verbs build, check, clean, info) |
| [exp:preload](preload.md) |  | Warm the page caches of a site: request its section pages, then follow its links, and report the broken ones |
| [exp:preloadjob](preloadjob.md) |  |  |
| [exp:publish_content](publish_content.md) |  |  |
| [exp:resetuserpassword](resetuserpassword.md) |  | Reset a user password with admin authentication or root bypass |
| [exp:solr](solr.md) | exp:search | Control the Solr search server, once it is installed and configured |
| [exp:systeminfo](systeminfo.md) | exp:system:info | The facts and health checks of Setup > System information, as text or JSON (--json, --sizes) |
| [exp:trashpurge](trashpurge.md) |  | Permanently delete all objects in the trash |
| [exp:updateisbn13](updateisbn13.md) |  | Update the ISBN range data in the database from an XML file (--url is required) |
| [exp:updateniceurls](updateniceurls.md) |  | Import old URL aliases and update the URL aliases of all nodes (--update-nodes) |
| [exp:updatesearchindex](updatesearchindex.md) |  | Reindex all content objects in the search engine |
| [exp:userlogincheck](userlogincheck.md) | exp:user-login-check | Unique logins: list the logins more than one user account has; --fix-index adds the unique index on ezuser.login once there are none |
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
