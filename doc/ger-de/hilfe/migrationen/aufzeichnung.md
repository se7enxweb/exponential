---
title: "Migrationen: Änderungen aufzeichnen"
audience: admin, developer
category: administration
order: 5
summary: Setup > Migrationen > Aufzeichnung in der Administration. Die eigenen Änderungen oder in einem websiteweiten Fenster jede Änderung der Website als Schritte einer Migration aufzeichnen; sehen, was eine Aufzeichnung erfasst hat und welche Migration sie ergäbe; Aufzeichnungen beenden und verwerfen; der Aufzeichnungsbalken.
keywords: Migrationen, Aufzeichnung, Änderungen aufzeichnen, Sitzungsaufzeichnung, websiteweite Aufzeichnung, Aufzeichnungsbalken, Migration erzeugen, migrations, recording, record changes, site-wide recording, exp:migration:record, migration/recording, migration/edit, Identität bestätigen, personenbezogene Daten
context: migration/recording
type: help
---
# Migrationen: Änderungen aufzeichnen

**Setup > Migrationen > Aufzeichnung** hält fest, was Sie in der Administration ändern, als Schritte einer Migration:
Inhalt, den Sie veröffentlichen, Klassen, die Sie speichern, Rollen, die Sie zuweisen, Einstellungen, die Sie
schreiben. Wenn Sie fertig sind, beenden Sie die Aufzeichnung, sehen sich an, was sie erfasst hat, und erzeugen eine
Migration, die dieselben Änderungen auf Staging und Produktion macht. Wie die Aufzeichnung arbeitet, ihre Befehle
und die Schritte, die sie aufzeichnet, stehen in der Anleitung
[Migrationen: Generatoren und das Aufzeichnen von Änderungen](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md).

**Wer diese Seite benutzen darf:** Benutzer mit der Richtlinie `migration/edit`. In einer neuen Installation hat sie
nur die Rolle Administrator. Das websiteweite Fenster zu öffnen fragt zuerst nach
[„Identität bestätigen“](../../anleitungen/identitaet-bestaetigen.md).

## Die eigenen Änderungen aufzeichnen

Die Karte **Meine Änderungen** zeichnet nur auf, was Sie ändern, wo immer Sie es ändern (hier, in der
Website-Seitenleiste oder in einem Befehl, der als Sie läuft), bis Sie sie beenden.

1. Geben Sie der Aufzeichnung auf Wunsch eine Bezeichnung (wofür sie ist) und wählen Sie
   **Aufzeichnung meiner Änderungen starten**.
2. Ein roter Balken **Aufzeichnung…** erscheint nun auf jeder Seite der Administration. Machen Sie Ihre Änderungen
   wie gewohnt.
3. Wählen Sie **Aufzeichnung beenden** im Balken oder auf dieser Seite.

Die Karte sagt dann, wie viele Änderungen die Aufzeichnung hat; **Zeigen, was erfasst wurde** öffnet sie.

## Das websiteweite Fenster

Die Karte **Websiteweites Fenster** zeichnet jede Änderung jedes Benutzers und der Konsole auf, bis es geschlossen
wird. Nutzen Sie es, wenn mehrere Personen gemeinsam ein Release vorbereiten. Solange es offen ist, sieht jeder
Benutzer der Administration den Aufzeichnungsbalken, weil auch seine Änderungen aufgezeichnet werden; Inhaber von
`migration/edit` können es im Balken oder auf dieser Seite schließen.

Es kann nur ein websiteweites Fenster und eine Aufzeichnung Ihrer eigenen Änderungen gleichzeitig laufen. Eine
Änderung, während beide laufen, steht in beiden.

## Was eine Aufzeichnung erfasst hat

Wählen Sie eine Aufzeichnung in der Liste (oder **Zeigen** im Balken). Die Seite hat drei Teile:

- **Die Migration, die sie ergäbe:** ein Schritt je geänderter Sache, in der Reihenfolge der ersten Änderung. Eine
  Sache, die angelegt und geändert wurde, ist ein Schritt, der sie mit ihren letzten Werten anlegt; eine Sache, die
  angelegt und wieder entfernt wurde, fällt weg. Hat die Installation die YAML-Schritttypen, die die Migration
  braucht, noch nicht, nennt ein Hinweis sie, und der Konsolenbefehl
  `./console exp:migration:generate --from-recording=<id>` listet dasselbe.
- **Was sie erfasst hat:** jede Änderung in ihrer Reihenfolge: die Zeit, die Änderung (das Audit-Ereignis), der
  Schritt, für den sie steht, und worum es geht, über Remote-ID oder Bezeichner, nie über eine Zahl, die sich
  zwischen Installationen unterscheidet.
- **Verwerfen:** entfernt die Aufzeichnung und was sie erfasst hat. Was geändert wurde, bleibt geändert. Haken Sie
  zuerst **Ja, diese Aufzeichnung verwerfen** an.

Die Seite listet die ersten 1000 Änderungen; `./console exp:migration:record:show <id>` listet jede.

## Was nie aufgezeichnet wird

- Das Konto eines Benutzers (Login, E-Mail-Adresse, Passwort) und die Felder von Benutzerkonten: eine Aufzeichnung
  hält nur fest, dass ein Benutzer angelegt oder geändert wurde, seine Gruppen und ob er aktiv ist.
  „Absichtlich weggelassen“ nennt, was weggelassen wurde.
- Der Wert einer Einstellung, deren Name sagt, dass sie ein Geheimnis ist.
- Wer eine Änderung gemacht hat, in der Migration: eine erzeugte Migration nennt niemanden.
- Lesen, Suchen, Anmelden und Caches leeren: sie ändern nichts.
- Die Änderungen eines Migrationslaufs: sie sind schon eine Migration.

## Wenn etwas schiefgeht

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| „Die Aufzeichnungstabellen gibt es noch nicht“ | Die Installation hat das Upgrade, das sie anlegt, noch nicht ausgeführt: `./console exp:upgrade --apply` (Schritt `6.0.15-migration-recording-tables`). |
| „Aufzeichnen ist in der migration.ini ausgeschaltet“ | `[RecordingSettings] Recording=disabled`; schalten Sie es in `settings/override/migration.ini.append.php` ein. |
| „Eine Aufzeichnung dieser Art läuft schon“ | Sie zeichnen schon auf, oder ein websiteweites Fenster ist offen. Beenden Sie es zuerst. |
| Der Balken erscheint, aber keine Aufzeichnung ist gelistet | Die Datei der laufenden Aufzeichnungen in `var/` ist veraltet: `./console exp:migration:record:status` schreibt sie neu. |
| Eine Aufzeichnung sagt, sie füge ab einer Zahl von Ereignissen nichts mehr hinzu | `[RecordingSettings] MaxEvents` ist erreicht. Beenden Sie sie und starten Sie eine neue. |

## Verwandte Seiten

- [Migrationen in der Administration](README.md)
- [Migrationen: Generatoren und das Aufzeichnen von Änderungen](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md)
- [Rezept: eine Sitzung in der Administration aufzeichnen](../../beispiele/kochbuch/migrationen-sitzung-aufzeichnen.md)
- [Die Ansicht migration/recording](../../beispiele/modulansichten/migration/recording.md) und [die Richtlinien des Moduls migration](../../beispiele/richtlinien/migration.md)
