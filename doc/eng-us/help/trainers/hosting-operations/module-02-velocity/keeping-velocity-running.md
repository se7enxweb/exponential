---
title: "Lesson 2.2: Keeping Velocity running"
audience: trainer, hosting, admin
category: training
order: 22
summary: Learners stop, restart and gracefully reload Velocity, write a boot unit that runs exp:velocity, read the watchdog's behavior and find the server's logs and its configuration tree. 45 minutes.
keywords: hosting and operations, Velocity, restart, graceful, stop, kill, systemd, boot service, watchdog, console.log, access log, /etc/vc, layout, Debian style
type: help
---
# Lesson 2.2: Keeping Velocity running

A long-running server must come back after a reboot and after a crash, and an operator must be able to reload it
without dropping requests. This lesson covers the control verbs, a boot unit, the optional watchdog, the logs and the
configuration tree that is laid out like Debian's `/etc/apache2`.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 2: Velocity](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 2.1](what-velocity-is.md); root on the learner's own virtual machine for the boot unit |

## Learning objectives

After this lesson, learners can:

- choose between `stop`, `restart`, `graceful` and `kill` for a situation;
- write a systemd unit that runs `exp:velocity start`, `stop` and `graceful`;
- explain when the watchdog starts the server and when it does not;
- find the access log, the error log, `console.log` and the pid file;
- read the configuration tree with `exp:velocity layout`.

## Trainer's notes

### How to present it

Source: [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md#keeping-it-running-boot-service-and-watchdog)
and [Chapter 8, sections 8.3.10 to 8.3.12](../../../../install/08-serving-the-site.md#8310-the-configuration-tree-etcvc-debian-style).

1. **The verbs** (4 minutes). `stop` asks the server to stop and waits; `restart` stops and starts and succeeds only
   when a new parent answers; `graceful` re-executes the server while keeping the listening socket, so no request is
   dropped; `kill` stops without asking, for a wedged worker. A stopped server stays stopped: the watchdog does not
   start it again until `start`.
2. **Boot service** (5 minutes). `exp:velocity` has no command that installs one. The engine's own units start the
   engine directly and bypass what `exp:velocity` adds (the generated site file, `KeepGlobals`, PHP options, the worker
   user). Show the derived unit of section 8.3.11: `Type=forking`, `ExecStart` with `exp:velocity start`,
   `ExecReload` with `graceful`, `PIDFile` on `var/vc/qbix/run/server.pid`. It is not shipped.
3. **The watchdog** (4 minutes). Off until installed (root, systemd). It starts the engine only when no process of it
   runs, nobody stopped it on purpose, no restart is in progress and it is not backing off. It never stops or restarts
   a running server. Log: `var/log/velocity-watchdog.log`.
4. **Logs** (3 minutes). `var/vc/qbix/log/<siteaccess>-access.log` and `-error.log`; `var/vc/qbix/run/console.log`
   for start-up lines; the kernel's own logs stay in `var/<site>/log/`.
5. **The tree** (4 minutes). `/etc/vc` when run as root, else `var/vc/qbix/etc`. `vc.conf`, `ports.conf`, `envvars`,
   `conf-`, `mods-` and `sites-available` with `-enabled` symlinks, enabled with `exp:velocity site|conf|mod enable`.

### Common questions

**Which verb after a PHP change?** None of these alone: `exp:velocity deploy` (Module 4) runs the steps in order.

**Does `graceful` work on every engine?** It is a verb for every engine; on FrankenPHP it reloads the configuration
through Caddy's admin API.

**Can I edit a generated file in the tree?** Generated files carry `"_generated": "exp:velocity"`; remove the marker
and the file is never overwritten again, like a dpkg conffile.

**Who may read the access log?** It names visitors, so `[LogSettings] FileMode` ships as `0640`.

### Pitfalls

- **Two units for the same ports.** Do not combine the derived unit with the operating-system package's unit.
- **`Instances` above 1.** The `PIDFile` then names instance 0 only.
- **Learners without root.** Without root, the tree is in `var/vc/qbix/etc` and the boot unit cannot be installed;
  let them write the unit file and read it with the trainer.
- **Stopping the trainer's demo server.** Each learner controls only their own installation.

## Hands-on exercise

Use your own training installation from lesson 2.1, served by Velocity.

1. Run `php bin/php/console exp:velocity graceful`, then `status`. Note that the site kept answering.
2. Run `php bin/php/console exp:velocity restart` and check with `status` that a new parent answers.
3. List the log directory: `ls -l var/vc/qbix/log/`. Note the access and error log names and their mode.
4. Read the last access lines: `tail -n 5 var/vc/qbix/log/*-access.log`.
5. Show the configuration tree: `php bin/php/console exp:velocity layout`. Note which directory is used and which
   modules are enabled.
6. Print the command line the server would run: `php bin/php/console exp:velocity command`.
7. On a virtual machine where you are root, write the derived unit of
   [section 8.3.11](../../../../install/08-serving-the-site.md#8311-running-velocity-as-a-service) to
   `/etc/systemd/system/exponential-velocity-training.service`, with your installation path.
8. Stop the server you started by hand (`exp:velocity stop`), then run `systemctl daemon-reload` and
   `systemctl enable --now exponential-velocity-training`.
9. Check with `systemctl status exponential-velocity-training` and `exp:velocity status`.
10. Read the watchdog's state without installing it: `php bin/php/console exp:velocity watchdog status`.

## Expected result

- Steps 1 and 2: the site answers after each command; `status` shows a new parent after `restart`.
- Step 3: the logs exist with mode `-rw-r-----` (`0640`).
- Step 5 names `/etc/vc` (as root) or `var/vc/qbix/etc`.
- Step 9: systemd shows the unit active and `exp:velocity status` shows the server running.
- Step 10 reports that the watchdog is not installed (it is off until installed).
- The trainer checks one learner's `systemctl status` on the projector.

## Quiz

1. Which verb reloads Velocity without dropping a request?
   A. `restart` B. `kill` C. `graceful` D. `stop`
2. Why does the derived unit run `exp:velocity start` rather than the engine directly?
3. You stopped Velocity on purpose with `exp:velocity stop`. Will the watchdog start it again?
4. Where are the start-up lines, such as `Workers as: ...`, written?
5. What does `exp:velocity conf enable <name>` do?

<details>
<summary>Answers</summary>

1. **C.** It re-executes the server while keeping the listening socket.
2. Starting the engine directly bypasses what `exp:velocity` adds: the generated site file, `KeepGlobals`, the PHP
   options, the worker user, the engine archive and the instances (section 8.3.11).
3. No. It does not start a server that was stopped on purpose, until `start`.
4. In `var/vc/qbix/run/console.log`.
5. It links `conf-available/<name>.conf` into `conf-enabled/`, as `a2enconf` does.

</details>

## Further reading

- [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md): boot service, watchdog and logs.
- [Velocity will not start](../../../hosting/runbooks/velocity-will-not-start.md): what to do when it does not come
  up.
- [Velocity control panel and dashboard](../../../../features/6.0/velocity-control-panel.md): the `/Q/` pages.
- [Velocity scheduler](../../../../features/6.0/velocity-scheduler.md): tasks the server runs on a schedule.

## Related pages

- [Module 2: Velocity](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
