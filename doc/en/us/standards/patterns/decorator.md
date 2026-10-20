---
title: Decorator / filter
audience: developer
category: standards
order: 331
summary: Wraps an object or a value to add behavior while keeping its interface: output filters, input filters, pre-/post-processors. 62 instances in Exponential.
keywords: design pattern, decorator, structure, pattern catalogue
type: catalogue
---
# Decorator / filter

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Wraps an object or a value to add behavior while keeping its interface: output filters, input filters, pre-/post-processors.

| | |
|---|---|
| Group | Structure |
| Instances | 62 |
| Recognized by | Class name contains Decorator or Filter. |

## Classes by area

### extension/cjw_newsletter (6)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterEditionFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewslettereditionfilter.php:32` | This filter allowed, fetch all lists by a specific status status-variants: -›DRAFT -›PROCESS | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterEditionFilter] ClassName |
| `CjwNewsletterFilter` | class | `extension/cjw_newsletter/classes/cjwnewsletterfilter.php:17` | Base Class to manage Filter | – |
| `CjwNewsletterFilterType` | class | `extension/cjw_newsletter/classes/cjwnewsletterfiltertype.php:17` | Base Class to manage Filter | – |
| `CjwNewsletterFilterTypeEmail` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypeemail.php:19` | Class description here | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterFilterTypeSalutation` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypesalutation.php:19` | Class description here | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterListFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewsletterlistfilter.php:29` | This filter allowed, fetch all lists by a specific siteaccess reserved word "current_siteaccess" == take current siteaccess to match param siteaccess… | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterListFilter] ClassName |

### extension/expsite_api (1)

[All patterns of extension/expsite_api](area-extension-expsite-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSiteApiFilterService` | class | `extension/expsite_api/classes/expsiteapifilterservice.php:2` | – | – |

### extension/ezfind (4)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFindAllWordsExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindallwordsextendedattributefilter.php:25` | eZFindAllWordsExtendedAttributeFilter | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[ezfindallwords] |
| `eZFindExtendedAttributeFilterFactory` | class | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Extended attribute filters factory | – |
| `eZFindExtendedAttributeFilterInterface` | interface | `extension/ezfind/classes/ezfindextendedattributefilterinterface.php:10` | Extended attribute filter interface | – |
| `eZFindGeoDistExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindgeodistextendedattributefilter.php:28` | SolR Geodist extended attribute filter | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[geodist] |

### extension/ezgmaplocation (1)

[All patterns of extension/ezgmaplocation](area-extension-ezgmaplocation.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezgmlLocationFilter` | class | `extension/ezgmaplocation/classes/ezgmllocationfilter.php:32` | – | extension/ezgmaplocation/settings/extendedattributefilter.ini [ezgmlLocationFilter] ClassName |

### extension/ezie (8)

[All patterns of extension/ezie](area-extension-ezie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezie\Ezie\FilterBrightness` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_brightness.php:23` | The code of extension/ezie/modules/ezie/filter_brightness.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterBw` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_bw.php:23` | The code of extension/ezie/modules/ezie/filter_bw.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterContrast` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_contrast.php:23` | The code of extension/ezie/modules/ezie/filter_contrast.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterSepia` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_sepia.php:23` | The code of extension/ezie/modules/ezie/filter_sepia.php, moved into a class (#207 stage 1). | – |
| `eZIEImageFilterBrightness` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_brightness.php:11` | – | – |
| `eZIEImageFilterBW` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_bw.php:12` | – | – |
| `eZIEImageFilterContrast` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_contrast.php:11` | – | – |
| `eZIEImageFilterSepia` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_sepia.php:11` | – | – |

### extension/ezprestapi (1)

[All patterns of extension/ezprestapi](area-extension-ezprestapi.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRestAtomDecorator` | class extends ezpRestFeedDecorator | `extension/ezprestapi/classes/views/atom_decorator.php:10` | – | – |

### extension/ezstarrating (1)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrRatingFilter` | class | `extension/ezstarrating/classes/ezsrratingfilter.php:32` | – | extension/ezstarrating/settings/extendedattributefilter.ini [ezsrRatingFilter] ClassName |

### extension/eztags (2)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTagsAttributeFilter` | class | `extension/eztags/classes/eztagsattributefilter.php:6` | eZTagsAttributeFilter class implements TagsAttributeFilter extended attribute | extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndMultipleFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeFilter] ClassName |
| `eZTagsTreeAttributeFilter` | class | `extension/eztags/classes/eztagstreeattributefilter.php:6` | eZTagsTreeAttributeFilter class implements TagsTreeAttributeFilter extended attribute | extension/eztags/settings/extendedattributefilter.ini [TagsTreeAttributeFilter] ClassName |

### extension/syndication (6)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFilterAttribute` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfilterattribute.php:48` | – | – |
| `eZFilterSection` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfiltersection.php:47` | – | – |
| `eZImportEditFilter` | class extends eZImportEditWizard | `extension/syndication/modules/syndication/import_edit/ezimporteditfilter.php:47` | – | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfilter.php:48` | – | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – | – |

### extension/xrowextract (5)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractFilters` | class | `extension/xrowextract/classes/xrowextractfilters.php:16` | Filters of an export: a date range (published, modified, or a date attribute of the class; any, today, the last N days, since, before, between, futur… | – |
| `XrowExtractHasChildrenFilter` | class | `extension/xrowextract/classes/xrowextracthaschildrenfilter.php:11` | Extended attribute filter "XrowExtractHasChildren": only objects whose node has at least one child (leaf content otherwise). | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractHasChildren] ClassName |
| `XrowExtractRelationFilter` | class | `extension/xrowextract/classes/xrowextractrelationfilter.php:17` | Extended attribute filter "XrowExtractRelation": only objects that relate to a given object (relation_type ignored — any relation counts, including o… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractRelation] ClassName |
| `XrowExtractTranslationFilter` | class | `extension/xrowextract/classes/xrowextracttranslationfilter.php:24` | Extended attribute filter "XrowExtractTranslation": only objects that have a translation in the given language. | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractTranslation] ClassName |
| `XrowExtractUserStatusFilter` | class | `extension/xrowextract/classes/xrowextractuserstatusfilter.php:12` | Extended attribute filter "XrowExtractUserStatus": only user objects that are enabled, or only ones that are disabled, via the standard `ezuser_setti… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractUserStatus] ClassName |

### kernel/classes (2)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ExpInstallationDetailsOutputFilter` | class | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Installation name output filter. | – |
| `eZURLAliasFilter` | abstract class | `kernel/classes/ezurlaliasfilter.php:22` | The eZURLAliasFilter class the interface for all url alias filters, the filter implementation must implement the process method. | – |

