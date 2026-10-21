---
title: FAQ zu Mitgliederwebsites
audience: businesses, project-manager, website-creator
category: business-solutions
order: 70
summary: Kurze Antworten mit Quellen auf die Fragen, die Mitgliederorganisationen zum Betrieb ihrer Website auf Exponential stellen - Beitritt, Freigabe, Seiten und Dateien nur für Mitglieder, Stufen, Newsletter, Abmeldung, Veranstaltungen, Foren, Beiträge und personenbezogene Daten.
keywords: FAQ zu Mitgliederwebsites, Fragen zur Mitgliedschaft, Registrierung, Freigabe, nur für Mitglieder, Stufen, Newsletter, abmelden, Veranstaltungen, Kalender, Foren, Mitgliedsbeiträge, personenbezogene Daten, Ehrenamtliche, member site FAQ, membership questions, registration, approval, members only, levels, newsletter, unsubscribe, events, calendar, forums, membership fees, personal data, volunteers
type: help
---
# FAQ zu Mitgliederwebsites

Diese Seite ist für Vorstände, Mitarbeiter der Geschäftsstelle und Ehrenamtliche, die eine Mitgliederwebsite betreiben.
Jede Antwort ist kurz und verlinkt die Seite mit den Einzelheiten.

## Inhalt

