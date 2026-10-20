---
title: One-click unsubscribe and the mail gate
audience: government, admin
category: public-sector
order: 70
summary: How every optional e-mail an Exponential site sends gets an unsubscribe link and the List-Unsubscribe headers (RFC 8058) from the kernel's mail gate, what the site must add (sender postal address, categories, DKIM), and how to test it without sending.
keywords: unsubscribe, one-click unsubscribe, List-Unsubscribe, List-Unsubscribe-Post, RFC 8058, mail gate, CAN-SPAM, mail preferences, footer, DKIM, opt-out
type: help
---
# One-click unsubscribe and the mail gate

This page is for administrators. Every mail the installation sends passes the **mail gate** in
`eZMailTransport::send()`; optional mail gets a footer with the reason and an unsubscribe link, and the headers
`List-Unsubscribe` and `List-Unsubscribe-Post: List-Unsubscribe=One-Click`
([Privacy standards](../../../../standards/privacy.md#can-spam-act-and-one-click-unsubscribe),
[Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)).

## Contents

1. [What the gate does](#what-the-gate-does)
2. [What the site must add](#what-the-site-must-add)
3. [The status page](#the-status-page)
4. [Testing without sending](#testing-without-sending)

## What the gate does

- Adds the unsubscribe link (to `mailpreferences/unsubscribe/<token>`) and the two headers to optional mail.
- The unsubscribe needs no login, no form token and no cookie; a one-click POST takes effect at once.
- Never rewrites `From`, `Reply-To` or `Subject`.
- Keeps essential mail (password reset, activation) separate from optional mail.

The privacy standard rates this **Complies** for mail that goes through the gate, with the items below left to the site.

## What the site must add

- The sender's postal address and organization name in the footer (sender details on the status page).
- A category for every optional mail: mail sent without a category gets no footer and no unsubscribe link.
- DKIM signing of the two headers, done by the mail server.
- Keep the default per-recipient sending; `SplitRecipients=disabled` sends to several recipients at once without
  `List-Unsubscribe`.

## The status page

**Setup > E-mail preferences** (`/mailpreferences/admin/status`) shows the state of the gate and lists problems first,
such as an empty organization name or postal address in the footer.

## Testing without sending

The mail preferences guide has a section on testing the gate without sending mail; test with the file transport and
addresses on a reserved test domain, never real people ([Mail preferences for administrators, section 10](../../../../guides/mail-preferences-administrator.md#10-testing-the-gate-without-sending-mail)).

## Related pages

- [E-mail preferences](../../../../features/6.0/mail-preferences.md)
- [Bounces, blacklist and list hygiene](bounces-blacklist-and-list-hygiene.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
