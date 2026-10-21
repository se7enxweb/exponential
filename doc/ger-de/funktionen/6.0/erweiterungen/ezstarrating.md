# ezstarrating: Sternebewertungen für Inhalte

Diese Seite ist für Website-Entwickler, die Besucher Inhalte bewerten lassen. `ezstarrating` fügt einen Datentyp
**Star Rating** hinzu (`ezsrrating`). Fügen Sie das Attribut einer Inhaltsklasse hinzu, und Besucher können Objekte
dieser Klasse mit 1 bis 5 Sternen bewerten.

- Jeder Benutzer darf einmal pro Sitzung abstimmen.
- Bewertungen werden in der Tabelle `ezstarrating_data` gespeichert und zu einem Durchschnitt pro Objekt
  zusammengerechnet.
- Templates können die Bewertung anzeigen, und der erweiterte Attributfilter `ezsrRatingFilter` sortiert und holt nach
  Bewertung.
- Es hängt von `ezjscore` ab: Der Bewertungsaufruf ist die ezjscore-Funktion `ezstarrating_rate`, mit
  `ezstarrating_user_has_rated`.

## Einrichten

1. Aktivieren Sie die Extension (`ActiveExtensions[]=ezstarrating`) und erzeugen Sie die Autoloads neu:
   `php bin/php/ezpgenerateautoloads.php -e`.
2. Legen Sie die Bewertungstabelle aus `extension/ezstarrating/sql/<engine>/` an (mysql, postgresql, oracle; ein
   SQLite-Schema kam in 6.0.3 hinzu).
3. Gewähren Sie den Rollen, die abstimmen dürfen, die Richtlinie Modul `ezjscore`, Funktion `call`, Einschränkung
   `ezstarrating_rate`.
4. Fügen Sie der zu bewertenden Klasse ein Attribut **Star Rating** hinzu.
5. Leeren Sie die Caches und öffnen Sie ein Objekt dieser Klasse: Die Sterne erscheinen.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 6.0.3 | 27. September 2026 | Siehe unten. Ein SQLite-Schema für die Bewertungstabellen kam hinzu. |
| 6.0.4 | | Die Beschreibung nennt Exponential. |
| 6.0.5 | 1. Oktober 2026 | Die Sternebewertung verwendet jQuery 4 (`.on()` und `.off()` statt der Kurzform für Klicks und `.unbind()`). |
| 6.0.6 | 2. Oktober 2026 | Das Bewertungsskript für YUI 3 ist entfernt. `ezsrrating.tpl` lädt `ezstarrating_jquery.js` unabhängig davon, was `ezjscore.ini [...] PreferredLibrary` sagt, und `ezstarrating_yui3.js` ist weg. Siehe [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md). |
| 6.0.7, 6.0.8 | | Copyright-Hinweise; englische und deutsche Übersetzungen für jeden Text, den die Administrationsoberfläche unübersetzt zeigte. |

Zu 6.0.3:

- Die Bewertungsklassen deklarierten **geschützte Konstruktoren** über dem öffentlichen Konstruktor von
  `eZPersistentObject`, was PHP 8 beim Laden der Klasse zurückweist. Sie sind wieder öffentlich;
  `ezsrRatingObjectTreeNode` ruft den Konstruktor des persistenten Objekts auf, und die leeren Konstruktoren im Stil von
  PHP 4 sind weg.
- `ezsrRatingObject::stats()` hielt seinen Cache in einer statischen Funktionsvariablen, die ein dauerhafter Worker
  (Velocity) von einer Anfrage zur nächsten mitnimmt. Er liegt jetzt in einer statischen Klasseneigenschaft, sodass er
  begrenzt bleibt, neue Stimmen zeigt und nie Datenbanken überschreitet.

## Verwandte Seiten

- [sevenx_themes_simple](sevenx_themes_simple.md): Die Bewertungsansicht des Simple-Themes lädt das jQuery-Skript
- [Backend-Dienste von ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Chronik](../../../geschichte/erweiterungen/ezstarrating.md) und [Versionshinweise](../../../../changelogs/extensions/ezstarrating.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezstarrating.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
