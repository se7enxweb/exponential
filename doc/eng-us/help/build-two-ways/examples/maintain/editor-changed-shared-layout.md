---
title: "When an editor changed the shared layout by mistake"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1332
summary: A change to Header / Footer or Prefooter shows on many pages; roll it back from the last export.
keywords: shared layout, mistake, rollback, Header / Footer, Prefooter
type: help
---
# When an editor changed the shared layout by mistake

Every page's footer is back to normal in minutes.

*Life cycle: Maintain, example 33 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. If it is only a draft: open it and **Discard**.

## On the command line

1. `./console exp:layouts import <last export> --only=fd0aa7af77a85a03bf1080018f5db1dc --strategy=overwrite --publish --dry-run`, check, run without `--dry-run`, then `./console exp:cache clear --tag=content`.

## Which to choose

Keep exports of the shared layouts especially.

## Check

- All pages show the restored footer.

## Related pages

- [Roll a layout back](../../roll-back-a-layout.md)
- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
