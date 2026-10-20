---
title: "Ein Einbettungscode eines anderen Dienstes"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1109
summary: Der HTML-snippet-Block (Raw HTML) für einen Widget-Code und worauf zu achten ist; oder der Code in einem Template.
keywords: HTML-Snippet, Einbettung, Widget, rohes HTML, iframe, Skript, Sicherheit, CSP, HTML snippet, embed, raw HTML, script, security
type: help
---
# Ein Einbettungscode eines anderen Dienstes

Ein Widget eines anderen Dienstes (ein Buchungsformular, ein Feed), das als Stück HTML kommt.

*Lebenszyklus: Bauen, Beispiel 10 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **HTML snippet** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Raw HTML** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das HTML wird unverändert ausgegeben. Geben Sie der Redakteursrolle diesen Block nur, wenn die Personen, die ihn verwenden, rohes HTML veröffentlichen dürfen; Skripte darin unterliegen der Content Security Policy der Site.

## Mit Templates

1. Setzen Sie den Code in ein Template Ihrer Design-Erweiterung (zum Beispiel das Template eines Template-Blocks), wo er mit dem Code geprüft wird.

## Welchen Weg Sie wählen

Einmalige Einbettungen durch vertrauenswürdige Redakteure: der Block. Alles, was auf vielen Seiten läuft oder Skripte lädt: ein Template.

## Prüfen

- Das Widget erscheint, und die Browserkonsole zeigt keine Fehler der Content Security Policy.

## Verwandte Seiten

- [Eigene Template-Blöcke](../../eigene-template-bloecke.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
