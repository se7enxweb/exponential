---
title: "Lektion 5.4: Ein Paket mit dem Assistenten erstellen"
audience: trainer, admin, developer
category: training
order: 54
summary: Die Teilnehmenden erstellen mit dem Paket-Assistenten ein Paket mit Inhaltsklassen, wählen eine Lizenz aus der konfigurierten Liste und eine Version nach Semantic Versioning, überprüfen es und laden es herunter; außerdem lernen sie die vier Assistenten kennen und wie man eine Lizenz hinzufügt. 50 Minuten.
keywords: multisite und seitenpakete, paket-assistent, neues paket erstellen, export von inhaltsklassen, export von inhaltsobjekten, seitenstil, lizenz, multisite and site packages, package wizard, Create new package, Start the wizard, content class export, content object export, extension export, site style, license, SPDX, GPL-2.0-or-later, Semantic Versioning, LicenseSettings, Review, Create package
type: help
---
# Lektion 5.4: Ein Paket mit dem Assistenten erstellen

Die Administration hat vier Assistenten, die aus dem, was die Website hat, ein Paket machen: Inhalte, Inhaltsklassen,
Erweiterungen und einen Seitenstil. Jedes Paket nennt eine Lizenz aus einer strengen Liste und eine Version nach
Semantic Versioning. Diese Lektion geht den Assistenten für Inhaltsklassen Schritt für Schritt durch und endet mit
einem Paket im Repository `local`, das heruntergeladen und anderswo installiert werden kann.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 5: Seitenpakete](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 5.2](die-paketseiten.md); eine Rolle mit `package/create` (die Rolle Administrator) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- unter den vier Assistenten den richtigen für eine Aufgabe wählen;
- sich mit **Fortfahren**, **Zurück** und **Abbrechen** durch die Schritte des Assistenten bewegen und einen Fehler
  an seinem Feld korrigieren;
- eine Lizenz aus der Liste wählen und eine gültige Version nach Semantic Versioning schreiben;
- ein Paket überprüfen, bevor **Paket erstellen** es schreibt;
- eine Lizenz zur Liste in `package.ini` hinzufügen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Der Paket-Assistent](../../../../../eng-us/features/6.0/package-wizard.md) und
[Paketlizenzen und Semantic Versioning](../../../../../eng-us/features/6.0/package-licenses-and-versions.md). Die Beschriftungen
sind die des Designs admin4.

1. **Die vier Assistenten** (4 Minuten). **Setup** > **Pakete** > **Neues Paket erstellen** öffnet **Erstelle Paket**,
   das **Export von Inhaltsobjekten** (Knoten oder ganze Teilbäume mit ihren Inhalten), **Export von Inhaltsklassen**
   (die Definitionen von Inhaltsklassen, keine Inhalte), **Extension-Export** (die Dateien von Erweiterungen; das
   Exportieren muss zuerst in `package.ini [ExtensionExportSettings]` eingeschaltet werden) und **Seitenstil** (zwei
   Stylesheets, ihre Bilder und ein Vorschaubild) auflistet. Wählen Sie einen und klicken Sie auf **Assistent
   starten**.
2. **Die Schritte** (4 Minuten). Jeder Schritt zeigt die Liste der Schritte, "Schritt n von m" und seinen Namen.
   **Fortfahren** prüft den Schritt; Fehler werden oben mit Links zu ihren Feldern aufgelistet und unter jedem Feld
   angezeigt. **Zurück** behält gültige Eingaben; **Abbrechen** schreibt nichts. Der letzte Schritt zeigt die
   Zusammenfassung **Überprüfen**; **Paket erstellen** schreibt das Paket nach `local` und öffnet seine Seite.
3. **Lizenzen** (4 Minuten). Die Auswahlliste **Lizenz** ist gruppiert (Software, Dokumentation, Creative Commons,
   gemeinfrei, proprietär). Die Voreinstellung ist `GPL-2.0-or-later`, die Lizenz von Exponential selbst. Der Server
   akzeptiert nur einen konfigurierten SPDX-Bezeichner oder einen Alias wie `GPL`. Fügen Sie eine Lizenz in
   `settings/override/package.ini.append.php` unter `[LicenseSettings] LicenseList[]` mit einem Block
   `[License_<identifier>]` hinzu.
4. **Versionen** (3 Minuten). Neue Pakete beginnen bei `1.0.0`. Die Assistenten akzeptieren `MAJOR.MINOR.PATCH` mit
   einem optionalen `-prerelease` und `+build`, höchstens 24 Zeichen.

### Häufige Fragen

**Welche Lizenz für Kursmaterial?** Dokumentation: `GFDL-1.3-or-later` wird unter den Dokumentationslizenzen
angeboten. Code: `GPL-2.0-or-later`, sofern es keinen Grund für eine andere gibt.

**Wird `1.0` akzeptiert?** Nicht für ein neues Paket: Es muss `1.0.0` sein. Vorhandene Pakete mit `1.0` werden als
`1.0.0` gelesen und nie umgeschrieben.

**Warum fehlt der Schritt für Betreuer?** Er wird ausgelassen, wenn Ihre Rolle keine Betreuerrolle vergeben darf.

**Kann ich das Format wählen?** Ja: Classic XML oder JSON, im Schritt mit den Paketinformationen.

### Stolperfallen

