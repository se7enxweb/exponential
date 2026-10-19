---
title: "Lesson 5.1: Monitoring and health checks"
audience: trainer, hosting, admin
category: training
order: 51
summary: Learners build a monitoring plan in four layers, combine checks from outside with status commands on the server, read Velocity's health endpoint and treat planned maintenance as what it is. 45 minutes.
keywords: hosting and operations, monitoring, health check, uptime, exp:systeminfo, cache.php status, audit verify, /Q/health, /Q/metrics, Prometheus, maintenance 503 Retry-After, load balancer health check
type: help
---
# Lesson 5.1: Monitoring and health checks

Exponential has no single health URL. A good monitoring setup combines a check from outside with status commands on
the server, and knows the difference between an outage and a planned maintenance window. This lesson teaches hosting
staff the four layers of monitoring and the commands and endpoints that feed each one.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 5: Monitoring and logs](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Module 4](../module-04-deploying-and-cron/README.md); a training installation served by Velocity |

## Learning objectives

After this lesson, learners can:

- name the four layers of monitoring (reachability, platform, application, integrity) and a tool for each;
- run the status commands that end in an exit code or `PASS` and say what healthy looks like;
- explain why the administration login page is checked besides the front page;
- read Velocity's `/Q/health` and say what it does not prove;
- configure a check so that a maintenance window (`503` with `Retry-After`) is not reported as an outage.

## Trainer's notes

### How to present it

Source: [Monitoring overview](../../../hosting/operations/monitoring-overview.md) and
[Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md).

1. **Four layers** (4 minutes). Reachability (an HTTP check from outside, `/Q/health`), platform (`exp:systeminfo`,
   cache status, crontab), application (logs, error digest, mail and notification status), integrity (the audit
   trail and `exp:audit verify`).
2. **Checks from outside** (4 minutes). The front page may be served from a cache while the database is down; the
   administration login page goes through the kernel. A word only the rendered page contains is stronger than a
   status code. Point the monitor at the HTTPS address visitors use.
3. **Status commands** (5 minutes). `./console exp:systeminfo --json` exits 1 when a health check fails;
   `php bin/php/cache.php status --json` ends in `PASS`; `php bin/php/audit.php verify` exits 0 when every chain is
   intact. `exp:systeminfo` reports the command-line PHP; the administration page shows the server that answered.
4. **Velocity** (3 minutes). `/Q/health` answers anyone with `{"status":"ok"}` and figures only to an admin;
   `/Q/metrics` gives Prometheus text. It proves the server process answers, not that the site renders.
5. **Maintenance** (4 minutes). Maintenance mode answers `503` with `Retry-After`; maintenance never ends by itself
   when the expected time passes. Pause the check or accept a 503 with `Retry-After` while maintenance is on, and
   alert when the window overruns.

### Common questions

**Is one script on one server enough?** For a single server, the sources say a small script in the site user's
crontab that runs these commands and mails on a non-zero exit status is enough. For many servers, run them from your
monitoring agent.

**What should a load balancer check?** The administration login page (through the kernel, heavier), `/Q/health`
(cheap, process only) or a static file (cheapest, stays green when PHP is broken). Make sure a node in maintenance is
taken out.

**Does the site alert by itself?** The audit trail's alert rules run in the `frequent` cron part: brute-force
sign-ins, an administrator role granted, settings written out of hours, mass deletes.

**Does the documentation give uptime figures for a plan?** No; that is a business decision.

### Pitfalls

- **Monitoring only the front page.** It stays green from a cache while the database is down.
- **Opening `/Q/` endpoints to the internet.** Keep `Remote` disabled; use a token or the same machine.
- **Learners without Velocity.** Skip step 6 of the exercise and use the Apache checks instead.

## Hands-on exercise

Use your own training installation, served by Velocity, and a shell as the site's user.

1. Check the site from outside the application, with the address `exp:velocity status` shows:

   ```bash
   curl -s -o /dev/null -w '%{http_code} %{time_total}\n' http://127.0.0.1:8088/
   curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/admin/user/login
   ```

2. Run the platform health checks and print the exit status:
   `php bin/php/console exp:systeminfo --json > var/training-systeminfo.json; echo "exit $?"`.
3. Check the caches: `php bin/php/cache.php status --json | tail -3`.
4. Check the audit trail: `php bin/php/audit.php verify; echo "exit $?"`.
5. Check disk space: `df -h .`.
6. Request Velocity's health endpoint: `curl -s http://127.0.0.1:8088/Q/health`.
7. Switch maintenance mode on with an expected duration:
   `php bin/php/maintenance.php on --message="Monitoring practice" --until=15m`.
8. Repeat the first request of step 1 with headers: `curl -sI http://127.0.0.1:8088/ | head -5`. Note the status and
   `Retry-After`.
9. Request a stylesheet path from the front page and note its status.
10. Switch maintenance mode off: `php bin/php/maintenance.php off`.
11. Write the four lines of a monitoring plan for your site, one per layer, each with a command or URL and what healthy
    looks like.

## Expected result

- Step 1: `200` for both, with the times noted.
- Steps 2 and 4 end with `exit 0`; step 3 ends in `PASS`.
- Step 6 prints `{"status":"ok"}`.
- Step 8 shows `503` with a `Retry-After` header; step 9 still answers `200`.
- The trainer checks two plans from step 11 for all four layers.

## Quiz

1. Why is checking only the front page not enough?
2. What does `exp:systeminfo --json` do when a health check fails?
   A. prints FAIL and exits 0 B. exits 1 C. sends an e-mail D. switches maintenance on
3. What does a `{"status":"ok"}` from `/Q/health` prove, and what not?
4. How does a maintenance window look to an HTTP check?
5. Which layer does `php bin/php/audit.php verify` cover?

<details>
<summary>Answers</summary>

1. The front page is often served from a cache and can answer while the database is down; check the administration
   login page too.
2. **B.** It exits 1 when a check fails ([Monitoring overview](../../../hosting/operations/monitoring-overview.md#status-commands-for-a-monitoring-system)).
3. That the Velocity server process answers; not that the site renders a page.
4. A `503` with `Retry-After` and `Cache-Control: no-store`; static files are still served.
5. Integrity: has anyone changed what they should not?

</details>

## Further reading

- [Monitoring overview](../../../hosting/operations/monitoring-overview.md): every signal and who reads it.
- [Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md): checks from outside, load
  balancers.
- [System information](../../../../guides/system-information.md): the health checks with what to do.
- [Maintenance mode](../../../hosting/operations/maintenance-mode.md): windows for hosts.

## Related pages

- [Module 5: Monitoring and logs](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
