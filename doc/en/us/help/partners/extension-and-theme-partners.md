---
title: Extension and theme partners
audience: partner
category: partnership
order: 410
summary: For partners who build and publish extensions, themes and designs for Exponential - how this work fits the partner profiles, the ecosystem it joins, business models that respect the GPL, maintenance duties and how to work with the original authors of forked extensions.
keywords: extension partner, theme partner, extensions, themes, designs, ecosystem, Packagist, Composer, business model, maintenance, fork, upstream
type: help
---
# Extension and theme partners

This page is for partners whose product is an extension, a theme or a design for Exponential. No separate "extension
partner" profile is published; such partners usually choose the IT profile (extensions) or the Creative profile
(themes and designs) ([Partner profiles](partner-profiles.md)).

## The ecosystem you join

- `composer.json` of the kernel requires 66 `se7enxweb/*` packages, and the se7enxweb vendor has 219 packages on
  Packagist ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-extension-ecosystem)).
- Every extension is a Git repository and a Composer package; the administration lists installed extensions under
  **Setup > Extensions** ([Extensions](../../guides/extensions.md#2-find-an-extension)).
- The community projects portal <https://projects.exponential.earth> hosts "user created and maintained
  extensions/plugins/designs/bundles projects" (<https://exponential.earth/about/project>).

## Business models that respect the GPL

| Model | How it earns | Notes |
|---|---|---|
| Free extension, paid services | Installation, customization, support | The most common model in this ecosystem |
| Free extension, paid support subscription | Guaranteed answers and updates | Publish the support terms clearly |
| Sponsored development | A customer or sponsor pays for a feature that is published for all | See [Feature requests and sponsored features](feature-requests-and-sponsored-features.md) |
| Paid distribution | You charge for delivering the package | Under the GPL the recipient may share it on |
| OEM resale through the program | "resell your own products based on Exponential" | [OEM and reselling](oem-and-reselling.md) |

Whether your extension must be GPL depends on how it is combined with Exponential; ask counsel. The project's own
extensions are GPL ([Licensing for partners](licensing-for-partners.md)).

## Site packages: please contribute yours

7x and the Exponential Foundation strongly encourage partners to submit their site packages (complete site setups
that the setup wizard installs) by pull request to
<https://github.com/se7enxweb/exponential.packages.exponential.earth>. That repository is the package server of the
setup wizard: "Installation / Addons Packages for Exponential CMS" (repository description). A package you contribute
there lets every new installation start from your work, and credits you as its author.

Before you send one, remove all customer content, names and credentials from it, and check the licenses of every
image and font it contains ([Publishing a theme or design as a partner](publishing-a-theme.md#assets-and-their-licenses)).
The repository to fork, the folder layout, the checklist and the pull request step by step:
[Contribute your site package](../site-packages/contribute-your-site-package.md).

## Maintenance duties

Customers rely on extensions for years. Publish only what you will maintain, or say clearly that you will not.

- Keep it working on supported PHP versions (8.0 to 8.5 for 6.0.15).
- Follow the release rules: version in `ezinfo.php` and `extension.xml`, never move a published tag
  ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).
- Keep a changelog and answer issues.
- Mark abandoned extensions as such and point to an alternative.

## Forks and original authors

Many extensions in the ecosystem continue the work of earlier authors. When you fork or upgrade one:

1. Keep the original copyright and license notices.
2. Credit the original author in the README.
3. Offer your changes back to the original repository if it is still active
   ([Contributing extensions back upstream](contributing-extensions-back.md)).
4. Rename only when you must, and say where it came from.

## Related pages

- [Publishing an extension as a partner](publishing-an-extension.md)
- [Publishing a theme or design as a partner](publishing-a-theme.md)
- [Extensions](../../guides/extensions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
