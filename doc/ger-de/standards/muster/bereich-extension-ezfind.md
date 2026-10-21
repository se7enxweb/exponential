---
title: Muster in extension/ezfind
audience: developer
category: standards
order: 438
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezfind mit den Entwurfsmustern.
keywords: extension/ezfind, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezfind

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezfind` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (51)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezauthorSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezauthorsolrstorage.php:12` | – |
| `ezbinaryfileSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezbinaryfilesolrstorage.php:12` | – |
| `ezdatatypeSolrStorage` | class | – | `extension/ezfind/classes/ezdatatypesolrstorage.php:12` | – |
| `ezdateSolrStorage` | class extends ezdatetimeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezdatesolrstorage.php:12` | – |
| `ezdatetimeSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezdatetimesolrstorage.php:12` | – |
| `ezenumSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezenumsolrstorage.php:12` | – |
| `ezfBaseException` | abstract class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md) | `extension/ezfind/classes/exceptions/ezfexception.php:15` | Basisausnahmeklasse für eZ Find |
| `ezfeZPSolrQueryBuilder` | class | [Builder](builder.md) | `extension/ezfind/classes/ezfezpsolrquerybuilder.php:8` | – |
| `eZFindAllWordsExtendedAttributeFilter` | class implements eZFindExtendedAttributeFilterInterface | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezfind/classes/extendedattributefilters/ezfindallwordsextendedattributefilter.php:25` | eZFindAllWordsExtendedAttributeFilter |
| `eZFindElevateConfiguration` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezfind/classes/ezfindelevateconfiguration.php:8` | – |
| `ezfIndexParentName` | class implements ezfIndexPlugin | [Strategie / Handler](strategie-handler.md) | `extension/ezfind/classes/indexplugins/ezfindexparentname.php:16` | Diese Klasse fügt den Namen des Elternknotens des Hauptknotens als eigenes Feld hinzu. |
| `ezfIndexPlugin` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `extension/ezfind/classes/indexplugins/ezfindexplugin.php:18` | Beschreibung von ezfIndexPlugin. |
| `eZFindExtendedAttributeFilterFactory` | class | [Dekorierer / Filter](dekorierer.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Fabrik für erweiterte Attributfilter |
| `eZFindExtendedAttributeFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/ezfind/classes/ezfindextendedattributefilterinterface.php:10` | Schnittstelle für erweiterte Attributfilter |
| `eZFindGeoDistExtendedAttributeFilter` | class implements eZFindExtendedAttributeFilterInterface | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezfind/classes/extendedattributefilters/ezfindgeodistextendedattributefilter.php:28` | Erweiterter Attributfilter SolR Geodist |
| `ezfindInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezfind/ezinfo.php:7` | – |
| `eZFindQueryText` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezfind/classes/ezfindquerytext.php:29` | eZFindQueryText |
| `eZFindResultNode` | class extends eZContentObjectTreeNode | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezfind/classes/ezfindresultnode.php:8` | – |
| `eZFindResultObject` | class extends eZContentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezfind/classes/ezfindresultobject.php:8` | – |
| `eZFindServerCallFunctions` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezfind/classes/ezfindservercallfunctions.php:7` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden |
| `ezfModuleFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `extension/ezfind/classes/ezfmodulefunctioncollection.php:12` | Die ezfModuleFunctionCollection enthält Methoden für die im Modul ezfind definierten Funktionen. |
| `ezfSearchResultInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezfind/classes/ezfsearchresultinfo.php:12` | ezfSearchResultInfo enthält zusätzliche Informationen zum Suchergebnis. |
| `ezfSolrDocumentFieldBase` | class | [Singleton / instance()](singleton.md) | `extension/ezfind/classes/ezfsolrdocumentfieldbase.php:21` | Klasse ezfSolrDocumentFieldBase. |
| `ezfSolrDocumentFieldDummyExample` | class extends ezfSolrDocumentFieldBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezfind/classes/ezfsolrdocumentfielddummyexample.php:42` | Diese Klasse dient als Beispiel dafür, wie man einen datentypspezifischen Handler für Indizierung und Abfrage schreibt. |
| `ezfSolrDocumentFieldGmapLocation` | class extends ezfSolrDocumentFieldBase | – | `extension/ezfind/classes/ezfsolrdocumentfieldgmaplocation.php:12` | – |
| `ezfSolrDocumentFieldName` | class | – | `extension/ezfind/classes/ezfsolrdocumentfieldname.php:13` | Klasse zum Nachschlagen und Speichern von Feldnamen von Solr-Dokumenten. |
| `ezfSolrDocumentFieldObjectRelation` | class extends ezfSolrDocumentFieldBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezfind/classes/ezfsolrdocumentfieldobjectrelation.php:14` | Die Klasse ezfSolrDocumentFieldObjectRelation übernimmt Indizierung und Abfrage für ezobjectrelation und ezobjectrelationlist in eZFind. |
| `ezfSolrDocumentFieldXML` | class extends ezfSolrDocumentFieldBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezfind/classes/ezfsolrdocumentfieldxml.php:8` | – |
| `ezfSolrException` | class extends ezfBaseException | [Ausnahmehierarchie](ausnahme.md) | `extension/ezfind/classes/exceptions/ezfsolrexception.php:15` | Ausnahme für Solr-Probleme |
| `ezfSolrStorage` | class | – | `extension/ezfind/classes/ezfsolrstorage.php:18` | – |
| `ezfSolrUtils` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezfind/classes/ezfsolrutils.php:16` | Die Klasse ezfSolrUtils enthält verschiedene Methoden, um einen oder mehrere Solr-Indizes mit Funktionen höherer Ebene zu bearbeiten |
| `ezfTemplateOperators` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezfind/classes/ezfTemplateOperators.php:13` | Operatoren für eZFind |
| `ezfUpdateSearchIndexSolr` | class | – | `extension/ezfind/bin/php/updatesearchindexsolr.php:50` | Klasse mit steuernden Funktionen zum Aktualisieren des Suchindex. |
| `ezkeywordSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezkeywordsolrstorage.php:12` | – |
| `ezmatrixSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezmatrixsolrstorage.php:12` | – |
| `ezmultioptionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezmultioptionsolrstorage.php:12` | – |
| `ezobjectrelationlistSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezobjectrelationlistsolrstorage.php:12` | – |
| `ezoptionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezoptionsolrstorage.php:12` | – |
| `ezpFileArchive` | abstract class | – | `extension/ezfind/classes/archive/filestorage/ezpfilearchive.php:11` | – |
| `ezpFileArchiveFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezfind/classes/archive/filestorage/ezpfilearchivefactory.php:12` | – |
| `ezpFileArchiveFileSystem` | class extends ezpFileArchive | – | `extension/ezfind/classes/archive/filestorage/ezpfilearchivefilesystem.php:12` | – |
| `ezpriceSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezpricesolrstorage.php:12` | – |
| `ezproductcategorySolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezproductcategorysolrstorage.php:12` | – |
| `ezrangeoptionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezrangeoptionsolrstorage.php:12` | – |
| `ezselectionSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezselectionsolrstorage.php:12` | – |
| `eZSolr` | class implements ezpSearchEngine | [Strategie / Handler](strategie-handler.md) | `extension/ezfind/search/plugins/ezsolr/ezsolr.php:11` | Solr-Such-Plugin für Exponential |
| `eZSolrBase` | class | – | `extension/ezfind/classes/ezsolrbase.php:15` | – |
| `eZSolrDoc` | class | – | `extension/ezfind/classes/ezsolrdoc.php:17` | Solr-Dokumentklasse. |
| `ezurlSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezurlsolrstorage.php:12` | – |
| `ezuserSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezusersolrstorage.php:12` | – |
| `ezxmltextSolrStorage` | class extends ezdatatypeSolrStorage | – | `extension/ezfind/classes/solrstorage/ezxmltextsolrstorage.php:12` | – |

