---
title: exphelp.ini [IndexSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [IndexSettings] in exphelp.ini: Roots, ActiveExtensionDocs, ExtensionDocDirs, ExtensionTopFiles, AllowlistSources, DocumentSuffixes, PlainTextWithoutSuffix, AssetSuffixes ..."
keywords: Einstellung, exphelp.ini, IndexSettings, INI, setting, Roots, ActiveExtensionDocs, ExtensionDocDirs, ExtensionTopFiles, AllowlistSources, DocumentSuffixes, PlainTextWithoutSuffix, AssetSuffixes, ExcludePatterns, MaxFileSize, StatFiles, StatFilesInterval, GitHistory
type: generated
generated: exp:help:examples
---
# exphelp.ini [IndexSettings]

Der Block `[IndexSettings]` von `settings/exphelp.ini`, mit 13 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Roots

> The directories (relative to the installation root) whose git-tracked files the help shows. Nothing outside them  
> is ever read or served.  

Standard: eine Liste mit 1: `doc`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "Roots[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Roots[]
Roots[]=doc
*/ ?>
```

## ActiveExtensionDocs

> enabled: the documentation of every active extension is shown too, found when the help looks (an extension  
> switched on or off is added or left out at once): the files of extension/<name>/doc tracked by the extension's own  
> repository (or listed in its share/filelist.md5), and its README, CHANGELOG, LICENSE ... (ExtensionTopFiles\[\]).  
> Their pages have the id ext/<name>/<path below doc> (ext/<name>/README.md for a top file), in the folder and  
> category "Extensions" and on exphelp/extensions. disabled: only the extensions of ExtensionDocDirs\[\].  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
ActiveExtensionDocs=enabled
*/ ?>
```

## ExtensionDocDirs

> Extensions whose doc directory is shown whether active or not, as <extension name>. Empty: none.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "ExtensionDocDirs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDocDirs[]
*/ ?>
```

## ExtensionTopFiles

> The files at the top of an extension's directory that are shown besides its doc/ (shell patterns, without case);  
> nothing else outside doc/ is ever read. PHP and other source files are never shown: only DocumentSuffixes\[\] (and  
> text files without a suffix that do not start like a program, #! or <?) are pages.  

Standard: eine Liste mit 6: `README*`, `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "ExtensionTopFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionTopFiles[]
ExtensionTopFiles[]=README*
ExtensionTopFiles[]=CHANGELOG*
ExtensionTopFiles[]=LICENSE*
ExtensionTopFiles[]=INSTALL*
ExtensionTopFiles[]=USAGE*
ExtensionTopFiles[]=COPYING*
*/ ?>
```

## AllowlistSources

> Where the list of git-tracked files comes from, first one that answers: the .git index (read in PHP, no program  
> is started), \`git ls-files\`, and share/filelist.md5 for an installation without .git  

Standard: eine Liste mit 3: `gitindex`, `gitcommand`, `manifest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "AllowlistSources[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowlistSources[]
AllowlistSources[]=gitindex
AllowlistSources[]=gitcommand
AllowlistSources[]=manifest
*/ ?>
```

## DocumentSuffixes

> The suffixes of pages  

Standard: eine Liste mit 4: `md`, `markdown`, `txt`, `rst`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "DocumentSuffixes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DocumentSuffixes[]
DocumentSuffixes[]=md
DocumentSuffixes[]=markdown
DocumentSuffixes[]=txt
DocumentSuffixes[]=rst
*/ ?>
```

## PlainTextWithoutSuffix

> enabled: files without a suffix (the old changelogs) are pages too, as plain text  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
PlainTextWithoutSuffix=enabled
*/ ?>
```

## AssetSuffixes

> The suffixes of images the asset view serves  

Standard: eine Liste mit 5: `png`, `jpg`, `jpeg`, `gif`, `webp`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "AssetSuffixes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AssetSuffixes[]
AssetSuffixes[]=png
AssetSuffixes[]=jpg
AssetSuffixes[]=jpeg
AssetSuffixes[]=gif
AssetSuffixes[]=webp
*/ ?>
```

## ExcludePatterns

> Never served, even when tracked (shell patterns on the path relative to the installation root; a pattern without  
> "/" also matches the file name): generated documentation, HTML, SVG (it can carry scripts) and PHP, and  
> doc/README.md (the list of the language directories for readers of the repository; the help chooses the language)  

Standard: eine Liste mit 6: `doc/eng-us/doxygen/*`, `doc/README.md`, `*.html`, `*.htm`, `*.svg`, `*.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
# ein leeres "ExcludePatterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludePatterns[]
ExcludePatterns[]=doc/eng-us/doxygen/*
ExcludePatterns[]=doc/README.md
ExcludePatterns[]=*.html
ExcludePatterns[]=*.htm
ExcludePatterns[]=*.svg
ExcludePatterns[]=*.php
*/ ?>
```

## MaxFileSize

> Bytes; larger files are left out  

Standard: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
MaxFileSize=2097152
*/ ?>
```

## StatFiles

> enabled: every request compares the size and time of each listed file with the cached index, so an edited page  
> shows at once. disabled: only the git index, the manifest and the settings are compared (faster on big doc trees;  
> run "./console exp:help index" after changing a page without a commit)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
StatFiles=enabled
*/ ?>
```

## StatFilesInterval

> With StatFiles enabled: seconds for which that comparison, once made, is reused by every request and process (it is  
> kept in the help cache directory). Thousands of listed files make it the most expensive part of a help request,  
> about 25 ms behind PHP-FPM and 0.5 s under Velocity. An edited page then shows within this many seconds; a page  
> added or removed changes the git index and shows at once, and "./console exp:help index" always looks again.  
> 0: compare on every request  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
StatFilesInterval=5
*/ ?>
```

## GitHistory

> enabled: when git can be run, the index reads the history of the roots once per build, for each page's  
> "last changed" date and the dashboard's recent changes. disabled: the file times are used, no recent changes  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
GitHistory=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
