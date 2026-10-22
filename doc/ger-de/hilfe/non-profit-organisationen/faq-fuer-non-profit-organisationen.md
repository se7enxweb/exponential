---
title: FAQ für Non-Profit-Organisationen
audience: non-profit, businesses, government
category: non-profits
order: 90
summary: Kurze Antworten, jeweils mit Quelle, auf die Fragen, die Geschäftsführungen, Fundraiser, Koordinatoren von Ehrenamtlichen und Schatzmeister von Non-Profit-Organisationen zum Betrieb ihrer Website mit Exponential stellen - Kosten, Spenden, Fördermittel, Ehrenamtliche, Barrierefreiheit, Transparenz, Newsletter, Veranstaltungen, Sprachen, Hosting, Datenschutz und Partner.
keywords: FAQ, Fragen, Non-Profit-Organisation, gemeinnützig, Verein, Kosten, Lizenzgebühr, Spenden, Spendenbutton, regelmäßige Spenden, Fördermittel, Ehrenamtliche, Barrierefreiheit, Jahresbericht, Protokolle, Newsletter, Veranstaltungen, Sprachen, Hosting, SQLite, Velocity, DSGVO, Partnerrabatt, questions, non-profit, charity, cost, license fee, donations, donate button, recurring giving, grants, volunteers, accessibility, annual report, minutes, events, languages, GDPR, partner discount
type: help
---
# FAQ für Non-Profit-Organisationen

Kurze Antworten auf die Fragen, die Non-Profit-Organisationen am häufigsten stellen. Jede Antwort nennt ihre Quelle und
verlinkt auf die Seite mit den Einzelheiten. Wenn Ihre Frage nicht dabei ist, beginnen Sie bei
[Non-Profit-Organisationen: hier beginnen](non-profit-organisationen-hier-beginnen.md).

## Inhalt

