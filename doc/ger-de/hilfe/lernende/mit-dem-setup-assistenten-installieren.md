---
title: Mit dem Setup-Assistenten installieren
audience: student
category: learning
order: 12
summary: Ein Leitfaden für Lernende zum Setup-Assistenten im Browser - wie er startet, die fünfzehn Seiten und was eine Lernwebsite auf jeder eingeben sollte, was der Assistent schreibt, was er Ihnen über eine Installation beibringt und wie er sich von der Installation mit einem Befehl unterscheidet.
keywords: setup-assistent, installation im browser, installieren, systemtest, datenbank, site-paket, siteaccess, administrator, CheckValidity, lernwebsite, einsteiger, setup wizard, browser installer, install, system check, database, site package, study site, beginner
type: help
---
# Mit dem Setup-Assistenten installieren

Der Setup-Assistent installiert Exponential im Browser, Seite für Seite. Er ist langsamer als ein einzelner Befehl,
aber er zeigt Ihnen jede Entscheidung, die eine Installation trifft: den Systemtest, die Datenbank, die Sprachen, das
Site-Paket und die Siteaccesses. Das macht ihn zu einem guten Lehrer. Diese Seite folgt
[Exponential 6.0 installieren, Abschnitt 5](../../INSTALL.md#5-installation-mit-dem-einrichtungsassistenten) und ergänzt Hinweise für
eine Lernwebsite.

## Inhalt

1. [Wie der Assistent startet](#wie-der-assistent-startet)
2. [Die Seiten und was Sie eingeben](#die-seiten-und-was-sie-eingeben)
3. [Was Sie vom Assistenten lernen](#was-sie-vom-assistenten-lernen)
4. [Assistent oder ein Befehl](#assistent-oder-ein-befehl)
5. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)

## Wie der Assistent startet

1. Holen Sie die Dateien: `composer create-project se7enxweb/exponential exponential` und `cd exponential`
   ([Eine Lernwebsite mit einem Befehl installieren](eine-lernwebsite-mit-einem-befehl-installieren.md#die-dateien-holen)).
2. Liefern Sie die Installation aus, zum Beispiel mit `php bin/php/console exp:velocity start --engine=php`.
3. Öffnen Sie die Adresse der Website in einem Browser (`http://localhost:8087/` mit dem Standardport der Engine
   `php`).

`settings/site.ini` wird mit `[SiteAccessSettings] CheckValidity=true` ausgeliefert; solange es `true` ist, geht jede
Anfrage an den Setup-Assistenten. Der letzte Schritt schreibt `CheckValidity=false` in
`settings/override/site.ini.append.php`, und von da an wird die Website ausgeliefert
([Exponential 6.0 installieren](../../INSTALL.md#wie-er-startet)).

Während der Assistent läuft, kommt nur Ihr Browser durch; andere Besucher sehen eine Seite „Die Website wird
eingerichtet“.

## Die Seiten und was Sie eingeben

Die Tabelle fasst die Schritte der Installationsanleitung zusammen, mit einem Vorschlag für eine Lernwebsite.

| # | Seite | Für eine Lernwebsite |
|---|---|---|
| 1 | Willkommen | Weiter |
| 2 | Systemtest | Beheben Sie jedes kritische Problem, das er auflistet; zu jedem gibt es die Lösung. Übergehen Sie auf einer Lernwebsite, die Sie behalten wollen, keine Prüfungen |
| 3 | Feineinstellung des Systems | Optionale Prüfungen; lesen Sie sie, dann **Weiter** |
| 4 | Postausgang | Wählen Sie eine Einstellung, mit der Übungen keine echten E-Mails senden (siehe den Hinweis unten) |
| 5 | Datenbanksystem auswählen | Behalten Sie SQLite, das als Erstes aufgeführt und vorausgewählt ist |
| 6 | Initialisierung der Datenbank | Für SQLite nur ein Dateiname; behalten Sie die Voreinstellung |
| 7 | Sprachunterstützung | `eng-US` und eine zweite Sprache, wenn Sie [Projekt 5](projekt-05-eine-mehrsprachige-website.md) planen |
| 8 | Site Paket | Behalten Sie das vorausgewählte; siehe [Site-Pakete für Lernende](site-pakete-fuer-lernende.md) |
| 9 | Sprachoptionen | Nur bei Bedarf angezeigt; behalten Sie die Vorauswahl |
| 10 | Konfiguration der Siteaccesses | **URL-Pfad** ist auf einem einzelnen Rechner am einfachsten |
| 11 | Seitendetails | Ein Titel wie „Meine Lernwebsite“; die Zuordnungswerte von `site`, `admin` und `editor` |
| 12 | Administrator der Seite | Ein Passwort mit mindestens 10 Zeichen; eine E-Mail-Adresse unter `example.invalid` |
| 13 | Seitensicherheit | Lesen Sie die Hinweise |
| 14 | Registrierung | Nichts einzugeben; der Assistent sendet keine Registrierungs-E-Mail |
| 15 | Abgeschlossen | Notieren Sie die Adressen der öffentlichen Website, der Administration und des Editors |

Der Assistent protokolliert seinen Fortschritt in `var/log/setup.log` ([Exponential 6.0 installieren](../../INSTALL.md#die-schritte)).

> **Hinweis**
> Keine Lernübung sollte E-Mails an eine echte Person senden. Verwenden Sie für jedes Testkonto Adressen unter
> `example.invalid`, wie Trainer es tun ([Trainer: hier beginnen](../trainer/trainer-hier-beginnen.md#die-regeln-die-jeder-trainer-einhält)).
> Wenn Sie nicht sicher sind, wie E-Mails auf Ihrem Rechner verarbeitet werden, fragen Sie, bevor Sie etwas testen,
> das E-Mails sendet.

Anders als `exp:install` schreibt der Assistent keine Datei `var/log/initial-admin-password`, weil Sie das Passwort
selbst eingeben ([Erste Schritte](../../anleitungen/erste-schritte.md#8-wenn-etwas-schiefgeht)). Bewahren Sie es in
einem Passwortmanager auf.

## Was Sie vom Assistenten lernen

| Seite | Begriff, den Sie in Ihrem Tagebuch notieren | Mehr dazu |
|---|---|---|
| Systemtest | Welche PHP-Erweiterungen Exponential braucht | [Anforderungen](../../INSTALL.md#2-voraussetzungen) |
| Datenbank | SQLite braucht keinen Server; andere Datenbanken sind wählbar | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Sprachen | Inhaltssprachen werden bei der Installation gewählt und können später ergänzt werden | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| Site-Paket | Eine Website wird aus Paketen aufgebaut, nicht nur aus Code | [Site-Pakete: hier beginnen](../seitenpakete/README.md) |
| Siteaccess | Eine Installation ist auf mehreren Wegen erreichbar, jeder mit eigenen Einstellungen | [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md) |

## Assistent oder ein Befehl

| | Setup-Assistent | `exp:install` |
|---|---|---|
| Wie | Browser, fünfzehn Seiten | Ein Befehl |
| Gut für | Jede Entscheidung sehen | Schnelle, wiederholbare Neuaufbauten |
| Administratorpasswort | Sie geben es ein | Erzeugt und einmal angezeigt, oder mit `--password` übergeben |
| Dieselbe Installation wiederholen | Langsam | Eine Zeile |

Viele Lernende verwenden den Assistenten einmal, um zu verstehen, und danach `exp:install`, um schnell neu aufzubauen
([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)).

## Wenn etwas schiefgeht

- Der Systemtest nennt jedes Problem und seine Lösung; bei Dateirechten zeigt er die genauen Befehle.
- Nach der Installation stehen Fehler in `var/log/error.log`; das eigene Log des Assistenten ist `var/log/setup.log`
  ([Logs und Fehler lesen](logs-und-fehler-lesen.md)).
- Das vollständige Kapitel: [Der Setup-Assistent](../../installation/05-setup-assistent.md); Fehlerbehebung:
  [Fehlerbehebung](../../installation/12-fehlerbehebung.md).

## Verwandte Seiten

- [Ihre erste Website auf drei Wegen](ihre-erste-website-auf-drei-wegen.md)
- [Setup-Assistent und Siteaccess editor](../../funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md)
- [Ihre erste Stunde in der Administrationsoberfläche](ihre-erste-stunde-in-der-administration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
