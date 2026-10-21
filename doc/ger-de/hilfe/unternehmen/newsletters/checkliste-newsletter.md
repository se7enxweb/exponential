---
title: Checkliste Newsletter
audience: businesses, project-manager, editor, admin
category: business-solutions
order: 80
summary: Eine Checkliste für einen Newsletter auf Exponential mit cjw_newsletter - Entscheidungen, Einrichtung, Einwilligung, Probe, der erste echte Versand, jeder Versand danach und die monatliche Routine - jeder Punkt verlinkt auf die Seite, die ihn erklärt.
keywords: Checkliste Newsletter, Livegang, erster Versand, Checkliste Einwilligung, Probe, monatliche Routine, Newsletter-Start, newsletter checklist, go-live, first send, consent checklist, rehearsal, monthly routine, cjw_newsletter, newsletter launch
type: help
---
# Checkliste Newsletter

Diese Seite richtet sich an die Person, die sagen muss: „Der Newsletter ist bereit.“ Sie sammelt die Prüfungen dieses
Bereichs in der Reihenfolge, in der Sie ihnen begegnen, von der ersten Entscheidung bis zur Routine nach dem Start.
Kopieren Sie sie in Ihr Projektwerkzeug; jeder Punkt verlinkt auf die Seite, die ihn erklärt.

## Inhalt

