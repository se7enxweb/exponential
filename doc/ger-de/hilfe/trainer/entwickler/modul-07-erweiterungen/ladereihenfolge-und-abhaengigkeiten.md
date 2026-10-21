---
title: "Lektion 7.4: Ladereihenfolge und Abhängigkeiten"
audience: trainer, developer
category: training
order: 74
summary: Die Teilnehmenden sehen, warum die Reihenfolge der aktiven Erweiterungen entscheidet, welche Einstellungen und Templates gewinnen, deklarieren extends, requires und uses in extension.xml, schalten ExtensionOrdering ein und lesen auf der Seite Extensions die tatsächliche Ladeposition ab. 40 Minuten.
keywords: entwickler, ladereihenfolge, reihenfolge von ActiveExtensions, abhängigkeiten in extension.xml, extends, requires, uses, ExtensionOrdering, ezpExtension getLoadingOrder, Setup Extensions, geladen als nummer, developers course, loading order, dependencies, Loads as number
type: help
---
# Lektion 7.4: Ladereihenfolge und Abhängigkeiten

Wenn zwei Erweiterungen dieselbe Einstellung setzen oder dasselbe Template liefern, gewinnt eine von ihnen, und die
Reihenfolge der aktiven Erweiterungen entscheidet, welche. In dieser Lektion sehen die Teilnehmenden die Reihenfolge
bei der Arbeit, deklarieren in `extension.xml`, worauf ihre Erweiterung aufbaut, und lesen auf der Seite
**Extensions** ab, wo jede Erweiterung tatsächlich geladen wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 7: Erweiterungen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 7.3](autoloads-und-aktivierung.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, dass eine früher geladene Erweiterung bei Einstellungen und Design-Templates gewinnt;
- mit `exp:ini where` herausfinden, aus welcher Erweiterung eine Einstellung stammt;
- `<extends>`, `<requires>` und `<uses>` in `extension.xml` deklarieren;
- erklären, was `[ExtensionSettings] ExtensionOrdering=enabled` ändert;
- unter **Setup** > **Extensions** die tatsächliche Ladeposition ablesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Abhängigkeiten in extension.xml deklarieren](../../../../../eng-us/help/designers/extensions/dependencies-in-extension-xml.md),
[Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md) und
[Die Seite Extensions](../../../../anleitungen/extensions-seite.md).

1. **Wer gewinnt** (4 Minuten). Die Reihenfolge von `ActiveExtensions[]` entscheidet: Eine früher geladene
   Erweiterung gewinnt bei Einstellungen und Design-Templates. `exp:ini where` listet jede Datei auf, die eine
   Variable setzt, in Ladereihenfolge, und den wirksamen Wert.
2. **Abhängigkeiten** (5 Minuten). In `extension.xml`, nach `<metadata>`:

   | Element | Lädt Ihre Erweiterung | Verwenden Sie es für |
   |---|---|---|
   | `<extends>` | vor der genannten (Ihre gewinnt) | Das Überschreiben von Templates oder Einstellungen einer Basiserweiterung |
   | `<requires>` | nach der genannten | Etwas, ohne das Ihre nicht funktioniert |
   | `<uses>` | nach der genannten | Etwas, das Ihre verwendet, wenn es vorhanden ist |

   Der Kernel liest sie in `ezpExtension::getLoadingOrder()`, wenn `ExtensionOrdering=enabled` gesetzt ist. Sie
   ordnen nur aktive Erweiterungen: `<requires>` aktiviert die andere nicht.
3. **Die Seite** (3 Minuten). Unter **Setup** > **Extensions** zeigt jede Karte, wo die Erweiterung tatsächlich
   geladen wird (**Geladen als Nummer n**), wenn Abhängigkeiten sie verschieben; Verschiebungen werden geprüft, bevor
   sie geschrieben werden.
4. **Auch Composer** (2 Minuten). Dieselben Abhängigkeiten gehören in `composer.json` (`require` oder `suggest`),
   damit Composer installiert, was gebraucht wird.

### Häufige Fragen

**Warum hat das Template meines Themes nicht gegen das eines anderen Themes gewonnen?** Die andere Erweiterung wird
früher geladen oder deklariert, dass sie Ihre erweitert. Lesen Sie die Reihenfolge und die Blöcke `<extends>`.

