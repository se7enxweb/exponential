---
title: Einträge des Kopfmenüs umordnen oder entfernen
audience: website-creator, admin, developer, editor
category: build-two-ways
order: 140
summary: Die Reihenfolge des Kopfmenüs ändern oder einen Eintrag herausnehmen, in der Administration (die Zeilen bearbeiten) oder mit exp:ini rem und exp:ini clear, mit den echten Unterschieden, die die Konsole zeigt.
keywords: Kopfmenü, Menü umordnen, Menüeintrag entfernen, MainMenuID, exp:ini rem, exp:ini clear, Menüreihenfolge, menu.ini, header menu, reorder menu, remove menu item, menu order
type: help
---
# Einträge des Kopfmenüs umordnen oder entfernen

Das Kopfmenü zeigt die Knoten-IDs aus `menu.ini [SiteInfo] MainMenuID[]` in der Reihenfolge, in der sie aufgeführt sind.
Umordnen heißt, die Zeilen umzuordnen; Entfernen heißt, eine Zeile zu löschen. Beides braucht danach das Leeren der
Template- und Inhalts-Caches, wie das Hinzufügen ([Caches nach einer Änderung](caches-nach-einer-aenderung.md)).

## Inhalt

1. [In der Administration](#in-der-administration)
2. [Einen Eintrag über die Kommandozeile entfernen](#einen-eintrag-über-die-kommandozeile-entfernen)
3. [Über die Kommandozeile umordnen](#über-die-kommandozeile-umordnen)
4. [Eine Seite aus dem Menü nehmen, ohne die Einstellungen anzufassen](#eine-seite-aus-dem-menü-nehmen-ohne-die-einstellungen-anzufassen)

## In der Administration

Öffnen Sie **Setup > INI-Einstellungen**, `menu.ini`, Siteaccess `site`, und klicken Sie bei `MainMenuID[]` auf
**Bearbeiten** ([der Durchgang](kopfmenue-in-der-administration.md) hat Bildschirmfotos). Im Feld **Wert**:

- zum **Umordnen** verschieben Sie die Zeilen: Die erste Zeile `=<id>` ist der erste Eintrag;
- zum **Entfernen** löschen Sie die Zeile des Eintrags;
- behalten Sie die **leere erste Zeile**: Sie ist das Zurücksetzen, das die eigene Liste der Theme-Erweiterung verwirft.
  Ohne sie würden die fünf IDs des Themes vor Ihren hinzugefügt.

Klicken Sie auf **Speichern** und leeren Sie dann im Kasten **Cache leeren** **Template & Inhalt**.

## Einen Eintrag über die Kommandozeile entfernen

`exp:ini rem` mit dem Wert entfernt ein Element aus einer Datei:

```text
$ ./console exp:ini rem menu.ini/SiteInfo/MainMenuID[] 185 siteaccess:site --dry-run
--- a/settings/siteaccess/site/menu.ini.append.php
+++ b/settings/siteaccess/site/menu.ini.append.php
@@ -10,7 +10,6 @@
 MainMenuID[]=164
 MainMenuID[]=26210
 MainMenuID[]=192
-MainMenuID[]=185
 NexusMainMenuID[]
 NexusMainMenuID[]=167
 NexusMainMenuID[]=168
Dry run: remove menu.ini/SiteInfo/MainMenuID[] in siteaccess:site, nothing written
```

Führen Sie es ohne `--dry-run` noch einmal aus, um zu schreiben, dann `./console exp:cache clear --tag=template,content`.

## Über die Kommandozeile umordnen

`exp:ini add` hängt immer an. Für eine neue Reihenfolge leeren Sie die Liste in dieser Datei und fügen die IDs in der
gewünschten Reihenfolge hinzu. `exp:ini clear` behält die Zurücksetz-Zeile und entfernt die Werte der Datei:

```text
$ ./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site --dry-run
--- a/settings/siteaccess/site/menu.ini.append.php
+++ b/settings/siteaccess/site/menu.ini.append.php
@@ -3,14 +3,6 @@
 [SiteInfo]
 RemoteID=media-o-site-info
 MainMenuID[]
-MainMenuID[]=92
-MainMenuID[]=130
-MainMenuID[]=144
-MainMenuID[]=163
-MainMenuID[]=164
-MainMenuID[]=26210
-MainMenuID[]=192
-MainMenuID[]=185
 NexusMainMenuID[]
 NexusMainMenuID[]=167
 NexusMainMenuID[]=168
Dry run: clear menu.ini/SiteInfo/MainMenuID[] in siteaccess:site, nothing written
```

Dann, zum Beispiel um Showcase (192) an den Anfang zu stellen:

```bash
./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site
for id in 192 92 130 144 163 164 26210; do
    ./console exp:ini add menu.ini/SiteInfo/MainMenuID[] $id siteaccess:site --no-backup
done
./console exp:ini where menu.ini/SiteInfo/MainMenuID site
./console exp:cache clear --tag=template,content
```

Der erste Befehl behält seine Sicherung unter `var/backup/ini/<timestamp>/`, sodass sich die alte Reihenfolge daraus wiederherstellen lässt.

## Eine Seite aus dem Menü nehmen, ohne die Einstellungen anzufassen

Das Template überspringt Listeneinträge, deren Knoten sich nicht holen lässt; eine Seite in den Papierkorb zu verschieben,
nimmt sie also auch aus dem Menü. Das ist ein Nebeneffekt, kein Menüwerkzeug: Die ID bleibt in der Einstellung, und eine
aus dem Papierkorb wiederhergestellte Seite kann eine neue Knoten-ID bekommen; prüfen Sie die Liste also nach dem
Wiederherstellen. Um einen Eintrag endgültig herauszunehmen, entfernen Sie seine Zeile.

## Verwandte Seiten

- [Einen Eintrag zum Kopfmenü hinzufügen](../../../eng-us/help/build-two-ways/add-a-header-menu-item.md)
- [Das Kopfmenü über die Kommandozeile](kopfmenue-per-kommandozeile.md)
- [exp:ini für Site-Entwickler](exp-ini-fuer-site-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
