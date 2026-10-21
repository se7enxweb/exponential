---
title: Integrators' FAQ
audience: integrator, developer, project-manager
category: integration
order: 520
summary: Short answers to the questions integrators ask most about Exponential - APIs, authentication, webhooks, imports, identity, search, performance, upgrades, licensing and the partner program - each with a link to the page that explains it.
keywords: FAQ, questions, answers, integrator, API, REST, GraphQL, webhook, OAuth, API key, SSO, SAML, LDAP, import, remote_id, Solr, Velocity, upgrade, license, partner
type: help
---
# Integrators' FAQ

Short answers with links. Each answer is checked against the code and settings of Exponential 6.0.15 and the
extensions the root `composer.json` requires.

## Contents

1. [APIs](#apis)
2. [Authentication and identity](#authentication-and-identity)
3. [Imports and exports](#imports-and-exports)
4. [Events and hooks](#events-and-hooks)
5. [Operations](#operations)
6. [Business](#business)

## APIs

**Is there a REST API?** Yes: the `ezprestapi` extension serves content reads at `/api/ezp/v1/...` and `/api/ezp/v2/...`,
and create and delete at v2 ([REST API for integrators](rest-api-for-integrators.md)).

**Can I update content over REST?** Not through `ezprestapi`: the update route answers `501`. Use `expobject::update`
of `expservices`, or PHP on the server ([Remote services with expservices](remote-services-with-expservices.md)).

**Is there GraphQL or an OpenAPI description?** No. Velocity publishes `/.well-known/openapi.json` for its own handlers,
not for the content API ([Integration surfaces at a glance](integration-surfaces-at-a-glance.md#what-is-not-provided)).

**What is `expservices`?** About a thousand named JSON services over `/ezjscore/call/exp<domain>::<method>`, the
administration's functions as an API, each behind its policy.

**Is there search over an API?** `expsearch::search`, `count`, `facets` and more in `expservices`; not in the REST API.

**Is SOAP still there?** Yes, SOAP 1.1 without WSDL, off by default (`soap.ini EnableSOAP=false`)
([WebDAV and SOAP](webdav-and-soap.md)).

## Authentication and identity

**How does a server authenticate?** With a personal API key of a service account
(`Authorization: Bearer expk_...`) for REST, or an `expservices` token ([Choosing credentials](choosing-credentials-for-an-integration.md)).

**Is there a client-credentials grant?** No; the OAuth server supports `authorization_code` and `refresh_token`.
Use an API key for machine-to-machine ([OAuth applications](oauth-applications-for-integrations.md)).

**Can staff sign in with Active Directory?** Through the LDAP login handler, with `LDAPVersion=3` and an `ldaps://`
connection ([Login handlers](login-handlers-ldap-and-textfile.md)).

**Is there SAML?** No. Use an identity-aware proxy in front and an SSO handler of your own, or custom work
([Single sign-on](single-sign-on-and-social-login.md)).

**Social login?** Yes, through `sevenx_authentication_2fa` (Google, Facebook, Meta, Instagram, Twitter, ID.me).

## Imports and exports

**How do I avoid duplicates?** Give every imported object a `remote_id` from a key scheme and find it with
`eZContentObject::fetchByRemoteID()` before creating ([Idempotent imports](idempotent-imports-keyed-by-remote-id.md)).

**Can I write to the database tables directly?** No. Use `eZContentFunctions`; direct writes skip versions, workflows,
caches, the search index and the audit.

**Will an import send thousands of notification mails?** Not if you pass `$notify = false` to the content functions
([Mail and notifications](mail-and-notifications-integration.md#imports-and-notifications)).

**Is there a no-code file exchange?** `xrowextract`: CSV, JSON, XML and packages, scheduled, delivered to SFTP, S3,
WebDAV or HTTP ([Bulk data exchange](bulk-data-exchange-with-xrowextract.md)).

**How do I move a subtree between installations?** A package with `ezpm.php`, or continuously with `syndication`.

## Events and hooks

**Is there a webhook when content is published?** Not a generic one. Use a workflow event on `content / publish / after`
with an outbox, or route `content.object.publish` to the audit trail's signed webhook sink
([Workflows and triggers](workflows-and-triggers-as-hooks.md), [Audit webhooks](outgoing-webhooks-from-the-audit-trail.md)).

**Can another system approve a publication?** A workflow event on `content / publish / before` can accept, reject or
defer to the cronjob.

**How do I purge a CDN?** Listen to `content/cache` and `content/cache/all` ([Event listeners](event-listeners-for-integrations.md)).

**Is there an incoming webhook endpoint?** No; build one as a module view, a server function or an `expservices`
domain of your extension, storing the payload first (inbox).

## Operations

**Do I need to do anything special for Velocity?** Restart it after class changes, never keep request state in
statics, and know that anonymous `GET` answers may come from its response cache when it is enabled
([Velocity and performance](velocity-and-performance-for-integrations.md)).

**Why does the site show old content after my import?** Content published through the content functions clears its
own caches; template blocks, Velocity's response cache and outer caches may need clearing
([Caching after imports](caching-after-imports.md)).

**How do I know the integration still works?** Summary lines, alert on their absence, backlog and freshness checks
([Monitoring](monitoring-integrations.md)).

**Will an upgrade break my integration?** Read the behavior change notes, rehearse on a copy, run the acceptance cases
([Upgrades](upgrades-without-breaking-integrations.md)).

## Business

**Under which license can I distribute my connector?** Choose GPL v2 or later for code that runs inside Exponential and
is distributed; not legal advice ([Contributing integrations back](contributing-integrations-back.md#licensing-points)).

**Does the partner program fit integration companies?** Yes, usually as an IT partner; levels from Student to Platinum,
10% or 20% lead commission ([Integrators and the partner program](integrators-and-the-partner-program.md)).

**Who answers questions about the kernel?** The forums and Telegram for the community; 7x for paid support
([Integrators: start here](integrators-start-here.md#who-to-ask)).

## Related pages

- [Integrators: start here](integrators-start-here.md)
- [Integrators' glossary](integrators-glossary.md)
- [Partner FAQ](../partners/partner-faq.md)
- [App creator FAQ](../creators/app-creators/app-creator-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
