---
title: Bereit für die App-Store-Prüfung
audience: mobile-app-developer, developer, project-manager
category: mobile-apps
order: 930
summary: Allgemeine Ratschläge, um eine App mit Exponential-Inhalten auf die Prüfung im App Store vorzubereiten - ein Konto für die Prüfer, vorhandene und stabile Inhalte, Fragen zu Anmeldung und Kontolöschung, Datenschutzangaben zu dem, was die App sendet, Barrierefreiheit von Bildern und Text und den Prüf-Build fern von Demos halten. Keine Zusammenfassung der Regeln irgendeines Stores.
keywords: App Store, Prüfung, Einreichung, Prüferkonto, Demokonto, Datenschutz, Datenerhebung, Kontolöschung, Barrierefreiheit, Alternativtext, Moderation, nutzergenerierte Inhalte, Mobile App, app store, review, submission, reviewer account, privacy, account deletion, accessibility, alternative text, content moderation, user generated content, mobile app
type: help
---
# Bereit für die App-Store-Prüfung

Diese Seite richtet sich an App-Entwickler und Product Owner, die eine App einreichen, die Exponential-Inhalte zeigt.
Sie enthält allgemeine Ratschläge zu den Teilen einer Prüfung, die vom Inhaltsserver abhängen. Die Regeln jedes Stores
ändern sich und werden hier nicht wiederholt: Lesen Sie die aktuellen Richtlinien des Stores, bei dem Sie einreichen,
und behandeln Sie diese Seite als Liste der Dinge, die Sie auf der Seite von Exponential vorbereiten.

## Inhalt

1. [Ein Prüfer kann die App benutzen](#ein-prüfer-kann-die-app-benutzen)
2. [Anmeldung und Konten](#anmeldung-und-konten)
3. [Datenschutzangaben](#datenschutzangaben)
4. [Inhalte, die Benutzer hinzufügen](#inhalte-die-benutzer-hinzufügen)
5. [Barrierefreiheit](#barrierefreiheit)

## Ein Prüfer kann die App benutzen

- [ ] Der Prüf-Build zeigt auf die Produktion oder auf eine stabile Staging-Installation, nie auf eine öffentliche Demo,
      die planmäßig neu aufgebaut wird ([Gegen eine Demo testen](../testen/gegen-eine-demo-testen.md#regeln-für-gemeinsam-genutzte-demos)).
- [ ] Wenn die App eine Anmeldung braucht, gibt es auf dieser Installation ein Prüferkonto mit derselben Rolle wie echte
      Benutzer, und seine Zugangsdaten stehen nur in den Prüfhinweisen des Stores.
- [ ] Die Ordner, die die App zeigt, haben während der Prüfung Inhalte; eine leere Liste wirkt wie eine defekte App.
- [ ] Das Prüferkonto kann keine echten Inhalte ändern oder entfernen.

## Anmeldung und Konten

- [ ] Wenn sich Benutzer über OAuth mit ihrem Exponential-Konto anmelden, öffnet sich die Anmeldung im Systembrowser und
      kehrt zur App zurück ([OAuth2-Autorisierungscode-Ablauf](../anmeldung/oauth2-autorisierungscode-ablauf.md#schritt-2-die-autorisierungsseite)).
- [ ] Benutzer ohne `oauth/authorize` sehen eine klare Meldung statt einer Sackgasse (die Website leitet mit
      `error=access_denied` weiter).
- [ ] Wenn die App Konten anlegen lässt, planen Sie, wie ein Benutzer die Löschung seines Kontos beantragt; das Löschen
      von Benutzern ist eine Aufgabe des Administrators auf der Website, also braucht die App einen Weg zu diesem
      Vorgang (ein Formular, eine Support-Adresse).
- [ ] Das Abmelden funktioniert und entfernt Tokens ([Geheimnisse auf Geräten speichern](../anmeldung/geheimnisse-auf-geraeten-speichern.md#abmelden)).

## Datenschutzangaben

Die Formulare der Stores fragen, welche Daten die App erhebt und sendet. Listen Sie für die Seite von Exponential
ehrlich auf:

- [ ] Welche Benutzerdaten die App an die Installation sendet (Anmeldung, Inhalte, die der Benutzer erstellt).
- [ ] Dass Anfragen vom Server aufgezeichnet werden: Ereignisse von API-Schlüsseln und Tokens im Prüfprotokoll, bei der
      Schlüsselnutzung mit der Client-Adresse ([Prüfprotokoll und Grenzen](../../../anleitungen/api-schluessel.md#8-audit-und-grenzen)),
      und die Protokolle des Webservers.
- [ ] Welche Drittanbieter Ihr Backend oder Ihre Analyse hinzufügen.
- [ ] Wo die Installation gehostet wird, wenn Ihr Datenschutztext das nennt. Die Datenschutzgrundlagen der
      Organisation: [DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md) (Grundlagen, keine Rechtsberatung).

## Inhalte, die Benutzer hinzufügen

- [ ] Wenn Benutzer Inhalte einstellen können, landen neue Elemente in einem Ordner oder Zustand, den Redakteure prüfen,
      bevor die Öffentlichkeit sie sieht, oder es gibt einen Weg, Inhalte schnell zu melden und zu entfernen.
- [ ] Redakteure wissen, wo Einsendungen aus der App ankommen und wer sie moderiert.
- [ ] Schreibzugriffe sind per Rolle auf diesen Ordner beschränkt ([Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)).

## Barrierefreiheit

- [ ] Jedes Bild hat eine Bedienungshilfen-Beschriftung; verwenden Sie den Alternativtext aus `expimage::alt` oder
      `expimage::info` und bitten Sie die Redakteure, ihn auszufüllen
      ([Bilder in REST-Antworten](../medien-und-bilder/bilder-in-rest-antworten.md#alternativtext-und-bildinformationen)).
- [ ] Text aus Rich-Text-Feldern skaliert mit den Schriftgrößeneinstellungen des Benutzers
      ([Rich Text in Apps](../inhaltsmodellierung/rich-text-in-apps.md)).
- [ ] Rechts-nach-links-Sprachen spiegeln das Layout, wenn die App sie unterstützt
      ([Mehrsprachige Inhalte](../inhaltsmodellierung/mehrsprachige-inhalte.md#ersatzsprachen-in-der-app)).

## Verwandte Seiten

- [Checkliste für den Start von Apps](checkliste-fuer-den-start.md)
- [Sicherheitscheckliste für Apps](sicherheitscheckliste.md)
- [Barrierefreiheit testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
