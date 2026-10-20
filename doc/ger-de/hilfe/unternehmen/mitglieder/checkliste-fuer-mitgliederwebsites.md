---
title: Checkliste für Mitgliederwebsites
audience: businesses, project-manager, website-creator
category: business-solutions
order: 60
summary: Eine Checkliste für den Start und Betrieb einer Mitgliederwebsite auf Exponential - Grundlagen, Registrierung, Inhalte nur für Mitglieder, Kommunikation, Veranstaltungen, Datenschutz, Betrieb, die ersten Mitglieder und die Routine danach.
keywords: Checkliste für Mitgliederwebsites, Start, Go-live, Registrierungstest, Test des Mitgliederbereichs, Generalprobe des Newsletters, Veranstaltungen, Datenschutz, Backups, Cronjobs, Pflege der Mitgliederliste, Routine, member site checklist, launch, go-live, registration test, members area test, newsletter rehearsal, events, privacy, backups, cronjobs, member list hygiene, routine
type: help
---
# Checkliste für Mitgliederwebsites

Diese Seite ist für die Person, die eine Mitgliederwebsite startet, oft ein kleines Team oder Ehrenamtliche. Jeder Block
listet auf, was zu prüfen ist, und verlinkt die Seite, die es erklärt. Tragen Sie bei jeder Zeile einen Namen ein;
haken Sie sie erst ab, wenn sie ausprobiert wurde, nicht wenn sie eingerichtet wurde.

## Inhalt

