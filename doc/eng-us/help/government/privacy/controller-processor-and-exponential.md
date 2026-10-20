---
title: Controller, processor and the software
audience: government, project-manager
category: public-sector
order: 30
summary: For public bodies sorting out data protection roles around a site - Exponential is software, not a data controller; who is usually controller and processor (your body, hosting company, implementation partner, third-party services in extensions), and what agreements and records follow.
keywords: controller, processor, sub-processor, data processing agreement, hosting company, implementation partner, support, third-party services, GDPR article 28, Exponential project, roles, public sector
type: help
---
# Controller, processor and the software

This page is for data protection officers and project managers. The project's statement is plain: "Exponential is
software, not a data controller. Compliance ... is the responsibility of the organization that runs the site"
([Privacy standards](../../../standards/privacy.md)). Around a public site several parties touch personal data; this
page helps you name them.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Typical parties](#typical-parties)
2. [Third-party services that extensions add](#third-party-services-that-extensions-add)
3. [Agreements and records](#agreements-and-records)
4. [Related pages](#related-pages)

## Typical parties

| Party | Usual role (to be confirmed with your counsel) | What they touch |
|---|---|---|
| Your public body | controller | everything the site processes |
| Hosting company | processor | the server, database, backups, logs |
| Implementation partner or agency | processor, when it accesses production data | content, user accounts, form submissions during work |
| Support provider (7x or a partner) | processor, when it accesses the installation | as above, for support cases |
| The Exponential project | none for your site: it publishes software | nothing, unless you send data in a support request |

The project's own error report in the help system builds its installation summary from a fixed list of facts and
removes e-mail addresses, IP addresses, credentials and paths before anything leaves your site
([Privacy standards: GDPR](../../../standards/privacy.md#gdpr)).

## Third-party services that extensions add

Some extensions installed with Exponential send visitor data to a third party when a site activates them
([Privacy standards: Third-party services](../../../standards/privacy.md#third-party-services-in-shipped-extensions)):

| Extension | Third party |
|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager |
| `recaptcha` | Google reCAPTCHA |
| `hcaptcha` | hCaptcha |
| `sevenx_authentication_2fa` social login | the chosen identity provider |
| `ezgmaplocation` | Google Maps (editors' map, and maps in your templates) |

Each of these is a separate relationship (processor or independent controller, depending on the service and your
counsel's view) with its own terms.

## Agreements and records

1. A data processing agreement with each processor (hosting, implementation, support).
2. An entry in your record of processing for the website's processing activities (see "Records of processing").
3. A list of active extensions with third parties: **Setup > Extensions**, or
   `./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user`.
4. The privacy notice names the recipients (see "A privacy notice outline").

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Hosting companies: compliance and GDPR](../../hosting/security/compliance-and-gdpr.md)
- [Implementation partners](../../partners/implementation-partners.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
