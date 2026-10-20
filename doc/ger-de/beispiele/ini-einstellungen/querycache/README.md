---
title: querycache.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von querycache.ini.
keywords: Beispiele, Referenz, querycache.ini, examples, reference
type: generated
generated: exp:help:examples
---
# querycache.ini

Die ausgelieferten Einstellungen von `settings/querycache.ini`: 2 Blöcke, 7 Einstellungen.

> Der SQL-Abfrage-Cache: die Zeilen, die ein SELECT zurückgegeben hat, erneut geliefert, bis ein Schreibzugriff  
> auf eine der gelesenen Tabellen sie veraltet macht. Für die SQL-Engines (MySQL,  
> MariaDB, PostgreSQL, SQLite und Oracle über den Treiber der Erweiterung  
> ezoracle); der MongoDB-Treiber verwendet ihn nicht.  
> Siehe doc/eng-us/bc/6.0/sql-query-cache.md.  

| Block | Einstellungen |
|---|---|
| [QueryCacheSettings](QueryCacheSettings.md) | 6 |
| [DebugSettings](DebugSettings.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/querycache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
