---
title: "Lesson 6.1: One installer engine, three front ends"
audience: trainer, admin, developer
category: training
order: 61
summary: Learners explain the step classes every install method runs, compare the setup wizard, the kickstarter and exp:install, and choose a method for a given situation with the decision guide. 40 minutes.
keywords: multisite and site packages, install methods, setup wizard, kickstarter, exp:install, step classes, eZStepData, CreateSites, CheckValidity, maintenance mode, setup.log, decision guide
type: help
---
# Lesson 6.1: One installer engine, three front ends

Exponential installs in three ways that all end in the same kind of installation: the setup wizard in a browser, the
kickstarter driven by `kickstart.ini`, and the one-command `exp:install`. Under all three runs one engine: the step
classes of `kernel/setup/steps/`, and a site package whose install scripts shape the new site. This lesson gives
learners that picture and the decision guide, before they use the command-line methods in lessons 6.2 and 6.3.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 6: Installing sites with the kickstarter and exp:install](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Administrators, developers and hosts |
| Prerequisites | [Lesson 5.1](../module-05-site-packages/what-a-site-package-is.md) |

## Learning objectives

After this lesson, learners can:

- list the main installer steps and say which one installs the site package;
- compare the three methods by entry point, source of answers, dry run and guard against overwriting;
- recommend a method for a situation with the decision guide;
- name the logs an installation writes and the maintenance page it shows.

## Trainer's notes

### How to present it

Source: [4. Choosing an install method](../../../../install/04-choosing-an-install-method.md).

1. **The steps** (6 minutes). Each step is a class `eZStep<Name>`, in the order of `eZStepData::$StepTable`:
   Welcome, SystemCheck, SystemFinetune, EmailSettings, DatabaseChoice, DatabaseInit, LanguageOptions, SiteTypes,
   PackageLanguageOptions, SiteAccess, SiteDetails, SiteAdmin, Security, Registration, CreateSites, Final.
   `CreateSites` writes the database, the settings and installs the site package; it calls the package's install
   scripts (lesson 5.1).
2. **The front ends** (8 minutes). Put the comparison on the projector:

   | | Setup wizard | Kickstarter | Console install |
   |---|---|---|---|
   | Entry point | any page while `CheckValidity=true` | `php bin/php/console exp:kickstarter run --force` | `php bin/php/console exp:install` |
   | Answers from | the forms | `kickstart.ini` | command-line options with defaults |
   | Dry run | no | `run --dry-run` | `--dry-run` and `--print` |
   | Guard | starts only while `CheckValidity=true` | `--force` needed for `CreateSites` | refuses over an installation unless `--force` |
   | Best for | a first install, hosts without shell | repeatable installs from a reviewed file | the fastest install, scripts, CI, demos |

   `exp:install` is a front end to the kickstarter: it writes a temporary `kickstart.ini` from its options and runs
   the same steps.
3. **During the run** (3 minutes). Every method shows the maintenance page to visitors while it works, and writes
   `var/log/setup.log`; the kickstarter also `var/log/kickstart.log`, `exp:install` a masked
   `var/log/exp-install-<date>.ini`.
4. **The decision guide** (3 minutes). No shell: the wizard. A person must review every answer first: the
   kickstarter. Part of a script or pipeline: `exp:install`. Otherwise `exp:install` is the shortest path and the
   wizard the most explained one.

### Common questions

**Do the three methods give different sites?** No. They run the same steps and the same site package.

**Which does the training lab use?** Train the trainer recommends `exp:install` for one installation per learner, and
the kickstarter when the same lab is rebuilt often.

**What about the multi-site installer of the site package?** It is the site package's part of every install, not a
fourth method.

**Can the wizard read `kickstart.ini`?** Yes; it pre-fills and skips pages from it (chapter 5).

### Pitfalls

- **Treating `exp:install` as harmless.** Its default `--db-action` is `remove`; with `--force` over an existing
  installation it empties the database. Lessons 6.2 and 6.4 show the guards.
- **Mixing up `CheckValidity`.** Setting it back to `true` on a live site sends every visitor to the wizard.
- **Forgetting the logs.** When something fails, `var/log/setup.log` is the first place to read.

## Hands-on exercise

A discussion and research exercise in pairs, with chapter 4 of the installation guide open, plus two read-only
commands on the training installation.

1. Print the kickstarter's step table without installing:
   `php bin/php/console exp:kickstarter run --list-steps`. If it says `kickstart.ini` is not found, note the message:
   the command needs the file even for this.
2. Print what `exp:install` would use, without installing: `php bin/php/console exp:install --print`.
3. In the output of step 2, find the site package, the database type and the editor siteaccess.
4. For each situation, choose a method and give the reason:
   a. A host without shell access.
   b. A training lab rebuilt every week the same way.
   c. A container image that installs at start-up.
   d. A first production install done by hand by an administrator who wants each decision explained.
   e. A server without internet access, with the packages already imported.
5. Compare with another pair, then with section 4.5 of the guide.
6. Name the step that installs the site package and the error code that reports a stopped post-install.

## Expected result

- Step 1 prints the sixteen steps (Welcome to Final, `CreateSites` marked hidden) or the "not found" message with the
  command that writes the file.
- Step 2 prints the configuration with passwords masked; the package is `sevenx_multisite`, the type `sqlite3`.
- Step 4: a. the wizard; b. the kickstarter; c. `exp:install`; d. the wizard, or `exp:install` with explicit options;
  e. the kickstarter or the wizard with the local packages.
- Step 6: `CreateSites`; `EZSW-080`.
- The trainer checks step 4 against [4.5](../../../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation).

## Quiz

1. What do the three install methods have in common?
2. Which method needs a file written first?
   A. The wizard B. The kickstarter C. `exp:install` D. None
3. What protects an existing installation from `exp:install`?
4. Which log do all three methods write?
5. Which method for a CI pipeline, and why?

<details>
<summary>Answers</summary>

1. They run the same step classes and install a site package
   ([4.1](../../../../install/04-choosing-an-install-method.md#41-one-installer-three-front-ends)).
2. **B.** `kickstart.ini`, which `exp:kickstarter ini` writes.
3. It refuses when `settings/override/site.ini.append.php` holds `[DatabaseSettings]`, unless `--force` is given.
4. `var/log/setup.log`.
5. `exp:install`: options or environment variables, an exit status and no file to template.

</details>

## Further reading

- [4. Choosing an install method](../../../../install/04-choosing-an-install-method.md).
- [5. The setup wizard](../../../../install/05-setup-wizard.md).
- [Choosing an install method](../../../creators/website-creators/choosing-an-install-method.md): the website
  creator's view.
- [Provisioning: overview](../../../hosting/provisioning/README.md): the host's view.

## Related pages

- [Module 6: Installing sites with the kickstarter and exp:install](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
