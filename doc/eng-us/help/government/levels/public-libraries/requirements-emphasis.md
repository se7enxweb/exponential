---
title: Requirements emphasis for public libraries
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for a public library site - accessibility and inclusion, many languages, reader privacy and third-party services, security on shared staff computers, records - and what Exponential provides.
keywords: requirements, library, accessibility, inclusion, easy read, languages, reader privacy, third-party, analytics, shared computers, two-step sign-in
type: help
---
# Requirements emphasis for public libraries

This page is for project managers and administrators of a public library site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accessibility and inclusion](#accessibility-and-inclusion)
2. [Languages](#languages)
3. [Reader privacy](#reader-privacy)
4. [Security](#security)
5. [Records](#records)

## Accessibility and inclusion

Libraries serve everyone, including people who read with difficulty, use screen readers or magnification, or are new
to computers. Public libraries run by public bodies fall under public-sector accessibility rules in many
jurisdictions (in the EU, [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)).

Exponential's status: the admin4 administration **partly complies** with WCAG 2.2 AA; EN 301 549 and Section 508
**unverified**; public designs not audited ([Accessibility standards](../../../../standards/accessibility.md)). So:

| Area | Your work |
|---|---|
| The calendar | the month grid must be usable with a keyboard and a screen reader; the program list view is often easier for both |
| Event descriptions | date, time, place and accessibility of the venue in text, not only in a poster image |
| Images | alternative text required on image attributes ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)) |
| Easy reading | short sentences on service pages; plain language guidance at [plainlanguage.gov](https://www.plainlanguage.gov/) |
| Statement | an accessibility statement ([W3C](https://www.w3.org/WAI/planning/statements/)) |

## Languages

Library communities often speak many languages. Translations per object and a siteaccess per language let key pages
exist in several languages ([Content languages](../../../../guides/content-languages.md)); right-to-left scripts are
supported ([Right-to-left languages](../../../../features/6.0/right-to-left.md)). Start with joining, opening hours and
the events for language learners.

## Reader privacy

Libraries traditionally protect what people read and borrow. On the website:

| Risk | Measure |
|---|---|
| Third-party analytics or embedded social media that profile visitors | prefer privacy-friendly statistics; Exponential loads no third-party scripts in the administration ([Privacy standards](../../../../standards/privacy.md)) |
| Event sign-up forms storing names and contact details | collect the minimum; remove submissions after the event ([Collected information](../../../../guides/collected-information.md)) |
| Newsletter | consent and one-click unsubscribe through mail preferences ([Mail preferences](../../../../features/6.0/mail-preferences.md)) |
| Catalog sign-in on the website | do not build it; link to the catalog's own sign-in |
| CAPTCHA on forms | `recaptcha` and `hcaptcha` are third parties ([Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md)) |

Exponential has no cookie banner; decide with your data protection officer what you need under the
[ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj) and the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj)
in the EU.

## Security

Staff publish from shared computers at the desk. Use two-step sign-in ([Two-step sign-in](../../../../guides/two-factor-login.md)),
sign out after use, and review accounts of staff who left (**Users > Check user accounts**,
[Check user accounts](../../../../features/6.0/user-hygiene.md)). The account lockout setting
(`MaxNumberOfFailedLogin`) is off by default ([Security hardening](../../../security-hardening.md)).

## Records

Library websites hold few formal records, but governance documents (board minutes, annual reports, plans) are records
of the library's governing body; keep them as File objects and in your records system. Events need no long-term
record on the website; past events can be removed or kept in an archive folder by year.

## Related pages

- [Public libraries](README.md)
- [The first 90 days for public libraries](first-90-days.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
