---
title: "Runbook: compromised site"
audience: hosting, admin
category: hosting
order: 60
summary: What a host does when an Exponential site may have been compromised: contain without destroying evidence, check the audit trail and its hash chain, the security log and changed files, rotate credentials and sessions, rebuild from known-good code and a clean backup, report, and notify as the law requires.
keywords: runbook, compromised, hacked, breach, incident response, containment, evidence, audit trail, exp:audit verify, security.log, sessions, password reset, credentials, rebuild, Git, restore, disclosure, notification, GDPR
type: help
---
# Runbook: compromised site

For the host's security lead and system administrators when there are signs that an attacker changed a customer's
Exponential site: defaced pages, unknown administrator accounts, files that should not exist, spam sent from the
site, or an alert from the audit trail. This is a severity 1 incident ([Support tiers](../business/support-tiers.md)).

> **Warning**
> A compromise can have legal duties attached: notifying the customer, and in many jurisdictions authorities and the
> people whose data was affected, within fixed times. Involve the customer and your legal adviser early. This page is
> not legal advice.

## Contents

1. [Contain](#contain)
2. [Preserve evidence](#preserve-evidence)
3. [Investigate](#investigate)
4. [Eradicate and rebuild](#eradicate-and-rebuild)
5. [Recover](#recover)
6. [Report and notify](#report-and-notify)
7. [Prevent a repeat](#prevent-a-repeat)

## Contain

1. Take the public site offline with maintenance mode, letting only your own addresses in:

   ```bash
   php bin/php/maintenance.php on --message="Maintenance" --allow-ip=<your address> --allow-root-user
   ```

2. If the attacker may hold an administrator account or session, restrict the administration at the network level
   now ([Restricting the administration](../security/restricting-the-administration.md)).
3. If the machine itself may be compromised (unknown processes, changed system files), isolate it at the network
   level and work from a copy.
4. Do not delete anything yet.

## Preserve evidence

- Copy the current files and a database dump to a place the site's user cannot write.
- Copy the logs: `var/log/` (with `error.log`, `security.log`, `auth.log`, `audit/`), Velocity's `var/vc/qbix/log/`,
  and the web server's and PHP-FPM's logs.
- Note the time of every action you take.

## Investigate

| Question | Where to look |
|---|---|
| Who signed in, changed roles, settings or passwords? | the audit trail: **Audit** in the administration, `./console exp:audit tail` ([Audit trail](../security/audit-trail.md)) |
| Was the audit trail itself tampered with? | `./console exp:audit verify` (or `php bin/php/audit.php verify --allow-root-user`): exit status 0 when every channel's chain is intact, 1 when broken; compare with the copy forwarded off the machine, if a sink was configured |
| Which requests looked like attacks, and did any get a 200? | `var/log/security.log` ([Security log](../operations/security-log.md)) |
| Which files changed? | compare the code with its Git tag: `git status` and `git diff` in a Git checkout; for Composer installs, compare with a fresh `composer install` of the same lock file |
| Were files planted where PHP could write? | `var/`, `settings/`, and `design/` or `extension/` if they were writable; any `.php` under `var/storage` is suspect |
| Did the site send mail? | the mail logs of the server |
| Which version ran? | the About page or `composer show se7enxweb/exponential`; compare with the fixes listed in later release notes |

The principle behind the file check, from [13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership):
"If the PHP process can change only `var/` and `settings/`, and the web server refuses to run anything below the
root, a flaw in an extension cannot plant code."

## Eradicate and rebuild

1. **Code from a known-good source**: a fresh checkout of the version's tag and `composer install`, plus the custom
   extensions from their repositories. Do not trust files from the compromised tree.
2. **Data from a clean backup**: the newest backup from before the attacker's first change, found during the
   investigation ([Restore from backup](restore-from-backup.md)). If no clean backup exists, review the current
   database for unknown accounts, roles and content before keeping it.
3. **Update** Exponential and extensions to the newest 6.0.x before reopening, if the hole may be a known one
   ([Upgrading customer sites](../operations/upgrading-customer-sites.md)).

## Recover

1. **Credentials**: change the administrator passwords; the database password (and update
   `settings/override/site.ini.append.php`); API keys and tokens the site holds; SFTP and panel passwords.
2. **Sessions**: a password change signs out every other session of that user (`[PasswordSettings]
   EndOtherSessions=enabled`); after a suspected compromise also remove all sessions
   (**Setup** > **Sessions**, with the database session handler) ([Security hardening](../../security-hardening.md#session-hijacking)).
3. **Accounts**: remove accounts and role assignments the attacker created.
4. **Caches**: clear everything, so no cached page from the compromised state is served:
   `php bin/php/ezcache.php --clear-all --allow-root-user`, and with Velocity restart it and clear its response cache.
5. Verify, then switch maintenance mode off.

## Report and notify

- **To the customer**: what happened, what data may be affected, what you did.
- **To the project**, if a vulnerability in Exponential or one of its extensions was the cause: privately, as
  `SECURITY.md` describes, never in a public issue ([Security updates and disclosure](../security/security-updates-and-disclosure.md)).
- **To authorities and affected people**, where the law requires it: the customer is usually the controller of the
  personal data and decides with their legal adviser; you support them with facts and logs
  ([Compliance and GDPR](../security/compliance-and-gdpr.md)).

## Prevent a repeat

- Go through the [Security review checklist](../checklists/security-review.md).
- Make the code read-only for the site's user (13.11).
- Forward the audit trail off the machine with a sink (`[AuditSink_syslog]`, `[AuditSink_webhook]`), "so a compromised
  machine cannot quietly rewrite its own history" ([Security hardening](../../security-hardening.md#watching-logs-the-request-classifier-and-the-audit-trail)).
- Consider request-shield in front ([request-shield and request rules](../security/request-shield-and-request-rules.md)).

## Related pages

- [Hardening for hosts](../security/hardening-for-hosts.md)
- [Restore from backup](restore-from-backup.md)
- [Chapter 13: Security hardening](../../../install/13-security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
