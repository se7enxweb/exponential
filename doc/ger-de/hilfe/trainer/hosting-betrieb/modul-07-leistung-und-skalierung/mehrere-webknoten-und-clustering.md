---
title: "Lektion 7.4: Mehrere Webknoten und Clustering"
audience: trainer, hosting, admin
category: training
order: 74
summary: Die Teilnehmenden entscheiden, was gemeinsam genutzt werden muss und was lokal bleibt, wenn mehrere Webknoten eine Website ausliefern, erklären den DFS-Cluster-Dateihandler und die Session-Handler und planen Cron, Bereitstellungen, Wartung und Logs für einen Cluster. 45 Minuten.
keywords: hosting und betrieb, mehrere webknoten, cluster, load balancer, eZDFSFileHandler, NFS, ezdfsfile, index_cluster.php, sitzungen, ezpSessionHandlerDB, sticky sessions, cron auf einem knoten, var nicht gemeinsam, hosting and operations, several web nodes, sessions, cron on one node, var not shared
type: help
---
# Lektion 7.4: Mehrere Webknoten und Clustering

Wenn ein Rechner nicht genügt oder ein Tarif verspricht, dass ein Knoten ausfallen kann, ohne die Website mitzureißen,
liefern mehrere Webknoten eine Website hinter einem Load Balancer aus. Das ändert, wo Dateien, Sitzungen und Caches
liegen und wie Cron, Bereitstellungen und Logs funktionieren. Diese Lektion ist eine Entwurfsübung auf Grundlage der
Seiten für Hoster; die meisten Schulungsumgebungen haben keinen Cluster zum Üben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 7: Leistung und Skalierung](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Entwurfsübung) |
| Niveau | Experten |
| Zielgruppe | Systemadministratoren und Lösungsarchitekten |
| Voraussetzungen | [Lektion 7.3](valkey-cache.md); [Modul 3](../modul-03-datenbanken/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- aufzählen, was zwischen Knoten gemeinsam genutzt wird (Datenbank, hochgeladene Dateien, Sitzungen) und was lokal
  bleibt (Caches, Logs);
- erklären, warum SQLite nicht die Datenbank einer Website mit mehreren Knoten sein kann;
- den DFS-Cluster beschreiben: ein NFS-Export, die Metadatentabelle `ezdfsfile`, lokale Cache-Kopien und
  var-Verzeichnisse, die nie gemeinsam genutzt werden;
- zwischen Datenbanksitzungen und Sticky Sessions wählen;
- Cron auf einem Knoten, Bereitstellungen auf jedem Knoten, Wartung und Logs für einen Cluster planen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md) und
[Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md).

1. **Die erste Frage** (2 Minuten). Ein einzelner Rechner mit genügend CPU und Speicher ist für die meisten Websites die
   einfachere Wahl. Verwenden Sie mehrere Knoten, wenn ein Rechner nicht genügt oder wenn ein Knoten ausfallen können
   muss.
2. **Gemeinsam und lokal** (5 Minuten). Zeigen Sie die Tabelle aus *Mehrere Webknoten*: Datenbank gemeinsam;
   `var/storage` gemeinsam über den DFS-Handler und einen NFS-Mount; Einstellungen und Code auf jedem Knoten identisch;
   Sitzungen gemeinsam, oder Benutzer bleiben auf einem Knoten; Caches lokal und gleichgehalten; Logs lokal; Cron auf
   einem Knoten.
3. **Die Datenbank** (2 Minuten). Jedes unterstützte Datenbanksystem außer SQLite unterstützt mehrere Webserver auf
   einer Datenbank; das WAL von SQLite braucht jeden Prozess auf einem Rechner.
4. **Der DFS-Cluster** (5 Minuten). `file.ini [ClusteringSettings] FileHandler=eZDFSFileHandler`; Referenzkopien auf
   einem NFS-Export, der auf jedem Knoten unter demselben Pfad eingehängt ist; Metadaten in `ezdfsfile`; Cache-Dateien
   werden lokal kopiert. Die var-Verzeichnisse „should in NO CASE be shared among instances“. Cluster-Einstellungen
   gehören in das globale Override.
5. **Sitzungen** (3 Minuten). `[Session] Handler=ezpSessionHandlerDB` legt Sitzungen für jeden Knoten in der Datenbank
   ab; oder ein Load Balancer hält jeden Benutzer anhand des Sitzungs-Cookies auf einem Knoten, und ein ausgefallener
   Knoten meldet seine Benutzer ab.
6. **Betrieb** (3 Minuten). Eine Crontab auf einem Knoten; Bereitstellung auf jedem Knoten; ein gemeinsames
   Pausenverzeichnis; jeder Knoten hat sein eigenes `var/log`, sehen Sie also auf dem richtigen Knoten nach oder sammeln
   Sie Logs zentral.

### Häufige Fragen

**Kann ich `var/` für jeden Knoten einfach auf NFS legen?** Nein. Der DFS-Handler teilt nur, was geteilt werden muss,
und hält das var-Verzeichnis jedes Knotens gleich.

**Welche Datenbanken können die Metadatentabelle des Clusters enthalten?** MySQL und MariaDB über das Backend des
Kernels; PostgreSQL über das Paket `ezpostgresqlcluster`; Oracle über die Erweiterung `ezoracle`.

