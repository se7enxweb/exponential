---
title: Newsletter-Routine
audience: customer, editor, businesses
category: customer-success
order: 3
summary: Die Routine für jede Newsletter-Ausgabe und der monatliche Blick auf die Listen einer Exponential-Website mit cjw_newsletter - schreiben, Vorschau, Testgruppe, Freigabe, geplanter Versand, Archiv - mit der Angabe, wer welchen Schritt erledigt, und einer Checkliste zum Kopieren.
keywords: Newsletter-Routine, Ausgabe, Testgruppe, Vorschau, Freigabe, Versand, wiederkehrender Versand, Bounces, Sperrliste, monatliche Auswertung, cjw_newsletter, newsletter routine, edition, test group, preview, approval, send, recurring send, bounces, suppression list, monthly review
type: help
---
# Newsletter-Routine

Diese Seite ist für die Autoren, freigebenden Personen und Versender eines Newsletters und für den Betreiber, der
möchte, dass er zuverlässig läuft. Sie macht aus [Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md)
eine Routine, die Sie kopieren können. Die Einrichtung und die Entscheidungen stehen unter [Newsletter](newsletter.md).

## Inhalt

1. [Jede Ausgabe](#jede-ausgabe)
2. [Der Tag des Versands](#der-tag-des-versands)
3. [Wenn etwas nicht hinausgehen soll](#wenn-etwas-nicht-hinausgehen-soll)
4. [Jeden Monat](#jeden-monat)
5. [Checkliste zum Kopieren](#checkliste-zum-kopieren)

## Jede Ausgabe

Aus [Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md):

| Schritt | Wer | Wo |
|---|---|---|
| 1. Die Ausgabe mit ihren Artikeln anlegen | Autor | Inhaltsbaum, unterhalb der Liste |
| 2. Den Skin wählen | Autor | Versandformular der Ausgabe |
| 3. Als Abonnent in der Vorschau ansehen und an eine Testgruppe senden | Autor | `newsletter/preview_as/...`, Formular für den Testversand |
| 4. Um Freigabe bitten (wenn die Liste das verlangt) | Autor | `newsletter/approval/<edition>/<version>` |
| 5. Freigeben oder ablehnen | freigebende Person | Posteingang der Zusammenarbeit oder die Freigabeseite |
| 6. Den Versand planen | Versender | **Senden** an der Ausgabe |
| 7. Den Versand beobachten und das Archiv prüfen | Versender | Dashboard `/newsletter/index`, `newsletter/archive` |

Hinweise, die Ärger ersparen:

- **Als Abonnent in der Vorschau ansehen** zeigt Betreff, Sprache, Interessen, HTML- und Textteil so, wie ein Abonnent
  sie bekäme; nichts wird gesendet oder gespeichert.
- **Testgruppen** umfassen standardmäßig höchstens 25 Adressen; Test-E-Mails beginnen mit `[Test]` und werden nie in
  der Statistik gezählt.
- Die Freigabe gilt pro **Version**: Eine Änderung nach der Freigabe braucht eine neue Freigabe. Was die freigebende
  Person gesehen hat, ist das, was hinausgeht.
- **Artikelpools** können die neuesten Artikel eines Ordners anbieten; angeboten werden nur Inhalte, die anonyme
  Besucher lesen dürfen, weil ein Newsletter öffentlich ist.

## Der Tag des Versands

- [ ] Betreff, Preheader und Absendername stimmen.
- [ ] Jeder Link funktioniert und zeigt auf die öffentliche Website.
- [ ] Bilder haben Alternativtexte; die E-Mail liest sich auch ohne Bilder gut.
- [ ] Die Testgruppe hat sie erhalten, und jemand hat sie auf dem Smartphone gelesen.
- [ ] Die Ausgabe ist freigegeben (falls verlangt) und geplant.
- [ ] Während und nach dem Versand ist jemand im Dienst, nicht auf dem Heimweg
      ([Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md)).

Die ersten drei stammen aus der Checkliste für Website-Ersteller
([Newsletter sicher versenden](../../../../en/us/help/creators/website-creators/sending-newsletters.md)).

## Wenn etwas nicht hinausgehen soll

Erst stoppen, später erklären. Die Seite [Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md)
beschreibt, wie Sie einen Versand abbrechen, der nicht hinausgehen soll. Dann:

1. Teilen Sie dem Betreiber und dem Listenverantwortlichen mit, was an wen hinausgegangen ist, soweit das Archiv es zeigt.
2. Entscheiden Sie, ob eine Korrektur-E-Mail nötig ist; senden Sie sie nicht in Eile.
3. Schreiben Sie eine Zeile in die Routine dazu, was beim nächsten Mal zu prüfen ist.

## Jeden Monat

| Ansehen | Warum | Lesen Sie |
|---|---|---|
| Die Problemliste im Newsletter-Dashboard | Probleme bei Transport, Cron und Konfiguration | [cjw_newsletter](../../../../en/us/features/6.0/extensions/cjw_newsletter.md) |
| Bounces, Blacklist und Sperrliste | eine saubere Liste hält die Zustellung gesund | [Bounces und Listenpflege](../../../../en/us/help/businesses/newsletters/bounces-and-list-hygiene.md) |
| An- und Abmeldungen | wächst die Liste, und warum gehen Menschen? | [Listen und Abonnements](../../../../en/us/help/businesses/newsletters/lists-and-subscriptions.md) |
| Statistik, wo Abonnenten eingewilligt haben | welche Artikel geöffnet wurden | [Opt-in und Einwilligung](../../../../en/us/help/businesses/newsletters/opt-in-and-consent.md) |
| Wiederkehrende Versände | liefen wie erwartet; Läufe, die ausfielen, während Cron stand, werden nicht nachgeholt | [Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md) |

## Checkliste zum Kopieren

```text
EDITION: ______________________   List: ______________   Send date: __________
Writer: ______________   Approver: ______________   Sender: ______________
[ ] articles placed          [ ] skin chosen            [ ] previewed as a subscriber
[ ] test group received it   [ ] read on a phone        [ ] links checked
[ ] alternative texts        [ ] approved (version __)  [ ] scheduled for ______
[ ] send watched             [ ] archive checked        Notes: ____________________
```

## Verwandte Seiten

- [Newsletter](newsletter.md)
- [Newsletter-Checkliste](../../../../en/us/help/businesses/newsletters/newsletter-checklist.md)
- [Redaktionskalender](../alltag/redaktionskalender.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
