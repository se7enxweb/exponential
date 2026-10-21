---
title: Ecosystem checklists
audience: ecosystem, developer, partner, advocate
category: ecosystem
order: 8
summary: Checklists for working in the Exponential ecosystem - before describing a product or distribution, before depending on a package, before publishing a package, and before quoting a number about the ecosystem.
keywords: checklist, distribution, describe product, depend on package, publish package, numbers, facts, composer.json
type: help
---
# Ecosystem checklists

## Contents

1. [Before describing a product or distribution](#before-describing-a-product-or-distribution)
2. [Before depending on a package](#before-depending-on-a-package)
3. [Before publishing a package](#before-publishing-a-package)
4. [Before quoting a number](#before-quoting-a-number)

## Before describing a product or distribution

```text
[ ] I read its composer.json (require), not only its description
[ ] Features I name are in its requirements or its documentation
[ ] I do not borrow features of another distribution (the DXP has no layouts editor,
    workflow, scheduler or legacy kernel)
[ ] I name the version I describe
[ ] I link the source
```

Pages: [Exponential Platform DXP](products/platform-dxp.md), [Choosing a product](products/choosing-a-product.md).

## Before depending on a package

```text
[ ] License compatible with my use
[ ] PHP and kernel versions in its composer.json fit my installation
[ ] Recent releases or commits; issues answered
[ ] Version fields match the tag
[ ] Tested on a copy of my site
[ ] I know who maintains it, or I can maintain it myself
```

Pages: [Finding an extension](packages/finding-an-extension.md), [Compatibility and dependencies](packages/compatibility-and-dependencies.md).

## Before publishing a package

```text
[ ] No secrets, customer data or site-specific settings
[ ] LICENSE file; README with what it does, supported versions, installation
[ ] composer.json; Packagist entry; webhook
[ ] ezinfo.php and extension.xml carry the release version
[ ] Next version chosen from the published tags sorted by version
[ ] Tag pushed only after the checks; never moved afterwards
[ ] Listed on Projects and announced
```

Pages: [Publishing into the ecosystem](people/publishing-into-the-ecosystem.md),
[The release checklist](../extension-development/part-7-releasing/the-release-checklist.md).

## Before quoting a number

```text
[ ] I know the source and its date
[ ] I say the date, or recount with the command the source names
[ ] It is not a guess presented as fact
```

Page: [Keeping help accurate](../community/helping-others/keeping-help-accurate.md).

## Related pages

- [Ecosystem: start here](ecosystem-start-here.md)
- [Community checklists](../community/community-checklists.md)
- [Where the facts live](../advocates/where-the-facts-live.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
