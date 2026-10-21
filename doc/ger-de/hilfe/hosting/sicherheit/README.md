---
title: Sicherheit für Hoster: Überblick
audience: hosting
category: hosting
order: 10
summary: Der Bereich Sicherheit der Hilfe für Hoster - was Exponential standardmäßig schützt, was ein Hoster auf dem Server hinzufügen muss, und eine Seite pro Thema, von Härtung, TLS und der Content Security Policy bis zur Abschottung zwischen Kunden, dem Prüfprotokoll, Compliance und Aufbewahrung.
keywords: Sicherheit, Hosting, Überblick, Härtung, TLS, HTTPS, CSP, request-shield, Request-Regeln, Administration, Passwörter, zweistufige Anmeldung, Prüfprotokoll, Sicherheitsupdates, Offenlegung, Abschottung, Mandanten, PHP-FPM-Pools, DSGVO, Aufbewahrung, security, hosting, overview, hardening, request rules, administration, passwords, two-factor, audit trail, security updates, disclosure, isolation, multi-tenant, PHP-FPM pools, GDPR, retention
type: help
---
# Sicherheit für Hoster: Überblick

Dieser Bereich ist für die Sicherheitsverantwortlichen, die Systemadministratoren und die Produktmanager eines Hosting-Anbieters, der
Exponential-Websites für Kunden betreibt. Er beantwortet zwei Fragen: was Exponential bereits von selbst tut, und was der Teil der Arbeit
des Hosters auf dem Server, im Panel und im Vertrag ist.

Exponential 6 ist so gebaut, dass es standardmäßig sicher ist: Eine frische Installation liefert nur eine feste Liste von Dateien aus,
sendet Sicherheitsheader, hält die Debug-Ausgabe aus, schützt Formulare mit Tokens, hasht Passwörter mit bcrypt, führt ein
manipulationssichtbares Prüfprotokoll und weigert sich, seinen Setup-Assistenten auf einer installierten Website auszuführen. Die
Begründung hinter jedem Standard steht auf [Sicherheitshärtung](../../sicherheitshaertung.md), und die Referenz Einstellung für
Einstellung ist [Kapitel 13 des Installationsbuchs](../../../installation/13-sicherheitshaertung.md). Die Seiten hier nehmen die Sicht des
Hosters ein: viele Websites, viele Kunden, ein Team.

Zurück zu [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md).

## Inhalt

