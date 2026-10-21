---
title: exphelp.ini [SecuritySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SecuritySettings] in exphelp.ini: Email, PolicyURL, AdvisoryURL"
keywords: Einstellung, exphelp.ini, SecuritySettings, INI, setting, Email, PolicyURL, AdvisoryURL
type: generated
generated: exp:help:examples
---
# exphelp.ini [SecuritySettings]

Der Block `[SecuritySettings]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Email

> Sicherheitsprobleme werden nie in einem öffentlichen Tracker gemeldet (SECURITY.md)  

Standard: `security@se7enx.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SecuritySettings]
Email=security@se7enx.com
*/ ?>
```

## PolicyURL

Standard: `https://github.com/se7enxweb/exponential/blob/main/SECURITY.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SecuritySettings]
PolicyURL=https://github.com/se7enxweb/exponential/blob/main/SECURITY.md
*/ ?>
```

## AdvisoryURL

> Das private Formular von GitHub zum Melden von Schwachstellen (nur die Maintainer sehen die Meldung)  

Standard: `https://github.com/se7enxweb/exponential/security/advisories/new`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SecuritySettings]
AdvisoryURL=https://github.com/se7enxweb/exponential/security/advisories/new
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
