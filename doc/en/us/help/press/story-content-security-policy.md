---
title: "Story: the Content Security Policy"
audience: press
category: press-media
order: 60
summary: A feature story about the Content Security Policy of Exponential - report-only on the administration by default, off on public sites, an administration rebuilt without inline scripts so a strict policy can be enforced, and a report log - with printable facts, sources and the claims to avoid.
keywords: story, Content Security Policy, CSP, report-only, enforce, inline scripts, XSS, administration, security headers, press
type: help
---
# Story: the Content Security Policy

This page is for technology and security journalists. A Content Security Policy is a browser rule that limits which
scripts and styles a page may run. In October 2026 Exponential's administration was rebuilt so that a strict policy
can be switched on. This page explains what that means and how to describe it accurately.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Explaining it to readers](#explaining-it-to-readers)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"A strict policy (`script-src 'self'`, no `'unsafe-inline'`) means that a script injected into a page through a stored
or reflected value does not run, even where a template forgets to escape it"
([Content Security Policy](../../features/6.0/content-security-policy.md)). To make such a policy possible, the
administration designs admin4 and admin4l were changed to carry "no inline `<script>`, no `on...=` attribute, no
`javascript:` link, no `style=""` attribute and no `<style>` element"
([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#16-october-2026-the-administration-runs-without-inline-scripts-handlers-and-styles)).
The policy ships in report-only mode on the administration and off on public sites; the administrator decides when to
enforce it, and rolling back is one setting.

## What is new, and when

All in the 6.0.15 development line, which is not released.

| What | When | Source |
|---|---|---|
| The policy setting, report-only on the administration by default | 16 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#16-october-2026-a-content-security-policy-report-only-on-the-administration) |
| The administration without inline scripts, handlers and styles | 16 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#16-october-2026-the-administration-runs-without-inline-scripts-handlers-and-styles) |
| What enforcing needs: extra sources, a nonce per request, a stricter companion policy | 17 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-enforcing-the-content-security-policy) |
| Reports in a log of their own, with rate limits | 17 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-the-reports-of-the-content-security-policy-in-a-log-of-their-own) |
| Extensions without inline scripts in their templates | 17 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-extensions-without-inline-scripts-in-their-templates) |

## Facts you can print

| Fact | Source |
|---|---|
| By default the policy is "report-only on the administration and off on the sites". | [Content Security Policy](../../features/6.0/content-security-policy.md) |
| The shipped policy allows scripts and styles from the site itself only (`'self'`) and blocks plugins (`object-src 'none'`). | [Content Security Policy, Settings](../../features/6.0/content-security-policy.md#settings) |
| In report-only mode the browser reports what it would block and blocks nothing. | same |
| Rolling back from enforce is one setting: `Mode=report-only`. | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-enforcing-the-content-security-policy) |
| The online editor falls back from TinyMCE 3 to TinyMCE 8 where a strict policy is enforced. | same |
| New administration templates may carry no inline styles or scripts; contributors must follow the rule. | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| Reports go to `var/log/csp.log`, rate limited per client and in total. | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#17-october-2026-the-reports-of-the-content-security-policy-in-a-log-of-their-own) |

## What not to claim

- "Exponential blocks cross-site scripting." By default the administration only reports; enforcement is the
  administrator's decision, and public sites have no policy unless one is set.
- "Every extension is ready for an enforced policy." The changelog keeps a shrinking allowlist of extensions that
  still have inline code.
- "The CSP protects you" without the mode. Always say report-only or enforced.

## Explaining it to readers

```text
A Content Security Policy is a list the website sends to the browser: "only run scripts that come from my own
server". If an attacker manages to slip a script into a page, the browser refuses to run it. Exponential's
administration was rewritten so that this list can be strict. It starts in "report" mode, where the browser
only tells the site what it would have blocked.
```

## Angles and headline ideas

```text
"Rewriting an administration so the browser can say no"
"Report first, enforce later: how Exponential rolls out a strict browser policy"
"No inline scripts: the quiet security rule behind Exponential's new admin"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Why report-only first, plans for enforcing | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| A bypass or vulnerability | privately | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Related pages

- [Story: security](story-security.md)
- [Content Security Policy (feature page)](../../features/6.0/content-security-policy.md)
- [Talking points: security](../advocates/talking-points-security.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