### kernel/classes/notification (1)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZNotificationEventFilter` | class | `kernel/classes/notification/eznotificationeventfilter.php:17` | – | – |

### kernel/private (21)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKeyAuthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/auth/apikey_auth_filter.php:19` | Authentication filter for a personal API key (expApiKey) presented as "Authorization: Bearer expk_..." (doc/en/us/guides/api-keys.md, "How the REST layer r… | – |
| `expRestPasswordAuthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/auth/password_auth_filter.php:19` | Checks a login and password for the REST interface's basic authentication the way the sign-in does. | – |
| `ezpAsynchronousPublishingFilter` | abstract class | `kernel/private/classes/ezpasynchronouspublishingfilter.php:24` | This interface is to be used to create accept/reject filters for the Asynchronous Publishing feature Each filter class must implement the accept() me… | – |
| `ezpAsynchronousPublishingFilterInterface` | interface | `kernel/private/interfaces/asynchronouspublishingfilter.php:17` | Base interface for eZ Asynchronous Publishing filter classes | – |
| `ezpMobileDeviceDetectFilter` | class | `kernel/private/classes/ezpmobiledevicedetectfilter.php:12` | – | – |
| `ezpMobileDeviceDetectFilterInterface` | interface | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php:12` | – | – |
| `ezpMobileDeviceRegexpFilter` | class | `kernel/private/classes/ezpmobiledeviceregexpfilter.php:15` | Default implementation of mobile device detect interface. | settings/site.ini [SiteAccessSettings] MobileDeviceFilterClass |
| `ezpNativeUserAuthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/auth/native_user_authfilter.php:11` | – | – |
| `ezpOauthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/oauth/oauth_filter.php:12` | – | – |
| `ezpRestDefaultRegexpPrefixFilter` | class extends ezpRestPrefixFilterInterface | `kernel/private/rest/classes/default_regexp_prefix_filter.php:15` | Default implementation of prefix filter interface. | settings/rest.ini [System] PrefixFilterClass |
| `ezpRestFeedDecorator` | class | `kernel/private/rest/classes/views/feed_decorator.php:24` | This class decorates the feed provided in the REST interface for content sync mechanisms. | – |
| `ezpRestFilterMissingParameterException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/filter_param_missing.php:12` | – | – |
| `ezpRestFilterNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/filter_not_found.php:12` | – | – |
| `ezpRestIniRouteFilter` | class extends ezpRestRouteFilterInterface | `kernel/private/rest/classes/auth/ini_route_security.php:12` | – | settings/rest.ini [RouteSettings] RouteSettingImpl |
| `ezpRestPrefixFilterInterface` | abstract class | `kernel/private/rest/classes/prefix_filter.php:12` | – | – |
| `ezpRestPreRoutingFilterInterface` | interface | `kernel/private/rest/classes/interfaces/prerouting_filter.php:12` | – | – |
| `ezpRestRequestFilterInterface` | interface | `kernel/private/rest/classes/interfaces/request_filter.php:12` | – | – |
| `ezpRestResponseFilterInterface` | interface | `kernel/private/rest/classes/interfaces/response_filter.php:12` | – | – |
| `ezpRestResultFilterInterface` | interface | `kernel/private/rest/classes/interfaces/result_filter.php:12` | – | – |
| `ezpRestRouteFilterInterface` | abstract class | `kernel/private/rest/classes/interfaces/route_filter.php:11` | – | – |
| `ezpRestRouteSecurityFilterNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/route_security_filter_not_found.php:12` | – | – |

### kernel/private/classes/clusterfilehandlers (1)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandlerDFSBackendFilterIterator` | class extends FilterIterator | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php:15` | A filter iterator used by eZDFSFileHandlerDFSBackend. | – |

### kernel/private/classes/urlaliasfilters (1)

[All patterns of kernel/private/classes/urlaliasfilters](area-kernel-private-classes-urlaliasfilters.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLAliasFilterAppendNodeID` | class extends eZURLAliasFilter | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php:18` | This class is an URL alias filter to be run with the eZURLAliasFilter system | settings/site.ini [URLTranslator] FilterClasses[] |

### kernel/private/classes/views (1)

[All patterns of kernel/private/classes/views](area-kernel-private-classes-views.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Kernel\Notification\Runfilter` | class extends ModuleView | `kernel/private/classes/views/notification/runfilter.php:21` | The code of kernel/notification/runfilter.php, moved into a class (#207 stage 1). | – |
