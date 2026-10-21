---
title: "Lektion 5.3: Pakete auf der Kommandozeile mit ezpm"
audience: trainer, admin, developer
category: training
order: 53
summary: Die Teilnehmenden listen, untersuchen, erstellen, befüllen, exportieren, validieren und importieren Pakete mit ezpm, verpacken einen Inhalts-Teilbaum der zweiten Website und erklären die Sperre, die stapelweisen Commits und was nach einer großen Installation zu tun ist. 55 Minuten.
keywords: multisite und seitenpakete, paketmanager, inhalts-teilbaum, exportieren, importieren, validieren, installieren, sperre, multisite and site packages, ezpm, ezpm.php, package manager, create, add, ezcontentobject, ezcontentsubtree, export, import, validate, install, --format=json, --as-drafts, ezpm.lock, updatesearchindex
type: help
---
# Lektion 5.3: Pakete auf der Kommandozeile mit ezpm

`ezpm.php` ist der Paketmanager auf der Kommandozeile. Er tut, was die Paketseiten tun, und mehr: Er verpackt einen
ganzen Inhalts-Teilbaum, exportiert im JSON-Format und installiert große Pakete ohne einen Browser, der in eine
Zeitüberschreitung laufen könnte. In dieser Lektion verpacken die Teilnehmenden die Inhalte von *Site B* und sehen
sich das Ergebnis an, ohne es irgendwo zu installieren.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 5: Seitenpakete](README.md) |
| Dauer | 55 Minuten (20 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 5.2](die-paketseiten.md); *Site B* mit einigen Inhalten (Module 1 und 2) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `ezpm.php` direkt und über die Konsole ausführen und seine Hilfe pro Befehl lesen;
- ein Paket erstellen und einen Inhalts-Teilbaum mit gewählten Optionen hinzufügen;
- ein Paket als `.ezpkg` im klassischen oder im JSON-Format exportieren und ein Archiv validieren;
- die Sperre, die stapelweisen Commits und die verzögerte Indizierung von `install` erklären;
- die Schritte nach einer großen Installation nennen (Suchindex, Caches).

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [ezpm: der Paketmanager auf der Kommandozeile](../../../../funktionen/6.0/ezpm-paketmanager-cli.md).

1. **Ausführen** (3 Minuten). `php ezpm.php help` listet die Befehle: `create`, `install`, `import`, `export`,
   `convert`, `validate`, `check`, `add`, `set`, `delete`, `list`, `info`. `php ezpm.php help <command>` erklärt
   einen. `php bin/php/console list ezpm` zeigt den Eintrag der Konsole. Zu den globalen Optionen gehören `-s`
   (Siteaccess), `-r` (Repository), `-q` (still) und `-l`/`-p` (als Benutzer anmelden).
2. **Ein Paket mit einem Inhalts-Teilbaum** (6 Minuten). Drei Schritte: `create`, `add`, `export`.

   ```bash
   php ezpm.php create mypackage 'My package summary' 1.0.1 install
   php ezpm.php add mypackage ezcontentsubtree /content/my-page --exclude-classes --siteaccess=site
   php ezpm.php export mypackage -d var/tmp
   ```

   `ezcontentsubtree` ist ein Alias von `ezcontentobject`; es nimmt eine Knoten-ID oder einen URL-Pfad. Zu den
   Optionen gehören `--include-classes`/`--exclude-classes`, `--include-templates`/`--exclude-templates`,
   `--language=`, `--current-version`/`--all-versions` und `--minimal-template-set`.
3. **Formate und Prüfungen** (3 Minuten). `export --format=json` schreibt das JSON-Format mit Prüfsummen;
   `validate <archive, directory or name>` prüft ein Paket in beiden Formaten, ohne es zu importieren.
4. **Installieren** (6 Minuten). `install <name> [-d <node id>]` legt Inhaltsobjekte unter einem übergeordneten Knoten
   ab (Voreinstellung 2). Immer nur ein Lauf: `install` und `add` setzen eine Sperre (`ezpm.lock` im var-Verzeichnis).
   View- und Template-Caches sind ausgeschaltet, und die Suchindizierung wird für den Lauf verzögert; der
   Inhalts-Handler führt alle 50 Objekte einen Commit aus; Abhängigkeiten werden zuerst installiert. `--as-drafts`
   schreibt Inhaltsobjekte als Entwürfe und installiert sonst nichts. Danach: `php bin/php/updatesearchindex.php` und
   ein Leeren des Caches.

### Häufige Fragen

**Brauche ich `--allow-root-user`?** Die Prüfung, auf der die Feature-Seite beruht, ergab, dass `ezpm.php` als root
ohne die Option lief, während andere Skripte danach fragen. Arbeiten Sie als Website-Benutzer, dann stellt sich die
Frage nicht.

**Kann ich das Paket auf derselben Installation installieren?** Sie können es, aber es schreibt Inhalte. In diesem
Kurs exportieren und validieren wir nur; das Installieren ist Lektion 5.6, auf einer Kopie.

**Was bedeutet "Another ezpm process is currently running"?** Ein zweites `install` oder `add` wurde gestartet,
während das erste die Sperre hält. Warten Sie auf das erste.

