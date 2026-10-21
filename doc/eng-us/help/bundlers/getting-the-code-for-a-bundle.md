---
title: Getting the code for a bundle
audience: bundler, developer
category: partnership
order: 60
summary: The three documented ways to get Exponential's code (git clone and composer install, composer create-project, composer require into your own project), which one suits which kind of bundle, and how to check the result before you build on it.
keywords: get the code, git clone, composer install, create-project, composer require, --no-dev, --prefer-source, --keep-vcs, ezpublish-legacy, LegacyKernelInstaller, allow-plugins, bundle build
type: help
---
# Getting the code for a bundle

This page is for whoever writes the build of a bundle. The installation guide documents three ways to get the code
([3.2 Three ways to get the code](../../install/03-getting-the-code.md#32-three-ways-to-get-the-code)); each suits a
different kind of bundle.

## Contents

1. [The three ways](#the-three-ways)
2. [Which way for which bundle](#which-way-for-which-bundle)
3. [Build commands](#build-commands)
4. [Check the result](#check-the-result)
5. [Common mistakes](#common-mistakes)

## The three ways

| Way | Gives you | Source |
|---|---|---|
| A. `git clone` and `composer install` | the kernel and every se7enxweb extension as Git clones with their history | [3.2 A](../../install/03-getting-the-code.md#a-git-clone-and-composer-install-recommended) |
| B. `composer create-project` | release archives without history (clones with `--prefer-source --keep-vcs`) | [3.2 B](../../install/03-getting-the-code.md#b-composer-create-project) |
| C. `composer require` into an empty directory | the kernel as a package of your own project, with your own `composer.json` | [3.2 C](../../install/03-getting-the-code.md#c-composer-require-into-an-empty-directory) |

## Which way for which bundle

| Bundle | Way | Why |
|---|---|---|
| Container or server image of a fixed release | B, with a version | archives are smaller; the image is rebuilt for every update anyway |
| Agency starter kit | C | your `composer.json` and `composer.lock` are the specification of the kit |
| A bundle whose recipients develop on it | A | Git shows what changed and lets them update with Git ([Develop with Git](../develop-with-git.md)) |
| Hosting panel installer | B or a prepared archive of B | fast, no history needed in every account |
| Distribution package | B or a source archive of the tag | the package manager does the updating |

## Build commands

A pinned release, archives, no test tools (way B):

```bash
composer create-project se7enxweb/exponential:v6.0.14 exponential --no-dev
```

`create-project` "treats the downloaded `composer.json` as the root package, so its `config.allow-plugins` ... and its
`post-install-cmd` script apply: the class maps are generated at the end (`php bin/php/ezpgenerateautoloads.php`). On
a production server add `--no-dev`, which leaves out the test tools" ([3.2 B](../../install/03-getting-the-code.md#b-composer-create-project)).

Your own project (way C):

```bash
mkdir exponential
cd exponential
composer config allow-plugins.se7enxweb/exponential-legacy-installer true
composer require se7enxweb/exponential
php bin/php/ezpgenerateautoloads.php
```

The package has the type `ezpublish-legacy`; the installer plugin puts it in the project root. "If you answer no [to
trusting the plugin], the kernel lands in `vendor/se7enxweb/exponential/` and the extensions in `vendor/` as well,
where nothing finds them." Composer runs only your root package's scripts, so generate the class maps yourself.

> **Note**
> Which PHP a version needs decides whether Composer installs it at all: `v6.0.8` to `v6.0.14` require PHP `^8.1`;
> the 6.0.15 line (`main`) accepts PHP 8.0. "No published tag accepts PHP 8.0"
> ([3.1](../../install/03-getting-the-code.md#31-versions-tags-and-branches)). Build with the PHP your bundle ships.

## Check the result

```bash
php bin/php/console --version
ls vendor/autoload.php
composer show se7enxweb/exponential
```

The first command names the version (for example `console (Exponential) 6.0.15stable`). "If `vendor/autoload.php` is
missing, Composer did not finish; run `composer install` again and read its output from the first error"
([Check the result](../../install/03-getting-the-code.md#check-the-result)).

Then check what the PHP of your bundle lacks:

```bash
php bin/php/console exp:velocity ext check
```

It "lists every extension the installation wants, marks what is missing and prints the install command"
([Containers: Docker and Podman](../hosting/models/containers-docker-and-podman.md#images)).

## Common mistakes

- **Building from `main` and calling it a release.** `main` moves; record the commit, or build from a tag
  ([Versions, tags and pinning](versions-tags-and-pinning.md)).
- **Running `composer update` in a build.** It resolves new versions each time. Build with `composer install` from
  your lock file ([Lock files and reproducible builds](lock-files-and-reproducible-builds.md)).
- **Building as root and shipping root-owned files.** Fix ownership before you package
  ([File permissions and var/ ownership](file-permissions-and-var-ownership.md)).
- **Shipping `.git` directories by accident.** Way A and `--keep-vcs` keep them; decide whether your recipients want
  them.

## Related pages

- [Chapter 3: Getting the code](../../install/03-getting-the-code.md)
- [Composer-based bundles](composer-based-bundles.md)
- [Composer and Git on servers](../hosting/basics/composer-and-git-on-servers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
