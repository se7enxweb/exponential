---
title: Härtung für Hoster
audience: hosting, admin
category: hosting
order: 20
summary: Die Härtung, die ein Hoster auf jede Exponential-Website anwendet, Schicht für Schicht - ausgelieferte Dateien, Geheimnisse, Debug-Ausgabe, die Administration, Passwörter, Sitzungen, Header und TLS, Proxys, Eigentum an Dateien, Velocity, Mail, das Prüfprotokoll und Updates, mit den Einstellungen und Prüfungen für jede und einer Checkliste für die Server-Vorlage.
keywords: Härtung, Sicherheit, Hosting, Server-Vorlage, htaccess_root, Rewrite-Regeln, settings/override, Geheimnisse, Debug, DebugOutput, Administration, Passwörter, Sitzungen, CookieSecure, Sicherheitsheader, HSTS, TrustedProxies, Dateiberechtigungen, FileSettings, Velocity, AllowRootWorkers, Audit, request-shield, Updates, hardening, security, hosting, server template, rewrite rules, secrets, administration, passwords, sessions, security headers, file permissions, audit, updates
featured: true
type: help
---
# Härtung für Hoster

Diese Seite ist für Systemadministratoren und Sicherheitsverantwortliche eines Hosters, die die Server-Vorlage bauen, aus der jede
Exponential-Website angelegt wird. Sie geht die Schichten einer Website von außen nach innen durch, sagt, was Exponential standardmäßig
tut, und nennt die Einstellung oder den Befehl, die der Hoster setzt oder prüft. Sie folgt der Reihenfolge von
[Kapitel 13 des Installationsbuchs](../../../installation/13-sicherheitshaertung.md), das jedes Detail enthält, und der Sicht Bedrohung für
Bedrohung von [Sicherheitshärtung](../../sicherheitshaertung.md).

> **Hinweis**
> Einstellungen werden in Überschreibungen geändert, nie in den mitgelieferten Dateien: `settings/override/<file>.ini.append.php` für die
> ganze Installation, `settings/siteaccess/<siteaccess>/<file>.ini.append.php` für einen Siteaccess. Leeren Sie nach einer Änderung den
> INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`), und führen Sie unter Velocity `exp:velocity deploy` aus
> ([Kapitel 13](../../../installation/13-sicherheitshaertung.md#inhalt-dieses-kapitels)).

## Inhalt

1. [Die Prinzipien](#die-prinzipien)
2. [Ausgelieferte Dateien](#ausgelieferte-dateien)
3. [Geheimnisse](#geheimnisse)
4. [Debug-Ausgabe](#debug-ausgabe)
5. [Administration, Passwörter und Sitzungen](#administration-passwörter-und-sitzungen)
6. [Header, TLS und Proxys](#header-tls-und-proxys)
7. [Dateien, Benutzer und Prozesse](#dateien-benutzer-und-prozesse)
8. [Beobachten](#beobachten)
9. [Ein Schutz davor](#ein-schutz-davor)
10. [Updates](#updates)
11. [Checkliste für die Server-Vorlage](#checkliste-für-die-server-vorlage)

## Die Prinzipien

[Sicherheitshärtung](../../sicherheitshaertung.md#grundsätze-sicher-ab-werk) misst Exponential an der Arbeitsweise von OpenBSD:
standardmäßig sicher, geringste Rechte, Trennung von Rechten, minimale Angriffsfläche, Audit und Korrektur nach vorn. Für einen Hoster
heißt das vor allem eine Regel: **Lockern Sie keinen Standard, damit etwas funktioniert; finden Sie die Ursache.** Ein Support-Ticket, das
damit endet, dass die Rewrite-Regel ausgeschaltet wurde, hat auf dieser Website eine Lücke geöffnet.

## Ausgelieferte Dateien

Der Document Root von Exponential ist das Wurzelverzeichnis der Installation, sodass `settings/`, `var/`, `vendor/` und die
SQLite-Datenbank darin liegen. Die Sicherheit kommt vom Routing: Nur eine feste Liste von Asset-Pfaden wird als Dateien ausgeliefert,
alles andere geht an `index.php` ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)).

| Webserver | Was der Hoster tut |
|---|---|
| Apache, LiteSpeed mit `.htaccess` | `cp .htaccess_root .htaccess`; nach jedem Upgrade vergleichen und neue Regeln einfügen |
| nginx | der abgeleitete Server-Block aus [8.6](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm), mit den Regeln für Dateien mit Punkt und für Skripte vor jeder statischen Location |
| Velocity | nichts; `FollowSymlinks=disabled` (der Standard) verweigert auch Links aus der Wurzel hinaus |
| jeder | nie eine allgemeine Konfiguration „Datei ausliefern, wenn sie existiert“ |

Prüfen Sie nach jeder Änderung der Konfiguration des Webservers von außen:

```bash
for p in /.git/config /.env /settings/site.ini /composer.json /kernel/classes/ezini.php /var/log/error.log; do
  printf '%-32s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"
