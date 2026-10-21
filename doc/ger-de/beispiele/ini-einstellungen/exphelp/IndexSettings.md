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

> Die Verzeichnisse (relativ zum Wurzelverzeichnis der Installation), deren von git versionierte Dateien die Hilfe zeigt. Nichts außerhalb  
> wird je gelesen oder ausgeliefert.  

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

> enabled: Die Dokumentation jeder aktiven Erweiterung wird ebenfalls angezeigt, gefunden, wenn die Hilfe nachsieht (eine Erweiterung,  
> die ein- oder ausgeschaltet wird, kommt sofort hinzu oder fällt weg): die Dateien von extension/<name>/doc, die das eigene  
> Repository der Erweiterung versioniert (oder ihre share/filelist.md5 aufführt), und ihre README, CHANGELOG, LICENSE ... (ExtensionTopFiles\[\]).  
> Ihre Seiten haben die ID ext/<name>/<path below doc> (ext/<name>/README.md für eine Datei der obersten Ebene), im Ordner und in der  
> Kategorie „Erweiterungen“ und auf exphelp/extensions. disabled: nur die Erweiterungen aus ExtensionDocDirs\[\].  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
ActiveExtensionDocs=enabled
*/ ?>
```

## ExtensionDocDirs

> Erweiterungen, deren doc-Verzeichnis angezeigt wird, ob aktiv oder nicht, als <extension name>. Leer: keine.  

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

> Die Dateien auf der obersten Ebene des Verzeichnisses einer Erweiterung, die neben ihrem doc/ angezeigt werden (Shell-Muster, ohne Beachtung der Groß-/Kleinschreibung);  
> sonst wird außerhalb von doc/ nie etwas gelesen. PHP- und andere Quelldateien werden nie angezeigt: Nur DocumentSuffixes\[\] (und  
> Textdateien ohne Endung, die nicht wie ein Programm beginnen, #! oder <?) sind Seiten.  

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

> Woher die Liste der von git versionierten Dateien kommt, die erste, die antwortet: der .git-Index (in PHP gelesen, es wird kein Programm  
> gestartet), \`git ls-files\` und share/filelist.md5 für eine Installation ohne .git  

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

> Die Endungen von Seiten  

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

> enabled: Dateien ohne Endung (die alten Changelogs) sind ebenfalls Seiten, als reiner Text  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
PlainTextWithoutSuffix=enabled
*/ ?>
```

## AssetSuffixes

> Die Endungen der Bilder, die die Asset-Ansicht ausliefert  

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

> Nie ausgeliefert, auch wenn versioniert (Shell-Muster auf den Pfad relativ zum Wurzelverzeichnis der Installation; ein Muster ohne  
> "/" passt auch auf den Dateinamen): erzeugte Dokumentation, HTML, SVG (es kann Skripte enthalten) und PHP, und  
> doc/README.md (die Liste der Sprachverzeichnisse für Leser des Repositorys; die Hilfe wählt die Sprache)  

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

> Bytes; größere Dateien werden ausgelassen  

Standard: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
MaxFileSize=2097152
*/ ?>
```

## StatFiles

> enabled: Jede Anfrage vergleicht Größe und Zeit jeder aufgeführten Datei mit dem gecachten Index, sodass eine bearbeitete Seite  
> sofort erscheint. disabled: Nur der git-Index, das Manifest und die Einstellungen werden verglichen (schneller bei großen doc-Bäumen;  
> führen Sie "./console exp:help index" aus, nachdem Sie eine Seite ohne Commit geändert haben)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
StatFiles=enabled
*/ ?>
```

## StatFilesInterval

> Mit aktiviertem StatFiles: Sekunden, für die dieser Vergleich, einmal gemacht, von jeder Anfrage und jedem Prozess wiederverwendet wird (er wird  
> im Cache-Verzeichnis der Hilfe gehalten). Tausende aufgeführte Dateien machen ihn zum teuersten Teil einer Hilfeanfrage,  
> etwa 25 ms hinter PHP-FPM und 0.5 s unter Velocity. Eine bearbeitete Seite erscheint dann innerhalb so vieler Sekunden; eine  
> hinzugefügte oder entfernte Seite ändert den git-Index und erscheint sofort, und "./console exp:help index" sieht immer neu nach.  
> 0: bei jeder Anfrage vergleichen  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IndexSettings]
StatFilesInterval=5
*/ ?>
```

## GitHistory

> enabled: Wenn git ausgeführt werden kann, liest der Index die Historie der Wurzeln einmal pro Aufbau, für das Datum  
> „zuletzt geändert“ jeder Seite und die neuen Änderungen des Dashboards. disabled: Die Dateizeiten werden verwendet, keine neuen Änderungen  

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
