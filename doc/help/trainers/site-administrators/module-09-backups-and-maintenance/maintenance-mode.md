---
title: "Lesson 9.2: Maintenance mode"
audience: trainer, admin
category: training
order: 92
summary: Learners take a training site offline from Setup > Maintenance with a message, an expected duration and their own address allowed, check what visitors get, switch it off, do the same from a shell, and get back in when locked out. 40 minutes.
keywords: site administration, maintenance mode, Setup Maintenance, Take the site offline, Bring the site back online, 503, maintenance.php, exp:maintenance, allow-admin, var/maintenance.json
type: help
---
# Lesson 9.2: Maintenance mode

Before an upgrade, an import or a restore, the public site should stop taking changes and show visitors an honest
"back soon" page. Maintenance mode does that: every page request gets a maintenance page with status 503, while the
administration stays reachable to bring the site back. This lesson covers the page, the command line and the way back
in when something goes wrong.

> **Note**
> Maintenance mode takes the whole public site offline. Use your own training installation only.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 9: Backups and maintenance](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | [Lesson 9.1](what-to-back-up.md); an administrator account and a shell on the training installation |

## Learning objectives

After this lesson, learners can:

- take the public site offline from **Setup > Maintenance** with a message, a duration and allowed addresses;
- check what a visitor gets (status 503 and the maintenance page) with `curl`;
- bring the site back online and read **Recent changes**;
- do the same from a shell, and explain what `--allow-admin` changes;
- get back in by removing `var/maintenance.json` when nothing else works.

## Trainer's notes

### How to present it

Source: [Maintenance mode: taking the site offline and bringing it back](../../../../guides/maintenance-mode.md),
sections 1 to 7 and 15. The page described is the one of the admin4 design.

1. **What it does** (3 minutes). Every page request gets the maintenance page with `503 Service Unavailable`,
   `Retry-After` and `no-store`. Images, styles and scripts are still served. The administration under `/admin`
   stays reachable.
2. **The page** (5 minutes). **Setup**, then **Maintenance**. Show **Plan the maintenance window**: **Message for
   visitors (optional)**, **Expected duration in minutes (optional)** with the buttons **15 min** to **4 h**, **Let
   my own address ... still see the site**, **Other addresses that still see the site (optional)**, the confirmation
   tick and **Take the site offline**. Show **Preview: what visitors will see**.
3. **While offline** (2 minutes). An amber banner says why, by whom, since when, until when and whether **you** see the
   site. Maintenance never ends by itself when the expected time passes: **Bring the site back online** ends it.
4. **The shell** (3 minutes). `php bin/php/maintenance.php on|off|status`, or `php bin/php/console exp:maintenance`.
   Without `--allow-admin` the administration is offline too.
5. **The way back in** (2 minutes). The whole state is one file, `var/maintenance.json`. Deleting it ends maintenance.

### Common questions

**Do cronjobs stop during maintenance?** Not by themselves. Stop the crontab for the window or pause the parts
(Module 6), and run them afterwards.

**I switched it on but I still see the site.** Your address is allowed, or a CDN still holds the page. Look at **You**
in the banner and check with `curl -sI` from another address.

**Is every switch recorded?** Yes: as `system.maintenance.change` in the audit; the page lists the latest eight.

### Pitfalls

- **An administration on its own host.** If the administration is not under `/admin`, switching on from the page can
  lock it out. Know the shell command before you start.
- **Old pages after the window.** A cache in front of PHP kept pages from before. Clear Velocity's response cache and
  purge the CDN.
- **Restoring `var/` taken during maintenance.** The restored site comes back in maintenance; switch it off.

## Hands-on exercise

Use your own training installation, served for example on `http://127.0.0.1:8087`; replace the address with yours.

1. As administrator, open **Setup > Maintenance**. Read **The site is online** and **How it works**.
2. Type a **Message for visitors (optional)**: `Training: back in 15 minutes`. Press **15 min**.
3. Leave **Let my own address ... still see the site** unticked. Read **Preview: what visitors will see**.
4. Tick the confirmation and press **Take the site offline**. Read the banner.
5. In a terminal, check what a visitor gets:

   ```bash
   curl -sI http://127.0.0.1:8087/ | head -5
   ```

6. Open the administration's dashboard in another tab: it still works.
7. Press **Bring the site back online**. Run the `curl` command again.
8. Now from the shell, keeping the administration reachable:

   ```bash
   php bin/php/maintenance.php on --message="Training from the shell" --until=30m --allow-admin
   php bin/php/maintenance.php status
   ```

9. Reload **Setup > Maintenance**. The banner shows the shell's switch; **Recent changes** lists both windows.
10. Practise the way back in: `ls -l var/maintenance.json`, then end maintenance with
    `php bin/php/maintenance.php off`. Your trainer shows the last resort, deleting the file, on the projector.

## Expected result

- Step 5 shows `HTTP/1.1 503 Service Unavailable` (or HTTP/2 503) and a `Retry-After` header.
- Step 6: the administration answers normally.
- After step 7 the `curl` shows `200` again.
- Step 8 `status` reports maintenance as on, with the message and the expected end.
- After step 10 `var/maintenance.json` no longer exists and the site answers `200`.
- The trainer checks **Recent changes** on one learner's page: two windows, each switched on and off.

## Quiz

1. What HTTP status does a visitor get during maintenance?
   A. 200 B. 404 C. 500 D. 503
2. True or false: maintenance ends by itself when the expected duration has passed.
3. What does `--allow-admin` change on the command line?
4. You are locked out of the administration and the site shows the maintenance page. What do you do?
5. Why should cronjobs be paused for a maintenance window?

<details>
<summary>Answers</summary>

1. **D.** With `Retry-After` and `no-store`
   ([Maintenance mode, In short](../../../../guides/maintenance-mode.md#in-short)).
2. **False.** It ends only when someone switches it off.
3. It keeps the administration under `/admin` reachable; without it the administration is offline too.
4. In a shell: `php bin/php/maintenance.php off`; if PHP cannot run, delete `var/maintenance.json`
   ([Troubleshooting](../../../../guides/maintenance-mode.md#15-troubleshooting)).
5. They keep running against a site that is being changed and can fail or write half-finished data.

</details>

## Further reading

- [Maintenance mode](../../../../guides/maintenance-mode.md): the full guide, including caches and deploys.
- [Maintenance mode feature reference](../../../../features/6.0/maintenance-mode.md).
- [Maintenance mode for hosts](../../../hosting/operations/maintenance-mode.md).

## Related pages

- [Module 9: Backups and maintenance](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