1. [Seiten in diesem Bereich](#seiten-in-diesem-bereich)
2. [Wer was tut](#wer-was-tut)
3. [Wo Sie beginnen](#wo-sie-beginnen)
4. [Verwandte Seiten](#verwandte-seiten)

## Seiten in diesem Bereich

| Seite | Was sie beantwortet |
|---|---|
| [Härtung für Hoster](haertung-fuer-hoster.md) | die Härtung, die ein Hoster auf jede Exponential-Website anwendet, Schicht für Schicht |
| [TLS und HTTPS](tls-und-https.md) | HTTPS auf jedem Namen, HSTS, sichere Cookies und Proxys, die TLS beenden |
| [Content Security Policy](content-security-policy.md) | die mitgelieferte Richtlinie im Berichtsmodus, die Meldungen und das Umstellen eines Siteaccess auf Durchsetzung |
| [request-shield und Request-Regeln](request-shield-und-request-regeln.md) | die Firewall vor dem Kernel, der Request-Klassifizierer des Kernels und seine Request-Regeln |
| [Die Administration einschränken](die-administration-einschraenken.md) | ein eigener Hostname, Einschränkungen im Netzwerk, geringste Rechte und `ManagedByDeployment` |
| [Erste Anmeldung und Standardpasswörter](erste-anmeldung-und-standardpasswoerter.md) | was jeder Installer als Administratorpasswort setzt und was ein Hoster nach der Bereitstellung tut |
| [Demomodus für Hoster](demomodus-fuer-hoster.md) | eine öffentliche Demo mit `EXP_DEMO_MODE=1` betreiben und sie von jeder Produktions-Website fernhalten |
| [Zweistufige Anmeldung](zweistufige-anmeldung.md) | die Erweiterung sevenx_authentication_2fa: einschalten, verlangen, verlorene Telefone |
| [Prüfprotokoll](pruefprotokoll.md) | was festgehalten wird, die Hash-Kette prüfen, Alarme, Senken und Schlüssel |
| [Sicherheitsupdates und Offenlegung](sicherheitsupdates-und-offenlegung.md) | unterstützte Versionen, Releases verfolgen, aktualisieren und wie Sie eine Schwachstelle melden |
| [Trennung von Mandanten](trennung-von-mandanten.md) | wie weit die Website eines Kunden von der eines anderen getrennt ist und welche Einrichtung welche Grenze gibt |
| [Benutzer und Pools pro Kunde](benutzer-und-pools-pro-kunde.md) | ein Benutzer des Betriebssystems, ein PHP-FPM-Pool oder eine Velocity-Instanz, ein Datenbankbenutzer pro Kunde |
| [Compliance und DSGVO](compliance-und-dsgvo.md) | was die Software für Datenschutz und E-Mail-Recht bereitstellt und was bei Hoster und Kunde bleibt |
| [Aufbewahrung von Logs und Daten](aufbewahrung-von-logs-und-daten.md) | wie lange jedes Log und jede Aufzeichnung behalten wird und die Einstellungen, die das ändern |

## Wer was tut

Sicherheit wird zwischen der Software, dem Hoster und dem Kunden geteilt. Eine klare Aufteilung vermeidet Lücken, von denen jede Seite
annimmt, die andere decke sie ab.

| Bereich | Exponential stellt bereit | Der Hoster stellt bereit | Der Kunde entscheidet |
|---|---|---|---|
| Ausgelieferte Dateien | die Rewrite-Regeln aus `.htaccess_root` und die Liste von Velocity | eine Konfiguration des Webservers, die sie einhält | nichts, es sei denn, er bringt eigene Regeln mit |
| Transport | HSTS und Zertifikatsverwaltung in Velocity; sichere Cookies | Zertifikate, Erneuerung, Umleitungen, die Stelle, die TLS beendet | welche Hostnamen es gibt |
| Konten | bcrypt-Hashes, Passwortregeln, die Prüfung auf schwache Passwörter, zweistufige Anmeldung | einen Ablauf der Bereitstellung, der nie ein bekanntes Passwort hinterlässt | wer welche Rolle bekommt |
| Abschottung | nichts zwischen Installationen: das ist Aufgabe des Betriebssystems | ein Benutzer, ein Pool, ein Datenbankbenutzer pro Kunde | nichts |
| Protokollierung | `security.log`, `auth.log`, `csp.log`, das Prüfprotokoll mit Hash-Ketten | Speicherplatz, Weiterleitung von der Maschine, Aufbewahrung in Backups | Aufbewahrungswerte, die seine Datenschutzerklärung nennt |
| Updates | unveränderliche Releases, Changelogs, die Upgrade-Prüfung | eine Routine, die sie einspielt | Freigabe eines Wartungsfensters, wo der Vertrag es verlangt |

> **Hinweis**
> Diese Aufteilung ist ein Ausgangspunkt für Ihre eigene Leistungsbeschreibung, keine Aussage darüber, was ein Vertrag sagen muss. Tarife,
> SLAs und Support-Stufen sind Optionen, die ein Hoster wählt; siehe [Das Hosting-Geschäft](../geschaeft/README.md).

## Wo Sie beginnen

Für einen Hoster, der sein erstes Angebot für Exponential einrichtet:

1. Lesen Sie [Härtung für Hoster](haertung-fuer-hoster.md) und machen Sie daraus Ihre Server-Vorlage.
2. Entscheiden Sie das Modell der Abschottung mit [Trennung von Mandanten](trennung-von-mandanten.md) und
   [Benutzer und Pools pro Kunde](benutzer-und-pools-pro-kunde.md).
3. Machen Sie die Bereitstellung sicher: [Erste Anmeldung und Standardpasswörter](erste-anmeldung-und-standardpasswoerter.md).
4. Machen Sie das Beobachten möglich: [Prüfprotokoll](pruefprotokoll.md) und das [Sicherheitslog](../betrieb/sicherheitslog.md).
5. Schreiben Sie die Routine für Updates: [Sicherheitsupdates und Offenlegung](sicherheitsupdates-und-offenlegung.md).
6. Vor dem Go-Live und jedes Quartal gehen Sie die [Checkliste für die Sicherheitsprüfung](../checklisten/checkliste-fuer-die-sicherheitspruefung.md) durch.

Wenn bereits etwas schiefgegangen ist, ist das Runbook [Kompromittierte Website](../runbooks/kompromittierte-website.md) die Seite, die Sie öffnen.

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Kapitel 13: Sicherheitshärtung für die Produktion](../../../installation/13-sicherheitshaertung.md)
- [Checkliste für die Sicherheitsprüfung](../checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Kompromittierte Website](../runbooks/kompromittierte-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
