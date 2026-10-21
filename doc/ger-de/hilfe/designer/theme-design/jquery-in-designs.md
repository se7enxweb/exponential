---
title: jQuery in Designs
audience: designer
category: theme-design
order: 310
summary: Wann ein Design jQuery braucht, wie Sie das mitgelieferte jQuery 4 mit ezjsc::jquery und ezjsc::jqueryUI laden, jQuery Migrate für älteren Code, lokales Laden gegenüber CDN, die Entfernung von YUI und wann reines JavaScript die bessere Wahl ist.
keywords: jQuery, jQuery 4, jQuery Migrate, jQuery UI, ezjsc::jquery, ezjsc::jqueryUI, LoadFromCDN, LocalScripts, YUI entfernt, reines JavaScript, Skripte eines Designs, jquery, jquery migrate, jquery ui, YUI removed, vanilla javascript, design scripts
type: help
---
# jQuery in Designs

Diese Seite ist für Designer, deren Skripte jQuery verwenden oder die sich fragen, ob sie es sollten. Sie beantwortet: welches jQuery
die Installation mitliefert, wie Sie es laden und was sich mit jQuery 4 und der Entfernung von YUI geändert hat.

## Inhalt

1. [Was mitgeliefert wird](#was-mitgeliefert-wird)
2. [Es laden](#es-laden)
3. [Lokal oder CDN](#lokal-oder-cdn)
4. [Älterer Code und jQuery Migrate](#älterer-code-und-jquery-migrate)
5. [YUI ist weg](#yui-ist-weg)
6. [Brauchen Sie jQuery überhaupt?](#brauchen-sie-jquery-überhaupt)

## Was mitgeliefert wird

`extension/ezjscore/design/standard/javascript/` enthält jQuery 4.0.0, jQuery Migrate 4.0.2 und jQuery UI 1.14.2 mit ihren
Lizenzdateien. `ezjscore.ini [eZJSCore]` nennt sie:

```ini
LocalScripts[jquery]=jquery-4.0.0.min.js
LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.min.js
LocalScripts[jqueryUI]=jquery-ui-1.14.2.min.js
```

## Es laden

Nennen Sie den Generatorschlüssel im selben Aufruf vor Ihrem eigenen Skript, damit die Reihenfolge stimmt:

```tpl
{ezscript_load( array( 'ezjsc::jquery', 'mytheme.js' ) )}
```

`ezjsc::jquery` gibt jQuery gefolgt von jQuery Migrate aus; `ezjsc::jqueryUI` gibt jQuery UI aus. Mehrere Templates können
`ezjsc::jquery` anfordern; es wird einmal ausgegeben.

## Lokal oder CDN

`LoadFromCDN=disabled` (Standard) liefert die lokalen Dateien aus, die der Packer bündeln kann und die eine Content Security Policy mit
`script-src 'self'` erlaubt. Mit `LoadFromCDN=enabled` werden stattdessen die URLs aus `ExternalScripts[]` (code.jquery.com) verwendet;
das braucht den CDN-Host in der Richtlinie der Website und schickt die Anfragen der Besucher an einen Drittanbieter. Behalten Sie den
Standard, wenn Sie keinen Grund haben.

## Älterer Code und jQuery Migrate

jQuery 4 hat alte APIs entfernt. jQuery Migrate 4.0.2, das `ezjsc::jquery` nach jQuery lädt, stellt sie wieder her, sodass älterer Code
weiter funktioniert. Der mitgelieferte Build ist still; um jeden alten Aufruf, den er wiederherstellt, in der Konsole des Browsers zu
sehen, setzen Sie auf einem Entwicklungs-Siteaccess den meldenden Build:

```ini
# ezjscore.ini.append.php of a development siteaccess
[eZJSCore]
LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.js
```

Beheben Sie die gemeldeten Aufrufe; dann kann ein Design jQuery 4 allein laden (`LocalScripts[jqueryMigrate]=` leer, laut dem Kommentar
in `ezjscore.ini`).

## YUI ist weg

ezjscore 1.5.0 hat YUI 2 und 3 entfernt (`ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` und die Einstellungen `[YUI3]`). Ein Design, das
sie noch anfordert, bekommt nichts. Durchsuchen Sie Ihr Design:

```bash
grep -rn "ezjsc::yui2\|ezjsc::yui3\|YUI(" extension/mytheme/design
```

und ersetzen Sie jede Verwendung durch jQuery, die Exponential-UI-Module von `expui` oder reines JavaScript
([ezjscore](../../../../eng-us/features/6.0/extensions/ezjscore.md), „Upgrade to 1.5“).

## Brauchen Sie jQuery überhaupt?

Für einen Menüschalter, einen Dialog oder einen fetch-Aufruf genügen die eigenen APIs aktueller Browser (`querySelector`, `classList`,
`fetch`, `<dialog>`, `addEventListener`). Ein öffentliches Design ohne jQuery spart dessen Download bei jedem ersten Besuch. Verwenden
Sie jQuery, wenn Sie auf Templates oder Erweiterungen aufbauen, die es brauchen (das Design simple lädt es, ebenso mehrere
Erweiterungen).

## Verwandte Seiten

- [JavaScript laden](../../../../eng-us/help/designers/theme-design/loading-javascript.md)
- [Kein Inline-JavaScript](../../../../eng-us/help/designers/theme-design/no-inline-javascript.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
