---
title: logviewer.ini [DigestSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DigestSettings] in logviewer.ini: Hours, MaxMessages, MaxBytesPerFile, Levels, LogLevels, GroupBy"
keywords: Einstellung, logviewer.ini, DigestSettings, INI, setting, Hours, MaxMessages, MaxBytesPerFile, Levels, LogLevels, GroupBy
type: generated
generated: exp:help:examples
---
# logviewer.ini [DigestSettings]

Der Block `[DigestSettings]` von `settings/logviewer.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Hours

> Die Fehlerübersicht (Setup > Logdateien > Fehlerübersicht, setup/logsdigest): die verschiedenen Meldungen der letzten Hours  
> Stunden jedes Kernel-Logs (oder eines Logs), normalisiert (Zahlen, IDs, Hexadezimalwerte, Werte in Anführungszeichen und Pfade  
> beiseitegelassen) und gezählt, die häufigsten zuerst. Immer maskiert. Bekannte Meldungen werden pro Benutzer ausgeblendet (die Einstellung  
> admin\_logviewer\_known). Die Stunden des Zeitfensters, wenn die Seite keine nennt (1 bis 168).  

Standard: `24`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
Hours=24
*/ ?>
```

## MaxMessages

> Die meisten pro Log aufgeführten Meldungen.  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
MaxMessages=50
*/ ?>
```

## MaxBytesPerFile

> Die meisten Bytes, die pro Log ab dem Beginn des Zeitfensters gelesen werden (der Beginn wird per Binärsuche über die Zeiten gefunden).  

Standard: `8388608`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
MaxBytesPerFile=8388608
*/ ?>
```

## Levels

> Die aufgeführten Stufen.  

Standard: eine Liste mit 2: `error`, `warning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
# ein leeres "Levels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Levels[]
Levels[]=error
Levels[]=warning
*/ ?>
```

## LogLevels

> Die für ein Log aufgeführten Stufen, nach seinem Schlüssel, statt Levels\[\]: eine Kommaliste. Die nicht gefundenen Anfragen (404.log) sind  
> notices, die Übersicht listet sie also nur hierüber auf.  

Standard: ein Hash mit 1: `404=error,warning,notice`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
# ein leeres "LogLevels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LogLevels[]
LogLevels[404]=error,warning,notice
*/ ?>
```

## GroupBy

> Wie die Einträge eines Logs gruppiert werden, nach seinem Schlüssel:  
>   message        die normalisierte Meldung (der Standard)  
>   category-rule  das erste Wort der Zeile, <category>/<rule> (security.log: probe/wp-login, injection/sql ...)  
>   reason         das Feld reason=, ohne IDs und Pfade (404.log: module:foo, view:content/view, node:# ...)  
>   reason-kind    nur die Art des Feldes reason= (module, view, disabled, node, object, rule, kernel)  
>   uri            die Methode und der Pfad, ohne Query, Zahlen beiseitegelassen (GET /blog/#)  
>   directive-blocked  die Direktive und die Herkunft dessen, was sie blockiert hat (csp.log: csp/script-src blocked=inline,  
>                  csp/img-src blocked=https://maps.example.com); der Pfad einer blockierten URI beiseitegelassen  
> Ein Eintrag ohne ein solches Feld wird nach seiner Meldung gruppiert.  

Standard: ein Hash mit 3: `404=reason`, `security=category-rule`, `csp=directive-blocked`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
# ein leeres "GroupBy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupBy[]
GroupBy[404]=reason
GroupBy[security]=category-rule
GroupBy[csp]=directive-blocked
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
