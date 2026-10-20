---
title: Kunden zu Ihrem Hosting migrieren
audience: hosting
category: hosting
order: 20
summary: Der durchgängige Ablauf, dem ein Hoster folgt, um die Website eines Kunden auf sein Exponential-Hosting zu holen - Fragen zur Bewertung, das Angebot, eine Staging-Kopie, die Umschaltung mit Wartungsmodus und DNS, Prüfung, Rückfall und Nachbetreuung.
keywords: Migration, Onboarding, Kunden-Website, Bewertung, Bestandsaufnahme, Staging, Umschaltung, DNS, Wartungsmodus, Rückfall, Prüfung, Nachbetreuung, Backup, Wiederherstellung, migration, onboarding, customer site, assessment, inventory, staging, cut-over, maintenance mode, rollback, verification, aftercare, restore
featured: true
type: help
---
# Kunden zu Ihrem Hosting migrieren

Diese Seite ist für das Migrationsteam eines Hosting-Anbieters: die Projektleitung, den Systemadministrator und den
Ansprechpartner im Support. Sie beantwortet: was Sie fragen, bevor Sie ein Angebot machen, wie Sie den Umzug in Staging
vorbereiten, wie Sie mit möglichst wenig Ausfallzeit umschalten, wie Sie prüfen und was gilt, wenn es schiefgeht.

## Inhalt

