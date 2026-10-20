---
title: Integration and API requirements
audience: government, project-manager, developer, partner
category: public-sector
order: 90
summary: Requirement wording for how a public CMS connects to other systems - content API, authentication, directory sign-in, feeds, webhooks, mail - with Exponential's documented protocols and their status from the APIs and protocols standards page.
keywords: integration, API, REST, OAuth, API keys, LDAP, single sign-on, SSO, webhooks, RSS, sitemaps, SMTP, WebDAV, SOAP, interoperability, open standards, tender
type: help
---
# Integration and API requirements

This page is for project managers and architects who must state how the CMS exchanges data with the rest of the
organization: a citizen portal, a directory, a records system, a mobile app, a search platform. It gives requirement
wording and the status of each protocol as documented in
[APIs and protocols standards](../../../../standards/apis-and-protocols.md).

## Contents
1. [Start from your integration map](#start-from-your-integration-map)
2. [Requirement wording](#requirement-wording)
3. [Exponential's documented protocols](#exponentials-documented-protocols)
4. [Gaps to ask about](#gaps-to-ask-about)
5. [Pitfalls](#pitfalls)

## Start from your integration map

List every system the CMS must talk to, in which direction, how often, and with what data. Requirements written from
that list are concrete; requirements written as "must have an open API" are not.

| System | Direction | Data | Frequency |
|---|---|---|---|
| <directory service> | in | staff accounts, groups | at sign-in |
| <records system> | out | published pages and documents | <interval> |
| <mobile app> | out | news, events | on request |
| <mail relay> | out | notifications, newsletters | continuous |

## Requirement wording

| Id | Requirement |
|---|---|
| INT-01 | Published content is available through a documented HTTP API returning JSON; describe authentication, versions and limits. |
| INT-02 | Scripts and integrations authenticate without sharing a person's password (tokens or keys that can be revoked). |
| INT-03 | Staff can sign in with the organization's directory (<protocol>); describe the supported protocols and their security options. |
| INT-04 | Lists of content are published as RSS or Atom feeds, and the site publishes an XML sitemap. |
| INT-05 | Security-relevant events can be delivered to the organization's log or SIEM platform, with integrity protection. |
| INT-06 | Mail is sent through the organization's SMTP relay with TLS. |
| INT-07 | Content can be imported in bulk from <format> and exported in an open, documented format. |
| INT-08 | Every API and import/export format is documented publicly. |

## Exponential's documented protocols

| Id | Evidence | Status |
|---|---|---|
| INT-01 | REST API v1 and v2 from one extension ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)); JSON | REST **Partly complies**, JSON **Complies** |
| INT-02 | OAuth 2.0 token endpoint; personal API keys ([Personal API keys](../../../../guides/api-keys.md)); remote services ([Remote services and apps](../../../../guides/remote-services-and-apps.md)) | OAuth 2.0 **Partly complies** (no PKCE on the server side) |
| INT-03 | LDAP authentication in the kernel; social and OpenID Connect sign-in through `sevenx_authentication_2fa` | LDAP **Partly complies** (default LDAPv2, no StartTLS); OIDC client **Partly complies** (ID tokens not validated) |
| INT-04 | RSS 1.0/2.0 and Atom export, RSS import ([RSS feeds](../../../../guides/rss-feeds.md)); sitemaps with `bcgooglesitemaps` | RSS **Complies**, Atom **Partly complies**, sitemaps **Complies** |
| INT-05 | Audit sinks including a signed outgoing webhook (HMAC-SHA-256) ([Audit trail](../../../../features/6.0/audit-trail.md)) | **Complies** with its own documented contract |
| INT-06 | SMTP transport with STARTTLS | **Complies** |
| INT-07 | Export tools (site archive, CSV, JSON, packages) ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)); import tools ([Exponential's tools](../../../../install/migration/exponential-tools.md)) | documented |
| INT-08 | The API guides and standards pages above are public in `doc/` under the GNU FDL | no OpenAPI document for the kernel REST API |

## Gaps to ask about

From [APIs and protocols standards](../../../../standards/apis-and-protocols.md):

- **No OpenAPI description** of the kernel's REST API; ask the bidder how they document the endpoints they build.
- **SAML** is not listed among the documented protocols. If your directory requires SAML single sign-on, ask the
  bidder how they deliver it and treat it as custom work.
- **LDAP** defaults (version 2, no StartTLS) need changing or a secure network path; put the target configuration in
  the requirement.
- **SOAP** exists but is off (`EnableSOAP=false`) and has no WSDL; **XML-RPC** is not implemented.
- **Bearer tokens in query parameters** are accepted; ask the bidder to configure clients to use the header.

## Pitfalls

- "Must integrate with everything." Name the systems; integrations are where projects overrun.
- Assuming an API means real-time sync. State the frequency and who runs the job.
- Forgetting ownership: custom integration code written for you should be delivered as source
  ([Tender template: intellectual property of custom work](../tender-templates/intellectual-property-of-custom-work-clause.md)).

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Functional requirements for a public-sector CMS](functional-requirements-for-a-public-cms.md)
- [Exit and portability requirements](exit-and-portability-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
