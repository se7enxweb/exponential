---
title: "Lektion 10.5: Pakete"
audience: trainer, admin
category: training
order: 105
summary: Die Teilnehmenden erklären, was ein Paket ist und wo Pakete liegen, finden und lesen ein Paket unter Setup > Pakete, validieren es, exportieren eines in der Shell in eine Datei und erklären, warum das Entfernen eines Pakets es nicht deinstalliert. 35 Minuten.
keywords: website-administration, pakete, setup pakete, ezpkg, package.xml, ablagen, repositories, var/storage/packages, installer-quelle, prüfen, herunterladen, ezpm.php, import, export, paket entfernen, site administration, packages, installer source, Validate, Download, remove package
type: help
---
# Lektion 10.5: Pakete

Pakete übertragen Inhaltsklassen, Inhalte, Erweiterungen und Einstellungen zwischen Installationen, und aus ihnen baut
der Einrichtungsassistent neue Websites. Ein Administrator muss herausfinden können, was ein Paket enthält, bevor er
es installiert, und muss wissen, dass das Entfernen eines Pakets nicht dasselbe ist wie seine Deinstallation. Diese
Lektion vermittelt **Setup > Pakete** und das Kommandozeilenwerkzeug `ezpm.php`, mit Aktionen, die nichts ändern.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 10: Upgrades, Erweiterungen und Pakete](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Lektion 10.4](die-erweiterungsseite.md); ein Administratorkonto und eine Shell |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was ein Paket ist: installiert, importiert oder exportiert;
- unter **Setup > Pakete** ein Paket nach Suche, Ablage, Typ und Zustand finden;
- die Seite eines Pakets lesen und es validieren, ohne es zu installieren;
- ein Paket mit `ezpm.php` in eine `.ezpkg`-Datei exportieren;
- erklären, was **Installer-Quelle** bedeutet und warum das Entfernen eines Pakets es nicht deinstalliert.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Pakete: was sie enthalten, wo sie liegen und wie Sie sicher mit ihnen umgehen](../../../../anleitungen/pakete.md).

1. **Was ein Paket ist** (3 Minuten). Ein Verzeichnis mit einer `package.xml` und den Dateien, die sie nennt. Es kann
   **installiert** sein (seine Elemente werden auf der Website angelegt), **importiert** (nur aufbewahrt) oder
   **exportiert** (als eine `.ezpkg`-Datei geschrieben).
2. **Wo Pakete liegen** (2 Minuten). In Ablagen unterhalb von `var/storage/packages/`: `local` für hier erstellte oder
   importierte Pakete und ein Verzeichnis pro Anbieter. Die Ablage des Einrichtungsassistenten ist gekennzeichnet.
3. **Die Liste** (3 Minuten). **Setup > Pakete**: Zahlen, **Ablagen**, **Pakete finden** (Suche, Ablage, Typ,
   Zustand, Sortierung) und eine Karte pro Paket mit **Ansehen** und **Herunterladen**.
4. **Die Seite eines Pakets** (3 Minuten). Nur lesend, bis eine Schaltfläche gedrückt wird: Details, was es enthält,
   das Changelog, der Paketinhalt (jede Datei lässt sich ansehen) und **Prüfen**, das es so prüft, wie ein Upload
   geprüft wird.
5. **Installer-Quellen und Entfernen** (3 Minuten). Ein Paket, das der Installer als Quelle verwendet, ist als
   **Installer-Quelle** gekennzeichnet; um es zu entfernen, ist eine eigene Bestätigung nötig. Das Entfernen eines
   Pakets löscht sein Verzeichnis; was es installiert hat, bleibt auf der Website.
6. **Uploads** (1 Minute). Ein Upload wird geprüft, bevor irgendetwas geschrieben wird; ein Pfad außerhalb des
   Pakets, ein Link oder ein Gerät wird abgelehnt. Ein vorhandenes Paket wird nie überschrieben.

### Häufige Fragen

**Kann ein Paket deinstallieren, was es installiert hat?** Seine Seite bietet **Deinstallieren** an, wenn das Paket
das kann und Ihre Rolle es erlaubt. **Entfernen** ist etwas anderes: Es löscht nur das Verzeichnis des Pakets.

**Woher kommt ein heruntergeladenes Paket?** **Herunterladen** schreibt die `.ezpkg` des Pakets; die Shell tut
dasselbe mit `ezpm.php export`.

**Sind Pakete und Composer-Pakete dasselbe?** Nein. Pakete von Exponential (`.ezpkg`) enthalten Klassen, Inhalte und
Einstellungen; Composer-Pakete enthalten Code. Lektion 10.3 hat Composer behandelt.

### Stolperfallen

- **Eine Installer-Quelle entfernen.** Neue Installationen aus dem Einrichtungsassistenten hängen von ihr ab. Die Seite
  fragt eigens nach; lesen Sie die Frage.
