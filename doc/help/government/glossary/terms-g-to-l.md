---
title: "Government glossary: G to L"
audience: government, project-manager, admin, editor
category: public-sector
order: 30
summary: Public-sector and Exponential terms from G to L in one to three sentences each - GPL, go-live, hardening, hosting model, information collection, interface language, legal basis, lock-in, location and more - with a link to the page that explains each.
keywords: glossary, terms, definitions, GPL, go-live, hardening, hosting, information collection, incident, interface language, legal basis, lock-in, location, log, public sector
type: reference
---
# Government glossary: G to L

This page is for readers in a public body who meet a term they do not know. Each entry has one to three sentences and
a link to the page that explains it. The full product glossary is [the Exponential glossary](../../../glossary.md).

> **Note** Definitions of legal terms are plain-language descriptions, not legal definitions. Check the law that
> applies to your organization with your legal counsel.

## Contents

1. [G and H](#g-and-h)
2. [I](#i)
3. [K and L](#k-and-l)

## G and H

**GNU Free Documentation License (GFDL).** The license of the Exponential documentation, including this help; it lets
you copy, adapt and republish pages if you keep the notices ([Free documentation](../../free-documentation.md);
[gnu.org](https://www.gnu.org/licenses/fdl-1.3.html)).

**GNU General Public License (GPL).** The license of the Exponential software, version 2 or any later version. It
charges no fee and lets you run, study, change and share the code ([Licensing questions](../../project/licensing-faq.md);
[GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)).

**Go-live.** The moment a site is opened to the public. A go-live checklist covers content, accessibility, security,
backups and monitoring ([Launch checklist](../../creators/website-creators/launch-checklist.md)).

**Hardening.** Changing settings and the server so a site resists attack better than with defaults
([Security hardening](../../security-hardening.md), [13. Security hardening for production](../../../install/13-security-hardening.md)).

**Hosting model.** How and where a site runs: shared hosting, a virtual server, dedicated servers, containers, or a
managed service ([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).

**HSTS.** HTTP Strict Transport Security, a header telling browsers to use HTTPS only. In Exponential it is empty by
default and set by the operator once the whole site is HTTPS ([Security standards](../../../standards/security.md#known-gaps)).

## I

**Incident.** An event that harms or threatens the service or its data, such as an outage or a break-in. Hosts have
runbooks such as [Compromised site](../../hosting/runbooks/compromised-site.md) and [Site down](../../hosting/runbooks/site-down.md).

**Information collection.** Exponential's feature for forms, polls and surveys whose answers are stored and listed under
**Setup > Collected information** ([Collected information](../../../guides/collected-information.md)).

**Installer source.** A package the setup wizard installs sites from; such packages are marked and need their own
confirmation to remove ([Packages](../../../guides/packages.md)).

**Interface language.** The language of the administration's menus and buttons, separate from the language of the
content ([UI switch settings](../../../guides/ui-switch-settings.md), [Languages](../../languages.md)).

**Interoperability.** The ability of systems to exchange data. Exponential offers a REST API, RSS feeds and exports in
CSV, XML and JSON ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md), [RSS feeds](../../../guides/rss-feeds.md)).

## K and L

**Kickstarter.** Exponential's command-line installer that reads its answers from a file, `kickstart.ini`
([6. The kickstarter](../../../install/06-kickstarter.md)).

**Legal basis.** In the GDPR, the reason that makes processing personal data lawful, such as consent or a public task.
Choosing it is the controller's decision, not the software's ([Privacy standards](../../../standards/privacy.md)).

**Limitation.** A restriction inside a permission, for example "only in this section" or "only content of this class"
([Roles, policies and unactivated users](../../../guides/roles-and-policies.md)).

**Lock-in.** Dependence on one supplier that makes changing costly. With GPL software and documented exports, lock-in
comes mostly from contracts, custom code and missing documentation, not from the license
([Migrating away from Exponential](../../../install/migration/leaving-exponential.md)).

**Location.** Another word for a node: one place of a content object in the content tree. An object can have several
([the Exponential glossary](../../../glossary.md#l)).

**Log.** A file where the system records events and errors. **Setup > Log files** lists them with an error digest
([Log viewer](../../../features/6.0/log-viewer.md), [Log rotation](../../../features/6.0/log-rotation.md)).

**Log retention.** How long logs are kept. In Exponential the audit trail keeps 90 days live and two years archived per
channel; some other logs have age limits set in `logviewer.ini` ([Privacy standards](../../../standards/privacy.md#gdpr)).

## Related pages

- [Government glossary](README.md)
- [Government glossary: A to F](terms-a-to-f.md)
- [Government glossary: M to R](terms-m-to-r.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
