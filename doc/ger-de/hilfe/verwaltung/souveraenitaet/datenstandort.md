---
title: Datenstandort
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Wo eine Exponential-Installation Daten ablegt (Datenbank, Speicherdateien, Caches, Protokolle, Audit-Einträge, Datensicherungen, E-Mail, Suchindex), damit eine öffentliche Stelle ihren Datenstandort angeben kann, und welche optionalen Funktionen Daten anderswohin senden.
keywords: Datenstandort, Datenresidenz, wo liegen die Daten, Speicher, Verzeichnis var, Datenbank, Protokolle, Audit, Datensicherungen, Solr, E-Mail, Drittanbieter, Souveränität, data location, data residency, storage, var directory, logs, backups, mail, third party, sovereignty
type: help
---
# Datenstandort

Diese Seite richtet sich an Administratoren, Datenschutzbeauftragte und Führungskräfte, die angeben müssen, wo die
Daten der Stelle gespeichert und verarbeitet werden. Exponential hält alles auf den Servern und Diensten, die Sie
wählen; diese Liste hilft Ihnen, jeden Ort zu erfassen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wo eine Installation Daten ablegt](#wo-eine-installation-daten-ablegt)
2. [Daten, die nur hinausgehen, wenn Sie es wählen](#daten-die-nur-hinausgehen-wenn-sie-es-wählen)
3. [Die Erklärung verfassen](#die-erklärung-verfassen)

## Wo eine Installation Daten ablegt

| Daten | Ort | Hinweise |
|---|---|---|
| Inhalte, Benutzer, in der Datenbank gespeicherte Einstellungen | der Datenbankserver oder die SQLite-Datei | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Hochgeladene Dateien und Bilder | `var/<site>/storage/` | kann auf gemeinsamem oder eingehängtem Speicher liegen |
| Caches (Seiten, Templates, INI) | `var/<site>/cache/` oder ein anderer Pfad | pro Website verschiebbar ([Protokolle, Cache und Ablauf pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)) |
| Protokolle | `var/log/` oder pro Website | Rotation und Maskierung in [Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md), [Protokollanzeige](../../../../eng-us/features/6.0/log-viewer.md) |
| Audit-Einträge | `var/<site>/log/audit/`, dazu ein Index in der Datenbank | Senken können sie an ein Syslog oder einen Webhook Ihrer Wahl kopieren ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)) |
| Sitzungen | die Datenbank oder der eingerichtete Sitzungsspeicher | [Sitzungen](../../../anleitungen/sitzungen.md) |
| Suchindex | die Datenbank (eingebaute Suchmaschine) oder der Solr-Server | [Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md) |
| E-Mail | Ihr Mailserver; der Dateitransport schreibt E-Mails in Dateien innerhalb der Installation | [E-Mail-Zustellung](../../hosting/betrieb/mailzustellung.md) |
| Datensicherungen | wohin Ihr Sicherungsprozess sie legt | oft die vergessene Kopie |
| Exporte | Dateien, die von Exporten und Aufträgen geschrieben werden | löschen Sie sie, wenn sie erledigt sind |

## Daten, die nur hinausgehen, wenn Sie es wählen

| Funktion | Wohin die Daten gehen |
|---|---|
| Erweiterungen für Webanalyse, CAPTCHA, Karten, Social Login | zu deren Anbietern (siehe „Zu prüfende Dienste von Drittanbietern“) |
| KI-Assistent (`expai`) | zum eingerichteten Anbieter; Region pro Anbieter festgehalten; lokale Modelle möglich |
| CDN oder HTTP-Cache vor der Website | zu den Standorten des CDN ([HTTP-Caching und CDNs](../../../../eng-us/help/creators/website-creators/http-caching-and-cdns.md)) |
| Audit-Senken | zum Syslog oder Webhook-Empfänger, den Sie einrichten |
| Zeitgesteuerte Exporte (`xrowextract`) | zu den Zielen, die Sie festlegen (SFTP, S3, WebDAV, ...) |

## Die Erklärung verfassen

1. Listen Sie jeden Server und jeden Dienst mit Betreiber und Land auf: Web, Datenbank, Speicher, Solr, E-Mail,
   Datensicherungen, CDN.
2. Listen Sie die aktiven Erweiterungen auf, die Drittanbieter kontaktieren (**Setup > Extensions**).
3. Notieren Sie die Aufbewahrung pro Ort: Audit (standardmäßig 90 Tage aktiv, 730 Tage archiviert), Protokolle,
   Datensicherungen, Exporte.
4. Überprüfen Sie sie, wenn sich Hosting, Erweiterungen oder Verträge ändern.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Verwaltungscloud und Hostingmodelle](verwaltungscloud-und-hostingmodelle.md)
- [Aufbau des Speichers](../../hosting/betrieb/aufbau-des-speichers.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
