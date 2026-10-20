---
title: fetchalias.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von fetchalias.ini.
keywords: Beispiele, Referenz, fetchalias.ini, examples, reference
type: generated
generated: exp:help:examples
---
# fetchalias.ini

Die ausgelieferten Einstellungen von `settings/fetchalias.ini`: 8 Blöcke, 27 Einstellungen.

> Exponential-Konfigurationsdatei für Fetch-Aliase.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/fetchalias.ini.append.php an,  
>       für mehr Sicherheit im Modus ohne virtuellen Host  
> fetch\_alias dient als Ersatz für fetch bei häufig verwendeten Fetch-Funktionen  
>             oder zur Vereinfachung komplizierter Fetch-Funktionen.  
> Allgemeine Syntax  
>
> \[<parameter>\]         # name of first parameter in fetch\_alias  
> Module=          # which module to use  
> FunctionName=    # name of function defined in kernel/<module>/function\_definition.php  
> Parameter\[key\]=<value>  # key is the name of the parameter defined for the function in kernel/<module>/function\_definition.php  
>                         # value is the key of the hash entry in fetch\_alias  
> Constant\[key\]=<value>   # key is the name of the parameter defined for the function in kernel/<module>/function\_definition.php  
>                         # value is a constant defined in this ini file. use ; to separate elements if the value is supposed to be an array  

| Block | Einstellungen |
|---|---|
| [object](object.md) | 3 |
| [news_list](news_list.md) | 3 |
| [by_identifier](by_identifier.md) | 3 |
| [comments](comments.md) | 4 |
| [children](children.md) | 3 |
| [children_count](children_count.md) | 3 |
| [subtree](subtree.md) | 4 |
| [reviews](reviews.md) | 4 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/fetchalias.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
