---
title: "Die Leiter: Benutzer, Builder, Erweiterungsentwickler, Theme-Entwickler"
audience: user, developer, designer, app-creator
category: extension-development
order: 20
summary: Vier Stufen vom Benutzen von Exponential bis zum Erweitern - Benutzer, Builder, Erweiterungsentwickler, Theme-Entwickler -, was jede tut, was jede wissen muss und welcher Teil dieses Buchs sie vermittelt.
keywords: Leiter, Laufbahn, Lernpfad, Benutzer, Builder, Site-Builder, Erweiterungsentwickler, Theme-Entwickler, Designer, Anwendungsentwickler, Fähigkeiten, ladder, career, learning path, user, site builder, extension developer, theme developer, designer, application creator, skills
type: help
---
# Die Leiter: Benutzer, Builder, Erweiterungsentwickler, Theme-Entwickler

Teil 1, Kapitel 2. Menschen wachsen mit Exponential in kleinen, sicheren Schritten in die Entwicklung hinein. Dieses
Kapitel benennt die Stufen, sagt, was Sie auf jeder tun und wissen müssen, und verweist auf den Teil des Buchs, der sie
vermittelt. Finden Sie Ihren Platz auf der Leiter und lesen Sie dann nach oben weiter.

## Die vier Stufen

| Stufe | Sie ändern | Sie brauchen | Teil des Buchs |
|---|---|---|---|
| **Benutzer** | Inhalte: Artikel, Bilder, Menüs, Benutzer | die Administration | Teil 1 (dieser Teil) |
| **Builder** | Einstellungen, Templates, Overrides, Inhaltsklassen, Rollen, Workflows | einen Texteditor, die INI- und die Template-Sprache, Geduld | [Teil 2](../teil-2-builder/README.md) |
| **Erweiterungsentwickler** | Neue Module, Datentypen, Operatoren, Workflow-Ereignisse, Cronjobs, Befehle | PHP, die Klassen des Kernels, Git | [Teil 3](../teil-3-erweiterungsentwickler/README.md), [Teil 4](../teil-4-aktualisieren/README.md) |
| **Theme-Entwickler** | Das Aussehen: eine Design-Erweiterung mit Templates, CSS, JavaScript | HTML, CSS, die Template-Sprache, die Design-Kette | [Teil 6](../teil-6-theme-entwickler/README.md) |

Zwei weitere Rollen stehen neben der Leiter statt auf ihr:

- **Maintainer**: Sie veröffentlichen Ihre Erweiterung, damit andere sie installieren, und Sie kümmern sich um sie. Das
  Veröffentlichen ist [Teil 7](../teil-7-veroeffentlichen/README.md); zur Arbeit anderer beizutragen und die Community wachsen
  zu lassen, ist [Teil 5](../teil-5-erweitern-forken-zurueckgeben/README.md).
- **Anwendungsentwickler**: Sie kombinieren Inhaltsklassen, Erweiterungen und Einstellungen zu einer vollständigen
  Anwendung (ein Mitgliederbereich, ein Shop, ein Buchungssystem). Sie nutzen alle vier Stufen, vor allem die des
  Builders und die des Erweiterungsentwicklers.

## Stufe 1: Benutzer

Die Administration kennen Sie bereits. Was Ihnen beim Aufstieg hilft:

- Wissen Sie, **wo Dinge konfiguriert werden**. Jede Seite der Administration wird aus Templates gezeichnet und von
  Einstellungen gesteuert. Wenn Sie sich fragen „Warum tut es das?“, ist die Antwort fast immer eine Einstellung oder
  ein Template.
- Wissen Sie, **was installiert ist**.
  [Die auf Ihrer Website installierten Erweiterungen finden](die-erweiterungen-ihrer-website-finden.md) zeigt Ihnen, wie.
- Lernen Sie, **ein Log zu lesen**. Wenn etwas schiefgeht, nennt das Fehlerlog die Ursache weit öfter, als man erwartet.
  Siehe [Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md).

Was Sie schon ohne Code tun können: Inhaltsklassen, Sektionen, Rollen und Richtlinien, Workflows, URL-Aliase und
Übersetzungen anlegen. Jedes davon ist auch ein Werkzeug des Builders.

## Stufe 2: Builder

Ein Builder ändert, wie sich die Website verhält und aussieht, **ohne PHP zu schreiben**:

- **Einstellungen** (Dateien `.ini`): welche Erweiterungen laufen, welches Design eine Website verwendet, wie Bilder
  skaliert werden, welche Sprachen es gibt, was gecacht wird.
- **Templates** (Dateien `.tpl`): das HTML jeder Seite, geschrieben in der Template-Sprache mit Variablen, Schleifen,
  Bedingungen, Operatoren und Fetch-Funktionen.
