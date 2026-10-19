---
title: Builder
audience: developer
category: standards
order: 322
summary: Builds a complex object or text step by step. 9 instances in Exponential.
keywords: design pattern, builder, creation, pattern catalogue
type: catalogue
---
# Builder

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Builds a complex object or text step by step.

| | |
|---|---|
| Group | Creation |
| Instances | 9 |
| Recognized by | Class name contains Builder or Generator. |

## Classes by area

### extension/cjw_newsletter (1)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterEditionBuilder` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionbuilder.php:24` | Makes and fills editions: finds the latest unsent edition of a list, copies a template edition under its list, and takes articles from a pool into an… | – |

### extension/explayouts_api (1)

[All patterns of extension/explayouts_api](area-extension-explayouts-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsEzValueUrlGenerator` | class | `extension/explayouts_api/classes/explayoutsezvalueurlgenerator.php:2` | – | – |

### extension/explayouts_site_api (1)

[All patterns of extension/explayouts_site_api](area-extension-explayouts-site-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsSiteApiItemValueUrlGenerator` | class | `extension/explayouts_site_api/classes/explayoutssiteapiitemvalueurlgenerator.php:2` | – | – |

### extension/ezfind (1)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfeZPSolrQueryBuilder` | class | `extension/ezfind/classes/ezfezpsolrquerybuilder.php:8` | – | – |

### extension/ezodf (1)

[All patterns of extension/ezodf](area-extension-ezodf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZOOGenerator` | class | `extension/ezodf/classes/ezoogenerator.php:41` | – | – |

### extension/xrowmetadata (1)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowMetaDataLicenseCatalogueGenerator` | class | `extension/xrowmetadata/bin/php/xrowmetadatalicenses.php:84` | Builds the license catalogue share/licenses/licenses.json of xrowmetadata from two downloaded files, never from the network: | – |

### kernel/classes/help (1)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpExamplesGenerator` | class | `kernel/classes/help/exphelpexamplesgenerator.php:25` | Writes the generated reference examples below doc/examples/ (doc/examples/README.md, "How the generated pages are made"): one Markdown page per templ… | – |

### kernel/classes/pdfexport (1)

[All patterns of kernel/classes/pdfexport](area-kernel-classes-pdfexport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPDFExportGenerator` | class | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Generates the PDF of an export: into its stored file, or into a temporary file that is then sent and removed. | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZAutoloadGenerator` | class | `kernel/private/classes/ezautoloadgenerator.php:19` | Utility class for generating autoload arrays for Exponential. | – |
