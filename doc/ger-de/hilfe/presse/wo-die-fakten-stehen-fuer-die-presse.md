---
title: Wo die Fakten stehen, für die Presse
audience: press
category: press-media
order: 20
summary: Eine Quellenkarte für Reporter - welche Datei im Repository, welche Hilfeseite oder welche öffentliche Website die primäre Quelle für jede Art von Fakt zu Exponential ist, wie primäre und sekundäre Quellen gewichtet werden und was Sie tun, wenn sich zwei Quellen widersprechen.
keywords: Quellen, primäre Quelle, Quellenkarte, wo prüfen, README, SECURITY.md, COPYRIGHT.md, Changelog, Release-Übersicht, Geschichte, Websites, Überprüfung, widersprüchliche Quellen, sources, primary source, source map, where to check, changelog, release overview, history, websites, verification, conflicting sources
type: help
---
# Wo die Fakten stehen, für die Presse

Diese Seite ist für Reporter, die die primäre Quelle für einen Fakt suchen, keine Zusammenfassung. Exponential ist
insofern ungewöhnlich, als fast alles in seinem öffentlichen Git-Repository aufgeschrieben ist. Nutzen Sie diese Karte,
um zur Quelle zu gehen und zu entscheiden, welche Quelle Vorrang hat, wenn sich zwei widersprechen.

## Inhalt

1. [Die Rangfolge der Quellen](#die-rangfolge-der-quellen)
2. [Fakt und Quelle](#fakt-und-quelle)
3. [Die Dateien im Repository](#die-dateien-im-repository)
4. [Die Websites](#die-websites)
5. [Wenn sich zwei Quellen widersprechen](#wenn-sich-zwei-quellen-widersprechen)

## Die Rangfolge der Quellen

| Rang | Quelle | Warum |
|---|---|---|
| 1 | Das Git-Repository: Tags, Commits, `composer.json`, `SECURITY.md`, `COPYRIGHT.md` | Es ist das, was tatsächlich veröffentlicht wurde, mit Daten, die Sie prüfen können |
| 2 | Die Dokumentation in `doc/` (diese Hilfe, die Changelogs, die Geschichte) | Mit dem Code geschrieben, mit Quellen, versioniert |
| 3 | Die Websites der Organisationen: exponential.earth, foundation.exponential.earth, se7enx.com | Selbstbeschreibungen und Ankündigungen |
| 4 | Pressemitteilungen und Beiträge | Die Aussagen der Organisationen zu einem bestimmten Zeitpunkt |
| 5 | Artikel Dritter, auch anderer Presse | Nützliche Hinweise; prüfen Sie sie anhand der Ränge 1 bis 3 |

## Fakt und Quelle

| Sie brauchen | Primäre Quelle |
|---|---|
| Was Exponential ist | [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md), „What is Exponential?“ |
| Release-Nummern und Daten | Git-Tags (`git tag -l 'v6.0.*' --sort=version:refname`); [Release-Übersicht](../../../eng-us/release-overview) |
| Was sich in einem Release geändert hat | [Changelogs](../../../changelogs/README.md); der Changelog 6.0.15 für die Linie in Entwicklung |
| Die Geschichte Monat für Monat | [Die Geschichte](../../../eng-us/history/README.md) |
| Inhaber des Urheberrechts | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| Lizenz | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE); [Die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md) |
| Sicherheitsprozess | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Wie man beiträgt | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| PHP-Versionen, Pakete | [composer.json](https://github.com/se7enxweb/exponential/blob/main/composer.json); README, „Requirements“ |
| Datenbanken, Betriebssysteme | [Unterstützte Datenbanken](../datenbankunterstuetzung.md); [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Das Verhalten einer Funktion | Die Funktionsseite in `doc/eng-us/features/6.0/`, zum Beispiel [Content Security Policy](../../../eng-us/features/6.0/content-security-policy.md) |
| Begriffe | [Das Glossar](../../../eng-us/glossary.md) |
| 7x | <https://se7enx.com/about>; [Wer ist 7x?](../wer-ist-7x.md) |
| Die Foundation | <https://foundation.exponential.earth/>; [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| Die Umbenennung | <https://exponential.earth/about/project>; [August 2025](../../../eng-us/history/2025/2025-08.md) |
| Pressekit, frühere Berichte | [Das Pressekit](das-pressekit.md); [Frühere Pressemitteilungen und Berichte](fruehere-pressemitteilungen-und-berichte.md) |

Die Fürsprecher führen eine ähnliche Karte für Gesprächspunkte: [Wo die Fakten stehen](../fuersprecher/wo-die-fakten-stehen.md).

## Die Dateien im Repository

Sie müssen nichts installieren, um sie zu lesen; öffnen Sie sie auf GitHub. Um Daten und Zählungen zu prüfen, klonen
Sie es:

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
cat COPYRIGHT.md SECURITY.md | less
git log -1 --format='%ad %s' v6.0.14
```

## Die Websites

| Website | Gut für | Vorsicht bei |
|---|---|---|
| exponential.earth | Ankündigungen, das Pressekit, Kontakte, den Hinweis zur Umbenennung | Datiertem Material wie den wichtigen Fakten im Pressekit ([Das Pressekit](das-pressekit.md#fakten-im-kit-die-veraltet-sind)) |
| foundation.exponential.earth | Die Selbstbeschreibung und den Auftrag der Foundation | Zielen, die erwähnt, aber nicht aufgeführt werden |
| se7enx.com | Die Dienstleistungen und die Selbstbeschreibung von 7x | Preisen und Angeboten, die sich ändern; nennen Sie das Datum |
| doc.exponential.earth | Die offizielle Dokumentationswebsite | Prüfen Sie, ob eine Seite zum aktuellen Release passt |

## Wenn sich zwei Quellen widersprechen

1. **Bevorzugen Sie den höheren Rang.** Ein Tag schlägt eine Pressemitteilung über dieselbe Version.
2. **Bevorzugen Sie innerhalb desselben Rangs die neuere datierte Quelle.**
3. **Erklären Sie, statt stillschweigend zu wählen**, wenn beide berechtigt sind. Beispiel: Die Jahre 2014, 2022 und
   2023 für 7x markieren verschiedene Etappen ([7x in drei Etappen](7x-in-drei-etappen.md)).
4. **Fragen Sie nach**, wenn die Quellen es nicht klären können, und berichten Sie die Antwort mit Quellenangabe.
5. **Melden Sie dem Projekt** einen Widerspruch in seinen eigenen Dokumenten im
   [Community-Tracker](https://github.com/se7enxweb/exponential-community/issues).

## Verwandte Seiten

- [Leitfaden zum Faktencheck](leitfaden-faktenpruefung.md)
- [Was die Quellen nicht sagen](was-die-quellen-nicht-sagen.md)
- [Hilfeseiten nach Thema, für die Presse](hilfeseiten-nach-thema-fuer-die-presse.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
