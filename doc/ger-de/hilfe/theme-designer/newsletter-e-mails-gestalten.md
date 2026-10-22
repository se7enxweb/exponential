---
title: "Newsletter-E-Mails gestalten"
audience: theme-designer
category: theme-design
order: 29
summary: Wie Theme-Designer Newsletter-E-Mails für Exponential-Websites gestalten, die die Erweiterung CJW Newsletter verwenden - Skins mit einem HTML- und einem Textteil, warum das Design von E-Mails anderen Regeln folgt als das Web-Theme, die vier mitgelieferten Skins als Ausgangspunkte, Markenakzente, Platzhalter und wie Sie prüfen, ohne echte E-Mails zu senden.
keywords: Theme-Designer, Newsletter, E-Mail-Design, E-Mail, cjw_newsletter, Skin, HTML-Teil, Textteil, AvailableSkinArray, E-Mail-Templates, Platzhalter, Vorschau, theme designer, newsletter, email design, mail, skin, html part, text part, email templates, placeholders, preview
type: help
---
# Newsletter-E-Mails gestalten

Diese Seite ist für Theme-Designer, die den Newsletter an die Marke anpassen sollen, auf Installationen, die die
Erweiterung **CJW Newsletter** (`cjw_newsletter`) verwenden. Das Aussehen des Newsletters ist ein **Skin**: zwei
Templates, eines für den HTML-Teil und eines für den Textteil jeder E-Mail. Die Schritte, einen Skin anzulegen und zu
registrieren, sind eine Lektion des Newsletter-Kurses:
[Lektion 6.2: Einen eigenen Skin schreiben](../trainer/newsletter/modul-06-templates-und-skins/einen-eigenen-skin-schreiben.md).
Diese Seite ist das Design-Briefing dafür.

## Inhalt

