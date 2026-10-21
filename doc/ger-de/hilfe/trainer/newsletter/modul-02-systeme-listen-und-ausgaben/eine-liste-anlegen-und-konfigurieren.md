---
title: "Lektion 2.2: Eine Liste anlegen und konfigurieren"
audience: trainer, editor, admin
category: training
order: 22
summary: Die Teilnehmenden legen in ihrem System eine Newsletter-Liste an und füllen ihre Konfiguration (Siteaccess, Ausgabeformate, Skin, Absender, Testempfänger, Personalisierung) mit sicheren Schulungswerten aus. 60 Minuten.
keywords: newsletter mit cjw newsletter, newsletter-liste, listenkonfiguration, absender, testempfänger, ausgabeformat, skin, siteaccess, personalisieren, abonnement freigeben, cjw_newsletter_list, newsletter list configuration, newsletters with cjw newsletter, newsletter list, sender, test receiver, output format, personalize, approve subscription
type: help
---
# Lektion 2.2: Eine Liste anlegen und konfigurieren

Eine Liste ist das, was man abonniert, und ihre Konfiguration entscheidet, wie jede ihrer Ausgaben versendet wird:
von welcher Adresse, in welchen Formaten, von welchem Siteaccess und Skin gerendert und an wen Testmails gehen. In
dieser Lektion legt jede Person eine Liste an und füllt die Konfiguration mit Werten aus, die für die Schulung sicher
sind.

