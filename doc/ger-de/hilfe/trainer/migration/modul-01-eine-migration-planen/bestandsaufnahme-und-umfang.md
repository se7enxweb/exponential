---
title: "Lektion 1.2: Bestandsaufnahme und Umfang"
audience: trainer, admin, developer, project-manager
category: training
order: 12
summary: Die Teilnehmenden machen vor einer Migration eine vollständige Bestandsaufnahme einer Website (Adressen, Inhaltstypen, Medien, Benutzer, Daten, die keine Inhalte sind, Integrationen, eigener Code, rechtliche Regeln) und legen den Umfang und die Verantwortlichen jedes Teils fest. 60 Minuten.
keywords: migration zu exponential, bestandsaufnahme, umfang, einfrieren der inhalte, adressliste, crawl, inhaltstypen, benutzer, personenbezogene daten, verantwortliche, migrations-checkliste, migrating to exponential, inventory, scope, content freeze, address list, content types, users, personal data, owners, migration checklist
type: help
---
# Lektion 1.2: Bestandsaufnahme und Umfang

Eine Migration kann nur umziehen, was jemand aufgelistet hat. Diese Lektion vermittelt die Bestandsaufnahme der
gemeinsamen Migrations-Checkliste und die daraus folgenden Entscheidungen zum Umfang, und die Teilnehmenden machen eine
echte Bestandsaufnahme ihrer Schulungsinstallation, als wäre sie die alte Website.

> **Hinweis**
> Verwenden Sie eine Schulungsinstallation, niemals eine Live-Website. Die Abfragen dieser Lektion lesen nur, aber die
> Gewohnheit, an einer Kopie zu arbeiten, beginnt hier.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 1: Eine Migration planen](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und Projektleiter |
| Voraussetzungen | [Lektion 1.1](einen-weg-waehlen.md); eine Schulungsinstallation mit Shell-Zugang |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die acht Teile einer Migrations-Bestandsaufnahme aus der gemeinsamen Checkliste aufzählen;
- erklären, warum zusätzlich zu einer Sitemap ein Crawl und das Server-Log nötig sind;
- auf einer Exponential-Installation die Inhalte pro Klasse und Sprache zählen;
- jedem Teil der Umfangstabelle eine Entscheidung und einen Verantwortlichen zuordnen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md), Abschnitte 1 und 2.

1. **Warum zuerst** (3 Minuten). Alles Spätere (Zuordnung, Weiterleitungen, Tests, das Zeitfenster der Umstellung)
   wird an der Bestandsaufnahme gemessen. Ein Teil, der darin fehlt, ist ein Teil, der nach dem Go-live gefunden wird.
2. **Die acht Teile** (8 Minuten). Gehen Sie Abschnitt 1 der Checkliste durch: jede Adresse, jeder Inhaltstyp mit
   Anzahlen pro Sprache, Medien, Benutzer und Gruppen, Daten, die keine Inhalte sind (Formulare, Kommentare,
   Bestellungen, Newsletter), Integrationen, eigener Code, Recht und Aufbewahrung.
3. **Adressen** (4 Minuten). Eine Sitemap übersieht verwaiste Seiten; das Zugriffs-Log der letzten Monate und die
   Liste der Adressen, auf die andere Websites verlinken, vervollständigen sie. Diese Liste steuert später die
   Weiterleitungen (Lektion 2.3).
4. **Umfang und Verantwortliche** (5 Minuten). Zeigen Sie die Tabelle in Abschnitt 2: Inhalte, Design, Adressen,
   Benutzer, Funktionen, Einfrieren. Jede Zeile braucht eine Entscheidung und einen namentlich genannten
   Verantwortlichen.

### Häufige Fragen

**Wie weit sollte das Zugriffs-Log zurückreichen?** Die Checkliste sagt „die letzten Monate“. Vereinbaren Sie mit dem
Website-Betreiber einen Zeitraum und schreiben Sie ihn in den Plan.

**Müssen wir jeden Benutzer umziehen?** Nein. Ob Konten umgezogen werden oder Benutzer sich neu registrieren müssen,
ist eine Entscheidung zum Umfang, die der Administrator zusammen mit dem Datenschutzbeauftragten trifft (Checkliste,
Abschnitt 2).

**Was ist mit personenbezogenen Daten, die niemand mehr braucht?** Die Checkliste rät, sie zurückzulassen: Ziehen Sie
nur um, was die neue Website braucht, und dokumentieren Sie die Rechtsgrundlage.

**Was, wenn die alte Website eine Exponential-Website ist?** Es gilt dieselbe Bestandsaufnahme; Kapitel 14.3 ergänzt
die Abfragen für Version, Datentypen, Passwort-Hash-Typen, Zeichensatz und Cluster-Modus (Lektion 4.1).

### Stolperfallen

- **Nur veröffentlichte Seiten zählen.** Fragen Sie auch nach Entwürfen, archivierten Elementen und anderen Sprachen,
  und entscheiden Sie dann.
- **Daten vergessen, die keine Inhalte sind.** Formulareinsendungen und Newsletter-Abonnenten sind oft die
  sensibelsten Daten einer Website. Listen Sie sie auf, auch wenn sie archiviert werden.
- **Eine Umfangstabelle ohne Namen.** „Das Team“ ist kein Verantwortlicher. Bestehen Sie auf einer Person pro Zeile.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, installiert mit `php bin/php/console exp:install --apply` wie in
[Installation über die Kommandozeile](../../../../installation/07-installation-ueber-die-konsole.md). Sie steht für „die alte Website“.
Die Standardinstallation verwendet SQLite mit der Datenbank in `var/storage/sqlite3/exponential.db`; öffnen Sie sie
nur lesend.

