---
title: "Objection: \"Is open source secure?\""
audience: advocate
category: advocacy
order: 35
summary: How to answer "open source is less secure" or "anyone can read the code and attack it" with Exponential's documented security process, the evidence a skeptic can check, and the limits an honest advocate states.
keywords: objection, open source security, vulnerabilities, public code, attackers, security process, SECURITY.md, disclosure, hardening, audit trail, CSP, supply chain
type: help
---
# Objection: "Is open source secure?"

This page is for advocates who hear "open source is less secure", "anyone can read the code and find holes" or "who
fixes it when something is found?" Do not debate open source in general. Show the process Exponential documents and how
to check it.

## Contents

1. [Reframe the question](#reframe-the-question)
2. [The process, step by step](#the-process-step-by-step)
3. [What a site owner can do](#what-a-site-owner-can-do)
4. [Reply templates](#reply-templates)
5. [Do not say](#do-not-say)

## Reframe the question

"Is it secure?" cannot be answered with yes or no for any software. Turn it into questions that have answers:

| Instead of | Ask |
|---|---|
| "Is it secure?" | "How are vulnerabilities reported and fixed?" |
| "Can attackers read the code?" | "Can defenders read it too, and see every change?" |
| "Who is responsible?" | "Who answers a report, and how fast?" |
| "Is our site safe?" | "What must our administrators do?" |

## The process, step by step

| Step | What happens | Source |
|---|---|---|
| 1. Report | Privately by e-mail, or GitHub's private vulnerability reporting; never public | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [When something goes wrong](../when-something-goes-wrong.md#security-problems) |
| 2. First answer | "within one business day" | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| 3. Decision | The reporter is told whether it is accepted, and kept informed | same |
| 4. Fix | On `main`, released with the next 6.0.x version | [Security fixes](../who-is-the-exponential-foundation.md#security-fixes) |
| 5. Release | A new version; published versions never change | [Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers) |
| 6. Upstream | Security fixes go to dependencies too (Zeta Components) | [The PHP stack](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |

Open code helps here: anyone can read every change in the public history, including security fixes, and verify what a
release contains ([Principles you can see in the code](../who-is-7x.md#principles-you-can-see-in-the-code)).

## What a site owner can do

Most incidents in any CMS come from how a site is run. The documentation gives administrators:

- a threat-by-threat [hardening guide](../security-hardening.md) with a [checklist](../security-hardening.md#checklist);
- the [Content Security Policy](../../features/6.0/content-security-policy.md);
- the [audit trail](../../features/6.0/audit-trail.md) and [request logs](../../features/6.0/request-logs.md);
- [two-step sign-in](../../guides/two-factor-login.md);
- generated administrator passwords on new installations ([The default administrator password](../default-administrator-password.md)).

## Reply templates

```text
SHORT
Security is a process, not a claim. Exponential's is written down: private reports, a first answer within
one business day, fixes in the next 6.0.x release (SECURITY.md). Admins get a hardening guide and checklist.

LONGER
Open code cuts both ways: attackers can read it, but so can defenders, and every fix is visible in the
public history. What matters is the process. For Exponential: reports go privately to the address in
SECURITY.md with a first answer within one business day; accepted issues are fixed on main and shipped in
the next 6.0.x release; published versions never change. For your own site, the hardening guide and its
checklist are the place to start. (links)
```

## Do not say

- "Open source is always more secure." That is a debate, not a fact.
- "It has never been hacked." No source says so.
- "Security is guaranteed." Nothing is.
- Details of any vulnerability that is not public. If someone tells you one, follow
  [Security reports stay private](security-reports-stay-private.md).

## Related pages

- [Talking points: security](talking-points-security.md)
- [Advocating for security](advocating-for-security.md)
- [Handling objections, Security](../business/objection-handling.md#security)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
