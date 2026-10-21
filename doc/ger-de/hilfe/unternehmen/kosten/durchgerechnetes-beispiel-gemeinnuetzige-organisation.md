---
title: "Durchgerechnetes Beispiel: eine gemeinnützige Organisation"
audience: businesses, project-manager, website-creator
category: business-solutions
order: 60
summary: Eine Kosten-Nutzen-Rechnung Schritt für Schritt für die kleine Website einer gemeinnützigen Organisation, die teilweise von Ehrenamtlichen betrieben wird, mit Online-Spenden und einem Newsletter, nur mit gekennzeichneten Beispielannahmen; zeigt, wie Sie die Zeit der Ehrenamtlichen zählen und die laufenden Kosten niedrig halten.
keywords: gemeinnützig, Wohltätigkeit, Verein, durchgerechnetes Beispiel, Spenden, Ehrenamtliche, kleines Budget, TCO, ROI, SQLite, Newsletter, Hosting, nonprofit, charity, association, worked example, donations, volunteers, low budget, newsletter, hosting
type: help
---
# Durchgerechnetes Beispiel: eine gemeinnützige Organisation

Diese Seite ist für Vorstände, Schatzmeister und Koordinatoren gemeinnütziger Organisationen, die entscheiden müssen,
ob eine Website das Geld und die Zeit der Ehrenamtlichen wert ist. Sie rechnet eine kleine, erfundene Organisation
Schritt für Schritt durch: was die Website kostet, was sie einbringt und wie Sie die Stunden zählen, die Ehrenamtliche
geben.

> **Hinweis**
> Die Organisation und jede Zahl unten sind **Beispielannahmen**: runde Zahlen in einer neutralen Währungseinheit. Es
> sind weder Spendenstatistiken noch Preise noch Ergebnisse einer echten Organisation. Ersetzen Sie jede durch Ihre
> eigenen Zahlen.

## Inhalt

