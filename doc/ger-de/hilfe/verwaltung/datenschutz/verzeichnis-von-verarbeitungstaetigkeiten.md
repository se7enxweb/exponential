---
title: Verzeichnis von Verarbeitungstätigkeiten für eine Website
audience: government, project-manager, admin
category: public-sector
order: 280
summary: Für öffentliche Stellen, die ein Verzeichnis von Verarbeitungstätigkeiten führen - Exponential führt es nicht für Sie; diese Seite nennt die typischen Verarbeitungstätigkeiten einer Exponential-Website mit den Fakten, die jeder Eintrag braucht (Daten, Empfänger, Aufbewahrung, Sicherheitsmaßnahmen), entnommen aus dem Produkt.
keywords: Verzeichnis von Verarbeitungstätigkeiten, VVT, DSGVO Artikel 30, Verarbeitungstätigkeiten, Website, Benutzerkonten, Formulare, Newsletter, Prüfprotokoll, Protokolle, Aufbewahrung, Empfänger, Sicherheitsmaßnahmen, Dokumentation, öffentlicher Sektor, record of processing activities, ROPA, GDPR
type: help
---
# Verzeichnis von Verarbeitungstätigkeiten für eine Website

Diese Seite richtet sich an Datenschutzbeauftragte und Projektleitungen. Das Verzeichnis von Verarbeitungstätigkeiten
(Art. 30 DSGVO) zu führen, ist Aufgabe des Betreibers; die Aussage des Projekts sagt das ausdrücklich
([Datenschutzstandards: Status](../../../../eng-us/standards/privacy.md#status)). Exponential kann Ihr Verzeichnis nicht
schreiben, aber es kann die Fakten dafür liefern. Unten finden Sie die Verarbeitungstätigkeiten einer typischen
öffentlichen Website mit den Fakten aus dem Produkt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Typische Tätigkeiten](#typische-tätigkeiten)
2. [Fakten pro Tätigkeit](#fakten-pro-tätigkeit)
3. [Anzuführende Sicherheitsmaßnahmen](#anzuführende-sicherheitsmaßnahmen)
4. [Verwandte Seiten](#verwandte-seiten)

## Typische Tätigkeiten

1. Veröffentlichen von Informationen (Inhalte können Beschäftigte oder Bürgerinnen und Bürger nennen).
2. Konten der Beschäftigten für die Redaktion (Authentifizierung, Rollen).
3. Öffentliche Benutzerkonten (Registrierung, Mitgliederbereiche), falls vorhanden.
4. Kontakt- und Antragsformulare.
5. Newsletter und Warnmeldungen per E-Mail.
6. Sicherheitsprotokollierung und Audit.
7. Betriebsprotokolle und Statistiken.
8. Optionale Dienste von Drittanbietern (Webanalyse, CAPTCHA, Karten, KI).

## Fakten pro Tätigkeit

| Tätigkeit | Daten | Wo (siehe die Bestandsaufnahme) | Standardaufbewahrung | Empfänger |
|---|---|---|---|---|
| Konten der Beschäftigten | Name, Anmeldename, E-Mail, Besuche, Rollen | Benutzerobjekte, `ezuservisit` | keine: Überprüfung mit „Benutzerkonten prüfen“ | Hosting-Auftragsverarbeiter |
| Formulare | was Besucher eingeben | Informationssammlungen; Empfängerpostfach | keine: manuelles Entfernen | Zuständige für die Formulare; E-Mail-Anbieter |
| E-Mail | Adresse, Entscheidungen, Einwilligungsverlauf mit IP-Adresse | E-Mail-Einstellungen, Einwilligungsprotokoll, Hashes der Sperrliste | Einwilligungsprotokoll 1095 Tage für Personen, die nicht mehr da sind | Mailserver; Newsletter-Erweiterung |
| Audit | Handelnde, gekürzte Adressen, Handlungen | Audit-Dateien und Index | 90 Tage live, 730 archiviert; nach 90 pseudonymisiert | SIEM, falls weitergeleitet |
| Anfrageprotokolle | maskierte Adressen, User-Agents, URLs | `404.log`, `security.log` | 30 und 90 Tage | Hoster |
| Suchstatistiken | Suchbegriffe | `ezsearch_search_phrase` (kein Benutzer, keine Adresse) | bis zum Zurücksetzen; standardmäßig aus | keine |
| Dienste von Drittanbietern | hängt vom Dienst ab | der Anbieter | die des Anbieters | der Anbieter |

Zweck und Rechtsgrundlage tragen Sie selbst ein; sie hängen von Ihren Aufgaben und Ihrem Recht ab.

## Anzuführende Sicherheitsmaßnahmen

Aus dem Sicherheitsbereich dieser Hilfe: Passwort-Hashing (bcrypt), optionale zweistufige Anmeldung, Rollen nach dem
Prinzip der geringsten Rechte, CSRF-Formular-Token, Flags des Sitzungscookies, Sicherheits-Header, Content Security
Policy, manipulationssicheres Prüfprotokoll mit Warnungen, maskierte Protokolle, HTTPS und HSTS, Datensicherungen. Nennen
Sie nur, was Sie tatsächlich konfiguriert haben.

## Verwandte Seiten

- [Wo personenbezogene Daten in Exponential liegen](bestandsaufnahme-personenbezogener-daten.md)
- [Aufbewahrung personenbezogener Daten im Überblick](aufbewahrung-im-ueberblick.md)
- [Die DSGVO und Exponential](dsgvo-zuordnung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
