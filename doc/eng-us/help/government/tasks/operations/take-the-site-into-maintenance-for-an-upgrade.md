---
title: Take the site into maintenance for an upgrade
audience: government, admin, hosting
category: public-sector
order: 20
summary: Step by step - switch a public website into maintenance mode for a planned upgrade with a clear message for citizens, an expected end, access for testers and the administration, and check that visitors get the 503 page.
keywords: maintenance mode, planned upgrade, take offline, 503, Setup Maintenance, maintenance.php, allow-ip, allow-admin, Retry-After, maintenance window
type: help
---
# Take the site into maintenance for an upgrade

This task is for administrators opening a planned maintenance window. At the end, visitors get a maintenance page
with your message and a `503` answer, while you and your testers still see the site. Background:
[Maintenance windows](../../operations/upgrades-and-change/maintenance-windows.md); reference:
[Maintenance mode](../../../../guides/maintenance-mode.md).

## Contents
1. [Before you start](#before-you-start)
2. [In the administration](#in-the-administration)
3. [On the shell](#on-the-shell)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- The change is approved and announced; a backup plan is ready ([Take a backup before a change](take-a-backup-before-a-change.md)).
- `setup/administrate` for the page, or shell access to the installation directory.
- Shell access in any case, so you can end maintenance if a browser cannot.
- Decide which cronjobs must not run ([Pause cronjobs during maintenance](pause-cronjobs-during-maintenance.md)).

## In the administration

1. Open **Setup > Maintenance** at the `/admin` address. The banner says **The site is online**.
2. **Message for visitors**: for example "We are upgrading this service. Back at 06:00 UTC. For urgent matters call
   <your service number>."
3. **Expected duration**: choose 15 min, 30 min, 1 h, 2 h or 4 h (sent as `Retry-After`).
4. **Let my own address still see the site**: tick it to check your work.
5. **Other addresses that still see the site**: your testers' addresses, one per line.
6. Look at the **Preview**.
7. Tick the confirmation and press **Take the site offline**.

## On the shell

```bash
php bin/php/maintenance.php on --message="We are upgrading this service. Back at 06:00 UTC." --until=2h --allow-admin --allow-ip=<your address> --allow-root-user
php bin/php/maintenance.php status --allow-root-user
```

`./console exp:maintenance on|off|status` does the same. Switching on also empties the caches in front of
`index.php`; under Velocity its response cache pauses during maintenance.

## Check it worked

```bash
curl -sI https://www.example.gov/ | head -1      # HTTP/1.1 503 Service Unavailable
```

From another network (a phone without Wi-Fi), the maintenance page is shown; from your address, the real site. The
change is recorded as `system.maintenance.change` and listed under **Recent changes** on the page.

## Pitfalls

- An administration that is not under `/admin` (its own host name) is not let through by default; use `--allow-ip`
  for your address or keep a shell open.
- The REST API answers 503 too; allow API clients you depend on.
- The expected end is not a timer: maintenance stays on until someone switches it off.

## Related pages
- [Bring the site back online](bring-the-site-back-online.md)
- [Maintenance mode and planned downtime](../../operations/availability/maintenance-mode-and-planned-downtime.md)
- [Maintenance mode feature](../../../../features/6.0/maintenance-mode.md)
- [Maintenance mode (hosting)](../../../hosting/operations/maintenance-mode.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
