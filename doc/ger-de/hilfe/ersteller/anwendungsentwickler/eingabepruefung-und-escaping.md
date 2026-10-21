---
title: Eingabeprüfung und Escaping
audience: app-creator, developer
category: app-creation
order: 580
summary: Jede Eingabe als nicht vertrauenswürdig und jede Ausgabe als zu maskieren behandeln - Typumwandlung und Positivlisten in PHP, Prüfung von Ansichtsparametern und POSTs, wash in Templates für HTML und Attribute, URLs, JSON und SQL sowie die häufigen Fehler.
keywords: Eingabeprüfung, Ausgabe maskieren, Escaping, XSS, Typumwandlung, Positivliste, Ansichtsparameter, URL-Kodierung, SQL-Maskierung, Anwendungsentwickler, input validation, output escaping, wash, htmlspecialchars, casting, allow list, view parameters, JSON, URL encoding, SQL escaping, app creator
type: help
---
# Eingabeprüfung und Escaping

Diese Seite richtet sich an Anwendungsentwickler, die Daten aus Anfragen, Importen oder APIs verarbeiten. Zwei Regeln
verhindern die meisten Sicherheitsfehler: Prüfen Sie Eingaben dort, wo sie hereinkommen, und maskieren Sie Ausgaben
dort, wo sie hinausgehen, im Format des Ortes, an den sie gelangen.

## Inhalt

1. [Eingaben prüfen](#eingaben-prüfen)
2. [Ausgaben maskieren](#ausgaben-maskieren)
3. [Kontexte und ihre Maskierung](#kontexte-und-ihre-maskierung)
4. [Häufige Fehler](#häufige-fehler)

## Eingaben prüfen

| Eingabe | Prüfung |
|---|---|
| IDs und Zahlen | `(int)`, danach eine Bereichsprüfung (`max( 1, min( 100, (int)$limit ) )`) |
| Auswahlwerte | eine Positivliste: `in_array( $sort, array( 'name', 'published' ), true )` |
| Text | Längenbegrenzung, trim, die für das Feld erlaubten Zeichen |
| E-Mail, URL | die Prüfung des Datentyps oder `filter_var()`; bei URLs zusätzlich das Schema (nur `https`) |
| Dateien | Typ nach Inhalt und Größe, gespeichert über den Kernel |
| Weiterleitungsziele | nur Adressen der Website ([Sichere Weiterleitungen](../../../../eng-us/features/6.0/safe-redirects.md)) |

Ansichtsparameter (`$Params`) und ungeordnete Parameter sind Zeichenketten aus der Adresse und genauso wenig
vertrauenswürdig wie POST-Daten.

## Ausgaben maskieren

In Templates läuft jeder Wert von Benutzern oder aus der Datenbank durch `|wash`:

```text
<h1>{$item.title|wash}</h1>
<a href={$item.url|ezurl} title="{$item.title|wash}">...</a>
<div data-conf="{json_encode( hash( 'id', $item.id ) )|wash}"></div>
```

Ausnahmen sind Werte, die konstruktionsbedingt bereits sicheres HTML sind: `$module_result.content`, die Ausgabe von
`attribute_view_gui` und die des Rich-Text-Renderings.

## Kontexte und ihre Maskierung

| Die Ausgabe landet in | Maskieren mit |
|---|---|
| HTML-Text und Attributen | `|wash` (Templates), `htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' )` (PHP) |
| URLs in Templates | `|ezurl` für Pfade der Website; `rawurlencode()` für Teile einer URL ([Templates und Design, Teil 10](../../../anleitungen/templates-und-design.md#10-einen-wert-für-eine-url-kodieren-rawurlencode-und-rawurldecode)) |
| JSON | `json_encode()`; innerhalb eines `<script type="application/json">` zusätzlich `<` und `>` ersetzen |
| SQL | Typumwandlungen und `$db->escapeString()` oder Bedingungen von `eZPersistentObject` |
| Shell-Befehlen | vermeiden; sonst `escapeshellarg()` |
| CSV für Tabellenkalkulationen | Werten, die mit `=`, `+`, `-`, `@` beginnen, ein Präfix voranstellen (der CSV-Export des Kernels tut das) |
| Logzeilen | Zeilenumbrüche entfernen; niemals Geheimnisse protokollieren |

## Häufige Fehler

- `{$value}` ohne `|wash`, weil „es nur eine Zahl ist“: Eine spätere Änderung macht Text daraus.
- Einem versteckten Formularfeld vertrauen.
- Eine Weiterleitung aus `$_SERVER['HTTP_REFERER']` bauen.
- Bei der Eingabe statt bei der Ausgabe maskieren (doppelte Maskierung oder gar keine bei einer neuen Ausgabe).
- Ein JSON-Wert, der in ein Inline-Skript geschrieben wird: Verwenden Sie stattdessen einen Datenblock oder ein Attribut.

## Verwandte Seiten

- [Formulare, CSRF und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md)
- [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md)
- [Sicherheitsstandard](../../../../eng-us/standards/security.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
