# Datenbanken, die das MongoDB-Protokoll sprechen

Diese Seite richtet sich an Administratoren, die Exponential auf einer Datenbank betreiben wollen, die das
MongoDB-Wire-Protokoll spricht, aber nicht MongoDB ist, und an Entwickler, die an ihrem Treiber arbeiten: **FerretDB**,
**Amazon DocumentDB** und **Azure Cosmos DB for MongoDB** (Request Units und vCore). Ab Exponential 6.0.15 hat der
Kernel für jede von ihnen einen Treiber. Jeder ist der MongoDB-Adapter `expMongoDB` mit einem Profil dessen, was seine
Datenbank anders macht (`expMongoWireProfile`); der Kernel behandelt die Datenbank also als MongoDB.

**Stand.** Treiber und Profile sind nach der Dokumentation der Hersteller geschrieben und werden getestet: FerretDB 2
und der Emulator von Azure Cosmos DB laufen in der CI, für Amazon DocumentDB gibt es keinen Emulator, es ist nicht
getestet. Die erwartete Stufe jeder Datenbank steht in der Tabelle; sie wird mit den Tests nachgeführt.

## Inhalt

- [Die Datenbanken](#die-datenbanken)
- [Einstellungen](#einstellungen)
- [Was die Profile ändern](#was-die-profile-ändern)
- [Eine Site installieren](#eine-site-installieren)
- [Tests](#tests)

## Die Datenbanken

| Datenbank | DatabaseImplementation | Port | Erwartete Stufe | Getestet |
|---|---|---|---|---|
| MongoDB 4.0 und neuer | `mongodb` (`expMongoDB`) | 27017 | ganze Site | ja |
| FerretDB 2 (PostgreSQL mit der DocumentDB-Erweiterung) | `ferretdb` (`expFerretDB`) | 27017 | ganze Site | läuft |
| Amazon DocumentDB 5.0 | `documentdb` (`expDocumentDB`) | 27017, TLS | Datentabellen | nein (kein Emulator) |
| Azure Cosmos DB for MongoDB, Request Units (4.2) | `cosmosdb` (`expCosmosMongoDB`) | 10255, TLS | Datentabellen | läuft (Emulator) |
| Azure Cosmos DB for MongoDB vCore | `cosmosdbvcore` (`expCosmosVCoreDB`) | 10260, TLS | ganze Site | nein |

FerretDB 1 (über reines PostgreSQL oder SQLite) hat kein `$lookup` und kann keine Site tragen.

## Einstellungen

`site.ini [DatabaseSettings]` gibt Server, Port, User, Password und Database wie für MongoDB; ein leerer Port ist der
des Profils. `[MongoWireSettings]`:

| Einstellung | Bedeutung |
|---|---|
| `Profile` | leer: das Profil des Treibers; `mongodb`, `ferretdb`, `documentdb`, `cosmosdb` oder `cosmosdbvcore` |
| `TLS` | `enabled`, `disabled`; leer: wie das Profil (an für DocumentDB und Cosmos DB) |
| `TLSCAFile` | das CA-Bündel, z. B. Amazons `global-bundle.pem` für DocumentDB |
| `ReplicaSet` | leer: wie das Profil (`rs0` für DocumentDB); `none`: kein Replica Set |
| `RetryWrites` | `enabled`, `disabled`; leer: wie das Profil (aus für FerretDB, DocumentDB und Cosmos DB) |
| `AuthSource`, `ReadPreference` | werden in die Verbindungs-URI geschrieben, wenn gesetzt |
| `URIOptions[]` | weitere Optionen, je `name=wert`, z. B. `URIOptions[]=tlsAllowInvalidCertificates=true` für den Cosmos-DB-Emulator |

## Was die Profile ändern

- **Bitweise Aggregationsoperatoren.** Der Kernel filtert Sprachmasken mit `$bitAnd` (und die SQL-Übersetzung schreibt
  `$bitAnd`, `$bitOr`, `$bitXor`, `$bitNot`). MongoDB hat sie erst ab 6.3, FerretDB, DocumentDB und Cosmos DB gar
  nicht. Wo der Server sie nicht hat, schreibt der Treiber sie als Arithmetik (`$mod`, `$floor`, `$divide`), sobald
  eine Seite eine Konstante ist, was jede Maske des Kernels ist. Damit laufen auch MongoDB 4.0 bis 6.2, wie der
  Assistent verspricht.
- **Retryable Writes.** DocumentDB und Cosmos DB lehnen sie ab; der Treiber schreibt `retryWrites=false` in die URI.
- **TLS und das Replica Set** kommen aus Profil und Einstellungen in die URI.
- **Sortieren auf Cosmos DB.** Cosmos DB (Request Units) sortiert nur nach einem indizierten Feld: der Treiber
  indiziert jede Spalte jeder Tabelle, die er anlegt (`exp_sort_<spalte>`), solange die Collection leer ist, wie Cosmos
  DB es auch für eindeutige Indizes verlangt.
- **`$lookup` mit `let` und `pipeline`.** Der Kernel nutzt es an 19 Stellen. DocumentDB und Cosmos DB (Request Units)
  lehnen es ab; der Treiber schreibt eine Warnung mit der Tabelle. Deshalb sind beide auf der Stufe Datentabellen
  erwartet.
- **Transaktionen** nutzt der Adapter nicht; kein Profil hängt davon ab.
- **Index-Grenzen.** DocumentDB erlaubt Index-Schlüssel bis 2048 Byte und 64 Indizes je Collection.

## Eine Site installieren

Der Setup-Assistent listet die Datenbanken direkt nach MongoDB, wenn PHP die Erweiterung `mongodb` hat; der
Kickstarter nimmt `ferretdb`, `documentdb`, `cosmosdb` und `cosmosdbvcore` als `Type` von `[database_choice]`.

## Tests

- `php vendor/bin/phpunit --testsuite mongowire`: die Profile, die Arithmetik der bitweisen Operatoren gegen die von
  PHP, die Verbindungs-URI und die Sortier-Indizes, ohne Server.
- `mongowire-live`: gegen einen Server aus `EXP_MONGOWIRE_URI` mit `EXP_MONGOWIRE_PROFILE`.
