---
title: Maintenance windows
audience: government, admin, project-manager
category: public-sector
order: 120
summary: Planning and running a maintenance window for a public website - announcing it, taking the site offline with a clear message and a 503 answer, letting testers in, pausing cronjobs, and bringing the site back with checks.
keywords: maintenance window, planned downtime, maintenance mode, 503, Retry-After, announcement, public notice, testers, allow-ip, cronjobs, change window, service continuity
type: help
---
# Maintenance windows

This page is for service owners and administrators who plan downtime of a public website: an upgrade, a database
migration, a large import. Exponential's maintenance mode answers every visitor with a 503 page and a `Retry-After`
header while you work. The tool is described in [Maintenance mode](../../../../guides/maintenance-mode.md); this page
covers planning and communication.

![Setup, Maintenance: plan a maintenance window and preview the page visitors will see](../../../images/government/gov-maintenance.webp)

## Contents
1. [Before the window](#before-the-window)
2. [Opening the window](#opening-the-window)
3. [During the window](#during-the-window)
4. [Closing the window](#closing-the-window)
5. [Communicating with the public](#communicating-with-the-public)
6. [Related pages](#related-pages)

## Before the window

- **Approval**: the window is part of the approved change ([Change management and advisory boards](change-management-and-advisory-boards.md)).
- **Timing**: choose a time of low use; your web statistics or the access log tell you when. Avoid times when the
  public depends on the site (deadlines, elections, emergencies).
- **Announcement**: a notice on the site and to editors a few days before (see below).
- **Backup** taken and verified ([What to back up](../backups-and-recovery/what-to-back-up.md)).
- **Shell access** checked, so you can always end maintenance even if a browser cannot.
- **Cronjobs**: decide which must not run during the window ([Pause cronjobs during maintenance](../../tasks/operations/pause-cronjobs-during-maintenance.md)).

## Opening the window

In the administration, **Setup > Maintenance** (`/setup/maintenance`): the message for visitors, the expected
duration (15 min to 4 h with one click), who still gets in (the administration under `/admin` always; your own address
with one tick; other addresses), the confirmation, **Take the site offline**. A preview shows the page visitors get.

Or on the shell:

```bash
php bin/php/maintenance.php on --message="We are upgrading the site. Back at 06:00 UTC." --until=2h --allow-admin --allow-root-user
php bin/php/maintenance.php status --allow-root-user
curl -sI https://www.example.gov/ | head -1      # HTTP/1.1 503 Service Unavailable
```

Visitors get a 503 with `Retry-After`, `Cache-Control: no-store` and `noindex`, so search engines keep the real page.
Switching on empties the caches in front of `index.php`; under Velocity the response caches pause during maintenance
([Maintenance mode feature](../../../../features/6.0/maintenance-mode.md)).

## During the window

- Testers whose addresses you listed see the real site; everyone else sees the maintenance page.
- The expected end is not a timer: maintenance never ends by itself. The page warns once the end has passed.
- The REST API is a front controller too and answers 503; allow the addresses of API clients you depend on, or keep
  the window short.

## Closing the window

1. Check the site from an allowed address.
2. **Bring the site back online** on **Setup > Maintenance**, or `php bin/php/maintenance.php off --allow-root-user`.
3. Clear the caches in front of the site if content or code changed; under Velocity:
   `./console exp:velocity cache clear --allow-root-user`.
4. `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/` answers 200 through every server.
5. Run the cronjob parts you paused (**Setup > Cronjobs**).

Every switch is recorded as `system.maintenance.change`; the page's **Recent changes** lists the latest eight. Add
them to the change record.

## Communicating with the public

| When | What | Where |
|---|---|---|
| Some days before | date, time, expected duration, alternatives (phone, counter) | a notice on the site; social channels your organization uses |
| During | the maintenance page message: when the site is back, in plain words | `--message` or the page's message field |
| After | nothing, unless the window ran long or something changed for users | a notice |

Keep the maintenance page's own design accessible and in the site's languages; the page is the theme's
`errors/maintenance.html` when an active extension has one, otherwise `share/maintenance.html`.

## Related pages
- [Maintenance mode and planned downtime](../availability/maintenance-mode-and-planned-downtime.md)
- [Take the site into maintenance for an upgrade](../../tasks/operations/take-the-site-into-maintenance-for-an-upgrade.md)
- [Bring the site back online](../../tasks/operations/bring-the-site-back-online.md)
- [Maintenance mode (hosting)](../../../hosting/operations/maintenance-mode.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
