---
title: Active Record (eZPersistentObject)
audience: developer
category: standards
order: 310
summary: A class whose objects are rows of one database table: it describes the table in definition() and loads, stores and removes itself through eZPersistentObject. 234 instances in Exponential.
keywords: design pattern, active-record, data access, pattern catalogue
type: catalogue
---
# Active Record (eZPersistentObject)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A class whose objects are rows of one database table: it describes the table in definition() and loads, stores and removes itself through eZPersistentObject.

| | |
|---|---|
| Group | Data access |
| Instances | 234 |
| Recognized by | Extends eZPersistentObject, directly or through a parent class. |
| Extension point | A new table: a class that extends eZPersistentObject with a static definition(); schema in the extension's share/db_schema.dba or sql/ENGINE/schema.sql. |

## Classes by area

### extension/cjw_newsletter (38)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterAbTest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtest.php:20` | The A/B subject test of a send: samples, criterion, wait time, winner. | – |
| `CjwNewsletterAbVariant` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabvariant.php:20` | One subject variant of an A/B test and its counts. | – |
| `CjwNewsletterApproval` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapproval.php:20` | The approval state of an edition version for a list that requires one (collaboration inbox). | – |
| `CjwNewsletterArticlePool` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterarticlepool.php:20` | Where articles for editions come from (nodes, classes, sections, tags, states, age), per list or the global default. | – |
| `CjwNewsletterBlacklistItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterblacklistitem.php:17` | Handle Blacklist of NL users or emails | – |
| `CjwNewsletterCsvExport` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettercsvexport.php:17` | Export different data in csv format | – |
| `CjwNewsletterEdition` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletteredition.php:17` | Data management datatyp cjwnewsletteredition | – |
| `CjwNewsletterEditionArticle` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionarticle.php:20` | An article taken into an edition from a pool, by an editor or by the auto-fill. | – |
| `CjwNewsletterEditionSend` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsend.php:17` | Class description here | – |
| `CjwNewsletterEditionSendItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsenditem.php:20` | Mailqueue | – |
| `CjwNewsletterEditionSendOutput` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewslettereditionsendoutput.php:20` | The rendered output of a send in one language (one edition, many translations). | – |
| `CjwNewsletterImport` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterimport.php:17` | Handle Import sets of Subscriptions | – |
| `CjwNewsletterImportMapping` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportmapping.php:20` | A stored column mapping of a CSV import (column -› field), delimiter, header, consent source. | – |
| `CjwNewsletterInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterest.php:20` | An interest subscribers can pick (a topic of a list, or an eztags tag). | – |
| `CjwNewsletterLink` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlink.php:20` | A link of a send that the click redirect knows (only stored URLs are redirected to). | – |
| `CjwNewsletterLinkClick` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlinkclick.php:20` | One click on a link; per person (send item) only with consent, else the item is 0. | – |
| `CjwNewsletterList` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterlist.php:20` | Data management datatyp cjwnewsletterlist | – |
| `CjwNewsletterListVirtual` | class extends CjwNewsletterList | `extension/cjw_newsletter/classes/cjwnewsletterlistvirtual.php:20` | Data management datatyp cjwnewsletterlist | – |
| `CjwNewsletterMailbox` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettermailbox.php:17` | Connect with mailaccount and fetch/parse mailitems | – |
| `CjwNewsletterMailboxItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettermailboxitem.php:18` | Store mail items and parse | – |
| `CjwNewsletterMailinAddress` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinaddress.php:20` | An address (or a plus-address tag) of a list that takes subscribe and unsubscribe mails. | – |
| `CjwNewsletterMailinMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinmessage.php:20` | One mail that came in at a mail-in address, and what was done with it. | – |
| `CjwNewsletterMigrationLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewslettermigrationlog.php:20` | One row the eznewsletter importer read, and what it did with it (or would do, in a dry run). | – |
| `CjwNewsletterOpen` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletteropen.php:20` | One open of a send; per person only with consent. | – |
| `CjwNewsletterSchedule` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedule.php:20` | A recurring send of a list: copy a template edition or send the latest edition, when, auto-fill from a pool. | – |
| `CjwNewsletterScheduleLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulelog.php:20` | One run of a schedule: sent, skipped (empty, condition) or failed. | – |
| `CjwNewsletterSendBatch` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersendbatch.php:20` | One batch of a send: which items it took, how far it got (resumable), its counts. | – |
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
| `CjwNewsletterUtils` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterutils.php:17` | class with some useful functions | – |

### extension/enhancedselection2 (1)

[All patterns of extension/enhancedselection2](area-extension-enhancedselection2.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SckEnhancedSelection` | class extends eZPersistentObject | `extension/enhancedselection2/classes/sckenhancedselection.php:3` | – | – |

