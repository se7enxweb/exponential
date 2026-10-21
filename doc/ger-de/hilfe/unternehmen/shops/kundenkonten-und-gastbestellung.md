---
title: Kundenkonten und Gastbestellung
audience: businesses, admin, website-creator
category: business-solutions
order: 90
summary: Ob Kunden in einem Exponential-Shop als Gast oder mit einem Konto kaufen - die mitgelieferten Shop-Account-Handler, was jeder vom Kunden verlangt, was gespeichert wird und wie Sie wählen.
keywords: Gastbestellung, Kundenkonto, Registrierung, Anmeldung, Shop-Account-Handler, shopaccount.ini, ezuser, ezdefault, Kundendaten, Stammkunden, personenbezogene Daten, guest checkout, customer account, registration, sign in, shop account handler, customer data, returning customers, personal data
type: help
---
# Kundenkonten und Gastbestellung

Diese Seite richtet sich an Geschäftsinhaber, die entscheiden, ob Kunden für einen Kauf ein Konto anlegen müssen. Sie
erklärt die Möglichkeiten, die der Shop mitbringt, was jede davon vom Kunden verlangt, was mit der Bestellung
gespeichert wird und wie sich die Wahl auf Belege, Wunschlisten und personenbezogene Daten auswirkt.

## Inhalt

