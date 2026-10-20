---
title: Warum die meisten Änderungen klein sind
audience: developer, app-creator
category: extension-development
order: 10
summary: Warum eine vor Jahren geschriebene Erweiterung für Exponential 6 meist nur kleine Änderungen braucht - das Erweiterungssystem, die Klassennamen und die Einstellungen sind geblieben, und die Änderungen kommen von PHP und vom Browser.
keywords: Upgrade, alte Erweiterung, Kompatibilität, Erweiterungssystem, beibehaltene APIs, Aufwand, Schätzung, upgrade, old extension, compatibility, PHP 8, extension system, kept APIs, effort, estimate
type: help
---
# Warum die meisten Änderungen klein sind

Teil 4, Kapitel 1. Dieses Kapitel ist für alle, die eine alte Erweiterung betrachten und sich fragen, ob sich der
Aufwand lohnt. Es erklärt, was gleich geblieben ist, woher die Änderungen kommen und wie Sie die Arbeit schätzen,
bevor Sie beginnen.

## Was gleich geblieben ist

Exponential 6 ist die Fortführung des PHP-Kernels für Content-Management, für den alte Erweiterungen geschrieben
wurden. Die Stellen, an die sich eine Erweiterung anschließt, wurden bewusst beibehalten:

| Was eine Erweiterung verwendet | In Exponential 6 |
|---|---|
| Der Ordner `extension/<name>/` mit `settings/`, `design/`, `modules/`, `classes/`, `autoloads/`, `translations/` | derselbe Aufbau, auf dieselbe Weise gelesen ([Der Verzeichnisaufbau einer Erweiterung](../teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md)) |
| Die Aktivierung in `site.ini` `[ExtensionSettings] ActiveExtensions[]` | dieselbe Einstellung |
| Klassennamen wie `eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`, `eZDataType` | dieselben Namen; neuer Kernel-Code verwendet ein `exp`-Präfix, aber die alten Klassen wurden nie umbenannt |
| `module.php` mit `$Module`, `$ViewList`, `$FunctionList` | dieselben Arrays |
| Template-Operatoren, registriert über `eztemplateautoload.php` | dasselbe `$eZTemplateOperatorArray` |
| Datentypen, Workflow-Ereignistypen, Cronjob-Teile, Fetch-Funktionen | dieselben Basisklassen und Einstellungen |
| Die Template-Sprache, ihre Operatoren und die Design-Kette | dieselbe, um Operatoren ergänzt |

Eine Erweiterung ist also selten als Ganzes „inkompatibel“. Was bricht, ist hier eine Zeile und dort eine Zeile.

## Woher die Änderungen kommen

Fast jede Änderung, die Sie vornehmen werden, hat eine von drei Ursachen.

### 1. PHP selbst hat sich weiterentwickelt

Alte Erweiterungen wurden für PHP 5 geschrieben. PHP 8 hat viele Warnungen in Fehler verwandelt, Funktionen entfernt,
eine Methode mit dem Namen ihrer Klasse nicht mehr als Konstruktor behandelt und markiert in jedem Minor-Release
ein paar weitere Dinge als veraltet. Das ist die größte Gruppe, und sie wird in den PHP-Kapiteln dieses Teils
behandelt, beginnend mit [PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md).

Die meisten dieser Korrekturen sind eine einzige Zeile. Hier eine typische, aus se7enxweb/expsite_api, Commit
0f708f4:

```php
// before
public function __construct( eZContentObject $object = null )

// after
public function __construct( ?eZContentObject $object = null )
```

Ein Fragezeichen. PHP 8.4 meldet die alte Form bei jeder Anfrage, die das Objekt erzeugt, als veraltet.

### 2. Der Browser ist strenger geworden

Exponential 6 kann eine Content Security Policy senden. Eine Policy ohne `'unsafe-inline'` lässt den Browser
Inline-`<script>`-Blöcke, `onclick`-Attribute und `style=""`-Attribute verweigern. Die Administrationsseite einer
alten Erweiterung kann richtig aussehen und trotzdem nichts tun, wenn Sie klicken. Die Lösung besteht darin, den Code
in Dateien zu verschieben; das behandeln die CSP-Kapitel, beginnend mit
[CSP: Inline-Skripte in Dateien verschieben](csp-inline-skripte-auslagern.md).

Auch die JavaScript-Bibliotheken haben sich geändert: YUI 2 und YUI 3 wurden aus der Administration entfernt, und
jQuery liegt in Version 4 vor. Siehe [Von YUI zu expui](yui-zu-expui.md) und
[Vom alten jQuery zu jQuery 4](altes-jquery-zu-jquery-4.md).

### 3. Die Konventionen des Projekts

Exponential 6 hat Konventionen, die eine alte Erweiterung nicht kannte: eine Version in `ezinfo.php` und
`extension.xml`, die zum Release passt, eine Lizenz und eine Website in beiden, eine `composer.json`, die sie
installiert, Administrationsseiten, die mit den gemeinsamen admin4-Komponenten statt mit eigenem CSS gestaltet sind.
Keine davon hindert die Erweiterung am Laufen; alle machen es leichter, sie zu installieren, ihr zu vertrauen und sie
zu pflegen. Siehe [ezinfo.php und extension.xml modernisieren](ezinfo-und-extension-xml-modernisieren.md).

