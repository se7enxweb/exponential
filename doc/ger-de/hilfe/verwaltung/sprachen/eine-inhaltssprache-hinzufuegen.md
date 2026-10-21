---
title: Eine Inhaltssprache hinzufügen
audience: government, admin
category: public-sector
order: 40
summary: Schritt für Schritt für Administratoren - eine Inhaltssprache unter Setup > Sprachen hinzufügen, was das Hinzufügen bewirkt und was nicht, die Option „Individuell“ für eine Locale, die nicht in der Liste steht, und die nächsten Schritte, bevor Besucher etwas sehen.
keywords: Sprache hinzufügen, Inhaltssprache, Setup Sprachen, content/translations, eigene Locale, share/locale, neue Sprache, Amtssprache, add language, content language, custom locale, new language, official language
type: help
---
# Eine Inhaltssprache hinzufügen

Diese Seite richtet sich an Administratoren, die eine Sprache hinzufügen, in der Redakteure Inhalte schreiben können,
zum Beispiel eine zweite Amtssprache. Die Schritte und Meldungen stammen aus
[Inhaltssprachen, Abschnitt 4](../../../anleitungen/inhaltssprachen.md#4-eine-sprache-hinzufügen).

![Inhaltssprachen: die Sprachen, in denen Inhalte geschrieben werden können, mit „Sprache hinzufügen“](../../../../eng-us/help/images/government/gov-add-language.webp)

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Sprache hinzufügen](#die-sprache-hinzufügen)
3. [Eine Sprache, die nicht in der Liste steht](#eine-sprache-die-nicht-in-der-liste-steht)
4. [Was das Hinzufügen nicht bewirkt](#was-das-hinzufügen-nicht-bewirkt)
5. [Nächste Schritte](#nächste-schritte)

## Bevor Sie beginnen

- Sie brauchen die Richtlinie `content/translations` (Administratoren haben sie).
- Legen Sie den Locale-Code fest: drei Buchstaben für die Sprache, ein Bindestrich, zwei für das Land, etwa `fre-CA`,
  `esl-MX` (Spanisch ist in den Codes von Exponential `esl`) oder `cym-GB`. Die vollständige Liste mit den Namen in
  der jeweiligen Sprache steht in [Sprachen in Exponential](../../../../eng-us/languages.md#3-every-locale).

## Die Sprache hinzufügen

1. Öffnen Sie **Setup > Sprachen** (`/content/translations`) und klicken Sie auf **Sprache hinzufügen**.
2. Geben Sie unter **Sprache und Land** einen Teil des Namens, den Code oder das Land in **Sprache suchen** ein; die
   Liste wird beim Tippen eingegrenzt. Ohne JavaScript ist die Liste ein einfaches, nach Namen sortiertes Listenfeld.
3. Wählen Sie den Eintrag, zum Beispiel *German (Austria) · Deutsch (Österreich) · ger-AT*, und klicken Sie auf
   **Sprache hinzufügen**.

Die Übersicht zeigt die Meldung, dass die Sprache hinzugefügt wurde, und eine neue Karte, die besagt, dass sie noch
nicht verwendet wird und in keiner `SiteLanguageList` steht. Die Sprache erhält das nächste freie Bit; der Cache der
Inhaltsansichten wird geleert.

## Eine Sprache, die nicht in der Liste steht

Wählen Sie ganz oben in der Liste **Individuell** und füllen Sie **Name der spezifischen Übersetzung** und
**Örtlichkeit der spezifischen Übersetzung** (`xxx-YY`) aus. Für diesen Code muss eine Datei `share/locale/<code>.ini`
existieren, denn die Locale entscheidet auch, wie Datum und Zahlen geschrieben werden; kopieren Sie zuerst die
nächstliegende Locale-Datei unter dem neuen Namen und passen Sie sie an (siehe „Eigene Locales für nicht gelistete
Sprachen“).

## Was das Hinzufügen nicht bewirkt

Es übersetzt nichts und ändert keinen Siteaccess: Noch sieht kein Besucher etwas Neues.

## Nächste Schritte

1. Geben Sie der Sprache einen Siteaccess oder fügen Sie sie der `SiteLanguageList[]` eines bestehenden hinzu (siehe
   „Ein Siteaccess pro Sprache“).
2. Geben Sie Übersetzern eine auf die Sprache beschränkte Rolle (siehe „Rollen und Rechte für Übersetzer“).
3. Übersetzen Sie zuerst die Startseite und das Hauptmenü (siehe „Einen Eintrag Schritt für Schritt übersetzen“).
4. Markieren Sie gemeinsam genutzte Einträge als immer verfügbar (siehe „Immer verfügbare Inhalte“).

## Verwandte Seiten

- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Eine mehrsprachige Website bauen](../../../../eng-us/help/creators/website-creators/multilingual-sites.md)
- [Sprachen in Exponential](../../../../eng-us/languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
