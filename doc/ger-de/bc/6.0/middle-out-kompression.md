# Middle-Out: Kompression mit gemeinsamem Wörterbuch

Lesen Sie diese Seite, wenn Sie den Antwort-Cache von Velocity betreiben und überlegen, ob Sie `MiddleOutCompression`
einschalten sollen. Die Funktion wurde in Exponential 6.0.15 eingeführt, ist implementiert und getestet und ist
**standardmäßig ausgeschaltet**. Der größte Teil dieser Seite erklärt, warum sie ausgeschaltet ist und wann genau Sie
sie einschalten sollten. Für eine typische Website lautet die Antwort: Lassen Sie sie aus.

Der Name stammt von der Kompression in der Fernsehserie *Silicon Valley*, die Fiktion ist. Die Technik ist echt und
älter als die Serie: Sie ist die Idee hinter dem eingebauten Wörterbuch von Brotli und dem eingestellten SDCH, und
zlib unterstützt sie länger als beide.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue optionale Einstellung `MiddleOutCompression`, die Einträge des Antwort-Caches gegen ein gemeinsames Wörterbuch komprimiert speichert. |
| Wer betroffen ist | Niemand, solange Sie sie nicht einschalten. |
| Wie Sie es prüfen | `grep -n "MiddleOutCompression" settings/velocity.ini settings/override/velocity.ini.append.php` |
| Wie Sie entscheiden | Schalten Sie sie nur ein, wenn der Cache größer ist als der Speicher, den er hat, oder unkomprimierte Bodys enthält (siehe „Wann sich das Einschalten lohnt“). |

## Was sie tut

Das Fenster von gzip ist 32 KB groß und beginnt für jedes Dokument leer. Ein Seiten-Cache ist voller Dokumente, die
größtenteils identisch sind (derselbe Kopf, dieselbe Navigation, dieselbe Fußzeile, dieselben Asset-URLs), daher
zahlt die hundertste Seite den vollen Preis für einen Kopf, den auch die neunundneunzig davor enthielten.

Ein Wörterbuch ist ein Block von Bytes, auf den der Kompressor verweisen darf, bevor er sie gesehen hat. Geben Sie ihm
das gemeinsame Markup der Website, und jedes Dokument wird gegen die ganze Website komprimiert statt nur gegen sich
selbst.

Gemessen an den eigenen 273 gecachten Seiten einer Installation:

```
plain gzip        1,626,195 bytes
with dictionary   1,290,140 bytes
saved               336,055 bytes   20.7% smaller
round-tripped       273 of 273 exactly
dictionary           32 KB, built in 140 ms
```

Bei einem synthetischen Korpus mit einem großen gemeinsamen Rahmen erreicht sie 80,4 %. Bei Dokumenten, die nichts
gemeinsam haben, meldet sie 1,00× und behauptet keinen Gewinn.

## Warum sie ausgeschaltet ist

Gegen diesen Cache eingeschaltet, komprimierte sie **1 von 272 Einträgen**.

Der Cache speichert Bodys in **Übertragungsform**: absichtlich bereits gezippt, sodass ein Treffer überhaupt keine
Kompressionsarbeit braucht. Ein Wörterbuch kann die Ausgabe von gzip nicht verkleinern. Die 20,7 % oben sind das, was
sie beim *reinen* HTML spart.

Um das zu nutzen, muss reines HTML gespeichert und beim Ausliefern komprimiert werden. Gemessen:

```
storage saved                      377,991 bytes  (23.5%)
decompress with the dictionary       0.080 ms
re-gzip at level 6                   0.533 ms
total added per hit                  0.612 ms

a cached hit: 0.36 ms  ->  0.97 ms
memory saved across the whole cache: 369 KB
```

Das sind 369 KB, erkauft mit 0,61 ms bei jeder Anfrage, auf einer Maschine mit 24 GB freiem Speicher: Etwas
Reichliches wird gegen etwas Knappes getauscht, die falsche Richtung.

Beachten Sie, wo die Kosten liegen: **Das Dekomprimieren dauert 0,080 ms, das erneute gzip 0,533 ms**, 6,7-mal mehr.
Nicht das Wörterbuch macht das teuer.

## Wann sich das Einschalten lohnt

### 1. Ein Cache, der größer ist als der ihm verfügbare Speicher

Dafür ist sie eigentlich gedacht. Die Wahl ist dann nicht „Übertragungsform oder Wörterbuchform“, sondern
„Wörterbuchform im Arbeitsspeicher oder Übertragungsform auf der Festplatte“, und ein Lesen von der Festplatte kostet
weit mehr als 0,61 ms, während ein vollständiges Rendern 1382 ms kostet.

```
against a disk read:   WORTH IT     0.61 ms added, 1.64 ms saved
against a render:      WORTH IT     by 2,258x
```

Wie viele Seiten mehr derselbe Speicher hält:

