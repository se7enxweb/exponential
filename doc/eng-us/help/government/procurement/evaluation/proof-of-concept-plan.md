---
title: A proof-of-concept plan for evaluating Exponential
audience: government, project-manager, admin
category: public-sector
order: 80
summary: A proof-of-concept plan that lets a public body test Exponential on its own machine in days, starting with a one-command SQLite installation - scope, environment, test cases with expected results, data rules, and the report the panel writes.
keywords: proof of concept, PoC, pilot, trial installation, SQLite, exp:install, test plan, test cases, evaluation, acceptance criteria
type: help
---
# A proof-of-concept plan for evaluating Exponential

This page is for the technical lead of an evaluation panel who wants to test Exponential before or during a tender,
on an environment the public body controls. Because the software is free and installs in one command on SQLite,
a proof of concept needs no license, no database server and no contract. The steps come from
[Installing in one command](../../../../features/6.0/install-in-one-command.md) and
[Getting started](../../../../guides/getting-started.md).

## Contents
1. [Scope and roles](#scope-and-roles)
2. [The environment](#the-environment)
3. [Installing in one command](#installing-in-one-command)
4. [Test cases](#test-cases)
5. [Data rules for the proof of concept](#data-rules-for-the-proof-of-concept)
6. [The proof-of-concept report](#the-proof-of-concept-report)
7. [Cleaning up](#cleaning-up)

## Scope and roles

- **Goal**: confirm, on your own machine, that the requirements the bids rely on work as documented.
- **Not the goal**: building the final site, or testing the bidder's public design (that is part of acceptance).
- **Roles**: a technical lead who installs and runs commands; an editorial tester; an accessibility tester; a
  security reviewer. Each runs the test cases of their area.
- **Time box**: fix it before you start, and stop when it ends.

## The environment

| Need | From [Requirements](../../../../install/02-requirements.md) |
|---|---|
| A machine or virtual machine you may wipe | AlmaLinux 9 and Ubuntu are tier 1, "tested here" ([Operating system support](../../../os-support.md#tier-1-tested-here)) |
| PHP 8.0 or newer (for the built-in Velocity server too) | check with `php -v` |
| Composer 2 and Git | to get the code |
| No database server | SQLite 3 is the default database |

## Installing in one command

```bash
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
composer install
./console exp:install --dry-run          # checks configuration and packages, installs nothing
./console exp:install --url=http://localhost:8087
./console exp:velocity start --engine=php
```

The installer prints the site and administration addresses, the user name `admin` and a generated password of 24
characters, which it also writes to `var/log/initial-admin-password`. Open the administration, sign in, change the
password with **Change password**, then delete that file. The `php` engine listens on port 8087 by default
([Getting started, step 4](../../../../guides/getting-started.md#4-see-the-site)).

If your hosting plan names another database, repeat the installation with `--db=pgsql`, `--db=mysql` or another
engine from the table in [Installing in one command](../../../../features/6.0/install-in-one-command.md#choose-a-database),
giving the password through the environment variable `EXP_INSTALL_DB_PASSWORD`.

## Test cases

| # | Test | Expected result | Source |
|---|---|---|---|
| T1 | Create, preview, publish a page | published version visible on the site | [Content model and editing](../../../../guides/content-model-and-editing.md) |
| T2 | Edit again, compare versions, restore | a new draft made from the older version, then published | [Versions of an object](../../../../guides/content-history.md) |
| T3 | Create a role limited to one section; sign in as a test editor | editor sees and edits only that section | [Roles and policies](../../../../guides/roles-and-policies.md) |
| T4 | Audit trail shows T1 to T3 | events listed in the audit console | [Audit trail](../../../../features/6.0/audit-trail.md) |
| T5 | Verify the audit chain: `./console exp:audit verify` | the chain verifies | [Security and audit](../../../../guides/security-and-audit.md) |
| T6 | Admin with keyboard only | skip link, visible focus, all actions reachable | [Accessibility standards](../../../../standards/accessibility.md) |
| T7 | Add a second content language and translate T1 | both languages published | [Content languages](../../../../guides/content-languages.md) |
| T8 | Maintenance mode on and off | visitors see the maintenance page | [Maintenance mode](../../../../guides/maintenance-mode.md) |
| T9 | Backup and restore on a second machine | restored site shows T1 | [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) |
| T10 | Export a site archive | ZIP with `manifest.json` and one CSV per class | [Leaving Exponential](../../../../install/migration/leaving-exponential.md) |
| T11 | Run the upgrade check | report of files that differ from the release | [Upgrade check](../../../../guides/upgrade-check.md) |
| T12 | Read the help in the administration | help opens from the "?" button | [Help system](../../../../features/6.0/help-system.md) |

Add test cases for the requirements of your tender, and record for each one: date, tester, result (pass, fail,
partly), evidence (screenshot of invented data, log line, file listing).

## Data rules for the proof of concept

- Use invented content and invented accounts only, with addresses at `example.invalid` or your own test domain.
- Never import real personal data of citizens or staff into a proof of concept.
- Do not expose the test installation to the internet without hardening; it runs with a demo password policy and no
  TLS ([Security hardening](../../../security-hardening.md)).

## The proof-of-concept report

```text
Proof of concept - <tender or project>         Version tested: Exponential <version / commit>
Environment: <OS, PHP version, database, engine>
Period: <dates>      Testers: <names and roles>
Results: T1 pass ... T12 pass/fail - with evidence references
Deviations from the documentation: <list, with links>
Open questions for bidders: <list>
Conclusion: <facts only; scores are given in the scoring sheet>
```

## Cleaning up

Stop the server with `./console exp:velocity stop --engine=php`, then remove the test machine or the installation
directory as your IT rules require. The proof of concept held only invented data, so no personal data needs special
handling.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Installing in one command](../../../../features/6.0/install-in-one-command.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
