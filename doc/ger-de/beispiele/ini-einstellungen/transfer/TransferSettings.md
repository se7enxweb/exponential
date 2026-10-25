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

> Which engine runs a tool's background runs:  
>   auto        the job queue (expqueue) when it can take them, else the kernel's content jobs, else the  
>               tool's own process (the default)  
>   expqueue    the job queue; needs expqueue active, its tables installed, the tool's job type in expqueue.ini  
>               (the tool's QueueJobType=) and a live queue worker (see QueueNeedsWorker)  
>   contentjob  the kernel's content jobs (content.ini \[ContentJobSettings\] JobTypes\[transfer\]), which start a  
>               worker process of their own for each run  
>   process     the tool's own way of running in the background, as before 6.0.15  
> An engine that is not available falls back in that order. A tool may name its own engine (Engine= in its  
> \[Service\_transfer.<id>\] block of kernelservices.ini); ToolEngine\[<tool id>\] here wins over both.  

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
> Whether the job queue is used only while a queue worker is alive (a run queued with no worker would wait  
> until one starts). disabled: queue the run anyway.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transfer.ini.append.php
[TransferSettings]
QueueNeedsWorker=enabled
*/ ?>
```

## KeepDays

> Days the run history keeps a finished run (exp\_transfer\_run); 0 keeps every run.  
> The tools keep their own records and files as long as their own settings say.  

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
