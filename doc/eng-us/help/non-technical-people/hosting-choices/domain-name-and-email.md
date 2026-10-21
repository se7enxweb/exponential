---
title: Domain name and e-mail
audience: non-technical, customer
category: plain-language
order: 45
summary: Who provides your domain name and your e-mail - not Exponential, but a registrar and a mail provider - why the domain must be in your own name, what DNS changes when you move, and why the site's own e-mails (password resets, form notifications) need working mail.
keywords: domain name, registrar, DNS, e-mail, mailbox, mail provider, ownership, renewal, outgoing mail, password reset, form notification, non-technical
type: help
---
# Domain name and e-mail

Exponential runs your website. It does **not** register your domain name, and it does **not** run your mailboxes.
Those come from other providers, and they deserve as much care as the site itself.

## Contents

1. [The domain name](#the-domain-name)
2. [Your e-mail](#your-e-mail)
3. [The e-mails the site sends](#the-e-mails-the-site-sends)
4. [A small checklist](#a-small-checklist)

## The domain name

Your domain name (`example.org`) is rented by the year from a **registrar**. A setting there, the **DNS**, points the
name at your host's server ([Hosting, domains and HTTPS explained](../words/hosting-domains-and-https-explained.md)).

Three rules:

1. **Register it in your organization's name**, with an e-mail address you will still read in five years (not a
   personal address of someone who might leave).
2. **Know who has the login** to the registrar, and write it in [Who to call](../when-stuck/who-to-call.md).
3. **Watch the renewal date.** If the domain lapses, the site and the e-mail stop working, and someone else may buy
   the name. Turn on automatic renewal where possible.

When you move to a new host, only the DNS setting changes; the domain stays yours.

## Your e-mail

Mailboxes such as `info@example.org` are run by your host or by a separate mail provider. The DNS settings at the
registrar decide where mail goes. Exponential has nothing to do with reading or sending your everyday mail.

If you move hosts, make sure the mail settings move too, or mail can be lost for days. Name this explicitly when you
plan a move ([Moving to a new host](moving-to-a-new-host.md)).

## The e-mails the site sends

Exponential itself sends e-mails: password reset links, two-step sign-in codes by e-mail, notifications to people who
follow a part of the site, form notifications, shop order confirmations and newsletters, where those are in use. For
that the server needs working **outgoing mail**, which the host sets up
([Choosing a host](../../customers/partners-and-hosts/choosing-a-host.md#what-the-site-needs)).

If people say "the password reset mail never arrives", check the spam folder first, then ask your host whether the
site can send mail ([Forgot your password?](../../logging-into-the-editor.md#forgot-your-password)).

## A small checklist

- [ ] The domain is registered in our organization's name.
- [ ] We know the registrar, the login holder and the renewal date.
- [ ] Automatic renewal is on, with a payment method that will not expire soon.
- [ ] We know who runs our mailboxes.
- [ ] A test password reset mail from the site arrives.

## Related pages

- [Hosting choices](README.md)
- [Hosting, domains and HTTPS explained](../words/hosting-domains-and-https-explained.md)
- [Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md), for site builders
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
