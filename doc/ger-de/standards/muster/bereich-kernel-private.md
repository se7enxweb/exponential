---
title: Muster in kernel/private
audience: developer
category: standards
order: 527
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private mit den Entwurfsmustern.
keywords: kernel/private, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (181)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expApiKeyAuthFilter` | class extends ezcAuthenticationFilter | [Dekorierer / Filter](dekorierer.md) | `kernel/private/rest/classes/auth/apikey_auth_filter.php:19` | Authentifizierungsfilter für einen persönlichen API-Schlüssel (expApiKey), übergeben als „Authorization: Bearer expk_...“ (doc/eng-us/guides/api-keys.md, „How the REST… |
| `expApiKeyRest` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/auth/apikey_rest.php:24` | Wie die REST-Schicht einen persönlichen API-Schlüssel auflöst (doc/eng-us/guides/api-keys.md, „For developers“). |
| `expContentAccessReport` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/expcontentaccessreport.php:23` | Beantwortet „darf dieser Benutzer dies mit diesem Objekt oder Knoten tun, und wenn nicht, welche Einschränkung verweigert es“: die Zugriffsprüfungen des Kernels für einen anderen Ben… |
| `expContentSecurityPolicy` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/expcontentsecuritypolicy.php:39` | Die Content Security Policy einer Seite, aus site.ini [ContentSecurityPolicy]. |
| `expContentSecurityPolicyReport` | class | – | `kernel/private/classes/expcontentsecuritypolicyreport.php:40` | Die Berichte der Content Security Policy (doc/eng-us/features/6.0/content-security-policy.md, „Reports“): was die Ansicht csp/report von den… |
| `expFileConsistencyReport` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/expfileconsistencyreport.php:36` | Vergleicht die Dateien einer Installation mit den Prüfsummenlisten, die mit ihr ausgeliefert werden: share/filelist.md5 von Exponential und die share/filelist.md5, die eine Exte… |
| `expOAuthAuthorizePolicy` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/oauth/authorize_policy.php:21` | Entscheidet, ob ein angemeldeter Benutzer einen OAuth-Client in oauth/authorize autorisieren darf. |
| `expOAuthAuthorizePolicyGrant` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/oauth/authorize_policy_grant.php:26` | Plant und vergibt beim Upgrade die Richtlinie oauth/authorize (bin/php/addoauthauthorizepolicy.php). |
| `expOAuthPkce` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/oauth/pkce.php:24` | Proof Key for Code Exchange (PKCE, RFC 7636) für den OAuth-Authorization-Code-Flow. |
| `expRestContentPermission` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/auth/content_permission.php:36` | Die Rechte des aktuellen Benutzers an dem Inhalt, den ein REST-Aufruf liest oder schreibt, geprüft so, wie das Content-Modul sie prüft (doc/eng-us/guides/api-keys.md,… |
| `expRestCors` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/rest/classes/cors.php:29` | Cross-Origin Resource Sharing (CORS) für die REST-Schnittstelle (index_rest.php: die Inhalts-API, der OAuth-Token-Endpunkt und jede andere Route einer RE… |
| `expRestPasswordAuthFilter` | class extends ezcAuthenticationFilter | [Dekorierer / Filter](dekorierer.md) | `kernel/private/rest/classes/auth/password_auth_filter.php:23` | Prüft Login und Passwort für die Basic-Authentifizierung der REST-Schnittstelle so, wie es die Anmeldung tut. |
| `expRestPasswordChangeRequiredStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/password_change_required.php:15` | 403 Forbidden mit dem Fehlercode password_change_required: Die Zugangsdaten sind richtig, aber das Konto ist mit „Passwort bei der nächsten Anmeldung ändern“ markiert (e… |
| `expRestRateLimitedStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/rate_limited.php:14` | 429 Too Many Requests mit Retry-After, für einen persönlichen API-Schlüssel über seinem Ratenlimit (rest.ini [ApiKeySettings] RateLimitPerMinute). |
| `expRestSecondFactorRequiredStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/second_factor_required.php:16` | Die REST-Antwort auf ein richtiges Passwort bei einem Konto, das einen zweiten Anmeldeschritt braucht (expSignInSecondFactor): 401 mit WWW-Authenticate (ein… |
| `expSchemaConsistencyReport` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/expschemaconsistencyreport.php:36` | Vergleicht die Datenbank mit dem Schema, das mit Exponential und seinen aktiven Extensions ausgeliefert wird (share/db_schema.dba der Wurzel und jeder Extension),… |
| `eZAutoloadGenerator` | class | [Builder](builder.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/ezautoloadgenerator.php:19` | Hilfsklasse zum Erzeugen von Autoload-Arrays für Exponential. |
| `eZClusterFileHandlerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/ezclusterfilehandlerinterface.php:15` | Schnittstelle für Cluster-Datei-Handler |
| `eZContentObjectState` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/classes/ezcontentobjectstate.php:18` | Klasse, die einen Inhaltsobjektzustand darstellt |
| `eZContentObjectStateGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/classes/ezcontentobjectstategroup.php:18` | Klasse, die eine Inhaltsobjekt-Zustandsgruppe darstellt |
| `eZContentObjectStateGroupLanguage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/classes/ezcontentobjectstategrouplanguage.php:21` | Diese Klasse kümmert sich um die Lokalisierung einer Inhaltsobjekt-Zustandsgruppe. |
| `eZContentObjectStateLanguage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/classes/ezcontentobjectstatelanguage.php:21` | Diese Klasse kümmert sich um die Lokalisierung eines Inhaltsobjektzustands. |
| `ezpActiveExtensions` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/ezpactiveextensions.php:26` | Ändert [ExtensionSettings] ActiveExtensions in settings/override/site.ini.append.php für Setup › Extensions und lässt alles andere in dieser… |
| `ezpAsynchronousPublisherOutput` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/asynchronouspublisheroutput.php:16` | Diese Schnittstelle dient als Basis für den Daemon ezpasynchronouspublisher.php |
| `ezpAsynchronousPublishingFilter` | abstract class implements ezpAsynchronousPublishingFilterInterface | [Dekorierer / Filter](dekorierer.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/classes/ezpasynchronouspublishingfilter.php:24` | Diese Schnittstelle dient zum Erstellen von Annahme-/Ablehnungsfiltern für die Funktion Asynchronous Publishing; jede Filterklasse muss die Methode accept() imp… |
| `ezpAsynchronousPublishingFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/asynchronouspublishingfilter.php:17` | Basisschnittstelle für Filterklassen von eZ Asynchronous Publishing |
| `ezpAttributeOperatorFormatter` | class | – | `kernel/private/eztemplate/ezpattributeoperatorformatter.php:12` | – |
| `ezpAttributeOperatorFormatterInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php:12` | – |
| `ezpAttributeOperatorHTMLFormatter` | class extends ezpAttributeOperatorFormatter implements ezpAttributeOperatorFormatterInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/eztemplate/ezpattributeoperatorhtmlformatter.php:12` | – |
| `ezpAttributeOperatorManager` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – |
| `ezpAttributeOperatorTextFormatter` | class extends ezpAttributeOperatorFormatter implements ezpAttributeOperatorFormatterInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/eztemplate/ezpattributeoperatortextformatter.php:12` | – |
| `ezpAutoloadCliOutput` | class implements ezpAutoloadOutput | – | `kernel/private/classes/ezpautoloadclioutput.php:17` | Hilfsklasse für CLI-Ausgabe und schrittweise Fortschrittsinformationen. |
| `ezpAutoloadFileFindContext` | class extends ezcBaseStruct | [Wertobjekt / Struktur](wertobjekt.md) | `kernel/private/structs/ezpautoloadfilefindcontext.php:17` | Struktur, die die vom Datei-Walker gesammelten Informationen zum Auffinden von Dateien definiert. |
| `ezpAutoloadGeneratorOptions` | class extends ezcBaseOptions | – | `kernel/private/options/ezpautoloadgeneratoroptions.php:55` | Klasse mit Optionen für eZAutoloadGenerator |
| `ezpAutoloadOutput` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/ezpautoloadoutput.php:17` | Schnittstelle für Klassen, die Ausgaben für die Autoload-Erzeugung bereitstellen. |
| `ezpCacheClusterException` | class extends ezcCacheException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/cache/exceptions/cluster.php:15` | Generische Ausnahmeklasse für ezpCacheCluster |
| `ezpCacheStorageClusterOptions` | class extends ezcBaseOptions | – | `kernel/private/rest/classes/cache/options/storage_cluster.php:15` | Optionsklasse für die Klasse ezpCacheStorageCluster. |
| `ezpContent` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/api/content/content.php:56` | Klasse, die ein Inhaltselement darstellt. |
| `ezpContentAccessDeniedException` | class extends ezpContentException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/api/content/exceptions/access_denied.php:12` | – |
| `ezpContentClassCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/class.php:16` | Diese Klasse ermöglicht die Konfiguration von Kriterien auf Basis der Inhaltsklasse |
| `ezpContentCriteria` | class | – | `kernel/private/api/content/criteria.php:28` | Diese Klasse dient zum Erzeugen und Bearbeiten von Kriterien für Inhaltslisten. |
| `ezpContentCriteriaInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/api/interfaces/content_criteria.php:16` | Diese Schnittstelle ist die Basis für die Implementierung von Kriterien für Inhaltsabfragen |
| `ezpContentCriteriaSet` | class implements ArrayAccess, Countable, Iterator | [Iterator](iterator.md) | `kernel/private/api/content/criteria_set.php:16` | Diese Klasse verwaltet eine Liste von Kriterien für Inhaltsabfragen |
| `ezpContentDepthCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/depth.php:16` | Tiefenkriterien |
| `ezpContentException` | abstract class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/api/content/exceptions/content_exception.php:17` | Dies ist die Basisausnahme der Inhalts-API von Exponential |
| `ezpContentField` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/api/content/field.php:17` | Diese Klasse verwaltet Inhaltsfelder. |
| `ezpContentFieldCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/field.php:17` | Diese Klasse dient zum Erzeugen und Bearbeiten eines Inhaltskriteriums für Feldwerte. |
| `ezpContentFieldNotFoundException` | class extends ezpContentException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/api/content/exceptions/content_field_not_found.php:11` | – |
| `ezpContentFieldSet` | class implements ArrayAccess, Iterator | [Fabrik und statische Fabrikmethoden](fabrik.md), [Iterator](iterator.md) | `kernel/private/api/content/field_set.php:18` | Ermöglicht das Lesen und Schreiben von Inhaltsfeldern (Attributen) über eine Array-ähnliche Schnittstelle. |
| `ezpContentLimitation` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/ezpcontentlimitation.php:26` | Fragt den Handler, den eine Extension für eine Inhaltsrichtlinien-Einschränkung registriert hat, die der Kernel nicht kennt (site.ini [RoleSettings] LimitationHandlers[]=, siehe… |
| `ezpContentLimitationHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/interfaces/ezpcontentlimitationhandler.php:29` | Wertet eine Richtlinieneinschränkung des Moduls content aus, die der Kernel selbst nicht kennt. |
| `ezpContentLimitationSolrHandler` | interface extends ezpContentLimitationHandler | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/interfaces/ezpcontentlimitationsolrhandler.php:23` | Ein Handler für Inhaltseinschränkungen, der auch eine Suche in einer Suchmaschine einschränken kann, die selbst nach den Richtlinien des Benutzers filtert, etwa eZ Find mit… |
| `ezpContentLimitCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/limit.php:16` | Diese Klasse ermöglicht die Konfiguration von Kriterien auf Basis von Offset/Limit |
| `ezpContentList` | class implements Countable, Iterator | [Iterator](iterator.md) | `kernel/private/api/content/list.php:18` | Mit dieser Klasse können Sie eine zuvor abgerufene Inhaltsliste durchblättern, zum Beispiel mit ezpContentRepository::query(). Sie ist mehr oder weniger ein ResultSet aus einem… |
| `ezpContentLocation` | class extends ezpLocation | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/api/content/location.php:21` | Diese Klasse wird verwendet, um einen bestimmten Inhaltsort zu bearbeiten. |
| `ezpContentLocationCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/location.php:16` | Diese Klasse ermöglicht die Konfiguration von Kriterien auf Basis des Ortes |
| `ezpContentLocationSet` | class | – | `kernel/private/api/content/location_set.php:15` | Diese Klasse stellt die Orte eines Inhalts dar |
| `ezpContentNotFoundException` | class extends ezpContentException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/api/content/exceptions/content_not_found.php:12` | – |
| `ezpContentPublishingProcess` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/ezpcontentpublishingprocess.php:18` | Diese Klasse bietet eine PersistentObject-Schnittstelle zu einem Prozess der Veröffentlichungswarteschlange. |
| `ezpContentPublishingQueue` | class implements ezpContentPublishingQueueReaderInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/ezpcontentpublishingqueue.php:19` | Diese Klasse verwaltet die Warteschlange zum Veröffentlichen von Inhalten. |
| `ezpContentPublishingQueueProcessor` | class | [Singleton / instance()](singleton.md) | `kernel/private/classes/ezpcontentpublishingqueueprocessor.php:21` | Diese Klasse verwaltet die Veröffentlichungswarteschlange über persistente Objekte ezpContentPublishingProcess |
| `ezpContentPublishingQueueReaderInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/ezpcontentpublishingqueuereader.php:18` | Diese Schnittstelle beschreibt einen Leser der asynchronen Veröffentlichungswarteschlange. Queue-Leser sollen dem asynchronen Veröffentlichungssystem das nächste… |
| `ezpContentRepository` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/api/content/repository.php:16` | Diese Klasse verarbeitet Abfragen des Inhalts-Repositorys |
| `ezpContentSortingCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/sorting.php:16` | Sortierkriterien |
| `ezpContentXHTMLRenderer` | class extends ezpRestContentRendererInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/rest/classes/renderers/xhtml_content_renderer.php:12` | – |
| `ezpDatabaseBasedClusterFileHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/interfaces/ezpdatabasebasedclusterfilehandler.php:20` | Diese Schnittstelle beschreibt einen datenbankbasierten Cluster-Datei-Handler |
| `ezpEvent` | class | [Beobachter / Listener / Ereignis](beobachter.md), [Singleton / instance()](singleton.md) | `kernel/private/classes/ezpevent.php:20` | Diese Klasse verarbeitet interne Kernel-Ereignisse in Exponential, auch Hooks genannt. |
| `ezpExtension` | class | [Singleton / instance()](singleton.md) | `kernel/private/classes/ezpextension.php:15` | Objekt, das eine Exponential-Extension darstellt |
| `ezpExtensionOptions` | class extends ezcBaseOptions | – | `kernel/private/options/ezpextensionoptions.php:58` | Klasse mit Optionen für eZExtension; dieses Optionssystem wird bisher nur von eZExtension::getHandlerClass verwendet. |
| `ezpFormTokenRefusal` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/ezpformtokenrefusal.php:27` | Macht aus einer ezpFormTokenException -- ein POST, dessen Formular-Token fehlte oder falsch war -- eine Ablehnung: HTTP 403, Cache-Control: no-store, die Sicherheits-Hea… |
| `ezpHttpResponseCodes` | class | – | `kernel/private/rest/classes/http_response_codes.php:12` | – |
| `ezpKernel` | class implements ezpWebBasedKernelHandler | [Front Controller](front-controller.md), [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/ezpkernel.php:16` | Basisklasse des Exponential-Kernels. |
| `ezpKernelHandler` | interface | [Front Controller](front-controller.md), [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/interfaces/ezpkernelhandler.php:14` | Schnittstelle für Kernel-Handler |
| `ezpKernelRedirect` | class extends ezpKernelResult | – | `kernel/private/classes/ezpkernelredirect.php:14` | Struktur mit Informationen zu einer HTTP-Weiterleitung. |
| `ezpKernelRest` | class implements ezpKernelHandler | [Front Controller](front-controller.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/rest/classes/ezpkernelrest.php:11` | – |
| `ezpKernelResult` | class | – | `kernel/private/classes/ezpkernelresult.php:14` | Struktur mit dem Ergebnis des Kernels. |
| `ezpKernelTreeMenu` | class implements ezpKernelHandler | [Front Controller](front-controller.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/ezpkerneltreemenu.php:11` | – |
| `ezpKernelWeb` | class implements ezpWebBasedKernelHandler | [Front Controller](front-controller.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/ezpkernelweb.php:16` | Stellt einen Kernel-Handler im Web-Kontext bereit |
| `ezpLanguageSwitcher` | class implements ezpLanguageSwitcherCapable | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/classes/ezplanguageswitcher.php:18` | Hilfsklasse zum Umwandeln von URLs zwischen Siteaccesses. |
| `ezpLanguageSwitcherCapable` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/ezplanguageswitchercapable.php:17` | Schnittstelle für Klassen, die Funktionen zur Sprachumschaltung bereitstellen. |
| `ezpLanguageSwitcherFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/private/modules/switchlanguage/ezpLanguageSwitcherFunctionCollection.php:14` | – |
| `ezpLanguageSwitcherOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/private/eztemplate/ezplanguageswitcheroperator.php:12` | – |
| `ezpLocation` | class | – | `kernel/private/api/location.php:16` | Diese Klasse dient zum Bearbeiten von Orten |
| `ezpMail` | class extends ezcMail | – | `kernel/private/classes/ezpmail.php:14` | ezpMail erweitert ezcMail, um Standardwerte und Einschränkungen zu überschreiben. |
| `ezpMobileDeviceDetect` | class | – | `kernel/private/classes/ezpmobiledevicedetect.php:15` | ezpMobileDeviceDetect-Klassenimplementierung |
| `ezpMobileDeviceDetectFilter` | class | [Dekorierer / Filter](dekorierer.md) | `kernel/private/classes/ezpmobiledevicedetectfilter.php:12` | – |
| `ezpMobileDeviceDetectFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php:12` | – |
| `ezpMobileDeviceRegexpFilter` | class implements ezpMobileDeviceDetectFilterInterface | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/classes/ezpmobiledeviceregexpfilter.php:15` | Standardimplementierung der Schnittstelle zur Erkennung mobiler Geräte. |
| `ezpMultivariateTest` | class | – | `kernel/private/classes/ezpmultivariatetest.php:12` | – |
| `ezpMultivariateTestHandler` | class implements ezpMultivariateTestHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/ezpmultivariatetesthandler.php:12` | – |
| `ezpMultivariateTestHandlerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/ezpmultivariatetesthandlerinterface.php:12` | – |
| `ezpMvcConfigurableDispatcher` | class extends ezcMvcConfigurableDispatcher | – | `kernel/private/rest/classes/dispatcher.php:12` | – |
| `ezpMvcConfiguration` | class implements ezcMvcDispatcherConfiguration | – | `kernel/private/rest/classes/mvc_configuration.php:11` | – |
| `ezpMvcRailsRoute` | class extends ezcMvcRailsRoute | – | `kernel/private/rest/classes/routes/rails.php:11` | – |
| `ezpMvcRegexpRoute` | class extends ezcMvcRegexpRoute | [Front Controller](front-controller.md) | `kernel/private/rest/classes/routes/regexp.php:16` | Überschreibung von ezcMvcRegexpRoute. |
| `ezpNativeUserAuthFilter` | class extends ezcAuthenticationFilter | [Dekorierer / Filter](dekorierer.md) | `kernel/private/rest/classes/auth/native_user_authfilter.php:11` | – |
| `ezpOauthBadRequestException` | abstract class extends ezpOauthException | [Ausnahmehierarchie](ausnahme.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/exceptions/bad_request.php:17` | Dies ist die Basisausnahme zum Auslösen einer BAD-REQUEST-Antwort. |
| `ezpOauthErrorType` | class | – | `kernel/private/rest/classes/outh_error_type.php:12` | – |
| `ezpOauthException` | abstract class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/exceptions/oauth.php:17` | Dies ist die Basisausnahme der OAuth-Bibliothek von Exponential. |
| `ezpOauthExpiredTokenException` | class extends ezpOauthRequiredException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/expired_token.php:18` | Diese Ausnahme wird ausgelöst, wenn das Zugriffstoken abgelaufen ist, aber erneuert werden kann. |
| `ezpOauthFilter` | class extends ezcAuthenticationFilter | [Dekorierer / Filter](dekorierer.md) | `kernel/private/rest/classes/oauth/oauth_filter.php:12` | – |
| `ezpOauthInsufficientScopeException` | class extends ezpOauthRequiredException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/insufficient_scope.php:18` | Diese Ausnahme wird ausgelöst, wenn eine Anfrage mit einem Token gestellt wird, dessen Scope nicht ausreicht, damit die Anfrage angenommen wird. |
| `ezpOauthInvalidRequestException` | class extends ezpOauthBadRequestException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/invalid_request.php:21` | Diese Ausnahme wird ausgelöst, wenn eine Anfrage ungültig ist. |
| `ezpOauthInvalidTokenException` | class extends ezpOauthRequiredException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/invalid_token.php:19` | Diese Ausnahme wird ausgelöst, wenn ein Token ungültig ist. |
| `ezpOauthNoAuthInfoException` | class extends ezpOauthRequiredException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/missing_auth_info.php:18` | Diese Ausnahme wird ausgelöst, wenn der Client in der Anfrage keine Authentifizierungsinformationen angegeben hat. |
| `ezpOauthRequired` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/oauth_required.php:15` | Dieser Ergebnistyp signalisiert einen HTTP-Basic-Auth-Header |
| `ezpOauthRequiredException` | abstract class extends ezpOauthException | [Ausnahmehierarchie](ausnahme.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/exceptions/auth.php:17` | Dies ist die Basisausnahme für Antworten, die eine Authentifizierung erfordern. |
| `ezpOauthTokenEndpointErrorType` | class | – | `kernel/private/rest/classes/oauth_token_endpoint_error.php:12` | – |
| `ezpOauthTokenNotFoundException` | class extends ezpOauthException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/oauth_token_not_found.php:17` | Dies ist die Basisausnahme der OAuth-Bibliothek von Exponential. |
| `ezpOauthUtility` | class extends ezpRestModel | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/oauth/utility.php:17` | Funktionalität für die Arbeit mit Draft 10 der OAuth2-Spezifikation. |
| `ezpRestAuthcode` | class implements ezcPersistentObject | – | `kernel/private/rest/classes/models/ezprest_authcode.php:16` | Datenklasse ezprest_tokens. |
| `ezpRestAuthConfiguration` | class | – | `kernel/private/rest/classes/auth/auth_configuration.php:19` | Klasse, die die Authentifizierung in der REST-Schicht steuert. |
| `ezpRestAuthController` | class extends ezcMvcController | – | `kernel/private/rest/classes/controllers/auth.php:15` | Behandelt die Authentifizierung mit der REST-Schnittstelle. |
| `ezpRestAuthenticationStyle` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/auth/styles/abstract_auth_style.php:11` | – |
| `ezpRestAuthenticationStyleInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/auth/auth_style.php:12` | – |
| `ezpRestAuthorizedClient` | class | – | `kernel/private/oauth/classes/restauthorizedclient.php:16` | Persistente Objektklasse, die die Autorisierung einer REST-Anwendung durch einen Benutzer darstellt. |
| `ezpRestAuthProvider` | class implements ezpRestProviderInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/rest/classes/auth/auth_provider.php:11` | – |
| `ezpRestAuthStyleNotFoundException` | class extends ezpRestException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/auth_style_not_found.php:12` | – |
| `ezpRestBasicAuthStyle` | class extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface | – | `kernel/private/rest/classes/auth/styles/basic_auth.php:23` | HTTP-Basic-Authentifizierung gegen die Benutzer der Website. |
| `ezpRestCacheStorageApcCluster` | class extends ezcCacheStorageApcPlain | – | `kernel/private/rest/classes/cache/apc.php:11` | – |
| `ezpRestCacheStorageCluster` | abstract class extends ezpRestCacheStorageFile implements ezcCacheStackableStorage, ezcCacheStackMetaDataStorage | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/cache/cluster.php:11` | – |
| `ezpRestCacheStorageClusterObject` | class extends ezpRestCacheStorageCluster | – | `kernel/private/rest/classes/cache/object.php:16` | Cache-Speicher-Implementierung für kompatible Objekte (die die Schnittstelle ezcBaseExportable implementieren). |
| `ezpRestCacheStorageFile` | abstract class extends ezcCacheStorageFile | – | `kernel/private/rest/classes/cache/storage.php:11` | – |
| `ezpRestClient` | class | – | `kernel/private/oauth/classes/restclient.php:15` | Persistente Objektklasse, die eine REST-Anwendung darstellt. |
| `ezpRestContentRenderer` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/content_renderer.php:12` | – |
| `ezpRestContentRendererInterface` | abstract class | – | `kernel/private/rest/classes/interfaces/content_renderer.php:12` | – |
| `ezpRestContentRendererNotFoundException` | class extends ezpRestException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/content_renderer_not_found.php:12` | – |
| `ezpRestDbConfig` | class implements ezcBaseConfigurationInitializer | [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/lazy.php:12` | – |
| `ezpRestDebug` | class | [Singleton / instance()](singleton.md) | `kernel/private/rest/classes/debug/debug.php:11` | – |
| `ezpRestDebugPHPFormatter` | class implements ezcDebugOutputFormatter | – | `kernel/private/rest/classes/debug/formatters/php_formatter.php:11` | – |
| `ezpRestDefaultRegexpPrefixFilter` | class extends ezpRestPrefixFilterInterface | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/rest/classes/default_regexp_prefix_filter.php:15` | Standardimplementierung der Präfixfilter-Schnittstelle. |
| `ezpRestErrorController` | class extends ezcMvcController | – | `kernel/private/rest/classes/controllers/error.php:15` | Dieser Controller behandelt Fehlersituationen in der REST-Schicht. |
| `ezpRestException` | abstract class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/exceptions/rest_exception.php:17` | Dies ist die Basisausnahme der REST-Schicht von Exponential |
| `ezpRestFeedDecorator` | class | [Dekorierer / Filter](dekorierer.md) | `kernel/private/rest/classes/views/feed_decorator.php:24` | Diese Klasse dekoriert den Feed, den die REST-Schnittstelle für Mechanismen der Inhaltssynchronisation bereitstellt. |
| `ezpRestFilterMissingParameterException` | class extends ezpRestException | [Dekorierer / Filter](dekorierer.md), [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/filter_param_missing.php:12` | – |
| `ezpRestFilterNotFoundException` | class extends ezpRestException | [Dekorierer / Filter](dekorierer.md), [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/filter_not_found.php:12` | – |
| `ezpRestHttpRequestParser` | class extends ezcMvcHttpRequestParser | – | `kernel/private/rest/classes/request/http_parser.php:19` | Eigener Request-Parser, der Instanzen von ezpRestRequest erzeugt. |
| `ezpRestHttpResponse` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/http_response.php:12` | – |
| `ezpRestHttpResponseWriter` | class extends ezcMvcHttpResponseWriter | – | `kernel/private/rest/classes/rest_http_response_writer.php:12` | – |
| `ezpRestHTTPSRequiredException` | class extends ezpRestException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/https_required.php:12` | – |
| `ezpRestIniRouteFilter` | class extends ezpRestRouteFilterInterface | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/rest/classes/auth/ini_route_security.php:12` | – |
| `ezpRestJsonView` | class extends ezcMvcView | – | `kernel/private/rest/classes/views/json.php:12` | – |
| `ezpRestModel` | abstract class | – | `kernel/private/rest/classes/models/rest_model.php:15` | Abstrakte Klasse für jedes REST-Modell |
| `ezpRestMvcController` | abstract class extends ezcMvcController | [Front Controller](front-controller.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/controllers/rest_controller.php:15` | Abstrakte Klasse, die jeder REST-Controller erweitern muss |
| `ezpRestMvcResult` | class extends ezcMvcResult implements ezcBaseExportable | [Fabrik und statische Fabrikmethoden](fabrik.md), [Wertobjekt / Struktur](wertobjekt.md) | `kernel/private/rest/classes/response/result.php:15` | Diese Struktur enthält die Ergebnisdaten, die von eZP-REST-Controllern zurückgegeben werden |
| `ezpRestNoAuthStyle` | class extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface | – | `kernel/private/rest/classes/auth/styles/no_auth.php:16` | Dieser Authentifizierungsstil wird verwendet, wenn für die aktuelle REST-Anfrage keine Authentifizierung nötig ist. Es wird ein Standardbenutzer zurückgegeben. |
| `ezpRestOauthAuthenticationStyle` | class extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/private/rest/classes/auth/styles/oauth.php:12` | – |
| `ezpRestOauthErrorStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/oauth_error.php:12` | – |
| `ezpRestOauthTokenController` | class extends ezcMvcController | – | `kernel/private/rest/classes/controllers/oauth_token.php:18` | Controller für den OAuth-Token-Endpunkt. |
| `ezpRestPoConfig` | class implements ezcBaseConfigurationInitializer | – | `kernel/private/rest/classes/lazy.php:101` | – |
| `ezpRestPrefixFilterInterface` | abstract class | [Dekorierer / Filter](dekorierer.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/prefix_filter.php:12` | – |
| `ezpRestPreRoutingFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/interfaces/prerouting_filter.php:12` | – |
| `ezpRestProvider` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/rest_provider.php:12` | – |
| `ezpRestProviderInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/interfaces/rest_provider.php:12` | – |
| `ezpRestProviderNotFoundException` | class extends ezpRestException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/rest_provider_not_found.php:12` | – |
| `ezpRestRequest` | class extends ezcMvcRequest | – | `kernel/private/rest/classes/request/rest_request.php:20` | Klasse, die ezcMvcRequest nachahmt, mit getrennten Containern für GET- und POST-Variablen. |
| `ezpRestRequestFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/interfaces/request_filter.php:12` | – |
| `ezpRestResponseFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/interfaces/response_filter.php:12` | – |
| `ezpRestResultFilterInterface` | interface | [Dekorierer / Filter](dekorierer.md), [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/interfaces/result_filter.php:12` | – |
| `ezpRestRouteFilterInterface` | abstract class | [Dekorierer / Filter](dekorierer.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/interfaces/route_filter.php:11` | – |
| `ezpRestRouter` | class extends ezcMvcRouter | [Zuständigkeitskette (Chain of Responsibility)](kette.md) | `kernel/private/rest/classes/router.php:11` | – |
| `ezpRestRoutesCacheClear` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/rest/classes/cache/clear_routes.php:15` | Handler zum Leeren des Caches. |
| `ezpRestRouteSecurityFilterNotFoundException` | class extends ezpRestException | [Dekorierer / Filter](dekorierer.md), [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/route_security_filter_not_found.php:12` | – |
| `ezpRestStatusResponse` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/response.php:12` | – |
| `ezpRestToken` | class implements ezcPersistentObject | – | `kernel/private/rest/classes/models/ezprest_token.php:16` | Datenklasse ezprest_tokens. |
| `ezpRestTokenManager` | class | [Fassade / Dienst](fassade.md) | `kernel/private/oauth/classes/tokenmanager.php:17` | Diese Klasse verwaltet Anwendungs-Tokens |
| `ezpRestVersionedRoute` | class implements ezcMvcRoute, ezcMvcReversibleRoute | – | `kernel/private/rest/classes/routes/versioned_route.php:19` | Route, die eine vorhandene Instanz von ezcMvcRoute umhüllt und mehrere Versionen davon bereitstellt. |
| `ezpRestViewControllerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/rest/classes/interfaces/view_controller.php:12` | – |
| `ezpRouteMethodNotAllowedException` | class extends ezcMvcToolsException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/route_method_not_allowed.php:12` | – |
| `ezpSearchEngine` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/interfaces/ezpsearchengine.php:18` | Diese Schnittstelle dient als Grundlage für die verschiedenen Suchmaschinen-Implementierungen |
| `ezpSiteAccessMenu` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/ezpsiteaccessmenu.php:48` | Das Site-Menü der Administrationsoberfläche: die Siteaccesses dieser Installation mit ihren Startseitenadressen, für die Globus-Schaltfläche im Kopf von admin4. |
| `ezpSiteAccessURL` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/ezpsiteaccessurl.php:44` | Die Adresse der Startseite eines Siteaccess, ermittelt aus den Siteaccess-Einstellungen und der Anfrage, auf der der Besucher ist: der Admin-Link "Site öffnen"… |
| `ezpStaticCache` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/interfaces/ezpstaticcache.php:16` | Diese Schnittstelle dient als Basis für die verschiedenen Implementierungen der StaticCache-Engines |
| `ezpTemplateOverrides` | class | – | `kernel/private/classes/ezptemplateoverrides.php:33` | Ändert die Template-Überschreibungen eines Siteaccess, für Design › Templates, in settings/siteaccess//override.ini.append.php und nirgendwo sonst. |
| `ezpTopologicalSort` | class | – | `kernel/private/classes/ezptopologicalsort.php:15` | Sortiert eine Reihe von Abhängigkeiten in eine lineare Reihenfolge (topologische Sortierung). |
| `ezpTopologicalSortNode` | class | – | `kernel/private/classes/ezptopologicalsortnode.php:17` | Knoten in einer topologischen Sortierung. |
| `ezpUpdatedContent` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/private/rest/classes/sync/updates.php:23` | Diese Klasse stellt eine API bereit, über die man nach aktualisierten Inhaltselementen abfragen kann. |
| `ezpUserNotFoundException` | class extends ezpRestException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/rest/classes/exceptions/user_not_found.php:11` | – |
| `ezpWebBasedKernelHandler` | interface extends ezpKernelHandler | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/interfaces/ezpwebbasedkernelhandler.php:14` | Schnittstelle für webbasierte Kernel-Handler |
| `eZScriptClusterPurge` | class | – | `kernel/private/classes/ezscriptclusterpurge.php:32` | Diese Klasse übernimmt das Bereinigen von Cluster-Einträgen. |
| `eZScriptTrashPurge` | class | – | `kernel/private/classes/ezscripttrashpurge.php:12` | Diese Klasse übernimmt das Bereinigen von Papierkorb-Einträgen. |

## Modulansichten (8)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/oauth/authorize` | `authorize.php` | – | `kernel/private/modules/oauth/module.php:21` |
| `/oauthadmin/list` | `list.php` | – | `kernel/private/modules/oauthadmin/module.php:21` |
| `/oauthadmin/edit` | `edit.php` | – | `kernel/private/modules/oauthadmin/module.php:26` |
| `/oauthadmin/action` | `action.php` | – | `kernel/private/modules/oauthadmin/module.php:37` |
| `/oauthadmin/view` | `view.php` | – | `kernel/private/modules/oauthadmin/module.php:46` |
| `/oauthadmin/keys` | `keys.php` | – | `kernel/private/modules/oauthadmin/module.php:54` |
| `/oauthadmin/keyaction` | `keyaction.php` | – | `kernel/private/modules/oauthadmin/module.php:61` |
| `/switchlanguage/to` | `to.php` | – | `kernel/private/modules/switchlanguage/module.php:16` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `oauth/authorize` | – | `kernel/private/modules/oauth/module.php:29` |

## Fetch-Funktionen (1)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'switchlanguage', 'url_alias' )` | `ezpLanguageSwitcherFunctionCollection::fetchUrlAlias()` | `kernel/private/modules/switchlanguage/function_definition.php:13` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| language_switcher | `ezpLanguageSwitcherOperator` | `kernel/private/eztemplate/eztemplateautoload.php:13` |

## Ereignis-Hooks (4)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `request/preinput` | notify | `kernel/private/classes/ezpkernelweb.php:1661` |
| `request/input` | notify | `kernel/private/classes/ezpkernelweb.php:1786` |
| `response/preoutput` | filter | `kernel/private/classes/global_functions.php:83` |
| `response/output` | filter | `kernel/private/classes/global_functions.php:102` |
