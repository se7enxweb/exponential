---
title: Wie gebe ich einer Seite eine zusätzliche Adresse (URL-Alias)?
audience: admin, editor
category: manual
order: 520
summary: Wählen Sie im Kontextmenü der Seite URL-Aliase verwalten, geben Sie unter Neuer Alias die neue Adresse ein, wählen Sie, ob sie weiterleitet, und klicken Sie auf Erstellen; globale Aliase für Modulansichten stehen unter Setup > URL-Übersetzer.
keywords: URL-Alias, zusätzliche Adresse, Kurzadresse, Weiterleitung, URL-Aliase verwalten, URL-Übersetzer, globaler Alias, content/urltranslator, URL alias, extra address, short address, redirect, manage URL aliases, URL translator, global alias
type: help
---
# Wie gebe ich einer Seite eine zusätzliche Adresse (URL-Alias)?

Jede Seite hat eine Adresse, die aus ihrem Namen gebildet wird. Ein Alias fügt eine weitere hinzu, zum Beispiel eine
kurze Adresse für Gedrucktes (`/summer`) oder die alte Adresse einer verschobenen Seite.

## Wer das tun kann

Benutzer, deren Rolle `content/urltranslator` hat, oder `content/edit` für die Seite für deren eigene Aliase.

## Schritte

1. Öffnen Sie in der **Inhaltsstruktur** das Kontextmenü der Seite und wählen Sie **URL-Aliase verwalten**.
   Die Seite **URL aliases for** *Name* listet die **Generated aliases** und Ihre eigenen auf.
2. Geben Sie unter **Neuer Alias** die Adresse ein, zum Beispiel `summer`.
3. Wählen Sie die **Sprache**. Markieren Sie **Alias im Hauptverzeichnis erstellen**, damit daraus `/summer` wird statt
   einer Adresse unterhalb des übergeordneten Elements der Seite.
4. Markieren Sie **Der Alias soll zum Ziel weiterleiten**, um Besucher an die echte Adresse weiterzuschicken (am besten
   für Suchmaschinen); lassen Sie es aus, um die Seite unter dem Alias anzuzeigen.
5. Klicken Sie auf **Erstellen**.
6. Für einen Alias, der auf etwas anderes als eine Seite zeigt (eine Modulansicht, zum Beispiel `content/search`),
   verwenden Sie **Setup** > **URL-Übersetzer**: Füllen Sie unter **Neuer Alias** die Felder **Neuer URL-Alias** und
   **Ziel** aus und klicken Sie auf **Erstellen**.

![Der URL-Übersetzer mit dem Formular Neuer Alias und der Liste der globalen Aliase](../../../eng-us/help/manual/images/url-translator.webp)

## Was schiefgehen kann

- **„The URL alias ... already exists“.** Eine andere Seite oder ein anderer Alias verwendet die Adresse. Wählen Sie
  eine andere, oder entfernen Sie zuerst den alten Alias.
- **Zeichen ändern sich.** Leerzeichen und Sonderzeichen werden durch gültige ersetzt, wie die Seite angibt.
- **Viele alte Adressen müssen irgendwohin führen.** Verwenden Sie einen Platzhalter: siehe
  [Wie leite ich eine Gruppe alter Adressen um?](wie-leite-ich-alte-adressen-um.md)

## Verwandte Seiten

- [URLs und Aliase](../../anleitungen/urls-und-aliase.md)
- [URL-Aliase und sprechende URLs](../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)
- [URLs, Weiterleitungen und verschobene Seiten](../kunden/alltag/urls-weiterleitungen-und-verschobene-seiten.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
