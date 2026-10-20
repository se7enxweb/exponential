---
title: Hosting-Partner
audience: partner
category: partnership
order: 380
summary: Für Hosting-Partner - was das Profil bedeutet, die Hosting-Optionen, die das Programm nennt, was ein Exponential-Hoster bereitstellen muss (PHP, Datenbanken, Engines, Backups, Sicherheit), Dienstleistungen, die Sie anbieten können, und wie Hosting-Partner etwas zurückgeben.
keywords: Hosting-Partner, Hosting, Managed Hosting, PHP, Datenbanken, Velocity, PHP-FPM, FrankenPHP, Backups, Sicherheit, Verfügbarkeit, Infrastruktur, hosting partner, managed hosting, databases, security, uptime, infrastructure
type: help
---
# Hosting-Partner

Diese Seite ist für Partner mit dem Profil Hosting. „Hosting-Partner stellen die sichere, skalierbare Infrastruktur bereit, die für Exponential-Anwendungen unverzichtbar ist“ und „stellen die Infrastruktur für das Hosting von Lösungen auf Grundlage von Exponential bereit. Sie haben Erfahrung darin, die optimale Umgebung für eine bestimmte Lösung zu gestalten“ (<https://exponential.earth/partner/hosting-partners>, <https://exponential.earth/partner/program>, gelesen am 18. Oktober 2026).

## Hosting-Optionen, die das Programm nennt

Die Seite der Hosting-Partner führt „7x Premium Hosting“ auf und, als „alternative Hosting-Anbieter, von denen bekannt ist, dass sie gut funktionieren. Beachten Sie, dass niemand 7x für die Platzierung bezahlt hat“: Hosting.com („früher a2hosting.com“) und Amazon AWS. Ein
Hosting-Partner, der dem Programm beitritt, ergänzt diese Landschaft um sein eigenes Angebot.

## Was ein Exponential-Hoster bereitstellen muss

| Bereich | Anforderung | Quelle |
|---|---|---|
| PHP | 8.0 bis 8.5 für die Linie 6.0.15; Velocity braucht 8.1 oder später | `doc/en/us/install/02-requirements.md` |
| PHP-Erweiterungen | Unter anderem `dom`, `xsl`, `mbstring`, `iconv`, `intl`, `zlib` und eine Datenbankerweiterung | ebenda |
| php.ini | `memory_limit` 64M oder mehr, `max_execution_time` 30, `date.timezone` gesetzt, `file_uploads` an | ebenda |
| Datenbank | SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle oder MySQL, in dieser Reihenfolge der Präferenz | [Datenbankunterstützung](../../../en/us/help/database-support.md) |
| Betriebssystem | Siehe die Stufen | [Betriebssystemunterstützung](../../../en/us/help/os-support.md) |
| Engine | Velocity (empfohlen), FrankenPHP oder ein Webserver mit PHP-FPM | [Wer ist die Exponential Foundation?](../../../en/us/help/who-is-the-exponential-foundation.md#exponential-velocity) |
| Kommandozeile | PHP-CLI mit derselben Version und denselben Erweiterungen wie das Web-PHP, für Cronjobs und Skripte | `doc/en/us/install/02-requirements.md` |
| Sicherheit | TLS, Sicherheits-Header, Trennung der Benutzer, kein offen erreichbarer Installer | [Sicherheitshärtung](../../../en/us/help/security-hardening.md) |
| Backups | Datenbank und Speicher, Wiederherstellung getestet | [Datenbankunterstützung](../../../en/us/help/database-support.md#backup-and-restore) |

Die ausführliche Checkliste: [Technische Checkliste für Hosting-Partner](technische-checkliste-fuer-hosting-partner.md).

## Dienstleistungen, die Sie anbieten können

- Managed Hosting für Exponential mit eingespielten Updates und Sicherheitskorrekturen.
- Staging-Umgebungen für Release-Tests ([Releases testen](releases-testen.md)).
- Backups mit getesteten Wiederherstellungen.
- Leistungsoptimierung: Caches, Engine, Datenbank.
- Migration bestehender Websites auf aktuelles PHP.

## Mit anderen Partnern zusammenarbeiten

Hosting-Partner betreuen oft die Kunden von Implementierungs- und Kreativpartnern. Vereinbaren Sie schriftlich, wer
wofür zuständig ist: Server und PHP (Hoster), Anwendung und ihre Erweiterungen (Implementierungspartner), Inhalte
(Kunde).

## Etwas zurückgeben

- Melden Sie umgebungsspezifische Fehler mit genauen Versionen ([Fehler gut melden](fehler-gut-melden.md)).
- Dokumentieren Sie als Tutorial, was auf Ihrer Plattform funktioniert (Betriebssystem, Control Panel, Container); das
  Projekt pflegt eine Seite [Betriebssystemunterstützung](../../../en/us/help/os-support.md), die mit solchen Berichten wächst.

## Verwandte Seiten

- [Technische Checkliste für Hosting-Partner](technische-checkliste-fuer-hosting-partner.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../../en/us/help/leadership/choosing-hosting-database-and-os.md)
- [Partnerprofile](partnerprofile.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
