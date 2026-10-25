---
title: Erweiterungspunkte in kernelservices.ini
audience: developer, admin
category: examples
summary: Einstellungen von kernelservices.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (25, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, kernelservices.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in kernelservices.ini

Einstellungen von `settings/kernelservices.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/kernelservices.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## Service_db Class

Ausgeliefert: `eZDBInterface`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_db.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_db]
Class=eZDBInterface    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_db Factory

Ausgeliefert: `eZDB::instance`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_db.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_db]
Factory=eZDB::instance    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_http Class

Ausgeliefert: `eZHTTPTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_http.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_http]
Class=eZHTTPTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_http Factory

Ausgeliefert: `eZHTTPTool::instance`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_http.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_http]
Factory=eZHTTPTool::instance    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_ini.site Class

Ausgeliefert: `eZINI`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_ini.site.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.site]
Class=eZINI    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_ini.site Factory

Ausgeliefert: `eZINI::instance`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_ini.site.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.site]
Factory=eZINI::instance    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_ini.i18n Class

Ausgeliefert: `eZINI`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_ini.i18n.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.i18n]
Class=eZINI    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_ini.i18n Factory

Ausgeliefert: `eZINI::instance`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_ini.i18n.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.i18n]
Factory=eZINI::instance    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_extensions.overrides Class

Ausgeliefert: `expKernelExtensionOverrideList`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_extensions.overrides.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_extensions.overrides]
Class=expKernelExtensionOverrideList    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.registry Class

Ausgeliefert: `expKernelTransferRegistry`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.registry.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.registry]
Class=expKernelTransferRegistry    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.registry Factory

Ausgeliefert: `expKernelTransferRegistry::fromContainer`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.registry.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.registry]
Factory=expKernelTransferRegistry::fromContainer    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.package Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.package.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.package]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.package Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.package.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.package]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.rss_import Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.rss_import.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_import]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.rss_import Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.rss_import.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_import]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.rss_export Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.rss_export.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_export]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.rss_export Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.rss_export.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_export]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.infocollector Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.infocollector.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.infocollector]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.infocollector Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.infocollector.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.infocollector]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.audit Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.audit.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.audit]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.audit Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.audit.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.audit]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.datatable Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.datatable.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.datatable]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.datatable Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.datatable.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.datatable]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.pdf Class

Ausgeliefert: `expKernelTransferTool`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.pdf.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.pdf]
Class=expKernelTransferTool    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Service_transfer.pdf Factory

Ausgeliefert: `expKernelTransferTool::fromSettings`. [Die Einstellung](../ini-einstellungen/kernelservices/Service_transfer.pdf.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.pdf]
Factory=expKernelTransferTool::fromSettings    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von kernelservices.ini](../ini-einstellungen/kernelservices/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
