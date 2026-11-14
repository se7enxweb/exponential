---
title: Bild-Aliase
audience: admin, hosting
category: administration
order: 30
summary: Setup > Bild-Aliase zeigt, wie die Bild-Aliase der Website auf Abruf erzeugt werden, und steuert es: die Übersicht, Massenläufe zum Erzeugen, Neu erzeugen, Entfernen oder Abgleichen, die Testwerkzeuge für ein Bild und eine Staging-Quelle, der Verlauf und die Einstellungen, dazu das Menü Bild-Aliase im Inhaltsbaum und der Befehl exp:image:alias.
keywords: Bild-Aliase, auf Abruf, Übersicht, Massenläufe, Erzeugen, Neu erzeugen, Entfernen, Abgleich, Staging, Testwerkzeuge, Verlauf, Platzhalter, Austauschskript, Signaturschlüssel, Schlüssel wechseln, Vorab-Erzeugung, Kontextmenü, setup/aliases, setup/managealiases, imagealias.log, WebP, AVIF, MaxParallel, MaxWait, exp:image:alias, image aliases, on demand, dashboard, bulk runs, generate, regenerate, purge, resync, test tools, run history, placeholder, swap script, signing key, rotate, pre-generation, context menu
context: setup/aliases
type: help
---
# Bild-Aliase

Ein **Bild-Alias** ist eine Größe oder Variante eines hochgeladenen Bildes, die die Templates der Website zeigen: ein
Vorschaubild, ein Artikelbild, ein Banner in voller Breite. Seit Exponential 6.0.15 werden sie **auf Abruf erzeugt**:
Eine Seite listet ihre Bilder auf, ohne etwas umzuwandeln, und jeder Alias wird erzeugt, wenn ein Browser zum ersten
Mal nach ihm fragt, oder direkt nach dem Veröffentlichen im Hintergrund. Während ein Bild erzeugt wird, sehen Besucher
einen ruhigen grauen Platzhalter in der richtigen Größe, der einen Moment später durch das echte Bild ersetzt wird.

**Setup > Bild-Aliase** zeigt, wie das auf dieser Website funktioniert, und lässt Sie es steuern. Wie es im Einzelnen
funktioniert: [Auf Abruf erzeugte Bild-Aliase](../funktionen/6.0/bild-aliase-auf-abruf.md).

## Öffnen

Klicken Sie in der Administration im oberen Menü auf **Setup** und dann in der Gruppe System auf **Bild-Aliase**, direkt
nach **Cache** (die Adresse ist `https://www.example.com/admin/setup/aliases`). Die Seite zu sehen braucht die
Berechtigung `setup/aliases`; Läufe zu starten, Einstellungen zu speichern und jede andere Aktion braucht
`setup/managealiases`. Administratoren haben beide. Wer nur `setup/aliases` hat, sieht die Seite ohne ihre Formulare.

Die Seite hat fünf Teile, als Reiter oben.

## Übersicht

Was die Website gerade tut und was Aufmerksamkeit braucht:

- **Modus und Auslieferung**: bei Bedarf (der Standard) oder Render (der alte Weg: Bilder werden beim Erzeugen der
  Seite umgewandelt); die Adressform, wie vorhandene Bilder beantwortet werden, wie lange eine Anfrage warten darf
  (`MaxWait`), wie viele Umwandlungen gleichzeitig laufen und wie viele gerade laufen.
- **Platzhalter und Austauschskript**, **Formatauswahl** (WebP/AVIF), **Vorab-Erzeugung nach dem Veröffentlichen**
  (mit den Aliasen, die sie erzeugt, wenn nur einige gewählt sind) und die Zahl der Aliase.
- **Konverter der Formatvarianten**, in der Reihenfolge, in der sie versucht werden, mit der Angabe, ob jeder WebP
  und AVIF schreiben kann.
- **Umgebung und Quelle**: nur auf einer Entwicklungs- oder Staging-Website, die ihre Bilder von der Produktion holt.
  Zeigt, ob die Quelle antwortet, die Fehler in Folge, den letzten Fehler und ob sie pausiert ist. **Fehler
  zurücksetzen und die Quelle wieder fragen** beendet eine Pause.
