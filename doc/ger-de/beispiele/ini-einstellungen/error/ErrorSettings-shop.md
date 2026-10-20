---
title: error.ini [ErrorSettings-shop]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ErrorSettings-shop] in error.ini: ErrorHandler, RedirectURL, RerunURL, EmbedURL, HTTPError"
keywords: Einstellung, error.ini, ErrorSettings-shop, INI, setting, ErrorHandler, RedirectURL, RerunURL, EmbedURL, HTTPError
type: generated
generated: exp:help:examples
---
# error.ini [ErrorSettings-shop]

Der Block `[ErrorSettings-shop]` von `settings/error.ini`, mit 5 Einstellung(en).

> Allow the default Exponential 500 error message to be overridden  
> Note: this will not work with siteaccess settings, will only be global (settings/override/error.ini.append.php), because of how early this is registered  
> FatalErrorHandler=CustomFatalErrorHandlerClass::FatalErrorHandlerFunction  
> Error handling for shop errors  
> See the kernel errors for details on usage  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ErrorHandler

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-shop]
# ein leeres "ErrorHandler[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ErrorHandler[]
*/ ?>
```

## RedirectURL

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-shop]
# ein leeres "RedirectURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RedirectURL[]
*/ ?>
```

## RerunURL

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-shop]
# ein leeres "RerunURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RerunURL[]
*/ ?>
```

## EmbedURL

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-shop]
# ein leeres "EmbedURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EmbedURL[]
*/ ?>
```

## HTTPError

Standard: ein Hash mit 1: `1=404`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-shop]
# ein leeres "HTTPError[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HTTPError[]
HTTPError[1]=404
*/ ?>
```

## Siehe auch

- [Alle Blöcke von error.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
