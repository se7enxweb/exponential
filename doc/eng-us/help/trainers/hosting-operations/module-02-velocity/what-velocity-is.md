---
title: "Lesson 2.1: What Velocity is and a first start"
audience: trainer, hosting, admin
category: training
order: 21
summary: Learners explain how Velocity serves a site with a parent process and persistent workers, choose the qbix engine, change settings with exp:velocity config and start and check the server. 45 minutes.
keywords: hosting and operations, Velocity, qbix, engine, exp:velocity, status, start, config set, velocity.ini, persistent workers, parent process, ports 8088 8080
type: help
---
# Lesson 2.1: What Velocity is and a first start

Exponential Velocity is the application server that ships for Exponential and the recommended way to serve a site at
every stage. It needs no separate web server or TLS terminator. This lesson explains the model in plain terms and
takes every learner through a first start on their own installation.

> **Note**
> Learners start and stop Velocity only on their own training installation. Never practise on a server that hosts
> other sites.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 2: Velocity](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Module 1](../module-01-serving-the-site/README.md); a training installation with PHP 8.0 or later and the `sockets`, `pcntl`, `posix` and `openssl` extensions |

## Learning objectives

After this lesson, learners can:

- describe the parent process, the workers and what the parent answers without a worker;
- name the three engines `exp:velocity` drives and which one serves real sites;
- read and change a setting with `exp:velocity config` without editing `settings/velocity.ini`;
- start the server, read `exp:velocity status` and check the site with `curl`;
- name the files that tell why a first start failed.

## Trainer's notes

### How to present it

Source: [Chapter 8, section 8.3](../../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)
and [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md).

1. **The model** (5 minutes). Draw the diagram of section 8.3: a parent process loads the application once and forks
   workers that answer one request after another; between requests each worker is reset. The parent accepts every
   connection, answers static files, response-cache hits and its own `/Q/` pages, and terminates TLS.
2. **What changes for operations** (4 minutes). Use the table in *Velocity for hosts*: one long-running server per
   site; a changed class or INI file reaches the workers only after a restart; memory is judged by PSS, never RSS.
3. **Engines** (3 minutes). `qbix` (Velocity's own server, recommended), `frankenphp` (production-ready
   alternative), `php` (PHP's built-in development server, the shipped default only because it always works).
   Shipped ports: `qbix` 8088 and 8080, `frankenphp` 8089 and 8444, `php` 8087.
4. **Settings** (4 minutes). `settings/velocity.ini` holds the commented defaults and is never edited;
   `exp:velocity config set` writes `settings/override/velocity.ini.append.php`; the configuration tree is generated
   from both on every start. A changed setting reaches the server at the next `restart` or `graceful`.
5. **First start** (4 minutes). Run `start` and `status` on the projector. Point out that `status` lists every
   address as a full URL and says `running` only when the server answers a request.

### Common questions

**Do I need Apache in front?** No. Velocity serves HTTP and HTTPS itself. A proxy in front is still right when a load
balancer or CDN terminates TLS (section 8.8).

**Does the engine package come installed?** Yes: Exponential's `composer.json` requires it at `~0.0.4.55`; 0.0.4.54 was the first
release that runs on PHP 8.0. Before that it needed PHP 8.1 and was only suggested; an older installation adds it
with `composer require se7enxweb/exponential-velocity:~0.0.4.55`.

**What does `ext check` do?** It lists the PHP extensions this PHP lacks, with the install command for the operating
system (`ext install-hint <extension>`).

**Can two engines run at once?** Yes, each has its own port, pid file and logs; a site is served by one.

### Pitfalls

- **The site is not reachable from another machine.** `Host` is still `127.0.0.1`. That is the safe default; in class,
  test with `curl` on the machine itself or through an SSH tunnel.
- **Port already in use.** Another engine or program holds it. `exp:velocity status --all` shows every engine.
- **A maintenance page after install.** An installation run left maintenance mode on:
  `php bin/php/maintenance.php status`, then `off`.
- **Composer not allowed in class.** Prepare installations with the engine package already required.

## Hands-on exercise

Use your own training installation. Run every command from the installation root.

1. Check the PHP extensions the engine expects:

   ```bash
   php bin/php/console exp:velocity ext check
   ```

2. Read the full command list once: `php bin/php/console exp:velocity --help | less`.
3. Show which engine is the default and which settings you have overridden:

   ```bash
   php bin/php/console exp:velocity config get ServerSettings Engine
   php bin/php/console exp:velocity config list ServerSettings
   ```

4. If the engine is not `qbix`, set it:
   `php bin/php/console exp:velocity config set ServerSettings Engine qbix`.
5. Show which file was written: `php bin/php/console exp:velocity config paths`.
6. Start the server: `php bin/php/console exp:velocity start`.
7. Read the status: `php bin/php/console exp:velocity status`. Note the site address, the administration address and
   the dashboard address.
8. Check the site: `curl -sI http://127.0.0.1:8088/ | head -1` (use the port the status shows).
9. Read the start-up lines: `tail -n 20 var/vc/qbix/run/console.log`. Find the line that starts with `Workers as:`.
10. Print the machine-readable status: `php bin/php/console exp:velocity status --json | head -30`.

## Expected result

- Step 1 lists nothing missing, or names the extensions with their install commands.
- Step 5 shows that the override file is `settings/override/velocity.ini.append.php`.
- Step 7 shows the `qbix` engine running with full URLs for the site and the administration.
- Step 8 prints `HTTP/1.1 200 OK` or a redirect.
- Step 9 names the user the workers run as.
- The trainer checks by asking each learner for the `Workers as:` user and whether it is root.

## Quiz

1. What does the Velocity parent process answer without a worker?
2. Which file do you never edit to change a Velocity setting?
   A. `settings/override/velocity.ini.append.php` B. `settings/velocity.ini` C. none, both are generated
   D. `/etc/vc/vc.conf`
3. You changed `[ServerSettings] Workers`. When does the server use the new value?
4. Why is the shipped `Host` `127.0.0.1`?
5. True or false: `exp:velocity status` says `running` as soon as the pid file exists.

<details>
<summary>Answers</summary>

1. Static files, response-cache hits and its own `/Q/` pages; it also terminates TLS (section 8.3).
2. **B.** It holds the shipped defaults; `config set` writes the override file.
3. At the next `restart` or `graceful`.
4. So the server is reachable only from the machine, through a tunnel or a reverse proxy, until you decide otherwise.
5. **False.** It says `running` only for a server that answers an HTTP request, and exits non-zero otherwise.

</details>

## Further reading

- [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md): requirements, settings and operations.
- [Chapter 8, Command reference](../../../../install/08-serving-the-site.md#8316-command-reference): every verb.
- [Velocity: running Exponential in a persistent-worker web server](../../../../features/6.0/velocity-persistent-worker-server.md):
  the feature page.
- [Velocity will not start](../../../hosting/runbooks/velocity-will-not-start.md): the runbook for a failed start.

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
