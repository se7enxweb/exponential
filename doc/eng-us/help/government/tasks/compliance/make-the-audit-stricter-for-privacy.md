---
title: Make the audit stricter for privacy
audience: government, admin
category: public-sector
order: 210
summary: Step by step - apply the audit guide's strict-privacy profile (hashed logins and addresses, no user agents, keys-only before and after values, 30 days to pseudonymization, shorter retention) where your data protection officer asks for less personal data in the audit.
keywords: audit privacy, strict privacy, AuditPrivacySettings, Field, hash, off, truncate, BeforeAfter keys, PseudonymiseAfterDays, LiveDays, ArchiveDays, KeepDays, data minimization, public sector
type: help
---
# Make the audit stricter for privacy

For administrators, with the data protection officer's decision. The shipped audit already minimizes (addresses
truncated, sessions hashed, no content values). The guide's strict-privacy profile keeps even less
([Privacy in the audit trail](../../privacy/audit-trail-privacy.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Weigh investigations: with hashed logins you can still match a known login, but you cannot read who acted from the
  record alone; with `BeforeAfter=keys` you see which fields changed, not their values.
- The settings apply to new records; existing files are not rewritten (that would break the chain).
- Back up `settings/override/audit.ini.append.php` first.

## Steps

1. In `settings/override/audit.ini.append.php`:

   ```ini
   [AuditPrivacySettings]
   Field[actor.login]=hash
   Field[actor.ip]=hash
   Field[actor.ua]=off
   Field[actor.cli.os_user]=hash
   Field[request.url]=truncate
   Field[request.host]=hash
   Field[object.name]=truncate
   PseudonymiseAfterDays=30

   [AuditRecordSettings]
   BeforeAfter=keys

   [AuditChannel_access]
   LiveDays=30
   ArchiveDays=180

   [AuditIndexSettings]
   KeepDays=180
   ```

   Repeat the channel block for `content`, `system` and `commerce` with the retention your DPO chose.
2. Clear the INI cache, reload PHP-FPM, restart Velocity.
3. Preview the pseudonymization of existing index rows: `./console exp:audit pseudonymise --dry-run --allow-root-user`.

## Check that it worked

- **Setup > Audit** (`/audit/settings`) shows the values and their file.
- A new sign-in appears in `./console exp:audit tail --channel=access --lines=3 --allow-root-user` with a hashed login
  (`h:...`) and no user agent.
- The brute-force alerts still work: they group by the hashed address.

## Related pages

- [Audit guide, 6.4 Strict privacy](../../../../bc/6.0/audit.md#64-strict-privacy)
- [Privacy in the audit trail](../../privacy/audit-trail-privacy.md)
- [Audit retention](../../privacy/audit-retention.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
