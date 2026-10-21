# ez-support-tools: Systeminformationen für Administratoren

Diese Seite ist für Administratoren und Support-Mitarbeiter der Symfony-basierten Plattform. `ez-support-tools` ist
das Plattform-Bundle, das **Informationen über das System** zeigt, auf dem die Plattform läuft (die Bildschirme
„System Info“). Es ist ein Symfony-Bundle, keine Legacy-Extension. Der Fork von se7enxweb behält es, damit es sich
neben den anderen Paketen `se7enxweb/ezplatform-*` installieren lässt.

## Was sich im Fork geändert hat

| Version oder Datum | Änderung |
|---|---|
| 2024 | Lizenzangaben und Copyright-Jahr des Upstream-Bundles; die fest eingetragenen Enddaten der Wartung für Version 3.3. |
| 2.3.13 (1. Juli 2025) | Der Abschnitt `require` nennt die Herstellerpakete von 7x. |
| 2.3.14 (28. September 2025) | Paketbeschreibungen und unterstützte PHP-Versionen aktualisiert; benötigt `se7enxweb/ezplatform-core`. |
| 11. April 2026 | Die PHP-Anforderung erlaubt PHP 8.5. Eine Absicherung gegen eine **Null-Version** aus `InstalledVersions::getVersion()`. |

Zur Absicherung gegen die Null-Version: Ein Paket, das aus einem Pfad- oder VCS-Repository oder ohne erfasste Version
installiert wurde, lieferte null. Die Übergabe von null an `getStability(string $version)` war beim Start ein fataler
`TypeError`, der jeden Konsolenbefehl lahmlegte. Der Code greift jetzt auf `getMinimumStability()` zurück.

Die Plattformseite des Projekts beschreibt die [Übersicht über das Ökosystem](../../../geschichte/oekosystem.md).

## Verwandte Seiten

- [Chronik des Repositorys](../../../geschichte/oekosystem/ez-support-tools.md) und [Versionshinweise](../../../../changelogs/extensions/ez-support-tools.md) (zusammen mit den Plattform-Repositorys behandelt)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ez-support-tools.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
