---
title: SQLite-Bundles
audience: bundler, hosting
category: bundling
order: 250
summary: Warum SQLite 3, die Standarddatenbank, zu vielen Bundles passt, was der Treiber für gleichzeitige Nutzung tut, die vier Dateien und ihre Rechte, die Regel des einen schreibenden Hosts, Backups und wann ein Bundle stattdessen einen Datenbankserver anbieten sollte.
keywords: SQLite, SQLite 3, Standarddatenbank, eine Maschine, Rechte, Backup, Appliance, Demo, Schulung, default database, var/storage/sqlite3, WAL, writer-lock, one machine, permissions, backup, appliance, demo, classroom, bundle
type: help
---
# SQLite-Bundles

Diese Seite richtet sich an Bündler, die Exponential auf SQLite 3 ausliefern, der Standarddatenbank. SQLite entfernt
den Datenbankserver aus dem Bundle: Es gibt nichts separat zu installieren, zu starten, abzusichern oder zu
aktualisieren.

## Inhalt

1. [Warum SQLite zu Bundles passt](#warum-sqlite-zu-bundles-passt)
2. [Was der Treiber ergänzt](#was-der-treiber-ergänzt)
3. [Dateien und Rechte](#dateien-und-rechte)
4. [Die Regel des einen Hosts](#die-regel-des-einen-hosts)
5. [Backups](#backups)
6. [Wann Sie stattdessen einen Server anbieten](#wann-sie-stattdessen-einen-server-anbieten)

## Warum SQLite zu Bundles passt

- "SQLite 3 ist der Standard. Der Einrichtungsassistent wählt es vor, `exp:install` verwendet es, wenn kein `--db`
  angegeben ist, und die Vorlage des Kickstarters nennt es zuerst. Es braucht keinen Datenbankserver: Das gesamte
  Inhalts-Repository ist eine Datei"
  ([Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).
- "Mit dem Treiber, den Exponential mitliefert, ist es eine vollwertige Produktionsdatenbank für eine Website, die von
  einer Maschine ausgeliefert wird."
- `exp:install` ohne Optionen installiert auf SQLite mit der Datei `exponential.db`
  ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md#eine-datenbank-wählen)).

## Was der Treiber ergänzt

Aus [1. SQLite 3, der Standard](../datenbankunterstuetzung.md#1-sqlite-3-der-standard):

- Write-Ahead-Logging, "sodass Seitenaufrufe weiterlesen, während ein Redakteur veröffentlicht";
- für einen Webserver gewählte PRAGMAs der Verbindung;
- seit 6.0.15 eine **Schreibwarteschlange**: "Jede Transaktion nimmt eine exklusive Sperre auf
  `<database>.writer-lock` und führt dann `BEGIN IMMEDIATE` aus, sodass gleichzeitige Veröffentlichungen nacheinander
  drankommen (bis zu `SQLiteTransactionWait` Sekunden, Standard 25)".

## Dateien und Rechte

"Die Datenbank besteht aus vier Dateien: der Datenbank, `-wal`, `-shm` und `.writer-lock`. Jeder Prozess, der die
Website verwendet (das PHP des Webservers, die Worker von Velocity, Cronjobs, Konsolenbefehle), muss das Verzeichnis
und die Dateien schreiben können; geben Sie ihnen eine gemeinsame Gruppe, den Verzeichnismodus `2775` und eine Umask
von `0002`. Liefern Sie das Verzeichnis nie über HTTP aus (die mitgelieferten Rewrite-Regeln tun das nicht)."

Für ein Bundle:

- [ ] `var/storage/sqlite3/` existiert im Image und gehört dem Benutzer der Website (oder der gemeinsamen Gruppe).
- [ ] Der Installer läuft als dieser Benutzer.
- [ ] Cron- und Konsolenbefehle laufen als dieser Benutzer.
- [ ] Die mitgelieferte Webserver-Konfiguration behält das Routing der mitgelieferten Regeln bei
      ([Webserver-Beispiele](../../beispiele/webserver/README.md)).

## Die Regel des einen Hosts

"Wählen Sie stattdessen einen Datenbankserver, wenn mehr als eine Maschine in die Datenbank schreiben muss (WAL braucht
gemeinsamen Speicher auf einem Host, ein Netzwerkdateisystem ist also kein Ersatz)". Für Container: "Betreiben Sie mit
SQLite **einen** Container, der schreibt"
([Container](../hosting/modelle/container-docker-und-podman.md#die-datenbank-und-die-regel-eines-einzigen-schreibers)).

Ein Bundle auf SQLite sollte das in seiner Dokumentation klar sagen, damit niemand es auf drei Replikate auf einem
gemeinsamen Volume skaliert.

## Backups

Der Sicherungsumfang jeder Installation ist "die Datenbank und `var/storage`, `settings/override` und
`settings/siteaccess`"
([Container](../hosting/modelle/container-docker-und-podman.md#volumes-was-erhalten-bleiben-muss)). Sichern Sie bei SQLite die
Datenbank konsistent, statt eine Datei mitten in einem Schreibvorgang zu kopieren; die Methoden stehen unter
[SQLite für Hoster: Sicherung und Wiederherstellung](../hosting/grundlagen/sqlite-fuer-hoster.md#backup-und-wiederherstellung) und in
[Kapitel 9.2](../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei).

## Wann Sie stattdessen einen Server anbieten

| Bedarf | Wählen Sie |
|---|---|
| Mehrere Webserver oder Replikate | PostgreSQL oder einen anderen Server |
| Replikation, Datenbankbenutzer und Berechtigungen | einen Server |
| Viele Redakteure, die den ganzen Tag große Änderungen veröffentlichen | einen Server |
| Alles andere auf einer Maschine | SQLite genügt |

Ein Bundle kann beides anbieten: SQLite als Standard, PostgreSQL als Option (`--db=pgsql`), mit dokumentiertem
Wechsel. [Die Datenbank-Engine wechseln](../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md) behandelt einen
späteren Umzug.

## Verwandte Seiten

- [Datenbankwahl für Bundles](datenbankwahl-fuer-bundles.md)
- [SQLite für Hoster](../hosting/grundlagen/sqlite-fuer-hoster.md)
- [Bundles für Schulung und Demo](bundles-fuer-schulung-und-demo.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
