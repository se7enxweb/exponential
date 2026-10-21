---
title: "Lektion 3.5: Caches für Template-Entwickler"
audience: trainer, developer
category: training
order: 35
summary: Die Teilnehmenden unterscheiden den Cache der kompilierten Templates, den View-Cache der Inhalte und Cache-Blöcke, leeren genau den Cache, den eine Änderung braucht, und schreiben in einem Teil des Pagelayouts einen cache-block mit Schlüsseln und Ablaufzeit. 45 Minuten.
keywords: entwickler, cache, cache leeren, TemplateCompile, ViewCaching, CachedViewModes, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, ezcache.php, clear-tag, clear-id, template-block, eztc, view-cache, developers course, clear the cache, view cache
type: help
---
# Lektion 3.5: Caches für Template-Entwickler

Exponential ist schnell, weil es auf mehreren Ebenen zwischenspeichert, und Template-Entwickler begegnen jeder davon:
Eine Änderung, die „nicht funktioniert“, ist oft ein Cache, der noch das alte Ergebnis enthält. Diese Lektion erklärt
die drei Caches, durch die ein Template läuft, wie man genau den leert, den eine Änderung braucht, und wie man
`{cache-block}` in eigenen Templates verwendet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 3: Designs, Overrides und Fehlersuche](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 3.4](das-template-hinter-einer-seite-finden.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Cache der kompilierten Templates, den View-Cache der Inhalte und Cache-Blöcke beschreiben;
- Caches mit `php bin/php/ezcache.php` nach Tag und nach ID leeren;
- den zu leerenden Cache für eine Template-Bearbeitung, einen neuen Override, eine Änderung der Einstellungen und eine
  Änderung eines Stylesheets wählen;
- einen `{cache-block}` mit `keys` und `expiry` schreiben und wissen, wann `subtree_expiry` und
  `ignore_content_expiry` helfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Caches](../../../../anleitungen/caches.md), `settings/site.ini` (`[TemplateSettings]`, `[ContentSettings]`)
und `lib/eztemplate/classes/eztemplatecachefunction.php`.

1. **Drei Ebenen** (6 Minuten).

   | Cache | Einstellung (Standard) | Enthält | Leeren mit |
   |---|---|---|---|
   | Kompilierte Templates | `[TemplateSettings] TemplateCompile=enabled` | Jede `.tpl`, zu PHP kompiliert | `--clear-tag=template` |
   | View-Cache der Inhalte | `[ContentSettings] ViewCaching=enabled`, `CachedViewModes=full;sitemap;pdf` | Das gerenderte Ergebnis einer Knotenansicht | `--clear-tag=content` oder pro Knoten mit `ezcontentcache.php` |
   | Cache-Blöcke | `[TemplateSettings] TemplateCache=enabled` | Teile eines Templates, die in `{cache-block}` eingeschlossen sind | `--clear-id=template-block` |

2. **Leeren nach Tag und ID** (4 Minuten). `php bin/php/ezcache.php --list-tags` und `--list-ids` zeigen, was es
   gibt. Tags fassen IDs zusammen (`content`, `template`, `ini` ...); IDs sind einzelne Caches (`template-override`,
   `template-block`, `design_base`, `ezjscore-packer` ...). Gewöhnen Sie sich `--clear-all` nicht an: Es kostet die
   nächsten Besucher jeweils eine langsame Seite.
3. **Welcher Cache für welche Änderung** (4 Minuten).

   | Sie haben geändert | Leeren |
   |---|---|
   | Eine vorhandene `.tpl` | `template` (nichts, solange `TemplateCompile=disabled`), dann `content` für zwischengespeicherte Seiten |
   | Eine neue Override-Regel oder einen neuen Template-Pfad | `ini`, `template-override`, `template`, `content` |
   | Eine Einstellung | `ini` |
   | Ein Stylesheet oder Skript, das der Packer lädt | `ezjscore-packer` und `template-block` |

4. **`cache-block`** (6 Minuten). `{cache-block keys=array( $uri_string ) expiry=3600}...{/cache-block}` speichert die
   Ausgabe einmal pro Schlüsselkombination. Ohne `expiry` gelten standardmäßig zwei Stunden. Standardmäßig verfällt
   ein Cache-Block, sobald irgendein Inhalt veröffentlicht wird; `ignore_content_expiry` schaltet das ab,
   `subtree_expiry` beschränkt es auf einen Teilbaum.

### Häufige Fragen

**Sollte ich das Kompilieren auf meinem Entwicklungsrechner abschalten?** Viele Entwickler setzen beim Bearbeiten von
Templates `TemplateCompile=disabled`, damit jede Änderung frisch gelesen wird. Niemals auf einer Live-Website.

**Was tut `eztc.php`?** Es kompiliert alle Templates eines Siteaccess im Voraus (`php bin/php/eztc.php -s<sa>`),
damit die ersten Besucher nach einem Deployment nicht für das Kompilieren bezahlen.

**Kann die Administration Caches leeren?** Ja: **Setup** > **Cache-Verwaltung** oder der Kasten **Cache leeren** mit
**Gesamter Cache**, **Inhalt**, **Template**, **Template und Inhalt**, **Ini Einstellungen** und **Statisch**.

