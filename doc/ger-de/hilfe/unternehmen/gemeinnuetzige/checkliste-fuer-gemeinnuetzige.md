---
title: Checkliste für gemeinnützige Organisationen
audience: businesses, project-manager, editor, admin
category: business-solutions
order: 70
summary: Eine Checkliste für die Website einer gemeinnützigen Organisation auf Exponential - Planung, Spenden, Formulare für Ehrenamtliche, Kampagnen, Newsletter, Barrierefreiheit, Datenschutz, Betrieb mit kleinem Budget, Start und die monatliche Routine - jeder Punkt verlinkt auf die Seite, die ihn erklärt.
keywords: Checkliste gemeinnützige Organisation, Checkliste Website Hilfsorganisation, Start, Spenden, Ehrenamtliche, Kampagnen, Barrierefreiheit, Datenschutz, Betrieb, monatliche Routine, nonprofit checklist, charity website checklist, launch, donations, volunteers, campaigns, accessibility, data protection, operations, monthly routine
type: help
---
# Checkliste für gemeinnützige Organisationen

Diese Seite richtet sich an die Person, die in einer gemeinnützigen Organisation sagen muss: „Die Website ist bereit.“
Sie sammelt die Prüfungen dieses Bereichs in der Reihenfolge des Projekts, sodass Sie sie in ein gemeinsames Dokument
kopieren und mit Ihrem Vorstand oder Ihren Ehrenamtlichen abhaken können. Jeder Punkt verlinkt auf die Seite, die ihn
erklärt.

## Inhalt

