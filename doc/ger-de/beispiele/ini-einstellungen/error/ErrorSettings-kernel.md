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

> Fehlerbehandlung für Fehler des Kernels  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ErrorHandler

> Eine Liste von Fehlercodes und des dafür zu verwendenden Handlers  
> Überschreibt DefaultErrorHandler  
> Beispiel:  
> Was zu tun ist, wenn ein Modul nicht existiert: displayerror oder redirect  
> ErrorHandler\[20\]=displayerror  
> Bei verweigertem Zugriff die Anmeldung einbetten  

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

> Eine Liste von Fehlercodes und der URL zur Weiterleitung.  
> Die Weiterleitung sagt dem Browser, eine andere URL anzuzeigen.  
> Überschreibt DefaultRedirectURL  
> Beispiel:  
> Auf user/login weiterleiten, wenn der Zugriff verweigert wurde  
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

> Eine Liste von Fehlercodes und der URL zum erneuten Ausführen.  
> Erneutes Ausführen heißt, ein anderes Modul und eine andere Ansicht auszuführen,  
> als wäre dieses Modul in der Browser-URL angegeben.  
> Überschreibt DefaultRerunURL  
> Beispiel:  
> user/login erneut ausführen, wenn der Zugriff verweigert wurde  
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

> Eine Liste von Fehlercodes und der URL zum Einbetten.  
> Einbetten heißt, das Modul wie bei rerun auszuführen, aber  
> das Ergebnis des Moduls wird an das Fehler-Template geschickt  
> und eingebettet.  
> Überschreibt DefaultEmbedURL  
> Beispiel:  
> user/login einbetten, wenn der Zugriff verweigert wurde  
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

> Legt den HTTP-Fehlercode fest, der für den angegebenen Fehlercode von Exponential verwendet wird.  
> Die Definition ist HTTPError\[Exponential error code\]=HTTP error code.  
> Der HTTP-Fehlercode muss mit einer Gruppe HTTPError-code wie unten definiert sein.  
> Beispiel:  
> 404 für den Fehler Modul nicht gefunden hinzufügen  
> HTTPError\[20\]=404  
> 301 = dauerhaft verschoben  

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
