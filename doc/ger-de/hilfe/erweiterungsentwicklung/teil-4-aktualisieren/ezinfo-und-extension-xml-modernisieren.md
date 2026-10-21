---
title: ezinfo.php und extension.xml modernisieren
audience: developer, app-creator
category: extension-development
order: 110
summary: Bringen Sie ezinfo.php und extension.xml einer alten Erweiterung auf den aktuellen Stand - Schlüssel in Anführungszeichen, ein lesbarer Name, die Release-Version, die Lizenz, die Website und die Nennung enthaltener Software - und prüfen Sie, was die Seite „Über“ anzeigt.
keywords: Metadaten, Version, Lizenz, Seite Über, Upgrade, Nennung der Urheber, ezinfo.php, extension.xml, metadata, version, license, Info_url, Includes, about page, expInfo, upgrade, credit
type: help
---
# ezinfo.php und extension.xml modernisieren

Teil 4, Kapitel 11. Zwei kleine Dateien sagen dem System und den Menschen, die es betreiben, was eine Erweiterung ist:
ihren Namen, ihre Version, Lizenz, ihr Copyright und ihre Website. Bei alten Erweiterungen sind sie oft falsch,
unvollständig oder fehlen, und die Seite „Über“ der Administration zeigt dann einen Verzeichnisnamen und leere
Spalten. Dieses Kapitel aktualisiert beide Dateien. Was jedes Feld bedeutet, steht in Teil 3
([ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md),
[extension.xml](../teil-3-erweiterungsentwickler/extension-xml.md)); hier
sehen wir uns die typischen alten Formen an und wie Sie sie korrigieren.

## Wer diese Dateien liest

Die Kernel-Klasse `expInfo` (`lib/ezutils/classes/expinfo.php`) liest beide Dateien und führt sie zusammen: zuerst
`extension.xml`, dann `ezinfo.php` für alles, was noch fehlt, dann `composer.json` und den Git-Origin. Die Seite
„Über“ (die View `ezinfo/about` der Administration) zeigt das Ergebnis, eine Zeile pro Erweiterung. Die Schlüssel
von `ezinfo.php` werden ohne Beachtung der Groß- und Kleinschreibung verglichen. Die vollständigen Regeln stehen in der
[Spezifikation der Erweiterungs-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md).

## Die alten Formen, denen Sie begegnen werden

### Nackte Schlüssel (ein fataler Fehler unter PHP 8)

```php
// before: se7enxweb/expsite_installer, before commit 0c771fe
return array( Name => "expsite_installer",
              Version => "1.0.1",
              Copyright => "Copyright (C) 1998 - 2026 7x. All rights reserved.",
              License => "GNU General Public License v2.0 (or any later version)",
              info_url => "https://github.com/se7enxweb/expsite_installer" );
```

PHP 8 liest `Name` als Konstante und bricht ab. Setzen Sie jeden Schlüssel in Anführungszeichen. Siehe
[PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md).

### Eine Platzhalter-Version

```xml
<version>//autogentag//</version>
```

`//autogentag//` wurde im alten Release-Verfahren von einem Build-Skript ersetzt. Dieses Skript gehört nicht mehr zum
Leben einer Erweiterung, also bleibt der Platzhalter stehen, und die Seite „Über“ zeigt ihn an. Schreiben Sie die
echte Version hinein.

### Ein Verzeichnisname als Name

```php
'Name' => "myoldext",
```

Der Name wird Menschen angezeigt. Verwenden Sie einen lesbaren Namen („My Old Extension“). Die Spezifikation hält
fest, dass drei Erweiterungen bis zu ihrem Release vom 30. September 2026 auf der Seite „Über“ ihren Verzeichnisnamen
zeigten.

### Eine alte Website

