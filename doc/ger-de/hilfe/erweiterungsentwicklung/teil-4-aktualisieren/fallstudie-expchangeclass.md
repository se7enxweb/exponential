---
title: "Fallstudie: von eZChangeclass zu expchangeclass"
audience: developer, app-creator
category: extension-development
order: 230
summary: Ein Upgrade, aus seinen Commits erzählt - wie aus einer Erweiterung von 2007, die die Klasse eines Objekts ändert, expchangeclass auf PHP 8 wurde, mit Transaktionen, einer Richtlinie, Übersetzungen, CSP-sicheren Menüs und neun Releases.
keywords: Fallstudie, Transaktion, Rollback, statischer Aufruf, PHP-4-Konstruktor, Richtlinie, Release, Upgrade, case study, expchangeclass, eZChangeclass, fork, PHP 8, transaction, rollback, static call, PHP 4 constructor, policy, CSP, admin4, release, upgrade
type: help
---
# Fallstudie: von eZChangeclass zu expchangeclass

Teil 4, Kapitel 23. Dieses Kapitel verfolgt ein Upgrade von Anfang bis Ende anhand der Historie von
se7enxweb/expchangeclass auf GitHub. Es zeigt, in welcher Reihenfolge die Arbeit erledigt wurde, warum, und wie jeder
Schritt zu einem Release wurde. Jede Aussage hier stammt aus den Commits der Erweiterung, ihrer README oder ihrem
Changelog.

## Was die Erweiterung tut

Sie ändert die Inhaltsklasse eines bestehenden Inhaltsobjekts und ordnet jedes Attribut der alten Klasse einem der
neuen zu: für ein Objekt aus der Administration (ein Eintrag im Kontextmenü) oder für jedes Objekt einer Klasse über die
Kommandozeile.

## Woher sie kam

Die ursprüngliche Erweiterung, eZChangeclass, stammt aus dem Jahr 2007 (ihre Copyright-Zeile in `ezinfo.php`) und
steht unter der GPL. Die README von expchangeclass erklärt, dass zwei Forks davon im Umlauf waren und dass keiner davon
sicher auszuführen war:

- Ein Fork behielt „die ursprüngliche PHP-4-Form“: eine Klasse `conversionFunctions` mit einem Konstruktor
  `conversionFunctions()` und nicht statischen Methoden, die das Modul statisch aufrief. Unter PHP 8 „bricht er an der
  Zeile ab, die die Arbeit macht“, mit `Non-static method conversionFunctions::customConverter() cannot be called
  statically`.
- Der andere Fork hat das behoben, aber „eine Umwandlung, die mittendrin fehlschlug, wurde nie zurückgerollt, und das
  Stapelskript konnte nicht enden, wenn sich ein Objekt nicht umwandeln ließ“.

Die neue Erweiterung „nimmt also den Code von OpenContent als Basis, übernimmt die Vorauswahl der Attribute und die
Paketierung aus dem Fork von Netgen und behebt, was keiner von beiden hatte“. Das ist ein gutes Vorbild: Lesen Sie
zuerst jede vorhandene Version, nehmen Sie das Beste von jeder und sagen Sie, woher jeder Teil stammt.

## Der Zeitablauf