1. [Kosten und Lizenz](#kosten-und-lizenz)
2. [Spenden](#spenden)
3. [Fördermittel](#fördermittel)
4. [Ehrenamtliche](#ehrenamtliche)
5. [Barrierefreiheit und Transparenz](#barrierefreiheit-und-transparenz)
6. [Newsletter, Veranstaltungen und Sprachen](#newsletter-veranstaltungen-und-sprachen)
7. [Hosting und Betrieb der Website](#hosting-und-betrieb-der-website)
8. [Datenschutz](#datenschutz)
9. [Partner und Hilfe](#partner-und-hilfe)

## Kosten und Lizenz

**Zahlen wir eine Lizenzgebühr?**
Nein. „Using Exponential under the terms of the GNU GPL is free of charge“ (`README.md`, „License“): Die Nutzung von
Exponential unter den Bedingungen der GNU GPL ist kostenlos. Sie bezahlen für das Hosting und für Arbeit, die Sie
einkaufen ([Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md)).

**Gibt es eine Gebühr pro Redakteur oder pro Sprache?**
Nein. Es gibt keine Lizenz, also gibt es auch nichts zu zählen. Ihre Kosten sind das Hosting, eine Domain und die Zeit
der beteiligten Menschen ([Budgetposten für Hosting und Wartung](foerdermittel/budgetposten-fuer-hosting-und-wartung.md)).

**Müssen Newsletter, Formulare und Landingpages zusätzlich gekauft werden?**
Nein. `cjw_newsletter`, der Informationssammler und Exponential Layouts werden über `composer.json` mit dem Produkt
installiert.

## Spenden

**Gibt es ein Spendenmodul?**
Nein. Eine Suche in Code und Dokumentation findet keinen Spenden-Datentyp, kein Feld „Betrag nach Wahl“ und keine
Funktion für Zuwendungsbestätigungen ([Spenden- und Fundraising-Seiten](../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md)).

**Wie nehmen wir dann Spenden an?**
Entweder verlinken Sie auf eine externe Spendenplattform (so machen es die meisten kleinen Non-Profit-Organisationen),
oder Sie verkaufen feste Spendenartikel über den eingebauten Shop mit dem Zahlungs-Gateway `ezpaypal` oder
`ezauthorize`. Beide Wege werden in [Spendenwege im Überblick](spenden/spendenwege-im-ueberblick.md) verglichen.

**Können Spender einen monatlichen Betrag geben?**
Wiederkehrende Zahlungen sind für den Shop und seine Gateways nicht dokumentiert. Nutzen Sie die regelmäßigen Spenden
einer externen Plattform.

**Kann Exponential Zuwendungsbestätigungen ausstellen?**
Nein. Die Bestellbelege des Shops sind Belege für eine Bestellung, keine Steuer- oder Spendenbescheinigungen
([Bestellbelege](../../funktionen/6.0/bestellbelege.md)). Fragen Sie Ihren Steuerberater, was in Ihrem Land vorgeschrieben ist.

## Fördermittel

**Kann Exponential unsere Förderanträge verwalten?**
Nein, ein Modul zur Fördermittelverwaltung gibt es nicht. Die Website hilft bei dem, was Geldgeber hinterher verlangen:
Nachweise der Ergebnisse (Seiten, Veröffentlichungen, Veranstaltungen), Nutzungszahlen und eine Aufzeichnung, wer was
getan hat. Siehe [Fördermittel](foerdermittel/README.md).

**Können wir die Website selbst in ein Förderbudget aufnehmen?**
Viele Geldgeber erlauben das; es hängt vom jeweiligen Geldgeber ab. [Budgetposten für Hosting und Wartung](foerdermittel/budgetposten-fuer-hosting-und-wartung.md)
listet die Posten auf, die Sie kalkulieren sollten.

## Ehrenamtliche

**Können sich Ehrenamtliche auf der Website anmelden?**
Ja, mit einem Formular auf Basis des Informationssammlers, geschützt durch ein Formular-Token und ein CAPTCHA
([Anmeldeformular für Ehrenamtliche, Schritt für Schritt](ehrenamtliche/anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md)).

**Können Ehrenamtliche die Website bearbeiten, ohne sie beschädigen zu können?**
Ja: ein Konto pro Person, eine auf einen Bereich beschränkte Rolle, auf Wunsch eine Freigabe vor dem Veröffentlichen,
Versionen zum Rückgängigmachen und das Audit-Protokoll ([Ehrenamtliche als Redakteure](ehrenamtliche/ehrenamtliche-als-redakteure.md)).

**Was geschieht, wenn eine Ehrenamtliche oder ein Ehrenamtlicher geht?**
Sie deaktivieren das Konto und verschieben es aus der Redakteursgruppe; die Inhalte bleiben erhalten
([Wenn Ehrenamtliche gehen](betrieb/wenn-ehrenamtliche-gehen.md)).

## Barrierefreiheit und Transparenz

**Ist Exponential barrierefrei?**
Die Software gibt Ihnen die Mittel dazu (Hinweise für Templates, verpflichtende Alternativtexte, ein barrierefreies
Administrationsdesign); ob Ihre öffentliche Website barrierefrei ist, hängt von ihrem Design und ihren Inhalten ab
([Was Exponential für die Barrierefreiheit bietet](../verwaltung/barrierefreiheit/was-exponential-bietet.md)).

**Können wir unseren Jahresbericht und unsere Finanzberichte veröffentlichen?**
Ja, als Dateidownloads in einer nach Datum geordneten Struktur ([Jahresveröffentlichungen als Downloads](transparenz/jahresveroeffentlichungen-als-downloads.md)).

**Können wir zeigen, was sich auf einer Seite geändert hat und wer es geändert hat?**
Jedes Objekt behält seine Versionen (standardmäßig 45), und das Audit-Protokoll zeichnet Änderungen auf
([Versionen und das Audit-Protokoll](transparenz/versionen-und-das-audit-protokoll.md)).

## Newsletter, Veranstaltungen und Sprachen

**Gibt es einen Newsletter?**
Ja, `cjw_newsletter`, mit Double-Opt-in, Versand per Cronjob und Bounce-Verarbeitung
([Unterstützer-Newsletter](kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md)).

**Gibt es einen Veranstaltungskalender?**
Ja. `ezwebin` liefert die Klassen `event`, `event_calendar` und `multicalendar` mit. Es gibt keinen Ticketverkauf,
keine Platzbegrenzung und keinen iCal-Export ([Veranstaltungen und Kalender](kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md)).

**Wie viele Sprachen kann eine Website haben?**
Bis zu 62 Inhaltssprachen pro Installation unter 64-Bit-PHP
([Die Grenze von 62 Sprachen](../verwaltung/sprachen/die-grenze-von-62-sprachen.md)).

## Hosting und Betrieb der Website

**Welches ist das kleinste Hosting, das funktioniert?**
Shared Hosting mit Apache oder LiteSpeed kann eine kleine Website mit SQLite betreiben; ein kleiner virtueller Server
gibt Ihnen mehr Kontrolle ([Hosting mit kleinem Budget](betrieb/hosting-mit-kleinem-budget.md)).

**Brauchen wir einen Datenbankserver?**
Nicht mit SQLite, der Voreinstellung ([SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md)). Wenn viele
Redakteure gleichzeitig veröffentlichen, wählen Sie stattdessen einen Datenbankserver.

**Was ist Velocity?**
Der mit Exponential gebündelte Webserver, der „needs nothing installed beyond PHP“ (`README.md`), also außer PHP nichts
Installiertes benötigt. Auf Shared Hosting läuft er meist nicht
([SQLite und Velocity für kleine Teams](betrieb/sqlite-und-velocity-fuer-kleine-teams.md)).

## Datenschutz

**Wo werden die Daten von Spendern und Ehrenamtlichen gespeichert?**
In den Formulareinsendungen, Benutzerkonten, Bestellungen und Newsletter-Abonnements Ihrer Installation sowie in Ihren
Exporten und Sicherungen ([Personenbezogene Daten in Exponential](../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)).

**Wer kann Bewerbungen von Ehrenamtlichen lesen?**
Nur Personen mit der Berechtigung `infocollector/read`. Geben Sie sie den Koordinatoren, nicht jedem Redakteur
([Datenschutz für Spender und Ehrenamtliche](betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md)).

## Partner und Hilfe

**Gibt es besondere Partnerpreise für Non-Profit-Organisationen?**
Nein. Das Partnerprogramm veröffentlicht eine einzige Reihe von Plänen und Preisen und nennt keinen Rabatt für
Non-Profit-Organisationen ([Partnerpläne und Preise](../partner/partnertarife-und-preise.md)). Fragen Sie einen Partner
nach seinen eigenen Konditionen.

**Wo bekommen wir Hilfe?**
Bei einem Partner, bei Ihrem Hosting-Anbieter oder über die öffentlichen Kanäle des Projekts
([Mit einem Partner arbeiten](betrieb/mit-einem-partner-arbeiten.md), [Probleme melden](../kunden/gesund-halten/probleme-melden.md)).

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](non-profit-organisationen-hier-beginnen.md)
- [Glossar für Non-Profit-Organisationen](glossar-fuer-non-profit-organisationen.md)
- [FAQ für Unternehmen](../unternehmen/faq-fuer-unternehmen.md)
- [FAQ für Kunden](../kunden/faq-fuer-kunden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
