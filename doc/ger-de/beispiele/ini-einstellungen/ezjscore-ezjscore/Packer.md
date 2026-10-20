---
title: ezjscore.ini [Packer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Packer] in ezjscore.ini (ezjscore): AppendLastModifiedTime, CustomHosts"
keywords: Einstellung, ezjscore.ini, Packer, INI, setting, AppendLastModifiedTime, CustomHosts
type: generated
generated: exp:help:examples
---
# ezjscore.ini [Packer]

Der Block `[Packer]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 2 Einstellung(en).

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AppendLastModifiedTime

> Appends the last modified time to the cached file name so Proxy/Browser cache  
> is forced to re fetch file on change. Note: cronjob to cleanup expired  
> files does not exist, but should be fairly trivial to create.  
> Enabled by default: packed scripts and styles are served with a long browser cache lifetime, so without the  
> time in the name an update to a stylesheet or script would not reach browsers until their copy expired.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[Packer]
AppendLastModifiedTime=enabled
*/ ?>
```

## CustomHosts

> For spreading the load of static files to separate domains as recommended by  
> YDN Performance Best Practices. Key should match file extension: .js | .css  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[Packer]
# ein leeres "CustomHosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CustomHosts[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
