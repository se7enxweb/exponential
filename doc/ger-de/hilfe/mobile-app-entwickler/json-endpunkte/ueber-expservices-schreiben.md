---
title: Über expservices schreiben
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 330
summary: Inhalte aus einer App mit den Knotendiensten von expservices anlegen, aktualisieren und entfernen - POST mit einem Token und ohne Formular-Token, expnode::create mit Klasse, Attributen als JSON und Sprache, expnode::update, das eine neue Version veröffentlicht, expnode::remove mit dem Papierkorb, die Felder, die sich nicht entfernt schreiben lassen, und das Prüfprotokoll jedes Schreibzugriffs.
keywords: schreiben, anlegen, aktualisieren, entfernen, expnode::create, expnode::update, expnode::remove, POST, Attribute, JSON, Sprache, remote_id, move_to_trash, Formular-Token, X-CSRF-Token, deniedInputTypes, Prüfprotokoll, Papierkorb, Mobile App, write, create, update, remove, attributes, language, form token, audit, trash, mobile app
type: help
---
# Über expservices schreiben

Diese Seite richtet sich an App-Entwickler, deren App Inhalte auf eine Weise ändert, die die REST-API nicht bietet, vor
allem das Aktualisieren eines vorhandenen Elements. Die Knotendienste sind in
`extension/expservices/classes/content/expnodeservices.php` deklariert; die Regeln für Schreibzugriffe stehen in der
[expservices-Referenz](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#schreibvorgänge).

## Inhalt

1. [Regeln für jeden Schreibzugriff](#regeln-für-jeden-schreibzugriff)
2. [Anlegen](#anlegen)
3. [Aktualisieren](#aktualisieren)
4. [Entfernen](#entfernen)
5. [Felder, die sich nicht schreiben lassen](#felder-die-sich-nicht-schreiben-lassen)
6. [Nach einem Schreibzugriff](#nach-einem-schreibzugriff)

## Regeln für jeden Schreibzugriff

- Ein Schreibzugriff braucht `POST`; seine Argumente sind Formularfelder im Inhalt, außer den IDs, die der Dienst in der
  URL entgegennimmt.
- Mit einem expservices-Token (`Bearer` und `X-Exp-Token`) ist kein Formular-Token nötig. Mit einem Sitzungscookie holen
  Sie das Formular-Token von `expsession::token` und senden es als Feld `ezxform_token` und als Header `X-CSRF-Token`.
- Der Dienst prüft seine Richtlinie als aufrufender Benutzer. Eine fehlende Richtlinie antwortet mit `403` im Umschlag.
- Jeder erfolgreiche Schreibzugriff schreibt das Ereignis `service.<domain>.<method>` ins Prüfprotokoll.

## Anlegen

```bash
curl -s -H "Authorization: Bearer $EXPSERVICES_TOKEN" -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     --data-urlencode 'class=article' \
     --data-urlencode 'attributes={"title":"Report from the field","intro":"<p>Short text</p>"}' \
     --data-urlencode 'language=eng-US' \
     'https://example.com/ezjscore/call/expnode::create::<parent node id>?ContentType=json'
```

`expnode::create` braucht `content/create` und veröffentlicht sofort. POST-Felder: `class`, `attributes` (eine JSON-Map
von Bezeichner zu Zeichenkette), `language` und optional `remote_id`. Der Dienst lehnt eine Klasse ab, die der Benutzer
unterhalb dieses Elternknotens nicht anlegen darf (`403`), prüft die Pflichtfelder der Klasse und antwortet mit `409`,
wenn die `remote_id` bereits vergeben ist. Eine eigene `remote_id` (zum Beispiel eine auf dem Gerät erzeugte UUID)
erlaubt der App daher sichere Wiederholungen: Ein zweiter Versuch antwortet mit `409`, statt das Element doppelt
anzulegen ([Knoten-IDs, Remote-IDs und Deep Links](../inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md)). Die
Feldbezeichner im Beispiel sind Platzhalter; verwenden Sie die Ihrer Klasse.

## Aktualisieren

```bash
curl -s -H "Authorization: Bearer $EXPSERVICES_TOKEN" -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     --data-urlencode 'attributes={"title":"Corrected title"}' --data-urlencode 'language=eng-US' \
     'https://example.com/ezjscore/call/expnode::update::<node id>?ContentType=json'
```

`expnode::update` braucht `content/edit`, ändert die Attribute, die Sie senden, und veröffentlicht eine neue Version.
Felder, die Sie nicht senden, behalten ihre Werte. Kleinere Änderungen haben eigene Dienste: `expnode::rename` (POST
`name`), `expnode::setPriority`, `expnode::hide`, `expnode::reveal`.

## Entfernen

```bash
curl -s -H "Authorization: Bearer $EXPSERVICES_TOKEN" -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     -X POST 'https://example.com/ezjscore/call/expnode::remove::<node id>?ContentType=json'
```

`expnode::remove` braucht `content/remove` und entfernt einen Knoten samt seinem Teilbaum. Das POST-Feld
`move_to_trash` ist standardmäßig `1`, die Inhalte landen also im Papierkorb, aus dem ein Redakteur sie wiederherstellen
kann. Große Entfernungen können an einen Inhaltsjob übergeben werden und antworten dann mit dem Job statt mit
`{removed}`.

## Felder, die sich nicht schreiben lassen

Die Knotendienste schreiben Felder der Datentypen `ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`, `ezpassword` und
`ezflowblock` nie entfernt: Ihr Zeichenkettenimport liest einen lokalen Pfad oder eine URL, oder sie enthalten
Zugangsdaten (`expContentBase::$deniedInputTypes`). Eine App kann über diese Dienste daher keine Fotos oder Dateien
anhängen ([Dateien und Uploads](../medien-und-bilder/dateien-und-uploads.md)).

## Nach einem Schreibzugriff

- Lesen Sie das Element erneut (über die Knoten-ID, mit der REST-API), um anzuzeigen, was veröffentlicht wurde;
  Workflows auf dem Server können es verändert haben.
- Machen Sie den Cache der App für das Element und für die Liste seines Elternknotens ungültig
  ([Caching in der App](../offline-und-leistung/caching-in-der-app.md)).
- Ist ein Schreibzugriff in eine Zeitüberschreitung gelaufen, suchen Sie das Element über seine `remote_id`
  (`expnode::getByRemoteId`), bevor Sie es erneut versuchen.

## Verwandte Seiten

- [expservices aus einer App aufrufen](expservices-aus-einer-app-aufrufen.md)
- [Inhalte anlegen und entfernen](../rest-api/inhalte-anlegen-und-entfernen.md)
- [Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
