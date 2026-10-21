---
title: "Lektion 2.2: Das Pagelayout, der Rahmen Ihrer Website"
audience: trainer, designer, developer
category: training
order: 22
summary: Die Teilnehmenden lesen, wie eine Seite zusammengesetzt wird, verwenden die Variablen, die das Pagelayout erhält ($module_result, $site, $current_user), fügen ihrem eigenen Pagelayout einen Sprunglink und einen Breadcrumb hinzu und prüfen die Datei mit eztemplatecheck. 60 Minuten.
keywords: theme-design, pagelayout, pagelayout.tpl, module_result, site, current_user, breadcrumb, sprunglink, landmarks, include, design:, eztemplatecheck, wash, theme design, skip link
type: help
---
# Lektion 2.2: Das Pagelayout, der Rahmen Ihrer Website

Jede Seite eines Siteaccess ist die Ausgabe einer Modul-View, umhüllt von einem einzigen Template, `pagelayout.tpl`.
Im Pagelayout verbringen Designer die meiste Zeit der ersten Tage: Kopfbereich, Kopfzeile, Menüs, Fußzeile. In dieser
Lektion erfahren die Teilnehmenden, was das Pagelayout erhält, und erweitern das Pagelayout, das der Assistent für sie
geschrieben hat.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 2: Templates, Overrides und das Pagelayout](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 2.1](ihre-eigene-design-erweiterung.md): `training_theme` zeichnet den öffentlichen Siteaccess der Teilnehmenden |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Schritte von einer Anfrage bis zur fertigen Seite beschreiben;
- `$module_result.content`, `$module_result.path`, `$site` und `$current_user` in einem Pagelayout verwenden;
- das Pagelayout mit `{include uri='design:...'}` in Teile zerlegen;
- einen Sprunglink, Landmarks und einen Breadcrumb hinzufügen und von Benutzern eingegebenen Text mit `wash` sicher
  ausgeben;
- die Syntax eines Templates mit `eztemplatecheck.php` prüfen, bevor sie den Browser öffnen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Das Pagelayout: der Rahmen Ihrer Website](../../../../../eng-us/help/creators/website-creators/the-pagelayout.md) und
[Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten).

1. **Wie eine Seite zusammengesetzt wird** (4 Minuten). Die Anfrage erreicht `index.php`, und der Siteaccess wird
   bestimmt; die Modul-View läuft (für Inhalte rendert `content/view/full/<node>` das Template `node/view/full.tpl` und
   legt das HTML in `$module_result.content` ab); dann wird das Pagelayout der Design-Kette darum herum gerendert.
2. **Was das Pagelayout erhält** (6 Minuten). Zeigen Sie die Tabelle am Projektor:

   | Variable | Enthält |
   |---|---|
   | `$module_result.content` | das HTML der View, ausgegeben dort, wo der Hauptinhalt hingehört; nie mit `wash` behandeln |
   | `$module_result.path` | die Elemente des Breadcrumbs, jedes mit `text`, `url` und, bei Inhalten, `url_alias` |
   | `$module_result.node_id` | den Knoten einer Inhaltsseite |
   | `$module_result.uri` | die Adresse der View |
   | `$site.title` | den Seitentitel, den der Kernel gebaut hat |
   | `$current_user` | den Benutzer, der die Seite ansieht (`is_logged_in`, `contentobject_id` ...) |

3. **Die Sprache in fünf Zeilen** (5 Minuten). `{$x|wash}` gibt aus und maskiert; `{def}` und `{undef}` deklarieren;
   `{foreach}` durchläuft eine Schleife, `{delimiter}` trennt; `{if}` mit `eq`, `gt` und Verwandten; `|ezurl` erzeugt
   einen Link, der den Siteaccess berücksichtigt, und wird in einem Attribut ohne Anführungszeichen geschrieben.
   Entwickler lernen die ganze Sprache im Entwicklerkurs; Designer brauchen diese Teile.
4. **Teile** (3 Minuten). Öffnen Sie das `pagelayout.tpl` des Assistenten: Es bindet `design:page_head.tpl`,
   `design:page_header.tpl` und `design:page_footer.tpl` ein. `design:` bedeutet „diesen Pfad entlang der Kette
   suchen“, sodass sich ein Teil für sich allein ersetzen lässt.
5. **Prüfen, bevor Sie hinsehen** (2 Minuten). `php bin/php/eztemplatecheck.php -s<site> <file>` gibt
   `Template file valid: <file>` oder `Template file invalid: <file>` mit dem Grund aus.

### Häufige Fragen

**Warum wird `$module_result.content` nicht mit `wash` behandelt?** Es ist HTML, das die View bereits erzeugt (und wo
nötig maskiert) hat. Mit `wash` würden die Tags als Text ausgegeben.

**Woher kommt der Seitentitel?** Die `page_head.tpl` des Assistenten baut ihn aus `$module_result.path`, das letzte
Element zuerst, gefolgt vom Namen der Website. `$site.title` ist die Fassung des Kernels davon.

**Kann ich für einen schnellen Test einen `<style>`-Block ins Pagelayout setzen?** Lassen Sie es: Stile gehören in die
Stylesheets des Designs (Modul 3), und eine Website unter einer Content Security Policy blockiert Inline-Stile und
Inline-Skripte.

### Stolperfallen

- **Ein Fehler im Pagelayout ergibt eine leere Seite.** Führen Sie zuerst `eztemplatecheck.php` aus; die
  Debug-Ausgabe nennt Template und Zeile.
- **Fetches ohne Cache-Block.** Die Kopfzeile des Assistenten holt die Elemente der obersten Ebene bei jeder Anfrage.
  Für die Schulung in Ordnung; Lektion 2.4 legt sie in einen Cache-Block.
