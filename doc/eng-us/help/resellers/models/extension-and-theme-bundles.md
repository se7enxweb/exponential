---
title: Extension and theme bundles
audience: reseller, partner, developer, designer
category: partnership
order: 110
summary: How to resell extensions and themes for Exponential as bundles - your own work, project extensions and third-party ones - with the license check for each part, business models that respect the GPL, what a bundle must contain, versioning against Exponential releases, and support terms.
keywords: extension bundle, theme bundle, extensions, themes, designs, site package, resell, license, GPL, extension.xml, ezinfo.php, Info About, versioning, support, Composer
type: help
---
# Extension and theme bundles

This page is for resellers who package extensions, themes and designs into offers: "the intranet bundle", "the
newsroom bundle", "the store starter". For building the bundles themselves see
[Bundlers: start here](../../bundlers/bundlers-start-here.md); this page is about selling them.

## Contents

1. [What goes into a bundle](#what-goes-into-a-bundle)
2. [Check the license of every part](#check-the-license-of-every-part)
3. [Business models that respect the GPL](#business-models-that-respect-the-gpl)
4. [Versioning and compatibility](#versioning-and-compatibility)
5. [Support terms](#support-terms)
6. [Bundle sheet](#bundle-sheet)

## What goes into a bundle

| Part | Typical source | Notes |
|---|---|---|
| Your own extensions | Your repositories | You decide the license, within what combining with GPL code allows |
| Project extensions | `se7enxweb/*` Composer packages | The project's extensions are GPL ([Licensing for partners](../../partners/licensing-for-partners.md)) |
| Third-party extensions | Other authors | Their own licenses |
| A theme or design | Your design work, or a project theme | Check the licenses of images and fonts ([Publishing a theme](../../partners/publishing-a-theme.md)) |
| A site package | Your own, built like the shipped ones | Installed by the setup wizard, the kickstarter or `exp:install --package=<name>` ([Site packages for hosts](../../hosting/provisioning/site-packages.md)) |
| Services | Installation, configuration, training, support | Your work, priced separately |

The ecosystem is large: the kernel's `composer.json` requires many `se7enxweb/*` packages, and every extension is a
Git repository and a Composer package ([Extension and theme partners](../../partners/extension-and-theme-partners.md)).

## Check the license of every part

- Each extension states its license in its own `ezinfo.php` and `extension.xml`; the administration shows them on
  **Info > About** ([About and Copyright pages](../../../features/6.0/about-and-package-pages.md)).
- Exponential Velocity is MIT; third-party components have their own licenses, listed on the About page
  ([Licensing for hosts](../../hosting/business/licensing-for-hosts.md#extensions-and-other-components)).
- Whether your own extension must be GPL depends on how it is combined with Exponential: ask counsel.

```text
| Part | Version | License (from extension.xml / About) | Source repository | May we charge for distribution? |
|------|---------|--------------------------------------|-------------------|---------------------------------|
```

## Business models that respect the GPL

| Model | How it earns | Notes |
|---|---|---|
| Free bundle, paid services | Installation, customization, support | The most common model in this ecosystem |
| Paid distribution | You charge for delivering the bundle | Recipients of GPL parts may share them on |
| Paid support subscription | Guaranteed answers and updates for the bundle | Publish the support terms clearly |
| Sponsored development | A customer pays for a feature that is published for all | [Feature requests and sponsored features](../../partners/feature-requests-and-sponsored-features.md) |
| OEM resale through the program | "resell your own products based on Exponential" | [OEM product reseller](oem-product-reseller.md) |

Source: [Extension and theme partners](../../partners/extension-and-theme-partners.md#business-models-that-respect-the-gpl).

## Versioning and compatibility

- Give each bundle a version, and state which Exponential release and PHP versions it was tested with.
- Every release of an extension should carry its version in `ezinfo.php` and `extension.xml`, so customers can read it
  on **Info > About** and **Setup > Extensions**.
- Test each new 6.0.x release with the bundle before you recommend the update to customers
  ([Testing releases](../../partners/testing-releases.md)).
- An extension that is no longer maintained can hold back an upgrade; plan who maintains every part
  ([Release line and support](../../customers/healthy/release-line-and-support.md#extensions-have-versions-of-their-own)).

## Support terms

Write down, for each bundle:

- what is supported (your extensions, the configuration you delivered) and what is not (third-party code you did not
  change, the customer's own changes);
- for how long a bundle version is supported after the next one appears;
- how fixes reach the customer (a new bundle version, not a hot fix on the live site);
- where the customer reports a bug.

## Bundle sheet

```text
Bundle: ______________________   Version: ______   Release date: ________
For: <kind of site>
Built on: Exponential 6.0.__   PHP: 8.__ to 8.__   Database(s) tested: ____________
Contents: <extensions, theme, site package, versions>
Licenses: see license table (attached)
Price: distribution $____  installation $____  support $____ / month
Support: <what, how long, channel>
Upgrade policy: <how bundle versions follow Exponential releases>
```

## Related pages

- [Extension and theme partners](../../partners/extension-and-theme-partners.md)
- [Publishing an extension as a partner](../../partners/publishing-an-extension.md)
- [Contributing extensions back](../../partners/contributing-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
