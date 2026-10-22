---
title: Logs und Fehler lesen
audience: student
category: learning
order: 21
summary: Wie Lernende herausfinden, was auf einer Lernwebsite schiefgegangen ist - eine Seite zu fehlenden Rechten von einem echten Fehler unterscheiden, die Fehlerreferenz, die Logdateien in var/log und die Log-Anzeige, Syntaxprüfungen für PHP und Templates, bevor Sie im Browser nachsehen, und wie Sie aus einem Fehler eine gute Frage oder einen guten Fehlerbericht machen.
keywords: logs, fehler, error.log, var/log, log-anzeige, logdateien, fehlerreferenz, debug, php -l, eztemplatecheck, fehlerbehebung, weiße seite, 500, lernende, error, log viewer, error reference, troubleshooting, white page, learner
type: help
---
# Logs und Fehler lesen

Fehler gehören zum Lernen. Je früher Sie sie lesen können, desto schneller lernen Sie. Diese Seite zeigt, wo
Exponential festhält, was schiefgegangen ist, und wie Lernende das nutzen.

## Inhalt

1. [Ist es überhaupt ein Fehler?](#ist-es-überhaupt-ein-fehler)
2. [Die Fehlerseite und ihre Referenz](#die-fehlerseite-und-ihre-referenz)
3. [Die Logdateien](#die-logdateien)
4. [Die Log-Anzeige in der Administrationsoberfläche](#die-log-anzeige-in-der-administrationsoberfläche)
5. [Prüfen, bevor Sie nachsehen](#prüfen-bevor-sie-nachsehen)
6. [Ein Ablauf für jeden Fehler](#ein-ablauf-für-jeden-fehler)
7. [Vom Fehler zur Frage](#vom-fehler-zur-frage)

## Ist es überhaupt ein Fehler?

Manche Seiten sehen aus wie Fehler, sagen aber, dass etwas nicht erlaubt oder nicht vorhanden ist
([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#zuerst-ist-es-ein-fehler)):

| Die Seite sagt | Das bedeutet |
|---|---|
| *Die angeforderte Seite konnte nicht angezeigt werden. (1)* oder *Ihr Benutzerkonto hat nicht die notwendigen Privilegien um Zugriff auf die angeforderte Seite zu erhalten.* | Ihre Rolle darf diese Seite nicht öffnen |
| *Die angeforderte Seite konnte nicht angezeigt werden. (20)* | Die Adresse ist falsch, oder dieser Teil ist nicht installiert |
| *Möglicher Bearbeitungs-Konflikt* | Es gibt bereits einen Entwurf des Elements |
| Eine ausgegraute Schaltfläche | Sie dürfen sie hier oder jetzt nicht verwenden |

Auf Ihrer eigenen Lernwebsite als `admin` sollte die erste nicht erscheinen; für ein Testkonto ist sie oft das
erwartete Ergebnis einer Übung zu Rollen ([Projekt 9](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md)).

## Die Fehlerseite und ihre Referenz

Ein echter Fehler zeigt eine Seite wie **Bei uns ist etwas schiefgegangen** mit einer **Referenz** unten. Der Fehler
ist bereits protokolliert; mit der Referenz finden Sie diesen Zeitpunkt in den Logs
([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#wenn-wirklich-etwas-schiefgegangen-ist),
[Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).

Notieren Sie die Referenz, die Uhrzeit, die Adresse und was Sie unmittelbar davor getan haben.

## Die Logdateien

Die Logs liegen in `var/log/` der Installation. Diejenigen, denen Lernende am häufigsten begegnen:

| Datei | Was sie enthält | Quelle |
|---|---|---|
| `var/log/error.log` | Fehler; der erste Ort, an dem Sie nach einer weißen Seite oder einem Fehler 500 nachsehen | [Erste Schritte](../../anleitungen/erste-schritte.md#8-wenn-etwas-schiefgeht) |
| `var/log/warning.log` | Warnungen, zum Beispiel zu Caches | [Caches](../../anleitungen/caches.md#6-häufige-fragen) |
| `var/log/setup.log` | Der Fortschritt einer Installation | [Erste Schritte](../../anleitungen/erste-schritte.md#31-der-eine-befehl) |
| `var/log/security.log` | Sicherheitsereignisse, zum Beispiel Zeilen zum Demomodus | [Demomodus-FAQ](../demomodus-faq.md#woran-erkenne-ich-dass-er-eingeschaltet-ist) |

Auf der Kommandozeile die letzten Zeilen eines Logs:

```bash
tail -n 50 var/log/error.log
```

## Die Log-Anzeige in der Administrationsoberfläche

**Setup > Logdateien** zeigt die Logs im Browser: Wählen Sie eine Datei, verfolgen Sie neue Zeilen, während sie
geschrieben werden, filtern Sie nach Stufe, suchen Sie nach einem Wort wie einer Fehlerreferenz und öffnen Sie die
**Fehlerübersicht** für einen Überblick. Sie braucht die Richtlinie `setup/logs`; personenbezogene Daten wie
IP-Adressen werden standardmäßig maskiert ([Wie lese ich die Logdateien?](../handbuch/wie-lese-ich-die-logdateien.md)).

## Prüfen, bevor Sie nachsehen

Viele Fehler lassen sich abfangen, bevor Sie den Browser öffnen:

| Sie haben geändert | Prüfung | Erwartet | Quelle |
|---|---|---|---|
| Eine PHP-Datei | `php -l <file>` | `No syntax errors detected in <file>` | [Erweiterungen](../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand) |
| Ein Template | `php bin/php/eztemplatecheck.php -s<siteaccess> <file> --allow-root-user` | `Template file valid: <file>` | [Templates und Design](../../anleitungen/templates-und-design.md#5-die-syntax-prüfen-bevor-sie-in-den-browser-schauen) |
| Klassen einer Erweiterung | `php bin/php/checkclasses.php --quiet-ok --allow-root-user` | Keine Ausgabe, wenn alles geladen wird | [Erweiterungen](../../anleitungen/extensions.md#63-der-handler-assistent-schritt-für-schritt) |
| Eine Einstellung | `php bin/php/console exp:ini where <file/Block/Variable> <siteaccess>` | Der wirksame Wert | [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md#die-installation-befragen) |

Template-Pfadkommentare zeigen, welches Template welches Markup geschrieben hat
([Templates und Design](../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat)).

## Ein Ablauf für jeden Fehler

1. **Lesen Sie die Meldung.** Kopieren Sie sie genau in Ihr [Lerntagebuch](ein-lerntagebuch-fuehren.md).
2. **Lesen Sie das Log.** `var/log/error.log`, die neuesten Zeilen.
3. **Machen Sie die letzte Änderung gedanklich rückgängig**: Was haben Sie unmittelbar davor geändert? Die meisten
   Fehler kommen vom letzten Schritt.
4. **Führen Sie die Prüfungen** oben für die Dateien aus, die Sie angefasst haben.
5. **Schließen Sie Caches aus** ([Caches verstehen](caches-verstehen.md#cache-problem-oder-echter-fehler)).
6. **Durchsuchen Sie die Hilfe** nach der Meldung, dann die Issue-Tracker.
7. **Fragen Sie**, mit den Fakten, die Sie gesammelt haben.

## Vom Fehler zur Frage

Eine gute Frage oder ein guter Fehlerbericht enthält: was Sie getan haben, Schritt für Schritt; was Sie erwartet
haben; was passiert ist, mit der genauen Meldung; und die Umgebung (Versionen von Exponential und PHP, der Webserver,
die Datenbank). Die Seite **Fehler melden** der Hilfe und `php bin/php/console exp:info --plain --no-logo` liefern Ihnen
eine Zusammenfassung der Installation, die Passwörter, Schlüssel und Serverpfade weglässt
([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung)).

Siehe [Um Hilfe bitten und Lerngruppen](um-hilfe-bitten-und-lerngruppen.md) und
[Fehler, die Sie beim Lernen finden](fehler-die-sie-beim-lernen-finden.md).

## Verwandte Seiten

- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Caches verstehen](caches-verstehen.md)
- [Fehlerbehebung](../../installation/12-fehlerbehebung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
