---
title: cronjob.ini [CronjobPart-cache_cleanup]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CronjobPart-cache_cleanup] in cronjob.ini: Scripts"
keywords: Einstellung, cronjob.ini, CronjobPart-cache_cleanup, INI, setting, Scripts
type: generated
generated: exp:help:examples
---
# cronjob.ini [CronjobPart-cache_cleanup]

Der Block `[CronjobPart-cache_cleanup]` von `settings/cronjob.ini`, mit 1 Einstellung(en).

> Entfernt View-Cache- und Cache-Block-Dateien, die abgelaufen oder alt geworden sind;  
> ohne ihn bleiben sie auf der Festplatte, bis derselbe Schlüssel erneut erzeugt wird. Läuft für  
> das var-Verzeichnis des angegebenen Siteaccess, also einmal pro VarDir:  
>   php runcronjobs.php -s <siteaccess> cache\_cleanup  

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Scripts

> Der rollenbewusste HTTP-Cache (settings/httpcache.ini); tut nichts, solange er aus ist.  
> Die Ergebnisdateien des SQL-Abfrage-Caches (settings/querycache.ini Storage=file,  
> oder auto ohne APCu); tut nichts, solange es keine gibt.  

Standard: eine Liste mit 3: `cachecleanup.php`, `httpcache_cleanup.php`, `querycache_cleanup.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-cache_cleanup]
# ein leeres "Scripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scripts[]
Scripts[]=cachecleanup.php
Scripts[]=httpcache_cleanup.php
Scripts[]=querycache_cleanup.php
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
