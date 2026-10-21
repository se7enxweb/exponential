---
title: "Lektion 1.1: Was ein Siteaccess ist"
audience: trainer, admin, developer
category: training
order: 11
summary: Die Teilnehmenden erklären, was ein Siteaccess ist, finden die Siteaccesses einer Installation in ihren Einstellungsdateien und sagen, welche Datei gewinnt, wenn mehrere denselben Wert setzen. 45 Minuten.
keywords: multisite und seitenpakete, siteaccess, ladereihenfolge, einstellungsdateien, multisite and site packages, AvailableSiteAccessList, RelatedSiteAccessList, DefaultAccess, settings/siteaccess, settings/override, load order, exp:ini where
type: help
---
# Lektion 1.1: Was ein Siteaccess ist

Alles in diesem Kurs beruht auf einer Idee: Eine Exponential-Installation wird über **Siteaccesses** betreten. Ein
Siteaccess ist ein Name, ein Verzeichnis mit Einstellungen und eine Regel, die entscheidet, welche Anfragen er
beantwortet. Mehrere öffentliche Websites, die Administration und der Editor sind alle Siteaccesses derselben
Installation. Diese Lektion macht die Idee greifbar, bevor eine Website hinzugefügt wird.

> **Hinweis**
> Jede Übung dieses Kurses läuft auf einer Schulungsinstallation, niemals auf einer Live-Website. Die Teilnehmenden
> ändern Einstellungsdateien und leeren Caches; auf einer Live-Website würde das ändern, was Besucher sehen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 1: Siteaccesses für mehrere Websites](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler, die mehrere Websites auf einer Installation betreiben werden |
| Voraussetzungen | Eine eigene Schulungsinstallation mit Shell-Zugang (installiert mit `php bin/php/console exp:install`); Grundkenntnisse im Terminal |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Teile eines Siteaccess nennen: seinen Namen, sein Einstellungsverzeichnis und seine Zuordnungsregel;
- die Siteaccesses einer Installation aus `settings/siteaccess/` und aus `AvailableSiteAccessList[]` auflisten;
- die Ladereihenfolge der Einstellungsdateien erklären und sagen, welche Datei bei einem einfachen Wert gewinnt;
- mit `exp:ini where` jede Datei sehen, die einen Wert setzt, und den wirksamen Wert;
- `RelatedSiteAccessList[]` von `AvailableSiteAccessList[]` unterscheiden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site),
[Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md) und
[Einstellungen über die Kommandozeile ändern: exp:ini](../../../../../eng-us/bc/6.0/console-exp-ini.md).

1. **Die Definition** (5 Minuten). Schreiben Sie an die Tafel: *Siteaccess = Name + Einstellungen in
   `settings/siteaccess/<name>/` + eine Regel, welche Anfragen er beantwortet.* Jede Installation hat mindestens einen
   öffentlichen Siteaccess und die Administration; `exp:install` und der Kickstarter legen außerdem den Editor an. Die
   Verzeichnisse heißen immer `site`, `admin` und `editor` (und `adminui`, wenn die Erweiterung `exp_adminui`
   vorhanden ist); die URL-Pfade, Hosts oder Ports, die zu ihnen führen, werden bei der Installation gewählt.
2. **Wo die Liste steht** (5 Minuten). Zeigen Sie `settings/override/site.ini.append.php`, Block
   `[SiteAccessSettings]`: `AvailableSiteAccessList[]` listet die Siteaccesses, die antworten dürfen,
   `RelatedSiteAccessList[]` die, die dieselbe Datenbank teilen (der Kommentar des Kernels in `settings/site.ini`
   sagt genau das), und `MatchOrder` die Reihenfolge der Regeln (`uri;host;port` im Auslieferungszustand). `[SiteSettings]
   DefaultAccess` ist der Siteaccess, der antwortet, wenn keine Regel gegriffen hat.
