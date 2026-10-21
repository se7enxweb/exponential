---
title: "Von Hand: die kleinste Erweiterung in fünf Minuten"
audience: developer, app-creator, designer
category: extension-development
order: 100
summary: Drei Dateien ergeben eine vollständige Erweiterung - ezinfo.php, extension.xml und eine Einstellungsdatei. Legen Sie sie an, prüfen Sie sie, schalten Sie die Erweiterung ein, sehen Sie sie unter Setup > Extensions und sehen Sie, wie ihre Einstellung greift, und stellen Sie sie dann unter Git.
keywords: Tutorial, von Hand, minimale Erweiterung, erste Erweiterung, Einstellungen, aktivieren, tutorial, by hand, minimal extension, first extension, ezinfo.php, extension.xml, settings, activate, setup extensions, git init
type: help
---
# Von Hand: die kleinste Erweiterung in fünf Minuten

Tutorial 9. Dies ist die kleinste nützliche Erweiterung: Sie hat Metadaten, damit die Administration weiß, was sie ist,
und eine Einstellung, damit sie etwas Sichtbares ändert. Jedes spätere Tutorial baut auf demselben Ordner auf,
`extension/mysite_hello`. Die Dateien unten haben `php -l` und `xmllint` bestanden.

## 1. Der Ordner

```bash
mkdir -p extension/mysite_hello/settings
cd extension/mysite_hello
git init
```

## 2. ezinfo.php

```php
<?php
class mysite_helloInfo
{
    public static function info()
    {
        return array(
            'Name'      => "My Site Hello",
            'Version'   => "0.1.0",
            'Copyright' => "Copyright (C) 2026 Your Organization",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/your-organization/mysite_hello"
        );
    }
}
```

Die Klasse heißt wie der Ordner plus `Info`; die Schlüssel stehen in Anführungszeichen; `info()` ist statisch
([ezinfo.php](../ezinfo-php.md)).

## 3. extension.xml

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>My Site Hello</name>
        <version>0.1.0</version>
        <copyright>Copyright (C) 2026 Your Organization</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/your-organization/mysite_hello</info_url>
    </metadata>
</software>
```

## 4. Eine Einstellung

`settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=Hello from mysite_hello

*/ ?>
```

## 5. Prüfen

```bash
php -l ezinfo.php
php -l settings/site.ini.append.php
xmllint --noout extension.xml
cd ../..
```

Erwartet: zwei Zeilen `No syntax errors detected` und keine Ausgabe von `xmllint`.

## 6. Einschalten

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezcache.php --clear-tag=ini
```

## 7. Ansehen

- **Setup > Extensions**: eine Karte „My Site Hello“, Version 0.1.0, Ihre Lizenz und Website.
- Die Einstellung:

  ```bash
  ./console exp:ini where site.ini/SiteSettings/SiteName
  ```

  führt Ihre Datei auf. Ob sie greift, hängt von der Reihenfolge ab: Eine spätere Datei (der globale Override oder eine
  Erweiterung, die vor Ihrer aufgeführt ist) kann `SiteName` ebenfalls setzen; die letzte Zeile der Ausgabe nennt den
  wirksamen Wert. Siehe
  [Geltungsbereiche von Einstellungen und ihre Rangfolge](../../teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md).

## 8. Committen

```bash
git -C extension/mysite_hello add ezinfo.php extension.xml settings/site.ini.append.php
git -C extension/mysite_hello commit -m "Added: The mysite_hello extension with its metadata and a site name"
```

## Rückgängig machen

```bash
./console exp:ini rem 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezcache.php --clear-tag=ini
```

Die nächsten Tutorials lassen die Erweiterung aktiv und fügen einen Template-Operator, eine Fetch-Funktion, ein Modul und
mehr hinzu. Die Zeile `SiteName` war nur für diese Übung gedacht: Entfernen Sie sie, bevor Sie weitermachen.

## Verwandte Seiten

- Zurück: [Mit RAD: Handler und Kernel-Overrides](rad-handler-und-kernel-overrides.md)
- Weiter: [Von Hand: ein Template-Operator](von-hand-template-operator.md)
- [Der Verzeichnisaufbau einer Erweiterung](../verzeichnisaufbau-einer-erweiterung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
