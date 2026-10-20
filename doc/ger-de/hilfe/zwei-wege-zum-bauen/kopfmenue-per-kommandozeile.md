---
title: Das Kopfmenü über die Kommandozeile
audience: admin, developer, website-creator, designer
category: build-two-ways
order: 120
summary: Eine Seite mit ./console exp:ini zum Kopfmenü hinzufügen; sehen, wo die Liste gesetzt ist, die Änderung mit --dry-run in der Vorschau ansehen, sie schreiben, die Caches leeren. Echte Ausgaben von Exponential 6.0.15+.
keywords: exp:ini, Konsole, Kommandozeile, CLI, Kopfmenü, MainMenuID, menu.ini, Probelauf, exp:cache, Cache leeren, Siteaccess-Geltungsbereich, console, command line, header menu, dry run, clear cache, siteaccess scope
type: help
---
# Das Kopfmenü über die Kommandozeile

Zwei Befehle fügen eine Seite zum Kopfmenü hinzu: `exp:ini add` schreibt die Knoten-ID in die `menu.ini`, `exp:cache clear`
lässt die Site sie zeigen. Führen Sie sie im Wurzelverzeichnis der Installation aus. Als root fügen Sie jedem Befehl
`--allow-root-user` hinzu (die Ausgaben unten entstanden so; die Option ist in den Eingabezeilen der Lesbarkeit halber weggelassen).

## Inhalt