- **Im Kurs installieren, „um zu sehen, was passiert“.** Eine Installation legt Klassen und Inhalte auf der Website
  an. Verwenden Sie stattdessen **Prüfen** und den Inhalt der Seite.
- **In die Installation exportieren.** Schreiben Sie Exporte in ein Verzeichnis außerhalb des Web-Wurzelverzeichnisses
  oder nach `var/tmp`, und räumen Sie danach auf.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, im Browser und in einem Terminal im Wurzelverzeichnis der
Installation. Nichts wird installiert oder entfernt.

1. Öffnen Sie **Setup > Pakete**. Lesen Sie **Übersicht** und **Ablagen**: Welche Ablage gehört dem
   Einrichtungsassistenten?
2. Wählen Sie unter **Pakete finden** den Zustand **Installer-Quellen** (oder geben Sie unter **Suchen** `sevenx` ein)
   und wenden Sie den Filter an. Notieren Sie die Karten, die als **Installer-Quelle** gekennzeichnet sind.
3. Klicken Sie bei einem Paket auf **Ansehen**. Lesen Sie **Details** und **Was es enthält**.
4. Öffnen Sie **Paketinhalt** und sehen Sie sich die Paketdefinition und eine Datei einer Inhaltsklasse an.
5. Führen Sie **Prüfen** aus und lesen Sie, was geprüft wurde.
6. Listen Sie in einem Terminal die Pakete auf:

   ```bash
   php ezpm.php list
   ```

7. Exportieren Sie das angesehene Paket in eine Datei (das liest das Paket nur):

   ```bash
   mkdir -p var/tmp/packages-training
   php ezpm.php export <package name> -d var/tmp/packages-training
   ls -l var/tmp/packages-training
   ```

8. Erklären Sie Ihrer Sitznachbarin oder Ihrem Sitznachbarn in zwei Sätzen, was **Ausgewählte entfernen** in der Liste
   mit diesem Paket und mit der Website machen würde.
9. Räumen Sie das Exportverzeichnis aus Schritt 7 auf, wie Ihr Trainer es zeigt.

## Erwartetes Ergebnis

- Schritt 2 zeigt die Pakete, aus denen der Einrichtungsassistent Websites installiert, gekennzeichnet als
  **Installer-Quelle**.
- Schritt 5 meldet die Prüfungen, die das Paket bestanden hat, oder jedes Problem.
- Schritt 7 schreibt eine `.ezpkg`-Datei.
- Schritt 8: Das Verzeichnis des Pakets würde gelöscht; was es installiert hat, bleibt auf der Website; bei einer
  Installer-Quelle würde sie neuen Installationen aus dem Assistenten fehlen.
- Der Trainer prüft die Antwort aus Schritt 8 bei zwei Teilnehmenden.

![Setup, Pakete: die Pakete in den Ablagen](../../../../../eng-us/help/images/trainers/sa-packages.webp)

## Quiz

1. Was ist der Unterschied zwischen dem Installieren und dem Importieren eines Pakets?
2. Wo liegen Pakete auf der Festplatte?
   A. `extension/` B. `var/storage/packages/` C. `settings/packages/` D. `vendor/`
3. Sie entfernen ein Paket, das installiert wurde. Was geschieht mit den Klassen, die es angelegt hat?
4. Was bedeutet **Installer-Quelle** auf einer Karte?
5. Wie sehen Sie, was ein Paket enthält, ohne es zu installieren?

<details>
<summary>Antworten</summary>

1. Das Installieren legt seine Elemente auf der Website an; das Importieren bewahrt das Paket nur auf
   ([Pakete, Kurz gesagt](../../../../anleitungen/pakete.md#kurz-gesagt)).
2. **B.** In Ablagen wie `local` und einer pro Anbieter.
3. Sie bleiben: Das Entfernen eines Pakets löscht sein Verzeichnis; es deinstalliert das Paket nicht.
4. Der Einrichtungsassistent verwendet es als Quelle für neue Installationen; um es zu entfernen, ist eine eigene
   Bestätigung nötig.
5. Auf seiner Seite (**Ansehen**): **Was es enthält**, **Paketinhalt** und **Prüfen**.

</details>

## Weiterführende Literatur

- [Pakete](../../../../anleitungen/pakete.md): jede Seite, die Prüfungen eines Uploads und die Fehlerbehebung.
- [Pakete vergleichen und importieren](../../../../funktionen/6.0/pakete-vergleichen-und-importieren.md).
- [Der Paketmanager ezpm auf der Kommandozeile](../../../../funktionen/6.0/ezpm-paketmanager-cli.md).
- [Seitenpakete für Hoster](../../../hosting/bereitstellung/seitenpakete-fuer-hoster.md).

## Verwandte Seiten

- [Modul 10: Upgrades, Erweiterungen und Pakete](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
