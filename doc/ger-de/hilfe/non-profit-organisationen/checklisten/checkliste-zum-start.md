---
title: Checkliste zum Start für Non-Profit-Organisationen
audience: non-profit, businesses, project-manager, website-creator
category: non-profits
order: 20
summary: Die Checkliste, die eine Non-Profit-Organisation durcharbeitet, bevor ihre Website mit Exponential live geht - Eigentum und Zugänge, Hosting, HTTPS und Cron, Sicherungen mit getesteter Wiederherstellung, Spenden, Formulare für Ehrenamtliche und Kontakt, Newsletter, Barrierefreiheit, Transparenzseiten, Datenschutzerklärung und Datenschutz, geschulte Redakteure, eingeschaltete Messungen und Abzeichnung.
keywords: Checkliste zum Start, Livegang, Relaunch, Eigentum, Hosting, HTTPS, Cron, Sicherungen, Wiederherstellungstest, Spenden, Formulare, CAPTCHA, Newsletter, Barrierefreiheit, Datenschutzerklärung, Schulung, Messungen, Abzeichnung, Non-Profit, launch checklist, go-live, relaunch, backups, restore test, donations, privacy notice, sign-off
type: help
---
# Checkliste zum Start für Non-Profit-Organisationen

Arbeiten Sie diese Liste durch, bevor die Website oder ein Relaunch live geht. Jede Zeile verlinkt auf die Seite, die
sie erklärt. Für die technischen Einzelheiten des Livegangs verwendet der Website-Ersteller zusätzlich die
[Checkliste zum Start](../../ersteller/website-ersteller/checkliste-fuer-den-start.md).

## Inhalt

