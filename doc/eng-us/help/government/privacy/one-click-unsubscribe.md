---
title: One-click unsubscribe
audience: government, admin, project-manager
category: public-sector
order: 130
summary: For public bodies sending bulk e-mail - how Exponential makes withdrawal as easy as consent: an unsubscribe link and List-Unsubscribe headers (RFC 8058) in every optional mail, no login, immediate effect, and what the site must add (postal address, DKIM, categories).
keywords: unsubscribe, one-click unsubscribe, List-Unsubscribe, List-Unsubscribe-Post, RFC 8058, RFC 2369, withdrawal of consent, GDPR article 7(3), CAN-SPAM, mail gate, footer, DKIM, bulk sender, public sector
type: help
---
# One-click unsubscribe

This page is for administrators and communication teams. Withdrawing consent must be as easy as giving it, and large
mailbox providers require one-click unsubscribe from bulk senders. Exponential's mail gate adds both to every optional
mail ([Privacy standards: CAN-SPAM Act and one-click unsubscribe](../../../standards/privacy.md#can-spam-act-and-one-click-unsubscribe)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What every optional mail carries](#what-every-optional-mail-carries)
2. [What happens on a click](#what-happens-on-a-click)
3. [What the site must add](#what-the-site-must-add)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## What every optional mail carries

Added by `expMailGate::decorate()`:

- a footer with the reason the person gets the mail, an **Unsubscribe** link and a link to their preference page;
- `List-Unsubscribe`: an HTTPS link to `mailpreferences/unsubscribe/<token>`;
- `List-Unsubscribe-Post: List-Unsubscribe=One-Click` (RFC 8058, building on RFC 2369).

The gate never rewrites `From`, `Reply-To` or `Subject`. Essential mail (password reset, activation, orders) is kept
separate and carries no advertising. The project rates this **Complies** for mail that goes through the mail gate.

## What happens on a click

- No login, no form token and no cookie needed; a POST with `List-Unsubscribe=One-Click` takes effect at once.
- Links do not expire by default.
- The change is recorded in the consent log with its source.
- On the preference page, **Turn off all optional e-mail** stops every optional kind at once.

## What the site must add

- **The sender's postal address** in the footer: `mailpreferences.ini [FooterSettings] OrganisationName` and
  `OrganisationAddress` (Sender details on the status page). Without them optional mail goes out without an address.
- **A category on every commercial or optional mail**: mail sent without `eZMail::setCategory()` gets no footer and no
  unsubscribe link.
- **DKIM signing** of the headers: done by the mail server.
- **Per-recipient sending**: keep the default; `SplitRecipients=disabled` sends to several recipients at once without
  `List-Unsubscribe`.
- **The site secret** in `settings/override/mailpreferences.ini.append.php` backed up: without it, links in mail already
  sent stop working.

## Checking

```bash
php bin/php/console exp:mail:status --allow-root-user     # five times ok
```

Then send a test mail of an optional category to a test mailbox and look at its headers for `List-Unsubscribe` and
`List-Unsubscribe-Post`. Test the gate without sending as described in
[Testing the gate without sending mail](../../../guides/mail-preferences-administrator.md#10-testing-the-gate-without-sending-mail).

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Mail preferences for administrators](../../../guides/mail-preferences-administrator.md)
- [The suppression list](suppression-list.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
