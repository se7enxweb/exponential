# Eine Paketinstallation, die große Pakete übersteht

Diese Seite ist für alle, die ein Website-Paket im Browser installieren und erlebt haben, dass es in eine Zeitüberschreitung
läuft. Die Installation eines Website-Pakets (Inhalte, Klassen und Design einer Demo-Website) ist das Längste, was der
Einrichtungsassistent und **Setup > Pakete** tun. Seit Juni 2026 (Entwicklung von Release 6.0.15, Commits `277ec0ae12`
und `86911400dd`) ist der
Installer auf drei Arten gehärtet:

1. **Jedes Paketelement wird in einer eigenen Transaktion installiert.** Scheitert ein Element, werden seine
   Schreibvorgänge zurückgerollt, statt ein halbes Element in der Datenbank zu hinterlassen, und
   die Installation endet mit einem klaren Fehler, der das Element nennt.
2. **Der Browser installiert in Teilschritten.** Die Seite installiert einige Elemente, leitet dann
   auf sich selbst weiter und macht weiter, sodass keine einzelne Anfrage so lange läuft, dass sie
   an das Zeitlimit des Webservers oder von PHP stößt.
3. **Die Größe eines Teilschritts ist eine Einstellung.** Langsame Hardware kann kleinere Teilschritte verwenden,
   schnelle Hardware größere.

## Einstellungen

Datei `settings/package.ini`, Block `[InstallerSettings]`:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `InstallBatchMaxItems` | `12` | Höchstzahl nicht interaktiver Installationselemente, die in einer Anfrage verarbeitet werden. Werte unter 1 werden als 1 behandelt. |
| `InstallBatchTimeBudgetSeconds` | `6` | Sekunden Echtzeit, nach denen die Anfrage keine neuen Elemente mehr annimmt. Ein Wert von 0 oder weniger wird als 1 behandelt. |

Ein Teilschritt endet, wenn eine der beiden Grenzen erreicht ist. Findet der Code keine der beiden Einstellungen,
verwendet er 8 Elemente und 3 Sekunden. Beispiel für eine Überschreibung auf einem langsamen Shared Host:

```ini
# settings/override/package.ini.append.php
[InstallerSettings]
InstallBatchMaxItems=4
InstallBatchTimeBudgetSeconds=3
```

Leeren Sie danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`).

## Was der Installer jetzt prüft

`eZPackage::installItem()` (`kernel/classes/ezpackage.php`) lehnt ein Element ab, das:

- keinen `type` hat;
- für seinen Typ keinen Installations-Handler hat;
- XML-Inhalt haben sollte, dessen Datei sich aber nicht lesen lässt oder leer ist.

Es beginnt eine Datenbanktransaktion, führt den Handler aus und rollt zurück, wenn der Handler false zurückgibt, wenn
die Transaktion ungültig wurde oder wenn der Handler eine Ausnahme wirft. Eine Ausnahme gelangt nicht mehr bis zur
Seite; sie wird protokolliert und als Installationsfehler angezeigt. `eZPackage::install()` (alle Elemente) hält beim
ersten fehlschlagenden Element an, sofern der Aufrufer nicht den Parameter `continue-on-error` setzt, und markiert das
Paket nur dann als installiert, wenn jedes Element erfolgreich war.

Die Installationsansicht hebt außerdem `max_execution_time` von PHP für die Anfrage auf, sodass auch ein
einzelnes großes Element nicht an die Grenze stößt.

## Wenn eine Installation anhält

Die Fehlerseite nennt Typ und Namen des Elements. Beheben Sie die Ursache (eine fehlende Extension,
ein Datenbankfehler in der Debug-Ausgabe) und starten Sie dann den Installationsschritt
erneut. Die Ansicht behält die Position des aktuellen Elements (`currentItem`) zwischen Anfragen in den dauerhaften
Daten des Installationsschritts (`kernel/private/classes/views/package/install.php`);
in den Templates gibt es keine Schaltfläche namens „Retry“, laden Sie also den Installationsschritt neu,
statt eine zu suchen.

## Verwandte Seiten

- [ezpm: der Paketmanager auf der Kommandozeile](../../../eng-us/features/6.0/ezpm-package-manager-cli.md), [in ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Elemente importieren](../../../eng-us/features/6.0/package-compare-and-import.md)
- [Kickstarter: eine ganze Website aus einer Datei installieren](../../../eng-us/features/6.0/kickstarter-cli.md), [Exponential mit einem Befehl installieren](../../../eng-us/features/6.0/install-in-one-command.md)
- [MongoDB-Datenbankunterstützung](../../../eng-us/features/6.0/mongodb-database-support.md) (die Änderungen am Assistenten in denselben Wochen)
- [Installer-Protokolle und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik: Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
