---
title: Compliance und DSGVO
audience: hosting, admin
category: hosting
order: 130
summary: Was Exponential bereitstellt, das einem Hoster und seinen Kunden hilft, Datenschutz- und E-Mail-Recht einzuhalten (DSGVO, ePrivacy, CAN-SPAM, CASL, CCPA) - Datenschutz als Standard in Logs und Prüfprotokoll, das System für Einwilligung und Unterdrückung bei Mails, Befehle für Auskunft und Löschung, Aufbewahrungseinstellungen und welche Entscheidungen bei Hoster und Kunde bleiben. Keine Rechtsberatung.
keywords: Compliance, DSGVO, Datenschutz, ePrivacy, CAN-SPAM, CASL, CCPA, Einwilligung, E-Mail-Einstellungen, Unterdrückungsliste, Löschung, Auskunftsrecht, Aufbewahrung, Prüfprotokoll, Pseudonymisierung, maskierte IP, Auftragsverarbeiter, Verantwortlicher, Datenschutzerklärung, keine Rechtsberatung, compliance, GDPR, data protection, consent, mail preferences, suppression list, erasure, right of access, retention, audit trail, pseudonymization, masked IP, processor, controller, privacy notice, not legal advice
type: help
---
# Compliance und DSGVO

Diese Seite ist für die Compliance-Verantwortlichen, Produktmanager und Systemadministratoren eines Hosters und für
Support-Mitarbeitende, die eine Datenschutzanfrage eines Kunden erhalten. Sie beschreibt, **was die Software bereitstellt**:
Datenschutz als Standard, Aufzeichnungen über Einwilligungen, Befehle für Auskunft und Löschung und Aufbewahrungseinstellungen, die im
Repository zu finden sind. Sie sagt nicht, ob eine Website ein Gesetz einhält.

> **Warnung**
> Diese Seite ist keine Rechtsberatung. Ob eine Website die Vorschriften einhält, hängt davon ab, was sie verarbeitet, für wen, und von
> Einstellungen und Dokumenten, die nur ihr Eigentümer bereitstellen kann. Der Hoster und jeder Kunde müssen ihren eigenen rechtlichen
> Rat einholen. Die eigene rechtliche Checkliste des Projekts sagt dasselbe: Fragen Sie zu Ihrem eigenen Fall einen Anwalt
> ([E-Mail-Einstellungen: die rechtliche Checkliste](../../../spezifikationen/6.0/mail-einstellungen-compliance.md)).

## Inhalt

