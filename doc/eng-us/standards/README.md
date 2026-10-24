---
title: Standards
audience: developer, admin
category: standards
order: 1
summary: The standards Exponential follows and how well it complies (accessibility, security, privacy, web, internationalization, PHP, documentation, APIs, databases, Git, coding and testing), its architectural rules, and a catalogue of the design patterns found in its code.
keywords: standards, compliance, conformance, WCAG, WAI-ARIA, EN 301 549, OWASP, ASVS, CSP, GDPR, HTTP, HTML, CSS, ECMAScript, Unicode, CLDR, BCP 47, RTL, PSR, PHP-FIG, Composer, semver, DITA, GFDL, EPUB, PDF, OAuth, OpenID, REST, WebDAV, SQL, Git, commit messages, phpcs, PHPUnit, CI, architecture, design patterns, naming conventions, backwards compatibility
featured: true
type: reference
---
# Standards

This section is for developers who build on Exponential and for administrators and decision makers who have to say
which standards a site meets. It answers three questions: which published standards Exponential follows and how
far it complies with each of them, which architectural rules its code follows, and which design patterns the code
uses and where.

Every page cites the code, settings, tests or continuous integration jobs that support what it says. Where a
claim could not be checked, the page says **Unverified**; where Exponential does not comply, the page says so and
lists the gap. Compliance of a *site* also depends on its own templates, content and operation: the software can
make compliance possible, but a site has to keep it.

## How to read the status

| Status | Meaning |
|---|---|
| **Complies** | The requirement is met by the software as shipped, and the page names the evidence. |
| **Partly complies** | Some requirements are met, others are not or only in some parts (for example the administration but not every public design). |
| **Does not comply** | The software does not meet it; the page says what is missing. |
| **Not applicable** | The standard does not apply to what Exponential does. |
| **Unverified** | No evidence was found either way; nothing is claimed. |

## Standards Exponential follows

| Area | Page | Standards covered |
|---|---|---|
| Accessibility | [Accessibility](accessibility.md) | WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 |
| Security | [Security](security.md) | OWASP Top 10, OWASP ASVS, Content Security Policy Level 3, OWASP cheat sheets |
| Privacy | [Privacy](privacy.md) | GDPR, ePrivacy (cookies), consent and unsubscribe |
| The web platform | [Web platform](web-platform.md) | HTTP semantics and caching, HTTP/2, TLS, HTML, CSS, ECMAScript, URLs, cookies, feeds, sitemaps |
| Languages | [Internationalization](internationalization.md) | Unicode and UTF-8, CLDR and ICU, BCP 47 and ISO language codes, right-to-left and bidi, time zones |
| PHP | [PHP and Composer](php-and-composer.md) | Supported PHP versions, PHP-FIG PSRs, Composer, Semantic Versioning |
| Code style | [Coding standards](coding-standards.md) | The phpcs ruleset, PHPStan, file headers, naming, CSS and JavaScript rules |
| Code prefixes | [Naming prefixes: sevenx and exp](naming-prefixes-sevenx-and-exp.md) | `sevenx` as 7x's official public PHP code prefix, `exp` for work assigned to the Exponential project and the Exponential Foundation |
| Tests | [Testing and continuous integration](testing-and-ci.md) | PHPUnit, the test map, the CI workflows and the gates before a push |
| History | [Source control and releases](source-control-and-releases.md) | Git conventions, commit messages, changelogs, tags and version numbers |
| Documentation | [Documentation and publishing](documentation-and-publishing.md) | DITA 1.3, CommonMark and GFM, GNU FDL 1.3, PDF, EPUB 3 and EPUB Accessibility |
| Interfaces | [APIs and protocols](apis-and-protocols.md) | OAuth 2.0 and OpenID Connect, REST and JSON, SOAP, feeds, mail, LDAP |
| Databases | [Databases and SQL](databases-and-sql.md) | SQL and the dialect of each supported database, the database abstraction |

## Architecture

| Page | What it covers |
|---|---|
| [Kernel architecture](architecture/kernel-architecture.md) | The request lifecycle, modules, views and functions, policies, datatypes, persistent objects, settings and siteaccesses, designs and overrides, templates, extensions, caches, workflows, cronjobs, the database layer and the Velocity application server |
| [Layering and naming rules](architecture/layering-and-naming.md) | Which layer may use which, and how new classes, modules, templates, settings and commands are named |
| [Compatibility, releases and deployment](architecture/compatibility-releases-deployment.md) | The backwards compatibility policy, version numbers and releases, and how a change is deployed |

## Design patterns

The [design pattern catalogue](patterns/README.md) lists every abstraction of the Exponential 6 stack: factories,
singletons, registries, handlers and strategies, abstract base classes, observers and event hooks, adapters and
facades, persistent objects, datatypes, template operators, fetch functions, module views, policy functions, workflow
events, cronjob parts and the INI settings that plug classes in, with the file, the class, a description and the
extension point of each. It is generated from the source code and can be rebuilt at any time with
`php bin/php/exppatterncatalogue.php --write`.

## Related

- [Source file header template (inherited, 4.1)](header.txt): the license header every PHP file of the 4.x code base
  began with, kept as history. New files use the header described in [Coding standards](coding-standards.md).
- [Accessibility, security and compliance checklists](../help/leadership/accessibility-security-and-compliance-checklists.md) for project managers and buyers
- [The license of this documentation](../README-LICENSE.md)
- [Security hardening](../help/security-hardening.md), [Database support](../help/database-support.md) and
  [Operating system support](../help/os-support.md) for the operator's view of the same subjects.
