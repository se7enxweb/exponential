---
title: Run a proof of concept on SQLite in one command
audience: government, project-manager, admin
category: public-sector
order: 70
summary: Step by step, install Exponential on a test machine with one command on SQLite, sign in, try the tasks your requirements name, and remove it afterwards - so a public body sees the product itself before or during an evaluation.
keywords: proof of concept, PoC, test installation, SQLite, exp:install, one command, evaluation, try Exponential, procurement task
type: help
---
# Run a proof of concept on SQLite in one command

**Who:** an IT specialist on the evaluation team, or anyone with a terminal. **When:** before the tender (to know the
product) or during evaluation. The command comes from [Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)
and [7. The console install](../../../../install/07-console-install.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What to try](#what-to-try)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- A test machine (not production) with PHP 8.0 or later, Composer 2 and Git ([2. Requirements](../../../../install/02-requirements.md#21-the-checklist)).
- The code, cloned and with its Composer packages installed ([3. Getting the code](../../../../install/03-getting-the-code.md)).
- No database server is needed: SQLite is the default.

## Steps

1. Check first without installing anything:

   ```bash
   ./console exp:install --dry-run
   ```

2. Install. Without options this installs the multisite package on SQLite in `eng-US`, with the siteaccesses `site`
   and `admin`:

   ```bash
   ./console exp:install --apply
   ```

3. Read the summary: it prints the site and administration addresses and the user name. The administrator password is
   generated (24 characters), printed once and written to `var/log/initial-admin-password`, readable by the owner only.
4. Serve the site, for example with Velocity or PHP's built-in server ([8. Serving the site](../../../../install/08-serving-the-site.md)).
5. Sign in to the administration and change the password; then delete `var/log/initial-admin-password`.
6. If something fails, read `var/log/setup.log`, which lists every step and its errors.

## What to try

Take the tasks from your requirements, for example:

- create, preview and publish an article; compare two versions ([Publishing and versions](../../../publishing-and-versions.md));
- add a translation; switch the interface language ([Languages](../../../languages.md));
- set up an approval workflow ([Workflows](../../../../guides/workflows.md));
- create a role limited to one section ([Roles and policies](../../../../guides/roles-and-policies.md));
- switch to the admin4 design and try it with the keyboard only ([The admin4 design](../../../../features/6.0/admin4-design.md));
- look at the audit trail and **Setup > System information**;
- run an export ([Run an exit drill](run-an-exit-drill.md)).

The proof-of-concept plan in the evaluation part has a structured version ([Proof-of-concept plan](../../procurement/evaluation/proof-of-concept-plan.md)).

## Result

A working test installation your team used itself, and notes per task: done, how, and any gap.

## Pitfalls

- Using real personal data in a test installation.
- Judging the public design of the demo package as your future site: your site's design will be built for you.
- Leaving the test installation reachable from the internet with a known password.
- Running `exp:install` over an existing installation: it refuses unless you add `--force`.

## Related pages

- [Procurement and training tasks](README.md)
- [Getting started](../../../../guides/getting-started.md)
- [Install from zero (demo script)](../../../press/demo-script-install-from-zero.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