## Modulansichten (3)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezfind/elevate` | `elevate.php` | elevate | `extension/ezfind/modules/ezfind/module.php:11` |
| `/ezfind/elevation_detail` | `elevation_detail.php` | elevate | `extension/ezfind/modules/ezfind/module.php:24` |
| `/ezfind/remove_elevation` | `remove_elevation.php` | elevate | `extension/ezfind/modules/ezfind/module.php:37` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `ezfind/elevate` | – | `extension/ezfind/modules/ezfind/module.php:46` |

## Fetch-Funktionen (7)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'ezfind', 'search' )` | `ezfModuleFunctionCollection::search()` | `extension/ezfind/modules/ezfind/function_definition.php:10` |
| `fetch( 'ezfind', 'getDefaultSearchFacets' )` | `ezfModuleFunctionCollection::getDefaultSearchFacets()` | `extension/ezfind/modules/ezfind/function_definition.php:112` |
| `fetch( 'ezfind', 'facetParameters' )` | `ezfModuleFunctionCollection::getFacetParameters()` | `extension/ezfind/modules/ezfind/function_definition.php:120` |
| `fetch( 'ezfind', 'filterParameters' )` | `ezfModuleFunctionCollection::getFilterParameters()` | `extension/ezfind/modules/ezfind/function_definition.php:128` |
| `fetch( 'ezfind', 'rawSolrRequest' )` | `ezfModuleFunctionCollection::rawSolrRequest()` | `extension/ezfind/modules/ezfind/function_definition.php:138` |
| `fetch( 'ezfind', 'moreLikeThis' )` | `ezfModuleFunctionCollection::moreLikeThis()` | `extension/ezfind/modules/ezfind/function_definition.php:156` |
| `fetch( 'ezfind', 'elevateConfiguration' )` | `ezfModuleFunctionCollection::fetchElevateConfiguration()` | `extension/ezfind/modules/ezfind/function_definition.php:211` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| solr_escape, solr_quotes_escape | `ezfTemplateOperators` | `extension/ezfind/autoloads/eztemplateautoload.php:12` |

## Cronjob-Teile (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezfindexsubtree` | `extension/ezfind/cronjobs/ezfindexsubtree.php` | – |
| `ezfoptimizeindex` | `extension/ezfind/cronjobs/ezfoptimizeindex.php` | – |

## INI-Plugin-Punkte (8)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SolrFieldMapSettings] CustomMap[dummy_example]` | `ezfSolrDocumentFieldDummyExample` | `extension/ezfind/settings/ezfind.ini:118` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelation]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:119` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelationlist]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:120` |
| `[SolrFieldMapSettings] CustomMap[ezxmltext]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:121` |
| `[SolrFieldMapSettings] CustomMap[ezmatrix]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:122` |
| `[ExtendedAttributeFilters] FiltersList[geodist]` | `eZFindGeoDistExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:337` |
| `[ExtendedAttributeFilters] FiltersList[ezfindallwords]` | `eZFindAllWordsExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:341` |
| `[ezjscServer_ezfind] Class` | `eZFindServerCallFunctions` | `extension/ezfind/settings/ezjscore.ini.append.php:4` |
