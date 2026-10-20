---
title: "Lesson 1.3: System information and health checks"
audience: trainer, admin
category: training
order: 13
summary: Learners read the System information page, act on its health checks, compare it with the command-line report, and produce a masked report for a support request. 35 minutes.
keywords: site administration, system information, health checks, setup/info, exp:system:info, support report, opcache, cronjobs, siteaccesses, site data
type: help
---
# Lesson 1.3: System information and health checks

When a site misbehaves, the first question is always "what does it run on?". **Setup > System information** answers
it on one page: which server answered, which PHP, which database, what the caches do, when cronjobs last ran, and a
list of health checks with what to do about each. It only reads, so it is safe to open at any time.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 1: Getting oriented as a site administrator](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators and support staff |
| Prerequisites | [Lesson 1.2](a-tour-of-the-setup-area.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- read the banner that says which server answered, and explain why figures differ between servers;
- read the **Health checks** and say what to do about a warning;
- find the siteaccesses of the installation and how a request reaches each;
- download a support report and say what it never contains;
- print the same report with `exp:system:info` in a terminal.

## Trainer's notes

### How to present it

Source: [System information](../../../../guides/system-information.md).

1. **Open the page** (2 minutes): **Setup > System information**. The headings are **Health checks**, **Overview**,
   then folded parts such as **Active extensions**, **PHP extensions and settings**, **Site addresses**,
   **Siteaccesses**, **OPcache and APCu**, **Database connection** and **Report for support**.
2. **Who answered** (3 minutes). The banner names the server: Apache with PHP-FPM, Exponential Velocity, FrankenPHP
   or PHP's built-in server. Each has its own PHP and OPcache, so the same page shows other figures on another
   server. The training installation runs on Velocity.
3. **Health checks** (4 minutes). Each check is **Failure**, **Warning**, **Note** or **OK**, failures first, each
   with what to do. Typical findings on a training installation: mail with `Transport=file` (a note: nothing is
   sent, which is what a lab wants) and no cronjob run yet.
4. **Overview cards** (3 minutes): **Exponential**, **Server**, **PHP**, **OPcache and APCu**, **Database**,
   **Storage**, **Caches**, **Cronjobs**, **Mail**, **Locale and time**, **Machine**.
5. **Siteaccesses** (2 minutes). Open the folded part: this request, the resolution map (which rule reaches which
   siteaccess) and a card per siteaccess. Module 4 comes back to it.
6. **The report** (1 minute). **Download report (.txt)**, **Download as JSON** and **Copy report as text** give a
   report for a support request without passwords, keys, tokens, session ids or full server paths.

### Common questions

**The figures differ between the administration on port 443 and the one on Velocity.** They should: two servers, two
PHP processes, two OPcaches. Check the banner before comparing.

**The cronjob check warns, but cron runs.** The check looks at the run history of **Setup > Cronjobs** and at
`var/log/cronjob*.log`. Runs that write to neither are not seen.

**Who may open this page?** Anyone with the policy `setup/system_info`. Give it only to administrators: even masked,
the page says a lot about the installation.

**What is Site data?** **Setup > Site data** lists the rows of the table `ezsite_data`, such as the database schema
version. It is read-only and needs the same policy.

### Pitfalls

- **The command line reports another PHP.** `exp:system:info` reports the command-line PHP: OPcache is usually off
  there, and `max_execution_time` is "no limit". That is not a fault.
- **Measure sizes is slow on big sites.** It reads every file of `var/` once, within ten seconds. On a lab it is
  quick; on a live site with millions of files, say so before pressing it.
- **Pasting the report in a public forum.** It is masked, but it still describes the installation. Send it to support,
  not to the world.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Setup > System information**.
2. Read the banner: which server answered, and which PHP version and SAPI does the badge show?
3. Under **Health checks**, note every check that is not **OK**, with its advice.
4. In the **Overview**, read the **Database** card: which engine and how many tables?
5. Read the **Cronjobs** card: when did cronjobs last run?
6. Open the folded part **Siteaccesses**. Find the default siteaccess and the rule that chose the siteaccess of this
   page.
7. Open **Active extensions** and count the extensions.
8. Press **Download report (.txt)** and open the file. Search it for the word `password`: what do you find?
9. In a terminal, in the installation root, run:

   ```bash
   php bin/php/console exp:system:info
   ```

10. Compare the PHP and OPcache lines of the terminal report with the page. Explain the difference to your neighbor.
11. Open **Setup > Site data** and find the row that holds the installed schema version.

## Expected result

- Learners name the server (Velocity on the lab) and the PHP version.
- The list from step 3 typically has a note about mail and a warning about cronjobs on a new installation.
- Step 6 shows `admin` as the siteaccess of this page, reached by its URI element; the default siteaccess is `site`.
- Step 8: secrets appear only as "(set, hidden)" or "(not set)", never as a value.
- Step 10: the terminal report shows the command-line SAPI `cli`, often with OPcache off.
- The trainer checks by asking each learner for one health check and its advice.

![System information with the health checks of PHP, the database and the server](../../../images/trainers/sa-system-info.webp)

## Quiz

1. Which policy does a user need to open **System information**?
2. Why can the page show different PHP settings for the same installation?
   A. The page caches old values B. Each server has its own PHP process and settings C. The database stores them
   D. Only administrators see real values
3. What does a **Note** about `Transport=file` mean?
4. Name two kinds of values the support report never contains.
5. What does `exp:system:info` return when a check fails, and why is that useful?

<details>
<summary>Answers</summary>

1. `setup/system_info` ([System information](../../../../guides/system-information.md#1-opening-the-page)).
2. **B.** Apache with PHP-FPM, Velocity and the command line each run their own PHP.
3. Mail is written to files and nothing is sent; set `[MailSettings]` on a live site.
4. For example passwords, keys, tokens, session ids, credentials in addresses, full server paths
   ([What is never shown](../../../../guides/system-information.md#7-what-is-never-shown)).
5. It exits with 1, so a deploy script can stop when the installation is unhealthy.

</details>

## Further reading

- [System information](../../../../guides/system-information.md): every check, card and report field.
- [Database support](../../../database-support.md): what the Database card's engine and version mean.
- [Operating system support](../../../os-support.md): the servers and PHP SAPIs the page can show.

## Related pages

- [Module 1: Getting oriented as a site administrator](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
