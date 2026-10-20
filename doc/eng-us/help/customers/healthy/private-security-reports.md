---
title: Private security reports
audience: customer, admin, partner
category: customer-success
order: 20
summary: What a site owner does when someone on the team, a customer or a researcher finds a possible security problem in Exponential - report it privately to security@se7enx.com, what to include and leave out, what happens next, and how it differs from a break-in on your own site.
keywords: security report, vulnerability, responsible disclosure, SECURITY.md, security@se7enx.com, private, GitHub private vulnerability reporting
type: help
---
# Private security reports

A security problem in the software (a way to see or change what one should not) must reach the people who can fix
it, and must not reach attackers first. This page tells an owner and their team what to do with such a finding. The
rules come from `SECURITY.md` at the root of every installation.

## Two different situations

| Situation | What it is | What to do |
|---|---|---|
| A weakness in Exponential itself | Anyone running the same version could be affected | Report it privately to the project (this page) |
| A break-in or misuse of your site | Something happened on your site: unknown accounts, changed files, pages you did not publish | Call your host now; it is a severity 1 incident ([When the site is down](when-the-site-is-down.md), [Compromised site](../../hosting/runbooks/compromised-site.md)) |

Sometimes the second leads to the first: the investigation finds that the cause is a weakness in the software. Then
both apply.

## Report it privately

"Please report security issues privately by e-mail to security@se7enx.com, not in the public issue tracker or the
discussions" (`SECURITY.md`). The help's error report page also offers GitHub's private vulnerability reporting of
the repository ([When something goes wrong](../../when-something-goes-wrong.md)).

Do **not** post it in a public issue, a forum, a chat, a pull request or a social network.

## What to include

`SECURITY.md` asks for:

- the version (`composer show se7enxweb/exponential`, or the tag or commit you run) and the PHP version;
- the engine that serves the site (Exponential Velocity, FrankenPHP, Apache or nginx with PHP-FPM) and the database;
- what an attacker can do, and the steps to reproduce it.

**Leave out passwords, secrets and personal data** (same file). Reproduce the problem on a test copy if you can,
never against someone else's site.

Usually your administrator or partner writes the report, because they can describe versions and steps precisely.
The owner's job is to make sure it is sent, privately, and to know it was sent.

## What happens next

From `SECURITY.md`:

1. "You can expect a first answer within one business day."
2. "You will be told whether the report is accepted or declined, and kept informed while a fix is prepared."
3. "An accepted issue is fixed on `main` and released with the next 6.0.x version."

That first answer is the only response commitment in the project's own files
([Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md)).

## While you wait for the fix

- Keep the finding to the people who need to know.
- Ask your partner or host whether a temporary measure (a configuration or web server rule) protects your site in the
  meantime; the hardening guides describe many ([Security hardening](../../security-hardening.md)).
- When the release with the fix appears, have it applied quickly ([Upgrades with your provider](upgrades-with-your-provider.md)).
- Partners do not tell the public before a fix is released
  ([Escalations and security issues](../../partners/escalations-and-security-issues.md)); hold your own
  communications to the same rule.

## Extensions from other authors

An extension that is not part of the project has its own author and its own security contact, if any. Check the
extension's repository for a security policy; if it has none, contact its author privately rather than in public.
The **Info > About** page names each extension's website ([About and Copyright pages](../../../features/6.0/about-and-package-pages.md)).

## Personal data

If customers' personal data may have been exposed, there may be legal duties to report it. Ask your data
protection officer or lawyer at once. This page is not legal advice; see
[GDPR basics](../../businesses/security/gdpr-basics.md).

## Related pages

- [Security basics](security-basics.md)
- [Security updates and disclosure (for businesses)](../../businesses/security/security-updates-and-disclosure.md)
- [Reporting problems](reporting-problems.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
