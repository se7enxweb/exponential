---
title: "A security policy for a theme"
audience: designer, developer
category: theme-distribution
order: 390
summary: Write a SECURITY.md for a theme - supported versions, a private way to report, what to include, what reporters can expect - modeled on the policy of the Exponential repository.
keywords: SECURITY.md, security policy, vulnerability, private report, disclosure, supported versions, GitHub private vulnerability reporting
type: help
---
# A security policy for a theme

This page is for designers publishing a theme. Themes contain code: templates that print user input, scripts, sometimes
PHP. A vulnerability report needs a private path to you, or it ends up as a public issue that helps attackers first.

## The model: Exponential's own policy

The `SECURITY.md` at the root of the Exponential repository has three parts:

1. **Supported versions**: which line gets fixes, and that a published tag is never changed; a fix arrives as the next
   release.
2. **Reporting**: report privately by email, not in the public issue tracker, with a stated first-response time.
3. **What to include**: the version, the PHP version, the server engine and database, what an attacker can do, the
   steps to reproduce, and no passwords, secrets or personal data.

It ends with what the reporter is told and when.

## Step by step

1. Create `SECURITY.md` at the root of the theme:

   ```markdown
   # Security Policy

   ## Supported versions

   The newest 1.0.x release gets security fixes. A published tag is never changed: a fix arrives as the
   next release.

   ## Reporting a vulnerability

   Please report security issues privately to <your security address>, not in the public issues.
   Include the theme version, the Exponential and PHP versions, what an attacker can do and how to
   reproduce it. Leave out passwords and personal data.

   You will get an answer within <a time you can keep>.
   ```

2. Use an address that more than one person reads, if you can.
3. On GitHub you can also enable private vulnerability reporting for the repository (**Settings > Security**), which
   gives reporters a private form.
4. Link `SECURITY.md` from the README's support section.

## Related pages

- [Handling security reports](../lifecycle/handling-security-reports.md)
- [Publishing a security release](../lifecycle/publishing-a-security-release.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
