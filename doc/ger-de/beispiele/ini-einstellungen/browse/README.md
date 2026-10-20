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

> Exponential configuration file for content browsing.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/browse.ini.append or  
>       settings/override/browse.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
> Settings for the browse page.  
> For start nodes and top level nodes it is possible to use text aliases  
> You can use the following aliases:  
>  content - Represents the content root node defined NodeSettings/RootNode in content.ini, usually 2  
>  users   - Represents the users root node defined NodeSettings/UserRootNode in content.ini, usually 5  
> Other aliases are also possible by defining them in AliasList below  

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
