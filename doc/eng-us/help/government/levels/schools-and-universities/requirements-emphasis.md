---
title: Requirements emphasis for schools and universities
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for an education site - accessibility, data and images of minors, staff and student accounts that change every year, languages, records - and what Exponential provides for each.
keywords: requirements, school, university, accessibility, minors, children's data, consent, photos, accounts, deactivation, two-step sign-in, languages, records
type: help
---
# Requirements emphasis for schools and universities

This page is for project managers and administrators of a school, college or university site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accessibility](#accessibility)
2. [Data and images of minors](#data-and-images-of-minors)
3. [Accounts that change every year](#accounts-that-change-every-year)
4. [Languages](#languages)
5. [Records](#records)

## Accessibility

Public education institutions are public sector bodies under accessibility rules in many jurisdictions (in the EU,
[Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj); in the United States, institutions
receiving federal funds face their own rules, and [Section 508](https://www.section508.gov/) applies to federal
agencies). Students with disabilities depend on the intranet as much as on the public site.

Exponential's status: admin4 administration **partly complies** with WCAG 2.2 AA; EN 301 549 and Section 508
**unverified**; public designs not audited; no VPAT ([Accessibility standards](../../../../standards/accessibility.md)).
Departments that publish their own documents need training: accessible documents and alternative texts
([Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)).

## Data and images of minors

Schools publish for and about children. Practice in many jurisdictions:

| Topic | Measure |
|---|---|
| Photos of pupils | only with recorded consent; no names next to photos of children unless consent covers it |
| Pupils' work | consent; no personal details |
| Forms for parents | collect the minimum; remove after use ([Collected information](../../../../guides/collected-information.md)) |
| Staff profiles | public role information only |
| Third-party services (video embeds, CAPTCHA, maps) | check what they collect before using them |

Exponential stores what you upload; it has no consent register for photos. Keep consent records in your school
administration and note in an image's description field that consent exists. The
[GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) gives children's data particular protection in the EU
([Privacy standards](../../../../standards/privacy.md)).

## Accounts that change every year

| Need | Exponential | Read |
|---|---|---|
| Find accounts nobody uses | **Users > Check user accounts**, filters *Never signed in*, *Not seen for N days* | [Check user accounts](../../../../features/6.0/user-hygiene.md) |
| Disable leavers in bulk | **Disable** on ticked accounts; **Deactivate and move** (opt-in) to a hidden group without roles | the same |
| Strong sign-in for staff | two-step sign-in, enforceable | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Password rules | `MinPasswordLength` (10), password expiry and rules with ezmbpaex | [ezmbpaex](../../../../features/6.0/extensions/ezmbpaex.md) |
| Removal of an account | final (user objects never go to the trash); mail preferences erased | [Privacy standards](../../../../standards/privacy.md) |

## Languages

Universities often teach in several languages and schools serve families with many home languages. Translations per
object and a siteaccess per language ([Content languages](../../../../guides/content-languages.md)); the Course class
can carry its language of instruction as an attribute.

## Records

Policies, governance minutes, inspection reports and admissions rules are records. Keep the approved versions as File
objects with approval dates; versions and the audit trail record changes on the website
([Versions of an object](../../../../guides/content-history.md), [Audit trail](../../../../features/6.0/audit-trail.md)).
The formal archive is your records system.

## Related pages

- [Schools and universities](README.md)
- [The first 90 days for schools and universities](first-90-days.md)
- [Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
