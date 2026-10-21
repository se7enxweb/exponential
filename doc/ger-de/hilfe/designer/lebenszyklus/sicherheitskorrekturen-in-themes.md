---
title: "Sicherheitskorrekturen in Themes"
audience: designer, developer
category: theme-lifecycle
order: 180
summary: Die Sicherheitsprobleme, die ein Theme haben kann - nicht maskierte Ausgabe, Ausgabe im falschen Kontext, eingeschleuste Inline-Skripte, Links, die andere Websites öffnen, offengelegte Einstellungen - wie Sie sie finden, und eine echte Korrektur aus einem veröffentlichten Theme.
keywords: Sicherheit, XSS, Maskierung, wash, wash javascript, Kontext, Inline-Skript, Template-Sicherheit, Korrektur, security, escaping, context, inline script, template security, fix
type: help
---
# Sicherheitskorrekturen in Themes

Diese Seite ist für Designer, die ein Theme betreuen. Templates geben Inhalte aus, die Redakteure und Besucher geschrieben
haben; ein Template, das sie ohne Maskierung ausgibt, lässt jemanden Skripte in den Browsern anderer ausführen
(Cross-Site-Scripting).

## Typische Probleme

| Problem | Beispiel | Korrektur |
|---|---|---|
| Ausgabe nicht maskiert | `{$node.name}` in HTML | `{$node.name\|wash}` ([Wash und Maskierung](../vorlagen/wash-und-maskierung.md)) |
| Für den falschen Kontext maskiert | Nur für JavaScript maskiert, aber der Wert landet in HTML | Für jeden Kontext maskieren, den er durchläuft |
| Werte in Inline-Skripten | Ein Titel, in einen Skriptblock geschrieben | In einem `data-*`-Attribut übergeben; kein Inline-Skript |
| Besuchereingaben in URLs | Ein Suchbegriff in einem Link | Für die URL maskieren, dann für HTML |
| Einstellungen oder Pfade angezeigt | Debug-Ausgabe auf einer Live-Website | Debug nur für Ihre IP; nie Pfade ausgeben |

## Eine echte Korrektur

`cjw_themes_jumper` 0.1.4: „The single-location map escapes the address and the title for HTML before it escapes them
for JavaScript (`|wash|wash( 'javascript' )`). Both go into `L.popup().setContent()`, which reads HTML, so markup in an
address or a title ran in the popup.“ Der Wert lief durch zwei Kontexte, also brauchte er für beide eine Maskierung.

## Schritt für Schritt: nach Problemen suchen

1. Suchen Sie nach Ausgaben ohne `wash`:

   ```bash
   grep -rn -E "\{\\\$[a-z_.]+\}" extension/mytheme/design | grep -v wash | head -50
   ```

   Lesen Sie jeden Treffer; Zahlen und schon maskierte Werte sind in Ordnung.
2. Suchen Sie nach Werten, die in Skripte geschrieben werden: `grep -rn "<script" extension/mytheme/design`.
3. Beheben Sie, testen Sie mit einem Titel, der die Zeichen `<b>"'` enthält, und veröffentlichen Sie
   ([Ein Sicherheitsrelease veröffentlichen](ein-sicherheitsrelease-veroeffentlichen.md)).

## Verwandte Seiten

- [Sicherheitsmeldungen bearbeiten](sicherheitsmeldungen-bearbeiten.md)
- [Checkliste: Code-Review für Templates](../theme-design/checkliste-code-review-fuer-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