done
```

Jede Zeile muss `404` zeigen, oder `200` mit einer Exponential-Seite und nie dem Inhalt der Datei. Details zu Webservern:
[Einen Webserver wählen](../server/einen-webserver-waehlen.md) und [Kapitel 8](../../../installation/08-die-website-ausliefern.md).

## Geheimnisse

`settings/override/` enthält das Datenbankpasswort (`site.ini.append.php`), die Signatur- und Pseudonymschlüssel des Audits
(`audit.ini.append.php`), das Website-Geheimnis der E-Mail-Einstellungen (`mailpreferences.ini.append.php`, das auch den
Passwort-Stempel der Sitzung schlüsselt) und, wenn gesetzt, das Token des Dashboards von Velocity
([13.3](../../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride)).

- Modus `0640` oder strenger, nie in der Versionsverwaltung, nie an einem öffentlichen Ort.
- Sichern Sie es als Geheimnis, verschlüsselt, mit der Datenbank ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)).
- Kopieren Sie es bei jedem Umzug der Website mit. Eine Kopie ohne `mailpreferences.ini.append.php` erzeugt ein neues Website-Geheimnis:
  Alte Abmeldelinks passen nicht mehr, und jeder Benutzer wird einmal abgemeldet.
- Entfernen Sie `kickstart.ini` und `var/log/initial-admin-password` nach der Bereitstellung
  ([Erste Anmeldung und Standardpasswörter](erste-anmeldung-und-standardpasswoerter.md)).

## Debug-Ausgabe

`[DebugSettings] DebugOutput=disabled` und `[TemplateSettings] Debug=disabled` werden ausgeschaltet ausgeliefert. Lassen Sie sie aus. Wenn
der Support eine Live-Website debuggen muss, beschränken Sie es auf die Adresse des Support-Desks und entfernen den Eintrag wieder:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=203.0.113.10
```

