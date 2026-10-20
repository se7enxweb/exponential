---
title: Answer an access request for mail preferences
audience: government, admin
category: public-sector
order: 250
summary: Step by step - give a person their e-mail preferences and full consent history from Exponential as JSON or CSV, from the administration or the console, check the file and hand it over safely.
keywords: access request, subject access request, DSAR, mail preferences, consent history, export, JSON, CSV, exp:mail:preferences export, mailpreferences/export, GDPR article 15, public sector
type: help
---
# Answer an access request for mail preferences

For administrators who handle data protection requests. The mail preferences and the consent log are the part of a
person's data that Exponential exports directly ([The right of access](../../privacy/right-of-access.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps in the administration](#steps-in-the-administration)
3. [Steps on the console](#steps-on-the-console)
4. [Hand-over](#hand-over)
5. [Related pages](#related-pages)

## Before you start

- The person's identity is verified as your procedure says.
- The policy `mailpreferences/export` (for downloads) and access to Setup > E-mail preferences.
- Tell the person they can download the same data themselves on their preference page, without waiting for you.

## Steps in the administration

1. **Setup > E-mail preferences** > **A user**: find the user by name, login, address or id, open them.
2. You see their page as they see it, with their consent history.
3. **Download as JSON** or **Download as CSV**.

## Steps on the console

Also for an address without an account:

```bash
./console exp:mail:preferences show --email=<address> --allow-root-user
./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/<case>/mail.json --allow-root-user
```

The file is written with mode 0600. Console output hides addresses unless you add `--addresses`.

## Hand-over

- Check the file: it should contain only this person's data.
- Send it through a channel your procedure allows; JSON for portability, CSV or a readable summary otherwise.
- Delete the working file when the request is closed and record what you sent and when.
- Other data (account, form submissions, audit) is collected separately; see "Export audit records about a person".

## Related pages

- [The right of access](../../privacy/right-of-access.md)
- [Mail preferences for administrators: Requests](../../../../guides/mail-preferences-administrator.md#6-requests-under-data-protection-law)
- [Consent records for mail](../../privacy/consent-records-for-mail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