1. [E-Mail ist nicht das Web](#e-mail-ist-nicht-das-web)
2. [Die mitgelieferten Skins](#die-mitgelieferten-skins)
3. [Was ein Skin enthält](#was-ein-skin-enthält)
4. [Den HTML-Teil gestalten](#den-html-teil-gestalten)
5. [Den Textteil gestalten](#den-textteil-gestalten)
6. [Marke, ohne etwas zu zerbrechen](#marke-ohne-etwas-zu-zerbrechen)
7. [Platzhalter und der Abmeldelink](#platzhalter-und-der-abmeldelink)
8. [Prüfen, ohne zu senden](#prüfen-ohne-zu-senden)

## E-Mail ist nicht das Web

E-Mail-Programme sind keine Browser. Viele ignorieren verlinkte Stylesheets und `<style>`-Blöcke, blockieren entfernte
Bilder standardmäßig und führen keine Skripte aus. Der Newsletter-Kurs lehrt deshalb andere Regeln für Skins: ein
Tabellenlayout von etwa 600 Pixel Breite, jeder Stil inline geschrieben, keine Webfonts, keine Hintergrundbilder und
absolute Adressen für jeden Link und jedes Bild. Das ist die einzige Stelle, an der Inline-Stile die dokumentierte
Praxis sind, und es gilt nur für die E-Mail-Templates des Skins. Ihr Web-Theme behält seine Regeln: keine Inline-Stile,
Tokens, gepackte Stylesheets ([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md)).

## Die mitgelieferten Skins

Die Erweiterung liefert vier Skins, jeder mit `html.tpl` und `text.tpl` unter
`newsletter/skin/<name>/outputformat/` in ihrem Standarddesign:

| Skin | Charakter |
|---|---|
| `default` | der klassische Skin; sein Textteil wird aus dem HTML umgewandelt |
| `company` | ein ruhiger Geschäftsbrief |
| `news` | eine Nachrichtenübersicht |
| `shop` | Karten mit Schaltflächen |

Die Funktion **Preview the skins** (Skins in der Vorschau ansehen) der Administration zeigt jeden mit seinem HTML-Teil
und seinem Textteil. Beginnen Sie mit dem, der Ihrem Briefing am nächsten kommt, und kopieren Sie ihn unter einem
neuen Namen in Ihr eigenes Design; bearbeiten Sie nie die Dateien der Erweiterung
([Lektion 6.1: Skins und Ausgabeformate](../trainer/newsletter/modul-06-templates-und-skins/skins-und-ausgabeformate.md)).

## Was ein Skin enthält

- Den **Betreff**, aus dem Template gesetzt.
- Den **Kopfbereich**: Logo, den Namen des Newsletters, das Datum der Ausgabe.
- Den **Inhalt** der Ausgabe, vom Skin angeordnet.
- Den **Fußbereich**: wer ihn sendet und warum, die Post- oder Kontaktangaben, die der Kunde machen muss, die Links zum
  Abmelden und zu den Einstellungen.

Ein Skin ändert, wie derselbe Inhalt einer Ausgabe angeordnet wird, nicht den Inhalt.

## Den HTML-Teil gestalten

- Eine Spalte für die meisten E-Mails; zwei Spalten fallen in manchen Programmen schlecht zusammen.
- Fließtext in einer Größe, die auf dem Telefon lesbar ist; Schaltflächen groß genug zum Antippen, mit echtem Text, nicht
  Text in einem Bild.
- Bilder mit Breite, Höhe und Alternativtext, denn viele Leser sehen die E-Mail zuerst mit blockierten Bildern. Die
  E-Mail muss ohne sie Sinn ergeben.
- Farben mit ausreichendem Kontrast; manche E-Mail-Programme wenden ihren eigenen Dunkelmodus an und kehren Farben um,
  testen Sie also ein dunkles Logo auf dunklem Hintergrund.
- Ein Logo als Bilddatei mit absoluter Adresse, nicht als Inline-SVG.

## Den Textteil gestalten

Manche Leser, Filter und Geräte sehen nur den Textteil. Gestalten Sie auch ihn: eine klare Titelzeile, Abschnitte durch
Leerzeilen getrennt, ausgeschriebene Links in eigenen Zeilen und den Abmeldelink am Ende. In Skins, die ihren eigenen
Textteil schreiben, brauchen Zeilenumbrüche neben Template-Tags Sorgfalt; die Lektion erklärt, wie.

## Marke, ohne etwas zu zerbrechen

Jeder Skin hat eine Einstellungsgruppe `[Skin_<name>]` mit einer Akzentfarbe, sodass sich ein Markenakzent oft ohne
Änderung der Templates setzen lässt. In einer Multisite-Installation mit mehreren Marken hält ein Skin pro Marke (für
die Listen dieser Marke registriert) jeden einfach ([Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md)).
Schriften: Verwenden Sie in den Inline-Stilen einen Stapel aus Systemschriften; ein Webfont lädt in den meisten
Programmen nicht.

## Platzhalter und der Abmeldelink

Redakteure können Platzhalter wie `[[name]]` und `[[unsubscribe_url]]` in eine Ausgabe schreiben, die pro Abonnent
ersetzt werden. Platzhalter für Namen brauchen die Personalisierungsoption der Liste und Abonnentendaten; gestalten Sie
also Begrüßungen, die sich auch ohne Namen gut lesen ("Hallo," statt "Hallo ,"). Jede Ausgabe trägt einen Abmeldelink;
der Fußbereich des Skins ist der Platz dafür, und er muss sichtbar und lesbar bleiben.

## Prüfen, ohne zu senden

Testen Sie einen Skin nie, indem Sie an echte Menschen senden. Die Einrichtung des Kurses schreibt Testsendungen auf
einer Entwicklungsinstallation in Dateien, und Testadressen verwenden `example.invalid`
([Newsletter mit cjw_newsletter](../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md#proben-ohne-echte-mails)).
Prüfen Sie:

1. die Vorschau des Skins, HTML- und Textteil;
2. eine Testsendung, in eine Datei geschrieben und in einem E-Mail-Programm geöffnet;
3. mit blockierten Bildern, in Telefonbreite und im Dunkelmodus eines E-Mail-Programms;
4. eine lange und eine kurze Ausgabe.

## Verwandte Seiten

- [Lektion 6.2: Einen eigenen Skin schreiben](../trainer/newsletter/modul-06-templates-und-skins/einen-eigenen-skin-schreiben.md)
- [Newsletter mit cjw_newsletter](../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md)
- [Die Funktionsseite zu cjw_newsletter](../../funktionen/6.0/erweiterungen/cjw_newsletter.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