| Datum | Commit | Schritt | Kapitel dieses Teils |
|---|---|---|---|
| 2026-09-13 | 7c3f164 | erster Import des Forks, für PHP 8 gehärtet, mit Rollback per Transaktion | [PHP-8-Fehler](php-8-fehler-die-alten-code-stoppen.md), [Datenbankcode](datenbankcode-fuer-php-8.md) |
| 2026-09-13 | 5fc6818 | ein auskommentiert gelassenes Include ließ Modul-Views abbrechen, wenn das Autoload-Array eine Klasse nicht enthielt | [Bestandsaufnahme](bestandsaufnahme-einer-alten-erweiterung.md) |
| 2026-09-16 | a28b3da | der Menüeintrag der Unterelemente hatte dieselbe Element-ID wie der des Knotenmenüs | [Kontextmenüs](kontextmenues-und-admin-integration.md) |
| 2026-09-28 | 9f1e911, 6062a43 | jeder sichtbare Text ein Übersetzungsstring, Deutsch hinzugefügt; Version 1.0.2 | [Namen und Marken](namen-und-marken-bei-einem-upgrade.md) |
| 2026-09-30 | f5dadff, ffc1e4c | die Seite „Über“ nennt sie „Exponential Change Class“; 1.0.3 | [ezinfo.php und extension.xml](ezinfo-und-extension-xml-modernisieren.md) |
| 2026-10-02 | ac8b9b6, dc0d533 | Kommandozeilenskripte, Cronjob-Teile und Modul-Views wurden zu Klassen, die die Dateien aufrufen; 1.0.4 | |
| 2026-10-02 | b86989f, e936f91 | Menüeinträge nur für Benutzer, die eine Klasse umwandeln dürfen; 1.0.5 | [Kontextmenüs](kontextmenues-und-admin-integration.md) |
| 2026-10-05 | 32196f0 | installiert sich und läuft auf PHP 8.0; 1.0.6 | [composer.json](composer-json-modernisieren.md) |
| 2026-10-16 | c2a4f41 | weitere Übersetzungen | |
| 2026-10-17 | e2cd128 | Ergebniskästen verwenden den gemeinsamen Admin-Hinweis statt `style=""` | [Inline-CSS](inline-css-in-stylesheets.md), [admin4-Styling](admin4-styling-fuer-erweiterungsseiten.md) |
| 2026-10-17 | 4958e02 | die nach einer Umwandlung angezeigte Parameterdatei wird escapet | |
| 2026-10-17 | 88b60d9, 2fa5a66 | Einträge im Kontextmenü ohne Inline-Handler; 1.0.9 | [Event-Handler-Attribute](csp-event-handler-zu-data-attributen.md) |

Beachten Sie den Rhythmus: eine Korrektur, dann eine höhere Version, dann die nächste Korrektur. Jedes Release ist
klein genug, um es in einer Zeile zu beschreiben, und eine Website kann jedes davon übernehmen.

## Schritt 1: sicher machen, bevor es hübsch wird

Der erste Commit enthielt bereits die Korrekturen, die für Inhalte wichtig sind. Aus dem Changelog von 1.0.0:

- „Die Umwandlung läuft in einer Transaktion und wird als Einheit zurückgerollt.“
- „`die()` in der Attributschleife durch Ausnahmen ersetzt, sodass ein Fehler zurückgerollt und gemeldet werden kann,
  statt die Anfrage mitten im Schreiben zu beenden.“
- „Die Zuordnung der Attribute wird gegen die Data Maps beider Klassen geprüft, bevor etwas geschrieben wird.“
- „Konvertierungsmethoden als statisch deklariert, passend zu der Art, wie sie aufgerufen werden.“
- „Konverter von Relationslisten zu Blöcken auf DOMDocument neu geschrieben, als Ersatz für die entfernte
  eZXML-API aus PHP 4.“
- „Die Umwandlung von Text in ezxmltext escapet den Wert zuerst; Inhalte mit einem kaufmännischen Und oder einer
  spitzen Klammer erzeugen kein ungültiges XML mehr.“

Eine Erweiterung, die Inhalte umschreibt, darf sie nie halb geändert zurücklassen. Das kam zuerst, vor jedem Styling.

Die Aufrufkonvention, die die README nach der Änderung zeigt:

```php
$error = null;
if ( !conversionFunctions::convertObject( $objectID, $classID, $mapping, $error ) )
{
    // content is exactly as it was
}
```

Ein Ergebnis und eine Fehlermeldung statt `die()`, sodass das Modul zeigen kann, was passiert ist.

## Schritt 2: Fehler, die beim Lesen der Einstellungen gefunden wurden

