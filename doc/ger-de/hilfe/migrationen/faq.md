---
title: "Migrationen: häufige Fragen"
audience: developer, admin, software-engineer, php-developer, hosting, dba, integrator, dxp-developer, project-manager
category: migrations
order: 31
summary: Kurze Antworten auf die Fragen, die zu Migrationen in Exponential 6.0.15 gestellt werden - wofür sie da sind, wie sie sich von Upgrades und Umzügen unterscheiden, wie Läufe, Sicherungspunkte, Rücknahme, Umgebungen, Teams, CI, Datenbanken, Erweiterungen und die Administration funktionieren - jeweils mit einem Link auf die Seite, die es erklärt.
keywords: Häufige Fragen, Fragen und Antworten, Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, Rücknahme, Sicherungspunkt, Umgebung, Erweiterung, Paket, Administration, migrations FAQ, frequently asked questions, migration, migrations, schema change, database change, deploy, rollback, caption, environment, CI, git, Doctrine, Kaliop, YAML, MongoDB, Oracle, MySQL, PostgreSQL, SQLite, extension, package, administration, questions, answers
featured: true
type: help
---
# Migrationen: häufige Fragen

## Inhalt

1. [Die Idee](#die-idee)
2. [Migrationen schreiben](#migrationen-schreiben)
3. [Migrationen ausführen](#migrationen-ausführen)
4. [Sicherheit und der Weg zurück](#sicherheit-und-der-weg-zurück)
5. [Teams, Umgebungen und Auslieferung](#teams-umgebungen-und-auslieferung)
6. [In der Administration](#in-der-administration)
7. [Datenbanken](#datenbanken)
8. [Umstieg von anderen Werkzeugen](#umstieg-von-anderen-werkzeugen)
9. [Verwandte Seiten](#verwandte-seiten)

## Die Idee

**Was ist eine Migration?**
Eine Änderung an einer Installation (eine Tabelle, eine Spalte, eine Inhaltsklasse, Inhalte, eine Rolle, eine
Einstellung) in einer PHP-Datei unter Versionskontrolle, einmal auf jede Installation einer Site in derselben
Reihenfolge angewendet, mit einer Aufzeichnung, was wo gelaufen ist. [Migrationen](README.md).

**Ist eine Migration dasselbe wie der Umzug meiner Site zu Exponential oder auf einen anderen Server?**
Nein. Migrationen ändern eine Site Schritt für Schritt; eine Site migrieren heißt, eine ganze Installation umzuziehen oder
zu aktualisieren. [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md).

**Warum nicht einfach SQL von Hand auf jedem Server ausführen?**
Weil nichts festhielte, welche Anweisungen wo gelaufen sind, ein Fehler auf der Produktion keinen automatischen Weg
zurück hätte und Staging und Produktion auseinanderliefen. Eine Migration läuft einmal je Installation, der Reihe nach,
nach einem Sicherungspunkt, und ihre Aufzeichnung wird in Git committet.
[Umstieg von SQL-Update-Dateien](rezepte/umstieg-von-sql-update-dateien.md).

**Wann nehme ich einen Upgrade-Schritt statt einer Migration?**
Für das, was jede Installation einer Erweiterung beim Upgrade braucht, einen Upgrade-Schritt von `exp:upgrade`; für eine
Änderung, die eine Site einmal macht, eine Migration. [Upgrade-Schritt oder Migration?](rezepte/upgrade-schritt-oder-migration.md).

**Brauche ich eine Migration für die Änderung eines Templates oder einer Einstellungsdatei?**
Nein: Templates und Einstellungsdateien sind Code und reisen mit Git. Eine Migration ist für das, was in der Datenbank
liegt (und für `settings/override`, das nicht in Git ist):
[INI-Einstellungen verschieben oder umbenennen](rezepte/ini-einstellungen-verschieben-oder-umbenennen.md).

## Migrationen schreiben

**Wie beginne ich eine neue?**
`./console exp:migration:generate --skeleton <name> --apply` schreibt `migrations/<zeitstempel>_<name>.php` mit dem Kopf,
`plan()`, `up()` und `down()`. [Das Tutorial](../../anleitungen/migrationen-tutorial.md).

**Kann ich Migrationen in YAML oder SQL schreiben?**
Ja: `.yml`-Dateien mit Schritten, `.sql`-Dateien (eine portable Datei oder eine Datei je Engine und eine
`.mongodb.json`) und `.ini`-Dateien für Einstellungen. [Migrationen: SQL und Einstellungen](../../anleitungen/migrationen-sql-und-einstellungen.md).

**Kann ich die PHP-API des Kernels in einer Migration nutzen?**
Ja: `eZContentFunctions`, `eZContentClass`, `eZRole`, `eZINI`, `expKernelTableSchema` und der Rest, immer mit
`$context->db()` für die Datenbank. [Migrationen in PHP schreiben](../php-entwickler/migrationen-in-php-schreiben.md).

**Darf ich eine Migration ändern, nachdem sie gelaufen ist?**
Nein. Ein Lauf lehnt eine Migration ab, deren Datei sich nach dem Anwenden geändert hat. Schreiben Sie eine neue
Migration für die neue Änderung; eine harmlose Bearbeitung (ein Kommentar) nehmen Sie mit `--force --reason=<text>` an.
[Eine Migration im Code-Review prüfen](rezepte/eine-migration-pruefen-im-code-review.md).

**Wie teilen zwei Migrationen einen Wert?**
`$context->setReference( 'name', $value )` in der einen, `$context->reference( 'name' )` in einer späteren desselben
Laufs. Über Läufe hinweg finden Sie den Wert in der Installation wieder (über die Remote-ID).
[PHP-Migrationen](../../anleitungen/migrationen-php.md).

**Braucht jede Migration ein down()?**
Nein. Lassen Sie es weg, wenn sich eine Änderung nicht von selbst zurücknehmen lässt (entfernte Inhalte); der
Sicherungspunkt vor dem Lauf ist dann der Weg zurück. [Ein umkehrbares down() schreiben](rezepte/ein-umkehrbares-down-schreiben.md).

## Migrationen ausführen

**Was tut `exp:migration:migrate --apply`, Schritt für Schritt?**
Es nimmt die Sperre, prüft, ob der Code committet ist, nimmt einen Sicherungspunkt oder verwendet einen wieder, führt die
ausstehenden Migrationen der Reihe nach aus (jede in einer Transaktion, wo die Datenbank eine hat), hält beim ersten
Fehler an und stellt wieder her, nimmt nach einem erfolgreichen Lauf einen Sicherungspunkt und committet, taggt und pusht
dann die Aufzeichnung. [Die Anleitung](../../anleitungen/migrationen.md#der-lebenszyklus).

**Kann ich zuerst sehen, was ein Lauf täte?**
Ja: `./console exp:migration:migrate` ohne `--apply` ist der Probelauf, und `./console exp:migration:dry-run --simulate
--diff` führt die Migrationen wirklich aus, in einer Transaktion, die zurückgerollt wird, auf PostgreSQL und SQLite.
[Migrationen testen](rezepte/migrationen-testen.md).

**Kann ich Migrationen in der Administration ausführen?**
Ja, unter Setup > Migrationen, mit der Richtlinie `migration/run`. Der Lauf geht im Hintergrund weiter, wenn Sie die
Seite schließen. [In der Administration](in-der-administration.md), [Migrationen für Administratoren](migrationen-fuer-administratoren.md).

**Unter welchem Benutzer läuft eine Migration?**
In der Administration unter dem angemeldeten Benutzer; auf der Konsole als `migration.ini [RunSettings] RunAsUser`
(`admin`) oder `--login=<login>`. Inhalte, die sie anlegt, gehören diesem Benutzer. [Einstellungen](einstellungen.md).

**Was geschieht mit den Upgrade-Schritten des Kernels?**
Sie werden zuerst gelistet, als Migrationen der Quelle `kernel`, und ein Lauf führt die ausstehenden der Stufen
`database` und `scripts` vor den Migrationen der Site aus; die anderen Stufen bleiben bei `exp:upgrade`.
[Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md#die-schritte-unter-den-migrationen).

**Eine Migration dauert eine Stunde. Läuft sie in ein Zeitlimit?**
Nicht auf der Konsole, die kein Zeitlimit hat. Schreiben Sie sie in Stapeln, die sich sicher erneut ausführen lassen, und
setzen Sie einen unterbrochenen Lauf mit `exp:migration:resume --apply` fort.
[Große Datenmigrationen](rezepte/grosse-datenmigrationen.md).

## Sicherheit und der Weg zurück

**Was ist ein Sicherungspunkt?**
Ein sicherer Stand der Installation (die Datenbank, der Speicher in `var/`, `settings/override`, die Commits des Codes),
genommen vor jedem Lauf und nach einem Fehler automatisch wiederhergestellt. Im Englischen „caption“.
[Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md).

**Wie nehme ich einen erfolgreichen Lauf zurück?**
`./console exp:migration:rollback <run-id> --apply` stellt seinen Sicherungspunkt von vorher wieder her;
`./console exp:migration:rollback <migration-id> --apply` führt das `down()` einer Migration aus.
[Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md).

**Verliert eine Rücknahme die Inhalte, die Redakteure nach dem Lauf geschrieben haben?**
Die Wiederherstellung eines Sicherungspunkts setzt die Datenbank auf den Zeitpunkt des Sicherungspunkts zurück, also ja,
für die Datenbank. Bevorzugen Sie auf einer Site, an der Redakteure arbeiten, das `down()` einer Migration oder eine neue
Migration. [Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md).

**Wer kann sehen, was wann gelaufen ist?**
Setup > Migrationen > Verlauf, die Tabellen `exp_migration` und `exp_migration_run`, die Statusdatei und das
Laufprotokoll in Git und die Audit-Ereignisse `system.migration.*`. [Die Audit-Spur der Läufe](rezepte/die-audit-spur-der-laeufe.md).

## Teams, Umgebungen und Auslieferung

**Zwei Entwickler haben gleichzeitig Migrationen angelegt. Was passiert?**
Beide laufen, in der Reihenfolge ihrer Zeitstempel; braucht die eine die andere, sagt `requires:` das. Eine doppelte ID
oder ein Zyklus hält jeden Lauf an, bis er behoben ist. [Teamarbeit mit Git](rezepte/teamarbeit-mit-git.md).

**Kann eine Migration nur auf Staging laufen, oder nie auf der Produktion?**
Ja: mit der Kopfzeile `environments: staging` oder `environments: !production`.
[Migrationen je Umgebung](rezepte/migrationen-je-umgebung.md).

**Wie führe ich Migrationen in meiner Deploy-Pipeline aus?**
Wenn der Code an Ort und Stelle ist: `./console exp:migration:migrate --apply`, dann `./console exp:velocity deploy`;
Pull-Requests sichern Sie mit `./console exp:migration:check` ab. [Mit Migrationen ausliefern](rezepte/mit-migrationen-ausliefern.md).

**Wohin geht die Aufzeichnung in Git, und wer pusht sie?**
Nach `migrations/status/<environment>.json` und `migrations/runs/<run-id>.log` im Repository der Site (oder einem eigenen),
committet, getaggt mit `migrations/<environment>/<run-id>` und vom Lauf selbst gepusht. [Tags pushen](rezepte/tags-pushen.md).

**Kann eine Erweiterung Migrationen mitbringen?**
Ja, in `extension/<name>/migrations/`; sie laufen, solange die Erweiterung aktiv ist. Pakete tragen sie in ihrem eigenen
Ordner `migrations/`. [Migrationen in einer Erweiterung oder einem Paket](rezepte/migrationen-in-einer-erweiterung-oder-einem-paket.md).

## In der Administration

**Wo fange ich in der Administration an?**
Unter **Setup > Migrationen**: seine Reiter sind Stand, Verlauf, Probelauf, Ausführen, Rücknahme, Aufzeichnung,
Gegenstellen, Auslieferungen und Redaktion. [Ein Rundgang durch Setup > Migrationen](rundgang-durch-setup-migrationen.md).

**Kann ich eine Migration ohne Shell erstellen?**
Ja: Zeichnen Sie Ihre Änderungen auf und drücken Sie **Migration erzeugen** ([Änderungen aufzeichnen](aufzeichnung.md)),
oder bauen Sie sie in der **Redaktion** ([Der Migrationseditor](der-migrationseditor.md)).

**Der Editor speichert meine Änderung an einer alten Migration nicht. Warum?**
Die Migration ist gelaufen; jede Installation, die sie angewendet hat, behält ihre Prüfsumme, und eine geänderte Datei
würde dort den nächsten Lauf anhalten. Schreiben Sie die Änderung als neue Migration.
[Eine Migration, die gelaufen ist](der-migrationseditor.md#eine-migration-die-gelaufen-ist).

**Wie lange bleibt eine Aufzeichnung für die ganze Website offen?**
Bis jemand sie schließt, oder so viele Minuten, wie unter **Von selbst schließen nach (Minuten, optional)** stehen
(höchstens 31 Tage). [Das Fenster für die ganze Website](aufzeichnung.md#das-fenster-für-die-ganze-website).

**Wie füge ich Staging oder Produktion als Ziel hinzu?**
**Setup > Migrationen > Gegenstellen > Neue Gegenstelle**: fünf Schritte, die Zugangsdaten als Name eines Geheimnisses.
[Eine Gegenstelle anlegen, Schritt für Schritt](eine-gegenstelle-anlegen-schritt-fuer-schritt.md).

**Steht ein Passwort in den Einstellungen der Gegenstelle?**
Nie. Die Einstellungen enthalten den Namen eines Geheimnisses aus Setup > Secrets (oder eine Umgebungsvariable); ein
in den Assistenten getipptes Passwort wird nicht geschrieben.
[Schritt 3: die Zugangsdaten](eine-gegenstelle-anlegen-schritt-fuer-schritt.md#schritt-3-die-zugangsdaten).

**Kann ich eine Auslieferung ausprobieren, ohne etwas zu ändern?**
Ja: **Nur Preflight** auf der Seite Auslieferungen prüft jede Gegenstelle und plant die Dateien, sendet aber nichts.
[Nur Preflight](ausliefern-schritt-fuer-schritt.md#nur-preflight).

**Meine Auslieferung wartet an einem Tor. Was nun?**
Es ist eine Auslieferung in Stufen: Prüfen Sie die ausgelieferte Stufe, dann **Freigeben** auf der Seite des Deploys.
[Deploys in Stufen und das Tor](ausliefern-schritt-fuer-schritt.md#deploys-in-stufen-und-das-tor).

**Was heißt „nicht im Gleichstand“?**
Eine Gegenstelle ist gescheitert, und nicht jede konnte zurückgenommen werden; die Gegenstellen unterscheiden sich
jetzt. [Im Gleichstand und nicht im Gleichstand](ausliefern-schritt-fuer-schritt.md#im-gleichstand-und-nicht-im-gleichstand).

**Unser Host bietet nur FTP. Können wir dorthin ausliefern?**
Mit **Nur übertragen** werden die Dateien gesendet und geprüft; damit sie dort auch laufen, richten Sie den
signierten HTTP-Trigger ein. [Hosts nur mit FTP und der Trigger](eine-gegenstelle-anlegen-schritt-fuer-schritt.md#hosts-nur-mit-ftp-und-der-trigger).

## Datenbanken

**Funktionieren Migrationen auf jeder Datenbank?**
Ja, mit Unterschieden: PostgreSQL und SQLite nehmen eine gescheiterte Migration vollständig zurück, MySQL/MariaDB und
Oracle ihre Daten, aber nicht ihre Schemaänderungen, MongoDB hat keine Transaktionen; der Sicherungspunkt deckt den Rest
ab (Oracle von Hand). [Hinweise zu den Datenbanken](rezepte/hinweise-zu-den-datenbanken.md).

**Wie füge ich auf jeder Engine eine Spalte hinzu?**
Über den Schema-Handler der Engine, wie es die eigenen Upgrade-Schritte des Kernels tun.
[Eine Spalte oder einen Index hinzufügen](rezepte/eine-spalte-oder-einen-index-hinzufuegen.md).

## Umstieg von anderen Werkzeugen

**Ich kenne Doctrine Migrations. Was ist anders?**
Migrationen ändern Inhalte ebenso wie das Schema, und jeder Lauf hat Sicherungspunkte, eine Aufzeichnung in Git und eine
Seite in der Administration. [Umstieg von Doctrine Migrations](rezepte/umstieg-von-doctrine-migrations.md),
[Exponential-Migrationen für Doctrine-Nutzer](../dxp-entwickler/exponential-migrationen-fuer-doctrine-nutzer.md).

**Wir haben auf eZ Platform das Kaliop-Migrations-Bundle genutzt. Können wir unser YAML umwandeln?**
Ja: `./console exp:migration:convert <Datei oder Ordner>` liest die YAML-Migrationen dieses Bundles und schreibt für
jede Datei eine YAML-Migration von Exponential; prüfen Sie jede, bevor sie läuft.
[Umstieg vom Kaliop-Migrations-Bundle](rezepte/umstieg-vom-kaliop-ezmigrationbundle.md),
[Migrationen des Referenz-Bundles umwandeln](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md#migrationen-des-referenz-bundles-umwandeln).

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](in-der-administration.md), [Befehle](befehle.md), [Einstellungen](einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Glossar](../../glossar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
