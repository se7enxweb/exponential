---
title: syscard.ini [Logo]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Logo] in syscard.ini: Logo, Directories"
keywords: Einstellung, syscard.ini, Logo, INI, setting, Directories
type: generated
generated: exp:help:examples
---
# syscard.ini [Logo]

Der Block `[Logo]` von `settings/syscard.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Logo

> Die Zeichnungen: <Logo>-big.txt, <Logo>-small.txt, <Logo>-mini.txt (und .ascii.txt), wordmark.txt, in diesen  
> Verzeichnissen (das zuletzt aufgeführte wird zuerst durchsucht)  

Standard: `e`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Logo]
Logo=e
*/ ?>
```

## Directories

Standard: eine Liste mit 1: `share/syscard/logos`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Logo]
# ein leeres "Directories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Directories[]
Directories[]=share/syscard/logos
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
