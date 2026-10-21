---
title: "Lektion 6.5: Abschlussprojekt, eine Multisite von Grund auf"
audience: trainer, admin, developer
category: training
order: 65
summary: Die Teilnehmenden bauen selbstständig eine Multisite mit zwei Marken auf einer frischen Installation auf, von exp:install über Host-Zuordnung, eine Sprachweiterleitung, eine Website-Erweiterung und Logs pro Website bis zu einem Inhaltspaket, und der Trainer prüft sie anhand einer Checkliste mit acht Punkten. 90 Minuten.
keywords: multisite und seitenpakete, abschlussprojekt, praktische aufgabe, host-zuordnung, website-erweiterung, checkliste, multisite and site packages, capstone, practical task, exp:install, sevenx_multisite_clean, host matching, DefaultHostUriMatchMapItems, site extension, exp:ini move-all, UseGlobalLogDir, ezpm, checklist
type: help
---
# Lektion 6.5: Abschlussprojekt, eine Multisite von Grund auf

Die letzte Lektion führt den Kurs zusammen. Jede Person baut selbstständig und ausgehend von einem leeren Verzeichnis
eine kleine Multisite nach einem Auftrag auf, mit den Befehlen der Module 1 bis 6. Der Trainer hilft nur auf Nachfrage
und prüft das Ergebnis am Ende anhand einer Checkliste. Die Checkliste ist die eigene Prüfung des Trainers, keine
offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt
([Zertifizierungen](../../../partner/zertifizierungen.md)).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md) |
| Dauer | 90 Minuten (10 Einweisung, 70 Arbeit, 10 Prüfung) |
| Niveau | Experten |
| Zielgruppe | Teilnehmende, die die Module 1 bis 5 und die Lektionen 6.1 bis 6.4 abgeschlossen haben |
| Voraussetzungen | Ein frisches Code-Verzeichnis pro Person; die Kursseiten zum Nachschlagen geöffnet |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- selbstständig eine Multisite mit `exp:install` planen und installieren;
- eine Website mit eigenem Host und einen Sprach-Siteaccess mit einer Weiterleitung zur Sprache des Besuchers
  hinzufügen;
- die Einstellungen dieser Website in einer Website-Erweiterung halten, mit eigenen Logs;
- die Inhalte der Website verpacken und jeden Schritt mit einem Befehl oder einer Anfrage nachweisen.

## Hinweise für Trainer

### So präsentieren Sie es

1. **Der Auftrag** (5 Minuten). Lesen Sie ihn laut vor und zeigen Sie ihn am Beamer:

   *Eine Genossenschaft betreibt zwei Marken aus einer Geschäftsstelle: "North" unter `north.localhost` auf Englisch
   und "South" unter `south.localhost` auf Englisch (`/en`) und Deutsch (`/de`), wobei Besucher von
   `south.localhost/` zu ihrer Sprache geschickt werden. Die Einstellungen von South liegen in einer eigenen
   Erweiterung `site_south`, seine Logs in einem eigenen Verzeichnis. Am Ende werden die Inhalte von South als Paket
   für die Agentur exportiert, die sie übersetzen wird.*
2. **Regeln** (3 Minuten). Arbeiten Sie allein; verwenden Sie die Kursseiten und `--help`; schreiben Sie jeden Befehl
   in eine Textdatei (`capstone-commands.txt`), denn die Befehle sind Teil des Ergebnisses. Nur Adressen unter
   `example.invalid`.
3. **Hinweise, nur auf Nachfrage**. Verweisen Sie auf die Lektion, nicht auf die Antwort: Installation (6.2), zweite
   Website (1.3), Sprach-Siteaccess (1.4), Host-Zuordnung (2.2), Host und Pfad (2.3), Weiterleitung (2.4),
   Website-Erweiterung (3.1), Logs (3.3), Paket (5.3).
4. **Prüfen** (10 Minuten). Gehen Sie die Checkliste unten mit jeder Person durch, oder lassen Sie Paare sich damit
   gegenseitig prüfen und prüfen Sie zwei Personen selbst.

### Häufige Fragen

**Darf ich `sevenx_multisite` statt des clean-Pakets verwenden?** Ja; das clean-Paket hält den Baum klein, was die
Prüfungen beschleunigt.

**Müssen North und South getrennte Ordner sein?** Ja: Jede Marke hat einen eigenen Wurzelordner, wie in Lektion 1.3.

**Was, wenn mir die Zeit ausgeht?** Hören Sie auf, wo Sie sind, und prüfen Sie die Punkte, die Sie erreicht haben; die
Befehlsdatei zeigt, was geplant war.

**Wird die Checkliste benotet?** Das entscheidet der Trainer; die Checkliste ist die eigene Prüfung des Trainers,
keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt
([Zertifizierungen](../../../partner/zertifizierungen.md)).

### Stolperfallen

- **Im falschen Verzeichnis beginnen.** Das Abschlussprojekt braucht ein frisches Verzeichnis, nie die Installation
  früherer Module.
- **Host-Zuordnung, bevor die Siteaccesses existieren.** Legen Sie die Siteaccesses zuerst an und registrieren Sie
  sie, dann ordnen Sie sie zu.
