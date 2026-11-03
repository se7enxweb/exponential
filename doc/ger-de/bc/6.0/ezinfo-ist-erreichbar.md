# ezinfo/is_alive antwortet "Exponential is alive" (6.0.15)

Lesen Sie diese Seite, wenn eine Überwachung, ein Load Balancer oder ein Skript `/ezinfo/is_alive` abruft und den Text
der Antwort vergleicht. Sie sagt, was sich geändert hat, wie Sie es prüfen und was zu tun ist.

## Was sich geändert hat

| Änderung | So prüfen Sie es | Was zu tun ist |
|---|---|---|
| Die Ansicht `ezinfo/is_alive` antwortet `Exponential is alive`, wenn die Datenbank verbunden ist. Sie antwortete `eZ Publish is alive`, mit dem alten Namen des Produkts. Ohne Datenbankverbindung antwortet sie weiterhin `No connection`; Statuscode (200) und Inhaltstyp (`text/plain`) sind unverändert. | `curl -s https://<ihr-host>/ezinfo/is_alive` gibt `Exponential is alive` aus. | Passen Sie jede Prüfung an, die nach dem Text `eZ Publish is alive` sucht: Prüfen Sie auf `is alive` oder `Exponential is alive`. Eine Prüfung, die nur den Statuscode testet oder `No connection` als Fehler erkennt, bleibt, wie sie ist. |

Sonst liest niemand diesen Text: Exponential Velocity prüft seine Worker und die Knoten seines Clusters über einen
eigenen Endpunkt, nicht über diese Ansicht.
