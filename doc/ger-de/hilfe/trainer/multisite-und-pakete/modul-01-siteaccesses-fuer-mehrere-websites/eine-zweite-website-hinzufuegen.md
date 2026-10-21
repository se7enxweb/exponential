---
title: "Lektion 1.3: Schritt für Schritt eine zweite Website hinzufügen"
audience: trainer, admin, developer
category: training
order: 13
summary: Die Teilnehmenden fügen ihrer Schulungsinstallation eine zweite öffentliche Website hinzu, mit eigenem Wurzelordner, Siteaccess, eigener Startseite und eigenem Namen, registrieren sie und erreichen sie über ihren URL-Pfad. 60 Minuten.
keywords: multisite und seitenpakete, zweite website, siteaccess, wurzelknoten, startseite, multisite and site packages, second site, RootNode, IndexPage, DefaultPage, SiteName, AvailableSiteAccessList, RelatedSiteAccessList, SiteList, FrontSiteAccess, exp:ini set, exp:ini add
type: help
---
# Lektion 1.3: Schritt für Schritt eine zweite Website hinzufügen

Das ist die zentrale praktische Lektion des Moduls. Jede Person baut eine Schulungsinstallation mit einer Website zu
einer mit zwei Websites aus: Ein neuer Ordner wird die Wurzel einer zweiten Website, ein neuer Siteaccess liefert sie
aus, und die Administration erfährt, dass die Website existiert. Jede Einstellung wird mit `exp:ini` geschrieben,
damit die Teilnehmenden jede Änderung sehen und rückgängig machen können.

> **Hinweis**
> Verwenden Sie Ihre eigene Schulungsinstallation. Die Schritte schreiben Einstellungsdateien und leeren den INI-Cache.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 1: Siteaccesses für mehrere Websites](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 1.1](was-ein-siteaccess-ist.md) und [Lektion 1.2](mehrere-websites-planen.md); eine Schulungsinstallation mit URL-Zuordnung (die Voreinstellung von `exp:install`) und ein Administratorkonto darauf |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Wurzelordner einer zweiten Website anlegen und seine Knoten-ID ablesen;
- einen Siteaccess durch Kopieren eines vorhandenen anlegen und seinen Namen, seine Startseite und seinen Wurzelknoten
  setzen;
