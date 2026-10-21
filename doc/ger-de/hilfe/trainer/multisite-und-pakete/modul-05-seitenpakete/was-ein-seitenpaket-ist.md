---
title: "Lektion 5.1: Was ein Seitenpaket ist"
audience: trainer, admin, developer
category: training
order: 51
summary: Die Teilnehmenden erklären Pakete und Seitenpakete, die Repositorys unter var/storage/packages, woher der Installer Pakete bezieht, die vollständigen und die _clean-Varianten und die Installationsskripte, die ein Seitenpaket mitbringt. 45 Minuten.
keywords: multisite und seitenpakete, seitenpaket, paket, repository, installationsskripte, multisite and site packages, site package, package, package.xml, type site, var/storage/packages, 7x, local, RemotePackagesIndexURL, sevenx_multisite, sevenx_multisite_clean, ezpkg, install scripts, eZSiteInstaller
type: help
---
# Lektion 5.1: Was ein Seitenpaket ist

Jede Exponential-Installation beginnt mit einem Seitenpaket: dem Paket, das der Installer installiert, um die neue
Website zu formen, zusammen mit den Paketen, die es verlangt. Diese Lektion erklärt, was ein Paket ist, was es zu
einem Seitenpaket macht, wo Pakete aufbewahrt werden und wo der Installer sie findet, und die PHP-Installationsskripte,
die das Standard-Seitenpaket zu einem "Multi-Site-Installer" machen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 5: Seitenpakete](README.md) |
| Dauer | 45 Minuten (25 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | Eine Schulungsinstallation mit Shell-Zugang |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Paket, ein Seitenpaket und die drei Dinge definieren, die man mit einem Paket tun kann (installieren,
  importieren, exportieren);
- die Repositorys unter `var/storage/packages/` finden und sagen, welches der Installer verwendet;
- die drei Quellen von Seitenpaketen nennen (entfernter Index, lokales Repository, Hochladen);
- zwischen einem vollständigen Seitenpaket und seiner Variante `_clean` wählen;
- die Installationsskripte eines Seitenpakets erklären und sagen, wann sie laufen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [4.3 Seitenpakete: was installiert wird](../../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)
und [Pakete](../../../../anleitungen/pakete.md#1-wo-pakete-liegen).

1. **Paket** (5 Minuten). Ein Verzeichnis mit einer `package.xml` und den Dateien, die sie nennt: Inhaltsklassen,
   Inhaltsobjekte, Erweiterungen, Einstellungsdateien, Bilder und Dokumente. Ein Paket wird **installiert** (seine
   Elemente werden auf der Website angelegt), **importiert** (nur aufbewahrt, wie ein Seitenpaket) oder **exportiert**
   (als eine `.ezpkg`-Datei geschrieben).
2. **Seitenpaket** (4 Minuten). Ein Paket vom Typ `site` (`<type>site</type>`) plus die Pakete, die es verlangt. Das
   Standardpaket `sevenx_multisite` verlangt `sevenx_classes` (die Inhaltsklassen) und `sevenx_multisite_democontent`
   (die Demo-Inhalte).
3. **Repositorys** (4 Minuten). Unter `var/storage/packages/`: `local` für hier erstellte oder importierte Pakete,
   sonst ein Verzeichnis pro Anbieter. Das Repository des Installers ist `package.ini [RepositorySettings] Vendor`
   (`7x`), sodass ein importiertes Paket in `var/storage/packages/7x/<name>/` landet.
4. **Quellen** (4 Minuten). Der Installer liest den entfernten Index aus `[RepositorySettings] RemotePackagesIndexURL`
   und bietet dessen Seitenpakete neben den bereits importierten an; ein entfernt aufgeführtes und noch nicht
   importiertes Paket wird mit seinen Abhängigkeiten heruntergeladen und importiert. Ein Paket, das bereits im lokalen
   Repository liegt, wird so verwendet, wie es ist; kann der Index nicht gelesen werden, wird eine `index.xml` im
   System-Repository verwendet (Offline-Installation). Der Einrichtungsassistent akzeptiert außerdem eine hochgeladene
   `.ezpkg`.
5. **Vollständig oder clean** (3 Minuten). Ein `_clean`-Paket installiert dieselbe Struktur ohne die Demo-Artikel und
   -Bilder: Wählen Sie es für eine echte Website; das vollständige, um Exponential zu bewerten oder zu unterrichten.
6. **Installationsskripte** (5 Minuten). Ein Seitenpaket kann PHP-Installationsskripte als Einträge
   `<settings-file>` mitbringen; der Schritt `CreateSites` ruft ihre Funktionen an festen Punkten auf
   (`eZSitePreInstall()`, `eZSiteINISettings()`, `eZSiteRoles()`, `eZSitePostInstall()` und andere).
   `sevenx_multisite` erzeugt einen `sevenxMultiSiteInstaller`, eine Unterklasse von `eZSiteInstaller` aus dem Kernel.
   Dieser "Multi-Site-Installer" ist keine vierte Installationsmethode: Er ist der Teil des Seitenpakets bei jeder
   Installation.

### Häufige Fragen

**Welche Seitenpakete bietet diese Version an?** Der Index von 6.0.15 bietet elf an: `sevenx_multisite`,
`sevenx_site` und ihre `_clean`-Varianten sowie die älteren `ezwebin_site`, `ezflow_site`, `ezdemo_site` (jeweils auch
`_clean`) und `plain_site`, die für Websites erhalten bleiben, die darauf aufgebaut sind.

**Wird ein Seitenpaket später erneut installiert?** Nein; es wird vom Einrichtungsassistenten zusammen mit den
Paketen, die es verlangt, importiert. Seine Seite sagt, dass es für sich allein nichts zu installieren gibt.

**Was, wenn ein Skript nach der Installation abbricht?** `CreateSites` meldet den Fehler `EZSW-080` und nennt den
Schritt, der abgebrochen hat.

**Kann ich mein eigenes Seitenpaket erstellen?** Ja, aber das ist eine Aufgabe für Entwickler; dieses Modul zeigt die
Pakete, die Sie in der Administration erstellen können (Lektion 5.4).

### Stolperfallen

- **Pakete und Composer-Pakete verwechseln.** Exponential-Pakete (`.ezpkg`) enthalten Inhalte und Einstellungen;
  Composer-Pakete enthalten Code. Ein Theme kann beides sein; siehe den Theme-Kurs.
- **Eine Quelle des Installers löschen.** Das Entfernen eines Pakets aus dem Repository `7x` ändert, woraus neue
  Installationen aufgebaut werden. Lektion 5.2 zeigt die Warnung.
- **Alte entfernte Pakete.** Um eine frische Kopie eines Pakets zu erzwingen, muss zuerst sein importiertes
  Verzeichnis entfernt werden; tun Sie das nie während eines Kurses auf einem gemeinsamen Server.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation. Die Befehle laufen in ihrem Wurzelverzeichnis.

1. Listen Sie die Repositorys auf: `ls var/storage/packages/`.
2. Listen Sie die Pakete des Repositorys des Installers auf: `ls var/storage/packages/7x/`.
3. Lesen Sie den wirksamen Anbieter und die URL des Index:

   ```bash
   php bin/php/console exp:ini get package.ini/RepositorySettings/Vendor
   php bin/php/console exp:ini get package.ini/RepositorySettings/RemotePackagesIndexURL
   ```

4. Öffnen Sie `var/storage/packages/7x/sevenx_multisite/package.xml` (nur lesen) und suchen Sie `<type>`, die
   verlangten Pakete und die Einträge `<settings-file>`.
5. Listen Sie die Pakete mit dem Paketmanager auf: `php ezpm.php list -r 7x`.
6. Zeigen Sie die Informationen eines Pakets: `php ezpm.php info sevenx_multisite -r 7x`.
7. Zu zweit: Ein Kunde möchte eine echte Website mit der Standardstruktur und ohne Demo-Artikel. Welches Seitenpaket?
8. Schreiben Sie die drei Quellen auf, aus denen der Installer Seitenpakete bezieht.

## Erwartetes Ergebnis

- Schritt 2 listet auf einer mit dem Standardpaket angelegten Installation `sevenx_classes`, `sevenx_multisite` und
  `sevenx_multisite_democontent`.
- Schritt 4: `<type>site</type>`, zwei verlangte Pakete und Einstellungsdateien wie `ini-site.php`, `ini-admin.php`,
  `install-scripts.php`, `roles.php` und `preferences.php`.
- Schritt 7: `sevenx_multisite_clean`.
- Schritt 8: der entfernte Paketindex, das lokale Repository, das Hochladen einer `.ezpkg`.
- Der Trainer prüft Schritt 4 am Beamer mit einer Person.

## Quiz

1. Was macht ein Paket zu einem Seitenpaket?
2. Wo landet ein Paket, das vom Anbieter `7x` importiert wurde?
   A. `var/storage/packages/local/` B. `var/storage/packages/7x/<name>/` C. `extension/` D. `var/cache/`
3. Was ist der Unterschied zwischen `sevenx_multisite` und `sevenx_multisite_clean`?
4. Welcher Schritt ruft die Installationsskripte eines Seitenpakets auf?
5. Richtig oder falsch: Der Multi-Site-Installer ist eine vierte Installationsmethode neben dem Assistenten, dem
   Kickstarter und `exp:install`.

<details>
<summary>Antworten</summary>

1. Sein Typ ist `site` in `package.xml`.
2. **B.** Der Anbieter benennt das Repository; `package.ini [RepositorySettings] Vendor` ist `7x`.
3. Das `_clean`-Paket installiert dieselbe Struktur ohne die Demo-Artikel und -Bilder.
4. `CreateSites` ([4.3](../../../../installation/04-installationsweg-waehlen.md#installationsskripte-der-pakete-der-multi-site-installer)).
5. **Falsch.** Er ist der Teil des Seitenpakets bei jeder Installation, egal welche Oberfläche sie gestartet hat.

</details>

## Weiterführende Informationen

- [4.3 Seitenpakete: was installiert wird](../../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird).
- [Seitenpakete für Hoster](../../../hosting/bereitstellung/seitenpakete-fuer-hoster.md).
- [Seitenpakete und die mitgelieferten Themes](../../../../../eng-us/help/creators/website-creators/site-packages-and-shipped-themes.md).
- [Inhaltspakete (.ezpkg)](../../../../../eng-us/help/creators/app-creators/packages-and-ezpkg.md) und
  [Was ein Exponential-Paket ist](../../../../../eng-us/help/designers/extensions/what-an-exponential-package-is.md).
- Train the Trainer: [Das Seitenpaket für einen Kurs wählen](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/choosing-the-site-package-for-class.md).

## Verwandte Seiten

- [Modul 5: Seitenpakete](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
