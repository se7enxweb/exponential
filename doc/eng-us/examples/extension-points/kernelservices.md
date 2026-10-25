---
title: Extension points in kernelservices.ini
audience: developer, admin
category: examples
summary: Settings of kernelservices.ini that list handlers, classes or directories an extension can add to (25, found by name).
keywords: kernelservices.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Extension points in kernelservices.ini

Settings of `settings/kernelservices.ini` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting's name: a heuristic.** Read the setting's comment (linked) before relying on it.

An extension adds to a list in its own `settings/kernelservices.ini.append.php` (active extensions' settings are read after the kernel's and before `settings/override`):

## Service_db Class

Shipped: `eZDBInterface`. [The setting](../ini-settings/kernelservices/Service_db.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_db]
Class=eZDBInterface    # replace with your class or value
*/ ?>
```

## Service_db Factory

Shipped: `eZDB::instance`. [The setting](../ini-settings/kernelservices/Service_db.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_db]
Factory=eZDB::instance    # replace with your class or value
*/ ?>
```

## Service_http Class

Shipped: `eZHTTPTool`. [The setting](../ini-settings/kernelservices/Service_http.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_http]
Class=eZHTTPTool    # replace with your class or value
*/ ?>
```

## Service_http Factory

Shipped: `eZHTTPTool::instance`. [The setting](../ini-settings/kernelservices/Service_http.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_http]
Factory=eZHTTPTool::instance    # replace with your class or value
*/ ?>
```

## Service_ini.site Class

Shipped: `eZINI`. [The setting](../ini-settings/kernelservices/Service_ini.site.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.site]
Class=eZINI    # replace with your class or value
*/ ?>
```

## Service_ini.site Factory

Shipped: `eZINI::instance`. [The setting](../ini-settings/kernelservices/Service_ini.site.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.site]
Factory=eZINI::instance    # replace with your class or value
*/ ?>
```

## Service_ini.i18n Class

Shipped: `eZINI`. [The setting](../ini-settings/kernelservices/Service_ini.i18n.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.i18n]
Class=eZINI    # replace with your class or value
*/ ?>
```

## Service_ini.i18n Factory

Shipped: `eZINI::instance`. [The setting](../ini-settings/kernelservices/Service_ini.i18n.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_ini.i18n]
Factory=eZINI::instance    # replace with your class or value
*/ ?>
```

## Service_extensions.overrides Class

Shipped: `expKernelExtensionOverrideList`. [The setting](../ini-settings/kernelservices/Service_extensions.overrides.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_extensions.overrides]
Class=expKernelExtensionOverrideList    # replace with your class or value
*/ ?>
```

## Service_transfer.registry Class

Shipped: `expKernelTransferRegistry`. [The setting](../ini-settings/kernelservices/Service_transfer.registry.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.registry]
Class=expKernelTransferRegistry    # replace with your class or value
*/ ?>
```

## Service_transfer.registry Factory

Shipped: `expKernelTransferRegistry::fromContainer`. [The setting](../ini-settings/kernelservices/Service_transfer.registry.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.registry]
Factory=expKernelTransferRegistry::fromContainer    # replace with your class or value
*/ ?>
```

## Service_transfer.package Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.package.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.package]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.package Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.package.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.package]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Service_transfer.rss_import Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.rss_import.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_import]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.rss_import Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.rss_import.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_import]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Service_transfer.rss_export Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.rss_export.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_export]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.rss_export Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.rss_export.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.rss_export]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Service_transfer.infocollector Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.infocollector.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.infocollector]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.infocollector Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.infocollector.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.infocollector]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Service_transfer.audit Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.audit.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.audit]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.audit Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.audit.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.audit]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Service_transfer.datatable Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.datatable.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.datatable]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.datatable Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.datatable.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.datatable]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Service_transfer.pdf Class

Shipped: `expKernelTransferTool`. [The setting](../ini-settings/kernelservices/Service_transfer.pdf.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.pdf]
Class=expKernelTransferTool    # replace with your class or value
*/ ?>
```

## Service_transfer.pdf Factory

Shipped: `expKernelTransferTool::fromSettings`. [The setting](../ini-settings/kernelservices/Service_transfer.pdf.md#factory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/kernelservices.ini.append.php
[Service_transfer.pdf]
Factory=expKernelTransferTool::fromSettings    # replace with your class or value
*/ ?>
```

## Related

- [All extension points](README.md)
- [An extension skeleton](../cookbook/extension-skeleton.md)
- [The blocks of kernelservices.ini](../ini-settings/kernelservices/README.md)

---

Generated by `./console exp:help:examples` from `settings/kernelservices.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
