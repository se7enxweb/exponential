---
title: Checkliste zum Start für Anwender
audience: adopter, project-manager, admin, customer
category: adoption
order: 51
summary: Eine Checkliste zum Start für Anwender von Exponential - Inhalte und URLs, Konten und Rollen, Sicherheitshärtung, Sicherungen und Wiederherstellung, Caches und Geschwindigkeit, Überwachung, Barrierefreiheit, rechtliche Seiten und die menschliche Seite - vor dem Go-live und am Tag selbst.
keywords: Checkliste zum Start, Go-live, Sicherheitshärtung, Sicherungen, Wiederherstellung, Caches, Überwachung, Barrierefreiheit, Konten, Rollen, Demomodus, launch checklist, go-live, security hardening, backups, restore, caches, monitoring, accessibility, accounts, roles, demo mode
type: help
---
# Checkliste zum Start für Anwender

## Inhalt

1. [Vor dem Go-live](#vor-dem-go-live)
2. [Am Tag selbst](#am-tag-selbst)
3. [Die erste Woche](#die-erste-woche)

## Vor dem Go-live

```text
Inhalte und URLs
[ ] Inhalte migriert und abgenommen (Checkliste zur Inhaltsmigration)
[ ] Alte Adressen leiten weiter; keine wichtigen 404-Fehler

Konten und Rollen
[ ] Standard- und Testkonten entfernt oder deaktiviert; Administratorpasswort geändert
[ ] Rollen und Rechte für jede Gruppe gesetzt (Rollen und Rechte für die Einführung)
[ ] Zweistufige Anmeldung für Administratoren, falls verwendet

Sicherheit
[ ] Härtung angewendet (Sicherheitshärtung, Kapitel 13 des Installationsbuchs)
[ ] PHP und Exponential in unterstützten Versionen
[ ] Kein Betrieb im Demomodus

Betrieb
[ ] Sicherungen eingeplant UND eine Wiederherstellung getestet
[ ] Überwachung von Erreichbarkeit und Fehlern eingerichtet
[ ] Caches konfiguriert; Geschwindigkeit unter erwarteter Last geprüft
[ ] Wartungsmodus getestet

Menschen
[ ] Redakteure kurz vor dem Start geschult
[ ] Supportkontakte und Eskalation bekannt
[ ] Verantwortliche haben die Website abgenommen
```

Seiten: [Sicherheitshärtung](../../sicherheitshaertung.md), [Das Administratorpasswort ändern](../../administratorpasswort-aendern.md),
[Standard-Administratorpasswort](../../standard-administratorpasswort.md), [Demomodus](../../demomodus.md),
[Sicherungen](../../kunden/gesund-halten/backups.md), [Test der Wiederherstellung](../../kunden/gesund-halten/wiederherstellungstest.md),
[Überwachung](../../kunden/gesund-halten/ueberwachung.md), [Geschwindigkeit und Caching](../../kunden/gesund-halten/geschwindigkeit-und-caching.md).

## Am Tag selbst

```text
[ ] Inhaltsstopp aktiv; letzter Migrationslauf
[ ] Rauchtest: Startseite, eine Seite pro Inhaltstyp, Suche, ein Formular, Anmeldung, Kasse
[ ] Fehlerlog ohne neue Fehler
[ ] DNS oder Proxy umgestellt; HTTPS gültig
[ ] Ankündigung an Redakteure und Benutzer
```

## Die erste Woche

```text
[ ] Fehlerlog und 404-Meldungen jeden Tag gelesen
[ ] Fragen der Redakteure gesammelt und beantwortet
[ ] Erste Sicherung nach dem Start auf einem Testsystem wiederhergestellt
[ ] Alte Website bis zur Abnahme schreibgeschützt behalten
```

## Verwandte Seiten

- [Starten](README.md)
- [Checkliste für den Go-live](../../unternehmen/vorlagen/checkliste-go-live.md)
- [Sicherheitscheckliste für Betreiber](../../kunden/gesund-halten/sicherheits-checkliste-fuer-betreiber.md)
- [Checkliste zur Inhaltsmigration](../umziehen/checkliste-inhaltsmigration.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
