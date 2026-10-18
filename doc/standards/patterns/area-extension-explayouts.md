---
title: Patterns in extension/explayouts
audience: developer
category: standards
order: 412
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts with its design patterns.
keywords: extension/explayouts, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (146)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsAbstractContentBlockHandler` | abstract class implements expLayoutsBlockHandlerInterface | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/explayouts/classes/explayoutsabstractcontentblockhandler.php:3` | – |
| `expLayoutsAccordionBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsaccordionblockhandler.php:2` | – |
| `expLayoutsAdminLeftMenuBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsadminleftmenublockhandler.php:7` | The left menu block of the admin layouts. |
| `expLayoutsAdminPartBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsadminpartblockhandler.php:7` | The handler of the admin blocks that take no parameter (design admin4l): the block shows one part of the admin page, drawn by its view template desig… |
| `expLayoutsAjaxLoadMoreServer` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer functions for loading paged collection blocks. |
| `expLayoutsAlertBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsalertblockhandler.php:2` | – |
| `expLayoutsBadgeBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsbadgeblockhandler.php:2` | – |
| `expLayoutsBlock` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutsblock.php:2` | – |
| `expLayoutsBlockHandlerFactory` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – |
| `expLayoutsBlockHandlerInterface` | interface | [Interface (contract)](interface.md) | `extension/explayouts/classes/explayoutsblockhandlerinterface.php:2` | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – |
| `expLayoutsButtonBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsbuttonblockhandler.php:2` | – |
| `expLayoutsCardBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutscardblockhandler.php:2` | – |
| `expLayoutsCarouselBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutscarouselblockhandler.php:2` | – |
| `expLayoutsChildrenQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutschildrenqueryhandler.php:2` | – |
| `expLayoutsCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutscollection.php:2` | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – |
| `expLayoutsComponentBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutscomponentblockhandler.php:2` | – |
| `expLayoutsContainerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutscontainerblockhandler.php:2` | – |
| `expLayoutsContent` | class | – | `extension/explayouts/classes/explayoutsvalues.php:9` | Exponential 4 value object wrappers compatible with the Exp Site API Values pattern. |
| `expLayoutsContentByTopicQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutscontentbytopicqueryhandler.php:3` | – |
| `expLayoutsContentComponentBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php:17` | Handler for the content-backed component blocks (hero, features, about, logos, quote, lead). |
| `expLayoutsDebugBarPanel` | class implements expDebugBarPanelInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/explayouts/classes/explayoutsdebugbarpanel.php:16` | The Exp Layouts panel of the Exp Debug bar: which layout built the page, which rule chose it, its zones and blocks with links to edit them in the adm… |
| `expLayoutsDividerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsdividerblockhandler.php:2` | – |
| `expLayoutsDynamicCollection` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsdynamiccollection.php:15` | Executes 'dynamic' collections (collection queries imported from Netgen Layouts). |
| `expLayoutsExpAdminUI` | class | – | `extension/explayouts/classes/explayoutsexpadminui.php:4` | – |
| `expLayoutsExpBlock` | class | – | `extension/explayouts/classes/explayoutsexpblock.php:4` | – |
| `expLayoutsExpCollection` | class | – | `extension/explayouts/classes/explayoutsexpcollection.php:4` | – |
| `expLayoutsExpContentProvider` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsexpcontentprovider.php:4` | – |
| `expLayoutsExpContext` | class | – | `extension/explayouts/classes/explayoutsexpcontext.php:4` | – |
| `expLayoutsExpForm` | class | – | `extension/explayouts/classes/explayoutsexpform.php:3` | – |
| `expLayoutsExpHttpCache` | class | – | `extension/explayouts/classes/explayoutsexphttpcache.php:4` | – |
| `expLayoutsExpItem` | class | [Value object / struct](value-object.md) | `extension/explayouts/classes/explayoutsexpitem.php:4` | – |
| `expLayoutsExpLayout` | class | – | `extension/explayouts/classes/explayoutsexplayout.php:4` | – |
| `expLayoutsExpLocale` | class | – | `extension/explayouts/classes/explayoutsexplocale.php:4` | – |
| `expLayoutsExponential` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexponential.php:4` | – |
| `expLayoutsExponentialContentSearchQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php:3` | – |
| `expLayoutsExponentialorter` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexporter.php:2` | – |
| `expLayoutsExpParameters` | class | – | `extension/explayouts/classes/explayoutsexpparameters.php:4` | – |
| `expLayoutsExpRelationListQuery` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexprelationlistquery.php:6` | Exponential 4 port of Netgen Layouts Exp RelationListQuery handler. |
| `expLayoutsExpReverseRelationListQuery` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexpreverserelationlistquery.php:6` | Exponential 4 port of Netgen Layouts Exp ReverseRelationListQuery handler. |
| `expLayoutsExpSearch` | class | – | `extension/explayouts/classes/explayoutsexpsearch.php:4` | – |
| `expLayoutsExpSecurity` | class | – | `extension/explayouts/classes/explayoutsexpsecurity.php:4` | – |
| `expLayoutsExpSiteApi` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – |
| `expLayoutsExpSiteApiContentProvider` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsexpsiteapicontentprovider.php:3` | – |
| `expLayoutsExpTagsQuery` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexptagsquery.php:6` | Exponential 4 port of Netgen Layouts Exp TagsQuery handler. |
| `expLayoutsExpUtils` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsexputils.php:4` | – |
| `expLayoutsExpValidator` | class | – | `extension/explayouts/classes/explayoutsexpvalidator.php:4` | – |
| `expLayoutsFixtures` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsfixtures.php:2` | – |
| `expLayoutsFullTextCriterion` | class | – | `extension/explayouts/classes/explayoutsfulltextcriterion.php:6` | Exponential 4 port of Netgen SiteBundle API/Search/Criterion/FullText. |
| `expLayoutsFullViewBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsfullviewblockhandler.php:2` | – |
| `expLayoutsFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/explayouts/modules/explayouts/functioncollection.php:4` | – |
| `expLayoutsGalleryBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsgalleryblockhandler.php:2` | – |
| `expLayoutsGridBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsgridblockhandler.php:2` | – |
| `expLayoutsHtmlBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutshtmlblockhandler.php:2` | – |
| `expLayoutsImageBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsimageblockhandler.php:2` | – |
| `expLayoutsImporter` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsimporter.php:2` | – |
| `explayoutsInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts/ezinfo.php:12` | – |
| `expLayoutsInstall` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsinstall.php:2` | – |
| `expLayoutsLanguageResolver` | class | [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Exponential 4 language resolver compatible with the Exp Site API LanguageResolver pattern. |
| `expLayoutsLatestQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutslatestqueryhandler.php:2` | – |
| `expLayoutsLayout` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutslayout.php:2` | – |
| `expLayoutsLayoutType` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutslayouttype.php:2` | – |
| `expLayoutsLinkParameter` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutslinkparameter.php:23` | The link parameter shared by the blocks that can carry one. |
| `expLayoutsListBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutslistblockhandler.php:2` | – |
| `expLayoutsLocation` | class | – | `extension/explayouts/classes/explayoutsvalues.php:150` | – |
| `expLayoutsMailHelper` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsmailhelper.php:6` | Exponential 4 port of Netgen SiteBundle Helper/MailHelper. |
| `expLayoutsManualQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsmanualqueryhandler.php:2` | – |
| `expLayoutsMapBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsmapblockhandler.php:2` | – |
| `expLayoutsMarkdownBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsmarkdownblockhandler.php:2` | – |
| `expLayoutsMongoInstaller` | class | – | `extension/explayouts/classes/explayoutsmongoinstaller.php:2` | – |
| `expLayoutsPager` | class | [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutspager.php:8` | Exponential 4 pagination helper compatible with the Exp Site API Pagerfanta adapters. |
| `expLayoutsParentQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsparentqueryhandler.php:2` | – |
| `expLayoutsPathHelper` | class | – | `extension/explayouts/classes/explayoutspathhelper.php:8` | Exponential 4 port of Netgen SiteBundle PathHelper. |
| `expLayoutsProgressBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsprogressblockhandler.php:2` | – |
| `expLayoutsQueryHandlerFactory` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsqueryhandlerfactory.php:2` | – |
| `expLayoutsQueryHandlerInterface` | interface | [Interface (contract)](interface.md) | `extension/explayouts/classes/explayoutsqueryhandlerinterface.php:2` | – |
| `expLayoutsQueryType` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsquerytype.php:9` | Exponential 4 query builders compatible with the Exp Site API QueryType pattern. |
| `expLayoutsQuoteBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsquoteblockhandler.php:2` | – |
| `expLayoutsRandomQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsrandomqueryhandler.php:2` | – |
| `expLayoutsRedirectHelper` | class | – | `extension/explayouts/classes/explayoutsredirecthelper.php:9` | Exponential 4 port of Netgen SiteBundle RedirectHelper. |
| `expLayoutsRelationListQueryHandler` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php:3` | – |
| `expLayoutsRenderer` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsrenderer.php:2` | – |
| `expLayoutsResolver` | class | [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsresolver.php:2` | – |
| `expLayoutsReverseRelationListQueryHandler` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – |
| `expLayoutsReverseRelationProvider` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsreverserelationprovider.php:15` | The Layouts blocks that name content a removal or a hide takes away, for the kernel's reverse relation check (content.ini [ReverseRelationSettings] P… |
| `expLayoutsRichTextBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsrichtextblockhandler.php:2` | – |
| `expLayoutsRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutsrule.php:2` | – |
| `expLayoutsRuleCondition` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutsrulecondition.php:2` | – |
| `expLayoutsRuleTarget` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutsruletarget.php:2` | – |
| `expLayoutsSiblingsQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php:2` | – |
| `expLayoutsSingleBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutssingleblockhandler.php:3` | – |
| `expLayoutsSite` | class | [Singleton / instance()](singleton.md) | `extension/explayouts/classes/explayoutssite.php:9` | Exponential 4 Site registry compatible with the Exp Site API Site interface. |
| `expLayoutsSiteAPI` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssiteapi.php:10` | Exponential 4 / Exponential Site API compatibility layer. |
| `expLayoutsSiteBundleCommand` | class | [Command (console, scripts, cronjobs)](command.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlecommand.php:4` | – |
| `expLayoutsSiteBundleContentForms` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlecontentforms.php:4` | – |
| `expLayoutsSiteBundleContextProvider` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutssitebundlecontextprovider.php:4` | – |
| `expLayoutsSiteBundleController` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlecontroller.php:4` | – |
| `expLayoutsSiteBundleCore` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlecore.php:4` | – |
| `expLayoutsSiteBundleDataCollector` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundledatacollector.php:4` | – |
| `expLayoutsSiteBundleDebug` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundledebug.php:4` | – |
| `expLayoutsSiteBundleDependencyInjection` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundledependencyinjection.php:4` | – |
| `expLayoutsSiteBundleEntity` | class | – | `extension/explayouts/classes/explayoutssitebundleentity.php:4` | – |
| `expLayoutsSiteBundleEvent` | class | [Observer / listener / event](observer.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundleevent.php:4` | – |
| `expLayoutsSiteBundleEventListener` | class | [Observer / listener / event](observer.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundleeventlistener.php:4` | – |
| `expLayoutsSiteBundleImagine` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundleimagine.php:4` | – |
| `expLayoutsSiteBundleInfoCollection` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundleinfocollection.php:4` | – |
| `expLayoutsSiteBundleLayouts` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlelayouts.php:4` | – |
| `expLayoutsSiteBundleMenu` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlemenu.php:4` | – |
| `expLayoutsSiteBundleOpenGraph` | class | – | `extension/explayouts/classes/explayoutssitebundleopengraph.php:4` | – |
| `expLayoutsSiteBundlePagerfanta` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlepagerfanta.php:4` | – |
| `expLayoutsSiteBundleQueryType` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlequerytype.php:4` | – |
| `expLayoutsSiteBundleRelation` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlerelation.php:4` | – |
| `expLayoutsSiteBundleRichText` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlerichtext.php:4` | – |
| `expLayoutsSiteBundleSecurity` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundlesecurity.php:4` | – |
| `expLayoutsSiteBundleTemplating` | class | – | `extension/explayouts/classes/explayoutssitebundletemplating.php:4` | – |
| `expLayoutsSiteBundleTopic` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutssitebundletopic.php:4` | – |
| `expLayoutsSiteInstaller` | class | – | `extension/explayouts/classes/explayoutssiteinstaller.php:6` | Exponential 4 port of Netgen Site Installer Bundle. |
| `expLayoutsSpacerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsspacerblockhandler.php:2` | – |
| `expLayoutsStandardDesignParameters` | class | – | `extension/explayouts/classes/expLayoutsStandardDesignParameters.php:3` | – |
| `expLayoutsSubtreeQueryHandler` | class implements expLayoutsQueryHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutssubtreequeryhandler.php:2` | – |
| `expLayoutsTabsBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutstabsblockhandler.php:2` | – |
| `expLayoutsTagsQueryHandler` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – |
| `expLayoutsTemplateEditor` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explayoutstemplateeditor.php:2` | – |
| `expLayoutsTextBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutstextblockhandler.php:2` | – |
| `expLayoutsTitleBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutstitleblockhandler.php:2` | – |
| `expLayoutsTplBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutstplblockhandler.php:2` | – |
| `expLayoutsVideoBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/explayouts/classes/explayoutsvideoblockhandler.php:2` | – |
| `expLayoutsZone` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/explayouts/classes/explayoutszone.php:2` | – |
| `ExplBlockDebug` | class | [Utility (static helper)](utility.md) | `extension/explayouts/event/explblockdebug.php:15` | response/output event listener that renders an expl block debug overlay when the page is requested with ?expl_debug=1. |
| `ExplBlockFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `extension/explayouts/classes/explblockfunction.php:9` | Template function {explblock name=... |
| `ExplBlockOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/explayouts/classes/explblockoperator.php:13` | Template operators for {explblock} extraction. |
| `ExplBlockParser` | class | [Utility (static helper)](utility.md) | `extension/explayouts/classes/explblockparser.php:15` | ExplBlockParser — pure parser for {explblock} comment markers. |
| `ExplBlockXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/explayouts/classes/explblockxhtmlxmloutput.php:10` | Rich text output handler that escapes the expl: marker sequence. |
| `Exponential\Command\Extension\Explayouts\Updatecomponentblockidentifiers` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/explayouts/classes/runnable/commands/php_updatecomponentblockidentifiers.php:33` | The code of extension/explayouts/bin/php/updatecomponentblockidentifiers.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Explayouts\Updatelinklocationreferences` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/explayouts/classes/runnable/commands/php_updatelinklocationreferences.php:41` | The code of extension/explayouts/bin/php/updatelinklocationreferences.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\BlockEdit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/block_edit.php:10` | The code of extension/explayouts/modules/explayouts/block_edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\Dashboard` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/dashboard.php:22` | The code of extension/explayouts/modules/explayouts/dashboard.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutEdit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/layout_edit.php:77` | The code of extension/explayouts/modules/explayouts/layout_edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutList` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/layout_list.php:10` | The code of extension/explayouts/modules/explayouts/layout_list.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\Preview` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/preview.php:10` | The code of extension/explayouts/modules/explayouts/preview.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleEdit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/rule_edit.php:10` | The code of extension/explayouts/modules/explayouts/rule_edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleList` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/rule_list.php:10` | The code of extension/explayouts/modules/explayouts/rule_list.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\Setup` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/setup.php:10` | The code of extension/explayouts/modules/explayouts/setup.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Explayouts\Explayouts\TemplateEditor` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts/classes/runnable/views/explayouts/template_editor.php:27` | The code of extension/explayouts/modules/explayouts/template_editor.php, moved into a class (#207 stage 1). |

## Module views (9)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts/dashboard` | `dashboard.php` | read | `extension/explayouts/modules/explayouts/module.php:7` |
| `/explayouts/setup` | `setup.php` | edit | `extension/explayouts/modules/explayouts/module.php:14` |
| `/explayouts/template_editor` | `template_editor.php` | read, edit | `extension/explayouts/modules/explayouts/module.php:21` |
| `/explayouts/layout_list` | `layout_list.php` | read | `extension/explayouts/modules/explayouts/module.php:28` |
| `/explayouts/layout_edit` | `layout_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:35` |
| `/explayouts/layout_preview` | `preview.php` | read | `extension/explayouts/modules/explayouts/module.php:42` |
| `/explayouts/block_edit` | `block_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:49` |
| `/explayouts/rule_list` | `rule_list.php` | read | `extension/explayouts/modules/explayouts/module.php:56` |
| `/explayouts/rule_edit` | `rule_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:63` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts/read` | – | `extension/explayouts/modules/explayouts/module.php:71` |
| `explayouts/edit` | – | `extension/explayouts/modules/explayouts/module.php:72` |

## Fetch functions (8)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'explayouts', 'layout' )` | `expLayoutsFunctionCollection::fetchLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:4` |
| `fetch( 'explayouts', 'resolve_layout' )` | `expLayoutsFunctionCollection::resolveLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:18` |
| `fetch( 'explayouts', 'resolve_admin_layout' )` | `expLayoutsFunctionCollection::resolveAdminLayout()` | `extension/explayouts/modules/explayouts/function_definition.php:32` |
| `fetch( 'explayouts', 'admin_layout_cache_key' )` | `expLayoutsFunctionCollection::adminLayoutCacheKey()` | `extension/explayouts/modules/explayouts/function_definition.php:47` |
| `fetch( 'explayouts', 'admin_layouts_enabled' )` | `expLayoutsFunctionCollection::adminLayoutsEnabled()` | `extension/explayouts/modules/explayouts/function_definition.php:62` |
| `fetch( 'explayouts', 'layout_summary_for_node' )` | `expLayoutsFunctionCollection::layoutSummaryForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:74` |
| `fetch( 'explayouts', 'resolve_layout_for_node' )` | `expLayoutsFunctionCollection::resolveLayoutForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:88` |
| `fetch( 'explayouts', 'rules_for_node' )` | `expLayoutsFunctionCollection::rulesForNode()` | `extension/explayouts/modules/explayouts/function_definition.php:102` |

## INI plugin points (85)

| Setting | Class | Set in |
|---|---|---|
| `[ReverseRelationSettings] ProviderClassList[]` | `expLayoutsReverseRelationProvider` | `extension/explayouts/settings/content.ini.append.php:6` |
| `[Panel_explayouts] Class` | `expLayoutsDebugBarPanel` | `extension/explayouts/settings/debugbar.ini.append.php:10` |
| `[BlockDefinition_two_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:48` |
| `[BlockDefinition_column] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:58` |
| `[BlockDefinition_tpl_block] Handler` | `expLayoutsTplBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:66` |
| `[BlockDefinition_text] Handler` | `expLayoutsTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:72` |
| `[BlockDefinition_title] Handler` | `expLayoutsTitleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:78` |
| `[BlockDefinition_html] Handler` | `expLayoutsHtmlBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:84` |
| `[BlockDefinition_rich_text] Handler` | `expLayoutsRichTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:90` |
| `[BlockDefinition_image] Handler` | `expLayoutsImageBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:96` |
| `[BlockDefinition_list] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:102` |
| `[BlockDefinition_single] Handler` | `expLayoutsSingleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:114` |
| `[BlockDefinition_button] Handler` | `expLayoutsButtonBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:120` |
| `[BlockDefinition_spacer] Handler` | `expLayoutsSpacerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:126` |
| `[BlockDefinition_divider] Handler` | `expLayoutsDividerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:132` |
| `[BlockDefinition_card] Handler` | `expLayoutsCardBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:138` |
| `[BlockDefinition_accordion] Handler` | `expLayoutsAccordionBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:144` |
| `[BlockDefinition_tabs] Handler` | `expLayoutsTabsBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:150` |
| `[BlockDefinition_grid] Handler` | `expLayoutsGridBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:156` |
| `[BlockDefinition_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:163` |
| `[BlockDefinition_slider] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:170` |
| `[BlockDefinition_thumb_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:177` |
| `[BlockDefinition_grid_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:184` |
| `[BlockDefinition_sushi_bar] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:191` |
| `[BlockDefinition_list_zigzag] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:198` |
| `[BlockDefinition_list_accordion] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:205` |
| `[BlockDefinition_quote] Handler` | `expLayoutsQuoteBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:212` |
| `[BlockDefinition_alert] Handler` | `expLayoutsAlertBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:218` |
| `[BlockDefinition_video] Handler` | `expLayoutsVideoBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:224` |
| `[BlockDefinition_badge] Handler` | `expLayoutsBadgeBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:230` |
| `[BlockDefinition_progress] Handler` | `expLayoutsProgressBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:236` |
| `[BlockDefinition_map] Handler` | `expLayoutsMapBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:242` |
| `[BlockDefinition_carousel] Handler` | `expLayoutsCarouselBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:248` |
| `[BlockDefinition_markdown] Handler` | `expLayoutsMarkdownBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:255` |
| `[BlockDefinition_full_view] Handler` | `expLayoutsFullViewBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:261` |
| `[BlockDefinition_hero] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:270` |
| `[BlockDefinition_about] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:276` |
| `[BlockDefinition_features] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:282` |
| `[BlockDefinition_lead] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:288` |
| `[BlockDefinition_logos] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:294` |
| `[BlockDefinition_three_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:301` |
| `[BlockDefinition_four_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:311` |
| `[BlockDefinition_exp_component_hero] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:335` |
| `[BlockDefinition_exp_component_features] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:343` |
| `[BlockDefinition_exp_component_about] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:355` |
| `[BlockDefinition_exp_component_logos] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:365` |
| `[BlockDefinition_exp_component_quote] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:372` |
| `[BlockDefinition_exp_component_lead] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:378` |
| `[QueryType_children] Handler` | `expLayoutsChildrenQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:400` |
| `[QueryType_parent] Handler` | `expLayoutsParentQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:404` |
| `[QueryType_subtree] Handler` | `expLayoutsSubtreeQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:408` |
| `[QueryType_siblings] Handler` | `expLayoutsSiblingsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:412` |
| `[QueryType_latest] Handler` | `expLayoutsLatestQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:416` |
| `[QueryType_random] Handler` | `expLayoutsRandomQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:420` |
| `[QueryType_manual] Handler` | `expLayoutsManualQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:424` |
| `[QueryType_exp_content_relation_list] Handler` | `expLayoutsRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:428` |
| `[QueryType_exp_content_reverse_relation_list] Handler` | `expLayoutsReverseRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:432` |
| `[QueryType_exp_content_tags] Handler` | `expLayoutsTagsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:436` |
| `[QueryType_exponential_content_search] Handler` | `expLayoutsExponentialContentSearchQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:440` |
| `[QueryType_content_by_topic] Handler` | `expLayoutsContentByTopicQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:444` |
| `[BlockDefinition_admin_logo] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:478` |
| `[BlockDefinition_admin_search] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:485` |
| `[BlockDefinition_admin_theme_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:492` |
| `[BlockDefinition_admin_menu_button] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:499` |
| `[BlockDefinition_admin_sidebar_controls] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:506` |
| `[BlockDefinition_admin_sidebar_toggles] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:515` |
| `[BlockDefinition_admin_tab_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:522` |
| `[BlockDefinition_admin_left_menu] Handler` | `expLayoutsAdminLeftMenuBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:529` |
| `[BlockDefinition_admin_content_tree] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:536` |
| `[BlockDefinition_admin_clear_cache] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:543` |
| `[BlockDefinition_admin_bookmarks] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:550` |
| `[BlockDefinition_admin_ui_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:557` |
| `[BlockDefinition_admin_current_user] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:564` |
| `[BlockDefinition_admin_preferences] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:571` |
| `[BlockDefinition_admin_quick_settings] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:578` |
| `[BlockDefinition_admin_toolbar_right] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:585` |
| `[BlockDefinition_admin_toolbar_developer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:592` |
| `[BlockDefinition_admin_breadcrumb] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:599` |
| `[BlockDefinition_admin_module_result] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:606` |
| `[BlockDefinition_admin_footer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:613` |
| `[BlockDefinition_admin_popup_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:620` |
| `[BlockDefinition_admin_overlay] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:627` |
| `[BlockDefinition_admin_debug_area] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:634` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/explayouts/settings/ezjscore.ini.append.php:7` |
| `[OutputSettings] HandlerClass` | `ExplBlockXHTMLXMLOutput` | `extension/explayouts/settings/ezxml.ini.append.php:4` |
