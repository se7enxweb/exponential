# Wie die Arbeit gemacht wurde, und was als Nächstes kommt

Die LoveStack-Funktionen kamen in neun Phasen nach Exponential, in dieser Reihenfolge. Alle sind abgeschlossen und
Teil von Exponential 6.0.15.

| Phase | Name | Geliefert |
|---|---|---|
| 0 | Ausgangslage und die LoveStack-Prüfung | eine Testdatenbank in der Form von LoveStack (das Schema von Exponential plus die Schemaschritte 1 bis 4 von LoveStack), auf der dieselben Inhaltsoperationen ausgeführt wurden wie auf einer Exponential-Datenbank, mit denselben Ergebnissen; `./console exp:lovestack check` |
| 1 | Sicherheit und Sitzungen | L01–L06 und der Name der Einstellung `SameSite` von LoveStack: die Richtlinie für Knotendaten-Aufrufe, die Richtlinie für die Registrierung, ein Abmelden, das die Sitzung beendet, eindeutige Anmeldenamen mit einem Prüfbefehl, Bild-Uploads, die anhand ihres Inhalts geprüft werden |
| 2 | Diagnose | L07–L12: die Debug-Stufe und das Log für Deprecations, die Einstellung der Log-Größe, die Warnung für Template-Operatoren, die Bearbeitungs-URL für Templates, Fehlernummern der Datenbank, die Warnung vor „alles leeren“ |
| 3 | Inhalte und Administration | L13–L21: Audit von Änderungen an Orten, Site-Daten in Templates und der Administration, die Schaltfläche Kopieren, Pflicht-Alternativtext, getrimmte Textzeilen, Klassen zum Auflösen von Namensmustern, der Standard des Klassenfilters, zwei Template-Korrekturen |
| 4 | Rich-Text-Editor | L22–L24: versteckte Custom Tags, Bedienelemente für Custom Tags für TinyMCE 3, TinyMCE 8 und Tiptap, der Dialog sub/sup |
| 5 | APIs und Einstellungen für Entwickler | L25–L31: `eZURI::build()`, der Mail-Transport pro Nachricht, `rawurlencode`, Includes in Einstellungen, Header für Fehlerseiten, die Reihenfolge der Suche von ezjscore, der Design-Schlüssel `layout` |
| 6 | URLs und Suche | L32–L36: einmal dekodieren, das doppelte Anführungszeichen in IRI-Aliasen, die Begrenzung von Ganzzahlen in der Suche, die Indizierung von Relationen, der Cronjob hide und der Suchindex |
| 7 | PHP-8-Prüfung | die Korrekturen von LoveStack für PHP 7.2–8.3 unter PHP 8.5 und 8.1 nachgestellt, korrigiert, wo Exponential noch fehlschlug ([php8-pruefung.md](php8-pruefung.md)) |
| 8 | Migration, Tests und Dokumentation | `./console exp:lovestack migrate`; die eigenen Testfälle von LoveStack in die Suite übernommen ([zuordnung-der-tests.md](zuordnung-der-tests.md)); ein Test jeder Funktion und der Standardwerte, die eine aktualisierte Website behält; diese Seiten |

## Wie die Reihenfolge gewählt wurde

- Phase 0 zuerst: Sie zeigte, ob eine LoveStack-Datenbank irgendeine Änderung braucht (sie braucht keine, abgesehen
  von der Versionszeile, die `exp:lovestack migrate` einträgt).
- Phase 1 als Nächstes: Sicherheit.
- Die Phasen 2, 3 und 5 hingen nicht voneinander ab.
- Phase 4 baute auf der Arbeit am Rich-Text-Editor beim Ersatz von YUI auf, sodass das Editor-Plugin kein YUI braucht.
- Phase 6 kam unter den Funktionen als letzte: Sie ist die einzige, die URLs und Suchergebnisse ändern kann, daher
  bleiben ihre beiden URL-Optionen ausgeschaltet, sofern Sie sie nicht einschalten.

## Wie das Verhalten geschützt wird

- Eine Funktion, die das Verhalten einer vorhandenen Website ändern könnte, ist nach dem Upgrade ausgeschaltet oder
  wirkt nur bei neuen Installationen; der Überblick sagt, wie Sie sie einschalten.
- Jede Funktion hat Tests, die für sich laufen, und ein Test prüft die Standardwerte, die eine aktualisierte Website
  behält ([testen.md](testen.md)).
- Jede Funktion ist so gebaut, dass sie unter PHP-FPM und unter der Velocity-Engine gleich funktioniert, deren Worker
  zwischen Anfragen weiterleben: Nichts wird von einer Anfrage zur nächsten behalten.

## Was als Nächstes kommt

- Funktionen, die Mugo Web nach dem für diese Portierung verwendeten Commit zu LoveStack hinzufügt
  ([Quellenangabe](../lovestack.md#quellenangabe-woher-die-funktionen-stammen-und-wann)), können auf dieselbe Weise
  betrachtet werden: jede verglichen, neu gebaut, getestet und gewürdigt.
- Zwei kleine Punkte bleiben für ein späteres Release: ob ein Autor ohne E-Mail-Adresse akzeptiert werden soll
  (LoveStack akzeptiert ihn, Exponential lehnt ihn ab, siehe [php8-pruefung.md](php8-pruefung.md)), und ob die alten
  Testdatendateien das Ausführungsrecht verlieren sollen, das LoveStack ihnen entzogen hat (nur eine Änderung des
  Dateimodus).
- Der [Changelog](changelog.md) nennt das Release, das diese Funktionen enthält, sobald es erstellt ist.
