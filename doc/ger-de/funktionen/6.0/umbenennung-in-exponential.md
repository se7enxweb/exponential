# Das Produkt heißt Exponential

Diese Seite ist für alle, die von einem Release vor 6.0.10 aktualisieren, und für Website-Entwickler, die möchten, dass
ihre eigenen Templates den aktuellen Namen verwenden. Ab Version 6.0.10 (August 2025) nennt sich die Software
**Exponential**. Der Installer, die Administrationsoberfläche, die Kommandozeilenskripte, die Übersetzungen, die
Dokumentation und der Paketserver verwenden alle den neuen Namen. Geändert hat sich nur Text, den ein Mensch liest; jeder
Bezeichner ist geblieben, sodass keine Website und keine Extension kaputtgeht.

## Den alten Namen auf Ihrer eigenen Website finden

Ihre Überschreibungen, Übersetzungen und eigenen Templates gehören Ihnen und wurden daher nicht umbenannt. Listen Sie sie
auf:

```bash
grep -rIlE 'eZ[[:space:]]+Publish' settings/override settings/siteaccess design extension/*/design extension/*/settings 2>/dev/null
```

Die Ausgabe ist eine Liste von Dateien. Ersetzen Sie den Produktnamen im Text dieser Dateien; lassen Sie Klassennamen
und Pfade unverändert.

## Checkliste zur Aktualisierung

1. Holen Sie das Release 6.0.10 oder neuer und leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

2. Wenn Sie die URL des Paketservers in Ihrer eigenen Überschreibung von `package.ini` festgelegt haben, entfernen Sie
   die Festlegung oder richten Sie sie auf den Paketserver von Exponential (siehe die Tabelle unten). Die alte Adresse
   antwortet für alte Installationen weiterhin.
3. Laden Sie PHP-FPM neu, damit die neue `lib/version.php` gesehen wird. Die Konstante `EDITION` lautet `Exponential`;
   die Klasse heißt ab 6.0.15 `ExponentialSDK`.
4. Wenn Sie eine eigene Dateiliste für die Aktualisierungsprüfung pflegen, erzeugen Sie sie neu (siehe
   [Dateikonsistenzprüfung](../../../eng-us/features/6.0/file-consistency-check.md)).

## Was sich geändert hat

| Wo | Vorher | Jetzt |
|---|---|---|
| `lib/version.php`, `EDITION` | der alte Produktname | `Exponential` (Konstante `EDITION`; die Klasse selbst wurde erst in der Linie 6.0.15 in `ExponentialSDK` umbenannt, der alte Klassenname funktioniert weiter) |
| Composer-Paket | der alte Anbieter- und Produktname | `se7enxweb/exponential` und `se7enxweb/exponential-legacy-installer` |
| Einrichtungsassistent | alter Name in Überschriften, Text und Links | überall „Exponential“ |
| Admin-Anmeldung und obere Leiste | altes Logo und Copyright | Logo von Exponential und der aktualisierte Copyright-Text |
| `bin/`, `design/`, `doc/`, `share/translations/` | alter Name in Hilfetexten, Templates und Übersetzungsdateien | in einem Durchgang über 332 Dateien umbenannt (13. August 2025, `837c7b835d`), gefolgt von Aufräum-Commits |
| `settings/package.ini` | Paketserver unter der alten Domain | `RemotePackagesIndexURL` und `RemotePackagesIndexURLBase` zeigen auf den Paketserver von Exponential (im Januar 2025 zuerst auf eine Marken-Domain umgezogen; die alte Adresse leitet laut der Commit-Nachricht von `5fb85b97f0` transparent weiter; hier nicht erneut getestet) |
| Logo des Standard-Designs | altes Logo-Bild | das Logo von Exponential (April 2026) |

Die Fußzeile des Standard-Themes gibt eine Zeile „Powered by“ aus. Das Theme `sevenx_themes_simple` (seit 1.0.4 von
`composer.json` verlangt) hat `page_footer.tpl` so umgebaut, dass sich diese Zeile leicht entfernen lässt: Kopieren Sie
das Template in Ihre eigene Design-Extension und löschen Sie den Block.

## Was sich mit Absicht nicht geändert hat

Die Umbenennung betraf Text, nicht Bezeichner. Diese bleiben genau, wie sie waren:

- PHP-Klassen mit dem Präfix `eZ` (`eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`) und ihre Dateinamen;
- die Pfade im Stil von `ezpublish-legacy` und die Namen der Datenbanktabellen (`ezcontentobject` und die übrigen);
- Template-Operatoren, Fetch-Funktionen, Namen von INI-Blöcken und -Schlüsseln;
- die Composer-Pakete der Extensions (`se7enxweb/ezwebin` und so weiter).

Ein Template, das Sie 2012 geschrieben haben, funktioniert also weiter.

## Verwandte Seiten

- [Eine saubere Installation, die zu Shared Hosting passt und Exponential sagt](standardwerte-einer-sauberen-installation.md)
- [Info- und Copyright-Seiten](info-und-paketseiten.md), [Paketlizenzen und Semantic Versioning](paketlizenzen-und-versionen.md)
- [Standard-Auslieferung der Extensions](standard-extension-auslieferung.md), [Dateikonsistenzprüfung](../../../eng-us/features/6.0/file-consistency-check.md)
- [Changelog 6.0.10](../../../changelogs/6.0/6.0.10.md)
- Chronik: [August 2025](../../../eng-us/history/2025/2025-08.md), [Juli 2025](../../../eng-us/history/2025/2025-07.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
