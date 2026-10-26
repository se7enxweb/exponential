---
title: Patterns in extension/expservices
audience: developer
category: standards
order: 429
summary: Every class, view, fetch function, operator and plugin point of extension/expservices with its design patterns.
keywords: extension/expservices, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expservices

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expservices` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (78)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expAccountServices` | class extends expUsersBase | [INI plugin point (extension point)](ini-plugin-point.md), [Registry](registry.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expaccountservices.php:13` | expaccount: the account flows of a person: registration and activation, forgotten password and reset, changing one's own password and e-mail, and the… |
| `expAttributeServices` | class extends expContentServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expattributeservices.php:14` | Attribute services: ezjscore/call/expattribute::[::arg...] |
| `expAttrServiceBase` | abstract class extends expServiceBase | [Template Method / abstract base class](template-method.md) | `extension/expservices/classes/media/expattrservicebase.php:11` | Shared helpers of the media services (images, files, media): finding a node's attribute of one datatype, absolute URLs, and the paged listing of the… |
| `expAuditServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expauditservices.php:12` | ezjscore/call/expaudit:: - the audit system, read only (policy audit/read, narrowed to the channels of its Channel limitation): events, one event, re… |
| `expBasketServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expbasketservices.php:12` | ezjscore/call/expbasket::: the basket of the current session and the checkout steps. |
| `expBookmarkServices` | class extends expUsersBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expbookmarkservices.php:12` | expbookmark: the bookmarks of the current user in their tree of virtual folders. |
| `expCacheServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expcacheservices.php:10` | expcache: the caches (setup/managecache): what exists, their state, and clearing them. |
| `expClassGroupServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expclassgroupservices.php:13` | Class group services: ezjscore/call/expclassgroup::[::arg...] |
| `expClassServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expclassservices.php:15` | Class services: ezjscore/call/expclass::[::arg...] |
| `expCollaborationServices` | class extends expUsersBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expcollaborationservices.php:12` | expcollaboration: the collaboration items of the current user (approvals and other handler items), their messages, participants and groups, and the a… |
| `expCommentServices` | class extends expCommunityBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/expcommentservices.php:12` | ezjscore/call/expcomment::: comments on any content (content objects of the comment class below the commented node) and the reviews of products (the… |
| `expCommerceExport` | class | [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expcommerceexport.php:11` | Plain-array encoders of the shop objects, shared by the commerce services. |
| `expCommunityBase` | abstract class extends expServiceBase | [Factory and static factory methods](factory.md), [Template Method / abstract base class](template-method.md) | `extension/expservices/classes/community/expcommunitybase.php:12` | The shared work of the forum, topic, reply and comment services: they are content services over configurable content classes ([Community] in expservi… |
| `expContentJobServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expcontentjobservices.php:15` | Content job services: ezjscore/call/expcontentjob::[::arg...] |
| `expContentServiceBase` | abstract class extends expServiceBase | [Template Method / abstract base class](template-method.md) | `extension/expservices/classes/content/expcontentbase.php:12` | The shared part of the content domains of expservices: fetch helpers with the access checks, the exporters that turn content objects into plain array… |
| `expCronjobServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expcronjobservices.php:10` | expcronjob: the cronjob parts and scripts (setup/managecronjobs), read-only. |
| `expCurrencyServices` | class extends expServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expcurrencyservices.php:11` | ezjscore/call/expcurrency::: the currencies of the shop with their rates, conversion, the preferred currency of the user. |
| `expDebugServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expdebugservices.php:10` | expdebug: the summary of the request's debug output (as the Exp Debug bar), settings read-only. |
| `expDesignServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expdesignservices.php:12` | ezjscore/call/expdesign:: - designs, templates and template overrides (read only, policy setup/administrate): which designs exist and are in use, the… |
| `expDiscountServices` | class extends expServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expdiscountservices.php:12` | ezjscore/call/expdiscount::: discount groups (rules), their rules (percent with class, section or product limits) and the users and user groups that… |
| `expEditorServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expeditorservices.php:12` | expeditor: the online editor (ezoe) for remote clients: the registered editor engines, the engine of the current user, the file types the editor's up… |
| `expExtensionServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expextensionservices.php:10` | expextension: the extensions of the installation, read-only. |
| `expFeedServices` | class extends expServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/expfeedservices.php:13` | ezjscore/call/expfeed::: feeds. |
| `expFileServices` | class extends expAttrServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/media/expfileservices.php:11` | ezjscore/call/expfile:: - binary files: information, download URLs (content/download, which checks content/read itself), signed time limited links, l… |
| `expForumServices` | class extends expCommunityBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/expforumservices.php:12` | ezjscore/call/expforum::: forums (content objects of the forum class, kept in forum containers). |
| `expImageServices` | class extends expAttrServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/media/expimageservices.php:11` | ezjscore/call/expimage:: - images and their aliases (image.ini), per node and attribute. |
| `expInfoCollectionServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/expinfocollectionservices.php:13` | ezjscore/call/expinfocollection::: collected forms (information collection): the forms that have collections, the collections and their values, expor… |
| `expIniServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expiniservices.php:11` | expini: reads the settings (INI files), read-only; secrets are masked as exp:ini masks them. |
| `expLanguageServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/explanguageservices.php:12` | ezjscore/call/explanguage:: - content languages, locales and translation files: the languages of the installation, the priority list of this siteacce… |
| `expLayoutServices` | class extends expServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/explayoutservices.php:12` | ezjscore/call/explayout:: - the layouts of the explayouts extension: layouts, zones, blocks, rules, the layout resolved for a node, and drafts (creat… |
| `expLocationServices` | class extends expContentServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/explocationservices.php:13` | Location services: ezjscore/call/explocation::[::arg...] |
| `expMediaServices` | class extends expAttrServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/media/expmediaservices.php:11` | ezjscore/call/expmedia:: - audio and video media files (the ezmedia datatype): player settings, file information, URLs, listings and statistics. |
| `expNewsletterServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expnewsletterservices.php:13` | ezjscore/call/expnewsletter:: - the cjw_newsletter extension: lists, editions, subscriber counts, and a user's own subscriptions (subscribe and unsub… |
| `expNodeServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expnodeservices.php:15` | Node services: ezjscore/call/expnode::[::arg...] |
| `expNotificationServices` | class extends expUsersBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expnotificationservices.php:12` | expnotification: the notification subscriptions of the current user (subtree rules, the collaboration types, the digest settings) and the notificatio… |
| `expObjectServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expobjectservices.php:14` | Object services: ezjscore/call/expobject::[::arg...] |
| `expOrderServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/exporderservices.php:12` | ezjscore/call/exporder::: the orders. |
| `expPackageServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/exppackageservices.php:10` | exppackage: the packages (package/list, package/read), read-only. |
| `expPaymentServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/exppaymentservices.php:13` | ezjscore/call/exppayment::: the payments of the shop: the payment objects the gateways create, their status by order, the gateways and payment workfl… |
| `expPdfServices` | class extends expAttrServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/exppdfservices.php:12` | ezjscore/call/exppdf:: - PDF output: whether a node can be exported (content/pdf policy), the link that produces the PDF of a node, and the PDF expor… |
| `expPolicyServices` | class extends expUsersBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/exppolicyservices.php:11` | exppolicy: policies of roles and their limitations, and the modules and functions a policy can name. |
| `expPollServices` | class extends expCommunityBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/exppollservices.php:12` | ezjscore/call/exppoll::: polls (content objects of the poll class with an option attribute that collects information): list, results, vote. |
| `expPreferencesServices` | class extends expUsersBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/exppreferencesservices.php:11` | exppreferences: the preferences of the current user (the key/value store the admin uses for the tree state, page sizes, ...). |
| `expProductServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expproductservices.php:11` | ezjscore/call/expproduct::: the products of the shop (content objects whose class has a price attribute): lists, search, one product with its prices… |
| `expRadServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expradservices.php:10` | exprad: the extension point survey (setup/rad), summary level, read-only. |
| `expRelationServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/exprelationservices.php:13` | Relation services: ezjscore/call/exprelation::[::arg...] |
| `expReplyServices` | class extends expCommunityBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/expreplyservices.php:11` | ezjscore/call/expreply::: the replies of a topic (content objects of the reply class below a topic). |
| `expRoleServices` | class extends expUsersBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/exproleservices.php:13` | exprole: roles, their editing drafts and their assignments to users and groups. |
| `expSearchServices` | class extends expContentServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expsearchservices.php:14` | Search services: ezjscore/call/expsearch::[::arg...] |
| `expSectionServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expsectionservices.php:13` | Section services: ezjscore/call/expsection::[::arg...] |
| `expServiceBase` | abstract class extends ezjscServerFunctions | [Template Method / abstract base class](template-method.md) | `extension/expservices/classes/expservicebase.php:38` | – |
| `expServiceException` | class extends Exception | [Exception hierarchy](exception.md) | `extension/expservices/classes/expservicebase.php:20` | The base of every expservices domain class: one public static method per service, called as ezjscore/call/exp::[::arg...]. |
| `expServicesAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expservices/classes/expservicesauditbranch.php:10` | The audit taxonomy branch of expservices: personal API tokens. |
| `expServicesCatalog` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expservicescatalog.php:12` | ezjscore/call/expservices::catalog \| schema:: \| version \| domains \| service:::: The discovery of the API: built from the $services of every class reg… |
| `expservicesInfo` | class | [Value object / struct](value-object.md) | `extension/expservices/ezinfo.php:7` | – |
| `expServiceToken` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expservices/classes/expservicetoken.php:11` | A personal API token of a user for the remote services (table expservices_token). |
| `expSessionAdminServices` | class extends expUsersBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expsessionadminservices.php:12` | expsessionadmin: the sessions of the installation. |
| `expSessionServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expsessionservices.php:11` | expsession: the session login of remote apps. |
| `expShippingServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expshippingservices.php:11` | ezjscore/call/expshipping::: what the shop knows about shipping: the handler, the simple shipping workflow cost, the shipping info of a basket or an… |
| `expSitemapServices` | class extends expAttrServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expsitemapservices.php:12` | ezjscore/call/expsitemap:: - sitemap data: the entries of a subtree as a sitemap lists them (location, last modification, change frequency, priority)… |
| `expStateServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expstateservices.php:14` | Object state services: ezjscore/call/expstate::[::arg...] |
| `expStatsServices` | class extends expAttrServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expstatsservices.php:11` | ezjscore/call/expstats:: - statistics: search phrases, content counts, growth, languages, users and sessions. |
| `expSubitemsServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expsubitemsservices.php:13` | ezjscore/call/expsubitems:: - the subitems column catalogue and rows (the admin's "Table options"), reusing expSubitemsColumnRegistry, expSubitemsSer… |
| `expSystemServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expsystemservices.php:10` | expsystem: what the installation is and runs on. |
| `expTagsServices` | class extends expAttrServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/media/exptagsservices.php:13` | ezjscore/call/exptags:: - the tag tree of the eztags extension: tree, search, children, synonyms, translations, related content, and writes (add, ren… |
| `expTopicServices` | class extends expCommunityBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/community/exptopicservices.php:12` | ezjscore/call/exptopic::: the topics of a forum (content objects of the topic class). |
| `expTranslationServices` | class extends expContentServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/exptranslationservices.php:14` | Translation services: ezjscore/call/exptranslation::[::arg...] |
| `expTrashServices` | class extends expContentServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/exptrashservices.php:13` | Trash services: ezjscore/call/exptrash::[::arg...] |
| `expUrlAliasServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expurlaliasservices.php:14` | URL alias and wildcard services: ezjscore/call/expurlalias::[::arg...] |
| `expUrlServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/misc/expurlservices.php:12` | ezjscore/call/expurl:: - the external links stored by URL attributes (ezurl): lists, search, validity, the objects that use a link, and a check of a… |
| `expUserGroupServices` | class extends expUsersBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expusergroupservices.php:10` | expusergroup: the user group tree and its members. |
| `expUsersBase` | abstract class extends expServiceBase | [Template Method / abstract base class](template-method.md) | `extension/expservices/classes/users/expusersbase.php:21` | The shared part of the users and access domains of expservices: the compact declaration format and the helpers that encode users and groups without e… |
| `expUserServices` | class extends expUsersBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/users/expuserservices.php:13` | expuser: look up, list, search and administer users. |
| `expVatServices` | class extends expServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expvatservices.php:13` | ezjscore/call/expvat::: VAT types, VAT charging rules by country and product category, the product categories the rules use, and the VAT of a product… |
| `expVelocityServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expvelocityservices.php:10` | expvelocity: the Velocity server's state, read-only (setup/system_info). |
| `expVersionServices` | class extends expContentServiceBase | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/content/expversionservices.php:13` | Version services: ezjscore/call/expversion::[::arg...] |
| `expWishlistServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/commerce/expwishlistservices.php:11` | ezjscore/call/expwishlist::: the wish list of the logged-in user, and for shop administrators the wish lists of all users. |
| `expWorkflowServices` | class extends expServiceBase | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expservices/classes/expworkflowservices.php:10` | expworkflow: workflows, their events, triggers and processes, read-only (setup/administrate). |

## Event hooks (12)

| Event | Kind | Called in |
|---|---|---|
| `content/class/cache` | notify | `extension/expservices/classes/content/expclassservices.php:530` |
| `content/section/cache` | notify | `extension/expservices/classes/content/expsectionservices.php:196` |
| `content/section/cache` | notify | `extension/expservices/classes/content/expsectionservices.php:208` |
| `content/state/group/cache` | notify | `extension/expservices/classes/content/expstateservices.php:230` |
| `content/state/group/cache` | notify | `extension/expservices/classes/content/expstateservices.php:242` |
| `content/state/group/cache` | notify | `extension/expservices/classes/content/expstateservices.php:253` |
| `content/state/cache` | notify | `extension/expservices/classes/content/expstateservices.php:266` |
| `content/state/cache` | notify | `extension/expservices/classes/content/expstateservices.php:280` |
| `content/state/cache` | notify | `extension/expservices/classes/content/expstateservices.php:294` |
| `content/cache` | notify | `extension/expservices/classes/content/expurlaliasservices.php:282` |
| `content/cache` | notify | `extension/expservices/classes/content/expurlaliasservices.php:303` |
| `content/cache` | notify | `extension/expservices/classes/content/expurlaliasservices.php:324` |

## INI plugin points (69)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[expservices]` | `expServicesAuditBranch` | `extension/expservices/settings/audit.ini.append.php:5` |
| `[ezjscServer_expservices] Class` | `expServicesCatalog` | `extension/expservices/settings/ezjscore.ini.append.php:7` |
| `[ezjscServer_expsession] Class` | `expSessionServices` | `extension/expservices/settings/ezjscore.ini.append.php:10` |
| `[ezjscServer_expsystem] Class` | `expSystemServices` | `extension/expservices/settings/ezjscore.ini.append.php:13` |
| `[ezjscServer_expini] Class` | `expIniServices` | `extension/expservices/settings/ezjscore.ini.append.php:16` |
| `[ezjscServer_expcache] Class` | `expCacheServices` | `extension/expservices/settings/ezjscore.ini.append.php:19` |
| `[ezjscServer_expcronjob] Class` | `expCronjobServices` | `extension/expservices/settings/ezjscore.ini.append.php:22` |
| `[ezjscServer_expextension] Class` | `expExtensionServices` | `extension/expservices/settings/ezjscore.ini.append.php:25` |
| `[ezjscServer_exppackage] Class` | `expPackageServices` | `extension/expservices/settings/ezjscore.ini.append.php:28` |
| `[ezjscServer_expworkflow] Class` | `expWorkflowServices` | `extension/expservices/settings/ezjscore.ini.append.php:31` |
| `[ezjscServer_expvelocity] Class` | `expVelocityServices` | `extension/expservices/settings/ezjscore.ini.append.php:34` |
| `[ezjscServer_exprad] Class` | `expRadServices` | `extension/expservices/settings/ezjscore.ini.append.php:37` |
| `[ezjscServer_expdebug] Class` | `expDebugServices` | `extension/expservices/settings/ezjscore.ini.append.php:40` |
| `[ezjscServer_expuser] Class` | `expUserServices` | `extension/expservices/settings/ezjscore.ini.append.php:44` |
| `[ezjscServer_expusergroup] Class` | `expUserGroupServices` | `extension/expservices/settings/ezjscore.ini.append.php:47` |
| `[ezjscServer_exprole] Class` | `expRoleServices` | `extension/expservices/settings/ezjscore.ini.append.php:50` |
| `[ezjscServer_exppolicy] Class` | `expPolicyServices` | `extension/expservices/settings/ezjscore.ini.append.php:53` |
| `[ezjscServer_expsessionadmin] Class` | `expSessionAdminServices` | `extension/expservices/settings/ezjscore.ini.append.php:56` |
| `[ezjscServer_exppreferences] Class` | `expPreferencesServices` | `extension/expservices/settings/ezjscore.ini.append.php:59` |
| `[ezjscServer_expnotification] Class` | `expNotificationServices` | `extension/expservices/settings/ezjscore.ini.append.php:62` |
| `[ezjscServer_expcollaboration] Class` | `expCollaborationServices` | `extension/expservices/settings/ezjscore.ini.append.php:65` |
| `[ezjscServer_expbookmark] Class` | `expBookmarkServices` | `extension/expservices/settings/ezjscore.ini.append.php:68` |
| `[ezjscServer_expaccount] Class` | `expAccountServices` | `extension/expservices/settings/ezjscore.ini.append.php:71` |
| `[ezjscServer_expimage] Class` | `expImageServices` | `extension/expservices/settings/ezjscore.ini.append.php:75` |
| `[ezjscServer_expfile] Class` | `expFileServices` | `extension/expservices/settings/ezjscore.ini.append.php:78` |
| `[ezjscServer_expmedia] Class` | `expMediaServices` | `extension/expservices/settings/ezjscore.ini.append.php:81` |
| `[ezjscServer_exptags] Class` | `expTagsServices` | `extension/expservices/settings/ezjscore.ini.append.php:84` |
| `[ezjscServer_explayout] Class` | `expLayoutServices` | `extension/expservices/settings/ezjscore.ini.append.php:87` |
| `[ezjscServer_expaudit] Class` | `expAuditServices` | `extension/expservices/settings/ezjscore.ini.append.php:90` |
| `[ezjscServer_expsubitems_svc] Class` | `expSubitemsServices` | `extension/expservices/settings/ezjscore.ini.append.php:93` |
| `[ezjscServer_expnewsletter] Class` | `expNewsletterServices` | `extension/expservices/settings/ezjscore.ini.append.php:96` |
| `[ezjscServer_expsitemap] Class` | `expSitemapServices` | `extension/expservices/settings/ezjscore.ini.append.php:99` |
| `[ezjscServer_expstats] Class` | `expStatsServices` | `extension/expservices/settings/ezjscore.ini.append.php:102` |
| `[ezjscServer_expdesign] Class` | `expDesignServices` | `extension/expservices/settings/ezjscore.ini.append.php:105` |
| `[ezjscServer_explanguage] Class` | `expLanguageServices` | `extension/expservices/settings/ezjscore.ini.append.php:108` |
| `[ezjscServer_expurl] Class` | `expUrlServices` | `extension/expservices/settings/ezjscore.ini.append.php:111` |
| `[ezjscServer_exppdf] Class` | `expPdfServices` | `extension/expservices/settings/ezjscore.ini.append.php:114` |
| `[ezjscServer_expproduct] Class` | `expProductServices` | `extension/expservices/settings/ezjscore.ini.append.php:118` |
| `[ezjscServer_expbasket] Class` | `expBasketServices` | `extension/expservices/settings/ezjscore.ini.append.php:121` |
| `[ezjscServer_exporder] Class` | `expOrderServices` | `extension/expservices/settings/ezjscore.ini.append.php:124` |
| `[ezjscServer_expvat] Class` | `expVatServices` | `extension/expservices/settings/ezjscore.ini.append.php:127` |
| `[ezjscServer_expcurrency] Class` | `expCurrencyServices` | `extension/expservices/settings/ezjscore.ini.append.php:130` |
| `[ezjscServer_expdiscount] Class` | `expDiscountServices` | `extension/expservices/settings/ezjscore.ini.append.php:133` |
| `[ezjscServer_expwishlist] Class` | `expWishlistServices` | `extension/expservices/settings/ezjscore.ini.append.php:136` |
| `[ezjscServer_expshipping] Class` | `expShippingServices` | `extension/expservices/settings/ezjscore.ini.append.php:139` |
| `[ezjscServer_exppayment] Class` | `expPaymentServices` | `extension/expservices/settings/ezjscore.ini.append.php:142` |
| `[ezjscServer_expinfocollection] Class` | `expInfoCollectionServices` | `extension/expservices/settings/ezjscore.ini.append.php:145` |
| `[ezjscServer_exppoll] Class` | `expPollServices` | `extension/expservices/settings/ezjscore.ini.append.php:148` |
| `[ezjscServer_expforum] Class` | `expForumServices` | `extension/expservices/settings/ezjscore.ini.append.php:151` |
| `[ezjscServer_exptopic] Class` | `expTopicServices` | `extension/expservices/settings/ezjscore.ini.append.php:154` |
| `[ezjscServer_expreply] Class` | `expReplyServices` | `extension/expservices/settings/ezjscore.ini.append.php:157` |
| `[ezjscServer_expcomment] Class` | `expCommentServices` | `extension/expservices/settings/ezjscore.ini.append.php:160` |
| `[ezjscServer_expfeed] Class` | `expFeedServices` | `extension/expservices/settings/ezjscore.ini.append.php:163` |
| `[ezjscServer_expnode] Class` | `expNodeServices` | `extension/expservices/settings/ezjscore.ini.append.php:167` |
| `[ezjscServer_expobject] Class` | `expObjectServices` | `extension/expservices/settings/ezjscore.ini.append.php:170` |
| `[ezjscServer_expclass] Class` | `expClassServices` | `extension/expservices/settings/ezjscore.ini.append.php:173` |
| `[ezjscServer_expclassgroup] Class` | `expClassGroupServices` | `extension/expservices/settings/ezjscore.ini.append.php:176` |
| `[ezjscServer_expattribute] Class` | `expAttributeServices` | `extension/expservices/settings/ezjscore.ini.append.php:179` |
| `[ezjscServer_expversion] Class` | `expVersionServices` | `extension/expservices/settings/ezjscore.ini.append.php:182` |
| `[ezjscServer_exptranslation] Class` | `expTranslationServices` | `extension/expservices/settings/ezjscore.ini.append.php:185` |
| `[ezjscServer_exprelation] Class` | `expRelationServices` | `extension/expservices/settings/ezjscore.ini.append.php:188` |
| `[ezjscServer_explocation] Class` | `expLocationServices` | `extension/expservices/settings/ezjscore.ini.append.php:191` |
| `[ezjscServer_exptrash] Class` | `expTrashServices` | `extension/expservices/settings/ezjscore.ini.append.php:194` |
| `[ezjscServer_expsection] Class` | `expSectionServices` | `extension/expservices/settings/ezjscore.ini.append.php:197` |
| `[ezjscServer_expstate] Class` | `expStateServices` | `extension/expservices/settings/ezjscore.ini.append.php:200` |
| `[ezjscServer_expurlalias] Class` | `expUrlAliasServices` | `extension/expservices/settings/ezjscore.ini.append.php:203` |
| `[ezjscServer_expsearch] Class` | `expSearchServices` | `extension/expservices/settings/ezjscore.ini.append.php:206` |
| `[ezjscServer_expcontentjob] Class` | `expContentJobServices` | `extension/expservices/settings/ezjscore.ini.append.php:209` |
| `[ezjscServer_expeditor] Class` | `expEditorServices` | `extension/expservices/settings/ezjscore.ini.append.php:212` |
