---
title: "Die GPL für Theme-Code"
audience: designer
category: theme-distribution
order: 250
summary: Warum die meisten Themes für Exponential unter GPL-2.0-or-later stehen, was die GPL von Ihnen und von Menschen verlangt, die Ihr Theme weitergeben, der Unterschied zwischen „only“ und „or later“ und wie Sie sie anwenden. Keine Rechtsberatung.
keywords: GPL, GPL-2.0-or-later, GPL-2.0-only, GPL-3.0, Copyleft, abgeleitetes Werk, Quellcode, Theme-Code, Lizenzhinweis, copyleft, derivative work, source code, theme code, license notice
type: help
---
# Die GPL für Theme-Code

Diese Seite ist für Designer, die eine Lizenz für den Code eines Themes wählen: Templates, Stylesheets, Skripte und PHP.
Sie erklärt die übliche Begründung; sie ist keine Rechtsberatung.

## Warum die GPL die übliche Wahl ist

- Exponential steht unter `GPL-2.0-or-later`, ebenso die Themes, die 7x veröffentlicht.
- Templates und PHP eines Themes laufen innerhalb von Exponential, rufen seine Funktionen auf und überschreiben seine
  Templates. Viele Autoren und die Free Software Foundation betrachten solchen Code als ein auf dem Programm beruhendes
  Werk, das dann zu vereinbaren Bedingungen verbreitet werden muss. Dieselbe Lizenz zu wählen, erspart die Frage.
- Die GPL erlaubt jedem, das Theme zu nutzen, zu studieren, zu ändern und weiterzugeben, und erhält diese Rechte für
  jeden, der eine geänderte Kopie erhält.

## Was die GPL verlangt

| Von Ihnen, wenn Sie das Theme verbreiten | Von anderen, wenn sie es verbreiten |
|---|---|
| Den Lizenztext beilegen und die Hinweise erhalten | Ihre Hinweise und die Lizenz erhalten |
| Den Quellcode verfügbar machen (ein öffentliches Repository tut das) | Den Quellcode ihrer geänderten Fassung weitergeben |
| Keine Einschränkungen über die Lizenz hinaus hinzufügen | Ebenfalls keine Einschränkungen hinzufügen |

Das Theme auf einer Website zu nutzen, ist kein Verbreiten. Eine Kopie an einen Kunden zu schicken oder sie zu
veröffentlichen, schon.

## „Only“ oder „or later“

| Bezeichner | Bedeutet |
|---|---|
| `GPL-2.0-or-later` | Version 2 oder nach Wahl des Empfängers jede spätere Version. Passt zu Exponential |
| `GPL-2.0-only` | Version 2 und keine andere |
| `GPL-3.0-or-later` | Version 3 oder später |

`GPL-2.0-or-later` hält Ihr Theme mit Exponential und mit Code unter der GPL 3 kombinierbar.

## Schritt für Schritt

1. Kopieren Sie den offiziellen Text der GPL 2 in `LICENSE`.
2. Schreiben Sie den Copyright-Hinweis und „licensed under the GNU General Public License v2.0 or any later version“ in
   das README ([Copyright-Hinweise in Dateien](copyright-hinweise-in-dateien.md)).
3. Setzen Sie `"license": "GPL-2.0-or-later"` in `composer.json` und den vollen Namen in `ezinfo.php` und `extension.xml`.
4. Prüfen Sie, dass jeder Bestandteil Dritter unter einer mit der GPL vereinbaren Lizenz steht
   ([Schriften, Icons und Bibliotheken Dritter](schriften-icons-und-bibliotheken-dritter.md)).

## Verwandte Seiten

- [Die GPL und der Verkauf](../verkauf/gpl-und-verkauf.md)
- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
