---
title: Factory and static factory methods
audience: developer
category: standards
order: 321
summary: A class or static method that creates objects for the caller, so the caller does not name the concrete class. 352 instances in Exponential.
keywords: design pattern, factory, creation, pattern catalogue
type: catalogue
---
# Factory and static factory methods

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A class or static method that creates objects for the caller, so the caller does not name the concrete class.

| | |
|---|---|
| Group | Creation |
| Instances | 352 |
| Recognized by | Class name ends in Factory, or has static create*(), from*(), factory(), make*() methods, or creates new self/new static in a static method. |
| Extension point | Often the INI setting that names the class to create (see the INI plugin points). |

## Classes by area

### extension/autorss (1)

[All patterns of extension/autorss](area-extension-autorss.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AutoRSSType` | class extends eZWorkflowEventType | `extension/autorss/eventtypes/event/autorss/autorsstype.php:27` | – | – |

### extension/cjw_newsletter (37)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterAbTest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtest.php:20` | The A/B subject test of a send: samples, criterion, wait time, winner. | – |
| `CjwNewsletterAbTester` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtester.php:27` | The A/B subject test of a send (cjw_newsletter 4.2.0, area N4 Statistics). | – |
| `CjwNewsletterAbVariant` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabvariant.php:20` | One subject variant of an A/B test and its counts. | – |
| `CjwNewsletterApproval` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapproval.php:20` | The approval state of an edition version for a list that requires one (collaboration inbox). | – |
| `CjwNewsletterApprovalFlow` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapprovalflow.php:23` | The optional approval of editions before they are sent, per list (cjwnl_list.approval_required). | – |
| `CjwNewsletterArticlePool` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterarticlepool.php:20` | Where articles for editions come from (nodes, classes, sections, tags, states, age), per list or the global default. | – |
| `CjwNewsletterBlacklistItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterblacklistitem.php:17` | Handle Blacklist of NL users or emails | – |
| `CjwNewsletterClassInstaller` | class | `extension/cjw_newsletter/classes/cjwnewsletterclassinstaller.php:18` | Installs the newsletter content classes from the packages shipped in extension/cjw_newsletter/packages into one content class group. | – |
| `CjwNewsletterEditionArticle` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionarticle.php:20` | An article taken into an edition from a pool, by an editor or by the auto-fill. | – |
| `CjwNewsletterEditionSend` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsend.php:17` | Class description here | – |
| `CjwNewsletterEditionSendItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsenditem.php:20` | Mailqueue | – |
| `CjwNewsletterEditionSendOutput` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewslettereditionsendoutput.php:20` | The rendered output of a send in one language (one edition, many translations). | – |
| `CjwNewsletterEznewsletterSource` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettereznewslettersource.php:21` | Read-only access to the tables of an old eznewsletter installation, for ext:cjw_newsletter:import-eznewsletter. | – |
| `CjwNewsletterImport` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterimport.php:17` | Handle Import sets of Subscriptions | – |
| `CjwNewsletterImportMapping` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportmapping.php:20` | A stored column mapping of a CSV import (column -› field), delimiter, header, consent source. | – |
| `CjwNewsletterInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterest.php:20` | An interest subscribers can pick (a topic of a list, or an eztags tag). | – |
| `CjwNewsletterLink` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlink.php:20` | A link of a send that the click redirect knows (only stored URLs are redirected to). | – |
| `CjwNewsletterLinkClick` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlinkclick.php:20` | One click on a link; per person (send item) only with consent, else the item is 0. | – |
| `CjwNewsletterMailinAddress` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinaddress.php:20` | An address (or a plus-address tag) of a list that takes subscribe and unsubscribe mails. | – |
| `CjwNewsletterMailinMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinmessage.php:20` | One mail that came in at a mail-in address, and what was done with it. | – |
| `CjwNewsletterMigrationLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewslettermigrationlog.php:20` | One row the eznewsletter importer read, and what it did with it (or would do, in a dry run). | – |
| `CjwNewsletterOpen` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletteropen.php:20` | One open of a send; per person only with consent. | – |
| `CjwNewsletterRichText` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrichtext.php:21` | Rich text in the DocBook format of ezrichtext (sections, para, emphasis, link, title, lists, tables, literallayout, programlisting, blockquote, ezemb… | – |
| `CjwNewsletterSchedule` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedule.php:20` | A recurring send of a list: copy a template edition or send the latest edition, when, auto-fill from a pool. | – |
| `CjwNewsletterScheduleLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulelog.php:20` | One run of a schedule: sent, skipped (empty, condition) or failed. | – |
| `CjwNewsletterSendBatch` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersendbatch.php:20` | One batch of a send: which items it took, how far it got (resumable), its counts. | – |
| `CjwNewsletterSms` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersms.php:25` | The SMS channel of cjw_newsletter: settings, the transports, phone numbers (E.164), the confirmation code (the double opt-in of a number), the STOP k… | – |
| `CjwNewsletterSmsCode` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscode.php:20` | A confirmation code sent by SMS (stored as a hash), with attempts and expiry. | – |
| `CjwNewsletterSmsInbound` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsinbound.php:20` | One SMS that came in (a STOP keyword, a code), and what was done with it. | – |
| `CjwNewsletterSmsMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsmessage.php:20` | One SMS of an SMS edition to one newsletter user. | – |
| `CjwNewsletterStatTotal` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstattotal.php:20` | Anonymous totals per send, link, type and day; kept after the per-person rows are removed. | – |
| `CjwNewsletterSubscription` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettersubscription.php:20` | Class description here | – |
| `CjwNewsletterSubscriptionVirtual` | class extends CjwNewsletterSubscription | `extension/cjw_newsletter/classes/cjwnewslettersubscriptionvirtual.php:21` | CjwNewsletterSubscriptionVirtual handels spezific function which are only for virtual newsletter lists | – |
| `CjwNewsletterTestGroup` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettertestgroup.php:20` | A named group of test addresses for test sends of a list (or of every list). | – |
| `CjwNewsletterThrottleState` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterthrottlestate.php:20` | The sending rate of one transport in the current minute or hour window, and a pause. | – |
| `CjwNewsletterUser` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletteruser.php:20` | Data management datatyp cjwnewsletterlist | – |
| `CjwNewsletterUserInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletteruserinterest.php:20` | An interest a newsletter user picked. | – |

### extension/cjw_themes_jumper (2)

[All patterns of extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwJumperImagineImageFactory` | class extends eZImageFactory | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagefactory.php:10` | Image handler factory of the converter cjwImagine (image.ini [cjwImagine] Handler=cjwJumperImagineImageFactory): produces cjwJumperImagineImageHandle… | extension/cjw_themes_jumper/settings/image.ini [cjwImagine] Handler |
| `cjwJumperImagineImageHandler` | class extends eZImageHandler | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagehandler.php:28` | Image converter for the cjw_* aliases of the design starter: draws them exactly the way the Nexus v2 starter draws its own aliases (Imagine 1.x with… | – |

### extension/cjw_tmv (2)

[All patterns of extension/cjw_tmv](area-extension-cjw-tmv.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwTmvFeed` | class | `extension/cjw_tmv/classes/cjwtmvfeed.php:14` | The TMV events of one event container (a tmv_container node), read from the content objects that the importer (cjwTmvImporter, cronjob part cjw_tmv)… | – |
| `cjwTmvImporter` | class | `extension/cjw_tmv/classes/cjwtmvimporter.php:28` | Imports the events of the TMV event database as content objects below their tmv_container, as the legacy extension cjw_tmv_veranstdb did (TmvHelper::… | – |

### extension/explayouts (16)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsBlock` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblock.php:2` | – | – |
| `expLayoutsBlockHandlerFactory` | class | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsLayout` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutslayout.php:2` | – | – |
| `expLayoutsPager` | class | `extension/explayouts/classes/explayoutspager.php:8` | Exponential 4 pagination helper compatible with the Exp Site API Pagerfanta adapters. | – |
| `expLayoutsQueryHandlerFactory` | class | `extension/explayouts/classes/explayoutsqueryhandlerfactory.php:2` | – | – |
| `expLayoutsRenderer` | class | `extension/explayouts/classes/explayoutsrenderer.php:2` | – | – |
| `expLayoutsRule` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrule.php:2` | – | – |
| `expLayoutsRuleCondition` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrulecondition.php:2` | – | – |
| `expLayoutsRuleTarget` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsruletarget.php:2` | – | – |
| `expLayoutsSiteBundlePagerfanta` | class | `extension/explayouts/classes/explayoutssitebundlepagerfanta.php:4` | – | – |
| `expLayoutsSiteBundleQueryType` | class | `extension/explayouts/classes/explayoutssitebundlequerytype.php:4` | – | – |
| `expLayoutsZone` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutszone.php:2` | – | – |

### extension/explayouts_relation_list_query (1)

[All patterns of extension/explayouts_relation_list_query](area-extension-explayouts-relation-list-query.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsRelationListQueryFactory` | class | `extension/explayouts_relation_list_query/classes/explayoutsrelationlistqueryfactory.php:2` | – | – |

### extension/explayouts_ui_api (1)

[All patterns of extension/explayouts_ui_api](area-extension-explayouts-ui-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsUIApplicationApi` | class | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – | – |

### extension/expservices (28)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expBookmarkServices` | class extends expUsersBase | `extension/expservices/classes/users/expbookmarkservices.php:12` | expbookmark: the bookmarks of the current user in their tree of virtual folders. | extension/expservices/settings/ezjscore.ini [ezjscServer_expbookmark] Class |
| `expClassGroupServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassgroupservices.php:13` | Class group services: ezjscore/call/expclassgroup::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclassgroup] Class |
| `expClassServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassservices.php:15` | Class services: ezjscore/call/expclass::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclass] Class |
| `expCommentServices` | class extends expCommunityBase | `extension/expservices/classes/community/expcommentservices.php:12` | ezjscore/call/expcomment::: comments on any content (content objects of the comment class below the commented node) and the reviews of products (the… | extension/expservices/settings/ezjscore.ini [ezjscServer_expcomment] Class |
| `expCommunityBase` | abstract class extends expServiceBase | `extension/expservices/classes/community/expcommunitybase.php:12` | The shared work of the forum, topic, reply and comment services: they are content services over configurable content classes ([Community] in expservi… | – |
| `expContentJobServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expcontentjobservices.php:15` | Content job services: ezjscore/call/expcontentjob::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expcontentjob] Class |
| `expCurrencyServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expcurrencyservices.php:11` | ezjscore/call/expcurrency::: the currencies of the shop with their rates, conversion, the preferred currency of the user. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcurrency] Class |
| `expDiscountServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expdiscountservices.php:12` | ezjscore/call/expdiscount::: discount groups (rules), their rules (percent with class, section or product limits) and the users and user groups that… | extension/expservices/settings/ezjscore.ini [ezjscServer_expdiscount] Class |
| `expFeedServices` | class extends expServiceBase | `extension/expservices/classes/community/expfeedservices.php:13` | ezjscore/call/expfeed::: feeds. | extension/expservices/settings/ezjscore.ini [ezjscServer_expfeed] Class |
| `expForumServices` | class extends expCommunityBase | `extension/expservices/classes/community/expforumservices.php:12` | ezjscore/call/expforum::: forums (content objects of the forum class, kept in forum containers). | extension/expservices/settings/ezjscore.ini [ezjscServer_expforum] Class |
| `expLayoutServices` | class extends expServiceBase | `extension/expservices/classes/misc/explayoutservices.php:12` | ezjscore/call/explayout:: - the layouts of the explayouts extension: layouts, zones, blocks, rules, the layout resolved for a node, and drafts (creat… | extension/expservices/settings/ezjscore.ini [ezjscServer_explayout] Class |
| `expNodeServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expnodeservices.php:15` | Node services: ezjscore/call/expnode::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expnode] Class |
| `expObjectServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expobjectservices.php:14` | Object services: ezjscore/call/expobject::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expobject] Class |
| `expPollServices` | class extends expCommunityBase | `extension/expservices/classes/community/exppollservices.php:12` | ezjscore/call/exppoll::: polls (content objects of the poll class with an option attribute that collects information): list, results, vote. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppoll] Class |
| `expRelationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exprelationservices.php:13` | Relation services: ezjscore/call/exprelation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exprelation] Class |
| `expReplyServices` | class extends expCommunityBase | `extension/expservices/classes/community/expreplyservices.php:11` | ezjscore/call/expreply::: the replies of a topic (content objects of the reply class below a topic). | extension/expservices/settings/ezjscore.ini [ezjscServer_expreply] Class |
| `expRoleServices` | class extends expUsersBase | `extension/expservices/classes/users/exproleservices.php:13` | exprole: roles, their editing drafts and their assignments to users and groups. | extension/expservices/settings/ezjscore.ini [ezjscServer_exprole] Class |
| `expSectionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expsectionservices.php:13` | Section services: ezjscore/call/expsection::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expsection] Class |
| `expServiceToken` | class extends eZPersistentObject | `extension/expservices/classes/expservicetoken.php:11` | A personal API token of a user for the remote services (table expservices_token). | – |
| `expSitemapServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/expsitemapservices.php:12` | ezjscore/call/expsitemap:: - sitemap data: the entries of a subtree as a sitemap lists them (location, last modification, change frequency, priority)… | extension/expservices/settings/ezjscore.ini [ezjscServer_expsitemap] Class |
| `expStateServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expstateservices.php:14` | Object state services: ezjscore/call/expstate::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expstate] Class |
| `expTagsServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/exptagsservices.php:13` | ezjscore/call/exptags:: - the tag tree of the eztags extension: tree, search, children, synonyms, translations, related content, and writes (add, ren… | extension/expservices/settings/ezjscore.ini [ezjscServer_exptags] Class |
| `expTopicServices` | class extends expCommunityBase | `extension/expservices/classes/community/exptopicservices.php:12` | ezjscore/call/exptopic::: the topics of a forum (content objects of the topic class). | extension/expservices/settings/ezjscore.ini [ezjscServer_exptopic] Class |
| `expUrlAliasServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expurlaliasservices.php:14` | URL alias and wildcard services: ezjscore/call/expurlalias::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expurlalias] Class |
| `expUserGroupServices` | class extends expUsersBase | `extension/expservices/classes/users/expusergroupservices.php:10` | expusergroup: the user group tree and its members. | extension/expservices/settings/ezjscore.ini [ezjscServer_expusergroup] Class |
| `expUserServices` | class extends expUsersBase | `extension/expservices/classes/users/expuserservices.php:13` | expuser: look up, list, search and administer users. | extension/expservices/settings/ezjscore.ini [ezjscServer_expuser] Class |
| `expVatServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expvatservices.php:13` | ezjscore/call/expvat::: VAT types, VAT charging rules by country and product category, the product categories the rules use, and the VAT of a product… | extension/expservices/settings/ezjscore.ini [ezjscServer_expvat] Class |
| `expVersionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expversionservices.php:13` | Version services: ezjscore/call/expversion::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expversion] Class |

