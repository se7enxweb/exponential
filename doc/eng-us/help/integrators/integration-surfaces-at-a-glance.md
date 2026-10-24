---
title: Integration surfaces at a glance
audience: integrator, developer, admin
category: integration
order: 20
summary: Every integration surface that Exponential 6.0.15 and its required extensions provide, with direction, protocol, authentication, where it lives and whether it is on by default, plus what is not provided and how to check what an installation has active.
keywords: integration surfaces, API overview, REST, OAuth, API keys, expservices, ezjscore, RSS, Atom, OPML, SOAP, syndication, packages, CSV, xrowextract, workflows, triggers, ezpEvent, audit webhook, login handler, LDAP, SSO, search plugin, mail transport, payment gateway, not provided, GraphQL, SAML
type: help
---
# Integration surfaces at a glance

This page is the inventory. It lists every way another system can exchange data with Exponential 6.0.15, as shipped
by the kernel and by the extensions the root `composer.json` requires. Each row names where the surface lives and
whether it works out of the box or must be switched on. The standards behind each protocol, with their known gaps,
are in [API and protocol standards](../../standards/apis-and-protocols.md).

## Contents

1. [HTTP APIs](#http-apis)
2. [Feeds, files and protocols](#feeds-files-and-protocols)
3. [Server-side surfaces](#server-side-surfaces)
4. [Identity, mail, search and payment](#identity-mail-search-and-payment)
5. [What is not provided](#what-is-not-provided)
6. [Checking what an installation has active](#checking-what-an-installation-has-active)

## HTTP APIs

| Surface | Direction | Protocol and format | Authentication | Where | Default |
|---|---|---|---|---|---|
| REST content API | In and out | HTTP, JSON; `/api/ezp/v1/...`, `/api/ezp/v2/...` | OAuth 2.0 token, personal API key, or HTTP basic (`AuthenticationStyle`) | Kernel framework `kernel/private/rest/`, routes in `ezprestapi` | Answers when `ezprestapi` is active; `rest.ini [Authentication] RequireAuthentication=enabled` |
| OAuth 2.0 authorization server | In (credentials) | `oauth/authorize`, `POST /api/auth/v1/oauth/token` | Client id and secret, user consent | `kernel/private/modules/oauth/`, Setup > oAuth admin | Grants `authorization_code` and `refresh_token` |
| Personal API keys | In (credentials) | `Authorization: Bearer expk_...` | The key's owner, limited to its scopes | `kernel/apikey/`, `rest.ini [ApiKeySettings]` | `ApiKeys=enabled`; no role has `apikey/create` until you give it |
| Remote services (`expservices`) | In and out | JSON over `/ezjscore/call/exp<domain>::<method>` | Session cookie and form token, or an `expt_...` token | `extension/expservices` | `expservices.ini [Services] Enabled=enabled` |
| ezjscore server functions | In and out | JSON over `/ezjscore/call/<server>::<function>` | Session, policy `ezjscore/call` | `extension/ezjscore` | On; your functions are added by `ezjscore.ini` |

## Feeds, files and protocols

| Surface | Direction | Format | Where | Default |
|---|---|---|---|---|
| RSS, Atom and OPML exports | Out | RSS 1.0, RSS 2.0, Atom, OPML 2.0, Apple Podcasts; `/rss/feed/<address>` | `kernel/classes/ezrssexport.php`, Setup > RSS | None configured |
| RSS imports | In | RSS 0.91 to 2.0 | `kernel/classes/ezrssimport.php`, cronjob `rssimport.php` | In the global cronjob part |
| `syndication` | Both, installation to installation | XML feeds over SOAP | `syndication` extension, `/syndication/menu` | Needs `soap.ini EnableSOAP=true` |
| SOAP server | In | SOAP 1.1, no WSDL | `soap.php`, `lib/ezsoap/` | `soap.ini [GeneralSettings] EnableSOAP=false` |
| Packages (`.ezpkg`) | Both | Package archives; JSON package format | `ezpm.php`, Setup > Packages | On |
| CSV import and export | Both | CSV | `bin/php/ezcsvimport.php`, `bin/php/ezcsvexport.php` | On |
| `xrowextract` | Both, scheduled | CSV, JSON, XML, `.ezpkg`; SFTP, FTP, S3, WebDAV, HTTP POST | `xrowextract` extension, admin **Export** | Installed with the distribution |
| Collected information export | Out | CSV | `infocollector/export/<object id>`; `bccie` extension | On |

## Server-side surfaces

| Surface | What it gives an integration | Where |
|---|---|---|
| PHP content API | Create, update, publish, move, hide, remove content the supported way | `kernel/classes/ezcontentfunctions.php`, `eZContentObject`, `eZContentObjectTreeNode` |
| CLI scripts and `ezexec.php` | Run PHP with the kernel loaded, from cron or a deployment | `bin/php/ezexec.php`, `eZScript`, `bin/php/console` |
| Cronjob parts | Scheduled imports and exports | `settings/cronjob.ini`, `runcronjobs.php` |
| Workflows and triggers | Run code before or after an operation (publish, checkout, ...) | `settings/workflow.ini`, Setup > Triggers |
| Event listeners | React to kernel events (cache cleared, state assigned, output) | `ezpEvent`, `site.ini [Event] Listeners[]` |
| Notification handlers and transports | Deliver notification events your own way | `settings/notification.ini`, `kernel/classes/notification/` |
| Audit trail sinks | Signed webhook, syslog and mail for recorded events | `settings/audit.ini [AuditSink_webhook]` |
| Content jobs and `expqueue` | Large operations and background work in batches | `content.ini [ContentJobSettings]`, `expqueue` extension |

## Identity, mail, search and payment

| Surface | Setting | Shipped handlers |
|---|---|---|
| Login handlers | `site.ini [UserSettings] LoginHandler[]` | `standard`, `LDAP` (`settings/ldap.ini`), `textfile` (`settings/textfile.ini`); `sevenxUser2fa` and social login from `sevenx_authentication_2fa` |
| Single sign-on handlers | `site.ini [UserSettings] SingleSignOnHandlerArray[]` | None shipped; class `eZ<name>SSOHandler` of your own |
| Mail transport | `site.ini [MailSettings] Transport`, `TransportAlias[]` | `sendmail`, `smtp`, `file` |
| Search engine | `site.ini [SearchSettings] SearchEngine` | `eZSearchEngine` (built in); Solr through the separately installed `ezfind` |
| Payment gateways | `paymentgateways.ini [GatewaysSettings]` | `ezpaypal` extension |

## What is not provided

Plan the following as custom work, or choose another approach:

| Not provided | What to do instead |
|---|---|
| GraphQL | Use the REST API or `expservices`, or write a module of your own |
| An OpenAPI description of the content API | Document your routes in your interface specification ([template](interface-specification-template.md)) |
| XML-RPC | Not implemented; use REST or SOAP |
| A WebDAV server (content as a network drive) | Removed in October 2026 ([WebDAV and SOAP](webdav-and-soap.md#webdav)); use the uploads of the administration, or a file import with `xrowextract` |
| SAML or an OpenID Connect provider | Write a login or SSO handler, or put an identity-aware proxy in front ([Single sign-on](single-sign-on-and-social-login.md)) |
| A generic outgoing webhook for content events | A workflow event or event listener of your own, or the audit webhook sink with `content.object.publish` switched on ([Audit webhooks](outgoing-webhooks-from-the-audit-trail.md)) |
| Updating content through REST | `POST /content/node/<id>` checks `content/edit` and answers 501; update from PHP or through `expservices` |
| A message broker connector (AMQP, Kafka) | Publish from a workflow event or a cronjob through a PHP client library you add to your extension |
| An MCP server in the kernel | The kernel repository ships none ([AI, job queue and MCP](ai-job-queue-and-mcp.md)) |

## Checking what an installation has active

Run these from the installation root (add `--allow-root-user` when you are root):

```bash
php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions   # ezprestapi, expservices, xrowextract ...
php bin/php/console exp:ini get rest.ini/Authentication/RequireAuthentication
php bin/php/console exp:ini get expservices.ini/Services/Enabled
php bin/php/console exp:ini get soap.ini/GeneralSettings/EnableSOAP
php bin/php/console exp:ini get site.ini/UserSettings/LoginHandler
php bin/php/console exp:ini get site.ini/MailSettings/Transport
php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
```

From outside, two requests show whether the HTTP APIs answer:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/api/ezp/v1/content/node/2   # 401 without credentials
```

Setup > RAD (`/setup/rad`) lists the extension points and the `expservices` catalog of the installation.

## Related pages

- [Choosing an integration surface](choosing-an-integration-surface.md)
- [API and protocol standards](../../standards/apis-and-protocols.md)
- [Integrations FAQ for public bodies](../government/faq/integrations-faq.md)
- [Extension points at a glance](../extension-development/part-8-reference/extension-points-at-a-glance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