3. **Die Ladereihenfolge** (6 Minuten). Zeichnen Sie vier Kästen von unten nach oben: die Kernel-Voreinstellungen in
   `settings/*.ini`, die Einstellungen aktiver Erweiterungen, `settings/siteaccess/<name>/` und `settings/override/`,
   das das letzte Wort hat. Ein einfacher Wert nimmt die letzte Datei, die ihn setzt; Arrays addieren sich, sofern
   eine Datei sie nicht mit einer leeren Zeile `Name[]` zurücksetzt. Modul 3 ergänzt dieses Bild um die
   Override-Verzeichnisse pro Website in Website-Erweiterungen.
4. **Live ansehen** (4 Minuten). Führen Sie `exp:ini where` am Beamer aus (Schritt 5 der Übung) und lesen Sie die
   nummerierte Liste laut vor: Sie ist die Ladereihenfolge, ausgegeben für eine Einstellung.

### Häufige Fragen

**Ist ein Siteaccess dasselbe wie eine Website?** Nein. Eine Website ist das, was Besucher sehen; sie kann mehrere
Siteaccesses haben, zum Beispiel einen pro Sprache. Die Administration und der Editor sind ebenfalls Siteaccesses, und
sie sind keine Websites.

**Haben Siteaccesses eine eigene Datenbank?** Normalerweise nicht. Die Siteaccesses aus `RelatedSiteAccessList[]`
teilen eine Datenbank und einen Inhaltsbaum. Getrennte Datenbanken bedeuten getrennte Installationen, die Lektion 1.4
vergleicht.

**Warum ist `admin` der `DefaultAccess` in `settings/site.ini`?** Das ist die Voreinstellung des Kernels für eine
leere URL; eine Installation schreibt ihren eigenen Wert in das Override. Lesen Sie immer den wirksamen Wert, nicht
die Datei mit den Voreinstellungen.

**Darf ich `settings/site.ini` bearbeiten?** Nein: Sie wird bei jedem Upgrade ersetzt. Einstellungen gehören nach
`settings/override/`, in ein Siteaccess-Verzeichnis oder in eine Erweiterung. `exp:ini` verweigert den Scope
`default`, sofern nicht `--allow-default` angegeben ist.

### Stolperfallen

- **Die falsche Datei lesen.** Die Teilnehmenden öffnen `settings/site.ini`, finden `MatchOrder=uri;host;port` und
  schließen daraus, dass die Website das verwendet. Lassen Sie sie `exp:ini get` oder `exp:ini where` ausführen, die
  den wirksamen Wert zeigen.
- **Als root arbeiten.** Auf einem gemeinsamen Schulungsserver brauchen `exp:ini` und andere Skripte
  `--allow-root-user`, wenn sie als root laufen; verwenden Sie lieber den Benutzer, der die Website ausliefert.
- **Velocity.** Läuft die Schulungswebsite auf Exponential Velocity, erreicht eine Einstellungsänderung sie erst nach
  `php bin/php/console exp:velocity restart`. Diese Lektion liest nur Einstellungen, deshalb spielt das hier noch
  keine Rolle.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, installiert mit `php bin/php/console exp:install` wie in
[7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md). Arbeiten Sie in ihrem
Wurzelverzeichnis (dem, das `index.php` enthält).

1. Listen Sie die Siteaccess-Verzeichnisse auf:

   ```bash
   ls settings/siteaccess/
   ```

2. Lesen Sie die Siteaccesses, die antworten dürfen, und die, die die Datenbank teilen:

   ```bash
   php bin/php/console exp:ini get 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]'
   php bin/php/console exp:ini get 'site.ini/SiteAccessSettings/RelatedSiteAccessList[]'
   ```

3. Vergleichen Sie die beiden Listen mit den Verzeichnissen aus Schritt 1. Notieren Sie jedes Verzeichnis, das nicht
   in der ersten Liste steht.
