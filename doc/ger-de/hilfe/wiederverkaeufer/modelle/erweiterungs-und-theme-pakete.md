---
title: Erweiterungs- und Theme-Pakete
audience: reseller, partner, developer, designer
category: reselling
order: 110
summary: Wie Sie Erweiterungen und Themes für Exponential als Pakete weiterverkaufen - Ihre eigene Arbeit, Projekterweiterungen und solche von Dritten -, mit der Lizenzprüfung für jeden Teil, Geschäftsmodellen, die die GPL respektieren, dem, was ein Paket enthalten muss, der Versionierung gegenüber Exponential-Releases und Supportbedingungen.
keywords: Erweiterungspaket, Theme-Paket, Erweiterungen, Themes, Designs, Seitenpaket, weiterverkaufen, Lizenz, GPL, extension.xml, ezinfo.php, Info Über, Versionierung, Support, Composer, extension bundle, theme bundle, extensions, designs, site package, resell, license, Info About, versioning, support
type: help
---
# Erweiterungs- und Theme-Pakete

Diese Seite ist für Wiederverkäufer, die Erweiterungen, Themes und Designs zu Angeboten paketieren: „das
Intranet-Paket“, „das Newsroom-Paket“, „der Shop-Starter“. Wie Sie die Pakete selbst bauen, steht unter
[Paketersteller: hier beginnen](../../buendler/buendler-hier-beginnen.md); diese Seite handelt davon, sie zu verkaufen.

## Inhalt

