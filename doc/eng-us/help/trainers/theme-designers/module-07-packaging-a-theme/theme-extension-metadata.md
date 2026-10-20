---
title: "Lesson 7.1: The theme's metadata: ezinfo.php, extension.xml, README and LICENSE"
audience: trainer, designer, developer
category: training
order: 71
summary: Learners complete the metadata of their theme extension so that name, version, copyright, license and website agree in ezinfo.php, extension.xml and the LICENSE file, check the files, and find their theme on the About page. 45 minutes.
keywords: theme design, theme extension, ezinfo.php, extension.xml, metadata, version, copyright, license, Info_url, About page, ezinfo/about, xmllint, php -l, README, LICENSE
type: help
---
# Lesson 7.1: The theme's metadata

A theme that others install must say who it is: its name, version, copyright, license and website. Exponential reads
that from two small files, `ezinfo.php` and `extension.xml`, and shows it on the About page and in **Setup >
Extensions**. In this lesson learners complete the files the wizard wrote and make them agree with each other and with
the `LICENSE` file.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 7: Packaging a theme](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish themes |
| Prerequisites | [Lesson 2.1](../module-02-templates-overrides-and-the-pagelayout/your-own-design-extension.md): `training_theme` created with the wizard |

## Learning objectives

After this lesson, learners can:

- name the keys of `ezinfo.php` and the elements of `extension.xml`, and which must be equal;
- explain why `extension.xml` wins when the two disagree;
- check both files (`php -l`, `xmllint --noout`) and see the result on the About page;
- write a README that says what the theme is and how to switch it on.

## Trainer's notes

### How to present it

Sources: [ezinfo.php](../../../designers/extensions/ezinfo-php.md),
[extension.xml](../../../designers/extensions/extension-xml.md) and
[Keeping every version field in sync](../../../designers/extensions/keeping-version-fields-in-sync.md).

1. **ezinfo.php** (4 minutes). The class is the folder name followed by `Info`, `info()` is `static`, and it returns
   `Name`, `Version` (without a `v`), `Copyright`, `License` (exactly what `LICENSE` says) and `Info_url`, optionally
   `Author`.
2. **extension.xml** (4 minutes). `<software><metadata>` with `<name>`, `<version>`, `<copyright>`, `<license>`,
   `<info_url>`. The About page reads it first when it has a `<metadata>` block, so a wrong value here wins over a
   correct one in `ezinfo.php`.
3. **Values never to write** (2 minutes). `//autogentag//` or `dev-master` as the version, a license that differs from
   `LICENSE`, a website that is not the theme's own.
4. **The rule for releases** (3 minutes). Every release raises the version in both files to exactly the release's
   version, in a commit before the tag; module 8 shows why tags are permanent.

### Common questions

**Which license should my training theme have?** The one you chose in the wizard; module 8 discusses the choice. The
wizard wrote the `LICENSE` file for it.

**Where does the About page show it?** In the extensions table of `/ezinfo/about` (columns **Order**, **Name**,
**Identifier**, **Website** and the version and license), and on the extension's card in **Setup > Extensions**.

**What if the theme needs another extension?** A `<dependencies>` block in `extension.xml`
([Declaring dependencies](../../../designers/extensions/dependencies-in-extension-xml.md)).

### Pitfalls

- **Two versions.** `ezinfo.php` says 1.0.1, `extension.xml` 1.0.0: the About page shows 1.0.0. Search both.
- **A class name that does not match the folder.** `training_themeInfo` for `extension/training_theme`.
- **Copyright of someone else's work left out.** If the theme contains third-party files, their holders belong in the
  copyright line or the credits (module 8).

## Hands-on exercise

On your training installation, in `extension/training_theme/`.

1. Open `ezinfo.php` and `extension.xml`. Write the five values of each side by side on a sheet.
2. Mark every value that differs between the two files, and any value that is empty or a leftover of a build script.
3. Set the version in both files to `0.1.0`, the copyright to `Copyright (C) 2026 <your name>`, and the website to the
   address where you would publish the theme (for a course, a repository address of your own organization).
4. Compare the license in both files with the first lines of `LICENSE`; make them identical.
5. Check both files:

   ```bash
   php -l extension/training_theme/ezinfo.php
   xmllint --noout extension/training_theme/extension.xml
   ```

6. Open the README the wizard wrote. Make sure it says, in this order: what the theme is, a short description of the
   look, how to activate it, the `SiteDesign` line for a siteaccess, and
   the license.
7. Open `/ezinfo/about` in the administration and find `training_theme` in the extensions table.
8. Change the version in `extension.xml` only to `0.1.1`, reload the About page, then set it back to `0.1.0`.

## Expected result

- Step 5: `No syntax errors detected in extension/training_theme/ezinfo.php`, and no output from `xmllint`.
- Step 7: the row shows the name, version `0.1.0`, the license and the website.
- Step 8: the About page showed `0.1.1`, proving that `extension.xml` is read first.
- The trainer checks two learners' rows on the About page.

![The About page reads each theme's version and license from its metadata](../../../images/trainers/td-about.webp)

## Quiz

1. Which file does the About page read first when it has a `<metadata>` block?
2. What is the class name in `ezinfo.php` for the extension `training_theme`?
3. Which of these is an acceptable version?
   A. `//autogentag//` B. `dev-master` C. `v0.1.0` D. `0.1.0`
4. Which command checks that `extension.xml` is well formed?
5. Which three places must name the same license?

<details>
<summary>Answers</summary>

1. `extension.xml` ([extension.xml](../../../designers/extensions/extension-xml.md)).
2. `training_themeInfo`.
3. **D.** Without a `v`, never a value left by a build script.
4. `xmllint --noout extension/training_theme/extension.xml`.
5. `ezinfo.php` (`License`), `extension.xml` (`<license>`) and the `LICENSE` file (and `composer.json` when the theme
   has one).

</details>

## Further reading

- [ezinfo.php: what the About page reads](../../../designers/extensions/ezinfo-php.md).
- [extension.xml: the packaged description](../../../designers/extensions/extension-xml.md).
- [A README that explains and sells](../../../designers/distribution/readme-that-explains-and-sells.md) and
  [A README for a theme](../../../designers/extensions/readme-for-a-theme.md).
- [The LICENSE file](../../../designers/extensions/license-file.md).

## Related pages

- [Module 7: Packaging a theme](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
