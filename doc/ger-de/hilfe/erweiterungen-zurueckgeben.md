---
title: "Erweiterungen zurückgeben: Ihre Arbeit mit allen teilen"
audience: developer, designer, partner, user
category: development
order: 7
summary: Warum und wie Sie die Erweiterungen, die Sie bauen, an das Exponential-Projekt zurückgeben, damit alle sie nutzen können; was die GNU GPL wirklich verlangt, wenn Sie eine Erweiterung verbreiten (und was sie für eine Site, die Sie nur selbst betreiben, nicht verlangt); und wie Sie eine Erweiterung Schritt für Schritt veröffentlichen, mit einem GitHub-Repository, composer.json, Packagist, ezinfo.php und extension.xml, einer Lizenzdatei und einer README, angeboten der Organisation se7enxweb, projects.exponential.earth und share.exponential.earth.
keywords: mitwirken, zurückgeben, teilen, Erweiterung, veröffentlichen, Release, Lizenz, Quellcode, Verbreitung, private Nutzung, interne Nutzung, Kunde, Agentur, Open Source, Community, contribute, contribute back, give back, share, extension, publish, release, GPL, GPL-2.0-or-later, license, source code, distribution, private use, internal use, customer, agency, GitHub, composer.json, Packagist, ezinfo.php, extension.xml, README, LICENSE, se7enxweb, projects.exponential.earth, share.exponential.earth, open source, community
featured: true
type: help
---
# Erweiterungen zurückgeben: Ihre Arbeit mit allen teilen

Fast jede Exponential-Site hat irgendwann eine eigene Erweiterung: einen Datentyp, einen Operator, eine Seite in der
Administration, ein Workflow-Ereignis, ein Theme. Viele davon würden auch anderen Sites helfen. Diese Seite erklärt,
warum es sich lohnt, sie zu teilen, was die Lizenz von Exponential von Ihnen verlangt, wenn Sie eine Erweiterung an
jemand anderen weitergeben, und wie Sie eine Erweiterung so veröffentlichen, dass jeder sie mit einem einzigen Befehl
installieren kann.

Diese Seite erklärt die Lizenz in einfachen Worten. Sie ist **keine Rechtsberatung**: Für eine echte Entscheidung in
einem kommerziellen Projekt lesen Sie den Lizenztext und fragen Sie eine qualifizierte Person.

## Warum Sie eine Erweiterung zurückgeben sollten

- **Alle bekommen sie.** Eine Erweiterung auf GitHub und Packagist kann jede Site mit `composer require` installieren.
  Je mehr Erweiterungen es gibt, desto mehr kann jede Site ohne neuen Code.
- **Andere pflegen sie mit Ihnen.** Benutzer melden Fehler, schicken Korrekturen und halten sie mit neuen Releases von
  PHP und Exponential lauffähig. Eine private Erweiterung pflegen Sie allein, für immer.
- **So ist Exponential selbst gewachsen.** Die meisten Erweiterungen in der Organisation se7enxweb sind Fortführungen
  von Arbeit, die ihre Autoren einst geteilt haben; das Projekt hält sie lauffähig, weil ihr Quellcode veröffentlicht
  wurde.
- **Sie zeigt, was Sie können.** Eine öffentliche Erweiterung mit Ihrem Namen in der Copyright-Zeile ist ein Portfolio
  für einen Entwickler, einen Designer oder ein Partnerunternehmen.

## Was die GPL verlangt, und wann

Exponential ist freie Software unter der **GNU General Public License, Version 2 oder (nach Ihrer Wahl) jeder späteren
Version** (`COPYRIGHT.md` und `LICENSE.md` im Wurzelverzeichnis der Installation; `composer.json` nennt
`GPL-2.0-or-later`). Fast jede Erweiterung verwendet dieselbe Lizenz: Von den 79 Erweiterungen, die im Oktober 2026
auf der Referenz-Site des Projekts installiert waren, nennen 75 in `composer.json` GPL 2 oder später
(`GPL-2.0-or-later` oder `GPL-2.0+`), drei GPL 2 (`GPL-2.0-only` oder die ältere Kennung `GPL-2.0`) und eine `GPL-3.0-or-later`. Lesen Sie immer die Lizenz der Erweiterung, die Sie vor
sich haben ([Lizenzen lesen, bevor Sie etwas wiederverwenden](../../eng-us/help/extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)).

