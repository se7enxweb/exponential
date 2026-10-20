---
title: "Lesson 2.8: Choosing the site package for a class"
audience: trainer, admin
category: training
order: 28
summary: Pick the site package that fits the course, sevenx_multisite with demo content for tours and editor courses, a _clean package when learners build from an empty site, and install it with exp:install --package. 30 minutes.
keywords: site package, sevenx_multisite, sevenx_multisite_clean, sevenx_site, demo content, clean install, package, training lab, exp:install --package
type: help
---
# Lesson 2.8: Choosing the site package for a class

Every installation starts from a **site package**, which decides the content types, the demo content and the
settings of the new site. An editor course benefits from a site full of articles and images to work with; a developer
course that builds a site from scratch is better served by an empty one. This lesson shows the choices and how to
install each.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Trainers who prepare labs |
| Prerequisites | [Lesson 2.3](one-install-per-learner.md) |

## Learning objectives

After this lesson, learners can:

- name the site packages most likely to be used and what each contains;
- install a chosen package with `exp:install --package=<name>`;
- explain what a `_clean` package leaves out;
- match a package to a course.

## Trainer's notes

### How to present it

1. Show the table from [Choosing an install method, 4.3](../../../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)
   (5 minutes):
   - `sevenx_multisite`: the 7x MultiSite default installation, with the content classes and demo content (the
     default of `exp:install`);
   - `sevenx_multisite_clean`: the same site without demo content;
   - `sevenx_site` and `sevenx_site_clean`: a simpler site, with or without demo content;
   - older packages (`ezwebin_site`, `ezflow_site`, `ezdemo_site`, `plain_site`) for sites built on them.
2. Match to courses (5 minutes): Editors 101 and the layouts course: `sevenx_multisite` (real-looking content).
   Developers building a site: a `_clean` package. Migration exercises: a `_clean` package as the target.

### Common questions

**Where do the packages come from?** From the remote package index set in `settings/package.ini`
(`[RepositorySettings] RemotePackagesIndexURL`), or from the local repository under `var/storage/packages/` when they
were imported already. Without a network, an offline installation uses an `index.xml` in the local repository.

**Can I make my own training package?** Yes, a site package is an Exponential package of type `site`. The course
Multisite and site packages teaches it.

### Pitfalls

- **No network in the classroom.** The install downloads the packages. Install in advance or prepare an offline
  repository.
- **Demo content with real-looking names.** The demo content is invented; keep it that way when you add your own.

## Hands-on exercise

1. In a new code directory, print the configuration for a clean site:
   `php bin/php/console exp:install --print --package=sevenx_multisite_clean --url=http://127.0.0.1:8087`.
   Find `Site_package=sevenx_multisite_clean` in the output.
2. Run a dry run with the same options and wait for "Dry-run completed".
3. Install it (same options without `--print`/`--dry-run`).
4. Start the server, sign in and open **Content structure**. Compare it with the `sevenx_multisite` site from lesson
   2.3: count the items below the top folder in each.
5. Decide which package you will use for each course you plan, and write it on the lab sheet.

## Expected result

- The clean installation has the same structure of top folders but far fewer items, without the demo articles and
  images.
- The lab sheet names a site package for each planned course.

## Quiz

1. Which site package does `exp:install` install by default?
2. What does a `_clean` package leave out?
3. Which option chooses the site package?
4. Which package would you use for a course in which developers build a site from scratch?

<details>
<summary>Answers</summary>

1. `sevenx_multisite`.
2. The demo articles and images; the structure is the same.
3. `--package=<name>`.
4. A `_clean` package, for example `sevenx_multisite_clean`.

</details>

## Further reading

- [Choosing an install method](../../../../install/04-choosing-an-install-method.md): site packages and the installer.
- [Packages](../../../../guides/packages.md): packages in the administration.
- [Getting started](../../../getting-started.md): what learners see after the install.

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
