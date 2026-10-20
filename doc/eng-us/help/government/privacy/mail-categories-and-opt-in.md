---
title: Mail categories and opt-in
audience: government, admin, project-manager, developer
category: public-sector
order: 120
summary: For public bodies sending optional e-mail - Exponential's essential and optional mail categories, why optional ones are off until a person turns them on, double opt-in for newsletters and offers, adding your own categories, and making sure every mail carries a category.
keywords: mail categories, opt-in, double opt-in, essential mail, optional mail, DefaultOn, AllowDefaultOn, newsletter, marketing, setCategory, mail gate, consent, ePrivacy article 13, public sector
type: help
---
# Mail categories and opt-in

This page is for administrators, communication teams and developers. Every mail an Exponential site sends passes a
mail gate that knows its **category**: essential mail always goes, optional mail goes only to people who switched it
on ([13.13 Mail: consent and suppression](../../../install/13-security-hardening.md#1313-mail-consent-and-suppression),
[E-mail preferences](../../../features/6.0/mail-preferences.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Essential and optional](#essential-and-optional)
2. [Off until turned on](#off-until-turned-on)
3. [Double opt-in](#double-opt-in)
4. [Your own categories](#your-own-categories)
5. [Mail without a category](#mail-without-a-category)
6. [Related pages](#related-pages)

## Essential and optional

| Kind | Categories (default installation) | Behavior |
|---|---|---|
| Essential | `security`, `orders`, `legal`, `admin` | always sent; no advertising |
| Optional | Content notifications, Collaboration and approvals, Newsletters, Offers and news, Application and system notices | sent only when the person turned it on; footer with the reason and a one-click unsubscribe |

## Off until turned on

Optional categories are off by default (`[Category_*] DefaultOn=false`). `DefaultOn=true` is refused unless
`[CategorySettings] AllowDefaultOn=enabled`; the administrator's guide warns never to set it for an optional category,
as it would send mail to people who did not ask for it ([Privacy standards: GDPR](../../../standards/privacy.md#gdpr)).

## Double opt-in

**Newsletters** and **Offers and news** need a confirmation: turning one on sends a link, and nothing is sent until the
person confirms. After `[ConsentSettings] PendingDays` (7) the request lapses. Importing consent from elsewhere
(`./console exp:mail:preferences set ... --source=import --wording="..."`) switches it on at once and is meant only for
consent you can prove; never switch a double opt-in category on from the console without `--no-mail` on a system that
really sends.

## Your own categories

**Setup > E-mail preferences > Categories > New category**: an identifier (fixed once made), a name and description as
people see them, the frequencies people may choose, and whether confirmation is asked. A category made there is always
optional and off until a person turns it on. Code sends in it with `eZMail::setCategory( '<identifier>' )`. Settings
categories are in `settings/mailpreferences.ini [CategorySettings] Categories[]`
([Mail preferences for administrators: Categories](../../../guides/mail-preferences-administrator.md#4-categories)).

Name categories by purpose, plainly ("Road works alerts for your district"), so the consent is specific.

## Mail without a category

Mail sent without `eZMail::setCategory()` is sent without a footer or unsubscribe link (for compatibility) and is
counted in `var/<vardir>/log/mailgate.jsonl`; the status page names its sender. Ask developers to give every optional
mail a category. Check **Setup > E-mail preferences** status regularly.

## Related pages

- [E-mail preferences](../../../features/6.0/mail-preferences.md)
- [Mail preferences for developers](../../../guides/mail-preferences-developer.md)
- [Consent records for mail](consent-records-for-mail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
