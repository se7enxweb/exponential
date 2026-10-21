---
title: Eine Newsletter-Liste für Warnmeldungen hinzufügen
audience: government, admin, editor
category: public-sector
order: 340
summary: Aufgabe für Administratoren - cjw_newsletter eine Liste für Warnmeldungen (zum Beispiel zu Beteiligungsverfahren) hinzufügen, ihr Anmeldeformular von den Seiten des Themas aus verlinken und nach einer Probe eine erste Warnmeldung senden.
keywords: Newsletter-Liste, Warnmeldungen, Anmeldeformular, Newsletter-System, Double-Opt-in, Ausgabe, newsletter list, alerts, cjw_newsletter, subscribe form, newsletter system, double opt-in, edition
type: help
---
# Eine Newsletter-Liste für Warnmeldungen hinzufügen

Diese Aufgabe richtet sich an Administratoren und Redakteure. Hintergrund:
[Listen für Warnmeldungen zu öffentlichen Bekanntmachungen](../../../../../eng-us/help/government/building/newsletters/alert-lists-for-public-notices.md).

## Bevor Sie beginnen

- `cjw_newsletter` ist aktiv, seine Klassen sind installiert, `[NewsletterSettings] RootFolderNodeId` ist gesetzt, die
  Cron-Teile laufen.
- Unter dem Newsletter-Ordner gibt es ein Newsletter-System (zum Beispiel „Öffentliche Warnmeldungen“).

## Schritte

1. Öffnen Sie in der **Inhaltsstruktur** das Newsletter-System; **Neu erstellen**, die Klasse der Newsletter-Liste;
   nennen Sie sie „Beteiligungsverfahren“; veröffentlichen Sie.
2. Verlinken Sie auf den Seiten der Beteiligungsverfahren das Anmeldeformular (`newsletter/subscribe`), mit einem Satz
   dazu, was Abonnenten erhalten und wie oft.
3. Proben Sie mit dem Datei-Transport (siehe „Einen Newsletter mit dem Datei-Transport proben“).
4. Schreiben Sie die erste Warnmeldung: unter der Liste **Neu erstellen**, eine Ausgabe; Vorschau; einen Test senden;
   **Send**.

## Prüfen

Das Dashboard **Newsletter** (`/newsletter/index`) zeigt die Liste unter **Lists**, ihre Abonnenten unter
**Subscribers** und den Versand unter **Last sends**.

## Probleme

| Problem | Lösung |
|---|---|
| Abonnenten erhalten nichts | sie sind noch *pending*: die Bestätigung wurde nicht angeklickt |
| Nichts wird gesendet | der Cron-Teil `cjw_newsletter` läuft nicht für den Siteaccess, oder der Transport ist `file` |

## Verwandte Seiten

- [Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