- **Extension-Export ausgeschaltet.** Jede Erweiterung wird aufgeführt, kann aber nicht gewählt werden, mit Angabe
  des Grundes; das Einschalten ist eine Einstellungsänderung und nicht Teil dieser Lektion.
- **Echte Namen und Adressen.** Die Felder für Betreuer und Änderungsprotokoll landen in `package.xml` und reisen mit
  dem Paket. Verwenden Sie im Kurs einen Rollennamen und eine Adresse unter `example.invalid`.
- **Die Eingabetaste drücken.** Die Eingabetaste in einem Feld bedeutet **Fortfahren**, nie **Zurück**.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation und ein Administratorkonto.

1. Öffnen Sie **Setup** > **Pakete** und klicken Sie auf **Neues Paket erstellen**.
2. Lesen Sie die vier Assistenten, wählen Sie **Export von Inhaltsklassen** und klicken Sie auf **Assistent starten**.
3. Filtern Sie im Schritt für Inhaltsklassen die Liste nach `folder`, kreuzen Sie **Folder** an, leeren Sie den
   Filter und kreuzen Sie auch **Article** an. Notieren Sie "2 of n selected". Klicken Sie auf **Fortfahren**.
4. Lassen Sie unter **Paket Informationen** den Namen leer und klicken Sie auf **Fortfahren**. Lesen Sie die
   Fehlerzusammenfassung und folgen Sie ihrem Link zum Feld.
5. Geben Sie den Namen `course_classes`, eine Zusammenfassung und die Version `1.0` ein und klicken Sie auf
   **Fortfahren**. Lesen Sie den Versionsfehler.
6. Korrigieren Sie die Version auf `1.0.0`. Öffnen Sie **Lizenz** und suchen Sie `GPL-2.0-or-later` und
   `GFDL-1.3-or-later`. Behalten Sie die Voreinstellung. Wählen Sie das Format Classic XML. Klicken Sie auf
   **Fortfahren**.
7. Geben Sie im Schritt für Betreuer (falls angezeigt) `Course trainer` und `trainer@example.invalid` ein. Klicken Sie
   auf **Fortfahren**.
8. Schreiben Sie im Schritt für das Änderungsprotokoll "Course package of lesson 5.4" und lesen Sie die
   Zusammenfassung **Überprüfen**.
9. Klicken Sie einmal auf **Zurück**, dann wieder auf **Fortfahren**: Ihre Eingaben bleiben erhalten.
10. Klicken Sie auf **Paket erstellen**. Die Seite des Pakets öffnet sich.
11. Klicken Sie auf der Seite auf **Prüfen**, dann auf **Herunterladen**.

## Erwartetes Ergebnis

- Schritt 4 und Schritt 5 bleiben auf ihrem Schritt, mit einer Fehlerzusammenfassung und dem Fehler unter dem Feld.
- Nach Schritt 10 öffnet sich die Seite von `course_classes` im Repository `local`, Version `1.0.0`, Lizenz
  GNU General Public License v2.0 or later, mit zwei Inhaltsklassen.
- Schritt 11: Die Validierung ist erfolgreich, und der Browser speichert `course_classes-1.0.0...ezpkg`.
- Der Trainer prüft die Zusammenfassung **Überprüfen** aus Schritt 8 auf einem Bildschirm.

![Erstelle Paket: die Assistenten, mit denen man beginnen kann](../../../../../eng-us/help/images/trainers/ms-create.webp)

## Quiz

1. Welcher Assistent exportiert die Definitionen von Klassen ohne Inhalte?
   A. Export von Inhaltsobjekten B. Export von Inhaltsklassen C. Extension-Export D. Seitenstil
2. Welche Version akzeptiert der Assistent: `1.0`, `1.0.0` oder `1.0.0.1`?
3. Was ist die voreingestellte Lizenz, und warum?
4. Wo fügen Sie eine Lizenz hinzu, die nicht in der Liste steht?
5. Was schreibt **Abbrechen**?

<details>
<summary>Antworten</summary>

1. **B.**
2. `1.0.0`: striktes Semantic Versioning 2.0.0, `MAJOR.MINOR.PATCH`
   ([Paketlizenzen und Semantic Versioning](../../../../../eng-us/features/6.0/package-licenses-and-versions.md#versions-semantic-versioning)).
3. `GPL-2.0-or-later`, die Lizenz von Exponential selbst und das, was "GPL" immer bedeutet hat.
4. In `settings/override/package.ini.append.php`: `[LicenseSettings] LicenseList[]=<identifier>` und ein Block
   `[License_<identifier>]` mit `Name`, `URL` und `Group`; danach den INI-Cache leeren.
5. Nichts; es kehrt zur Paketliste zurück.

</details>

## Weiterführende Informationen

- [Der Paket-Assistent](../../../../../eng-us/features/6.0/package-wizard.md).
- [Paketlizenzen und Semantic Versioning](../../../../../eng-us/features/6.0/package-licenses-and-versions.md).
- [Der Paket-Assistent Schritt für Schritt](../../../../../eng-us/help/designers/extensions/package-creator-wizard-step-by-step.md) und
  [Paketlizenzen im Assistenten](../../../../../eng-us/help/designers/extensions/package-licenses-in-the-wizard.md).
- [Erweiterungsliste und Downloads](../../../../../eng-us/features/6.0/extension-list-and-downloads.md): den Export von
  Erweiterungen einschalten.

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