**Brauchen die Caches Valkey?** Nicht unbedingt; der Cluster-Handler hält die Caches gleich. Ein gemeinsames
Cache-Backend wie die Valkey-Erweiterung ist eine weitere Möglichkeit (Lektion 7.3).

**Was ist mit der Zustandsprüfung des Load Balancers?** Siehe Lektion 5.1: Ein Knoten im Wartungsmodus oder mit einer
gestoppten Engine muss herausgenommen werden.

### Stolperfallen

- **Clustering als Standard lehren.** Es bringt einen Mount und eine Datenbank, die betrieben, gesichert und überwacht
  werden müssen.
- **Cron auf jedem Knoten.** Skriptsperren gelten nur auf einem Knoten; zwei Knoten führen dieselbe Aufgabe zweimal
  aus.
- **Einen Entwurf ohne Test versprechen.** Jeder Entwurf muss einen Test auf Staging vor dem Livegang nennen.

## Praktische Übung

Eine Entwurfsübung in Dreiergruppen. Es wird kein Cluster gebraucht.

1. Lesen Sie das Szenario: Die Website eines Kunden muss erreichbar bleiben, wenn einer ihrer Webserver ausfällt; sie
   läuft heute auf MariaDB und hat viele Redakteure.
2. Zeichnen Sie die Architektur: Load Balancer, zwei Webknoten, Datenbankserver, NFS-Export.
3. Füllen Sie die Tabelle *Was gemeinsam genutzt werden muss, was lokal bleibt* aus
   [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md#was-geteilt-werden-muss-was-lokal-bleibt) mit Ihren
   konkreten Entscheidungen aus.
4. Wählen Sie den Dateihandler und notieren Sie den Namen der globalen Override-Datei, in die er gehört.
5. Wählen Sie den Session-Handler und schreiben Sie einen Satz dazu, was mit angemeldeten Benutzern passiert, wenn ein
   Knoten ausfällt.
6. Legen Sie fest, welcher Knoten die Crontab ausführt und wo das Pausenverzeichnis liegt.
7. Schreiben Sie das Bereitstellungsverfahren in vier Zeilen: auf welchen Knoten, in welcher Reihenfolge und wann der
   Antwort-Cache geleert wird.
8. Nennen Sie, wo eine Fehlerreferenz nachgeschlagen wird, wenn der Kunde nicht sagen kann, welcher Knoten geantwortet
   hat.
9. Nennen Sie die Zustandsprüfung, die Sie für den Load Balancer verwenden würden, und warum.
10. Stellen Sie den Entwurf in drei Minuten vor; die anderen Gruppen stellen je eine Frage.

## Erwartetes Ergebnis

- Jeder Entwurf teilt die Datenbank, `var/storage` über den DFS-Handler und die Sitzungen (Datenbank oder Sticky) und
  hält Caches und Logs lokal.
- Der Dateihandler ist `eZDFSFileHandler`, konfiguriert in `settings/override/file.ini.append.php`.
- Die Crontab läuft auf einem Knoten; das Pausenverzeichnis liegt auf gemeinsamem Speicher.
- Die Bereitstellung läuft auf jedem Knoten und leert die Caches mit dargestellter Ausgabe, nachdem der neue Code läuft.
- Der Trainer prüft jeden Entwurf anhand der Checkliste aus *Mehrere Webknoten*.

## Quiz

1. Warum kann SQLite nicht die Datenbank einer Website mit zwei Webknoten sein?
2. Was darf zwischen den Knoten eines DFS-Clusters nie gemeinsam genutzt werden?
   A. der NFS-Export B. die Datenbank C. die var-Verzeichnisse D. das Code-Release
3. Wo werden die Metadaten der Cluster-Dateien abgelegt?
4. Nennen Sie die zwei Wege, auf denen die Anmeldung über Knoten hinweg funktioniert.
5. Auf wie vielen Knoten läuft die Crontab, und warum?

<details>
<summary>Antworten</summary>

1. WAL verwendet gemeinsamen Speicher, daher muss jeder Prozess auf einem Rechner laufen; ein Netzwerkdateisystem ist
   kein Ersatz.
2. **C.** Die var-Verzeichnisse „should in NO CASE be shared among instances“
   ([Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md#wie-der-dfs-cluster-funktioniert)).
3. In der Datenbanktabelle `ezdfsfile`.
4. Sitzungen in der Datenbank (`ezpSessionHandlerDB`) oder ein Load Balancer, der jeden Benutzer auf einem Knoten hält.
5. Auf einem Knoten; Skriptsperren gelten nur auf einem Knoten, daher würden mehrere Knoten dieselben Aufgaben zweimal
   ausführen.

</details>

## Weiterführende Informationen

- [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md)
- [Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Sitzungen](../../../../anleitungen/sitzungen.md): die Session-Handler.
- [Kubernetes](../../../hosting/modelle/kubernetes.md) und
  [Container, Docker und Podman](../../../hosting/modelle/container-docker-und-podman.md): Knoten als Container.

## Verwandte Seiten

- [Modul 7: Leistung und Skalierung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