1. [Was in ein Paket gehört](#was-in-ein-paket-gehört)
2. [Prüfen Sie die Lizenz jedes Teils](#prüfen-sie-die-lizenz-jedes-teils)
3. [Geschäftsmodelle, die die GPL respektieren](#geschäftsmodelle-die-die-gpl-respektieren)
4. [Versionierung und Kompatibilität](#versionierung-und-kompatibilität)
5. [Supportbedingungen](#supportbedingungen)
6. [Paketblatt](#paketblatt)

## Was in ein Paket gehört

| Teil | Typische Quelle | Hinweise |
|---|---|---|
| Ihre eigenen Erweiterungen | Ihre Repositorys | Sie bestimmen die Lizenz, im Rahmen dessen, was die Kombination mit GPL-Code erlaubt |
| Projekterweiterungen | Composer-Pakete `se7enxweb/*` | Die Erweiterungen des Projekts stehen unter der GPL ([Lizenzierung für Partner](../../partner/lizenzierung-fuer-partner.md)) |
| Erweiterungen Dritter | Andere Autoren | Ihre eigenen Lizenzen |
| Ein Theme oder Design | Ihre Designarbeit oder ein Theme des Projekts | Prüfen Sie die Lizenzen von Bildern und Schriften ([Ein Theme veröffentlichen](../../partner/ein-theme-veroeffentlichen.md)) |
| Ein Seitenpaket | Ihr eigenes, gebaut wie die mitgelieferten | Installiert vom Setup-Assistenten, vom Kickstarter oder mit `exp:install --package=<name>` ([Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)) |
| Dienstleistungen | Installation, Konfiguration, Schulung, Support | Ihre Arbeit, gesondert bepreist |

Das Ökosystem ist groß: Die `composer.json` des Kernels verlangt viele Pakete `se7enxweb/*`, und jede Erweiterung ist
ein Git-Repository und ein Composer-Paket ([Partner für Erweiterungen und Themes](../../partner/partner-fuer-erweiterungen-und-themes.md)).

## Prüfen Sie die Lizenz jedes Teils

- Jede Erweiterung gibt ihre Lizenz in ihrer eigenen `ezinfo.php` und `extension.xml` an; die Administration zeigt
  sie unter **Info > Über** ([Die Seiten „Über“ und „Copyright“](../../../funktionen/6.0/info-und-paketseiten.md)).
- Exponential Velocity steht unter MIT; Komponenten Dritter haben ihre eigenen Lizenzen, die auf der Seite „Über“
  aufgeführt sind ([Lizenzierung für Hoster](../../hosting/geschaeft/lizenzierung-fuer-hoster.md#erweiterungen-und-andere-komponenten)).
- Ob Ihre eigene Erweiterung unter der GPL stehen muss, hängt davon ab, wie sie mit Exponential kombiniert wird:
  Fragen Sie einen Rechtsberater.

```text
| Part | Version | License (from extension.xml / About) | Source repository | May we charge for distribution? |
|------|---------|--------------------------------------|-------------------|---------------------------------|
```

## Geschäftsmodelle, die die GPL respektieren

| Modell | Wie es verdient | Hinweise |
|---|---|---|
| Kostenloses Paket, bezahlte Dienstleistungen | Installation, Anpassung, Support | Das häufigste Modell in diesem Ökosystem |
| Bezahlte Weitergabe | Sie verlangen Geld für die Lieferung des Pakets | Empfänger von GPL-Teilen dürfen sie weitergeben |
| Bezahltes Support-Abonnement | Garantierte Antworten und Updates für das Paket | Veröffentlichen Sie die Supportbedingungen klar |
| Gesponserte Entwicklung | Ein Kunde bezahlt eine Funktion, die für alle veröffentlicht wird | [Funktionswünsche und gesponserte Funktionen](../../partner/funktionswuensche-und-gesponserte-funktionen.md) |
| OEM-Wiederverkauf über das Programm | „resell your own products based on Exponential“ (Ihre eigenen Produkte auf Basis von Exponential weiterverkaufen) | [OEM-Produkt-Wiederverkäufer](oem-produkt-wiederverkaeufer.md) |

Quelle: [Partner für Erweiterungen und Themes](../../partner/partner-fuer-erweiterungen-und-themes.md#geschäftsmodelle-die-die-gpl-respektieren).

## Versionierung und Kompatibilität

- Geben Sie jedem Paket eine Version und nennen Sie, mit welchem Exponential-Release und welchen PHP-Versionen es
  getestet wurde.
- Jedes Release einer Erweiterung sollte seine Version in `ezinfo.php` und `extension.xml` tragen, damit Kunden sie
  unter **Info > Über** und **Setup > Extensions** ablesen können.
- Testen Sie jedes neue Release 6.0.x mit dem Paket, bevor Sie Kunden das Update empfehlen
  ([Releases testen](../../partner/releases-testen.md)).
- Eine Erweiterung, die nicht mehr gepflegt wird, kann ein Upgrade aufhalten; planen Sie, wer jeden Teil pflegt
  ([Release-Linie und Unterstützung](../../kunden/gesund-halten/release-linie-und-support.md#erweiterungen-haben-eigene-versionen)).

## Supportbedingungen

Schreiben Sie für jedes Paket auf:

- was unterstützt wird (Ihre Erweiterungen, die Konfiguration, die Sie geliefert haben) und was nicht (Code Dritter,
  den Sie nicht geändert haben, die eigenen Änderungen des Kunden);
- wie lange eine Paketversion unterstützt wird, nachdem die nächste erschienen ist;
- wie Fehlerbehebungen den Kunden erreichen (eine neue Paketversion, kein Hotfix auf der Live-Website);
- wo der Kunde einen Fehler meldet.

## Paketblatt

```text
Bundle: ______________________   Version: ______   Release date: ________
For: <kind of site>
Built on: Exponential 6.0.__   PHP: 8.__ to 8.__   Database(s) tested: ____________
Contents: <extensions, theme, site package, versions>
Licenses: see license table (attached)
Price: distribution $____  installation $____  support $____ / month
Support: <what, how long, channel>
Upgrade policy: <how bundle versions follow Exponential releases>
```

## Verwandte Seiten

- [Partner für Erweiterungen und Themes](../../partner/partner-fuer-erweiterungen-und-themes.md)
- [Eine Erweiterung als Partner veröffentlichen](../../partner/eine-erweiterung-veroeffentlichen.md)
- [Erweiterungen zurückgeben](../../partner/erweiterungen-zurueckgeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
