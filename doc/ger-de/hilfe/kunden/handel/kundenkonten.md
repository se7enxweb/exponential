---
title: Kundenkonten
audience: customer, admin, businesses
category: customer-success
order: 170
summary: Die Kunden eines Exponential-Shops betreuen - Kauf als Gast und mit Konto, wie konfiguriert, die Kundenliste, Fragen von Kunden zu ihrem Konto, alte Konten aufräumen und wer im Team Kundendaten sehen darf.
keywords: Kundenkonten, Gastbestellung, Kundenliste, Shop-Account-Handler, shopaccount.ini, Registrierung, Passwort zurücksetzen, Benutzerkonten, Kundendaten, Kontenpflege, customer accounts, guest checkout, customer list, shop account handler, password reset, user accounts, customer data
type: help
---
# Kundenkonten

Diese Seite ist für die Shop-Leitung und den Eigentümer. Sie behandelt den laufenden Shop: wissen, wie Ihre Kasse Gäste
und Kontoinhaber behandelt, Fragen zu Konten beantworten und die Kundendaten in Ordnung halten. Die Wahl zwischen dem
Kauf als Gast und dem Kauf mit Konto ist unter
[Kundenkonten und Gastbestellung für Unternehmen](../../unternehmen/shops/kundenkonten-und-gastbestellung.md) erklärt.

## Kennen Sie Ihre Konfiguration

Wie der Shop Kundendaten erfasst, entscheidet der Shop-Account-Handler, `settings/shopaccount.ini
[AccountSettings] Handler`, ausgeliefert als `ezuser` (dieselbe Seite):

| Handler | Was Ihre Kunden erleben |
|---|---|
| `ezuser` (ausgeliefert) | Alle füllen an der Kasse ein Formular aus; Gäste kaufen ohne Konto; angemeldete Kunden finden ihre Daten vorausgefüllt |
| `ezdefault` | Kunden müssen sich anmelden; Besucher werden zur Anmeldeseite und zurück zum Warenkorb geschickt |
| `ezsimple` | Kunden werden für ihre Daten zu `shop/register` geschickt |
| `bcusauser` | Das `ezuser`-Formular mit einem US-Adressmodell |

Fragen Sie Ihren Partner, welcher Handler in Ihrem Shop verwendet wird, und schreiben Sie es in Ihre Shop-Notizen. Der
Abschnitt „So funktioniert Ihr Shop“ des Shop-Dashboards nennt ihn ebenfalls ([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)).

## Die Kundenliste

**Shop > Kunden** (`shop/customerlist`) zeigt, wer gekauft hat, und die Bestellansicht eines Kunden
(`shop/customerorderview`) listet die Bestellungen eines Kunden auf, gefunden über Kunde und E-Mail-Adresse
(`kernel/shop/module.php`). Beide benötigen `shop/administrate`. Das Dashboard zählt neue, wiederkehrende und alle
bisherigen Kunden.

> **Warnung**
> Die Kundenliste und die Bestellansichten zeigen Namen, Adressen und Bestellhistorien. Geben Sie `shop/administrate`
> nur Personen, die es brauchen, jeder mit einem eigenen Konto ([Sicherheits-Checkliste für den Shop](../../../../eng-us/help/businesses/stores/store-security-checklist.md)).

## Fragen von Kunden zu ihrem Konto

| Frage | Wer antwortet | Wie |
|---|---|---|
| „Ich habe mein Passwort vergessen“ | Der Kunde selbst | Über die Passwort-Zurücksetzung der Website ([Konten und Anmeldung](../../konten-und-anmeldung.md)) |
| „Ich habe die Aktivierungs-E-Mail nie bekommen“ | Shop-Leitung oder Administrator | Das Konto unter **Benutzer** prüfen; siehe [Konten und Anmeldung](../../konten-und-anmeldung.md) |
| „Ändern Sie meine E-Mail-Adresse“ | Der Kunde in seinem Profil oder ein Administrator | Konto bearbeiten |
| „Schicken Sie mir keine Angebote mehr“ | Der Kunde, über den Link in der E-Mail oder die Einstellungsseite | [E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md) |
| „Löschen Sie mein Konto und meine Daten“ | Der Eigentümer entscheidet, ein Administrator handelt | [DSGVO-Anfragen](dsgvo-anfragen.md) |
| „Kann ich ohne Konto kaufen?“ | Shop-Leitung | Ja mit `ezuser`; nicht mit `ezdefault` |

## Konten aufräumen

**Benutzer > Benutzerkonten prüfen** (`user/hygiene`) listet Konten, die niemand nutzt, Konten, die deaktiviert oder
gesperrt sind, Konten, die ihr Benutzerobjekt verloren haben, und Konten, die sich eine E-Mail-Adresse teilen
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

- [ ] Vierteljährlich: Sehen Sie die Liste mit dem Eigentümer durch und entscheiden Sie, was mit lange ungenutzten
  Kundenkonten geschieht, im Einklang mit Ihren Aufbewahrungsregeln ([Datenexport und Löschung](datenexport-und-loeschung.md)).
- [ ] Konten von Mitarbeitenden, die gegangen sind, werden noch am selben Tag deaktiviert.
- [ ] Das Entfernen eines Benutzers ist endgültig: Benutzerobjekte kommen nicht in den Papierkorb (dieselbe Seite).
  Prüfen Sie vorher offene Bestellungen.

## Was Konten Ihren Kunden bieten

Aus [Kundenkonten und Gastbestellung für Unternehmen](../../unternehmen/shops/kundenkonten-und-gastbestellung.md):
Angemeldete Kunden bekommen ihre Adresse vorausgefüllt, einen Wunschzettel ([Wunschzettel und Warenkörbe](wunschzettel-und-warenkoerbe.md)),
Ermäßigungsgruppen ([Rabatte](rabatte.md)) und ihre Belege ohne den Link. Gäste bekommen nichts davon, können aber
ihren Beleg-Link behalten, wenn dauerhafte Belege eingeschaltet sind ([Rechnungen und Belege](rechnungen-und-belege.md)).

## Verwandte Seiten

- [Kundenkonten und Gastbestellung für Unternehmen](../../unternehmen/shops/kundenkonten-und-gastbestellung.md)
- [Konten und Anmeldung](../../konten-und-anmeldung.md)
- [DSGVO-Anfragen](dsgvo-anfragen.md)
- [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
