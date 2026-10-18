---
title: Security reports stay private
audience: advocate
category: advocacy
order: 104
summary: What an advocate does when someone mentions a vulnerability in Exponential - stop public discussion, route it privately to the address in SECURITY.md or GitHub's private reporting, never reproduce it on others' sites, never promise dates - with message templates.
keywords: security, vulnerability, responsible disclosure, private report, SECURITY.md, security@se7enx.com, GitHub private vulnerability reporting, advocate, templates
type: help
---
# Security reports stay private

This page is for every advocate. Sooner or later someone will tell you about a possible vulnerability, in a forum, a
chat or after a talk. What you do in the next few minutes matters: a public vulnerability can be misused against
every site before a fix exists.

## Contents

1. [The rule](#the-rule)
2. [The private path](#the-private-path)
3. [Step by step](#step-by-step)
4. [Message templates](#message-templates)
5. [What you never do](#what-you-never-do)

## The rule

> Security problems are reported privately, never in a public tracker, forum, chat or post.

"Please report security issues privately by e-mail to security@se7enx.com, not in the public issue tracker or the
discussions. You can expect a first answer within one business day"
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). GitHub's private vulnerability
reporting of the repository is also accepted ([When something goes wrong](../when-something-goes-wrong.md#security-problems)).

## The private path

The report should include, from SECURITY.md:

- the version (`composer show se7enxweb/exponential`, or the tag or commit) and the PHP version;
- the engine (Exponential Velocity, FrankenPHP, Apache or nginx with PHP-FPM) and the database;
- what an attacker can do, and the steps to reproduce it.

And it should **leave out** passwords, secrets and personal data.

What happens next, per SECURITY.md: the reporter is told whether the report is accepted or declined and kept informed;
an accepted issue is fixed on `main` and released with the next 6.0.x version.

## Step by step

1. **Stop the spread.** Ask the person kindly not to post more details publicly; if details are already public, ask
   them to remove them.
2. **Route it.** Give them the private address or GitHub's private reporting. Let **them** report; they found it.
3. **If they cannot or will not**, and you have the details, report it privately yourself, crediting them, and tell
   them you did.
4. **Forget the details in public.** Do not discuss them in any channel, even after a fix, beyond what the release
   notes say.
5. **Do not test it on sites you do not own.** Testing someone else's site can be illegal and harmful.

## Message templates

```text
IN A PUBLIC THREAD
Thanks for spotting this! Could you remove the details here and send them privately as SECURITY.md
describes (security@se7enx.com, or GitHub's private vulnerability reporting)? That way it can be fixed
before anyone misuses it. You'll get a first answer within one business day.

IN PRIVATE
Thanks for telling me. The right place is security@se7enx.com (or the private vulnerability reporting
on the GitHub repository). Please include the version, PHP, engine, database, what an attacker could do
and the steps - and no passwords or personal data. I won't share what you told me.
```

## What you never do

- Post, retweet or forward vulnerability details.
- Open a public issue about it.
- Promise a fix date or an outcome.
- Judge the severity in public ("it's nothing", "it's critical").
- Ask for a reward or offer one; the sources describe no bounty program.

## Related pages

- [Talking points: security](talking-points-security.md)
- [Advocating for security](advocating-for-security.md)
- [Which tracker for which report](which-tracker-for-which-report.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