- **Job-Warteschlange**: wartende, laufende und fehlgeschlagene Bild-Jobs und ob Massenläufe pausiert sind.
- **Die letzten 24 Stunden**: erzeugte Aliase, ausgelieferte Platzhalter, Fehler, geholte Dateien, gestartete Läufe.
  Jede Zahl öffnet die passenden Zeilen des Verlaufs.
- **Signaturschlüssel**: das Alter des Schlüssels, der Bildadressen signiert, und wie lange der vorherige noch
  akzeptiert wird. **Schlüssel wechseln** erzeugt einen neuen; Adressen auf Seiten, die mit dem alten Schlüssel
  zwischengespeichert sind, funktionieren während der Übergangszeit weiter (standardmäßig eine Woche). Wechseln Sie
  ihn, wenn Sie vermuten, dass der Schlüssel bekannt geworden ist.
- **Was Aufmerksamkeit braucht**: Prüfungen mit je einer kurzen Erklärung (zum Beispiel: keine Job-Warteschlange,
  eine Quelle pausiert, viele Fehler).

## Massenläufe

Viele Bilder auf einmal bearbeiten:

- **Erzeugen**: die fehlenden Aliase erzeugen.
- **Neu erzeugen**: die Aliase entfernen und wieder erzeugen (zum Beispiel nachdem die Einstellungen eines Alias
  geändert wurden).
- **Entfernen**: die Aliase löschen; die Originale bleiben, und Seiten erzeugen die Aliase auf Abruf neu.
- **Neu abgleichen** (nur auf einer Staging-Website): fehlende Originale und Aliase von der Quelle holen;
  **Abweichende Dateien überschreiben (Abgleich)** ersetzt auch Dateien, die abweichen.

Wählen Sie den **Bereich**: die ganze Website, einen Knoten oder einen Knoten und seinen Teilbaum; wahlweise Klassen,
Attribute, Aliase, **Geändert seit** (ein Datum oder „2 days“) und **Höchstens Bilder**. Kreuzen Sie zuerst
**Probelauf** an, um die Bilder zu zählen, ohne etwas zu ändern. **Lauf starten** reiht ihn als Job ein; seine
Fortschrittskarte aktualisiert sich alle zwei Sekunden und hat **Diesen Job abbrechen**. **Massenläufe pausieren**
hält jeden Massenlauf auf jedem Server nach seinem aktuellen Bild an; **Massenläufe fortsetzen** lässt sie dort
weitermachen, wo sie angehalten haben. Ohne Job-Warteschlange läuft ein Lauf von höchstens 50 Bildern in der Anfrage.

Dieselben Aktionen für eine Stelle im Inhaltsbaum: Klicken Sie mit der rechten Maustaste auf ein Element, dann
**Fortgeschritten > Bild-Aliase**, und erzeugen, erzeugen neu oder entfernen Sie die Aliase des Elements oder seines
ganzen Teilbaums. Sie laufen als Jobs.

## Testwerkzeuge

- **Ein Bild**: Suchen Sie ein Objekt nach Knoten- oder Objekt-ID. Für jedes Bildfeld und jede Sprache sehen Sie das
  Original und jeden Alias mit der Adresse, die ein Template jetzt bekommt, seiner Größe und seinem Typ, ob die Datei
  vorhanden ist oder auf Abruf erzeugt wird, und seinen WebP-/AVIF-Varianten. **Erzeugen** erzeugt einen Alias sofort
  und zeigt, wie lange es gedauert hat; **Prüfen** berichtet nur.
- **Verbindung zu einer Quelle** (Staging-Websites): fragt die Produktions-Website nach ihrer Basisadresse oder einem
  Pfad in der Ablage, mit gesendeten, aber nie angezeigten Zugangsdaten, und berichtet Status, Typ, Größe und Zeit.

## Verlauf