- **Caches vergessen.** Leeren Sie nach jeder Einstellungsänderung den INI-Cache (und starten Sie Velocity neu, wenn
  es die Übungsumgebung ausliefert).

## Praktische Übung

Die Schritte sind der zerlegte Auftrag; die Teilnehmenden wählen die Befehle.

1. Installieren Sie mit `exp:install` auf SQLite, mit URL-Zuordnung, dem Paket `sevenx_multisite_clean`, dem Titel
   "Cooperative" und `--email=webmaster@example.invalid`. Ändern Sie das Administratorpasswort und löschen Sie die
   Passwortdatei.
2. Legen Sie unterhalb des obersten Ordners die Ordner `North` und `South` an und notieren Sie ihre Knoten-IDs.
3. Legen Sie die Siteaccesses `north`, `south_en` und `south_de` an (Kopien von `site`), jeden mit seinem `SiteName`,
   seiner `IndexPage`, seiner `DefaultPage` und seinem `RootNode`; `south_de` mit deutschen regionalen Einstellungen
   und `eng-US` als Fallback.
4. Registrieren Sie sie in `AvailableSiteAccessList[]`, `RelatedSiteAccessList[]` und `SiteList[]`.
5. Ordnen Sie `north.localhost` mit der Host-Prüfung `north` zu und `south.localhost` mit `en` und `de` mit
   `host_uri` den beiden Siteaccesses von South.
6. Schicken Sie Besucher von `south.localhost/` zu `/de`, wenn ihr Browser Deutsch möchte, und sonst zu `/en`.
7. Verschieben Sie die Einstellungen von `south_en` und `south_de` in eine neue, aktivierte Erweiterung `site_south`.
8. Geben Sie den Siteaccesses von South ein eigenes Log-Verzeichnis.
9. Verpacken Sie den Teilbaum `South` ohne Klassen und Templates und exportieren Sie ihn nach `var/tmp/capstone/`.
10. Liefern Sie das Verzeichnis aus und führen Sie die Prüfungen der Checkliste mit `curl` aus.

## Erwartetes Ergebnis

Die Checkliste des Trainers; jeder Punkt wird durch den Befehl oder die Anfrage in Klammern nachgewiesen.

1. Die Installation antwortet, und die Datei mit dem Administratorpasswort ist verschwunden
   (`ls var/log/initial-admin-password` schlägt fehl).
2. `curl -s -H 'Host: north.localhost' <lab address>/` zeigt den Namen *North*.
3. `curl -s -H 'Host: south.localhost' <lab address>/en` zeigt South auf Englisch, `/de` auf Deutsch.
4. `curl -sI -H 'Host: south.localhost' -H 'Accept-Language: de' <lab address>/` antwortet mit `302` auf `/de` und mit
   `Accept-Language: en` auf `/en`.
5. `exp:ini where site.ini/SiteSettings/SiteName south_de` nennt eine Datei in `extension/site_south/`.
6. Eine Anfrage an `south.localhost/en/no-such-page` schreibt ihre Zeile in die eigene `404.log` von South.
7. `php ezpm.php validate var/tmp/capstone/<file>.ezpkg` meldet das Paket als gültig.
8. `capstone-commands.txt` listet die Befehle der Reihe nach und enthält kein Passwort.

## Quiz

1. In welcher Reihenfolge mussten Sie Siteaccesses anlegen, registrieren und zuordnen, und warum?
2. Welche Prüfung hat `south.localhost/de` zugeordnet?
   A. `uri` B. `host` C. `host_uri` D. `port`
3. Warum brauchte `south.localhost/` einen Weiterleitungseintrag und keinen Eintrag in `HostUriMatchMapItems[]` mit
   leerem Pfad?
4. Woher kommt nach Schritt 7 der Wert von `SiteName` für `south_de`?
5. Nennen Sie eine Sache, die Sie auf einem Live-Server anders machen würden.

<details>
<summary>Antworten</summary>

1. Anlegen, registrieren, dann zuordnen: Eine Prüfung, die einen Namen findet, der nicht in
   `AvailableSiteAccessList[]` steht, zählt nicht.
2. **C.**
3. Ein Eintrag mit leerem Pfad würde `/` für jeden Besucher einem einzigen Siteaccess zuordnen; die Weiterleitung
   wählt nach der Sprache des Browsers ([Lektion 2.4](../modul-02-anfragen-zuordnen/die-weiterleitung-zur-sprache.md)).
4. Aus `extension/site_south/settings/siteaccess/south_de/site.ini.append.php`, sofern ihn keine später geladene Datei
   setzt.
5. Zum Beispiel: echte Hostnamen mit DNS, Zertifikaten und `SiteURL`; ein Datenbankserver; eine Sicherung vor jeder
   Neuinstallation; Logs und Caches für viele Websites geplant; `.ezpkg`-Dateien außerhalb des Document Root
   aufbewahrt.

</details>

## Weiterführende Informationen

- Der ganze Kurs: [Multisite und Seitenpakete: Kursüberblick](../README.md).
- [Mehrere Websites in einer Installation einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md).
- [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md).
- [10. Nach der Installation](../../../../installation/10-nach-der-installation.md).

## Verwandte Seiten

- [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md)
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
