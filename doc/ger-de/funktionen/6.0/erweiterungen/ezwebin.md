# ezwebin: das Design Website Interface

Diese Seite ist für Website-Entwickler, die von der klassischen fertigen Website ausgehen, und für alle, die eine darauf
beruhende Website aktualisieren. `ezwebin` („Exponential Website Interface LS“) enthält:

- ein Design mit Templates für Artikel, Blogs, Veranstaltungen, Foren, Galerien, Produkte, RSS- und Shop-Seiten;
- die Inhaltsklassen und Installationspakete dahinter;
- das Seitenlayout mit Menüs, Suchfeld und Fußzeile;
- die Templates der Bearbeitungsseite für Redakteure.

Es erweitert [ezwt](ezwt.md) (die Website-Werkzeugleiste) und benötigt `ezjscore`. Die Linie 6.0.x ist das Release der
Extension für Exponential; 6.0.16 (2. Oktober 2026) ist aktuell.

Für eine neue Website zeigt [ezdemo](ezdemo.md) das Design mit Demo-Inhalten, und das
[Simple-Theme](sevenx_themes_simple.md) ist eine leichtere Alternative auf Grundlage derselben Klassen.

## Eine Website auf das aktuelle Release aktualisieren

1. Installieren Sie `se7enxweb/expui` (es wird verlangt), damit die Datumsfelder nach dem Wegfall von YUI einen
   funktionierenden Kalender behalten.
2. Erzeugen Sie die Autoloads neu und leeren Sie die Template-Caches, damit die HTML5-Templates und die neuen
   Übersetzungsdateien verwendet werden:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=template,content --allow-root-user
```

3. Wenn Ihr Website-Design Datumsfelder, die Gruppen der Bearbeitungsseite oder das Template „Passwort vergessen“
   überschreibt, vergleichen Sie sie mit den Änderungen unten.

## Was sich in den Releases von Exponential 6 geändert hat

| Release | Änderung |
|---|---|
| 6.0.1 | Massenaktualisierung aus dem übergeordneten Paket-Repository `ezwebin-ezpackage`. |
| 6.0.2 | Pfadprüfung und Berechnung der Seitentiefe korrigiert. |
| 6.0.3 | **HTML5-Markup**: Die selbstschließenden Schrägstriche von XHTML (`<br />`, `<input />`, `<img />`, `<meta />`, `<link />` und andere leere Elemente) und veraltete Attribute `type` und `language` an Tags `<script>`, `<style>` und Stylesheet-`<link>` sind in 57 Dateien entfernt. Wenn Sie diese Templates überschreiben, rechnen Sie in Ihren mit derselben Bereinigung. |
| 6.0.4 bis 6.0.8 | `ezinfo.php` und `extension.xml` geben Name, Version, Copyright, Lizenz und Website an, sodass Info-Seite und Aktualisierungsprüfungen übereinstimmen; sichtbare Texte nennen Exponential. |
| 6.0.7 | **Passwort vergessen**: Die Seite verrät nicht mehr, ob zu einer Adresse ein Konto gehört, und maskiert, was sie ausgibt ([Einzelheiten](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#2-seiten-für-vergessene-passwörter-verraten-nicht-mehr-ob-eine-adresse-ein-konto-hat)). |
| 6.0.8 | **Fehlerseiten** zeigen ihren eigenen Fehler im Seitentitel ([Einzelheiten](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#3-fehlerseiten-zeigen-ihren-eigenen-fehler-im-seitentitel)). |
| 6.0.9, 6.0.10 | Jeder sichtbare Text ist ein Übersetzungstext mit Deutsch: der Veranstaltungskalender, das Forum, die Tag-Wolke, die Produkt- und Shop-Texte, die Seiten für RSS-Export, -Import und -Liste, die Zusammenfassung und die Summen der Bestellbestätigung, die Tags von Blogbeiträgen und die Meldung beim Dokumentimport. Englisch und der unübersetzte Katalog führen jede Meldung des deutschen. |
| 6.0.12 | Der Operator für das **Blog-Archiv** `ezarchive()` listet die Monate auch auf Oracle auf (er hatte nur Abfragen für MySQL und PostgreSQL; Oracle erhält ein aus der Epoche berechnetes Datum). Klassenbezeichner und ID des übergeordneten Knotens werden maskiert und umgewandelt. |
| 6.0.13 | Die aufklappbaren Attributgruppen der Bearbeitungsseite verwenden jQuery 4 (`.on()`). |
| 6.0.14 | Datums- und Datum/Zeit-Felder verwenden den Kalender von Exponential UI, `exp::datepicker`, wenn `expui` aktiv ist; die Extension benötigt `se7enxweb/expui ^1.0.0.1`. |
| 6.0.15 | **YUI entfernt**: Die Templates zum Bearbeiten von Datumsfeldern laden `exp::datepicker` (mit `exp_config()`), und `content.css` gestaltet den YUI-Kalender nicht mehr. Siehe [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md). |
| 6.0.16 | Die Copyright-Hinweise und die Info-Seite nennen zuerst „1998 - 2026 7x & Exponential Foundation“, über den Hinweisen von eZ Systems, die bleiben, wie die GPL es verlangt. |

## Verwandte Seiten

- [ezwt](ezwt.md), [ezflow](ezflow.md), [ezdemo](ezdemo.md), [ezwebin-ezpackage](ezwebin-ezpackage.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezwebin.md) und [Versionshinweise](../../../../changelogs/extensions/ezwebin.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezwebin.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-01](../../../geschichte/erweiterungen/monate/2024-01.md), [2026-04](../../../geschichte/erweiterungen/monate/2026-04.md), [2026-07](../../../geschichte/erweiterungen/monate/2026-07.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
