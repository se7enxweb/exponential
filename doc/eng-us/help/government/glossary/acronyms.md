---
title: Acronyms for government readers
audience: government, project-manager, admin
category: public-sector
order: 60
summary: The acronyms that appear in public-sector procurement, accessibility, security, privacy and open data, and in this help - ACR, ASVS, CSP, DCAT, EAA, FOI, GDPR, NIS2, OAIS, RFP, SLA, TCO, VPAT, WCAG and more - with their full names and official sources.
keywords: acronyms, abbreviations, ACR, ASVS, CSP, DCAT, EAA, EN 301 549, FOI, FOIA, GDPR, NIS2, OAIS, OWASP, RFP, RPO, RTO, SLA, TCO, VPAT, WAD, WCAG, public sector
type: reference
---
# Acronyms for government readers

This page is for readers in a public body who meet an abbreviation in this help, in a tender or in a standard. It gives
the full name, a one-line meaning and, where one exists, the official source. Where Exponential's own status matters,
the line says where to read it.

## Contents

1. [Accessibility](#accessibility)
2. [Security](#security)
3. [Privacy, law and open data](#privacy-law-and-open-data)
4. [Procurement and operations](#procurement-and-operations)
5. [Exponential and the web](#exponential-and-the-web)

## Accessibility

| Acronym | Full name | Meaning and source |
|---|---|---|
| ACR | Accessibility Conformance Report | a vendor's conformance report, often made with a VPAT; none exists for Exponential ([Accessibility standards](../../../standards/accessibility.md#section-508)) |
| ARIA | Accessible Rich Internet Applications | W3C roles and states for assistive technology ([WAI-ARIA 1.2](https://www.w3.org/TR/wai-aria-1.2/)) |
| EAA | European Accessibility Act | Directive (EU) 2019/882 ([EUR-Lex](https://eur-lex.europa.eu/eli/dir/2019/882/oj)) |
| EN 301 549 | (European standard number) | accessibility requirements for ICT products and services ([ETSI, v3.2.1](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf)); Exponential: Unverified |
| VPAT | Voluntary Product Accessibility Template | the template for an ACR, mainly for Section 508 ([section508.gov](https://www.section508.gov/)) |
| WAD | Web Accessibility Directive | Directive (EU) 2016/2102 for public sector websites and apps ([EUR-Lex](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)) |
| WAI | Web Accessibility Initiative | the W3C group that publishes WCAG and guidance |
| WCAG | Web Content Accessibility Guidelines | [WCAG 2.2](https://www.w3.org/TR/WCAG22/); Exponential admin4: Partly complies at AA |
| WCAG-EM | WCAG Evaluation Methodology | how to evaluate a website against WCAG ([WCAG-EM](https://www.w3.org/TR/WCAG-EM/)) |

## Security

| Acronym | Full name | Meaning and source |
|---|---|---|
| 2FA, MFA | two-factor or multi-factor authentication | a second proof at sign-in; in Exponential opt-in ([Two-step sign-in](../../../guides/two-factor-login.md)) |
| ASVS | Application Security Verification Standard | OWASP's catalog of testable requirements ([OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/)); no assessment of Exponential made |
| CSP | Content Security Policy | browser rules for allowed sources ([CSP3](https://www.w3.org/TR/CSP3/)); [Content Security Policy](../../../features/6.0/content-security-policy.md) |
| CSRF | Cross-Site Request Forgery | an attack that misuses a signed-in browser; Exponential puts tokens on forms ([Security standards](../../../standards/security.md)) |
| CSF | Cybersecurity Framework | NIST's framework, version 2.0 ([nist.gov](https://www.nist.gov/cyberframework)) |
| HSTS | HTTP Strict Transport Security | tells browsers to use HTTPS only; set by the operator in Exponential |
| NIS2 | Network and Information Security Directive 2 | Directive (EU) 2022/2555 ([EUR-Lex](https://eur-lex.europa.eu/eli/dir/2022/2555/oj)) |
| OWASP | Open Worldwide Application Security Project | publishes the [Top 10](https://owasp.org/Top10/) and ASVS |
| TLS | Transport Layer Security | the encryption behind HTTPS ([TLS and HTTPS](../../hosting/security/tls-and-https.md)) |
| XSS | Cross-Site Scripting | injecting script into pages; mitigated by escaping and CSP |

## Privacy, law and open data

| Acronym | Full name | Meaning and source |
|---|---|---|
| DCAT | Data Catalog Vocabulary | W3C vocabulary for data catalogs ([DCAT 3](https://www.w3.org/TR/vocab-dcat-3/)) |
| DPA | data processing agreement (also: data protection authority) | the controller-processor contract; the term is ambiguous, so spell it out |
| DPIA | data protection impact assessment | an assessment the GDPR requires for high-risk processing |
| FOI, FOIA | freedom of information; Freedom of Information Act | public right to request records; US: [foia.gov](https://www.foia.gov/) |
| GDPR | General Data Protection Regulation | Regulation (EU) 2016/679 ([EUR-Lex](https://eur-lex.europa.eu/eli/reg/2016/679/oj)); [Privacy standards](../../../standards/privacy.md) |
| OAIS | Open Archival Information System | reference model for digital archives, ISO 14721 ([iso.org](https://www.iso.org/standard/57284.html)) |
| ODD | Open Data Directive | Directive (EU) 2019/1024 ([EUR-Lex](https://eur-lex.europa.eu/eli/dir/2019/1024/oj)) |

## Procurement and operations

| Acronym | Full name | Meaning |
|---|---|---|
| PoC | proof of concept | a short test installation before a decision |
| RFI | request for information | an early market question before a tender |
| RFP | request for proposal | a tender asking for proposals |
| RFQ | request for quotation | a tender asking for prices on a defined scope |
| RPO | recovery point objective | how much data may be lost ([SLA options](../../hosting/business/sla-options.md)) |
| RTO | recovery time objective | how long a restore may take |
| SLA | service level agreement | measurable service promises; the project publishes none |
| TCO | total cost of ownership | all costs over the life of a system |

## Exponential and the web

| Acronym | Full name | Meaning |
|---|---|---|
| CMS | content management system | software for managing website content, such as Exponential |
| GPL | GNU General Public License | the license of Exponential ([Licensing questions](../../project/licensing-faq.md)) |
| GFDL | GNU Free Documentation License | the license of this documentation ([Free documentation](../../free-documentation.md)) |
| REST | Representational State Transfer | the style of Exponential's HTTP API ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)) |
| RSS | Really Simple Syndication | feeds of new content ([RSS feeds](../../../guides/rss-feeds.md)) |
| RTL | right-to-left | languages such as Arabic and Hebrew ([Right-to-left languages](../../../features/6.0/right-to-left.md)) |
| INI | (settings file format) | Exponential's settings files, `settings/*.ini` ([The settings page](../../../guides/settings-page.md)) |

## Related pages

- [Government glossary](README.md)
- [Exponential terms for government readers](exponential-terms-for-government-readers.md)
- [The Exponential glossary](../../../glossary.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
