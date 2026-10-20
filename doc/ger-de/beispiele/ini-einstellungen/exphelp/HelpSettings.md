---
title: exphelp.ini [HelpSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HelpSettings] in exphelp.ini: Enabled, CacheDir, EditBaseURL"
keywords: Einstellung, exphelp.ini, HelpSettings, INI, setting, Enabled, CacheDir, EditBaseURL
type: generated
generated: exp:help:examples
---
# exphelp.ini [HelpSettings]

Der Block `[HelpSettings]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> enabled|true: Das Modul antwortet; false: Jede exphelp-Ansicht ist 404, und die Hilfe-Schaltflächen werden nicht angezeigt  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[HelpSettings]
Enabled=true
*/ ?>
```

## CacheDir

> Das Verzeichnis unter dem Cache-Verzeichnis (var/<site>/cache/<CacheDir>) für den Index und den Suchindex  

Standard: `exphelp`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[HelpSettings]
CacheDir=exphelp
*/ ?>
```

## EditBaseURL

> Der Link „Auf GitHub bearbeiten“ einer Seite: diese URL, gefolgt vom Repository-Pfad der Seite (doc/...); leer: kein Link  

Standard: `https://github.com/se7enxweb/exponential/blob/main/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[HelpSettings]
EditBaseURL=https://github.com/se7enxweb/exponential/blob/main/
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