1. [Grundlagen](#grundlagen)
2. [Registrierung](#registrierung)
3. [Inhalte nur für Mitglieder](#inhalte-nur-für-mitglieder)
4. [Kommunikation](#kommunikation)
5. [Veranstaltungen](#veranstaltungen)
6. [Datenschutz](#datenschutz)
7. [Betrieb](#betrieb)
8. [Die ersten Mitglieder](#die-ersten-mitglieder)
9. [Die Routine danach](#die-routine-danach)
10. [Verwandte Seiten](#verwandte-seiten)

## Grundlagen

- [ ] Zweck, Stufen und verantwortliche Person entschieden ([Mitgliederwebsite planen](mitgliederwebsite-planen.md)).
- [ ] Die Website antwortet unter ihrer Adresse mit HTTPS
  ([Live gehen: Hosts, HTTPS und Siteaccesses](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).
- [ ] Das Standardpasswort des Administrators geändert
  ([Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- [ ] Der öffentliche Siteaccess hat `RequireUserLogin=false`
  ([Was Besucher sehen dürfen, Common mistakes](../../../../eng-us/help/creators/website-creators/visitor-permissions.md#common-mistakes)).
- [ ] Barrierefreiheit auf den wichtigsten Seiten geprüft
  ([Barrierefreiheit testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Registrierung

- [ ] Registrierung offen oder geschlossen; `user/register` nur dann an Anonymous vergeben, wenn offen
  ([Registrierung von Mitgliedern](registrierung-von-mitgliedern.md#die-registrierung-öffnen)).
- [ ] `DefaultUserPlacement` zeigt im öffentlichen Siteaccess auf die Gruppe Mitglieder (oder Bewerber).
- [ ] Die Rolle Mitglied hat nur, was Mitglieder brauchen.
- [ ] Eine Testregistrierung: Formular, Aktivierungsmail, Aktivierungslink, Anmeldung.
- [ ] Wenn die Organisation Mitglieder freigibt: Die Mail „Neuer Benutzer registriert“ kommt an, und ihre Links
  funktionieren ([Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md)).
- [ ] CAPTCHA in der Benutzerklasse
  ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).
- [ ] Vergessenes Passwort getestet
  ([Das Passwort eines Benutzers zurücksetzen](../../../../eng-us/features/6.0/reset-user-password.md)).

## Inhalte nur für Mitglieder

- [ ] Mitgliedersektion dem Mitgliederordner zugewiesen ([Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md)).
- [ ] Abgemeldet: Mitgliederseiten verlangen eine Anmeldung; Mitgliederdokumente werden abgelehnt; die Suche findet
  keinen Mitgliedertext.
- [ ] Als Mitglied angemeldet: Seiten und Dokumente öffnen sich; höhere Stufen werden abgelehnt.
- [ ] Öffentliche Teaser für wichtige Mitgliederinhalte.
- [ ] Moderatoren für Foren benannt, falls Foren angeboten werden.

## Kommunikation

- [ ] Newsletter-Listen pro Zielgruppe ([Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md)).
- [ ] Eine vollständige Generalprobe des Newsletters mit Dateitransport erledigt
  ([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
- [ ] Cron-Teile des Newsletters eingeplant.
- [ ] Fußzeile der E-Mail-Einstellungen: Name und Postanschrift der Organisation ausgefüllt.
- [ ] Einträge der Absenderdomain vom Hoster geprüft ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).
- [ ] `notification/use` in der Rolle Mitglied, wenn Benachrichtigungen angeboten werden.

## Veranstaltungen

- [ ] Veranstaltungskalender in den richtigen Sektionen ([Veranstaltungen und Kalender](veranstaltungen-und-kalender.md)).
- [ ] Anmeldeformular von Anfang bis Ende getestet, einschließlich der Mail an die Organisatoren.
- [ ] Kommende Veranstaltungen auf der Startseite sichtbar.

## Datenschutz

- [ ] Registrierungs- und Anmeldeformulare sagen, was erhoben wird und warum
  ([Einwilligung und Formulare](../../../../eng-us/help/businesses/security/consent-and-forms.md)).
- [ ] Nur die nötigen Felder in der Benutzerklasse.
- [ ] Suchstatistiken ausgeschaltet gelassen oder mit einem Grund eingeschaltet
  ([Suche auf der Website, Search statistics](../../../../eng-us/help/creators/website-creators/site-search.md#search-statistics)).
- [ ] Aufbewahrung für unbestätigte und inaktive Konten vereinbart
  ([Aufbewahrung und Löschung von Daten](../../../../eng-us/help/businesses/security/data-retention-and-deletion.md)).
- [ ] Datenschutzhinweis veröffentlicht ([DSGVO-Grundlagen](../../../../eng-us/help/businesses/security/gdpr-basics.md)).

## Betrieb

- [ ] Cronjobs eingeplant, einschließlich der Gruppe `frequent` ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Backups eingeplant und eine Wiederherstellung ausprobiert
  ([Backups, die sich wiederherstellen lassen](../../../../eng-us/help/businesses/reliability/backups-that-restore.md)).
- [ ] Plan für Aktualisierungen und eine Ansprechperson für Probleme
  ([Aktualisierungen ohne Überraschungen](../../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md)).
- [ ] Zweistufige Anmeldung für Administratoren und Redakteure
  ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] `MaxNumberOfFailedLogin` gesetzt
  ([Zwei-Faktor und Anmeldung](../intranets/zwei-faktor-und-anmeldung.md#ein-konto-nach-fehlgeschlagenen-anmeldungen-sperren)).

## Die ersten Mitglieder

- [ ] Der Vorstand oder eine kleine Gruppe registriert sich zuerst und meldet Probleme.
- [ ] Eine Ankündigung an alle Mitglieder mit der Adresse und dem Weg zum Beitritt.
- [ ] Jemand beantwortet in den ersten Wochen Fragen.

## Die Routine danach

| Rhythmus | Aufgabe | Seite |
|---|---|---|
| Wöchentlich | Registrierungen freigeben oder bereinigen | [Rollen und Richtlinien, 7](../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen) |
| Monatlich | Newsletter; Bounces und die Sperrliste prüfen | [Newsletter-Listen und Abonnements](../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md) |
| Vierteljährlich | inaktive und doppelte Konten prüfen | [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md) |
| Jährlich | Rollen, Sektionen und den Datenschutzhinweis prüfen | [Zugriffsprüfungen](../../../../eng-us/help/businesses/security/access-reviews.md) |

Passen Sie den Rhythmus an die Größe Ihrer Organisation an.

## Verwandte Seiten

- [Mitgliederwebsites](README.md)
- [Checkliste für kleine Gruppen](../vereine/checkliste-fuer-kleine-gruppen.md)
- [Checkliste für den Start für Website-Ersteller](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Vorlage Checkliste Go-live](../vorlagen/checkliste-go-live.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
