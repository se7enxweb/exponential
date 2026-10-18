---
title: Patterns in extension/xrowmetadata
audience: developer
category: standards
order: 477
summary: Every class, view, fetch function, operator and plugin point of extension/xrowmetadata with its design patterns.
keywords: extension/xrowmetadata, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/xrowmetadata

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/xrowmetadata` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (38)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Cronjob\Extension\Xrowmetadata\Archivesitemap` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php:11` | The code of extension/xrowmetadata/cronjobs/archivesitemap.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Extension\Xrowmetadata\Mobilesitemap` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php:11` | The code of extension/xrowmetadata/cronjobs/mobilesitemap.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Extension\Xrowmetadata\Newssitemap` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php:11` | The code of extension/xrowmetadata/cronjobs/newssitemap.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Extension\Xrowmetadata\Sitemap` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php:11` | The code of extension/xrowmetadata/cronjobs/sitemap.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Index` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowmetadata/classes/runnable/views/sitemaps/index.php:65` | The code of extension/xrowmetadata/modules/sitemaps/index.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Robots` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/xrowmetadata/classes/runnable/views/sitemaps/robots.php:10` | The code of extension/xrowmetadata/modules/sitemaps/robots.php, moved into a class (#207 stage 1). |
| `eZClusterDirectoryIterator` | class implements Iterator | [Iterator](iterator.md) | `extension/xrowmetadata/classes/ezclusterdirectoryiterator.php:3` | – |
| `ezfSolrDocumentFieldxrowMetadata` | class extends ezfSolrDocumentFieldBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/xrowmetadata/classes/ezfSolrDocumentFieldxrowMetadata.php:3` | – |
| `xrowMetaData` | class extends ezcBaseStruct | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowmetadata.php:3` | – |
| `xrowMetaDataFunctions` | class | [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowmetadatafunctions.php:3` | – |
| `xrowmetadataInfo` | class | [Value object / struct](value-object.md) | `extension/xrowmetadata/ezinfo.php:8` | – |
| `xrowMetaDataLicense` | class | [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowmetadatalicense.php:27` | The content licenses of xrowmetadata: the catalogue, the installation's allowlist, and which license a page has. |
| `xrowMetaDataLicenseCatalogueGenerator` | class | [Builder](builder.md) | `extension/xrowmetadata/bin/php/xrowmetadatalicenses.php:84` | Builds the license catalogue share/licenses/licenses.json of xrowmetadata from two downloaded files, never from the network: |
| `xrowMetaDataLicenseHeader` | class | [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowmetadatalicenseheader.php:28` | The license of a page as HTTP response headers: |
| `xrowMetaDataLicenseOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/xrowmetadata/autoloads/xrowmetadatalicenseoperator.php:37` | The template operator xrowmetadata_license: the content license of a node, or of the page being shown, ready for the page head and a visible notice. |
| `xrowMetaDataLicensePicker` | class | [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowmetadatalicensepicker.php:15` | What the edit forms need to choose a license: the catalogue (xrowMetaDataLicense) as option groups for the picker, the inherited license (class defau… |
| `xrowMetaDataLicensePickerOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/xrowmetadata/autoloads/xrowmetadatalicensepickeroperator.php:14` | Template operators of the license picker (xrowMetaDataLicensePicker), for templates that have no metadata attribute content at hand, such as the clas… |
| `xrowMetaDataOpenGraphImage` | class | [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowmetadataopengraphimage.php:16` | The Open Graph image of a metadata attribute: which object it is, whether that object can still be shown, and the picture of it that og:image and twi… |
| `xrowMetaDataOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/xrowmetadata/autoloads/xrowmetadataoperator.php:3` | – |
| `xrowMetaDataType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/xrowmetadata/datatypes/xrowmetadata/xrowmetadatatype.php:3` | – |
| `xrowMobileSitemap` | class extends xrowSitemapList | – | `extension/xrowmetadata/classes/xrowmobilesitemap.php:3` | – |
| `xrowSitemap` | class extends xrowSitemapList | – | `extension/xrowmetadata/classes/xrowsitemap.php:3` | – |
| `xrowSitemapConverter` | class implements xrowSitemapImageConverterInterface, xrowSitemapVideoConverterInterface, xrowSitemapNewsConverterInterface | [Strategy / handler](strategy-handler.md) | `extension/xrowmetadata/classes/plugins/standardplugin.php:3` | – |
| `xrowSitemapImageConverterInterface` | interface | [Interface (contract)](interface.md) | `extension/xrowmetadata/classes/interfaces/xrowsitemapimageconverterinterface.php:3` | – |
| `xrowSitemapIndex` | class extends xrowSitemapList | – | `extension/xrowmetadata/classes/xrowsitemapindex.php:3` | – |
| `xrowSitemapItem` | abstract class extends ezcBaseStruct | [Template Method / abstract base class](template-method.md), [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitem.php:3` | – |
| `xrowSitemapItemAlternateLink` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemalternatelink.php:3` | – |
| `xrowSitemapItemFrequency` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemfrequency.php:3` | – |
| `xrowSitemapItemImage` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemimage.php:3` | – |
| `xrowSitemapItemModified` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemmodified.php:3` | – |
| `xrowSitemapItemNews` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapnewsitem.php:3` | – |
| `xrowSitemapItemPriority` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitempriority.php:3` | – |
| `xrowSitemapItemVideo` | class extends xrowSitemapItem | [Value object / struct](value-object.md) | `extension/xrowmetadata/classes/structs/xrowsitemapvideoitem.php:3` | – |
| `xrowSitemapList` | class | – | `extension/xrowmetadata/classes/xrowsitemaplist.php:11` | – |
| `xrowSitemapNewsConverterInterface` | interface | [Interface (contract)](interface.md) | `extension/xrowmetadata/classes/interfaces/xrowsitemapnewsconverterinterface.php:3` | – |
| `xrowSitemapTools` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowsitemaptools.php:3` | – |
| `xrowSitemapVideoConverterInterface` | interface | [Interface (contract)](interface.md) | `extension/xrowmetadata/classes/interfaces/xrowsitemapvideoconverterinterface.php:3` | – |
| `xrowSiteUpdater` | class | [Utility (static helper)](utility.md) | `extension/xrowmetadata/classes/xrowsiteupdater.php:3` | – |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/sitemaps/index` | `index.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:9` |
| `/sitemaps/robots` | `robots.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:12` |

## Template operators (3)

| Operators | Class | Registered in |
|---|---|---|
| metadata | `xrowMetaDataOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:4` |
| xrowmetadata_license | `xrowMetaDataLicenseOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:11` |
| xrowmetadata_license_enabled, xrowmetadata_license_choices, xrowmetadata_license_info, xrowmetadata_license_inherited, xrowmetadata_license_class_settings | `xrowMetaDataLicensePickerOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:19` |

## Cronjob parts (8)

| Cronjob | File | Description |
|---|---|---|
| `archivesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php` | The code of extension/xrowmetadata/cronjobs/archivesitemap.php, moved into a class (#207 stage 1). |
| `mobilesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php` | The code of extension/xrowmetadata/cronjobs/mobilesitemap.php, moved into a class (#207 stage 1). |
| `newssitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php` | The code of extension/xrowmetadata/cronjobs/newssitemap.php, moved into a class (#207 stage 1). |
| `sitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php` | The code of extension/xrowmetadata/cronjobs/sitemap.php, moved into a class (#207 stage 1). |
| `archivesitemap` | `extension/xrowmetadata/cronjobs/archivesitemap.php` | – |
| `mobilesitemap` | `extension/xrowmetadata/cronjobs/mobilesitemap.php` | – |
| `newssitemap` | `extension/xrowmetadata/cronjobs/newssitemap.php` | – |
| `sitemap` | `extension/xrowmetadata/cronjobs/sitemap.php` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[SolrFieldMapSettings] CustomMap[xrowmetadata]` | `ezfSolrDocumentFieldxrowMetadata` | `extension/xrowmetadata/settings/ezfind.ini.append.php:4` |