1. [Entscheidungen](#entscheidungen)
2. [Einrichtung](#einrichtung)
3. [Einwilligung und Datenschutz](#einwilligung-und-datenschutz)
4. [Probe](#probe)
5. [Livegang](#livegang)
6. [Vor jedem Versand](#vor-jedem-versand)
7. [Nach jedem Versand](#nach-jedem-versand)
8. [Jeden Monat](#jeden-monat)
9. [Abnahme](#abnahme)

## Entscheidungen

Aus [Einen Newsletter planen](einen-newsletter-planen.md):

- [ ] Das Versprechen an die Abonnenten ist in einem Satz aufgeschrieben (was, wie oft).
- [ ] Die Listen sind benannt; Interessen und Sprachen sind entschieden.
- [ ] Autor, Freigebender, Versender und Administrator sind namentlich benannte Personen.
- [ ] Freigabe pro Liste: ein oder aus.
- [ ] Tracking ist entschieden: aus, anonyme Summen oder pro Person mit Einwilligung.
- [ ] Mail-Anbieter, Absenderadresse und Domain sind gewählt.
- [ ] Zwei oder drei Kennzahlen für die monatliche Auswertung sind gewählt.

## Einrichtung

Aus [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md#einrichten):

- [ ] `cjw_newsletter` ist aktiv, und seine Klassenpakete sind installiert.
- [ ] Ein Ordner für Newsletter existiert, und `[NewsletterSettings] RootFolderNodeId` ist in
      `settings/override/cjw_newsletter.ini.append.php` auf ihn gesetzt.
- [ ] Ein Newsletter-System und die Listen darunter sind angelegt.
- [ ] Die Seiten zum Abonnieren, Einstellen und Abmelden sind von der Website aus verlinkt (Fußzeile oder ein Block).
- [ ] Anonyme Besucher haben die Richtlinien des Newsletter-Moduls zum Abonnieren.
- [ ] Rollen: Abonnentendaten und `newsletter/import_export` nur für namentlich benannte Personen
      ([Listen und Abonnements](listen-und-abonnements.md#wer-abonnentendaten-sehen-darf)).
- [ ] Freigebende sind in `[ApprovalSettings]` gesetzt, wenn Freigaben verwendet werden
      ([Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md#freigabe-vor-dem-versand)).
- [ ] Die pro Liste erlaubten Skins sind gewählt, oder ein Designer hat einen eigenen Skin gebaut.

## Einwilligung und Datenschutz

Aus [Opt-in und Einwilligung](opt-in-und-einwilligung.md) (Grundlagen, keine Rechtsberatung):

- [ ] Das Anmeldeformular nennt das Versprechen und verlinkt auf den Datenschutzhinweis.
- [ ] Das Double-Opt-in funktioniert für öffentliche Anmeldungen.
- [ ] Importe nur mit einer schriftlichen Einwilligungsquelle und Nachweisen, die außerhalb der Website aufbewahrt
      werden.
- [ ] Die Seite der E-Mail-Einstellungen ist aus jeder Mail erreichbar; die Abmeldung ist getestet.
- [ ] Die Aufbewahrungseinstellungen des Einwilligungsprotokolls sind mit Ihren Richtlinien abgeglichen.
- [ ] Eine namentlich benannte Person für Auskunfts- und Löschanfragen.
- [ ] Die Formulierungen sind von Ihrem Anwalt oder Datenschutzbeauftragten geprüft.

## Probe

Aus [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md#erst-die-probe):

- [ ] `TransportMethodCronjob`, `TransportMethodPreview` und `TransportMethodDirectly` stehen alle auf `file`
      (der mitgelieferte Vorschau-Transport sendet wirklich).
- [ ] `FileTransportMailDir` ist für den Benutzer des Webservers und den Cron-Benutzer beschreibbar.
- [ ] Die Cron-Teile `cjw_newsletter` und `cjw_newsletter_mailbox` laufen für den richtigen Siteaccess.
- [ ] Abonnieren, bestätigen, senden, empfangen (als Dateien), abmelden: alles mit Testadressen bestanden.
- [ ] Die `.eml`-Dateien sind in einem Mail-Programm geöffnet: Aussehen, Links und Abmeldung geprüft.

## Livegang

- [ ] SPF und DKIM sind bei Ihrem Anbieter für die Absenderdomain eingerichtet.
- [ ] Der Vorschau-Transport steht auf `smtp`; ein Test an interne Adressen ist in mehreren Mail-Programmen geprüft.
- [ ] Die Ratenbegrenzungen des Anbieters sind bekannt; `[ThrottleSettings]` ist bei Bedarf gesetzt
      ([Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md#ratenbegrenzungen-und-pakete)).
- [ ] Das Bounce-Postfach ist konfiguriert, und sein Cron-Teil läuft
      ([Bounces und Listenpflege](bounces-und-listenpflege.md#wie-zurückkommende-mails-den-newsletter-erreichen)).
- [ ] Alte Bounces und Abmeldungen aus einem früheren Werkzeug sind in die Sperrliste importiert.
- [ ] Die übrigen Transporte sind an einem geplanten Tag auf `smtp` umgestellt.
- [ ] Jemand steht bereit, um den ersten echten Versand zu beobachten und ihn bei Bedarf abzubrechen.

## Vor jedem Versand

Aus [Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md#vor-jedem-versand) und
[Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md):

- [ ] Die Vorschau als Abonnent ist geprüft; die Mail an die Testgruppe ist angekommen.
- [ ] Betreff, Vorschauzeile und Absendername stimmen.
- [ ] Jeder Link funktioniert und zeigt auf die öffentliche Website.
- [ ] Bilder haben Alternativtexte; die Mail liest sich auch ohne Bilder gut.
- [ ] Die richtige Liste ist ausgewählt.
- [ ] Freigegeben, wenn die Liste eine Freigabe verlangt; seitdem wurde nichts geändert.
- [ ] Der Versandzeitpunkt passt zu Ihren Lesern, und danach ist jemand erreichbar.

## Nach jedem Versand

- [ ] Das Dashboard zeigt den Versand als abgeschlossen, ohne Probleme.
- [ ] Bounces und Abmeldungen sind angesehen; Ungewöhnliches ist erklärt.
- [ ] Die Ausgabe ist im Archiv.

## Jeden Monat

Aus [Bounces und Listenpflege](bounces-und-listenpflege.md#eine-monatliche-pflegeroutine):

- [ ] Die Probleme im Dashboard sind abgearbeitet.
- [ ] Der Bounce-Cron-Teil ist kürzlich gelaufen.
- [ ] Alte wartende Abonnements sind geprüft.
- [ ] Exporte und CSV-Dateien sind gelöscht, sobald sie nicht mehr gebraucht werden.
- [ ] Die Rollen sind für Menschen geprüft, die die Stelle gewechselt oder die Organisation verlassen haben.
- [ ] Die gewählten Kennzahlen sind festgehalten und besprochen.

## Abnahme

| Bereich | Geprüft von | Datum |
|---|---|---|
| Entscheidungen | | |
| Einrichtung | | |
| Einwilligung und Datenschutz | | |
| Probe | | |
| Livegang | | |

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Checkliste: Go-live](../vorlagen/checkliste-go-live.md)
- [Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md)
- [Start-Checkliste für Websites](../../ersteller/website-ersteller/checkliste-fuer-den-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
