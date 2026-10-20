---
title: site.ini [HTMLForms]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTMLForms] in site.ini: Secret, RefusalLogCollapseSeconds"
keywords: Einstellung, site.ini, HTMLForms, INI, setting, Secret, RefusalLogCollapseSeconds
type: generated
generated: exp:help:examples
---
# site.ini [HTMLForms]

Der Block `[HTMLForms]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Secret

> # Settings dealing with HTML forms and security aspects of it.  
> Setting to specify a secret for the csrf protection, it  
> is highly recommended that you specify this pr project.  

Standard: `ThisTokenIsNotSoSecretChangeIt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTMLForms]
Secret=ThisTokenIsNotSoSecretChangeIt
*/ ?>
```

## RefusalLogCollapseSeconds

> A POST without its form token, or with a wrong one, is answered with a 403  
> and one line in var/log/warning.log. The same refusal again (same check,  
> siteaccess, path and client) within this many seconds is counted instead of  
> written; the next line for it gives the count. 0 writes every one.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTMLForms]
RefusalLogCollapseSeconds=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
