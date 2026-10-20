---
title: error.ini [ErrorSettings-kernel]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ErrorSettings-kernel] in error.ini: ErrorHandler, RedirectURL, RerunURL, EmbedURL, HTTPError"
keywords: Einstellung, error.ini, ErrorSettings-kernel, INI, setting, ErrorHandler, RedirectURL, RerunURL, EmbedURL, HTTPError
type: generated
generated: exp:help:examples
---
# error.ini [ErrorSettings-kernel]

Der Block `[ErrorSettings-kernel]` von `settings/error.ini`, mit 5 Einstellung(en).

> Error handling for kernel errors  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ErrorHandler

> A list of error codes and which handler to use for it  
> Overrides DefaultErrorHandler  
> Example:  
> What to do when a module does not exists, use either displayerror or redirect  
> ErrorHandler\[20\]=displayerror  
> Set access denied to embed login  

Standard: ein Hash mit 1: `1=embed`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-kernel]
# ein leeres "ErrorHandler[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ErrorHandler[]
ErrorHandler[1]=embed
*/ ?>
```

## RedirectURL

> A list of error codes and the url for redirection.  
> Redirection tells the browser to choose another url for display.  
> Overrides DefaultRedirectURL  
> Example:  
> Redirect to user/login if access was denied  
> RedirectURL\[1\]=/user/login  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-kernel]
# ein leeres "RedirectURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RedirectURL[]
*/ ?>
```

## RerunURL

> A list of error codes and the url for rerunning.  
> Rerunning means executing another module and view  
> as though that module was specified in the browser url.  
> Overrides DefaultRerunURL  
> Example:  
> Rerun user/login if access was denied  
> RerunURL\[1\]=/user/login  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-kernel]
# ein leeres "RerunURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RerunURL[]
*/ ?>
```

## EmbedURL

> A list of error codes and the url for embedding.  
> Embedding means running the module as in rerun but  
> the result of the module is sent to the error template  
> and embedded.  
> Overrides DefaultEmbedURL  
> Example:  
> Embed user/login if access was denied  
> EmbedURL\[1\]=/user/login  

Standard: ein Hash mit 1: `1=/user/login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-kernel]
# ein leeres "EmbedURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EmbedURL[]
EmbedURL[1]=/user/login
*/ ?>
```

## HTTPError

> Defines the HTTP error code to use for the specified Exponential error code.  
> The definition is HTTPError\[Exponential error code\]=HTTP error code.  
> The HTTP error code must be defined with a HTTPError-code group as seen below.  
> Example:  
> Add 404 for module not found error  
> HTTPError\[20\]=404  
> 301 = moved permanently  

Standard: ein Hash mit 8: `1=401`, `2=404`, `3=404`, `4=301`, `6=403`, `20=404`, `21=404`, `22=404`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings-kernel]
# ein leeres "HTTPError[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HTTPError[]
HTTPError[1]=401
HTTPError[2]=404
HTTPError[3]=404
HTTPError[4]=301
HTTPError[6]=403
HTTPError[20]=404
HTTPError[21]=404
HTTPError[22]=404
*/ ?>
```

## Siehe auch

- [Alle Blöcke von error.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