### extension/exp_oe_tiptap (3)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapAIException` | class extends Exception | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexception.php:18` | An error of the AI hooks whose message may be shown to the editor (ezjscore sends exception messages to the browser as error_text). | – |
| `expOETiptapAIExpaiProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexpaiprovider.php:32` | The AI commands through the extension expai (Exponential Intelligence), [AISettings] Provider=expai: the request goes to the model expai is set up wi… | – |
| `expOETiptapAIService` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiservice.php:19` | The AI hooks on the server: checks, limits, prompt, provider call, answer. | – |

### extension/ezfind (2)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFindExtendedAttributeFilterFactory` | class | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Extended attribute filters factory | – |
| `ezpFileArchiveFactory` | class | `extension/ezfind/classes/archive/filestorage/ezpfilearchivefactory.php:12` | – | – |

### extension/ezflow (5)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFeedReader` | class | `extension/ezflow/autoloads/ezfeedreader.php:32` | eZFeedReader class impelement feedreader tpl operator methods | – |
| `eZPage` | class | `extension/ezflow/classes/ezpage.php:28` | – | – |
| `eZPageBlock` | class | `extension/ezflow/classes/ezpageblock.php:28` | – | – |
| `eZPageBlockItem` | class | `extension/ezflow/classes/ezpageblockitem.php:28` | – | – |
| `eZPageZone` | class | `extension/ezflow/classes/ezpagezone.php:28` | – | – |

