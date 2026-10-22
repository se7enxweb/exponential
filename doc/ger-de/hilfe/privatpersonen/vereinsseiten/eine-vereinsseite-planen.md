---
title: Eine Vereinsseite planen
audience: individual, website-creator
category: personal-sites
order: 1711
summary: Wie eine ehrenamtliche Person mit dem Vorstand die Website eines Vereins oder einer Hobbygruppe mit Exponential plant - Zweck, öffentliche Teile und Teile nur für Mitglieder, wer was tut, Baum und Klassen, Bedarf an E-Mail und Cron, Geldfragen und ein Plan, der den Wechsel der ehrenamtlichen Person übersteht.
keywords: Vereinsseite, Planung, Vorstand, Ehrenamtliche, öffentlich, nur für Mitglieder, Baum, Klassen, Veranstaltungen, Newsletter, Formulare, E-Mail, Cron, Hosting, Budget, Übergabe, Kontinuität, club site, planning, committee, volunteers, handover, continuity
type: help
---
# Eine Vereinsseite planen

Diese Seite ist für Privatpersonen, die zugesagt haben, die Website eines Vereins zu bauen oder zu übernehmen. Das
Schwierigste an einer Vereinsseite ist nicht die Software, sondern dass Ehrenamtliche wechseln. Planen Sie das vom
ersten Tag an ein. Tragen Sie die Antworten in das [Arbeitsblatt für die Websiteplanung](../checklisten/arbeitsblatt-fuer-die-websiteplanung.md)
ein.

## Inhalt

