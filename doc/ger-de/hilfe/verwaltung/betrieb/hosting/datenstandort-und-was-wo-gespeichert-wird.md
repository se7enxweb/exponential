---
title: Speicherort der Daten und was wo gespeichert wird
audience: government, hosting, admin, project-manager
category: public-sector
order: 100
summary: Für öffentliche Stellen, die wissen und dokumentieren müssen, wo die Daten einer Exponential-Website liegen. Jeder Ort, an dem die Software Inhalte, Dateien, Konten, Logs und Schlüssel speichert, welche davon personenbezogene Daten enthalten und was den Server nur verlässt, wenn Sie es einschalten.
keywords: Speicherort der Daten, Datenresidenz, wo werden Daten gespeichert, personenbezogene Daten, Verzeichnis von Verarbeitungstätigkeiten, Datenbank, var/storage, Logs, Audit-Trail, settings/override, Datensicherungen, Dritte, Souveränität, Inventar, data location, data residency, where is data stored, personal data, records of processing, database, audit trail, backups, third parties, sovereignty, inventory
type: help
---
# Speicherort der Daten und was wo gespeichert wird

Diese Seite richtet sich an Datenschutzbeauftragte, Architektinnen, Architekten und IT-Teams in öffentlichen Stellen,
die dokumentieren müssen, wo die Daten einer Website liegen, sei es für ein Verzeichnis von Verarbeitungstätigkeiten,
eine Sicherheitsbewertung oder eine Regel zur Datenresidenz. Sie führt jeden Ort auf, an dem Exponential Daten
speichert, sagt, welche Orte üblicherweise personenbezogene Daten enthalten und welche Funktionen Daten nur dann
anderswohin senden, wenn Sie sie einschalten. Wo der Server selbst steht, ist Ihre Hosting-Entscheidung; diese Seite
sagt Ihnen, was in das Inventar gehört. Die Fakten stammen aus [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md)
und den [Datenschutzstandards](../../../../../eng-us/standards/privacy.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Zwei Orte enthalten alles](#zwei-orte-enthalten-alles)
2. [Das Inventar](#das-inventar)
3. [Was den Server verlassen kann](#was-den-server-verlassen-kann)
4. [Kopien, die Sie selbst anfertigen](#kopien-die-sie-selbst-anfertigen)
5. [Mehrere Maschinen und Anbieter](#mehrere-maschinen-und-anbieter)
6. [Checkliste](#checkliste)

## Zwei Orte enthalten alles

Exponential hält seinen Zustand in der **Datenbank** (Inhalte, Benutzer, Rollen, Versionen, in Tabellen gespeicherte
Einstellungen) und in **Dateien** (hochgeladene Bilder und Dokumente, Einstellungen, Logs, der Audit-Trail)
([Datensicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)). Liegen beide auf
Maschinen an einem von Ihnen freigegebenen Standort, gilt das auch für die Daten der Website, vorausgesetzt, dass
nichts anderes eingeschaltet wurde, das Daten nach außen sendet (siehe unten), und dass Ihre Datensicherungen derselben
Regel folgen.

## Das Inventar

| Ort | Enthält | Personenbezogene Daten, üblicherweise |
|---|---|---|
| Die Datenbank (oder `var/storage/sqlite3/` bei SQLite) | Inhalte und ihre Versionen, Benutzerkonten (Name, Login, E-Mail-Adresse, Passwort-Hash), Rollenzuweisungen, gesammelte Formulareinträge, Suchbegriffe, E-Mail-Präferenzen und ihr Einwilligungsprotokoll | ja |
| `var/storage/` und `var/<site>/storage/` | hochgeladene Bilder und Dateien, ihre Varianten | möglicherweise (Dokumente, Fotos) |
| `var/<site>/log/audit/` | der Audit-Trail: wer was getan hat, mit gekürzten Adressen und gehashten Sitzungen | ja, teilweise pseudonymisiert |
| `var/log/`, `var/<site>/log/` | Fehler-, Warnungs-, 404-, Sicherheits- und weitere Logs; Client-Adressen in den Anfrage-Logs maskiert | möglicherweise |
| `var/vc/` (Velocity) | Zugriffs- und Fehler-Logs von Velocity, die Besucher nennen | ja |
| `var/log/mail/` | vom Dateitransport geschriebene E-Mails (Testmaschinen) | ja |
| `var/cache/` | kompilierte Templates und gerenderte Seiten, werden neu erzeugt | möglicherweise, kurzzeitig |
| `settings/override/` | Einstellungen, Zugangsdaten der Datenbank, die Audit-Schlüssel, das Geheimnis für die E-Mail-Präferenzen | nein, aber Geheimnisse |
| Die eigenen Logs des Webservers | außerhalb von Exponential | ja |

Quellen: [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md),
[Anfrage-Logs](../../../../../eng-us/features/6.0/request-logs.md), [Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md),
[Datenschutzstandards](../../../../../eng-us/standards/privacy.md). Wie lange jeder Eintrag standardmäßig aufbewahrt wird, steht
unter [Aufbewahrung von Logs und Daten](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md).

Datenschutzfunktionen, die verringern, was gespeichert wird: Die Anfrage-Logs maskieren Client-Adressen
(`[RequestLogSettings] MaskIP` eingeschaltet) und ersetzen Werte von Abfrageparametern durch `***`; der Audit-Trail
kürzt Adressen, hasht Sitzungen und zeichnet niemals Passwörter oder Token auf; der Log-Viewer maskiert E-Mail- und
IP-Adressen, wenn er Zeilen anzeigt.

## Was den Server verlassen kann

Der Kernel und die Administration senden standardmäßig nichts an Dritte. Die Skripte der Administration werden lokal
ausgeliefert, und admin4 lädt keine Webschriften oder Skripte von anderen Hosts. Daten verlassen den Server nur über
das, was Sie konfigurieren ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)):

| Funktion | Sendet Daten an | Standard |
|---|---|---|
| Ausgehende E-Mail | Ihren Mailserver oder Relay | von Ihnen konfiguriert |
| `bcwebsitestatistics` | Google Analytics oder Tag Manager | Erweiterung nicht aktiv |
| `recaptcha`, `hcaptcha` | den Captcha-Anbieter | nicht aktiv |
| Anmeldung über soziale Netzwerke von `sevenx_authentication_2fa` | den gewählten Identitätsanbieter | nicht aktiv |
| Audit-Senke Syslog | das eigene Syslog der Maschine (`Transport=local`) oder einen entfernten Syslog-Host, den Sie festlegen | für die Kanäle `access` und `system` eingeschaltet, lokal |
| Audit-Senken Webhook und E-Mail | die URL oder die Empfänger, die Sie festlegen | Webhook ohne URL; E-Mail bei kritischen Ereignissen an die Empfänger der Website |
| Solr-Suche (ezfind) | Ihren Solr-Server | nicht aktiv |
| Ein CDN oder externer Proxy vor der Website | diesen Anbieter | nicht konfiguriert |

Prüfen Sie, was auf einer Website aktiv ist, unter **Setup > Extensions** oder so:

```bash
grep -rn "ActiveExtensions" settings/override/site.ini.append.php
```

## Kopien, die Sie selbst anfertigen

Regeln zur Datenresidenz gelten üblicherweise auch für Kopien:

- **Datensicherungen** enthalten die Datenbank, den Speicher, den Audit-Trail und `settings/override`; bewahren Sie sie
  an einem freigegebenen Standort auf ([Externe und verschlüsselte Kopien](../../../../../eng-us/help/government/operations/backups-and-recovery/offsite-and-encrypted-copies.md)).
- **Testkopien**, die aus der Produktion erstellt werden, enthalten echte personenbezogene Daten
  ([Umgebungen für Entwicklung, Test und Produktion](umgebungen-entwicklung-test-produktion.md)).
- **Exporte**: Audit-Exporte, CSV-Exporte von Konten (**Benutzer > Benutzerkonten prüfen**), Exporte von
  Formulardaten. Legen Sie fest, wer sie anfertigen darf; jeder wird im Audit-Trail aufgezeichnet.
- **Supportberichte**: Der Fehlerbericht der Hilfe entfernt E-Mail-Adressen, IP-Adressen und Zugangsdaten, bevor er
  gesendet wird ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)).

## Mehrere Maschinen und Anbieter

Mit einem Datenbankserver, einem gemeinsamen Dateisystem für hochgeladene Dateien, einem zentralen Log-Sammler oder
einem CDN ist jeder davon ein eigener Standort. Führen Sie sie alle in Ihrem Inventar auf. Eine Website, die von einer
Maschine mit SQLite ausgeliefert wird, hält alles in einem Verzeichnisbaum, was der am einfachsten zu dokumentierende
Fall ist.

## Checkliste

- [ ] Das Inventar oben ist mit Ihren Maschinen, Anbietern und Standorten ausgefüllt.
- [ ] Aktive Erweiterungen sind geprüft; jede, die Kontakt zu Dritten aufnimmt, ist dokumentiert und begründet.
- [ ] Datensicherungen, Testkopien und Exporte folgen denselben Standortregeln.
- [ ] Die Aufbewahrungsfristen der Logs und des Audit-Trails sind bekannt und abgestimmt.
- [ ] Das Verzeichnis von Verarbeitungstätigkeiten nennt die Website und verweist auf dieses Inventar.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Hosting in der Behörden- und souveränen Cloud](verwaltungs-und-souveraene-cloud.md)
- [Compliance und DSGVO für Hoster](../../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
