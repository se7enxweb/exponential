---
title: CSP-safe design
audience: designer, developer
category: theme-design
order: 600
summary: Designing so a strict Content Security Policy can be enforced: what the policy allows by default, the three rules (no inline script, no inline style, no third-party origins), the patterns that replace inline code, the nonce for the rare exception, and how to audit a design.
keywords: content security policy, csp, csp safe, script-src self, unsafe-inline, nonce, csp_nonce, inline script, inline style, report-only, enforce, csp.log, ContentSecurityPolicy
featured: true
type: help
---
# CSP-safe design

This page is for designers whose templates must work under a Content Security Policy (CSP). A strict policy stops an
injected script from running even where a template forgets to escape a value. The administration sends one; public
sites can switch one on. The settings are described in [Content Security Policy](../../../features/6.0/content-security-policy.md).

## Contents

1. [The policy in brief](#the-policy-in-brief)
2. [The three rules for designs](#the-three-rules-for-designs)
3. [Replacement patterns](#replacement-patterns)
4. [The nonce: the rare exception](#the-nonce-the-rare-exception)
5. [Auditing a design](#auditing-a-design)

## The policy in brief

`site.ini [ContentSecurityPolicy]` with `Mode=auto` (default) sends a report-only policy on administration siteaccesses
and none on public sites; `report-only` and `enforce` can be set per siteaccess. The default directives allow only the
site's own origin:

```ini
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[connect-src]='self'
```

So: scripts and styles only from files on the same host; no inline `<script>`, no `onclick=""`, no `<style>` blocks or
`style=""` attributes once the policy is enforced.

## The three rules for designs

1. **No inline JavaScript**: no `<script>` with code, no `on*=` attributes, no `javascript:` links
   ([No inline JavaScript](no-inline-javascript.md)).
2. **No inline CSS**: no `<style>` blocks, no `style=""` attributes ([No inline CSS](no-inline-css.md)).
3. **No third-party origins you have not planned**: fonts, scripts, styles, frames from other hosts each need a
   directive change ([External resources and CDNs](external-resources-and-cdns.md)).

## Replacement patterns

| Instead of | Use |
|---|---|
| `<script>var cfg = {...}</script>` | `<script type="application/json" id="cfg">{json_encode(...)}</script>` read by your script file |
| `onclick="doThing()"` | `data-action="thing"` and `addEventListener` in a script file |
| `<a href="javascript:...">` | a `<button type="button">` with a listener |
| `style="width: 40%"` | a class (`.pct-40`) or a `data-` value a script turns into a custom property |
| `<style>` with per-page rules | a stylesheet required by the view template (`ezcss_require`) |
| a head script that must run before paint | a small file loaded with `ezscript` in the head ([The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md)) |

## The nonce: the rare exception

With `[ContentSecurityPolicy] Nonce=enabled` (default) the policy carries a fresh nonce per request for the
directives in `NonceDirectives[]` (`script-src`). The template operator `csp_nonce()` prints it:

```tpl
<script nonce="{csp_nonce()}">/* code a template truly cannot move into a file */</script>
```

Never print `csp_nonce()` inside a `{cache-block}` or a view-cached template: the cached copy would carry another
request's nonce and the script would be blocked (comments in `settings/site.ini` and
`kernel/common/expcontentsecuritypolicyoperator.php`). That is why files are always the better choice.

## Auditing a design

1. Run the site with `Mode=report-only` for a while and read the reports: the browser console shows them, and with
   `ReportEndpoint=enabled` the kernel writes them to `var/log/csp.log`, listed in Setup > Log files.
2. For administration designs and extensions, the design tests find inline code in templates:

   ```bash
   php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
   php vendor/bin/phpunit tests/tests/design/ExtensionInlineScriptsTest.php
   ```

3. Search your own design:

   ```bash
   grep -rn -E "<script>|<script [^>]*>[^<]|on(click|load|change|submit)=|javascript:|<style|style=\"" extension/mytheme/design
   ```

4. When nothing is reported for the pages you use, switch the siteaccess to `Mode=enforce`.

## Related pages

- [No inline CSS](no-inline-css.md)
- [No inline JavaScript](no-inline-javascript.md)
- [Checklist: CSP](checklist-csp.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