1. [Warum es wichtig ist](#warum-es-wichtig-ist)
2. [Die Shop-Account-Handler](#die-shop-account-handler)
3. [Gastbestellung mit dem mitgelieferten Handler](#gastbestellung-mit-dem-mitgelieferten-handler)
4. [Bestellung nur mit Konto](#bestellung-nur-mit-konto)
5. [Was Konten Ihren Kunden bieten](#was-konten-ihren-kunden-bieten)
6. [Kundendaten und Datenschutz](#kundendaten-und-datenschutz)
7. [Die Wahl](#die-wahl)

## Warum es wichtig ist

Ein Pflichtkonto fügt vor dem ersten Kauf einen Schritt hinzu; ganz ohne Konten tippen Stammkunden ihre Anschrift jedes
Mal neu ein. Exponential lässt Ihnen die Wahl, und die Wahl ist eine einzige Einstellung.

## Die Shop-Account-Handler

Wie der Shop Kundendaten erfasst, entscheidet der **Shop-Account-Handler**, festgelegt in `settings/shopaccount.ini`,
`[AccountSettings] Handler` ([Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md#checkout)).
Der Kernel liefert diese in `kernel/classes/shopaccounthandlers/` mit:

| Handler | Was der Kunde tut | Passt zu |
|---|---|---|
| `ezuser` (die ausgelieferte Einstellung) | Füllt an der Kasse ein Formular mit Name, E-Mail-Adresse und Anschrift aus (`shop/userregister`), mit oder ohne Konto | Den meisten Shops: Gäste und Kontoinhaber sind gleichermaßen willkommen |
| `ezdefault` | Muss sich anmelden; ein Besucher wird zur Anmeldeseite und danach zurück zum Warenkorb geschickt | Shops nur für registrierte Kunden, etwa im Großhandel |
| `ezsimple` | Wird für die Kundendaten zu `shop/register` geschickt | Websites mit eigenem Registrierungsformular |
| `bcusauser` | Das Formular von `ezuser` mit dem US-Adressmodell (Stadt und Telefon) | Shops mit Anschriften in den USA |

Entwickler können in einer Erweiterung einen eigenen Handler hinzufügen (`shopaccount.ini [HandlerSettings] ExtensionRepositories[]`).
Die Erweiterung `ezauthorize` zum Beispiel bringt eigene Templates für einen Account-Handler mit.

## Gastbestellung mit dem mitgelieferten Handler

Mit `ezuser` sieht jeder Kunde an der Kasse ein Formular. Die Felder, die seine Ansicht liest, sind Vorname, Nachname,
E-Mail-Adresse, zwei Adresszeilen, Postleitzahl, Stadt, Bundesland, Land, ein Kommentar und eine Telefonnummer, wenn
das Formular des Designs eine anbietet (`kernel/private/classes/views/shop/userregister.php`).

- Ein **Gast** füllt das Formular aus und kauft. Es wird kein Konto angelegt.
- Bei einem **angemeldeten Kunden** sind Name und E-Mail-Adresse aus seinem Konto vorausgefüllt, und die Anschrift
  seiner letzten Bestellung wird übernommen (dieselbe Datei).

Die Daten werden mit der Bestellung gespeichert, sodass die Shop-Mitarbeiter sie in der Bestellansicht sehen, ganz
gleich, welche Art von Kunde es war.

> **Tipp**
> Gäste sollten in ihrer Bestätigungsmail den Link zum dauerhaften Beleg erhalten. Ohne ihn funktioniert die
> Bestellansicht eines Gastes nicht mehr, sobald seine Browsersitzung endet
> ([Bestellbelege und Bestätigungsmails](bestellbelege-und-bestaetigungsmails.md)).

## Bestellung nur mit Konto

Mit `ezdefault` prüft der Shop, ob der Kunde ein registrierter Benutzer ist, und schickt ihn andernfalls zu
`user/login` und danach zurück zum Warenkorb, wo der Bestellvorgang automatisch weitergeht
(`kernel/classes/shopaccounthandlers/ezdefaultshopaccounthandler.php`). E-Mail-Adresse und Name des Kunden stammen dann
aus seinem Benutzerkonto.

Kundenkonten sind gewöhnliche Exponential-Benutzer. Registrierung, Aktivierung, Freigabe und Mitgliederbereiche sind
unter [Mitgliederregistrierung und Mitgliederbereiche](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
und, für den geschäftlichen Einsatz, unter [Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md)
beschrieben. Die Bestellung nur mit Konto passt gut zu einem Katalog für **Händler oder den Großhandel**, den nur
angemeldete Kunden sehen dürfen ([Händler- und Lieferantenportale](../extranets/haendler-und-lieferantenportale.md)).

## Was Konten Ihren Kunden bieten

| Funktion | Gast | Angemeldeter Kunde |
|---|---|---|
| Kaufen | ja (mit `ezuser`) | ja |
| Anschrift beim nächsten Mal ausgefüllt | nein | ja |
| Wunschliste | nicht sinnvoll: Die Wunschliste wird pro Benutzerkonto geführt | ja ([Rabatte und Wunschlisten](rabatte-und-wunschlisten.md)) |
| Rabattgruppen | nein | ja, Rabatte folgen der Gruppe des Benutzers |
| Beleg ohne den Link | nein | ja, der Kunde, der die Bestellung aufgegeben hat, kann sie angemeldet öffnen |
| Seite mit E-Mail-Einstellungen | über den Link in einer Mail | ja, im Profil ([Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)) |
| Zweistufige Anmeldung | entfällt | verfügbar mit der 2FA-Erweiterung ([sevenx_authentication_2fa](../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md)) |

## Kundendaten und Datenschutz

Ganz gleich, welcher Handler: Die Bestellung behält Name, E-Mail-Adresse und Anschrift des Kunden. Das sind
personenbezogene Daten. Die Funktion der Mail-Einstellungen behandelt Bestellmails als notwendige E-Mails, die immer
gesendet werden und nie Werbung enthalten ([Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)); Werbung an
Kunden braucht deren Opt-in ([Opt-in und Einwilligung](../../../../eng-us/help/businesses/newsletters/opt-in-and-consent.md)).

> **Hinweis**
> Dies sind Grundlagen, keine Rechtsberatung. Für Ihre Pflichten nach dem Datenschutzrecht lesen Sie die Verordnung,
> die für Sie gilt, und fragen Sie Ihren Anwalt oder Datenschutzbeauftragten. Beginnen Sie mit
> [DSGVO-Grundlagen](../../../../eng-us/help/businesses/security/gdpr-basics.md) und [Personenbezogene Daten in Exponential](../../../../eng-us/help/businesses/security/personal-data-in-exponential.md).

## Die Wahl

- [ ] Kaufen die meisten Kunden einmal (Gastbestellung) oder kommen sie oft wieder (Konten)?
- [ ] Gehören manche Produkte oder Preise nur zu registrierten Kunden (nur mit Konto oder eine geschützte Sektion)?
- [ ] Möchten Sie Rabattgruppen oder Wunschlisten (Konten)?
- [ ] Welche Adressfelder braucht Ihr Versanddienstleister, und fragt das Formular danach?
- [ ] Ist der dauerhafte Beleg für Gäste eingeschaltet?
- [ ] Wer darf die Kundenliste sehen (`shop/administrate`)?

## Verwandte Seiten

- [Warenkorb und Bestellablauf](warenkorb-und-bestellablauf.md)
- [Mitgliederregistrierung und Mitgliederbereiche](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Benutzerkonten und Passwörter](../../../../eng-us/help/businesses/security/user-accounts-and-passwords.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
