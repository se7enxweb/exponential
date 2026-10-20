---
title: "Lesson 2.4: Serving the lab sites with Velocity"
audience: trainer, admin
category: training
order: 24
summary: Start, check and stop the training sites with exp:velocity, using the php engine for a laptop lab and qbix for a shared training server, with one port per installation. 35 minutes.
keywords: velocity, exp:velocity, start, stop, status, php engine, qbix, port 8087, built-in server, training lab, serving
type: help
---
# Lesson 2.4: Serving the lab sites with Velocity

An installation needs a web server before learners can open it. Exponential's console starts one for you:
`exp:velocity` drives Exponential Velocity, and its shipped default engine is PHP's built-in server, which needs
nothing installed. This lesson shows how to serve each learner's site, check it and stop it again.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who prepare labs |
| Prerequisites | [Lesson 2.3](one-install-per-learner.md): an installed site |

## Learning objectives

After this lesson, learners can:

- start and stop a site with `php bin/php/console exp:velocity start` and `stop`;
- read `exp:velocity status` to find every address of the site;
- move an installation to another port so that several can run on one machine;
- choose between the `php` engine (laptop) and the `qbix` engine (shared training server).

## Trainer's notes

### How to present it

1. Show the engine table of [Serving the site, 8.3.2](../../../../install/08-serving-the-site.md#832-choose-the-engine)
   (4 minutes). `php`: development, the shipped default, port 8087, no TLS. `qbix`: recommended from development to
   production, ports 8088 (HTTP) and 8080 (HTTPS).
2. Start a site and run `status` (3 minutes). The status lists every address to open: the site paths, the
   administration login, Setup > System information and Setup > Caches.
3. Show the port change (3 minutes):
   `php bin/php/console exp:velocity config set PHPServerSettings Port 8091`, then restart.

### Common questions

**Can learners open each other's sites?** With the default bind address `127.0.0.1` the site is reachable only from
the machine itself. That is the right default for a laptop lab.

**Is the `php` engine good enough?** For a lab, yes. It is plain HTTP and for development only; never for visitors.

**What about a shared training server?** Use the `qbix` engine there, after reading
[Serving the site, 8.3](../../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way), or a
traditional web server with one virtual host per installation.

### Pitfalls

- **Port already in use.** Two installations on one machine both default to 8087. Give each its own port.
- **A maintenance page.** If an install run stopped half-way, the site is in maintenance:
  `php bin/php/maintenance.php status`, then `off`.
- **Wrong `--url`.** If the site was installed with `--url=http://localhost` but is opened at another port, links
  point to the wrong place. Install with the address you will use.

## Hands-on exercise

On the machine with the installation from lesson 2.3:

1. In the installation root: `php bin/php/console exp:velocity start`.
2. `php bin/php/console exp:velocity status` and find the address of the site and of the administration login.
3. `curl -sI http://127.0.0.1:8087/ | head -1` and expect `HTTP/1.1 200 OK` or a redirect.
4. Open the administration address from the status in a browser and sign in as `admin` with the generated password.
5. Stop the server: `php bin/php/console exp:velocity stop`.
6. Move the installation to another port: `php bin/php/console exp:velocity config set PHPServerSettings Port 8091`,
   then start again and check with `status`.
7. Put the port back with `php bin/php/console exp:velocity config unset PHPServerSettings Port`, and stop the server.

## Expected result

- The site answered on `http://127.0.0.1:8087/` and the administration login worked.
- After the port change, `status` listed addresses on port 8091.
- After `unset`, the shipped port is back; the server is stopped at the end.

## Quiz

1. Which Velocity engine is the shipped default, and on which port does it listen?
2. Which command shows every address of a running site?
3. Two installations on one laptop: what must differ?
4. Why is the default bind address `127.0.0.1`?

<details>
<summary>Answers</summary>

1. The `php` engine (PHP's built-in server), port 8087.
2. `php bin/php/console exp:velocity status`.
3. Their ports (for example with `exp:velocity config set PHPServerSettings Port <port>`).
4. The server has no authentication of its own and serves the whole document root; reachable only from the machine
   itself (or through a tunnel or proxy).

</details>

## Further reading

- [Serving the site](../../../../install/08-serving-the-site.md): Velocity, FrankenPHP, Apache and nginx.
- [Velocity web server](../../../../features/6.0/velocity-web-server.md): the feature page.
- [Maintenance mode](../../../../guides/maintenance-mode.md): when a site shows the maintenance page.

## Related pages

- [Module 2: Building the training lab](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
