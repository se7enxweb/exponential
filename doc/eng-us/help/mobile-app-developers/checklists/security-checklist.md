---
title: Security checklist for apps
audience: mobile-app-developer, app-creator, developer, admin
category: mobile-apps
order: 910
summary: A checklist for the security of an app on Exponential - credentials and where they live, the OAuth setup and its limits, roles of integration users, HTTPS and the Authorization header, logs, writes, web app specifics, and monitoring refusals in the audit trail.
keywords: security, checklist, credentials, OAuth, client secret, API key, expservices token, roles, least privilege, HTTPS, RequireHTTPS, logs, audit, CORS, CSP, mobile app
type: help
---
# Security checklist for apps

This checklist is for app developers and the administrator of the installation the app talks to. Go through it before
the first release and again whenever credentials, roles or endpoints change.

## Contents

1. [Credentials](#credentials)
2. [Accounts and roles](#accounts-and-roles)
3. [Transport](#transport)
4. [In the app](#in-the-app)
5. [Writes](#writes)
6. [Browser apps](#browser-apps)
7. [Monitoring](#monitoring)

## Credentials

- [ ] No OAuth client secret, personal API key or expservices token is in the app's code, resources or configuration
      ([Choosing a credential](../auth/choosing-a-credential.md#never)).
- [ ] The OAuth code exchange and refresh run on a back end that holds the client secret
      ([A back end for your app](../auth/a-backend-for-your-app.md)).
- [ ] The app correlates the OAuth callback itself, since `state` is not returned
      ([OAuth2 authorization code flow](../auth/oauth2-authorization-code-flow.md#limits-of-the-implementation)).
- [ ] `response_type=token` is not used.
- [ ] Keys used by back ends and tools have the smallest scopes and a lifetime, and a rotation date is in the calendar.
- [ ] `expk_` and `expt_` are in the secret scanner of every repository.

## Accounts and roles

- [ ] Each app has its own integration user; none uses the administrator or a person's account.
- [ ] `content/read` is limited to the sections or subtrees the app shows.
- [ ] Write policies are limited to one folder and the classes the app creates.
- [ ] No `setup`, `class` or `user` policies for app users.
- [ ] `oauth/authorize` only for the roles of people who use the app
      ([Permissions for app users](../publishing-workflow/permissions-for-app-users.md)).

## Transport

- [ ] Every request uses HTTPS; consider `rest.ini [Authentication] RequireHTTPS=enabled` on the server.
- [ ] The `Authorization` header reaches PHP on every server and through every proxy
      ([The Authorization header and proxies](../auth/authorization-header-and-proxies.md)).
- [ ] Proxies and CDNs do not cache answers to requests with `Authorization`.
- [ ] No credential is ever sent in a URL.

## In the app

- [ ] Tokens are in Keychain or Keystore, not in preferences or files ([Storing secrets on devices](../auth/storing-secrets-on-devices.md)).
- [ ] HTTP logging strips `Authorization` and `X-Exp-Token`, in every build that leaves the developer's machine.
- [ ] Sign-out deletes tokens and revokes expservices tokens.
- [ ] Rendered HTML (`renderedOutput`) is shown in a web view without scripts it does not need, and external links
      open in the system browser ([Rendered XHTML output](../rest-api/rendered-xhtml-output.md#showing-it-in-the-app)).

## Writes

- [ ] Removes ask the user to confirm ([Creating and removing content](../rest-api/creating-and-removing-content.md#remove)).
- [ ] Retries of creates cannot duplicate content (remote ids, `409`).
- [ ] Any upload endpoint built for the app checks size, type by content, and permissions
      ([Files and uploads](../media-and-images/files-and-uploads.md#a-brief-for-an-upload-endpoint)).

## Browser apps

- [ ] Any CORS headers added at the web server name explicit origins and cover only the API paths
      ([Web apps, CORS and CSP](../offline-and-performance/web-apps-cors-and-csp.md#cors)).
- [ ] The Content Security Policy allows the app's connections and nothing more.

## Monitoring

- [ ] Someone reads the `access` channel of the audit after each release: `access.apikey.use.failed`,
      `access.expservices.token.failed` ([Auditing app activity](../publishing-workflow/permissions-for-app-users.md#auditing-app-activity)).
- [ ] There is a plan for a leaked credential: revoke, rotate, check the audit.

## Related pages

- [Security for applications](../../creators/app-creators/security-for-applications.md)
- [Security hardening](../../security-hardening.md)
- [Personal API keys: security notes](../../../guides/api-keys.md#11-security-notes)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
