---
title: Patterns in extension/ezfind
audience: developer
category: standards
order: 438
summary: Every class, view, fetch function, operator and plugin point of extension/ezfind with its design patterns.
keywords: extension/ezfind, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezfind

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezfind` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (51)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezauthorSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezauthorsolrstorage.php:12` | – |
| `ezbinaryfileSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezbinaryfilesolrstorage.php:12` | – |
| `ezdatatypeSolrStorage` | class | – | `extension/ezfind/classes/ezdatatypesolrstorage.php:12` | – |
| `ezdateSolrStorage` | class extends ezdatetimeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezdatesolrstorage.php:12` | – |
| `ezdatetimeSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezdatetimesolrstorage.php:12` | – |
| `ezenumSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezenumsolrstorage.php:12` | – |
| `ezfBaseException` | abstract class extends ezcBaseException | [Exception hierarchy](exception.md) | `extension/ezfind/classes/exceptions/ezfexception.php:15` | Base exception class for eZ Find |
| `ezfeZPSolrQueryBuilder` | class | [Builder](builder.md) | `extension/ezfind/classes/ezfezpsolrquerybuilder.php:8` | – |
| `eZFindAllWordsExtendedAttributeFilter` | class implements eZFindExtendedAttributeFilterInterface | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezfind/classes/extendedattributefilters/ezfindallwordsextendedattributefilter.php:25` | eZFindAllWordsExtendedAttributeFilter |
| `eZFindElevateConfiguration` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezfind/classes/ezfindelevateconfiguration.php:8` | – |
| `ezfIndexParentName` | class implements ezfIndexPlugin | [Strategy / handler](strategy-handler.md) | `extension/ezfind/classes/indexplugins/ezfindexparentname.php:16` | This class adds the name of the parent of the main node as de dedicated field. |
| `ezfIndexPlugin` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `extension/ezfind/classes/indexplugins/ezfindexplugin.php:18` | Description of ezfIndexPlugin. |
| `eZFindExtendedAttributeFilterFactory` | class | [Decorator / filter](decorator.md), [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Extended attribute filters factory |
| `eZFindExtendedAttributeFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `extension/ezfind/classes/ezfindextendedattributefilterinterface.php:10` | Extended attribute filter interface |
| `eZFindGeoDistExtendedAttributeFilter` | class implements eZFindExtendedAttributeFilterInterface | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezfind/classes/extendedattributefilters/ezfindgeodistextendedattributefilter.php:28` | SolR Geodist extended attribute filter |
| `ezfindInfo` | class | [Value object / struct](value-object.md) | `extension/ezfind/ezinfo.php:7` | – |
| `eZFindQueryText` | class | [Utility (static helper)](utility.md) | `extension/ezfind/classes/ezfindquerytext.php:29` | eZFindQueryText |
| `eZFindResultNode` | class extends eZContentObjectTreeNode | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezfind/classes/ezfindresultnode.php:8` | – |
| `eZFindResultObject` | class extends eZContentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezfind/classes/ezfindresultobject.php:8` | – |
| `eZFindServerCallFunctions` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezfind/classes/ezfindservercallfunctions.php:7` | Implements methods called remotely by sending XHR calls |
| `ezfModuleFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/ezfind/classes/ezfmodulefunctioncollection.php:12` | The ezfModuleFunctionCollection contains methods for functions defined in the module ezfind. |
| `ezfSearchResultInfo` | class | [Value object / struct](value-object.md) | `extension/ezfind/classes/ezfsearchresultinfo.php:12` | ezfSearchResultInfo contains additional search result information. |
| `ezfSolrDocumentFieldBase` | class | [Singleton / instance()](singleton.md) | `extension/ezfind/classes/ezfsolrdocumentfieldbase.php:21` | Class ezfSolrDocumentFieldBase. |
| `ezfSolrDocumentFieldDummyExample` | class extends ezfSolrDocumentFieldBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezfind/classes/ezfsolrdocumentfielddummyexample.php:42` | This class is provided as an example on how to write a datatype-specific indexing and querying handler. |
| `ezfSolrDocumentFieldGmapLocation` | class extends ezfSolrDocumentFieldBase | – | `extension/ezfind/classes/ezfsolrdocumentfieldgmaplocation.php:12` | – |
| `ezfSolrDocumentFieldName` | class | – | `extension/ezfind/classes/ezfsolrdocumentfieldname.php:13` | Class for looking up and storing Solr doc field names. |
| `ezfSolrDocumentFieldObjectRelation` | class extends ezfSolrDocumentFieldBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezfind/classes/ezfsolrdocumentfieldobjectrelation.php:14` | The ezfSolrDocumentFieldObjectRelation class handles indexing and querying for the ezobjectrelation and ezobjectrelationlist in eZFind. |
| `ezfSolrDocumentFieldXML` | class extends ezfSolrDocumentFieldBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezfind/classes/ezfsolrdocumentfieldxml.php:8` | – |
| `ezfSolrException` | class extends ezfBaseException | [Exception hierarchy](exception.md) | `extension/ezfind/classes/exceptions/ezfsolrexception.php:15` | Exception for Solr issues |
| `ezfSolrStorage` | class | – | `extension/ezfind/classes/ezfsolrstorage.php:18` | – |
| `ezfSolrUtils` | class | [Utility (static helper)](utility.md) | `extension/ezfind/classes/ezfsolrutils.php:16` | class ezfSolrUtils contains various methods to manipulate one or more Solr indexes with higher level functions |
| `ezfTemplateOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezfind/classes/ezfTemplateOperators.php:13` | Operators for eZFind |
| `ezfUpdateSearchIndexSolr` | class | – | `extension/ezfind/bin/php/updatesearchindexsolr.php:50` | Class containing controlling functions for updating the search index. |
| `ezkeywordSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezkeywordsolrstorage.php:12` | – |
| `ezmatrixSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezmatrixsolrstorage.php:12` | – |
| `ezmultioptionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezmultioptionsolrstorage.php:12` | – |
| `ezobjectrelationlistSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezobjectrelationlistsolrstorage.php:12` | – |
| `ezoptionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezoptionsolrstorage.php:12` | – |
| `ezpFileArchive` | abstract class | – | `extension/ezfind/classes/archive/filestorage/ezpfilearchive.php:11` | – |
| `ezpFileArchiveFactory` | class | [Factory and static factory methods](factory.md) | `extension/ezfind/classes/archive/filestorage/ezpfilearchivefactory.php:12` | – |
| `ezpFileArchiveFileSystem` | class extends ezpFileArchive | – | `extension/ezfind/classes/archive/filestorage/ezpfilearchivefilesystem.php:12` | – |
| `ezpriceSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezpricesolrstorage.php:12` | – |
| `ezproductcategorySolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezproductcategorysolrstorage.php:12` | – |
| `ezrangeoptionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezrangeoptionsolrstorage.php:12` | – |
| `ezselectionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezselectionsolrstorage.php:12` | – |
| `eZSolr` | class implements ezpSearchEngine | [Strategy / handler](strategy-handler.md) | `extension/ezfind/search/plugins/ezsolr/ezsolr.php:11` | Solr search plugin for Exponential |
| `eZSolrBase` | class | – | `extension/ezfind/classes/ezsolrbase.php:15` | – |
| `eZSolrDoc` | class | – | `extension/ezfind/classes/ezsolrdoc.php:17` | Solr Document class. |
| `ezurlSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezurlsolrstorage.php:12` | – |
| `ezuserSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezusersolrstorage.php:12` | – |
| `ezxmltextSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezxmltextsolrstorage.php:12` | – |

## Module views (3)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezfind/elevate` | `elevate.php` | elevate | `extension/ezfind/modules/ezfind/module.php:11` |
| `/ezfind/elevation_detail` | `elevation_detail.php` | elevate | `extension/ezfind/modules/ezfind/module.php:24` |
| `/ezfind/remove_elevation` | `remove_elevation.php` | elevate | `extension/ezfind/modules/ezfind/module.php:37` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `ezfind/elevate` | – | `extension/ezfind/modules/ezfind/module.php:46` |

