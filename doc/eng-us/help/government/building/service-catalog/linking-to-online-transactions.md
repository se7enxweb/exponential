---
title: Linking to online transactions
audience: government, website-creator, project-manager
category: public-sector
order: 70
summary: How service pages built in Exponential hand people over to the systems where they apply, pay or book - clear links, what to say before the link, external services that change, link checks - and which transactions not to build as website forms.
keywords: online transactions, apply online, payments, booking, e-government portal, external systems, links, ezurl, link check, single sign-on, information collector limits
type: help
---
# Linking to online transactions

This page is for website creators and project managers who connect service pages with the systems where services are
actually used: application portals, payment providers, booking systems, case management. The website explains and
points; the transaction happens elsewhere. This page says how to make that hand-over clear and reliable, and which
transactions a CMS form should not try to replace.

## Contents

1. [What the website does and does not do](#what-the-website-does-and-does-not-do)
2. [The link on the service page](#the-link-on-the-service-page)
3. [Keeping links working](#keeping-links-working)
4. [When a simple form is enough](#when-a-simple-form-is-enough)
5. [Signing in across systems](#signing-in-across-systems)

## What the website does and does not do

| The website (Exponential) | The transaction system |
|---|---|
| Explains who can apply, what is needed, what it costs | Collects the application and documents |
| Links to the start of the transaction | Identifies the applicant, takes payment |
| Says what happens next and how long it takes | Tracks the case, sends decisions |

Exponential has a shop and payment gateways for selling products ([Shop overview](../../../creators/website-creators/shop-overview.md),
[Payment gateways](../../../creators/website-creators/payment-gateways.md)); public fees and fines are usually taken by
dedicated government payment services with their own accounting and audit, and are better linked than rebuilt.

## The link on the service page

| Practice | Why |
|---|---|
| The link is in its own **Apply online** attribute (`ezurl`), shown as a clear button near the top | one place to change it; consistent position |
| The link text names the action: "Apply for a resident parking permit" | screen reader users hear links out of context |
| Before the link, say what to have ready (documents, ID, payment card) and how long it takes | people do not abandon halfway |
| Say if the link leaves the site, and to whose system | trust; privacy notices differ |
| Offer the alternative (phone, office) for people who cannot use the online system | inclusion |

An Exponential `ezurl` attribute stores the address in the URL table shared by all links, which is what the link
checker reads ([Link and content checks](../../../creators/website-creators/link-and-content-checks.md)).

## Keeping links working

Transaction systems change addresses more often than service pages change.

| Measure | How |
|---|---|
| Link check | the `linkcheck.php` cronjob (infrequent part) tests external links; **Setup > Link management** (`/url/list`) shows the results |
| One URL object per address | all pages that use the same address share it; fixing it once fixes every page |
| A contact with the system owners | they tell you before an address changes |
| A redirect on your side | a short address on your own domain (a URL wildcard or global alias to the external address) that you can repoint ([Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)) |

## When a simple form is enough

The information collector suits short, low-risk requests: a question, a report of a broken streetlight, feedback. It
stores submissions in **Setup > Collected information** and can mail them ([Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)).

| Suitable for the information collector | Not suitable |
|---|---|
| A few fields, no identity check | applications that need identity verification |
| No payment | anything with payment |
| No special categories of personal data | health, social care, criminal matters |
| No case tracking needed | requests that become cases with deadlines |
| Small attachments, rarely | many documents per application |

Forms are protected by CSRF form tokens; CAPTCHA extensions are third parties
([Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md)).

## Signing in across systems

People dislike signing in twice. Exponential's login handlers can be extended, and `sevenx_authentication_2fa`
includes OAuth social login handler skeletons ([sevenx_authentication_2fa](../../../../features/6.0/extensions/sevenx_authentication_2fa.md));
connecting to a government identity service is a development task, not a setting. For most service catalogs, the
public pages need no sign-in at all, and the transaction system handles identity.

## Related pages

- [Building a service catalog](README.md)
- [The service page and plain language](service-page-and-plain-language.md)
- [Safe redirects](../../../../features/6.0/safe-redirects.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
