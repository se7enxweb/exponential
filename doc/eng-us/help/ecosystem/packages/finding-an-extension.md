---
title: Finding an extension in the ecosystem
audience: ecosystem, developer, admin, website-creator, project-manager
category: ecosystem
order: 26
summary: Where to look for an Exponential extension - what is already installed, the se7enxweb repositories, Packagist, Projects and the forums - and how to judge an extension before you depend on it.
keywords: find extension, search, Packagist, GitHub, Projects, forums, evaluate extension, maintenance, license, compatibility, abandoned
type: help
---
# Finding an extension in the ecosystem

## Contents

1. [Look in this order](#look-in-this-order)
2. [Judging what you find](#judging-what-you-find)
3. [When nothing fits](#when-nothing-fits)

## Look in this order

| Step | Where |
|---|---|
| 1 | What your installation already has: many bundled extensions are installed but inactive ([The bundled extensions](the-bundled-extensions.md)) |
| 2 | The feature pages of the extensions: [Extensions](../../../features/6.0/extensions/README.md) |
| 3 | The se7enxweb repositories on [GitHub](https://github.com/se7enxweb) and the vendor's packages on Packagist |
| 4 | [Projects](https://projects.exponential.earth/), where community projects and extensions are listed |
| 5 | Ask in the [forums](https://share.exponential.earth/forums) ([Asking a good question](../../community/joining/asking-a-good-question.md)) |

## Judging what you find

| Check | Why |
|---|---|
| The license (GPL 2 or later is the project's recommendation) | You must be allowed to use and distribute it with your site |
| The supported Exponential and PHP versions in `composer.json` | It has to fit your installation ([Compatibility and dependencies](compatibility-and-dependencies.md)) |
| The date of the last release and of the last commit | An old extension may need porting |
| Open issues and how they are answered | Shows whether someone maintains it |
| `ezinfo.php` and `extension.xml` versions matching the tag | Shows release discipline |
| Tests and documentation | Lower risk |

## When nothing fits

- Build it yourself and share it ([Publishing into the ecosystem](../people/publishing-into-the-ecosystem.md)).
- Adopt an abandoned extension ([Adopting an abandoned extension](../../extension-development/part-5-extend-fork-contribute/adopting-an-abandoned-extension.md)).
- Fund it: partners and sponsors can request or sponsor features
  ([Feature requests and sponsored features](../../partners/feature-requests-and-sponsored-features.md)).
- Plan it with [Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md).

## Related pages

- [Packages](README.md)
- [Extension development](../../extension-development/README.md)
- [When to fork](../../extension-development/part-5-extend-fork-contribute/when-to-fork.md)
- [Extending without forking](../../extension-development/part-5-extend-fork-contribute/extending-without-forking.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
