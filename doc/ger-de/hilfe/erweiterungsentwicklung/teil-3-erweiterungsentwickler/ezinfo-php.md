---
title: "ezinfo.php: wer sie gemacht hat und welche Version"
audience: developer, app-creator
category: extension-development
order: 20
summary: Die Metadatendatei ezinfo.php - Klassenname, die statische Methode info(), die Schlüssel, die die Administration liest, wie sie nach extension.xml gelesen wird, die Falle der unquotierten Schlüssel unter PHP 8 mit einer echten Korrektur, und wie Sie sie prüfen.
keywords: Metadaten, Info-Klasse, Version, Lizenz, Über-Seite, PHP 8, unquotierte Schlüssel, ezinfo.php, metadata, Info class, info(), Name, Version, Copyright, License, Info_url, expInfo, about page, PHP 8, bare keys
type: help
---
# ezinfo.php: wer sie gemacht hat und welche Version

Teil 3, Kapitel 2. `ezinfo.php` sagt dem System und seinen Benutzern, was eine Erweiterung ist: ihr Name, ihre
Version, ihr Copyright, ihre Lizenz und ihre Website. **Setup > Über**, **Setup > Extensions** und die
Upgrade-Prüfungen lesen sie. Die Referenz ist
[Metadaten von Erweiterungen](../../../spezifikationen/6.0/extension-metadaten.md); dieses Kapitel zeigt, wie Sie
eine solche Datei schreiben und welche Fehler Sie vermeiden.

## Die Datei

`extension/mysite_hello/ezinfo.php`:

```php
<?php
class mysite_helloInfo
{
    public static function info()
    {
        return array(
            'Name'      => "My Site Hello",
            'Version'   => "1.0.0",
            'Copyright' => "Copyright (C) 2026 Your Organization",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/your-organization/mysite_hello"
        );
    }
}
```

| Regel | Warum |
|---|---|
| Die Klasse ist der Ordnername plus `Info`: `mysite_helloInfo` | das ist der Name, nach dem der Leser sucht |
| `info()` ist `public static` | sie wird statisch aufgerufen |
| Schlüssel sind Strings in Anführungszeichen | ein bloßes `Name` ist ein Zugriff auf eine Konstante, unter PHP 8 ein Fehler (siehe unten) |
| `Name` ist ein lesbarer Name | die Über-Seite zeigt ihn an; ein Ordnername sieht dort kaputt aus |
| `Version` hat kein `v` | der Release-Tag `v1.0.0` ergibt `'Version' => "1.0.0"` |
| `License` ist der volle Name | zum Beispiel `GNU General Public License v2.0 (or any later version)` |
| `Info_url` ist die Projektseite | wird als Website der Erweiterung angezeigt |

Schlüssel werden ohne Beachtung der Groß- und Kleinschreibung verglichen, daher funktionieren ältere Erweiterungen, die
`info_url` schreiben, weiterhin; neue schreiben `Info_url`.

## Wer sie liest, und in welcher Reihenfolge

Der Leser ist `expInfo` (`lib/ezutils/classes/expinfo.php`): `expInfo::extensionInfo( 'mysite_hello' )` gibt die
normalisierten Felder `name`, `description`, `summary`, `version`, `copyright`, `author`, `license` und `info_url`
zurück. Er liest:

1. zuerst `extension.xml`; nicht leere Werte gewinnen,
2. dann `ezinfo.php`, für jedes Feld, das `extension.xml` nicht geliefert hat,
3. dann `composer.json` und den Git-Origin, für das, was dann noch fehlt.

Beide Dateien sollten also vorhanden sein und übereinstimmen. Eine schnelle Prüfung bei geladenem Kernel:

```php
<?php
// php bin/php/ezexec.php extension/mysite_hello/scripts/info.php
$info = expInfo::extensionInfo( 'mysite_hello' );
eZCLI::instance()->output( $info === false ? 'not found' : $info['name'] . ' ' . $info['version'] );
```

## Die Falle unter PHP 8: unquotierte Schlüssel

Ältere Erweiterungen schrieben die Schlüssel ohne Anführungszeichen. In PHP 5 wurde eine nicht definierte Konstante mit
einem Hinweis zu ihrem eigenen Namen; in PHP 8 ist sie ein `Error`, und die Seite, die die Metadaten liest, bricht ab.
Eine echte Korrektur aus se7enxweb/expsite_installer (Commit `0c771fe`, "Fixed ezinfo.php, whose keys were bare words
that PHP 8 reads as undefined constants and stops on"):

```php
// before
return array( Name => "expsite_installer",
              Version => "1.0.1",
              info_url => "https://github.com/se7enxweb/expsite_installer" );

// after
return array( 'Name' => "expsite_installer",
              'Version' => "1.0.1",
              'Info_url' => "https://github.com/se7enxweb/expsite_installer" );
```

`php -l` erkennt das **nicht** (die Datei ist syntaktisch gültig); nur das Ausführen zeigt es. Teil 4 enthält weitere
solche Fälle:
[ezinfo.php und extension.xml modernisieren](../teil-4-aktualisieren/ezinfo-und-extension-xml-modernisieren.md).

## HTML in den Werten

Einige ältere Erweiterungen setzen HTML-Links in `Name` oder `Copyright` (`<a href='...'>`). `expInfo` entfernt die
Tags (`strip_tags()`), wenn es die Felder normalisiert; angezeigt wird also nur der Text, und der Link geht verloren.
Schreiben Sie reinen Text und verwenden Sie `Info_url` für den Link.

## Mehrere Urheberrechtsinhaber

Behalten Sie jeden früheren Inhaber bei, wenn Sie eine Erweiterung übernehmen oder forken, und fügen Sie sich selbst
hinzu:

```php
'Copyright' => "Copyright (C) 2011 - 2014 Original Author Organization, 2026 Your Organization",
```

Siehe
[Namensnennung und Urheberrechtsvermerke](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md).

## Prüfen

```bash
php -l extension/mysite_hello/ezinfo.php
php bin/php/ezexec.php extension/mysite_hello/scripts/info.php
```

Öffnen Sie dann **Setup > Extensions**: Die Karte zeigt Ihren Namen, Ihre Version, Lizenz und Website.

## Bei jedem Release

Die `Version` in `ezinfo.php` und in `extension.xml` entspricht der Release-Version und wird in dem Commit geändert,
auf den der Tag zeigt; siehe
[ezinfo.php und extension.xml hochzählen](../teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md).

## Verwandte Seiten

- Zurück: [Der Verzeichnisaufbau einer Erweiterung](verzeichnisaufbau-einer-erweiterung.md)
- Weiter: [extension.xml: Metadaten und Abhängigkeiten](extension-xml.md)
- [Metadaten von Erweiterungen (Spezifikation)](../../../spezifikationen/6.0/extension-metadaten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
