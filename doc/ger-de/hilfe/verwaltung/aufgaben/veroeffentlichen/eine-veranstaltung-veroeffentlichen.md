---
title: Eine Veranstaltung veröffentlichen
audience: government, editor
category: public-sector
order: 140
summary: Für Redakteure - eine öffentliche Veranstaltung mit der mitgelieferten Klasse Event in den Veranstaltungskalender eintragen, mit Beginn und Ende, Ort, Angaben zur Barrierefreiheit und zur Buchung, und sie im Kalender prüfen.
keywords: Veranstaltung, Veranstaltung veröffentlichen, Veranstaltungskalender, From Time, To Time, Kurztitel, Kategorie, Ort, Barrierefreiheit, Buchung, Kalenderansicht, event, publish event, event calendar, short title, category, place, accessibility, booking, calendar view
type: help
---
# Eine Veranstaltung veröffentlichen

Diese Seite ist für Redakteure, die Veranstaltungen in den Kalender der Site eintragen. Sie verwendet die
mitgelieferte Klasse **Event** (Short title, Full title, Text, Category, From Time, To Time) innerhalb eines
**Event calendar** ([Was für Veranstaltungen mitgeliefert wird](../../aufbauen/veranstaltungen/was-fuer-veranstaltungen-mitgeliefert-wird.md)); Ihre Site hat
vielleicht zusätzliche Felder wie Ort, Zielgruppe oder Buchung
([Eine Klasse für Veranstaltungen modellieren](../../aufbauen/veranstaltungen/eine-klasse-fuer-veranstaltungen-modellieren.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie kennen den Kalender, in den die Veranstaltung gehört (einen je Site oder je Abteilung).
- Sie haben: Datum und Uhrzeit (Beginn und Ende), Ort mit Adresse, worum es geht, für wen, Kosten, Buchung und die
  Barrierefreiheit des Veranstaltungsorts
  ([Barrierefreie Informationen zu Veranstaltungen](../../aufbauen/veranstaltungen/barrierefreie-informationen-zu-veranstaltungen.md)).
- Ihre Rolle darf im Kalender Veranstaltungen anlegen (die Standardrolle Editor darf `event` unter `event_calendar`
  anlegen).

## Schritte

1. Öffnen Sie den Event calendar, **Neu erstellen**, wählen Sie **Event**.
2. **Short title** (Pflichtfeld): der Name, der im Kalender erscheint, kurz. **Full title**: der längere Name, falls
   nötig.
3. **From Time** (Pflichtfeld) und **To Time**: Datum und Uhrzeit.
4. **Text**: ein oder zwei Sätze dazu, worum es geht, dann der Ort, die Anfahrt, die Barrierefreiheit, die Kosten und
   wie man bucht.
5. **Category**: Stichwörter wie „Beteiligung“ oder „Familie“; oder das Feld für Schlagwörter, wenn Ihre Klasse eines
   hat.
6. Zusätzliche Felder Ihrer Site: Ort, Zielgruppe, Buchungsstatus, Buchungslink.
7. **Vorschau**, dann **Zur Veröffentlichung schicken**.

## Das Ergebnis prüfen

- [ ] Der Kalender zeigt die Veranstaltung am richtigen Tag (öffnen Sie die Monatsansicht und die Programmliste, wenn
  Ihre Site beide hat).
- [ ] Die Seite der Veranstaltung zeigt abgemeldet Datum, Uhrzeit, Ort und die Angaben zur Barrierefreiheit.
- [ ] Listen „Kommende Veranstaltungen“ auf der Startseite zeigen sie, nachdem sich ihr Cache erneuert hat (bis zur
  Ablaufzeit, die Ihr Web-Team eingestellt hat).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Datum und Uhrzeit nur auf einem Plakatbild | die Fakten gehören in den Text ([Barrierefreie Informationen zu Veranstaltungen](../../aufbauen/veranstaltungen/barrierefreie-informationen-zu-veranstaltungen.md)) |
| Kein To Time bei einer langen Veranstaltung | Listen kommender Veranstaltungen lassen sie womöglich fallen, sobald sie beginnt; setzen Sie das Ende |
| Eine wiederkehrende Veranstaltung | jeder Termin ist ein eigenes Event; kopieren Sie das erste ([Eine Seite als Ausgangspunkt kopieren](eine-seite-als-ausgangspunkt-kopieren.md)) |
| Anmeldungen per E-Mail an eine persönliche Adresse | verwenden Sie ein Team-Postfach oder ein Anmeldeformular ([Anmeldungen zu Veranstaltungen](../../aufbauen/veranstaltungen/anmeldungen-zu-veranstaltungen.md)) |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Veranstaltung absagen oder verschieben](eine-veranstaltung-absagen-oder-verschieben.md)
- [Kalenderansichten](../../aufbauen/veranstaltungen/kalenderansichten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
