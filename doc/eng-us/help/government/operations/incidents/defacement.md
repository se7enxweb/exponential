---
title: Defacement
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Runbook for a public website showing content nobody authorized - contain with maintenance mode, find out whether it came through an account (audit trail) or through the files (file consistency check), restore, and communicate.
keywords: defacement, hacked site, unauthorized content, vandalism, compromise, maintenance mode, audit trail, checkmanifest, file integrity, restore, version history, incident
type: help
---
# Defacement

This runbook is for administrators and incident leads when a public website shows text or images nobody in the
organization published. Defacement of a government site damages trust quickly; contain first, investigate second.
Start with [Incident response basics](incident-response-basics.md); the deeper procedure is
[Compromised site (hosting runbook)](../../../hosting/runbooks/compromised-site.md).

## Contents
1. [Contain](#contain)
2. [Through an account or through the files?](#through-an-account-or-through-the-files)
3. [Content defacement: repair](#content-defacement-repair)
4. [File defacement: rebuild](#file-defacement-rebuild)
5. [Communicate and close](#communicate-and-close)
6. [Related pages](#related-pages)

## Contain

```bash
php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-ip=<your address> --allow-root-user
```

If the defacement may have come through an administrator account, restrict the administration at the network level
too ([Restricting the administration](../../../hosting/security/restricting-the-administration.md)). Preserve
evidence before changing anything (files, database dump, `var/log/` with `audit/`).

## Through an account or through the files?

| Check | Command or page | Points to |
|---|---|---|
| Who published or edited the defaced items? | the item's versions page (`/content/history/<object id>`): creator of each version; **Audit** tab, Console, channel `content` | an account |
| Who signed in recently, from where? | `./console exp:audit tail --channel=access --lines=50 --allow-root-user` | an account |
| Were roles changed? | the `admin_role_granted` alert; audit `access` channel | an account with escalated rights |
| Did kernel files change? | `php bin/php/checkmanifest.php --all --extensions` | the files |
| Are there unexpected PHP files where PHP can write? | `find var/storage -name '*.php'` | the files |
| Is the audit trail intact? | `./console exp:audit verify --allow-root-user` | tampering |

## Content defacement: repair

1. Disable the account used ([Suspected account compromise](suspected-account-compromise.md)).
2. For each defaced object, open its versions page and **Make a new draft** from the last good version, then publish
   it (it passes the approval workflow like any version) ([Version history and accountability](../editorial-workflows/version-history-and-accountability.md)).
3. Remove items the attacker created; check the trash for items they removed and restore them.
4. Many items affected: consider restoring the database from the last clean backup instead, accepting the loss of
   legitimate edits since then ([Rollback planning](../upgrades-and-change/rollback-planning.md)).

## File defacement: rebuild

Do not trust files from the compromised tree: deploy the code of the version's tag from a known-good source, restore
`var/storage` and settings from a clean backup, update to the newest 6.0.x if the hole may be known, and change every
credential the site holds. Follow the hosting runbook's "Eradicate and rebuild" and "Recover" sections.

## Communicate and close

1. Clear every cache so no defaced page is served: `php bin/php/ezcache.php --clear-all --allow-root-user`, then
   `./console exp:velocity cache clear --allow-root-user`; purge a CDN if you use one.
2. Bring the site back online and check from outside.
3. Public statement as agreed with communications; if personal data may be involved, see
   [Data breach notification framing](data-breach-notification-framing.md).
4. Report to your security team or national authority as your rules require; hold a
   [post-incident review](post-incident-review.md).

## Related pages
- [Upgrade check as a monitor](../monitoring/upgrade-check-as-a-monitor.md)
- [Audit trail verification](../monitoring/audit-trail-verification.md)
- [Mass content error](mass-content-error.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
