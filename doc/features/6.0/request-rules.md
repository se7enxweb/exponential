# Request rules: decide what happens to a request before its view runs

A URL alias is `content/view/full/<node>` once the kernel has translated it, so neither `[SiteAccessRules]` nor
`content/read` limitations could tell `/content/view/full/123` from the same page at its alias. Request rules
close that gap, and much more: they run after the URL alias is translated and after the role policies allowed the
view, and can allow, deny, redirect, rewrite or log. Added 2026-10-02.

Complete guide with every condition, action, fact and sixteen recipes:
[doc/bc/6.0/view_full_security.md](../../bc/6.0/view_full_security.md).

## Quick start: send system URLs to their alias

In `settings/siteaccess/<site>/requestrules.ini.append.php` (or `settings/override/`):

```ini
<?php /*
[RequestRuleSettings]
RuleList[]=system_url_full_view_to_alias
*/ ?>
```

A visitor who types `/content/view/full/123` is redirected (301) to the node's alias, unless they hold the new
policy `content/view_system_url`; without an alias the answer is "not found". Check what the rules decide for any
address, as any user, without changing anything:

```bash
php bin/php/ezrequestrules.php -s site --check
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
```

## Settings (settings/requestrules.ini)

| Block | Key | Default | Meaning |
|---|---|---|---|
| `RequestRuleSettings` | `Enabled` | `true` | with `false` no rule is asked; the kernel behaves as before |
| `RequestRuleSettings` | `RuleList[]` | empty | rules asked in this order; the first whose conditions all match decides |
| `RequestRuleSettings` | `RuleProviders[]` | empty | classes implementing `ezpRequestRuleProvider` (rules computed in PHP) |
| `RequestRuleSettings` | `FactProviders[]` | empty | classes implementing `ezpRequestFactProvider`, for `Conditions[fact:<name>]` |
| `RequestRuleSettings` | `MaxRewrites` | `3` | rewrites per request before "not found" (stops loops) |
| `RequestRuleSettings` | `ConditionHandlers[]`, `ActionHandlers[]` | empty | extension conditions and actions by name |
| `Rule-<name>` | `Conditions[...]`, `Action`, `ActionArgs[...]`, `FallbackAction`, `Description` | per rule | one rule |

Scope: override or siteaccess (a siteaccess override that starts with an empty `RuleList[]` replaces the list).

## What a rule can say

- **25 conditions**: `requested_via` (system, alias, wildcard, front page), `uri`, `module_view`, `view_mode`,
  `node`, `subtree`, `class`, `section`, `state`, `user`, `role`, `policy`, `siteaccess`, `host`, `header`, `ip`,
  `fact`, and more.
- **8 actions**: `allow`, `notfound`, `forbidden`, `login`, `redirect`, `redirect_to_alias`, `rewrite`, `log`.
- Three rules ship, all off: `system_url_full_view_to_alias`, `system_url_content_view_notfound` (every system
  content view not found) and `system_url_full_view_audit` (writes a line to `var/log/requestrules.log` for each
  system URL typed by a user without `content/view_system_url`, changes nothing; put it first to see who would be
  affected before enforcing a stricter rule).

## Safety

- Rules only take access away or send the visitor elsewhere; they never give access a policy refused.
- Any answer except `allow` is sent private, no-store. A page that a rule with a request-dependent condition
  (header, ip, user) could decide stays out of the role-aware HTTP cache and the Velocity response cache; pages
  the rules cannot affect are cached as before.
- The `login` action never acts on the sign-in views, so no rule can loop.
- Rules read facts only, resolved on first use, so they are tested without a database (21 unit tests).

Related: [hardening](../../bc/6.0/hardening.md), [the audit trail](audit-trail.md),
[October 2026 chronicle](../../history/2026/2026-10.md).
