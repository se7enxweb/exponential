---
title: "Fallstudie: der Layout-Editor auf jQuery 4"
audience: developer
category: extension-development
order: 240
summary: Wie der Layout-Editor, eine Single-Page-Anwendung mit jQuery 2.2.4 und jQuery UI 1.10.4 in ihrem Bündel, auf das jQuery 4 der Administration umgezogen ist - der Plan, die entfernten Aufrufe, die umbenannten globalen Variablen, der Test durch Verwendung.
keywords: Fallstudie, Layout-Editor, Bündel, Single-Page-Anwendung, Upgrade, case study, layouts editor, explayouts_ui_api, explayouts_ui, jQuery 4, jQuery UI 1.14, bundle, noConflict, Bootstrap, single-page application, upgrade
type: help
---
# Fallstudie: der Layout-Editor auf jQuery 4

Teil 4, Kapitel 24. Die meisten Erweiterungen verwenden ein wenig jQuery. Einige liefern eine ganze
JavaScript-Anwendung mit einer eigenen Kopie von jQuery in einem Bündel. Der Layout-Editor von Exponential Layouts (die
Erweiterungen explayouts_ui_api und explayouts_ui) war eine davon. Dieses Kapitel erzählt, wie er in zwei Commits vom
1. Oktober 2026 auf das jQuery 4 der Administration umgezogen ist, damit Sie denselben Umzug für ein großes Skript
von Ihnen planen können.

## Der Ausgangspunkt

| Teil | Vorher |
|---|---|
| Der Editor (`explayouts_ui_api`, eine Single-Page-Anwendung) | ein Bündel, `netgen-layouts.js`, mit eingebautem jQuery 2.2.4 und jQuery UI 1.10.4, das `jQuery.noConflict( true )` aufruft |
| Die Administrationsseiten der Layouts (`explayouts_ui`) | `layouts-ibexa.js` mit eingebautem jQuery 3.7.1 und Bootstrap 4.6.2 |
| Die Administration um sie herum | jQuery 4.0.0, jQuery Migrate 4.0.2, jQuery UI 1.14.2 aus ezjscore 1.4.0 |

Eine Seite des Layout-Editors trug also bis zu drei jQuery-Versionen. Der Code stammte aus einem Upstream-Projekt für
eine andere Plattform (in den Erweiterungen genannt), mit den Namen dieses Projekts in seinen globalen Variablen und
Seiten-Tags.

## Der Plan

1. Das jQuery und jQuery UI der Seite vor dem Bündel laden.
2. Die Kopien aus dem Bündel entfernen und mit ihnen `noConflict( true )`.
3. Jeden Aufruf ersetzen, den jQuery 3 und 4 entfernt oder als veraltet markiert haben.
4. Da ohnehin jede globale Variable und jedes Seiten-Tag angefasst wird, ihnen die eigenen Namen der Erweiterung
   geben.
5. Durch Verwendung testen, in jedem Administrationsdesign, auf beiden Anwendungsservern.

## Schritt 1: zuerst die Bibliotheken der Seite laden

Das Shell-Template des Editors (`app.tpl`) hat vor dem verzögert geladenen Bündel Folgendes erhalten:

```text
{* The app bundle uses the page's jQuery and jQuery UI (ezjscore's), so they
   load first: these run before the deferred scripts below. *}
{ezscript( array( 'ezjsc::jquery', 'ezjsc::jqueryUI' ) )}
```

Der Kommentar sagt, warum die Reihenfolge wichtig ist: Das Bündel wird mit `defer` geladen, daher läuft es, nachdem
der Parser diese normalen Skripte passiert hat.

## Schritt 2: die Kopien und noConflict entfernen

Aus der Commit-Nachricht von se7enxweb/explayouts_ui_api, 8273a03: „das Bündel verwendet das jQuery der Seite: seine
eigenen Kopien sind weg, jQuery.noConflict(true) ist weg (es hätte das jQuery der Seite entfernt)“.

`noConflict( true )` ist die Falle bei dieser Art von Umzug. Es war richtig, solange das Bündel sein eigenes jQuery
mitbrachte: Es gab die globalen Namen an das zurück, was vorher da war. Gegen das jQuery der Seite entfernt es `jQuery`
und `$` von der Seite, und jedes andere Skript auf der Seite bricht ab.

## Schritt 3: die entfernten Aufrufe

Der Commit listet auf, was er ersetzt hat: „jqXHR.success, jQuery.camelCase, jQuery.proxy, die Event-Kurzformen,
attr(name, false) wie bisher als removeAttr beibehalten“.

