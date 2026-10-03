# Extensions, themes and packages

This page lists every legacy extension, theme and package of Exponential 6 that has its own page. Use it to find out
what an extension does before you activate it. Each page says what the extension is for, how to set it up, its
settings with defaults, and what changed in the Exponential 6 releases.

To activate an extension, add it to `ActiveExtensions[]` in `settings/override/site.ini.append.php`, regenerate the
autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear the caches. The page of each extension lists any extra
step (tables, policies, keys).

## Content and datatypes

| Extension | What it is |
|---|---|
| [birthday](birthday.md) | birthday datatype |
| [enhancedezbinaryfile](enhancedezbinaryfile.md) | file datatype for forms |
| [enhancedselection2](enhancedselection2.md) | selection datatype that stores identifiers |
| [exp_enhanced_link](exp_enhanced_link.md) | enhanced link datatype |
| [ezgmaplocation](ezgmaplocation.md) | map location datatype |
| [ezstarrating](ezstarrating.md) | star ratings |
| [eztags](eztags.md) | tags |
| [hcaptcha](hcaptcha.md) | hCaptcha datatype |
| [ngclasslist](ngclasslist.md) | class list datatype |
| [recaptcha](recaptcha.md) | reCAPTCHA datatype |
| [xrowmetadata](xrowmetadata.md) | meta data, Open Graph and sitemaps |

## Editing and page building

| Extension | What it is |
|---|---|
| [explayouts_ui](explayouts_ui.md) | the Layouts admin screens |
| [explayouts_ui_api](explayouts_ui_api.md) | the layout editor |
| [ezautosave](ezautosave.md) | draft autosave |
| [ezflow](ezflow.md) | pages, zones and blocks |
| [ezie](ezie.md) | image editor |
| [ezmultiupload](ezmultiupload.md) | multiple file upload |
| [ezodf](ezodf.md) | OpenDocument import and export |
| [ezoe](ezoe.md) | online editor repository |
| [ezwt](ezwt.md) | website toolbar |

## Designs and themes

| Extension | What it is |
|---|---|
| [ezdemo](ezdemo.md) | demo design |
| [ezwebin](ezwebin.md) | Website Interface design |
| [ezwebin-ezpackage](ezwebin-ezpackage.md) | installer packages of ezwebin |
| [sevenx_themes_admin_classic](sevenx_themes_admin_classic.md) | admin design switch |
| [sevenx_themes_simple](sevenx_themes_simple.md) | the simple theme |

## Shop, newsletter and visitors

| Extension | What it is |
|---|---|
| [bccie](bccie.md) | collected information export |
| [bcgooglesitemaps](bcgooglesitemaps.md) | Google sitemaps |
| [bcwebsitestatistics](bcwebsitestatistics.md) | Google Analytics |
| [cjw_newsletter](cjw_newsletter.md) | newsletter |
| [ezpaypal](ezpaypal.md) | PayPal gateway |
| [ezpm](ezpm.md) | private messages |

## Data exchange and APIs

| Extension | What it is |
|---|---|
| [ezprestapi](ezprestapi.md) | REST content provider |
| [ezprestapiprovider](ezprestapiprovider.md) | REST provider classes |
| [ezxmlexport](ezxmlexport.md) | XML export |
| [nxc_powercontent](nxc_powercontent.md) | content from code and REST |
| [syndication](syndication.md) | content syndication |
| [xrowextract](xrowextract.md) | export and import |

## Administration, security and operations

| Extension | What it is |
|---|---|
| [AdminAid](AdminAid.md) | administrator helper |
| [ezjscore](ezjscore.md) | JavaScript core and packer |
| [ezmbpaex](ezmbpaex.md) | password expiry |
| [ezupdate](ezupdate.md) | updates and packages |
| [git_manager](git_manager.md) | git dashboard and backups |
| [sevenx_authentication_2fa](sevenx_authentication_2fa.md) | two-factor and social login |
| [sevenx_dse](sevenx_dse.md) | Database Source Editor |
| [sevenx_valkey_cache](sevenx_valkey_cache.md) | Redis and Valkey cache |

## Template helpers

| Extension | What it is |
|---|---|
| [owsimpleoperator](owsimpleoperator.md) | simple template operators |
| [swark](swark.md) | template operators and workflow events |

## Platform packages

| Package | What it is |
|---|---|
| [cjw-exponential-media-site-data](cjw-exponential-media-site-data.md) | media site data |
| [ez-support-tools](ez-support-tools.md) | platform bundle (history in the ecosystem section) |
| [ngsymfonytools](ngsymfonytools.md) | platform bundle (history in the ecosystem section) |
| [sevenx-recipes](sevenx-recipes.md) | Symfony Flex recipes |

## Related pages

- [What a new installation comes with: the default extension distribution](../default-extension-distribution.md)
- [Extension list: sort, inspect and download any extension](../extension-list-and-downloads.md)
- [Setup > Extensions: loading order and safe saving](../extension-loading-order.md)
- [Extension metadata: `ezinfo.php` and `extension.xml`](../../../specifications/6.0/extension-metadata.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- History: [January 2024, first half](../../../history/2024/2024-01a.md), [January 2024, second half](../../../history/2024/2024-01b.md), [February 2024](../../../history/2024/2024-02.md), [March 2024](../../../history/2024/2024-03.md), [June 2024](../../../history/2024/2024-06.md)
