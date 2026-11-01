---
title: "Migrationen in der Website-Seitenleiste"
audience: admin, developer
category: administration
order: 4
summary: Das schreibgeschützte Panel Migrationen der Website-Seitenleiste (expwt) auf den Seiten der Site - der laufende Lauf, die fehlgeschlagenen und ausstehenden Migrationen, die verwaisten und der letzte Lauf - und wie es zu Setup > Migrationen führt.
keywords: Migrationen, Website-Seitenleiste, expwt, Panel Migrationen, ausstehende Migrationen, fehlgeschlagene Migrationen, letzter Lauf, schreibgeschützt, website sidebar, migrations panel, migration/read, expwt/use
type: help
---
# Migrationen in der Website-Seitenleiste

Die Website-Seitenleiste (die Erweiterung expwt) hat ein Panel **Migrationen**: der Stand der Migrationen der
Installation auf jeder Seite der Site, ohne in die Administration zu wechseln. Es zeigt nur an. Ausführen, Fortsetzen
und Zurücknehmen bleiben in **Setup > Migrationen**
([Ausführen, Probelauf und Rücknahme](ausfuehren-probelauf-und-rueckgaengig.md)): Die Seitenleiste hat hier keine
Schaltfläche, die etwas ändert.

**Wer das Panel sieht:** Benutzer, die die Seitenleiste nutzen dürfen (`expwt/use`) und `migration/read` haben. Ohne
diese Richtlinie oder auf einem Kernel ohne Migrationen gibt es keinen Reiter Migrationen.

## Was das Panel zeigt

- **Einen gerade aktiven Lauf**: die Sperre der Installation, was er tut, wer ihn gestartet hat und seit wann;
- die **fehlgeschlagenen** Migrationen, jede mit Quelle und Beschreibung;
- die **ausstehenden** Migrationen (Kennung, Quelle, Beschreibung; die ersten 50, der Rest gezählt);
- die Migrationen, die **aufgezeichnet sind, deren Datei aber fehlt** (die verwaisten des Stands);
- den **letzten Lauf**: seinen Stand, wann er begann und endete, wer ihn ausgeführt hat, seinen Sicherungspunkt, Tag
  und Push-Stand und die Meldung eines fehlgeschlagenen Laufs.

Jeder Eintrag hat **In der Administration öffnen**: den Stand oder die Seite seines Laufs
([Stand und Verlauf](status-und-verlauf.md)).

Die Zahl der ausstehenden, fehlgeschlagenen und ohne Datei aufgezeichneten Migrationen steht als Zähler am Reiter des
Panels und wird höchstens einmal pro Minute ermittelt.

## Verwandte Seiten

- [Migrationen in der Administration](README.md)
- [Stand und Verlauf](status-und-verlauf.md)
- [Die Website-Seitenleiste](../../funktionen/6.0/website-seitenleiste.md)
- [Migrationen: die Anleitung](../../anleitungen/migrationen.md)