Der letzte Punkt ist ein Detail, das Beachtung verdient. Im alten jQuery hat `.attr( 'disabled', false )` das
Attribut entfernt. Der Ersatz behält genau dieses Verhalten bei (`removeAttr`), statt auf `.prop()` umzustellen, weil
der eigene Code des Editors das Attribut später gelesen hat. Wenn Sie einen entfernten Aufruf ersetzen, behalten Sie
das Verhalten bei, nicht nur den Namen.

Das Skript der Administrationsseiten (se7enxweb/explayouts_ui, e3d2723) brauchte weniger, weil sein Bootstrap 4.6.2
„jQuery 4 akzeptiert“; es verlor sein eingebautes jQuery 3.7.1 und verwendete das der Seite.

Die Liste des Kernels mit den entfernten Aufrufen und ihrem Ersatz steht in
[Vom alten jQuery zu jQuery 4](altes-jquery-zu-jquery-4.md).

## Schritt 4: Namen

Beide Commits haben die Präfixe des Upstream-Projekts in globalen Variablen, Seiten-Tags (`<meta>`-Namen, die das
JavaScript liest), Session-Schlüsseln und Dateinamen in `explayouts`- und `exp`-Namen umbenannt; zum Beispiel wurden
aus den Meta-Tags `nglayouts-route-prefix` und `nglayouts-base-path` die Tags `explayouts-route-prefix` und
`explayouts-base-path`, und aus `layouts-ibexa.js` wurde `layouts-exponential.js`. Das waren die Interna der
Erweiterung: Nichts außerhalb der Erweiterung las sie, daher konnten sie im selben Commit umbenannt werden wie alles,
was sie verwendete. (Vergleichen Sie [Namen und Marken bei einem Upgrade](namen-und-marken-bei-einem-upgrade.md):
Namen, die anderer Code verwendet, werden nie so umbenannt.)

Unterwegs stellte sich eine Datei als tot heraus: Der „Content-Browser von `layouts-admin.js` brach ohne die
Seiten-Tags von Netgen sofort ab, daher lief nichts davon je auf diesen Seiten“, also laden die Seiten sie nicht mehr.
Ein Upgrade ist eine gute Gelegenheit herauszufinden, was nie gelaufen ist.

## Schritt 5: durch Verwendung testen

Beide Commits sagen, wie sie überprüft wurden, und die Listen sind es wert, übernommen zu werden. Der Editor:
„Überprüft durch Verwendung auf Exponential Velocity und PHP-FPM in den Designs admin, admin2 und admin3: ein Layout
öffnen, die Menüs, Duplizieren, Löschen über das Modal, Sortieren per Drag, Blöcke aus der Palette hinzufügen,
Bearbeiten im Modal, der Code-Editor, die Tabs der Seitenleiste, Inline-Bearbeitung und Verwerfen, ohne Fehler, ohne
fehlgeschlagene Anfrage und ohne Warnung von jQuery Migrate.“ Die Administrationsseiten: „jede Layout-Seite lädt ohne
Seitenfehler, ohne Konsolenfehler, ohne fehlende Datei und ohne Warnung von jQuery Migrate.“

Drei Dinge machen das zu einem guten Test:

- **Jede Funktion, von Hand**, nicht nur „die Seite lädt“. Ein defektes Drag and Drop zeigt sich erst, wenn Sie ziehen.
- **Jedes Design und beide Server**, weil jedes Skripte in seiner eigenen Reihenfolge lädt.
- **Ein klares Kriterium für das Bestehen**: kein Fehler, keine fehlgeschlagene Anfrage, keine Warnung von Migrate.

## Was danach kam

Diese beiden Commits betrafen jQuery. Das Shell-Template des Editors hatte noch Inline-Skripte (die Übersetzungen und
einige Werte), und seine Skript-Links trugen noch Versionsabfragen; die CSP-Messung vom 17. Oktober 2026 führte
explayouts_ui_api noch unter den Erweiterungen mit Inline-Code auf
([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)). Ein großes Upgrade nach dem
anderen ist in Ordnung, solange jedes fertig und veröffentlicht ist, bevor das nächste beginnt.

Der Commit nannte außerdem eine Voraussetzung: „Braucht ezjscore 1.4.0 oder neuer.“ Wenn Ihre Erweiterung von einer
neueren Bibliothek aus einer anderen Erweiterung abhängig wird, sagen Sie das im Commit, im Changelog und in der
Anforderung in `composer.json`.

## Verwandte Seiten

- Zurück: [Fallstudie: von eZChangeclass zu expchangeclass](fallstudie-expchangeclass.md)
- Weiter: [Fallstudie: Zwei-Faktor-Authentifizierung ohne Inline-Skripte](fallstudie-zwei-faktor-authentifizierung.md)
- [Vom alten jQuery zu jQuery 4](altes-jquery-zu-jquery-4.md)
- [jQuery 4 in der Administration, und YUI ist weg](../../../../eng-us/features/6.0/jquery4-and-yui-removal.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
