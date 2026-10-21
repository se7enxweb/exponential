---
title: Den Host härten
audience: government, hosting, admin
category: public-sector
order: 110
summary: Für IT- und Sicherheitsteams im öffentlichen Sektor, die einen Server für eine Exponential-Website vorbereiten. Die Ebenen der Härtung, die die Software vom Host erwartet, die Prüfungen, die sie belegen, und wie Sie sie einer Sicherheitsgrundlinie oder einer internen Bewertung zuordnen.
keywords: Härtung, Sicherheitsgrundlinie, Serverhärtung, Dateiberechtigungen, EZP_DIR_MODE_MAX, EZP_FILE_MODE_MAX, ausgelieferte Dateien, Geheimnisse, Debug-Ausgabe, Sicherheitsheader, HTTPS, HSTS, TLS, OWASP, Bewertung, NIS2, hardening, security baseline, server hardening, file permissions, served files, secrets, debug output, security headers, assessment
type: help
---
# Den Host härten

Diese Seite richtet sich an die Infrastruktur- und Sicherheitsteams einer öffentlichen Stelle, die den Server einer
Exponential-Website vorbereiten und bewerten. Sie beantwortet, welche Härtung die Software vom Host erwartet, wie Sie
jede Ebene prüfen und wie Sie das Ergebnis in einer Sicherheitsbewertung darstellen. Die Referenz ist
[Kapitel 13: Sicherheitshärtung](../../../../installation/13-sicherheitshaertung.md); die Seite der Anwendung behandeln
[Sicherheitshärtung](../../../sicherheitshaertung.md) und [Sicherheitsstandards](../../../../../eng-us/standards/security.md).
Hoster haben ihre eigene Fassung: [Härtung für Hoster](../../../hosting/sicherheit/haertung-fuer-hoster.md).

## Inhalt

