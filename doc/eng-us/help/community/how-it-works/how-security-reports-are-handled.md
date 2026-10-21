---
title: How security reports are handled
audience: community, admin, developer, hosting, advocate
category: community
order: 53
summary: What community members do when they find or hear of a security problem in Exponential - the private reporting path, what to include and leave out, what happens next, and how to behave in public channels until a fix is released.
keywords: security, vulnerability, report, security@se7enx.com, private vulnerability reporting, SECURITY.md, responsible disclosure, fix, release
type: help
---
# How security reports are handled

Security problems follow a different path from every other report: they are private until a fix is released.

## Contents

1. [Where to send a report](#where-to-send-a-report)
2. [What to include](#what-to-include)
3. [What happens next](#what-happens-next)
4. [In public channels](#in-public-channels)

## Where to send a report

"Please report security issues privately by e-mail to security@se7enx.com, not in the public issue tracker or the
discussions" (`SECURITY.md`). GitHub's private vulnerability reporting of the repository is also accepted
([When something goes wrong](../../when-something-goes-wrong.md)).

## What to include

From `SECURITY.md`:

- the version (`composer show se7enxweb/exponential`, or the tag or commit you run) and the PHP version;
- the engine that serves the site (Exponential Velocity, FrankenPHP, Apache or nginx with PHP-FPM) and the database;
- what an attacker can do, and the steps to reproduce it.

"Leave out passwords, secrets and personal data."

If you used an AI tool to find or describe the problem, verify it first: "Unverified security reports go nowhere
public; verify them and send them as `SECURITY.md` describes" (`CODE_OF_CONDUCT.md`).

## What happens next

| Step | What the policy says |
|---|---|
| First answer | "You can expect a first answer within one business day" |
| Decision | "You will be told whether the report is accepted or declined" |
| While a fix is prepared | You are "kept informed" |
| The fix | "An accepted issue is fixed on `main` and released with the next 6.0.x version" |

## In public channels

- If someone posts a vulnerability publicly, ask them to move it to security@se7enx.com; do not discuss the details
  ([Security reports stay private](../../advocates/security-reports-stay-private.md)).
- Do not speculate about unreleased fixes.
- Once a release is out, point people to the upgrade, not to the details of the attack.
- For hardening advice, link [chapter 13 of the installation book](../../../install/13-security-hardening.md).

## Related pages

- [How the community works](README.md)
- [Security responsibilities (partners)](../../partners/security-responsibilities.md)
- [Private security reports (customers)](../../customers/healthy/private-security-reports.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
