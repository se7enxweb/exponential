---
title: querycache.ini [DebugSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DebugSettings] in querycache.ini: Report"
keywords: Einstellung, querycache.ini, DebugSettings, INI, setting, Report
type: generated
generated: exp:help:examples
---
# querycache.ini [DebugSettings]

Der Block `[DebugSettings]` von `settings/querycache.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/querycache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/querycache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/querycache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/querycache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Report

> Mit eingeschalteter Debug-Ausgabe: Akkumulatoren der Gruppe „Query cache“ (Treffer,  
> Fehltreffer, Speicherungen, nicht gecachte Anweisungen), ein Zeitpunkt, wenn der Zustand  
> gelesen wird, und ein Bericht unten mit den Zählern dieser Anfrage, der Datenbank-  
> zeit, die die Treffer gespart haben, warum Anweisungen nicht gecacht wurden und welche Schreibzugriffe  
> alles veraltet gemacht haben. enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[DebugSettings]
Report=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von querycache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/querycache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