1. [Rollen: wer was entscheidet](#rollen-wer-was-entscheidet)
2. [Datenschutz als Standard in den Logs](#datenschutz-als-standard-in-den-logs)
3. [Das Prüfprotokoll und personenbezogene Daten](#das-prüfprotokoll-und-personenbezogene-daten)
4. [Einwilligung, Abmeldung und Unterdrückung bei Mails](#einwilligung-abmeldung-und-unterdrückung-bei-mails)
5. [Anfragen betroffener Personen](#anfragen-betroffener-personen)
6. [Sicherheit der Verarbeitung](#sicherheit-der-verarbeitung)
7. [Was bei Hoster und Kunde bleibt](#was-bei-hoster-und-kunde-bleibt)
8. [Checkliste](#checkliste)

## Rollen: wer was entscheidet

In einer typischen Hosting-Vereinbarung entscheidet der Kunde, warum und wie personenbezogene Daten auf seiner Website verarbeitet
werden, und der Hoster betreibt die Infrastruktur im Auftrag des Kunden. Welche rechtliche Rolle jede Partei hat und welcher Vertrag
zwischen ihnen nötig ist, klären die Parteien und ihre Berater. Für den Hoster ist die praktische Folge: Geben Sie dem Kunden die Fakten
und die Werkzeuge unten, halten Sie Ihre eigenen betrieblichen Logs und Backups unter Kontrolle und unterstützen Sie Anfragen, wenn der
Kunde darum bittet.

## Datenschutz als Standard in den Logs

| Log | Standardschutz | Einstellung |
|---|---|---|
| `404.log`, `security.log` | Adresse des Clients maskiert (`203.0.x.x`, `2001:db8:x:x`); User Agent als kurzer Hash plus 60 Zeichen; Werte der Query `***` in 404.log, Geheimnisse, E-Mail- und IP-Adressen in security.log maskiert | `site.ini [RequestLogSettings] MaskIP=enabled` |
| `csp.log` | Adresse wie in den Anfrage-Logs maskiert; URIs maskiert; Ausschnitte auf 40 Zeichen gekürzt | `[ContentSecurityPolicy]` |
| der Zähler für „nicht gefunden“ | über einen gesalzenen Hash der Adresse geführt; die Adresse wird nie gespeichert | |
| Log-Viewer | maskiert E-Mail-Adressen, IP-Adressen und Geheimnisse in dem, was er zeigt | `logviewer.ini [RedactionSettings]` |
| `auth.log` | jeden Schritt der zweistufigen Anmeldung, nie einen Code oder ein Geheimnis | |
| Zugriffslog von Velocity | das Format combined von Apache, Dateien `0640` in einem Verzeichnis `0750`: ein Zugriffslog nennt Besucher | `velocity.ini [LogSettings]` |

`MaskIP=disabled` schreibt volle Adressen; [Anfrage-Logs](../../../../en/us/features/6.0/request-logs.md#privacy) sagt, das nur für eine Website
zu nutzen, die einen Grund und eine Rechtsgrundlage dafür hat. Die eigenen Zugriffslogs des Webservers (Apache, nginx, die des Panels)
liegen außerhalb von Exponential und folgen den Einstellungen des Hosters.

## Das Prüfprotokoll und personenbezogene Daten

Aus [4.12 Datenschutz- und DSGVO-Aufgaben](../../../../en/us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks) der Anleitung zum Audit, die ebenfalls
sagt, dass sie keine Rechtsberatung ist:

- **Datenminimierung als Standard**: Adressen auf /24 (IPv6 /48) gekürzt, Sitzungen gehasht, User Agents auf Familie von Browser und
  Betriebssystem reduziert, E-Mail-Adressen und Anmeldeversuche unbekannter Benutzer gehasht; nie Passwörter, Tokens, Schlüssel zum
  Zurücksetzen, Geheimnisse oder POST-Inhalte; Lesezugriffe aus.
- **Strenger**: pro Feld `full | truncate | hash | off` in `[AuditPrivacySettings] Field[]`, `BeforeAfter=keys`, kürzere Aufbewahrung;
  die [Konfiguration für strengen Datenschutz](../../../../en/us/bc/6.0/audit.md#64-strict-privacy).
- **Pseudonymisierung**: Der Index ersetzt personenbezogene Felder nach `PseudonymiseAfterDays` (90) durch ihre gehashte Form; die Dateien
  werden nicht umgeschrieben, weil das die Kette brechen würde.
- **Auskunftsersuchen**: `exp:audit export --subject-user=<id>` exportiert jeden Eintrag von oder über einen Benutzer; prüfen Sie ihn vor
  der Herausgabe, weil er auch die Handlungen anderer Personen an diesem Benutzer enthält.
- **Löschung**: Die Anleitung beschreibt, Audit-Einträge für ihre Aufbewahrungsfrist zu behalten und mit dieser Frist und der
  angewandten Pseudonymisierung zu antworten; einen zu löschen, würde die Kette brechen.

## Einwilligung, Abmeldung und Unterdrückung bei Mails

Seit 6.0.15 passiert jede Mail die Mail-Sperre ([13.13](../../../installation/13-sicherheitshaertung.md#1313-mail-einwilligung-und-sperrliste)):

- **Notwendige** Kategorien (`security`, `orders`, `legal`, `admin`) gehen immer hinaus.
- **Optionale** Kategorien gehen nur an Empfänger, die sie eingeschaltet haben, aus, bis die Person sie einschaltet, mit einer Fußzeile,
  einem Link zum Abmelden mit einem Klick und Headern `List-Unsubscribe` (RFC 8058).
- **Einwilligungsprotokoll**: jede Änderung an den Einstellungen einer Person mit Zeit, Quelle, gezeigtem Text, IP-Adresse und handelndem
  Benutzer; Double-Opt-in für Newsletter und Marketing.
- **Unterdrückungsliste**: Hard Bounces, Beschwerden, „alles stoppen“ und rechtliche Anfragen; gespeichert wird nur ein gesalzener Hash
  jeder Adresse.

Die rechtliche Checkliste ordnet jede Anforderung von CAN-SPAM, DSGVO und ePrivacy, CCPA/CPRA und CASL dem Code und dem Test zu, der sie
beweist, und listet auf, was **der Website überlassen** bleibt
([Der Website überlassen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md#der-site-überlassen)):

1. die Postanschrift in der Fußzeile (`mailpreferences.ini [FooterSettings] OrganisationAddress`, nach einer Installation leer) und der
   Name des Absenders (`OrganisationName`);
2. eine Kategorie für jede kommerzielle Mail, die eigener Code versendet;
3. `[GateSettings] SplitRecipients=enabled` beibehalten;
4. DKIM-Signatur von `List-Unsubscribe` und `List-Unsubscribe-Post` durch den Mailserver;
5. ein Link zur Datenschutzerklärung im Registrierungsformular, wenn das Design einen möchte.

Punkt 4 ist oft Aufgabe des Hosters, weil der Hoster den Mailserver betreibt; siehe [Mailzustellung](../betrieb/mailzustellung.md) und
[Einwilligung und Bounces bei Mails](../betrieb/einwilligung-und-bounces-bei-mails.md). Prüfen Sie den Zustand einer Website mit:

```bash
php bin/php/console exp:mail:status
```

## Anfragen betroffener Personen

| Anfrage | Werkzeug |
|---|---|
| Auskunft über Mail-Daten | der eigene Download der Person oder `./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/request.json` (Modus `0600`) |
| Alle Mails stoppen | der Abmeldelink oder `exp:mail:suppression add --email=<address> --reason=legal` |
| Mail-Daten löschen | `./console exp:mail:preferences erase --email=<address> --yes`: Einstellungen und ausstehende Bestätigungen entfernt, Einwilligungsprotokoll anonymisiert, der Widerruf als Beleg behalten |
| Entfernen eines Kontos | das Entfernen eines Benutzerkontos löscht auch seine E-Mail-Einstellungen (`expMailPreferences::eraseForRemovedAccount()`, laut der rechtlichen Checkliste am 05.10.2026 geschlossen) |
| Audit-Einträge über einen Benutzer | `exp:audit export --subject-user=<id>` |

Löschen Sie exportierte Dateien, wenn die Anfrage, die sie beantworten, erledigt ist; sie enthalten Adressen. Geben Sie
`mailpreferences/export` und `audit/read` wenigen Personen.

## Sicherheit der Verarbeitung

Was die Software beiträgt, jeweils auf einer eigenen Seite beschrieben: bcrypt-Hashes von Passwörtern und Passwortregeln, zweistufige
Anmeldung, verschlüsselte Tokens zum Abmelden, Schutz von Sitzungen, das Prüfprotokoll mit Hash-Ketten, Sicherheitsheader und eine
Content Security Policy ([Härtung für Hoster](haertung-fuer-hoster.md)). Was der Hoster beiträgt: Abschottung zwischen Kunden,
verschlüsselte Backups, Patches, Zugriffskontrolle für Mitarbeitende und Logs, die an Systeme mit eigenen Zugriffsregeln weitergeleitet
werden ([Trennung von Mandanten](trennung-von-mandanten.md)).

## Was bei Hoster und Kunde bleibt

- Verträge zwischen Hoster und Kunde und ihre Rollen nach dem Gesetz.
- Die Datenschutzerklärung, das Verzeichnis der Verarbeitungstätigkeiten und die dort genannten Aufbewahrungswerte; die
  Aufbewahrungseinstellungen der Software müssen dann passend gesetzt werden ([Aufbewahrung von Logs und Daten](aufbewahrung-von-logs-und-daten.md)).
- Backups: die Aufbewahrung personenbezogener Daten in Backups und der Umgang mit Löschung bei Daten, die auch in einem Backup liegen.
- Logs von Webserver, Mailserver und Panel außerhalb von Exponential.
- Dienste Dritter, die eine Website einbindet (Karten, Analyse, Schriften), die die Content Security Policy sichtbar macht, aber nicht
  bewertet.
- Newsletter-Statistiken: Die Erweiterung cjw_newsletter zählt Öffnungen und Klicks nur, wenn die Website es einschaltet (standardmäßig
  `[TrackingSettings] Tracking=disabled`), und pro Person nur mit Einwilligung; die Checkliste nennt, was die Website in ihrer
  Datenschutzerklärung angeben muss.

## Checkliste

- [ ] Der Kunde weiß aus dieser Seite und den verlinkten Quellen, welche personenbezogenen Daten die Logs, das Prüfprotokoll und das
      Mail-System der Website behalten.
- [ ] `MaskIP` bleibt eingeschaltet, es sei denn, der Kunde hat einen dokumentierten Grund.
- [ ] Einstellungen zu Datenschutz und Aufbewahrung des Audits mit dem Kunden vereinbart; ein strengeres Profil angewandt, wo gewünscht.
- [ ] Fußzeile der Mails ausgefüllt; `exp:mail:status` ohne Befund; DKIM-Signatur auf dem Mailserver des Hosters.
- [ ] Ein dokumentierter Weg, Auskunfts- und Löschanfragen zu beantworten, einschließlich der anschließenden Löschung exportierter Dateien.
- [ ] Aufbewahrung von Backups für personenbezogene Daten entschieden und aufgeschrieben.
- [ ] Rechtliche Fragen an die eigenen Berater von Kunde und Hoster verwiesen.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Aufbewahrung von Logs und Daten](aufbewahrung-von-logs-und-daten.md)
- [E-Mail-Einstellungen: die rechtliche Checkliste](../../../spezifikationen/6.0/mail-einstellungen-compliance.md)
- [E-Mail-Einstellungen: die Anleitung für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)
- [Audit: Datenschutz- und DSGVO-Aufgaben](../../../../en/us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
