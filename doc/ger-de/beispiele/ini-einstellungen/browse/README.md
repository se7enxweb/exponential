---
title: browse.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von browse.ini.
keywords: Beispiele, Referenz, browse.ini, examples, reference
type: generated
generated: exp:help:examples
---
# browse.ini

Die ausgelieferten Einstellungen von `settings/browse.ini`: 34 Blöcke, 121 Einstellungen.

> Exponential-Konfigurationsdatei für das Durchsuchen von Inhalten.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/browse.ini.append oder  
>       settings/override/browse.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
> Einstellungen der Seite zum Durchsuchen.  
> Für Startknoten und Knoten der obersten Ebene können Text-Aliase verwendet werden  
> Sie können die folgenden Aliase verwenden:  
>  content - steht für den Wurzelknoten der Inhalte, definiert in NodeSettings/RootNode in content.ini, meist 2  
>  users   - steht für den Wurzelknoten der Benutzer, definiert in NodeSettings/UserRootNode in content.ini, meist 5  
> Weitere Aliase sind möglich, wenn Sie sie unten in AliasList definieren  

| Block | Einstellungen |
|---|---|
| [BrowseSettings](BrowseSettings.md) | 6 |
| [users](users.md) | 2 |
| [AssignSection](AssignSection.md) | 3 |
| [ExportSourceBrowse](ExportSourceBrowse.md) | 4 |
| [RSSExportImageBrowse](RSSExportImageBrowse.md) | 5 |
| [RSSObjectBrowse](RSSObjectBrowse.md) | 4 |
| [RSSOPMLNodeBrowse](RSSOPMLNodeBrowse.md) | 4 |
| [RSSUserBrowse](RSSUserBrowse.md) | 5 |
| [AssignRole](AssignRole.md) | 5 |
| [FindLimitationSubtree](FindLimitationSubtree.md) | 3 |
| [FindLimitationNode](FindLimitationNode.md) | 3 |
| [FindProduct](FindProduct.md) | 3 |
| [AddCustomer](AddCustomer.md) | 4 |
| [NewObjectAddNodeAssignment](NewObjectAddNodeAssignment.md) | 5 |
| [AddNodeAssignment](AddNodeAssignment.md) | 4 |
| [AddPrimaryNodeAssignment](AddPrimaryNodeAssignment.md) | 4 |
| [MoveNodeAssignment](MoveNodeAssignment.md) | 4 |
| [MoveNode](MoveNode.md) | 4 |
| [SwapNode](SwapNode.md) | 4 |
| [AddRelatedObject](AddRelatedObject.md) | 3 |
| [AddRelatedObjectToDataType](AddRelatedObjectToDataType.md) | 3 |
| [AddRelatedObjectListToDataType](AddRelatedObjectListToDataType.md) | 3 |
| [AddRelatedImageToDataType](AddRelatedImageToDataType.md) | 3 |
| [AddBookmark](AddBookmark.md) | 3 |
| [AddSubtreeSubscribingNode](AddSubtreeSubscribingNode.md) | 3 |
| [SelectObjectRelationListNode](SelectObjectRelationListNode.md) | 3 |
| [SelectObjectRelationNode](SelectObjectRelationNode.md) | 3 |
| [SelectToolbarNode](SelectToolbarNode.md) | 3 |
| [SelectToolbarNodePath](SelectToolbarNodePath.md) | 3 |
| [CopyNode](CopyNode.md) | 3 |
| [CopySubtree](CopySubtree.md) | 3 |
| [SelectSingleUser](SelectSingleUser.md) | 3 |
| [SelectMultipleUsers](SelectMultipleUsers.md) | 3 |
| [ContentCheckScope](ContentCheckScope.md) | 3 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
