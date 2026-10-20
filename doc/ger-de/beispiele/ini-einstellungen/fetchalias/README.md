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

> Exponential fetch alias configuration file.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/fetchalias.ini.append.php  
>       for more security in non-virtualhost modes  
> fetch\_alias is to used as a replacement for fetch for often used fetch functions  
>             or to simplify complicated fetch functions.  
> General syntax  
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
