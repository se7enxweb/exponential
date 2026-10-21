---
title: "Fallstudie: kleine PHP-8-Korrekturen in vielen Erweiterungen"
audience: developer, app-creator
category: extension-development
order: 260
summary: Wie dieses Projekt im Oktober 2026 mehr als zwanzig Erweiterungen auf PHP 8.0 bis 8.5 gebracht hat - immer wieder dieselben wenigen Korrekturen, ein Commit und ein Release pro Erweiterung, und wie die Arbeit geprüft wurde.
keywords: Fallstudie, viele Erweiterungen, Konstruktoren, statische Aufrufe, Release, Upgrade, case study, PHP 8.0, PHP 8.5, many extensions, curl_close, nullable, ezinfo, constructors, static calls, composer.json, release, upgrade
type: help
---
# Fallstudie: kleine PHP-8-Korrekturen in vielen Erweiterungen

Teil 4, Kapitel 26, das letzte dieses Teils. Die anderen Fallstudien folgen einer Erweiterung. Diese folgt einer Art
von Arbeit über viele hinweg: Im Oktober 2026 hat das Projekt jede Erweiterung, die es ausliefert, gegen PHP 8.0, die
älteste Version, die Exponential 6 unterstützt, und gegen PHP 8.5, die neueste, geprüft. Sie zeigt, was Sie finden,
wenn Sie eine ganze Sammlung aktualisieren, und wie Sie diese Arbeit organisieren.

## Was gefunden wurde

Die Commits, in den eigenen Repositorys der Erweiterungen unter se7enxweb auf GitHub, lassen sich wenigen Arten
zuordnen:

| Art | Beispiele | Kapitel |
|---|---|---|
| Nur die PHP-Bedingung und die Version | expchangeclass 32196f0, explayouts a58d5f3, explayouts_core 4834db1, expsite_api cfc9e02, expquery_translator f18e9b4, sevenx_themes_media 848b146 | [composer.json modernisieren](composer-json-modernisieren.md) |
| `ezinfo.php` mit nackten Schlüsseln | expsite_installer 0c771fe, expsite_data_media 81b88ef | [ezinfo.php und extension.xml](ezinfo-und-extension-xml-modernisieren.md) |
| Implizit nullable Parameter | expsite_api 0f708f4, expquery_translator 8ddf68d, ezfind 4134cbb | [Nullable-Parameter](nullable-parameter-und-typen.md) |
| In 8.5 als veraltet markierte Funktionen | cjw_tmv 0bfa27b (`curl_close()`, `imagedestroy()`), sevenx_authentication_2fa d36f58a, sevenx_themes_media da7c923 (`curl_close()`), sevenx_dse dbeacb3 (`$http_response_header`) | [Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md) |
| In 8.2 als veraltet markierte Funktionen | sevenx_dse b02ff09 (`utf8_decode()`) | dasselbe |
| Konstruktoren, die PHP 8 nicht aufruft | ezfind 7ff9824 (fünf Klassen) | [Konstruktoren alten Stils](alte-konstruktoren-und-statische-aufrufe.md) |
| Statische Aufrufe und fehlende Methoden | sevenx_themes_media f7d93c0, explayouts_site_api 031d380 | [PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md) |
| `method_exists()` auf `false` | explayouts_ui_api 2a95edc | dasselbe |

Die meisten Erweiterungen brauchten nur die erste Zeile. Einige brauchten eine echte Korrektur. Eine (ezfind) brauchte
fünf Konstruktoren, und ohne sie waren die Suchergebnisse leer: Die Größe einer Korrektur sagt nichts über ihre
Bedeutung.

## Wie die Arbeit organisiert war

### Eine Erweiterung, ein Commit pro Art, ein Release

Jede Erweiterung bekam ihre eigenen Commits, jeder zu einer Art von Änderung, gefolgt von einem Versions-Commit. Die
Commit-Nachricht sagt, was sich geändert hat und warum, in einer Form, aus der sich ein Changelog bauen lässt
(„Updated: ...“):

