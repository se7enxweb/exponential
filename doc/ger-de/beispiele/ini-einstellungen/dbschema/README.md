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

> Exponential-Konfigurationsdatei.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/dbschema.ini.append oder  
>       settings/override/dbschema.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  

| Block | Einstellungen |
|---|---|
| [SchemaSettings](SchemaSettings.md) | 2 |
| [LintChecker](LintChecker.md) | 8 |
| [mysql](mysql.md) | 1 |
| [postgresql](postgresql.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dbschema.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
