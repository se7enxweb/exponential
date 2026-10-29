---
title: datatable.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von datatable.ini.
keywords: Beispiele, Referenz, datatable.ini, examples, reference
type: generated
generated: exp:help:examples
---
# datatable.ini

Die ausgelieferten Einstellungen von `settings/datatable.ini`: 3 Blöcke, 10 Einstellungen.

> Datentabellen: die Tabellen der persistenten Klassen der Installation (eZPersistentObject), die benannten Datenbankverbindungen,  
> die sie und die Werkzeuge nutzen können, ihr Schema, ihre Beziehungen zu Inhalten und die Synchronisation zwischen  
> Tabellen und Inhalten. Siehe doc/eng-us/features/6.0/data-tables.md.  
>
> Verbindungen, die in der Administration angelegt werden (Setup > Datentabellen > Verbindungen), sind Zeilen der Tabelle  
> exp\_data\_connection und brauchen hier nichts. Diese Datei ist für das, was eine Erweiterung mitliefert: eine Verbindung, die sie deklariert, die  
> Verbindung, die eine ihrer Klassen nutzt, eine Beziehung, die ihre definition() nicht angibt.  

| Block | Einstellungen |
|---|---|
| [DataTableSettings](DataTableSettings.md) | 5 |
| [ConnectionSettings](ConnectionSettings.md) | 4 |
| [ClassSettings](ClassSettings.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatable.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
