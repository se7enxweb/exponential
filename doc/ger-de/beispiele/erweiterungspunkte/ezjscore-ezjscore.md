---
title: Erweiterungspunkte in ezjscore.ini
audience: developer, admin
category: examples
summary: Einstellungen von ezjscore.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (7, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, ezjscore.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in ezjscore.ini

Einstellungen von `extension/ezjscore/settings/ezjscore.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/ezjscore.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## ezjscServer_ezjsc Class

Ausgeliefert: `ezjscServerFunctionsJs`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/ezjscServer_ezjsc.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[ezjscServer_ezjsc]
Class=ezjscServerFunctionsJs    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ezjscServer_ezjscnode Class

Ausgeliefert: `ezjscServerFunctionsNode`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/ezjscServer_ezjscnode.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
Class=ezjscServerFunctionsNode    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ezjscServer_expsubitems Class

Ausgeliefert: `expSubitemsServerFunctions`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/ezjscServer_expsubitems.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[ezjscServer_expsubitems]
Class=expSubitemsServerFunctions    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ezjscServer_expdebugbar Class

Ausgeliefert: `expDebugBarServerFunctions`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/ezjscServer_expdebugbar.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[ezjscServer_expdebugbar]
Class=expDebugBarServerFunctions    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ezjscServer_ezpublishingqueue Class

Ausgeliefert: `ezjscServerFunctionsPublishingQueue`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/ezjscServer_ezpublishingqueue.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[ezjscServer_ezpublishingqueue]
Class=ezjscServerFunctionsPublishingQueue    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ezjscServer_ezajaxuploader Class

Ausgeliefert: `ezjscServerFunctionsAjaxUploader`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/ezjscServer_ezajaxuploader.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[ezjscServer_ezajaxuploader]
Class=ezjscServerFunctionsAjaxUploader    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AjaxUploader AjaxUploadHandler

Ausgeliefert: ein Hash mit 2: `ezobjectrelationlist=ezpRelationListAjaxUploader`, `ezobjectrelation=ezpRelationAjaxUploader`. [Die Einstellung](../ini-einstellungen/ezjscore-ezjscore/AjaxUploader.md#ajaxuploadhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezjscore.ini.append.php
[AjaxUploader]
AjaxUploadHandler[expexample]=expExampleAjaxUploadHandler    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von ezjscore.ini](../ini-einstellungen/ezjscore-ezjscore/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
