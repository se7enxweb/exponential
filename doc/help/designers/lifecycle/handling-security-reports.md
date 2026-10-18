---
title: "Handling security reports"
audience: designer, developer
category: theme-lifecycle
order: 190
summary: What to do from the moment a security report arrives - acknowledge, reproduce privately, assess, fix, release, credit - and what to do when the report arrives in a public issue.
keywords: security report, vulnerability, disclosure, acknowledge, reproduce, assess, credit, public issue, private
type: help
---
# Handling security reports

This page is for designers who receive a security report about their theme, through the address in `SECURITY.md`
([A security policy for a theme](../distribution/security-policy-for-a-theme.md)) or any other way.

## Step by step

1. **Acknowledge** within the time your policy promises: thank the reporter, say you are looking into it.
2. **Reproduce** on your test installation, never on a live site. Write down the steps.
3. **Assess**: what can an attacker do, who can do it (anonymous visitor or editor), which versions are affected.
4. **Fix** privately: on a local branch, not pushed to a public repository before the release.
5. **Tell the reporter** what you found and when the fix will be released; ask how they want to be credited.
6. **Release** ([Publishing a security release](publishing-a-security-release.md)).
7. **Credit** the reporter in the release notes, as they wish.

## When the report is a public issue

1. Thank the reporter and ask them to continue by the private address.
2. Hide or edit the details if your platform allows, without deleting the report's existence.
3. Fix and release quickly; the problem is already public.

## Never

- Never ask the reporter for credentials or customer data; never post them yourself.
- Never move a published tag to "fix" a version ([A pushed tag is permanent](../distribution/a-pushed-tag-is-permanent.md)).

## Related pages

- [Security fixes in themes](security-fixes-in-themes.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
