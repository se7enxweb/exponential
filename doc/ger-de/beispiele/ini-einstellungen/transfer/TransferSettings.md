---
title: transfer.ini [TransferSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TransferSettings] in transfer.ini: Engine, ToolEngine, QueueNeedsWorker, KeepDays"
keywords: Einstellung, transfer.ini, TransferSettings, INI, setting, Engine, ToolEngine, QueueNeedsWorker, KeepDays
type: generated
generated: exp:help:examples
---
# transfer.ini [TransferSettings]

Der Block `[TransferSettings]` von `settings/transfer.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/transfer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transfer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transfer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transfer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Engine

> Welche Engine die Hintergrundläufe eines Werkzeugs ausführt:  
>   auto        die Job-Warteschlange (expqueue), wenn sie sie annehmen kann, sonst die Inhalts-Jobs des Kernels, sonst der  
>               eigene Prozess des Werkzeugs (der Standard)  
>   expqueue    die Job-Warteschlange; braucht aktives expqueue, installierte Tabellen, den Job-Typ des Werkzeugs in expqueue.ini  
>               (das QueueJobType= des Werkzeugs) und einen laufenden Queue-Worker (siehe QueueNeedsWorker)  
>   contentjob  die Inhalts-Jobs des Kernels (content.ini \[ContentJobSettings\] JobTypes\[transfer\]), die für jeden Lauf einen  
>               eigenen Worker-Prozess starten  
>   process     die eigene Art des Werkzeugs, im Hintergrund zu laufen, wie vor 6.0.15  
> Eine nicht verfügbare Engine fällt in dieser Reihenfolge zurück. Ein Werkzeug kann seine eigene Engine nennen (Engine= in seinem  
> Block \[Service\_transfer.<id>\] von kernelservices.ini); ToolEngine\[<tool id>\] hier gewinnt gegenüber beiden.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transfer.ini.append.php
[TransferSettings]
Engine=auto
*/ ?>
```

## ToolEngine

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transfer.ini.append.php
[TransferSettings]
# ein leeres "ToolEngine[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ToolEngine[]
*/ ?>
```

## QueueNeedsWorker

> ToolEngine\[transfer.xrowextract\]=contentjob  
> Ob die Job-Warteschlange nur genutzt wird, solange ein Queue-Worker lebt (ein Lauf, der ohne Worker eingereiht wird, würde warten,  
> bis einer startet). disabled: den Lauf trotzdem einreihen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transfer.ini.append.php
[TransferSettings]
QueueNeedsWorker=enabled
*/ ?>
```

## KeepDays

> Tage, die der Laufverlauf einen beendeten Lauf behält (exp\_transfer\_run); 0 behält jeden Lauf.  
> Die Werkzeuge behalten ihre eigenen Aufzeichnungen und Dateien so lange, wie ihre eigenen Einstellungen es sagen.  

Standard: `365`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transfer.ini.append.php
[TransferSettings]
KeepDays=365
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transfer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transfer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
