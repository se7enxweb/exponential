---
title: "Ein README, das erklärt und verkauft"
audience: designer, sales-marketing
category: theme-distribution
order: 50
summary: Das README eines Themes zu der Seite machen, die überzeugt - der erste Bildschirm, ein Screenshot, Qualitätsnachweise (Barrierefreiheit, CSP, Dunkelmodus), ehrliche Voraussetzungen, eine Installation in zwei Minuten und klarer Support - ohne Behauptungen, die Sie nicht belegen können.
keywords: README, verkaufen, erklären, erster Bildschirm, Screenshot, Funktionen, Voraussetzungen, Installation, Support, Badges, Nachweis, sell, explain, first screen, features, requirements, install, badges, proof
type: help
---
# Ein README, das erklärt und verkauft

Diese Seite ist für Designer, die schon ein README haben ([Das README eines Themes schreiben](../erweiterungen/readme-fuer-ein-theme.md))
und möchten, dass es ebenso überzeugt wie anleitet. Auf GitHub und Packagist ist das README die Produktseite.

## Der erste Bildschirm

Ein Besucher entscheidet innerhalb des ersten Bildschirms. Setzen Sie dorthin:

1. Den Namen und einen Satz dazu, was es ist.
2. Einen Screenshot der Startseite (heller Modus), darunter einen Link zu einem Screenshot im Dunkelmodus und einem
   auf dem Telefon.
3. Drei bis fünf Fakten, die ein Käufer prüft, jeder wahr und überprüfbar:
   - „Funktioniert mit Exponential 6.0 (getestet mit 6.0.15) auf PHP 8.1 bis 8.5.“
   - „Besteht axe ohne ernsthafte Probleme bei 1440 px und 375 px.“ (nur, wenn Sie es ausgeführt haben)
   - „Läuft unter einer Content Security Policy ohne 'unsafe-inline'.“ (nur, wenn es das tut)
   - „GPL-2.0-or-later. Frei zu nutzen, zu ändern und weiterzugeben.“

## Nachweise statt Adjektive

| Statt | Schreiben Sie |
|---|---|
| „Rasend schnell“ | Die gemessenen Zahlen und wie Sie gemessen haben, oder nichts |
| „Vollständig barrierefrei“ | „Erfüllt die Kontrastanforderungen von WCAG 2.2 AA; mit axe geprüft auf den in doc/a11y.md aufgeführten Seiten“ |
| „Sicher“ | „Maskiert jede Ausgabe; keine Inline-Skripte; melden Sie Probleme wie in SECURITY.md beschrieben“ |

Die Themes von 7x schreiben ihre Release Notes so: `cjw_themes_jumper` 0.1.5 sagt genau, welche Prüfungen es besteht
(Kontrast, Zielgröße und Linknamen, WCAG 2.2 AA).

## Der Rest, der Reihe nach

Voraussetzungen, Installation, Einschalten, Konfigurieren, was enthalten ist, Lizenz und Hinweise, Support und
Sicherheit, Changelog, Danksagungen. Beschränken Sie die Installation auf Befehle, die ein Leser kopieren kann.

## Schritt für Schritt

1. Schreiben Sie den ersten Bildschirm mit der Liste oben neu.
2. Ersetzen Sie jedes Adjektiv durch einen Fakt oder entfernen Sie es.
3. Bitten Sie jemanden, der das Theme nie gesehen hat, es allein anhand des README zu installieren; korrigieren Sie
   jede Stelle, an der die Person hängen bleibt.
4. Prüfen Sie das dargestellte README auf GitHub und auf Packagist (Packagist zeigt es auf der Paketseite).

## Verwandte Seiten

- [Screenshots für ein Theme](screenshots-fuer-ein-theme.md)
- [Die Produktseite eines Themes schreiben](../../../../eng-us/help/designers/sales/product-page.md)
- [Positionierung und Nutzenversprechen](../../geschaeft/positionierung-und-nutzenversprechen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