## Wie die Zahlen aussahen

Im Oktober 2026 hat dieses Projekt jede Erweiterung, die es ausliefert, gegen PHP 8.0 geprüft, die älteste Version,
die Exponential 6 unterstützt. Die meisten brauchten nur eine höhere Versionsnummer und eine weitere PHP-Bedingung in
`composer.json`; einige brauchten echte Korrekturen. Beispiele, alle auf GitHub unter se7enxweb:

| Erweiterung | Commit | Was sie brauchte |
|---|---|---|
| expchangeclass | 32196f0 | nichts im Code: `composer.json` erlaubt `^8.0`, Version in drei Dateien erhöht |
| expsite_installer | 0c771fe | Schlüssel in `ezinfo.php` in Anführungszeichen (nackte Wörter sind in PHP 8 ein Fehler) |
| expsite_api | 0f708f4 | zwei Konstruktoren mit einem Nullable-Parameter |
| expquery_translator | 8ddf68d | 35 Parameter in 27 Dateien explizit nullable gemacht |
| sevenx_dse | b02ff09 | `utf8_decode()` in zwei Zeilen durch `mb_strlen()` ersetzt |
| cjw_tmv | 0bfa27b | `curl_close()` und `imagedestroy()` nur noch auf PHP vor 8.0 |
| ezfind | 7ff9824 | fünf Klassen, deren einziger Konstruktor eine Methode mit dem Namen der Klasse war |

Die letzte Zeile ist die Ausnahme, die die Regel bestätigt: eine Änderung an fünf Methoden, aber ohne sie waren die
Suchergebnisse leer, und das Indexierungsskript brach sofort ab. Kleine Änderungen sind nicht immer unwichtige.

## Den Aufwand schätzen, bevor Sie beginnen

Bevor Sie jemandem einen Termin zusagen, nehmen Sie sich eine halbe Stunde für eine Bestandsaufnahme
([Bestandsaufnahme einer alten Erweiterung](bestandsaufnahme-einer-alten-erweiterung.md)). Diese Anzeichen bedeuten
wenig Arbeit:

- Die Erweiterung hat kein eigenes PHP (nur Einstellungen, Templates und Übersetzungen). Templates brauchen für PHP 8
  fast nie Änderungen.
- Ihre PHP-Dateien bestehen `php -l` unter PHP 8.0.
- Ihre Administrationsseiten haben keine `<script>`-Blöcke und keine `on...=`-Attribute.

Diese Anzeichen bedeuten mehr Arbeit:

- Sie liefert eine eigene Kopie einer JavaScript-Bibliothek mit (YUI, ein altes jQuery, ein altes TinyMCE-Plugin).
- Sie ändert Kernel-Dateien oder liefert Ersatz-Kernel-Klassen mit („Kernel-Overrides“).
- Sie spricht mit der Datenbank über `mysql_*`-Funktionen statt über `eZDB`.
- Sie verwendet `create_function()`, `each()`, `ereg()` oder `split()`.
- Sie hängt von einem Dienst oder einer Bibliothek ab, die es nicht mehr gibt.

> **Tipp**
> Aktualisieren Sie in kleinen Commits, eine Art von Änderung pro Commit („Konstruktoren“, „Nullable-Parameter“,
> „Inline-Skripte“). Wenn später etwas bricht, sagt Ihnen `git log`, welche Art von Änderung es war, und ein Reviewer
> kann jeden Commit in einer Minute lesen. Siehe [Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](../teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md).

## Wann sich ein Upgrade nicht lohnt

Manchmal lautet die ehrliche Antwort Nein:

- Der Kernel macht inzwischen, was die Erweiterung gemacht hat. Verwenden Sie die Kernel-Funktion und legen Sie die
  Erweiterung still.
- Die Erweiterung hängt von einem externen Dienst ab, der eingestellt wurde.
- Die Lizenz erlaubt Ihnen nicht, sie zu ändern oder zu veröffentlichen, und der Autor ist nicht erreichbar.

Schreiben Sie in diesen Fällen auf, was die Erweiterung getan hat, finden oder bauen Sie einen Ersatz und halten Sie
das in den Notizen Ihres Projekts fest. [Eine alte Erweiterung bewerten](../teil-1-vom-benutzer-zum-entwickler/eine-alte-erweiterung-bewerten.md)
hat eine längere Checkliste.

## Verwandte Seiten

- Zurück: [Teil 4: Eine alte Erweiterung auf Exponential 6 aktualisieren](README.md)
- Weiter: [Die Upgrade-Checkliste](die-upgrade-checkliste.md)
- [PHP-8-Unterstützung](../../../../eng-us/bc/6.0/php8.md)
- [Verhaltensänderungen für Erweiterungen](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
