---
title: Reaktion auf Sicherheitsvorfälle bei einer Exponential-Website
audience: government, admin, project-manager, hosting
category: public-sector
order: 400
summary: Für öffentliche Stellen, die einen Sicherheitsvorfall auf ihrer Website bearbeiten - eindämmen, Beweise sichern, mit dem Audit-Protokoll und den Protokollen untersuchen, aus bekannt guten Quellen neu aufbauen, Zugangsdaten und Sitzungen erneuern, melden und eine Wiederholung verhindern, auf Grundlage des Hosting-Runbooks des Projekts.
keywords: Reaktion auf Sicherheitsvorfälle, Sicherheitsvorfall, Kompromittierung, Defacement, Eindämmung, Beweissicherung, Audit-Protokoll, Wartungsmodus, Neuaufbau, Zugangsdaten, Sitzungen, Meldung, Datenpanne, Lehren, öffentlicher Sektor, incident response, security incident, compromise, defacement, containment, evidence, audit trail, security.log, maintenance mode, rebuild, credentials, sessions, notification, breach, lessons learned, public sector
type: help
---
# Reaktion auf Sicherheitsvorfälle bei einer Exponential-Website

Diese Seite richtet sich an Administratorinnen und Administratoren, Sicherheitsbeauftragte und Projektverantwortliche
öffentlicher Stellen. Sie überträgt das Hosting-Runbook des Projekts
([Runbook: kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)) auf die Website einer öffentlichen
Stelle. Anzeichen eines Vorfalls: verunstaltete Seiten, unbekannte Administratorkonten, Dateien, die es nicht geben
sollte, von der Website versandter Spam, ein Audit-Alarm wie `admin_role_granted` oder `chain_broken`.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Eindämmen](#eindämmen)
2. [Beweise sichern](#beweise-sichern)
3. [Untersuchen](#untersuchen)
4. [Neu aufbauen und wiederherstellen](#neu-aufbauen-und-wiederherstellen)
5. [Melden](#melden)
6. [Eine Wiederholung verhindern](#eine-wiederholung-verhindern)
7. [Verwandte Seiten](#verwandte-seiten)

## Eindämmen

1. Nehmen Sie die öffentliche Website mit dem Wartungsmodus vom Netz und lassen Sie nur Ihre Adressen durch:
   `php bin/php/maintenance.php on --message="Maintenance" --allow-ip=<your address> --allow-root-user`
   ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)). Als öffentliche Stelle veröffentlichen Sie wesentliche
   Informationen währenddessen über einen anderen Kanal.
2. Schränken Sie die Administration auf Netzwerkebene ein, wenn ein Administratorkonto oder eine Sitzung in den Händen
   des Angreifers sein könnte.
3. Könnte der Rechner selbst kompromittiert sein, isolieren Sie ihn und arbeiten Sie mit einer Kopie.
4. Löschen Sie noch nichts.

## Beweise sichern

- Kopieren Sie die Dateien und einen Datenbank-Dump an einen Ort, den der Benutzer der Website nicht beschreiben kann.
- Kopieren Sie die Protokolle: `var/log/` (`error.log`, `security.log`, `auth.log`, das Audit-Verzeichnis), die
  Protokolle von Velocity, des Webservers und von PHP-FPM.
- Exportieren Sie die relevanten Audit-Einträge in einem signierten Bündel:
  `./console exp:audit export --from=<time> --format=bundle --out=<file> --allow-root-user`.
- Notieren Sie die Uhrzeit jeder Maßnahme, die Sie ergreifen.

## Untersuchen

| Frage | Wo |
|---|---|
| Wer hat sich angemeldet, Rollen, Einstellungen, Passwörter geändert? | Reiter Audit, Filter der Konsole; `./console exp:audit search` |
| Wurde das Audit selbst manipuliert? | `./console exp:audit verify`; mit der Kopie in Ihrem SIEM vergleichen |
| Welche Anfragen sahen nach Angriffen aus, und war eine davon erfolgreich? | `security.log` (Status 200 bei Injection oder Traversal) |
| Welche Dateien wurden geändert? | Dateikonsistenzprüfung; Vergleich mit dem Release-Tag |
| Wurden Dateien platziert? | `var/`, `settings/`; jede `.php` unter `var/storage` ist verdächtig |
| Welche Version lief? | `composer show se7enxweb/exponential`; mit späteren Release Notes vergleichen |

## Neu aufbauen und wiederherstellen

1. Code aus einer bekannt guten Quelle: das Release-Tag und `composer install` aus Ihrer Lock-Datei, Ihre
   Erweiterungen aus ihren Repositorys.
2. Daten aus der neuesten sauberen Sicherung, oder prüfen Sie die aktuelle Datenbank auf unbekannte Konten, Rollen und
   Inhalte.
3. Aktualisieren Sie auf die neueste 6.0.x, wenn die Lücke eine bekannte sein könnte.
4. Ändern Sie Zugangsdaten: Administratorpasswörter, Datenbankpasswort, API-Schlüssel und Tokens; wechseln Sie den
   Audit-Signaturschlüssel, wenn er gelesen worden sein könnte (`./console exp:audit key rotate --apply --allow-root-user`).
5. Beenden Sie Sitzungen: Passwortänderungen melden andere Sitzungen ab; entfernen Sie mit dem Datenbank-Session-Handler
   alle Sitzungen unter **Setup > Sitzungen**.
6. Leeren Sie alle Caches (`php bin/php/ezcache.php --clear-all --allow-root-user`; Velocity neu starten und seinen
   Antwort-Cache leeren), prüfen Sie und schalten Sie dann den Wartungsmodus aus.

## Melden

- Intern, wie es Ihr Verfahren für Sicherheitsvorfälle vorsieht (Sicherheitsbeauftragte, Datenschutzbeauftragte,
  Leitung).
- An Behörden und Betroffene, wo das Gesetz es verlangt, innerhalb seiner Fristen (zum Beispiel nach der DSGVO bei
  Verletzungen des Schutzes personenbezogener Daten oder nach nationalen Cybersicherheitsvorschriften).
- An das Exponential-Projekt, vertraulich, wenn eine Schwachstelle in Exponential oder einer Erweiterung die Ursache war
  (siehe „Offenlegung von Schwachstellen“).

## Eine Wiederholung verhindern

- Gehen Sie die Go-live-Checkliste und die Härtungsschichten erneut durch; machen Sie den Code für den Benutzer der
  Website schreibgeschützt.
- Leiten Sie das Audit-Protokoll vom Rechner weg; schalten Sie request-shield vor.
- Halten Sie eine Besprechung zu den gewonnenen Erkenntnissen ab und aktualisieren Sie dieses Verfahren.

## Verwandte Seiten

- [Runbook: kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)
- [Integrität und Prüfung des Audit-Protokolls](audit-integritaet-und-pruefung.md)
- [Sicherung und Wiederherstellung](sicherung-und-wiederherstellung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