**Wohin gehen Exporte ohne `-d`?** In das aktuelle Verzeichnis.

### Stolperfallen

- **In das Web-Wurzelverzeichnis exportieren.** Eine `.ezpkg` in einem öffentlichen Verzeichnis kann jeder
  herunterladen, der ihren Namen errät. Verwenden Sie `var/tmp` oder ein Verzeichnis außerhalb des Document Root und
  entfernen Sie sie nach dem Kurs.
- **Klassen in Inhaltspaketen.** Ohne `--exclude-classes` enthält das Paket die Inhaltsklassen; eine Installation
  anderswo trifft dann auf vorhandene Klassen. Entscheiden Sie bewusst.
- **Templates.** Standardmäßig gehen die Template-Overrides mit den Inhalten mit; verwenden Sie `--exclude-templates`,
  wenn nur Inhalte umziehen sollen.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation. Die Befehle laufen in ihrem Wurzelverzeichnis als Website-Benutzer.

1. Lesen Sie die Hilfe: `php ezpm.php help`, dann `php ezpm.php help add` und `php ezpm.php help export`.
2. Listen Sie die Pakete auf: `php ezpm.php list`.
3. Erstellen Sie ein Paket für die Inhalte von *Site B*:

   ```bash
   php ezpm.php create clubb_content 'Content of Site B' 1.0.0 install
   ```

4. Fügen Sie den Ordner *Site B* und alles darunter hinzu, ohne Klassen und Templates (ersetzen Sie `NNN` durch seine
   Knoten-ID):

   ```bash
   php ezpm.php add clubb_content ezcontentsubtree NNN --exclude-classes --exclude-templates
   ```

   Beobachten Sie, wie die Statuszeile ihre Phasen nennt.
5. Zeigen Sie das Paket an: `php ezpm.php info clubb_content`.
6. Exportieren Sie es in beiden Formaten:

   ```bash
   mkdir -p var/tmp/course
   php ezpm.php export clubb_content -d var/tmp/course
   php ezpm.php export clubb_content -d var/tmp/course --format=json
   ls -l var/tmp/course
   ```

7. Validieren Sie beide Archive: `php ezpm.php validate var/tmp/course/<file>.ezpkg` für jedes.
8. Öffnen Sie in der Administration **Setup** > **Pakete** und suchen Sie `clubb_content` im Repository `local`;
   öffnen Sie seine Seite und sehen Sie sich **Was es enthält** und **Paketinhalt** an.
9. Lesen Sie die Hilfe zu `install` (`php ezpm.php help install`) und schreiben Sie auf, was `-d` und `--as-drafts`
   tun würden.
10. Entfernen Sie am Ende des Kurses `var/tmp/course` (Ihr Trainer sagt Ihnen, wann).

## Erwartetes Ergebnis

- Schritt 4 endet ohne Fehler, und die Statuszeile hat Phasen wie *Collecting subtree nodes* und *Serializing content
  objects* gezeigt.
- Schritt 6 listet zwei Archive: `clubb_content-<version>...ezpkg` und eine `.json.ezpkg`.
- Schritt 7 meldet beide als gültig.
- Schritt 8: Das Paket liegt in `local`, mit dem Ordner *Site B* und seinen Unterelementen als Inhalt, ohne Klassen.
- Der Trainer prüft Schritt 7 in einer Übungsumgebung.

## Quiz

1. Welche drei `ezpm`-Befehle bauen ein Paket mit einem Inhalts-Teilbaum und schreiben es in eine Datei?
2. Was ändert `--exclude-classes`?
   A. Die Inhalte werden weggelassen B. Die Inhaltsklassen werden weggelassen C. Templates werden weggelassen D. Nichts
3. Warum setzen `install` und `add` eine Sperre?
4. Was müssen Sie nach einem großen `install` ausführen, und warum?
5. Welcher Befehl prüft ein Archiv, ohne es zu importieren?

<details>
<summary>Antworten</summary>

1. `create`, `add` (mit `ezcontentobject` oder `ezcontentsubtree`) und `export`.
2. **B.**
3. Damit immer nur ein Lauf arbeitet; zwei parallele Läufe haben Zeitüberschreitungen beim Warten auf Datenbanksperren
   verursacht ([ezpm](../../../../funktionen/6.0/ezpm-paketmanager-cli.md#ein-paket-installieren)).
4. `php bin/php/updatesearchindex.php` und ein Leeren des Caches, weil die Indizierung verzögert war und die Caches
   während des Laufs ausgeschaltet waren.
5. `php ezpm.php validate <archive>`.

</details>

## Weiterführende Informationen

- [ezpm: der Paketmanager auf der Kommandozeile](../../../../funktionen/6.0/ezpm-paketmanager-cli.md).
- [Das JSON-Paketformat](../../../../bc/6.0/package-json-format.md).
- [Mit ezpm exportieren](../../../designer/erweiterungen/exportieren-mit-ezpm.md).
- [Pakete, 11. Aus einer Shell](../../../../anleitungen/pakete.md#11-über-die-shell).

## Verwandte Seiten

- [Modul 5: Seitenpakete](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
