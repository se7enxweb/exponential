---
title: Komponenten Dritter und ihre Lizenzen
audience: bundler, partner, developer
category: bundling
order: 130
summary: Wie ein Bündler jede Komponente in einem Bundle und ihre Lizenz findet - Composer-Pakete, Erweiterungen, JavaScript- und CSS-Bibliotheken, Seitenpakete, die Laufzeitumgebung und das Betriebssystem - wie er die Liste der Komponenten Dritter erstellt und wie er eine unpassende Lizenz erkennt. Keine Rechtsberatung.
keywords: Komponenten Dritter, Lizenzen, Lizenz einer Erweiterung, JavaScript-Bibliothek, Lizenz eines Seitenpakets, Betriebssystem, Bestandsaufnahme, Vereinbarkeit, third-party, licenses, composer licenses, SPDX, extension license, JavaScript library, site package license, operating system, inventory, THIRD-PARTY, compatibility
type: help
---
# Komponenten Dritter und ihre Lizenzen

Diese Seite richtet sich an Bündler, die angeben müssen, was in ihrem Bundle steckt und unter welchen Bedingungen.
Exponential selbst steht unter der GPL Version 2 oder später; ein Bundle darum herum enthält viel mehr. Keine
Rechtsberatung: Fragen Sie einen Rechtsbeistand zu Kombinationen, bei denen Sie unsicher sind.

## Inhalt