## Fetch functions (7)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'ezfind', 'search' )` | `ezfModuleFunctionCollection::search()` | `extension/ezfind/modules/ezfind/function_definition.php:10` |
| `fetch( 'ezfind', 'getDefaultSearchFacets' )` | `ezfModuleFunctionCollection::getDefaultSearchFacets()` | `extension/ezfind/modules/ezfind/function_definition.php:112` |
| `fetch( 'ezfind', 'facetParameters' )` | `ezfModuleFunctionCollection::getFacetParameters()` | `extension/ezfind/modules/ezfind/function_definition.php:120` |
| `fetch( 'ezfind', 'filterParameters' )` | `ezfModuleFunctionCollection::getFilterParameters()` | `extension/ezfind/modules/ezfind/function_definition.php:128` |
| `fetch( 'ezfind', 'rawSolrRequest' )` | `ezfModuleFunctionCollection::rawSolrRequest()` | `extension/ezfind/modules/ezfind/function_definition.php:138` |
| `fetch( 'ezfind', 'moreLikeThis' )` | `ezfModuleFunctionCollection::moreLikeThis()` | `extension/ezfind/modules/ezfind/function_definition.php:156` |
| `fetch( 'ezfind', 'elevateConfiguration' )` | `ezfModuleFunctionCollection::fetchElevateConfiguration()` | `extension/ezfind/modules/ezfind/function_definition.php:211` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| solr_escape, solr_quotes_escape | `ezfTemplateOperators` | `extension/ezfind/autoloads/eztemplateautoload.php:12` |

## Cronjob parts (2)

| Cronjob | File | Description |
|---|---|---|
| `ezfindexsubtree` | `extension/ezfind/cronjobs/ezfindexsubtree.php` | – |
| `ezfoptimizeindex` | `extension/ezfind/cronjobs/ezfoptimizeindex.php` | – |

## INI plugin points (8)

| Setting | Class | Set in |
|---|---|---|
| `[SolrFieldMapSettings] CustomMap[dummy_example]` | `ezfSolrDocumentFieldDummyExample` | `extension/ezfind/settings/ezfind.ini:118` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelation]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:119` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelationlist]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:120` |
| `[SolrFieldMapSettings] CustomMap[ezxmltext]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:121` |
| `[SolrFieldMapSettings] CustomMap[ezmatrix]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:122` |
| `[ExtendedAttributeFilters] FiltersList[geodist]` | `eZFindGeoDistExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:337` |
| `[ExtendedAttributeFilters] FiltersList[ezfindallwords]` | `eZFindAllWordsExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:341` |
| `[ezjscServer_ezfind] Class` | `eZFindServerCallFunctions` | `extension/ezfind/settings/ezjscore.ini.append.php:4` |