Die neuesten Zeilen von `imagealias.log`: was erzeugt, geholt, abgewiesen oder eingereiht wurde, mit der Zeit, die es
gedauert hat. Filtern Sie nach Ereignis (oder nur den Läufen), Lauf-ID, Alias oder einem Teil des Pfads, und wählen
Sie, wie viele Zeilen gezeigt werden. **Verfolgen** hält die Liste alle drei Sekunden aktuell. Eine Lauf-ID öffnet
die Zeilen dieses Laufs. Die ganze Datei steht auch unter Setup > Logdateien.

## Einstellungen

Jede Einstellung der Funktion, mit ihrem Hilfetext und Standard: der Modus, die Adressform, `MaxWait`, die
gleichzeitigen Umwandlungen (`MaxParallel`), die Formatauswahl, Platzhalter und Austauschskript, die Vorab-Erzeugung
und die Aliase, die sie erzeugt, sowie die Staging-Einstellungen mit Adresse und Zugangsdaten der Quelle.
**Änderungen vorab ansehen** zeigt, was das Speichern ändern würde; **Speichern** fragt „Bestätigen Sie, dass Sie es
sind“ und schreibt dann `settings/override/image.ini.append.php` (mit einer Sicherung). Kreuzen Sie „Hier gesetzt;
ankreuzen, um es zu entfernen“ an, um zum ausgelieferten Wert zurückzukehren. Zugangsdaten sind der **Name** eines
Geheimnisses aus Setup > Geheimnisse, nie ein Passwort selbst.

Um sofort zum alten Weg zurückzukehren, setzen Sie den Modus auf **render**. Nichts wird migriert; Sie können
jederzeit zurückschalten.

## Aus einer Shell

`./console exp:image:alias` tut alles, was die Seite tut:

```bash
./console exp:image:alias status
./console exp:image:alias generate --subtree=93 --dry-run
./console exp:image:alias regenerate --node=93 --alias=i160,i320 --queue
./console exp:image:alias test --node=93
./console exp:image:alias status --pause="nightly import"
```

Jeder Unterbefehl und jede Option: [Die Kommandozeile](../funktionen/6.0/bild-aliase-auf-abruf.md#die-kommandozeile-expimagealias).

## Wenn etwas falsch aussieht

| Sie sehen | Was zu tun ist |
|---|---|
| Graue Platzhalter, die bis zum Neuladen bleiben | Das Theme lädt das Austauschskript `exp_image_swap.js` nicht; fragen Sie Ihren Entwickler ([Platzhalter](../funktionen/6.0/bild-aliase-auf-abruf.md#platzhalter-und-das-austauschskript)). |
| Seiten mit vielen neuen Bildern laden langsam | Erhöhen Sie die gleichzeitigen Umwandlungen (`MaxParallel`, etwa die Hälfte der Prozessorkerne des Servers); lassen Sie die Vorab-Erzeugung an; setzen Sie WebP/AVIF nur auf große Aliase ([Abstimmung](../funktionen/6.0/bild-aliase-auf-abruf.md#leistung-und-abstimmung)). |
| Viele Bilder antworten mit „Too many requests“ (429) | Der Request Shield zählt Bildanfragen als Seitenaufrufe; geben Sie Bildern ein eigenes Budget ([Rezept](../beispiele/webserver/bild-aliase-auf-abruf.md#das-bildbudget-des-request-shield)). |
| Ein fehlendes Bild unter seiner Adresse in der Ablage ist eine 404-Seite | Die Webserver-Regeln für `index_image.php` fehlen ([Rezepte](../beispiele/webserver/bild-aliase-auf-abruf.md)). |
| Die Staging-Quelle ist pausiert | Sehen Sie sich den letzten Fehler in der Übersicht an, korrigieren Sie die Adresse oder die Zugangsdaten und setzen Sie dann die Fehler zurück. |
| Das Speichern der Einstellungen schlägt fehl | Die Einstellungsdatei gehört einem anderen Systembenutzer; bitten Sie Ihr Hosting, sie dem Benutzer des Webservers zu übergeben. |

Etwas anderes: [Wenn etwas schiefgeht](wenn-etwas-schiefgeht.md).
