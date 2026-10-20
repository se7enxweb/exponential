---
title: mailpreferences.ini [LinkSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LinkSettings] in mailpreferences.ini: BaseURL"
keywords: Einstellung, mailpreferences.ini, LinkSettings, INI, setting, BaseURL
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [LinkSettings]

Der Block `[LinkSettings]` von `settings/mailpreferences.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BaseURL

> Where the links in mail point to: scheme and host (and path of the  
> siteaccess), e.g. https://www.example.com. Empty: https:// and the SiteURL  
> of the default siteaccess.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[LinkSettings]
BaseURL=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
