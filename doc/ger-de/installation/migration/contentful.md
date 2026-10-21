---
title: Contentful und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 46
summary: Inhalte zwischen Contentful und Exponential 6 übertragen – Export und Import eines Contentful-Space, die Content Delivery API, was der Export auslässt, die Abbildung von Inhaltstypen, Einträgen, Assets, Locales und Rich Text sowie das Beibehalten eines Headless-Frontends, während sich die Inhaltsquelle ändert.
keywords: Contentful, contentful-cli, Space-Export, Space-Import, Content Delivery API, Content Management API, Einträge, Assets, Locales, Rich Text, Headless, Migration, space export, space import, entries, assets, locales, rich text, headless, migration
type: install
---
# Contentful und Exponential

Contentful ist eine Headless-Content-Plattform: Sie speichert Inhalte und liefert sie über APIs aus, und ein separates
Frontend rendert die Website. Ein Wechsel zu Exponential ersetzt entweder beides (Exponential rendert die Website) oder
nur die Inhaltsquelle (das Frontend liest stattdessen aus Exponential, siehe [JavaScript- und Node.js-Anwendungen](javascript-anwendungen.md)).

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Zuordnung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## 1. Was Contentful exportieren kann

Laut der Dokumentation von Contentful und den READMEs seiner offiziellen CLI-Werkzeuge:

| Werkzeug | Was Sie erhalten | Grenzen |
|---|---|---|
| **`contentful space export`** (Contentful CLI) | eine JSON-Datei mit Inhaltstypen, Einträgen, Assets, Locales, Tags, Webhooks, Rollen, Editor-Oberflächen und Releases; Optionen `--space-id`, `--management-token`, `--export-dir`, `--content-file`, `--download-assets`, `--include-drafts`, `--skip-content`, `--skip-roles`, `--skip-webhooks`, `--content-model-only` | keine Space-Mitgliedschaften; die Zugangsdaten der Webhooks müssen von Hand neu eingetragen werden; nur aktive Releases |
| **Content Delivery API** | veröffentlichte Einträge und Assets als JSON, `skip`/`limit`/`total`/`items`; `limit` standardmäßig 100, höchstens 1000; auch eine Cursor-Paginierung ist dokumentiert | nur veröffentlichte Inhalte |
| **Content Management API** | Lese- und Schreibzugriff einschließlich Entwürfen | wird von der CLI verwendet |

`contentful space export --download-assets --include-drafts` liefert Ihnen in einem Durchlauf alles, was eine Migration braucht.

## 2. Abbildung auf Exponential

| Contentful | Exponential 6 |
|---|---|
| Inhaltstyp mit Feldern | Klasse mit Attributen (Symbol/Text zu `ezstring`/`eztext`, Integer/Number, Boolean, Date zu `ezdatetime`, Location zu `ezgmaplocation`, JSON-Objekt zu `eztext` oder nach Einzelfallentscheidung) |
| Eintrag (Entry) | Objekt; `sys.id` als Remote-ID (`contentful-<id>`) |
| Referenz / Referenzen | `ezobjectrelation` / `ezobjectrelationlist` |
| Asset | Bild- oder Dateiobjekt |
| Rich Text (ein JSON-Dokument aus Knoten) | `ezxmltext`: den Knotenbaum (Absätze, Überschriften, Listen, Hyperlinks, eingebettete Einträge und Assets) in die vereinfachte Eingabe umwandeln |
| Locales | Übersetzungen desselben Objekts; Fallback-Locales zu den Sprach-Fallbacks von Exponential |
| Tags | `eztags` |
| Slug-Feld | der URL-Alias des Knotens |
| Kein Baum | Contentful ist flach: Legen Sie den Inhaltsbaum fest (nach Typ, nach einer Referenz auf ein übergeordnetes Element oder nach den Routen des Frontends) |

## 3. Schritt für Schritt

1. `contentful space export --space-id <id> --management-token <token> --download-assets --include-drafts --export-dir <dir>`.
2. Entwerfen Sie den Baum und die Klassen anhand der Inhaltstypen; schreiben Sie die Zuordnungstabelle.
3. Wandeln Sie das JSON um: Einträge pro Typ mit ihren Feldern pro Locale, Rich Text zu XmlText, Referenzen zu Remote-IDs.
4. Importieren Sie die Assets, dann die Einträge ohne Referenzen, dann in einem zweiten Durchlauf die Referenzen; Übersetzungen pro Locale.
5. Wenn ein Headless-Frontend bleibt: Richten Sie es auf die REST-API von Exponential oder auf `expservices` aus
   ([JavaScript- und Node.js-Anwendungen](javascript-anwendungen.md)); andernfalls erstellen Sie TPL-Templates.
6. Weiterleitungen von den Routen des Frontends ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)).

## 4. Von Exponential zu Contentful

Exportieren Sie mit xrowextract ([Migration weg von Exponential](exponential-verlassen.md)), bauen Sie das Inhaltsmodell
in Contentful auf und schreiben Sie eine Datei für `contentful space import` (oder verwenden Sie die Management API).
Die eigenen Hinweise von Contentful: Quelle und Ziel brauchen dasselbe Inhaltsmodell und dieselbe Standard-Locale,
`space import` führt keine Inhaltsumwandlungen durch („please use contentful-migration“), Rollen und Mitgliedschaften
werden nicht importiert, und importierte Inhalte zeigen den Eigentümer des Tokens als Autor. Weiterleitungen sind
Sache des Frontends oder seines Hosts.

## 5. Checkliste

- [ ] Space mit Assets und Entwürfen exportiert.
- [ ] Baum entworfen; Klassen angelegt; Rich-Text-Konverter getestet.
- [ ] Assets, Einträge, Referenzen und Locales mit den Remote-IDs `contentful-<id>` importiert.
- [ ] Frontend umgestellt oder Templates erstellt; Weiterleitungen getestet.

## Quellen

- Exportwerkzeug von Contentful: <https://github.com/contentful/contentful-export>
- Importwerkzeug von Contentful: <https://github.com/contentful/contentful-import>
- Tutorial zu Import und Export mit der CLI: <https://www.contentful.com/developers/docs/tutorials/cli/import-and-export/>

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md)).