**Legt eine fehlerhafte `extension.xml` die Website lahm?** Sie wird in der Debug-Ausgabe gemeldet, und ihre
Ladereihenfolge wird ignoriert; prüfen Sie sie mit `xmllint`.

**Sollte jede Erweiterung Abhängigkeiten deklarieren?** Jede Erweiterung, die eine andere überschreibt oder braucht,
ja; so wird die Reihenfolge erklärbar statt zufällig.

### Stolperfallen

- **Die Reihenfolge auf einer Live-Website von Hand ändern.** Verwenden Sie die Seite **Extensions** oder `exp:ini`
  mit einer Prüfung; eine falsche Reihenfolge kann Templates auf der ganzen Website verändern.
- **`<requires>` als Installationsprogramm.** Es ordnet; es installiert und aktiviert nichts.
- **Unterschiedliche Reihenfolge in Entwicklung und Live-Betrieb.** Die Einstellungen kommen auf jedem Rechner aus
  anderen Dateien; vergleichen Sie auf beiden mit `exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Zeigen Sie die Reihenfolge der aktiven Erweiterungen an:
   `php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.
2. Prüfen Sie, ob die Sortierung nach Abhängigkeiten eingeschaltet ist:
   `php bin/php/console exp:ini where site.ini/ExtensionSettings/ExtensionOrdering`.
3. Suchen Sie eine Einstellung, die mehrere Erweiterungen setzen, zum Beispiel `php bin/php/console exp:ini where
   design.ini/ExtensionSettings/DesignExtensions <sa>`, und lesen Sie, welche Datei zuerst kommt.
4. Lesen Sie einen echten Abhängigkeitsblock: `grep -A8 "<dependencies>" extension/*/extension.xml | head -40`.
5. Fügen Sie Ihrer `extension.xml` nach `</metadata>` eine Abhängigkeit hinzu:

   ```xml
   <dependencies>
       <uses>
           <extension name="ezjscore" />
       </uses>
   </dependencies>
   ```

6. Prüfen Sie sie: `xmllint --noout extension/mysite_hello/extension.xml`.
7. Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und öffnen Sie **Setup** > **Extensions**.
   Suchen Sie, wo `mysite_hello` geladen wird.
8. Committen Sie `extension.xml` mit `Updated: Declare the use of ezjscore`.

## Erwartetes Ergebnis

- Schritt 1: die Liste in Ladereihenfolge, mit `mysite_hello` darin.
- Schritt 3: mehrere Dateien in Ladereihenfolge, am Ende der wirksame Wert.
- Schritt 6: keine Ausgabe.
- Schritt 7: die Karte von `mysite_hello`; mit `ExtensionOrdering=enabled` wird sie nach `ezjscore` geladen.
- Der Trainer fragt alle Teilnehmenden, welches Element sie für ein Theme verwenden würden, das die Templates eines
  anderen Themes ändert.

## Quiz

1. Zwei Erweiterungen liefern dasselbe Template. Welche gewinnt?
2. Ihr Theme überschreibt Templates eines Basis-Themes. Welches Element deklarieren Sie?
   A. `<requires>` B. `<uses>` C. `<extends>` D. `<metadata>`
3. Welche Einstellung lässt den Kernel die Erweiterungen nach ihren deklarierten Abhängigkeiten ordnen?
4. Aktiviert `<requires>` die andere Erweiterung?
5. Welcher Befehl listet jede Datei auf, die eine Variable setzt, in Ladereihenfolge?

<details>
<summary>Antworten</summary>

1. Die Erweiterung, die in der Reihenfolge der aktiven Erweiterungen früher geladen wird.
2. **C.** `<extends>` lädt Ihre vor der Basis, sodass Ihre gewinnt.
3. `site.ini [ExtensionSettings] ExtensionOrdering=enabled`.
4. Nein; es ordnet nur aktive Erweiterungen.
5. `exp:ini where <file>/<block>/<variable>`.

</details>

## Weiterführende Literatur

- [Abhängigkeiten in extension.xml deklarieren](../../../../../eng-us/help/designers/extensions/dependencies-in-extension-xml.md).
- [Die Ladereihenfolge und welches Theme gewinnt](../../../../../eng-us/help/designers/extensions/loading-order-for-themes.md).
- [Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md).

## Verwandte Seiten

- [Modul 7: Erweiterungen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
