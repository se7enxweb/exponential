---
title: exphelp.ini [ProjectFiles]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ProjectFiles] in exphelp.ini: Enabled, Include, Exclude, Category, Type, Audiences, AudienceRules, LicenseName ..."
keywords: Einstellung, exphelp.ini, ProjectFiles, INI, setting, Enabled, Include, Exclude, Category, Type, Audiences, AudienceRules, LicenseName, LicensePage
type: generated
generated: exp:help:examples
---
# exphelp.ini [ProjectFiles]

Der Block `[ProjectFiles]` von `settings/exphelp.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

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

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Enabled=enabled
*/ ?>
```

## Include

> Shell patterns on the path in the repository: "\*" stays inside a directory, "\*\*" crosses directories  

Standard: eine Liste mit 4: `*.md`, `bin/**/README.md`, `design/*/README.md`, `kernel/**/README.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "Include[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Include[]
Include[]=*.md
Include[]=bin/**/README.md
Include[]=design/*/README.md
Include[]=kernel/**/README.md
*/ ?>
```

## Exclude

> Left out although Include\[\] matches: test fixtures and the copies of other projects (a JavaScript library's README)  

Standard: eine Liste mit 5: `**/fixtures/**`, `**/_fixtures/**`, `**/javascript/**`, `**/third-party/**`, `**/thirdparty/**`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "Exclude[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `project-files`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Category=project-files
*/ ?>
```

## Type

Standard: `project`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Type=project
*/ ?>
```

## Audiences

Standard: eine Liste mit 2: `developer`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "Audiences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Audiences[]
Audiences[]=developer
Audiences[]=admin
*/ ?>
```

## AudienceRules

Standard: eine Liste mit 7: `README.md;admin,developer,editor,user,partner,hosting`, `INSTALL.md;admin,developer,hosting`, `SECURITY.md;admin,developer,hosting`, `CONTRIBUTING.md;developer,supporter,advocate,partner`, `CODE_OF_CONDUCT.md;developer,supporter,advocate,partner,trainer,user`, `COPYRIGHT.md;admin,developer,partner,businesses`, `LICENSE.md;admin,developer,partner,businesses`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "AudienceRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `GNU General Public License, version 2 (or any later version)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
LicenseName=GNU General Public License, version 2 (or any later version)
*/ ?>
```

## LicensePage

Standard: `project/LICENSE.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
LicensePage=project/LICENSE.md
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