- den Siteaccess in `AvailableSiteAccessList[]` und `RelatedSiteAccessList[]` registrieren;
- die neue Website über ihren URL-Pfad erreichen und sie auf der Kommandozeile prüfen;
- der Administration mitteilen, welche öffentlichen Websites es gibt (`SiteList[]`).

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Mehrere Websites in einer Installation einrichten, Schritt für Schritt](../../../../../eng-us/help/creators/website-creators/multisite-setup.md#step-by-step-a-second-site).

1. **Die fünf Änderungen** (5 Minuten). Zeigen Sie die Reihenfolge an der Tafel: Inhalt (ein Ordner), Siteaccess (ein
   Verzeichnis), Registrierung (zwei Listen), Zuordnung (Modul 2; bei URL-Zuordnung ist nichts nötig), Caches.
   Erklären Sie, dass bei `MatchOrder=uri` und dem mitgelieferten `URIMatchType=element` das erste Element des Pfads
   als Name eines Siteaccess genommen wird, sodass `/site_b` den Siteaccess `site_b` erreicht, sobald er in
   `AvailableSiteAccessList[]` steht.
2. **Die Einstellungen des neuen Siteaccess** (5 Minuten). In `site.ini [SiteSettings]`: `SiteName`, `IndexPage` und
   `DefaultPage` (`/content/view/full/<node id>`). In `content.ini [NodeSettings]`: `RootNode`. Erwähnen Sie
   `[DesignSettings] SiteDesign` für ein anderes Aussehen; der Kurs Theme-Design behandelt Designs.
3. **Warum `exp:ini`** (3 Minuten). Es ändert nur die Zeilen der Einstellung, behält Kommentare, legt eine Sicherung
   der Datei an und leert den INI-Cache; `--dry-run` zeigt vorher den Diff. Zeigen Sie ein `--dry-run` am Beamer.
4. **Der Administration Bescheid geben** (2 Minuten). `SiteList[]` in `site.ini [SiteSettings]` listet die
   öffentlichen Websites, damit Vorschauen und Links sie verwenden können. Der Installer schreibt es in das globale
   Override, also fügen Sie eine neue Website dort hinzu. Wenn mehrere Websites jeweils eine eigene Administration
   haben, nennt `FrontSiteAccess` in der `site.ini` dieser Administration die Website, zu der ihre Links **Website
   öffnen** führen.

### Häufige Fragen

**Warum `site` kopieren statt leer zu beginnen?** Die Kopie bringt das Design, die Sprache und die anderen
Einstellungen mit, die der Installer geschrieben hat, sodass die neue Website sofort dargestellt wird. Ändern Sie, was
abweicht.

**Braucht die neue Website eigene Benutzer?** Nein. Benutzer und Rollen werden geteilt; Lektion 4.3 beschränkt ein
Team auf eine Website.

**Was, wenn die Kopie einen `PathPrefix` mitbringt?** Ein `PathPrefix` blendet für diesen Siteaccess den ersten Teil
der URL-Aliase aus. Eine Kopie behält das Präfix des Originals, das zum Ordner der anderen Website gehört. Schritt 6
prüft das.

**Ist `/site_b` die endgültige Adresse?** Für die Übungsumgebung ja. Modul 2 gibt der Website einen eigenen Hostnamen.

### Stolperfallen

- **Knoten-ID oder Objekt-ID.** Der Reiter **Details** zeigt beide. `RootNode` und `IndexPage` brauchen die
  **Knoten ID**.
- **Ein Tippfehler im Namen des Siteaccess.** `exp:ini` verweigert einen Scope für einen Siteaccess ohne Verzeichnis;
  kopieren Sie zuerst das Verzeichnis und schreiben Sie dann die Einstellungen.
- **Velocity.** Läuft die Übungsumgebung auf Exponential Velocity, starten Sie ihn nach der Einstellungsänderung neu
  (`php bin/php/console exp:velocity restart`), sonst behalten die Worker die alten Einstellungen.
- **Unveröffentlichter Ordner.** Ein Ordner, der nur als Entwurf gespeichert wurde, hat keinen Knoten;
  veröffentlichen Sie ihn zuerst.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation (URL-Zuordnung, wie von `exp:install` ohne `--access` installiert).
Die Befehle laufen in ihrem Wurzelverzeichnis; fügen Sie `--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Melden Sie sich in der Administration an. Öffnen Sie in der **Inhaltsstruktur** den obersten Ordner. Klicken Sie
   unter **Unterelemente** auf **Neu erstellen** und wählen Sie **Folder**. Nennen Sie ihn `Site B` und klicken Sie
   auf **Zur Veröffentlichung schicken**.
2. Öffnen Sie den neuen Ordner, öffnen Sie seinen Reiter **Details** und notieren Sie die **Knoten ID**.
3. Kopieren Sie in der Shell den öffentlichen Siteaccess:

   ```bash
   cp -r settings/siteaccess/site settings/siteaccess/site_b
   ```

4. Setzen Sie den Namen und die Startseite (ersetzen Sie `NNN` durch die Knoten-ID):

   ```bash
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Site B" siteaccess:site_b --dry-run
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Site B" siteaccess:site_b
   php bin/php/console exp:ini set site.ini/SiteSettings/IndexPage /content/view/full/NNN siteaccess:site_b
   php bin/php/console exp:ini set site.ini/SiteSettings/DefaultPage /content/view/full/NNN siteaccess:site_b
   ```

5. Setzen Sie den Wurzelknoten:

   ```bash
   php bin/php/console exp:ini set content.ini/NodeSettings/RootNode NNN siteaccess:site_b
   ```

6. Prüfen Sie das Pfadpräfix, das die Kopie geerbt hat:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/PathPrefix siteaccess:site_b
   ```

   Gibt der Befehl einen Wert aus, entfernen Sie ihn aus der Kopie:
   `php bin/php/console exp:ini rem site.ini/SiteAccessSettings/PathPrefix siteaccess:site_b`.
7. Registrieren Sie den Siteaccess für die ganze Installation:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]' site_b global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/RelatedSiteAccessList[]' site_b global
   ```

8. Teilen Sie der Administration die Website mit:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteSettings/SiteList[]' site_b global
   ```

