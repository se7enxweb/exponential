---
title: Fehlerbehebung beim Kopfmenü
audience: admin, website-creator, developer, designer
category: build-two-ways
order: 160
summary: Der neue Eintrag des Kopfmenüs erscheint nicht; die auf einer echten Installation gefundenen Ursachen in der Reihenfolge, in der sie zu prüfen sind; kompiliertes Template, Seiten-Cache, falsche Datei oder falscher Geltungsbereich, falscher Siteaccess, ein Knoten, der sich nicht holen lässt, Velocity-Worker.
keywords: Kopfmenü aktualisiert sich nicht, Menüeintrag fehlt, Cache, kompiliertes Template, ezini, exphttpcache, Siteaccess, Geltungsbereich, exp:ini where, Velocity-Neustart, Fehlersuche, header menu not updating, menu item missing, cache, compiled template, siteaccess, scope, Velocity restart, troubleshooting
type: help
---
# Fehlerbehebung beim Kopfmenü

Sie haben eine Knoten-ID zu `MainMenuID[]` hinzugefügt, und die Kopfzeile hat sich nicht geändert. Prüfen Sie diese Punkte
der Reihe nach; jeder wurde beim Schreiben dieser Seiten beobachtet.

## Inhalt

1. [Das kompilierte Template hat noch die alte Liste](#1-das-kompilierte-template-hat-noch-die-alte-liste)
2. [Der Seiten-Cache liefert die alte Seite aus](#2-der-seiten-cache-liefert-die-alte-seite-aus)
3. [Der Wert landete in einer Datei, die nicht gewinnt](#3-der-wert-landete-in-einer-datei-die-nicht-gewinnt)
4. [Sie haben einen anderen Siteaccess geändert](#4-sie-haben-einen-anderen-siteaccess-geändert)
5. [Der Knoten lässt sich nicht holen](#5-der-knoten-lässt-sich-nicht-holen)
6. [Die Worker von Velocity haben die alten Einstellungen behalten](#6-die-worker-von-velocity-haben-die-alten-einstellungen-behalten)
7. [Eine kurze Checkliste](#eine-kurze-checkliste)

## 1. Das kompilierte Template hat noch die alte Liste

Die häufigste Ursache. `pagelayout/header/main_menu.tpl` liest die Liste mit `ezini()`, und der Template-Compiler schreibt
die Werte in die kompilierte Datei unter `var/<site>/cache/template/compiled/` (eine Datei namens `main_menu-<hash>.php`).
Das Speichern der Einstellung leert den INI-Cache, nicht die kompilierten Templates, und so wird weiterhin die alte Liste gezeigt.

Auf der Testkopie, nach dem Speichern von `=192`: Das Leeren des INI-Cache, des View-Caches der Inhalte, des Caches der
Template-Blöcke und des Seiten-Cache ließ das Menü jeweils unverändert; das Leeren des **Template**-Cache brachte den neuen
Eintrag sofort.

Abhilfe: in der Administration **Cache leeren > Template & Inhalt > Leeren** oder
`./console exp:cache clear --tag=template,content`. Einmal auf der Testkopie (für die Liste der Fußzeile) behielt eine Seite,
die zwischen der Änderung der Einstellung und dem Leeren dargestellt wurde, nach dem ersten Leeren die alte Liste; ein zweites
`exp:cache clear --tag=template,content` zeigte den neuen Eintrag. Leeren Sie noch einmal, bevor Sie weitersuchen.

## 2. Der Seiten-Cache liefert die alte Seite aus

Seiten werden im HTTP-Seiten-Cache gehalten (Cache-ID `exphttpcache`). Ein Antwort-Header zeigt es:

```text
X-Exp-Cache: HIT
Age: 9
```

Die Tags `template` und `content` enthalten beide `exphttpcache`, die Abhilfe aus 1 leert ihn also auch. Allein:
`./console exp:cache httpcache clear`.

## 3. Der Wert landete in einer Datei, die nicht gewinnt

`MainMenuID[]` wird in mehr als einer Datei gesetzt. Auf der Site Fit & Healthy setzt die Theme-Erweiterung fünf IDs, und
`settings/siteaccess/site/menu.ini.append.php` beginnt mit `MainMenuID[]` (dem Zurücksetzen) und setzt eigene. Ein Wert, der
zur Datei der Erweiterung hinzugefügt wird, wird durch dieses Zurücksetzen verworfen. Prüfen Sie es mit:

```text
./console exp:ini where menu.ini/SiteInfo/MainMenuID site
```

Die Administration zeigt dasselbe unter **In 2 Dateien gesetzt, 1 überschrieben**. Schreiben Sie in die Datei, die mit
„Elemente wirksam“ markiert ist (Geltungsbereich `siteaccess:site`).

## 4. Sie haben einen anderen Siteaccess geändert

Die Site `site` und zum Beispiel `bold` oder `eng` lesen jeweils ihre eigene `menu.ini`. Auf der Einstellungsseite
entscheidet die Auswahl **Siteaccess**, welche Sie bearbeiten. Der Siteaccess einer Seite ist der, auf den ihre Adresse passt
(Host oder erstes Pfadelement); `./console exp:ini where ... <siteaccess>` zeigt jede Liste.

## 5. Der Knoten lässt sich nicht holen

`main_menu.tpl` überspringt eine ID, deren Knoten sich nicht holen lässt, ohne Meldung. Ursachen: ein Tippfehler in der ID,
eine Objekt-ID statt einer Knoten-ID (die Administration zeigt beide: `Knoten 192 · Objekt 190`), eine Seite im Papierkorb.
Öffnen Sie `/content/view/full/<id>` in der Administration, um die ID zu prüfen.

## 6. Die Worker von Velocity haben die alten Einstellungen behalten

Auf einer Site, die Exponential Velocity ausliefert, gibt `exp:ini` aus: *Velocity workers keep the settings of their warm-up:
exp:velocity restart to apply it there.* Führen Sie auf einer solchen Site nach dem Leeren des Cache `./console exp:velocity restart` aus.

## Eine kurze Checkliste

1. `./console exp:ini get menu.ini/SiteInfo/MainMenuID -s site` listet die neue ID.
2. `./console exp:cache clear --tag=template,content` endet mit `PASS`.
3. Der Quelltext der Seite enthält `menu-item-main_menu-location-id-<new id>`.
4. Velocity: neu gestartet.

## Verwandte Seiten

- [Caches nach einer Änderung](caches-nach-einer-aenderung.md)
- [Warum erscheint meine Änderung nicht?](warum-erscheint-meine-aenderung-nicht.md)
- [Einen Eintrag zum Kopfmenü hinzufügen](kopfmenue-eintrag-hinzufuegen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
