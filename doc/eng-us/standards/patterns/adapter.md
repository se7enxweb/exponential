---
title: Adapter / bridge / wrapper
audience: developer
category: standards
order: 329
summary: Makes one interface usable through another: wrappers around libraries, bridges between subsystems. 6 instances in Exponential.
keywords: design pattern, adapter, structure, pattern catalogue
type: catalogue
---
# Adapter / bridge / wrapper

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Makes one interface usable through another: wrappers around libraries, bridges between subsystems.

| | |
|---|---|
| Group | Structure |
| Instances | 6 |
| Recognized by | Class name contains Adapter, Bridge, Wrapper or Proxy. |

## Classes by area

### extension/cjw_themes_jumper (1)

[All patterns of extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwOsmTileProxy` | class | `extension/cjw_themes_jumper/classes/cjwosmtileproxy.php:13` | The map tiles of the osm_proxy module: validation, the local tile cache and the fetch from the tile server. | – |

### extension/expai (1)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiMcpBridgeTool` | class extends expAiTool | `extension/expai/classes/mcp/expaimcpbridgetool.php:22` | A bridge-safe read tool of the extension expmcp, called in process as the current user (exp__). | – |

### extension/sqliimport (1)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImportLegacyHandlerAdapter` | class | `extension/sqliimport/classes/exp/handlers/expimportlegacyhandleradapter.php:21` | Runs a handler of sqliimport 1.x (ISQLIImportHandler extending SQLIImportAbstractHandler) in the 2.x engine, untouched: the handler is built with its… | – |

### kernel/classes/audit (1)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditBridge` | class | `kernel/classes/audit/bridge/expauditbridge.php:24` | The ezpEvent bridge of the audit (doc/eng-us/bc/6.0/audit.md, "The ezpEvent bridge", Z3): kernel and extension events that already exist (ezpEvent::g… | – |

### lib/ezsession (1)

[All patterns of lib/ezsession](area-lib-ezsession.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpSessionHandlerAdapter` | class | `lib/ezsession/classes/ezpsessionhandleradapter.php:24` | Gives an ezpSessionHandler to PHP as an object implementing SessionHandlerInterface. | – |

### lib/ezutils (1)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTrustedProxy` | class | `lib/ezutils/classes/eztrustedproxy.php:30` | Decides which forwarded request headers can be believed. | – |
