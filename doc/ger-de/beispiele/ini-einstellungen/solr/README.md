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

> Exponential Solr search server settings.  
>
> Solr is OPTIONAL. Out of the box this installation uses the built-in eZ search  
> index (bin/php/updatesearchindex.php); Solr is only used once it is installed  
> and enabled here. The exp:solr command reads this file to decide whether Solr  
> is configured, and, when it is, how to reach or run it.  
>
> Enable Solr by setting Enabled=true and either:  
>   - Url  = the base URL of an already-running (local or remote) Solr, or  
>   - BinaryPath = the Solr control binary on this machine (e.g. /opt/solr/bin/solr),  
>     with Host, Port and DataDir for the local instance.  
>
> Override per installation in settings/override/solr.ini.append.php; never edit  
> the packaged file for a single machine.  

| Block | Einstellungen |
|---|---|
| [SolrSettings](SolrSettings.md) | 9 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/solr.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