1. [Was die Kosten niedrig hält](#was-die-kosten-niedrig-hält)
2. [Die Eingabewerte](#die-eingabewerte)
3. [Schritt 1: die Barkosten](#schritt-1-die-barkosten)
4. [Schritt 2: die Spenden, die die Website hinzufügt](#schritt-2-die-spenden-die-die-website-hinzufügt)
5. [Schritt 3: ROI](#schritt-3-roi)
6. [Schritt 4: die Zeit der Ehrenamtlichen zählen](#schritt-4-die-zeit-der-ehrenamtlichen-zählen)
7. [Was Sie für Ihre Organisation ändern](#was-sie-für-ihre-organisation-ändern)

## Was die Kosten niedrig hält

| Fakt | Wirkung auf das Budget | Quelle |
|---|---|---|
| Keine Lizenzgebühr | Kein Abonnement, das aus einem knappen Budget verlängert werden muss | `README.md`, „License“ |
| SQLite 3 ist der Standard und braucht keinen Datenbankserver | Ein kleiner Hosting-Tarif oder ein kleiner Server reicht für eine Website auf einer Maschine | `README.md`, „Requirements“; [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Läuft auf fast jedem Betriebssystem mit PHP 8.0 oder neuer | Große Auswahl an Hostern | [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) |
| Newsletter über `cjw_newsletter`, das `composer.json` verlangt | Kein separates Newsletter-Produkt nötig | [Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md) |
| Gemeinsames Bearbeiten mit Rollen und Freigabe | Ehrenamtliche können bearbeiten, ohne vollen Zugriff zu haben | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Freie Dokumentation unter der GNU FDL | Schulungsmaterial, das Sie kopieren und anpassen dürfen | [Freie Dokumentation](../../freie-dokumentation.md) |

> **Hinweis**
> Das Repository hat kein eigenes Spendenmodul. Eine Spendenseite wird mit dem eingebauten Shop und einem
> Zahlungsanbieter gebaut oder mit einem Link auf die eigene Spendenseite Ihres Zahlungsanbieters; siehe
> [Spenden und Spendenaufrufe](../gemeinnuetzige/spenden-und-spendenaufrufe.md). Es gelten die Gebühren des Anbieters.

## Die Eingabewerte

| Eingabewert | Wert |
|---|---|
| Beispielannahme: Einrichtung durch einen Freiberufler oder Partner | 3.000 Währungseinheiten, einmalig |
| Beispielannahme: Hosting | 20 Währungseinheiten im Monat |
| Beispielannahme: bezahlte Hilfe bei Aktualisierungen | 8 Stunden im Jahr zu 50 Währungseinheiten pro Stunde |
| Beispielannahme: Online-Spenden | 30 im Monat |
| Beispielannahme: durchschnittliche Spende | 25 Währungseinheiten |
| Beispielannahme: Gebühren des Zahlungsanbieters | 3 % der Spenden |
| Beispielannahme: Anteil der Online-Spenden, die sonst nicht eingegangen wären | 50 % |
| Beispielannahme: Bearbeitungszeit der Ehrenamtlichen | 4 Stunden pro Woche, 48 Wochen im Jahr |
| Lizenzgebühr | 0 (Fakt) |

## Schritt 1: die Barkosten

1. Einrichtung: **3.000**.
2. Hosting pro Jahr: 20 x 12 = **240**.
3. Bezahlte Hilfe pro Jahr: 8 x 50 = **400**.
4. Laufende Kosten pro Jahr: 240 + 400 = **640**.
5. Erstes Jahr: 3.000 + 640 = **3.640**.
6. Fünf Jahre: 3.000 + (640 x 5) = 3.000 + 3.200 = **6.200**.

## Schritt 2: die Spenden, die die Website hinzufügt

1. Spenden pro Jahr: 30 x 25 x 12 = **9.000**.
2. Zahlungsgebühren: 9.000 x 3 % = **270**.
3. Nach Gebühren: 9.000 - 270 = **8.730**.
4. Nur der neue Anteil zählt: 8.730 x 50 % = **4.365** im Jahr.

## Schritt 3: ROI

Erstes Jahr:

1. Nettonutzen: 4.365 - 3.640 = **725**.
2. ROI: 725 / 3.640 x 100 % = etwa **20 %**.

Fünf Jahre:

1. Nutzen: 4.365 x 5 = **21.825**.
2. Kosten: **6.200**.
3. Nettonutzen: 21.825 - 6.200 = **15.625**.
4. ROI: 15.625 / 6.200 x 100 % = etwa **252 %**.

## Schritt 4: die Zeit der Ehrenamtlichen zählen

Ehrenamtliche Stunden kosten kein Bargeld, aber sie sind nicht umsonst: Sie könnten in andere Arbeit gehen. Zeigen Sie
sie neben den Barzahlen, nicht darin.

1. Stunden pro Jahr: 4 x 48 = **192**.
2. Beispielannahme: ein Schattenwert von 20 Währungseinheiten pro Stunde.
3. Schattenwert: 192 x 20 = **3.840** im Jahr.

Stellen Sie es so dar: „Die Website kostet 640 in bar im Jahr und etwa 192 ehrenamtliche Stunden.“ Der Vorstand kann
dann entscheiden, ob diese Stunden gut angelegt sind. Gemeinsames Bearbeiten mit klaren Rollen und eine schriftliche
Übergabe halten die Stunden niedrig
([Übergabe bei Wechsel der Ehrenamtlichen](../vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md)).

## Was Sie für Ihre Organisation ändern

- [ ] Ihre Spendenzahlen aus dem Jahresabschluss des letzten Jahres und einen realistischen Anteil, den die Website
      hinzufügt.
- [ ] Die Gebührenstruktur Ihres Zahlungsanbieters aus seinem Vertrag.
- [ ] Zuschüsse oder Sachleistungen beim Hosting, die Sie erhalten; fragen Sie Hoster und Partner, ob sie so etwas
      anbieten.
- [ ] Arbeit für Barrierefreiheit, die eine gemeinnützige Organisation oft leisten muss
      ([Barrierefreiheit für gemeinnützige Organisationen](../gemeinnuetzige/barrierefreiheit-fuer-gemeinnuetzige.md)).
- [ ] Datenschutzgrundlagen für die Daten von Spendern und Mitgliedern
      ([Sicherheit und Datenschutz](../sicherheit/README.md)).
- [ ] Einen Plan für die Jahre mit kleinem Budget: [Betrieb mit kleinem Budget](../gemeinnuetzige/betrieb-mit-kleinem-budget.md).

## Verwandte Seiten

- [Gemeinnützige Organisationen](../gemeinnuetzige/README.md)
- [Website-Planung für gemeinnützige Organisationen](../gemeinnuetzige/website-planung-fuer-gemeinnuetzige.md)
- [Wofür Sie nicht bezahlen](wofuer-sie-nicht-bezahlen.md)
- [Vorlage ROI-Arbeitsblatt](../vorlagen/vorlage-roi-arbeitsblatt.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
