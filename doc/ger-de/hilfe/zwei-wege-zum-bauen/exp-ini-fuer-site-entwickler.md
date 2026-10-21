---
title: exp:ini für Site-Entwickler
audience: admin, developer, website-creator, designer, hosting
category: build-two-ways
order: 420
summary: Die Befehle von exp:ini, die ein Site-Entwickler für Menüs und Theme-Einstellungen braucht (where, get, add, rem, clear, set, scopes, Probeläufe und Sicherungen), mit den Geltungsbereichen einer Installation.
keywords: exp:ini, Konsole, ini.php, where, get, add, rem, clear, set, Geltungsbereiche, dry-run, Probelauf, Sicherung, Siteaccess-Geltungsbereich, global, Erweiterungs-Geltungsbereich, menu.ini, console, scopes, backup, siteaccess scope, extension scope
type: help
---
# exp:ini für Site-Entwickler

`./console exp:ini` (das Skript `bin/php/ini.php`) liest und ändert INI-Einstellungen in jedem Geltungsbereich. Es ist der
schnellste Weg, die auf diesen Seiten beschriebenen Änderungen an Einstellungen vorzunehmen, und es lässt sich gefahrlos
ausprobieren: `--dry-run` zeigt den Unterschied und schreibt nichts. Seine vollständige Anleitung ist `doc/eng-us/bc/6.0/console-exp-ini.md`.

## Die auf diesen Seiten verwendeten Befehle

| Aufgabe | Befehl |
|---|---|
| Wo es gesetzt ist, was wirksam ist | `./console exp:ini where menu.ini/SiteInfo/MainMenuID site` |
| Der wirksame Wert | `./console exp:ini get menu.ini/SiteInfo/MainMenuID -s site` |
| Einen Menüeintrag hinzufügen (anhängen) | `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site` |
| Einen Eintrag entfernen | `./console exp:ini rem menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site` |
| Eine Liste in einer Datei leeren (behält die Zurücksetz-Zeile) | `./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site` |
| Einen einfachen Wert setzen | `./console exp:ini set menu.ini/SiteInfo/FooterLinksRemoteID my-footer-links siteaccess:site` |
| Die Blöcke einer Datei auflisten | `./console exp:ini list menu.ini site` |
| Die Geltungsbereiche auflisten | `./console exp:ini scopes` |

Fügen Sie jedem schreibenden Befehl `--dry-run` hinzu, um die Änderung zuerst zu sehen, und `--allow-root-user`, wenn Sie als root arbeiten.

## Syntax einer Einstellung

Die Einstellung wird als `<file>/<Block>/<Variable>` geschrieben; die Endung `.ini` kann entfallen. Arrays enden auf `[]`,
Hash-Einträge auf `[key]`. Die Hilfe akzeptiert auch `menu.ini:SiteInfo.MainMenuID` und `"menu.ini [SiteInfo] MainMenuID"`.

## Geltungsbereiche

| Geltungsbereich | Datei |
|---|---|
| `global` (= `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` (oder nur `<sa>`) | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`, verweigert ohne `--allow-default` |

## Was ein Schreibvorgang macht

- Die Datei wird Zeile für Zeile geschrieben: Kommentare, Leerzeilen und Reihenfolge bleiben; Eigentümer, Gruppe und Rechte bleiben erhalten.
- Der Schreibvorgang ist atomar, nach einer Sicherungskopie nach `var/backup/ini/<timestamp>/` (`--no-backup` überspringt sie).
- Der INI-Cache wird geleert (`--no-clear-cache` überspringt das).
- Exit-Codes: 0 erledigt (oder nichts zu ändern), 1 Aufruffehler, 2 nicht gefunden, 3 verweigert, 4 Schreiben fehlgeschlagen.

Auf der Testkopie dauerte ein `exp:ini add` 1,3 bis 2,1 Sekunden ([Gemessene Schritte und Zeiten](gemessene-schritte-und-zeiten.md)).

## Nach einem Schreibvorgang

Von Templates mit `ezini()` gelesene Einstellungen werden in die Templates kompiliert: Führen Sie
`./console exp:cache clear --tag=template,content` aus. Auf Exponential Velocity `./console exp:velocity restart`, wie der
eigene Hinweis des Befehls sagt.

## Theme-Einstellungen in Ihre Erweiterung verschieben

`exp:ini move` und `exp:ini move-all` verschieben einen Block oder einen ganzen Geltungsbereich in einen anderen, zum
Beispiel die Einstellungen eines Siteaccess in Ihre Design-Erweiterung (`--create-extension --activate`). So bleiben die
Menüs und Theme-Einstellungen einer Site mit dem Theme unter Versionsverwaltung. Probieren Sie es zuerst mit `--dry-run` aus.

## Verwandte Seiten

- [Das Kopfmenü über die Kommandozeile](kopfmenue-per-kommandozeile.md)
- [INI-Einstellungen in der Administration](ini-einstellungen-in-der-administration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
