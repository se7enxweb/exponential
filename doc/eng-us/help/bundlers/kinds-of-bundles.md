---
title: Kinds of bundles
audience: bundler, partner, hosting
category: bundling
order: 20
summary: The ten kinds of Exponential bundles - container images, server and virtual machine images, hosting panel installers, Linux distribution packages, agency starter kits, Platform distributions, site packages, extension bundles, appliances and education or demo bundles - with what each contains, how it installs, how it updates and which page covers it.
keywords: kinds of bundles, container, Docker, Podman, image, virtual machine, one-click installer, panel, deb, rpm, distribution package, starter kit, Nexus, Legacy, site package, extension bundle, appliance, demo, classroom
type: help
---
# Kinds of bundles

This page is for bundlers who want to see where their product fits. Each kind of bundle has its own page; this one
compares them side by side.

## Contents

1. [The ten kinds at a glance](#the-ten-kinds-at-a-glance)
2. [How each kind installs and updates](#how-each-kind-installs-and-updates)
3. [Combinations](#combinations)
4. [Choosing your kind](#choosing-your-kind)

## The ten kinds at a glance

| Kind | You ship | The recipient | Page |
|---|---|---|---|
| Container image | an image with PHP, Exponential and an entry point | a platform team, a hosting company, a developer | [Container images](container-images.md) |
| Server or virtual machine image | an operating system with PHP, a web server, Exponential | a cloud or virtualization customer | [Server and virtual machine images](server-and-virtual-machine-images.md) |
| Hosting panel installer | a script or panel module that installs Exponential into an account | a hosting customer | [Hosting panel installers](hosting-panel-installers.md) |
| Linux distribution package | a `.deb` or `.rpm` and its dependencies | a system administrator | [Linux distribution packaging](linux-distribution-packaging.md) |
| Agency starter kit | a repository with Composer files, extensions, a theme, a site package and install configuration | your project teams, your customers | [Agency starter kits](agency-starter-kits.md) |
| Platform distribution | a Symfony project that carries Exponential 6 (Legacy, Nexus) | developers and agencies | [Platform distributions as a base](platform-distributions-as-a-base.md) |
| Site package | an `.ezpkg` (classes, content, settings, install scripts) | anyone running the installers | [Site packages in a bundle](site-packages-in-a-bundle.md) |
| Extension bundle | several extensions released and installed together | site builders | [Extension bundles](extension-bundles.md) |
| Appliance | hardware or a sealed virtual machine with Exponential inside | an organization that wants a box | [Appliances](appliances.md) |
| Education or demo bundle | a quick-start image or script, often SQLite, sometimes with a published sign-in | students, trainers, prospects | [Education and demo bundles](education-and-demo-bundles.md) |

## How each kind installs and updates

| Kind | Installs the site with | Updates code by | Typical database |
|---|---|---|---|
| Container image | `exp:install` in the entry point, once | a new image | a database server container or a managed service; SQLite for one container |
| Server image | `exp:install` or the kickstarter at first boot | Git and Composer on the server, or your packages | SQLite or PostgreSQL on the same machine |
| Panel installer | `exp:install` run as the account's user | the panel, or the customer with Git and Composer | the database the panel creates |
| Distribution package | a post-install step or a command the administrator runs | the package manager | the administrator's choice |
| Starter kit | the kickstarter with a reviewed `kickstart.ini` | Composer, Git | per project |
| Platform distribution | the Platform's own installer (`exponential:install` in Nexus) | Composer | per distribution |
| Site package | any of the three installers | a new package version | any |
| Extension bundle | Composer and `ActiveExtensions` | Composer | any |
| Appliance | a first-boot wizard of yours calling `exp:install` | your update channel | usually on the box |
| Demo bundle | `exp:install` on SQLite, often on a schedule | rebuilt from scratch | SQLite |

Sources for the installers: [Choosing an install method](../../install/04-choosing-an-install-method.md); for
Nexus: [Exponential Platform Nexus](../../features/6.0/platform-nexus-starter.md).

## Combinations

Most real bundles combine kinds:

- A hosting company builds a **server image** that contains a **panel installer** that installs a **site package**.
- An agency keeps a **starter kit** repository that holds an **extension bundle** and its own **site package**, and
  builds a **container image** from it for its customers.
- A school uses an **education bundle**: a **container image** that runs `exp:install` on SQLite with a site package
  chosen for the course.

Each layer brings its own duties. A container that carries a site package carries that package's license, too
([Third-party components and their licenses](third-party-components-and-their-licenses.md)).

## Choosing your kind

| If your recipient wants ... | Ship |
|---|---|
| to run it on their own platform with no setup | a container image |
| a ready server in their cloud account | a server image |
| a button in their hosting panel | a panel installer |
| updates through `apt` or `dnf` | a distribution package (and expect work: see its page) |
| a starting point for a new project | a starter kit |
| a site design and content model for any installation | a site package |
| a feature set for existing sites | an extension bundle |
| a box that works when plugged in | an appliance |
| to try it for an hour | a demo bundle |

## Related pages

- [What a bundle is](what-a-bundle-is.md)
- [One-click installers for Exponential](../hosting/provisioning/one-click-installers.md)
- [OEM and reselling](../partners/oem-and-reselling.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
