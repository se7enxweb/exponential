---
title: "Unsere Ziele: warum es Exponential gibt und warum wir Sie einladen mitzumachen"
audience: user, editor, admin, developer, partner, supporter, advocate, community, press, businesses, individual
category: project
summary: Warum 7x und die Entwickler von Exponential diesen Code am Leben und aktuell halten, was „die Probleme sind schon gelöst“ in Funktionen bedeutet, die eine Site ohne zusätzliche Arbeit bekommt, was die Freiheiten der GPL Ihnen und anderen geben, wie 7x seinen Lebensunterhalt verdient, ohne sie wegzunehmen, ehrliche Antworten für alle, die an unseren Absichten zweifeln, und die konkreten Wege mitzumachen.
keywords: Ziele, warum, Mission, Absichten, Motive, Altruismus, freie Software, Open Source, vier Freiheiten, GPL, GFDL, 7x, Exponential Foundation, Community, mitmachen, beitragen, übersetzen, Haken, bleibt es frei, wem gehört der Code, CLA, wenn 7x aufhört, Kontinuität, Nachhaltigkeit, goals, why, mission, free software, join, contribute, catch
type: help
---
# Unsere Ziele: warum es Exponential gibt und warum wir Sie einladen mitzumachen

Diese Seite schreiben die Menschen, die Exponential machen: 7x, die Entwickler, die jeden Tag am Code arbeiten, und die
Exponential Foundation, die das Projekt betreibt. Sie sagt, warum wir diese Arbeit tun, was wir Ihnen geben wollen und
warum wir uns freuen, wenn Sie mitmachen. Sie beantwortet auch die Fragen derer, die an unseren Absichten zweifeln. Das
sind berechtigte Fragen, und die Antworten unten verweisen auf die Seiten und Dateien, in denen Sie sie selbst prüfen
können.