> **Hinweis**
> Alle Adressen in dieser Lektion enden auf `@example.invalid`. Tippen Sie niemals eine echte Adresse in eine
> Schulungsliste, nicht einmal Ihre eigene.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 2: Systeme, Listen und Ausgaben](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure und Administratoren |
| Voraussetzungen | [Lektion 2.1](newsletter-systeme-und-der-baum.md): Die Person hat ein Newsletter-System |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- unterhalb ihres Systems eine **CJW Newsletter Liste** anlegen;
- jeden Teil von **Newsletter list configuration** erklären und sichere Schulungswerte wählen;
- den Unterschied zwischen Absender-, Reply-To- und Return-Path-Adresse erklären;
- die Konfiguration auf der Seite der Liste nachlesen und die Zahlen ihrer Abonnements finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#the-parts)
und die [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md). Die Feldbeschriftungen unten sind
die, die das Bearbeitungsformular einer Standardinstallation zeigt; prüfen Sie Ihr eigenes Formular einmal vor dem
Kurs.

1. **Die Felder** (5 Minuten). Eine Liste hat **Title** (Pflicht), **Short title**, **Short description** und das
   Pflichtfeld **Newsletter list configuration**. Die Konfiguration ist ein Feld mit vielen Teilen.
2. **Rendering** (5 Minuten). Die Liste benennt den **Siteaccess**, der ihre Ausgaben rendert (und ihren
   Haupt-Siteaccess, die Website-URL und die Locale, die auf der Seite der Liste gezeigt werden), die verfügbaren
   Ausgabeformate (HTML und Text) und die **Newsletter Vorlage** (den Skin). Die gerenderten Links zeigen auf die
   Website-URL dieses Siteaccess, deshalb muss `[SiteSettings] SiteURL` auf einer echten Website stimmen.
3. **Adressen** (5 Minuten). **Newsletter E-Mail-Absender** und **Newsletter Absendername** sind das *From* jeder
   Ausgabe. **E-Mail Antwortadresse (Reply-To)** ist, wohin Antworten gehen; **E-Mail Rücksendepfad (Return-Path)**
   ist, wohin Bounces gehen (Modul 5). **Newsletter Standard E-Mail-Testempfänger** ist die Adresse, an die ein
   Testversand geht (Lektion 4.1).
4. **Optionen** (5 Minuten). **Newsletter personalisieren?** füllt Platzhalter wie den Namen (Lektion 6.1). **Neue
   Newslettereinträge nach Anmeldung automatisch bestätigen?** nimmt Abonnements registrierter Benutzer ohne
   Bestätigungsschritt an. Lassen Sie die Optionen aus 4.2 (Freigabe, Artikel-Pool, wiederkehrende Versände,
   erlaubte Skins, Sprachen, Interessen, Statistiken, SMS) vorerst auf ihren Standardwerten; Statistiken bleiben aus.

### Häufige Fragen

**Warum warnt das Dashboard vor der Standard-Absenderadresse?** Das mitgelieferte `EmailSender` ist
`newsletter@example.com`. Der eigene Absender der Liste überschreibt es; auf einer echten Website setzen Sie beide
([Spezifikation](../../../../spezifikationen/6.0/cjw_newsletter.md#probleme-die-das-dashboard-meldet)).

**Kann eine Ausgabe an zwei Listen gehen?** Nein. Eine Ausgabe gehört zu einer Liste. Erstellen Sie eine zweite
Ausgabe (Kopieren hilft).

**Was bedeutet „Abonnement möglich“?** Es steuert, ob die Liste im öffentlichen Anmeldeformular angeboten wird.
Prüfen Sie das genaue Verhalten auf Ihrer Installation, indem Sie sich nach der Änderung das Anmeldeformular ansehen.

**Was ist der Knopf „Filter hier erstellen“?** Er gehört zu virtuellen Listen, die Abonnenten über Filter auswählen.
Dieser Kurs behandelt sie nicht.

### Stolperfallen

- **Aus Gewohnheit eine echte Adresse eingetippt.** Die Teilnehmenden geben ihre eigene Adresse als Testempfänger
  ein. Prüfen Sie jedes Formular vor dem Veröffentlichen.
- **Falscher Siteaccess.** Eine vom Siteaccess der Administration gerenderte Liste erzeugt Links der Administration in
  der Mail. Wählen Sie den öffentlichen Siteaccess der Website.
- **Kein Ausgabeformat angehakt.** Eine Ausgabe ohne Format kann nicht gerendert werden; haken Sie HTML und Text an.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Schulungskonto für die Administration. Ersetzen Sie `<name>` durch
Ihren Vornamen.

1. Öffnen Sie Ihr System `TS <name>` aus Lektion 2.1.
2. Klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie **CJW Newsletter Liste**.
3. Geben Sie in **Title** `Weekly tips <name>` ein. Lassen Sie **Short title** leer.
4. Geben Sie in **Short description** einen Satz ein, den ein Besucher im Anmeldeformular lesen würde.
5. Wählen Sie in **Newsletter list configuration** den öffentlichen Siteaccess der Schulungswebsite (fragen Sie den
   Trainer, welchen).
6. Haken Sie beide verfügbaren Ausgabeformate an (HTML und Text).
7. Wählen Sie den Skin `default`.
8. **Newsletter E-Mail-Absender**: `newsletter@example.invalid`. **Newsletter Absendername**: `Training newsletter`.
9. **E-Mail Antwortadresse (Reply-To)**: `reply@example.invalid`. **E-Mail Rücksendepfad (Return-Path)**:
   `bounces@example.invalid`.
10. **Newsletter Standard E-Mail-Testempfänger**: `test-<name>@example.invalid`.
11. Haken Sie **Newsletter personalisieren?** an. Lassen Sie die anderen Optionen, wie sie sind.
12. Klicken Sie auf **Zur Veröffentlichung schicken**.
13. Lesen Sie auf der Seite der Liste die Konfiguration nach und suchen Sie die Links **Abonnements**, **Wartend**,
    **Bestätigt**, **Angenommen**, **Rückläufer**, **Entfernt** und **Blacklisted** mit ihren Zahlen.

## Erwartetes Ergebnis

- Die Liste `Weekly tips <name>` ist im System der Person veröffentlicht, ohne Abonnements.
- Ihre Seite zeigt den Absender `newsletter@example.invalid`, den Testempfänger unter `example.invalid`, beide
  Ausgabeformate und den Skin `default`.
- Die Liste erscheint in der Tabelle **Listen** des Newsletter-Dashboards.
- Der Trainer prüft jede Listenseite auf Adressen, die nicht unter `example.invalid` liegen.

## Quiz

1. Welche Adresse einer Liste erhält die Bounces?
   A. Absender B. Reply-To C. Return-Path D. Testempfänger
2. Warum muss die Liste einen öffentlichen Siteaccess benennen?
3. Was ermöglicht **Newsletter personalisieren?**?
4. Sie sehen den Dashboard-Hinweis zur Standard-Absenderadresse („Default sender address“). Was ist noch auf eine
   Beispieladresse gesetzt?
5. Kann eine Ausgabe an zwei Listen gesendet werden?

<details>
<summary>Antworten</summary>

1. **C.** Der Return-Path ist die Adresse, an die Mailserver unzustellbare Mail zurückschicken ([Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
2. Weil die Ausgabe dort gerendert wird und ihre Links und Bilder auf die Website-URL dieses Siteaccess zeigen.
3. Das Füllen von Platzhaltern wie dem Namen des Abonnenten, wenn die Daten vorhanden sind (siehe Lektion 6.1 und die
   Rendering-Anleitung, verlinkt von der [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
4. `EmailSender` in den Einstellungen oder der Absender einer Liste ([Spezifikation](../../../../spezifikationen/6.0/cjw_newsletter.md#probleme-die-das-dashboard-meldet)).
5. Nein. Eine Ausgabe gehört zu einer Liste.

</details>

## Weiterführende Informationen

- [Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md):
  Status und wo Abonnenten verwaltet werden.
- [Newsletter sicher versenden](../../../../../eng-us/help/creators/website-creators/sending-newsletters.md): Absenderdomain und
  Transporte für echten Versand.
- [cjw_newsletter: Newsletter in der Administration](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use):
  die Einstellungen hinter den Standardwerten der Liste.

## Verwandte Seiten

- [Modul 2: Systeme, Listen und Ausgaben](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