1. [Standardmäßig sicher, von Ihnen geprüft](#standardmäßig-sicher-von-ihnen-geprüft)
2. [Die Ebenen und ihre Prüfungen](#die-ebenen-und-ihre-prüfungen)
3. [Dateimodi und Eigentümer](#dateimodi-und-eigentümer)
4. [Geheimnisse auf dem Server](#geheimnisse-auf-dem-server)
5. [Zuordnung zu Ihrer Sicherheitsgrundlinie](#zuordnung-zu-ihrer-sicherheitsgrundlinie)
6. [Checkliste](#checkliste)

## Standardmäßig sicher, von Ihnen geprüft

Seit September 2026 sendet eine Standardinstallation Sicherheitsheader, verwendet Sitzungscookies mit `HttpOnly` und
`SameSite` und antwortet mit 404 auf Punktdateien und PHP-Dateien unterhalb des Dokumentwurzelverzeichnisses
([Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)). Prüfen Sie das, statt sich darauf zu verlassen; die
Prüfungen unten dauern wenige Minuten, und ihre Ausgabe ist ein guter Nachweis für eine Bewertungsakte.

## Die Ebenen und ihre Prüfungen

| Ebene | Was zu prüfen ist | Befehl oder Quelle |
|---|---|---|
| Ausgelieferte Dateien | `.env`, `.git/config` und PHP-Dateien von Erweiterungen antworten mit 404 | die `curl`-Schleife unten; [13.2](../../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf) |
| Sicherheitsheader | fünf Header auf jeder Seite | `curl -sI`; [13.9](../../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https) |
| HTTPS | jeder Name über HTTPS ausgeliefert; HSTS nur, wenn das dauerhaft ist | [TLS und HTTPS](../../../hosting/sicherheit/tls-und-https.md) |
| Debug-Ausgabe | ausgeschaltet oder nur für aufgeführte Adressen mit Ablaufdatum | [13.4](../../../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige) |
| Die Administration | eigener Hostname und Netzwerkbeschränkung | [Die Administration von der öffentlichen Website trennen](administrations-und-oeffentliche-siteaccesses-trennen.md) |
| Der Einrichtungsassistent | geschlossen: `CheckValidity=false` in `settings/override/site.ini.append.php` | [13.5](../../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess) |
| Velocity | Worker nicht als root, Passwort des Panels gesetzt, Dashboard nicht aus der Ferne erreichbar | [13.12](../../../../installation/13-sicherheitshaertung.md#1312-velocity-spezifische-härtung) |
| Auf dem neuesten Stand bleiben | Sicherheits-Releases eingespielt | [Sicherheitsupdates und Offenlegung](../aktualisierungen-und-aenderungen/sicherheitsaktualisierungen-und-offenlegung.md) |

```bash
curl -sI https://www.example.gov/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
for p in .env .git/config extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" https://www.example.gov/$p
done
```

Erwartet: die fünf Header (`x-content-type-options`, `x-frame-options`, `content-security-policy`,
`referrer-policy`, `permissions-policy`) und dreimal `404` ([Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)).

## Dateimodi und Eigentümer

Die Website legt Logs, Caches und Speicher so offen an, wie `site.ini [FileSettings]` und der Code es verlangen,
standardmäßig 0777 und 0666. Setzen Sie auf einem Produktionsserver Grenzen in `config.php`, damit nichts Neues für
andere Konten lesbar oder beschreibbar ist:

```php
define( 'EZP_DIR_MODE_MAX', 0750 );
define( 'EZP_FILE_MODE_MAX', 0640 );
```

Schränken Sie dann einmalig ein, was bereits vorhanden ist ([Dateimodi](../../../../bc/6.0/dateimodi.md)). Führen Sie
das PHP des Webservers, die Worker von Velocity, Cronjobs und Kommandozeilenskripte unter einem Benutzer aus oder geben
Sie ihnen eine gemeinsame Gruppe mit setgid-Verzeichnissen; zwei Benutzer, die dieselben Dateien schreiben, sind die
häufigste Ursache seltsamer Fehler
([Beschreibbare Verzeichnisse und Berechtigungen](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).
Prüfen Sie nach jedem als root ausgeführten Befehl den Eigentümer neuer Dateien in `var/`.

## Geheimnisse auf dem Server

`settings/override/` enthält das Datenbankpasswort, die Signatur- und Pseudonymschlüssel des Audit-Trails und das
Geheimnis für die E-Mail-Präferenzen ([13.3](../../../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride)).
Beschränken Sie, wer es auf dem Server lesen kann, halten Sie es aus der Versionsverwaltung heraus oder verschlüsseln
Sie es dort, und behandeln Sie Datensicherungen, die es enthalten, als vertraulich (siehe
[Schlüssel und Geheimnisse in Datensicherungen](../datensicherung-und-wiederherstellung/schluessel-und-geheimnisse-in-datensicherungen.md)). Velocity
bewahrt private Schlüssel von Zertifikaten in seinem Konfigurationsbaum mit dem Modus `0600` auf.

## Zuordnung zu Ihrer Sicherheitsgrundlinie

Öffentliche Stellen bewerten Anwendungen oft anhand einer nationalen Grundlinie, eines Maßnahmenkatalogs nach
ISO/IEC 27001 ([ISO/IEC 27001](https://www.iso.org/standard/27001)), des
[NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) oder, in der EU, anhand von Maßnahmen nach der
[NIS2-Richtlinie](https://eur-lex.europa.eu/eli/dir/2022/2555/oj). Exponential beansprucht keine Zertifizierung nach
einem davon. Was hilft:

- [Sicherheitsstandards](../../../../../eng-us/standards/security.md) ordnet die Software den
  [OWASP Top 10](https://owasp.org/Top10/) und dem [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/)
  zu, mit ehrlichen Statusangaben „Teilweise erfüllt“ und „Nicht geprüft“ und den bekannten Lücken (zum Beispiel die
  standardmäßig ausgeschaltete Kontosperre und eine Mindestlänge für Passwörter von 10, unter den 12 der ASVS-Stufe 1).
- Die Prüfungen auf dieser Seite erzeugen Nachweise, die Sie der Bewertung beilegen können.
- Der Audit-Trail zeichnet Schreibvorgänge an Einstellungen, das Leeren von Caches und Bereitstellungen auf, was
  Änderungs- und Zugriffskontrollen unterstützt ([Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md)).

## Checkliste

- [ ] Die Prüfungen der ausgelieferten Dateien und der Header sind bestanden, die Ausgabe ist abgelegt.
- [ ] Überall HTTPS; über HSTS bewusst entschieden.
- [ ] Debug-Ausgabe in der Produktion ausgeschaltet.
- [ ] `EZP_DIR_MODE_MAX` und `EZP_FILE_MODE_MAX` gesetzt; ein Benutzer oder eine Gruppe schreibt `var/`.
- [ ] `settings/override/` ist nur für den Benutzer der Website und Administratoren lesbar.
- [ ] Der Einrichtungsassistent ist geschlossen und die Administration beschränkt.
- [ ] Die in [Sicherheitsstandards](../../../../../eng-us/standards/security.md) aufgeführten Lücken sind geprüft und entschieden.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Checkliste für die Sicherheitsprüfung](../../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Request-Shield und Anfrageregeln](../../../hosting/sicherheit/request-shield-und-request-regeln.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
