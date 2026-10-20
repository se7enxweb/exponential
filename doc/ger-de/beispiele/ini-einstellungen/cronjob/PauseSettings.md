---
title: cronjob.ini [PauseSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PauseSettings] in cronjob.ini: Pause, Directory"
keywords: Einstellung, cronjob.ini, PauseSettings, INI, setting, Pause, Directory
type: generated
generated: exp:help:examples
---
# cronjob.ini [PauseSettings]

Der Block `[PauseSettings]` von `settings/cronjob.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Pause

> Pausieren von Cronjob-Teilen unter Setup > Cronjobs: Einen pausierten Teil (oder "all") lässt  
> runcronjobs.php aus, egal wer es startet, bis er fortgesetzt wird. Der Zustand ist eine Datei pro Teil,  
> cronpart\_deactivated\_<part>.txt mit "1" für pausiert, das Format, das die Layout-Erweiterungen für pausierte  
> Teile schon verwenden, sodass eine Seite einer solchen Erweiterung denselben Zustand zeigt. disabled ignoriert sie.  
> Anleitung: doc/eng-us/features/6.0/cronjob-pause.md  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[PauseSettings]
Pause=enabled
*/ ?>
```

## Directory

> Wo die Dateien liegen, relativ zum var-Verzeichnis des Siteaccess. Leer: das  
> Verzeichnis cronjob\_control unter dem Cache-Verzeichnis.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[PauseSettings]
Directory=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
