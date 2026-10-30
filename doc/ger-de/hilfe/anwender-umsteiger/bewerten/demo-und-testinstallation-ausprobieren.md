---
title: Demo und Testinstallation ausprobieren
audience: adopter, customer, businesses, admin
category: adoption
order: 12
summary: Wie Anwender Exponential arbeiten sehen, bevor sie sich festlegen - die öffentliche Demonstrationsinstallation und ihr Demomodus, eine Testinstallation auf SQLite mit einem Befehl, was Sie sich an einem Nachmittag ansehen und was nicht in ein Testsystem gehört.
keywords: Demo, Demonstration, Demomodus, Testinstallation, SQLite, Installation mit einem Befehl, Rundgang durch die Administration, bewerten, Nachmittag, demo, demonstration, demo mode, test installation, install in one command, admin tour, evaluate, afternoon
type: help
---
# Demo und Testinstallation ausprobieren

## Inhalt

1. [Die öffentliche Demo](#die-öffentliche-demo)
2. [Eine eigene Testinstallation](#eine-eigene-testinstallation)
3. [Was Sie sich an einem Nachmittag ansehen](#was-sie-sich-an-einem-nachmittag-ansehen)
4. [Was Sie mit einem Testsystem nicht tun](#was-sie-mit-einem-testsystem-nicht-tun)

## Die öffentliche Demo

Die Hilfe nennt eine laufende Demonstrationsinstallation als Quelle ihrer Fakten
([Unternehmen: hier beginnen](../../unternehmen/unternehmen-hier-beginnen.md)). Eine öffentliche Demo läuft absichtlich im
Demomodus: "administrator passwords cannot be changed or reset there", Administratorpasswörter lassen sich dort also
weder ändern noch zurücksetzen ([Demomodus](../../demomodus.md), [FAQ zum Demomodus](../../demomodus-faq.md)). Behandeln
Sie alles, was Sie dort eintippen, als öffentlich.

## Eine eigene Testinstallation

Eine Testinstallation zeigt die Software auf Ihrem eigenen Rechner, mit Ihren eigenen Inhalten:

1. Installieren Sie PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar) und Composer.
2. Folgen Sie [Installation mit einem Befehl](../../../funktionen/6.0/installation-mit-einem-befehl.md); SQLite ist der Standard
   und braucht keinen Datenbankserver.
3. Liefern Sie sie mit Velocity oder dem eingebauten Server von PHP aus ([Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)).

Die Schritt-für-Schritt-Anleitung für Erstanwender ist [Erste Schritte](../../erste-schritte.md); das vollständige
Installationsbuch beginnt bei [Einleitung](../../../installation/01-einfuehrung.md).

## Was Sie sich an einem Nachmittag ansehen

| Ansehen | Seite |
|---|---|
| Die Administration und ihre Designs | [Rundgang durch die Administration](../../rundgang-durch-die-administration.md) |
| Inhalte anlegen und veröffentlichen, Versionen | [Inhalte bearbeiten](../../inhalte-bearbeiten.md), [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md) |
| Eine Seite bauen | [Der Layout-Editor](../../layout-editor.md) |
| Sprachen | [Sprachen](../../sprachen.md) |
| Suche | [Suchen](../../suchen.md) |
| Bilder und Dateien | [Medien und Uploads](../../medien-und-uploads.md) |
| Die Hilfe selbst | das "?" in der Administration |

Schreiben Sie auf, was Sie überrascht hat, in beide Richtungen. Diese Notizen fließen in den Bewertungsbogen ein
([Der Bewertungsbogen](bewertungsbogen.md)).

## Was Sie mit einem Testsystem nicht tun

- Verwenden Sie keine echten personenbezogenen Daten von Kunden oder Mitarbeitenden.
- Stellen Sie den eingebauten Server von PHP nicht ins Internet: Er ist "Development only", nur für die Entwicklung
  (`README.md`).
- Verwenden Sie die Testinstallation nicht als Produktion weiter, ohne sie neu zu installieren und abzusichern
  ([Sicherheitshärtung](../../sicherheitshaertung.md)).

## Verwandte Seiten

- [Exponential bewerten](README.md)
- [Ein Machbarkeitsnachweis](machbarkeitsnachweis.md)
- [Erste Schritte](../../erste-schritte.md)
- [Rundgang durch die Administration](../../rundgang-durch-die-administration.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