- **`wash` vergessen.** Jeder Wert, den ein Benutzer eingegeben hat (Namen, Titel), wird mit `|wash` ausgegeben.

## Praktische Übung

Auf Ihrer Schulungsinstallation, mit eingeschalteten Template-Pfadkommentaren für `<site>` (Lektion 1.3). Alle Dateien
liegen unterhalb von `extension/training_theme/design/training_theme/templates/`.

1. Öffnen Sie `pagelayout.tpl` und suchen Sie das Element `<main>` und darin `{$module_result.content}`.
2. Fügen Sie direkt nach `<body ...>` einen Sprunglink ein:

   ```
   <a class="skip-link" href="#content">{'Skip to content'|i18n( 'design/training_theme' )}</a>
   ```

3. Fügen Sie zwischen dem Include der Kopfzeile und `<main>` einen Breadcrumb ein:

   ```
   <nav class="training_theme-path" aria-label="{'You are here'|i18n( 'design/training_theme' )}">
   {foreach $module_result.path as $item}
       {if $item.url}<a href={cond( is_set( $item.url_alias ), $item.url_alias, $item.url )|ezurl}>{$item.text|wash}</a>{else}<span aria-current="page">{$item.text|wash}</span>{/if}
       {delimiter} / {/delimiter}
   {/foreach}
   </nav>
   ```

4. Speichern Sie und prüfen Sie dann die Syntax:

   ```bash
   php bin/php/eztemplatecheck.php -s<site> extension/training_theme/design/training_theme/templates/pagelayout.tpl
   ```

5. Öffnen Sie eine Seite zwei Ebenen tief in der Website. Prüfen Sie den Breadcrumb und den Sprunglink (drücken Sie
   nach dem Laden einmal die Tabulatortaste).
6. Öffnen Sie `page_footer.tpl` und fügen Sie einen zweiten Absatz hinzu, der einen angemeldeten Benutzer begrüßt:

   ```
   {if $current_user.is_logged_in}<p>{'Signed in as %name'|i18n( 'design/training_theme', '', hash( '%name', $current_user.contentobject.name|wash ) )}</p>{/if}
   ```

7. Prüfen Sie die Datei mit `eztemplatecheck.php` und laden Sie dann eine Seite abgemeldet und angemeldet neu (melden
   Sie sich über `/user/login` im Siteaccess an).
8. Machen Sie das Pagelayout absichtlich kaputt: Löschen Sie ein `{/foreach}`. Führen Sie `eztemplatecheck.php` aus
   und lesen Sie die Meldung. Setzen Sie die Zeile wieder ein und prüfen Sie erneut.
9. Suchen Sie im Seitenquelltext die Kommentare `START` von `page_header.tpl` und `page_footer.tpl` innerhalb der
   Kommentare des Pagelayouts.

## Erwartetes Ergebnis

- Schritt 4 und Schritt 7 geben `Template file valid: ...` aus.
- Schritt 5: Der Breadcrumb zeigt den Pfad, die aktuelle Seite ohne Link; der erste Druck auf die Tabulatortaste zeigt
  den Sprunglink, und wer ihm folgt, gelangt zum Inhalt.
- Schritt 7: Die Begrüßung erscheint nur im angemeldeten Zustand.
- Schritt 8: Die Prüfung meldet das Template als ungültig, bis die Zeile wiederhergestellt ist.
- Der Trainer prüft Breadcrumb und Sprunglink einer Person am Projektor.

## Quiz

1. Welche Variable enthält das HTML der Modul-View, und warum wird sie ohne `wash` ausgegeben?
2. Was bedeutet `design:` in `{include uri='design:page_footer.tpl'}`?
3. Welcher Befehl prüft die Syntax eines Templates, und was gibt er für eine fehlerfreie Datei aus?
4. Warum wird `$item.text` mit `|wash` ausgegeben?
5. Was davon gehört ins Pagelayout?
   A. Ein `<style>`-Block B. `onclick`-Attribute C. Landmarks wie `<header>`, `<nav>`, `<main>`, `<footer>`
   D. Ein `<script>`-Block mit Code

<details>
<summary>Antworten</summary>

1. `$module_result.content`; es ist bereits von der View erzeugtes HTML
   ([Das Pagelayout](../../../../../eng-us/help/creators/website-creators/the-pagelayout.md#what-the-pagelayout-gets)).
2. Das Template entlang der Design-Kette des Siteaccess suchen.
3. `php bin/php/eztemplatecheck.php -s<site> <file>`; er gibt `Template file valid: <file>` aus.
4. Es ist Text, den ein Redakteur eingegeben hat; `wash` maskiert HTML, sodass kein Markup und kein Skript
   eingeschleust werden kann.
5. **C.** Stile und Skripte gehören in die Dateien des Designs (Modul 3).

</details>

## Weiterführende Literatur

- [Das Pagelayout: der Rahmen Ihrer Website](../../../../../eng-us/help/creators/website-creators/the-pagelayout.md): ein minimales
  Pagelayout, Cache-Blöcke und Fehler, die Sie vermeiden sollten.
- [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten):
  die Template-Sprache in zehn Minuten.
- [Menüs und Navigation](../../../../../eng-us/help/creators/website-creators/menus-and-navigation.md): Menüs über die oberste Ebene
  hinaus.
- [Barrierefreiheit für Website-Bauer](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md): warum
  Sprunglink und Landmarks wichtig sind (Modul 5).

## Verwandte Seiten

- [Modul 2: Templates, Overrides und das Pagelayout](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
