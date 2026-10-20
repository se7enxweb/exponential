---
title: "Wenn Einstellungen nicht reichen: vom Builder zum Erweiterungsentwickler"
audience: developer, designer, app-creator
category: extension-development
order: 220
summary: Wie Sie erkennen, dass eine Anforderung Code braucht, und welcher Erweiterungspunkt passt - eine Tabelle vom Bedarf zum Mechanismus (Operator, Fetch-Funktion, Modul, Datentyp, Ereignis, Cronjob, Listener, Handler) und seinem RAD-Assistenten.
keywords: Entscheidung, wann programmieren, Erweiterungspunkt, Template-Operator, Fetch-Funktion, Modul, Datentyp, Workflow-Ereignis, Cronjob, Event-Listener, Handler, Kernel-Override, RAD, decision, when to code, extension point, template operator, fetch function, datatype, workflow event, event listener, kernel override
type: help
---
# Wenn Einstellungen nicht reichen: vom Builder zum Erweiterungsentwickler

Teil 2, Kapitel 22, das letzte dieses Teils. Builder können ohne PHP sehr viel erreichen. Dieses Kapitel hilft Ihnen,
den Punkt zu erkennen, an dem Code das bessere Werkzeug ist, und welcher der Erweiterungspunkte des Kernels zur
Anforderung passt, damit Ihr erstes PHP klein ist und am richtigen Ort steht.

## Anzeichen, dass ein Template die Arbeit eines Programms macht

- Ein Template enthält mehr `{if}`, `{set}` und Arithmetik als HTML.
- Dieselben zwanzig Zeilen Template-Logik tauchen in mehreren Templates auf.
- Sie rufen Hunderte von Knoten ab, um eine einzige Zahl zu berechnen.
- Sie brauchen Daten, die keine Inhalte sind: einen externen Dienst, eine Datei, eine Berechnung, eine eigene
  Datenbanktabelle.
- Etwas muss ohne Seitenaufruf geschehen: beim Veröffentlichen, nachts, nach Zeitplan.

## Vom Symptom zum Mechanismus

| Sie brauchen | Mechanismus | Kapitel in Teil 3 | RAD-Assistent |
|---|---|---|---|
| Einen Wert oder eine Umwandlung in Templates (`{$price\|mysite_tax}`) | Template-Operator | [Template-Operatoren](../../../../eng-us/help/extension-development/part-3-extension-developer/template-operators.md) | `/setup/templateoperator` |
| Eine Liste oder ein Objekt, das `fetch()` nicht ausdrücken kann | Fetch-Funktion | [Fetch-Funktionen](../teil-3-erweiterungsentwickler/fetch-funktionen.md) | `/setup/templateoperator` |
| Eine Seite mit eigener Adresse, ein Formular, einen Bericht | Modul und View | [Module und Views](../../../../eng-us/help/extension-development/part-3-extension-developer/modules-and-views.md) | `/setup/moduleextension`, `/setup/modulewizard` |
| Eine neue Art von Feld für Redakteure | Datentyp | [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md) | `/setup/datatype` |
| Einen Schritt rund um das Veröffentlichen, der warten oder nachfragen kann | Workflow-Ereignis | [Workflow-Ereignistypen](../../../../eng-us/help/extension-development/part-3-extension-developer/workflow-event-types.md) | `/setup/workflowevent` |
| Etwas nach Zeitplan | Cronjob-Teil | [Cronjob-Teile](../teil-3-erweiterungsentwickler/cronjob-teile.md) | `/setup/cronjobs` |
| Einen Befehl für Administratoren | Konsolenbefehl | [CLI-Skripte und Konsolenbefehle](../teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md) | |
| Eine Reaktion auf ein Kernel-Ereignis oder einen Filter für einen Wert | Event-Listener | [Ereignisse, Listener und Filter](../teil-3-erweiterungsentwickler/ereignisse-und-filter.md) | `/setup/settingsextension` |
| Einen anderen Mail-Transport, Login, Session, Suche, Mehrwertsteuer | über eine Einstellung benannter Handler | [Über Einstellungen benannte Handler](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) | `/setup/handlerextension/<kind>` |
| Einen dynamischen Teil einer Seite aus JavaScript | ezjscore-Serverfunktion | [Ajax mit ezjscore-Serverfunktionen](../teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md) | `/setup/handlerextension/ajaxfunction` |
| Eine eigene Tabelle | persistentes Objekt | [Persistente Objekte und eigene Tabellen](../../../../eng-us/help/extension-development/part-3-extension-developer/persistent-objects-and-tables.md) | `/setup/modulewizard` |
| Das Verhalten einer Kernel-Klasse ändern, wenn nichts anderes hilft | Kernel-Override | [Kernel-Klassen-Overrides: der letzte Ausweg](../teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md) | `/setup/kerneloverride` |

Der RAD-Katalog unter `/setup/rad` listet jeden Erweiterungspunkt mit denselben Informationen auf; diese Tabelle ist die
Kurzfassung.

## Bevorzugen Sie den leichtesten Mechanismus

1. Eine Einstellung.
2. Ein Template oder ein Override.
3. Ein Operator oder eine Fetch-Funktion (reine Funktionen, leicht zu testen).
4. Ein Listener oder ein Handler (über eine Einstellung registriert, austauschbar).
5. Ein Modul, ein Datentyp, ein Workflow-Ereignis (mehr Code, mehr Angriffsfläche).
6. Ein Kernel-Override (zuletzt).

Jede Stufe nach oben bringt Code, den Sie bei jedem Upgrade pflegen. Wählen Sie die niedrigste Stufe, die die
Anforderung erfüllt.

## Bevor Sie Code schreiben: Suchen Sie danach

Die Erweiterung, die Sie brauchen, gibt es vielleicht schon: Prüfen Sie die Erweiterungen auf Ihrer Festplatte, den
[se7enxweb-Katalog](../teil-1-vom-benutzer-zum-entwickler/der-se7enxweb-erweiterungskatalog.md),
[Exponential Projects](../teil-1-vom-benutzer-zum-entwickler/projects-exponential-earth.md) und GitHub. Ein Operator zum
Ersetzen von Zeichenketten, ein Tag-Feld, eine Sitemap: Das alles gibt es bereits.

## Ihr erstes PHP

Beginnen Sie mit [Teil 3: Der Erweiterungsentwickler](../teil-3-erweiterungsentwickler/README.md) und den
[Tutorials](../../../../eng-us/help/extension-development/part-3-extension-developer/tutorials/README.md): Die kleinste Erweiterung dauert fünf Minuten, und ein
Template-Operator ist ein gutes erstes Stück Code, weil er klein und rein ist und sofort sichtbar wird.

## Verwandte Seiten

- Zurück: [Eine Website bauen, ohne Code zu schreiben](eine-website-ohne-code-bauen.md)
- Weiter: [Teil 3: Der Erweiterungsentwickler](../teil-3-erweiterungsentwickler/README.md)
- [Erweiterungspunkte](../../../../eng-us/bc/6.0/rad-extension-points.md)
- [RAD-Werkzeuge](../../../../eng-us/features/6.0/rad-tools.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