Bei der GPL geht es um das **Kopieren, Ändern und Verbreiten** des Codes. Was sie von Ihnen verlangt, hängt davon ab,
was Sie mit Ihrer Erweiterung tun.

### Selbst nutzen: keine Pflicht zur Veröffentlichung

| Was Sie tun | Verlangt die GPL, dass Sie den Quellcode herausgeben? |
|---|---|
| Sie schreiben oder ändern eine Erweiterung und betreiben sie auf Ihrer eigenen Site | **Nein.** Die GPL sagt, dass das Ausführen des Programms nicht eingeschränkt ist (GPL 2, Abschnitt 0). |
| Ihre Site ist öffentlich im Internet, und Besucher nutzen sie | **Nein.** Besucher erhalten Webseiten, keine Kopie der Erweiterung. GPL 2 und GPL 3 haben keine Klausel über die Nutzung über ein Netzwerk (diese Klausel steht in einer anderen Lizenz, der GNU Affero GPL, die Exponential nicht verwendet). |
| Sie nutzen eine geänderte Erweiterung nur innerhalb Ihres Unternehmens oder Ihrer Organisation | **Nein.** Die [GPL-FAQ](https://www.gnu.org/licenses/gpl-faq.html#InternalDistribution) der Free Software Foundation sagt, dass die Nutzung von Kopien innerhalb einer Organisation keine Verbreitung ist. |

Eine Site, die nur privat oder intern betrieben wird und deren Code nie an jemand anderen weitergegeben wird, löst die
Verbreitungsklausel also nicht aus. Sie dürfen diese Erweiterung für sich behalten. Diese Seite hofft trotzdem, dass
Sie sie teilen, aber die Lizenz zwingt Sie nicht dazu.

### Verbreiten: der Quellcode geht mit

Sie **verbreiten** eine Erweiterung, wenn Sie jemand anderem eine Kopie geben: Sie veröffentlichen sie, Sie verkaufen
sie, oder Sie geben sie einem anderen Unternehmen oder einer anderen Person zur eigenen Nutzung. Ab diesem Moment gilt
die GPL vollständig:

- **Der Quellcode kommt mit.** GPL 2, Abschnitt 3, verlangt, dass jeder, der das Programm erhält, seinen vollständigen
  Quellcode bekommen kann: Sie geben den Quellcode mit oder ein schriftliches Angebot, ihn bereitzustellen. Eine
  Exponential-Erweiterung besteht aus PHP, Templates und Einstellungen, die übergebenen Dateien *sind* also
  normalerweise der Quellcode; achten Sie darauf, dass nichts fehlt (komprimiertes oder generiertes JavaScript und CSS
  braucht die Dateien, aus denen es gebaut wurde).
- **Unter derselben Lizenz.** Eine geänderte Version wird als Ganzes ebenfalls unter der GPL verbreitet (GPL 2,
  Abschnitt 2). Sie behalten jeden Copyright- und Lizenzhinweis bei, der bereits in den Dateien steht.
- **Keine zusätzlichen Einschränkungen.** Sie dürfen für die Kopie, für die Installation und für Support Geld
  verlangen, aber Sie dürfen dem Empfänger nicht verbieten, den Code zu studieren, zu ändern oder weiterzugeben (GPL 2,
  Abschnitt 6).
- **Gegenüber denen, die sie erhalten.** Die Pflicht besteht gegenüber den Personen, an die Sie verbreiten, nicht
  gegenüber der ganzen Welt. Wer Ihre Erweiterung erhält, bekommt ihren Quellcode und die Rechte der GPL und darf sie
  seinerseits weitergeben; Sie müssen sie nicht für alle auf GitHub veröffentlichen.

**Agenturen und Freiberufler.** Die eigene [Lizenz-FAQ](https://exponential.earth/faq/licensing) des Projekts vertritt
die Auffassung, dass eine Agentur oder ein Freiberufler, die den Code einer Site im Auftrag eines Kunden
zusammenstellen, als Beauftragte des Kunden handeln und den Code nicht im Sinne der GPL verbreiten. Dem Kunden gehört
das Ergebnis, und er entscheidet, ob er es verbreitet. Dieselbe Erweiterung an andere Kunden zu verkaufen oder zu geben
oder sie zu veröffentlichen, ist eine Verbreitung.

Eine Erweiterung läuft im PHP-Prozess des Exponential-Kernels und ruft dessen Klassen auf. Code, der so eng mit einem
GPL-Programm verbunden ist, fällt nach Auffassung der Free Software Foundation
([GPL-FAQ: Plug-ins](https://www.gnu.org/licenses/gpl-faq.html#GPLPlugins)) unter die GPL, wenn er zusammen mit ihm
verbreitet wird. Die [Lizenz-FAQ](https://exponential.earth/faq/licensing) des Projekts sagt dasselbe mit eigenen
Worten: Module und Designs sind abgeleitete Werke von Exponential, wenn Sie sie also verbreiten, tun Sie das unter der
GPL Version 2 oder später, während Sie nie verpflichtet sind, sie überhaupt zu verbreiten. Bilder und andere Dateien,
die Ihr PHP-Code nur an den Browser schickt, sind Daten und fallen nicht unter diese Regel, es sei denn, Sie haben sie
aus den GPL-Dateien von Exponential selbst erstellt. Deshalb verwenden die meisten Erweiterungen GPL 2 oder später.

Die Lizenz-FAQ beantwortet auch verwandte Fragen, die diese Seite nicht wiederholt: GPL-kompatible Bibliotheken in
einer Erweiterung, „Brücken“-Erweiterungen, die mit anderen Systemen kommunizieren, den Verkauf von Erweiterungen und
was „Quellcode“ bei PHP, JavaScript, CSS und Bildern bedeutet. Lesen Sie sie, bevor Sie sich für eine Lizenz
entscheiden.

### Wählen Sie für Ihre Erweiterung GPL 2 oder später

Schreiben Sie „GNU General Public License v2.0 (or any later version)“, sofern Sie keinen Grund haben, eine andere
GPL-kompatible Lizenz zu wählen. So bleibt Ihre Erweiterung mit dem Kernel und mit fast jeder anderen Erweiterung
kombinierbar, auch mit den wenigen unter GPL 3 ([Lizenzen lesen, bevor Sie etwas wiederverwenden](../../eng-us/help/extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md#combining-licenses-a-quick-table)).
Enthält Ihre Erweiterung Code von jemand anderem, behalten Sie dessen Hinweise und Lizenz bei; haben Sie sie für einen
Kunden gebaut, stellen Sie sicher, dass Sie sie veröffentlichen dürfen (siehe
[Eine Erweiterung als Partner veröffentlichen](../../eng-us/help/partners/publishing-an-extension.md)).

## Eine Erweiterung Schritt für Schritt veröffentlichen

Der Entwicklerleitfaden [Erweiterungen](../anleitungen/extensions.md) erklärt, wie Sie eine Erweiterung bauen; sein Abschnitt
[Veröffentlichen](../anleitungen/extensions.md#7-die-extension-veröffentlichen) nennt die genauen Befehle. Hier ist der ganze Weg von einem
Ordner auf Ihrer Site bis zu einem Paket, das jeder installieren kann.

### 1. Aufräumen

- Entfernen Sie alles, was zu einer einzelnen Site gehört: Kundennamen, Adressen, Inhalte, Passwörter, API-Schlüssel und
  andere Geheimnisse. Machen Sie aus sitespezifischem Verhalten eine Einstellung mit neutralem Standardwert.
- Behalten Sie den üblichen Aufbau einer Erweiterung bei: `settings/`, `design/`, `classes/`, `modules/`,
  `translations/`, `autoloads/` nach Bedarf
  ([Was eine Erweiterung ist](../../eng-us/help/extension-development/part-1-from-user-to-developer/what-an-extension-is.md)).

### 2. Die Lizenzdatei hinzufügen

- Eine Datei `LICENSE` mit dem vollständigen Lizenztext, zum Beispiel dem Text der GPL 2 von
  [gnu.org](https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt).
- Eine Copyright-Zeile, die Sie oder Ihr Unternehmen nennt, sowie jeden früheren Autor, dessen Arbeit sie enthält.
- Einen kurzen Lizenzkopf in jeder PHP-Datei (`@copyright` und `@license`), wie ihn die Dateien des Kernels haben.

### 3. ezinfo.php und extension.xml schreiben

Die Info-Seite der Administration (**Setup > Über**) und die Upgrade-Prüfungen lesen diese beiden Dateien. Die
Konvention des Projekts für jedes Release:

- `ezinfo.php`: eine Klasse `<extension>Info` mit einer statischen Methode `info()`, die `'Name'`, `'Version'` (die
  Release-Nummer, ohne `v`: Tag `v1.0.0` ergibt `"1.0.0"`), `'Copyright'`, `'License'` und `'Info_url'` (die
  Webadresse des Repositorys) zurückgibt.
- `extension.xml`: ein Block `<metadata>` mit derselben `<version>`, `<license>` und `<info_url>`. Lassen Sie darin nie
  einen Platzhalter wie `//autogentag//` oder `dev-master` stehen.
- Beide Dateien müssen dieselbe Nummer zeigen. Prüfen Sie sie:

  ```bash
  php -l extension/myextension/ezinfo.php
  xmllint --noout extension/myextension/extension.xml
  ```

### 4. composer.json schreiben

Composer installiert die Erweiterung über den Installer, den der Kernel bereits verwendet, nach `extension/<name>/`:

```json
{
    "name": "yourvendor/myextension",
    "description": "What it does, in one sentence.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "authors": [ { "name": "Your Name" } ],
    "require": { "se7enxweb/ezpublish-legacy-installer": "*" },
    "extra": { "ezpublish-legacy-extension-name": "myextension" }
}
```

Übernehmen Sie `type`, den Installer und den Schlüssel `extra` von einer Erweiterung, die bereits auf Ihrer Site
installiert ist (`extension/<name>/composer.json`), damit Ihre zu dem passt, was Ihr Kernel erwartet. Die `license` ist
eine [SPDX-Kennung](https://spdx.org/licenses/).

### 5. Die README schreiben

Eine gute README beantwortet in dieser Reihenfolge: was die Erweiterung tut (zwei Sätze); die Voraussetzungen
(Versionen von Exponential und PHP, andere Erweiterungen); wie man sie installiert (`composer require
yourvendor/myextension` oder ein Git-Klon); wie man sie einschaltet (**Setup > Extensions** oder `ActiveExtensions[]`);
wie man sie konfiguriert; wo man Issues meldet; die Lizenz und die Danksagungen.

### 6. Auf GitHub stellen

```bash
cd extension/myextension
git init
git add .
git commit -m "Added: First public version of myextension"
git remote add origin https://github.com/<you>/myextension.git
git push -u origin main
```

Aktivieren Sie **Issues** im Repository, damit Benutzer Fehler dort melden können, wo Sie sie sehen.

### 7. Eine Version veröffentlichen und auf Packagist registrieren

1. Wählen Sie die Nummer anhand der bereits vorhandenen Tags, nach Version sortiert, und schreiben Sie sie in
   `ezinfo.php`, `extension.xml` (und in `composer.json`, falls es ein Feld `version` hat). Committen Sie das und
   taggen Sie **danach** diesen Commit:

   ```bash
   git tag -l 'v*' --sort=version:refname | tail -5
   git commit -am "Updated: Version 1.0.0"
   git tag -a v1.0.0 -m "Version 1.0.0"
   git push origin main v1.0.0
   ```

2. Melden Sie sich bei [packagist.org](https://packagist.org/) an und reichen Sie die Adresse des Repositorys ein.
   Lassen Sie Packagist seinen GitHub-Webhook installieren, damit jedes neue Tag erscheint, ohne dass Sie etwas tun
   müssen.
3. Ein veröffentlichtes Tag ist dauerhaft. Ist ein Release fehlerhaft, verschieben oder löschen Sie sein Tag nie:
   Veröffentlichen Sie die nächste Version mit der Korrektur und schreiben Sie in ihre Release-Notes, was falsch war.
   Packagist liest jedes Tag nur einmal, ein verschobenes Tag würde also zwei verschiedenen Paketen dieselbe
   Versionsnummer geben.

Jetzt kann jeder sie mit `composer require yourvendor/myextension` installieren.

### 8. Der Community anbieten

- **Die Organisation se7enxweb.** Die meisten Erweiterungen des Exponential-Ökosystems liegen unter
  [github.com/se7enxweb](https://github.com/se7enxweb), wo sie mit jedem neuen Release lauffähig gehalten werden.
  Möchten Sie, dass das Projekt Ihre Erweiterung übernimmt, oder haben Sie eine der dort bereits vorhandenen
  Erweiterungen verbessert, öffnen Sie ein Issue oder einen Pull-Request im betreffenden Repository, oder für eine neue
  Erweiterung in [se7enxweb/exponential](https://github.com/se7enxweb/exponential/issues), und sagen Sie, was sie tut
  und unter welcher Lizenz sie steht. Ein Repository kann an die Organisation übertragen oder in sie geforkt werden,
  wobei Ihre History und Ihr Copyright erhalten bleiben. Code, der in die eigenen Repositories des Projekts eingeht,
  muss unter derselben Lizenz stehen wie Exponential, GPL Version 2 oder später (nicht „nur GPL 2“ oder „nur GPL 3“),
  wie die [Lizenz-FAQ](https://exponential.earth/faq/licensing) erklärt; Mitwirkende behalten ihr Copyright.
- **Exponential Projects**, [projects.exponential.earth](https://projects.exponential.earth/): die Projektplattform,
  die Erweiterungen mit ihren Neuigkeiten, Downloads und Links zum Quellcode auflistet
  ([Exponential Projects](../../eng-us/help/extension-development/part-1-from-user-to-developer/projects-exponential-earth.md)).
  Geben Sie Ihrer Erweiterung dort eine Projektseite, die auf ihr Repository verweist.
- **Share Exponential**, [share.exponential.earth](https://share.exponential.earth/): die Community-Site mit den Foren.
  Kündigen Sie die Erweiterung in den [Foren](https://share.exponential.earth/forums) an, suchen Sie Tester und
  beantworten Sie Fragen
  ([Share Exponential](../../eng-us/help/extension-development/part-1-from-user-to-developer/share-exponential-earth.md)).

### 9. Am Leben erhalten

Testen Sie sie mit jedem neuen Release von Exponential, beantworten Sie Issues, mergen Sie gute Pull-Requests und
schreiben Sie in die README, wenn Sie die Pflege einstellen, damit jemand anderes sie übernehmen kann.

## Die Erweiterung eines anderen verbessern

Zurückgeben heißt auch, Ihre Korrekturen an eine Erweiterung zu schicken, die Sie nicht geschrieben haben, statt eine
geänderte Kopie zu behalten. Forken Sie sie, korrigieren Sie sie auf einem Branch und öffnen Sie einen Pull-Request,
genau wie es [Mit Git entwickeln: Eine Änderung per Pull-Request beitragen](mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen)
zeigt. Antwortet der ursprüngliche Autor nicht mehr, forken Sie sie respektvoll: Behalten Sie die History, das
Copyright und die Lizenz bei und nennen Sie den Autor in der README
([Erweiterungen an das Upstream-Projekt zurückgeben](../../eng-us/help/partners/contributing-extensions-back.md)).

## Verwandte Seiten

- [Mit Git entwickeln](mit-git-entwickeln.md) und [Mit Git aktualisieren](mit-git-aktualisieren.md): Arbeiten Sie mit Klonen,
  damit das Zurückschicken einer Änderung ein einziger Pull-Request ist.
- [Das Buch zur Entwicklung von Erweiterungen](../../eng-us/help/extension-development/README.md): vom Benutzer zum Entwickler von
  Erweiterungen und Themes, Schritt für Schritt.
- [Erweiterungen](../anleitungen/extensions.md): eine Erweiterung bauen, konfigurieren und veröffentlichen.
- [Lizenzen lesen, bevor Sie etwas wiederverwenden](../../eng-us/help/extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Eine Erweiterung als Partner veröffentlichen](../../eng-us/help/partners/publishing-an-extension.md)
- [Ihr Site-Paket beitragen](../../eng-us/help/site-packages/contribute-your-site-package.md): eine ganze Site (Design, Klassen, Demo-Inhalte) über den Einrichtungsassistenten teilen.
- Die vollständigen Lizenztexte: [GNU GPL Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) und die
  [GPL-FAQ](https://www.gnu.org/licenses/gpl-faq.html).
- [Freie Dokumentation](freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
