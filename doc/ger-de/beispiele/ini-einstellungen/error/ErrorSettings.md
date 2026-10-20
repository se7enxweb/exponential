---
title: error.ini [ErrorSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ErrorSettings] in error.ini: DefaultErrorHandler, DefaultRedirectURL, DefaultRerunURL, DefaultEmbedURL"
keywords: Einstellung, error.ini, ErrorSettings, INI, setting, DefaultErrorHandler, DefaultRedirectURL, DefaultRerunURL, DefaultEmbedURL
type: generated
generated: exp:help:examples
---
# error.ini [ErrorSettings]

Der Block `[ErrorSettings]` von `settings/error.ini`, mit 4 Einstellung(en).

> Global error settings which are used for all error types  
> A specific error type group is defined with ErrorSettings followed  
> by a - (dash) and the error type name, e.g. ErrorSettting-kernel  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultErrorHandler

> Static pages for failures outside the template engine (lib/ezutils/classes/ezexecution.php):  
> StaticErrorPage\[500\], StaticErrorPage\[503\], StaticErrorPage\[default\], and one per known error  
> case, shown instead of the general page when the installation explains the failure:  
>   StaticErrorPage\[dependencies\]  the Composer libraries (vendor/) are missing; placeholders  
>   {status} {title} {message} {steps} {repair} {reference} {home} {detail}. Unset: a built-in page.  
> StaticErrorPage\[dependencies\]=design/standard/errors/dependencies.html  
> Which handler to use, possible handlers are:  
> displayerror - Displays a text explaining the error  
> redirect     - Redirect to a specific url  
> rerun        - Rerun a module/view on the page, specified by a url  
> embed        - Run a module/view and embed the results on the  
>                same page used in displayerror  

Standard: `displayerror`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultErrorHandler=displayerror
*/ ?>
```

## DefaultRedirectURL

Standard: `/content/view/full/2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultRedirectURL=/content/view/full/2
*/ ?>
```

## DefaultRerunURL

Standard: `/content/view/full/2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultRerunURL=/content/view/full/2
*/ ?>
```

## DefaultEmbedURL

Standard: `/user/login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultEmbedURL=/user/login
*/ ?>
```

## Siehe auch

- [Alle Blöcke von error.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