1. [Mit dem Vorstand entscheiden](#mit-dem-vorstand-entscheiden)
2. [Öffentlich und nur für Mitglieder](#öffentlich-und-nur-für-mitglieder)
3. [Wer was tut](#wer-was-tut)
4. [Der Baum und die Klassen](#der-baum-und-die-klassen)
5. [Was der Server können muss](#was-der-server-können-muss)
6. [Geld](#geld)
7. [Kontinuität](#kontinuität)

## Mit dem Vorstand entscheiden

Bitten Sie in einer Vorstandssitzung um eine Stunde und gehen Sie mit Antworten hinaus:

| Frage | Warum |
|---|---|
| Wozu dient die Website: Mitglieder gewinnen, Mitglieder informieren, beides? | Entscheidet über die Startseite |
| Was müssen Mitglieder online tun können? | Neuigkeiten lesen, sich zu Veranstaltungen anmelden, den Newsletter erhalten, Dokumente herunterladen |
| Wer darf im Namen des Vereins veröffentlichen? | Rollen und Freigabe |
| Was muss intern bleiben? | Der Mitgliederbereich |
| Wem gehören die Domain und das Hostingkonto? | Dem Verein, nicht einer ehrenamtlichen Person privat |
| Welches Budget gibt es? | Hosting, Domain, E-Mail ([Mit kleinem Budget betreiben](../einrichtung/mit-kleinem-budget-betreiben.md)) |

## Öffentlich und nur für Mitglieder

| Öffentlich | Nur für Mitglieder |
|---|---|
| Über den Verein, Geschichte | Protokolle, Satzung, Mitgliederliste (falls vorhanden) |
| Wie man beitritt, Beiträge als Text | Interne Neuigkeiten |
| Treffzeiten und -ort, öffentliche Veranstaltungen | Anmeldungen zu Veranstaltungen, Ergebnisse |
| Kontaktformular | Foren, Dokumente |
| Newsletter-Abonnement | Newsletter-Archiv, falls gewünscht |

Die Mitgliederseite ist eine Sektion, die anonyme Besucher nicht lesen können
([Private Bereiche mit Rollen und Sektionen](../familienseiten/private-bereiche-mit-rollen-und-sektionen.md)).

## Wer was tut

| Rolle | Person | Rechte |
|---|---|---|
| Website-Administrator | Sie und eine Vertretung | Alles |
| Redakteure | Die Schriftführung, die Veranstaltungsorganisation | Neuigkeiten und Veranstaltungen schreiben und veröffentlichen |
| Newsletter-Versand | Die ehrenamtliche Person für Kommunikation | `cjw_newsletter` |
| Mitglieder | Alle anderen | Den Mitgliederbereich lesen, sich anmelden |

Einzelheiten: [Die Arbeit mit Ehrenamtlichen teilen](die-arbeit-mit-ehrenamtlichen-teilen.md).

## Der Baum und die Klassen

| Ordner | Klassen | Sektion |
|---|---|---|
| Über uns | *Article* | Standard |
| Mitglied werden | *Article*, *Feedback form* | Standard |
| Neuigkeiten | *Blog* / *Blog post* oder *Folder* / *Article* | Standard |
| Kalender | *Event calendar* / *Event* | Standard |
| Fotos | *Gallery* / *Image* | Standard oder Mitglieder |
| Mitglieder | *Folder* mit Dokumenten (*File*), Anmeldeformularen, Foren | Nur für Mitglieder |

Alle Klassen werden mit dem Standard-Seitenpaket geliefert; für den Anfang brauchen Sie keine neue Klasse.

## Was der Server können muss

| Bedarf | Weil | Seite |
|---|---|---|
| Zuverlässig E-Mails versenden | Registrierung, Zurücksetzen des Passworts, Formularbenachrichtigungen, Newsletter | [E-Mail-Zustellung von Newslettern](../../unternehmen/newsletters/zustellung-der-newsletter-mails.md) |
| Cron ausführen | Newsletter werden per Cron versendet; zeitgesteuertes Veröffentlichen ebenso | [Cronjobs](../../../anleitungen/cronjobs.md) |
| HTTPS | Mitglieder melden sich an | [Domainnamen und HTTPS](../einrichtung/domainnamen-und-https.md) |
| Sicherungen | Mitgliederdaten | [Sicherungen für eine Person](../im-betrieb/sicherungen-fuer-eine-person.md) |

Fragen Sie jeden Hoster vor dem Vertragsabschluss nach E-Mail und Cron
([Shared Hosting oder ein kleiner Server](../einrichtung/shared-hosting-oder-ein-kleiner-server.md)).

## Geld

- **Hosting und Domain**: laufende Kosten, die der Verein von seinem eigenen Konto bezahlen sollte.
- **Mitgliedsbeiträge**: Die Website kann sie erklären; sie online einzuziehen ist nicht eingebaut. Die meisten
  kleinen Vereine bleiben bei Überweisung oder einem Zahlungsdienst und verlinken darauf.
- **Hilfe**: Wenn der Verein etwas ausgeben kann, richtet ein Partner ein, was Sie nicht können
  ([Wann Sie sich Hilfe holen sollten](../im-betrieb/wann-sie-sich-hilfe-holen-sollten.md)).

## Kontinuität

- Die Domain, das Hostingkonto und das Administratorpasswort gehören dem Verein und sind dort hinterlegt, wo der
  Vorstand sie findet (ein versiegelter Umschlag, ein mit dem Vorsitz geteilter Passwortmanager).
- Immer zwei Administratoren.
- Ein einseitiges Handbuch im Mitgliederbereich: wie man Neuigkeiten einstellt, eine Veranstaltung hinzufügt, den
  Newsletter verschickt.
- Eine Übergabe-Checkliste für den Tag, an dem Sie aufhören ([Übergabe beim Wechsel von Ehrenamtlichen](../../unternehmen/vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md)).

## Verwandte Seiten

- [Vereinsseiten](README.md)
- [Website-Planung für kleine Gruppen](../../unternehmen/vereine/website-planung-fuer-kleine-gruppen.md)
- [Planung einer Mitgliederwebsite](../../unternehmen/mitglieder/mitgliederwebsite-planen.md)
- [Arbeitsblatt für die Websiteplanung](../checklisten/arbeitsblatt-fuer-die-websiteplanung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
