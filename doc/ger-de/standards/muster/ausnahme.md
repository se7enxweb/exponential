---
title: Ausnahmehierarchie
audience: developer
category: standards
order: 335
summary: Fehler als Klassen, damit Aufrufer abfangen, was sie behandeln können. 51 Instanzen in Exponential.
keywords: Entwurfsmuster, Ausnahme, Struktur, Musterkatalog, design pattern, exception, pattern catalogue
type: catalogue
---
# Ausnahmehierarchie

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Fehler als Klassen, damit Aufrufer abfangen, was sie behandeln können.

|  |  |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 51 |
| Erkannt an | Erweitert Exception, eine *Exception-Klasse oder Error. |

## Klassen nach Bereich

### extension/expchangeclass (1)

[Alle Muster von extension/expchangeclass](bereich-extension-expchangeclass.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expChangeClassException` | class extends Exception | `extension/expchangeclass/classes/functions.php:35` | Wird ausgelöst, wenn eine Konvertierung nicht abgeschlossen werden kann. | – |

### extension/expservices (1)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expServiceException` | class extends Exception | `extension/expservices/classes/expservicebase.php:20` | Die Basis jeder Domänenklasse von expservices: eine öffentliche statische Methode pro Dienst, aufgerufen als ezjscore/call/exp::[::arg...]. | – |

### extension/exp_oe_tiptap (1)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapAIException` | class extends Exception | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexception.php:18` | Ein Fehler der KI-Hooks, dessen Meldung dem Redakteur angezeigt werden darf (ezjscore sendet Ausnahmemeldungen als error_text an den Browser). | – |

### extension/ezfind (2)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfBaseException` | abstract class extends ezcBaseException | `extension/ezfind/classes/exceptions/ezfexception.php:15` | Basisausnahmeklasse für eZ Find | – |
| `ezfSolrException` | class extends ezfBaseException | `extension/ezfind/classes/exceptions/ezfsolrexception.php:15` | Ausnahme für Solr-Probleme | – |

### extension/ezoe (1)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEUrlException` | class extends RuntimeException | `extension/ezoe/classes/expoeurlexception.php:10` | Ein Grund, aus dem der Online-Editor es ablehnt, eine Datei von einer URL abzurufen; die Meldung ist für den Benutzer bestimmt. | – |

### extension/sevenx_dse (1)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\EzExit` | class extends RuntimeException | `extension/sevenx_dse/adminneo/core/EzExit.php:13` | Wird anstelle von exit() ausgelöst, wenn AdminNeo in Exponential eingebettet ist. | – |

### kernel/classes/contentjob (1)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJobException` | class extends Exception | `kernel/classes/contentjob/expcontentjobexception.php:13` | Der Fehler, mit dem ein Inhaltsjob ablehnt oder fehlschlägt; seine Meldung wird dem Benutzer angezeigt. | – |

### kernel/classes/ini (1)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniException` | class extends Exception | `kernel/classes/ini/expiniexception.php:15` | Ein Fehler des INI-Editors. | – |

### kernel/classes/logviewer (1)

[Alle Muster von kernel/classes/logviewer](bereich-kernel-classes-logviewer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogSearchLimitException` | class extends RuntimeException | `kernel/classes/logviewer/explogsearch.php:502` | Ein regulärer Ausdruck, der in einer Zeile pcre.backtrack_limit (oder eine andere PCRE-Grenze) überschritten hat. | – |

### kernel/private (24)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpCacheClusterException` | class extends ezcCacheException | `kernel/private/rest/classes/cache/exceptions/cluster.php:15` | Generische Ausnahmeklasse für ezpCacheCluster | – |
| `ezpContentAccessDeniedException` | class extends ezpContentException | `kernel/private/api/content/exceptions/access_denied.php:12` | – | – |
| `ezpContentException` | abstract class extends ezcBaseException | `kernel/private/api/content/exceptions/content_exception.php:17` | Dies ist die Basisausnahme der Inhalts-API von Exponential | – |
| `ezpContentFieldNotFoundException` | class extends ezpContentException | `kernel/private/api/content/exceptions/content_field_not_found.php:11` | – | – |
| `ezpContentNotFoundException` | class extends ezpContentException | `kernel/private/api/content/exceptions/content_not_found.php:12` | – | – |
| `ezpOauthBadRequestException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/bad_request.php:17` | Dies ist die Basisausnahme zum Auslösen einer BAD-REQUEST-Antwort. | – |
| `ezpOauthException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/oauth.php:17` | Dies ist die Basisausnahme der OAuth-Bibliothek von Exponential. | – |
| `ezpOauthExpiredTokenException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/expired_token.php:18` | Diese Ausnahme wird ausgelöst, wenn das Zugriffstoken abgelaufen ist, aber erneuert werden kann. | – |
| `ezpOauthInsufficientScopeException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/insufficient_scope.php:18` | Diese Ausnahme wird ausgelöst, wenn eine Anfrage mit einem Token gestellt wird, dessen Scope nicht ausreicht, damit die Anfrage angenommen wird. | – |
| `ezpOauthInvalidRequestException` | class extends ezpOauthBadRequestException | `kernel/private/rest/classes/exceptions/invalid_request.php:21` | Diese Ausnahme wird ausgelöst, wenn eine Anfrage ungültig ist. | – |
| `ezpOauthInvalidTokenException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/invalid_token.php:19` | Diese Ausnahme wird ausgelöst, wenn ein Token ungültig ist. | – |
| `ezpOauthNoAuthInfoException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/missing_auth_info.php:18` | Diese Ausnahme wird ausgelöst, wenn der Client in der Anfrage keine Authentifizierungsinformationen angegeben hat. | – |
| `ezpOauthRequiredException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/auth.php:17` | Dies ist die Basisausnahme für Antworten, die eine Authentifizierung erfordern. | – |
| `ezpOauthTokenNotFoundException` | class extends ezpOauthException | `kernel/private/rest/classes/exceptions/oauth_token_not_found.php:17` | Dies ist die Basisausnahme der OAuth-Bibliothek von Exponential. | – |
| `ezpRestAuthStyleNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/auth_style_not_found.php:12` | – | – |
| `ezpRestContentRendererNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/content_renderer_not_found.php:12` | – | – |
| `ezpRestException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/rest_exception.php:17` | Dies ist die Basisausnahme der REST-Schicht von Exponential | – |
| `ezpRestFilterMissingParameterException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/filter_param_missing.php:12` | – | – |
| `ezpRestFilterNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/filter_not_found.php:12` | – | – |
| `ezpRestHTTPSRequiredException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/https_required.php:12` | – | – |
| `ezpRestProviderNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/rest_provider_not_found.php:12` | – | – |
| `ezpRestRouteSecurityFilterNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/route_security_filter_not_found.php:12` | – | – |
| `ezpRouteMethodNotAllowedException` | class extends ezcMvcToolsException | `kernel/private/rest/classes/exceptions/route_method_not_allowed.php:12` | – | – |
| `ezpUserNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/user_not_found.php:11` | – | – |

### kernel/private/classes/clusterfilehandlers (1)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerBackendException` | class extends Exception | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerbackendexception.php:10` | Diese Datei ist Teil des Exponential-Pakets | – |

### kernel/private/classes/exceptions (14)

[Alle Muster von kernel/private/classes/exceptions](bereich-kernel-private-classes-exceptions.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZClusterHandlerDBNoConnectionException` | class extends eZDBException | `kernel/private/classes/exceptions/cluster/noconnection.php:19` | Klasse für die Ausnahme „keine Cluster-Verbindung“ | – |
| `eZClusterHandlerDBNoDatabaseException` | class extends eZDBException | `kernel/private/classes/exceptions/cluster/nodatabase.php:19` | Klasse für die Ausnahme „keine Datenbank“ | – |
| `eZDBException` | class extends ezcBaseException | `kernel/private/classes/exceptions/database/exception.php:26` | Klasse, die die oberste Klasse für alle datenbankbezogenen Ausnahmen darstellt | – |
| `eZDBNoConnectionException` | class extends eZDBException | `kernel/private/classes/exceptions/database/noconnection.php:19` | Klasse für die Ausnahme „keine Verbindung“ | – |
| `eZDFSFileHandlerNFSMountPointNotFoundException` | class extends ezcBaseException | `kernel/private/classes/exceptions/cluster/mount_point_not_found.php:19` | Klasse für die Ausnahme „Cluster-Mountpunkt nicht gefunden“ | – |
| `eZDFSFileHandlerNFSMountPointNotWriteableException` | class extends ezcBaseException | `kernel/private/classes/exceptions/cluster/mount_point_not_writeable.php:19` | Klasse für die Ausnahme „Cluster-Mountpunkt nicht beschreibbar“ | – |
| `eZDFSFileHandlerTableNotFoundException` | class extends ezcBaseException | `kernel/private/classes/exceptions/cluster/table_not_found.php:19` | Klasse für die Ausnahme „Cluster-Tabelle nicht gefunden“ | – |
| `ezpAccessDenied` | class extends Exception | `kernel/private/classes/exceptions/kernel/accessdenied.php:17` | Ausnahme, die auftritt, wenn der Zugriff verweigert wird. | – |
| `ezpFormTokenException` | class extends Exception | `kernel/private/classes/exceptions/kernel/formtoken.php:23` | Ein POST wurde abgelehnt, weil sein Formular-Token (CSRF-Token) fehlte oder nicht zur Sitzung gehörte: das Formular wurde in einer anderen Sitzung geladen, bevor e… | – |
| `ezpLanguageNotFound` | class extends Exception | `kernel/private/classes/exceptions/kernel/languagenotfound.php:17` | Ausnahme, die auftritt, wenn eine unbekannte Sprache verwendet wird. | – |
| `ezpModuleDisabled` | class extends Exception | `kernel/private/classes/exceptions/kernel/moduledisabled.php:17` | Ausnahme, die auftritt, wenn ein Modul deaktiviert ist. | – |
| `ezpModuleNotFound` | class extends Exception | `kernel/private/classes/exceptions/kernel/modulenotfound.php:17` | Ausnahme, die auftritt, wenn ein Modul nicht gefunden wird. | – |
| `ezpModuleViewDisabled` | class extends Exception | `kernel/private/classes/exceptions/kernel/moduleviewdisabled.php:17` | Ausnahme, die auftritt, wenn ein Modul/eine Ansicht deaktiviert ist. | – |
| `ezpModuleViewNotFound` | class extends Exception | `kernel/private/classes/exceptions/kernel/moduleviewnotfound.php:17` | Ausnahme, die auftritt, wenn ein Modul/eine Ansicht nicht gefunden wird. | – |

### lib/eztemplate (1)

[Alle Muster von lib/eztemplate](bereich-lib-eztemplate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplateFailedExecutingCompiledTemplate` | class extends Exception | `lib/eztemplate/classes/exceptions/failedexecutingcompiledtemplate.php:17` | Ausnahme, die auftritt, wenn die Ausführung eines kompilierten Templates fehlschlägt | – |

### lib/ezutils (1)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFatalUserError` | class extends Error | `lib/ezutils/classes/ezfatalusererror.php:35` | Ein schwerer Fehler, den der Kernel selbst auslöst, anstelle eines Benutzerfehlers der Stufe E_USER_ERROR, den PHP 8.4 für trigger_error() als veraltet markiert („throw a… | – |