1. [Beitritt](#beitritt)
2. [Inhalte nur für Mitglieder](#inhalte-nur-für-mitglieder)
3. [Kommunikation](#kommunikation)
4. [Veranstaltungen und Gemeinschaft](#veranstaltungen-und-gemeinschaft)
5. [Geld und Daten](#geld-und-daten)
6. [Die Website betreiben](#die-website-betreiben)
7. [Verwandte Seiten](#verwandte-seiten)

## Beitritt

**Können Menschen auf der Website beitreten?**
Ja. Mit der Richtlinie `user/register` in der Rolle Anonymous registrieren sich Besucher und bestätigen ihre
E-Mail-Adresse ([Registrierung von Mitgliedern](registrierung-von-mitgliedern.md)).

**Können wir jedes neue Mitglied prüfen, bevor es Zugriff bekommt?**
Ja. Die Geschäftsstelle bekommt eine Mail mit signierten Links, um jede Registrierung zu aktivieren oder abzulehnen,
und Registrierungen können bis zur Freigabe zurückgehalten werden
([Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md)).

**Kann die Geschäftsstelle Konten für Mitglieder anlegen, die auf Papier beigetreten sind?**
Ja, in der Administration; die Mitglieder setzen dann ihr Passwort mit „Forgot your password?“
([Registrierung von Mitgliedern, Von der Geschäftsstelle angelegte Konten](registrierung-von-mitgliedern.md#von-der-geschäftsstelle-angelegte-konten)).

**Was ist mit gefälschten Anmeldungen?**
Lassen Sie die E-Mail-Bestätigung eingeschaltet, fügen Sie der Benutzerklasse ein CAPTCHA-Feld hinzu und bereinigen Sie
unbestätigte Registrierungen regelmäßig
([Registrierung von Mitgliedern, Bots fernhalten](registrierung-von-mitgliedern.md#bots-fernhalten)).

## Inhalte nur für Mitglieder

**Wie verbergen wir Seiten vor der Öffentlichkeit?**
Legen Sie sie in eine Mitgliedersektion, die die Rolle Anonymous nicht liest; Besucher werden gebeten, sich anzumelden
([Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md)).

**Können Nichtmitglieder Dokumente herunterladen, wenn ein Link weitergegeben wird?**
Nein. Jeder Download prüft, ob die Person die Datei lesen darf
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads)).

**Können manche Mitglieder mehr sehen als andere?**
Ja, mit einer Sektion und einer Rolle pro Mitgliedschaftsstufe
([Inhalte nur für Mitglieder, Stufen der Mitgliedschaft](inhalte-nur-fuer-mitglieder.md#stufen-der-mitgliedschaft)).

**Können wir Besuchern zeigen, was sie bei einem Beitritt bekämen?**
Ja, mit öffentlichen Teaserseiten, die auf die Mitgliederinhalte verlinken
([Inhalte nur für Mitglieder, Teaser für Besucher](inhalte-nur-fuer-mitglieder.md#teaser-für-besucher)).

## Kommunikation

**Gibt es ein Newsletter-System?**
Ja, `cjw_newsletter`: Listen, Double-Opt-in, Versand per Cron, Bounces, Sperrliste und ein Archiv
([Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md#der-newsletter)).

**Können sich Mitglieder leicht abmelden?**
Ja. Jede optionale Mail hat einen Link zum Abmelden mit einem Klick und einen Link zur Seite der E-Mail-Einstellungen
der Person; eine Anmeldung ist nicht nötig
([E-Mail-Einstellungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/mail-preferences.md)).

**Können Mitglieder informiert werden, wenn neue Dokumente erscheinen?**
Ja, mit Benachrichtigungen für die Bereiche, denen sie folgen, sofort oder als Digest
([Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md)).

**Können wir einen Newsletter testen, ohne ihn an alle zu schicken?**
Ja. `cjw_newsletter` kann für eine vollständige Generalprobe alle Mails in Dateien schreiben
([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).

## Veranstaltungen und Gemeinschaft

**Gibt es einen Veranstaltungskalender?**
Ja. Das Design `ezwebin` liefert die Klassen Event, Event calendar und Multicalendar mit Kalender- und
Programmansicht ([Veranstaltungen und Kalender](veranstaltungen-und-kalender.md)).

**Können sich Mitglieder für Veranstaltungen anmelden?**
Mit einem Formular, das auf dem Information Collector aufbaut. Platzgrenzen, Wartelisten und Tickets sind nicht
eingebaut ([Veranstaltungen und Kalender, Anmelden](veranstaltungen-und-kalender.md#für-eine-veranstaltung-anmelden)).

**Können Mitglieder Veranstaltungen in den Kalender ihres Telefons übernehmen?**
Im Repository wurde kein Export von Kalenderdateien gefunden; das wäre eine Erweiterung
([Veranstaltungen und Kalender, Was nicht eingebaut ist](veranstaltungen-und-kalender.md#was-nicht-eingebaut-ist)).

**Gibt es ein Forum?**
`ezwebin` liefert Forenklassen und Templates; Mitglieder schreiben Beiträge, wenn ihre Rolle es erlaubt
([Inhalte nur für Mitglieder, Foren](inhalte-nur-fuer-mitglieder.md#foren-und-beiträge-von-mitgliedern)).

## Geld und Daten

**Können Mitglieder ihren Beitrag online bezahlen?**
Eine einmalige Zahlung lässt sich als Produkt über den eingebauten Shop mit einem Zahlungsanbieter verkaufen
([Shops](../shops/README.md), [Zahlungsanbieter](../../../../eng-us/help/creators/website-creators/payment-gateways.md)). Wiederkehrende
Beiträge und ein automatisches Ende der Mitgliedschaft sind keine dokumentierten Funktionen
([Mitgliederwebsite planen, Was nicht eingebaut ist](mitgliederwebsite-planen.md#was-nicht-eingebaut-ist)).

**Welche personenbezogenen Daten speichert die Website?**
Was die Benutzerklasse und die Formulare erheben, dazu Anmeldeprotokolle, Newsletter-Abonnements und das
Einwilligungsprotokoll der E-Mail-Einstellungen. Prüfen Sie es mit Ihrer Ansprechperson für den Datenschutz
([Personenbezogene Daten in Exponential](../sicherheit/personenbezogene-daten-in-exponential.md)). Diese FAQ ist keine
Rechtsberatung.

**Kann ein Mitglied herunterladen, was die Website über seine Mail-Einstellungen weiß?**
Ja, seine E-Mail-Einstellungen und jede Änderung als JSON oder CSV
([E-Mail-Einstellungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/mail-preferences.md)).

## Die Website betreiben

**Können Ehrenamtliche nur ihren eigenen Bereich bearbeiten?**
Ja, mit einer Redakteursrolle, die mit einer Sektions-Einschränkung zugeordnet ist
([Abteilungen, Rollen und Sektionen](../intranets/abteilungen-rollen-und-sektionen.md)).

**Wie gehen wir mit Mitgliedern um, die austreten?**
Deaktivieren Sie ihre Konten, oder verwenden Sie, wenn eingeschaltet, Deaktivieren und verschieben
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

**Was geschieht, wenn unsere Ehrenamtlichen wechseln?**
Siehe [Übergabe bei Wechsel der Ehrenamtlichen](../vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md).

**Was kostet es?**
Siehe den Bereich [Kosten](../kosten/README.md), dessen durchgerechnete Beispiele gekennzeichnete Beispielannahmen
verwenden.

## Verwandte Seiten

- [Mitgliederwebsites](README.md)
- [Checkliste für Mitgliederwebsites](checkliste-fuer-mitgliederwebsites.md)
- [Extranet-FAQ](../extranets/extranet-faq.md)
- [FAQ für Unternehmen](../faq-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
