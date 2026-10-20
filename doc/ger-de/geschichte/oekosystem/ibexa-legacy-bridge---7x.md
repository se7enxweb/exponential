# ibexa-legacy-bridge---7x: Geschichte des Plattform-Repositorys

Die Geschichte von `ibexa-legacy-bridge---7x`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Legacy Bridge und Site-Bundles). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 16 Änderungen von 2024-02-19 bis 2026-04-19: 7 vom se7enxweb-Team und 9 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Bridge von Ibexa 4 zum Legacy-Kernel von Exponential (se7enxweb/ibexa-legacy-bridge).

## Wie es mit Exponential zusammenhängt

Portierung der APIs von Ibexa 3 auf die mit Ibexa 4 kompatiblen; benennt den Script-Befehl in exponential:legacy:script um.

## Was Anwender davon haben

Legacy Bridge für Platform 4.6.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ibexa-legacy-bridge
```

## Befehle

Die Bridge für Platform 4 bringt einen Befehl mit, geprüft in `bundle/Command/LegacyEmbedScriptCommand.php`: `exponential:legacy:script` führt ein Legacy-Kommandozeilen-Script innerhalb der Bridge aus (`php bin/console exponential:legacy:script --help` in einem Projekt mit der Bridge). Die übrigen Befehle `exponential:legacy:*` gehören zur [Legacy Bridge](legacyBridge.md) für Platform 3.x / 5.x.

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 4 |
| Fehlerbehebungen | 4 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 2 |
| Releases | 1 |
| Ohne Nutzen für Anwender | 4 |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2026-03-16 `8ce72f9` Werkzeuge: Refactor bundle to replace ibexa3 apis with MediataCom ibexa4 compatible apis. Added se7enxweb/mediata-ezpage-fieldtype-bundle requirement to bundle's

### Marke Exponential (1)

- 2026-03-16 `5bd8c73` Funktion: Replacing vendor in composer.json. Rebranding.

### Versionsnummern (1)

- 2026-03-17 `340aedb` Release: Replaced netgen/ezpublish-letgacy-installer package. Version bump to composer config. Rebranding.

### Unterstützung für SQLite (1)

- 2026-04-07 `efe787a` Funktion: SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI

### Namen der Konsolenbefehle (1)

- 2026-04-07 `dc19470` BC: rename LegacyEmbedScriptCommand to exponential:legacy:script, keep ezpublish:legacy:script as deprecated alias (v4/4.x)

### Kompatibilität mit Symfony und der Plattform (1)

- 2026-04-19 `e6ba76f` Fehlerbehebung: update mediata-ezpage-fieldtype-bundle constraint to ^4.0 for Ibexa 4.6 compat

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-02 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `5c4d2ab` Allow ibexa-support branch of netgen/ezpublish-legacy; `5aaf2a2` Remove dev-ibexa4 require |
| 2024-04 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `8ebc459` Use Ibexa 4.6 |
| 2024-05 | 4 | 0 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `ca13b11` Fix issues with non matching interfaces after Twig 3.9; `7efbc39` Fix rendering legacy Twig templates with Twig 3.9+ |
| 2024-09 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6101698` Fix support for Twig 3.13 |

## Verwandte Seiten

- [Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md)
- [Spezifikation der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ibexa-legacy-bridge---7x.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- Plattform-Ökosystem nach Monat: [2024-02](monate/2024-02.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-09](monate/2024-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
