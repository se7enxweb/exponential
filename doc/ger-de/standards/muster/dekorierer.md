---
title: Dekorierer / Filter
audience: developer
category: standards
order: 331
summary: "Umhüllt ein Objekt oder einen Wert, um Verhalten hinzuzufügen, und behält dabei seine Schnittstelle: Ausgabefilter, Eingabefilter, Vor- und Nachverarbeiter. 64 Instanzen in Exponential."
keywords: Entwurfsmuster, Dekorierer, Struktur, Musterkatalog, design pattern, decorator, pattern catalogue
type: catalogue
---
# Dekorierer / Filter

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Umhüllt ein Objekt oder einen Wert, um Verhalten hinzuzufügen, und behält dabei seine Schnittstelle: Ausgabefilter, Eingabefilter, Vor- und Nachverarbeiter.

| | |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 64 |
| Erkannt an | Der Klassenname enthält Decorator oder Filter. |

## Klassen nach Bereich

### extension/cjw_newsletter (6)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterEditionFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewslettereditionfilter.php:32` | Dieser Filter erlaubt es, alle Listen nach einem bestimmten Status abzurufen, Statusvarianten: -›DRAFT -›PROCESS | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterEditionFilter] ClassName |
| `CjwNewsletterFilter` | class | `extension/cjw_newsletter/classes/cjwnewsletterfilter.php:17` | Basisklasse zur Verwaltung von Filtern | – |
| `CjwNewsletterFilterType` | class | `extension/cjw_newsletter/classes/cjwnewsletterfiltertype.php:17` | Basisklasse zur Verwaltung von Filtern | – |
| `CjwNewsletterFilterTypeEmail` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypeemail.php:19` | Klassenbeschreibung hier | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterFilterTypeSalutation` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypesalutation.php:19` | Klassenbeschreibung hier | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterListFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewsletterlistfilter.php:29` | Dieser Filter erlaubt es, alle Listen nach einem bestimmten Siteaccess abzurufen; reserviertes Wort „current_siteaccess“ == aktuellen Siteaccess nehmen, um den Parameter siteaccess abzugleichen… | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterListFilter] ClassName |

### extension/explayouts_content_browser_core (1)

