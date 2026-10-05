# 15. Migrating from the 5.x legacy stack

This chapter moves a site of the 5.x era to Exponential 6.0: eZ Publish 5.0 to 5.4 and the community releases
2013.x and 2014.x, which ran two kernels side by side (the Symfony "new stack" in `ezpublish/` and the legacy kernel
in `ezpublish_legacy/`, joined by the legacy bundle that later became LegacyBridge), and the 5.90 line that followed.
Exponential is the continuation of that legacy kernel, so the move is an extraction rather than a rewrite: you take
`ezpublish_legacy/` out of the Symfony application, write down in INI files what the Symfony side used to inject into
it at run time, and upgrade the database in place. The chapter covers every point of the vendor's own migration
guide for this path and says where the Exponential code does something different, then goes further: the shared
database tables, field types against datatypes, users and password hashes, image variations against image aliases,
siteaccess configuration in YAML against `site.ini`, routes, caches, Twig against TPL, REST, cron, search, clustering,
the database steps per engine, verification, rollback, a timeline and a checklist. For a site that never ran the
Symfony side, read [chapter 14](14-migrating-from-4x.md) instead; for the version-by-version update files, see
[chapter 11](11-upgrading.md).

[Contents](README.md) · Previous: [14. Migrating from the 4.x line](14-migrating-from-4x.md) · Next: [16. Migrating from eZ Platform and Ibexa](16-migrating-from-ez-platform-and-ibexa.md)

## 15.1 What changes, and what the vendor's guide says

