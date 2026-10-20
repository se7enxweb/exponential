---
title: "Lesson 9.1: The console"
audience: trainer, developer
category: training
order: 91
summary: Learners find any command with php bin/php/console list, read its help, understand the exp, shell, bin, ext and cron namespaces, and run cronjob parts through the console. 35 minutes.
keywords: developers course, console, bin/php/console, list, help, namespaces, exp:, shell:, bin:, ext:, cron:, crontab:list, allow-root-user, @description, did you mean
type: help
---
# Lesson 9.1: The console

Exponential has more than a hundred command line scripts. The console, `php bin/php/console`, finds all of them,
those of the kernel and those of every extension, describes them and runs them by name. In this lesson learners find
their way through it, so that the next lesson's command of their own appears in the list beside the kernel's.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 9: The console and persistent objects](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Beginner |
| Audience | Developers and administrators |
| Prerequisites | [Module 1](../module-01-the-development-installation/README.md) |

## Learning objectives

After this lesson, learners can:

- list every command, or the commands of one namespace, with `php bin/php/console list`;
- read a command's help in three ways;
- explain the namespaces `exp:`, `shell:`, `bin:`, `ext:<ext>:` and `cron:`;
- run a cronjob part with `cron:<part>` and show the crontab with `crontab:list`;
- explain `--allow-root-user` and why commands should run as the site user.

## Trainer's notes

### How to present it

Source: [Exponential Console](../../../../bc/6.0/console.md).

1. **`list`** (3 minutes). `php bin/php/console list` prints every command with a one-line description, grouped by
   namespace; `list exp`, `list shell`, `list cron`, `list ext:<ext>` filter.
2. **Names** (3 minutes). Project the table:

   | Namespace | Runs |
   |---|---|
   | `exp:<name>` | `bin/php/<name>.php` |
   | `shell:<name>` | `bin/shell/<name>.sh` |
   | `bin:<name>` | `bin/<name>.sh` or `bin/<name>.php` |
   | `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` or `extension/<ext>/bin/<name>.sh` |
   | `cron:<part>` | a cronjob part through `runcronjobs.php` |

   Without a namespace the console tries `exp:`, then `shell:`, then `bin:`. Many commands have aliases, such as
   `exp:logs` for `exp:explogs`.
3. **Help** (2 minutes). `help <command>`, `--help <command>` and `<command> --help` print the script's own usage.
4. **Root** (2 minutes). Scripts refuse to run as root without `--allow-root-user`; files a script writes as root can
   break a site that runs as another user.

### Common questions

**Is `./console` the same?** Yes, if you made the link `ln -s bin/php/console console`; it is not shipped.

**Where do descriptions come from?** From a `@description` tag in the script's docblock (lesson 9.2).

**Can I see scheduled jobs?** `php bin/php/console crontab:list` prints the system crontab of the current user;
**Setup** > **Cronjobs** shows the parts in the browser.

### Pitfalls

- **Running from another directory.** The console must run from the installation root, or `autoload.php` is not
  found.
- **Commands that delete.** Some commands remove data (`exp:trashpurge`, `exp:cleanupversions`). Read `--help` first
  and prefer `--dry-run` where offered.
- **Typos.** An unknown name prints "Did you mean:" with the closest match.

## Hands-on exercise

On the development installation, from its root folder.

1. List every command and count them: `php bin/php/console list | tail -3`.
2. List the cronjob parts: `php bin/php/console list cron`.
3. List the shell commands: `php bin/php/console list shell`.
4. Read the help of the cache command in two ways: `php bin/php/console help exp:ezcache` and
   `php bin/php/console exp:ezcache --help`.
5. Mistype a name on purpose: `php bin/php/console exp:ezcach`. Read the suggestion.
6. Find the command that checks template syntax and the one that compiles templates (hint: `list exp`, look for
   `template`).
7. Run the system information command and read its first lines:
   `php bin/php/console exp:systeminfo | head -20`.
8. Show the version: `php bin/php/console --version`.

## Expected result

- Step 1 ends with the number of commands available.
- Step 2 lists parts such as `cron:frequent` and `cron:infrequent` with descriptions.
- Step 5 prints `UNKNOWN COMMAND`, `Command "exp:ezcach" was not found.` and `Did you mean:  exp:ezcache`.
- Step 6: `exp:eztemplatecheck` and `exp:eztc`.
- The trainer asks each learner which file `ext:mysite_hello:latest` would run.

## Quiz

1. Which command lists the commands of one extension?
2. What does `exp:ezcache` run?
   A. `bin/shell/ezcache.sh` B. `bin/php/ezcache.php` C. `cronjobs/ezcache.php` D. `kernel/ezcache.php`
3. Name two ways to read a command's help.
4. Why does a script refuse to run as root?
5. Which file does `ext:mysite_hello:latest` run?

<details>
<summary>Answers</summary>

1. `php bin/php/console list ext:<ext>`.
2. **B.** `bin/php/ezcache.php`.
3. Two of `help <command>`, `--help <command>`, `<command> --help`.
4. Files written as root can break a site that runs as another user; `--allow-root-user` confirms on purpose.
5. `extension/mysite_hello/bin/php/latest.php`.

</details>

## Further reading

- [Exponential Console](../../../../bc/6.0/console.md): names, options, cronjob parts and extension commands.
- [The cronjobs console](../../../../features/6.0/cronjobs-console.md): the browser view of the cronjob parts.

## Related pages

- [Module 9: The console and persistent objects](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
