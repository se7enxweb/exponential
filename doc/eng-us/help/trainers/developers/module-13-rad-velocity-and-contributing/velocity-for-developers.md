---
title: "Lesson 13.2: Velocity for developers"
audience: trainer, developer
category: training
order: 132
summary: Learners choose a Velocity engine, explain why a changed PHP class reaches persistent workers only after a restart, deploy a PHP change with exp:velocity deploy in the right order, and clear the response cache. 45 minutes.
keywords: developers course, Velocity, exp:velocity, engine, php, qbix, frankenphp, start, status, restart, deploy, dry-run, kernel, packer, response cache, persistent workers, OPcache, order of cache clearing
type: help
---
# Lesson 13.2: Velocity for developers

Exponential Velocity is the application server that comes with Exponential. Its workers keep PHP code in memory
between requests, which makes it fast and changes one habit of every developer: a changed class is not live until
the workers have the new code. This lesson explains the engines, the deploy command that does every step in the
right order, and why the order matters.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 13: RAD, Velocity and contributing](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and operators |
| Prerequisites | [Lesson 1.2](../module-01-the-development-installation/a-development-installation-with-git.md) and [Lesson 3.5](../module-03-designs-overrides-and-debugging/caches-for-template-developers.md) |

## Learning objectives

After this lesson, learners can:

- name the three engines (`php`, `qbix`, `frankenphp`), their roles and default ports;
- start, check and restart Velocity with `exp:velocity`;
- explain why persistent workers need a restart after a class or INI change;
- deploy a PHP change with `exp:velocity deploy`, first with `--dry-run`, and know when to add `--kernel` and
  `--packer`;
- explain why rendered-output caches are cleared only after the new code runs.

## Trainer's notes

### How to present it

Sources: [Velocity engines](../../../../bc/6.0/velocity-engines.md) and
[8. Serving the site](../../../../install/08-serving-the-site.md).

1. **Engines** (4 minutes). `velocity.ini [ServerSettings] Engine` sets the default: `php` (shipped; PHP's built-in
   server, development, port 8087), `qbix` (the recommended engine for every stage, port 8088), `frankenphp` (a
   production-ready alternative, port 8089). `--engine=` chooses one for a command.
2. **Persistent workers** (4 minutes). Qbix workers load classes once and keep them; a changed class, operator or
   INI file reaches them after `exp:velocity restart`. The `php` engine starts fresh per request, which is why
   development installations rarely notice.
3. **Deploy** (6 minutes). `exp:velocity deploy` runs, in order: extension autoloads (`--kernel`: kernel autoloads
   too), INI caches, template, override, translation and design caches, the engine archive when kernel files
   changed, PHP-FPM, the Velocity restart, then the content, HTTP cache and template-block caches (with `--packer`
   the packed scripts and styles just before them), then Velocity's response cache, then warm-up. Each step says
   PASS, FAIL or SKIP; the first failure stops the run.
4. **The order is the point** (3 minutes). Rendered output cached before the workers run the new code is rendered by
   the old code and served as if the change had not worked. Clear output caches last.
5. **Status** (2 minutes). `exp:velocity status` shows what runs; `exp:velocity cache clear` clears the response
   cache.

### Common questions

**When do I need `--kernel`?** When a kernel class was added or renamed (the kernel autoloads change).

**When do I need `--packer`?** When an ezjscore server function or its settings changed; an edited `.js` or `.css`
file gets a new packed file by itself.

**Does deploy reload PHP-FPM every time?** Only when it must; otherwise it waits until OPcache has revalidated the
changed files (`[DeploySettings] PhpFpmReload=auto`).

### Pitfalls

- **Clearing caches before the restart.** The old code fills them again. Use `deploy`, which keeps the order.
- **Testing a class change on Qbix without a restart.** The old class still runs; the change "does not work".
- **Running the built-in engine on a public address.** Keep the `php` engine at 127.0.0.1; it is a development server.

## Hands-on exercise

On the development installation.

1. Show the engines and what runs: `php bin/php/console exp:velocity status`.
2. Read the default engine: `php bin/php/console exp:ini where velocity.ini/ServerSettings/Engine`.
3. See what a deploy would do: `php bin/php/console exp:velocity deploy --dry-run`.
4. Change the text your `mysite_initials` operator returns for empty input (for example `-` instead of an empty
   string), and run the deploy for real: `php bin/php/console exp:velocity deploy`.
5. Read the PASS, FAIL and SKIP lines and the total.
6. Open the playground or a page that uses the operator and check the change.
7. If your trainer runs a Qbix engine on the training server, compare: change the operator again, reload a page
   served by Qbix without a restart, then after `exp:velocity restart`.
8. Undo the change, deploy again, and run the test of lesson 12.2.

## Expected result

- Step 1 lists the engine in use, its address and port.
- Step 3 lists the steps it would run and runs none.
- Step 5 ends with `velocity deploy: PASS` and the number of steps and seconds.
- Step 7: on Qbix, the old behavior until the restart.
- Step 8: the tests pass again.
- The trainer asks each learner why the output caches are cleared last.

## Quiz

1. Which engine is shipped as the default, and which is recommended for real sites?
2. A class changed. What must happen before Qbix workers run it?
   A. Nothing B. `exp:velocity restart` (or a deploy) C. A browser reload D. Clearing the INI cache only
3. Which option shows what `deploy` would do without doing it?
4. When is `--kernel` needed?
5. Why are the rendered-output caches cleared after the restart?

<details>
<summary>Answers</summary>

1. `php` is the shipped default; `qbix` is recommended for every stage.
2. **B.** The workers keep the classes of their warm-up until a restart.
3. `--dry-run`.
4. When a kernel class was added or renamed.
5. Pages rendered in between by the old code would be cached again and served as if the change had not worked.

</details>

## Further reading

- [Velocity engines](../../../../bc/6.0/velocity-engines.md): engines, deploy, restart and the watchdog.
- [Velocity persistent worker server](../../../../features/6.0/velocity-persistent-worker-server.md).
- [Velocity response cache](../../../../features/6.0/velocity-response-cache.md).

## Related pages

- [Module 13: RAD, Velocity and contributing](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
