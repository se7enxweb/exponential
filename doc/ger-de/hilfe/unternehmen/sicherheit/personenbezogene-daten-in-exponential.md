---
title: Personenbezogene Daten in Exponential
audience: businesses, admin, developer
category: business-solutions
order: 30
summary: Wo eine Exponential-Installation personenbezogene Daten speichert - Benutzerkonten, Sessions, Bestellungen, Formulareingaben, E-Mail-Einstellungen und Einwilligung, Newsletter-Abonnenten, Benachrichtigungen, Suchbegriffe, Logs, der Audit-Trail, Exporte und Backups - jeweils mit dem Datenbankschema und der Dokumentation abgeglichen, mit dem, wer sie sehen darf und wie man sie entfernt. Keine Rechtsberatung.
keywords: personenbezogene Daten, Dateninventar, Datenlandkarte, Benutzerkonten, Bestellungen, Informationssammler, Newsletter, Einwilligungsprotokoll, Sessions, Logs, Audit-Trail, Backups, DSGVO, personal data, data inventory, data map, user accounts, ezuser, orders, ezorder, information collector, ezinfocollection, newsletter, cjw_newsletter, cjwnl_user, consent log, expmail, sessions, logs, audit trail, backups, GDPR
type: help
---
# Personenbezogene Daten in Exponential

Diese Seite richtet sich an Verantwortliche von Websites, Datenschutzbeauftragte und Administratoren, die eine Übersicht
brauchen, wo in einer Exponential-Installation personenbezogene Daten aufbewahrt werden. Daten, die Sie nicht gefunden
haben, können Sie weder schützen noch exportieren noch löschen. Jede Zeile unten wurde mit dem Datenbankschema
(`kernel/sql/mysql/kernel_schema.sql`, dem Schema von `cjw_newsletter`) und der genannten Dokumentation abgeglichen.

> **Warnung**
> Dies ist eine technische Übersicht, keine Rechtsberatung. Ihre eigene Website kann mehr speichern: Eigene
> Inhaltsklassen, Erweiterungen und Templates können überall personenbezogene Daten hinzufügen. Prüfen Sie sie mit
> Ihrem Entwickler und bringen Sie rechtliche Fragen zu einem Anwalt oder Ihrem Datenschutzbeauftragten.

## Inhalt

