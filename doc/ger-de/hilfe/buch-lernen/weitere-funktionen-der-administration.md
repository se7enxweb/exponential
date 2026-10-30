---
title: "Weitere Funktionen der Administration"
audience: admin, builder
category: books
order: 310
summary: Die Administratorfunktionen, die das Buch Learning nach den Inhalten aufzählt (Papierkorb, URL-Adressen, Linkverwaltung, RSS, Caches, Suchstatistik, Systeminformationen, Sektionen, PDF-Export, Erweiterungen, Pakete, Benachrichtigungen), den heutigen Seiten und Anleitungen zugeordnet.
keywords: Administrator, Papierkorb, wiederherstellen, URL-Aliase, Links, RSS, Cache, Suchstatistik, Systeminformationen, Sektionen, PDF-Export, Erweiterungen, Pakete, Benachrichtigung, Buch Learning, administrator, trash, restore, URL translator, URL aliases, links, cache, search statistics, system information, sections, PDF export, extensions, packages, notification, Learning book
type: help
---
# Weitere Funktionen der Administration

Nach den Inhaltsfunktionen geht das Buch die übrigen Administratorfunktionen des Bereichs **Setup** durch. Fast jede hat heute eine vollständige Anleitung. Diese Tabelle ordnet jeder Überschrift des Buchs die Seite zu, die Sie lesen sollten.

| Überschrift im Buch | Was es ist | Lesen |
|---|---|---|
| Removing and restoring objects | Ein entferntes Objekt kommt in den **Papierkorb**; von dort kann es wiederhergestellt (an seine alte Stelle oder an eine andere) oder endgültig entfernt werden. Das Leeren des Papierkorbs braucht eine eigene Berechtigung (`cleantrash`). | [Der Papierkorb](../../anleitungen/inhaltsmodell-und-bearbeitung.md#7-der-papierkorb), [Den Papierkorb leeren](../handbuch/wie-leere-ich-den-papierkorb.md), [Papierkorb: wer und wo](../../funktionen/6.0/papierkorb-wer-und-wo.md) |
| URL translation | Die Adressen von Inhalten ergeben sich aus den Objektnamen. **Globale URL-Aliase** geben einer Modul-View eine Adresse Ihrer Wahl, und **Platzhalter** bilden eine ganze Gruppe ab. | [Links, URL-Aliase, Platzhalter](../../anleitungen/urls-und-aliase.md), [Einer Seite eine zusätzliche Adresse geben](../handbuch/wie-gebe-ich-einer-seite-eine-zusaetzliche-adresse.md) |
| URL management | Die Liste aller Links, die in Inhalten verwendet werden, mit einer Prüfung, ob sie funktionieren. | [Defekte Links finden und reparieren](../handbuch/wie-finde-und-repariere-ich-defekte-links.md) |
| RSS export and import | Einen Zweig als Feed veröffentlichen oder einen Feed in Inhalte importieren. | [RSS-Feeds](../../anleitungen/rss-feeds.md) |
| Cache administration | Die Caches nach Namen leeren, nach einer Änderung, die sich nicht zeigt. | [Caches](../../anleitungen/caches.md), [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md) |
| Search statistics | Wonach Besucher gesucht haben und ob sie es gefunden haben. | [Links, URL-Aliase, Platzhalter und Suchstatistik](../../anleitungen/urls-und-aliase.md) |
| System information | PHP, Datenbank und die aktiven Erweiterungen. | [Systeminformationen](../../anleitungen/systeminformationen.md) |
| Section setup | Sektionen anlegen und Inhalte zuweisen. | [Sektionen](../../anleitungen/sektionen.md) |
| PDF export | Inhalte über eine Klasse als PDF exportieren. | [PDF-Exporte](../../anleitungen/pdf-exporte.md) |
| Rapid Application Development (RAD) | Die Gruppe der Setup-Werkzeuge zum Bauen im Buch: **Klassen**, **Extensions**, **Pakete**, **Benachrichtigungen** und weitere. | unten |
| Extension setup | Erweiterungen einschalten und sehen, welche aktiv sind. | [Die Seite Erweiterungen](../../anleitungen/extensions-seite.md), [Eine Erweiterung aktivieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/eine-erweiterung-aktivieren.md) |
| Packages | Inhalte, Klassen und Designs als Pakete installieren und exportieren. | [Pakete](../../anleitungen/pakete.md) |
| Notification | Benachrichtigungen über Inhaltsänderungen per E-Mail versenden. | [Benachrichtigungen für Administratoren](../../anleitungen/benachrichtigungen-administrator.md) |
| Personal | Die eigenen Seiten des Benutzers: Lesezeichen, Entwürfe, Einstellungen, Benachrichtigungseinstellungen. | [Was auf meiner Kontoseite steht](../handbuch/was-steht-auf-meiner-kontoseite.md) |
| Shop | Bestellungen, Produkte, Preise. | [E-Commerce](../e-commerce/lesepfad-fuer-e-commerce.md) |

## Was seit dem Buch neu ist

- Mehrere Designs der Administration und ein Menü, das sich je nach Rolle unterscheidet ([Rundgang durch die Administration](../rundgang-durch-die-administration.md)).
- Inhaltsprüfungen, API-Schlüssel, Anmeldung mit zwei Faktoren und die Audit-Seiten, die im Buch keine Entsprechung haben
  ([Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)).
- Die Kommandozeile bietet das meiste, was die Seiten bieten: `./console list` ([CLI-Skripte und Konsolenbefehle](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md)).

Weiter: [Eine Beispielsite planen](eine-beispielsite-planen.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
