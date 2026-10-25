---
title: Patterns in kernel/private
audience: developer
category: standards
order: 526
summary: Every class, view, fetch function, operator and plugin point of kernel/private with its design patterns.
keywords: kernel/private, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (181)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expApiKeyAuthFilter` | class extends ezcAuthenticationFilter | [Decorator / filter](decorator.md) | `kernel/private/rest/classes/auth/apikey_auth_filter.php:19` | Authentication filter for a personal API key (expApiKey) presented as "Authorization: Bearer expk_..." (doc/eng-us/guides/api-keys.md, "How the REST… |
| `expApiKeyRest` | class | [Utility (static helper)](utility.md) | `kernel/private/rest/classes/auth/apikey_rest.php:24` | How the REST layer resolves a personal API key (doc/eng-us/guides/api-keys.md, "For developers"). |
| `expContentAccessReport` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/expcontentaccessreport.php:23` | Answers "may this user do this with this object or node, and if not, which limitation refuses it": the access checks of the kernel made for another u… |
| `expContentSecurityPolicy` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/expcontentsecuritypolicy.php:39` | The Content Security Policy of a page, from site.ini [ContentSecurityPolicy]. |
| `expContentSecurityPolicyReport` | class | – | `kernel/private/classes/expcontentsecuritypolicyreport.php:40` | The reports of the Content Security Policy (doc/eng-us/features/6.0/content-security-policy.md, "Reports"): what the view csp/report receives from th… |
| `expFileConsistencyReport` | class | [Factory and static factory methods](factory.md) | `kernel/private/classes/expfileconsistencyreport.php:36` | Compares the files of an installation with the checksum lists that ship with it: share/filelist.md5 of Exponential and the share/filelist.md5 an exte… |
| `expOAuthAuthorizePolicy` | class | [Utility (static helper)](utility.md) | `kernel/private/rest/classes/oauth/authorize_policy.php:21` | Decides whether a signed in user may authorize an OAuth client in oauth/authorize. |
| `expOAuthAuthorizePolicyGrant` | class | [Utility (static helper)](utility.md) | `kernel/private/rest/classes/oauth/authorize_policy_grant.php:26` | Plans and applies the upgrade grant of the policy oauth/authorize (bin/php/addoauthauthorizepolicy.php). |
| `expOAuthPkce` | class | [Utility (static helper)](utility.md) | `kernel/private/rest/classes/oauth/pkce.php:24` | Proof Key for Code Exchange (PKCE, RFC 7636) for the OAuth authorization code flow. |
| `expRestContentPermission` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/private/rest/classes/auth/content_permission.php:36` | The current user's rights on the content a REST call reads or writes, checked the way the content module checks them (doc/eng-us/guides/api-keys.md,… |
| `expRestCors` | class | [Factory and static factory methods](factory.md) | `kernel/private/rest/classes/cors.php:29` | Cross-origin resource sharing (CORS) for the REST interface (index_rest.php: the content API, the OAuth token end point and every other route of a RE… |
| `expRestPasswordAuthFilter` | class extends ezcAuthenticationFilter | [Decorator / filter](decorator.md) | `kernel/private/rest/classes/auth/password_auth_filter.php:23` | Checks a login and password for the REST interface's basic authentication the way the sign-in does. |
| `expRestPasswordChangeRequiredStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/password_change_required.php:15` | 403 Forbidden with the error code password_change_required: the credentials are right, but the account is marked "change password at next sign-in" (e… |
| `expRestRateLimitedStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/rate_limited.php:14` | 429 Too Many Requests with Retry-After, for a personal API key over its rate limit (rest.ini [ApiKeySettings] RateLimitPerMinute). |
| `expRestSecondFactorRequiredStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/second_factor_required.php:16` | The REST answer to a password that was right for an account that needs a second sign-in step (expSignInSecondFactor): 401 with WWW-Authenticate (an a… |
| `expSchemaConsistencyReport` | class | [Factory and static factory methods](factory.md) | `kernel/private/classes/expschemaconsistencyreport.php:36` | Compares the database with the schema that ships with Exponential and its active extensions (share/db_schema.dba of the root and of each extension),… |
| `eZAutoloadGenerator` | class | [Builder](builder.md), [Factory and static factory methods](factory.md) | `kernel/private/classes/ezautoloadgenerator.php:19` | Utility class for generating autoload arrays for Exponential. |
| `eZClusterFileHandlerInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/interfaces/ezclusterfilehandlerinterface.php:15` | Cluster file handlers interface |
| `eZContentObjectState` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/ezcontentobjectstate.php:18` | Class representing a content object state |
| `eZContentObjectStateGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/ezcontentobjectstategroup.php:18` | Class respresenting a content object state group |
| `eZContentObjectStateGroupLanguage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/ezcontentobjectstategrouplanguage.php:21` | This class takes care of the localization of a content object state group. |
| `eZContentObjectStateLanguage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/ezcontentobjectstatelanguage.php:21` | This class takes care of the localization of a content object state. |
| `ezpActiveExtensions` | class | [Factory and static factory methods](factory.md) | `kernel/private/classes/ezpactiveextensions.php:26` | Changes [ExtensionSettings] ActiveExtensions in settings/override/site.ini.append.php, for Setup › Extensions, without touching anything else in that… |
| `ezpAsynchronousPublisherOutput` | interface | [Interface (contract)](interface.md) | `kernel/private/interfaces/asynchronouspublisheroutput.php:16` | This interface is used as the basis for the ezpasynchronouspublisher.php daemon |
| `ezpAsynchronousPublishingFilter` | abstract class implements ezpAsynchronousPublishingFilterInterface | [Decorator / filter](decorator.md), [Template Method / abstract base class](template-method.md) | `kernel/private/classes/ezpasynchronouspublishingfilter.php:24` | This interface is to be used to create accept/reject filters for the Asynchronous Publishing feature Each filter class must implement the accept() me… |
| `ezpAsynchronousPublishingFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `kernel/private/interfaces/asynchronouspublishingfilter.php:17` | Base interface for eZ Asynchronous Publishing filter classes |
| `ezpAttributeOperatorFormatter` | class | – | `kernel/private/eztemplate/ezpattributeoperatorformatter.php:12` | – |
| `ezpAttributeOperatorFormatterInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php:12` | – |
| `ezpAttributeOperatorHTMLFormatter` | class extends ezpAttributeOperatorFormatter implements ezpAttributeOperatorFormatterInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/eztemplate/ezpattributeoperatorhtmlformatter.php:12` | – |
| `ezpAttributeOperatorManager` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – |
| `ezpAttributeOperatorTextFormatter` | class extends ezpAttributeOperatorFormatter implements ezpAttributeOperatorFormatterInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/eztemplate/ezpattributeoperatortextformatter.php:12` | – |
| `ezpAutoloadCliOutput` | class implements ezpAutoloadOutput | – | `kernel/private/classes/ezpautoloadclioutput.php:17` | Utility class for providing CLI output and incremental progress information. |
| `ezpAutoloadFileFindContext` | class extends ezcBaseStruct | [Value object / struct](value-object.md) | `kernel/private/structs/ezpautoloadfilefindcontext.php:17` | Struct which defines the information collected by the file walker for locating files. |
| `ezpAutoloadGeneratorOptions` | class extends ezcBaseOptions | – | `kernel/private/options/ezpautoloadgeneratoroptions.php:55` | Class containing options for eZAutoloadGenerator |
| `ezpAutoloadOutput` | interface | [Interface (contract)](interface.md) | `kernel/private/interfaces/ezpautoloadoutput.php:17` | Interface for classes providing output for autoload generation. |
| `ezpCacheClusterException` | class extends ezcCacheException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/cache/exceptions/cluster.php:15` | Generic exception class for ezpCacheCluster |
| `ezpCacheStorageClusterOptions` | class extends ezcBaseOptions | – | `kernel/private/rest/classes/cache/options/storage_cluster.php:15` | Option class for the ezpCacheStorageCluster class. |
| `ezpContent` | class | [Factory and static factory methods](factory.md) | `kernel/private/api/content/content.php:56` | Class that represents a content item. |
| `ezpContentAccessDeniedException` | class extends ezpContentException | [Exception hierarchy](exception.md) | `kernel/private/api/content/exceptions/access_denied.php:12` | – |
| `ezpContentClassCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/class.php:16` | This class allows for configuration of a content class based criteria |
| `ezpContentCriteria` | class | – | `kernel/private/api/content/criteria.php:28` | This class is used to instantiate and manipulate content listing criterias. |
| `ezpContentCriteriaInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/api/interfaces/content_criteria.php:16` | This interface is the base one when it comes to implementing content query criteria |
| `ezpContentCriteriaSet` | class implements ArrayAccess, Countable, Iterator | [Iterator](iterator.md) | `kernel/private/api/content/criteria_set.php:16` | This class handles a list of content query criteria |
| `ezpContentDepthCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/depth.php:16` | Depth criteria |
| `ezpContentException` | abstract class extends ezcBaseException | [Exception hierarchy](exception.md), [Template Method / abstract base class](template-method.md) | `kernel/private/api/content/exceptions/content_exception.php:17` | This is the base exception for the Exponential content API |
| `ezpContentField` | class | [Factory and static factory methods](factory.md) | `kernel/private/api/content/field.php:17` | This class handles content fields. |
| `ezpContentFieldCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/field.php:17` | This class is used to instantiate and manipulate a field value content criteria. |
| `ezpContentFieldNotFoundException` | class extends ezpContentException | [Exception hierarchy](exception.md) | `kernel/private/api/content/exceptions/content_field_not_found.php:11` | – |
| `ezpContentFieldSet` | class implements ArrayAccess, Iterator | [Factory and static factory methods](factory.md), [Iterator](iterator.md) | `kernel/private/api/content/field_set.php:18` | Allows for reading/writing of content fields (attributes) through an array like interface. |
| `ezpContentLimitation` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/ezpcontentlimitation.php:26` | Asks the handler an extension registered for a content policy limitation the kernel does not know (site.ini [RoleSettings] LimitationHandlers[]=, see… |
| `ezpContentLimitationHandler` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/interfaces/ezpcontentlimitationhandler.php:29` | Evaluates a policy limitation of the content module that the kernel does not know itself. |
| `ezpContentLimitationSolrHandler` | interface extends ezpContentLimitationHandler | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/interfaces/ezpcontentlimitationsolrhandler.php:23` | A content limitation handler that can also restrict a search in a search engine that filters by the policies of the user itself, such as eZ Find with… |
| `ezpContentLimitCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/limit.php:16` | This class allows for configuration of an offset/limit based criteria |
| `ezpContentList` | class implements Countable, Iterator | [Iterator](iterator.md) | `kernel/private/api/content/list.php:18` | This class lets you browse a previously fetched content list, for instance using ezpContentRepository::query() It is more or less a ResultSet from a… |
| `ezpContentLocation` | class extends ezpLocation | [Factory and static factory methods](factory.md) | `kernel/private/api/content/location.php:21` | This class is used to manipulate a specific content location. |
| `ezpContentLocationCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/location.php:16` | This class allows for configuration of a location based criteria |
| `ezpContentLocationSet` | class | – | `kernel/private/api/content/location_set.php:15` | This class represents the locations for a piece of content |
| `ezpContentNotFoundException` | class extends ezpContentException | [Exception hierarchy](exception.md) | `kernel/private/api/content/exceptions/content_not_found.php:12` | – |
| `ezpContentPublishingProcess` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/private/classes/ezpcontentpublishingprocess.php:18` | This class provides a PersistentObject interface to a publishing queue process. |
| `ezpContentPublishingQueue` | class implements ezpContentPublishingQueueReaderInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `kernel/private/classes/ezpcontentpublishingqueue.php:19` | This class manages the content publishing queue. |
| `ezpContentPublishingQueueProcessor` | class | [Singleton / instance()](singleton.md) | `kernel/private/classes/ezpcontentpublishingqueueprocessor.php:21` | This class manages the publishing queue through ezpContentPublishingProcess persistent objects |
| `ezpContentPublishingQueueReaderInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/interfaces/ezpcontentpublishingqueuereader.php:18` | This interface describes an asynchronous publishing queue reader Queue readers are meant to provide the asynchronous publishing system with the next… |
| `ezpContentRepository` | class | [Utility (static helper)](utility.md) | `kernel/private/api/content/repository.php:16` | This class handles querying of content repository |
| `ezpContentSortingCriteria` | class implements ezpContentCriteriaInterface | – | `kernel/private/api/content/criteria/sorting.php:16` | Sorting criteria |
| `ezpContentXHTMLRenderer` | class extends ezpRestContentRendererInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/rest/classes/renderers/xhtml_content_renderer.php:12` | – |
| `ezpDatabaseBasedClusterFileHandler` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/interfaces/ezpdatabasebasedclusterfilehandler.php:20` | This interface describes a database based cluster file handler |
| `ezpEvent` | class | [Observer / listener / event](observer.md), [Singleton / instance()](singleton.md) | `kernel/private/classes/ezpevent.php:20` | This class handles internal kernel events in Exponential, aka hooks. |
| `ezpExtension` | class | [Singleton / instance()](singleton.md) | `kernel/private/classes/ezpextension.php:15` | Object representing an Exponential extension |
| `ezpExtensionOptions` | class extends ezcBaseOptions | – | `kernel/private/options/ezpextensionoptions.php:58` | Class containing options for eZExtension, this option system is only used by eZExtension::getHandlerClass so far. |
| `ezpFormTokenRefusal` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/ezpformtokenrefusal.php:27` | Turns an ezpFormTokenException -- a POST whose form token was missing or wrong -- into a refusal: HTTP 403, Cache-Control: no-store, the security hea… |
| `ezpHttpResponseCodes` | class | – | `kernel/private/rest/classes/http_response_codes.php:12` | – |
| `ezpKernel` | class implements ezpWebBasedKernelHandler | [Front Controller](front-controller.md), [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/ezpkernel.php:16` | Base Exponential kernel class. |
| `ezpKernelHandler` | interface | [Front Controller](front-controller.md), [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/interfaces/ezpkernelhandler.php:14` | Interface for kernel handlers |
| `ezpKernelRedirect` | class extends ezpKernelResult | – | `kernel/private/classes/ezpkernelredirect.php:14` | Struct containing information on HTTP redirection. |
| `ezpKernelRest` | class implements ezpKernelHandler | [Front Controller](front-controller.md), [Strategy / handler](strategy-handler.md) | `kernel/private/rest/classes/ezpkernelrest.php:11` | – |
| `ezpKernelResult` | class | – | `kernel/private/classes/ezpkernelresult.php:14` | Struct containing the kernel result. |
| `ezpKernelTreeMenu` | class implements ezpKernelHandler | [Front Controller](front-controller.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/ezpkerneltreemenu.php:11` | – |
| `ezpKernelWeb` | class implements ezpWebBasedKernelHandler | [Front Controller](front-controller.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/ezpkernelweb.php:16` | Provides a kernel handler in web context |
| `ezpLanguageSwitcher` | class implements ezpLanguageSwitcherCapable | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/ezplanguageswitcher.php:18` | Utility class for transforming URLs between siteaccesses. |
| `ezpLanguageSwitcherCapable` | interface | [Interface (contract)](interface.md) | `kernel/private/interfaces/ezplanguageswitchercapable.php:17` | Interface for classes providing language switcher functionality. |
| `ezpLanguageSwitcherFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `kernel/private/modules/switchlanguage/ezpLanguageSwitcherFunctionCollection.php:14` | – |
| `ezpLanguageSwitcherOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/private/eztemplate/ezplanguageswitcheroperator.php:12` | – |
| `ezpLocation` | class | – | `kernel/private/api/location.php:16` | This class is used to manipulate locations |
| `ezpMail` | class extends ezcMail | – | `kernel/private/classes/ezpmail.php:14` | ezpMail extends ezcMail in order to override default values and limitations. |
| `ezpMobileDeviceDetect` | class | – | `kernel/private/classes/ezpmobiledevicedetect.php:15` | ezpMobileDeviceDetect class implementation |
| `ezpMobileDeviceDetectFilter` | class | [Decorator / filter](decorator.md) | `kernel/private/classes/ezpmobiledevicedetectfilter.php:12` | – |
| `ezpMobileDeviceDetectFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php:12` | – |
| `ezpMobileDeviceRegexpFilter` | class implements ezpMobileDeviceDetectFilterInterface | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/classes/ezpmobiledeviceregexpfilter.php:15` | Default implementation of mobile device detect interface. |
| `ezpMultivariateTest` | class | – | `kernel/private/classes/ezpmultivariatetest.php:12` | – |
| `ezpMultivariateTestHandler` | class implements ezpMultivariateTestHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/ezpmultivariatetesthandler.php:12` | – |
| `ezpMultivariateTestHandlerInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/ezpmultivariatetesthandlerinterface.php:12` | – |
| `ezpMvcConfigurableDispatcher` | class extends ezcMvcConfigurableDispatcher | – | `kernel/private/rest/classes/dispatcher.php:12` | – |
| `ezpMvcConfiguration` | class implements ezcMvcDispatcherConfiguration | – | `kernel/private/rest/classes/mvc_configuration.php:11` | – |
| `ezpMvcRailsRoute` | class extends ezcMvcRailsRoute | – | `kernel/private/rest/classes/routes/rails.php:11` | – |
| `ezpMvcRegexpRoute` | class extends ezcMvcRegexpRoute | [Front Controller](front-controller.md) | `kernel/private/rest/classes/routes/regexp.php:16` | Override of ezcMvcRegexpRoute. |
| `ezpNativeUserAuthFilter` | class extends ezcAuthenticationFilter | [Decorator / filter](decorator.md) | `kernel/private/rest/classes/auth/native_user_authfilter.php:11` | – |
| `ezpOauthBadRequestException` | abstract class extends ezpOauthException | [Exception hierarchy](exception.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/exceptions/bad_request.php:17` | This is the base exception for triggering BAD REQUEST response. |
| `ezpOauthErrorType` | class | – | `kernel/private/rest/classes/outh_error_type.php:12` | – |
| `ezpOauthException` | abstract class extends ezcBaseException | [Exception hierarchy](exception.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/exceptions/oauth.php:17` | This is the base exception for the Exponential oauth library. |
| `ezpOauthExpiredTokenException` | class extends ezpOauthRequiredException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/expired_token.php:18` | This exception is thrown when the accept token has expired, but can be renewed. |
| `ezpOauthFilter` | class extends ezcAuthenticationFilter | [Decorator / filter](decorator.md) | `kernel/private/rest/classes/oauth/oauth_filter.php:12` | – |
| `ezpOauthInsufficientScopeException` | class extends ezpOauthRequiredException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/insufficient_scope.php:18` | This exception is thrown when a request is made with a token not repsresenting sufficient scope for the request to be accepted. |
| `ezpOauthInvalidRequestException` | class extends ezpOauthBadRequestException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/invalid_request.php:21` | This exception is thrown when a request is invalid. |
| `ezpOauthInvalidTokenException` | class extends ezpOauthRequiredException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/invalid_token.php:19` | This exception is thrown when a token is invalid. |
| `ezpOauthNoAuthInfoException` | class extends ezpOauthRequiredException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/missing_auth_info.php:18` | This exception is thrown when the client did not provide any authentication information in the request. |
| `ezpOauthRequired` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/oauth_required.php:15` | This result type is used to signal a HTTP basic auth header |
| `ezpOauthRequiredException` | abstract class extends ezpOauthException | [Exception hierarchy](exception.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/exceptions/auth.php:17` | This is the base exception for responsenses need authentication. |
| `ezpOauthTokenEndpointErrorType` | class | – | `kernel/private/rest/classes/oauth_token_endpoint_error.php:12` | – |
| `ezpOauthTokenNotFoundException` | class extends ezpOauthException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/oauth_token_not_found.php:17` | This is the base exception for the Exponential oauth library. |
| `ezpOauthUtility` | class extends ezpRestModel | [Utility (static helper)](utility.md) | `kernel/private/rest/classes/oauth/utility.php:17` | Functionality for working against the draft 10 of the oauth2 spec. |
| `ezpRestAuthcode` | class implements ezcPersistentObject | – | `kernel/private/rest/classes/models/ezprest_authcode.php:16` | Data class ezprest_tokens. |
| `ezpRestAuthConfiguration` | class | – | `kernel/private/rest/classes/auth/auth_configuration.php:19` | Class controlling authentication inside REST layer. |
| `ezpRestAuthController` | class extends ezcMvcController | – | `kernel/private/rest/classes/controllers/auth.php:15` | Handles authentication with the REST interface. |
| `ezpRestAuthenticationStyle` | abstract class | [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/auth/styles/abstract_auth_style.php:11` | – |
| `ezpRestAuthenticationStyleInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/rest/classes/auth/auth_style.php:12` | – |
| `ezpRestAuthorizedClient` | class | – | `kernel/private/oauth/classes/restauthorizedclient.php:16` | Persistent object class representing a REST application authorization by a user. |
| `ezpRestAuthProvider` | class implements ezpRestProviderInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/private/rest/classes/auth/auth_provider.php:11` | – |
| `ezpRestAuthStyleNotFoundException` | class extends ezpRestException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/auth_style_not_found.php:12` | – |
| `ezpRestBasicAuthStyle` | class extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface | – | `kernel/private/rest/classes/auth/styles/basic_auth.php:23` | HTTP basic authentication against the site's users. |
| `ezpRestCacheStorageApcCluster` | class extends ezcCacheStorageApcPlain | – | `kernel/private/rest/classes/cache/apc.php:11` | – |
| `ezpRestCacheStorageCluster` | abstract class extends ezpRestCacheStorageFile implements ezcCacheStackableStorage, ezcCacheStackMetaDataStorage | [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/cache/cluster.php:11` | – |
| `ezpRestCacheStorageClusterObject` | class extends ezpRestCacheStorageCluster | – | `kernel/private/rest/classes/cache/object.php:16` | Cache storage implementation for compatible objects (implementing ezcBaseExportable interface). |
| `ezpRestCacheStorageFile` | abstract class extends ezcCacheStorageFile | – | `kernel/private/rest/classes/cache/storage.php:11` | – |
| `ezpRestClient` | class | – | `kernel/private/oauth/classes/restclient.php:15` | Persistent object class representing a REST application. |
| `ezpRestContentRenderer` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/private/rest/classes/content_renderer.php:12` | – |
| `ezpRestContentRendererInterface` | abstract class | – | `kernel/private/rest/classes/interfaces/content_renderer.php:12` | – |
| `ezpRestContentRendererNotFoundException` | class extends ezpRestException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/content_renderer_not_found.php:12` | – |
| `ezpRestDbConfig` | class implements ezcBaseConfigurationInitializer | [Registry](registry.md), [Utility (static helper)](utility.md) | `kernel/private/rest/classes/lazy.php:12` | – |
| `ezpRestDebug` | class | [Singleton / instance()](singleton.md) | `kernel/private/rest/classes/debug/debug.php:11` | – |
| `ezpRestDebugPHPFormatter` | class implements ezcDebugOutputFormatter | – | `kernel/private/rest/classes/debug/formatters/php_formatter.php:11` | – |
| `ezpRestDefaultRegexpPrefixFilter` | class extends ezpRestPrefixFilterInterface | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/rest/classes/default_regexp_prefix_filter.php:15` | Default implementation of prefix filter interface. |
| `ezpRestErrorController` | class extends ezcMvcController | – | `kernel/private/rest/classes/controllers/error.php:15` | This controller deals with error situations arising in the REST layer. |
| `ezpRestException` | abstract class extends ezcBaseException | [Exception hierarchy](exception.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/exceptions/rest_exception.php:17` | This is the base exception for the Exponential REST layer |
| `ezpRestFeedDecorator` | class | [Decorator / filter](decorator.md) | `kernel/private/rest/classes/views/feed_decorator.php:24` | This class decorates the feed provided in the REST interface for content sync mechanisms. |
| `ezpRestFilterMissingParameterException` | class extends ezpRestException | [Decorator / filter](decorator.md), [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/filter_param_missing.php:12` | – |
| `ezpRestFilterNotFoundException` | class extends ezpRestException | [Decorator / filter](decorator.md), [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/filter_not_found.php:12` | – |
| `ezpRestHttpRequestParser` | class extends ezcMvcHttpRequestParser | – | `kernel/private/rest/classes/request/http_parser.php:19` | Custom request parser which creates instances of ezpRestRequest. |
| `ezpRestHttpResponse` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/http_response.php:12` | – |
| `ezpRestHttpResponseWriter` | class extends ezcMvcHttpResponseWriter | – | `kernel/private/rest/classes/rest_http_response_writer.php:12` | – |
| `ezpRestHTTPSRequiredException` | class extends ezpRestException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/https_required.php:12` | – |
| `ezpRestIniRouteFilter` | class extends ezpRestRouteFilterInterface | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/rest/classes/auth/ini_route_security.php:12` | – |
| `ezpRestJsonView` | class extends ezcMvcView | – | `kernel/private/rest/classes/views/json.php:12` | – |
| `ezpRestModel` | abstract class | – | `kernel/private/rest/classes/models/rest_model.php:15` | Abstract class for every REST model |
| `ezpRestMvcController` | abstract class extends ezcMvcController | [Front Controller](front-controller.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/controllers/rest_controller.php:15` | Abstract class that must be extended by every REST controller |
| `ezpRestMvcResult` | class extends ezcMvcResult implements ezcBaseExportable | [Factory and static factory methods](factory.md), [Value object / struct](value-object.md) | `kernel/private/rest/classes/response/result.php:15` | This struct contains the result data to be returned by eZP REST controllers |
| `ezpRestNoAuthStyle` | class extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface | – | `kernel/private/rest/classes/auth/styles/no_auth.php:16` | This auth style is used when no authentication is required for current REST request A default user will be returned. |
| `ezpRestOauthAuthenticationStyle` | class extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/private/rest/classes/auth/styles/oauth.php:12` | – |
| `ezpRestOauthErrorStatus` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/oauth_error.php:12` | – |
| `ezpRestOauthTokenController` | class extends ezcMvcController | – | `kernel/private/rest/classes/controllers/oauth_token.php:18` | Controller for OAuth token endpoint. |
| `ezpRestPoConfig` | class implements ezcBaseConfigurationInitializer | – | `kernel/private/rest/classes/lazy.php:101` | – |
| `ezpRestPrefixFilterInterface` | abstract class | [Decorator / filter](decorator.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/prefix_filter.php:12` | – |
| `ezpRestPreRoutingFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `kernel/private/rest/classes/interfaces/prerouting_filter.php:12` | – |
| `ezpRestProvider` | class | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `kernel/private/rest/classes/rest_provider.php:12` | – |
| `ezpRestProviderInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/rest/classes/interfaces/rest_provider.php:12` | – |
| `ezpRestProviderNotFoundException` | class extends ezpRestException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/rest_provider_not_found.php:12` | – |
| `ezpRestRequest` | class extends ezcMvcRequest | – | `kernel/private/rest/classes/request/rest_request.php:20` | Class mimicking ezcMvcRequest with distinct containers for GET and POST variables. |
| `ezpRestRequestFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `kernel/private/rest/classes/interfaces/request_filter.php:12` | – |
| `ezpRestResponseFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `kernel/private/rest/classes/interfaces/response_filter.php:12` | – |
| `ezpRestResultFilterInterface` | interface | [Decorator / filter](decorator.md), [Interface (contract)](interface.md) | `kernel/private/rest/classes/interfaces/result_filter.php:12` | – |
| `ezpRestRouteFilterInterface` | abstract class | [Decorator / filter](decorator.md), [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/interfaces/route_filter.php:11` | – |
| `ezpRestRouter` | class extends ezcMvcRouter | [Chain of Responsibility](chain.md) | `kernel/private/rest/classes/router.php:11` | – |
| `ezpRestRoutesCacheClear` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `kernel/private/rest/classes/cache/clear_routes.php:15` | Clear cache handler. |
| `ezpRestRouteSecurityFilterNotFoundException` | class extends ezpRestException | [Decorator / filter](decorator.md), [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/route_security_filter_not_found.php:12` | – |
| `ezpRestStatusResponse` | class implements ezcMvcResultStatusObject | – | `kernel/private/rest/classes/status/response.php:12` | – |
| `ezpRestToken` | class implements ezcPersistentObject | – | `kernel/private/rest/classes/models/ezprest_token.php:16` | Data class ezprest_tokens. |
| `ezpRestTokenManager` | class | [Facade / service](facade.md) | `kernel/private/oauth/classes/tokenmanager.php:17` | This class handles application tokens |
| `ezpRestVersionedRoute` | class implements ezcMvcRoute, ezcMvcReversibleRoute | – | `kernel/private/rest/classes/routes/versioned_route.php:19` | Route wrapping around existing instance of ezcMvcRoute providing multiple versions of it. |
| `ezpRestViewControllerInterface` | interface | [Interface (contract)](interface.md) | `kernel/private/rest/classes/interfaces/view_controller.php:12` | – |
| `ezpRouteMethodNotAllowedException` | class extends ezcMvcToolsException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/route_method_not_allowed.php:12` | – |
| `ezpSearchEngine` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/interfaces/ezpsearchengine.php:18` | This interface is used as the basis for the different search engine implementation |
| `ezpSiteAccessMenu` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/private/classes/ezpsiteaccessmenu.php:48` | The administration's site menu: the siteaccesses of this installation with their front page addresses, for the globe button in the admin4 header. |
| `ezpSiteAccessURL` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/ezpsiteaccessurl.php:44` | The address of a siteaccess's front page, worked out from the siteaccess settings and the request the visitor is on: the admin's "open the site" link… |
| `ezpStaticCache` | interface | [Interface (contract)](interface.md) | `kernel/private/interfaces/ezpstaticcache.php:16` | This interface is used as the basis for the different StaticCache engine implementation |
| `ezpTemplateOverrides` | class | – | `kernel/private/classes/ezptemplateoverrides.php:33` | Changes the template overrides of one siteaccess, for Design › Templates, in settings/siteaccess//override.ini.append.php and nowhere else. |
| `ezpTopologicalSort` | class | – | `kernel/private/classes/ezptopologicalsort.php:15` | Sorts a series of dependencies in linear order (topological sort). |
| `ezpTopologicalSortNode` | class | – | `kernel/private/classes/ezptopologicalsortnode.php:17` | Node in a topological sort. |
| `ezpUpdatedContent` | abstract class | [Template Method / abstract base class](template-method.md) | `kernel/private/rest/classes/sync/updates.php:23` | This class provides API through which one can query for updated content items. |
| `ezpUserNotFoundException` | class extends ezpRestException | [Exception hierarchy](exception.md) | `kernel/private/rest/classes/exceptions/user_not_found.php:11` | – |
| `ezpWebBasedKernelHandler` | interface extends ezpKernelHandler | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/interfaces/ezpwebbasedkernelhandler.php:14` | Interface for web-based kernel handlers |
| `eZScriptClusterPurge` | class | – | `kernel/private/classes/ezscriptclusterpurge.php:32` | This class handles purging of cluster items. |
| `eZScriptTrashPurge` | class | – | `kernel/private/classes/ezscripttrashpurge.php:12` | This class handles purging of trash items. |

## Module views (8)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/oauth/authorize` | `authorize.php` | – | `kernel/private/modules/oauth/module.php:21` |
| `/oauthadmin/list` | `list.php` | – | `kernel/private/modules/oauthadmin/module.php:21` |
| `/oauthadmin/edit` | `edit.php` | – | `kernel/private/modules/oauthadmin/module.php:26` |
| `/oauthadmin/action` | `action.php` | – | `kernel/private/modules/oauthadmin/module.php:37` |
| `/oauthadmin/view` | `view.php` | – | `kernel/private/modules/oauthadmin/module.php:46` |
| `/oauthadmin/keys` | `keys.php` | – | `kernel/private/modules/oauthadmin/module.php:54` |
| `/oauthadmin/keyaction` | `keyaction.php` | – | `kernel/private/modules/oauthadmin/module.php:61` |
| `/switchlanguage/to` | `to.php` | – | `kernel/private/modules/switchlanguage/module.php:16` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `oauth/authorize` | – | `kernel/private/modules/oauth/module.php:29` |

## Fetch functions (1)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'switchlanguage', 'url_alias' )` | `ezpLanguageSwitcherFunctionCollection::fetchUrlAlias()` | `kernel/private/modules/switchlanguage/function_definition.php:13` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| language_switcher | `ezpLanguageSwitcherOperator` | `kernel/private/eztemplate/eztemplateautoload.php:13` |

## Event hooks (4)

| Event | Kind | Called in |
|---|---|---|
| `request/preinput` | notify | `kernel/private/classes/ezpkernelweb.php:1658` |
| `request/input` | notify | `kernel/private/classes/ezpkernelweb.php:1783` |
| `response/preoutput` | filter | `kernel/private/classes/global_functions.php:83` |
| `response/output` | filter | `kernel/private/classes/global_functions.php:102` |