1. [Die Übersicht auf einen Blick](#die-übersicht-auf-einen-blick)
2. [Konten und Anmeldung](#konten-und-anmeldung)
3. [Shop: Warenkörbe und Bestellungen](#shop-warenkörbe-und-bestellungen)
4. [Formulare: der Informationssammler](#formulare-der-informationssammler)
5. [E-Mail-Einstellungen und Newsletter](#e-mail-einstellungen-und-newsletter)
6. [Logs und der Audit-Trail](#logs-und-der-audit-trail)
7. [Alles andere, was zu prüfen ist](#alles-andere-was-zu-prüfen-ist)
8. [Ihr eigenes Dateninventar aufbauen](#ihr-eigenes-dateninventar-aufbauen)

## Die Übersicht auf einen Blick

| Daten | Wo | Typischer Inhalt | Lesen |
|---|---|---|---|
| Benutzerkonten | Tabelle `ezuser` und das Inhaltsobjekt des Benutzers | Benutzername, E-Mail-Adresse, Passwort-Hash; Name und andere Felder der Benutzerklasse | [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md) |
| Anmeldeaktivität | `ezuservisit` | letzter Besuch, Zahl der Anmeldungen, fehlgeschlagene Versuche | [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md) |
| Sessions | `ezsession` (Datenbank-Handler) oder die Session-Dateien von PHP | Benutzer-ID, Session-Daten | [Sessions](../../../anleitungen/sitzungen.md) |
| Bestellungen | `ezorder` | E-Mail-Adresse, Benutzer-ID und ein Kontodatensatz mit Name, Adresse, Telefon und Kommentar | [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md) |
| Formulareingaben | `ezinfocollection`, `ezinfocollection_attribute` | was Besucher eingegeben haben, oft Namen und E-Mail-Adressen | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| E-Mail-Einstellungen und Einwilligung | `expmail_preference`, `expmail_consent_log`, `expmail_pending` | Entscheidungen pro Kategorie; Einwilligungsverlauf mit Zeit, Quelle, angezeigtem Text, IP-Adresse, handelndem Benutzer | [E-Mail-Einstellungen](../../../anleitungen/mail-einstellungen-administrator.md) |
| Sperrliste | `expmail_suppression` | ein gesalzener Hash jeder Adresse, nie die Adresse | dieselbe |
| Newsletter-Abonnenten | `cjwnl_user`, `cjwnl_subscription`, `cjwnl_blacklist_item` | E-Mail, Anrede, Vor- und Nachname, Organisation, Geburtstag, Telefonnummer, Bounce-Status | [cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) |
| Abonnements von Benachrichtigungen | `ezsubtree_notification_rule` | welcher Benutzer welchen Inhalten folgt | [Benachrichtigungen](../../../anleitungen/benachrichtigungen-administrator.md) |
| Suchbegriffe | `ezsearch_search_phrase` | Begriffe, die Besucher eingegeben haben und die personenbezogene Daten enthalten können | [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#datenschutz-und-privatsphäre-zu-beantwortende-fragen) |
| Anfrage- und Sicherheits-Logs | `var/<site>/log/` | maskierte Adressen, gekürzte User-Agents | [Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md) |
| Audit-Trail | `var/<site>/log/audit/` und die Index-Tabellen `expaudit_*` | wer was getan hat; Adressen gekürzt, Sessions gehasht | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| Exporte | Dateien, die Sie herunterladen, und Dateien in `var/tmp/` | CSV- und JSON-Exporte von Formularen, Mail-Daten, Audit-Datensätzen | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md) |
| Backups | wo auch immer Ihr Hoster sie aufbewahrt | alles oben Genannte | [Backups: was zu sichern ist](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |

## Konten und Anmeldung

- **Das Konto** ist eine Zeile in `ezuser` (Benutzername, E-Mail-Adresse, Passwort-Hash), verbunden mit einem
  Inhaltsobjekt der Benutzerklasse, das den Namen und alle anderen Felder enthält, die Ihre Klasse definiert. Passwörter
  werden als bcrypt-Hashes gespeichert
  ([Sicherheitshärtung](../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung)).
- **Aktivität**: `ezuservisit` hält den letzten Besuch, die Zahl der Anmeldungen und fehlgeschlagene Versuche fest;
  **Benutzer > Benutzerkonten prüfen** liest sie aus ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
- **Schlüssel**: Aktivierungsschlüssel und Schlüssel zum Zurücksetzen des Passworts haben eigene Tabellen
  (`ezuser_accountkey`, `ezforgot_password`).
- **Zweistufige Anmeldung**: Das Geheimnis des Authenticators wird beim Benutzer gespeichert; ein `SecretKey`
  verschlüsselt es in der Datenbank ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- **Entfernen**: Ein Benutzerobjekt kommt nie in den Papierkorb; es zu entfernen ist endgültig. Das Entfernen eines
  Kontos löscht auch seine E-Mail-Einstellungen ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md),
  [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#anfragen-betroffener-personen)).

## Shop: Warenkörbe und Bestellungen

- `ezorder` hält die E-Mail-Adresse und die Benutzer-ID des Kunden fest und in seinem Kontodatensatz Vor- und Nachname,
  E-Mail-Adresse, Straße, Postleitzahl, Ort, Bundesland, Land, Telefon und Kommentar, die an der Kasse eingegeben wurden
  (`kernel/private/classes/views/shop/userregister.php`).
- `ezbasket` verbindet einen Warenkorb mit einer Session; `ezwishlist` mit einem Benutzer.
- Bestellungen lassen sich in der Administration archivieren und entfernen (`shop/archiveorder`, `shop/removeorder`,
  [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md)). Ihre
  Buchführungspflichten können verlangen, Bestellungen eine Zeit lang aufzubewahren; entscheiden Sie mit Ihrem
  Steuerberater.
- Kartendaten: Der Audit-Trail zeichnet Zahlungen auf, „nie Kartendaten“ ([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md));
  siehe [Kartenzahlungen und PCI-Grundlagen](../shops/kartenzahlungen-und-pci-grundlagen.md).

## Formulare: der Informationssammler

Jedes Mal, wenn ein Besucher ein Formular absendet, wird eine Sammlung mit einem Wert pro Sammelfeld gespeichert.
**Setup > Gesammelte Informationen** listet Sammlungen auf, exportiert sie (CSV) und entfernt sie; dafür braucht man die
Richtlinie `infocollector/read`. Jeder Export wird mit der Zahl der Zeilen im Audit-Trail festgehalten, nie mit den
Werten ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)). Eine Mail an einen Empfänger legt dieselben
Daten zusätzlich in einem Posteingang ab
([Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).

## E-Mail-Einstellungen und Newsletter

- Die Mail-Schranke des Kernels hält die Entscheidungen jeder Person, ein Einwilligungsprotokoll und eine Sperrliste
  fest, die als gesalzene Hashes gespeichert ist ([Einwilligung und Formulare](einwilligung-und-formulare.md)).
- Die Newsletter-Erweiterung `cjw_newsletter` führt eigene Datensätze der Abonnenten (`cjwnl_user`) mit den oben
  aufgeführten Feldern, Abonnements pro Liste und eine Blacklist mit Adresse und Hash. Das Tracking von Öffnungen und
  Klicks ist standardmäßig aus (`[TrackingSettings] Tracking=disabled`)
  ([Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#was-bei-hoster-und-kunde-bleibt)).

## Logs und der Audit-Trail

- `404.log` und `security.log` maskieren Client-Adressen standardmäßig (`MaskIP=enabled`); der Log-Viewer maskiert
  E-Mail-Adressen, IP-Adressen und Geheimnisse in dem, was er anzeigt
  ([Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#datenschutz-als-standard-in-den-logs)).
- Das Zugriffslog von Velocity steht im kombinierten Format von Apache und „nennt Besucher“; seine Dateien sind nur für
  Eigentümer und Gruppe lesbar (dieselbe Quelle).
- Der Audit-Trail kürzt Adressen, hasht Sessions, kürzt User-Agents und zeichnet nie Passwörter, Tokens oder Geheimnisse
  auf ([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).
- Logs von Webserver, Mail-Server und Panel außerhalb von Exponential folgen den Einstellungen Ihres Hosters.

## Alles andere, was zu prüfen ist

- [ ] Eigene Inhaltsklassen mit personenbezogenen Feldern (Mitglieder, Mitarbeiterprofile, Kommentare, Anmeldungen zu
      Veranstaltungen).
- [ ] Urheberschaft von Inhalten: Jedes Objekt und jede Version hält seinen Ersteller und Eigentümer fest.
- [ ] Private Nachrichten, wenn `ezpm` aktiv ist (es fügt Posteingänge und Postausgänge hinzu; Beschreibung in
      `composer.json`).
- [ ] Exponential Intelligence, wenn eingeschaltet: welcher Anbieter welchen Text erhält
      ([expai](../../../../eng-us/features/6.0/extensions/expai.md)).
- [ ] Dienste Dritter, die Templates einbetten: Schriften, Karten, Webanalyse, Videos.
- [ ] Exportierte Dateien auf Computern der Mitarbeiter.
- [ ] Backups und ihre Aufbewahrung.

## Ihr eigenes Dateninventar aufbauen

Kopieren Sie diese Tabelle in Ihre Unterlagen und füllen Sie sie Zeile für Zeile aus:

```text
DATA INVENTORY
Data | Purpose | Legal basis (ask your adviser) | Where stored | Who may see it | Retention | How deleted
-----|---------|--------------------------------|--------------|----------------|-----------|------------
User accounts      |  |  | ezuser + user objects |  |  |
Orders             |  |  | ezorder               |  |  |
Form submissions   |  |  | ezinfocollection      |  |  |
Mail consent       |  |  | expmail_*             |  |  |
Newsletter         |  |  | cjwnl_*               |  |  |
Logs               |  |  | var/<site>/log        |  |  |
Audit trail        |  |  | var/<site>/log/audit  |  |  |
Backups            |  |  | (host)                |  |  |
```

## Verwandte Seiten

- [DSGVO-Grundlagen](dsgvo-grundlagen.md)
- [Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md)
- [Formulareingaben](../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
