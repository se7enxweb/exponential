---
title: "Die Bausteine einer Erweiterung: Module, Datentypen, Operatoren, Workflow-Ereignisse, Benachrichtigungen"
audience: developer
category: books
order: 530
summary: Der Rest des Kapitels über das Erweitern (Moduldefinitionen, View-Parameter, Aktionen, Navigation, Modulprogrammierung, Datentypen, Operatoren, Workflow-Ereignisse, Benachrichtigungen, SOAP und RSS) als Wegweiser zu den Entwicklerseiten, die heute jeden Teil behandeln.
keywords: Modul, View-Parameter, Post-Aktionen, Navigationsteil, Datentyp, Template-Operator, Workflow-Ereignistyp, Benachrichtigungs-Handler, Buch Learning, module, module.php, ViewList, view parameters, post actions, navigation part, Result, datatype, template operator, workflow event type, notification handler, SOAP, RSS, REST, Learning book
type: help
---
# Die Bausteine einer Erweiterung

Das fünfte Kapitel des Buchs geht die Arten von Erweiterungen einzeln durch. Diese Seite behält seine Reihenfolge bei,
sagt, was jeder Baustein ist, und schickt Sie zu der Seite, die ihn heute behandelt. Der Code im Buch ist im Stil von
PHP 4 geschrieben (`&`-Referenzen, `include_once`, `EZ_`-Konstanten). Das PHP von Exponential ist 8: Klassen werden per
Autoloading geladen, Konstanten gehören zu Klassen, und Referenzen werden nicht gebraucht.

## Module

Ein **Modul** ist eine Menge von Views, die über eine Adresse erreicht werden: `/hello/world` ist die View `world` des
Moduls `hello`. Ein Modul liegt in `modules/<name>/` einer Erweiterung.

- **Moduldefinition** (`module.php`): der Name des Moduls, seine `$ViewList` (jede View mit ihrem `script`, den `params`,
  die sie annimmt, `unordered_params`, `functions`, `default_navigation_part` und `single_post_actions` oder
  `post_actions`) und seine `$FunctionList` für Berechtigungen ([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md),
  [Richtlinienfunktionen und Zugriff](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)).
- **View-Parameter**: die Teile der Adresse nach dem Namen der View, in der Reihenfolge, in der `params` sie nennt
  ([View-Parameter](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/view-parameter.md)).
- **View-Aktionen und Post-Variablen**: Der Name einer Formular-Schaltfläche wird einer Aktion zugeordnet, und
  `isCurrentAction()` sagt dem View-Skript, was gedrückt wurde. Formulare müssen das Formular-Token mitführen.
- **View-Navigation**: `default_navigation_part` wählt, welcher Reiter der Administration aktiv ist.
- **Modulprogrammierung**: Ein View-Skript liest seine Eingabe (`$Params`, `$_POST` über das `http`-Objekt des Moduls)
  und endet damit, `$Result` zu füllen: `content` (die Ausgabe eines Templates), `path` (die Brotkrumen) und optional
  `left_menu`, `title_path`, `pagelayout` ([View-Ergebnisse und das Seitenlayout](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/view-ergebnisse-und-das-pagelayout.md)).
  Leiten Sie mit `$Module->redirectTo( ... )` weiter und geben Sie Fehler mit `$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' )` zurück.

## Datentypen

Ein Datentyp ist eine Klasse, die eine Art von Wert prüfen, speichern, anzeigen und bearbeiten kann, mit Templates für
jede dieser Aufgaben. Das Kapitel des Buchs über Datentypen baut einen, indem es die Methoden von `eZDataType`
überschreibt. Heute gilt dasselbe Muster:
[Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentypen.md) und
[Speicherung, Prüfung und Templates von Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentyp-speicherung-validierung-und-templates.md).

## Template-Operatoren und -Funktionen

Ein Operator (`{$x|myoperator}`) ist eine Klasse, die von einer Template-Autoload-Datei registriert wird. Für einfache
Fälle genügt eine PHP-Funktion in `template.ini` ([Caching in Templates](caching-in-templates.md), letzter Abschnitt).
Echte Operatoren:
[Template-Operatoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/template-operatoren.md),
[Template-Funktionen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/template-funktionen.md).

## Workflow-Ereignisse und Trigger

Ein neuer **Ereignistyp** ist eine Klasse in `eventtypes/event/<type>/`, die bei ihrer Ausführung entscheidet, ob der
Workflow weiterläuft (`STATUS_ACCEPTED`), wartet (`STATUS_FETCH_TEMPLATE_REPEAT`, `STATUS_DEFERRED_TO_CRON_REPEAT`) oder
anhält (`STATUS_REJECTED`) ([Workflow-Ereignistypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/workflow-ereignistypen.md),
[Trigger und Workflows für Entwickler](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/trigger-und-workflows-fuer-entwickler.md)).
Die Benutzerseite steht in [Workflows und Trigger](workflows-und-trigger.md).

## Benachrichtigungen

Ein Benachrichtigungs-Handler macht aus den Ereignissen, die das System sammelt, E-Mails. Die Status, die das Buch nennt
(`EZ_NOTIFICATIONEVENTHANDLER_EVENT_HANDLED`, `_SKIPPED`, `_UNKNOWN`, `_ERROR`), sind heute die Klassenkonstanten
`EVENT_HANDLED`, `EVENT_SKIPPED` und so weiter von `eZNotificationEventHandler`. Siehe [Benachrichtigungen für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md).

## Daten hinein und hinaus bewegen: SOAP und RSS

Das Buch schließt mit Webdiensten: SOAP für Programme, RSS für Feeds. Feeds sind weiterhin RSS und Atom, aus der
Administration ([RSS-Feeds](../../anleitungen/rss-feeds.md)). Für Programme ist die Schnittstelle die REST-API, mit Schlüsseln
und OAuth ([REST-API](../../anleitungen/rest-api-v1-und-v2.md), [API-Schlüssel](../../anleitungen/api-schluessel.md)); bauen Sie darauf
auf, statt SOAP hinzuzufügen.

Weiter: [Bewährte Praktiken der Erweiterungsentwicklung](bewaehrte-praktiken-der-erweiterungsentwicklung.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 5, „Extending eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: als Wegweiser zu den aktuellen Entwicklerseiten umgestellt; Ordnernamen, Einstellungen und Konstanten gegen Exponential 6 geprüft; SOAP durch die REST-API ersetzt.