`'Info_url' => "http://ez.no/developer/contribs/..."` und ähnliche Adressen zeigen auf Websites, die es nicht mehr
gibt. Verwenden Sie das Repository der Erweiterung auf GitHub oder ihre Seite auf
[projects.exponential.earth](https://projects.exponential.earth), über https.

### Keine Lizenz

Eine fehlende Lizenz ist keine Formalität: Ohne sie darf niemand die Erweiterung rechtmäßig weitergeben. Wenn die
Dateien der alten Erweiterung einen Lizenzkopf tragen (die meisten tragen die GPL), übernehmen Sie dessen genauen
Namen in beide Dateien. Wenn sie keinen tragen, fragen Sie den Autor, bevor Sie etwas veröffentlichen
([Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)).

## Die moderne ezinfo.php

```php
<?php
class myoldextInfo
{
    public static function info()
    {
        return array( 'Name' => "My Old Extension",
                      'Version' => "2.0.0",
                      'Copyright' => "Copyright (C) 2009 The Original Author; Copyright (C) 2026 Your Name",
                      'License' => "GNU General Public License v2.0 (or any later version)",
                      'Info_url' => "https://github.com/your-account/myoldext" );
    }
}
```

| Regel | Warum |
|---|---|
| Klassenname: der Verzeichnisname plus `Info` | so findet der Kernel sie |
| `info()` ist `public static` | sie wird statisch aufgerufen |
| Schlüssel stehen in Anführungszeichen | PHP 8 |
| `Version` ohne `v` | Tag `v2.0.0` ist Version `2.0.0` |
| Das ursprüngliche Copyright behalten, Ihres hinzufügen | Sie sind nicht der einzige Autor; siehe [Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md) |

## Nennung von Software, die Sie einbinden

Wenn die Erweiterung Code von jemand anderem enthält (einen Fork einer älteren Erweiterung, eine mitgelieferte
Bibliothek), sagen Sie das mit einem Schlüssel, der mit `Includes` beginnt. Die Seite „Über“ listet jeden Eintrag
unter der Zeile der Erweiterung auf. Aus se7enxweb/expchangeclass, einem Fork einer älteren Erweiterung:

```php
'Includes' => array(
    array( 'Name' => "eZChangeclass",
           'Copyright' => "Copyright (C) 2007 (the original author)",
           'License' => "GNU General Public License v2.0" ) )
```

(Der Name des ursprünglichen Autors steht in der echten Datei; hier ist er gekürzt.) Jeder Eintrag kann `Name`,
`Version`, `License`, `Copyright` und `Info_url` haben. Ein einzelner Eintrag kann auch als ein Hash statt als Liste
angegeben werden.

## Die moderne extension.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<software>
    <metadata>
        <name>My Old Extension</name>
        <version>2.0.0</version>
        <copyright>Copyright (C) 2009 The Original Author; Copyright (C) 2026 Your Name</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/your-account/myoldext</info_url>
    </metadata>
    <dependencies>
        <requires>
            <extension name="ezjscore" />
        </requires>
    </dependencies>
</software>
```

`extension.xml` wird zuerst gelesen, daher müssen die beiden Dateien übereinstimmen. Die flache Form
(`<extension><name>...</name> <version>...</version></extension>`), die manche Erweiterungen verwenden, darunter
se7enxweb/expchangeclass, wird auf dieselbe Weise gelesen.

`<requires>` ist für die Ladereihenfolge wichtig: Eine Erweiterung, die zum Beispiel den Packer von ezjscore braucht,
sollte das angeben ([Ladereihenfolge der Erweiterungen](../teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md)).

## Ihre Arbeit prüfen

```bash
cd extension/myoldext
php -l ezinfo.php
xmllint --noout extension.xml
grep -n "Version\|<version>" ezinfo.php extension.xml
```

Erwartet: „No syntax errors detected“, keine Ausgabe von `xmllint` und zweimal dieselbe Version. Öffnen Sie dann die
Seite „Über“ in der Administration und suchen Sie die Zeile Ihrer Erweiterung: Name, Version, Lizenz und Website
sollten alle ausgefüllt sein.

## Wenn Sie veröffentlichen

Jedes Release erhöht die Version in beiden Dateien auf genau die Nummer des Releases, in dem Commit, auf den der Tag
zeigt, niemals danach. Das ist Teil des Release-Verfahrens in
[ezinfo.php und extension.xml hochzählen](../teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md).

## Verwandte Seiten

- Zurück: [Datenbankcode für PHP 8 und jede Engine](datenbankcode-fuer-php-8.md)
- Weiter: [composer.json modernisieren](composer-json-modernisieren.md)
- [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md) und [extension.xml](../teil-3-erweiterungsentwickler/extension-xml.md)
- [Spezifikation der Erweiterungs-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
