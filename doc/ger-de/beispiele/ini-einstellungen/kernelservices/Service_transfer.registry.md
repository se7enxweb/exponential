---
title: kernelservices.ini [Service_transfer.registry]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.registry] in kernelservices.ini: Class, Factory, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_transfer.registry, INI, setting, Class, Factory, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.registry]

Der Block `[Service_transfer.registry]` von `settings/kernelservices.ini`, mit 5 Einstellung(en).

> Die Transfer-Registry (doc/eng-us/features/6.0/transfer-registry.md): jedes Import- und Export-Werkzeug deklariert sich selbst in einem  
> Block mit dem Tag transfer.tool, die des Kernels unten, die einer Erweiterung in ihrer eigenen  
> settings/kernelservices.ini.append.php. Jeder Link zu einem Werkzeug liest die Registry: die "Suchen Sie …?"-Boxen, die  
> Einträge des Import/Export-Menüs der Inhaltsansicht und der Klassenseiten, die Liste "Import und Export" des Setup-  
> Menüs und der linken Menüs der Werkzeuge, die Werkzeugvorschläge der Admin-Suche und die Kontexthilfe. Ein Werkzeug, dessen  
> Erweiterung nicht aktiv ist, dessen Modul nicht existiert oder das der Benutzer nicht nutzen darf, wird nie angezeigt.  
>
> Class=expKernelTransferTool, Factory=expKernelTransferTool::fromSettings, Arguments\[\]=<die id des Blocks>,  
> Scope=worker, Tags\[\]=transfer.tool, und:  
> Description  ein Satz: wofür es ist  
> Name         sein Name in Listen  
> Direction    import, export oder both  
> Scopes\[\]     site, subtree, object, class, data (Datensätze, die kein Inhalt sind: Einsendungen, Abonnenten, Logs)  
> Formats\[\]    die Dateiformate, die es liest oder schreibt (CSV, XLSX, ezpkg ...)  
> EntryURL     seine Hauptseite; ihr erster Teil ist das Modul des Werkzeugs  
> Permission   module/function, die zur Nutzung nötig ist; Alternativen durch "|" getrennt  
> Modules\[\]    weitere Module, deren Seiten zum Werkzeug gehören  
> HelpPage     seine Hilfeseite (eine Seiten-ID unterhalb von doc/eng-us/, ext/<extension>/<path> für die einer Erweiterung, #anchor erlaubt)  
> RoundTrip\[\]  die ids der Werkzeuge, die lesen, was es schreibt, oder schreiben, was es liest  
> Keywords\[\]   Aufgabenwörter, unter denen die Admin-Suche es findet (Englisch und Deutsch)  
> Actions\[\]    context|import oder export|label|URL\[|module/function\]: ein Eintrag eines Kontexts. Kontexte: node (jedes  
>              veröffentlichte Element), container (ein Element, unterhalb dessen der Benutzer anlegen darf), class (eine Klassenseite),  
>              setup (die Setup-Seiten); %nodeID%, %objectID%, %classID%, %classIdentifier% in der URL  
> Weight       die Reihenfolge in Listen (niedrigere zuerst, dann nach Name; Standard 100)  

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferRegistry`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Class=expKernelTransferRegistry
*/ ?>
```

## Factory

Standard: `expKernelTransferRegistry::fromContainer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Factory=expKernelTransferRegistry::fromContainer
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `kernel.core`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.core
*/ ?>
```

## Description

Standard: `The transfer registry: the import and export tools of the installation (tag transfer.tool).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Description=The transfer registry: the import and export tools of the installation (tag transfer.tool).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
