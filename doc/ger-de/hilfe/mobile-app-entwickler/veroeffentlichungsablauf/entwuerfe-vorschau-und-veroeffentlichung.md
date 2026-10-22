---
title: Entwürfe, Vorschau und Veröffentlichung
audience: mobile-app-developer, app-creator, developer, editor
category: mobile-apps
order: 710
summary: Wie Entwürfe und Versionen von Exponential-Inhalten mit einer App zusammenhängen - REST liest nur veröffentlichte Inhalte, Entwürfe und Versionen über die expversion-Dienste mit dem Recht versionread lesen, eine Redakteursvorschau für App-Bildschirme bauen, einen Entwurf veröffentlichen, und was Redakteure wissen sollten.
keywords: Entwürfe, Versionen, Vorschau, Veröffentlichen, expversion, versionread, dataMap, viewUrl, createDraft, publish, Redakteursvorschau, Staging, Mobile App, drafts, versions, preview, publishing, editor preview, staging, mobile app
type: help
---
# Entwürfe, Vorschau und Veröffentlichung

Diese Seite richtet sich an App-Entwickler, die gefragt werden: „Können Redakteure sehen, wie das in der App
aussieht, bevor es live geht?“, und an die Redakteure, die diese Frage stellen. Sie erklärt, was die APIs für
unveröffentlichte Inhalte bieten und wie Sie eine Vorschau bauen.

## Inhalt

1. [Was die REST-API liest](#was-die-rest-api-liest)
2. [Versionen und Entwürfe über die Remote-Dienste](#versionen-und-entwürfe-über-die-remote-dienste)
3. [Eine App-Vorschau bauen](#eine-app-vorschau-bauen)
4. [Aus einer App veröffentlichen](#aus-einer-app-veröffentlichen)
5. [Was Redakteure wissen sollten](#was-redakteure-wissen-sollten)

## Was die REST-API liest

Lesezugriffe auf Knoten und Objekte bauen die Antwort aus dem veröffentlichten Inhalt; die REST-Routen haben keinen
Parameter für Versionen oder Entwürfe ([Routenreferenz](../rest-api/routenreferenz.md)). Der Entwurf eines
Redakteurs ist für die App unsichtbar, bis er veröffentlicht wird.

## Versionen und Entwürfe über die Remote-Dienste

Die Domäne `expversion` (deklariert in `extension/expservices/classes/content/expversionservices.php`) liest
Versionen:

| Dienst | Argumente | Zugriff |
|---|---|---|
| `expversion::list` | Objekt-ID, Limit, Offset | `content/versionread` |
| `expversion::drafts` | Objekt-ID | `content/versionread` |
| `expversion::myDrafts` | Limit, Offset | `content/edit` |
| `expversion::dataMap` | Objekt-ID, Version, Sprache | `content/versionread` |
| `expversion::viewUrl` | Objekt-ID, Version, Sprache | `content/versionread`; die relative URL, die eine Version auf der Website in der Vorschau zeigt |
| `expversion::publish` (schreibend) | Objekt-ID, Version | `content/publish` |

`content/versionread` ist ein Recht für Redakteure. Der Benutzer einer öffentlichen App darf es nicht haben.

## Eine App-Vorschau bauen

Das für Headless-Frontends empfohlene Muster lautet: „die Entwurfsversion über einen authentifizierten Aufruf mit
Redakteursrechten lesen und in einer geschützten Vorschauroute rendern“
([Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md#vorschau-und-entwürfe)). Für eine App:

1. Ein Vorschau-Build der App (oder ein Vorschaumodus hinter einer Redakteursanmeldung), der sich als Redakteur
   anmeldet.
2. Der Redakteur öffnet einen Eintrag; die App listet dessen Entwürfe mit `expversion::drafts` auf und liest einen
   davon mit `expversion::dataMap::<object id>::<version>::<language>`.
3. Die App rendert den Entwurf mit denselben Bildschirmen wie veröffentlichte Inhalte, deutlich als Vorschau
   gekennzeichnet.

`expversion::dataMap` antwortet im Attributformat von expservices, nicht im Feldformat von REST; die Vorschau braucht
deshalb eine kleine Zuordnungsschicht. Testen Sie sie mit jeder Klasse, die die App anzeigt.

## Aus einer App veröffentlichen

Eine Redakteurs-App kann einen Entwurf mit `expversion::publish` (POST, `content/publish`) veröffentlichen oder mit
dem Anlegen über REST beziehungsweise `expnode::create` in einem Schritt anlegen und veröffentlichen. Der
Veröffentlichungs-Workflow der Website läuft wie in der Administrationsoberfläche, Freigabeschritte gelten also
weiterhin.

## Was Redakteure wissen sollten

- Durch das Veröffentlichen wird der Eintrag für die App sichtbar, möglicherweise erst, nachdem der Antwort-Cache des
  Servers abgelaufen ist (standardmäßig zehn Minuten) und die App das nächste Mal aktualisiert.
- Felder, die die App anzeigt (Teaser, Hauptbild), brauchen Inhalt, auch wenn das Template der Website sie nicht
  anzeigt.
- Wird ein Eintrag entfernt oder aus einem Ordner verschoben, den die App anzeigt, verschwindet er bei der nächsten
  Synchronisierung aus der App.

## Verwandte Seiten

- [Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Inhaltsverlauf](../../../anleitungen/inhaltsverlauf.md)
- [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
