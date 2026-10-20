---
title: Checkliste für kleine Gruppen
audience: businesses, project-manager, editor, admin
category: business-solutions
order: 60
summary: Eine Checkliste für einen Verein, eine Gesellschaft oder eine ehrenamtlich geführte Gruppe mit Exponential - Planung, Bearbeiten durch Ehrenamtliche, Veranstaltungen und Neuigkeiten, Formulare, Datenschutz, Betrieb, Start, die monatliche Routine und die jährliche Übergabe - jeder Punkt mit der Seite verlinkt, die ihn erklärt.
keywords: Checkliste für kleine Gruppen, Checkliste für Vereinswebsites, Verein, Ehrenamtliche, Veranstaltungen, Formulare, Übergabe, monatliche Routine, jährliche Prüfung, small group checklist, club website checklist, association, volunteers, events, forms, handover, monthly routine, yearly review
type: help
---
# Checkliste für kleine Gruppen

Diese Seite ist für den Web-Koordinator eines Vereins, einer Gesellschaft oder einer anderen ehrenamtlich geführten
Gruppe. Sie sammelt die Prüfungen dieses Bereichs in der Reihenfolge, in der Sie ihnen begegnen, von der Planung bis
zur jährlichen Übergabe. Kopieren Sie sie in ein Dokument, das der Vorstand gemeinsam nutzt; jeder Punkt verweist auf
die Seite, die ihn erklärt.

## Inhalt

