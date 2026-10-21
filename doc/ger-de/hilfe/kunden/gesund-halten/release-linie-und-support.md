---
title: Release-Linie und Support
audience: customer, businesses, project-manager
category: customer-success
order: 4
summary: Welche Exponential-Versionen gepflegt werden, wie Sie die Version Ihrer Website herausfinden, wie Sicherheitskorrekturen Sie erreichen, welche PHP-Versionen wichtig sind und was Sie sich als Betreiber in den Kalender eintragen sollten.
keywords: Release-Linie, unterstützte Versionen, 6.0.x, SECURITY.md, PHP-Version, Versionsnummer, Ende der Unterstützung, Changelog, release line, supported versions, PHP version, version number, end of life, changelog
type: help
---
# Release-Linie und Support

Diese Seite beantwortet die drei Fragen des Betreibers zu Versionen: Welche Version betreiben wir, wird sie noch
gepflegt, und was müssen wir tun, um gepflegt zu bleiben? Die vollständige Richtlinie für Unternehmen steht unter
[Release-Linie und Support-Richtlinie](../../../../eng-us/help/businesses/reliability/release-line-and-support-policy.md); diese Seite ist
die Kurzfassung mit den Dingen, die zu prüfen sind.

## Was gepflegt wird

Aus `SECURITY.md` im Wurzelverzeichnis der Installation:

| Version | Unterstützt |
|---|---|
| 6.0.x, die aktuelle Linie (`main` und das nächste daraus erstellte Tag) | Ja |
| 6.0.x, veröffentlichte Tags (`v6.0.0` bis `v6.0.14`) | Korrigiert durch ein Upgrade auf die neueste 6.0.x |
| 5.x, 4.x, 3.x und älter | Nein |

In einfachen Worten: **Bleiben Sie auf dem neuesten Release 6.0.x.** Eine ältere 6.0.x ist nicht abgeschnitten, aber
eine Korrektur erreicht Sie erst, wenn Sie innerhalb der Linie aktualisieren. Eine Website auf 5.x oder älter braucht
ein Upgrade-Projekt; [Upgrade](../../../anleitungen/aktualisieren.md) hat einen Teil für jede Ausgangsversion.

## Die Version Ihrer Website herausfinden

- In der Administration: **Setup > Systeminformationen** nennt die Version
  ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
- **Info > Über** listet die Version von Exponential und von jeder Erweiterung
  ([Die Seiten Über und Copyright](../../../../eng-us/features/6.0/about-and-package-pages.md)).
- Fragen Sie Ihren Anbieter: Ein Anbieter, der ein Verzeichnis der Websites seiner Kunden führt, kann sofort antworten
  ([Kunden aktuell halten](../../partner/kunden-websites-aktuell-halten.md)).

Notieren Sie die Version mit dem Datum. Vergleichen Sie sie mit dem neuesten Release im Changelog
([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).

## Wie Sicherheitskorrekturen Sie erreichen

`SECURITY.md` legt den Ablauf fest: Ein Problem wird vertraulich gemeldet, ein angenommenes Problem „wird auf `main`
behoben und mit der nächsten Version 6.0.x veröffentlicht“, und „ein veröffentlichtes Tag wird nie geändert“. Der Plan
ist also einfach: **Aktualisieren Sie innerhalb der Linie, in einem regelmäßigen Rhythmus, und schnell, wenn ein
Release eine Sicherheitskorrektur enthält.** Vereinbaren Sie mit Ihrem Anbieter, wie schnell das ist (siehe
[Verträge und SLAs](../partner-und-hoster/vertraege-und-slas.md)).

## Auch PHP ist wichtig

Exponential läuft auf PHP, und PHP-Versionen haben ihre eigenen Unterstützungszeiträume. Aus der Seite zur
Release-Linie für Unternehmen: PHP 7.4 nur bis 6.0.7; PHP 8.0 ist die älteste unterstützte; 8.1 bis 8.5 werden
unterstützt, und Exponential Velocity läuft ab seinem Release 0.0.4.54 unter 8.0 bis 8.5. **Setup > Systeminformationen** warnt, wenn die
Sicherheitsunterstützung des laufenden PHP-Zweigs beendet ist ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
Fragen Sie Ihren Hoster, welche PHP-Versionen er anbietet und wann er eine einstellt.

## Erweiterungen haben eigene Versionen

Jede Erweiterung trägt ihre eigene Version, angezeigt unter **Setup > Extensions** und **Info > Über**
([Liste der Erweiterungen](../../../../eng-us/features/6.0/extension-list-and-downloads.md)). Eine Erweiterung, die nicht mehr
gepflegt wird, kann ein Upgrade aufhalten. Fragen Sie Ihren Anbieter einmal im Jahr, welche Erweiterungen die Website
verwendet und ob jede noch gepflegt wird.

## Was das Projekt nicht veröffentlicht

Keinen Release-Kalender, kein Ende-der-Unterstützung-Datum für 6.0 und kein Service Level Agreement
([Release-Linie und Support-Richtlinie](../../../../eng-us/help/businesses/reliability/release-line-and-support-policy.md)). Die einzige
Zusage zur Reaktionszeit in den eigenen Dateien des Projekts ist eine erste Antwort auf eine vertrauliche
Sicherheitsmeldung „innerhalb eines Werktags“ (`SECURITY.md`). Jedes andere Versprechen, etwa wie schnell Ihre Website
aktualisiert wird, kommt aus Ihrem Vertrag mit Ihrem Anbieter oder mit 7x.

## In Ihrem Kalender

- [ ] Jedes Quartal: Die Version der Website mit dem neuesten Release 6.0.x vergleichen.
- [ ] Wenn ein Release Sicherheitskorrekturen enthält: Ihren Anbieter nach dem Datum fragen, an dem es eingespielt wird.
- [ ] Jedes Jahr: Fragen, welche PHP-Version die Website verwendet und bis wann sie unterstützt wird.
- [ ] Jedes Jahr: Die Liste der Erweiterungen und ihrer Betreuer durchsehen.

## Verwandte Seiten

- [Wie Updates funktionieren](wie-updates-funktionieren.md)
- [Upgrades mit Ihrem Anbieter](upgrades-mit-ihrem-anbieter.md)
- [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md)
- [Wer ist 7x?](../../wer-ist-7x.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
