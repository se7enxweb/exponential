---
title: Sharing extensions and themes with the community
audience: community, developer, designer, partner
category: community
order: 29
summary: How community members share their own Exponential extensions, themes and site packages - publishing in your own repository under the GPL, Composer and Packagist, Projects, versioning and permanent tags, and when handing code over to the project needs the CLA.
keywords: extension, theme, site package, share, publish, GitHub, Packagist, Composer, Projects, GPL, version, tag, ezinfo.php, extension.xml, CLA
type: help
---
# Sharing extensions and themes with the community

Much of what makes Exponential useful lives in extensions, and many of them started as one person's solution to
their own problem. Sharing yours lets others build on it.

## Contents

1. [Two ways to share](#two-ways-to-share)
2. [Publishing your own work](#publishing-your-own-work)
3. [Telling people about it](#telling-people-about-it)
4. [Handing it to the project](#handing-it-to-the-project)
5. [Keeping it alive](#keeping-it-alive)

## Two ways to share

| Way | You keep | CLA |
|---|---|---|
| **Publish it yourself**: your own repository, your own Packagist package | The copyright and the maintenance | Not needed: "if you have written a contribution and want to make it available on GitHub, you do not need to sign the CLA" (`CONTRIBUTING.md`) |
| **Hand it to 7x for inclusion** in the project, for example to be maintained in the se7enxweb organization | Credit; the copyright is assigned | Needed, as for kernel code ([The CLA explained](the-cla-explained.md)) |

## Publishing your own work

The full walk-through is [Contribute extensions back](../../contribute-extensions-back.md) and Part 7 of the
extension development book, [Releasing](../../extension-development/part-7-releasing/README.md). In short:

1. **Clean it up**: no secrets, no customer data, no site-specific settings.
2. **Choose a license.** If you distribute an extension, the GPL's conditions apply; the project recommends GPL 2 or
   later ([Contribute extensions back](../../contribute-extensions-back.md)).
3. **Describe it** in `ezinfo.php` and `extension.xml` with the same version number
   ([Bumping ezinfo.php and extension.xml](../../extension-development/part-7-releasing/bumping-ezinfo-and-extension-xml.md)).
4. **Publish** a public repository and a Composer package on Packagist
   ([Composer and Packagist](../../extension-development/part-7-releasing/composer-and-packagist.md)).
5. **Tag releases** and never move a tag that has been pushed
   ([Tags are permanent](../../extension-development/part-7-releasing/tags-are-permanent.md)).

Themes follow the designers' sections: [Theme extensions](../../designers/extensions/theme-extensions-start-here.md)
and [Distribution](../../designers/distribution/distribution-start-here.md). Site packages:
[Contribute your site package](../../site-packages/contribute-your-site-package.md).

## Telling people about it

- List it on [Projects](https://projects.exponential.earth/) ([Publishing on Share and Projects](../../advocates/publishing-on-share-and-projects.md)).
- Announce it in the forums with what it does, which Exponential versions it supports and how to install it
  ([Announcing a release](../../extension-development/part-7-releasing/announcing-a-release.md)).
- Name new code by the prefix rule so it does not collide with others
  ([Naming prefixes](../../../standards/naming-prefixes-sevenx-and-exp.md)).

## Handing it to the project

If you want the project to maintain your extension, open an issue on the community tracker or talk to the
maintainers first. Code handed to 7x "for inclusion in the project" is covered by the CLA "in the same way" as kernel
code (`CONTRIBUTING.md`, "What it does not cover").

## Keeping it alive

- Answer issues, even with "not now".
- Test it against new 6.0.x releases.
- If you cannot maintain it any more, say so in the README so someone can take it over
  ([Adopting an abandoned extension](../../extension-development/part-5-extend-fork-contribute/adopting-an-abandoned-extension.md)).

## Related pages

- [Contributing as a community member](README.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Publishing an extension (partners)](../../partners/publishing-an-extension.md)
- [Ecosystem: start here](../../ecosystem/ecosystem-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