1. [Planung](#planung)
2. [Spenden](#spenden)
3. [Ehrenamtliche und Formulare](#ehrenamtliche-und-formulare)
4. [Kampagnen](#kampagnen)
5. [Newsletter](#newsletter)
6. [Barrierefreiheit](#barrierefreiheit)
7. [Datenschutz](#datenschutz)
8. [Betrieb](#betrieb)
9. [Start](#start)
10. [Jeden Monat](#jeden-monat)
11. [Abnahme](#abnahme)

## Planung

Aus [Website-Planung für gemeinnützige Organisationen](website-planung-fuer-gemeinnuetzige.md):

- [ ] Ziele sind aufgeschrieben, jedes mit einem beobachtbaren Zeichen.
- [ ] Zielgruppen und ihre wichtigsten Aufgaben sind aufgelistet; das Hauptmenü ist entworfen.
- [ ] Die Liste der Seiten ist abgestimmt (Startseite, Über uns, Unsere Arbeit, Hilfe finden, Spenden, Mitmachen,
      Neuigkeiten, Berichte, Kontakt, Datenschutz, Erklärung zur Barrierefreiheit).
- [ ] Redakteure, Leser der Formulare und Administrator sind benannt; für jede Rolle gibt es eine zweite Person.
- [ ] Die Funktionen sind gewählt, die wenigsten zuerst.

## Spenden

Aus [Spenden und Spendenaufrufe](spenden-und-spendenaufrufe.md):

- [ ] Möglichkeit gewählt: externe Plattform, Shop-Artikel mit `ezpaypal` oder beides; die Begründung ist
      aufgeschrieben.
- [ ] Allen ist klar, dass es in Exponential kein eigenes Spendenmodul, kein Feld für freie Beträge und keine
      regelmäßigen Spenden gibt.
- [ ] Die Spendenseite ist vollständig und auf einem Telefon getestet.
- [ ] Shop-Möglichkeit: Sandbox-Zahlung abgeschlossen, die Bestellung wurde bezahlt; eine abgebrochene Zahlung lässt sie
      unbezahlt.
- [ ] Quittungen und steuerliche Behandlung sind mit dem Steuerberater abgestimmt.

## Ehrenamtliche und Formulare

Aus [Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md):

- [ ] Die Formularfelder sind auf das Nötige beschränkt; der Text der Einwilligung steht neben dem Kontrollkästchen.
- [ ] Der Empfänger ist pro Formular gesetzt; Weiterleitung auf eine Dankesseite.
- [ ] Das Formular-Token ist aktiv; ein CAPTCHA ist hinzugefügt; abgemeldet getestet.
- [ ] Datei-Uploads nur, wenn nötig, mit gesetztem `MaxFiles`.
- [ ] `infocollector/read` nur für Koordinatoren.

## Kampagnen

Aus [Kampagnen und Landingpages](kampagnen-und-landingpages.md):

- [ ] Redakteure sind im Layout-Editor geschult; jeweils eine Person bearbeitet ein Layout.
- [ ] Kampagnenregeln mit Zeitbedingungen sind auf der Website geprüft.
- [ ] Jede Schaltfläche ist abgemeldet getestet.

## Newsletter

Aus dem Bereich [Newsletter](../newsletter/README.md):

- [ ] Das Versprechen an die Unterstützer ist aufgeschrieben; das Double-Opt-in ist eingeschaltet.
- [ ] Die Probe mit dem Datei-Transport ist bestanden (alle drei Transporte auf `file`).
- [ ] Absenderdomain mit SPF und DKIM.
- [ ] Die Behandlung von Bounces ist eingerichtet.

## Barrierefreiheit

Aus [Barrierefreiheit für gemeinnützige Organisationen](barrierefreiheit-fuer-gemeinnuetzige.md):

- [ ] Die Anforderungen an die Templates stehen im Briefing oder Vertrag.
- [ ] Bildfelder verlangen Alternativtexte; Redakteure haben die einseitige Liste.
- [ ] Die Seiten für Spenden, Ehrenamt und Kontakt sind mit der Tastatur, bei 200 % Zoom und mit einem Screenreader
      getestet.
- [ ] Eine Erklärung zur Barrierefreiheit ist veröffentlicht, mit einer Möglichkeit, Probleme zu melden.

## Datenschutz

Grundlagen, keine Rechtsberatung; siehe [Sicherheit und Datenschutz](../sicherheit/README.md):

- [ ] Der Datenschutzhinweis nennt den Zahlungsanbieter, den CAPTCHA-Anbieter und die Webanalyse.
- [ ] Die Aufbewahrung von Formulareingaben, Newsletter-Daten und Exporten ist entschieden.
- [ ] Eine namentlich benannte Person für Auskunfts- und Löschanfragen.
- [ ] Administratorkonten sind mit zweistufiger Anmeldung geschützt ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

## Betrieb

Aus [Betrieb mit kleinem Budget](betrieb-mit-kleinem-budget.md):

- [ ] Hosting-Modell gewählt; Datenbank gewählt (SQLite, sofern nicht viele Redakteure gleichzeitig veröffentlichen).
- [ ] Backups von Datenbank und Speicher sind eingeplant; eine Wiederherstellung ist getestet.
- [ ] Eine Update-Routine ist vereinbart, mit einer Kopie zum Testen.
- [ ] Cron läuft.
- [ ] Die monatlichen Prüfungen haben einen Namen und ein Datum.

## Start

- [ ] Die [Start-Checkliste](../../ersteller/website-ersteller/checkliste-fuer-den-start.md) für Website-Ersteller ist bestanden.
- [ ] HTTPS auf jedem Host ([Online gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).
- [ ] Die Redakteure haben die Website mit einer kurzen Anleitung übernommen
      ([Die Website an Redakteure übergeben](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)).
- [ ] Die Adressen der alten Website sind umgeleitet ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)).

## Jeden Monat

- [ ] Formulareingaben sind bearbeitet, und alte werden nach Plan entfernt.
- [ ] Spenden oder Bestellungen sind mit den Aufzeichnungen des Anbieters abgeglichen.
- [ ] Logs und das Newsletter-Dashboard sind angesehen.
- [ ] Konten ausgeschiedener Ehrenamtlicher sind deaktiviert ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- [ ] Updates sind geprüft; Backups sind kontrolliert.

## Abnahme

| Bereich | Geprüft von | Datum |
|---|---|---|
| Planung | | |
| Spenden | | |
| Formulare | | |
| Barrierefreiheit | | |
| Datenschutz | | |
| Betrieb | | |

## Verwandte Seiten

- [Gemeinnützige Organisationen](README.md)
- [Checkliste: Go-live](../vorlagen/checkliste-go-live.md)
- [Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md)
- [Checkliste für kleine Gruppen](../vereine/checkliste-fuer-kleine-gruppen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
