---
title: Eine Lernwebsite mit einem Befehl installieren
audience: student
category: learning
order: 11
summary: Ein Gang für Lernende durch den dokumentierten Schnellstart - den Code mit Composer holen, mit exp:install auf SQLite installieren, die Website mit der Engine php ausliefern, sich anmelden, das Passwort ändern -, mit der erwarteten Ausgabe jedes Schritts und was zu tun ist, wenn ein Schritt fehlschlägt.
keywords: installieren, exp:install, ein befehl, composer create-project, sqlite, velocity, engine php, localhost, lernwebsite, schnellstart, zufälliges passwort, einsteiger, install, one command, php engine, study site, quick start, random password, beginner
type: help
---
# Eine Lernwebsite mit einem Befehl installieren

Diese Seite führt Sie durch den Schnellstart aus [Exponential 6.0 installieren](../../INSTALL.md#1-was-sie-erhalten-und-der-schnellstart)
und [Erste Schritte: Exponential installieren](../../anleitungen/erste-schritte.md), mit Hinweisen für Lernende. Sie
brauchen PHP und Composer und keinen Datenbankserver. Planen Sie fünfzehn Minuten ein; die Installation selbst dauert
laut der Anleitung für die ersten Schritte auf SQLite etwa eine Minute.

## Inhalt

1. [Ihr PHP prüfen](#ihr-php-prüfen)
2. [Die Dateien holen](#die-dateien-holen)
3. [Installieren](#installieren)
4. [Die Website ausliefern](#die-website-ausliefern)
5. [Anmelden und das Passwort ändern](#anmelden-und-das-passwort-ändern)
6. [Wenn ein Schritt fehlschlägt](#wenn-ein-schritt-fehlschlägt)

## Ihr PHP prüfen

```bash
php -v
```

Erwartet: eine Zeile, die mit `PHP 8.` beginnt. Exponential 6.0 läuft auf PHP 8.0 bis 8.5
([Anforderungen](../../INSTALL.md#2-voraussetzungen)). Setzen Sie `memory_limit` (mindestens `64M`) und `date.timezone`
in der `php.ini`, bevor Sie weitermachen.

## Die Dateien holen

Für eine Website, die Sie nur ausprobieren und studieren möchten, ist das Composer-Projekt der schnellste Weg:

```bash
composer create-project se7enxweb/exponential exponential
cd exponential
php bin/php/console --version
```

Erwartet: Die erste Zeile lautet `console (Exponential) 6.0.15stable` (oder die Version, die Sie installiert haben).
Schreiben Sie sie in Ihr [Lerntagebuch](ein-lerntagebuch-fuehren.md).

Die Konsole ist `bin/php/console`. Viele Installationen legen im Wurzelverzeichnis eine Abkürzung an
(`ln -s bin/php/console console`), sodass `./console` und `php bin/php/console` dasselbe Programm sind
([Exponential 6.0 installieren](../../INSTALL.md)). Dieser Ordner schreibt `php bin/php/console`, was überall
funktioniert. Für den Entwicklerpfad holen Sie die Dateien stattdessen als Git-Klone:
[Aus Git-Klonen zum Lernen installieren](aus-git-klonen-zum-lernen-installieren.md).

## Installieren

```bash
php bin/php/console exp:install --apply --random-password --url=http://localhost:8087
```

Was das bewirkt, wie die [Anleitung für die ersten Schritte](../../anleitungen/erste-schritte.md#31-der-eine-befehl) es
beschreibt:

- installiert das Multisite-Site-Paket auf SQLite, in `eng-US`;
- legt die Siteaccesses an, wobei die öffentliche Website und die Administrationsoberfläche am ersten Teil der Adresse
  unterschieden werden;
- erzeugt ein Administratorpasswort mit 24 Zeichen, zeigt es am Ende **einmal** an und schreibt es nach
  `var/log/initial-admin-password`;
- verweigert die Ausführung über einer bestehenden Installation, sofern Sie nicht `--force` hinzufügen.

Am Ende sehen Sie einen Kasten mit der Adresse der Website, der Anmeldeadresse der Administrationsoberfläche, dem
Benutzernamen `admin` und dem Passwort. Kopieren Sie das Passwort jetzt in einen Passwortmanager. Schreiben Sie es
nicht in Ihr Tagebuch und nicht in einen Commit.

Wenn Sie als `root` arbeiten (zum Beispiel auf einer virtuellen Maschine für Schulungen), verlangen Skripte, die es
brauchen, `--allow-root-user`.

## Die Website ausliefern

```bash
php bin/php/console exp:velocity start --engine=php
php bin/php/console exp:velocity status --engine=php
```

Die Engine `php` ist der eingebaute Server von PHP, für die Entwicklung; sie lauscht standardmäßig auf Port 8087,
weshalb die Installation `--url=http://localhost:8087` verwendet hat ([Erste Schritte](../../anleitungen/erste-schritte.md#4-die-site-ansehen)).

Öffnen Sie `http://localhost:8087/site/` in einem Browser. Sie sehen die Startseite Ihrer Lernwebsite. Beenden Sie den
Server mit `php bin/php/console exp:velocity stop --engine=php`, wenn Sie für den Tag fertig sind.

## Anmelden und das Passwort ändern

1. Öffnen Sie `http://localhost:8087/admin/user/login`.
2. Melden Sie sich als `admin` mit dem ausgegebenen Passwort an.
3. Verwenden Sie **Passwort ändern** im Benutzermenü und wählen Sie Ihr eigenes.
4. Löschen Sie danach `var/log/initial-admin-password`, wie es die Anleitung verlangt
   ([Erste Schritte](../../anleitungen/erste-schritte.md#5-erste-anmeldung)).

Sie sind jetzt auf dem Dashboard der Administrationsoberfläche. Machen Sie weiter mit
[Ihre erste Stunde in der Administrationsoberfläche](ihre-erste-stunde-in-der-administration.md).

## Wenn ein Schritt fehlschlägt

| Was Sie sehen | Was Sie tun | Quelle |
|---|---|---|
| `exp:install` meldet, dass eine Installation existiert | Fügen Sie `--force` nur hinzu, wenn Sie sie ersetzen wollen | [Erste Schritte](../../anleitungen/erste-schritte.md#8-wenn-etwas-schiefgeht) |
| Eine weiße Seite oder ein Fehler 500 | Lesen Sie `var/log/error.log`; prüfen Sie `php -v` und ob `vendor/` existiert | ebenda |
| Die Adresse antwortet nicht | Läuft der Server (`exp:velocity status --engine=php`)? Passt der Port zu `--url`? | ebenda |
| Sie haben das Passwort verloren | Sehen Sie in `var/log/initial-admin-password` nach, oder setzen Sie mit `php bin/php/resetuserpassword.php --allow-root-user -u admin -g` ein neues | ebenda |
| Eine Änderung wird nicht angezeigt | Leeren Sie den Inhalts-Cache: `php bin/php/ezcache.php --clear-tag=content --allow-root-user` | [Erste Schritte](../../anleitungen/erste-schritte.md#6-erster-inhalt) |
| Eine PHP-Erweiterung fehlt | `php bin/php/console exp:velocity ext check` listet auf, was fehlt | [Anforderungen](../../INSTALL.md#2-voraussetzungen) |

Kommen Sie immer noch nicht weiter? [Logs und Fehler lesen](logs-und-fehler-lesen.md) und
[Um Hilfe bitten und Lerngruppen](um-hilfe-bitten-und-lerngruppen.md).

## Verwandte Seiten

- [Ihre erste Website auf drei Wegen](ihre-erste-website-auf-drei-wegen.md)
- [Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)
- [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
