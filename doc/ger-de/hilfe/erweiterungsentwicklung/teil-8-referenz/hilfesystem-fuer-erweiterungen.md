---
title: Das Hilfesystem für Erweiterungen
audience: developer
category: extension-development
order: 360
summary: Die Dokumentation einer Erweiterung in die Hilfe der Administration bringen - ExtensionDocDirs, Front Matter und Zielgruppen-IDs, README-Dateien der Ordner, Kontexthilfe mit Map[] und Providern sowie exp:exphelp check.
keywords: Hilfesystem, Hilfe, Dokumentation, Front Matter, Zielgruppe, Kontexthilfe, Linkprüfung, help system, exphelp, ExtensionDocDirs, front matter, audience, category, summary, featured, hidden, ContextHelp, Map, Providers, expHelpContextProviderInterface, exp:exphelp check
type: help
---
# Das Hilfesystem für Erweiterungen

Teil 8, Referenz 32. Das Hilfezentrum der Administration zeigt die Dokumentation des Kernels und, wenn sich die
Installation dafür entscheidet, die von Erweiterungen. Diese Seite fasst die Einstellungen und Dateikonventionen an
einer Stelle zusammen. Die Erklärung steht in [Ihre Erweiterung dokumentieren](../teil-3-erweiterungsentwickler/ihre-erweiterung-dokumentieren.md),
das Tutorial in [Hilfeseiten für Ihre Erweiterung, von Hand](../teil-3-erweiterungsentwickler/tutorials/von-hand-hilfeseiten-fuer-ihre-erweiterung.md)
und die vollständige Beschreibung in [Das Hilfesystem](../../../../eng-us/features/6.0/help-system.md) und seiner
[Spezifikation](../../../spezifikationen/6.0/hilfesystem.md).

## Ihren doc-Ordner ausliefern

```ini
[IndexSettings]
ExtensionDocDirs[]=myext
```

in `settings/exphelp.ini.append.php` der Installation (oder Ihrer Erweiterung). Die Hilfe liefert dann die **von Git
verfolgten** Markdown-Dateien aus `extension/myext/doc/` aus, mit den Seiten-IDs `ext/myext/<path below doc>`. Nicht
verfolgte Dateien werden nicht angezeigt.

## Front Matter

```text
---
title: My extension: settings
audience: developer, admin
category: development
order: 10
summary: One sentence, at most 220 characters.
keywords: myext, settings
type: help
---
# My extension: settings
```

| Schlüssel | Bedeutung |
|---|---|
| `title` | der Seitentitel (setzen Sie ihn in Anführungszeichen, wenn er einen Doppelpunkt gefolgt von einem Leerzeichen enthält) |
| `audience` | eine oder mehrere Zielgruppen-IDs, durch Kommas getrennt |
| `category` | eine Kategorie-ID |
| `order` | die Position unter den Geschwisterseiten |
| `summary` | wird in Listen und in der Suche angezeigt; nach 220 Zeichen abgeschnitten |
| `keywords` | zusätzliche Suchbegriffe |
| `type` | `help` oder einer der Dokumentationstypen (`reference`, `bc`, `install`, `specification` ...) |
| `featured: true` | die Seite auf das Dashboard stellen |
| `hidden: true` | sie ausliefern, aber aus Listen und Suche heraushalten |

Zielgruppen-IDs (`exphelp.ini [AudienceSettings] Audiences[]`): `editor`, `admin`, `developer`, `user`,
`project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator`,
`supporter`, `sponsor`.

## Ordner

Die `README.md` eines Ordners ist seine Indexseite: Ihr Titel und ihre Reihenfolge sind die des Ordners in der
Navigation. Verlinken Sie jede Seite aus der README ihres Ordners, damit keine verwaist.

## Kontexthilfe

Die Hilfe-Schaltfläche in der Kopfzeile öffnet die Hilfe zur aktuellen Seite:

```ini
[ContextHelp]
Map[myext]=ext/myext/README.md
Map[myext/settings]=ext/myext/settings.md#clearing-the-cache
Providers[]=myExtHelpContextProvider
```

Schlüssel sind `module`, `module/view` oder solche mit einem Design-Präfix (`admin4l:myext/settings`). Ein Provider
implementiert `expHelpContextProviderInterface` (`kernel/classes/help/exphelpcontextproviderinterface.php`):
`helpContext( $module, $view, $design )` gibt `"path[#anchor]"` oder `null` zurück.

## Schreibregeln, die die Hilfe durchsetzt

- Nur Markdown: Rohes HTML wird maskiert.
- Relative Links müssen auf existierende Dateien zeigen, Anker auf existierende Überschriften.
- Bilder im selben Ordnerbaum.

## Prüfen

```bash
./console exp:exphelp check
```

meldet defekte Links, fehlende Anker und Probleme im Front Matter, mit Fehlern und Warnungen. Führen Sie es vor jedem
Release Ihrer Dokumentation aus.

## Verwandte Seiten

- Zurück: [admin4-Komponenten und -Tokens](admin4-komponenten-und-tokens.md)
- Weiter: [expservices: entfernte Dienste für Erweiterungen](expservices.md)
- [Das Hilfesystem](../../../../eng-us/features/6.0/help-system.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
