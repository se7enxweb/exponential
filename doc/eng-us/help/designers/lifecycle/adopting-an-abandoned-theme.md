---
title: "Adopting an abandoned theme"
audience: designer, developer
category: theme-lifecycle
order: 300
summary: Take over a theme whose author stopped maintaining it - ask first, then continue in the original repository if you are given access, or fork it under a new name; and how old extensions from the community archive can be brought up to current Exponential.
keywords: abandoned theme, adopt, take over, unmaintained, fork, ask the author, old extensions, projects archive, upgrade
type: help
---
# Adopting an abandoned theme

This page is for designers who use or like a theme nobody maintains any more. Many older extensions and designs are
listed in the archive types of [projects.exponential.earth](https://projects.exponential.earth/types) (the "Via
Projects.ez.no" types); some deserve a second life.

## Step by step

1. **Check it is really abandoned**: no releases or answers for a long time, an end-of-life notice, or an archived
   repository.
2. **Ask the author** (an issue, the address in its files): would they hand it over or add you as maintainer?
3. **If yes**, follow the handover from the new maintainer's side ([Handing a theme over](handover-to-a-new-maintainer.md)).
4. **If no answer or no**, fork it under a new name ([Forking a theme](forking-a-theme.md),
   [Rebranding a fork, fairly](rebranding-a-fork.md)). The GPL allows it; keep every notice.
5. **Bring it up to date**: metadata files ([ezinfo.php](../extensions/ezinfo-php.md), [extension.xml](../extensions/extension-xml.md)),
   `composer.json`, PHP 8 ([PHP version compatibility](php-version-compatibility.md)), jQuery 4 and no YUI
   ([JavaScript library upgrades](javascript-library-upgrades.md)), escaping ([Security fixes](security-fixes-in-themes.md)),
   accessibility and CSP audits.
6. **Release** it with a changelog that credits the original author and lists what you changed.
7. **Tell the community** where the theme continues.

## Mostly small changes

Bringing an old extension to current Exponential is usually a series of small changes: the kernel's APIs stayed
largely compatible. Work through the list in step 5 one item at a time, with a test after each.

## Related pages

- [Credits and attribution](credits-and-attribution.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