### extension/expai (14)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiBatch` | class extends eZPersistentObject | `extension/expai/classes/lanes/expaibatch.php:17` | One provider batch (table expai_batch): which provider, lane and model, the provider's id, the state and counts, when it was submitted, ended, its re… | – |
| `expAiChainRun` | class extends eZPersistentObject | `extension/expai/classes/chain/expaichainrun.php:20` | One run of an AI chain (table expai_chain_run). | – |
| `expAiChainStep` | class extends eZPersistentObject | `extension/expai/classes/chain/expaichainstep.php:19` | One step of a chain run (table expai_chain_step): each call to a model is one row with its queue job, model, lane, tokens, cost and the usage row; a… | – |
| `expAiChange` | class extends eZPersistentObject | `extension/expai/classes/vector/expaichange.php:23` | A staged proposal (table expai_change, doc/review.md): one object x language x attribute that a write tool of a thread with staged review proposed in… | – |
| `expAiCheckpoint` | class extends eZPersistentObject | `extension/expai/classes/vector/expaicheckpoint.php:28` | Checkpoints of a thread (table expai_checkpoint, doc/checkpoints.md): the state at the start of every turn of the lead run and before staged changes… | – |
| `expAiConversation` | class extends eZPersistentObject | `extension/expai/classes/expaiconversation.php:16` | The conversation of one user on one node (table expai_conversation). | – |
| `expAiDraftLog` | class extends eZPersistentObject | `extension/expai/classes/expaidraftlog.php:20` | One version the assistant wrote (table expai_draft_log): the object and version, the user it was written for, when, a summary of the request, the mod… | – |
| `expAiFollowup` | class extends eZPersistentObject | `extension/expai/classes/vector/expaifollowup.php:16` | A message queued while a run works (table expai_followup). | – |
| `expAiPlan` | class extends eZPersistentObject | `extension/expai/classes/vector/expaiplan.php:19` | A plan of a thread (table expai_plan, doc/plan-mode.md): what propose_plan stored for the editor to approve. | – |
| `expAiProbeResult` | class extends eZPersistentObject | `extension/expai/classes/expaiproberesult.php:15` | One run of bin/php/expaiprobe.php (table expai_probe_result): the model alias, provider and model id, the tool mode, live or mock, the tasks, how man… | – |
| `expAiThread` | class extends eZPersistentObject | `extension/expai/classes/vector/expaithread.php:22` | A thread (table expai_thread): one unit of work of one editor. | – |
| `expAiThreadEvent` | class extends eZPersistentObject | `extension/expai/classes/vector/expaithreadevent.php:17` | The append-only event stream of a thread (table expai_thread_event). | – |
| `expAiThreadRun` | class extends eZPersistentObject | `extension/expai/classes/vector/expaithreadrun.php:16` | One agent run of a thread (table expai_thread_run). | – |
| `expAiUsageLog` | class extends eZPersistentObject | `extension/expai/classes/expaiusagelog.php:17` | One request to the model (table expai_usage_log): when, for whom (user id, never a name), where (node, conversation, turn), what for (action), the mo… | – |