9. Leeren Sie den INI-Cache (und starten Sie Velocity neu, wenn er die Übungsumgebung ausliefert):

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

10. Prüfen Sie aus der Shell, mit der Adresse Ihrer Übungsumgebung:

    ```bash
    curl -sI http://127.0.0.1:8087/site_b | head -1
    ```

11. Öffnen Sie `/site_b` im Browser. Der Seitentitel zeigt *Site B*, und die Startseite ist der neue Ordner.
12. Führen Sie `php bin/php/console exp:ini where site.ini/SiteSettings/SiteName site_b` aus und finden Sie die
    Datei, die gewinnt.

## Erwartetes Ergebnis

- `curl` antwortet für `/site_b` mit `HTTP/1.1 200 OK` (oder `HTTP/2 200`).
- Der Browser zeigt den Ordner *Site B* als Startseite, mit dem Design der Hauptwebsite und dem Namen *Site B*.
- `exp:ini where` listet `settings/siteaccess/site_b/site.ini.append.php` mit `SiteName=Site B` als wirksamen Wert
  (es sei denn, das globale Override setzt `SiteName` ebenfalls; dann gewinnt das Override, und die Person entfernt
  diese Zeile oder setzt den Namen dort pro Siteaccess mit den Werkzeugen aus Modul 3).
- **Setup** > **Systeminformationen**, Abschnitt **Siteaccesses**, zählt einen Siteaccess mehr als in Lektion 1.1.
- Der Trainer prüft das, indem er das `/site_b` von zwei Teilnehmenden am Beamer ansieht.

## Quiz

1. Welche ID nimmt `RootNode`, die Knoten-ID oder die Objekt-ID?
2. Was sorgt bei `MatchOrder=uri` und `URIMatchType=element` dafür, dass `/site_b` den Siteaccess `site_b` erreicht?
   A. Ein Eintrag in `URIMatchMapItems[]` B. Der Name steht in `AvailableSiteAccessList[]` C. Ein DNS-Eintrag D. Ein
   Neustart von Velocity
3. Warum `settings/siteaccess/site` kopieren, statt mit einem leeren Verzeichnis zu beginnen?
4. Welche Einstellung teilt der Administration die öffentlichen Websites mit?
5. Nach der Änderung zeigt die Website noch den alten Namen. Nennen Sie zwei Ursachen.

<details>
<summary>Antworten</summary>

1. Die Knoten-ID, wie auf dem Reiter **Details**.
2. **B.** Die Methode element nimmt den ersten Teil des Pfads als Namen des Siteaccess; es muss ein verfügbarer
   Siteaccess sein (`settings/site.ini`, `URIMatchType=element`).
3. Die Kopie bringt das Design, die Sprache und andere Einstellungen mit, sodass die neue Website sofort dargestellt
   wird; Sie ändern nur, was abweicht.
4. `SiteList[]` in `site.ini [SiteSettings]`, das der Installer in das globale Override schreibt
   ([Mehrere Websites einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md#step-by-step-a-second-site)).
5. Der INI-Cache wurde nicht geleert; das globale Override setzt `SiteName` und gewinnt; Velocity wurde nicht neu
   gestartet.

</details>

## Weiterführende Informationen

- [Mehrere Websites in einer Installation einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md): die
  Schritte mit Host-Zuordnung.
- [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../../eng-us/bc/6.0/console-exp-ini.md): `set`, `add`, `rem`,
  `--dry-run`.
- [10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site).
- [Den Inhaltsbaum strukturieren](../../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md).

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
