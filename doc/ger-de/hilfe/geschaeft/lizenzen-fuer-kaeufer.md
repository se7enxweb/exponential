---
title: "Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation"
audience: sales-marketing, project-manager
category: business
order: 110
summary: Was ein Käufer mit Exponential unter der GNU GPL v2 oder höher und mit seiner Dokumentation unter der GNU FDL 1.3 tun darf, in einfachen Worten; was die Lizenzen im Gegenzug verlangen, wo andere Lizenzen gelten und wie Sie all das im Produkt prüfen. Keine Rechtsberatung.
keywords: Lizenz, Lizenzierung, Urheberrecht, Käufer, Beschaffung, Einkauf, freie Software, Gewährleistung, Erweiterungen, Drittanbieter, Herstellerbindung, license, licensing, GPL, GNU General Public License, GPL-2.0-or-later, GFDL, GNU Free Documentation License, copyright, buyer, procurement, open source, free software, warranty, MIT, extensions, third-party, vendor lock-in
type: help
---
# Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation

Diese Seite richtet sich an Vertrieb und Marketing, die Lizenzfragen von Käufern beantworten, und an Projektleiter,
die die Lizenz dem Einkauf, der Rechtsabteilung oder der Geschäftsführung erklären müssen. Sie beantwortet: Unter
welchen Lizenzen steht Exponential, was darf ein Käufer damit tun, was verlangen die Lizenzen im Gegenzug, und wo kann
der Käufer das prüfen?

