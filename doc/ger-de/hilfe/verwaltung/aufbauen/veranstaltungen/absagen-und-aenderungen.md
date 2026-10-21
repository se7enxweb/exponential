---
title: Absagen und Änderungen
audience: government, website-creator, editor
category: public-sector
order: 70
summary: Wie Sie geänderte und abgesagte Veranstaltungen auf einer Exponential-Site behandeln - Zeit oder Ort ändern, ein sichtbarer Status, die Adresse behalten, Menschen informieren, die folgen oder sich angemeldet haben, die Historie in den Versionen und das Verstecken abgesagter Veranstaltungen nach ihrem Datum.
keywords: Veranstaltung abgesagt, verschoben, Änderung einer Veranstaltung, Status, Buchungsfeld, Versionen, Benachrichtigungen, Anmeldungen, verstecken, Historie, Kalender, event cancelled, postponed, event change, status, booking field, versions, notifications, sign-ups, hide, event history, calendar
type: help
---
# Absagen und Änderungen

Diese Seite richtet sich an Site-Ersteller, die festlegen, wie Veranstaltungen geändert und abgesagt werden, und an die
Redakteure, die es tun. Menschen verlassen sich auf Veranstaltungsseiten; eine Änderung muss für alle sichtbar sein,
die die Seite öffnen, nicht nur für die, denen die Bearbeitung auffällt. Die genannten Funktionen sind in
[Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) und
[Benachrichtigungen: die Anleitung für Benutzer](../../../../funktionen/6.0/benachrichtigungen.md) dokumentiert.

## Inhalt

1. [Grundsätze](#grundsätze)
2. [Eine Änderung von Zeit oder Ort](#eine-änderung-von-zeit-oder-ort)
3. [Verschoben oder abgesagt](#verschoben-oder-abgesagt)
4. [Menschen informieren](#menschen-informieren)
5. [Danach](#danach)

## Grundsätze

| Grundsatz | Warum |
|---|---|
| Behalten Sie dieselbe Seite und Adresse | Menschen haben den Link geteilt; Suchmaschinen kennen ihn |
| Sagen Sie oben, was sich geändert hat | nicht im Text versteckt |
| Ein Statusfeld, nicht nur Worte im Text | Listen und der Kalender können ihn ebenfalls zeigen |
| Entfernen Sie eine abgesagte Veranstaltung nicht vor ihrem Datum | wer sie nachschlägt, muss „abgesagt“ finden |

Die in [Eine Klasse für Veranstaltungen modellieren](eine-klasse-fuer-veranstaltungen-modellieren.md) vorgeschlagene Auswahl Booking trägt
den Status (freier Eintritt, Buchung erforderlich, ausgebucht, abgesagt, verschoben); Templates zeigen ihn in der
Veranstaltung, im Kalender und in Listen.

## Eine Änderung von Zeit oder Ort

1. **Bearbeiten** Sie die Veranstaltung.
2. Ändern Sie **From Time**, **To Time** oder den Ort.
3. Oben im Text: „Geändert am <Datum>: jetzt um <Uhrzeit> in <Ort>.“
4. **Zur Veröffentlichung schicken**. Die vorherige Version bleibt in der Historie; **Vergleichen** zeigt, was sich
   geändert hat.
5. Prüfen Sie den Kalender: Eine in einen anderen Monat verschobene Veranstaltung wandert in der Kalenderansicht mit.

## Verschoben oder abgesagt

| Situation | Tun Sie |
|---|---|
| Verschoben, neues Datum bekannt | ändern Sie die Daten, Status „verschoben“ nur, wenn das Template das neue Datum klar zeigt; sonst aktualisieren Sie einfach die Daten und den Hinweis |
| Verschoben, Datum unbekannt | Status „verschoben“; behalten Sie das alte Datum, damit sie im Kalender dort bleibt, wo Menschen suchen |
| Abgesagt | Status „abgesagt“; ein kurzer Grund, wenn angemessen; behalten Sie die Seite |

Templates können in Listen „Abgesagt:“ vor den Titel setzen, damit es niemand in einer Kalenderzelle übersieht.

## Menschen informieren

| Wer | Wie |
|---|---|
| Menschen, die dem Kalender folgen | das Veröffentlichen der Änderung sendet beim nächsten Lauf Benachrichtigungsmails (verwenden Sie hier nicht **Ohne Benachrichtigung veröffentlichen**) |
| Menschen, die sich angemeldet haben | schreiben Sie ihnen anhand der Anmeldeliste (**Setup > Gesammelte Informationen**, **CSV exportieren**) mit Ihrem üblichen Mailsystem |
| Feedreader | der RSS-Export zeigt das aktualisierte Element, wenn der Feed das nächste Mal geschrieben wird |
| Listen auf der Startseite | aktualisiert mit dem Cache-Ablauf der Liste ([Kommende Veranstaltungen auflisten](kommende-veranstaltungen-auflisten.md#caching)) |

## Danach

- Verstecken Sie abgesagte Veranstaltungen nach ihrem Datum, oder behalten Sie sie als abgesagt markiert im Archiv
  ([Vergangene Veranstaltungen und das Archiv](vergangene-veranstaltungen-und-das-archiv.md)).
- Entfernen Sie die Anmeldedaten abgesagter Veranstaltungen, sobald die Menschen informiert wurden.

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Anmeldungen zu Veranstaltungen](anmeldungen-zu-veranstaltungen.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
