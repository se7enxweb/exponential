---
title: "Lektion 2.4: Operatoren, wash und Übersetzungen"
audience: trainer, developer
category: training
order: 24
summary: Die Teilnehmenden verketten String-, Array- und Rechenoperatoren, wählen den richtigen wash-Typ, formatieren Datumsangaben mit l10n und datetime, übersetzen Oberflächentexte mit i18n und untersuchen jede Variable mit attribute show. 50 Minuten.
keywords: entwickler, template-operatoren, wash, email, javascript, shorten, upcase, concat, implode, explode, append, merge, count, inc, sum, l10n, datetime, i18n, attribute show, ezini, übersetzung, datumsformat, developers course, template operators, translations
type: help
---
# Lektion 2.4: Operatoren, wash und Übersetzungen

Operatoren machen aus Werten andere Werte: einen Titel kürzen, eine Liste zusammenfügen, ein Datum formatieren, die
Beschriftung einer Schaltfläche übersetzen. In dieser Lektion verketten die Teilnehmenden Operatoren mit Pipes, lernen
die Operatorfamilien kennen, die der Kernel mitbringt, wählen für jede Stelle, an der ein Wert ausgegeben wird, das
richtige `wash` und untersuchen eine unbekannte Variable.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 2: Die Template-Sprache](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 2.3](schleifen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Operatoren mit Pipes verketten, etwa `{$text|shorten( 40 )|wash}`;
- String-, Array- und Rechenoperatoren aus den Listen des Kernels verwenden;
- `wash`, `wash( 'email' )` oder `wash( 'javascript' )` passend zu der Stelle wählen, an der ein Wert ausgegeben wird;
- Datumsangaben mit `l10n` und `datetime` formatieren und Oberflächentexte mit `i18n` übersetzen;
- eine Variable mit `attribute( 'show' )` untersuchen und eine Einstellung mit `ezini` lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Operatorlisten in `lib/eztemplate/classes/eztemplateautoload.php` und
`kernel/common/eztemplateautoload.php` sowie [String-Template-Operatoren](../../../../../eng-us/features/6.0/string-template-operators.md).

1. **Familien** (5 Minuten). Zeigen Sie die Registrierungen in `lib/eztemplate/classes/eztemplateautoload.php` am
   Projektor. Wissenswerte Familien:

   | Familie | Beispiele |
   |---|---|
   | Strings | `upcase`, `downcase`, `upfirst`, `shorten`, `trim`, `wash`, `concat`, `pad` |
   | Arrays | `array`, `hash`, `append`, `merge`, `contains`, `implode`, `explode`, `reverse`, `unique` |
   | Rechnen | `sum`, `sub`, `inc`, `dec`, `mul`, `div`, `mod`, `round`, `count` |
   | Logik und Typen | `eq`, `gt`, `and`, `is_set`, `is_array`, `cond`, `first_set` |
   | Datum und Gebietsschema | `l10n`, `datetime`, `currentdate` |
   | Kernel | `ezurl`, `ezdesign`, `ezimage`, `ezini`, `i18n`, `fetch` |

2. **Verketten** (3 Minuten). `{concat( 'a', 'b' )|upcase}` gibt `AB` aus: Jede Pipe reicht ihr Ergebnis an die
   nächste weiter. wash kommt zuletzt: `{$title|shorten( 40 )|wash}`.
3. **wash-Typen** (4 Minuten). Der Standard (`xhtml`) maskiert für HTML. `wash( 'email' )` verbirgt zusätzlich das
   `@` und den `.` einer Adresse vor Adresssammlern, wie in `site.ini [WashSettings]` eingestellt.
   `wash( 'javascript' )` maskiert einen Wert für ein JavaScript-String-Literal.
4. **Datumsangaben** (3 Minuten). `{$node.object.published|l10n( 'shortdate' )}` verwendet das Format des
   Gebietsschemas; `{$node.object.published|datetime( 'custom', '%Y-%m-%d' )}` ein eigenes Format.
5. **Übersetzungen** (3 Minuten). `{'Search'|i18n( 'design/standard/content/search' )}`: der Text und sein Kontext.
   Ohne Übersetzung wird der englische Text angezeigt.
6. **Untersuchen** (2 Minuten). `{$node|attribute( 'show', 1 )}` gibt eine Tabelle der Attribute, ihrer Typen und
   Werte aus, eine Ebene tief.

### Häufige Fragen

**Gibt es eine Liste aller Operatoren?** Die oben genannten Registrierungen sind die vollständige Liste für den Kernel;
jede Erweiterung fügt ihre eigenen in `autoloads/eztemplateautoload.php` hinzu. Lektion 7.5 schreibt einen.

**Kann ich jede PHP-Funktion aufrufen?** Nein. Nur die Funktionen, die in `settings/template.ini [PHP]
PHPOperatorList[]` aufgeführt sind, stehen als Operatoren zur Verfügung, zum Beispiel `rawurlencode`.

**Woher kommen die Übersetzungen?** Aus `share/translations/<locale>/translation.ts` und den Ordnern `translations/`
der Erweiterungen. Legen Sie für die Texte Ihrer Erweiterung einen eigenen Kontext an.

### Stolperfallen

- **Doppeltes wash.** `{$text|wash|wash}` zeigt `&amp;lt;`. Maskieren Sie einmal, am Ende.
- **`attribute( 'show' )` auf einer Live-Seite.** Es gibt interne Werte aus; verwenden Sie es nur in der Spielwiese oder
  mit Debug-Ausgabe auf einer Entwicklungsinstallation.
- **`ezini` und Geheimnisse.** Ein Template kann jede Einstellung ausgeben, die es liest. Geben Sie niemals Datenbank-
  oder Mail-Einstellungen aus.

## Praktische Übung

Verwenden Sie die Spielwiese aus Lektion 2.1 und führen Sie sie nach jedem Schritt aus.

1. Verketten Sie Operatoren: `{concat( 'exponential', ' ', 'course' )|upcase}`.
2. Kürzen Sie einen langen Text: `{'A long sentence that needs shortening'|shorten( 15 )}`.
3. Bauen Sie eine Liste und fügen Sie sie zusammen:

   ```
   {def $list = array( 3, 1 )|append( 7 )}
   {$list|implode( '-' )} has {$list|count} items, sum {$list|array_sum}
   {undef $list}
   ```

4. Teilen Sie einen String: `{'a,b,c'|explode( ',' )|count}`.
5. Vergleichen Sie die drei wash-Typen:

   ```
   [{'<b>x</b>'|wash}]
   [{'ann@example.invalid'|wash( 'email' )}]
   [{"It's \"quoted\""|wash( 'javascript' )}]
   ```

6. Formatieren Sie das Veröffentlichungsdatum von Knoten 2 auf zwei Arten:

   ```
   {$node.object.published|l10n( 'shortdate' )}
   {$node.object.published|datetime( 'custom', '%Y-%m-%d' )}
   ```

7. Übersetzen Sie einen Oberflächentext: `{'Search'|i18n( 'design/standard/content/search' )}`.
8. Lesen Sie eine Einstellung: `{ezini( 'SiteSettings', 'SiteName' )|wash}`.
9. Untersuchen Sie den Knoten: `{$node|attribute( 'show', 1 )}`. Suchen Sie `node_id`, `depth` und `url_alias` in der
   Tabelle.
10. Committen Sie das Template.

## Erwartetes Ergebnis

- Schritt 1 gibt `EXPONENTIAL COURSE` aus; Schritt 2 `A long sente...`.
- Schritt 3 gibt `3-1-7 has 3 items, sum 11` aus; Schritt 4 gibt `3` aus.
- Schritt 5: `&lt;b&gt;x&lt;/b&gt;`, die Adresse, bei der das `@` durch den in `[WashSettings]` eingestellten Text
  ersetzt ist, und die Anführungszeichen maskiert als `\047` und `\042`.
- Schritt 6 gibt das Datum im kurzen Format des Gebietsschemas und als `YYYY-MM-DD` aus.
- Schritt 9 gibt eine HTML-Tabelle der Attribute des Knotens aus.
- Der Trainer geht Schritt 5 mit den Teilnehmenden durch und fragt, wohin jeder wash-Typ gehört.

## Quiz

1. Warum steht in `{$title|shorten( 40 )|wash}` das `wash` am Ende?
2. Welchen wash-Typ verwenden Sie für einen Wert in einem JavaScript-String?
   A. `xhtml` B. `email` C. `javascript` D. `pdf`
3. Welche zwei Werte braucht `i18n`?
4. Wo steht die Liste der PHP-Funktionen, die ein Template als Operatoren aufrufen darf?
5. Wie sehen Sie alle Attribute einer unbekannten Variablen?

<details>
<summary>Antworten</summary>

1. Damit der endgültige Text maskiert ist; Operatoren nach `wash` könnten unmaskierte Zeichen hinzufügen oder eine
   Entität in der Mitte abschneiden.
2. **C.** `wash( 'javascript' )`; der Standard `xhtml` ist für HTML.
3. Den Text und seinen Übersetzungskontext, zum Beispiel `'design/standard/content/search'`.
4. In `settings/template.ini`, `[PHP] PHPOperatorList[]`
   ([Templates und Design, Teil 10](../../../../anleitungen/templates-und-design.md#10-einen-wert-für-eine-url-kodieren-rawurlencode-und-rawurldecode)).
5. `{$var|attribute( 'show', 1 )}`.

</details>

## Weiterführende Literatur

- [String-Template-Operatoren](../../../../../eng-us/features/6.0/string-template-operators.md): die in Exponential 6
  hinzugekommenen String-Operatoren.
- [Template-Operatoren für Rollen und Richtlinien](../../../../../eng-us/features/6.0/role-and-policy-template-operators.md):
  `has_role`, `has_policy` und Verwandte.
- [Templates und Design](../../../../anleitungen/templates-und-design.md): Website-Daten und URL-Kodierung in Templates.

## Verwandte Seiten

- [Modul 2: Die Template-Sprache](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
