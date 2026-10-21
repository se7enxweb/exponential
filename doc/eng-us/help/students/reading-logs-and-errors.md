---
title: Reading logs and errors
audience: student
category: learning
order: 21
summary: How a learner finds out what went wrong on a study site - telling a permission page from a real error, the error reference, the log files in var/log and the log viewer, syntax checks for PHP and templates before you look in the browser, and how to turn an error into a good question or bug report.
keywords: logs, error, error.log, var/log, log viewer, error reference, debug, php -l, eztemplatecheck, troubleshooting, white page, 500, learner
type: help
---
# Reading logs and errors

Errors are part of learning. The sooner you can read them, the faster you learn. This page shows where Exponential
writes what went wrong and how a learner uses it.

## Contents

1. [Is it an error at all?](#is-it-an-error-at-all)
2. [The error page and its reference](#the-error-page-and-its-reference)
3. [The log files](#the-log-files)
4. [The log viewer in the administration](#the-log-viewer-in-the-administration)
5. [Check before you look](#check-before-you-look)
6. [A routine for any error](#a-routine-for-any-error)
7. [From error to question](#from-error-to-question)

## Is it an error at all?

Some pages look like errors but say that something is not allowed or not there
([When something goes wrong](../when-something-goes-wrong.md#first-is-it-an-error)):

| The page says | It means |
|---|---|
| *The requested page could not be displayed. (1)* or *Your account does not have the proper privileges* | Your role may not open this page |
| *The requested page could not be displayed. (20)* | The address is wrong, or that part is not installed |
| *Possible edit conflict* | A draft of the item exists already |
| A grayed-out button | You may not use it here or now |

On your own study site as `admin`, the first one should not appear; for a test account it is often the expected
result of a role exercise ([project 9](project-09-roles-for-a-restricted-editor.md)).

## The error page and its reference

A real error shows a page such as **Something went wrong on our side** with a **reference** at the bottom. The error
is already logged; the reference finds that moment in the logs
([When something goes wrong](../when-something-goes-wrong.md#when-something-really-went-wrong),
[Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)).

Note the reference, the time, the address and what you did just before.

## The log files

The logs are in `var/log/` of the installation. The ones learners meet most:

| File | What it holds | Source |
|---|---|---|
| `var/log/error.log` | Errors; the first place to look after a white page or a 500 | [Getting started](../../guides/getting-started.md#8-when-something-goes-wrong) |
| `var/log/warning.log` | Warnings, for example about caches | [Caches](../../guides/caches.md#6-usual-questions) |
| `var/log/setup.log` | The progress of an installation | [Getting started](../../guides/getting-started.md#31-the-one-command) |
| `var/log/security.log` | Security events, for example demo mode lines | [Demo mode FAQ](../demo-mode-faq.md#how-do-i-know-it-is-on) |

On the command line, the last lines of a log:

```bash
tail -n 50 var/log/error.log
```

## The log viewer in the administration

**Setup > Log files** shows the logs in the browser: choose a file, follow new lines as they are written, filter by
level, search for a word such as an error reference, and open the **Error digest** for an overview. It needs the
`setup/logs` policy; personal data such as IP addresses is masked by default
([How do I read the log files?](../manual/how-do-i-read-the-log-files.md)).

## Check before you look

Many errors can be caught before you open the browser:

| You changed | Check | Expected | Source |
|---|---|---|---|
| A PHP file | `php -l <file>` | `No syntax errors detected in <file>` | [Extensions](../../guides/extensions.md#61-the-smallest-working-extension-by-hand) |
| A template | `php bin/php/eztemplatecheck.php -s<siteaccess> <file> --allow-root-user` | `Template file valid: <file>` | [Templates and design](../../guides/templates-and-design.md#5-check-the-syntax-before-you-look-at-the-browser) |
| Classes of an extension | `php bin/php/checkclasses.php --quiet-ok --allow-root-user` | No output when everything loads | [Extensions](../../guides/extensions.md#63-the-handler-wizard-step-by-step) |
| A setting | `php bin/php/console exp:ini where <file/Block/Variable> <siteaccess>` | The value in effect | [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md#ask-the-installation) |

Template path comments show which template wrote which markup
([Templates and design](../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup)).

## A routine for any error

1. **Read the message.** Copy it exactly into your [study journal](keeping-a-study-journal.md).
2. **Read the log.** `var/log/error.log`, the newest lines.
3. **Undo the last change** in your mind: what did you change just before? Most errors come from the last step.
4. **Run the checks** above for the files you touched.
5. **Rule out caches** ([Understanding caches](understanding-caches.md#cache-problem-or-real-error)).
6. **Search the help** for the message, then the issue trackers.
7. **Ask**, with the facts you collected.

## From error to question

A good question or bug report has: what you did, step by step; what you expected; what happened, with the exact
message; and the environment (Exponential and PHP versions, the web server, the database). The help's **Report an
error** page and `php bin/php/console exp:info --plain --no-logo` give you a summary of the installation that leaves out
passwords, keys and server paths ([When something goes wrong](../when-something-goes-wrong.md#a-good-bug-description)).

See [Asking for help and study groups](asking-for-help-and-study-groups.md) and
[Bugs you find while learning](bugs-you-find-while-learning.md).

## Related pages

- [When something goes wrong](../when-something-goes-wrong.md)
- [Understanding caches](understanding-caches.md)
- [Troubleshooting](../../install/12-troubleshooting.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
