---
title: rad.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von rad.ini.
keywords: Beispiele, Referenz, rad.ini, examples, reference
type: generated
generated: exp:help:examples
---
# rad.ini

Die ausgelieferten Einstellungen von `settings/rad.ini`: 4 Blöcke, 20 Einstellungen.

> Die Register, die die Übersicht der Erweiterungspunkte Eintrag für Eintrag liest (Setup > RAD-Werkzeuge > Übersicht der Erweiterungspunkte,  
> setup/radsurvey/(show)/registries; doc/eng-us/bc/6.0/rad-extension-surface.md "Registries declared in rad.ini").  
>
> Ein Register ist eine Liste von Implementierungen, die etwas aus seinen Einstellungen oder aus seinem Code liest: KI-Provider,  
> MCP-Werkzeuge, Auftragstypen der Warteschlange, Event-Listener. Jedes wird hier vom Kernel deklariert oder von einer Erweiterung in  
> extension/<name>/settings/rad.ini.append.php, damit die Übersicht seine Einträge zählt, jeden prüft und  
> sie zu den Kopfzahlen von setup/rad hinzufügt, ohne dass der Kernel die Erweiterung kennen muss.  
>
> Deklarationen werden aus settings/rad.ini, settings/override/rad.ini.append.php und der rad.ini.append.php  
> jeder aktiven Erweiterung gelesen. Eine spätere Datei ersetzt einen einzelnen Wert einer früheren und ergänzt ein Array; eine Zeile  
> "Registries\[\]" ohne Wert leert die Liste zuerst, wie überall in den Einstellungen (eine Erweiterung beginnt ihre  
> eigene Liste also nie damit: Sie würde die Register des Kernels und jeder Erweiterung davor verwerfen).  

| Block | Einstellungen |
|---|---|
| [RegistrySettings](RegistrySettings.md) | 1 |
| [Registry_eventlisteners](Registry_eventlisteners.md) | 6 |
| [Registry_cronjobparts](Registry_cronjobparts.md) | 7 |
| [Registry_auditsources](Registry_auditsources.md) | 6 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rad.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
