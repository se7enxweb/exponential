---
title: Hosting, domains and HTTPS explained
audience: non-technical, customer
category: plain-language
order: 17
summary: The three things every website needs besides Exponential - a server to run on, a domain name to be found at and HTTPS for the padlock - explained with a house comparison, who provides each, how they connect, and what to keep in your own hands.
keywords: hosting, host, server, domain name, registrar, DNS, HTTPS, certificate, padlock, e-mail, database, SQLite, explained, ownership
type: help
---
# Hosting, domains and HTTPS explained

## Contents

1. [The comparison: a house](#the-comparison-a-house)
2. [Hosting: where the site lives](#hosting-where-the-site-lives)
3. [The domain name: the address](#the-domain-name-the-address)
4. [HTTPS: the padlock](#https-the-padlock)
5. [How the three connect](#how-the-three-connect)
6. [Keep these in your own hands](#keep-these-in-your-own-hands)

## The comparison: a house

A website is like a house. **Hosting** is the plot of land and the building. The **domain name** is the street
address that lets people find it. **HTTPS** is the lock on the front door that keeps conversations inside private.
Exponential is the furniture and the household that make the house useful. Exponential needs all three, but provides
none of them itself.

## Hosting: where the site lives

A website runs on a computer that is always on and connected to the internet: a **server**. A **hosting company**
rents you one, or a share of one, and keeps it running.

For Exponential, the server needs PHP (the language Exponential is written in), a place to store the content (a
**database**) and a web server program ([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)).
You do not have to understand these, but they explain two things a host may say:

- **"SQLite is enough for you."** SQLite keeps the whole database in one file, with no database server to set up
  ([SQLite database support](../../../features/6.0/sqlite-database.md)). For a small site that keeps things simple
  and cheap.
- **"We run it on Velocity."** Exponential Velocity is Exponential's own fast web server program; Apache or nginx are
  common alternatives ([Velocity for businesses](../../businesses/reliability/velocity-for-businesses.md)). Either is
  fine for you.

How to choose a host is in [Hosting choices](../hosting-choices/README.md).

## The domain name: the address

Your **domain name** (`example.org`) is bought, or rather rented by the year, from a **domain registrar**. Exponential
does not sell domains. A setting at the registrar, called **DNS**, points the name at your host's server. When you
move to a new host, that setting is changed and the address stays the same.

The same DNS settings also decide where **e-mail** for `@example.org` goes. Your e-mail is usually run by your host or
a separate mail provider, not by Exponential ([Domain name and e-mail](../hosting-choices/domain-name-and-email.md)).

## HTTPS: the padlock

**HTTPS** means the connection between a visitor and your site is encrypted: nobody in between can read passwords,
form entries or orders. Browsers show a padlock, and warn visitors away from sites without it. HTTPS needs a
**certificate**, which the host sets up on the server; many hosts renew it automatically. Signing in to the
administration and taking payments should never happen without HTTPS.

## How the three connect

```
Visitor types example.org
        │
        ▼
  DNS (at the registrar) says: that is server 203.0.113.10
        │
        ▼
  The host's server answers, with HTTPS (padlock)
        │
        ▼
  Exponential builds the page from your content
```

If any link in this chain breaks, the site is unreachable even though Exponential is fine. That is why "the site is
down" is often a question for the host or the registrar first ([Who to call](../when-stuck/who-to-call.md)).

## Keep these in your own hands

Whoever builds your site, make sure that:

- the **domain name** is registered in **your** organization's name, with your e-mail address as the contact;
- you have the **login to the registrar** (or at least know who has it);
- you know **where the backups are** and can get a copy;
- the agreement with your host says you get your **files and database** when you leave.

[What you own](../../customers/what-you-own.md) explains why, and
[What to ask before you sign](../hosting-choices/what-to-ask-before-you-sign.md) turns it into questions.

## Related pages

- [Words explained](README.md)
- [Three ways to get a site](../hosting-choices/three-ways-to-get-a-site.md)
- [Choosing a host](../../customers/partners-and-hosts/choosing-a-host.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
