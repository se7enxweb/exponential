---
title: Escalations and security issues
audience: partner
category: partnership
order: 340
summary: What partners do when a ticket is not moving or when a security problem is found - an escalation ladder, private disclosure to security@se7enx.com, what to tell customers, and how security fixes reach you.
keywords: escalation, security, vulnerability, disclosure, private, security@se7enx.com, incident, customers, fixes, SECURITY.md
type: help
---
# Escalations and security issues

This page is for partnership owners and technical leads. It covers two situations: a support issue that needs more
attention, and a security vulnerability.

## Escalating a support issue

The program does not publish an escalation procedure. A reasonable ladder, built from the published channels:

| Step | When | How |
|---|---|---|
| 1 | The response time of your level has passed | Comment on the ticket with the time it was opened and your level |
| 2 | Production impact continues | Write to the partner team (<partners@exponential.earth>) with the ticket link |
| 3 | Bring it to the next guidance meeting, or request one early | [Partner meetings](partner-meetings-and-briefings.md) |
| 4 | You need guaranteed help now | Buy paid support from 7x (<https://exponential.earth/support>) |

Keep escalations factual and polite: what is broken, since when, what it costs, what you need.

## Security vulnerabilities

The rules are in [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

- **Report privately** to **security@se7enx.com**. Never in a public issue, forum or chat, and never in a partner
  ticket in a repository others can read.
- "You can expect a first answer within one business day" (SECURITY.md).
- "Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch ... and published with the
  next 6.0.x release" (SECURITY.md). 5.x, 4.x, 3.x and older are not supported.
- exponential.earth also asks for responsible reports ("please report it responsibly to security @ exponential
  [dot] earth", <https://exponential.earth/about/project>).

### What to send

```text
Subject: Security: <component> <one-line summary>

Affected version(s): ...
Component / file / URL pattern: ...
Impact: (data exposure / privilege escalation / XSS / ...)
Steps to reproduce (on a test installation): ...
Proof of concept: ...  (no customer data)
Your contact and preferred disclosure timeline: ...
```

### Customers and disclosure

- Do not tell the public before a fix is released.
- Protect affected customers with temporary measures you can apply yourself (configuration, web server rules) from
  [Security hardening](../security-hardening.md).
- After the release, update every affected customer site and say what was done, without details that help attackers
  on unpatched sites.

## Related pages

- [Security responsibilities of partners](security-responsibilities.md)
- [Support levels and response times](support-levels-and-response-times.md)
- [Security hardening](../security-hardening.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
