# ezplatform-xmltext-fieldtype: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-xmltext-fieldtype`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 7 Änderungen von 2026-03-25 bis 2026-04-17, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Feldtyp XmlText für die Symfony-Plattform.

## Wie es mit Exponential zusammenhängt

Lässt die Plattform XML-Textinhalte lesen, die im Legacy-Kernel von Exponential entstanden sind; auf die Service-Namen von Ibexa 4.6 aktualisiert.

## Was Anwender davon haben

Bestehende XML-Textfelder aus Legacy-Inhalten werden im neuen Stack angezeigt.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-xmltext-fieldtype
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 6 |
| Verhaltens- und Upgrade-Änderungen | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-25 | v2.0.3 | `26123b1` | ezsystems/ -> se7enxweb/; expand PHP to ^8.5; add replace |

## Änderungen des se7enxweb-Teams, nach Thema

### Fehlerbehebungen (4)

- 2026-04-17 `f7680a9` Fehlerbehebung: update persistence converter DI tag to ibexa.field_type.storage.legacy.converter
- 2026-04-17 `ee23086` Fehlerbehebung: update all DI service tags from ezplatform. to ibexa. namespace
- 2026-04-17 `9da623f` Fehlerbehebung: update ezurl storage gateway service ID to Ibexa FQCN
- 2026-04-17 `d28c30d` Fehlerbehebung: use single quotes for FQCN service ID in YAML (backslash escape)

### Kompatibilität mit Symfony und der Plattform (2)

- 2026-04-04 `7a26416` Fehlerbehebung: Ibexa 4.6 compat - FieldTypeParentCompatibilityPass, dual extension support, ezrichtext aliases
- 2026-04-17 `2730518` Fehlerbehebung: update all service IDs to Ibexa 4.6 naming conventions

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-03-25 `26123b1` BC: ezsystems/ -> se7enxweb/; expand PHP to ^8.5; add replace

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-xmltext-fieldtype.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-xmltext-fieldtype.md)
- Plattform-Ökosystem nach Monat: [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
