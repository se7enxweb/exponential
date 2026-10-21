---
title: "Lektion 2.8: Das Seitenpaket für den Kurs wählen"
audience: trainer, admin
category: training
order: 28
summary: Wählen Sie das Seitenpaket, das zum Kurs passt, sevenx_multisite mit Demo-Inhalten für Rundgänge und Kurse für Redakteure, ein _clean-Paket, wenn die Teilnehmenden auf einer leeren Website aufbauen, und installieren Sie es mit exp:install --package. 30 Minuten.
keywords: seitenpaket, sevenx_multisite, sevenx_multisite_clean, sevenx_site, demo-inhalte, leere installation, paket, übungsumgebung, exp:install --package, site package, demo content, clean install, package, training lab
type: help
---
# Lektion 2.8: Das Seitenpaket für den Kurs wählen

Jede Installation beginnt mit einem **Seitenpaket**, das die Inhaltstypen, die Demo-Inhalte und die Einstellungen der
neuen Website festlegt. Ein Kurs für Redakteure profitiert von einer Website voller Artikel und Bilder, mit denen man
arbeiten kann; einem Entwicklerkurs, der eine Website von Grund auf baut, ist mit einer leeren besser gedient. Diese
Lektion zeigt die Möglichkeiten und wie man jede installiert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Übungsumgebungen vorbereiten |
| Voraussetzungen | [Lektion 2.3](eine-installation-pro-teilnehmer.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die am häufigsten verwendeten Seitenpakete nennen und sagen, was jedes enthält;
- ein gewähltes Paket mit `exp:install --package=<name>` installieren;
- erklären, was ein `_clean`-Paket weglässt;
- einem Kurs ein Paket zuordnen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie die Tabelle aus [Eine Installationsmethode wählen, 4.3](../../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)
   (5 Minuten):
   - `sevenx_multisite`: die Standardinstallation 7x MultiSite mit den Inhaltsklassen und Demo-Inhalten (die
     Voreinstellung von `exp:install`);
   - `sevenx_multisite_clean`: dieselbe Website ohne Demo-Inhalte;
   - `sevenx_site` und `sevenx_site_clean`: eine einfachere Website, mit oder ohne Demo-Inhalte;
   - ältere Pakete (`ezwebin_site`, `ezflow_site`, `ezdemo_site`, `plain_site`) für Websites, die darauf aufbauen.
2. Ordnen Sie sie Kursen zu (5 Minuten): Redakteure 101 und der Layouts-Kurs: `sevenx_multisite` (echt wirkende
   Inhalte). Entwickler, die eine Website bauen: ein `_clean`-Paket. Migrationsübungen: ein `_clean`-Paket als Ziel.

### Häufige Fragen

**Woher kommen die Pakete?** Aus dem entfernten Paketindex, der in `settings/package.ini` festgelegt ist
(`[RepositorySettings] RemotePackagesIndexURL`), oder aus dem lokalen Repository unter `var/storage/packages/`, wenn sie
schon importiert wurden. Ohne Netz verwendet eine Offline-Installation eine `index.xml` im lokalen Repository.

**Kann ich ein eigenes Schulungspaket erstellen?** Ja, ein Seitenpaket ist ein Exponential-Paket vom Typ `site`. Der
Kurs Multisite und Seitenpakete vermittelt das.

### Stolperfallen

- **Kein Netz im Kursraum.** Die Installation lädt die Pakete herunter. Installieren Sie im Voraus oder bereiten Sie
  ein Offline-Repository vor.
- **Demo-Inhalte mit echt wirkenden Namen.** Die Demo-Inhalte sind erfunden; lassen Sie es so, wenn Sie eigene
  hinzufügen.

## Praktische Übung

1. Geben Sie in einem neuen Code-Verzeichnis die Konfiguration für eine leere Website aus:
   `php bin/php/console exp:install --print --package=sevenx_multisite_clean --url=http://127.0.0.1:8087`.
   Suchen Sie `Site_package=sevenx_multisite_clean` in der Ausgabe.
2. Führen Sie mit denselben Optionen einen Probelauf aus und warten Sie auf „Dry-run completed“.
3. Installieren Sie sie (dieselben Optionen ohne `--print`/`--dry-run`).
4. Starten Sie den Server, melden Sie sich an und öffnen Sie **Inhaltsstruktur**. Vergleichen Sie sie mit der Website
   `sevenx_multisite` aus Lektion 2.3: Zählen Sie bei jeder die Elemente unter dem obersten Ordner.
5. Entscheiden Sie, welches Paket Sie für jeden geplanten Kurs verwenden werden, und schreiben Sie es auf das
   Datenblatt der Übungsumgebung.

## Erwartetes Ergebnis

- Die leere Installation hat dieselbe Struktur der obersten Ordner, aber weit weniger Elemente, ohne die
  Demo-Artikel und -Bilder.
- Das Datenblatt der Übungsumgebung nennt für jeden geplanten Kurs ein Seitenpaket.

## Quiz

1. Welches Seitenpaket installiert `exp:install` standardmäßig?
2. Was lässt ein `_clean`-Paket weg?
3. Welche Option wählt das Seitenpaket?
4. Welches Paket würden Sie für einen Kurs verwenden, in dem Entwickler eine Website von Grund auf bauen?

<details>
<summary>Antworten</summary>

1. `sevenx_multisite`.
2. Die Demo-Artikel und -Bilder; die Struktur ist dieselbe.
3. `--package=<name>`.
4. Ein `_clean`-Paket, zum Beispiel `sevenx_multisite_clean`.

</details>

## Weiterführende Literatur

- [Eine Installationsmethode wählen](../../../../installation/04-installationsweg-waehlen.md): Seitenpakete und das
  Installationsprogramm.
- [Pakete](../../../../anleitungen/pakete.md): Pakete in der Administration.
- [Erste Schritte](../../../erste-schritte.md): was die Teilnehmenden nach der Installation sehen.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
