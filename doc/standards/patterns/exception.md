---
title: Exception hierarchy
audience: developer
category: standards
order: 335
summary: Errors as classes, so callers catch what they can handle. 51 instances in Exponential.
keywords: design pattern, exception, structure, pattern catalogue
type: catalogue
---
# Exception hierarchy

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Errors as classes, so callers catch what they can handle.

| | |
|---|---|
| Group | Structure |
| Instances | 51 |
| Recognized by | Extends Exception, an *Exception class or Error. |

## Classes by area

### extension/expchangeclass (1)

[All patterns of extension/expchangeclass](area-extension-expchangeclass.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expChangeClassException` | class extends Exception | `extension/expchangeclass/classes/functions.php:35` | Raised when a conversion cannot be completed. | – |

### extension/expservices (1)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expServiceException` | class extends Exception | `extension/expservices/classes/expservicebase.php:20` | The base of every expservices domain class: one public static method per service, called as ezjscore/call/exp::[::arg...]. | – |

### extension/exp_oe_tiptap (1)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapAIException` | class extends Exception | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexception.php:18` | An error of the AI hooks whose message may be shown to the editor (ezjscore sends exception messages to the browser as error_text). | – |

### extension/ezfind (2)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfBaseException` | abstract class extends ezcBaseException | `extension/ezfind/classes/exceptions/ezfexception.php:15` | Base exception class for eZ Find | – |
| `ezfSolrException` | class extends ezfBaseException | `extension/ezfind/classes/exceptions/ezfsolrexception.php:15` | Exception for Solr issues | – |

### extension/ezoe (1)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEUrlException` | class extends RuntimeException | `extension/ezoe/classes/expoeurlexception.php:10` | A reason the online editor refuses to fetch a file from a URL; the message is meant for the user. | – |

### extension/sevenx_dse (1)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AdminNeo\EzExit` | class extends RuntimeException | `extension/sevenx_dse/adminneo/core/EzExit.php:13` | Thrown instead of exit() when AdminNeo is embedded inside Exponential. | – |

### kernel/classes/contentjob (1)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJobException` | class extends Exception | `kernel/classes/contentjob/expcontentjobexception.php:13` | The error a content job refuses or fails with; its message is shown to the user. | – |

### kernel/classes/ini (1)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniException` | class extends Exception | `kernel/classes/ini/expiniexception.php:15` | An error of the INI editor. | – |

### kernel/classes/logviewer (1)

[All patterns of kernel/classes/logviewer](area-kernel-classes-logviewer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogSearchLimitException` | class extends RuntimeException | `kernel/classes/logviewer/explogsearch.php:502` | A regular expression that exceeded pcre.backtrack_limit (or another PCRE limit) on a line. | – |

### kernel/private (24)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpCacheClusterException` | class extends ezcCacheException | `kernel/private/rest/classes/cache/exceptions/cluster.php:15` | Generic exception class for ezpCacheCluster | – |
| `ezpContentAccessDeniedException` | class extends ezpContentException | `kernel/private/api/content/exceptions/access_denied.php:12` | – | – |
| `ezpContentException` | abstract class extends ezcBaseException | `kernel/private/api/content/exceptions/content_exception.php:17` | This is the base exception for the Exponential content API | – |
| `ezpContentFieldNotFoundException` | class extends ezpContentException | `kernel/private/api/content/exceptions/content_field_not_found.php:11` | – | – |
| `ezpContentNotFoundException` | class extends ezpContentException | `kernel/private/api/content/exceptions/content_not_found.php:12` | – | – |
| `ezpOauthBadRequestException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/bad_request.php:17` | This is the base exception for triggering BAD REQUEST response. | – |
| `ezpOauthException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/oauth.php:17` | This is the base exception for the Exponential oauth library. | – |
| `ezpOauthExpiredTokenException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/expired_token.php:18` | This exception is thrown when the accept token has expired, but can be renewed. | – |
| `ezpOauthInsufficientScopeException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/insufficient_scope.php:18` | This exception is thrown when a request is made with a token not repsresenting sufficient scope for the request to be accepted. | – |
| `ezpOauthInvalidRequestException` | class extends ezpOauthBadRequestException | `kernel/private/rest/classes/exceptions/invalid_request.php:21` | This exception is thrown when a request is invalid. | – |
| `ezpOauthInvalidTokenException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/invalid_token.php:19` | This exception is thrown when a token is invalid. | – |
| `ezpOauthNoAuthInfoException` | class extends ezpOauthRequiredException | `kernel/private/rest/classes/exceptions/missing_auth_info.php:18` | This exception is thrown when the client did not provide any authentication information in the request. | – |
| `ezpOauthRequiredException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/auth.php:17` | This is the base exception for responsenses need authentication. | – |
| `ezpOauthTokenNotFoundException` | class extends ezpOauthException | `kernel/private/rest/classes/exceptions/oauth_token_not_found.php:17` | This is the base exception for the Exponential oauth library. | – |
| `ezpRestAuthStyleNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/auth_style_not_found.php:12` | – | – |
| `ezpRestContentRendererNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/content_renderer_not_found.php:12` | – | – |
| `ezpRestException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/rest_exception.php:17` | This is the base exception for the Exponential REST layer | – |
| `ezpRestFilterMissingParameterException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/filter_param_missing.php:12` | – | – |
| `ezpRestFilterNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/filter_not_found.php:12` | – | – |
| `ezpRestHTTPSRequiredException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/https_required.php:12` | – | – |
| `ezpRestProviderNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/rest_provider_not_found.php:12` | – | – |
| `ezpRestRouteSecurityFilterNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/route_security_filter_not_found.php:12` | – | – |
| `ezpRouteMethodNotAllowedException` | class extends ezcMvcToolsException | `kernel/private/rest/classes/exceptions/route_method_not_allowed.php:12` | – | – |
| `ezpUserNotFoundException` | class extends ezpRestException | `kernel/private/rest/classes/exceptions/user_not_found.php:11` | – | – |

### kernel/private/classes/clusterfilehandlers (1)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandlerBackendException` | class extends Exception | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerbackendexception.php:10` | This file is part of the Exponential package | – |

### kernel/private/classes/exceptions (14)

[All patterns of kernel/private/classes/exceptions](area-kernel-private-classes-exceptions.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZClusterHandlerDBNoConnectionException` | class extends eZDBException | `kernel/private/classes/exceptions/cluster/noconnection.php:19` | Class representing a cluster no connection exception | – |
| `eZClusterHandlerDBNoDatabaseException` | class extends eZDBException | `kernel/private/classes/exceptions/cluster/nodatabase.php:19` | Class representing a no database exception | – |
| `eZDBException` | class extends ezcBaseException | `kernel/private/classes/exceptions/database/exception.php:26` | Class representing a the top class for any database related exception | – |
| `eZDBNoConnectionException` | class extends eZDBException | `kernel/private/classes/exceptions/database/noconnection.php:19` | Class representing a no connection exception | – |
| `eZDFSFileHandlerNFSMountPointNotFoundException` | class extends ezcBaseException | `kernel/private/classes/exceptions/cluster/mount_point_not_found.php:19` | Class representing a cluster mount point not found exception | – |
| `eZDFSFileHandlerNFSMountPointNotWriteableException` | class extends ezcBaseException | `kernel/private/classes/exceptions/cluster/mount_point_not_writeable.php:19` | Class representing a cluster mount point not writeable exception | – |
| `eZDFSFileHandlerTableNotFoundException` | class extends ezcBaseException | `kernel/private/classes/exceptions/cluster/table_not_found.php:19` | Class handling a cluster table not found exception | – |
| `ezpAccessDenied` | class extends Exception | `kernel/private/classes/exceptions/kernel/accessdenied.php:17` | Exception occuring when access is denied. | – |
| `ezpFormTokenException` | class extends Exception | `kernel/private/classes/exceptions/kernel/formtoken.php:23` | A POST was refused because its form token (CSRF token) was missing or did not belong to the session: the form was loaded in another session, before a… | – |
| `ezpLanguageNotFound` | class extends Exception | `kernel/private/classes/exceptions/kernel/languagenotfound.php:17` | Exception occuring when an unknown language is used. | – |
| `ezpModuleDisabled` | class extends Exception | `kernel/private/classes/exceptions/kernel/moduledisabled.php:17` | Exception occuring when a module is disabled. | – |
| `ezpModuleNotFound` | class extends Exception | `kernel/private/classes/exceptions/kernel/modulenotfound.php:17` | Exception occuring when a module is not found. | – |
| `ezpModuleViewDisabled` | class extends Exception | `kernel/private/classes/exceptions/kernel/moduleviewdisabled.php:17` | Exception occuring when a module/view is disabled. | – |
| `ezpModuleViewNotFound` | class extends Exception | `kernel/private/classes/exceptions/kernel/moduleviewnotfound.php:17` | Exception occuring when a module/view is not found. | – |

### lib/eztemplate (1)

[All patterns of lib/eztemplate](area-lib-eztemplate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplateFailedExecutingCompiledTemplate` | class extends Exception | `lib/eztemplate/classes/exceptions/failedexecutingcompiledtemplate.php:17` | Exception occuring when the execution of a compiled template fails | – |

### lib/ezutils (1)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFatalUserError` | class extends Error | `lib/ezutils/classes/ezfatalusererror.php:35` | A fatal error raised by the kernel itself, in place of a user error of the level E_USER_ERROR, which PHP 8.4 deprecates for trigger_error() ("throw a… | – |
