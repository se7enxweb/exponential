---
title: Wash und Maskierung
audience: designer, developer
category: templates
order: 230
summary: Warum und wie jeder ausgegebene Wert mit wash maskiert wird - die Modi xhtml, email, javascript und pdf, was kein wash braucht (Rich-Text-Ausgabe, URL-Operatoren), Maskierung in Attributen, JSON für Skripte und die Fehler, die Lücken für Cross-Site-Scripting öffnen.
keywords: wash, maskieren, Maskierung, XSS, Cross-Site-Scripting, htmlspecialchars, wash email, wash javascript, Maskierung in Attributen, json_encode, Ausgabekodierung, Sicherheit, escape, escaping, cross site scripting, attribute escaping, output encoding, security
type: help
---
# Wash und Maskierung

Diese Seite ist für alle, die Templates schreiben. Alles, was ein Template ausgibt und aus Inhalten, Einstellungen, der URL oder
von einem Benutzer stammt, muss **maskiert** werden, sonst kann ein Besucher HTML und Skripte in die Seite einschleusen. In
Exponential-Templates ist der Operator zum Maskieren `wash`.

## Inhalt

1. [Die Regel](#die-regel)
2. [Die Modi](#die-modi)
3. [Was nicht maskiert wird](#was-nicht-maskiert-wird)
4. [In Attributen](#in-attributen)
5. [Werte für Skripte](#werte-für-skripte)
6. [Fehler](#fehler)

## Die Regel

```tpl
<h1>{$node.name|wash}</h1>
<p>{$search_text|wash}</p>
<input value="{$query|wash}">
```

Maskieren Sie jeden Wert an der Stelle, an der er in HTML ausgegeben wird. Zuerst formen, zuletzt maskieren:
`{$text|shorten( 100 )|wash}`.

## Die Modi

Aus `eZTemplateStringOperator::wash()` (`lib/eztemplate/classes/eztemplatestringoperator.php`):

| Aufruf | Tut |
|---|---|
| `\|wash` oder `\|wash( 'xhtml' )` | `htmlspecialchars` mit Anführungszeichen: `<`, `>`, `&`, `"`, `'` werden zu Entitäten |
| `\|wash( 'email' )` | dasselbe, dann werden `.` und `@` durch `template.ini [WashSettings] EmailDotText` und `EmailAtText` ersetzt (ein einfacher Spamschutz) |
| `\|wash( 'javascript' )` | maskiert für eine JavaScript-Zeichenkette |
| `\|wash( 'pdf' )` | bereitet Text für den PDF-Export vor |

## Was nicht maskiert wird

- **Rich-Text-Ausgabe**: `{attribute_view_gui attribute=$node.data_map.body}` rendert über die Templates des Datentyps, die Text
  selbst maskieren; ihr HTML erneut zu maskieren, würde Tags als Text zeigen.
- **URL-Operatoren**: `ezurl`, `ezroot`, `ezimage`, `ezdesign` maskieren, was sie zurückgeben (der URL-Operator des Kernels wendet
  `htmlspecialchars` auf die Pfade an, die er baut).
- **Werte, die Sie selbst aus Konstanten gebaut haben** (ein Klassenname, den Sie in das Template geschrieben haben).
- **Übersetzte Texte ohne Benutzereingaben**: `{'Search'|i18n( 'design/mytheme' )}` gibt eine Übersetzung aus; maskieren Sie sie,
  wenn sie in ein Attribut geht oder Platzhalter mit Benutzerwerten trägt.

## In Attributen

Setzen Sie Attribute immer in Anführungszeichen und maskieren Sie den Wert:

```tpl
<a href={$node.url_alias|ezurl} title="{$node.name|wash}">
<img alt="{$image.alternative_text|wash}" ...>
<div data-label="{'Close'|i18n( 'design/mytheme' )|wash}">
```

## Werte für Skripte

Bauen Sie kein JavaScript in Templates. Übergeben Sie Skripten Werte als JSON-Daten, die `json_encode` (ezjscore) für ihren
Kontext korrekt maskiert:

```tpl
<script type="application/json" id="mytheme-data">{json_encode( hash( 'title', $node.name ) )}</script>
```

([Kein Inline-JavaScript](../theme-design/kein-inline-javascript.md)).

## Fehler

- **Zu früh maskieren** und den Wert dann in einer Berechnung oder einem Vergleich verwenden: Maskieren Sie nur bei der Ausgabe.
- **Doppelt maskieren**: `&amp;amp;` auf der Seite. Maskieren Sie einmal.
- **Werte der Anfrage unmaskiert ausgeben**: `{ezhttp( 'q', 'get' )}` muss `{ezhttp( 'q', 'get' )|wash}` sein.
- **Einstellungen vertrauen**: Werte aus INI-Dateien werden von vielen Templates ausgegeben; maskieren Sie auch sie.

Eine Content Security Policy ist die zweite Verteidigungslinie, wenn ein Template das Maskieren vergisst, kein Ersatz dafür
([CSP-sicheres Design](../theme-design/csp-sicheres-design.md)).

## Verwandte Seiten

- [URL-Operatoren](url-operatoren.md)
- [Der Operator ezhttp](der-ezhttp-operator.md)
- [Häufige Template-Fehler](haeufige-template-fehler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
