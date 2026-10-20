---
title: syscard.ini [Runtime]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Runtime] in syscard.ini: TotalBudget, Isolation"
keywords: Einstellung, syscard.ini, Runtime, INI, setting, TotalBudget, Isolation
type: generated
generated: exp:help:examples
---
# syscard.ini [Runtime]

Der Block `[Runtime]` von `settings/syscard.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TotalBudget

> The seconds all providers together may take; the ones left after that show "n/a (time)"  

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Runtime]
TotalBudget=6
*/ ?>
```

## Isolation

> enabled: each provider runs in a child process that is ended when its budget is spent, so one that hangs (a  
> database that does not answer, a stalled disk) shows "n/a (timeout)" and never hangs the card (needs the pcntl and  
> posix extensions of the command-line PHP; without them the providers keep their budget themselves)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Runtime]
Isolation=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