> **Warnung**
> Diese Seite ist eine Zusammenfassung in einfachen Worten, keine Rechtsberatung. Maßgeblich sind die Lizenztexte: die
> GNU General Public License Version 2 in `LICENSE.md` (und `LICENSE`) des Repositorys und die GNU Free Documentation
> License 1.3 in [doc/eng-us/LICENSE-GFDL-1.3.txt](../../../eng-us/LICENSE-GFDL-1.3.txt). Ein Käufer mit konkreten Vorhaben (ein
> auf Exponential aufbauendes Produkt verkaufen, es an Dritte weitergeben, es mit proprietärem Code kombinieren) sollte
> seinen eigenen Rechtsberater fragen.

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Die Lizenzen auf einen Blick](#die-lizenzen-auf-einen-blick)
3. [Die Software: GNU GPL v2 oder höher](#die-software-gnu-gpl-v2-oder-höher)
4. [Was ein Käufer mit der Software tun darf](#was-ein-käufer-mit-der-software-tun-darf)
5. [Was die GPL im Gegenzug verlangt](#was-die-gpl-im-gegenzug-verlangt)
6. [Eine Website zu betreiben ist keine Weitergabe](#eine-website-zu-betreiben-ist-keine-weitergabe)
7. [Die Dokumentation: GNU FDL 1.3 oder höher](#die-dokumentation-gnu-fdl-13-oder-höher)
8. [Erweiterungen, verwandte Produkte und Teile von Drittanbietern](#erweiterungen-verwandte-produkte-und-teile-von-drittanbietern)
9. [Copyright-Inhaber](#copyright-inhaber)
10. [Keine Lizenzgebühren, und wofür Käufer tatsächlich bezahlen](#keine-lizenzgebühren-und-wofür-käufer-tatsächlich-bezahlen)
11. [Gewährleistung](#gewährleistung)
12. [Wo Sie es im Produkt prüfen](#wo-sie-es-im-produkt-prüfen)
13. [FAQ für Käufer](#faq-für-käufer)
14. [Eine Lizenzübersicht für den Einkauf](#eine-lizenzübersicht-für-den-einkauf)

## Kurz gesagt

- **Die Software** ist freie Software unter der **GNU General Public License, Version 2 oder jeder späteren Version**
  (`COPYRIGHT.md`, `composer.json`: `"license": "GPL-2.0-or-later"`).
- **Die Nutzung kostet keine Lizenzgebühr**: „Using Exponential under the terms of the GNU GPL is free of charge. The
  Professional License is no longer available.“ (README, „License“) - die Nutzung unter der GNU GPL ist kostenlos, und
  die Professional License gibt es nicht mehr.
- **Die Dokumentation**, einschließlich dieser Seite, steht unter der **GNU Free Documentation License 1.3 oder jeder
  späteren Version** ([Die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
- **Käufer dürfen** die Software nutzen, untersuchen, ändern, selbst hosten und weitergeben, unter den Bedingungen
  der Lizenz.
- **Wenn sie sie weitergeben** (Kopien verbreiten, geändert oder nicht), erhalten die Empfänger dieselben Freiheiten
  und den Quellcode.

## Die Lizenzen auf einen Blick

| Was | Lizenz | Quelle |
|---|---|---|
| Exponential 6 (der Kernel) | GNU GPL v2 oder höher (`GPL-2.0-or-later`) | `COPYRIGHT.md`, `LICENSE.md`, `composer.json` |
| Beiträge zum Kernel | Dieselbe: „any modifications made to the project are also released under the same GPLv2 or later license“ | `CONTRIBUTING.md` |
| Die Dokumentation in `doc/` (6.x) | GNU FDL 1.3 oder höher, ohne unveränderliche Abschnitte, ohne Vorderseitentexte, ohne Rückseitentexte | [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md) |
| Ältere, von eZ Systems übernommene Dokumentation | Die Bedingungen, mit denen sie kam, die GPL v2 des Repositorys | [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md#ältere-seiten-mit-eigenen-bedingungen) |
| Exponential Velocity | MIT | seine `composer.json` ([Wer ist 7x?](../wer-ist-7x.md#open-source-lizenzen)) |
| Exponential Platform DXP (Metapaket) | „GPL-2.0-or-later or proprietary“ | seine `composer.json` ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)) |
| Exponential Basic | GNU GPLv2+ | Beschreibung des Repositorys ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-basic)) |
| Jede Erweiterung | Angegeben in ihrer eigenen `ezinfo.php` und `extension.xml`, angezeigt unter Info > Über | [Die Seiten „Über“ und „Copyright“](../../funktionen/6.0/info-und-paketseiten.md) |

## Die Software: GNU GPL v2 oder höher

Die GPL ist eine Lizenz für freie Software, verfasst von der Free Software Foundation. „Frei“ bezieht sich auf
Freiheit, nicht auf den Preis: „When we speak of free software, we are referring to freedom, not price“ (Präambel von
`LICENSE.md`).

„Oder jede spätere Version“ bedeutet, dass der Empfänger den Bedingungen der Version 2 oder jeder späteren Version
folgen darf, die die Free Software Foundation veröffentlicht (`COPYRIGHT.md`). `CONTRIBUTING.md` erklärt den Grund:
„The 'or later' clause allows the project to upgrade to future versions of the GPL“ - die Klausel erlaubt dem Projekt,
auf künftige Versionen der GPL zu wechseln.

Die README stellt fest, dass die frühere Doppellizenz nicht mehr besteht: „You can no longer choose between the GNU
GPLv2-or-later and the eZ Systems' eZ Publish Professional Licenses ... This effectively makes Exponential
GNU GPLv2-or-later ONLY.“ Exponential steht damit ausschließlich unter der GNU GPLv2 oder höher.

## Was ein Käufer mit der Software tun darf

Sinngemäß nach `LICENSE.md`; die Abschnittsnummern sind die der Lizenz.

| Ein Käufer darf | Grundlage in der Lizenz |
|---|---|
| Sie für jeden Zweck ausführen, auf so vielen Servern und Sites, wie er möchte | Abschnitt 0: „The act of running the Program is not restricted“ |
| Den vollständigen Quellcode lesen und untersuchen | Der gesamte Code ist veröffentlicht; Abschnitt 3 definiert Quellcode als „the preferred form of the work for making modifications to it“, die bevorzugte Form für Änderungen |
| Sie für seine eigenen Bedürfnisse ändern | Abschnitt 2 |
| Sie selbst hosten oder von einem beliebigen Anbieter hosten lassen | Das Ausführen ist nicht eingeschränkt (Abschnitt 0); keine Klausel bindet die Software an einen Hoster |
| Ein beliebiges Unternehmen oder einen beliebigen Entwickler beauftragen, daran zu arbeiten | Die Rechte stehen jedem Lizenznehmer zu; nichts schreibt einen bestimmten Lieferanten vor |
| Unveränderte Kopien anfertigen und weitergeben | Abschnitt 1 |
| Geänderte Kopien weitergeben | Abschnitt 2, mit seinen Bedingungen |
| Für Kopien Geld verlangen und gegen Gebühr Gewährleistung anbieten | Abschnitt 1: „You may charge a fee for the physical act of transferring a copy, and you may at your option offer warranty protection in exchange for a fee“ |

Das ist der Kern des Arguments „keine Herstellerbindung“: Die Lizenz selbst gibt dem Käufer das Recht, mit dem Code zu
einem anderen Lieferanten zu gehen. Zusammen mit eigenem Hosting auf vielen Betriebssystemen und Datenbanken
([Datenbankunterstützung](../datenbankunterstuetzung.md), [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md)) und der
vollständigen Git-Historie ([Mit Git entwickeln](../mit-git-entwickeln.md)) behält der Käufer die Kontrolle.

## Was die GPL im Gegenzug verlangt

Die Bedingungen gelten, wenn jemand die Software **weitergibt** (Kopien an andere verschenkt oder verkauft), geändert
oder nicht.

| Bedingung | In einfachen Worten | Grundlage in der Lizenz |
|---|---|---|
| Vermerke erhalten | Copyright-Vermerke, der Lizenzhinweis und der Gewährleistungsausschluss bleiben unverändert, und die Empfänger erhalten eine Kopie der Lizenz | Abschnitt 1 |
| Änderungen kennzeichnen | Geänderte Dateien tragen „prominent notices stating that you changed the files and the date of any change“, also deutliche Hinweise auf die Änderung und ihr Datum | Abschnitt 2a |
| Dieselbe Lizenz für das Ganze | Ein weitergegebenes Werk, das Exponential enthält oder davon abgeleitet ist, wird als Ganzes unter der GPL lizenziert, für Dritte kostenlos | Abschnitt 2b |
| Den Quellcode bereitstellen | Weitergegebene Kopien kommen mit dem vollständigen Quellcode oder einem schriftlichen Angebot dafür | Abschnitt 3 |
| Keine zusätzlichen Einschränkungen | Die Empfänger erhalten dieselben Rechte; wer gegen die Lizenz verstößt, verliert seine Rechte daraus | Abschnitte 4 und 6 |

Unabhängige Werke, die nur neben Exponential gespeichert oder ausgeliefert werden („mere aggregation“, bloße
Zusammenstellung), fallen allein dadurch nicht unter die GPL (Abschnitt 2). Ob ein bestimmtes Stück Code „a work based
on the Program“ ist, ein auf dem Programm basierendes Werk, ist eine Rechtsfrage; geben Sie sie an einen Rechtsberater
weiter.

## Eine Website zu betreiben ist keine Weitergabe

Die meisten Käufer betreiben eine Website. Abschnitt 0 der Lizenz sagt: „Activities other than copying, distribution
and modification are not covered by this License ... The act of running the Program is not restricted“. Andere
Tätigkeiten als Kopieren, Weitergeben und Ändern sind von der Lizenz nicht erfasst. Exponential auf Ihrem eigenen
Server auszuführen, um Ihre Site auszuliefern, ist Ausführen.

Was das in der Praxis bedeutet, als Fragen, die Sie mit Ihrem Berater klären sollten:

1. Geben wir Kopien der Software (oder unserer geänderten Version) an jemanden außerhalb unserer Organisation weiter?
   Wenn nicht, lösen wir mit unserer Nutzung die Bedingungen für die Weitergabe nicht aus.
2. Baut eine Agentur die Site und übergibt uns den Code? Dann gibt die Agentur ihn an uns weiter, und wir erhalten die
   Rechte der GPL, einschließlich des Quellcodes.
3. Verkaufen oder geben wir ein auf Exponential aufbauendes Produkt an unsere eigenen Kunden? Dann geben wir es
   weiter, und die obigen Bedingungen gelten für uns.
4. Kombinieren wir Exponential mit Code, den wir proprietär halten wollen? Fragen Sie einen Rechtsberater, bevor Sie
   beginnen.

> **Hinweis**
> Die GPL v2 enthält keine Klausel zu Software, die über ein Netzwerk genutzt wird. Die Lizenzliste des Paketsystems
> bietet für Ihre eigenen Pakete auch die Lizenzen `AGPL-3.0` an, die eine solche Klausel haben ([Paketlizenzen](../../funktionen/6.0/paketlizenzen-und-versionen.md));
> Exponential selbst steht nicht unter der AGPL.

## Die Dokumentation: GNU FDL 1.3 oder höher

Die Dokumentation in `doc/`, einschließlich aller Hilfeseiten, ist freie Dokumentation. Die vollständige Erklärung
steht unter [Freie Dokumentation](../freie-dokumentation.md); für Käufer ist das Wesentliche:

| Ein Käufer darf | Bedingungen |
|---|---|
| Sie für jeden Zweck lesen und studieren, auch für Schulungen | Keine |
| Sie kopieren und teilen, auf Papier oder als Dateien, kostenlos oder gegen Gebühr | Die Copyright-Vermerke, den Lizenzhinweis und eine Kopie der Lizenz beibehalten; keine Bedingungen oder Kopierschutz hinzufügen |
| Sie übersetzen | Eine Übersetzung ist eine geänderte Fassung; den englischen Originaltext der Lizenz beifügen |
| Sie für das eigene Team oder die eigene Site anpassen und die angepasste Fassung teilen | Dieselbe Lizenz, die Vermerke beibehalten, angeben, dass und von wem sie geändert wurde, ihr einen eigenen Titel geben, sofern der Herausgeber nicht zustimmt |
| Sie neben eigene Dokumente stellen | Die Lizenz erstreckt sich nicht auf unabhängige Werke |

Für große Auflagen oder mehr als 100 Kopien in einem schwer bearbeitbaren Format gelten zusätzliche Bedingungen
(Abschnitt 3 der FDL; siehe [Freie Dokumentation](../freie-dokumentation.md#kopieren-und-an-andere-benutzer-weitergeben)).

Für einen Käufer bedeutet das: Die Hilfe ist kein geschlossenes Handbuch. Das Handbuch für seine Redakteure kann von
diesen Seiten ausgehen, übersetzt und angepasst, solange es freie Dokumentation bleibt.

## Erweiterungen, verwandte Produkte und Teile von Drittanbietern

- Jede **Erweiterung** gibt ihre Lizenz in `ezinfo.php` und `extension.xml` an; die Seite „Über“ führt alle in einer
  sortierbaren Tabelle auf, mit einer Tabelle **Software von Drittanbietern** der enthaltenen Komponenten, ihrer
  Versionen und Lizenzen ([Die Seiten „Über“ und „Copyright“](../../funktionen/6.0/info-und-paketseiten.md)).
- **Pakete**, die in der Administration erstellt werden, wählen ihre Lizenz aus einer Liste, die GPL, LGPL, AGPL, MIT,
  GFDL, Creative Commons, Gemeinfreiheit und eine proprietäre Option enthält; der Standard ist `GPL-2.0-or-later`
  ([Paketlizenzen und Semantic Versioning](../../funktionen/6.0/paketlizenzen-und-versionen.md)).
- **Exponential Velocity** steht unter der MIT-Lizenz, einer freizügigen Lizenz.
- **Exponential Platform DXP** gibt in seiner `composer.json` „GPL-2.0-or-later or proprietary“ an. Die Quellen
  erklären die proprietäre Option nicht; braucht ein Käufer sie, muss er 7x fragen.

Bevor Sie einem Käufer sagen, dass „alles GPL ist“, prüfen Sie die Seite „Über“ der Installation, die er betreiben
wird.

## Copyright-Inhaber

| Vermerk | Wo |
|---|---|
| „Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ | `COPYRIGHT.md` |
| „Copyright (C) 1999 - 2014 eZ Systems AS. All rights reserved.“ | `COPYRIGHT.md` |
| „Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ | die Dokumentation ([Die Lizenz dieser Dokumentation](../../README-LIZENZ.md)) |

„All rights reserved“ in einem Vermerk hebt die Erlaubnisse der GPL nicht auf; die Lizenz gewährt sie zusätzlich zum
Copyright. Übernehmen Sie diese Vermerke genau, wenn Sie sie zitieren.

## Keine Lizenzgebühren, und wofür Käufer tatsächlich bezahlen

- Die Lizenz kostet nichts: „Using Exponential under the terms of the GNU GPL is free of charge“ (README).
- Käufer bezahlen für **Arbeit und Dienstleistungen**, wenn sie sie wünschen: Design, Entwicklung, Migration,
  Support, Hosting, Schulung. 7x bietet diese an; „Prices and terms are on the 7x website, not here“, Preise und
  Bedingungen stehen auf der Website von 7x ([Wer ist 7x?](../wer-ist-7x.md#dienstleistungen-und-hosting)). Jedes andere
  Unternehmen darf sie ebenfalls anbieten.
- Ein Sponsoring von 7x oder der Foundation ist freiwillig ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#unterstützen-oder-mitmachen)).

## Gewährleistung

Die GPL kommt ohne Gewährleistung. Die Abschnitte 11 und 12 von `LICENSE.md` („NO WARRANTY“) schließen Gewährleistung
und Haftung aus, soweit das Gesetz es erlaubt, und die Seite „Copyright“ der Administration zeigt den
Gewährleistungsausschluss ([Die Seiten „Über“ und „Copyright“](../../funktionen/6.0/info-und-paketseiten.md#die-seite-copyright-lesen)).
Ein Käufer, der eine Gewährleistung, eine garantierte Reaktionszeit oder eine Haftungsdeckung braucht, erhält sie über
einen Vertrag mit einem Lieferanten; Abschnitt 1 der GPL erlaubt ausdrücklich, „warranty protection in exchange for a
fee“ anzubieten, also Gewährleistung gegen Gebühr. Die einzige Zusage zur Reaktionszeit in den eigenen Dateien des
Projekts steht in `SECURITY.md`: eine erste Antwort auf eine vertrauliche Sicherheitsmeldung „within one business
day“, innerhalb eines Werktags.

## Wo Sie es im Produkt prüfen

| Was | Wo |
|---|---|
| Die Lizenz und das Copyright der Installation | **Info > Copyright** (`/ezinfo/copyright`) |
| Erweiterungen und Software von Drittanbietern mit ihren Lizenzen | **Info > Über** (`/ezinfo/about`) |
| Die Lizenz der Dokumentation | Der Hinweis am Ende jeder Hilfeseite und ihres PDFs; [Freie Dokumentation](../freie-dokumentation.md) |
| Die Lizenzdateien | `LICENSE.md`, `LICENSE`, `COPYRIGHT.md` im Wurzelverzeichnis der Installation; `doc/eng-us/LICENSE-GFDL-1.3.txt` |
| Die angegebene Lizenz des Pakets | `composer.json` (`"license": "GPL-2.0-or-later"`) und [Packagist](https://packagist.org/packages/se7enxweb/exponential) |

## FAQ für Käufer

| Frage | Antwort | Quelle |
|---|---|---|
| Zahlen wir eine Lizenzgebühr? | Nein. | README, „License“ |
| Können wir noch eine kommerzielle Lizenz kaufen? | Nein: „The Professional License is no longer available.“ | README |
| Dürfen wir den Code ändern? | Ja. | `LICENSE.md` Abschnitt 2 |
| Müssen wir unsere Änderungen veröffentlichen? | Nicht deshalb, weil Sie sie auf Ihrer eigenen Site nutzen. Wenn Sie die geänderte Software weitergeben, erhalten die Empfänger den Quellcode unter der GPL. | `LICENSE.md` Abschnitte 0, 2, 3 |
| Können wir den Lieferanten wechseln? | Ja; die Lizenz gibt jedem Lizenznehmer die Rechte. | `LICENSE.md` |
| Können wir es überall hosten? | Ja, das Ausführen ist nicht eingeschränkt. | `LICENSE.md` Abschnitt 0 |
| Gibt es eine Gewährleistung? | Nicht aus der Lizenz; holen Sie sie sich per Vertrag. | `LICENSE.md` Abschnitte 1, 11, 12 |
| Dürfen wir die Hilfe in unser Handbuch übernehmen? | Ja, unter den Bedingungen der GNU FDL 1.3. | [Freie Dokumentation](../freie-dokumentation.md) |
| Wer hält das Copyright? | Exponential Foundation and 7x, für 1999 bis 2014 zusammen mit eZ Systems AS. | `COPYRIGHT.md` |
| Bleibt ein Beitrag von uns unserer? | Sie bleiben Urheber; Sie veröffentlichen den Beitrag unter der GPL v2 oder höher. | `CONTRIBUTING.md` |
| Steht jeder Teil unter der GPL? | Der Kernel ja; prüfen Sie Erweiterungen und verwandte Produkte unter Info > Über. Velocity steht unter MIT. | [Die Seiten „Über“ und „Copyright“](../../funktionen/6.0/info-und-paketseiten.md) |

## Eine Lizenzübersicht für den Einkauf

```text
LICENSE SUMMARY: Exponential                      Prepared: <date>   For: <buyer>

Software           Exponential <6.0.x>, package se7enxweb/exponential
License            GNU General Public License v2 or any later version (GPL-2.0-or-later)
License fee        None ("Using Exponential under the terms of the GNU GPL is free of charge")
Commercial license Not available ("The Professional License is no longer available")
Copyright          Exponential Copyright © 1998-2026 Exponential Foundation and 7x; eZ Systems AS (1999-2014)
Documentation      GNU Free Documentation License 1.3 or later
Extensions in use  <list from Info > About, with each license>
Other components   <list from Info > About, Third-party software>
Warranty           None under the license; by contract with: <supplier, if any>
Security reports   Private, as described in SECURITY.md
Notes              This summary is not legal advice. License texts: LICENSE.md, doc/eng-us/LICENSE-GFDL-1.3.txt
```

## Verwandte Seiten

- [Vertrieb und Marketing: hier beginnen](vertrieb-und-marketing-hier-beginnen.md)
- [Einwandbehandlung: Sicherheit, Langlebigkeit und Support](einwandbehandlung.md)
- [Exponential sachlich vergleichen](exponential-sachlich-vergleichen.md)
- [Marken- und Namensregeln](marken-und-namensregeln.md)
- [Risikomanagement für Exponential-Projekte](../fuehrung/risikomanagement.md)
- [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md), [Die Seiten „Über“ und „Copyright“](../../funktionen/6.0/info-und-paketseiten.md)
- Die Lizenztexte im Web: [GNU GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html), [GNU FDL 1.3](https://www.gnu.org/licenses/fdl-1.3.html)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](../urheberrecht-und-copyleft.md).