Jede Tatsache auf dieser Seite stammt von einer anderen Seite dieser Dokumentation, und der Link steht dabei. Wo etwas
unsere Sicht ist und keine Tatsache, sagt die Seite das.

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Warum es das Projekt gibt](#warum-es-das-projekt-gibt)
3. [„Die Probleme sind schon gelöst“](#die-probleme-sind-schon-gelöst)
4. [Warum es freie Software ist, und was Ihnen das gibt](#warum-es-freie-software-ist-und-was-ihnen-das-gibt)
5. [Wer 7x ist, und wie 7x seinen Lebensunterhalt verdient](#wer-7x-ist-und-wie-7x-seinen-lebensunterhalt-verdient)
6. [Fragen zu unseren Absichten](#fragen-zu-unseren-absichten)
7. [Wie Sie mitmachen](#wie-sie-mitmachen)
8. [Verwandte Seiten](#verwandte-seiten)

## Kurz gesagt

- **Kontinuität.** Der Code, den Sie verwenden, wird seit 1999 entwickelt, zuerst als eZ Publish, ein Produkt, das sein
  Hersteller nach fast achtzehn Jahren eingestellt hat. 7x hat ihn weitergeführt und veröffentlicht seit dem 1. Januar
  2024 die Releases 6.0.x ([Wer ist 7x?](hilfe/wer-ist-7x.md#kurz-gesagt)).
- **Probleme einmal gelöst, für alle.** Installation, Wahl der Datenbank, Übersetzung, zweistufige Anmeldung,
  Seitenlayouts, Freigaben und Upgrade-Prüfungen sind schon eingebaut, eine Site muss sie nicht noch einmal bauen.
- **Freie Software.** Der Code steht unter der GNU GPL Version 2 oder später, die Dokumentation unter der GNU FDL 1.3
  oder später. Sie dürfen beides verwenden, untersuchen, ändern und weitergeben, und jeder, dem Sie eine Kopie geben,
  darf das auch.
- **Ein Unternehmen und eine gemeinnützige Organisation.** 7x führt das Projekt und verkauft Dienstleistungen rund
  darum; die Exponential Foundation, eine gemeinnützige Organisation, betreibt es. Die Software selbst bleibt frei
  ([Wer ist 7x?, Dienstleistungen und Hosting](hilfe/wer-ist-7x.md#dienstleistungen-und-hosting)).
- **Eine offene Einladung.** Melden Sie ein Problem, übersetzen Sie, schreiben Sie Hilfe, schicken Sie einen
  Pull-Request, oder betreiben Sie eine Site und erzählen Sie anderen davon. Alle Wege stehen unter
  [Wie Sie mitmachen](#wie-sie-mitmachen).

## Warum es das Projekt gibt

### Kontinuität für die Sites und die Menschen, die davon abhängen

Organisationen haben mehr als zwanzig Jahre lang Websites, Intranets und Shops auf diesem Code gebaut. Als sein erster
Hersteller aufhörte, brauchten diese Sites weiterhin Updates. „The platform originally known as eZ Publish reached
end-of-life in 2021. We kept going“ ([Wer ist die Exponential Foundation?, Zeitleiste](hilfe/wer-ist-die-exponential-foundation.md#zeitleiste)).
Die [Geschichte von Exponential](geschichte/README.md) beginnt mit dem Monat, in dem eine ruhende Codebasis den
Besitzer wechselte, dem Dezember 2023: Damals brachten die Maintainer den Setup-Assistenten auf PHP 8 zum Laufen,
veröffentlichten die Skripte für die Datenbank-Updates und setzten die erste Beta von 6.0.0
([Dezember 2023](geschichte/2023/2023-12.md)).

7x ist in drei Stufen dorthin gekommen: die Idee 2014, der Start des Projekts 2022 und sein Launch im Dezember 2023
([Wer ist 7x?, Von Brookins Consulting zu 7x](hilfe/wer-ist-7x.md#von-brookins-consulting-zu-7x-in-drei-stufen)).
Das Unternehmen arbeitete seit den ersten Versionen mit dieser Software.

Unser erstes Ziel ist einfach: Eine Site, die auf diesem Code gebaut ist, soll weiterlaufen können, sicher und auf
aktuellem PHP, ohne dass sie auf etwas anderem neu gebaut werden muss. Die Migration einer Site aus den Jahren von
eZ Publish auf Exponential 6 ist eine der Dienstleistungen von 7x
([Dienstleistungen und Hosting](hilfe/wer-ist-7x.md#dienstleistungen-und-hosting)), und der Code dafür ist für alle frei.

### Langfristige Pflege eines freien CMS und einer DXP

Exponential ist „a professional PHP application framework with advanced CMS (content management system)
functionality“ ([Wer ist 7x?, Das Produkt](hilfe/wer-ist-7x.md#das-produkt-was-exponential-ist)). Daneben pflegt 7x
die auf Symfony beruhenden Distributionen von Exponential Platform, Exponential Basic und Exponential Velocity
([Wer ist die Exponential Foundation?, Die Produktfamilie](hilfe/wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick)).

Langfristige Pflege heißt die unscheinbare Arbeit, die Software über Jahre benutzbar hält: Jedes Release wird getaggt
und danach nie geändert, sodass „a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release,
not as a corrected copy of the old one“; Sicherheitsmeldungen bekommen „a first answer within one business day“
([Die Zusage zur Unterstützung](hilfe/wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)); und jede
Änderung seit Dezember 2023 wird in [der Geschichte](geschichte/README.md) erzählt und in
[den Änderungsverzeichnissen](geschichte/aenderungsverzeichnis/README.md) festgehalten.

### Aktuell bleiben

| Was | Stand | Quelle |
|---|---|---|
| PHP | 8.0 bis 8.5 unterstützt; die Testsuite läuft bei jedem Push und Pull-Request auf PHP 8.1 bis 8.5 | [PHP-Versionen](hilfe/wer-ist-die-exponential-foundation.md#php-versionen) |
| Sicherheit | Vertrauliche Meldungen an security@se7enx.com; Korrekturen auf `main`, veröffentlicht mit dem nächsten Release 6.0.x; eine Content Security Policy, ein manipulationssicheres Prüfprotokoll und eine Anleitung zur Härtung | [Sicherheitskorrekturen](hilfe/wer-ist-die-exponential-foundation.md#sicherheitskorrekturen), [Prüfprotokoll](funktionen/6.0/pruefprotokoll.md), [Sicherheitshärtung](hilfe/sicherheitshaertung.md) |
| Barrierefreiheit | Das Design admin4 folgt WCAG-Regeln, die Tests prüfen (Kontrast im hellen und dunklen Modus, Zielgrößen); Layouts von rechts nach links; Tastenkürzel | [Sprachen und Barrierefreiheit](hilfe/wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit), [Rechts nach links](funktionen/6.0/rechts-nach-links.md), [Tastenkürzel](hilfe/tastenkuerzel.md) |
| Der Stack drumherum | Gepflegte Forks und Patches von Zeta Components und von Teilen von Symfony, Twig und Doctrine, mit Sicherheits-Pull-Requests an die Originalprojekte | [Der PHP-Stack, auf dem es steht](hilfe/wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |

## „Die Probleme sind schon gelöst“

Unsere Sicht, in den Worten von 7x (26. Oktober 2026): Außerhalb der Exponential-Familie von CMS- und DXP-Produkten ist
die Welt schwerer zu bearbeiten. Innerhalb davon sind viele Probleme schon gelöst und in Minuten statt in Tagen
erledigt. Wir lösen die Probleme, damit Sie, wo immer es geht, nichts zusätzlich tun müssen.

Das ist eine Sicht, und Sie sollten sie an dem messen, was die Software tut. Das bekommt eine Site heute ohne
zusätzliche Arbeit, jeweils mit der Seite, die es beschreibt:

| Das Problem | Was schon da ist | Mehr dazu |
|---|---|---|
| Eine funktionierende Site bekommen | `./console exp:install` installiert ein Multisite-Paket auf SQLite und gibt die Adressen und die Anmeldung aus: eine funktionierende Exponential-Site in wenigen Minuten, ohne `kickstart.ini` und ohne Datenbankserver | [Exponential mit einem Befehl installieren](funktionen/6.0/installation-mit-einem-befehl.md) |
| Eine Datenbank wählen | Sechs unterstützte Datenbanken, in der Reihenfolge der Empfehlung: SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL; dokumentierte Werkzeuge ziehen eine Site von einer zur anderen um | [Datenbankunterstützung](hilfe/datenbankunterstuetzung.md) |
| Eine Maschine wählen | Betriebssysteme in dokumentierten Stufen, Apache, Nginx und andere, oder der mitgelieferte Server Velocity | [Betriebssystemunterstützung](hilfe/betriebssystemunterstuetzung.md) |
| Viele Sprachen | 268 Locale-Dateien, 40 davon von rechts nach links, und 71 Übersetzungen der Administration, alle vollständig | [Sprachen in Exponential](sprachen.md) |
| Hilfe für die Menschen, die damit arbeiten | Die Dokumentation in der Administration: durchsuchbar, nach Zielgruppen, mit Kontexthilfe auf den Seiten | [Das Hilfesystem](funktionen/6.0/hilfesystem.md) |
| Freigaben vor dem Veröffentlichen | Workflows, Ereignisse und Auslöser, ein Freigabeschritt mit dem Posteingang der Zusammenarbeit | [Workflows](anleitungen/workflows.md) |
| Sicherer anmelden | Zweistufige Anmeldung (TOTP und Codes per E-Mail), Passkeys und „Identität bestätigen“ vor heiklen Aktionen | [Zweistufige Anmeldung](anleitungen/zwei-faktor-anmeldung.md), [Passkeys](anleitungen/passkeys.md) |
| Seiten bauen | Exponential Layouts: Layouts, Zonen, Blöcke und Regeln, im Browser bearbeitet | [Der Layout-Editor](hilfe/layout-editor.md) |
| Wissen, ob ein Upgrade sicher ist | Setup > Upgrade-Prüfung vergleicht jede Datei und das Datenbankschema mit dem Release und liest dabei nur | [Die Upgrade-Prüfung](anleitungen/upgrade-pruefung.md) |
| Wissen, wer was geändert hat | Ein manipulationssicheres Prüfprotokoll von Anmeldungen, Inhaltsänderungen und Schreibvorgängen in den Einstellungen, standardmäßig eingeschaltet | [Prüfprotokoll](funktionen/6.0/pruefprotokoll.md) |
| Große Lösch-, Kopier- und Verschiebevorgänge | Inhaltsaufträge führen sie im Hintergrund in Stapeln aus und setzen dort fort, wo sie stehen geblieben sind, statt an einem Zeitlimit zu scheitern | [Inhaltsaufträge](funktionen/6.0/inhaltsauftraege.md) |
| Ein erster Entwurf | Exponential Intelligence schreibt Entwürfe und veröffentlicht nie selbst; gehostete Anbieter in den USA oder der EU oder ein lokales Modell | [Exponential Intelligence](funktionen/6.0/erweiterungen/expai.md) |
| Auf dem neuesten Stand bleiben | Git-Klone mit ihrer vollen Geschichte, von Hand oder automatisch aktualisiert | [Mit Git entwickeln](hilfe/mit-git-entwickeln.md), [Mit Git aktualisieren](hilfe/mit-git-aktualisieren.md) |

Nichts davon ist eine eigene Edition. Es steckt im selben freien Paket, und das Neueste davon steht im
[Changelog 6.0.15](../changelogs/6.0/6.0.15.md), solange 6.0.15 in Entwicklung ist.

## Warum es freie Software ist, und was Ihnen das gibt

Exponential steht unter der GNU General Public License, Version 2 oder jede spätere Version. „Using Exponential under
the terms of the GNU GPL is free of charge. The Professional License is no longer available“
([Wer ist 7x?, Open-Source-Lizenzen](hilfe/wer-ist-7x.md#open-source-lizenzen)). Die Dokumentation, diese Seite
eingeschlossen, steht unter der GNU Free Documentation License 1.3 oder später
([Die Lizenz dieser Dokumentation](README-LIZENZ.md)).

Wir haben uns entschieden, das so zu lassen, weil die Freiheiten diesen Code haben überleben lassen. Als sein erster
Hersteller aufhörte, ließ die Lizenz andere ihn weiterführen. Wir wollen, dass der Nächste dieselbe Chance hat.

### Die vier Freiheiten, für Sie und für andere

| Freiheit | Was sie hier bedeutet | Für andere |
|---|---|---|
| **Verwenden** | Exponential für jeden Zweck betreiben, auch für kommerzielle Arbeit, auf eigenen Servern, in Containern oder Cloud-Maschinen | Die Sites, die Sie für andere bauen, dürfen diese ohne Vertrag mit irgendwem betreiben |
| **Untersuchen** | Jede Zeile lesen: Der Code und seine Geschichte bis 2002 sind auf GitHub öffentlich | Studierende, Trainer und Prüfende können jede Behauptung prüfen, auch die auf dieser Seite |
| **Ändern** | Auf einem Branch ändern; Ihre Änderungen privat behalten, wenn Sie sie nie weitergeben | Ihre Erweiterung oder Korrektur kann das Problem eines anderen lösen, wenn Sie sie teilen |
| **Weitergeben** | Kopien verschenken oder verkaufen, unter der GPL Version 2 oder später | Wer eine Kopie erhält, bekommt dieselben vier Freiheiten |

Quellen: [Fragen zur Lizenz](hilfe/projekt/lizenz-faq.md), [Exponential sachlich vergleichen](hilfe/geschaeft/exponential-sachlich-vergleichen.md).

### Was die Freiheiten für jeden von Ihnen bedeuten

- **Site-Betreiber**: keine Lizenzgebühr, keine Bindung an einen Hoster oder einen Anbieter. Die Inhalte liegen in einer
  Standarddatenbank Ihrer Wahl, und jedes Unternehmen darf Ihre Site betreuen, nicht nur 7x
  ([Exponential sachlich vergleichen](hilfe/geschaeft/exponential-sachlich-vergleichen.md)).
- **Agenturen**: Sites und Dienstleistungen für Kunden bauen und verkaufen; die GPL erlaubt kommerzielle Arbeit.
  Erweiterungen und Designs, die Sie weitergeben, gehen unter der GPL hinaus; was Sie nie weitergeben, bleibt Ihres
  ([Fragen zur Lizenz](hilfe/projekt/lizenz-faq.md); was Sie Ihren Kunden bei der Übergabe einer Site schulden:
  [Lizenzverstöße](hilfe/projekt/lizenz-faq.md#lizenzverstöße)).
- **Redakteure**: Die Hilfe, die Sie lesen, ist freie Dokumentation; Sie dürfen sie in Ihr eigenes Schulungsmaterial
  übernehmen, übersetzen und verbessern ([Freie Dokumentation](hilfe/freie-dokumentation.md)).
- **Übersetzer**: Eine Übersetzung erreicht alle, die eine Sprache sprechen, oft Menschen, die nie ein englisches
  Handbuch lesen würden ([Übersetzungen beitragen](hilfe/gemeinschaft/mitwirken/uebersetzungen-beitragen.md)). Die
  71 vollständigen Übersetzungen der Administration sind ein greifbares Beispiel für „für andere“: Jede wurde einmal
  gemacht und ist jetzt für jede Installation da ([Sprachen in Exponential](sprachen.md)).
- **Entwickler**: Der ganze Stack besteht aus öffentlichen Git-Repositories; eine Korrektur ist einen Pull-Request
  entfernt, und der Code, den Sie hier lernen, ist schlichtes PHP ([Mit Git entwickeln](hilfe/mit-git-entwickeln.md)).

## Wer 7x ist, und wie 7x seinen Lebensunterhalt verdient

7x ist „a responsive North American web design, support, development and website hosting company“; es hieß Brookins
Consulting, bis es den Namen 7x annahm, und „took over leadership of the project and it's development, support,
adoption and community growth in 2023“ ([Wer ist 7x?](hilfe/wer-ist-7x.md#kurz-gesagt)). Die Exponential Foundation
ist eine eigene Organisation, „a 501(c)(3) Non-Profit Corporation founded in Seattle, Washington, USA“, gegründet am 19. März
2026, die das Projekt betreibt und finanziert
([Wer ist die Exponential Foundation?](hilfe/wer-ist-die-exponential-foundation.md#kurz-gesagt)).

Was die Dokumentation über Geld sagt:

- **7x verkauft Dienstleistungen rund um die Open-Source-Software; die Software selbst bleibt frei.** Die
  Dienstleistungen sind Webdesign und Entwicklung, E-Commerce und Integration von Zahlungsanbietern, Schulungen,
  Migrationen von Sites aus den Jahren von eZ Publish auf Exponential 6, Support- und Wartungsverträge und verwaltetes Hosting
  ([Dienstleistungen und Hosting](hilfe/wer-ist-7x.md#dienstleistungen-und-hosting)).
- **Das Projekt wird durch Spenden von Zeit und Mitteln finanziert.** „The Exponential Open Source Project is funded by
  donations of time and resources by 7x (and Exponential Sponsors)“; wer 7x sponsert, finanziert „the company that
  keeps the lights on“, wer die Foundation sponsert, „the open-source project itself“
  ([Die Foundation und 7x](hilfe/wer-ist-die-exponential-foundation.md#die-foundation-und-7x)).
- **Partner** tragen finanziell und mit Code bei und arbeiten direkt mit 7x und der Foundation
  ([Partner: hier beginnen](hilfe/partner/partner-hier-beginnen.md)).

Was die Dokumentation nicht sagt: das Budget der beiden Organisationen und wer im Vorstand der Foundation sitzt
([Was die Quellen nicht sagen](hilfe/wer-ist-die-exponential-foundation.md#was-die-quellen-nicht-sagen)). Wir raten
hier nicht.

Keiner dieser Wege, Geld zu verdienen, nimmt eine Freiheit weg. Sie müssen nichts von 7x kaufen, um Exponential zu
verwenden, zu ändern oder weiterzugeben, und niemand braucht die Erlaubnis von 7x, um dieselben Dienstleistungen
anzubieten.

## Fragen zu unseren Absichten

### Wo ist der Haken?

Es gibt keine Lizenzgebühr und keine bezahlte Edition: „The Professional License is no longer available“. Die Kosten,
die Sie haben, sind Ihre eigenen: Ihr Server, Ihre Zeit und die Dienstleistungen, die Sie kaufen wollen, von 7x oder
von jemand anderem. Die üblichen Risiken einer Bindung an einen Anbieter stehen einzeln, mit dem Grund, warum sie nicht
zutreffen, auf [Exponential sachlich vergleichen](hilfe/geschaeft/exponential-sachlich-vergleichen.md).

Eine Bedingung sollten Sie kennen, bevor Sie Code zum Kernel beitragen: 7x und die Foundation verlangen ein
unterschriebenes Contributor Licensing Agreement, und mit Unterschrift und Einreichung übertragen Sie das Urheberrecht
an diesem Beitrag auf 7x, das ihn dann verwenden, ändern, neu lizenzieren und verbreiten darf, ohne Sie zu fragen oder
zu bezahlen. Ihre eigenen Erweiterungen sind nicht betroffen
([Copyright assignment (CLA)](../../CONTRIBUTING.md#copyright-assignment-cla),
[Das CLA erklärt](hilfe/gemeinschaft/mitwirken/das-cla-erklaert.md)). Wir sagen das deutlich, weil Sie mit allen
Fakten entscheiden sollen.

### Bleibt es frei?

Die Lizenz des Codes ist die GPL Version 2 oder später, die der Dokumentation die GNU FDL 1.3 oder später; die Seiten
der Community führen beide als festgelegt ([Wer entscheidet](hilfe/gemeinschaft/so-funktioniert-es/wer-entscheidet.md)).
Jedes Release wird unter dieser Lizenz veröffentlicht, und eine veröffentlichte Version wird danach nie geändert
([Veröffentlichte Versionen ändern sich nie](hilfe/presse/veroeffentlichte-versionen-aendern-sich-nie.md)). Eine
Kopie, die Sie unter der GPL erhalten haben, kommt mit den Freiheiten der GPL, und sie ist mit ihrer vollen Geschichte
auf GitHub und Packagist öffentlich.

Wir versprechen nicht mehr, als die Dokumente sagen. Wir zeigen Ihnen, woran Sie sich halten können: die Lizenz, die
öffentliche Geschichte und den Code selbst.

### Wem gehört der Code?

Der Copyright-Vermerk des Kernels lautet „Copyright (C) 1998 - 2026 7x & Exponential Foundation“, neben „Copyright (C)
1999 - 2014 eZ Systems AS“, und der Code trägt die Arbeit von Hunderten von Mitwirkenden über die Jahre
([Wer ist 7x?, 7x und die Exponential Foundation](hilfe/wer-ist-7x.md#7x-und-die-exponential-foundation),
[Mitwirkende und Freunde des Projekts](hilfe/wer-ist-7x.md#mitwirkende-und-freunde-des-projekts)). Neue
Code-Beiträge zum Kernel werden über das oben beschriebene CLA auf 7x übertragen. In einem Satz: 7x führt das Projekt,
7x und die Foundation halten das Urheberrecht, und die Lizenz ist die GPL
([Häufige Fehler in Berichten](hilfe/presse/haeufige-fehler-in-berichten.md)).

Die Erweiterungen, Designs und Sites, die Sie schreiben, gehören Ihnen.

### Was passiert, wenn 7x aufhört?

Die Antwort ist dieselbe, die dieser Code schon einmal erlebt hat. Der Code, seine Geschichte bis 2002, die
Dokumentation und jedes Paket sind öffentlich: auf GitHub unter se7enxweb und auf Packagist. Die Lizenz erlaubt
jedem, den Code zu behalten und zu pflegen ([Aussagen, die Sie nicht machen dürfen](hilfe/fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md)),
und die Dokumentation erlaubt jedem, sie zu kopieren und fortzuführen. Die Foundation besteht als eigene gemeinnützige
Organisation, damit das Projekt nicht nur die Arbeit eines Unternehmens ist
([Was die Foundation ist](hilfe/wer-ist-die-exponential-foundation.md#was-die-foundation-ist)).

Wir sagen nicht „es wird nie verschwinden“. Wir sagen: Ihnen werden nie die Mittel fehlen, es weiterzuführen.

### Ist das nicht einfach Werbung für 7x?

Messen Sie es an dem, was Sie prüfen können. Die Hilfeseiten nennen für jede Tatsache eine Quelle, führen auf, was die
Quellen nicht sagen, und sagen Journalisten und Fürsprechern, welche Behauptungen sie nicht aufstellen sollen: keine
Nutzerzahlen, keine Geschwindigkeitsvergleiche, die nicht veröffentlicht wurden, keine versprochenen Termine
([Was die Quellen nicht sagen](hilfe/presse/was-die-quellen-nicht-sagen.md),
[Eine faire Rezension schreiben](hilfe/presse/eine-faire-rezension-schreiben.md)). Exponential ist nicht mit
eZ Systems oder Ibexa verbunden
([Nicht verbunden mit eZ Systems oder Ibexa](hilfe/presse/nicht-verbunden-mit-ez-systems-oder-ibexa.md)).

### Warum sollte ich beitragen, statt es nur zu verwenden?

Verwenden genügt, und Sie schulden dafür niemandem etwas: Sie müssen den Code Ihrer Site nicht teilen, und eine
Erweiterung, die Sie in Ihrer Organisation behalten, muss nicht veröffentlicht werden
([Fragen zur Lizenz](hilfe/projekt/lizenz-faq.md)).

Beitragen lohnt sich aus praktischen Gründen. Eine Korrektur, die Sie zurückschicken, erreicht mit dem nächsten Release
jede Installation, und Sie tragen sie nicht mehr allein durch jedes Upgrade. Eine geteilte Erweiterung bekommt
Rückmeldungen, Fehlerberichte und Verbesserungen zurück ([Erweiterungen zurückgeben](hilfe/erweiterungen-zurueckgeben.md)).
Jeder Pull-Request wird übernommen, sobald sein Autor ihn so überarbeitet hat, wie die Reviews es verlangen
([Thema: Jeder Pull-Request wird übernommen](hilfe/presse/thema-jeder-pull-request-wird-uebernommen.md)). Und der
Nächste mit Ihrem Problem findet es schon gelöst.

### Wer entscheidet, was als Nächstes geschieht?

Die Foundation betreibt das Projekt, 7x führt die Entwicklung, Maintainer prüfen und taggen Releases, und
Pull-Requests, Issues, die Foren und Tests sind die Wege, auf denen Mitglieder der Community Entscheidungen
beeinflussen. Eine dokumentierte Abstimmung oder einen Rat gibt es nicht
([Wer entscheidet](hilfe/gemeinschaft/so-funktioniert-es/wer-entscheidet.md)).

## Wie Sie mitmachen

| Weg | Erster Schritt |
|---|---|
| Ein Problem melden | [Wenn etwas schiefgeht](hilfe/wenn-etwas-schiefgeht.md), [Einen Fehler melden](hilfe/gemeinschaft/mitwirken/einen-fehler-melden.md) |
| Fragen stellen und beantworten | Die [Foren](https://share.exponential.earth/forums) und der [Telegram-Chat](https://t.me/exponentialcms); [Unterstützung erhalten](hilfe/projekt/unterstuetzung-erhalten.md) |
| Übersetzen | [Übersetzungen beitragen](hilfe/gemeinschaft/mitwirken/uebersetzungen-beitragen.md), [Sprachen in Exponential](sprachen.md) |
| Die Hilfe schreiben oder verbessern | Jede Hilfeseite hat „Auf GitHub bearbeiten“; [Dokumentation und Hilfeseiten beitragen](hilfe/gemeinschaft/mitwirken/dokumentation-und-hilfeseiten-beitragen.md) |
| Einen Pull-Request schicken | [Mit Git entwickeln](hilfe/mit-git-entwickeln.md), [Code beitragen](hilfe/gemeinschaft/mitwirken/code-beitragen.md), [CONTRIBUTING.md](../../CONTRIBUTING.md) |
| Eine Erweiterung oder ein Theme teilen | [Erweiterungen zurückgeben](hilfe/erweiterungen-zurueckgeben.md), [Erweiterungen und Themes teilen](hilfe/gemeinschaft/mitwirken/erweiterungen-und-themes-teilen.md) |
| Eine Site betreiben und anderen davon erzählen | [Fürsprecher: hier beginnen](hilfe/fuersprecher/fuersprecher-hier-beginnen.md) |
| Testen, was kommt | [Releases testen](hilfe/gemeinschaft/mitwirken/releases-testen.md) |
| Die Arbeit finanzieren | [Sponsoren: hier beginnen](hilfe/sponsoren/sponsoren-hier-beginnen.md), [Partner: hier beginnen](hilfe/partner/partner-hier-beginnen.md) |

Den Überblick über alle Wege zu helfen gibt [Zum Projekt beitragen](hilfe/projekt/zum-projekt-beitragen.md), und die
eigene Startseite der Community ist [Community: hier beginnen](hilfe/gemeinschaft/community-hier-beginnen.md).

## Verwandte Seiten

- [Wer ist 7x?](hilfe/wer-ist-7x.md) und [Wer ist die Exponential Foundation?](hilfe/wer-ist-die-exponential-foundation.md)
- [Die Geschichte von Exponential](geschichte/README.md)
- [Das Exponential-Projekt](hilfe/projekt/README.md) und seine [FAQ](hilfe/projekt/faq.md)
- [Faktenblatt für Journalisten](hilfe/presse/faktenblatt-fuer-journalisten.md)
- [Freie Dokumentation](hilfe/freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.