1. [Sehen, wo die Liste gesetzt ist](#1-sehen-wo-die-liste-gesetzt-ist)
2. [Die Änderung in der Vorschau ansehen](#2-die-änderung-in-der-vorschau-ansehen)
3. [Sie schreiben](#3-sie-schreiben)
4. [Die Caches leeren](#4-die-caches-leeren)
5. [Das Ergebnis prüfen](#5-das-ergebnis-prüfen)
6. [Dasselbe für jeden Siteaccess](#6-dasselbe-für-jeden-siteaccess)

## 1. Sehen, wo die Liste gesetzt ist

`exp:ini where` listet jede Datei, die eine Einstellung setzt, in der Lesereihenfolge, und den wirksamen Wert:

```text
$ ./console exp:ini where menu.ini/SiteInfo/MainMenuID site
menu.ini/SiteInfo/MainMenuID (load order of siteaccess site)
 1. extension/sevenx_themes_media/settings/siteaccess/site/menu.ini.append.php scope extension:sevenx_themes_media:siteaccess:site
      MainMenuID[]=79
      MainMenuID[]=117
      MainMenuID[]=131
      MainMenuID[]=150
      MainMenuID[]=151
 2. settings/siteaccess/site/menu.ini.append.php                 scope siteaccess:site
      MainMenuID[]=92
      MainMenuID[]=130
      MainMenuID[]=144
      MainMenuID[]=163
      MainMenuID[]=164
      MainMenuID[]=26210
      MainMenuID[]=192
In effect:
      MainMenuID[]=92
      ...
```

Die zweite Datei (Geltungsbereich `siteaccess:site`) ist die wirksame, also ist das der Geltungsbereich, in den zu schreiben
ist. (Diese Ausgabe enthält bereits `192`, in der Administration auf [der anderen Seite](kopfmenue-in-der-administration.md) hinzugefügt.)

## 2. Die Änderung in der Vorschau ansehen

`--dry-run` zeigt die Änderung als Diff und schreibt nichts. Das Beispiel fügt **Contact** (Knoten 185) hinzu:

```text
$ ./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 185 siteaccess:site --dry-run
--- a/settings/siteaccess/site/menu.ini.append.php
+++ b/settings/siteaccess/site/menu.ini.append.php
@@ -10,6 +10,7 @@
 MainMenuID[]=164
 MainMenuID[]=26210
 MainMenuID[]=192
+MainMenuID[]=185
 NexusMainMenuID[]
 NexusMainMenuID[]=167
 NexusMainMenuID[]=168
Dry run: add to menu.ini/SiteInfo/MainMenuID[] in siteaccess:site, nothing written
```

`add` hängt an: Der neue Eintrag wird der letzte. Für eine andere Position siehe
[Einträge des Kopfmenüs umordnen oder entfernen](kopfmenue-eintraege-umordnen-oder-entfernen.md).

## 3. Sie schreiben

```text
$ ./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 185 siteaccess:site
Add to menu.ini/SiteInfo/MainMenuID[] in siteaccess:site: settings/siteaccess/site/menu.ini.append.php
Backup: var/backup/ini/20261019-031440/settings/siteaccess/site/menu.ini.append.php
INI cache: cleared tag ini: 5 caches
Hint: PHP-FPM reads the change on its next request (ini cache cleared); Velocity workers keep the settings of their warm-up: exp:velocity restart to apply it there.
Done
```

Die Datei wird Zeile für Zeile geschrieben (Kommentare und Reihenfolge bleiben), atomar, nach einer Sicherungskopie unter
`var/backup/ini/`. Der INI-Cache wird geleert. Wie der Hinweis sagt, übernimmt eine Site, die Exponential Velocity
ausliefert, die Änderung nach `./console exp:velocity restart`.

## 4. Die Caches leeren

Das Menü-Template der Kopfzeile wird mit der Liste der IDs darin kompiliert; leeren Sie also die Caches für Templates und Inhalte:

```text
$ ./console exp:cache clear --tag=template,content
  exphttpcache         on  content,template           new HTTP cache generation: every stored page becomes stale
  template             on  template                   directory removed
  template-block       on  template,content           expiry timestamp global-template-block-cache set, directory moved aside
  template-override    on  template                   directory removed, in-memory override list emptied
  ...
  content              on  content                    expiry timestamp content-view-cache set, directory moved aside
  ...
PASS  cleared tag template, content: 17 caches
```

## 5. Das Ergebnis prüfen

Die Menüeinträge tragen das Attribut `id="menu-item-main_menu-location-id-<id>"`, eine schnelle Prüfung ist also:

```text
$ curl -s https://www.example.com/ | grep -o 'menu-item-main_menu-location-id-[0-9]*' | tail -3
menu-item-main_menu-location-id-26210
menu-item-main_menu-location-id-192
menu-item-main_menu-location-id-185
```

(Auf dieser Site zeigen die ersten fünf Einträge in diesem Attribut stattdessen die IDs aus `NexusMainMenuID[]`, siehe
[Wie die Kopfzeile aufgebaut ist](../../../eng-us/help/build-two-ways/how-the-header-is-built.md).)

## 6. Dasselbe für jeden Siteaccess

Jeder Site-Siteaccess kann seine eigene Liste haben. Um alle zu finden: `./console exp:ini scopes` listet die
Geltungsbereiche der Installation, und `exp:ini where menu.ini/SiteInfo/MainMenuID <siteaccess>` zeigt die wirksame Liste
eines davon. Schreiben Sie jeden mit seinem eigenen Geltungsbereich, `siteaccess:<name>`. Der Geltungsbereich `global`
(`settings/override/menu.ini.append.php`) wird nach jeder Siteaccess-Datei gelesen, ein dort hinzugefügter Wert wird also
zur Liste jedes Siteaccess hinzugefügt; halten Sie Menülisten in den Siteaccess-Dateien, wo jede Site ihre eigene hat.

## Verwandte Seiten

- [Einen Eintrag zum Kopfmenü hinzufügen](../../../eng-us/help/build-two-ways/add-a-header-menu-item.md)
- [exp:ini für Site-Entwickler](exp-ini-fuer-site-entwickler.md)
- [Caches nach einer Änderung](caches-nach-einer-aenderung.md)
- [Gemessene Schritte und Zeiten](gemessene-schritte-und-zeiten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
