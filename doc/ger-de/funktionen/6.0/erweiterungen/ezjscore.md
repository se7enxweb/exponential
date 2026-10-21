# ezjscore: Packer für JavaScript und CSS, Serveraufrufe, jQuery

Diese Seite ist für Template-Autoren und Entwickler von Extensions, die Skripte und Stile laden oder PHP aus JavaScript
aufrufen. `ezjscore` („eZ JS Core“) ist die JavaScript-Grundlage von Exponential. Es:

- **packt** Skripte und Stylesheets in eine zwischengespeicherte Datei pro Seite;
- lädt **jQuery** und **jQuery UI**;
- stellt **Serveraufrufe** bereit: eine Ajax-Schnittstelle zu PHP-Klassen, geschützt durch Rollenrichtlinien;
- stellt Template-Operatoren zum Kodieren und Laden von Ressourcen bereit und die Funktionsbibliothek, auf die mehrere
  Extensions bauen ([ezstarrating](ezstarrating.md), [ezautosave](ezautosave.md), [ezwt](ezwt.md) und andere).

Das eigenständige Repository `se7enxweb/ezjscore` trägt denselben Code wie die mit dem Kernel von Exponential
ausgelieferte Kopie. Das Release 1.4.0 (1. Oktober 2026) brachte es wieder in Gleichschritt, nachdem es 2013 geleert
worden war, als der Code in den Legacy-Kernel umzog. Es lässt sich wieder für sich installieren und verfolgen:
`composer require se7enxweb/ezjscore`.

Wie Serveraufrufe geschrieben und abgesichert werden, beschreibt
[Backend-Dienste von ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md).

## Auf 1.5 aktualisieren (YUI entfernt)

1. Durchsuchen Sie Ihre Designs und Extensions nach YUI:

```bash
grep -rn "ezjsc::yui2\|ezjsc::yui3\|ezjsc::yui3io\|\[YUI3\]" design extension --include=*.tpl --include=*.ini* --include=*.php
```

   Es sollte nichts ausgegeben werden. Ersetzen Sie jeden Treffer durch `ezjsc::jquery`, `ezjsc::jqueryUI` oder die
   Module von Exponential UI aus `expui` (siehe die Tabelle der Ersetzungen in
   [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md)).
2. Wenn Sie jQuery-3-Dateien selbst aus `ezjscore.ini` laden, behalten Sie sie: Sie werden weiter mitgeliefert.
3. Leeren Sie die Ausgabe des Packers, damit gepackte Dateien neu erstellt werden:
   `php bin/php/ezcache.php --clear-tag=template,content --allow-root-user` und den Cache `ezjscore-packer`.

## Was sich in den Releases von Exponential 6 geändert hat

| Release | Änderung |
|---|---|
| 1.4.0 (1. Oktober 2026) | Das Repository trägt wieder das aktuelle ezjscore: den Packer und seine Template-Operatoren, Serveraufrufe, Kodier-Operatoren und das Laden von jQuery. `ezjsc::jquery` lädt **jQuery 4.0.0** gefolgt von **jQuery Migrate 4.0.2**, und `ezjsc::jqueryUI` lädt **jQuery UI 1.14.2**. Die jQuery-3-Dateien werden weiter mitgeliefert, und die CDN-Einträge, die jQuery 1.10.2 nannten, nennen dieselben Releases. `composer.json`, `ezinfo.php`, ein README und eine Anleitung zum Mitwirken sind enthalten. |
| 1.4.1 (2. Oktober 2026) | Korrektur am **CSS-Packer**: Auf Packstufe 3 wurde jedes Leerzeichen vor einem Doppelpunkt entfernt, auch in Selektoren, sodass `a :hover` zu `a:hover` und `:where(#a) :where(.b)` zu `:where(#a):where(.b)` wurde, jeweils ein anderer Selektor, und Regeln still nicht mehr griffen. Das Leerzeichen vor `:` wird jetzt nur innerhalb von Deklarationsblöcken entfernt (`color : red` wird zu `color:red`). |
| 1.5.0 | **YUI entfernt**: die Bibliotheken YUI 2 und YUI 3 (`design/standard/lib/yui`), die Packer-Schlüssel `ezjsc::yui2`, `ezjsc::yui3` und `ezjsc::yui3io` und ihre Konfigurationsfunktionen in `ezjscServerFunctionsJs`, die Einstellungen `[YUI3]` und die yui-Einträge in `ExternalScripts`, `LocalScripts` und `LocalScriptBasePath` von `ezjscore.ini`. `PreferredLibrary` ist `jquery`. Das ist eine inkompatible Änderung für Code, der nach YUI fragt. |
| 1.5.1, 1.5.2 | Kommandozeilenskripte, Cronjob-Teile und Modulansichten sind Klassen, die die Dateien aufrufen; die Copyright-Hinweise und die Info-Seite nennen zuerst 1998 - 2026 7x & Exponential Foundation, über den Hinweisen von eZ Systems; die Extension ist identisch mit der Kopie im Kernel. |
| 1.5.3, 1.5.4 | Die Serverfunktionen **`expsubitems`** bedienen die **Einstellungen** (Table options) der Unterelementliste der Administrationsoberfläche: registriert als `[ezjscServer_expsubitems]` mit der Klasse, die Exponential 6.0.15 mitliefert, wobei jede Funktion den Zugriff selbst prüft; neue Blöcke für Serverfunktionen in den Einstellungen für die Spalten der Unterelemente. Siehe [Optionen der Unterelementtabelle](../../../bc/6.0/tabellenoptionen-der-unterelemente.md). |
| 1.5.5 | Der Server-Router lässt Klassen von `expservices` in ihrer **eigenen Hülle** antworten, Fehler eingeschlossen. (Die `extension.xml` des Klons sagt 1.5.5; die hier installierte Kopie liest 1.5.4 in `ezinfo.php`.) |

## Sprachen

Die Extension enthält Übersetzungsdateien in `translations/<locale>/translation.ts`: eng-US und ger-DE. Die deutsche
Datei enthält 13 Meldungen (zählen Sie `<message` in `translations/ger-DE/translation.ts`). Die Texte werden in den
Kontexten `extension/ezjscore/ajaxuploader` und `design/standard/content/datatype` nachgeschlagen.

Nach dem Bearbeiten einer Übersetzungsdatei:

1. Erneuern Sie den kompilierten Übersetzungs-Cache: `./console exp:ezgeneratetranslationcache`.
2. Leeren Sie die Template- und Inhalts-Caches.

`./console exp:ezchecktranslation ger-DE` gibt Statistiken zur Datei `share/translations/ger-DE/translation.ts` des
Kernels aus, nicht zur Datei dieser Extension.

## Verwandte Seiten

- [jQuery 4 in der Administrationsoberfläche, und YUI ist weg](../jquery4-und-entfernung-von-yui.md)
- [Entfernte Dienste (expservices)](../entfernte-dienste-expservices.md)
- [Backend-Dienste von ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezjscore.md) und [Versionshinweise](../../../../changelogs/extensions/ezjscore.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezjscore.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [März 2024](../../../geschichte/2024/2024-03.md), [Monat: 2026-10 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
