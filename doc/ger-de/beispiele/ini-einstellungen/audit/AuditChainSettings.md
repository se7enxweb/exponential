---
title: audit.ini [AuditChainSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditChainSettings] in audit.ini: Algorithm, Checkpoints"
keywords: Einstellung, audit.ini, AuditChainSettings, INI, setting, Algorithm, Checkpoints
type: generated
generated: exp:help:examples
---
# audit.ini [AuditChainSettings]

Der Block `[AuditChainSettings]` von `settings/audit.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Algorithm

> Die Hash-Kette (Q4). Immer an: Es gibt kein "disabled".  
> sha256 (Vorschlag; das Präfix in jedem Hash nennt den Algorithmus, er kann also später wechseln).  

Standard: `sha256`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChainSettings]
Algorithm=sha256
*/ ?>
```

## Checkpoints

> Ein signierter Prüfpunkt des Kopfes jedes Kanals, täglich vom Cronjob-Teil geschrieben und  
> an die Ziele gesendet.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChainSettings]
Checkpoints=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