### Stolperfallen

- **Ein Cache-Block ohne Schlüssel um benutzerbezogene Inhalte.** Alle sehen dann die Fassung des ersten Besuchers,
  zum Beispiel dessen Namen. Halten Sie benutzerbezogene Teile außerhalb von Cache-Blöcken oder versehen Sie sie mit
  den richtigen Schlüsseln.
- **Leeren vor dem Neuladen der PHP-Worker.** Auf einem Server, der PHP-Code im Speicher hält (Velocity, PHP-FPM),
  leeren Sie die Caches erst, wenn die Worker den neuen Code haben, sonst werden alte Seiten erneut zwischengespeichert
  (Lektion 13.2).
- **Stylesheet-Änderungen, die nicht erscheinen.** Der Name der gepackten Datei ist in einem Cache-Block des
  Pagelayouts zwischengespeichert: Leeren Sie auch `template-block`.

## Praktische Übung

Auf der Entwicklungsinstallation, mit `TemplateCompile=enabled` (der Standard).

1. Listen Sie die Cache-Tags und -IDs auf: `php bin/php/ezcache.php --list-tags` und `php bin/php/ezcache.php --list-ids`.
2. Ändern Sie den Text der Kurszeile in `mysite_article.tpl` aus Lektion 3.2 und laden Sie einen Artikel neu.
   Notieren Sie, ob die Änderung erscheint.
3. Leeren Sie nur den Template-Cache und laden Sie neu; leeren Sie dann den Tag content und laden Sie neu. Notieren
   Sie, welcher Schritt die Änderung sichtbar gemacht hat.
4. Leeren Sie den Cache nur eines Knotens:
   `php bin/php/ezcontentcache.php --clear-node=<node ID>` und sehen Sie sich die Hilfe mit `--help` an.
5. Fügen Sie `mysite_article.tpl` einen Cache-Block hinzu:

   ```
   {cache-block keys=array( $node.node_id ) expiry=60}
       <p>Cached at {currentdate()|l10n( 'shortdatetime' )}</p>
   {/cache-block}
   ```

6. Leeren Sie die Tags `template` und `content` und laden Sie den Artikel zweimal neu: Die Uhrzeit bleibt gleich.
7. Veröffentlichen Sie in der Administration eine Änderung an einem beliebigen Element und laden Sie neu: Der Block
   wurde erneuert, weil Cache-Blöcke standardmäßig beim Veröffentlichen verfallen.
8. Fügen Sie den Parametern des Blocks `ignore_content_expiry` hinzu, leeren Sie die Template- und Inhalts-Caches und
   wiederholen Sie Schritt 7: Jetzt bleibt die Uhrzeit, bis die 60 Sekunden vorbei sind.
9. Kompilieren Sie die Templates Ihres Siteaccess im Voraus: `php bin/php/eztc.php -s<sa>`.
10. Entfernen Sie den Cache-Block wieder, leeren Sie die Caches und committen Sie.

## Erwartetes Ergebnis

- Schritt 3: Das Leeren des Template-Caches allein genügt nicht immer, wenn die Seite selbst im View-Cache liegt; nach
  dem Leeren des Inhalts-Caches erscheint die Änderung.
- Schritt 6: Zwei Neuladevorgänge zeigen dieselbe Uhrzeit; Schritt 7 zeigt eine neue Uhrzeit; Schritt 8 behält die
  Uhrzeit bis zum Ablauf.
- Schritt 9 listet die kompilierten Templates auf und endet ohne Fehler.
- Der Trainer fragt alle Teilnehmenden, welche Caches ein neuer Override braucht.

## Quiz

1. Welche Einstellung schaltet den View-Cache der Inhalte ein oder aus?
   A. `TemplateCompile` B. `ViewCaching` C. `TemplateCache` D. `DesignLocationCache`
2. Was ist der Unterschied zwischen `--clear-tag` und `--clear-id`?
3. Welche Caches braucht eine neue Override-Regel mit einem neuen Template?
4. Wann verfällt ein `{cache-block}` ohne Parameter?
5. Warum darf ein Block, der den Namen des aktuellen Benutzers zeigt, nicht ohne die richtigen Schlüssel
   zwischengespeichert werden?

<details>
<summary>Antworten</summary>

1. **B.** `[ContentSettings] ViewCaching`.
2. Ein Tag fasst mehrere Caches zusammen; eine ID nennt einen einzelnen Cache.
3. `ini`, `template-override`, `template` und `content` für bereits zwischengespeicherte Seiten.
4. Nach den standardmäßigen zwei Stunden oder früher, wenn Inhalte veröffentlicht werden.
5. Die Ausgabe des ersten Besuchers würde allen gezeigt.

</details>

## Weiterführende Literatur

- [Caches: was jeder enthält und wie man ihn leert](../../../../anleitungen/caches.md).
- [Caches vorab füllen](../../../../anleitungen/caches-vorwaermen.md): Seiten nach einem Deployment aufwärmen.
- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md): danach leeren, was sich geändert hat.

## Verwandte Seiten

- [Modul 3: Designs, Overrides und Fehlersuche](README.md)
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
