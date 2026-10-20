---
title: dbschema.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von dbschema.ini.
keywords: Beispiele, Referenz, dbschema.ini, examples, reference
type: generated
generated: exp:help:examples
---
# dbschema.ini

Die ausgelieferten Einstellungen von `settings/dbschema.ini`: 4 Blöcke, 12 Einstellungen.

> Exponential configuration file.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/dbschema.ini.append or  
>       settings/override/dbschema.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

| Block | Einstellungen |
|---|---|
| [SchemaSettings](SchemaSettings.md) | 2 |
| [LintChecker](LintChecker.md) | 8 |
| [mysql](mysql.md) | 1 |
| [postgresql](postgresql.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dbschema.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