1. [Planung](#planung)
2. [Bearbeiten durch Ehrenamtliche](#bearbeiten-durch-ehrenamtliche)
3. [Veranstaltungen und Neuigkeiten](#veranstaltungen-und-neuigkeiten)
4. [Formulare](#formulare)
5. [Datenschutz](#datenschutz)
6. [Betrieb](#betrieb)
7. [Start](#start)
8. [Jeden Monat](#jeden-monat)
9. [Jedes Jahr und bei jeder Übergabe](#jedes-jahr-und-bei-jeder-übergabe)
10. [Abnahme](#abnahme)

## Planung

Aus [Website-Planung für kleine Gruppen](website-planung-fuer-kleine-gruppen.md):

- [ ] Zwei oder drei Dinge, die die Website leisten muss, aufgeschrieben.
- [ ] Seiten aufgelistet; mitgelieferte `ezwebin`-Klassen statt eigener verwendet.
- [ ] Rollen benannt (Koordinator, Redakteure, Formularleser, Administrator, Stellvertreter), nicht Personen.
- [ ] Öffentliche Inhalte, Inhalte nur für Mitglieder und Inhalte nur für den Vorstand getrennt.
- [ ] Domain, Hosting, Backups und Absenderadressen dort festgehalten, wo der Vorstand darüber verfügt.

## Bearbeiten durch Ehrenamtliche

Aus [Gemeinsam bearbeiten mit Ehrenamtlichen](gemeinsam-bearbeiten-mit-ehrenamtlichen.md):

- [ ] Ein persönliches Konto pro Person; keine gemeinsam genutzten Zugangsdaten.
- [ ] Rollen Benutzergruppen zugewiesen, auf Sektionen beschränkt.
- [ ] Richtlinien für vollen Zugriff und für das Bearbeiten von Rollen nur für die beiden Administratoren.
- [ ] Zweistufige Anmeldung für alle, die veröffentlichen dürfen.
- [ ] Freigabe für neue Autoren, falls gewünscht, mit laufendem Cronjob des Workflows.
- [ ] Hausregeln an jeden Ehrenamtlichen ausgegeben; Rundgang durch die Administration mit den eigenen Inhalten der
  Website gemacht.

## Veranstaltungen und Neuigkeiten

Aus [Veranstaltungen und Neuigkeiten für Gruppen](veranstaltungen-und-neuigkeiten-fuer-gruppen.md):

- [ ] Ordner „Neuigkeiten“ und Veranstaltungskalender aus den mitgelieferten Klassen.
- [ ] Ein Veranstaltungsredakteur benannt.
- [ ] Feeds für Neuigkeiten und Veranstaltungen aktiv; Mitglieder wissen, wie sie folgen oder sich anmelden.
- [ ] Die Startseite zeigt die nächsten Veranstaltungen und die neuesten Neuigkeiten automatisch.

## Formulare

Aus [Formulare und Rückmeldungen von Mitgliedern](formulare-und-rueckmeldungen-von-mitgliedern.md):

- [ ] Kontakt- und Beitrittsformular, jedes mit einer Funktionsadresse als Empfänger.
- [ ] Weiterleitung auf eine Dankeseite.
- [ ] Token eingeschaltet, CAPTCHA hinzugefügt, abgemeldet getestet.
- [ ] `infocollector/read` nur für die Personen, die antworten.

## Datenschutz

Grundlagen, keine Rechtsberatung ([Sicherheit und Datenschutz](../../../../eng-us/help/businesses/security/README.md)):

- [ ] Keine personenbezogenen Daten von Mitgliedern auf öffentlichen Seiten.
- [ ] Die Datenschutzerklärung deckt Formulare, den CAPTCHA-Anbieter und einen eventuellen Newsletter ab.
- [ ] Löschplan für Formulareinsendungen und Exporte.
- [ ] Eine benannte Rolle beantwortet Auskunfts- und Löschanfragen.

## Betrieb

Aus [Betrieb mit kleinem Budget](../../../../eng-us/help/businesses/nonprofits/low-budget-operations.md):

- [ ] Hosting-Modell gewählt; Datenbank gewählt (SQLite, sofern nicht viele Redakteure gleichzeitig veröffentlichen).
- [ ] Backups geplant und eine Wiederherstellung getestet.
- [ ] Updates nach Plan eingespielt, zuerst an einer Kopie getestet.
- [ ] Cron läuft.

## Start

- [ ] Die [Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md) des Website-Erstellers
  bestanden.
- [ ] HTTPS auf jedem Host.
- [ ] Redakteure mit einem kurzen Leitfaden für Redakteure eingewiesen
      ([Die Website an Redakteure übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)).

## Jeden Monat

- [ ] Kalender aktuell; vergangene Veranstaltungen archiviert.
- [ ] Formulareinsendungen beantwortet; alte nach Plan entfernt.
- [ ] Logs angesehen; Backups überprüft.
- [ ] Konten, die lange nicht gesehen wurden, unter **Benutzerkonten > Benutzerkonten prüfen** geprüft
      ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

## Jedes Jahr und bei jeder Übergabe

Aus [Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md):

- [ ] Zugänge zu Registrar, Hosting, Mail und Passwortmanager an die neuen Inhaber übertragen.
- [ ] Neuer Administrator hinzugefügt, bevor der scheidende stillgelegt wird.
- [ ] Konten der Ausscheidenden deaktiviert (oder deaktiviert und verschoben); Eigentümer von Inhalten bei Bedarf
  geändert.
- [ ] Leitfaden für Redakteure, Hausregeln und die Liste, wer was tut, aktualisiert.
- [ ] Funktionen, die niemand genutzt hat, entfernt; die Website klein gehalten.
- [ ] Ein Termin für die nächste Prüfung im Kalender der Gruppe.

## Abnahme

| Bereich | Geprüft von (Rolle) | Datum |
|---|---|---|
| Planung | | |
| Bearbeiten durch Ehrenamtliche | | |
| Veranstaltungen und Neuigkeiten | | |
| Formulare | | |
| Betrieb | | |
| Übergabe | | |

## Verwandte Seiten

- [Vereine und kleine Gruppen](README.md)
- [Checkliste für gemeinnützige Organisationen](../../../../eng-us/help/businesses/nonprofits/nonprofit-checklist.md)
- [Checkliste für den Jahresrückblick](../../../../eng-us/help/businesses/templates/annual-review-checklist.md)
- [Monatliche Betriebscheckliste](../../../../eng-us/help/businesses/templates/monthly-operations-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
