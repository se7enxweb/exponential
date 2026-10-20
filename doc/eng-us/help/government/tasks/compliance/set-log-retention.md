---
title: Set log retention
audience: government, admin, hosting
category: public-sector
order: 330
summary: Step by step - give Exponential's kernel logs an age limit with MaxAgeDays per log (404, security and CSP have one already), schedule pruning with the logs cronjob part, and check with a dry run.
keywords: log retention, MaxAgeDays, LogFileMaxAge, logviewer.ini, RotationSettings, exp:logs prune, cronjob logs, error.log, auth.log, storage limitation, public sector
type: help
---
# Set log retention

For administrators. Apart from `404.log` (30 days), `security.log` (90) and `csp.log` (30), kernel logs are rotated by size
only and have no age limit by default ([Log retention](../../privacy/log-retention.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The periods decided with your DPO and security officer (long enough for investigations).
- The log keys: see **Setup > Log files** (the key is in each log's permalink, `setup/logs/view/<key>`), or
  `./console exp:logs --archives --allow-root-user`.

## Steps

1. In `settings/override/logviewer.ini.append.php`:

   ```ini
   [RotationSettings]
   MaxAgeDays[error]=90
   MaxAgeDays[warning]=30
   MaxAgeDays[notice]=30
   MaxAgeDays[auth]=90
   Compress[error]=enabled
   ```

   or one default for every log without its own entry in `settings/override/site.ini.append.php`:
   `[FileSettings] LogFileMaxAge=90`.
2. Schedule pruning: add the cronjob part `logs` to the site user's crontab, for example
   `15 3 * * * cd /path/to/exponential && php runcronjobs.php -q logs`.
3. Clear the INI cache.

## Check that it worked

```bash
./console exp:logs --archives --allow-root-user          # shows the limits per log
./console exp:logs --prune --dry-run --allow-root-user   # what would go now
```

After the first night, the audit shows `system.log.delete` for pruned archives.

## Related pages

- [Log retention](../../privacy/log-retention.md)
- [Log rotation and pruning](../../../../features/6.0/log-rotation.md)
- [Retention of personal data: an overview](../../privacy/retention-overview.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
