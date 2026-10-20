---
title: Erweiterungspunkte in content.ini
audience: developer, admin
category: examples
summary: Einstellungen von content.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (33, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, content.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in content.ini

Einstellungen von `settings/content.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/content.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## VersionManagement VersionHistoryClass

Ausgeliefert: ein Hash mit 1: `1=75`. [Die Einstellung](../ini-einstellungen/content/VersionManagement.md#versionhistoryclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[VersionManagement]
VersionHistoryClass[expexample]=expExampleVersionHistoryClas    # Schlüssel und Wert wählen Sie
*/ ?>
```

## EditSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/EditSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[EditSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## DataTypeSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/DataTypeSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[DataTypeSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ActionSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/ActionSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ActionSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## header AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/header.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[header]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## paragraph AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/paragraph.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[paragraph]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## table AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/table.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[table]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## tr AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/tr.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[tr]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## td AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/td.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[td]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## th AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/th.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[th]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## literal AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/literal.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[literal]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## strong AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/strong.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[strong]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## emphasize AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/emphasize.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[emphasize]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ol AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/ol.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ol]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ul AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/ul.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ul]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## link AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/link.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[link]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## object AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/object.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[object]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## embed AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/embed.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[embed]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## embed-inline AvailableClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/embed-inline.md#availableclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[embed-inline]
AvailableClasses[]=expExampleAvailableClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RelationGroupSettings ImagesClassList

Ausgeliefert: eine Liste mit 1: `image`. [Die Einstellung](../ini-einstellungen/content/RelationGroupSettings.md#imagesclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RelationGroupSettings]
ImagesClassList[]=expExampleImagesClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RelationGroupSettings FilesClassList

Ausgeliefert: eine Liste mit 1: `file`. [Die Einstellung](../ini-einstellungen/content/RelationGroupSettings.md#filesclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RelationGroupSettings]
FilesClassList[]=expExampleFilesClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## UnpublishSettings ClassList

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/UnpublishSettings.md#classlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[UnpublishSettings]
ClassList[]=expExampleClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## HideSettings ReverseRelationCheck

Ausgeliefert: `warn`. [Die Einstellung](../ini-einstellungen/content/HideSettings.md#reverserelationcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[HideSettings]
ReverseRelationCheck=warn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## FetchFunctionSettings InstClassListFilter

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/FetchFunctionSettings.md#instclasslistfilter).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[FetchFunctionSettings]
InstClassListFilter[]=expExampleInstClassListFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## NamePatternSettings ResolverClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/NamePatternSettings.md#resolverclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[NamePatternSettings]
ResolverClasses[]=expExampleResolverClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RemoveSettings ShowRemoveToTrashCheck

Ausgeliefert: `true`. [Die Einstellung](../ini-einstellungen/content/RemoveSettings.md#showremovetotrashcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RemoveSettings]
ShowRemoveToTrashCheck=true    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RemoveSettings ReverseRelationCheck

Ausgeliefert: `warn`. [Die Einstellung](../ini-einstellungen/content/RemoveSettings.md#reverserelationcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[RemoveSettings]
ReverseRelationCheck=warn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ReverseRelationSettings ProviderClassList

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/ReverseRelationSettings.md#providerclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[ReverseRelationSettings]
ProviderClassList[]=expExampleProviderClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlphabeticalFilterSettings ContentFilterList

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/AlphabeticalFilterSettings.md#contentfilterlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[AlphabeticalFilterSettings]
ContentFilterList[]=expExampleContentFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## PublishingSettings AsynchronousPublishingFilters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/PublishingSettings.md#asynchronouspublishingfilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingFilters[]=expExampleAsynchronousPublishingFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## PublishingSettings AsynchronousPublishingPreQueueHooks

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/PublishingSettings.md#asynchronouspublishingprequeuehooks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingPreQueueHooks[]=expExampleAsynchronousPublishingPreQueueHook    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## PublishingSettings AsynchronousPublishingPostHandlingHooks

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/content/PublishingSettings.md#asynchronouspublishingposthandlinghooks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingPostHandlingHooks[]=expExampleAsynchronousPublishingPostHandlingHook    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## TestingSettings MultivariateTestingHandlerClass

Ausgeliefert: `ezpMultivariateTestHandler`. [Die Einstellung](../ini-einstellungen/content/TestingSettings.md#multivariatetestinghandlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/content.ini.append.php
[TestingSettings]
MultivariateTestingHandlerClass=ezpMultivariateTestHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von content.ini](../ini-einstellungen/content/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
