---
title: Sicherungen, Updates und Sicherheit
audience: local-business, businesses, customer
category: local-business
order: 83
summary: Was Inhaber eines lokalen Unternehmens über die Sicherheit einer Exponential-Website wissen müssen, ohne technisch zu sein - Sicherungen und ein jährlicher Wiederherstellungstest, Exponential aktuell halten, HTTPS, Konten und Zwei-Faktor-Anmeldung, Rollen für Mitarbeitende, was zu tun ist, wenn Mitarbeitende kommen und gehen, und wenn etwas schiefgeht.
keywords: Sicherungen, Backups, Wiederherstellung, Updates, Sicherheit, HTTPS, Konten, Passwörter, Zwei-Faktor-Anmeldung, Rollen, Mitarbeitende, Ausscheiden, Vorfall, lokales Unternehmen, backups, restore, updates, security, accounts, passwords, two-factor, roles, staff, offboarding, incident, local business
type: help
---
# Sicherungen, Updates und Sicherheit

Diese Seite ist für Inhaber eines lokalen Unternehmens, die nicht technisch sind, aber für die Website verantwortlich
sind. Sie müssen diese Aufgaben nicht selbst erledigen; Sie müssen wissen, dass sie erledigt werden, und sich das
nachweisen lassen.

## Inhalt

1. [Sicherungen](#sicherungen)
2. [Updates](#updates)
3. [HTTPS](#https)
4. [Konten, wenn Mitarbeitende kommen und gehen](#konten-wenn-mitarbeitende-kommen-und-gehen)
5. [Zwei-Faktor-Anmeldung](#zwei-faktor-anmeldung)
6. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
7. [Fragen an Ihren Hoster oder Partner](#fragen-an-ihren-hoster-oder-partner)

## Sicherungen

Eine Sicherung ist nur nützlich, wenn sie sich wiederherstellen lässt. Eine Sicherung einer Exponential-Website umfasst
die **Datenbank** und die **Dateien** (hochgeladene Bilder und Dokumente im Speicherverzeichnis)
([Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).

- Fragen Sie, wie oft Sicherungen erstellt und wie lange sie aufbewahrt werden.
- Verlangen Sie einmal im Jahr einen **Wiederherstellungstest**: die Website aus einer Sicherung auf einer Testadresse
  wiederhergestellt ([Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md)).
- Behalten Sie das Recht, eine Kopie einer Sicherung zu erhalten, zum Beispiel beim Partnerwechsel.

## Updates

Exponential veröffentlicht Korrekturen als neue Versionen; eine veröffentlichte Version ändert sich nie
([Die Support-Zusage](../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)). Erweiterungen haben ihre
eigenen Versionen. Jemand muss sie einspielen.

- Vereinbaren Sie, wer aktualisiert und wie oft ([Updates und Upgrades](../../ersteller/website-ersteller/updates-und-aktualisierungen.md)).
- Sehen Sie sich nach einem Update die Startseite, ein Formular und, wenn Sie verkaufen, den Warenkorb an.
- Sicherheitsupdates sollten nicht bis zur nächsten jährlichen Überprüfung warten
  ([Sicherheitsupdates und Offenlegung](../../unternehmen/sicherheit/sicherheitsupdates-und-offenlegung.md)).

## HTTPS

Jede Seite sollte mit `https://` und einem Schloss im Browser öffnen, und das Zertifikat sollte automatisch erneuert
werden. Wenn der Browser jemals warnt, dass die Website nicht sicher ist, rufen Sie noch am selben Tag Ihren Hoster an
([Live gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).

## Konten, wenn Mitarbeitende kommen und gehen

| Wann | Tun |
|---|---|
| Jemand fängt an | geben Sie ihr oder ihm ein eigenes Konto mit der nötigen Rolle, nie ein geteiltes Passwort ([Rollen für neue Mitarbeitende wählen](../../kunden/alltag/rollen-fuer-neue-mitarbeitende-waehlen.md)) |
| Die Person ändert nur Öffnungszeiten und Angebote | eine Redakteursrolle, nicht Administrator ([Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md)) |
| Die Person bearbeitet Shop-Bestellungen | nur Rechte für die Shop-Verwaltung |
| Jemand geht | deaktivieren Sie das Konto am selben Tag, und ändern Sie die Passwörter externer Dienste, die die Person kannte ([Ausscheidende Mitarbeitende](../../kunden/alltag/ausscheiden-von-mitarbeitenden.md)) |
| Immer | mindestens zwei Administratorkonten, damit das Unternehmen nie ausgesperrt ist |

Schreiben Sie die Liste der Konten (Exponential, Hoster, Domain, Zahlungsanbieter, Schlüssel für Karten und CAPTCHA,
Analyse, Plattformen) an einem sicheren Ort auf.

## Zwei-Faktor-Anmeldung

Exponential unterstützt die Zwei-Faktor-Anmeldung über `sevenx_authentication_2fa`, das in der Standard-`composer.json`
steht ([sevenx_authentication_2fa](../../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md)). Bitten Sie Ihren
Administrator, sie mindestens für Administratoren und alle, die Bestellungen bearbeiten, vorzuschreiben
([Konten, Passwörter und Zwei-Faktor-Anmeldung](../../kunden/alltag/konten-passwoerter-und-zweistufige-anmeldung.md)).

## Wenn etwas schiefgeht

| Symptom | Erster Schritt |
|---|---|
| Die Website öffnet sich nicht | auf einem anderen Gerät und in einem anderen Netz prüfen, dann den Hoster anrufen |
| Eine Seite zeigt einen Fehler | Adresse, Uhrzeit und die angezeigte Fehlerreferenz notieren und an den Partner schicken ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)) |
| Formulare schicken keine E-Mails mehr | einmal testen, dann dem Hoster (E-Mail) oder dem Partner Bescheid geben |
| Seltsame Inhalte sind erschienen, oder Sie vermuten einen Einbruch | sofort den Partner anrufen; keine Spuren löschen ([Reaktion auf Vorfälle für kleine Teams](../../unternehmen/zuverlaessigkeit/stoerungsbehandlung-fuer-kleine-teams.md)) |
| Sie sind ausgesperrt | den zweiten Administrator verwenden oder den Partner fragen |

Halten Sie die Kontakte bereit: [Vorlage für Eskalationskontakte](../../kunden/partner-und-hoster/vorlage-eskalationskontakte.md).

## Fragen an Ihren Hoster oder Partner

1. Wann war die letzte Sicherung, und wann wurde zuletzt eine Wiederherstellung getestet?
2. Welche Exponential-Version läuft, und wann wurde sie zuletzt aktualisiert?
3. Wer erhält Sicherheitshinweise?
4. Ist die Zwei-Faktor-Anmeldung für Administratoren vorgeschrieben?
5. Wen rufe ich am Wochenende an, und wie schnell wird geantwortet?

## Verwandte Seiten

- [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Benutzerkonten und Passwörter](../../unternehmen/sicherheit/benutzerkonten-und-passwoerter.md)
- [Wöchentliche und monatliche Routine](woechentliche-und-monatliche-routine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