1. Kopieren Sie die Checkliste aus Abschnitt 1 von [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md)
   in eine Textdatei namens `inventory.md` in Ihren Notizen.
2. Öffnen Sie in einem Terminal im Wurzelverzeichnis der Installation die Datenbank nur lesend:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db
   ```

3. Zählen Sie die Inhaltsobjekte und die Knoten:

   ```sql
   SELECT COUNT(*) FROM ezcontentobject;
   SELECT COUNT(*) FROM ezcontentobject_tree;
   ```

4. Zählen Sie die Objekte pro Klasse:

   ```sql
   SELECT c.identifier, COUNT(*) FROM ezcontentobject o
     JOIN ezcontentclass c ON c.id = o.contentclass_id AND c.version = 0
    GROUP BY c.identifier ORDER BY 2 DESC;
   ```

5. Listen Sie die verwendeten Datentypen auf (die Abfrage aus [Kapitel 14.3](../../../../installation/14-migration-von-4x.md#143-eine-bestandsaufnahme-der-alten-website-machen)):

   ```sql
   SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute
    WHERE version = 0 GROUP BY data_type_string ORDER BY 2 DESC;
   ```

6. Zählen Sie die Benutzer nach Passwort-Hash-Typ:

   ```sql
   SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
   ```

7. Verlassen Sie die Datenbank mit `.quit`. Notieren Sie die Größe von `var/` mit `du -sh var/`.
8. Füllen Sie den Rest von `inventory.md` mit dem aus, was Sie über die Schulungswebsite wissen: Integrationen,
   eigener Code, rechtliche Regeln. Schreiben Sie „keine“, wo es nichts gibt; lassen Sie keine Zeile leer.
9. Kopieren Sie die Umfangstabelle aus Abschnitt 2 der Checkliste und tragen Sie für jede Zeile eine Entscheidung und
   einen Verantwortlichen ein (für die Übung eine Rolle).

## Erwartetes Ergebnis

- `inventory.md` enthält jeden Punkt aus Abschnitt 1 mit einer Zahl, einer Liste oder „keine“.
- Die Zählung der Klassen zeigt die Klassen des installierten Seitenpakets mit ihrer Anzahl an Objekten.
- Die Liste der Datentypen zeigt Kernel-Datentypen wie `ezstring` und `ezxmltext` und möglicherweise Datentypen aus
  Erweiterungen.
- Die Zählung der Hash-Typen zeigt die verwendeten Typen (Typ 7, `php_default`, ist der Standard von Exponential 6).
- Die Umfangstabelle hat in jeder Zeile eine Entscheidung und einen Verantwortlichen.
- Der Trainer prüft das, indem er jede Person nach drei Zahlen (Objekte, Knoten, Klassen) und einer Entscheidung zum
  Umfang mit Begründung fragt.

## Quiz

1. Warum reicht eine Sitemap als Liste der alten Adressen nicht aus?
2. Welcher dieser Punkte gehört **nicht** zur Bestandsaufnahme der gemeinsamen Checkliste?
   A. Integrationen B. Eigener Code C. Die Farbpalette des Designs D. Recht und Aufbewahrung
3. Wer entscheidet typischerweise, ob Benutzerkonten umgezogen werden? Nennen Sie die beiden Rollen aus der
   Umfangstabelle.
4. Richtig oder falsch: Personenbezogene Daten, die Sie nicht mehr brauchen, zieht man am besten trotzdem um, falls
   sie später gebraucht werden.
5. Was steuert die Bestandsaufnahme später im Projekt? Nennen Sie zwei Dinge.

<details>
<summary>Antworten</summary>

1. Eine Sitemap übersieht verwaiste Seiten; crawlen Sie die Website und nehmen Sie auch das Zugriffs-Log dazu
   ([Checkliste, Abschnitt 1](../../../../installation/migration/checkliste.md#1-bestandsaufnahme-wissen-was-sie-haben)).
2. **C.** Das Design ist eine Entscheidung zum Umfang (übernehmen, auffrischen oder neu), kein Punkt der
   Bestandsaufnahme
   ([Checkliste, Abschnitt 2](../../../../installation/migration/checkliste.md#2-umfang-und-verantwortliche-für-jeden-teil-festlegen)).
3. Der Administrator und der Datenschutz (Checkliste, Abschnitt 2).
4. **Falsch.** Man lässt sie am besten zurück; ziehen Sie nur um, was Sie brauchen (Checkliste, Abschnitte 1 und 11).
5. Zwei beliebige aus: die Weiterleitungen, die Zählungen nach jedem Probelauf, die Tests, die Entscheidungen zum
   Umfang
   ([Checkliste, Abschnitte 4, 6 und 7](../../../../installation/migration/checkliste.md)).

</details>

## Weiterführende Informationen

- [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md): die Abschnitte zu
  Bestandsaufnahme und Umfang vollständig.
- [Kapitel 14.3: Bestandsaufnahme der alten Website](../../../../installation/14-migration-von-4x.md#143-eine-bestandsaufnahme-der-alten-website-machen):
  die zusätzlichen Abfragen für eine alte Exponential-Website.
- [Kapitel 16.4: Bevor Sie beginnen](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen):
  die Bestandsaufnahme für eine Symfony-basierte Quelle.

## Verwandte Seiten

- [Modul 1: Eine Migration planen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