### extension/explayouts (10)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsBlock` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblock.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsLayout` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutslayout.php:2` | – | – |
| `expLayoutsRule` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrule.php:2` | – | – |
| `expLayoutsRuleCondition` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrulecondition.php:2` | – | – |
| `expLayoutsRuleTarget` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsruletarget.php:2` | – | – |
| `expLayoutsZone` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutszone.php:2` | – | – |

### extension/expqueue (3)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expQueueEvent` | class extends eZPersistentObject | `extension/expqueue/classes/expqueueevent.php:15` | The history of a job (table expqueue_event), append-only, removed with the job. | – |
| `expQueueGroup` | class extends eZPersistentObject | `extension/expqueue/classes/expqueuegroup.php:15` | A set of jobs that belong together (table expqueue_group): "translate subtree X into ten languages", a package import, a chain run. | – |
| `expQueueJob` | class extends eZPersistentObject | `extension/expqueue/classes/expqueuejob.php:25` | One job (table expqueue_job). | – |

### extension/expservices (1)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expServiceToken` | class extends eZPersistentObject | `extension/expservices/classes/expservicetoken.php:11` | A personal API token of a user for the remote services (table expservices_token). | – |

### extension/ezfind (3)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFindElevateConfiguration` | class extends eZPersistentObject | `extension/ezfind/classes/ezfindelevateconfiguration.php:8` | – | – |
| `eZFindResultNode` | class extends eZContentObjectTreeNode | `extension/ezfind/classes/ezfindresultnode.php:8` | – | – |
| `eZFindResultObject` | class extends eZContentObject | `extension/ezfind/classes/ezfindresultobject.php:8` | – | – |

### extension/ezflow (2)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFlowBlock` | class extends eZPersistentObject | `extension/ezflow/classes/ezflowblock.php:28` | – | – |
| `eZFlowPoolItem` | class extends eZPersistentObject | `extension/ezflow/classes/ezflowpoolitem.php:28` | – | – |

### extension/ezgmaplocation (1)

