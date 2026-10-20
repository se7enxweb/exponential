---
title: Cookie banners and consent
audience: government, project-manager, developer, designer
category: public-sector
order: 200
summary: For public bodies deciding whether their site needs a cookie consent banner - Exponential ships no banner or consent manager, the kernel needs none for its functional cookies, and what to do when you add analytics, embeds or other non-essential storage.
keywords: cookie banner, consent manager, cookie consent, ePrivacy, article 5(3), strictly necessary, analytics consent, embeds, two-click, accessibility of banners, CSP, public sector
type: help
---
# Cookie banners and consent

This page is for project managers, data protection officers, designers and developers. Exponential ships **no cookie
consent banner or consent manager** in the kernel or the shipped designs; the project's statement says none was found in
`kernel/`, `design/` or `settings/` ([Privacy standards: ePrivacy Directive](../../../standards/privacy.md#eprivacy-directive-cookies)).
The kernel's own cookies are functional. A banner becomes your task when you add storage or services that need consent.
ePrivacy Directive: <https://eur-lex.europa.eu/eli/dir/2002/58/oj>.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Do you need one](#do-you-need-one)
2. [If you need one](#if-you-need-one)
3. [Alternatives that avoid it](#alternatives-that-avoid-it)
4. [Related pages](#related-pages)

## Do you need one

| Your site has | Usual view (confirm with your counsel) |
|---|---|
| only the kernel's cookies (session, `is_logged_in`) | consent not needed for strictly necessary cookies; describe them in the privacy notice |
| analytics (`bcwebsitestatistics` or another service) | consent usually needed; the extension has no consent gate of its own |
| a CAPTCHA (`recaptcha`, `hcaptcha`) | consent needed "in most readings of art. 5(3)", says the project's statement |
| embedded maps or video from other services | usually consent, because the service sets its own storage |
| social login buttons | the user chooses the provider; mention it in the notice |

## If you need one

- Load the third-party script **only after** consent; a banner that does not block is not consent.
- Build it into your design: a template block and a script file. Under a Content Security Policy, the script must be a
  file (no inline code) and the third-party hosts allowed only where used.
- Make the banner accessible: keyboard operable, focus managed, not covering the focused element, readable at 200%
  zoom, clear buttons of equal weight for accept and refuse.
- Record consent decisions as your counsel requires; Exponential's consent log covers mail, not cookies.
- If you use a consent manager product, it is a third party: check its own data processing.

## Alternatives that avoid it

- Use the operational statistics Exponential keeps (search statistics, view counts) where they answer the question
  ([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)).
- Self-hosted analytics without cookies, where your counsel agrees.
- Two-click embeds: show a placeholder with a link; load the map or video only on click.
- Server-side spam protection (the form token of `ezformtoken`, checks in your own code) instead of a CAPTCHA.

## Related pages

- [Cookies set by Exponential](cookies.md)
- [Third-party extensions](third-party-extensions.md)
- [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
