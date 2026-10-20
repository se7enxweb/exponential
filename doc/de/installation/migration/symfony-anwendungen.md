---
title: Symfony-Anwendungen und Exponential
audience: developer, admin, project-manager
category: install-upgrade
order: 50
summary: Inhalte zwischen einer eigenen Symfony-Anwendung und Exponential 6 übertragen – Doctrine-Entitäten zu Inhaltsklassen, ein Konsolenbefehl für den Export, eine Symfony-Anwendung neben Exponential betreiben, eine Distribution von Exponential Platform wählen, wenn das Team bei Symfony bleiben möchte, und der Rückweg.
keywords: Symfony, Doctrine, Entitäten, Konsolenbefehl, Twig, Bundle, Exponential Platform, Platform Legacy, Legacy Bridge, REST, Migration, entities, console command, bundle, legacy bridge, migration
type: install
---
# Symfony-Anwendungen und Exponential

Eine eigene Symfony-Anwendung hält ihre Inhalte in Doctrine-Entitäten (oder einem anderen Speicher) und rendert sie
mit Twig. Ein Wechsel zu Exponential ist eine Inhaltsmigration aus Ihrer eigenen Datenbank; bei Symfony zu bleiben und
dabei ein Content-Repository zu gewinnen, ist eine Frage der Wahl einer Distribution von Exponential Platform.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Zuordnung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## 1. Zuerst entscheiden: Exponential 6 oder eine Symfony-basierte Exponential Platform

| Ihr Team und Ihre Anwendung | Ziel |
|---|---|
| Die Anwendung besteht überwiegend aus Inhalten (Seiten, Artikel, Medien); das Team akzeptiert TPL und INI | Exponential 6, diese Anleitung |
| Das Team möchte bei Symfony und Twig bleiben und braucht ein Content-Repository | [Exponential Platform DXP](exponential-platform-dxp.md) (Symfony 7.4) oder [Nexus](exponential-platform-nexus.md) |
| Beides: Symfony-Controller für die Anwendung, die Legacy-Administration und TPL für die Inhalte | [Platform Legacy](exponential-platform-legacy.md) |
| Die Anwendung hat Geschäftslogik über Inhalte hinaus (Buchungen, Konten) | behalten Sie die Anwendung; übertragen Sie nur ihre Inhalte und verbinden Sie beide (REST, gemeinsame Anmeldung) |

## 2. Von einer Symfony-Anwendung zu Exponential 6

1. **Die Entitäten zuordnen**: Jede inhaltsartige Entität wird zu einer Klasse, ihre Spalten zu Attributen,
   Doctrine-Beziehungen zu Objektbeziehungen oder Positionen im Baum, Dateien (VichUploader, Flysystem, einfache Pfade) zu Bild- oder Dateiobjekten
   ([Zuordnung](inhaltsmodell-zuordnen.md)).
2. **Aus der Anwendung selbst exportieren**: ein Symfony-Konsolenbefehl in der Anwendung, der die Entitäten über Doctrine
   lädt (in Stapeln, wobei der Entity Manager zwischen den Stapeln geleert wird) und JSON-Zeilen mit Remote-IDs
   (`app-<entity>-<id>`), dem gerenderten oder rohen Rich Text und der Route jedes Elements schreibt (aus dem Router, damit die alten
   Adressen exakt stimmen).
3. **Importieren** Sie auf dem Exponential-Server mit dem [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)
   oder dem Importer von xrowextract.
4. **Twig zu TPL**: Die Templates werden neu geschrieben; [17.5](../17-migrationsreferenz.md#175-templates-tpl-und-twig) ordnet
   die Konstrukte einander zu.
5. **Benutzer**: Der `PasswordHasher` von Symfony mit den Algorithmen `auto`, `bcrypt` oder `sodium` schreibt
   Standardwerte von PHPs `password_hash()` (`$2y$...`, `$argon2id$...`), die Exponential als Typ 7 prüfen kann
   ([Zuordnung, 6](inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter)); testen Sie mit einigen Konten. Andere
   Hasher bedeuten Zurücksetzungen.
6. **Routen**: Jede Route, die Inhalte ausgeliefert hat, wird zu einem Knoten-Alias oder einer Weiterleitung.

## 3. Eine Symfony-Anwendung neben Exponential betreiben

Wenn die Anwendung ihre Funktionen jenseits der Inhalte behält:

- Liefern Sie beide unter einer Domain aus, wobei der Webserver die Pfade jeweils zuweist, oder unter Subdomains.
- Lassen Sie die Anwendung Inhalte über REST mit einem persönlichen API-Schlüssel aus Exponential lesen
  ([REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)).
- Anmeldung: Halten Sie sie getrennt, oder verwenden Sie für beide denselben Identity Provider.

## 4. Von Exponential zu einer Symfony-Anwendung

Exportieren Sie mit xrowextract nach JSON ([Migration weg von Exponential](exponential-verlassen.md)) und laden Sie es mit einem
Doctrine-Befehl; oder wechseln Sie als Symfony-Team zu einer Distribution von Exponential Platform mit demselben
Inhaltsmodell, die das Content-Repository und seine Werkzeuge behält.

## 5. Checkliste

- [ ] Ziel gewählt (Exponential 6, DXP, Nexus, Platform Legacy oder Anwendung plus Content-Site).
- [ ] Zuordnung der Entitäten geschrieben; Exportbefehl in der Anwendung; Routen aus dem Router exportiert.
- [ ] Mit Remote-IDs importiert; Templates neu geschrieben; Passwort-Hashes getestet.
- [ ] Weiterleitungen für jede Inhaltsroute; REST-Zugriff für die verbleibende Anwendung.

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
