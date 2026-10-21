---
title: Überblick über Aktualisierungen für den öffentlichen Sektor
audience: government, admin, project-manager
category: public-sector
order: 20
summary: Die Aktualisierung einer Exponential-Website als Projekt des öffentlichen Sektors - welcher Aktualisierungsweg gilt, die Schritte von der Datensicherung bis zur Prüfung, was dem Änderungsbeirat vorzulegen ist und wer beteiligt werden sollte - aufbauend auf dem Leitfaden zum Aktualisieren und Kapitel 11 des Installationshandbuchs.
keywords: Aktualisierung, aktualisieren, Version, 6.0.x, Datenbankaktualisierung, dbupdate, Aktualisierungsweg, Projektplan, Änderungsbeirat, Datensicherung, Prüfung, Upgrade-Prüfung, öffentlicher Sektor, upgrade, upgrading, version, database update, upgrade path, project plan, change board, backup, verification, upgrade check, public sector
type: help
---
# Überblick über Aktualisierungen für den öffentlichen Sektor

Diese Seite richtet sich an Verantwortliche für Dienste, Projektleitungen und Administratoren, die die Aktualisierung
einer öffentlichen Website planen. Der technische Ablauf steht in [Aktualisieren](../../../../anleitungen/aktualisieren.md) und
[Kapitel 11 des Installationshandbuchs](../../../../installation/11-aktualisieren.md); diese Seite fasst ihn als Projekt mit
Freigaben, einem Wartungsfenster und Nachweisen.

## Inhalt
1. [Ihren Weg finden](#ihren-weg-finden)
2. [Die Schritte](#die-schritte)
3. [Wen Sie beteiligen](#wen-sie-beteiligen)
4. [Was der Änderungsbeirat sehen möchte](#was-der-änderungsbeirat-sehen-möchte)
5. [Häufige Überraschungen](#häufige-überraschungen)
6. [Verwandte Seiten](#verwandte-seiten)

## Ihren Weg finden

**Setup > Systeminformationen** nennt die Version; ebenso die Datenbankzeile `ezpublish-version` in `ezsite_data`
(der Name behält seine historische Schreibweise).

| Sie betreiben | Weg (aus dem Leitfaden zum Aktualisieren) |
|---|---|
| 3.10 bis 5.3 | Teil A (die alte Kette von Aktualisierungsdateien, eine pro Schritt), danach Teil B |
| 5.4 oder 5.90 | Teil B: auf 6.0.0, PHP 8 |
| 6.0.0 bis 6.0.14 | Teil C: neuer Code, `dbupdate-6.0.0-6.0.15.sql`, die Checklisten jedes Zeitraums |
| LoveStack | `./console exp:lovestack check`, danach Teil C, danach `exp:lovestack migrate` |

Exponential setzt die Codebasis von eZ Publish fort, die ihr Urheber nach fast 18 Jahren eingestellt hat; Websites auf
diesen alten Versionen folgen Teil A. Migrationen von anderen Systemen stehen in
[den Kapiteln 14 bis 17](../../../../installation/README.md).

## Die Schritte

1. **Sichern** Sie Datenbank und Dateien, und testen Sie die Wiederherstellung auf einer Wegwerfmaschine
   ([Aktualisieren, Schritt 1](../../../../anleitungen/aktualisieren.md#schritt-1-immer-eine-datensicherung)).
2. **Proben Sie auf dem Staging** mit einer Kopie der Produktion ([Staging und Freigabe](staging-und-freigabe.md)).
3. **Nachweise vorher**: **Setup > Upgrade-Prüfung** (Datei- und Datenbankkonsistenz), Bericht der
   Systeminformationen ([Upgrade-Prüfung vorher und nachher](upgrade-pruefung-vorher-und-nachher.md)).
4. **Öffnen Sie das Wartungsfenster** ([Wartungsfenster](wartungsfenster.md)).
5. **Neuer Code**, danach:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

6. **Datenbankaktualisierung** für Ihre Engine, einmal, vollständig, in der richtigen Reihenfolge, zum Beispiel
   `update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql`; danach die Skripte, die der Leitfaden aufführt
   (`createaudittables.php`, `movetrashrecords.php`).
7. **Erweiterungen**: `./console exp:checkclasses --allow-root-user` meldet Klassen, die PHP nicht laden will.
8. **PHP-FPM neu laden oder Velocity bereitstellen**; leeren Sie die Antwort-Caches nach dem Neuladen.
9. **Nachweise nachher** und Smoke-Tests: Startseite, Anmeldung, ein Testelement veröffentlichen, erneut die
   Upgrade-Prüfung.
10. **Schließen Sie das Wartungsfenster** und halten Sie die nun laufende Version fest.

## Wen Sie beteiligen

| Wer | Warum |
|---|---|
| Verantwortliche für den Dienst | akzeptieren das Risiko und das Wartungsfenster |
| Change-Manager | der Änderungsdatensatz und der Beirat |
| Webteam und wichtige Redakteurinnen und Redakteure | testen Bearbeiten und Veröffentlichen auf dem Staging |
| Sicherheitsbeauftragte | sicherheitsrelevante Änderungen in den Versionshinweisen, neue Standardwerte |
| Hosting-Anbieter oder Infrastrukturteam | PHP, Datenbankserver, Datensicherungen, Wartungsfenster |
| Datenschutzbeauftragte | wenn das Staging eine Kopie der Produktionsdaten erhält |

## Was der Änderungsbeirat sehen möchte

Die Versionshinweise, die Sie gelesen haben ([Versionshinweise lesen](versionshinweise-lesen.md)), das Ergebnis des
Stagings, den Plan mit Zeiten, den Rücknahmeplan ([Rücknahme planen](planung-der-ruecknahme.md)), die Kommunikation an die
Öffentlichkeit und die Nachweise, die Sie sammeln werden. Die Vorlage steht unter
[Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md#eine-vorlage-für-den-änderungsdatensatz).

## Häufige Überraschungen

Der Leitfaden zum Aktualisieren nennt die drei Änderungen, auf die man am häufigsten stößt: Konten ohne eine Zeile in
`ezuser_setting` können sich nicht anmelden (August 2026); Overrides folgen `Priority` (Juli 2026);
Sicherheitsheader, Cookies mit `HttpOnly`/`SameSite` und eine strengere Prüfung hochgeladener Dateien sind
standardmäßig eingeschaltet (September und Oktober 2026), was wichtig ist, wenn ein anderer Host Ihre Administration
in einem Frame einbettet. Außerdem: Ein eindeutiger Index auf `ezuser.login` wird bei der Aktualisierung nicht
angelegt; prüfen Sie das mit `./console exp:user-login-check --allow-root-user`.

## Verwandte Seiten
- [Kunden-Websites aktualisieren (Hosting)](../../../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Aktualisierungen von PHP und Datenbank](php-und-datenbank-aktualisierungen.md)
- [Konsistenz nach der Aktualisierung prüfen](../../../../../eng-us/help/government/tasks/operations/check-upgrade-consistency.md)
- [Kurs für Website-Administratoren, Modul Aktualisierungen](../../../../../eng-us/help/trainers/site-administrators/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
