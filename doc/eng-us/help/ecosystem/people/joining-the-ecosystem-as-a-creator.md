---
title: Joining the ecosystem as a creator
audience: ecosystem, developer, designer, website-creator, hosting
category: ecosystem
order: 53
summary: First steps for someone who wants to build with Exponential - a local installation from Git, the learning path for each kind of creator, the conventions to follow from day one, and where to show your first work.
keywords: creator, getting started, developer, designer, local installation, Git clone, SQLite, learning path, conventions, first project
type: help
---
# Joining the ecosystem as a creator

## Contents

1. [Step 1: a local installation](#step-1-a-local-installation)
2. [Step 2: your learning path](#step-2-your-learning-path)
3. [Step 3: conventions from day one](#step-3-conventions-from-day-one)
4. [Step 4: a first project](#step-4-a-first-project)
5. [Step 5: show it](#step-5-show-it)

## Step 1: a local installation

Install from Git clones on SQLite: no database server, and every fix you make is "a branch in your fork and a pull
request away" ([Develop with Git](../../develop-with-git.md), [Install in one command](../../../features/6.0/install-in-one-command.md)).
Serve it with Velocity ([Exponential Velocity](../products/exponential-velocity.md)).

## Step 2: your learning path

| You are | Read |
|---|---|
| A PHP developer | [Extension development, reading paths](../../extension-development/reading-paths.md) |
| A site builder | [Website creators](../../creators/website-creators/website-creators-start-here.md), [Template language](../../template-language/README.md) |
| An integrator or app developer | [Application creators](../../creators/app-creators/app-creators-start-here.md), [ezprestapi](../../../features/6.0/extensions/ezprestapi.md) |
| A designer | [Designers: start here](../../designers/designers-start-here.md) |
| A Symfony developer | [The Platform line](../products/the-platform-line.md), [Exponential Platform DXP](../products/platform-dxp.md) |
| A host | [Hosting companies: start here](../../hosting/hosting-companies-start-here.md) |

## Step 3: conventions from day one

- Name your code with your own prefix, and new code meant for the kernel with `exp`
  ([Naming prefixes](../../../standards/naming-prefixes-sevenx-and-exp.md)).
- Keep custom code in extensions, not in the kernel ([Extending without forking](../../extension-development/part-5-extend-fork-contribute/extending-without-forking.md)).
- Use Git, prefixed commit messages and permanent tags ([Standards and tooling](../php-stack/standards-and-tooling.md)).

## Step 4: a first project

A small extension with a module and a template, a theme for a test site, or a translation. The students' page lists
project ideas by level ([Student and education partners](../../partners/student-and-education-partners.md)).

## Step 5: show it

Publish it and tell people ([Publishing into the ecosystem](publishing-into-the-ecosystem.md)); ask for review in the
forums; answer the first issue someone opens.

## Related pages

- [People](README.md)
- [Creators in the ecosystem](creators-in-the-ecosystem.md)
- [Your first contribution](../../community/contributing/your-first-contribution.md)
- [The community for developers](../../community/paths/community-for-developers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
