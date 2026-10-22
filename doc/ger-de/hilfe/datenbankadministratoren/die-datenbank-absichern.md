---
title: Die Datenbank absichern
audience: dba
category: database-administration
order: 25
summary: Die Sicherheitsmaßnahmen, die die Datenbank einer Exponential-Website betreffen - wo die Zugangsdaten liegen und wie Sie sie schützen, wie Administration und Konsole Geheimnisse maskieren, Passwörter auf Kommandozeilen, Netzwerkzugang je Engine, die SQLite-Datei, Debug-Ausgabe, die SQL zeigt, Sicherungen als Geheimnisse und was der Audit-Trail festhält.
keywords: Sicherheit, Härtung, Zugangsdaten, Passwort, settings/override, chmod 640, maskierte Geheimnisse, exp:ini, Systeminformationen, Prozessliste, EXP_INSTALL_DB_PASSWORD, pg_hba.conf, bind-address, SQLite-Download, SQLOutput, Debug-Ausgabe, Verschlüsselung von Sicherungen, Audit-Trail, security, hardening, credentials, secrets masked, process list, debug output, backups encryption, audit trail
type: help
---
# Die Datenbank absichern

Diese Seite richtet sich an Datenbankadministratoren, die die Sicherheit der Datenbank einer Exponential-Website prüfen. Sie
beantwortet: wo die Zugangsdaten der Datenbank liegen, wer sie lesen kann, wo sie durchsickern könnten (Kommandozeilen,
Debug-Ausgabe, Sicherungen), wie Sie den Netzwerkzugang je Engine begrenzen und was die Anwendung festhält. Die allgemeine
Härtung der Website steht unter [Sicherheitshärtung](../sicherheitshaertung.md) und in
[Kapitel 13](../../installation/13-sicherheitshaertung.md); diese Seite ist der Teil der Datenbank daran.

## Inhalt