[Alle Muster von extension/explayouts_content_browser_core](bereich-extension-explayouts-content-browser-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserCoreSearchFilter` | class | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercoresearchfilter.php:10` | Der erweiterte Attributfilter der Suche des Content-Browsers (extendedattributefilter.ini [explayouts_content_browser_search]): jedes Wort muss in… | extension/explayouts_content_browser_core/settings/extendedattributefilter.ini [explayouts_content_browser_search] ClassName |

### extension/expsite_api (1)

[Alle Muster von extension/expsite_api](bereich-extension-expsite-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSiteApiFilterService` | class | `extension/expsite_api/classes/expsiteapifilterservice.php:2` | – | – |

### extension/ezfind (4)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFindAllWordsExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindallwordsextendedattributefilter.php:25` | eZFindAllWordsExtendedAttributeFilter | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[ezfindallwords] |
| `eZFindExtendedAttributeFilterFactory` | class | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Fabrik für erweiterte Attributfilter | – |
| `eZFindExtendedAttributeFilterInterface` | interface | `extension/ezfind/classes/ezfindextendedattributefilterinterface.php:10` | Schnittstelle für erweiterte Attributfilter | – |
| `eZFindGeoDistExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindgeodistextendedattributefilter.php:28` | Erweiterter Attributfilter SolR Geodist | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[geodist] |

### extension/ezgmaplocation (1)

[Alle Muster von extension/ezgmaplocation](bereich-extension-ezgmaplocation.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezgmlLocationFilter` | class | `extension/ezgmaplocation/classes/ezgmllocationfilter.php:32` | – | extension/ezgmaplocation/settings/extendedattributefilter.ini [ezgmlLocationFilter] ClassName |

### extension/ezie (8)

[Alle Muster von extension/ezie](bereich-extension-ezie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezie\Ezie\FilterBrightness` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_brightness.php:23` | Der Code von extension/ezie/modules/ezie/filter_brightness.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterBw` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_bw.php:23` | Der Code von extension/ezie/modules/ezie/filter_bw.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterContrast` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_contrast.php:23` | Der Code von extension/ezie/modules/ezie/filter_contrast.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterSepia` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_sepia.php:23` | Der Code von extension/ezie/modules/ezie/filter_sepia.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `eZIEImageFilterBrightness` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_brightness.php:11` | – | – |
| `eZIEImageFilterBW` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_bw.php:12` | – | – |
| `eZIEImageFilterContrast` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_contrast.php:11` | – | – |
| `eZIEImageFilterSepia` | class extends eZIEImageAction | `extension/ezie/classes/image_filter_sepia.php:11` | – | – |

### extension/ezodf (1)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOdfFilterCatalogue` | class | `extension/ezodf/classes/converter/expodffiltercatalogue.php:16` | Die Import- und Exportfilter einer LibreOffice-Installation, gelesen aus ihren Dateien share/registry/*.xcd (die Filters und Types der Typerkennung), damit… | – |

### extension/ezprestapi (1)

[Alle Muster von extension/ezprestapi](bereich-extension-ezprestapi.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRestAtomDecorator` | class extends ezpRestFeedDecorator | `extension/ezprestapi/classes/views/atom_decorator.php:10` | – | – |

### extension/ezstarrating (1)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrRatingFilter` | class | `extension/ezstarrating/classes/ezsrratingfilter.php:32` | – | extension/ezstarrating/settings/extendedattributefilter.ini [ezsrRatingFilter] ClassName |

### extension/eztags (2)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTagsAttributeFilter` | class | `extension/eztags/classes/eztagsattributefilter.php:6` | Die Klasse eZTagsAttributeFilter implementiert das erweiterte Attribut TagsAttributeFilter | extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndMultipleFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeFilter] ClassName |
| `eZTagsTreeAttributeFilter` | class | `extension/eztags/classes/eztagstreeattributefilter.php:6` | Die Klasse eZTagsTreeAttributeFilter implementiert das erweiterte Attribut TagsTreeAttributeFilter | extension/eztags/settings/extendedattributefilter.ini [TagsTreeAttributeFilter] ClassName |

### extension/syndication (6)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFilterAttribute` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfilterattribute.php:48` | – | – |
| `eZFilterSection` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfiltersection.php:47` | – | – |
| `eZImportEditFilter` | class extends eZImportEditWizard | `extension/syndication/modules/syndication/import_edit/ezimporteditfilter.php:47` | – | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfilter.php:48` | – | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – | – |

### extension/xrowextract (5)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractFilters` | class | `extension/xrowextract/classes/xrowextractfilters.php:16` | Filter eines Exports: ein Datumsbereich (veröffentlicht, geändert oder ein Datumsattribut der Klasse; beliebig, heute, die letzten N Tage, seit, vor, zwischen, zukünf… | – |
| `XrowExtractHasChildrenFilter` | class | `extension/xrowextract/classes/xrowextracthaschildrenfilter.php:11` | Erweiterter Attributfilter „XrowExtractHasChildren“: nur Objekte, deren Knoten mindestens ein Kind hat (sonst Blattinhalte). | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractHasChildren] ClassName |
| `XrowExtractRelationFilter` | class | `extension/xrowextract/classes/xrowextractrelationfilter.php:17` | Erweiterter Attributfilter „XrowExtractRelation“: nur Objekte, die eine Relation zu einem gegebenen Objekt haben (relation_type wird ignoriert — jede Relation zählt, einschließlich o… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractRelation] ClassName |
| `XrowExtractTranslationFilter` | class | `extension/xrowextract/classes/xrowextracttranslationfilter.php:24` | Erweiterter Attributfilter „XrowExtractTranslation“: nur Objekte, die eine Übersetzung in der gegebenen Sprache haben. | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractTranslation] ClassName |
| `XrowExtractUserStatusFilter` | class | `extension/xrowextract/classes/xrowextractuserstatusfilter.php:12` | Erweiterter Attributfilter „XrowExtractUserStatus“: nur Benutzerobjekte, die aktiviert sind, oder nur solche, die deaktiviert sind, über das Standard-`ezuser_setti… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractUserStatus] ClassName |

### kernel/classes (2)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ExpInstallationDetailsOutputFilter` | class | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Ausgabefilter für den Installationsnamen. | – |
| `eZURLAliasFilter` | abstract class | `kernel/classes/ezurlaliasfilter.php:22` | Die Klasse eZURLAliasFilter ist die Schnittstelle für alle URL-Alias-Filter; die Filterimplementierung muss die Methode process implementieren. | – |

### kernel/classes/notification (1)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZNotificationEventFilter` | class | `kernel/classes/notification/eznotificationeventfilter.php:17` | – | – |

### kernel/private (21)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expApiKeyAuthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/auth/apikey_auth_filter.php:19` | Authentifizierungsfilter für einen persönlichen API-Schlüssel (expApiKey), übergeben als „Authorization: Bearer expk_...“ (doc/eng-us/guides/api-keys.md, „How the REST… | – |
| `expRestPasswordAuthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/auth/password_auth_filter.php:23` | Prüft Login und Passwort für die Basic-Authentifizierung der REST-Schnittstelle so, wie es die Anmeldung tut. | – |
| `ezpAsynchronousPublishingFilter` | abstract class | `kernel/private/classes/ezpasynchronouspublishingfilter.php:24` | Diese Schnittstelle dient zum Erstellen von Annahme-/Ablehnungsfiltern für die Funktion Asynchronous Publishing; jede Filterklasse muss die Methode accept() imp… | – |
| `ezpAsynchronousPublishingFilterInterface` | interface | `kernel/private/interfaces/asynchronouspublishingfilter.php:17` | Basisschnittstelle für Filterklassen von eZ Asynchronous Publishing | – |
| `ezpMobileDeviceDetectFilter` | class | `kernel/private/classes/ezpmobiledevicedetectfilter.php:12` | – | – |
| `ezpMobileDeviceDetectFilterInterface` | interface | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php:12` | – | – |
| `ezpMobileDeviceRegexpFilter` | class | `kernel/private/classes/ezpmobiledeviceregexpfilter.php:15` | Standardimplementierung der Schnittstelle zur Erkennung mobiler Geräte. | settings/site.ini [SiteAccessSettings] MobileDeviceFilterClass |
| `ezpNativeUserAuthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/auth/native_user_authfilter.php:11` | – | – |
| `ezpOauthFilter` | class extends ezcAuthenticationFilter | `kernel/private/rest/classes/oauth/oauth_filter.php:12` | – | – |
| `ezpRestDefaultRegexpPrefixFilter` | class extends ezpRestPrefixFilterInterface | `kernel/private/rest/classes/default_regexp_prefix_filter.php:15` | Standardimplementierung der Präfixfilter-Schnittstelle. | settings/rest.ini [System] PrefixFilterClass |
| `ezpRestFeedDecorator` | class | `kernel/private/rest/classes/views/feed_decorator.php:24` | Diese Klasse dekoriert den Feed, den die REST-Schnittstelle für Mechanismen der Inhaltssynchronisation bereitstellt. | – |
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

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerDFSBackendFilterIterator` | class extends FilterIterator | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php:15` | Ein Filter-Iterator, der von eZDFSFileHandlerDFSBackend verwendet wird. | – |

### kernel/private/classes/urlaliasfilters (1)

[Alle Muster von kernel/private/classes/urlaliasfilters](bereich-kernel-private-classes-urlaliasfilters.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLAliasFilterAppendNodeID` | class extends eZURLAliasFilter | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php:18` | Diese Klasse ist ein URL-Alias-Filter, der mit dem eZURLAliasFilter-System ausgeführt wird | settings/site.ini [URLTranslator] FilterClasses[] |

### kernel/private/classes/views (1)

[Alle Muster von kernel/private/classes/views](bereich-kernel-private-classes-views.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Kernel\Notification\Runfilter` | class extends ModuleView | `kernel/private/classes/views/notification/runfilter.php:21` | Der Code von kernel/notification/runfilter.php, in eine Klasse verschoben (#207 Stufe 1). | – |
