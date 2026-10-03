<?php /* #?ini charset="utf-8"?

# expservices: one block per domain class. Every service checks itself (access, POST and form token
# for writes), see expServiceBase. Call: <root>/ezjscore/call/exp<domain>::<method>[::arg...]

[ezjscServer_expservices]
Class=expServicesCatalog

[ezjscServer_expsession]
Class=expSessionServices

[ezjscServer_expsystem]
Class=expSystemServices

[ezjscServer_expini]
Class=expIniServices

[ezjscServer_expcache]
Class=expCacheServices

[ezjscServer_expcronjob]
Class=expCronjobServices

[ezjscServer_expextension]
Class=expExtensionServices

[ezjscServer_exppackage]
Class=expPackageServices

[ezjscServer_expworkflow]
Class=expWorkflowServices

[ezjscServer_expvelocity]
Class=expVelocityServices

[ezjscServer_exprad]
Class=expRadServices

[ezjscServer_expdebug]
Class=expDebugServices

# users and access
[ezjscServer_expuser]
Class=expUserServices

[ezjscServer_expusergroup]
Class=expUserGroupServices

[ezjscServer_exprole]
Class=expRoleServices

[ezjscServer_exppolicy]
Class=expPolicyServices

[ezjscServer_expsessionadmin]
Class=expSessionAdminServices

[ezjscServer_exppreferences]
Class=expPreferencesServices

[ezjscServer_expnotification]
Class=expNotificationServices

[ezjscServer_expcollaboration]
Class=expCollaborationServices

[ezjscServer_expaccount]
Class=expAccountServices

# media and other (A5)
[ezjscServer_expimage]
Class=expImageServices

[ezjscServer_expfile]
Class=expFileServices

[ezjscServer_expmedia]
Class=expMediaServices

[ezjscServer_exptags]
Class=expTagsServices

[ezjscServer_explayout]
Class=expLayoutServices

[ezjscServer_expaudit]
Class=expAuditServices

[ezjscServer_expsubitems_svc]
Class=expSubitemsServices

[ezjscServer_expnewsletter]
Class=expNewsletterServices

[ezjscServer_expsitemap]
Class=expSitemapServices

[ezjscServer_expstats]
Class=expStatsServices

[ezjscServer_expdesign]
Class=expDesignServices

[ezjscServer_explanguage]
Class=expLanguageServices

[ezjscServer_expurl]
Class=expUrlServices

[ezjscServer_exppdf]
Class=expPdfServices

# commerce and community (shop, forms, polls, forums, comments, feeds)
[ezjscServer_expproduct]
Class=expProductServices

[ezjscServer_expbasket]
Class=expBasketServices

[ezjscServer_exporder]
Class=expOrderServices

[ezjscServer_expvat]
Class=expVatServices

[ezjscServer_expcurrency]
Class=expCurrencyServices

[ezjscServer_expdiscount]
Class=expDiscountServices

[ezjscServer_expwishlist]
Class=expWishlistServices

[ezjscServer_expshipping]
Class=expShippingServices

[ezjscServer_exppayment]
Class=expPaymentServices

[ezjscServer_expinfocollection]
Class=expInfoCollectionServices

[ezjscServer_exppoll]
Class=expPollServices

[ezjscServer_expforum]
Class=expForumServices

[ezjscServer_exptopic]
Class=expTopicServices

[ezjscServer_expreply]
Class=expReplyServices

[ezjscServer_expcomment]
Class=expCommentServices

[ezjscServer_expfeed]
Class=expFeedServices

# content: nodes, objects, classes, versions, translations, relations, locations, trash, sections, states, URL aliases, search, jobs
[ezjscServer_expnode]
Class=expNodeServices

[ezjscServer_expobject]
Class=expObjectServices

[ezjscServer_expclass]
Class=expClassServices

[ezjscServer_expclassgroup]
Class=expClassGroupServices

[ezjscServer_expattribute]
Class=expAttributeServices

[ezjscServer_expversion]
Class=expVersionServices

[ezjscServer_exptranslation]
Class=expTranslationServices

[ezjscServer_exprelation]
Class=expRelationServices

[ezjscServer_explocation]
Class=expLocationServices

[ezjscServer_exptrash]
Class=expTrashServices

[ezjscServer_expsection]
Class=expSectionServices

[ezjscServer_expstate]
Class=expStateServices

[ezjscServer_expurlalias]
Class=expUrlAliasServices

[ezjscServer_expsearch]
Class=expSearchServices

[ezjscServer_expcontentjob]
Class=expContentJobServices

*/ ?>
