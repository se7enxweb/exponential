---
title: Authentication for apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 200
summary: The landing page of the authentication pages for app developers - the three credentials Exponential accepts from programs (OAuth2 tokens, personal API keys, expservices tokens), the default that every REST call needs a user, and the pages in this folder.
keywords: authentication, OAuth2, token, API key, expk, expt, expservices token, RequireAuthentication, credential, sign in, mobile app
type: help
---
# Authentication for apps

This folder is for app developers who have to sign an app, or its users, in to Exponential. It explains the
credentials the server accepts, how each one is obtained and sent, and how to keep them safe on a device.

## Contents

1. [The default: every call needs a user](#the-default-every-call-needs-a-user)
2. [Three credentials](#three-credentials)
3. [Pages in this folder](#pages-in-this-folder)

## The default: every call needs a user

`rest.ini [Authentication] RequireAuthentication=enabled` is the default: every REST content route needs a user. An
administrator can set it to `disabled`, and then every request runs as `DefaultUserID` (or the anonymous user), with
the permission checks still applied ([REST API v1 and v2: configuration](../../../guides/rest-api-v1-and-v2.md#5-configuration)).
That is a server decision with consequences for every client; do not ask for it only to skip sign-in in an app.

The remote services work differently: services whose access is `public` answer anonymous calls, all others need a
signed-in user ([Calling expservices from an app](../json-endpoints/calling-expservices-from-an-app.md)).

## Three credentials

| Credential | Looks like | Sent as | Accepted by | Read |
|---|---|---|---|---|
| OAuth2 access token | an alphanumeric string | `Authorization: OAuth <token>` or `Bearer <token>` | REST | [OAuth2 authorization code flow](oauth2-authorization-code-flow.md) |
| Personal API key | `expk_<12 characters>_<40 characters>` | `Authorization: Bearer expk_...` | REST | [Personal API keys for tools](personal-api-keys-for-tools.md) |
| expservices token | `expt_` and 48 hex characters | `Authorization: Bearer expt_...` and `X-Exp-Token: expt_...` | expservices | [expservices tokens in apps](expservices-tokens-in-apps.md) |

HTTP basic authentication can be configured for REST instead of OAuth (`AuthenticationStyle=ezpRestBasicAuthStyle`),
but that means sending a password with every request; this section does not recommend it for apps.

## Pages in this folder

| Page | Answers |
|---|---|
| [Choosing a credential](choosing-a-credential.md) | Which credential for which kind of app |
| [OAuth2 authorization code flow](oauth2-authorization-code-flow.md) | Registering the client, the authorize page, the token endpoint, refreshing |
| [A back end for your app](a-backend-for-your-app.md) | Why the client secret stays on a server, and how to build that server |
| [Personal API keys for tools](personal-api-keys-for-tools.md) | Keys for your own scripts, tests and internal tools |
| [expservices tokens in apps](expservices-tokens-in-apps.md) | Signing in to the remote services and making a token |
| [Storing secrets on devices](storing-secrets-on-devices.md) | Keychain, Keystore and what never to store |
| [The Authorization header and proxies](authorization-header-and-proxies.md) | Why a valid credential can still arrive as nothing |

## Related pages

- [OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md)
- [Apps and API keys (for businesses)](../../businesses/mobile-and-headless/apps-and-api-keys.md)
- [REST API v1 and v2: authentication](../../../guides/rest-api-v1-and-v2.md#3-authentication)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
