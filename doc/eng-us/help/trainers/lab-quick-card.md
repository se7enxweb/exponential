---
title: "Lab quick card"
audience: trainer, admin
category: training
order: 6
summary: Every command a trainer needs to build, serve, check, reset and remove a training installation, on one printable card, with the page that explains each.
keywords: lab, quick card, cheat sheet, commands, exp:install, exp:velocity, ezcache, maintenance, resetuserpassword, training installation, printable
type: help
---
# Lab quick card

One card with the commands of a training lab, in the order a course uses them. Run them in the installation root,
as the user who owns the files. Each line names the page that explains it. Print it for yourself and your helpers.

## The card

```text
EXPONENTIAL TRAINING LAB                                   run in the installation root, not as root

CHECK THE MACHINE
  php -v ; php -m                        PHP 8.0+ (Velocity too); intl, xsl, sqlite3, mbstring ...
  composer --version ; git --version

GET THE CODE (a fixed release)
  git tag -l 'v6.*' --sort=version:refname | tail -3
  composer create-project se7enxweb/exponential:<tag> <dir>          editor and admin courses
  git clone https://github.com/se7enxweb/exponential.git <dir>        developer courses
    git switch -c training <tag> ; composer install

INSTALL (SQLite, one command)
  php bin/php/console exp:install --print   --url=http://127.0.0.1:8087
  php bin/php/console exp:install --dry-run --url=http://127.0.0.1:8087
  php bin/php/console exp:install           --url=http://127.0.0.1:8087 [--package=sevenx_multisite_clean]
  cat var/log/initial-admin-password        then sign in, change it, and: rm var/log/initial-admin-password

SERVE
  php bin/php/console exp:velocity start | status | stop
  php bin/php/console exp:velocity config set PHPServerSettings Port 8091     a second site on one machine

WHEN SOMETHING BREAKS
  tail -n 50 var/log/error.log           the reference ERR-... of an error page is in here
  php bin/php/maintenance.php status | off
  php bin/php/ezcache.php --clear-tag=template | --clear-tag=ini | --clear-all
  php bin/php/resetuserpassword.php -u admin -g --allow-root-user            (as root)

RESET FOR THE NEXT GROUP
  exp:velocity stop ; cp -a <golden>/storage/. var/storage/ ; php bin/php/ezcache.php --clear-all
  or: pwd (check!) ; exp:install --dry-run ... ; exp:install --force ...

AFTER THE COURSE
  stop servers; remove learner accounts on shared sites; delete lab directories you created
```

## Where each command is explained

| Commands | Page |
|---|---|
| Checking a machine | [Lesson 2.1: Requirements for a training machine](train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md), [Requirements](../../install/02-requirements.md) |
| Getting the code | [Lesson 2.2: Getting the code for a class](train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md), [Getting the code](../../install/03-getting-the-code.md) |
| Installing | [Lesson 2.3: One installation per learner](train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), [The console install](../../install/07-console-install.md) |
| Serving | [Lesson 2.4: Serving the lab sites](train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md), [Serving the site](../../install/08-serving-the-site.md) |
| Fixing | [Lesson 4.4: Troubleshooting in class](train-the-trainer/module-04-delivering/troubleshooting-in-class.md), [Troubleshooting](../../install/12-troubleshooting.md) |
| Resetting | [Lesson 2.6: Resetting the lab](train-the-trainer/module-02-building-the-lab/resetting-the-lab.md) |
| Cleaning up | [Lesson 4.5: Security and privacy in class](train-the-trainer/module-04-delivering/security-and-privacy-in-class.md) |

> **Warning**
> `exp:install --force` replaces the database and the settings of the installation it runs in. Run it only in a
> directory you created for training, and check `pwd` first.

## Related pages

- [Trainers: start here](trainers-start-here.md)
- [Trainer toolkit](trainer-toolkit.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
