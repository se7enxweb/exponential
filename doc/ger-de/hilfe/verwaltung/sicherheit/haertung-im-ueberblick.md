---
title: Eine Exponential-Website für eine öffentliche Stelle härten
audience: government, admin, developer, hosting
category: public-sector
order: 50
summary: Für Administratorinnen, Administratoren und Hosting-Teams öffentlicher Websites - die Härtungsschichten von Exponential in der Reihenfolge, in der Sie sie abarbeiten (Webserver, Einstellungen und Geheimnisse, Administration, Anmeldung, Sitzungen, Header, CSP, Uploads, Protokollierung, Updates), mit der Einstellung und der Quellseite für jede.
keywords: Härtung, Sicherheitsgrundlage, sichere Konfiguration, Schichten, Webserver-Regeln, Administrations-Siteaccess, Passwörter, Sitzungen, Header, Uploads, Protokollierung, Updates, öffentlicher Sektor, hardening, security baseline, secure configuration, layers, web server rules, settings override, administration siteaccess, passwords, sessions, headers, CSP, uploads, logging, updates, request-shield, public sector
type: help
---
# Eine Exponential-Website für eine öffentliche Stelle härten

Diese Seite richtet sich an Administratorinnen und Administratoren, Entwicklerinnen und Entwickler und Hosting-Teams,
die eine öffentliche Website für den Produktivbetrieb vorbereiten. Sie ordnet die Härtungsarbeit in Schichten und
verweist für jede auf die ausführliche Seite. Die beiden Quellen des Projekts sind die
[Sicherheitshärtung](../../sicherheitshaertung.md), der aus Sicht eines Angreifers geschriebene Leitfaden, und
[Kapitel 13: Sicherheitshärtung für den Produktivbetrieb](../../../installation/13-sicherheitshaertung.md), die Referenz
Einstellung für Einstellung. Seit September 2026 ist eine Standardinstallation ohne zusätzlichen Aufwand sicher; Härten
bedeutet, diese Standardwerte beizubehalten und zu schließen, was dem Betreiber überlassen bleibt.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Die Grundsätze](#die-grundsätze)
2. [Die Schichten in ihrer Reihenfolge](#die-schichten-in-ihrer-reihenfolge)
3. [Was Sie nicht tun sollten](#was-sie-nicht-tun-sollten)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Grundsätze

Der Härtungsleitfaden folgt dem Maßstab von OpenBSD: sicher ab Werk, minimale Rechte, Rechtetrennung, minimale
Angriffsfläche, Audit und öffentliches Beheben nach vorn
([Grundsätze: sicher ab Werk](../../sicherheitshaertung.md#grundsätze-sicher-ab-werk)). Für eine öffentliche Stelle
bedeutet das praktisch: Lockern Sie keinen Standardwert, damit etwas funktioniert; finden Sie die Ursache.

## Die Schichten in ihrer Reihenfolge

| Schicht | Tun | Seite in diesem Bereich |
|---|---|---|
| 1. Webserver | aktuelle Umschreiberegeln (`.htaccess_root` oder der abgeleitete nginx-Block; `./console exp:setup:htaccess` endet mit 1, wenn `.htaccess` abgewichen ist); unterhalb des Wurzelverzeichnisses wird nichts ausgeführt; Punktdateien ergeben 404 | Datei-Uploads und SVG; HTTPS und HSTS |
| 2. Geheimnisse | `settings/override/` und `config.php` nie in der Versionsverwaltung, Modus 0640, als Geheimnis gesichert; alte Kopien in `var/backup/` entfernt | Geheimnisse in settings/override; [config.php von der Konsole](../../../anleitungen/setup-dateien-von-der-konsole.md) |
| 3. Administration | eigener Hostname, eingeschränkt per Netzwerk oder zweiter Authentifizierung, `RequireUserLogin=true`, Einrichtungsassistent geschlossen | Die Administration abtrennen |
| 4. Konten | ein persönliches Konto je Person, `admin` stillgelegt, Rollen mit minimalen Rechten | Rollen und minimale Rechte |
| 5. Anmeldung | zweistufige Anmeldung, Passwortrichtlinie, Entscheidung zur Kontosperre | Zweistufige Anmeldung; Passwortrichtlinie; Kontosperre |
| 6. Sitzungen | `CookieSecure=true`, vernünftiges Zeitlimit, eigenes Cookie je Siteaccess | Sitzungen und Cookies |
| 7. Header und TLS | Sicherheits-Header vorhanden, HTTPS überall, HSTS bei stabilem Betrieb | Sicherheits-Header; HTTPS und HSTS |
| 8. Content Security Policy | Report-only, dann in der Administration durchsetzen, dann auf den Websites | Content Security Policy; Von Report-only zur Durchsetzung |
| 9. Formulare | `ezformtoken` aktiv | CSRF und Formular-Tokens |
| 10. Debug | `DebugOutput=disabled`, PHP `display_errors=Off` | Debug-Ausgabe im Produktivbetrieb |
| 11. Dateien | ein Schreiber, keine für alle beschreibbaren Dateien, Code schreibgeschützt | Dateirechte und Eigentümer |
| 12. Konfigurationssperre | `ManagedByDeployment=enabled`, wo die Konfiguration aus der Versionsverwaltung kommt | Von der Bereitstellung verwaltete Konfiguration |
| 13. Ein vorgeschalteter Schutz | request-shield, zuerst im Beobachtungsmodus | request-shield |
| 14. Protokollierung | Audit-Cronjob läuft, Empfänger der Alarme, Weiterleitung an ein SIEM, wöchentliche Prüfung | Audit-Protokoll; Routine zur Protokollprüfung |
| 15. Updates | Releases verfolgen, in einer Kopie testen, `composer audit`, Dateikonsistenzprüfung | Patch-Rhythmus; Dateikonsistenzprüfung |
| 16. Wiederherstellung | Sicherungen außerhalb des Rechners, Wiederherstellung getestet | Sicherung und Wiederherstellung |

## Was Sie nicht tun sollten

- Setzen Sie auf einer Live-Website nicht `CheckValidity=true` oder `AllowWizardOnInstalledSite=enabled`.
- Erweitern Sie `TrustedProxies[]` nicht auf ein Providernetz oder `0.0.0.0/0`
  ([13.10](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)).
- Fügen Sie der Richtlinie der Administration nicht `'unsafe-inline'` hinzu, um Berichte zum Schweigen zu bringen, ohne
  deren Quelle zu kennen.
- Führen Sie Cronjobs oder die Konsole nicht als root aus, außer für die dokumentierten Aufgaben.
- Geben Sie Kolleginnen und Kollegen nicht „für einen Moment“ die Rolle Administrator; legen Sie eine Rolle an.

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Kapitel 13: Sicherheitshärtung für den Produktivbetrieb](../../../installation/13-sicherheitshaertung.md)
- [Sicherheit und Audit: absichern und sehen, wer was getan hat](../../../anleitungen/sicherheit-und-audit.md)
- [Sicherheit für Hoster](../../hosting/sicherheit/README.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
