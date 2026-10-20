---
title: Extension points in content.ini
audience: developer, admin
category: examples
summary: Settings of content.ini that list handlers, classes or directories an extension can add to (33, found by name).
keywords: content.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Extension points in content.ini

Settings of `settings/content.ini` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting's name: a heuristic.** Read the setting's comment (linked) before relying on it.

An extension adds to a list in its own `settings/content.ini.append.php` (active extensions' settings are read after the kernel's and before `settings/override`):

## VersionManagement VersionHistoryClass

Shipped: a hash of 1: `1=75`. [The setting](../ini-settings/content/VersionManagement.md#versionhistoryclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[VersionManagement]
VersionHistoryClass[expexample]=expExampleVersionHistoryClas    # key and value are yours
*/ ?>
```

## EditSettings ExtensionDirectories

Shipped: an empty list. [The setting](../ini-settings/content/EditSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[EditSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # your class or value, added to the list
*/ ?>
```

## DataTypeSettings ExtensionDirectories

Shipped: an empty list. [The setting](../ini-settings/content/DataTypeSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[DataTypeSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # your class or value, added to the list
*/ ?>
```

## ActionSettings ExtensionDirectories

Shipped: an empty list. [The setting](../ini-settings/content/ActionSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ActionSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # your class or value, added to the list
*/ ?>
```

## header AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/header.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[header]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## paragraph AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/paragraph.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[paragraph]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## table AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/table.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[table]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## tr AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/tr.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[tr]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## td AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/td.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[td]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## th AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/th.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[th]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## literal AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/literal.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[literal]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## strong AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/strong.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[strong]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## emphasize AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/emphasize.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[emphasize]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## ol AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/ol.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ol]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## ul AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/ul.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ul]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## link AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/link.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[link]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## object AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/object.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[object]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## embed AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/embed.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[embed]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## embed-inline AvailableClasses

Shipped: an empty list. [The setting](../ini-settings/content/embed-inline.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[embed-inline]
AvailableClasses[]=expExampleAvailableClasse    # your class or value, added to the list
*/ ?>
```

## RelationGroupSettings ImagesClassList

Shipped: a list of 1: `image`. [The setting](../ini-settings/content/RelationGroupSettings.md#imagesclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RelationGroupSettings]
ImagesClassList[]=expExampleImagesClass    # your class or value, added to the list
*/ ?>
```

## RelationGroupSettings FilesClassList

Shipped: a list of 1: `file`. [The setting](../ini-settings/content/RelationGroupSettings.md#filesclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RelationGroupSettings]
FilesClassList[]=expExampleFilesClass    # your class or value, added to the list
*/ ?>
```

## UnpublishSettings ClassList

Shipped: an empty list. [The setting](../ini-settings/content/UnpublishSettings.md#classlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[UnpublishSettings]
ClassList[]=expExampleClass    # your class or value, added to the list
*/ ?>
```

## HideSettings ReverseRelationCheck

Shipped: `warn`. [The setting](../ini-settings/content/HideSettings.md#reverserelationcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[HideSettings]
ReverseRelationCheck=warn    # replace with your class or value
*/ ?>
```

## FetchFunctionSettings InstClassListFilter

Shipped: an empty list. [The setting](../ini-settings/content/FetchFunctionSettings.md#instclasslistfilter).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[FetchFunctionSettings]
InstClassListFilter[]=expExampleInstClassListFilter    # your class or value, added to the list
*/ ?>
```

## NamePatternSettings ResolverClasses

Shipped: an empty list. [The setting](../ini-settings/content/NamePatternSettings.md#resolverclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[NamePatternSettings]
ResolverClasses[]=expExampleResolverClasse    # your class or value, added to the list
*/ ?>
```

## RemoveSettings ShowRemoveToTrashCheck

Shipped: `true`. [The setting](../ini-settings/content/RemoveSettings.md#showremovetotrashcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RemoveSettings]
ShowRemoveToTrashCheck=true    # replace with your class or value
*/ ?>
```

## RemoveSettings ReverseRelationCheck

Shipped: `warn`. [The setting](../ini-settings/content/RemoveSettings.md#reverserelationcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RemoveSettings]
ReverseRelationCheck=warn    # replace with your class or value
*/ ?>
```

## ReverseRelationSettings ProviderClassList

Shipped: an empty list. [The setting](../ini-settings/content/ReverseRelationSettings.md#providerclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ReverseRelationSettings]
ProviderClassList[]=expExampleProviderClass    # your class or value, added to the list
*/ ?>
```

## AlphabeticalFilterSettings ContentFilterList

Shipped: an empty list. [The setting](../ini-settings/content/AlphabeticalFilterSettings.md#contentfilterlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[AlphabeticalFilterSettings]
ContentFilterList[]=expExampleContentFilter    # your class or value, added to the list
*/ ?>
```

## PublishingSettings AsynchronousPublishingFilters

Shipped: an empty list. [The setting](../ini-settings/content/PublishingSettings.md#asynchronouspublishingfilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingFilters[]=expExampleAsynchronousPublishingFilter    # your class or value, added to the list
*/ ?>
```

## PublishingSettings AsynchronousPublishingPreQueueHooks

Shipped: an empty list. [The setting](../ini-settings/content/PublishingSettings.md#asynchronouspublishingprequeuehooks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingPreQueueHooks[]=expExampleAsynchronousPublishingPreQueueHook    # your class or value, added to the list
*/ ?>
```

## PublishingSettings AsynchronousPublishingPostHandlingHooks

Shipped: an empty list. [The setting](../ini-settings/content/PublishingSettings.md#asynchronouspublishingposthandlinghooks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingPostHandlingHooks[]=expExampleAsynchronousPublishingPostHandlingHook    # your class or value, added to the list
*/ ?>
```

## TestingSettings MultivariateTestingHandlerClass

Shipped: `ezpMultivariateTestHandler`. [The setting](../ini-settings/content/TestingSettings.md#multivariatetestinghandlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[TestingSettings]
MultivariateTestingHandlerClass=ezpMultivariateTestHandler    # replace with your class or value
*/ ?>
```

## Related

- [All extension points](README.md)
- [An extension skeleton](../cookbook/extension-skeleton.md)
- [The blocks of content.ini](../ini-settings/content/README.md)

---

Generated by `./console exp:help:examples` from `settings/content.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
