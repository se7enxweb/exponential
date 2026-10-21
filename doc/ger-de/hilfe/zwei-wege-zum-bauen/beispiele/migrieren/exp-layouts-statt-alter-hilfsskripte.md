---
title: "exp:layouts statt der alten Hilfsskripte verwenden"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1512
summary: Mehrere ext:explayouts:*-Helfer (block_list, layout_info, layout_copy, layout_list, rule_list, share_create, collection_list) brachen mit einem Fehler „class not found“ ab; exp:layouts deckt Auflisten und Übertragen ab.
keywords: Hilfsskripte, exp:layouts, layout_copy, Migration, bekannte Probleme, helper scripts, migration, known issues
type: help
---
# exp:layouts statt der alten Hilfsskripte verwenden

Skripte, die gegen die alten Helfer geschrieben wurden, funktionieren weiter.

*Lebenszyklus: Migrieren, Beispiel 13 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Ein Layout im Editor von Hand zu kopieren heißt, es neu zu bauen; die Import-Kopie ist schneller.

## Auf der Kommandozeile

1. Auflisten: `exp:layouts list`. Details: `exp:layouts export --only=<identifier>` und `layouts.json` lesen. Kopieren: exportieren und `import --strategy=copy`.

## Welchen Weg Sie wählen

Stellen Sie Skripte jetzt auf `exp:layouts` um.

## Prüfen

- Die Skripte laufen ohne die alten Helfer.

## Verwandte Seiten

- [Bekanntes Verhalten des Editors](../../bekanntes-verhalten-des-editors.md)
- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