The vendor documented this migration for a different target: eZ Platform (later Ibexa DXP), a Symfony-only product
that drops the legacy kernel. Its pages are
[Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/),
[Migrating from eZ Publish Platform](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
and [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/).
Exponential goes the other way: it keeps the legacy kernel, its datatypes, its templates and its admin, and drops the
Symfony side. Most of the vendor's steps therefore either do not apply or apply in reverse. Every point it makes is
listed here with where this chapter covers it.

| The vendor's point | For Exponential | Section |
|---|---|---|
| Upgrade in stages: 5.4 or 2014.11 to eZ Platform 1.7, then 1.13, then 2.x | One path: the 5.4 database to 6.0.0 to 6.0.15, in place | [15.7](#157-the-database-step-by-step) |
| Requirements: PHP 7.1, MySQL 5.5 or MariaDB, a 2017 browser | PHP 8.0 to 8.5 (Velocity needs 8.1); MySQL, MariaDB, PostgreSQL, SQLite, Oracle, MongoDB | [15.4](#154-plan-and-timeline) |
| Back up; take the site offline | Same, with `maintenance.php` | [15.7](#157-the-database-step-by-step) |
| Content of the 4th and 5th generation stays compatible | Yes, and without conversion: it is the same schema | [15.3](#153-what-carries-over-the-shared-database) |
| XmlText is replaced by RichText; convert with `ezxmltext:convert-to-richtext` | **Contradicted.** XmlText (`ezxmltext`) is the kernel's rich text datatype. Nothing is converted. | [15.8](#158-field-types-against-datatypes) |
| The Page field (ezflow) is replaced by the Enterprise LandingPage; migrate with `ezflow-migration-toolkit` | **Contradicted.** `ezpage` stays, through the `ezflow` package, which Exponential installs by default | [15.8](#158-field-types-against-datatypes) |
| Star Rating is not supported | **Contradicted.** `ezsrrating` stays, through the `ezstarrating` package, installed by default | [15.8](#158-field-types-against-datatypes) |
| Sorting by class identifier (6) or class name (7) is not supported; find it with SQL | **Contradicted.** Both sort fields exist (`eZContentObjectTreeNode::SORT_FIELD_CLASS_IDENTIFIER = 6`, `SORT_FIELD_CLASS_NAME = 7`). The query is still useful to know | [15.20](#1520-common-issues) |
| Remove internal drafts before migrating (`InternalDraftsCleanUpLimit`, `InternalDraftsDuration` to 0) | The same settings and cronjob exist; recommended | [15.7](#157-the-database-step-by-step) |
| Rewrite custom field types, front end and admin modules for the Symfony stack | **Reversed.** Legacy datatypes, `.tpl` templates and modules keep working; Symfony bundles, Twig templates and Public API code are what you rewrite | [15.12](#1512-templates-twig-to-tpl), [15.11](#1511-routes-and-url-aliases) |
| Move `src/`, Composer packages and bundle registrations to the new project | Bundles have no place in Exponential; their logic becomes extensions | [15.5](#155-extract-the-legacy-part) |
| Move `parameters.yml`, `config.yml`, `ezpublish.yml` into the new `app/config/` | **Reversed.** Their values go into INI files | [15.6](#156-translate-the-symfony-configuration-into-ini) |
| Keep the SiteAccess names, or `user/login` policies break | Same rule, same reason (the limitation stores a CRC32 of the name) | [15.6](#156-translate-the-symfony-configuration-into-ini), [15.20](#1520-common-issues) |
| Define the legacy image aliases again in YAML | **Reversed.** The YAML `image_variations` must become `image.ini` aliases, because the bridge replaced the legacy alias list with them | [15.10](#1510-image-variations-and-image-aliases) |
| Optional: install Legacy Bridge and symlink `src/legacy_files` into `ezpublish_legacy/` | Not needed; Exponential runs on its own. A bridge to a Symfony platform still exists if you want one | [15.5](#155-extract-the-legacy-part) |
| Copy designs, siteaccess settings, override settings, extensions (not the built-in ones), `config.php`, `config.cluster.php`, `var/storage/packages` | Same list, with the Exponential package for each built-in extension | [15.5](#155-extract-the-legacy-part) |
| Copy binary files from `web/var/<site>/storage` (a symlink to `ezpublish_legacy/var`) | Same | [15.5](#155-extract-the-legacy-part) |
| Re-apply permissions; `composer update` | Permissions as in chapter 8; never `composer update` over your own checkouts | [15.5](#155-extract-the-legacy-part) |
| Apply `dbupdate-5.4.0-to-6.13.0.sql` from the new kernel | **Do not.** Apply the Exponential files; but one statement of that file, the wider `password_hash` column, is needed and is not in the Exponential 5.4 to 6.0 file | [15.7](#157-the-database-step-by-step), [15.9](#159-users-password-hashes-and-sessions) |
| Enterprise schemas (date-based publisher, form builder, notifications) | Not applicable | none |
| Custom tags and their attribute types in RichText | Not applicable: custom tags stay `ezxmltext` custom tags, with every `ezoe_attributes.ini` type | [15.8](#158-field-types-against-datatypes) |
| Varnish VCL; virtual host files in `doc/apache2`, `doc/nginx` | Exponential sends no purges to a proxy; its own caches replace it. Server configuration in chapter 8 | [15.14](#1514-caches), [15.18](#1518-serving-the-site) |
| `assets:install`, `assetic:dump` | Not needed: the kernel serves design files itself | [15.18](#1518-serving-the-site) |
| Unstyled login screen: add all siteaccesses to the Anonymous `user/login` limitation | Same cause and fix | [15.20](#1520-common-issues) |
| Old-style URL aliases: switch the slug converter to `urlalias_compat` or `urlalias_iri` | The legacy setting `[URLTranslator] TransformationGroup` | [15.11](#1511-routes-and-url-aliases) |
| Common issues: regenerate URL aliases, normalise image paths, "Unknown relation type 0", the always-available flag on all fields, empty `sort_key_string` | Each with its Exponential cause and fix; the `ezpublish:update:*` commands do not exist here | [15.20](#1520-common-issues) |
| "Practically impossible" to run the new platform beside legacy | Not the target here; the se7enxweb bridge keeps the legacy schema and does run both | [15.5](#155-extract-the-legacy-part) |

## 15.2 Which variant do you run?

Three kinds of site came out of the 5.x era. Look at the old installation's root:

| You see | You run | Read |
|---|---|---|
| `ezpublish/` (with `EzPublishKernel.php`, `console`, `config/ezpublish.yml`), `ezpublish_legacy/`, `web/`, `src/` | the dual-kernel stack: 5.0 to 5.4, or community 2013.x and 2014.x | this chapter |
| only the legacy tree (`index.php`, `kernel/`, `lib/`, `settings/`, `extension/` in the root), `lib/version.php` with `VERSION_MAJOR = 5` and `VERSION_MINOR = 90` | the 5.90 legacy-only line (2015.01, 2017.x and later) | [chapter 11](11-upgrading.md#115-from-54-or-590-to-600), plus [15.9](#159-users-password-hashes-and-sessions) |
| the dual-kernel tree, but the web server's document root points at `ezpublish_legacy/` | the legacy kernel run without Symfony | this chapter; the parts about injected settings matter less |

The checks, from the old root:

```bash
ls ezpublish/EzPublishKernel.php ezpublish/config/ezpublish.yml ezpublish_legacy/lib/version.php
grep -n "const VERSION_\|const EDITION" ezpublish_legacy/lib/version.php
grep -n '"symfony-app-dir"\|ezpublish-kernel\|ezpublish-legacy' composer.json
composer show ezsystems/ezpublish-kernel ezsystems/ezpublish-legacy
```

What the answers mean, checked against the upstream repositories:

| Release | `lib/version.php` of the legacy kernel | `ezsite_data` `ezpublish-version` | `ezuser.password_hash` | Hash types known |
|---|---|---|---|---|
| community 2014.11 | `VERSION_MAJOR = 2014`, `VERSION_MINOR = 11`, `VERSION_ALIAS = '5.4'`, edition "eZ Publish Community Project" | `5.4.0alpha1` | `varchar(50)` | 1 to 5 |
| 5.4 (enterprise) | read it in your copy | `5.4.0alpha1` after the 5.3 to 5.4 update file (it writes that value) | `varchar(50)` unless installed fresh with a wider column: no file of the update chain widens it | look for `PASSWORD_HASH_PHP_DEFAULT` ([15.21](#1521-rollback)) |
| 2015.01, 2017.08 | `5`, `90`, alias `5.90` or `2017.08` | | `varchar(50)` | 1 to 5 |
| 2017.12 and later | `5`, `90`, alias `2017.12` and up | | `varchar(255)` | 1 to 7 |

The community project's `composer.json` (`ezsystems/ezpublish-community`, tag `v2014.11.0`) requires
`ezsystems/ezpublish-kernel` and `ezsystems/ezpublish-legacy` and sets `"symfony-app-dir": "ezpublish"`; that key is what
makes the installer plugin put the legacy kernel into `ezpublish_legacy/`. Read the database version as in
[11.2](11-upgrading.md#112-which-version-do-you-run). A `5.4.0alpha1` there is normal for a 5.4 database and does not
mean a pre-release was installed.

## 15.3 What carries over: the shared database

**The new stack of 5.x had no tables of its own.** Its persistence layer, the "legacy storage engine", read and wrote
the legacy schema directly. Compared table by table at tag `v2014.11.0`, the new stack's `data/mysql/schema.sql` holds
every one of the 118 tables of the legacy `kernel/sql/mysql/kernel_schema.sql` and ten more, which belong to legacy
extensions, not to Symfony: `ezcomment`, `ezcomment_notification`, `ezcomment_subscriber`, `ezcomment_subscription`
(ezcomments), `ezgmaplocation` (ezgmaplocation), `ezm_block`, `ezm_pool` (ezflow), `ezstarrating`,
`ezstarrating_data` (ezstarrating) and `ezsearch_return_count`, which the 5.3 to 5.4 update file drops.

So content created through the new stack (the Public API, REST API v2, a Symfony import command) is made of the same
rows as content created in the legacy admin: `ezcontentobject`, `ezcontentobject_attribute`, `ezcontentobject_tree`,
`ezcontentobject_name`, `ezurlalias_ml`, `ezuser`, and so on. Exponential reads all of it. The exceptions are
field types that existed only on the Symfony side ([15.8](#158-field-types-against-datatypes)).

**Exponential's schema is that legacy schema plus ten tables**, all created by the update steps:
`expaudit_cursor`, `expaudit_event`, `expaudit_file` (audit index), `expbookmark_folder` (bookmark folders),
`expmail_category`, `expmail_consent_log`, `expmail_pending`, `expmail_preference`, `expmail_suppression`
(e-mail preferences) and `ezrss_export_opml_item` (OPML export). No legacy table was removed.

**Tables Exponential does not know are ignored.** A Symfony session table, a table of your own bundle, or tables of an
eZ Platform update that someone already started are left alone; the kernel never reads them. List what you have and
compare with the lists above:

```bash
mysql -u USER -p DATABASE -e "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY table_name;"
psql -U USER DATABASE -c "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename;"
```

Or let the schema tool compare the database with the schema of this release (`share/db_schema.dba`); every
`CREATE TABLE` or `DROP TABLE` in its output names a table that only one side has. Read the output; do not run it.

```bash
php bin/php/ezsqldiff.php --type=mysql --user=USER --password=PASS share/db_schema.dba DATABASE
```

If someone already applied the eZ Platform file `dbupdate-5.4.0-to-6.13.0.sql`, the database also carries its changes:
`ezsite_data` says `6.13.0`, `ezuser_login` became a unique index, `ezuser` attributes are marked not searchable, and
`content/publish` policies were added to roles that had `content/create` or `content/edit`. None of that stops
Exponential (the kernel has a `content/publish` policy function of its own), but set the version row as in
[15.7](#157-the-database-step-by-step) so that the update files apply. A site that went further and runs on eZ
Platform or Ibexa is covered by [chapter 16](16-migrating-from-ez-platform-and-ibexa.md).

## 15.4 Plan and timeline

**Requirements of the target.** PHP 8.0 to 8.5 (`composer.json`: `^8.0`), Velocity 8.1 or later; the old site ran on
PHP 5.3.17 or later (the community project's minimum), so every PHP file of your own extensions crosses PHP 7 and 8.
See [chapter 2](02-requirements.md), [PHP 8 support](../bc/6.0/php8.md) and the PHP migration guides in the
references. Databases: [chapter 9](09-databases.md).

**Inventory first.** Before any change, list from the old site:

1. The siteaccesses and how each is matched (`ezpublish.yml`, `siteaccess:` block).
2. Everything the Symfony side injected into legacy ([15.6](#156-translate-the-symfony-configuration-into-ini)).
3. The datatypes in use ([15.8](#158-field-types-against-datatypes)), especially any `ezrichtext`.
4. Your Symfony bundles in `src/` and in `composer.json`, with what each does: routes, controllers, Twig templates,
   event or signal-slot listeners, console commands, REST extensions.
5. Your legacy extensions and designs in `ezpublish_legacy/`.
6. Cron entries, the search engine, the cluster setup, the HTTP cache or Varnish in front.

**Timeline.** The durations depend on the amount of Symfony code, not on the amount of content. A workable order:

| Phase | What | Done when |
|---|---|---|
| 1. Inventory | the list above | every bundle has an owner and a decision: rewrite as extension, drop, or keep on a bridge |
| 2. Rehearsal install | a new Exponential next to the old site, a copy of the database, sections 15.5 to 15.7 | the copy opens in the admin and the front page renders |
| 3. Rewrite | Twig front end as TPL, bundle logic as extensions, routes as modules | the rehearsal site passes [15.19](#1519-verify) |
| 4. Dress rehearsal | the whole procedure again from a fresh dump, timed | you know the length of the frozen window |
| 5. Freeze | editors stop; final dump; maintenance mode on the old site | |
| 6. Cutover | database steps on the final dump, storage sync, DNS or virtual host switch | [15.19](#1519-verify) passes on the live address |
| 7. Watch | logs, sign-ins, search, cron for a week; keep the old site ready | [15.21](#1521-rollback) no longer needed |

Run phase 2 more than once; the database part of it is the measurement for phase 6.

## 15.5 Extract the legacy part

Do not install Exponential into the old project. The old `composer.json` sets `symfony-app-dir`, so a
`composer require` there would put Exponential into `ezpublish_legacy/` of a Symfony application that no longer
matches it. Make a new directory:

```bash
composer create-project se7enxweb/exponential exponential
```

(Other ways in [chapter 3](03-getting-the-code.md).) Then copy from the old tree, `<old>` being its root:

| From `<old>/` | To the new root | Note |
|---|---|---|
| `ezpublish_legacy/settings/override/` | `settings/override/` | then add what [15.6](#156-translate-the-symfony-configuration-into-ini) lists |
| `ezpublish_legacy/settings/siteaccess/<yours>/` | `settings/siteaccess/<yours>/` | keep the names |
| `ezpublish_legacy/extension/<yours>/` | `extension/<yours>/` | not the built-in ones (below) |
| `ezpublish_legacy/design/<yours>/` | `design/<yours>/` | not `admin`, `admin2`, `base`, `standard` |
| `ezpublish_legacy/var/<site>/storage/` (also reachable as `web/var/<site>/storage`) | `var/<site>/storage/` | images and files; keep the same `<site>` directory name |
| `ezpublish_legacy/var/storage/packages/` | `var/storage/packages/` | if you use packages |
| `ezpublish_legacy/config.php`, `config.cluster.php` | the new root | compare with `config.php-RECOMMENDED` first |

Symlinks: many projects kept settings and designs in version control outside `ezpublish_legacy/` and linked them in (by hand,
or with the `ezpublish:legacy:symlink` command and `src/legacy_files/` of the later Legacy Bridge). Copy the files the links point at, not the links: `cp -rL` or `rsync -aL`.

The built-in extensions of the 5.x legacy tree are not copied; Exponential has its own versions, most of them
installed with it (the `require` list of `composer.json`):

| 5.x built-in | Exponential |
|---|---|
| `ezjscore`, `ezoe`, `ezformtoken` | in the kernel repository |
| `ezflow`, `ezodf`, `ezie`, `ezmultiupload`, `ezmbpaex`, `ezprestapiprovider`, `ezwebin`, `ezdemo`, `ezstarrating`, `ezgmaplocation` | installed by default (`se7enxweb/<name>`) |
| `ezfind`, `ezscriptmonitor`, `ezsi` | suggested: `composer require se7enxweb/<name>` |
| `ezcomments` | `composer require se7enxweb/ezcomments` |
| `ez_network` | no equivalent (it tied the site to the vendor's support service); remove it from `ActiveExtensions` |

Then activate your own extensions in `settings/override/site.ini.append.php` (`[ExtensionSettings]
ActiveExtensions[]`), generate the class maps and set permissions:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:checkclasses
```

`exp:checkclasses` lists every class PHP refuses to load, which is how PHP 5 code in an old extension shows. File
permissions: [8.7](08-serving-the-site.md#87-file-permissions-and-ownership).

If you would rather keep a Symfony front end and run Exponential inside it, the se7enxweb legacy bridge does that on
current Symfony versions: [Legacy bridge](../features/6.0/legacy-bridge.md). The rest of this chapter assumes
Exponential runs on its own.

## 15.6 Translate the Symfony configuration into INI

**Under the dual-kernel stack, legacy did not read several of its own settings.** Before building the legacy kernel
for each request, the legacy bundle's configuration mapper (`LegacyMapper\Configuration`, checked at tag
`v2014.11.0`; the se7enxweb bridge does the same today, see the
[bridge specification](../specifications/6.0/legacy-bridge-bundle.md)) overwrote them with values from the Symfony
configuration. After the extraction nothing overwrites them, so whatever the legacy INI files say is what runs, and
in a 5.x project they were often empty or stale. Write each one:

| Legacy setting | Came from (Symfony side) | Where to write it |
|---|---|---|
| `site.ini [DatabaseSettings] Server`, `Port`, `User`, `Password`, `Database`, `Socket` | Doctrine connection: `host`, `port`, `user`, `password`, `dbname`, `unix_socket` (no socket: `disabled`) in `parameters.yml` / `ezpublish.yml` | `settings/override/site.ini.append.php` |
| `site.ini [DatabaseSettings] DatabaseImplementation` | Doctrine driver: `pdo_mysql` → `ezmysqli`, `pdo_pgsql` → `ezpostgresql`, `oci8` → `ezoracle` | same |
| `site.ini [FileSettings] VarDir`, `StorageDir` | `var_dir`, `storage_dir` | per siteaccess, or global if one site |
| `site.ini [UserSettings] AnonymousUserID` | `anonymous_user_id` | global |
| `site.ini [SiteAccessSettings] PathPrefix`, `PathPrefixExclude[]` | `content.tree_root.location_id`, `content.tree_root.excluded_uri_prefixes` | per siteaccess |
| `site.ini [SiteSettings] IndexPage`, `DefaultPage` | `/content/view/full/<root location>/`, or `default_page` | per siteaccess |
| `content.ini [NodeSettings] RootNode` | `content.tree_root.location_id` | per siteaccess |
| `logfile.ini [AccessLogFileSettings] PathPrefix[]` | the path prefix above | global |
| `image.ini [AliasSettings] AliasList[]` and each `[<alias>] Reference`, `Filters[]` | **all** `image_variations` (the legacy list was replaced, not extended) | [15.10](#1510-image-variations-and-image-aliases) |
| `image.ini [FileSettings] TemporaryDir`, `PublishedImages`, `VersionedImages` | `image.temporary_dir`, `image.published_images_dir`, `image.versioned_images_dir` | global |
| `image.ini [ImageMagick] IsEnabled`, `ExecutablePath`, `Executable`, `PreParameters`, `PostParameters`, `Filters[]` | `imagemagick:` options and `imagemagick.pre_parameters`, `post_parameters` | global |
| the form token secret (`site.ini [HTMLForms] Secret`) | Symfony `kernel.secret` (`secret` in `parameters.yml`), or the token switched off when Symfony's CSRF protection was off | global; a long random value |
| `file.ini [ClusteringSettings]`, `[eZDFSClusteringSettings]` (later bridge versions) | the DFS configuration | [15.17](#1517-cluster-and-dfs) |

`site.ini [ContentSettings] ViewCaching` was forced to `enabled` by the later bridge; it is `enabled` by default in
Exponential. The secret matters: the 5.1 notes already said that a site without Symfony must set `[HTMLForms] Secret`
itself ([changes in 5.1](../bc/5.1/changes-5.1.txt)), and since 6.0.15 a POST without a valid form token is refused
with HTTP 403.

Set a value from the command line, and see which file wins:

```bash
php bin/php/console exp:ini set site.ini/HTMLForms/Secret "<long random value>" global
php bin/php/console exp:ini where site.ini/DatabaseSettings/Database
```

### Siteaccesses: from `ezpublish.yml` to `site.ini`

In YAML a siteaccess is listed under `ezpublish: siteaccess: list`, grouped under `groups`, chosen by `match`, and
configured under `system: <siteaccess or group>`. In Exponential the list and the matching are global settings
(`settings/override/site.ini.append.php`, `[SiteAccessSettings]`), and each siteaccess's configuration lives in
`settings/siteaccess/<name>/`. Groups have no direct equivalent: put shared values in the global override or in an
extension's `settings/`, and per-site values in each siteaccess.

| YAML (`ezpublish.yml`) | `site.ini` |
|---|---|
| `siteaccess: list: [a, b]` | `[SiteAccessSettings] AvailableSiteAccessList[]=a`, `AvailableSiteAccessList[]=b`; also `[SiteSettings] SiteList[]` |
| `siteaccess: default_siteaccess: a` | `[SiteSettings] DefaultAccess=a` |
| `match: URIElement: 1` | `MatchOrder=uri`, `URIMatchType=element`, `URIMatchElement=1` |
| `match: Map\URI: { foo: a }` | `MatchOrder=uri`, `URIMatchType=map`, `URIMatchMapItems[]=foo;a` |
| `match: URIText: { prefix: .., suffix: .. }` | `URIMatchType=text`, `URIMatchSubtextPre=`, `URIMatchSubtextPost=` |
| `match: Regex\URI: { regex: .., itemNumber: 1 }` | `URIMatchType=regexp`, `URIMatchRegexp=`, `URIMatchRegexpItem=1` |
| `match: Map\Host: { www.example.com: a }` | `MatchOrder=host`, `HostMatchType=map`, `HostMatchMapItems[]=www.example.com;a` |
| `match: HostElement: 2` | `HostMatchType=element`, `HostMatchElement=2` |
| `match: HostText: { prefix: .., suffix: .. }` | `HostMatchType=text`, `HostMatchSubtextPre=`, `HostMatchSubtextPost=` |
| `match: Regex\Host: { regex: .., itemNumber: 1 }` | `HostMatchType=regexp`, `HostMatchRegexp=`, `HostMatchRegexpItem=1` |
| `match: Map\Port: { 8080: a }` | `MatchOrder=port`, `[PortAccessSettings] 8080=a` |
| `match: Compound\LogicalAnd` of a host and a URI | `MatchOrder=host_uri`, `HostUriMatchMapItems[]=www.example.com;en;a` |
| several matchers in order | `MatchOrder=uri;host;port` (tried left to right) |
| `system: a: languages: [eng-GB, fre-FR]` | `settings/siteaccess/a/site.ini.append.php`: `[RegionalSettings] SiteLanguageList[]=eng-GB`, `SiteLanguageList[]=fre-FR`, `Locale=eng-GB`, `ContentObjectLocale=eng-GB` |
| `system: a: session: name: ..` | `[Session] SessionNameHandler=custom`, `SessionNamePrefix=`, `SessionNamePerSiteAccess=enabled` |
| `system: a: legacy_mode: true` | nothing: legacy always handles the URLs now |
| `system: a: content: view_cache: ..` | `[ContentSettings] ViewCaching` |

Siteaccess matching in detail, with the setting names checked against `settings/site.ini`:
[10.2](10-after-installing.md#102-siteaccesses-and-site-addresses).

## 15.7 The database, step by step

Do this on a copy first ([15.4](#154-plan-and-timeline), phase 2), then on the final dump.

**1. Back up and stop writes.**

```bash
mysqldump -u USER -p DATABASE > before-exponential.sql
pg_dump -U USER DATABASE > before-exponential.sql
```

On the old site, stop editing (the old maintenance method of your project); on the new one, before it takes traffic:

```bash
php bin/php/maintenance.php on --message="Migration in progress" --until=2h
```

**2. Clean up internal drafts (recommended).** Internal drafts are versions the editor opened and never saved. The
vendor removes them because one of its tools fails on drafts without a name row; in Exponential they are harmless, but
they are noise in a migration. On the old site, or on the new one before it goes live, set in
`settings/override/content.ini.append.php`:

```ini
[VersionManagement]
InternalDraftsCleanUpLimit=0
InternalDraftsDuration[]
InternalDraftsDuration[days]=0
InternalDraftsDuration[hours]=0
InternalDraftsDuration[minutes]=0
InternalDraftsDuration[seconds]=0
```

```bash
php runcronjobs.php --script=internal_drafts_cleanup.php
```

Check the block name in your `content.ini` before writing it (the settings are in the kernel's `settings/content.ini`,
lines 45 to 51 in 6.0.15), and remove the override again afterwards: the defaults are one day and 100 per run.

**3. Bring the database to 5.4.** A 5.0 to 5.3 database first walks the old chain of
[11.3](11-upgrading.md#113-the-update-files) up to `5.4/dbupdate-5.3.0-to-5.4.0.sql` (plus the `dbupdate-cluster-`
files if the site used the database cluster), with the data scripts of each step from
[11.4](11-upgrading.md#114-from-310-to-53-the-old-chain). A 5.4 or 2014.11 database is already there.

**4. Record 6.0.0.** No schema change from 5.4 to 6.0.0, only the version rows:

```bash
mysql -u USER -p DATABASE -e "UPDATE ezsite_data SET value='6.0.0' WHERE name='ezpublish-version'; UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';"
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
```

The MySQL file `update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql` holds the same two `UPDATE` lines after a
`SET storage_engine=InnoDB;` that MySQL 5.7.6 and later and MariaDB reject; the command above runs only the two lines.

**5. Widen `ezuser.password_hash`.** Every 5.x schema up to 2017.08 has `password_hash varchar(50)`. Exponential stores
`php_default` hashes (bcrypt, 60 characters) and, with `UpdateHash=true`, rewrites each user's hash at the first
sign-in ([15.9](#159-users-password-hashes-and-sessions)). The 5.4 to 6.0.0 file does not widen the column, and the
6.0.0 to 6.0.15 file does not either; the statement exists only in `update/database/<engine>/6.12/dbupdate-5.4.0-to-6.12.0.sql`.
Run that one statement, not the file (its other lines would record version 6.12.0):

```sql
-- MySQL, MariaDB
ALTER TABLE ezuser CHANGE password_hash password_hash VARCHAR(255) default NULL;
-- PostgreSQL
ALTER TABLE ezuser ALTER COLUMN password_hash TYPE VARCHAR(255);
-- Oracle (standard syntax; ezoracle ships no file for it)
ALTER TABLE ezuser MODIFY ( password_hash VARCHAR2(255) );
```

**6. Apply the 6.0 line.**

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
php update/common/scripts/6.0/createaudittables.php --dry-run
php update/common/scripts/6.0/createaudittables.php
```

What the file adds, and what to do if a statement fails, is in [11.6](11-upgrading.md#116-from-any-60x-to-today).

**7. Run the 5.4 data scripts.** Even a 5.4 site benefits from them, because the new stack wrote rows the legacy kernel
would not have written the same way. Each has a dry-run mode; read its `--help` first:

```bash
php update/common/scripts/5.4/cleanupfieldvaluerelations.php --help
php update/common/scripts/5.4/cleanuntranslatablerelations.php --help
php update/common/scripts/5.4/fixremovedezurlobjectlinks.php --help
php update/common/scripts/5.4/fixtrashedimagereferences.php --help
php update/common/scripts/5.3/recreateimagesreferences.php --help
php update/common/scripts/5.3/updatenodeassignmentparentremoteids.php --help
```

**8. Accounts without a settings row.** Since 6.0.15 a user without an `ezuser_setting` row cannot sign in. Users made
through the Public API or an import may lack it; find them with the query in the
[August 2026 security specification](../specifications/6.0/security-hardening-2026-08.md#f-06-find-accounts-that-cannot-sign-in).

**9. Check the version** ([11.2](11-upgrading.md#112-which-version-do-you-run)): `6.0.15stable`.

### Per engine

| Engine | Note |
|---|---|
| MySQL, MariaDB | Run the `UPDATE` lines without `SET storage_engine`. Convert old `utf8` (three-byte) tables if needed: `php bin/php/ezconvertdbcharset.php`; MyISAM tables: `php bin/php/ezconvertmysqltabletype.php --newtype=InnoDB`. Strict SQL mode (the default since MySQL 5.7) turns a too-long `password_hash` into an error instead of a silent cut; widen the column first either way. |
| PostgreSQL | The `pgcrypto` extension must exist ([9.4](09-databases.md#94-postgresql)). |
| Oracle | The `ezoracle` chain ends at 5.3; then steps 4 to 6 with your Oracle client, the audit tables through `createaudittables.php`. |
| SQLite, MongoDB | 5.x never ran on them. Migrate on MySQL or PostgreSQL first, then switch engines: [9.9](09-databases.md#99-switching-an-existing-site-to-another-engine). |

## 15.8 Field types against datatypes

The Symfony field types of 5.x were implementations of the legacy datatypes over the same rows, with the same
identifiers in `ezcontentclass_attribute.data_type_string`. List what your classes use:

```sql
SELECT data_type_string, COUNT(DISTINCT contentclass_id) AS classes
FROM ezcontentclass_attribute WHERE version = 0
GROUP BY data_type_string ORDER BY data_type_string;
```

and compare with the datatypes Exponential has:

```bash
php bin/php/console exp:ini get content.ini/DataTypeSettings/AvailableDataTypes
```

| Identifier | In 5.x | In Exponential |
|---|---|---|
| `ezxmltext` (XmlText) | edited in legacy; the new stack rendered it | the kernel datatype; nothing to convert |
| `ezpage` (Page, ezflow) | legacy ezflow | `ezflow` package (default); block definitions in your `block.ini.append.php` keep working |
| `ezsrrating` (Star rating) | legacy ezstarrating | `ezstarrating` package (default) |
| `ezgmaplocation` | legacy extension | `ezgmaplocation` package (default) |
| `ezcomcomments` and the `ezcomment*` tables | legacy ezcomments | `composer require se7enxweb/ezcomments` |
| `ezrichtext` (RichText, DocBook XML) | the new stack only; the 2014.11 kernel already had the field type | **no datatype**: convert or remove (below) |
| your own field type | a Symfony class, often with a legacy datatype beside it | the legacy datatype, ported to PHP 8; a field type without one must be written as a datatype |

**RichText back to XmlText.** Exponential ships no converter from `ezrichtext` to `ezxmltext`, and the vendor's
`ezxmltext:convert-to-richtext` only goes the other way. Classes with `ezrichtext` are rare on a 5.x site (the legacy
admin could not edit them), and you choose per attribute:

- If the content can be rebuilt, change the attribute to `ezxmltext` in a copy of the class and re-enter it.
- Otherwise convert the stored DocBook with a script of your own and store the result through the kernel
  (`eZContentObjectAttribute::setAttribute( 'data_text', ... )` and `store()`), so sort keys and relations are kept.
  The usual element mapping:

| DocBook (RichText) | `ezxmltext` |
|---|---|
| `<section xmlns="http://docbook.org/ns/docbook" ...>` | `<section xmlns:image="..." xmlns:xhtml="..." xmlns:custom="...">` |
| `<para>` | `<paragraph>` |
| `<emphasis role="strong">` / `<emphasis>` | `<strong>` / `<emphasize>` |
| `<title ezxhtml:level="2">` | `<header level="2">` (inside nested `<section>` elements) |
| `<itemizedlist>` / `<orderedlist>`, `<listitem>` | `<ul>` / `<ol>`, `<li>` (each with a `<paragraph>`) |
| `<link xlink:href="ezlocation://42">`, `ezcontent://42`, an external URL | `<link node_id="42">`, `object_id="42"`, `url_id` (a row in `ezurl`) |
| `<ezembed xlink:href="ezcontent://42" view="embed">` | `<embed object_id="42" view="embed">` |
| `<informaltable>`, `<tr>`, `<td>` | `<table>`, `<tr>`, `<td>` |
| `<eztemplate name="x">` | `<custom name="x">` |
| `<literallayout>` | `<literal>` |

Validate every converted value in a copy: open the object in the online editor and publish it once. The kernel's
XHTML output also renders three DocBook elements when they occur in imported `ezxmltext`: `ezembed` (with
`xlink:href`), `para` (keeping its `ezxhtml:class`) and line breaks inside `literallayout` (added 30 July 2026, commit
`d7bebcb39b`, `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`). That keeps half-converted
text readable; it is not a converter.

Custom tags and their attributes stay as they were in `ezxmltext` (`content.ini [CustomTagSettings]`, attributes in
`ezoe_attributes.ini`), so the vendor's table of attribute types RichText does not support does not apply.

## 15.9 Users, password hashes and sessions

Users are content objects plus a row in `ezuser` (`login`, `email`, `password_hash`, `password_hash_type`) and
`ezuser_setting` (`is_enabled`). The new stack used the same rows, so every account carries over.

| `password_hash_type` | Name | 5.x (to 2017.08) | Exponential |
|---|---|---|---|
| 1 | `md5_password` | yes | yes |
| 2 | `md5_user` | yes (the old default) | yes |
| 3 | `md5_site` (depends on `[UserSettings] SiteName`) | yes | yes |
| 4 | `mysql` | yes | yes |
| 5 | `plaintext` | yes | yes |
| 6 | `bcrypt` | no | yes |
| 7 | `php_default` | no | yes, the default (`[UserSettings] HashType=php_default`) |

With `[UserSettings] UpdateHash=true` (the default), a user who signs in with an old hash type gets a new
`php_default` hash, written to `ezuser` in the same request (`kernel/classes/datatypes/ezuser/ezuser.php`). That is why
the column must be wide enough first ([15.7](#157-the-database-step-by-step), step 5). Keep `SiteName` as it was if any
account still has type 3. Two consequences for rollback are in [15.21](#1521-rollback).

**Sessions.** Under the dual-kernel stack Symfony owned the session, and the legacy kernel used it through
`ezpSessionHandlerSymfony`. Exponential uses its own handler (`[Session] Handler=` empty means PHP's session
handling), so every visitor signs in again after the switch; nothing to migrate. If you relied on the session count
or session clean-up in the admin, see the comments above `Handler` in `settings/site.ini` for `ezpSessionHandlerDB`.

**Form tokens.** The legacy bundle gave the form token extension Symfony's secret and token field name, or switched it
off when Symfony's CSRF protection was off. Exponential always checks it; set `[HTMLForms] Secret`
([15.6](#156-translate-the-symfony-configuration-into-ini)) and make sure every custom form posts the token.

## 15.10 Image variations and image aliases

The 2014.11 bridge built the legacy alias list from the YAML `image_variations`: it set
`image.ini [AliasSettings] AliasList` to exactly the variation names and wrote each variation's `reference` and
`filters` as `[<alias>] Reference` and `Filters[]` (only filters it knew as ImageMagick filters). Aliases that existed
only in `image.ini` files were therefore not in effect on the old site. Write each variation as an INI alias:

```yaml
# ezpublish.yml, 5.x
ezpublish:
    system:
        my_group:
            image_variations:
                articleimage:
                    reference: ~
                    filters:
                        - { name: geometry/scalewidth, params: [770] }
                articlethumbnail:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [170, 220] }
```

```ini
; settings/siteaccess/<each siteaccess of my_group>/image.ini.append.php, or settings/override/
[AliasSettings]
AliasList[]=articleimage
AliasList[]=articlethumbnail

[articleimage]
Reference=
Filters[]=geometry/scalewidth=770

[articlethumbnail]
Reference=
Filters[]=geometry/scaledownonly=170;220
```

Parameters are separated by `;`. The filter names (`geometry/scalewidth`, `geometry/scaledownonly`,
`geometry/scalewidthdownonly`, `geometry/crop` and others) are defined in `settings/image.ini`, with their ImageMagick
form under `[ImageMagick] Filters[]`.

**The files.** The legacy kernel records each generated alias in the attribute's XML (`data_text`) and in
`ezimagefile`. The 2014.11 new stack wrote its variations next to the original as `<name>_<variation>.<extension>`
(`IORepositoryResolver::getFilePath()`), the same naming legacy uses, but without recording them. After the switch,
aliases are made again on first use. To have every alias rebuilt with the new definitions, expire them all, or remove
the alias files (the originals stay):

```bash
php bin/php/ezcache.php --clear-id=imagealias
php bin/php/ezcache.php --clear-id=imagealias --purge
```

Keep `VarDir` and the storage directory name identical to the old site: stored image paths include them. If they had
to change, `php update/common/scripts/5.1/fiximagesoutsidevardir.php` moves the files and updates the references.

## 15.11 Routes and URL aliases

**URL aliases** live in `ezurlalias_ml`, shared by both kernels, so every alias keeps working. New aliases are
generated by the legacy rules: `site.ini [URLTranslator] TransformationGroup` (`urlalias` by default; `urlalias_iri`
for Unicode in URLs; `urlalias_compat` for the old style) and `WordSeparator`. Use the group the old legacy settings
had, which is the legacy counterpart of the vendor's slug converter setting. Check and repair the table:

```bash
php bin/php/verify_aliases.php
php bin/php/verify_aliases.php --fix
php bin/php/updateniceurls.php --update-nodes
```

Run them on the copy first and compare a few addresses before and after.

**Symfony routes** have no equivalent in the kernel. A route to a controller of your own becomes a module view of an
extension (`extension/<name>/modules/<module>/module.php` and the view file; see
[Extensions](../guides/extensions.md)), reached as `/<module>/<view>`; a pretty address for it is a custom URL alias
or a wildcard (**Setup > URL translator**). A route that only redirected becomes a URL alias or a
[request rule](../features/6.0/request-rules.md), which can allow, deny, redirect, rewrite or log after the alias is
translated. The new stack's own routes (`/content/location/...`, `/_fragment`, `/api/ezp/v2/...`) disappear; check
logs and external links for them.

## 15.12 Templates: Twig to TPL

The `.tpl` templates of your legacy designs keep working; only Twig templates need rewriting. In a 5.x site the
Symfony side usually rendered the public pages (`legacy_mode: false`) with the legacy templates as fallback, so the
Twig templates are often the whole front end.

| Symfony (`ezpublish.yml`, Twig) | Exponential |
|---|---|
| `system: <sa>: location_view: full: article: { template: ..., match: { Identifier\ContentType: article } }` | `settings/siteaccess/<sa>/override.ini.append.php`: a block with `Source=node/view/full.tpl`, `MatchFile=full/article.tpl`, `Subdir=templates`, `Match[class_identifier]=article` |
| matchers `Id\Location`, `Id\ParentLocation`, `Identifier\Section`, `Id\Remote`, `Depth`, `UrlAlias` | `Match[node]`, `Match[parent_node]`, `Match[section_identifier]`, `Match[remote_id]`, `Match[depth]`, `Match[url_alias]` (`kernel/classes/eznodeviewfunctions.php`) |
| `{% extends pagelayout %}` | `pagelayout.tpl` of the design; the view writes `$module_result.content` |
| `{{ ez_render_field(content, 'body') }}` | `{attribute_view_gui attribute=$node.data_map.body}` |
| `{{ ez_field_value(content, 'title') }}` | `{$node.data_map.title.content}` |
| `{{ ez_content_name(content) }}` | `{$node.name\|wash}` |
| `{{ path(location) }}` | `{$node.url_alias\|ezurl}` |
| `{{ ez_image_alias(field, versionInfo, 'large').uri }}` | `{$node.data_map.image.content.large.url\|ezroot}` |
| `{{ ez_is_field_empty(content, 'image') }}` | `{$node.data_map.image.has_content}` (negated) |
| `{{ render(controller(...)) }}`, `render_esi` | `{include uri='design:...'}`, a `fetch()`, or a module view |
| `{% for x in list %}`, `{% if %}`, `{% set %}` | `{foreach $list as $x}`, `{if}`, `{def}` / `{set}` |
| `\|escape`, `\|raw` | `\|wash`; output without `\|wash` |
| `asset('...')` | `'...'\|ezdesign`, `'...'\|ezimage` |
| translations (`\|trans`) | `'...'\|i18n('context')` |

`ngsymfonytools` (`symfony_include`) renders a Twig template from a `.tpl`, but only inside a Symfony application
through the bridge; it does not help a standalone site. How templates are found, overridden and debugged:
[Templates and design](../guides/templates-and-design.md); showing which template wrote which markup:
[template path comments](../features/6.0/template-path-comments.md).

## 15.13 REST

REST API v2 of the new stack (`/api/ezp/v2/`, media types such as `application/vnd.ez.api.Content+json`) goes away
with Symfony. Exponential has its own REST layer (`settings/rest.ini`, `ApiPrefix=/api`, OAuth by default:
`[Authentication] AuthenticationStyle=ezpRestOauthAuthenticationStyle`) and two content providers, both installed by
default:

| Package | Routes | Version in the URL |
|---|---|---|
| `ezprestapiprovider` | read: `content/node/:nodeId` with `/list`, `/listAtom`, `/fields`, `/field/:fieldIdentifier`, `/childrenCount`; `content/object/:objectId` with `/fields`, `/field/:fieldIdentifier` | `v1` |
| `ezprestapi` (since 6.0.9) | the same reads plus `content/node/create`, `content/node/delete/:nodeId` and an update by POST to `content/node/:nodeId` | `v2` |

Both register the provider `ezp`, so `ezprestapi`'s routes answer at `/api/ezp/v2/content/node/<id>` (checked by
`tests/tests/kernel/classes/rest/ezpRestRoutesTest.php`). The prefix is the same as REST API v2 of the new stack, the
API is not: other resources, other payloads, other authentication. Every client of the old API must be rewritten;
nothing about it is compatible. For administration from scripts and apps there are also the remote services:
[Remote services and apps](../guides/remote-services-and-apps.md).

## 15.14 Caches

| 5.x | Exponential |
|---|---|
| Symfony HTTP cache (the PHP reverse proxy in `ezpublish/`) or Varnish, purged by the legacy bundle on `content/cache` events | the content view cache and the template block cache in `var/`; optionally the role-aware HTTP cache ([HTTP cache](../bc/6.0/httpcache.md), off by default) and, under Velocity, the response cache ([8.3.8](08-serving-the-site.md#838-the-response-cache)) |
| the persistence (SPI) cache, Stash | none needed; the kernel has its own in-request caches |
| `ezpublish/console cache:clear` | `php bin/php/ezcache.php --clear-all`, or `./console exp:cache` ([cache console](../bc/6.0/cache-console.md)) |

**Varnish.** The legacy bundle attached the HTTP cache purger to the kernel's `content/cache` and `content/cache/all`
events. Exponential sends no purge or ban requests to a proxy (no `PURGE` or `BAN` in the kernel), so a Varnish
configured for 5.x keeps serving stale pages. Either drop Varnish (the HTTP cache or Velocity's response cache does
the same job inside Exponential), or let it cache anonymous pages for a short time only ([HTTP caching for anonymous
visitors](../bc/6.0/http-caching.md)), or purge it yourself: the HTTP cache can name each page's purge tags in a
header (`[HttpCacheSettings] TagHeader=xkey` for the Varnish xkey module), and the kernel still emits
`content/cache` with the node and object ids on every publish (`kernel/classes/ezcontentcachemanager.php`), which a
listener in `site.ini [Event] Listeners[]` can turn into purges. The order after any code change, and which cache to
clear when: [10.6](10-after-installing.md#106-caches).

## 15.15 Search

The 5.x new stack searched through the legacy search tables or, with `ezfind`, through Solr. Exponential searches
with its built-in engine (`site.ini [SearchSettings] SearchEngine=eZSearchEngine`) until you install `ezfind`
(`composer require se7enxweb/ezfind`) and switch to it. The search index is rebuilt, not migrated: an index made by
the new stack's search engine, or by an old ezfind against an old Solr schema, is not reused.

```bash
php bin/php/updatesearchindex.php --clean
```

`exp:solr` starts, stops and checks a local Solr server ([Solr commands](../features/6.0/web-server-and-solr-commands.md));
it is off until `settings/solr.ini [SolrSettings] Enabled=true`. Indexing details: [10.8](10-after-installing.md#108-search-indexing-and-image-handling).

## 15.16 Cron, workflows and events

Cron lines of a 5.x site ran legacy scripts through Symfony, for example
`php ezpublish/console ezpublish:legacy:script runcronjobs.php frequent`. In Exponential the scripts run directly:

```bash
php runcronjobs.php                    # the default part
php runcronjobs.php frequent           # a named part
php runcronjobs.php --list
```

The parts and a crontab: [10.3](10-after-installing.md#103-cronjobs); running and watching them from the admin:
[Cronjobs console](../features/6.0/cronjobs-console.md).

Legacy workflows (`ezworkflow*` tables, triggers, the `workflow.php` cronjob) carry over unchanged. Symfony-side
reactions to content changes (signal-slot listeners, event subscribers of your bundles) do not: rewrite each as a
workflow event type of an extension, or as a listener in `site.ini [Event] Listeners[]=<event>@<callback>` on one of
the kernel's events (`content/cache`, `content/cache/all`, `content/class/cache`, `content/section/cache`,
`content/state/assign`, `request/input`, `request/preinput`, `user/cache/all` and others). Symfony console commands
of your own become `bin/php/` style scripts of your extension.

## 15.17 Cluster and DFS

The DFS cluster keeps its tables (`ezdfsfile` and, since 5.1, a separate cache table) and its NFS mount, so a 5.x
cluster carries over. In 5.x, the Symfony side could configure DFS and the legacy bundle injected it; write it into
the global `settings/override/file.ini.append.php` (cluster settings are read before siteaccess and extension
settings, so they belong there):

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/path/to/nfs
DBBackend=eZDFSFileHandlerMySQLiBackend
DBHost=dbhost
DBPort=3306
DBName=cluster
DBUser=USER
DBPassword=PASS
```

`DBBackend` is `eZDFSFileHandlerMySQLiBackend`, `eZDFSFileHandlerPostgresqlBackend` (package `ezpostgresqlcluster`) or
`eZDFSFileHandlerOracleBackend` (`ezoracle`). `index_cluster.php` in the root serves the files and reads the
`CLUSTER_*` constants from `config.cluster.php` (examples in `config.php-RECOMMENDED`). Apply the
`dbupdate-cluster-` files of every step you pass ([11.3](11-upgrading.md#113-the-update-files)); then check:

```bash
php bin/php/dfscleanup.php -S
php bin/php/dfscleanup.php -B
```

`-S` checks the files on the share against the database, `-B` the other way; add `-D` only after reading the result.
`php bin/php/clusterize.php` moves `var/` into the cluster, and `-u` back out. Background:
[configurable DFS backend](../features/5.4/configurable_dfs_cluster_backend.md),
[cluster index](../features/4.7/cluster_index.md).

## 15.18 Serving the site

The 5.x document root was `web/`, with the front controller wrappers `index_rest.php` and `index_cluster.php` installed there by
`ezpublish:legacy:assets_install`, and assets copied by `assets:install` and `assetic:dump`. Exponential is served from
its own root: no asset step, the design files are served as they are and combined by the ezjscore packer. Choose how
to serve it in [chapter 8](08-serving-the-site.md): Exponential Velocity
([8.3](08-serving-the-site.md#83-exponential-velocity-the-recommended-way)), FrankenPHP, or Apache or nginx with
PHP-FPM using the rewrite rules of `.htaccess_root` ([8.5](08-serving-the-site.md#85-apache-with-php-fpm)). Do not
reuse the 5.x virtual host: its rewrite rules point at `web/` and at Symfony's front controller.

## 15.19 Verify

```bash
php bin/php/ezcache.php --clear-all
php bin/php/console exp:checkdbfiles
php bin/php/console exp:checkclasses
php bin/php/console --version
```

Then, on the new site:

- [ ] The version row reads `6.0.15stable`; `ezuser.password_hash` is 255 wide.
- [ ] Every siteaccess of the old `siteaccess: list` answers at its old address.
- [ ] An editor, an administrator and a front-end user each sign in, twice (the second sign-in uses the rewritten hash).
- [ ] A form of your own posts without a 403.
- [ ] One object of every class opens in the admin and on the site; attributes of every datatype in
      [15.8](#158-field-types-against-datatypes) render.
- [ ] Images show in every alias of the old `image_variations`.
- [ ] A sample of old URLs, taken from the access log of the old site, answers 200 or the same redirect as before.
- [ ] Search finds a word from a recently published object.
- [ ] Publishing an object updates the page for an anonymous visitor (caches, proxy).
- [ ] `php runcronjobs.php --list` shows the parts your crontab calls; one run of each ends without errors.
- [ ] **Setup > Upgrade check**, file consistency, lists only files you changed on purpose
      ([File consistency check](../features/6.0/file-consistency-check.md)).

## 15.20 Common issues

The vendor's list first, each with its cause in Exponential; the `ezpublish:update:legacy_storage_*` and
`ibexa:urls:*` commands it names do not exist here.

| Symptom | Cause in Exponential | Fix |
|---|---|---|
| URL aliases missing or wrong (vendor: regenerate URL aliases) | aliases made by the new stack with other transformation rules, or broken rows | `php bin/php/verify_aliases.php` (then `--fix`); `php bin/php/updateniceurls.php --update-nodes`; [15.11](#1511-routes-and-url-aliases) |
| Images do not display (vendor: normalise image paths with unprintable characters) | `VarDir` or the storage directory differs from the old site, or file names with characters the file system mangled | keep `VarDir` as before; `php update/common/scripts/5.1/fiximagesoutsidevardir.php`; `php update/common/scripts/5.3/recreateimagesreferences.php` |
| "Unknown relation type 0" (vendor: REST, after an edit) | rows in `ezcontentobject_link` whose `relation_type` bits were all cleared; the kernel itself does that when it removes the last relation type of a row (`removeContentObjectRelation()`), and every relation query filters on the bits, so Exponential never shows them | none needed; to tidy: `DELETE FROM ezcontentobject_link WHERE relation_type = 0;` on a backed-up database |
| The always-available flag is set on the fields of every language, not only the main one | content made by the new stack with several translations; the kernel keeps bit 1 of `ezcontentobject_attribute.language_id` and `ezcontentobject_name.language_id` on the main language only | find: `SELECT a.contentobject_id, a.version, a.language_code FROM ezcontentobject_attribute a JOIN ezcontentobject o ON o.id = a.contentobject_id WHERE (a.language_id & 1) = 1 AND (a.language_id & ~1) <> (o.initial_language_id & ~1);` then switch **Always available** off and on again for those objects in the admin, which rewrites both tables (Oracle: `bitand()` in place of `&`) |
| Sub-item lists sort wrongly or searches miss objects (vendor: empty `sort_key_string`) | attributes stored by the new stack without a sort key | find: `SELECT contentobject_id, data_type_string FROM ezcontentobject_attribute WHERE sort_key_string = '' AND data_text <> '' AND data_type_string IN ('ezstring', 'eztext');` then publish the objects again: the kernel computes sort keys whenever it stores an attribute (`eZContentObjectAttribute::updateSortKey()`); there is no bulk command |
| Sub-items tab errors on sort by class identifier or class name (vendor) | does not happen: sort fields 6 and 7 exist | none; `SELECT node_id, parent_node_id, sort_field FROM ezcontentobject_tree WHERE sort_field IN (6, 7);` lists them if you want to know |
| Unstyled login page, or "no access" on one siteaccess (vendor) | the `user/login` policy of the Anonymous role (or of the user) is limited to siteaccesses by CRC32 of their names; a renamed or new siteaccess is not in it | **Roles**: edit the `user/login` policy and add the siteaccesses; better, keep the old names |

And the issues of the 5.x path the vendor does not list:

| Symptom | Cause | Fix |
|---|---|---|
| Sign-in works once, then never again; or a database error at sign-in | `password_hash` is still `varchar(50)`, the rewritten bcrypt hash (60) was cut or refused | [15.7](#157-the-database-step-by-step), step 5; reset the affected passwords (`php bin/php/resetuserpassword.php --help`) |
| Some accounts cannot sign in at all | no `ezuser_setting` row (accounts made by API or import) | [15.7](#157-the-database-step-by-step), step 8 |
| Every POST of a custom form answers 403 | no form token: under 5.x the token was off or used Symfony's secret | set `[HTMLForms] Secret`; add the token to the form |
| Database error on the first request | the legacy `[DatabaseSettings]` were never filled; the bridge injected them | [15.6](#156-translate-the-symfony-configuration-into-ini) |
| Front page of a siteaccess shows the wrong tree | `PathPrefix`, `IndexPage`, `RootNode` came from `content.tree_root` | [15.6](#156-translate-the-symfony-configuration-into-ini) |
| An image alias is unknown, or has other dimensions | the alias list came from YAML | [15.10](#1510-image-variations-and-image-aliases) |
| An attribute renders as nothing in a class | `ezrichtext`, or a field type without a legacy datatype | [15.8](#158-field-types-against-datatypes) |
| `Class ... not found` or a fatal error in an extension | PHP 5 code, or no class map | `php bin/php/ezpgenerateautoloads.php -e`, `exp:checkclasses`, [PHP 8 support](../bc/6.0/php8.md) |
| `SET storage_engine=InnoDB;` fails | MySQL 5.7.6 and later, MariaDB | run only the `UPDATE` lines ([15.7](#157-the-database-step-by-step)) |
| Pages stay stale behind Varnish | Exponential sends no purges | [15.14](#1514-caches) |
| Cron does nothing | crontab still calls `ezpublish/console` | [15.16](#1516-cron-workflows-and-events) |
| Clients of the old REST API get 404 | REST API v2 of the new stack is gone | [15.13](#1513-rest) |

More symptoms and fixes: [chapter 12](12-troubleshooting.md).

## 15.21 Rollback

Keep the old installation and its database untouched until the watch phase is over. The migration changes the copy,
never the original, so rollback is switching the address back to the old site, provided nothing was written to the
new site that must survive.

If the new site was live and you must go back with its data:

- **Content** written by Exponential is in the same tables and readable by the old kernel, except rows of the ten new
  tables (ignored by it) and attributes of datatypes the old site lacked.
- **Passwords.** Every user who signed in to Exponential now has a type 7 hash, which a 5.x kernel up to 2017.08 does
  not know: those users cannot sign in to the old site. Check the old kernel
  (`grep -n "PASSWORD_HASH_PHP_DEFAULT" ezpublish_legacy/kernel/classes/datatypes/ezuser/ezuser.php`). If a rollback
  must stay possible for some weeks, set on the new site, before it goes live:

  ```ini
  [UserSettings]
  HashType=md5_user
  UpdateHash=false
  ```

  and switch back to `php_default` and `true` once the old site is retired. Use the type the old site used.
- **The version row** says `6.0.15stable`; the old kernel does not check it, but put back `5.4.0alpha1` (or your old
  value) for clarity.
- **The 6.0.15 columns and tables** stay; the old kernel ignores them. The widened `password_hash` is harmless.

A database restore is the clean way back: the dump of step 1 of [15.7](#157-the-database-step-by-step), plus a sync
of `var/<site>/storage/` if editors uploaded files.

## 15.22 Checklist

- [ ] Variant identified ([15.2](#152-which-variant-do-you-run)); database version read.
- [ ] Inventory done: siteaccesses, injected settings, datatypes, bundles, extensions, cron, search, cluster, cache.
- [ ] Every Symfony bundle has a decision: extension, drop, or keep behind a bridge.
- [ ] New Exponential installed in its own directory; your extensions, designs, settings and storage copied, links
      dereferenced; built-in extensions replaced by their packages.
- [ ] Database, file, image, siteaccess, user and form token settings written into INI ([15.6](#156-translate-the-symfony-configuration-into-ini)).
- [ ] `image_variations` written as `image.ini` aliases.
- [ ] Database: backup, internal drafts, chain to 5.4, 6.0.0 version rows, `password_hash` widened, 6.0.15 file, audit
      tables, 5.3/5.4 data scripts, `ezuser_setting` check.
- [ ] `ezrichtext` and custom field types converted or removed.
- [ ] Twig front end rewritten as TPL overrides; routes as modules, aliases or request rules.
- [ ] REST clients rewritten for the Exponential REST layer or the remote services.
- [ ] Cache and proxy decided; Varnish purging solved or Varnish removed.
- [ ] Search engine chosen; index rebuilt.
- [ ] Crontab rewritten to `runcronjobs.php`; listeners rewritten as workflows or event listeners.
- [ ] Cluster settings in the global `file.ini` override; `dfscleanup.php` clean.
- [ ] Served by Velocity, FrankenPHP, Apache or nginx; no 5.x virtual host reused.
- [ ] [15.19](#1519-verify) passes on a copy, timed; then on the live address.
- [ ] Rollback decided: hash settings for the window, old site kept.

## References

In this repository:

- [11. Upgrading](11-upgrading.md): the update files, the old chain, the 6.0 line; [14. Migrating from the 4.x line](14-migrating-from-4x.md);
  [12. Troubleshooting](12-troubleshooting.md); [8. Serving the site](08-serving-the-site.md);
  [9. Databases](09-databases.md); [10. After installing](10-after-installing.md); [3. Getting the code](03-getting-the-code.md).
- Guides: [Upgrading](../guides/upgrading.md), [Templates and design](../guides/templates-and-design.md),
  [Extensions](../guides/extensions.md), [Remote services and apps](../guides/remote-services-and-apps.md),
  [Operating a site](../guides/operating-a-site.md).
- The legacy bridge: [Legacy bridge](../features/6.0/legacy-bridge.md),
  [bridge specification](../specifications/6.0/legacy-bridge-bundle.md),
  [package forks and command renames](../bc/6.0/platform-package-forks-and-command-renames.md).
- 5.x notes: [changes in 5.1](../bc/5.1/changes-5.1.txt), [5.2](../bc/5.2/changes-5.2.txt),
  [5.4](../bc/5.4/changes-5.4.txt), [5.90](../bc/5.90/README.md),
  [password length](../bc/5.90/password_length.md), [PHP 7](../bc/5.90/php7.md).
- 6.0: [Changelog 6.0.0](../changelogs/6.0/6.0.0.md), [6.0.9](../changelogs/6.0/6.0.9.md),
  [6.0.15](../changelogs/6.0/6.0.15.md); [PHP 8 support](../bc/6.0/php8.md),
  [PHP 8.0 support](../bc/6.0/php-8.0-support.md); [HTTP cache](../bc/6.0/httpcache.md),
  [HTTP caching for anonymous visitors](../bc/6.0/http-caching.md),
  [Velocity response cache](../features/6.0/velocity-response-cache.md),
  [cache console](../bc/6.0/cache-console.md); [request rules](../features/6.0/request-rules.md);
  [cronjobs console](../features/6.0/cronjobs-console.md);
  [Solr commands](../features/6.0/web-server-and-solr-commands.md);
  [default extension distribution](../features/6.0/default-extension-distribution.md);
  [August 2026 security specification](../specifications/6.0/security-hardening-2026-08.md);
  [file consistency check](../features/6.0/file-consistency-check.md);
  [maintenance mode](../features/6.0/maintenance-mode.md).
- Cluster: [configurable DFS backend](../features/5.4/configurable_dfs_cluster_backend.md),
  [cluster index](../features/4.7/cluster_index.md).
- Code: `update/database/`, `update/common/scripts/`, `kernel/classes/datatypes/ezuser/ezuser.php`,
  `kernel/classes/ezcontentobjecttreenode.php`, `kernel/classes/eznodeviewfunctions.php`,
  `kernel/classes/ezcontentcachemanager.php`, `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`,
  `lib/ezsession/classes/ezpsessionhandlersymfony.php`, `extension/ezprestapi/classes/rest_provider.php`,
  `extension/ezprestapiprovider/classes/rest_provider.php`, `settings/site.ini`, `settings/image.ini`,
  `settings/file.ini`, `settings/rest.ini`, `share/db_schema.dba`, `kernel/sql/`.

The vendor's guide (eZ Platform and Ibexa DXP as targets):

- [Migrating from eZ Publish Platform](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
- [Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/)
- [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)
- [Platform update file 5.4.0 to 6.13.0](https://github.com/ezsystems/ezpublish-kernel/blob/v6.13.0/data/update/mysql/dbupdate-5.4.0-to-6.13.0.sql),
  [kernel API changes of 6.0](https://github.com/ezsystems/ezpublish-kernel/blob/6.13/doc/bc/changes-6.0.md)

Source repositories of the system being migrated from:

- [ezsystems/ezpublish-kernel](https://github.com/ezsystems/ezpublish-kernel) (the new stack and the legacy bundle),
  [ezsystems/ezpublish-legacy](https://github.com/ezsystems/ezpublish-legacy),
  [ezsystems/ezpublish-community](https://github.com/ezsystems/ezpublish-community),
  [ezsystems/LegacyBridge](https://github.com/ezsystems/LegacyBridge),
  [ezsystems/ezplatform-xmltext-fieldtype](https://github.com/ezsystems/ezplatform-xmltext-fieldtype).

Exponential:

- GitHub: [se7enxweb/exponential](https://github.com/se7enxweb/exponential),
  [se7enxweb/legacyBridge](https://github.com/se7enxweb/legacyBridge),
  [se7enxweb/ezflow](https://github.com/se7enxweb/ezflow), [se7enxweb/ezstarrating](https://github.com/se7enxweb/ezstarrating),
  [se7enxweb/ezfind](https://github.com/se7enxweb/ezfind), [se7enxweb/ezcomments](https://github.com/se7enxweb/ezcomments).
- Packagist: [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential),
  [se7enxweb/legacy-bridge](https://packagist.org/packages/se7enxweb/legacy-bridge),
  [se7enxweb/ezfind](https://packagist.org/packages/se7enxweb/ezfind),
  [se7enxweb/ezcomments](https://packagist.org/packages/se7enxweb/ezcomments),
  [se7enxweb/ezprestapi](https://packagist.org/packages/se7enxweb/ezprestapi).

External:

- PHP migration guides, from the PHP of a 5.x site to today: [5.6](https://www.php.net/manual/en/migration56.php),
  [7.0](https://www.php.net/manual/en/migration70.php), [7.1](https://www.php.net/manual/en/migration71.php),
  [7.2](https://www.php.net/manual/en/migration72.php), [7.3](https://www.php.net/manual/en/migration73.php),
  [7.4](https://www.php.net/manual/en/migration74.php), [8.0](https://www.php.net/manual/en/migration80.php),
  [8.1](https://www.php.net/manual/en/migration81.php).
- Symfony concepts named here: [bundles](https://symfony.com/doc/current/bundles.html),
  [configuration](https://symfony.com/doc/current/configuration.html), [routing](https://symfony.com/doc/current/routing.html),
  [templates](https://symfony.com/doc/current/templates.html), [HTTP cache](https://symfony.com/doc/current/http_cache.html),
  [sessions](https://symfony.com/doc/current/session.html); [Twig 3](https://twig.symfony.com/doc/3.x/).
- Databases: [MySQL SQL modes](https://dev.mysql.com/doc/refman/8.4/en/sql-mode.html),
  [mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html),
  [PostgreSQL ALTER TABLE](https://www.postgresql.org/docs/current/sql-altertable.html),
  [pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html),
  [Oracle ALTER TABLE](https://docs.oracle.com/en/database/oracle/oracle-database/19/sqlrf/ALTER-TABLE.html).
- [Varnish: purging and banning](https://varnish-cache.org/docs/trunk/users-guide/purging.html);
  [Composer create-project](https://getcomposer.org/doc/03-cli.md#create-project).

[Contents](README.md) · Previous: [14. Migrating from the 4.x line](14-migrating-from-4x.md) · Next: [16. Migrating from eZ Platform and Ibexa](16-migrating-from-ez-platform-and-ibexa.md)