1. [Wo die Zugangsdaten liegen](#wo-die-zugangsdaten-liegen)
2. [Die Override-Dateien schützen](#die-override-dateien-schützen)
3. [Was die Anwendung zeigt und verbirgt](#was-die-anwendung-zeigt-und-verbirgt)
4. [Passwörter auf Kommandozeilen](#passwörter-auf-kommandozeilen)
5. [Netzwerkzugang](#netzwerkzugang)
6. [Die SQLite-Datei](#die-sqlite-datei)
7. [Debug-Ausgabe zeigt SQL](#debug-ausgabe-zeigt-sql)
8. [Sicherungen sind Geheimnisse](#sicherungen-sind-geheimnisse)
9. [Was festgehalten wird](#was-festgehalten-wird)
10. [Checkliste](#checkliste)

## Wo die Zugangsdaten liegen

| Datei | Was sie enthält |
|---|---|
| `settings/override/site.ini.append.php` | `[DatabaseSettings] User`, `Password` und Replikat-Anmeldungen |
| `settings/override/file.ini.append.php` und `config.cluster.php` | die Datenbankanmeldung des DFS-Clusters, wenn Clustering verwendet wird |
| `settings/override/ezoracle.ini.append.php` | Oracle-Verbindungseinstellungen (kein Passwort, außer Sie legen eines dort ab) |
| `settings/siteaccess/<name>/site.ini.append.php` | die eigene Datenbank eines Siteaccess, falls vorhanden |

Quelle: [13.3 Geheimnisse in settings/override](../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride).

## Die Override-Dateien schützen

- **Committen Sie `settings/override/` nie** in die Versionsverwaltung und kopieren Sie es nie an einen öffentlichen Ort.
- Halten Sie die Dateien nur für den Benutzer der Website lesbar, wo nichts anderes sie braucht: `chmod 640`, Eigentümer der
  Benutzer der Website oder root, Gruppe die Gruppe der Website.
- Sichern Sie sie mit der Datenbank, als Geheimnisse.
- Kopieren Sie sie bei jedem Umzug der Installation mit.

## Was die Anwendung zeigt und verbirgt

Eine Regel, `expSecretRule`, entscheidet, was für `exp:ini`, die Einstellungsseiten, das Audit-Log und
**Setup > Systeminformationen** ein Geheimnis ist:

- ein Wert, dessen Name Wörter wie Passwort enthält, zeigt nur "(set, hidden)" oder `********`;
- **der Datenbankbenutzer ist** im Bericht der Systeminformationen **immer verborgen**;
- das Passwort in einer Adresse wird abgeschnitten (`mysql://user:***@db/site`).

`exp:ini get` und `list` maskieren Geheimnisse, sofern nicht `--show-secrets` angegeben ist
([Systeminformationen, was nie gezeigt wird](../../anleitungen/systeminformationen.md#7-was-niemals-angezeigt-wird)). Geben Sie die
Einstellungsansichten (`setup/setup`) trotzdem nur Administratoren.

## Passwörter auf Kommandozeilen

Ein als Kommandozeilenargument übergebenes Passwort ist für andere Benutzer der Maschine in der Prozessliste sichtbar und
bleibt in der Shell-Historie. Verwenden Sie lieber, was jedes Werkzeug stattdessen anbietet:

| Werkzeug | Sicherer Weg |
|---|---|
| `exp:install` | die Umgebungsvariable `EXP_INSTALL_DB_PASSWORD` |
| `ora-grant.php` | `-` als Passwort und `ORACLE_NEW_PASSWORD` in der Umgebung |
| `mysql`, `mysqldump` | `-p` ohne Wert (Abfrage) oder eine Optionsdatei, die nur Sie lesen können |
| `psql`, `pg_dump` | die Abfrage oder eine Datei `.pgpass` mit Modus 0600 |
| `mongosh`, `mongodump` | `passwordPrompt()` oder eine URI ohne Passwort, sodass das Werkzeug fragt |
| `ezsqldumpschema.php`, `ezsqlinsertschema.php`, `ezsqldiff.php` | sie nehmen nur `--password=`: Führen Sie sie auf einer Maschine aus, die Sie kontrollieren, oder mit einem vorübergehenden Passwort, das Sie danach ändern |

## Netzwerkzugang

| Engine | Maßnahme |
|---|---|
| PostgreSQL | `listen_addresses` nur auf der nötigen Schnittstelle; `pg_hba.conf`-Zeilen nur für die Webserver, diese Datenbank und diesen Benutzer, mit einer Passwortmethode |
| MySQL, MariaDB | an die nötige Schnittstelle binden; Host-Teil des Kontos auf die Webserver beschränkt |
| MongoDB | Zugriffskontrolle an; an die nötige Schnittstelle binden |
| Oracle | der Listener nur von den Webservern aus erreichbar |
| Alle | eine Firewall, die den Datenbankport nur von den Webservern und Cron-Hosts zulässt; TLS, wo die Datenbank auf einer anderen Maschine liegt, auf dem Server und in der Client-Bibliothek konfiguriert |

Exponential hat keine eigene Einstellung für Client-Zertifikate oder TLS-Optionen von MySQL- oder PostgreSQL-Verbindungen; es
gilt, was die Client-Bibliothek aus ihren Standards nimmt. Prüfen Sie die Verschlüsselung auf der Serverseite, wenn Ihre
Richtlinie sie verlangt.

## Die SQLite-Datei

Die Datenbankdatei darf nie über HTTP ausgeliefert werden. Prüfen Sie es auf Ihrem eigenen Host; alles außer `200` ist richtig:

```bash
curl -sI https://www.example.com/var/storage/sqlite3/exponential.db | head -1
```

Halten Sie das Verzeichnis nur für die Prozesse der Website gruppenschreibbar, nie für alle schreibbar
([SQLite-Dateien, Eigentümer und Rechte](sqlite-dateien-eigentuemer-und-rechte.md)).

## Debug-Ausgabe zeigt SQL

`[DatabaseSettings] SQLOutput=enabled` schreibt jede Anweisung in die Debug-Ausgabe. Schalten Sie die Debug-Ausgabe nur für
Ihre eigene Adresse ein (`DebugByIP=enabled`, `DebugIPList[]`), nie für alle auf einer öffentlichen Website
([12.4 Debug-Ausgabe](../../installation/12-fehlerbehebung.md#124-debug-ausgabe)).

## Sicherungen sind Geheimnisse

Ein Datenbank-Dump enthält die E-Mail-Adresse und den Passwort-Hash jedes Benutzers. Verschlüsseln Sie Sicherungen im Ruhezustand,
halten Sie sie vom Webserver fern, begrenzen Sie, wer sie lesen kann, und löschen Sie alte Generationen nach Plan
([Sicherungen je Engine](sicherungen-je-engine.md), [Aufbewahrung von Logs und Daten](../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)).

## Was festgehalten wird

Der Audit-Trail hält administrative Aktionen fest; jeder Lauf der Upgrade-Prüfung und jeder Download ihres Berichts wird als
`system.upgrade.run` geschrieben ([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md#sicherheit), [Audit](../../bc/6.0/audit.md)).
Anweisungen, die Sie direkt gegen die Datenbank ausführen, hält Exponential nicht fest; verwenden Sie dafür das eigene
Auditing der Engine.

## Checkliste

- [ ] `settings/override/` hat Modus 640 und ist nicht in der Versionsverwaltung.
- [ ] Kein Datenbankpasswort in einer Shell-Historie, Crontab oder einem Skript.
- [ ] Der Datenbankport ist nur von den Webservern und Cron-Hosts erreichbar.
- [ ] Die SQLite-Datei liefert über HTTP kein `200`.
- [ ] Die Debug-Ausgabe ist auf die Adressen von Administratoren beschränkt.
- [ ] Sicherungen sind verschlüsselt und zugriffsgeschützt.

## Verwandte Seiten

- [Datenbankkonten und Rechte](datenbankkonten-und-rechte.md)
- [Sicherheitshärtung](../sicherheitshaertung.md)
- [Härtung für Hoster](../hosting/sicherheit/haertung-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
