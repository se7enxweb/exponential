# Release notes of the extensions, themes, the web server and the platform packages

This page is the index of the release notes of every repository that ships next to Exponential. Read it when you
update an extension or a package and want to know what a version brings, or which version brought a change.

Each page lists the tagged releases of one repository, newest first, from December 2023 to October 2026. A release
groups its entries as **Added**, **Updated** (fixes included), **Removed** and **Renamed**; changes that only touch
packaging and documentation are listed apart, and version bumps and merge commits are counted, not listed. Every
entry links its commit.

The release notes of Exponential itself are in [changelogs 6.0](../6.0/6.0.15.md) (6.0.0 to 6.0.15). Which version of
each package the current line requires is in the [6.0.15 changelog](../6.0/6.0.15.md#required-packages-root-composerjson-2026-10-01-to-2026-10-02).

## How to find the version you run

```bash
grep -m1 "'Version'" extension/eztags/ezinfo.php
```

Expected: a line such as `'Version' => "2.4.11",`. The same number is in `extension.xml` and on **Setup > Extensions**.
Replace `eztags` with the extension you look at.

## Extensions and themes

| Extension | Latest release | Release notes |
|---|---|---|
| AdminAid | none tagged | [release notes](AdminAid.md) |
| bccie | v1.1.12 | [release notes](bccie.md) |
| bcgooglesitemaps | v1.1.6.4 | [release notes](bcgooglesitemaps.md) |
| bcwebsitestatistics | v1.0.9 | [release notes](bcwebsitestatistics.md) |
| birthday | 1.3.2 | [release notes](birthday.md) |
| cjw-exponential-media-site-data | v1.0.0 | [release notes](cjw-exponential-media-site-data.md) |
| cjw_newsletter | 4.1.15 | [release notes](cjw_newsletter.md) |
| enhancedezbinaryfile | v4.4.4 | [release notes](enhancedezbinaryfile.md) |
| enhancedselection2 | 2.1.7 | [release notes](enhancedselection2.md) |
| exp_enhanced_link | v1.0.4 | [release notes](exp_enhanced_link.md) |
| explayouts_ui | v1.3.6 | [release notes](explayouts_ui.md) |
| explayouts_ui_api | v1.3.7 | [release notes](explayouts_ui_api.md) |
| ezautosave | v6.0.8 | [release notes](ezautosave.md) |
| ezdemo | v6.0.9 | [release notes](ezdemo.md) |
| ezflow | v6.1.5 | [release notes](ezflow.md) |
| ezgmaplocation | v6.0.6 | [release notes](ezgmaplocation.md) |
| ezie | v6.0.8 | [release notes](ezie.md) |
| ezjscore | 1.5.5 | [release notes](ezjscore.md) |
| ezmbpaex | v6.0.4 | [release notes](ezmbpaex.md) |
| ezmultiupload | v6.0.8 | [release notes](ezmultiupload.md) |
| ezodf | v6.1.6 | [release notes](ezodf.md) |
| ezoe | none tagged | [release notes](ezoe.md) |
| ezpaypal | v1.2.3 | [release notes](ezpaypal.md) |
| ezpm | v0.10.0 | [release notes](ezpm.md) |
| ezprestapi | v1.2.4 | [release notes](ezprestapi.md) |
| ezprestapiprovider | v6.0.3 | [release notes](ezprestapiprovider.md) |
| ezstarrating | v6.0.8 | [release notes](ezstarrating.md) |
| eztags | v2.4.11 | [release notes](eztags.md) |
| ezupdate | v1.1.10 | [release notes](ezupdate.md) |
| ezwebin | v6.0.16 | [release notes](ezwebin.md) |
| ezwebin-ezpackage | none tagged | [release notes](ezwebin-ezpackage.md) |
| ezwt | v6.0.9 | [release notes](ezwt.md) |
| ezxmlexport | none tagged | [release notes](ezxmlexport.md) |
| git_manager | v2.0.14 | [release notes](git_manager.md) |
| hcaptcha | v1.2 | [release notes](hcaptcha.md) |
| ngclasslist | 1.1.3 | [release notes](ngclasslist.md) |
| nxc_powercontent | v1.4.3 | [release notes](nxc_powercontent.md) |
| owsimpleoperator | v1.2.5 | [release notes](owsimpleoperator.md) |
| recaptcha | v1.4.5 | [release notes](recaptcha.md) |
| sevenx-recipes | v1.3.23 | [release notes](sevenx-recipes.md) |
| sevenx_authentication_2fa | none tagged | [release notes](sevenx_authentication_2fa.md) |
| sevenx_dse | v1.1.4 | [release notes](sevenx_dse.md) |
| sevenx_themes_admin_classic | v0.2.4 | [release notes](sevenx_themes_admin_classic.md) |
| sevenx_themes_simple | v1.0.21 | [release notes](sevenx_themes_simple.md) |
| sevenx_valkey_cache | v1.0.2.0 | [release notes](sevenx_valkey_cache.md) |
| swark | v1.0.4 | [release notes](swark.md) |
| syndication | v1.3.2 | [release notes](syndication.md) |
| xrowextract | v2.5.6 | [release notes](xrowextract.md) |
| xrowmetadata | v1.4.4 | [release notes](xrowmetadata.md) |

## The web server

| Package | Latest release | Release notes |
|---|---|---|
| exponential-velocity (Exponential Velocity) | v0.0.4.42 | [release notes](exponential-velocity.md) |

## Platform packages

The repositories of the Symfony based platform and its bridge to the legacy kernel. How they relate to Exponential
is told in [the platform repositories](../../history/ecosystem.md).

| Package | Latest release | Release notes |
|---|---|---|
| admin-ui | v5.0.4 | [release notes](admin-ui.md) |
| cjw-exponential-platform-nexus | 1.0.0.6 | [release notes](cjw-exponential-platform-nexus.md) |
| doctrine-dbal-schema | v1.0.11 | [release notes](doctrine-dbal-schema.md) |
| exponential-legacy-installer | 2.2.3 | [release notes](exponential-legacy-installer.md) |
| exponential-platform-dxp | v0.0.0.2 | [release notes](exponential-platform-dxp.md) |
| exponential-platform-legacy | v2.5.0.3 | [release notes](exponential-platform-legacy.md) |
| exponential-platform-nexus-starter | 1.0.0.0 | [release notes](exponential-platform-nexus-starter.md) |
| exponential-platform-nexus | 1.0.0.6 | [release notes](exponential-platform-nexus.md) |
| exponential-platform | v3.2.9 | [release notes](exponential-platform.md) |
| ez-support-tools | v2.3.14 | [release notes](ez-support-tools.md) |
| ezplatform-admin-ui-assets | v5.3.6 | [release notes](ezplatform-admin-ui-assets.md) |
| ezplatform-admin-ui | v2.3.41 | [release notes](ezplatform-admin-ui.md) |
| ezplatform-core | v2.3.44 | [release notes](ezplatform-core.md) |
| ezplatform-cron | v3.1.7 | [release notes](ezplatform-cron.md) |
| ezplatform-graphql | v2.3.20 | [release notes](ezplatform-graphql.md) |
| ezplatform-http-cache | v2.3.19 | [release notes](ezplatform-http-cache.md) |
| ezplatform-kernel | v1.3.45 | [release notes](ezplatform-kernel.md) |
| ezplatform-matrix-fieldtype | v2.2.13 | [release notes](ezplatform-matrix-fieldtype.md) |
| ezplatform-query-fieldtype | v2.3.10 | [release notes](ezplatform-query-fieldtype.md) |
| ezplatform-richtext | v2.3.28 | [release notes](ezplatform-richtext.md) |
| ezplatform-search | v1.2.8 | [release notes](ezplatform-search.md) |
| ezplatform-solr-search-engine | v3.3.19 | [release notes](ezplatform-solr-search-engine.md) |
| ezplatform-standard-design | v0.3.11 | [release notes](ezplatform-standard-design.md) |
| ezplatform-user | v2.3.15 | [release notes](ezplatform-user.md) |
| ezplatform-xmltext-fieldtype | v2.0.3 | [release notes](ezplatform-xmltext-fieldtype.md) |
| ezpublish-kernel | v7.5.41 | [release notes](ezpublish-kernel.md) |
| fieldtype-richtext-ibexa | 5.0.0 | [release notes](fieldtype-richtext-ibexa.md) |
| layouts-core | 2.0.0-se7enx.1 | [release notes](layouts-core.md) |
| legacyBridge | v4.0.0.3 | [release notes](legacyBridge.md) |
| mediata-ezpage-fieldtype-bundle-main | 1.0.2 | [release notes](mediata-ezpage-fieldtype-bundle-main.md) |
| metadata-bundle | v5.0.0 | [release notes](metadata-bundle.md) |
| ngsymfonytools | 4.0.0.0, 4.x | [release notes](ngsymfonytools.md) |
| oss | v3.3.0.5 | [release notes](oss.md) |
| site-bundle | 3.0.6 | [release notes](site-bundle.md) |
| site-legacy-bundle | v2.1.0 | [release notes](site-legacy-bundle.md) |
| symfony | v3.4.50 | [release notes](symfony.md) |
| twig | v2.16.4 | [release notes](twig.md) |

## Related pages

- [Upgrading guide](../../guides/upgrading.md), part C4: check your extensions after an upgrade.
- [Extensions guide](../../guides/extensions.md): install, configure and release an extension.
- [Extension catalogue](../../features/6.0/extensions/README.md): what each extension does.
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md).
