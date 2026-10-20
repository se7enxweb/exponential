# Inhaltsklassen: ein Attribut an die erste oder letzte Stelle verschieben

Diese Seite ist für Administratoren, die Inhaltsklassen bearbeiten. Seit dem 29. September 2026 hat jedes Attribut im
Klasseneditor neben seinen Pfeilen nach oben und unten Schaltflächen **an die erste Stelle verschieben** und **an die
letzte Stelle verschieben**, und das Verschieben funktioniert wieder zuverlässig.

## Verwenden

1. Öffnen Sie **Setup > Klassen**, wählen Sie eine Klassengruppe und bearbeiten Sie eine Klasse.
2. Klicken Sie neben den Pfeilen eines Attributs auf **an die erste Stelle verschieben** oder **an die letzte Stelle
   verschieben**.
3. Die Zeile wird sofort verschoben. Positionen und Prioritäten werden neu nummeriert, und der Server speichert die
   Verschiebung. Speichert der Server sie nicht, wird die Zeile zurückgesetzt, und eine Meldung sagt es.
4. Klicken Sie auf **Anwenden** oder **OK**. Die Prioritätsfelder folgen nach jeder Verschiebung den Zeilen, sodass die
   Reihenfolge, die Sie sehen, die gespeicherte Reihenfolge ist.

Bei ausgeschaltetem JavaScript senden die Schaltflächen das Formular ab, wie es die Pfeile tun.

## Für Entwickler

- `eZContentClassAttribute::moveToEdge()` setzt das Attribut an die erste oder letzte Stelle und nummeriert die anderen
  in ihrer bisherigen Reihenfolge von 1 bis n.
- `class/edit` verarbeitet `MoveTop` und `MoveBottom`, sowohl direkt auf der Seite als auch als Absenden des Formulars.
- Antworten auf der Seite tragen den Header `X-Class-Attribute-Moved: 1`. Die Seite nimmt eine Verschiebung nur mit
  diesem Header an. Ein Server, der die Aktion nicht verarbeitet, antwortet mit einer gewöhnlichen Seite (200), was
  früher als gespeicherte Verschiebung galt.

## Drei Fehler, die im selben Release behoben wurden

| Fehler | Ursache | Behebung |
|---|---|---|
| Die Pfeile luden die ganze Seite neu und funktionierten nur manchmal. | Das Seitenskript rief `.size()` von jQuery auf, das in jQuery 3 entfernt wurde (die Administrationsoberfläche lädt 3.7.1); es scheiterte, bevor die Schaltflächen zum Verschieben eingerichtet waren. Es funktionierte nur dort, wo ein älteres jQuery oder jQuery Migrate geladen war. | `.length` statt `.size()` hier, im Übersetzungsschritt der Inhaltsbearbeitung (`edit_languages.tpl`) und im Skript für die Beziehungen. |
| Die Seite und die gespeicherte Reihenfolge liefen auseinander. | Eine Verschiebung, die der Server nicht bestätigte, wurde nicht rückgängig gemacht. | Eine abgelehnte, fehlgeschlagene oder unbeantwortete Verschiebung wird zurückgesetzt, und die Seite sagt es. Positionsnummern und Zeilenfarben folgen den Zeilen. Die Anfrage sendet sowohl den Header `X-CSRF-Token` als auch das Formular-Token. |
| Ein Attribut wurde nicht verschoben, oder zwei andere tauschten den Platz. | Importierte oder alte Klassen können doppelte oder fehlende Platzierungen haben; `move()` tauschte nach der Platzierung, ohne dass etwas Gleichstände auflöste. | Die Platzierungen der Klassenversion werden zuerst von 1 bis n neu nummeriert (nach Platzierung, dann ID), wenn sie es nicht schon sind, und die Anfragen zum Verschieben laden die ID des Attributs. |

Geprüft mit einer Klasse, deren Attribute gleiche und lückenhafte Platzierungen hatten: Das Attribut wurde um genau
eine Stelle verschoben, und die Platzierungen lauteten danach 1 bis n.

## Verwandte Seiten

- [jQuery 4 und die Entfernung von YUI](../../../eng-us/features/6.0/jquery4-and-yui-removal.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