```text
Updated: The importer and client call imagedestroy() and curl_close() only on PHP versions before 8.0,
where they still had an effect; PHP 8.5 deprecates both
```

Die Nachricht des Release-Commits sagt, was das Release bedeutet:

```text
Updated: Version 1.0.6: installs and runs on PHP 8.0, the oldest PHP Exponential 6 supports
```

Die Konventionen für Commit-Nachrichten stehen in
[Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](../teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md).

### Dieselbe Korrektur, dieselben Worte

Wenn dieselbe Änderung in viele Repositorys geht, schreiben Sie sie überall gleich. Jede Korrektur von `curl_close()`
verwendet dieselbe Zeile und denselben Kommentar:

```php
if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
```

Wer nach einer sucht, findet alle, und ein Reviewer erkennt die Änderung auf einen Blick.

### Überall auf dieselbe Weise geprüft

Der Commit, der expchangeclass auf PHP 8.0 erweitert hat, hält die Prüfungen fest, und es sind die, die Sie für jede
Erweiterung einer Sammlung ausführen sollten:

1. PHPCompatibility für das älteste PHP.
2. `php -l` mit diesem PHP (8.0.30 im Commit) und mit dem neuesten.
3. Laden jeder Klasse mit diesem PHP.
4. Ausliefern der Seiten der Erweiterung mit diesem PHP auf einer vollständigen Installation.

Die eigene Prüfung des Kernels für PHP 8.0 hat dieselben vier Prüfungen auf „den Kernel und 63 Erweiterungen“
angewendet ([PHP-8.0-Unterstützung](../../../bc/6.0/php-8.0-unterstuetzung.md)).

### Releases im Gleichschritt mit dem Kernel

Die `composer.json` des Kernels verlangt bestimmte Versionen der Erweiterungen. Wenn das neue Release einer
Erweiterung gebraucht wird (weil es etwas behebt, das die eigenen Tests oder die CI des Kernels prüfen), hebt der
Kernel seine Anforderung auf dieses Release an. Die Seite zur PHP-8.0-Unterstützung listet solche Releases auf:
„syndication 1.3.5, ezstarrating 6.0.9, swark 1.0.5, birthday 1.3.3 und enhancedezbinaryfile 4.4.5 wurden mit
Korrekturen für Code veröffentlicht, den kein PHP 8 laden konnte.“

## Das für Ihre eigene Sammlung tun

Wenn Sie mehrere Erweiterungen pflegen oder eine Website mit vielen:

1. **Listen Sie sie auf**, mit ihrer PHP-Bedingung und ihrem letzten Release (`composer show` und jede
   `composer.json`).
2. **Führen Sie die Suchen der Bestandsaufnahme** aus [Bestandsaufnahme einer alten Erweiterung](bestandsaufnahme-einer-alten-erweiterung.md)
   über alle auf einmal aus; sortieren Sie die Treffer nach Art, nicht nach Erweiterung.
3. **Beheben Sie eine Art nach der anderen in allen Erweiterungen**, mit demselben Wortlaut, und veröffentlichen Sie
   dann jede Erweiterung.
4. **Fügen Sie jedem Repository einen CI-Workflow hinzu**, mit dem Lint auf dem ältesten und neuesten PHP
   ([Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)), damit
   sich die nächste PHP-Version als rote Prüfung zeigt, nicht als defekte Website.

## Wie es weitergeht

Sie haben das Ende von Teil 4 erreicht. Wenn die Erweiterung, die Sie aktualisiert haben, jemand anderem gehört,
handelt der nächste Teil davon, das fair zu tun: Forks, Nennung der Urheber, Lizenzen und Pull Requests zurück an das
Original.

## Verwandte Seiten

- Zurück: [Fallstudie: Zwei-Faktor-Authentifizierung ohne Inline-Skripte](fallstudie-zwei-faktor-authentifizierung.md)
- Weiter: [Teil 5: Erweitern, ethisch forken und zurückgeben](../teil-5-erweitern-forken-zurueckgeben/README.md)
- [Die Upgrade-Checkliste](die-upgrade-checkliste.md)
- [PHP-8-Unterstützung](../../../bc/6.0/php8.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
