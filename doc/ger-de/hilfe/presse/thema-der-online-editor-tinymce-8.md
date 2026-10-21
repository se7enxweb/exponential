---
title: "Thema: der Online-Editor, TinyMCE 8"
audience: press
category: press-media
order: 52
summary: Ein Themenbeitrag über den Rich-Text-Editor von Exponential - TinyMCE 8.9.2 als optionale zweite Engine neben TinyMCE 3, identisch gespeichertes XML, Linkprüfungen und sichere Uploads - mit druckbaren Fakten, Quellen und Ideen für Überschriften.
keywords: Thema, TinyMCE 8, TinyMCE 3, Online-Editor, ezoe, Rich Text, Editor-Engine, Upload von einer URL, Linkprüfungen, Presse, story, online editor, rich text, editor engine, upload from URL, link checks, press
type: help
---
# Thema: der Online-Editor, TinyMCE 8

Diese Seite ist für Journalisten und Creator, die über den Rich-Text-Editor von Exponential schreiben. Sie erklärt, was
sich im Oktober 2026 geändert hat, was gleich bleibt und welche Fakten Sie mit Quelle drucken können.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
6. [Wen Sie fragen können](#wen-sie-fragen-können)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Der Online-Editor von Exponential hat eine zweite Engine bekommen. „Exponential 6.0.15 ships TinyMCE 8.9.2 as a second
engine of the eZ Online Editor. TinyMCE 3.5.12 stays the default, and nothing changes for existing installations. The
new engine writes the same XHTML that the ezoe input handler reads, so stored XML is identical with both engines and
editors can switch back and forth“ ([Der Online-Editor auf TinyMCE 8](../../../eng-us/features/6.0/online-editor-tinymce8.md)).
Es ist die Geschichte eines behutsamen Upgrades: ein moderner Editor, angeboten neben dem alten, ohne Umwandlung von
Inhalten und mit einem Umschalter unter dem Editor, sodass jeder Redakteur selbst wählen kann. („eZ Online Editor“ ist
der Name der Erweiterung `ezoe`, ein Bezeichner im Code.)

## Was neu ist, und seit wann

Die Linie 6.0.15 ist in Entwicklung und nicht veröffentlicht; die Daten sind Daten der Änderungen.

| Was | Wann | Quelle |
|---|---|---|
| TinyMCE 8.9.2 als optionale zweite Engine, mit den ezoe-Dialogen und Browsertests | 2. Oktober 2026 | [Der Online-Editor auf TinyMCE 8](../../../eng-us/features/6.0/online-editor-tinymce8.md), [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |
| Die Engine-Seite `/ezoe/engine` | 11. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#11-october-2026-the-online-editors-engine-page) |
| Der Engine-Umschalter als ein einziges Bedienelement | 15. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#15-october-2026-the-online-editors-engine-switch-is-one-control) |
| Eine klarere Meldung bei einem toten Link | 15. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#15-october-2026-the-online-editors-message-for-a-dead-link) |
| Härtung des Online-Editors | 16. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-online-editor-hardening) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| TinyMCE 8.9.2 ist optional; TinyMCE 3.5.12 bleibt der Standard. | [Funktionsseite TinyMCE 8](../../../eng-us/features/6.0/online-editor-tinymce8.md) |
| Beide Engines speichern identisches XML, sodass Redakteure hin- und herwechseln können. | ebenda |
| Ein Redakteur kann die Engine persönlich wählen, ohne dass eine Einstellung geändert wird. | [TinyMCE 8, In einer Minute ausprobieren](../../../eng-us/features/6.0/online-editor-tinymce8.md#try-it-in-one-minute) |
| Links auf entfernte Inhalte und Script-Links werden beim Speichern des Textes geprüft. | [TinyMCE 8, Linkprüfungen](../../../eng-us/features/6.0/online-editor-tinymce8.md#link-checks-when-the-text-is-saved) |
| Der Upload lehnt ausführbare Dateinamen wie `shell.php.jpg` ab. | [TinyMCE 8, Sicherheit](../../../eng-us/features/6.0/online-editor-tinymce8.md#security) |
| „Upload von einer URL“ holt nur öffentliche `http`- und `https`-Adressen, mit höchstens 3 Weiterleitungen, die jeweils erneut geprüft werden. | [TinyMCE 8, Upload von einer URL](../../../eng-us/features/6.0/online-editor-tinymce8.md#upload-from-a-url) |
| Ein Wechsel der Engine wird im Audit-Trail festgehalten. | [TinyMCE 8, Sicherheit](../../../eng-us/features/6.0/online-editor-tinymce8.md#security) |
| Das mitgelieferte TinyMCE 8.9.2 wird unter der GPL (Version 2 oder später) verwendet. | [TinyMCE 8, Tests](../../../eng-us/features/6.0/online-editor-tinymce8.md#tests) |

## Was Sie nicht behaupten sollten

- „Exponential ist auf TinyMCE 8 umgestiegen.“ Es ist optional; TinyMCE 3 bleibt der Standard.
- „Inhalte müssen migriert werden.“ Die Quellen sagen, dass das gespeicherte XML identisch ist.
- „Exponential hat TinyMCE entwickelt.“ TinyMCE ist ein eigenständiges Projekt; Exponential liefert es mit und
  integriert es.
- Eine Zahl von Tests als aktuell: Die Funktionsseite nennt 267 Tests „when written (not re-counted for this page)“.
  Zitieren Sie sie mit diesem Vorbehalt oder lassen Sie sie weg.

## Blickwinkel und Ideen für Überschriften

- Upgrade ohne Migration: zwei Engines, ein Speicherformat.
- Wahlfreiheit für Redakteure: Jede Person wählt den Editor, den sie mag.
- Sicherheit im Editor: Linkprüfungen und Upload-Regeln, die eine Redaktion interessieren.

```text
"Two editors, one format: Exponential adds TinyMCE 8 without touching stored content"
"Old editor or new? In Exponential, each editor decides"
"Why a rich text editor checks every link when you press Save"
```

## Wen Sie fragen können

| Frage | Fragen Sie | Wo |
|---|---|---|
| Hintergrund und eine Vorführung | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Technische Details öffentlich | die Community | <https://share.exponential.earth/forums> |
| Ein Sicherheitsbedenken zum Editor | vertraulich, nie öffentlich | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Verwandte Seiten

- [Thema: Inhalte bearbeiten](thema-inhalte-bearbeiten.md)
- [ezoe und TinyMCE 8 (Hinweise zum Upgrade)](../../../eng-us/bc/6.0/ezoe-tinymce8.md)
- [Themenbeiträge: Übersicht](../../../eng-us/help/press/feature-stories-index.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