[All patterns of extension/ezgmaplocation](area-extension-ezgmaplocation.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZGmapLocation` | class extends eZPersistentObject | `extension/ezgmaplocation/classes/ezgmaplocation.php:40` | Helper class used along with the eZGmapLocationType datatype. | – |

### extension/ezmbpaex (2)

[All patterns of extension/ezmbpaex](area-extension-ezmbpaex.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPaEx` | class extends eZPersistentObject | `extension/ezmbpaex/classes/ezpaex.php:11` | – | – |
| `eZPaExUser` | class extends eZUser | `extension/ezmbpaex/login_handler/ezpaexuser.php:23` | Handles logins for users using new Password Expiry constraints | – |

### extension/ezpm (3)

[All patterns of extension/ezpm](area-extension-ezpm.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContact` | class extends eZPersistentObject | `extension/ezpm/classes/ezcontact.php:15` | – | – |
| `eZPm` | class extends eZPersistentObject | `extension/ezpm/classes/ezpm.php:15` | – | – |
| `eZPmBlackList` | class extends eZPersistentObject | `extension/ezpm/classes/ezpmblacklist.php:15` | – | – |

### extension/ezstarrating (3)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrRatingDataObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingdataobject.php:27` | – | – |
| `ezsrRatingObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingobject.php:27` | – | – |
| `ezsrRatingObjectTreeNode` | class extends eZContentObjectTreeNode | `extension/ezstarrating/classes/ezsrratingobjecttreenode.php:36` | Only to be used by {@link ezsrRatingObject::fetchNodeByRating()} As other fetch functions won't have the needed rating values in the returned data. | – |

### extension/eztags (3)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTagsAttributeLinkObject` | class extends eZPersistentObject | `extension/eztags/classes/eztagsattributelinkobject.php:7` | eZTagsAttributeLinkObject class inherits eZPersistentObject class to be able to access eztags_attribute_link database table through API | – |
| `eZTagsKeyword` | class extends eZPersistentObject | `extension/eztags/classes/eztagskeyword.php:7` | eZTagsKeyword class inherits eZPersistentObject class to be able to access eztags_keyword database table through API | – |
| `eZTagsObject` | class extends eZPersistentObject | `extension/eztags/classes/eztagsobject.php:9` | eZTagsObject class inherits eZPersistentObject class to be able to access eztags database table through API | – |

### extension/sevenx_authentication_2fa (8)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFacebookUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezfacebookuser.php:17` | – | – |
| `eZGoogleUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezgoogleuser.php:17` | – | – |
| `eZIdmeUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezidmeuser.php:22` | – | – |
| `eZInstagramUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezinstagramuser.php:17` | – | – |
| `eZMetaUser` | class extends eZFacebookUser | `extension/sevenx_authentication_2fa/login_handler/ezmetauser.php:17` | – | – |
| `eZOAuthUser` | abstract class extends eZUser | `extension/sevenx_authentication_2fa/classes/ezoauthuser.php:26` | – | – |
| `eZsevenxUser2faUser` | class extends eZUser | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:32` | – | – |
| `eZTwitterUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/eztwitteruser.php:17` | – | – |

### extension/sqliimport (13)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImportDefinition` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportdefinition.php:24` | An import (sqliimport_definition): where the data comes from, what processes it (the no-code mapping or a handler), where the content goes, how it be… | – |
| `expImportMapping` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportmapping.php:14` | The no-code mapping of an import (sqliimport_mapping, one row) and its lines (sqliimport_mapping_field). | – |
| `expImportMappingField` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportmapping.php:128` | One line of a mapping (sqliimport_mapping_field). | – |
| `expImportNotice` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/engine/expimportnotifier.php:218` | A notice of a run for a user (sqliimport_notice). | – |
| `expImportRun` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportrun.php:17` | One run of an import (sqliimport_run): who and what started it, its job group in expqueue, its status and counters. | – |
| `expImportRunLog` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportrun.php:259` | A line of the log of a run (sqliimport_run_log): severity, the record it is about, message, context. | – |
| `expImportRunRow` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportrun.php:217` | One record of a run (sqliimport_run_row): what was done to which object. | – |
| `expImportSchedule` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimportschedule.php:18` | When an import runs by itself (sqliimport_schedule). | – |
| `expImportTrigger` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimporttrigger.php:23` | The webhook / API trigger of an import (sqliimport_trigger), one row per import, off until it is switched on in the schedule step of the editor: | – |
| `expImportTriggerKey` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimporttrigger.php:89` | An API key of a trigger (sqliimport_trigger_key): its label, its key id (the part of the key that finds it) and the HMAC of the key (expImportTrigger… | – |
| `SQLIImportItem` | class extends eZPersistentObject | `extension/sqliimport/classes/sqliimportitem.php:15` | Class handling items Objects of this class are stored in ezpending_actions table and are normally processed by CRON to trigger an import. | – |
| `SQLIImportToken` | class extends eZPersistentObject | `extension/sqliimport/classes/sqliimporttoken.php:12` | Token class for SQLI Import Handles import status | – |
| `SQLIScheduledImport` | class extends eZPersistentObject | `extension/sqliimport/classes/sqlischeduledimport.php:11` | – | – |

### extension/syndication (11)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFilterAttribute` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfilterattribute.php:48` | – | – |
| `eZFilterSection` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfiltersection.php:47` | – | – |
| `eZSyndicationFeed` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeed.php:47` | – | – |
| `eZSyndicationFeedItem` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditem.php:48` | – | – |
| `eZSyndicationFeedItemExport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemexport.php:48` | – | – |
| `eZSyndicationFeedItemStatus` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemstatus.php:48` | – | – |
| `eZSyndicationFeedSource` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsource.php:47` | – | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfilter.php:48` | – | – |
| `eZSyndicationImport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimport.php:47` | – | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – | – |

### extension/xrowextract (3)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractDestination` | class extends eZPersistentObject | `extension/xrowextract/classes/xrowextractdestination.php:9` | A named, reusable delivery destination (table xrowextract_destination). | – |
| `XrowExtractHistory` | class extends eZPersistentObject | `extension/xrowextract/classes/xrowextracthistory.php:12` | The export history index (table xrowextract_history): one row per run - a background job (started by hand, from the command line or by a schedule) or… | – |
| `XrowExtractSchedule` | class extends eZPersistentObject | `extension/xrowextract/classes/xrowextractschedule.php:20` | A scheduled export or import (table xrowextract_schedule, created lazily by XrowExtractSchema). | – |

### kernel/classes (66)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expBookmarkView` | class extends eZPersistentObject | `kernel/classes/expbookmarkview.php:39` | A bookmark of a system page: a page of a module view (setup/systemupgrade, exphelp/dashboard, audit/console/(user)/14 ...) instead of a node. | – |
| `eZBasket` | class extends eZPersistentObject | `kernel/classes/ezbasket.php:20` | – | – |
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
| `eZContentClassName` | class extends eZPersistentObject | `kernel/classes/ezcontentclassname.php:12` | – | – |
| `eZContentLanguage` | class extends eZPersistentObject | `kernel/classes/ezcontentlanguage.php:12` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObject` | class extends eZPersistentObject | `kernel/classes/ezcontentobject.php:17` | Encapsulates data about and methods to work with content objects | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectattribute.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectTrashNode` | class extends eZContentObjectTreeNode | `kernel/classes/ezcontentobjecttrashnode.php:15` | Encapsulates data about and methods to work with content objects which reside in the trash | – |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | `kernel/classes/ezcontentobjecttreenode.php:17` | Encapsulates data about and methods to work with content objects inside the content object tree | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectVersion` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectversion.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountrule.php:18` | – | – |
| `eZDiscountSubRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrule.php:18` | – | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrulevalue.php:17` | – | – |
| `eZInformationCollection` | class extends eZPersistentObject | `kernel/classes/ezinformationcollection.php:22` | – | – |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | `kernel/classes/ezinformationcollectionattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZNodeAssignment` | class extends eZPersistentObject | `kernel/classes/eznodeassignment.php:17` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZOrder` | class extends eZPersistentObject | `kernel/classes/ezorder.php:20` | – | – |
| `eZOrderItem` | class extends eZPersistentObject | `kernel/classes/ezorderitem.php:27` | – | – |
| `eZOrderStatus` | class extends eZPersistentObject | `kernel/classes/ezorderstatus.php:47` | – | – |
| `eZOrderStatusHistory` | class extends eZPersistentObject | `kernel/classes/ezorderstatushistory.php:35` | – | – |
| `eZPathElement` | class extends eZPersistentObject | `kernel/classes/ezpathelement.php:23` | – | – |
| `eZPDFExport` | class extends eZPersistentObject | `kernel/classes/ezpdfexport.php:19` | – | – |
| `eZPendingActions` | class extends eZPersistentObject | `kernel/classes/ezpendingactions.php:12` | – | – |
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
| `eZSection` | class extends eZPersistentObject | `kernel/classes/ezsection.php:18` | – | – |
| `eZSiteData` | class extends eZPersistentObject | `kernel/classes/ezsitedata.php:21` | eZPersistentObject implementation for ezsite_data table. | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | `kernel/classes/eztipafriendcounter.php:18` | – | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | `kernel/classes/eztipafriendrequest.php:17` | – | – |
| `eZTrigger` | class extends eZPersistentObject | `kernel/classes/eztrigger.php:17` | – | – |
| `eZURLAliasML` | class extends eZPersistentObject | `kernel/classes/ezurlaliasml.php:58` | – | – |
| `eZURLWildcard` | class extends eZPersistentObject | `kernel/classes/ezurlwildcard.php:19` | – | – |
| `eZUserDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezuserdiscountrule.php:18` | – | – |
| `eZVatRule` | class extends eZPersistentObject | `kernel/classes/ezvatrule.php:18` | – | – |
| `eZVatType` | class extends eZPersistentObject | `kernel/classes/ezvattype.php:19` | – | – |
| `eZViewCounter` | class extends eZPersistentObject | `kernel/classes/ezviewcounter.php:12` | – | – |
| `eZWishList` | class extends eZPersistentObject | `kernel/classes/ezwishlist.php:20` | – | – |
| `eZWorkflow` | class extends eZPersistentObject | `kernel/classes/ezworkflow.php:18` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZWorkflowEvent` | class extends eZPersistentObject | `kernel/classes/ezworkflowevent.php:18` | – | – |
| `eZWorkflowGroup` | class extends eZPersistentObject | `kernel/classes/ezworkflowgroup.php:18` | – | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | `kernel/classes/ezworkflowgrouplink.php:18` | – | – |
| `eZWorkflowProcess` | class extends eZPersistentObject | `kernel/classes/ezworkflowprocess.php:19` | – | – |

### kernel/classes/apikey (1)

[All patterns of kernel/classes/apikey](area-kernel-classes-apikey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKey` | class extends eZPersistentObject | `kernel/classes/apikey/expapikey.php:31` | A personal API key (publishing key) of a user: one row of expapikey (doc/eng-us/guides/api-keys.md). | – |

### kernel/classes/datatable (4)

[All patterns of kernel/classes/datatable](area-kernel-classes-datatable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDataConnection` | class extends eZPersistentObject | `kernel/classes/datatable/expdataconnection.php:19` | A named database connection made in the administration (Setup › Data tables › Connections): a row of exp_data_connection. | – |
| `expDataSyncConflict` | class extends eZPersistentObject | `kernel/classes/datatable/sync/expdatasyncdefinition.php:140` | A conflict of a two-way synchronisation with the rule "ask" (table exp_data_sync_conflict): both sides changed since the last synchronisation; the va… | – |
| `expDataSyncDefinition` | class extends eZPersistentObject | `kernel/classes/datatable/sync/expdatasyncdefinition.php:19` | A synchronisation between content and a data table (table exp_data_sync; doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): the persistent c… | – |
| `expDataSyncLink` | class extends eZPersistentObject | `kernel/classes/datatable/sync/expdatasyncdefinition.php:104` | The pairing of a row and a content object of a synchronisation (table exp_data_sync_link): the row's key, the object, and the hashes and times of bot… | – |

### kernel/classes/datatypes (16)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZBinaryFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php:19` | – | – |
| `eZEnumObjectValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumobjectvalue.php:18` | – | – |
| `eZEnumValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumvalue.php:19` | – | – |
| `eZForgotPassword` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezforgotpassword.php:19` | – | – |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZISBNGroup` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – | – |
| `eZLDAPUser` | class extends eZUser | `kernel/classes/datatypes/ezuser/ezldapuser.php:20` | – | – |
| `eZMedia` | class extends eZPersistentObject | `kernel/classes/datatypes/ezmedia/ezmedia.php:19` | – | – |
| `eZTextFileUser` | class extends eZUser | `kernel/classes/datatypes/ezuser/eztextfileuser.php:27` | – | – |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZURLObjectLink` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – | – |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUserAccountKey` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – | – |
| `eZUserSetting` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezusersetting.php:18` | – | – |

### kernel/classes/mailpreferences (5)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expConsentLog` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expconsentlog.php:22` | The consent log (table expmail_consent_log): one row per change of a person's e-mail preferences, with the time, the category, the action (on, off, p… | – |
| `expMailCategoryRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailcategoryrow.php:15` | A category made or changed in the admin's category manager (table expmail_category). | – |
| `expMailPendingRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailpendingrow.php:14` | A double opt-in waiting for its confirmation (table expmail_pending): kind 'category' (a newsletter or marketing category switched on), 'email_change… | – |
| `expMailPreferenceRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailpreferencerow.php:14` | One stored preference (table expmail_preference): a recipient (recipient_key 'u:' or 'a:'), a category, its state (on, off, pending) and frequency. | – |
| `expMailSuppression` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailsuppression.php:22` | The suppression list (table expmail_suppression): addresses no optional mail goes to (hard bounces, complaints, "unsubscribe from all", legal request… | – |

### kernel/classes/notification (6)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCollaborationNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php:17` | – | – |
| `eZGeneralDigestUserSettings` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigestusersettings.php:18` | – | – |
| `eZNotificationCollection` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollection.php:17` | – | – |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – | – |
| `eZNotificationEvent` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationevent.php:17` | – | – |
| `eZSubtreeNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php:17` | – | – |

### kernel/classes/passkey (2)

[All patterns of kernel/classes/passkey](area-kernel-classes-passkey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPasskey` | class extends eZPersistentObject | `kernel/classes/passkey/exppasskey.php:22` | A passkey of a user: one row of exppasskey (doc/eng-us/guides/passkeys.md). | – |
| `expPasskeyLogin` | class extends eZUser | `kernel/classes/passkey/exppasskeylogin.php:16` | Signs in an account whose passkey expPasskeyService checked, through the same housekeeping as every password sign-in (eZUser::loginSucceeded(): last… | – |

### kernel/classes/secrets (1)

[All patterns of kernel/classes/secrets](area-kernel-classes-secrets.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSecret` | class extends eZPersistentObject | `kernel/classes/secrets/expsecret.php:18` | One secret of the kernel's secret store (table exp_secret): its name, the tool it belongs to (scope), a description, and its value encrypted with XCh… | – |

### kernel/classes/subitems (2)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsSearchWordLinkRow` | class extends eZPersistentObject | `kernel/classes/subitems/columns/expsubitemscolumntables.php:31` | A row of ezsearch_object_word_link: one indexed word position of an object. | – |
| `expSubitemsUserVisitRow` | class extends eZPersistentObject | `kernel/classes/subitems/columns/expsubitemscolumntables.php:15` | A row of ezuservisit: a user's visits, logins and failed logins. | – |

### kernel/classes/transfer (1)

[All patterns of kernel/classes/transfer](area-kernel-classes-transfer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expKernelTransferRun` | class extends eZPersistentObject | `kernel/classes/transfer/expkerneltransferrun.php:26` | One run of an import or export tool: a row of exp_transfer_run (doc/eng-us/features/6.0/transfer-runs.md). | – |

### kernel/classes/user (1)

[All patterns of kernel/classes/user](area-kernel-classes-user.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUserSession` | class extends eZPersistentObject | `kernel/classes/user/expusersession.php:22` | One sign-in of a user (table expuser_session): when it started and was last used, from where (the address as the request gave it), with what (the Use… | – |

### kernel/classes/workflowtypes (1)

[All patterns of kernel/classes/workflowtypes](area-kernel-classes-workflowtypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZWaitUntilDateValue` | class extends eZPersistentObject | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatevalue.php:18` | – | – |

### kernel/modules (3)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCurrencyData` | class extends eZPersistentObject | `kernel/shop/classes/ezcurrencydata.php:12` | – | – |
| `eZMultiPriceData` | class extends eZPersistentObject | `kernel/shop/classes/ezmultipricedata.php:12` | – | – |
| `eZPaymentObject` | class extends eZPersistentObject | `kernel/shop/classes/ezpaymentobject.php:22` | – | – |

### kernel/private (5)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentObjectState` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstate.php:18` | Class representing a content object state | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroup` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategroup.php:18` | Class respresenting a content object state group | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroupLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategrouplanguage.php:21` | This class takes care of the localization of a content object state group. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstatelanguage.php:21` | This class takes care of the localization of a content object state. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `ezpContentPublishingProcess` | class extends eZPersistentObject | `kernel/private/classes/ezpcontentpublishingprocess.php:18` | This class provides a PersistentObject interface to a publishing queue process. | – |

### lib/ezutils (1)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZOperationMemento` | class extends eZPersistentObject | `lib/ezutils/classes/ezoperationmemento.php:18` | – | – |
