---
title: workflow.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von workflow.ini.
keywords: Beispiele, Referenz, workflow.ini, examples, reference
type: generated
generated: exp:help:examples
---
# workflow.ini

Die ausgelieferten Einstellungen von `settings/workflow.ini`: 3 Blöcke, 6 Einstellungen.

> Exponential-Konfigurationsdatei für Workflows.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/workflow.ini.append oder  
>       settings/override/workflow.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  

| Block | Einstellungen |
|---|---|
| [EventSettings](EventSettings.md) | 3 |
| [OperationSettings](OperationSettings.md) | 1 |
| [SimpleShippingWorkflow](SimpleShippingWorkflow.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