- **Overrides**: eine Regel, die sagt: „Für Artikel in dieser Sektion verwende *dieses* Template statt des
  Standard-Templates.“

Auch der Builder arbeitet in einer Erweiterung: Eine Einstellungs-Erweiterung und eine Design-Erweiterung halten jede
Änderung in einem Ordner unter Versionskontrolle, statt sie über `settings/override/` zu verstreuen. Teil 2 vermittelt
all das.

> **Hinweis**
> Die meisten Websites brauchen nichts über die Werkzeuge des Builders hinaus. Schreiben Sie kein PHP, solange eine
> Einstellung, ein Template oder ein Override die Aufgabe wirklich erledigen kann.
> [Wenn Einstellungen nicht ausreichen](../teil-2-builder/wenn-einstellungen-nicht-reichen.md) hilft Ihnen bei der
> Entscheidung.

## Stufe 3: Erweiterungsentwickler

Sie schreiben PHP, das der Kernel aufruft:

- ein **Modul** mit Views (eine Seite mit eigener Adresse, zum Beispiel `/report/list`),
- einen **Template-Operator** oder eine **Fetch-Funktion**, die Templates aufrufen können,
- einen **Datentyp** (eine neue Art von Feld),
- ein **Workflow-Ereignis** (etwas, das geschieht, wenn Inhalte veröffentlicht werden),
- einen **Cronjob-Teil**, einen **Befehl**, eine **Ajax-Funktion**, einen **Handler**, der einen Teil des Systems
  ersetzt.

Sie lernen den Aufbau einer Erweiterung und die Klassen des Kernels (`eZINI`, `eZContentObject`, `eZTemplate` und die
übrigen, siehe [Teil 8](../teil-8-referenz/README.md)). Die RAD-Werkzeuge unter **Setup > RAD** schreiben Ihnen für
die meisten davon ein funktionierendes Grundgerüst, sodass Sie mit Code beginnen, der läuft; die Tutorials in Teil 3
zeigen sowohl den Weg mit RAD als auch den Weg von Hand.

## Stufe 4: Theme-Entwickler

Sie bauen das Aussehen einer Website als **Design-Erweiterung**: Templates, Stylesheets, Skripte, Bilder und die
Override-Regeln, die sie verwenden. Sie arbeiten mit der Design-Kette, dem Stylesheet-Packer, den Tokens für den Dark
Mode und der Content Security Policy, die Inline-Skripte verbietet. Teil 6 behandelt, was an Themes besonders ist; der
Bereich für Designer in der Hilfe geht noch viel tiefer auf Theme-Design und Templates ein.

## Wie lange dauert jede Stufe?

Das hängt von Ihnen ab, und dieses Buch verspricht keine Stundenzahlen. Was es verspricht: Jede Stufe baut nur auf der
darunter auf, jedes Kapitel endet mit etwas, das Sie auf Ihrer eigenen Installation prüfen können, und nichts verlangt
von Ihnen, etwas zu lernen, das Sie noch nicht brauchen.

## Eine sinnvolle Reihenfolge

1. Lesen Sie Teil 1 vollständig und machen Sie [Ihre erste Stunde als Entwickler](ihre-erste-stunde-als-entwickler.md).
2. Bauen Sie mit Teil 2 etwas Echtes: eine Einstellungs-Erweiterung für Ihre eigene Website.
3. Machen Sie die ersten drei Tutorials in Teil 3: die
   [kleinste Erweiterung von Hand](../teil-3-erweiterungsentwickler/tutorials/von-hand-minimale-erweiterung.md), einen
   [Template-Operator](../teil-3-erweiterungsentwickler/tutorials/von-hand-template-operator.md) und ein
   [Modul mit einer View](../teil-3-erweiterungsentwickler/tutorials/von-hand-modul-mit-view.md).
4. Nehmen Sie eine alte Erweiterung, die Ihnen gefällt, und bringen Sie sie mit Teil 4 auf den neuesten Stand. Nichts
   bringt Ihnen den Kernel schneller bei.
5. Veröffentlichen Sie sie (Teil 7) und bieten Sie Ihre Änderungen ihrem Autor an (Teil 5).

[Lesepfade durch das Buch](../lesepfade.md) nennt fertige Reihenfolgen für Entwickler, Designer und
Anwendungsentwickler.

## Verwandte Seiten

- Zurück: [Was eine Erweiterung ist](was-eine-erweiterung-ist.md)
- Weiter: [Die Werkzeuge, die Sie brauchen](werkzeuge-die-sie-brauchen.md)
- [Mit Git entwickeln: vom Benutzer zum Entwickler wachsen](../../mit-git-entwickeln.md)
- [Lesepfade durch das Buch](../lesepfade.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