4. Lesen Sie die Zuordnungsregeln und die Voreinstellung:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   php bin/php/console exp:ini get site.ini/SiteSettings/DefaultAccess
   ```

5. Sehen Sie sich die Ladereihenfolge für den Namen der Website an, für den öffentlichen Siteaccess:

   ```bash
   php bin/php/console exp:ini where site.ini/SiteSettings/SiteName site
   ```

6. Führen Sie denselben Befehl für die Administration aus (`... SiteName admin`). Notieren Sie jeweils, welche Datei
   den Wert setzt.
7. Geben Sie jeden Scope aus, den die Installation hat:

   ```bash
   php bin/php/console exp:ini scopes
   ```

8. Melden Sie sich in der Administration Ihrer Schulungswebsite an und öffnen Sie **Setup** > **Systeminformationen**.
   Suchen Sie die Karte **Siteaccesses** und vergleichen Sie ihre Anzahl, ihre Voreinstellung und ihre Zeile zur
   Zuordnung mit den Schritten 2 und 4.
9. Schreiben Sie in je einem Satz auf, wofür die Siteaccesses `site`, `admin` und `editor` da sind.

## Erwartetes Ergebnis

- Schritt 1 zeigt mindestens `admin`, `editor` und `site` (und `adminui`, wenn `exp_adminui` vorhanden ist).
- Schritt 2 listet dieselben Namen; Schritt 4 zeigt die `MatchOrder`, die der Installer geschrieben hat (`uri` bei
  einem `exp:install` mit Voreinstellungen), und den Standard-Siteaccess.
- Schritt 5 gibt eine nummerierte Liste aus, die mit `settings/site.ini` (Scope `default`) beginnt und mit der Datei
  endet, deren Wert **In effect** ist.
- Schritt 8: **Systeminformationen** nennt dieselbe Anzahl Siteaccesses und dieselbe Voreinstellung.
- Der Trainer prüft das, indem er zwei Teilnehmende bittet, ihre Ausgabe von `where` laut vorzulesen und die
  gewinnende Datei zu nennen.

![Ein Siteaccess der Installation: die Medien-Website unter ihrer eigenen Adresse](../../../../../eng-us/help/images/trainers/ms-site-a.webp)

## Quiz

1. Aus welchen drei Teilen besteht ein Siteaccess?
2. Welche Liste nennt die Siteaccesses, die eine Datenbank teilen?
   A. `AvailableSiteAccessList[]` B. `RelatedSiteAccessList[]` C. `SiteList[]` D. `MatchOrder`
3. Ein Wert ist in `settings/siteaccess/site/` und in `settings/override/` gesetzt. Welcher ist für `site` wirksam?
4. Richtig oder falsch: Sie ändern die Voreinstellung einer Einstellung, indem Sie `settings/site.ini` bearbeiten.
5. Welcher Befehl zeigt jede Datei, die `SiteName` setzt, und den wirksamen Wert?

<details>
<summary>Antworten</summary>

1. Ein Name, ein Einstellungsverzeichnis in `settings/siteaccess/<name>/` und eine Regel, die entscheidet, welche
   Anfragen er beantwortet ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
2. **B.** Der Kommentar des Kernels in `settings/site.ini`: "A list of related siteaccesses sharing the same
   database".
3. Der Wert in `settings/override/`: Das globale Override wird zuletzt geladen und hat das letzte Wort.
4. **Falsch.** `settings/site.ini` enthält die Kernel-Voreinstellungen und wird bei Upgrades ersetzt; setzen Sie Werte
   in einem Override, einem Siteaccess-Verzeichnis oder einer Erweiterung.
5. `php bin/php/console exp:ini where site.ini/SiteSettings/SiteName` (optional mit dem Siteaccess als letztem
   Argument).

</details>

## Weiterführende Informationen

- [10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site):
  die Referenz für dieses Modul.
- [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../../eng-us/bc/6.0/console-exp-ini.md): jede Aktion und jeder
  Scope.
- [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md): die Sicht des
  Hosters.
- [Mehrere Websites in einer Installation einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md): die Sicht
  der Website-Ersteller.

## Verwandte Seiten

- [Modul 1: Siteaccesses für mehrere Websites](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
