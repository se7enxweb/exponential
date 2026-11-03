# ezinfo/is_alive answers "Exponential is alive" (6.0.15)

Read this page if a monitoring check, a load balancer or a script requests `/ezinfo/is_alive` and compares the text of
the answer. It says what changed, how to check it and what to do.

## What changed

| Change | How to check | What to do |
|---|---|---|
| The view `ezinfo/is_alive` answers `Exponential is alive` when the database is connected. It answered `eZ Publish is alive`, the product's old name. Without a database connection it still answers `No connection`; the status code (200) and the content type (`text/plain`) are unchanged. | `curl -s https://<your-host>/ezinfo/is_alive` prints `Exponential is alive`. | Update every check that looks for the text `eZ Publish is alive`: match `is alive`, or `Exponential is alive`. A check that only tests the status code, or matches `No connection` as the failure, needs no change. |

Nothing else reads this text: Exponential Velocity checks its workers and its cluster peers on its own endpoint, not on
this view.