In der `php.ini` oder im Pool von PHP-FPM: `display_errors=Off`, `log_errors=On`, `expose_php=Off`
([13.4](../../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige)). Hinter einem Proxy ist `DebugByIP` nur so
verlässlich wie `TrustedProxies[]` (siehe [TLS und HTTPS](tls-und-https.md#proxys-die-tls-beenden)).

## Administration, Passwörter und Sitzungen

| Thema | Standard | Teil des Hosters |
|---|---|---|
| Siteaccess der Administration | `RequireUserLogin=true` | ein eigener Hostname, Einschränkungen im Netzwerk: [Die Administration einschränken](die-administration-einschraenken.md) |
| Setup-Assistent | auf einer installierten Website geschlossen; `CheckValidity=false` vom Installer geschrieben | `[SetupSettings] AllowWizardOnInstalledSite=disabled` beibehalten |
| Passwörter | `MinPasswordLength=10`, `WeakPasswordCheck=enabled`, bcrypt | eine Richtlinie anbieten wie `MinPasswordLength=15`, `ForbidLogin`, `ForbidCurrentPassword` ([13.6](../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)) |
| Sperre | `MaxNumberOfFailedLogin=0` (aus) | mit dem Kunden entscheiden; eine Sperre lässt auch jeden ein bekanntes Konto sperren |
| Zweistufige Anmeldung | Erweiterung von `composer.json` verlangt und von den Installern aktiviert; `Enforce2FA=disabled` | `Enforce2FA` für Administratoren empfehlen: [Zweistufige Anmeldung](zweistufige-anmeldung.md) |
| Sitzungscookie | `HttpOnly`, `SameSite=Lax`, `CookieSecure=auto` | `CookieSecure=true` auf Websites nur mit HTTPS ([13.7](../../../installation/13-sicherheitshaertung.md#137-sitzungen-und-cookies)) |
| Formular-Tokens | `ezformtoken` aktiv | in `ActiveExtensions[]` behalten ([13.8](../../../installation/13-sicherheitshaertung.md#138-formular-tokens-gegen-cross-site-request-forgery)) |
| Konfiguration aus Git | `ManagedByDeployment=disabled` | `enabled` auf Siteaccesses der Administration in der Produktion, wenn Einstellungen und Templates aus der Versionsverwaltung kommen |

Eine Passwortänderung meldet jede andere Sitzung dieses Benutzers ab (`[PasswordSettings] EndOtherSessions=enabled`). Lassen Sie es an:
Das macht eine Passwortänderung nützlich, nachdem ein Konto übernommen wurde.

## Header, TLS und Proxys

Fünf Sicherheitsheader gehen auf jeder Seite hinaus (`X-Content-Type-Options`, `X-Frame-Options`,
`Content-Security-Policy: frame-ancestors 'self'`, `Referrer-Policy`, `Permissions-Policy`); HSTS ist aus, bis es eingeschaltet wird.
Apache und nginx fügen statischen Dateien von sich aus nichts hinzu. Der Teil des Hosters:

- HTTPS auf jedem Namen, HTTP umgeleitet, HSTS erhöht, sobald alles funktioniert: [TLS und HTTPS](tls-und-https.md).
- Die volle Richtlinie (`script-src`, `style-src`) ist in der Auslieferung auf der Administration im Berichtsmodus:
  [Content Security Policy](content-security-policy.md).
- Hinter einem Load Balancer, CDN oder Proxy eines Panels: Führen Sie genau Ihre Proxys in `site.ini [HTTPHeaderSettings] TrustedProxies[]`
  auf (Velocity: `Q.webserver.proxy.trusted`), und stellen Sie sicher, dass der Anwendungsserver nicht an ihnen vorbei erreichbar ist
  ([13.10](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)).

## Dateien, Benutzer und Prozesse

Wenn der PHP-Prozess nur `var/` und `settings/` ändern kann und der Webserver sich weigert, irgendetwas unterhalb der Wurzel auszuführen,
kann eine Schwachstelle in einer Erweiterung keinen Code einschleusen
([13.11](../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)).

- Ein Benutzer schreibt: PHP-FPM-Pool, Velocity-Worker, Cronjobs und Konsolenskripte laufen als Benutzer der Website.
- Der Code (`kernel/`, `lib/`, `vendor/`, `index.php`) gehört einem anderen Benutzer und ist für den Benutzer der Website nur lesbar.
- Keine für alle beschreibbaren Dateien. Die mitgelieferten Werte in `[FileSettings]` sind `0777` und `0666`; setzen Sie die gehärteten:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

- `TemporaryDir` wird als `/tmp/` ausgeliefert, mit jedem Benutzer der Maschine geteilt; geben Sie jeder Website ein eigenes unter `var/`.
- Velocity: `[ServerSettings] Host=127.0.0.1`, wenn ein Proxy davor ist, `User` und `Group` auf den Benutzer der Website gesetzt,
  `AllowRootWorkers=disabled`, Token des Dashboards und Passwort des Kontrollzentrums gesetzt oder die Ansichten lokal gehalten
  ([13.12](../../../installation/13-sicherheitshaertung.md#1312-velocity-spezifische-härtung)).

Wie das auf viele Kunden skaliert: [Benutzer und Pools pro Kunde](benutzer-und-pools-pro-kunde.md) und
[Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md).

## Beobachten

Härtung ohne Beobachtung verzögert nur den Moment, in dem Sie von einem Angriff erfahren.

| Quelle | Was sie festhält | Seite für den Hoster |
|---|---|---|
| `var/log/security.log` | die Urteile des Request-Klassifizierers: Abtastungen, Traversal, Injection, Angriffswerkzeuge, Häufungen von 404 | [Sicherheitslog](../betrieb/sicherheitslog.md) |
| das Prüfprotokoll | Anmeldungen, Änderungen an Rollen und Einstellungen, Entfernungen, Befehle; als Hash-Kette | [Prüfprotokoll](pruefprotokoll.md) |
| `var/log/auth.log` | jeden Schritt der zweistufigen Anmeldung (nie einen Code oder ein Geheimnis) | [Zweistufige Anmeldung](zweistufige-anmeldung.md) |
| `var/log/csp.log` | Verstöße gegen die Content Security Policy | [Content Security Policy](content-security-policy.md) |

Führen Sie die Cronjob-Gruppe `frequent` aus, damit die Alarmregeln des Audits laufen, leiten Sie das Protokoll mit einer Senke von der
Maschine weg und lesen Sie `security.log` wöchentlich
([Sicherheitshärtung](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)).

## Ein Schutz davor

[request-shield](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield), eine kleine Web Application Firewall für PHP vom CJW
Network, weist Scanner und Fluten ab, bevor der Kernel startet. Es wird für jede Website empfohlen, die aus dem Internet erreichbar ist,
und ist nicht Teil der `composer.json` von Exponential. Ein Hoster kann es als Teil eines verwalteten Tarifs anbieten:
[request-shield und Request-Regeln](request-shield-und-request-regeln.md).

## Updates

Nur die Linie 6.0 erhält Sicherheitskorrekturen, veröffentlicht als nächstes Release 6.0.x. Verfolgen Sie Releases, aktualisieren Sie
zuerst eine Kopie, prüfen Sie nach jedem Update die Konsistenz der Dateien, und halten Sie auch PHP, den Datenbankserver, das
Betriebssystem und die Engine von Velocity aktuell: [Sicherheitsupdates und Offenlegung](sicherheitsupdates-und-offenlegung.md).

## Checkliste für die Server-Vorlage

- [ ] Rewrite-Regeln: eine aktuelle Kopie von `.htaccess_root`, der abgeleitete nginx-Block oder Velocity; die Prüfung von außen liefert
      keine Datei.
- [ ] SOAP nur, wo ein Kunde es braucht: `index_soap.php` durchgelassen (vor 6.0.15 hieß es `soap.php`) und auf die
      Adressen der Aufrufer beschränkt; `soap.php` abgelehnt, sobald es niemand mehr aufruft
      ([Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../../bc/6.0/soap-einstiegsskript-index-soap.md)).
- [ ] `settings/override/` mit Modus `0640` oder strenger, außerhalb der Versionsverwaltung, verschlüsselt gesichert.
- [ ] Die Bereitstellung entfernt `kickstart.ini` und `var/log/initial-admin-password` und hinterlässt nie ein bekanntes Passwort.
- [ ] `DebugOutput=disabled`; PHP `display_errors=Off`, `expose_php=Off`.
- [ ] Administration auf einem eigenen Hostnamen, eingeschränkt durch Netzwerk, VPN oder die Browserprüfung des Schutzes.
- [ ] `CheckValidity=false`, `AllowWizardOnInstalledSite=disabled`.
- [ ] `ezformtoken` aktiv; `EndOtherSessions=enabled`.
- [ ] HTTPS auf jedem Namen, HTTP umgeleitet, `CookieSecure=true`, HSTS erhöht, sobald es stabil ist.
- [ ] `TrustedProxies[]` nennt genau Ihre Proxys oder bleibt beim Standard Loopback.
- [ ] Ein Benutzer des Betriebssystems pro Website; Code für ihn nur lesbar; gehärtete `[FileSettings]`; eigenes `TemporaryDir`.
- [ ] Velocity hinter einem Proxy an `127.0.0.1` gebunden; Worker nie als root.
- [ ] Cronjob-Gruppe `frequent` läuft als Benutzer der Website; Empfänger der Alarme des Audits geprüft; eine Senke konfiguriert.
- [ ] Eine Routine für Updates mit benannten Verantwortlichen.

Die Go-Live-Liste Einstellung für Einstellung ist [13.16](../../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang); die
wiederkehrende Prüfung ist die [Checkliste für die Sicherheitsprüfung](../checklisten/checkliste-fuer-die-sicherheitspruefung.md).

**Der Demomodus bleibt aus.** `EXP_DEMO_MODE=1` installiert und behält ein bekanntes Administratorpasswort; setzen Sie es nur für den eigenen Pool, virtuellen Host oder die eigene Unit einer öffentlichen Demo, nie serverweit oder in einer Vorlage, und lösen Sie bei Zeilen `demo/mode` in `security.log` von Kunden-Websites einen Alarm aus. [Demomodus für Hoster](demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Kapitel 13: Sicherheitshärtung für die Produktion](../../../installation/13-sicherheitshaertung.md)
- [Checkliste für einen neuen Server](../checklisten/checkliste-fuer-einen-neuen-server.md)
- [Kompromittierte Website](../runbooks/kompromittierte-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