1. [Die Phasen](#die-phasen)
2. [Bewertung: was Sie fragen](#bewertung-was-sie-fragen)
3. [Das Angebot](#das-angebot)
4. [Staging-Kopie](#staging-kopie)
5. [Umschaltung](#umschaltung)
6. [Prüfung](#prüfung)
7. [Rückfall](#rückfall)
8. [Nachbetreuung](#nachbetreuung)
9. [Zuständigkeiten](#zuständigkeiten)

## Die Phasen

| Phase | Ergebnis |
|---|---|
| 1. Bewertung | Sie kennen Version, Größe, Datenbank, Erweiterungen und Risiken |
| 2. Angebot | ein Umfang, ein Plan und ein Datum, die der Kunde annimmt |
| 3. Staging | eine funktionierende Kopie auf Ihrer Plattform, vom Kunden geprüft |
| 4. Umschaltung | die Produktions-Website läuft auf Ihrer Plattform |
| 5. Prüfung | vereinbarte Prüfungen bestehen |
| 6. Nachbetreuung | der alte Hoster wird freigegeben; Backups und Überwachung laufen |

## Bewertung: was Sie fragen

| Frage | Warum sie zählt | Wie Sie es herausfinden |
|---|---|---|
| Welche Version läuft? | eine Website 6.0.x zieht um; eine ältere wird aktualisiert | die Seite Über (**Info > Über**) oder `composer show se7enxweb/exponential` |
| Welche Datenbank und Version? | entscheidet die Werkzeuge für Backup und Wiederherstellung; vielleicht ein Wechsel der Engine | `[DatabaseSettings]` in `settings/override/site.ini.append.php` ([Welche Datenbank nutzt meine Website?](../../datenbankunterstuetzung.md#welche-datenbank-verwendet-meine-site)) |
| Welche PHP-Version? | das Ziel muss sie anbieten | `php -v` beim alten Hoster |
| Welche Engine liefert sie aus? | Velocity, FrankenPHP, Apache oder nginx | der alte Hoster |
| Wie groß sind `var/storage` und die Datenbank? | Übertragungszeit, Größe des Tarifs | `du -s`, die Werkzeuge der Datenbank |
| Welche Erweiterungen, und sind sie in Git? | eigener Code zieht mit um | `settings/override/site.ini.append.php` `[ExtensionSettings]`, das Verzeichnis `extension/` |
| Geclusterter Speicher? | die Dateien liegen vielleicht in der Datenbank oder auf einem gemeinsamen Mount | `settings/override/file.ini.append.php` |
| Cronjobs, Mail, Suchserver? | sie müssen auch auf Ihrer Seite laufen | die alte Crontab, `[MailSettings]`, die Einrichtung der Suche |
| Domains, TLS, Weiterleitungen? | DNS und Zertifikate müssen bereit sein | der Kunde |

## Das Angebot

- Trennen Sie den **Umzug** (gleiche Version, neue Server) von einem **Upgrade oder Plattformwechsel**; sie unterscheiden
  sich im Aufwand bei Weitem. Siehe [Von älteren Exponential-Versionen](kunden-von-aelteren-versionen-migrieren.md) und
  [Von eZ Platform und Ibexa](kunden-von-ez-platform-und-ibexa-migrieren.md).
- Nennen Sie das Einfrieren der Inhalte: Zwischen der letzten Kopie und der Umschaltung darf nichts veröffentlicht werden.
- Nennen Sie, wer was auf der Staging-Kopie testet und wer sie abnimmt.

## Staging-Kopie

1. Holen Sie ein vollständiges Backup vom alten Hoster: die Datenbank und `var/storage`, `settings/override`,
   `settings/siteaccess` sowie eigene Erweiterungen und Designs ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)).
2. Holen Sie den Code aus Git oder Composer in derselben Version, statt das Vendor-Verzeichnis des alten Hosters zu kopieren
   ([Composer und Git auf Servern](../grundlagen/composer-und-git-auf-servern.md)).
3. Stellen Sie Datenbank und Dateien auf Ihrer Plattform wieder her ([Wiederherstellung und Wiederherstellungstests](../betrieb/wiederherstellung-und-wiederherstellungstests.md)).
4. Passen Sie die Einstellungen an, die den alten Hoster nennen: Datenbankverbindung, Adressen der Website in den
   Einstellungen der Siteaccesses, Pfade, Mailtransport.
5. Leeren Sie jeden Cache und erzeugen Sie die Autoloads neu:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

6. Geben Sie dem Kunden die Staging-Adresse und einen Testplan.

## Umschaltung

1. Senken Sie die Time-to-Live im DNS der Domains des Kunden Tage im Voraus.
2. Kündigen Sie das Fenster an. Schalten Sie beim alten Hoster den Wartungsmodus ein, damit sich nichts ändert:

   ```bash
   php bin/php/maintenance.php on --message="Moving to a new server" --until=2h --allow-root-user
   ```

   (`./console exp:maintenance on` ist dasselbe; siehe [Wartungsmodus](../betrieb/wartungsmodus.md)).
3. Erstellen Sie das letzte Backup von Datenbank und Dateien, übertragen Sie es und stellen Sie es auf Ihrer Plattform wieder her.
4. Leeren Sie die Caches; mit Velocity leeren Sie seinen Antwort-Cache (`php bin/php/console exp:velocity cache clear --allow-root-user`).
5. Testen Sie vor der DNS-Änderung auf Ihrer Plattform über einen Eintrag in der hosts-Datei.
6. Schalten Sie das DNS um. Lassen Sie den alten Hoster im Wartungsmodus, nicht gelöscht.

## Prüfung

Nutzen Sie die Prüfungen des Installationsbuchs: Vergleichen Sie die Zeilenzahlen von `ezcontentobject`,
`ezcontentobject_tree` und `ezcontentobject_attribute` mit der alten Website, melden Sie sich an, veröffentlichen Sie ein
Testobjekt, laden Sie ein Bild hoch, führen Sie eine Suche aus
([Datenbankunterstützung](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)). Gehen Sie dann die
[Checkliste für den Start](../checklisten/checkliste-fuer-den-start-einer-kunden-website.md) durch.

## Rückfall

Bis der alte Hoster freigegeben ist, heißt Rückfall: das DNS zurückschalten und den Wartungsmodus beim alten Hoster
ausschalten. Inhalte, die nach der Umschaltung beim neuen Hoster veröffentlicht wurden, gehen bei einem Rückfall verloren;
sagen Sie das im Plan, und entscheiden Sie vorab, wie lange die Möglichkeit zum Rückfall offen bleibt.

## Nachbetreuung

- Backups laufen auf Ihrer Plattform, und eines wurde wiederhergestellt.
- Überwachung und Log-Viewer werden beobachtet ([Überblick über die Überwachung](../betrieb/ueberblick-ueber-die-ueberwachung.md)).
- Cronjobs laufen ([Cronjobs](../betrieb/cronjobs.md)).
- Mail verlässt den Server mit dem richtigen Absender ([Mailzustellung](../betrieb/mailzustellung.md)).
- Der alte Hoster wird erst nach dem vereinbarten Zeitraum freigegeben.

## Zuständigkeiten

| Aufgabe | Hoster | Kunde |
|---|---|---|
| Backup vom alten Hoster | erhält es | stellt es bereit oder erlaubt den Zugriff |
| Code, Datenbank, Dateien auf der neuen Plattform | erledigt es | |
| Testplan und Abnahme | unterstützt | erledigt es |
| Einfrieren der Inhalte | kündigt es an | hält es ein |
| DNS-Änderung | erledigt oder erklärt es | erledigt es, wenn ihm das DNS gehört |
| Eigene Erweiterungen, die auf einer neuen PHP-Version scheitern | meldet es | behebt es oder beauftragt die Behebung |

## Verwandte Seiten

- [Eine Website zwischen Hostern umziehen](eine-website-zwischen-hostern-umziehen.md)
- [Die Datenbank-Engine wechseln](die-datenbank-engine-einer-kunden-website-wechseln.md)
- [Aus einem Backup wiederherstellen (Runbook)](../runbooks/aus-einem-backup-wiederherstellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
