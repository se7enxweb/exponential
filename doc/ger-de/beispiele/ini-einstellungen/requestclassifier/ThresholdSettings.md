---
title: requestclassifier.ini [ThresholdSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ThresholdSettings] in requestclassifier.ini: Max404PerMinute, WindowSeconds, Statuses"
keywords: Einstellung, requestclassifier.ini, ThresholdSettings, INI, setting, Max404PerMinute, WindowSeconds, Statuses
type: generated
generated: exp:help:examples
---
# requestclassifier.ini [ThresholdSettings]

Der Block `[ThresholdSettings]` von `settings/requestclassifier.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Max404PerMinute

> Mehr als so viele Nicht-gefunden-Antworten (Statuses\[\]) an einen Client innerhalb von WindowSeconds machen jede weitere in  
> diesem Zeitfenster zu einem Urteil threshold/404-rate (medium; high über dem Doppelten der Grenze). 0 schaltet die Regel ab. Der Client  
> wird über einen gesalzenen Hash seiner Adresse in var/<site>/cache/requestclassifier/counter/ gezählt, gemeinsam für jeden PHP-  
> Prozess der Installation (den des Webservers und den von Velocity); die Adresse selbst wird nie gespeichert.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ThresholdSettings]
Max404PerMinute=30
*/ ?>
```

## WindowSeconds

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ThresholdSettings]
WindowSeconds=60
*/ ?>
```

## Statuses

Standard: eine Liste mit 2: `404`, `410`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ThresholdSettings]
# ein leeres "Statuses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Statuses[]
Statuses[]=404
Statuses[]=410
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestclassifier.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