Das Changelog hält einen Fehler fest, den keine PHP-Version gemeldet hätte: `changeclass.ini` deklarierte die Gruppe
`[ezstring]` zweimal. In INI-Dateien „wird eine wiederholte Gruppe nicht zusammengeführt: das zweite Vorkommen gewinnt“
(README), daher existierte eine Umwandlung stillschweigend nicht. Die Lösung war eine Gruppe pro Quelldatentyp. Lesen
Sie die Einstellungen einer alten Erweiterung so sorgfältig wie ihr PHP.

## Schritt 3: Zugriff

„Das Modul deklarierte keine Funktionen, daher war die einzig mögliche Berechtigung alles oder nichts für das Modul.“
Der Fork hat eine Richtlinienfunktion `changeclass/convert` hinzugefügt, und Commit b86989f zeigt die Menüeinträge nur
Benutzern, die sie besitzen:

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'changeclass', 'function', 'convert' ) )}
```

Siehe [Richtlinienfunktionen und Zugriff](../teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md).

## Schritt 4: Texte, Namen und Nennung der Urheber

Commit 9f1e911 hat jeden sichtbaren Text zu einem Übersetzungsstring gemacht. Commit f5dadff hat ihr auf der Seite
„Über“ einen lesbaren Namen gegeben. Der ursprüngliche Autor bleibt genannt: in `ezinfo.php` (ein `Includes`-Eintrag,
der eZChangeclass mit seinem Copyright und seiner Lizenz nennt), in `composer.json` (`authors`, mit der Rolle „Original
author of eZChangeclass“) und in der README, die das ursprüngliche Repository verlinkt.

## Schritt 5: das älteste PHP

Commit 32196f0 hat die PHP-Bedingung erst auf 8.0 erweitert, nachdem der Code „mit PHPCompatibility für PHP 8.0
geprüft, mit PHP 8.0.30 gelintet und geladen und auf einer vollständigen Installation von PHP 8.0 ausgeliefert“ wurde.
Die Version wurde im selben Commit in `composer.json`, `extension.xml` und `ezinfo.php` erhöht.

## Schritt 6: der Browser

Die letzten Schritte betrafen die Content Security Policy:

- e2cd128: Die Ergebniskästen der Umwandlung „mit style=""-Attributen und festen roten und grünen Rahmen“ wurden zur
  gemeinsamen Komponente `a4-notice` mit `is-ok` oder `is-bad`, die „den Admin-Tokens im hellen und im dunklen Modus
  folgt“, plus einem kleinen Stylesheet, das über `BackendCSSFileList[]` geladen wird. Ein Test ohne Datenbank hält die
  Administrations-Templates frei von Inline-CSS.
- 88b60d9: Die Einträge im Kontextmenü „deklarieren ihr Formular in data-menu-form“, mit einem kleinen Skript für
  ältere Kernel, und ein zweiter Test hält die Templates frei von Inline-Code.

Unterwegs hat 4958e02 einen Wert escapet, der ohne `wash` ausgegeben wurde: Ein Upgrade ist ein guter Moment, sich jede
Stelle anzusehen, an der ein Template Daten ausgibt.

## Was Sie daraus mitnehmen können

1. Lesen Sie jede vorhandene Version und jeden Fork, bevor Sie beginnen; nennen Sie jeden, von dem Sie etwas
   übernehmen.
2. Beheben Sie zuerst, was Inhalte beschädigen kann.
3. Veröffentlichen Sie oft, ein Thema pro Release, mit der Version in allen drei Dateien.
4. Fügen Sie für jede Art von Problem einen Test hinzu, wenn Sie es beheben, damit es behoben bleibt.
5. Heben Sie sich die visuelle Arbeit für den Schluss auf, aber erledigen Sie sie: Sie ist das, was Benutzer sehen.

## Verwandte Seiten

- Zurück: [Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)
- Weiter: [Fallstudie: der Layout-Editor auf jQuery 4](fallstudie-der-layout-editor-auf-jquery-4.md)
- [expchangeclass auf GitHub](https://github.com/se7enxweb/expchangeclass)
- [Fallstudie: ein Paket aus einem anderen Projekt mit Nennung der Urheber portieren](../teil-5-erweitern-forken-zurueckgeben/fallstudie-aus-einem-anderen-projekt-portiert.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
