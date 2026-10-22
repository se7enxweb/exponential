---
title: Newsletter für lokale Unternehmen
audience: local-business, businesses, customer
category: local-business
order: 62
summary: Ein Newsletter für die Stammkunden eines lokalen Unternehmens mit cjw_newsletter, das mit Exponential ausgeliefert wird - Anmeldung mit Double-Opt-in, Ausgaben als Inhalte, Versand per Cronjob, Abmeldung, was es vom Hoster braucht (E-Mail, Cron, ein Postfach für Rückläufer), ein realistischer Versandrhythmus und was Sie schreiben.
keywords: Newsletter, cjw_newsletter, Verteilerliste, Stammkunden, Double-Opt-in, Einwilligung, Abmeldung, Ausgabe, Cron, E-Mail-Zustellung, Rückläufer, Bounce, lokales Unternehmen, newsletter, mailing list, regulars, double opt-in, consent, unsubscribe, edition, mail delivery, local business
type: help
---
# Newsletter für lokale Unternehmen

Diese Seite ist für lokale Unternehmen, die mit Stammkunden per E-Mail in Kontakt bleiben wollen. Das Newsletter-System
`cjw_newsletter` gehört zur Installation (`se7enxweb/cjw_newsletter` im `require`-Block der `composer.json`), es gibt
also keine zusätzliche Software zu kaufen
([cjw_newsletter für Unternehmen](../../unternehmen/newsletters/cjw-newsletter-fuer-unternehmen.md)).

## Inhalt

1. [Was es für Sie tut](#was-es-für-sie-tut)
2. [Was es braucht](#was-es-braucht)
3. [Abonnenten auf dem richtigen Weg gewinnen](#abonnenten-auf-dem-richtigen-weg-gewinnen)
4. [Was Sie senden und wie oft](#was-sie-senden-und-wie-oft)
5. [Eine Ausgabe versenden](#eine-ausgabe-versenden)
6. [Checkliste](#checkliste)

## Was es für Sie tut

Aus [cjw_newsletter für Unternehmen](../../unternehmen/newsletters/cjw-newsletter-fuer-unternehmen.md#kurz-gefasst):

- Abonnenten melden sich auf Ihrer Website an und **bestätigen per E-Mail** (Double-Opt-in).
- Jede E-Mail enthält einen Link zum **Abmelden**.
- Eine **Ausgabe** ist ein Inhalt im Inhaltsbaum, geschrieben im selben Editor wie Ihre Seiten.
- Der Versand läuft im Hintergrund per Cronjob, mit einem Archiv jedes Versands.
- Sie können einen Versand proben, indem E-Mails in Dateien geschrieben werden, ohne jemandem etwas zu schicken.
- Mehrere Listen sind möglich, zum Beispiel „Neuigkeiten und Angebote“ und „Nur Veranstaltungen“.

## Was es braucht

| Bedarf | Wer | Warum |
|---|---|---|
| Ein Server, der E-Mails versenden kann, mit einer für Ihre Domain eingerichteten Absenderadresse (SPF, DKIM) | Hoster | sonst landen E-Mails im Spam ([Zustellung von Newsletter-E-Mails](../../unternehmen/newsletters/zustellung-der-newsletter-mails.md)) |
| Cronjobs für die E-Mail-Warteschlange | Hoster oder Ersteller | der Versand geschieht im Hintergrund |
| Ein Postfach für Rückläufer | Hoster oder Ersteller | Adressen, die nicht mehr funktionieren, werden entfernt |
| Einrichtung der Listen und der Anmeldeseite | Ersteller | [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md) |

`cjw_newsletter` stellt E-Mails nicht selbst zu und ist kein gehosteter Versanddienst: Es übergibt die E-Mails an den
E-Mail-Transport Ihres Servers. Für eine kleine Liste ist das meist in Ordnung; fragen Sie Ihren Hoster nach
Versandgrenzen.

## Abonnenten auf dem richtigen Weg gewinnen

- Ein Anmeldefeld auf der Website, und ein Anmeldeformular an der Theke nur dann, wenn Sie die Personen anschließend
  über denselben Bestätigungsablauf eintragen.
- Tragen Sie keine Kunden ein, die Ihnen geschrieben, bei Ihnen gekauft oder Ihnen eine Visitenkarte gegeben haben,
  es sei denn, sie haben darum gebeten, den Newsletter zu erhalten. Die Regeln unterscheiden sich von Land zu Land;
  dies ist keine Rechtsberatung ([Opt-in und Einwilligung](../../unternehmen/newsletters/opt-in-und-einwilligung.md)).
- Sagen Sie auf dem Anmeldeformular, was Sie senden werden und wie oft.

## Was Sie senden und wie oft

Für ein lokales Unternehmen ist einmal im Monat ein realistischer Rhythmus, den man durchhalten kann. Eine einfache
Ausgabe:

| Teil | Beispiel |
|---|---|
| Eine Zeile zur Begrüßung | „Der Frühling ist da, und die neuen Räder auch.“ |
| Was neu ist | ein neues Produkt, eine neue Leistung oder ein neues Gericht, mit Foto |
| Was los ist | ein oder zwei Veranstaltungen |
| Ein Angebot für Abonnenten | falls Sie eines haben |
| Praktisches | Feiertagszeiten, geänderte Öffnungszeiten |
| Fußzeile | Adresse, Telefon, Abmelden (automatisch ergänzt) |

Halten Sie es kurz; die meisten Menschen lesen auf dem Smartphone ([Mobile Besucher](mobile-besucher.md)).

## Eine Ausgabe versenden

Aus [Ausgaben schreiben und versenden](../../unternehmen/newsletters/ausgaben-schreiben-und-versenden.md):

1. Legen Sie in der Liste eine neue Ausgabe an.
2. Schreiben Sie sie, fügen Sie Fotos hinzu, sehen Sie sich die Vorschau an.
3. Schicken Sie einen Test an sich selbst und eine Kollegin oder einen Kollegen; prüfen Sie auf einem Smartphone.
4. Versenden Sie, oder planen Sie den Versand.
5. Sehen Sie sich einen Tag später Rückläufer und Abmeldungen auf dem Newsletter-Dashboard an.

## Checkliste

- [ ] Vom Hoster bestätigt: E-Mail-Versand, DNS-Einträge der Absenderdomain, Cron, Postfach für Rückläufer.
- [ ] Eine Liste für den Anfang, mit einem klaren Versprechen auf dem Anmeldeformular.
- [ ] Double-Opt-in mit Ihrer eigenen Adresse getestet.
- [ ] Abmelden getestet.
- [ ] Ein monatlicher Termin für die Ausgabe im Kalender.

## Verwandte Seiten

- [Newsletter planen](../../unternehmen/newsletters/einen-newsletter-planen.md)
- [Newsletter-Routine](../../kunden/wachstum/newsletter-routine.md)
- [Rückläufer und Listenpflege](../../unternehmen/newsletters/bounces-und-listenpflege.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
