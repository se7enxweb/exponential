---
title: Muster in extension/explayouts
audience: developer
category: standards
order: 415
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts mit den Entwurfsmustern.
keywords: extension/explayouts, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (150)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsAbstractContentBlockHandler` | abstract class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/explayouts/classes/explayoutsabstractcontentblockhandler.php:3` | – |
| `expLayoutsAccordionBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsaccordionblockhandler.php:2` | – |
| `expLayoutsAdminLeftMenuBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsadminleftmenublockhandler.php:7` | Der linke Menüblock der Admin-Layouts. |
| `expLayoutsAdminPartBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsadminpartblockhandler.php:7` | Der Handler der Admin-Blöcke, die keinen Parameter nehmen (Design admin4l): der Block zeigt einen Teil der Admin-Seite, gezeichnet von seinem Ansichts-Template desig… |
| `expLayoutsAjaxLoadMoreServer` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer-Funktionen zum Laden seitenweiser Collection-Blöcke. |
| `expLayoutsAlertBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsalertblockhandler.php:2` | – |
| `expLayoutsBadgeBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsbadgeblockhandler.php:2` | – |
| `expLayoutsBlock` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutsblock.php:2` | – |
| `expLayoutsBlockHandlerFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – |
| `expLayoutsBlockHandlerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/explayouts/classes/explayoutsblockhandlerinterface.php:2` | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – |
| `expLayoutsButtonBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsbuttonblockhandler.php:2` | – |
| `expLayoutsCardBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutscardblockhandler.php:2` | – |
| `expLayoutsCarouselBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutscarouselblockhandler.php:2` | – |
| `expLayoutsChildrenQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutschildrenqueryhandler.php:2` | – |
| `expLayoutsCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutscollection.php:2` | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – |
| `expLayoutsComponentBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutscomponentblockhandler.php:2` | – |
| `expLayoutsContainerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutscontainerblockhandler.php:2` | – |
| `expLayoutsContent` | class | – | `extension/explayouts/classes/explayoutsvalues.php:9` | Exponential-4-Wrapper für Wertobjekte, kompatibel mit dem Muster Exp Site API Values. |
| `expLayoutsContentByTopicQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutscontentbytopicqueryhandler.php:3` | – |
| `expLayoutsContentComponentBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php:17` | Handler für die inhaltsgestützten Komponentenblöcke (hero, features, about, logos, quote, lead). |
| `expLayoutsDebugBarPanel` | class implements expDebugBarPanelInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/explayouts/classes/explayoutsdebugbarpanel.php:16` | Das Panel Exp Layouts der Exp-Debug-Leiste: welches Layout die Seite gebaut hat, welche Regel es gewählt hat, seine Zonen und Blöcke mit Links, um sie in der Adm… |
| `expLayoutsDividerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsdividerblockhandler.php:2` | – |
| `expLayoutsDynamicCollection` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsdynamiccollection.php:15` | Führt „dynamische“ Collections aus (aus der Nexus-Referenz importierte Collection-Abfragen). |
| `expLayoutsExpAdminUI` | class | – | `extension/explayouts/classes/explayoutsexpadminui.php:4` | – |
| `expLayoutsExpBlock` | class | – | `extension/explayouts/classes/explayoutsexpblock.php:4` | – |
| `expLayoutsExpCollection` | class | – | `extension/explayouts/classes/explayoutsexpcollection.php:4` | – |
| `expLayoutsExpContentProvider` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsexpcontentprovider.php:4` | – |
| `expLayoutsExpContext` | class | – | `extension/explayouts/classes/explayoutsexpcontext.php:4` | – |
| `expLayoutsExpForm` | class | – | `extension/explayouts/classes/explayoutsexpform.php:3` | – |
| `expLayoutsExpHttpCache` | class | – | `extension/explayouts/classes/explayoutsexphttpcache.php:4` | – |
| `expLayoutsExpItem` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts/classes/explayoutsexpitem.php:4` | – |
| `expLayoutsExpLayout` | class | – | `extension/explayouts/classes/explayoutsexplayout.php:4` | – |
| `expLayoutsExpLocale` | class | – | `extension/explayouts/classes/explayoutsexplocale.php:4` | – |
| `expLayoutsExponential` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexponential.php:4` | – |
| `expLayoutsExponentialContentSearchQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php:3` | – |
| `expLayoutsExporter` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexporter.php:7` | Der Export eines Layouts (Version 1). |
| `expLayoutsExpParameters` | class | – | `extension/explayouts/classes/explayoutsexpparameters.php:4` | – |
| `expLayoutsExpRelationListQuery` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexprelationlistquery.php:6` | Exponential-Layouts-Portierung des Upstream-Handlers RelationListQuery. |
| `expLayoutsExpReverseRelationListQuery` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexpreverserelationlistquery.php:6` | Exponential-Layouts-Portierung des Upstream-Handlers ReverseRelationListQuery. |
| `expLayoutsExpSearch` | class | – | `extension/explayouts/classes/explayoutsexpsearch.php:4` | – |
| `expLayoutsExpSecurity` | class | – | `extension/explayouts/classes/explayoutsexpsecurity.php:4` | – |
| `expLayoutsExpSiteApi` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – |
| `expLayoutsExpSiteApiContentProvider` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsexpsiteapicontentprovider.php:3` | – |
| `expLayoutsExpTagsQuery` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexptagsquery.php:6` | Exponential-Layouts-Portierung des Upstream-Handlers TagsQuery. |
| `expLayoutsExpUtils` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsexputils.php:4` | – |
| `expLayoutsExpValidator` | class | – | `extension/explayouts/classes/explayoutsexpvalidator.php:4` | – |
| `expLayoutsFeaturesBlockHandler` | class extends expLayoutsComponentBlockHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsfeaturesblockhandler.php:8` | Der Features-Block: die Felder der anderen Komponentenblöcke (Titel, Text, Bild, Link) und eine Liste von Features, eines je Zeile als „Title\|Text“, die… |
| `expLayoutsFixtures` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsfixtures.php:2` | – |
| `expLayoutsFullTextCriterion` | class | – | `extension/explayouts/classes/explayoutsfulltextcriterion.php:6` | Exponential-4-Portierung von Netgen SiteBundle API/Search/Criterion/FullText. |
| `expLayoutsFullViewBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsfullviewblockhandler.php:2` | – |
| `expLayoutsFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `extension/explayouts/modules/explayouts/functioncollection.php:4` | – |
| `expLayoutsGalleryBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsgalleryblockhandler.php:2` | – |
| `expLayoutsGridBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsgridblockhandler.php:2` | – |
| `expLayoutsHtmlBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutshtmlblockhandler.php:2` | – |
| `expLayoutsImageBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsimageblockhandler.php:2` | – |
| `expLayoutsImporter` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsimporter.php:11` | Importiert einen Layout-Export, den dieser Extension (expLayoutsExporter, „version“: 1, Parameter als Map aus Name und Wert) und den des Layou… |
| `explayoutsInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts/ezinfo.php:12` | – |
| `expLayoutsInstall` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsinstall.php:2` | – |
| `expLayoutsLanguageResolver` | class | [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Sprachauflöser für Exponential 4, kompatibel mit dem LanguageResolver-Muster der Exp Site API. |
| `expLayoutsLatestQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutslatestqueryhandler.php:2` | – |
| `expLayoutsLayout` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutslayout.php:2` | – |
| `expLayoutsLayoutType` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutslayouttype.php:2` | – |
| `expLayoutsLinkParameter` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutslinkparameter.php:23` | Der Link-Parameter, den die Blöcke teilen, die einen tragen können. |
| `expLayoutsListBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutslistblockhandler.php:2` | – |
| `expLayoutsLocation` | class | – | `extension/explayouts/classes/explayoutsvalues.php:150` | – |
| `expLayoutsMailHelper` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsmailhelper.php:6` | Exponential-4-Portierung von Helper/MailHelper aus dem Netgen SiteBundle. |
| `expLayoutsManualQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsmanualqueryhandler.php:2` | – |
| `expLayoutsMapBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsmapblockhandler.php:2` | – |
| `expLayoutsMarkdown` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsmarkdown.php:22` | Markdown des Markdown-Blocks, als HTML. |
| `expLayoutsMarkdownBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsmarkdownblockhandler.php:2` | – |
| `expLayoutsMongoInstaller` | class | – | `extension/explayouts/classes/explayoutsmongoinstaller.php:2` | – |
| `expLayoutsPager` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutspager.php:8` | Paginierungshelfer für Exponential 4, kompatibel mit den Pagerfanta-Adaptern der Exp Site API. |
| `expLayoutsParentQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsparentqueryhandler.php:2` | – |
| `expLayoutsPathHelper` | class | – | `extension/explayouts/classes/explayoutspathhelper.php:8` | Exponential-4-Portierung von Netgen SiteBundle PathHelper. |
| `expLayoutsPreviewSiteAccess` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutspreviewsiteaccess.php:25` | Die öffentliche Siteaccess, in der eine Administration Layouts darstellt: die Layout-Vorschau (explayouts_ui/layout_preview) und das Nachladen einer Listenblock-Liste, das von… |
| `expLayoutsProgressBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsprogressblockhandler.php:2` | – |
| `expLayoutsQueryHandlerFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsqueryhandlerfactory.php:2` | – |
| `expLayoutsQueryHandlerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/explayouts/classes/explayoutsqueryhandlerinterface.php:2` | – |
| `expLayoutsQueryType` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsquerytype.php:9` | Query-Builder für Exponential 4, kompatibel mit dem QueryType-Muster der Exp Site API. |
| `expLayoutsQuoteBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsquoteblockhandler.php:2` | – |
| `expLayoutsRandomQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsrandomqueryhandler.php:2` | – |
| `expLayoutsRedirectHelper` | class | – | `extension/explayouts/classes/explayoutsredirecthelper.php:9` | Exponential-4-Portierung von Netgen SiteBundle RedirectHelper. |
| `expLayoutsRelationListQueryHandler` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php:3` | – |
| `expLayoutsRenderer` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsrenderer.php:2` | – |
| `expLayoutsResolver` | class | [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsresolver.php:2` | – |
| `expLayoutsReverseRelationListQueryHandler` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – |
| `expLayoutsReverseRelationProvider` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsreverserelationprovider.php:15` | Die Layouts-Blöcke, die Inhalte nennen, die ein Entfernen oder Verbergen wegnimmt, für die Prüfung umgekehrter Relationen des Kernels (content.ini [ReverseRelationSettings] P… |
| `expLayoutsRichTextBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsrichtextblockhandler.php:2` | – |
| `expLayoutsRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutsrule.php:2` | – |
| `expLayoutsRuleCondition` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutsrulecondition.php:2` | – |
| `expLayoutsRuleTarget` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutsruletarget.php:2` | – |
| `expLayoutsSafeValue` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/explayouts/classes/explayoutssafevalue.php:22` | Die Werte eines Blocks, die in Markup gelangen, wo Escaping nicht ausreicht: ein Link oder eine Einbettungsadresse (\|wash lässt „javascript:“ weiter funktionieren), eine Farbe inner… |
| `expLayoutsSiblingsQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php:2` | – |
| `expLayoutsSingleBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutssingleblockhandler.php:3` | – |
| `expLayoutsSite` | class | [Singleton / instance()](singleton.md) | `extension/explayouts/classes/explayoutssite.php:9` | Site-Registry für Exponential 4, kompatibel mit der Site-Schnittstelle der Exp Site API. |
| `expLayoutsSiteAPI` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssiteapi.php:10` | Kompatibilitätsschicht Exponential 4 / Exponential Site API. |
| `expLayoutsSiteBundleCommand` | class | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlecommand.php:4` | – |
| `expLayoutsSiteBundleContentForms` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlecontentforms.php:4` | – |
| `expLayoutsSiteBundleContextProvider` | class | [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutssitebundlecontextprovider.php:4` | – |
| `expLayoutsSiteBundleController` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlecontroller.php:4` | – |
| `expLayoutsSiteBundleCore` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlecore.php:4` | – |
| `expLayoutsSiteBundleDataCollector` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundledatacollector.php:4` | – |
| `expLayoutsSiteBundleDebug` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundledebug.php:4` | – |
| `expLayoutsSiteBundleDependencyInjection` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundledependencyinjection.php:4` | – |
| `expLayoutsSiteBundleEntity` | class | – | `extension/explayouts/classes/explayoutssitebundleentity.php:4` | – |
| `expLayoutsSiteBundleEvent` | class | [Beobachter / Listener / Ereignis](beobachter.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundleevent.php:4` | – |
| `expLayoutsSiteBundleEventListener` | class | [Beobachter / Listener / Ereignis](beobachter.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundleeventlistener.php:4` | – |
| `expLayoutsSiteBundleImagine` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundleimagine.php:4` | – |
| `expLayoutsSiteBundleInfoCollection` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundleinfocollection.php:4` | – |
| `expLayoutsSiteBundleLayouts` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlelayouts.php:4` | – |
| `expLayoutsSiteBundleMenu` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlemenu.php:4` | – |
| `expLayoutsSiteBundleOpenGraph` | class | – | `extension/explayouts/classes/explayoutssitebundleopengraph.php:4` | – |
| `expLayoutsSiteBundlePagerfanta` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlepagerfanta.php:4` | – |
| `expLayoutsSiteBundleQueryType` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlequerytype.php:4` | – |
| `expLayoutsSiteBundleRelation` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlerelation.php:4` | – |
| `expLayoutsSiteBundleRichText` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlerichtext.php:4` | – |
| `expLayoutsSiteBundleSecurity` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundlesecurity.php:4` | – |
| `expLayoutsSiteBundleTemplating` | class | – | `extension/explayouts/classes/explayoutssitebundletemplating.php:4` | – |
| `expLayoutsSiteBundleTopic` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutssitebundletopic.php:4` | – |
| `expLayoutsSiteInstaller` | class | – | `extension/explayouts/classes/explayoutssiteinstaller.php:6` | Exponential-4-Portierung des Netgen Site Installer Bundle. |
| `expLayoutsSpacerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsspacerblockhandler.php:2` | – |
| `expLayoutsStandardDesignParameters` | class | – | `extension/explayouts/classes/expLayoutsStandardDesignParameters.php:3` | – |
| `expLayoutsSubtreeQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutssubtreequeryhandler.php:2` | – |
| `expLayoutsTabsBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutstabsblockhandler.php:2` | – |
| `expLayoutsTagsQueryHandler` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – |
| `expLayoutsTemplateEditor` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explayoutstemplateeditor.php:2` | – |
| `expLayoutsTextBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutstextblockhandler.php:2` | – |
| `expLayoutsTitleBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutstitleblockhandler.php:2` | – |
| `expLayoutsTplBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutstplblockhandler.php:2` | – |
| `expLayoutsVideoBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/explayouts/classes/explayoutsvideoblockhandler.php:2` | – |
| `expLayoutsZone` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/explayouts/classes/explayoutszone.php:2` | – |
| `ExplBlockDebug` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/event/explblockdebug.php:15` | Event-Listener für response/output, der ein Debug-Overlay der expl-Blöcke ausgibt, wenn die Seite mit ?expl_debug=1 angefordert wird. |
| `ExplBlockFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `extension/explayouts/classes/explblockfunction.php:9` | Template-Funktion {explblock name=... |
| `ExplBlockOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/explayouts/classes/explblockoperator.php:13` | Template-Operatoren für die {explblock}-Extraktion. |
| `ExplBlockParser` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts/classes/explblockparser.php:15` | ExplBlockParser — reiner Parser für {explblock}-Kommentarmarkierungen. |
| `ExplBlockXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/explayouts/classes/explblockxhtmlxmloutput.php:10` | Rich-Text-Ausgabe-Handler, der die Markierungssequenz expl: maskiert. |
| `Exponential\Command\Extension\Explayouts\Updatecomponentblockidentifiers` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/explayouts/classes/runnable/commands/php_updatecomponentblockidentifiers.php:33` | Der Code von extension/explayouts/bin/php/updatecomponentblockidentifiers.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Explayouts\Updatelinklocationreferences` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/explayouts/classes/runnable/commands/php_updatelinklocationreferences.php:41` | Der Code von extension/explayouts/bin/php/updatelinklocationreferences.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\BlockEdit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/block_edit.php:10` | Der Code von extension/explayouts/modules/explayouts/block_edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\Dashboard` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/dashboard.php:22` | Der Code von extension/explayouts/modules/explayouts/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutEdit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/layout_edit.php:77` | Der Code von extension/explayouts/modules/explayouts/layout_edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutList` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/layout_list.php:10` | Der Code von extension/explayouts/modules/explayouts/layout_list.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\Preview` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/preview.php:10` | Der Code von extension/explayouts/modules/explayouts/preview.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleEdit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/rule_edit.php:10` | Der Code von extension/explayouts/modules/explayouts/rule_edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleList` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/rule_list.php:10` | Der Code von extension/explayouts/modules/explayouts/rule_list.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\Setup` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/setup.php:10` | Der Code von extension/explayouts/modules/explayouts/setup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\TemplateEditor` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts/classes/runnable/views/explayouts/template_editor.php:27` | Der Code von extension/explayouts/modules/explayouts/template_editor.php, in eine Klasse verschoben (#207 Stufe 1). |

## Modulansichten (9)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts/dashboard` | `dashboard.php` | read | `extension/explayouts/modules/explayouts/module.php:7` |
| `/explayouts/setup` | `setup.php` | edit | `extension/explayouts/modules/explayouts/module.php:14` |
| `/explayouts/template_editor` | `template_editor.php` | read, edit | `extension/explayouts/modules/explayouts/module.php:21` |
| `/explayouts/layout_list` | `layout_list.php` | read | `extension/explayouts/modules/explayouts/module.php:28` |
| `/explayouts/layout_edit` | `layout_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:35` |
| `/explayouts/layout_preview` | `preview.php` | read | `extension/explayouts/modules/explayouts/module.php:42` |
| `/explayouts/block_edit` | `block_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:49` |
| `/explayouts/rule_list` | `rule_list.php` | read | `extension/explayouts/modules/explayouts/module.php:56` |
| `/explayouts/rule_edit` | `rule_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:63` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `explayouts/read` | – | `extension/explayouts/modules/explayouts/module.php:71` |
| `explayouts/edit` | – | `extension/explayouts/modules/explayouts/module.php:72` |

## Fetch-Funktionen (9)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'explayouts', 'layout' )` | `expLayoutsFunctionCollection::fetchLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:4` |
| `fetch( 'explayouts', 'resolve_layout' )` | `expLayoutsFunctionCollection::resolveLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:18` |
| `fetch( 'explayouts', 'resolve_route_layout' )` | `expLayoutsFunctionCollection::resolveRouteLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:33` |
| `fetch( 'explayouts', 'resolve_admin_layout' )` | `expLayoutsFunctionCollection::resolveAdminLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:45` |
| `fetch( 'explayouts', 'admin_layout_cache_key' )` | `expLayoutsFunctionCollection::adminLayoutCacheKey()` | `extension/explayouts/modules/explayouts/function_definition.php:60` |
| `fetch( 'explayouts', 'admin_layouts_enabled' )` | `expLayoutsFunctionCollection::adminLayoutsEnabled()` | `extension/explayouts/modules/explayouts/function_definition.php:75` |
| `fetch( 'explayouts', 'layout_summary_for_node' )` | `expLayoutsFunctionCollection::layoutSummaryForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:87` |
| `fetch( 'explayouts', 'resolve_layout_for_node' )` | `expLayoutsFunctionCollection::resolveLayoutForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:101` |
| `fetch( 'explayouts', 'rules_for_node' )` | `expLayoutsFunctionCollection::rulesForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:115` |

## INI-Plugin-Punkte (85)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ReverseRelationSettings] ProviderClassList[]` | `expLayoutsReverseRelationProvider` | `extension/explayouts/settings/content.ini.append.php:6` |
| `[Panel_explayouts] Class` | `expLayoutsDebugBarPanel` | `extension/explayouts/settings/debugbar.ini.append.php:10` |
| `[BlockDefinition_two_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:61` |
| `[BlockDefinition_column] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:77` |
| `[BlockDefinition_tpl_block] Handler` | `expLayoutsTplBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:85` |
| `[BlockDefinition_text] Handler` | `expLayoutsTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:92` |
| `[BlockDefinition_title] Handler` | `expLayoutsTitleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:99` |
| `[BlockDefinition_html] Handler` | `expLayoutsHtmlBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:107` |
| `[BlockDefinition_rich_text] Handler` | `expLayoutsRichTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:114` |
| `[BlockDefinition_image] Handler` | `expLayoutsImageBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:121` |
| `[BlockDefinition_list] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:130` |
| `[BlockDefinition_single] Handler` | `expLayoutsSingleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:148` |
| `[BlockDefinition_button] Handler` | `expLayoutsButtonBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:155` |
| `[BlockDefinition_spacer] Handler` | `expLayoutsSpacerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:165` |
| `[BlockDefinition_divider] Handler` | `expLayoutsDividerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:171` |
| `[BlockDefinition_card] Handler` | `expLayoutsCardBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:177` |
| `[BlockDefinition_accordion] Handler` | `expLayoutsAccordionBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:188` |
| `[BlockDefinition_tabs] Handler` | `expLayoutsTabsBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:195` |
| `[BlockDefinition_grid] Handler` | `expLayoutsGridBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:202` |
| `[BlockDefinition_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:209` |
| `[BlockDefinition_slider] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:231` |
| `[BlockDefinition_thumb_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:247` |
| `[BlockDefinition_grid_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:263` |
| `[BlockDefinition_sushi_bar] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:279` |
| `[BlockDefinition_list_zigzag] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:295` |
| `[BlockDefinition_list_accordion] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:302` |
| `[BlockDefinition_quote] Handler` | `expLayoutsQuoteBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:309` |
| `[BlockDefinition_alert] Handler` | `expLayoutsAlertBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:318` |
| `[BlockDefinition_video] Handler` | `expLayoutsVideoBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:325` |
| `[BlockDefinition_badge] Handler` | `expLayoutsBadgeBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:335` |
| `[BlockDefinition_progress] Handler` | `expLayoutsProgressBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:343` |
| `[BlockDefinition_map] Handler` | `expLayoutsMapBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:351` |
| `[BlockDefinition_carousel] Handler` | `expLayoutsCarouselBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:363` |
| `[BlockDefinition_markdown] Handler` | `expLayoutsMarkdownBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:370` |
| `[BlockDefinition_full_view] Handler` | `expLayoutsFullViewBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:377` |
| `[BlockDefinition_hero] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:386` |
| `[BlockDefinition_about] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:398` |
| `[BlockDefinition_features] Handler` | `expLayoutsFeaturesBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:410` |
| `[BlockDefinition_lead] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:423` |
| `[BlockDefinition_logos] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:435` |
| `[BlockDefinition_three_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:442` |
| `[BlockDefinition_four_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:460` |
| `[BlockDefinition_exp_component_hero] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:485` |
| `[BlockDefinition_exp_component_features] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:494` |
| `[BlockDefinition_exp_component_about] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:507` |
| `[BlockDefinition_exp_component_logos] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:518` |
| `[BlockDefinition_exp_component_quote] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:526` |
| `[BlockDefinition_exp_component_lead] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:533` |
| `[QueryType_children] Handler` | `expLayoutsChildrenQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:555` |
| `[QueryType_parent] Handler` | `expLayoutsParentQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:559` |
| `[QueryType_subtree] Handler` | `expLayoutsSubtreeQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:563` |
| `[QueryType_siblings] Handler` | `expLayoutsSiblingsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:567` |
| `[QueryType_latest] Handler` | `expLayoutsLatestQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:571` |
| `[QueryType_random] Handler` | `expLayoutsRandomQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:575` |
| `[QueryType_manual] Handler` | `expLayoutsManualQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:579` |
| `[QueryType_exp_content_relation_list] Handler` | `expLayoutsRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:583` |
| `[QueryType_exp_content_reverse_relation_list] Handler` | `expLayoutsReverseRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:587` |
| `[QueryType_exp_content_tags] Handler` | `expLayoutsTagsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:591` |
| `[QueryType_exponential_content_search] Handler` | `expLayoutsExponentialContentSearchQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:595` |
| `[QueryType_content_by_topic] Handler` | `expLayoutsContentByTopicQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:599` |
| `[BlockDefinition_admin_logo] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:632` |
| `[BlockDefinition_admin_search] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:639` |
| `[BlockDefinition_admin_theme_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:646` |
| `[BlockDefinition_admin_menu_button] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:653` |
| `[BlockDefinition_admin_sidebar_controls] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:660` |
| `[BlockDefinition_admin_sidebar_toggles] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:669` |
| `[BlockDefinition_admin_tab_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:676` |
| `[BlockDefinition_admin_left_menu] Handler` | `expLayoutsAdminLeftMenuBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:683` |
| `[BlockDefinition_admin_content_tree] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:690` |
| `[BlockDefinition_admin_clear_cache] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:697` |
| `[BlockDefinition_admin_bookmarks] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:704` |
| `[BlockDefinition_admin_ui_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:711` |
| `[BlockDefinition_admin_current_user] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:718` |
| `[BlockDefinition_admin_preferences] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:725` |
| `[BlockDefinition_admin_quick_settings] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:732` |
| `[BlockDefinition_admin_toolbar_right] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:739` |
| `[BlockDefinition_admin_toolbar_developer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:746` |
| `[BlockDefinition_admin_breadcrumb] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:753` |
| `[BlockDefinition_admin_module_result] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:760` |
| `[BlockDefinition_admin_footer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:767` |
| `[BlockDefinition_admin_popup_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:774` |
| `[BlockDefinition_admin_overlay] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:781` |
| `[BlockDefinition_admin_debug_area] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:788` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/explayouts/settings/ezjscore.ini.append.php:7` |
| `[OutputSettings] HandlerClass` | `ExplBlockXHTMLXMLOutput` | `extension/explayouts/settings/ezxml.ini.append.php:4` |
