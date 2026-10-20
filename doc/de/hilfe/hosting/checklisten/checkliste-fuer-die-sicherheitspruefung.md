---
title: Checkliste für die Sicherheitsprüfung
audience: hosting, admin
category: hosting
order: 50
summary: Eine Sicherheitsprüfung, die ein Hoster für eine Exponential-Website vor dem Go-Live, nach einem Vorfall und einmal im Quartal durchführt - Versionen, Konten, Einstellungen, ausgelieferte Dateien, Header und CSP, Anfrage-Logs, Abschottung, Geheimnisse, Backups und der Weg zur Offenlegung.
keywords: Sicherheitsprüfung, Checkliste, Audit, Härtung, Versionen, Konten, Passwörter, zweistufige Anmeldung, Debug, ausgelieferte Dateien, Rewrite-Regeln, CSP, Header, TLS, security.log, request-shield, Abschottung, Geheimnisse, Backups, SECURITY.md, security review, checklist, audit, hardening, accounts, passwords, two-factor, served files, rewrite rules, headers, isolation, secrets
type: help
---
# Checkliste für die Sicherheitsprüfung

Diese Checkliste ist für den Sicherheitsprüfer oder Systemadministrator des Hosters. Nutzen Sie sie, bevor eine Website live
geht, nach einem Sicherheitsvorfall und mindestens einmal im Quartal. Die Gründe für jeden Punkt stehen auf
[Sicherheitshärtung](../../../../en/us/help/security-hardening.md) und in
[Kapitel 13 des Installationsbuchs](../../../../en/us/install/13-security-hardening.md); die Sicht des Hosters steht auf
[Härtung für Hoster](../sicherheit/haertung-fuer-hoster.md).

```text
Site: <domain>        Reviewer: <name>        Date: <date>        Reason: <go-live | incident | quarterly>
```

## Versionen

- [ ] Exponential ist auf dem neuesten Release 6.0.x, oder der Abstand ist mit einem Datum zum Schließen dokumentiert. Nur
      die Linie 6.0 erhält Sicherheitskorrekturen (`SECURITY.md`).
- [ ] Erweiterungen sind auf aktuellen Releases.
- [ ] PHP und das Betriebssystem erhalten Sicherheitsupdates.

## Konten

- [ ] Kein Standard- oder gemeinsames Administratorpasswort ([Das Standard-Administratorpasswort](../../../../en/us/help/default-administrator-password.md)).
- [ ] Administratoren nutzen die zweistufige Anmeldung, wo der Kunde zugestimmt hat ([Zweistufige Anmeldung](../sicherheit/zweistufige-anmeldung.md)).
- [ ] Jedes Konto mit Rechten in der Administration gehört einer benannten Person, die es noch braucht.
- [ ] Anonymous hat keine Richtlinien über das hinaus, was die öffentliche Website braucht.

## Einstellungen und ausgelieferte Dateien

- [ ] Die Debug-Ausgabe ist auf öffentlichen Siteaccesses aus.
- [ ] Nur die Asset-Pfade werden als Dateien ausgeliefert; `settings/`, `var/log`, `var/cache` und Quelldateien sind aus dem
      Web nicht erreichbar. Testen Sie es: Eine Anfrage nach einer Datei unter `settings/` darf sie nicht liefern.
- [ ] Der Setup-Assistent bleibt geschlossen: `settings/override/site.ini.append.php` hat `[SiteAccessSettings] CheckValidity=false`
      ([Kapitel 13](../../../../en/us/install/13-security-hardening.md), [Erste Anmeldung und Standardpasswörter](../sicherheit/erste-anmeldung-und-standardpasswoerter.md)).
- [ ] Uploads werden unter `var/` gespeichert und nicht als PHP ausgeführt.

## Transport und Header

- [ ] Überall HTTPS, Zertifikat gültig, HTTP leitet um ([TLS und HTTPS](../sicherheit/tls-und-https.md)).
- [ ] Die Content Security Policy wird in der Administration durchgesetzt und erzeugt keine unerwarteten Meldungen
      ([Content Security Policy](../sicherheit/content-security-policy.md)).
- [ ] Die Administration ist wie vereinbart über das Netzwerk oder einen zweiten Faktor eingeschränkt
      ([Die Administration einschränken](../sicherheit/die-administration-einschraenken.md)).

## Logs und Erkennung

- [ ] `security.log` wird geschrieben und durchgesehen ([Sicherheitslog](../betrieb/sicherheitslog.md)).
- [ ] Hinter einem Proxy oder Load Balancer ist `TrustedProxies[]` konfiguriert, sodass die Logs die echten
      Client-Adressen sehen ([Sicherheitshärtung](../../../../en/us/help/security-hardening.md)).
- [ ] Über einen Schutz vor der Website entschieden: request-shield oder die Request-Regeln des Kernels
      ([request-shield und Request-Regeln](../sicherheit/request-shield-und-request-regeln.md)).
- [ ] Das Prüfprotokoll ist an und für den Prüfer lesbar ([Prüfprotokoll](../sicherheit/pruefprotokoll.md)).

## Abschottung und Geheimnisse

- [ ] Die Website läuft als eigener Systembenutzer, mit eigenem PHP-FPM-Pool oder eigener Velocity-Instanz
      ([Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)).
- [ ] `settings/override` und Backups, die es enthalten, sind nur für den Benutzer der Website und das Backup-System lesbar.
- [ ] Keine Geheimnisse in Git-Repositories, Tickets oder geteilten Ordnern.
- [ ] Der Datenbankbenutzer der Website erreicht nur die Datenbank der Website.

## Backups und Wiederherstellung

- [ ] Backups sind außerhalb der Maschine gespeichert und können nicht aus dem Konto der Website gelöscht werden.
- [ ] Eine Wiederherstellung wurde innerhalb des vereinbarten Zeitraums getestet ([Wiederherstellung und Wiederherstellungstests](../betrieb/wiederherstellung-und-wiederherstellungstests.md)).

## Offenlegung

- [ ] Ihr Team weiß, wie es eine Schwachstelle in Exponential vertraulich meldet, wie `SECURITY.md` es beschreibt
      ([Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)).
- [ ] Ihr eigenes Verfahren für Vorfälle ist aktuell ([Kompromittierte Website](../runbooks/kompromittierte-website.md)).

**Demomodus.**

- [ ] `grep -r EXP_DEMO_MODE` über die PHP-FPM-Pools, virtuellen Hosts, `.htaccess`-Dateien, systemd-Units und Drop-ins, Crontabs, `/etc/environment` und Container-Definitionen findet nur die Konfiguration öffentlicher Demos; keine Produktions-Website hat eine Zeile `demo/mode` in `security.log` oder ein Audit-Ereignis `system.demo.mode` ([Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md)).

## Verwandte Seiten

- [Zusammenfassung der Sicherheitsprüfung für Führungskräfte](../../../../en/us/help/leadership/accessibility-security-and-compliance-checklists.md)
- [Checkliste für den monatlichen Betrieb](checkliste-fuer-den-monatlichen-betrieb.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
