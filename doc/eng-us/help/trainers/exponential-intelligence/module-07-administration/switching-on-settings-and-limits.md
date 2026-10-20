---
title: "Lesson 7.2: Switching on, keys, limits and budgets"
audience: trainer, admin
category: training
order: 72
summary: Administrators follow the steps to switch the assistant on, keep keys out of the repository, use the master switch and per-siteaccess switch, set usage limits, and read EI: Budgets. 60 minutes.
keywords: exponential intelligence, administration, activate, switch on, master switch, enabled, disabled siteaccesses, api key, settings override, limits, budgets, ei budgets, cron, job queue, expaischema
type: help
---
# Lesson 7.2: Switching on, keys, limits and budgets

The assistant ships with every installation and stays off until an administrator switches it on. This lesson walks
through switching it on safely on a training installation, where the keys go, how to switch it off again, and how to
cap its use with limits and budgets.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 7: Administration](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Administrators with shell access to a training installation |
| Prerequisites | [Lesson 7.1](providers-and-models.md); a training installation and a provider key meant for training, or a local model |

## Learning objectives

After this lesson, learners can:

- list the steps to switch the assistant on: activate, key, tables, policies, caches, cron;
- keep keys only in `settings/override/expai.ini.append.php` (mode 0640) or an untracked key location;
- switch the assistant off for the whole site or one siteaccess;
- set usage limits in `[Limits]` and read them on the dashboard;
- read **EI: Budgets**: the monthly budget, job groups, lanes and provider batches.

## Trainer's notes

### How to present it

Sources: [expai: Switch it on](../../../../features/6.0/extensions/expai.md#switch-it-on) and the configuration and
budgets pages under **EI: Help** (`extension/expai/doc/configuration.md`, `extension/expai/doc/budgets.md`).

1. **The steps** (6 minutes). From the feature page:
   1. activate `expai` for the admin siteaccess(es) (`ActiveAccessExtensions[]=expai` per admin siteaccess, or
      `ActiveExtensions[]` for all);
   2. put the key into `settings/override/expai.ini.append.php` only (mode 0640, never committed), with `[AISettings]
      Enabled=true`;
   3. create the tables: `php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>`;
   4. give editors `expai/write` and `ezjscore/call` (function list `expai`);
   5. regenerate autoloads, clear the INI and template caches, reload PHP;
   6. cron: the `expqueue` part every minute and the `expai` part every few minutes.
2. **Keys** (3 minutes). Never in a tracked file, never in a template, never in a ticket or chat. **EI: Models** warns
   about a key in any other file or a file every user of the server can read.
3. **Switching off** (3 minutes). `[AISettings] Enabled=false` switches everything off (no tab, no edit form controls,
   no Vector, nothing queued, queued jobs held, no request to any provider); `DisabledSiteAccesses[]` switches it off on
   some siteaccesses. The dashboard then says it is switched off.
4. **Limits** (4 minutes). All optional: tokens per conversation, per session, per user per day and month, cost per
   user per day and month, per site per month, per provider per month, requests per turn. Checked before every
   request; the editor is told which limit was reached and when it resets.
5. **Budgets** (4 minutes). Setup > **EI: Budgets**: **Budget this month** (queued work), **Job groups**, **Lanes**,
   **Provider batches**. A job group can have its own budget (for example the alt text report's optional budget).

### Common questions

**Can we try it without a hosted provider?** Yes, with a local model and region `local`; slow on ordinary servers.

**Where do the provider's own limits come in?** The provider's rate limits and the organization's spend limit apply as
well; the dashboard shows the rate limits the provider last reported.

**Do I need the job queue?** For chains, queued alt text, translation sync and background runs, yes.

### Pitfalls

- **A key in a committed file.** Rotate it at the provider if that happens, and move it.
- **No limits on a training site.** Set per-user daily limits before the course.
- **Forgetting the per-siteaccess activation** in cron lines: name the admin siteaccess with `-s`.

## Hands-on exercise

Use a training installation where you have shell access (your own, installed with `php bin/php/console exp:install`,
see [7. The console install](../../../../install/07-console-install.md)). Never on a live site.

1. Read the "Switch it on" steps of the feature page and check which are already done on your installation (is
   `expai` active for the admin siteaccess? are the tables there?).
2. Open `settings/override/expai.ini.append.php` (create it if missing, mode 0640). Add `[AISettings]`, `Enabled=true`
   and your training key, or configure a local model as the installation guide describes. Do not commit the file.
3. Run the table installation for your admin siteaccess:

   ```bash
   php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>
   ```

4. Add a limit for the training users, for example in the same file:

   ```ini
   [Limits]
   MaxCostPerUserPerDay=1
   ```

5. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and reload PHP as your installation requires.
6. Open the dashboard page **Exponential Intelligence**: the status line says **Ready**, and **Limits** shows a bar for
   your limit.
7. Open Setup > **EI: Budgets** and read its four sections.
8. Switch the assistant off with `Enabled=false`, clear the INI cache, and check that the dashboard says it is switched
   off. Switch it on again.

## Expected result

- The assistant is on, with the key only in the override file, a daily cost limit visible on the dashboard, and the
  budgets page read.
- Switching off and on worked.
- The trainer checks by asking each learner where their key is stored and showing `git status` on the projector (the
  override file must not appear as staged).

## Quiz

1. Where may an API key be stored?
2. What does `[AISettings] Enabled=false` do?
3. Which setting switches the assistant off for one siteaccess only?
   A. `Enabled` B. `DisabledSiteAccesses[]` C. `AllowedModels[]` D. `Region`
4. Name three kinds of limit.
5. Which cron parts does the assistant need?

<details>
<summary>Answers</summary>

1. In `settings/override/expai.ini.append.php` (mode 0640, never committed) or an untracked key location.
2. Switches everything off: no tab, no edit form controls, no Vector, nothing queued, no request to any provider.
3. **B.**
4. Any three of: tokens per conversation, per session, per user per day or month; cost per user per day or month;
   per site per month; per provider per month; requests per turn.
5. The `expqueue` part (every minute) and the `expai` part (every few minutes).

</details>

## Further reading

- [expai: Switch it on](../../../../features/6.0/extensions/expai.md#switch-it-on)
- [expqueue](../../../../features/6.0/extensions/expqueue.md): the job queue.
- The installation guide, configuration and budgets pages under **EI: Help**.

## Related pages

- [Module 7: Administration](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