| Speicher | Übertragungsform | Mit Wörterbuch | Zusätzlich |
|---|---|---|---|
| 64 MB | 11.302 | 14.772 | +3.470 |
| 256 MB | 45.208 | 59.089 | +13.881 |
| 1 GB | 180.835 | 236.356 | +55.521 |
| 4 GB | 723.340 | 945.427 | +222.087 |
| 16 GB | 2.893.362 | 3.781.711 | +888.349 |

**Die Regel: Sie lohnt sich genau dann, wenn die zusätzlichen Seiten, die sie im Speicher hält, Seiten sind, die
sonst verdrängt worden wären.** Die gemessene Installation hielt 271 Seiten in 1,5 MB und verdrängte nichts, daher
brachte der zusätzliche Platz nichts.

### 2. Compression Dictionary Transport: Wörterbücher bei der Übertragung

Seit 2024 gibt es einen echten Standard, um eine mit Wörterbuch komprimierte Antwort an einen Browser zu senden. Der
Server bietet mit `Use-As-Dictionary` ein Wörterbuch an, der Client bietet es mit `Available-Dictionary` zurück an,
und die Antwort trägt `Content-Encoding: dcb` (Brotli) oder `dcz` (Zstandard). Chrome liefert das seit Version 130
aus.

Das wäre Middle-Out für das, was zum Besucher geht, nicht für das, was auf dem Server liegt, und die Gewinne auf einer
Website mit gemeinsamem Rahmen sind weit größer als 20 %, weil die zweite Seite des Besuchers gegen die erste
komprimiert wird.

**Es braucht Brotli oder Zstandard.** Die Kodierungen des Dictionary Transport sind nur für diese beiden definiert;
eine Entsprechung für gzip gibt es nicht. Auf der gemessenen Installation fehlten beide:

```
brotli (dcb): MISSING
zstd   (dcz): MISSING
```

Die Installation von `ext-brotli` oder `ext-zstd` schaltet das frei.

### 3. Ein Cache, der unkomprimierte Bodys enthält

Zum Beispiel ein API-Cache mit JSON, das an Clients ausgeliefert wird, die keine Kompression anfordern. Dort steht
kein gzip im Weg, daher wirkt das Wörterbuch direkt und kostet nur die 0,080 ms zum Rückgängigmachen.

## Konfiguration

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/velocity.ini` | `CacheSettings` | `MiddleOutCompression` | `disabled` | Installation |

Der Schlüssel stand früher in `[ServerSettings]`; ein Wert dort funktioniert weiterhin, und ein Wert in
`[CacheSettings]` hat Vorrang.

```ini
[CacheSettings]
MiddleOutCompression=enabled
```

**Bauen Sie das Wörterbuch, bevor Sie sie einschalten.** Ohne Wörterbuch werden Einträge auf die gewöhnliche Weise
gespeichert, und nichts geht kaputt. Das Wörterbuch ist die Datei `middle-out.dict` im Verzeichnis des Antwort-Caches.
Es wird aus Dokumenten gebaut, die der Cache hält, mit `Q_WebServer_MiddleOut::buildDictionary()` aus der
Velocity-Engine (`vendor/se7enxweb/exponential-velocity/src/Q/WebServer/MiddleOut.php`); Exponential liefert dafür
keinen Befehl mit. Installieren Sie ein Wörterbuch nur, wenn es reines gzip schlägt und alles zurückliest, was es
geschrieben hat; der für die obigen Messungen verwendete Builder lehnte jedes Wörterbuch ab, das eine der beiden
Prüfungen nicht bestand.

Ein Neubau des Wörterbuchs macht jeden Eintrag ungültig, der gegen das alte geschrieben wurde. Das ist sicher und
gewollt: Jede Nutzlast trägt eine CRC des Wörterbuchs, gegen das sie gebaut wurde, und das Dekomprimieren **lehnt ab,
statt zu raten**, wenn sie nicht übereinstimmen. Ein Eintrag, der sich nicht lesen lässt, ist ein Cache-Fehltreffer,
der langsam ist; einer, der gegen das falsche Wörterbuch gelesen würde, wäre eine beschädigte Seite.

Das Leeren des Caches (`clear()`) löscht das Wörterbuch nicht. Es liegt im Cache-Verzeichnis, ist aber kein Eintrag,
und es zu löschen würde jedes spätere Schreiben unkomprimiert lassen, bis es jemand bemerkt.

## Tests

Der Unit-Test der Engine deckt 29 Fälle ab:

```bash
php vendor/se7enxweb/exponential-velocity/tests/unit-middle-out.php
```

## Verwandte Seiten

- [Antwort-Cache und Navigation](../../../eng-us/bc/6.0/response-cache-and-navigation.md)
- [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [HTTP-Caching für anonyme Besucher](http-caching.md)
- [Velocity-Engines](velocity-engines.md)
