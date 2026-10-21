---
title: Lock files and reproducible builds
audience: bundler, developer
category: partnership
order: 90
summary: Why a bundle must carry its own composer.lock (the kernel repository has none), how to build the same bundle twice, what else must be pinned besides Composer, and a build record to keep with every release.
keywords: composer.lock, reproducible build, lock file, composer install, composer update, build record, checksum, SHA256SUMS, image digest, pinning
type: help
---
# Lock files and reproducible builds

This page is for whoever maintains the build of a bundle. A bundle is reproducible when building the same release
twice gives the same files. Recipients rely on that when they report a problem, and you rely on it when you fix one.

## Contents

1. [Why the lock file is yours](#why-the-lock-file-is-yours)
2. [Building twice the same way](#building-twice-the-same-way)
3. [What else must be pinned](#what-else-must-be-pinned)
4. [The build record](#the-build-record)
5. [When the build changes anyway](#when-the-build-changes-anyway)

## Why the lock file is yours

The kernel repository ships `composer.json` but no `composer.lock`: the lock file is "your own, it is not part of the
repository" ([3.2 A](../../install/03-getting-the-code.md#a-git-clone-and-composer-install-recommended)). Without a lock
file, every `composer install` resolves the `~` constraints again and may take newer extension releases than the
last build did.

So a bundle:

1. resolves once, with `composer update`, when you prepare a release;
2. tests that result;
3. commits the resulting `composer.lock` with the bundle;
4. builds every image or archive of that release with `composer install`, which installs exactly what the lock file
   names.

## Building twice the same way

```bash
composer validate --no-check-publish
composer install --no-dev --no-interaction
composer show > var/log/bundle-composer-show.txt
```

- `composer install` from the lock file gives the same package versions.
- `--no-interaction` makes Composer fail instead of asking a question nobody is there to answer.
- Keep the `composer show` output with the release: it is the list your support team will ask for.

## What else must be pinned

Composer covers PHP packages only. A bundle also depends on:

| Layer | Pin with |
|---|---|
| The base image or operating system | an image digest, or a dated snapshot of the distribution's repositories |
| PHP and its extensions | the exact package versions or image tag |
| Velocity outside Composer | a fixed image tag ("Pin a fixed tag in production", [Containers](../hosting/models/containers-docker-and-podman.md#images)) or a release whose files you check against its `SHA256SUMS` ([Velocity packages](../../features/6.0/velocity-packages-and-binaries.md#pick-a-form)) |
| Site packages | the `.ezpkg` files you ship, with their checksums ([Site packages in a bundle](site-packages-in-a-bundle.md)) |
| FrankenPHP, if used | `exp:velocity install` downloads a pinned release and checks its SHA-256 ([FrankenPHP](../../bc/6.0/frankenphp.md)) |

## The build record

Keep one record per bundle release, next to the release notes:

```text
Bundle:                  <name> <version>
Built on:                <date>, by <pipeline or person>
Exponential:             <tag or commit>   (php bin/php/console --version)
composer.lock:           <checksum of the file>
Base image / OS:         <digest or version>
PHP:                     <version> with <extensions>
Velocity / web server:   <version>
Site package(s):         <name> <version>, <checksum>
Integrity check:         php bin/php/checkmanifest.php --all -> exit <code>
Tests run:               <list, result>
Source offer published:  <URL>
```

The integrity check is explained on [Integrity checks](integrity-checks.md); the source offer on
[GPL obligations when you distribute](gpl-obligations-when-distributing.md).

## When the build changes anyway

- **A package vanished or moved.** Mirror what you ship (Composer packages, site packages) so an old release can be
  rebuilt; keep the source archives you published for the GPL anyway.
- **The lock file no longer installs on a newer PHP.** That is a new bundle release: resolve again, test, publish
  under a new version.
- **Someone ran `composer update` in a build script.** Remove it; the release is no longer what you tested.

## Related pages

- [Versions, tags and pinning](versions-tags-and-pinning.md)
- [Composer-based bundles](composer-based-bundles.md)
- [Testing a bundle](testing-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
