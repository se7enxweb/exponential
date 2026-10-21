---
title: solr.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von solr.ini.
keywords: Beispiele, Referenz, solr.ini, examples, reference
type: generated
generated: exp:help:examples
---
# solr.ini

Die ausgelieferten Einstellungen von `settings/solr.ini`: 1 Blöcke, 9 Einstellungen.

> Einstellungen des Suchservers Solr für Exponential.  
>
> Solr ist OPTIONAL. Ab Werk verwendet diese Installation den eingebauten  
> Suchindex von eZ (bin/php/updatesearchindex.php); Solr wird erst verwendet, wenn es installiert  
> und hier aktiviert ist. Der Befehl exp:solr liest diese Datei, um zu entscheiden, ob Solr  
> konfiguriert ist, und, wenn ja, wie es zu erreichen oder auszuführen ist.  
>
> Aktivieren Sie Solr, indem Sie Enabled=true setzen und entweder:  
>   - Url  = die Basis-URL eines bereits laufenden (lokalen oder entfernten) Solr oder  
>   - BinaryPath = die Steuerdatei von Solr auf diesem Rechner (z. B. /opt/solr/bin/solr),  
>     mit Host, Port und DataDir für die lokale Instanz.  
>
> Überschreiben Sie dies pro Installation in settings/override/solr.ini.append.php; bearbeiten Sie nie  
> die ausgelieferte Datei für einen einzelnen Rechner.  

| Block | Einstellungen |
|---|---|
| [SolrSettings](SolrSettings.md) | 9 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/solr.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
