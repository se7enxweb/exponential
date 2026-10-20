---
title: content.ini [RSSImportCleanupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSImportCleanupSettings] in content.ini: Enabled, KeepPerFeed, ClassIdentifiers, CleanupUser, LogFile, MoveToTrash"
keywords: Einstellung, content.ini, RSSImportCleanupSettings, INI, setting, Enabled, KeepPerFeed, ClassIdentifiers, CleanupUser, LogFile, MoveToTrash
type: generated
generated: exp:help:examples
---
# content.ini [RSSImportCleanupSettings]

Der Block `[RSSImportCleanupSettings]` von `settings/content.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Kürzt, was der RSS-Import angelegt hat, und behält die neuesten Einträge jedes Feeds.  
>
> Ein Importlauf legt für jeden Eintrag im Feed ein Objekt an und nimmt nie eines  
> weg, eine Website, die einen lebhaften Feed importiert, wächst also unbegrenzt. Führen Sie es über den  
> Cronjob-Teil 'cleanuprss' oder von Hand aus:  
>
>   php bin/php/cleanuprss.php --dry-run  
>
> Nichts geschieht, bevor Enabled true ist UND ClassIdentifiers mindestens eine  
> Klasse nennt: Dies entfernt Inhalte, und bei einer Installation, die nicht dafür  
> eingerichtet ist, gibt es keine sichere Vermutung darüber, was weg darf.  
> Aus, bis die Website etwas anderes sagt.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
Enabled=false
*/ ?>
```

## KeepPerFeed

> Wie viele Einträge pro Feed behalten werden, die neuesten zuerst. Mindestens 1.  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
KeepPerFeed=100
*/ ?>
```

## ClassIdentifiers

> Die Klassen, die entfernt werden dürfen. Klassenbezeichner; nichts außerhalb dieser  
> Liste wird angefasst, wie alt es auch ist – ein Ordner oder eine Seite, die zufällig  
> im Ziel liegt, ist also sicher. Leer bedeutet, dass nichts entfernt wird.  
> Beispiel:  
> ClassIdentifiers\[\]=article  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
# ein leeres "ClassIdentifiers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassIdentifiers[]
*/ ?>
```

## CleanupUser

> Das Entfernen wird auf Berechtigungen geprüft, es läuft also als jemand, der entfernen darf.  

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
CleanupUser=admin
*/ ?>
```

## LogFile

> Wo der Lauf festgehalten wird. Leer schreibt keine Datei.  

Standard: `rsscleanup.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
LogFile=rsscleanup.log
*/ ?>
```

## MoveToTrash

> Ob entfernte Einträge in den Papierkorb kommen, wo sie sich wiederherstellen lassen, oder  
> sofort gelöscht werden. Der Papierkorb eines lebhaften Feeds muss seinerseits geleert werden.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
MoveToTrash=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