1. [Die Schichten eines Bundles](#die-schichten-eines-bundles)
2. [Composer-Pakete](#composer-pakete)
3. [Erweiterungen](#erweiterungen)
4. [Seitenpakete und Inhalte](#seitenpakete-und-inhalte)
5. [Laufzeitumgebung und Betriebssystem](#laufzeitumgebung-und-betriebssystem)
6. [Die Liste der Komponenten Dritter](#die-liste-der-komponenten-dritter)
7. [Warnzeichen](#warnzeichen)

## Die Schichten eines Bundles

| Schicht | Wie Sie sie auflisten | Lizenzangaben aus |
|---|---|---|
| Composer-Pakete (`vendor/`, mit Composer installierte Erweiterungen) | `composer licenses` | dem Feld `license` in der `composer.json` jedes Pakets |
| Erweiterungen | dem Verzeichnis `extension/`, **Info > Über** | `ezinfo.php`, `extension.xml`, `LICENSE` |
| Bibliotheken in Erweiterungen (JavaScript, CSS, Schriften) | den Dateien und der README der Erweiterung | dem eigenen Lizenzkopf oder der Lizenzdatei der Bibliothek |
| Seitenpakete | der `package.xml` jedes Pakets | dem Lizenzeintrag des Pakets ([Lizenzfelder](lizenzfelder-in-ihren-eigenen-paketen.md)) |
| Laufzeitumgebung: PHP, Velocity, ein Webserver, eine Datenbank | Ihrer Image- oder Paketliste | deren Projekten |
| Betriebssystem | der Paketliste Ihrer Distribution | der Distribution |

## Composer-Pakete

`composer licenses` (ein eingebauter Composer-Befehl) gibt jedes installierte Paket mit der Lizenz aus, die seine
`composer.json` angibt. Führen Sie ihn im gebauten Bundle aus und bewahren Sie die Ausgabe mit dem Release auf:

```bash
composer licenses --no-dev > THIRD-PARTY-composer.txt
composer licenses --no-dev --format=json > var/log/bundle-licenses.json
```

Ein Paket ohne Lizenzfeld erscheint als "none"; prüfen Sie es von Hand, bevor Sie es ausliefern.

## Erweiterungen

Die Seite **Info > Über** eines installierten Bundles listet jede Erweiterung "mit ihrer Lizenz und Website" auf, dazu
eine Tabelle **Third-party software** mit "Software, Version, Lizenz und der Erweiterung, die sie enthält"
([Die Seiten Über und Copyright](../../funktionen/6.0/info-und-paketseiten.md)). Nutzen Sie sie zum Abgleich mit
Ihrer Liste; eine Erweiterung, die dort keine Lizenz zeigt, braucht einen Blick in ihre Dateien.

Erweiterungen, die mit den RAD-Assistenten und dem Paketassistenten erstellt wurden, wählen ihre Lizenz aus der
festen Liste in `settings/package.ini [LicenseSettings]`
([Paketlizenzen und Semantic Versioning](../../funktionen/6.0/paketlizenzen-und-versionen.md)).

## Seitenpakete und Inhalte

Ein Seitenpaket kann Klassen, Templates, Bilder, Styles und Demo-Inhalte enthalten. Die Lizenz des Pakets gilt für
alles davon, sofern das Paket nichts anderes sagt. Vor allem Demobilder können eine eigene Lizenz haben; lesen Sie die
Dokumente des Pakets, bevor Sie seine Demo-Inhalte an Kunden ausliefern
([Anforderungen an die Dokumentation: Lizenz und Dokumente](../seitenpakete/anforderungen/lizenz-und-dokumente.md)).

## Laufzeitumgebung und Betriebssystem

- **Velocity** ist ein Composer-Paket (`se7enxweb/exponential-velocity`) und wird außerdem als deb- und rpm-Pakete,
  Docker-Images und statische Binärdateien veröffentlicht
  ([Velocity-Pakete, Docker-Images und Binärdateien](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)).
  Seine Lizenz steht in seinem Repository.
- **FrankenPHP** ist, falls Sie es verwenden, eine heruntergeladene Binärdatei, die Caddy, PHP und PHP-Erweiterungen
  bündelt ([FrankenPHP](../../bc/6.0/frankenphp.md)); führen Sie es mit seiner eigenen Lizenz auf.
- **PHP, die Datenbank und das Betriebssystem** haben ihre eigenen Bedingungen; die Werkzeuge Ihrer Distribution
  listen sie auf.

## Die Liste der Komponenten Dritter

Liefern Sie eine Liste (`THIRD-PARTY.txt` oder einen Abschnitt Ihres Hinweises) in dieser Form aus:

```text
Component            Version     License               Source / where to get it
se7enxweb/exponential 6.0.14     GPL-2.0-or-later      https://github.com/se7enxweb/exponential
<extension>          <version>   <license>             <URL>
<library>            <version>   <license>             <URL>
PHP                  <version>   <license>             <URL>
...
```

Erzeugen Sie den Composer-Teil mit `composer licenses`; ergänzen Sie den Rest von Hand anhand der Tabelle oben.

## Warnzeichen

| Sie sehen | Fragen Sie |
|---|---|
| Eine Komponente ganz ohne Lizenz | Dürfen Sie sie weitergeben? Ohne Lizenz lautet die Antwort meist nein. |
| Eine Creative-Commons-Lizenz mit "nicht kommerziell" oder "keine Bearbeitung" auf Code oder Bildern | Passt die Verwendung in Ihrem Bundle zu diesen Bedingungen? |
| Eine proprietäre Erweiterung neben GPL-Code | Darf sie im selben Bundle weitergegeben werden? Fragen Sie einen Rechtsbeistand. |
| `LicenseRef-Proprietary` in einem Paket | Das bedeutet "alle Rechte vorbehalten" ([Paketlizenzen](../../funktionen/6.0/paketlizenzen-und-versionen.md)): Holen Sie eine schriftliche Erlaubnis ein. |
| Eine in eine Erweiterung kopierte Bibliothek ohne ihren Lizenzkopf | Stellen Sie den Kopf wieder her oder ersetzen Sie die Bibliothek. |

## Verwandte Seiten

- [GPL-Pflichten beim Weitergeben](gpl-pflichten-beim-weitergeben.md)
- [Mitzuliefernde Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)
- [Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