### extension/ezgmaplocation (1)

[All patterns of extension/ezgmaplocation](area-extension-ezgmaplocation.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZGmapLocation` | class extends eZPersistentObject | `extension/ezgmaplocation/classes/ezgmaplocation.php:40` | Helper class used along with the eZGmapLocationType datatype. | – |

### extension/ezjscore (2)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscPacker` | class | `extension/ezjscore/classes/ezjscpacker.php:53` | – | – |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjsc] Class |

### extension/ezmbpaex (1)

[All patterns of extension/ezmbpaex](area-extension-ezmbpaex.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPaEx` | class extends eZPersistentObject | `extension/ezmbpaex/classes/ezpaex.php:11` | – | – |

### extension/ezmultiupload (1)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMultiuploadService` | class | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | The server side of the upload page: checks before an upload (the target location, the content class each file becomes, the create permission for it t… | – |

### extension/ezownerchange (1)

[All patterns of extension/ezownerchange](area-extension-ezownerchange.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOwnerChangeRequest` | class | `extension/ezownerchange/classes/expownerchangerequest.php:14` | One owner change request, checked before anything is read or written: the new owner, the scope (one object or a subtree), the start (object or node),… | – |

### extension/ezpm (1)

[All patterns of extension/ezpm](area-extension-ezpm.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPm` | class extends eZPersistentObject | `extension/ezpm/classes/ezpm.php:15` | – | – |

### extension/ezstarrating (3)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrRatingDataObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingdataobject.php:27` | – | – |
| `ezsrRatingObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingobject.php:27` | – | – |
| `ezsrRatingObjectTreeNode` | class extends eZContentObjectTreeNode | `extension/ezstarrating/classes/ezsrratingobjecttreenode.php:36` | Only to be used by {@link ezsrRatingObject::fetchNodeByRating()} As other fetch functions won't have the needed rating values in the returned data. | – |

### extension/eztags (1)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTags` | class | `extension/eztags/datatypes/eztags/eztags.php:6` | eZTags class implements functions used by eztags datatype | – |

### extension/ezupdate (1)

[All patterns of extension/ezupdate](area-extension-ezupdate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZUpdateJob` | class | `extension/ezupdate/classes/ezupdatejob.php:21` | A Composer run in the background, so a long update or install neither holds a web request open nor hits its time limit, and the page can show the out… | – |

### extension/ezurlmultisite (1)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLMultisite` | class | `extension/ezurlmultisite/classes/ezurlmultisite.php:27` | Cross-siteaccess link resolution for the ezurl template operator. | – |

### extension/git_manager (1)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `GitManagerBackupFreshness` | class | `extension/git_manager/classes/gitmanagerbackupfreshness.php:32` | How fresh the backups are: one answer, taken from the NEWEST backup. | – |

### extension/nxc_powercontent (1)

[All patterns of extension/nxc_powercontent](area-extension-nxc-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentOperationCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:20` | – | – |

### extension/sevenx_authentication_2fa (1)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxAuthentication2fa` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fa.php:22` | – | – |

### extension/sevenx_dse (4)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AdminNeo\Connection` | abstract class | `extension/sevenx_dse/adminneo/core/Connection.php:5` | – | – |
| `AdminNeo\Driver` | abstract class | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – | – |
| `AdminNeo\Locale` | class | `extension/sevenx_dse/adminneo/core/Locale.php:5` | – | – |
| `AdminNeo\Origin` | abstract class extends Plugin | `extension/sevenx_dse/adminneo/core/Origin.php:7` | – | – |

### extension/syndication (9)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSyndicationFeed` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeed.php:47` | – | – |
| `eZSyndicationFeedItem` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditem.php:48` | – | – |
| `eZSyndicationFeedItemExport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemexport.php:48` | – | – |
| `eZSyndicationFeedItemStatus` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemstatus.php:48` | – | – |
| `eZSyndicationFeedSource` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsource.php:47` | – | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfilter.php:48` | – | – |
| `eZSyndicationImport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimport.php:47` | – | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – | – |

### extension/xrowextract (7)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractArchive` | class | `extension/xrowextract/classes/xrowextractarchive.php:12` | The site archive: the objects of many classes below many nodes, one CSV file per class, with a manifest, packed as zip, tar.gz, tar.bz2, tar.xz, 7z o… | – |
| `XrowExtractFilters` | class | `extension/xrowextract/classes/xrowextractfilters.php:16` | Filters of an export: a date range (published, modified, or a date attribute of the class; any, today, the last N days, since, before, between, futur… | – |
| `XrowExtractImport` | class | `extension/xrowextract/classes/xrowextractimport.php:22` | The other direction of the export: reads a CSV or JSON file written by XrowExtractWriter (any column set, in particular "migration") back into conten… | – |
| `XrowExtractJob` | class | `extension/xrowextract/classes/xrowextractjob.php:16` | A background export job: one folder per job below the var directory (var//xrowextract-jobs//), holding job.json (state), progress.json (written by th… | – |
| `XrowExtractManifest` | class | `extension/xrowextract/classes/xrowextractmanifest.php:20` | The typed column manifest of an export: what every column of a file is (its id, the name in the file, the datatype, the chosen format and language),… | – |
| `XrowExtractPackage` | class | `extension/xrowextract/classes/xrowextractpackage.php:18` | Content-package (.ezpkg) support for xrowextract/package: inspecting a package's content classes and content objects before anything is written (the… | – |
| `XrowExtractUpload` | class | `extension/xrowextract/classes/xrowextractupload.php:22` | A chunked upload: one folder per upload (var//xrowextract/uploads//), holding meta.json (owner, original name, total size, bytes received so far, whe… | – |

### extension/xrowmetadata (1)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowSitemapTools` | class | `extension/xrowmetadata/classes/xrowsitemaptools.php:3` | – | – |

### kernel/classes (80)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expCacheCatalogue` | class | `kernel/classes/expcachecatalogue.php:22` | The caches of Setup › Caches, described for a person: what each holds, its id and tags, its directory, its size and number of files, when it was last… | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Creates, renames and deletes the collaboration groups of one user and moves items between them. | – |
| `expErrorHeaderList` | class | `kernel/classes/experrorheaderlist.php:25` | The response headers of an error page, from error.ini [HTTPError-]: | – |
| `expExtensionCatalogue` | class | `kernel/classes/expextensioncatalogue.php:20` | What Setup › Extensions knows about each extension: its metadata (expInfo), its declared dependencies (extension.xml ), the designs and settings file… | – |
| `expExtensionChangePlan` | class | `kernel/classes/expextensionchangeplan.php:33` | The change planner of Setup › Extensions: what a list of active extensions becomes when an administrator moves, activates or deactivates one, what di… | – |
| `expJsonSchemaValidator` | class | `kernel/classes/expjsonschemavalidator.php:20` | Checks decoded JSON (json_decode( ..., true )) against a JSON Schema (draft 2020-12), for the keywords the kernel's own schemas use: type, enum, cons… | – |
| `expPackageConflictReport` | class | `kernel/classes/exppackageconflictreport.php:22` | What installing a package would meet on this installation, by remote id, without writing anything: for every content object of the package whether an… | – |
| `expPhar` | class | `kernel/classes/expphar.php:21` | Packages the engine -- the kernel and library class files -- into a phar, so the runtime can load them from one archive instead of from a thousand se… | – |
| `expPreloadAddress` | class | `kernel/classes/exppreloadaddress.php:26` | Where the cache preloader finds a siteaccess: the address of its site, the url prefix that selects it on that host, and the pages a run starts from. | – |
| `expSiteAccessOverview` | class | `kernel/classes/expsiteaccessoverview.php:31` | Every siteaccess of the installation, how a request reaches it and where it is: the siteaccess part of Setup › System information (#552). | – |
| `expSystemReport` | class | `kernel/classes/expsystemreport.php:29` | The facts of Setup › System information and of exp:system:info, in sections, with health checks. | – |
| `expVelocity` | class | `kernel/classes/expvelocity.php:38` | Drives the bundled application server as a service. | – |
| `expVelocityMarkers` | class | `kernel/classes/expvelocitymarkers.php:30` | The small state files that say why an engine is not running. | – |
| `expVelocityRestartUnit` | class | `kernel/classes/expvelocityrestartunit.php:34` | Runs exp:velocity restart as one detached unit: the stop and the start in a process that does not belong to the caller. | – |
| `eZCollaborationGroup` | class extends eZPersistentObject | `kernel/classes/ezcollaborationgroup.php:18` | – | – |
| `eZCollaborationItem` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitem.php:18` | – | – |
| `eZCollaborationItemGroupLink` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemgrouplink.php:18` | – | – |
| `eZCollaborationItemMessageLink` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemmessagelink.php:18` | – | – |
| `eZCollaborationItemParticipantLink` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemparticipantlink.php:18` | – | – |
| `eZCollaborationItemStatus` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemstatus.php:18` | – | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | `kernel/classes/ezcollaborationprofile.php:18` | – | – |
| `eZCollaborationSimpleMessage` | class extends eZPersistentObject | `kernel/classes/ezcollaborationsimplemessage.php:18` | – | – |
| `eZContentBrowseBookmark` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowsebookmark.php:35` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentBrowseBookmarkFolder` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowsebookmarkfolder.php:32` | – | – |
| `eZContentBrowseRecent` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowserecent.php:34` | – | – |
| `eZContentClass` | class extends eZPersistentObject | `kernel/classes/ezcontentclass.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentclassattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassClassGroup` | class extends eZPersistentObject | `kernel/classes/ezcontentclassclassgroup.php:18` | – | – |
| `eZContentClassGroup` | class extends eZPersistentObject | `kernel/classes/ezcontentclassgroup.php:18` | – | – |
| `eZContentFunctions` | class | `kernel/classes/ezcontentfunctions.php:12` | – | – |
| `eZContentObject` | class extends eZPersistentObject | `kernel/classes/ezcontentobject.php:17` | Encapsulates data about and methods to work with content objects | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectattribute.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectTrashNode` | class extends eZContentObjectTreeNode | `kernel/classes/ezcontentobjecttrashnode.php:15` | Encapsulates data about and methods to work with content objects which reside in the trash | – |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | `kernel/classes/ezcontentobjecttreenode.php:17` | Encapsulates data about and methods to work with content objects inside the content object tree | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectVersion` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectversion.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZDataType` | class | `kernel/classes/ezdatatype.php:46` | – | – |
| `eZDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountrule.php:18` | – | – |
| `eZDiscountSubRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrule.php:18` | – | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrulevalue.php:17` | – | – |
| `eZInformationCollection` | class extends eZPersistentObject | `kernel/classes/ezinformationcollection.php:22` | – | – |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | `kernel/classes/ezinformationcollectionattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZNamePatternResolver` | class | `kernel/classes/eznamepatternresolver.php:40` | eZNamePatternResolver is a utility class for resolving object name and url alias patterns. | – |
| `eZNodeAssignment` | class extends eZPersistentObject | `kernel/classes/eznodeassignment.php:17` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZOrderStatus` | class extends eZPersistentObject | `kernel/classes/ezorderstatus.php:47` | – | – |
| `eZOrderStatusHistory` | class extends eZPersistentObject | `kernel/classes/ezorderstatushistory.php:35` | – | – |
| `eZPackage` | class | `kernel/classes/ezpackage.php:20` | – | – |
| `eZPackageComparison` | class | `kernel/classes/ezpackagecomparison.php:36` | Compares what a package carries with what the site has, for package/compare/. | – |
| `eZPackageRemovalPlan` | class | `kernel/classes/ezpackageremovalplan.php:20` | What removing a selection of packages would do, before it is done: per package its directory, files and size, whether it is installed (its install re… | – |
| `eZPDFExport` | class extends eZPersistentObject | `kernel/classes/ezpdfexport.php:19` | – | – |
| `eZPolicy` | class extends eZPersistentObject | `kernel/classes/ezpolicy.php:19` | – | – |
| `eZPolicyLimitation` | class extends eZPersistentObject | `kernel/classes/ezpolicylimitation.php:18` | – | – |
| `eZPolicyLimitationValue` | class extends eZPersistentObject | `kernel/classes/ezpolicylimitationvalue.php:18` | – | – |
| `eZProductCategory` | class extends eZPersistentObject | `kernel/classes/ezproductcategory.php:18` | – | – |
| `eZProductCollection` | class extends eZPersistentObject | `kernel/classes/ezproductcollection.php:16` | eZProductCollection is a container class which handles groups of products | – |
| `eZProductCollectionItem` | class extends eZPersistentObject | `kernel/classes/ezproductcollectionitem.php:20` | – | – |
| `eZProductCollectionItemOption` | class extends eZPersistentObject | `kernel/classes/ezproductcollectionitemoption.php:12` | – | – |
| `eZRole` | class extends eZPersistentObject | `kernel/classes/ezrole.php:35` | – | – |
| `eZRSSExport` | class extends eZPersistentObject | `kernel/classes/ezrssexport.php:19` | – | – |
| `eZRSSExportItem` | class extends eZPersistentObject | `kernel/classes/ezrssexportitem.php:19` | – | – |
| `eZRSSExportOPMLItem` | class extends eZPersistentObject | `kernel/classes/ezrssexportopmlitem.php:26` | One line of an OPML export: a feed that the export lists. | – |
| `eZRSSImport` | class extends eZPersistentObject | `kernel/classes/ezrssimport.php:19` | – | – |
| `eZSearch` | class | `kernel/classes/ezsearch.php:19` | – | – |
| `eZSiteData` | class extends eZPersistentObject | `kernel/classes/ezsitedata.php:21` | eZPersistentObject implementation for ezsite_data table. | – |
| `eZSiteInstaller` | class | `kernel/classes/ezsiteinstaller.php:23` | – | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | `kernel/classes/eztipafriendcounter.php:18` | – | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | `kernel/classes/eztipafriendrequest.php:17` | – | – |
| `eZTrigger` | class extends eZPersistentObject | `kernel/classes/eztrigger.php:17` | – | – |
| `eZURLAliasML` | class extends eZPersistentObject | `kernel/classes/ezurlaliasml.php:58` | – | – |
| `eZURLAliasQuery` | class | `kernel/classes/ezurlaliasquery.php:34` | – | – |
| `eZURLWildcard` | class extends eZPersistentObject | `kernel/classes/ezurlwildcard.php:19` | – | – |
| `eZUserDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezuserdiscountrule.php:18` | – | – |
| `eZVatRule` | class extends eZPersistentObject | `kernel/classes/ezvatrule.php:18` | – | – |
| `eZVatType` | class extends eZPersistentObject | `kernel/classes/ezvattype.php:19` | – | – |
| `eZViewCounter` | class extends eZPersistentObject | `kernel/classes/ezviewcounter.php:12` | – | – |
| `eZWorkflow` | class extends eZPersistentObject | `kernel/classes/ezworkflow.php:18` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZWorkflowEvent` | class extends eZPersistentObject | `kernel/classes/ezworkflowevent.php:18` | – | – |
| `eZWorkflowGroup` | class extends eZPersistentObject | `kernel/classes/ezworkflowgroup.php:18` | – | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | `kernel/classes/ezworkflowgrouplink.php:18` | – | – |
| `eZWorkflowProcess` | class extends eZPersistentObject | `kernel/classes/ezworkflowprocess.php:19` | – | – |
| `eZWorkflowType` | class | `kernel/classes/ezworkflowtype.php:18` | – | – |

### kernel/classes/apikey (1)

[All patterns of kernel/classes/apikey](area-kernel-classes-apikey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKey` | class extends eZPersistentObject | `kernel/classes/apikey/expapikey.php:31` | A personal API key (publishing key) of a user: one row of expapikey (doc/guides/api-keys.md). | – |

### kernel/classes/audit (6)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | The audit: what happens in Exponential, recorded as hash-chained JSON lines per channel (doc/bc/6.0/audit.md, "The developer API (Z3)"). | – |
| `expAuditAlertState` | class | `kernel/classes/audit/alerts/expauditalertstate.php:19` | The window state of one alert rule (doc/bc/6.0/audit.md, "Alerts (F5)"): /alerts/.json, read and written under flock() only when a matching record wa… | – |
| `expAuditConfig` | class | `kernel/classes/audit/expauditconfig.php:21` | The audit settings (settings/audit.ini, doc/bc/6.0/audit.md "The settings reference") read once into one array. | – |
| `expAuditExportStream` | class | `kernel/classes/audit/console/expauditexportstream.php:28` | The audit records of a filter as CSV, JSON lines or JSON, newest first, written piece by piece to a callback as they are read: the download of audit/… | – |
| `expAuditIndexRow` | class | `kernel/classes/audit/index/expauditindexrow.php:12` | One audit record as a row of expaudit_event (doc/bc/6.0/audit.md, "Tables"): the columns the console filters on, the record itself (as written, so th… | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | The registry of the audit console's sources (expAuditSource), from audit.ini [AuditSourceSettings] Sources[]: which are registered, which work here,… | – |

### kernel/classes/collaborationhandlers (1)

[All patterns of kernel/classes/collaborationhandlers](area-kernel-classes-collaborationhandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZApproveCollaborationHandler` | class extends eZCollaborationItemHandler | `kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php:25` | – | – |

### kernel/classes/contentcheck (1)

[All patterns of kernel/classes/contentcheck](area-kernel-classes-contentcheck.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentCheckReferences` | class | `kernel/classes/contentcheck/expcontentcheckreferences.php:37` | The reference index of the unreferenced list (doc/features/6.0/content-checks.md, "What counts as a reference"): every object id, node id, remote id… | – |

### kernel/classes/contentjob (2)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJob` | class | `kernel/classes/contentjob/expcontentjob.php:32` | A content job: a large subtree remove or copy that runs in batches in a background worker instead of in the web request, so it never fails from a req… | – |
| `expContentJobException` | class extends Exception | `kernel/classes/contentjob/expcontentjobexception.php:13` | The error a content job refuses or fails with; its message is shown to the user. | – |

### kernel/classes/datatypes (17)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expCountryGroups` | class | `kernel/classes/datatypes/ezcountry/expcountrygroups.php:24` | The country list as people pick from it: first the groups of settings/countrygroups.ini (by default one group of the most used countries, in ranked o… | – |
| `eZBinaryFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php:19` | – | – |
| `eZEnumObjectValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumobjectvalue.php:18` | – | – |
| `eZEnumValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumvalue.php:19` | – | – |
| `eZForgotPassword` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezforgotpassword.php:19` | – | – |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZISBNGroup` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – | – |
| `eZMedia` | class extends eZPersistentObject | `kernel/classes/datatypes/ezmedia/ezmedia.php:19` | – | – |
| `eZObjectRelationListType` | class extends eZDataType | `kernel/classes/datatypes/ezobjectrelationlist/ezobjectrelationlisttype.php:26` | – | – |
| `eZObjectRelationType` | class extends eZDataType | `kernel/classes/datatypes/ezobjectrelation/ezobjectrelationtype.php:19` | – | – |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZURLObjectLink` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – | – |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUserAccountKey` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – | – |
| `eZUserSetting` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezusersetting.php:18` | – | – |

### kernel/classes/help (7)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpAllowlist` | class | `kernel/classes/help/exphelpallowlist.php:29` | The files the help system may show (doc/features/6.0/help-system.md, "Which files are shown"). | – |
| `expHelpAudience` | class | `kernel/classes/help/exphelpaudience.php:28` | Who a help page is for, which section of the help it belongs to and what kind of page it is (doc/features/6.0/help-system.md, "Audiences, categories… | – |
| `expHelpContextRegistry` | class | `kernel/classes/help/exphelpcontextregistry.php:44` | Context help: which help page answers "help for this page", for a module, view and design. | – |
| `expHelpMarkdownRenderer` | class | `kernel/classes/help/exphelpmarkdownrenderer.php:50` | Renders the Markdown of the documentation (doc/**.md) to the HTML of the help viewer (exphelp/viewer, doc/features/6.0/help-system.md): CommonMark bl… | – |
| `expHelpPdf` | class | `kernel/classes/help/exphelppdf.php:18` | A help page as a PDF file (doc/features/6.0/help-system.md, "Print and PDF"): the view exphelp/pdf/ and `./console exp:help pdf ` ask here. | – |
| `expHelpPdfFontSubset` | class | `kernel/classes/help/exphelppdffontsubset.php:17` | Cuts a TrueType font down to the glyphs a PDF uses (doc/features/6.0/help-system.md, "Print and PDF"). | – |
| `expHelpSearch` | class | `kernel/classes/help/exphelpsearch.php:34` | expHelpSearch: the full-text search of the help system (doc/features/6.0/help-system.md, "Search"). | – |

### kernel/classes/ini (6)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniActionRegistry` | class | `kernel/classes/ini/actions/expiniactionregistry.php:22` | The actions of the exp:ini command and the scope providers of the settings editor, read from ini.ini [IniCommandSettings]: | – |
| `expIniEditor` | class | `kernel/classes/ini/expinieditor.php:27` | The INI editor. | – |
| `expIniException` | class extends Exception | `kernel/classes/ini/expiniexception.php:15` | An error of the INI editor. | – |
| `expIniWriter` | class | `kernel/classes/ini/expiniwriter.php:26` | One INI file as a list of lines, read exactly the way eZINI::parseFile() reads it, and changed one line at a time: comments, blank lines, the order o… | – |
| `expSettingsChain` | class | `kernel/classes/ini/expsettingschain.php:32` | The override chain of one INI file, built once from the files eZINI reads for it, in eZINI's own order. | – |
| `expSettingsSecretRule` | class | `kernel/classes/ini/expsettingssecretrule.php:30` | The rule the settings pages (settings/view, settings/edit) mask values by. | – |

### kernel/classes/logviewer (7)

[All patterns of kernel/classes/logviewer](area-kernel-classes-logviewer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogBundle` | class | `kernel/classes/logviewer/explogbundle.php:17` | "Download all logs" (Setup › Manage logs; doc/features/6.0/log-viewer.md, "Downloading every log"): one archive of every log the viewer lists, option… | – |
| `expLogManagerFactory` | class | `kernel/classes/logviewer/explogmanagerfactory.php:12` | The log manager the views of the log viewer use (doc/features/6.0/log-viewer.md, "Managing logs"): the kernel's service expLogManager when it is ther… | – |
| `expLogPermalink` | class | `kernel/classes/logviewer/explogpermalink.php:23` | The permalinks of the log viewer (doc/features/6.0/log-viewer.md, "Permalinks"): every view of a log as a path without a query string, short enough t… | – |
| `expLogRateLimit` | class | `kernel/classes/logviewer/explogratelimit.php:16` | The request limit of the log viewer's feed, per session ([RateLimitSettings], doc/features/6.0/log-viewer.md, "Cost"), and the memory of which files… | – |
| `expLogRedactor` | class | `kernel/classes/logviewer/explogredactor.php:16` | What the log viewer masks before a line leaves the server (doc/features/6.0/log-viewer.md, "Privacy"). | – |
| `expLogViewer` | class | `kernel/classes/logviewer/explogviewer.php:16` | The log viewer (Setup › Log files, setup/logs; doc/features/6.0/log-viewer.md): what its page, its feed and its command line use. | – |
| `expLogViewerConfig` | class | `kernel/classes/logviewer/explogviewerconfig.php:11` | The settings of the log viewer (settings/logviewer.ini, doc/features/6.0/log-viewer.md, "Settings"), read once per request with safe bounds. | – |

### kernel/classes/mailpreferences (6)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expConsentContext` | class | `kernel/classes/mailpreferences/expconsentcontext.php:19` | Where a change of a preference comes from, as the consent log records it: the source (page, link, admin, import, signup, bridge, confirm, system), th… | – |
| `expConsentLog` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expconsentlog.php:22` | The consent log (table expmail_consent_log): one row per change of a person's e-mail preferences, with the time, the category, the action (on, off, p… | – |
| `expMailPreferences` | class | `kernel/classes/mailpreferences/expmailpreferences.php:28` | The e-mail preferences of one person (expMailRecipient): the master switch "all optional mail", the state of each category (on, off, or pending while… | – |
| `expMailRecipient` | class | `kernel/classes/mailpreferences/expmailrecipient.php:23` | Identifies the person whose preferences are meant: a user account, or an e-mail address without account. | – |
| `expMailSuppression` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailsuppression.php:22` | The suppression list (table expmail_suppression): addresses no optional mail goes to (hard bounces, complaints, "unsubscribe from all", legal request… | – |
| `expMailToken` | class | `kernel/classes/mailpreferences/expmailtoken.php:24` | The links in mail that work without login: unsubscribe (one click, RFC 8058), manage (the preference page) and confirm (a double opt-in). | – |

### kernel/classes/notification (7)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCollaborationNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php:17` | – | – |
| `eZGeneralDigestUserSettings` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigestusersettings.php:18` | – | – |
| `eZNotificationCollection` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollection.php:17` | – | – |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – | – |
| `eZNotificationEvent` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationevent.php:17` | – | – |
| `eZNotificationEventType` | class | `kernel/classes/notification/eznotificationeventtype.php:18` | – | – |
| `eZSubtreeNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php:17` | – | – |

### kernel/classes/packagehandlers (1)

[All patterns of kernel/classes/packagehandlers](area-kernel-classes-packagehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentObjectPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezcontentobject] |

### kernel/classes/requestclassifier (1)

[All patterns of kernel/classes/requestclassifier](area-kernel-classes-requestclassifier.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRequestContext` | class | `kernel/classes/requestclassifier/exprequestcontext.php:20` | What the request classifier looks at: one request, as it came in. | – |

### kernel/classes/role (1)

[All patterns of kernel/classes/role](area-kernel-classes-role.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRoleGrantCheck` | class | `kernel/classes/role/exprolegrantcheck.php:37` | Whether a user who edits roles may grant a policy: only one that their own access covers (site.ini [RoleSettings] PreventPrivilegeEscalation). | – |

### kernel/classes/subitems (1)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsAttributeColumn` | class extends expSubitemsColumn | `kernel/classes/subitems/expsubitemsattributecolumn.php:16` | An automatic subitems column for one class attribute, key attr:/. | – |

### kernel/classes/uiswitch (1)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitch` | class | `kernel/classes/uiswitch/expuiswitch.php:28` | The UI switch framework: a registry of providers (uiswitch.ini [UISwitchSettings] ProviderList[]), the user's choices kept in the session, and the ea… | – |

### kernel/classes/user (5)

[All patterns of kernel/classes/user](area-kernel-classes-user.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRegistrationAdminAction` | class | `kernel/classes/user/expregistrationadminaction.php:23` | The signed links of the administrator's "New user registered" mail (design:user/registrationfeedback.tpl) and the actions behind them (user/registrat… | – |
| `expUnactivatedUsers` | class | `kernel/classes/user/expunactivatedusers.php:22` | The users who registered and never activated their account, for the page user/unactivated: how many there are, a page of them searched by login, e-ma… | – |
| `expUserAccountOverview` | class | `kernel/classes/user/expuseraccountoverview.php:22` | The account page of a user (user/edit, user/edit/), described for a person: who the account is (name, login, e-mail, groups, roles), its state (enabl… | – |
| `expUserDeactivation` | class | `kernel/classes/user/expuserdeactivation.php:36` | "Deactivate and move" and "Reactivate and move back": instead of removing a user account (users never go to the trash, so removing one cannot be undo… | – |
| `expUserHygiene` | class | `kernel/classes/user/expuserhygiene.php:31` | The accounts the page user/hygiene ("Check user accounts") lists: every user account, or those below a user group, narrowed by one filter (never sign… | – |

### kernel/classes/workflowtypes (2)

[All patterns of kernel/classes/workflowtypes](area-kernel-classes-workflowtypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPaymentLogger` | class | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentlogger.php:16` | – | – |
| `eZWaitUntilDateValue` | class extends eZPersistentObject | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatevalue.php:18` | – | – |

### kernel/modules (7)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCurrencyData` | class extends eZPersistentObject | `kernel/shop/classes/ezcurrencydata.php:12` | – | – |
| `eZExchangeRatesUpdateHandler` | class | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php:12` | – | – |
| `eZMultiEdit` | class | `kernel/content/multiedit_functions.php:16` | The working parts of content/multiedit. | – |
| `eZMultiPriceData` | class extends eZPersistentObject | `kernel/shop/classes/ezmultipricedata.php:12` | – | – |
| `eZPaymentObject` | class extends eZPersistentObject | `kernel/shop/classes/ezpaymentobject.php:22` | – | – |
| `eZShopFunctions` | class | `kernel/shop/classes/ezshopfunctions.php:12` | – | – |
| `eZStepCreateSites` | class extends eZStepInstaller | `kernel/setup/steps/ezstep_create_sites.php:52` | – | – |

### kernel/private (15)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expFileConsistencyReport` | class | `kernel/private/classes/expfileconsistencyreport.php:36` | Compares the files of an installation with the checksum lists that ship with it: share/filelist.md5 of Exponential and the share/filelist.md5 an exte… | – |
| `expRestContentPermission` | class | `kernel/private/rest/classes/auth/content_permission.php:36` | The current user's rights on the content a REST call reads or writes, checked the way the content module checks them (doc/guides/api-keys.md, "Permis… | – |
| `expSchemaConsistencyReport` | class | `kernel/private/classes/expschemaconsistencyreport.php:36` | Compares the database with the schema that ships with Exponential and its active extensions (share/db_schema.dba of the root and of each extension),… | – |
| `eZAutoloadGenerator` | class | `kernel/private/classes/ezautoloadgenerator.php:19` | Utility class for generating autoload arrays for Exponential. | – |
| `ezpActiveExtensions` | class | `kernel/private/classes/ezpactiveextensions.php:26` | Changes [ExtensionSettings] ActiveExtensions in settings/override/site.ini.append.php, for Setup › Extensions, without touching anything else in that… | – |
| `ezpAttributeOperatorManager` | class | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – | – |
| `ezpContent` | class | `kernel/private/api/content/content.php:56` | Class that represents a content item. | – |
| `ezpContentField` | class | `kernel/private/api/content/field.php:17` | This class handles content fields. | – |
| `ezpContentFieldSet` | class | `kernel/private/api/content/field_set.php:18` | Allows for reading/writing of content fields (attributes) through an array like interface. | – |
| `ezpContentLocation` | class extends ezpLocation | `kernel/private/api/content/location.php:21` | This class is used to manipulate a specific content location. | – |
| `ezpContentPublishingProcess` | class extends eZPersistentObject | `kernel/private/classes/ezpcontentpublishingprocess.php:18` | This class provides a PersistentObject interface to a publishing queue process. | – |
| `ezpRestContentRenderer` | class | `kernel/private/rest/classes/content_renderer.php:12` | – | – |
| `ezpRestMvcResult` | class extends ezcMvcResult | `kernel/private/rest/classes/response/result.php:15` | This struct contains the result data to be returned by eZP REST controllers | – |
| `ezpRestProvider` | class | `kernel/private/rest/classes/rest_provider.php:12` | – | – |
| `ezpSiteAccessMenu` | class | `kernel/private/classes/ezpsiteaccessmenu.php:48` | The administration's site menu: the siteaccesses of this installation with their front page addresses, for the globe button in the admin4 header. | – |

### kernel/private/classes/clusterfilehandlers (2)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandlerBackendFactory` | class | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php:14` | Instantiates the DFSBackend based on INI settings | – |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendfactoryinterface.php:14` | Makes a DFSBackend handler instantiable using the build static method. | – |

### kernel/private/classes/commands (1)

[All patterns of kernel/private/classes/commands](area-kernel-private-classes-commands.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Kernel\Checkdbfiles` | class extends Command | `kernel/private/classes/commands/checkdbfiles.php:149` | The code of bin/php/checkdbfiles.php, moved into a class (#207 stage 1). | – |

### kernel/private/classes/httpcache (1)

[All patterns of kernel/private/classes/httpcache](area-kernel-private-classes-httpcache.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpHttpCacheContract` | class | `kernel/private/classes/httpcache/ezphttpcachecontract.php:38` | The role-aware HTTP cache contract: keys, context hashes, the entry and record formats, tag validation and placeholder substitution. | – |

### kernel/private/classes/requestrules (2)

[All patterns of kernel/private/classes/requestrules](area-kernel-private-classes-requestrules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRequestRule` | class | `kernel/private/classes/requestrules/ezprequestrule.php:33` | One request rule: when every condition matches, the action decides. | – |
| `ezpRequestRuleResult` | class | `kernel/private/classes/requestrules/ezprequestruleresult.php:24` | What a rule decided. | – |

### kernel/private/classes/runnable (1)

[All patterns of kernel/private/classes/runnable](area-kernel-private-classes-runnable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Runnable\Runnable` | abstract class | `kernel/private/classes/runnable/runnable.php:30` | The base of everything Exponential runs from a file: CLI commands (bin/), cronjob parts (cronjobs/) and module views (kernel//.php, extension//module… | – |

### kernel/private/classes/views (1)

[All patterns of kernel/private/classes/views](area-kernel-private-classes-views.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Kernel\Content\Job` | class extends ModuleView | `kernel/private/classes/views/content/job.php:19` | The content/job/ view: the progress page of a content job (a large subtree remove or copy that runs in the background, see kernel/classes/contentjob/… | – |

### lib/ezfile (2)

[All patterns of lib/ezfile](area-lib-ezfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDir` | class | `lib/ezfile/classes/ezdir.php:17` | – | – |
| `eZFile` | class | `lib/ezfile/classes/ezfile.php:18` | – | – |

### lib/ezi18n (2)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |
| `eZUTF8Codec` | class | `lib/ezi18n/classes/ezutf8codec.php:22` | – | – |

### lib/ezimage (11)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageAnalyzer` | class | `lib/ezimage/classes/ezimageanalyzer.php:23` | – | – |
| `eZImageFactory` | class | `lib/ezimage/classes/ezimagefactory.php:21` | – | – |
| `eZImageGDFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimagegdfactory.php:12` | – | settings/image.ini [GD] Handler |
| `eZImageGDHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimagegdhandler.php:24` | – | – |
| `eZImageHandler` | class | `lib/ezimage/classes/ezimagehandler.php:19` | – | – |
| `eZImageInterface` | class | `lib/ezimage/classes/ezimageinterface.php:20` | – | – |
| `eZImageLayer` | class extends eZImageInterface | `lib/ezimage/classes/ezimagelayer.php:19` | – | – |
| `eZImageManager` | class | `lib/ezimage/classes/ezimagemanager.php:85` | – | – |
| `eZImageShellFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimageshellfactory.php:12` | – | settings/image.ini [ImageMagick] Handler |
| `eZImageShellHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimageshellhandler.php:19` | – | – |
| `eZImageTextLayer` | class extends eZImageLayer | `lib/ezimage/classes/ezimagetextlayer.php:19` | – | – |

### lib/ezlocale (4)

[All patterns of lib/ezlocale](area-lib-ezlocale.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDate` | class | `lib/ezlocale/classes/ezdate.php:58` | – | – |
| `eZDateTime` | class | `lib/ezlocale/classes/ezdatetime.php:62` | – | – |
| `eZLocale` | class | `lib/ezlocale/classes/ezlocale.php:111` | – | – |
| `eZTime` | class | `lib/ezlocale/classes/eztime.php:59` | – | – |

### lib/ezmath (1)

[All patterns of lib/ezmath](area-lib-ezmath.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPHPMath` | class | `lib/ezmath/classes/mathhandlers/ezphpmath.php:17` | – | settings/shop.ini [MathSettings] MathHandler |

### lib/eztemplate (4)

[All patterns of lib/eztemplate](area-lib-eztemplate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplate` | class | `lib/eztemplate/classes/eztemplate.php:200` | – | – |
| `eZTemplateCompiler` | class | `lib/eztemplate/classes/eztemplatecompiler.php:49` | – | – |
| `eZTemplateNodeTool` | class | `lib/eztemplate/classes/eztemplatenodetool.php:19` | – | – |
| `eZTemplateRoleOperator` | class | `lib/eztemplate/classes/eztemplateroleoperator.php:24` | The eZTemplateRoleOperator class provides template operators for checking user role membership | – |

### lib/ezutils (10)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDebug` | class | `lib/ezutils/classes/ezdebug.php:59` | – | – |
| `eZFatalUserError` | class extends Error | `lib/ezutils/classes/ezfatalusererror.php:35` | A fatal error raised by the kernel itself, in place of a user error of the level E_USER_ERROR, which PHP 8.4 deprecates for trigger_error() ("throw a… | – |
| `eZHTTPTool` | class | `lib/ezutils/classes/ezhttptool.php:25` | – | – |
| `eZINI` | class | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - new ini setting ext-siteaccess-override - - new ini setting ext-siteaccess… | – |
| `eZModule` | class | `lib/ezutils/classes/ezmodule.php:36` | The eZModule class is used to instanciate and use modules & views. | – |
| `eZOperationMemento` | class extends eZPersistentObject | `lib/ezutils/classes/ezoperationmemento.php:18` | – | – |
| `ezpRepairQueue` | class | `lib/ezutils/classes/ezprepairqueue.php:37` | Repairs an installation whose Composer libraries are missing, from the error page that says so (eZExecution::renderErrorCasePage('dependencies')), th… | – |
| `eZSys` | class | `lib/ezutils/classes/ezsys.php:33` | Easy access to various system settings | – |
| `eZURI` | class | `lib/ezutils/classes/ezuri.php:32` | Provides access to the HTTP uri | – |
| `eZWizardBaseClassLoader` | class | `lib/ezutils/classes/ezwizardbaseclassloader.php:18` | – | – |
