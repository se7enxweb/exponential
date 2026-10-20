---
title: "A CSP audit for every release"
audience: designer, developer
category: theme-lifecycle
order: 150
summary: Check before each release that the theme still works under a strict Content Security Policy - report-only mode on a test siteaccess, the browser console, a grep for inline code, and external resources.
keywords: CSP audit, Content Security Policy, report-only, unsafe-inline, console, inline scripts, external resources, release routine
type: help
---
# A CSP audit for every release

This page is for designers maintaining a theme that runs, or should run, under a Content Security Policy. Once a theme
is free of inline code ([Removing inline scripts and styles](removing-inline-scripts-and-styles.md)), each release must
keep it that way.

## The routine

1. **Policy on, report-only**, for the test siteaccess (`site.ini [ContentSecurityPolicy] Mode=report-only` in that
   siteaccess's settings; see [Content Security Policy](../../../features/6.0/content-security-policy.md)).
2. **Walk the page list** with the browser console open. Expected: no CSP reports. Use `ReportURI` to collect reports
   from a longer session.
3. **Grep the diff** of the release for inline code:

   ```bash
   git diff "$(git describe --tags --abbrev=0)"..HEAD -- design | grep -n -E "^\+.*(<script[^>]*>[^<]|on[a-z]+=|javascript:|style=\")"
   ```

   Expected: no output.
4. **External resources**: anything not served from the site itself (fonts, scripts, images) needs a policy entry
   and a reason; prefer self-hosting ([External resources and CDNs](../theme-design/external-resources-and-cdns.md)).
5. **Interactive parts** (menus, galleries, forms, maps) work with the policy enforced on the test siteaccess.

## Record

Write the result in the release notes ("Works under a Content Security Policy without 'unsafe-inline'"), only when
it is true.

## Related pages

- [Checklist: CSP](../theme-design/checklist-csp.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