1. [Eigentum und Zugänge](#eigentum-und-zugänge)
2. [Hosting und Technik](#hosting-und-technik)
3. [Spenden](#spenden)
4. [Formulare und Ehrenamtliche](#formulare-und-ehrenamtliche)
5. [Newsletter und Kommunikation](#newsletter-und-kommunikation)
6. [Barrierefreiheit](#barrierefreiheit)
7. [Transparenz und Datenschutz](#transparenz-und-datenschutz)
8. [Personen und Routine](#personen-und-routine)
9. [Abzeichnung](#abzeichnung)

## Eigentum und Zugänge

- [ ] Domain auf den Namen der Organisation registriert; zwei Personen können sie verwalten.
- [ ] Hosting-Vertrag auf den Namen der Organisation.
- [ ] Mindestens zwei Administratorkonten mit Zwei-Faktor-Anmeldung ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Standard-Administratorpasswort geändert ([Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- [ ] Jeder Redakteur hat ein persönliches Konto in der richtigen Gruppe ([Arbeitsblatt Rollen und Berechtigungen](../vorlagen/arbeitsblatt-rollen-und-berechtigungen.md)).

## Hosting und Technik

- [ ] Anforderungen mit dem Hoster bestätigt ([Hosting mit kleinem Budget](../betrieb/hosting-mit-kleinem-budget.md)).
- [ ] HTTPS auf jeder Seite; HTTP wird weitergeleitet ([Livegang: Hoster und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).
- [ ] Cron läuft, einschließlich der Gruppe `frequent` ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Mit SQLite: Die Datenbankdatei ist nicht herunterladbar ([SQLite und Velocity](../betrieb/sqlite-und-velocity-fuer-kleine-teams.md)).
- [ ] Sicherungen von Datenbank, Speicher und Einstellungen laufen; eine Wiederherstellung getestet ([Routine für Sicherungen und Updates](../betrieb/routine-fuer-sicherungen-und-updates.md)).
- [ ] Eigenes `[HTMLForms] Secret` gesetzt ([Spamschutz für Ehrenamtsformulare](../ehrenamtliche/spamschutz-fuer-ehrenamtsformulare.md)).
- [ ] Mailversand getestet.

## Spenden

- [ ] Spendenweg gewählt und schriftlich festgehalten ([Spendenwege im Überblick](../spenden/spendenwege-im-ueberblick.md)).
- [ ] Spendenseite vollständig ([Die Spendenseite](../spenden/die-spendenseite.md)).
- [ ] Externe Plattform: Link und Rückkehrseite getestet; oder Shop: Sandbox und eine kleine echte Spende getestet.
- [ ] Zuwendungsbestätigungen mit der Buchhaltung abgestimmt.
- [ ] Spendenseite nur durch namentlich benannte Mitarbeitende bearbeitbar.

## Formulare und Ehrenamtliche

- [ ] Formular für Ehrenamtliche nach dem freigegebenen Arbeitsblatt gebaut ([Arbeitsblatt Felder des Ehrenamtsformulars](../vorlagen/arbeitsblatt-felder-des-ehrenamtsformulars.md)).
- [ ] CAPTCHA in öffentlichen Formularen; abgemeldet getestet, einschließlich der barrierefreien Alternative.
- [ ] Empfänger sind gemeinsame Postfächer; Weiterleitung auf Dankeseiten.
- [ ] `infocollector/read` nur für Koordinatoren.

## Newsletter und Kommunikation

- [ ] Listen in `cjw_newsletter` angelegt; Probelauf mit Dateitransport durchgeführt; echter Versand getestet ([Unterstützer-Newsletter](../kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md)).
- [ ] Abonnementlink in der Fußzeile und auf den Dankeseiten.
- [ ] Veranstaltungskalender eingerichtet, falls verwendet ([Veranstaltungen und Kalender](../kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md)).
- [ ] Feeds für Nachrichten und Veröffentlichungen ([Offene Daten, RSS und REST](../transparenz/offene-daten-rss-und-rest.md)).
- [ ] Metadaten zum Teilen in sozialen Medien geprüft.

## Barrierefreiheit

- [ ] Templates geprüft ([Grundlagen der Barrierefreiheit](../barrierefreiheit/barrierefreiheit-grundlagen-fuer-non-profit-organisationen.md)).
- [ ] Wichtige Seiten mit der Tastatur und einem automatischen Prüfwerkzeug getestet.
- [ ] Erklärung zur Barrierefreiheit veröffentlicht ([Erklärung zur Barrierefreiheit](../barrierefreiheit/erklaerung-zur-barrierefreiheit-fuer-non-profit-organisationen.md)).

## Transparenz und Datenschutz

- [ ] Wer wir sind, offizieller Name und Registerangaben angezeigt.
- [ ] Neueste Jahresveröffentlichung und Jahresabschluss online ([Jahresveröffentlichungen als Downloads](../transparenz/jahresveroeffentlichungen-als-downloads.md)).
- [ ] Die Datenschutzerklärung nennt jeden Auftragsverarbeiter ([Datenschutz für Spender und Ehrenamtliche](../betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md)).
- [ ] Entscheidungen zu Cookies und Analyse getroffen ([Wirkung messen](../foerdermittel/wirkung-mit-website-daten-messen.md)).
- [ ] [Datenschutz-Checkliste](datenschutz-checkliste.md) abgeschlossen.

## Personen und Routine

- [ ] Redakteure geschult ([Ehrenamtliche Redakteure schulen](../ehrenamtliche/ehrenamtliche-redakteure-schulen.md)).
- [ ] Freigabe-Workflow für neue Autoren mit zwei Freigebenden.
- [ ] Die monatliche Checkliste hat einen Namen und einen Termin im Kalender ([Monatliche Checkliste](monatliche-checkliste.md)).
- [ ] Ausgangszahlen für die Messgrößen erfasst, über die Sie berichten werden.
- [ ] Schriftlich festgehalten, wen Sie anrufen, wenn die Website ausfällt ([Wann Sie wen anrufen](../../kunden/partner-und-hoster/wann-sie-wen-anrufen.md)).

## Abzeichnung

```text
Project owner ............................ date ________
Technical lead ........................... date ________
Data protection contact .................. date ________
```

## Verwandte Seiten

- [Checklisten für Non-Profit-Organisationen](README.md)
- [Checkliste für Non-Profit-Organisationen (Unternehmen)](../../unternehmen/gemeinnuetzige/checkliste-fuer-gemeinnuetzige.md)
- [Checkliste zum Livegang](../../unternehmen/vorlagen/checkliste-go-live.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
