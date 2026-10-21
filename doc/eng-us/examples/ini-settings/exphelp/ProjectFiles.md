---
title: exphelp.ini [ProjectFiles]
audience: developer, admin
category: examples
summary: "The settings of [ProjectFiles] in exphelp.ini: Enabled, Include, Exclude, Category, Type, Audiences, AudienceRules, LicenseName ..."
keywords: exphelp.ini, ProjectFiles, INI, setting, Enabled, Include, Exclude, Category, Type, Audiences, AudienceRules, LicenseName, LicensePage
type: generated
generated: exp:help:examples
---
# exphelp.ini [ProjectFiles]

The block `[ProjectFiles]` of `settings/exphelp.ini`, with 9 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Enabled

> The project files (expHelpProjectFiles; doc/eng-us/features/6.0/help-system.md, "Project files"): the Markdown files  
> of the repository outside doc/ that document the project itself (README.md, INSTALL.md, SECURITY.md,  
> CONTRIBUTING.md, CODE\_OF\_CONDUCT.md, COPYRIGHT.md, LICENSE.md, the README.md of bin/, design/ and kernel/  
> directories). Each has the id project/<path in the repository> (/exphelp/viewer/project/README.md;  
> /exphelp/viewer/README.md redirects there) and  
> is a page at the top of the page tree, next to the folders, not in a folder of its own. They are English only and  
> not translated, like doc/changelogs/: a reader of another language sees them with the note that the page is not  
> translated.  
> Not to be changed by any setting here or in an override: only files git tracks are shown (the git index, "git  
> ls-files" or the release manifest, never the directory: a private note in the root that is not committed is never  
> shown, searched or linked), never a file in a place .gitignore ignores, only Markdown, never a hidden file or a file  
> in a hidden directory (.github/ included), and never below doc/, extension/ (the extensions' documentation is the  
> category Extensions), a tests/ directory anywhere, var/, vendor/, settings/override/, settings/siteaccess/ or  
> node\_modules/.  
> enabled: the help shows them; disabled: none  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Enabled=enabled
*/ ?>
```

## Include

> Shell patterns on the path in the repository: "\*" stays inside a directory, "\*\*" crosses directories  

Default: a list of 4: `*.md`, `bin/**/README.md`, `design/*/README.md`, `kernel/**/README.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# an empty "Include[]" first replaces the shipped list; without it the items are added
Include[]
Include[]=*.md
Include[]=bin/**/README.md
Include[]=design/*/README.md
Include[]=kernel/**/README.md
*/ ?>
```

## Exclude

> Left out although Include\[\] matches: test fixtures and the copies of other projects (a JavaScript library's README)  

Default: a list of 5: `**/fixtures/**`, `**/_fixtures/**`, `**/javascript/**`, `**/third-party/**`, `**/thirdparty/**`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# an empty "Exclude[]" first replaces the shipped list; without it the items are added
Exclude[]
Exclude[]=**/fixtures/**
Exclude[]=**/_fixtures/**
Exclude[]=**/javascript/**
Exclude[]=**/third-party/**
Exclude[]=**/thirdparty/**
*/ ?>
```

## Category

> Their category, page type and audiences (\[CategorySettings\], \[TypeSettings\], \[AudienceSettings\]); a file's own front  
> matter wins. AudienceRules\[\]: "<pattern>;<audiences>", the first match wins, else Audiences\[\]  

Default: `project-files`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Category=project-files
*/ ?>
```

## Type

Default: `project`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Type=project
*/ ?>
```

## Audiences

Default: a list of 2: `developer`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# an empty "Audiences[]" first replaces the shipped list; without it the items are added
Audiences[]
Audiences[]=developer
Audiences[]=admin
*/ ?>
```

## AudienceRules

Default: a list of 7: `README.md;admin,developer,editor,user,partner,hosting`, `INSTALL.md;admin,developer,hosting`, `SECURITY.md;admin,developer,hosting`, `CONTRIBUTING.md;developer,supporter,advocate,partner`, `CODE_OF_CONDUCT.md;developer,supporter,advocate,partner,trainer,user`, `COPYRIGHT.md;admin,developer,partner,businesses`, `LICENSE.md;admin,developer,partner,businesses`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# an empty "AudienceRules[]" first replaces the shipped list; without it the items are added
AudienceRules[]
AudienceRules[]=README.md;admin,developer,editor,user,partner,hosting
AudienceRules[]=INSTALL.md;admin,developer,hosting
AudienceRules[]=SECURITY.md;admin,developer,hosting
AudienceRules[]=CONTRIBUTING.md;developer,supporter,advocate,partner
AudienceRules[]=CODE_OF_CONDUCT.md;developer,supporter,advocate,partner,trainer,user
AudienceRules[]=COPYRIGHT.md;admin,developer,partner,businesses
AudienceRules[]=LICENSE.md;admin,developer,partner,businesses
*/ ?>
```

## LicenseName

> The license of the project files: the notice under each of them names it and links its page (a page id)  

Default: `GNU General Public License, version 2 (or any later version)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
LicenseName=GNU General Public License, version 2 (or any later version)
*/ ?>
```

## LicensePage

Default: `project/LICENSE.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
LicensePage=project/LICENSE.md
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
