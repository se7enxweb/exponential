---
title: "Handing a theme over to a new maintainer"
audience: designer, project-manager
category: theme-lifecycle
order: 280
summary: Pass a theme to a new maintainer so users notice nothing but continuity - choosing someone, a handover period, documentation, access, the repository and package, and announcing it.
keywords: handover, new maintainer, succession, transfer, access, documentation, continuity, announcement
type: help
---
# Handing a theme over to a new maintainer

This page is for designers who want their theme to outlive their involvement.

## Choose someone

Good candidates have already contributed (pull requests, answers in issues) or depend on the theme professionally.
Ask publicly in the issues and on share.exponential.earth, then talk to the candidates.

## Step by step

1. **Agree** on the handover in writing: from when, what they take over, whether you stay as a contributor.
2. **Write down what is in your head**: release routine, test environment, page list, known issues, customers who
   rely on it (without their private data) ([An upgrade test environment](upgrade-test-environment.md)).
3. **Give access** first as a co-maintainer: repository write access, Packagist maintainer. Make one release together.
4. **Transfer** the repository and the package when ready
   ([Transferring the repository and the Packagist package](transferring-repository-and-packagist.md)).
5. **Update metadata**: `Info_url`, `homepage`, `support` and authors in `ezinfo.php`, `extension.xml`, `composer.json`;
   keep your copyright lines, add theirs ([Credits and attribution](credits-and-attribution.md)).
6. **Announce** in a release note, the README, the project page and your announcement thread.
7. **Remove your access** when the handover is complete, if agreed.

## What must stay

The copyright notices of everyone who contributed, and the license. A handover changes who maintains, not who wrote.

## Related pages

- [Adopting an abandoned theme](adopting-an-abandoned-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
