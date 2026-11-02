# Statisches Veröffentlichen unter Windows und in Clustern

Diese Anleitung ist für Administratoren, die Exponential 6.0.15+ unter Windows, auf mehreren Webservern mit gemeinsamem
Speicher oder unter Velocity betreiben und wissen wollen, was das statische Veröffentlichen dort anders macht.

[Anleitungen](README.md) · Verwandt: [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md#plattformen),
[Repositorys und Zugangsdaten](statisches-veroeffentlichen-repositorys-und-zugangsdaten.md)

## Was Sie am Ende haben

Builds und Veröffentlichungen, die unter Windows, in einem Cluster und unter Velocity gleich funktionieren, auf der
richtigen Maschine und als der richtige Benutzer.

## Windows

- **git**: Installieren Sie Git for Windows und sorgen Sie dafür, dass `git` im `PATH` des Benutzers liegt, der die
  Konsole ausführt. Git wird ohne Shell aus einer Argumentliste gestartet, Pfade mit Leerzeichen funktionieren also.
- **Pfade**: Das Manifest schreibt Pfade immer mit `/`, gleich auf welchem System, sodass sich ein unter Windows und
  ein unter Linux gemachter Build sauber vergleichen lassen.
- **Dateinamen**: Seitenpfade, die unter Windows keine Dateinamen sein können (mit `:`, `*`, `?` oder Namen wie `CON`),
  werden auf jedem System übersprungen und im Bericht aufgeführt, sodass ein Build nie davon abhängt, wo er lief.
- **Zeilenenden**: Der Build schreibt Dateien Byte für Byte, wie die Site sie sendet, und das Manifest enthält ihren
  SHA-256. Lassen Sie Git sie im Ordner des Ziels nicht umwandeln: einmal `git -C <ordner> config core.autocrlf false`,
  wenn Ihr Git for Windows auf Umwandeln eingestellt ist.
- **Der Deploy-Key** wird für die Dauer des Pushs mit den Rechten des aktuellen Benutzers als Datei geschrieben.
- **Die Vorschau** (`exp:static:serve`) startet den eingebauten Webserver von PHP ohne Shell.
- **Rechte**: Führen Sie die Befehle als der Benutzer aus, dem die Dateien der Site gehören, wie unter Linux.

## Cluster

In einem Cluster (mehrere Webserver mit dem Cluster-Datei-Handler und gemeinsamem Speicher):

- **Dateien** unterhalb von `var/` werden über den Cluster-Datei-Handler gelesen, sodass Speicherbilder und
  Bildvarianten im Speicher des Clusters gefunden werden, auch wenn sie nicht auf der lokalen Platte liegen. Alles
  andere, was eine Seite nennt, wird wie von einem Besucher bei der Site angefragt.
- **Bauen Sie auf einem Knoten.** Der Ordner des Ziels und sein Git-Repository sind lokale Dateien auf der Maschine,
  die baut. Wählen Sie einen Knoten (oder einen eigenen Build-Server mit Zugriff auf Datenbank und Speicher) für die
  Konsole, den Cronjob-Teil `staticpublish` und die Jobs der Administration, und halten Sie `WorkDir` auf diesem Knoten.
- **Jobs aus der Administration** starten als Konsolenprozesse auf dem Knoten, der die Anfrage beantwortet hat. Leiten
  Sie in einem Cluster entweder Setup > Statisches Veröffentlichen auf den Build-Knoten, oder veröffentlichen Sie dort
  von der Konsole oder per Cronjob.
- **Die Sperre** eines Ziels ist eine Datei in `var/<site>/static-publish/.locks/`: Sie schützt ein Ziel auf einer
  Maschine, nicht über Maschinen hinweg. Ein Grund mehr, nur auf einem Knoten zu bauen.
- **Der Abruf** holt Seiten über HTTP; setzen Sie `CrawlURL` auf die interne Adresse eines Knotens, damit der Build
  nicht über den Lastverteiler oder ein CDN geht, das zwischengespeicherte Seiten ausliefern kann.

## Velocity

Unter Velocity startet die Administration jeden Job als abgelösten Konsolenprozess, sodass ein langer Build nie einen
Worker belegt. Der Abruf geht über HTTP an Velocity wie an jeden Webserver. Starten Sie Velocity nach einer Änderung an
`staticcache.ini` neu, wie nach jeder Änderung von Einstellungen.

## Datenbanken

Jede Datenbank, auf der Exponential läuft, funktioniert. Der Build liest nur Inhalts- und Bildtabellen; der
Aktualisierungsschritt `6.0.15-static-publish-tables` legt `exp_static_run` und `exp_static_page` auf jeder an.

## Nächster Schritt

[Statisches Veröffentlichen: Fehlerbehebung](statisches-veroeffentlichen-fehlerbehebung.md).
