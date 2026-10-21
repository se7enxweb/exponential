---
title: Der Online-Editor und die Barrierefreiheit
audience: government, admin, editor, project-manager
category: public-sector
order: 110
summary: Für öffentliche Stellen, deren Beschäftigte Rich Text schreiben - welche Engines für den Online-Editor Exponential mitliefert (TinyMCE 3 als Standard, TinyMCE 8 auf Wunsch), warum ihre Barrierefreiheit nicht geprüft ist und wie Sie den Editor und seine Ausgabe für Ihre eigene Erklärung testen.
keywords: Online-Editor, Rich Text, Engine-Wechsel, Barrierefreiheit, Tastatur, Screenreader, nicht geprüft, Autorenwerkzeug, Ausgabe des Rich Text, Überschriften, Tabellen, online editor, rich text, TinyMCE, TinyMCE 3, TinyMCE 8, ezoe, engine switch, accessibility, keyboard, screen reader, unverified, authoring tool, rich text output, headings, tables
type: help
---
# Der Online-Editor und die Barrierefreiheit

Diese Seite richtet sich an die Administration, die den Rich-Text-Editor auswählt, an die Koordination
Barrierefreiheit, die die Autorenumgebung bewertet, und an Redakteurinnen und Redakteure mit Behinderungen, die ihn
nutzen. Der Online-Editor ist der Teil der Administration, den das Projekt **nicht** geprüft hat: Sein Status ist
**Nicht geprüft**, und die Barrierefreiheit der Editor-Engine ist die des Projekts TinyMCE
([Standards zur Barrierefreiheit: bekannte Lücken](../../../standards/barrierefreiheit.md#bekannte-lücken)).

## Inhalt

1. [Welchen Editor Ihre Beschäftigten erhalten](#welchen-editor-ihre-beschäftigten-erhalten)
2. [Was das Projekt sagt](#was-das-projekt-sagt)
3. [Den Editor testen](#den-editor-testen)
4. [Die Ausgabe testen](#die-ausgabe-testen)
5. [Auswählen und dokumentieren](#auswählen-und-dokumentieren)
6. [Verwandte Seiten](#verwandte-seiten)

## Welchen Editor Ihre Beschäftigten erhalten

Die Erweiterung eZ Online Editor (`extension/ezoe`) liefert zwei Engines mit
([Der Online-Editor auf TinyMCE 8](../../../funktionen/6.0/online-editor-tinymce8.md)):

| Engine | Status in einer neuen Installation | Wie Sie sie wählen |
|---|---|---|
| TinyMCE 3.5.12 | der Standard | nichts zu tun |
| TinyMCE 8.9.2 | auf Wunsch | je Benutzer: `/user/preferences/set/ezoe_engine/tinymce8` oder die Seite `/ezoe/engine`; je Siteaccess: `ezoe.ini [EditorSettings] EditorEngine=tinymce8` |

Mit `EngineSwitch=enabled` zeigt der Editor unter sich einen Engine-Wechsel, eine Gruppe von Umschaltflächen mit
`aria-pressed`. Beide Engines speichern dasselbe XML, sodass die Redaktion wechseln kann.

Wo die Content Security Policy ohne `'unsafe-inline'` in `script-src` durchgesetzt wird, wird TinyMCE 3 nicht
angeboten, und der Editor weicht auf TinyMCE 8 aus
([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Was das Projekt sagt

- Die Integrationen von TinyMCE wurden nicht auf WCAG geprüft. **Nicht geprüft.**
- Der Engine-Wechsel ist eine Umschaltgruppe mit `aria-pressed` (dokumentiert auf der Seite zu TinyMCE 8).
- EN 301 549 Abschnitt 11.8 (Autorenwerkzeuge) wurde nicht geprüft.

Weitere Nachweise gibt es im Repository nicht. Beschreiben Sie den Editor in Ihrer Erklärung nicht ohne eigenen Test
als barrierefrei.

## Den Editor testen

Testen Sie zuerst nur mit der Tastatur, dann mit einem Screenreader, und zwar mit der Engine, die Sie einsetzen:

- [ ] Tab erreicht den Editorbereich und verlässt ihn wieder (keine Tastaturfalle).
- [ ] Die Werkzeugleiste lässt sich mit der Tastatur erreichen und bedienen.
- [ ] Überschriften, Listen, Fettschrift und Links lassen sich ohne Maus anwenden.
- [ ] Die Dialoge für Links und Einbettungen öffnen sich, lassen sich ausfüllen und mit Escape schließen und geben den
  Fokus zurück.
- [ ] Eine Tabelle mit Kopfzeile und Beschriftung lässt sich anlegen.
- [ ] Ein Bild lässt sich einbetten und sein Alternativtext eingeben oder prüfen.
- [ ] Der Screenreader kündigt den Namen des Editors und die Schaltflächen der Werkzeugleiste an.
- [ ] Bei 200 % Zoom bleiben Werkzeugleiste und Dialoge benutzbar.

## Die Ausgabe testen

Für die Öffentlichkeit zählt am meisten das HTML, das die Inhalte des Editors in Ihrem Design ergeben:

1. Legen Sie einen Testartikel mit zwei Überschriftenebenen, einer Liste, einem Link mit aussagekräftigem Text, einer
   Tabelle mit Kopfzellen und Beschriftung und einem eingebetteten Bild mit Alternativtext an.
2. Veröffentlichen Sie ihn in einem verborgenen Testordner und öffnen Sie ihn im öffentlichen Siteaccess.
3. Untersuchen Sie das HTML: echte Elemente `h2`/`h3`, `th`-Zellen mit `scope`, eine `caption`, `alt` am Bild. Die
   Tabellen-Templates des Standarddesigns schreiben `caption`, `scope` und `abbr`, wenn sie gesetzt sind
   (`design/standard/templates/content/datatype/view/ezxmltags/table.tpl`, `th.tpl`).
4. Führen Sie eine automatische Prüfung der Seite und einen Tastaturdurchgang durch.

## Auswählen und dokumentieren

- Wählen Sie eine Engine für die Organisation und schreiben Sie sie in Ihren Redaktionsleitfaden.
- Halten Sie Ihre Testergebnisse mit der Engine und ihrer Version fest.
- Testen Sie nach Upgrades erneut: Das [Changelog von 6.0.15](../../../../changelogs/6.0/6.0.15.md) listet Änderungen
  am Editor auf.
- Beschreiben Sie in Ihrer Erklärung zur Barrierefreiheit die Bearbeitungsumgebung nur, wenn Ihr Recht es verlangt, und
  nur mit dem, was Sie getestet haben.

## Verwandte Seiten

- [Der Online-Editor auf TinyMCE 8](../../../funktionen/6.0/online-editor-tinymce8.md)
- [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
