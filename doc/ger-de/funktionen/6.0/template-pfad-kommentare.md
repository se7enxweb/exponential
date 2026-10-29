# Template-Pfad-Kommentare: sehen, welches Template welches Markup geschrieben hat

Diese Seite ist für Template-Entwickler, die die `.tpl`-Datei hinter einem Stück HTML finden müssen. Mit eingeschalteten
Template-Pfad-Kommentaren wird jedes ausgegebene Template von zwei HTML-Kommentaren umschlossen, die es nennen: dieselbe
Hilfe, die die Debug-Ausgabe von Symfony und Twig gibt. Hinzugefügt am 30. Juli 2026 (`58cc4bd4af`); verbessert am
5. und 6. August (`d2f4e1830d`, `da9ef35eff`, `8b31fd9801`, `08dfef7659`).

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...the footer markup...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

## Einschalten

1. Setzen Sie in `settings/override/site.ini.append.php` oder in dem Siteaccess, den Sie untersuchen:

   ```ini
   [TemplateSettings]
   ShowTemplatePathComments=enabled
   ```

   Oder schalten Sie es in der [Exp-Debug-Leiste](exp-debug-leiste.md) in der Gruppe Templates um (Einstellung „Template
   path comments“, `settings/debugbar.ini [Setting_template_path_comments]`), die denselben Schlüssel schreibt.

2. Leeren Sie den Template-Cache, denn kompilierte Templates enthalten die Prüfung:

   ```bash
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   ```

3. Öffnen Sie den Seitenquelltext im Browser, suchen Sie das Markup, das Sie interessiert, und lesen Sie das nächste
   `START` darüber.

Es funktioniert mit ein- oder ausgeschaltetem Template-Compiler und für `{include}` ebenso wie für das Haupttemplate der
Seite.

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/site.ini` | `TemplateSettings` | `ShowTemplatePathComments` | `disabled` | global oder Siteaccess | `enabled` umschließt jedes ausgegebene Template mit START- und STOP-Kommentaren. Jeder andere Wert bedeutet aus. |

## Die Quelle der Überschreibung lesen

Exponential löst einen Template-Namen wie `node/view/full.tpl` nach den Regeln von `override.ini` zu einer Datei auf.
Der Kommentar nennt die Datei, die wirklich lief, und, wenn sie sich vom angefragten Namen unterscheidet, das Original in
Klammern:

```html
<!-- START design/media/templates/node/view/full/article.tpl (node/view/full.tpl) -->
```

Lesen Sie es als „die Datei `article.tpl` hat die Anfrage `node/view/full.tpl` beantwortet“. Seit dem 6. August
(`8b31fd9801`) ist der Text in Klammern die `Source` des passenden Blocks in `override.ini`. Eine passende
Überschreibung, die sich nicht zurückverfolgen lässt, fällt auf den Template-Namen zurück.

Dieselbe Ortsangabe wird den Warnungen des Template-Compilers hinzugefügt (`08dfef7659`): Eine Warnung sagt jetzt, in
welchem Template und in welcher Zeile das Problem steckt, nicht nur, dass es eines gibt.

## Grenzen und Vorsicht

- **Nur für die Entwicklung.** Die Kommentare fügen der Ausgabe jedes Templates Markup hinzu. Sie zerstören JSON-, XML-
  und RSS-Ausgaben und alles, was das HTML streng parst. Lassen Sie den Schlüssel in der Produktion und auf jedem
  Siteaccess, der Feeds ausliefert, auf `disabled`.
- Der Kommentartext ersetzt `--` durch `- -` und entfernt `>`, sodass ein Pfad den Kommentar nie vorzeitig beenden
  kann.
- Ein Template, das in einen Start-Tag schreibt, etwa `parts/exp_content_direction.tpl` in
  `<h1 class="context-title"{include ...}>`, bekommt keine Kommentare: Einer im Tag würde ihn vorzeitig beenden, und
  die Seite zeigte ein einzelnes `>`. Solche Templates stehen in `site.ini [TemplateSettings]
  TemplatesWithoutDebugComments[]` mit ihrem Pfad unterhalb von `templates/`; eigene tragen Sie dort ein. Die Liste
  gilt auch für `Debug=enabled`. Leeren Sie nach einer Änderung der Liste den Inhalts-Cache.
- Überall sonst braucht es keine Liste: Jede gerenderte Ausgabe (`eZTemplate::fetch()`) wird einmal geprüft, und ein
  Kommentar, der in einem Start- oder End-Tag, einem Attributwert, `<script>`, `<style>`, `<title>`, `<textarea>` oder
  einem anderen Kommentar gelandet ist, wird herausgenommen (`eZTemplate::removeMisplacedDebugComments()`). Eine
  Ausgabe, die kein HTML ist (XML, JSON, Klartext wie eine Textmail, CSV oder iCal), bekommt keine. Ein `set-block`
  oder `append-block`, der einen Wert statt HTML erfasst (einen Namen, eine URL, Attribute, Klartext, nichts),
  bekommt ebenfalls keine (`eZTemplate::capturedText()`): Eine Prüfung auf `''`, ein daraus gebildeter Template-Name
  und ein damit gefülltes Attribut funktionieren wie ohne Kommentare; erfasstes HTML behält seine Kommentare. Die
  Liste braucht es nur noch für Ausgabe, die auf anderem Weg in die Seite gelangt; ihr Standard ist
  `parts/exp_content_direction.tpl`.
- Mit `Debug=enabled` werden die Kommentare in das Template kompiliert; solche Templates werden in eigene Dateien
  kompiliert (der Dateiname trägt den Zustand von `Debug`, `ShowXHTMLCode` und der Liste), sodass das Umschalten von
  `Debug` oder eine Änderung der Liste kein Leeren des Template-Caches braucht. Die Liste wird einmal pro Anfrage in
  ein Nachschlage-Array gelesen, und das Ergebnis jedes Templates bleibt erhalten: Die Prüfung je Include kostet einen
  Schlüsselzugriff.
- Zwischengespeicherte Ausgabe behält die Kommentare, mit denen sie ausgegeben wurde. Leeren Sie die Inhalts- und
  Template-Caches, wenn Sie den Schlüssel umschalten.
- Eine Anfrage an einen Siteaccess liest nicht mehr die Überschreibungsliste eines anderen Siteaccess (eine spätere
  Korrektur, siehe den [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)).

## Verwandte Seiten

- [Exp-Debug-Leiste](exp-debug-leiste.md), [Referenz der Debug-Leiste](../../bc/6.0/debug-leiste.md)
- [Template-Editor: Überschreibungen anlegen, ordnen und bearbeiten](template-editor-ueberschreibungen.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Juli 2026](../../geschichte/2026/2026-07.md), [August 2026](../../geschichte/2026/2026-08.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
