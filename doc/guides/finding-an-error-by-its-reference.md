# Finding an error by its reference

This guide shows how to go from what a visitor reports ("the page said *Something went wrong*, reference
ERR-0123456789") to the log lines of that request, what else the request logged, and who did what at that moment. It
is for administrators and developers who answer such reports.

[Guides](README.md) · Related: [The log viewer](../features/6.0/log-viewer.md),
[Security and audit](security-and-audit.md), [System information](system-information.md)

## In short

- An error page shows a reference such as `ERR-0123456789`; the same reference is written to `error.log` beside the
  details of the failure.
- **Setup > Log files**, field **Error reference**: paste the reference and press **Search**. The page shows the entry
  and every line of the same request (same address and client, within two seconds) from all kernel logs.
- From there: **Audit of this minute** for who did what, the links to the node, object or template the lines name.
- The same from a shell: `./console exp:logs --reference=ERR-0123456789`.
- Personal data stays masked (e-mail and IP addresses, tokens); only a user with `setup/unmasklogs` can switch
  that off for themselves.

## 1. Get the reference

Ask for the reference the error page showed, and roughly when it happened. A reference is `ERR-` and ten digits or
letters A to F. Without one, note the time and the address of the page.

## 2. Find it

1. Sign in to the administration and open **Setup > Log files** (in the **System** group of the Setup menu).
2. In the search form, type the reference into **Error reference** and press **Search**. It is looked for in every
   kernel log and its rotations (`error.log`, `error.log.1` ... and the compressed `.gz` ones).
3. The entry is shown with its time, level, siteaccess, masked client address and request, and below it **Same
   request**: the warnings, notices and debug lines the request wrote, in time order.

Not found? The log may have rotated away (look at the oldest **Rotated archives** in the navigator and the
`site.ini [FileSettings] LogFileRotateCount` of the site), or the failure happened on another server: each server keeps
its own `var/log`.

Without a reference, search by time and address instead: **From** and **To** a few minutes around the report, the
address in **Request**, levels **Error** and **Warning**, scope **Every kernel log**.

## 3. Read the request

- The **source** of each entry (`eZTemplate`, `eZMySQLiDB`, `kernel/content/view.php` ...) says which part of the code
  wrote it.
- Node and object ids, class identifiers and template paths in the messages are links: open the content or the
  template the error was about.
- **Audit of this minute** opens the audit console at the same time: an edit, a publish or a settings change right
  before the error is often the cause.
- **Related lines** on any entry finds the rest of its request the same way.

## 4. From a shell

```
./console exp:logs --reference=ERR-0123456789
./console exp:logs --search=ERR-0123456789 --scope=kernel --rotations --json
./console exp:logs --level=error,warning --from='2026-10-17 01:00' --to='2026-10-17 01:10' --uri=/shop --scope=kernel
```

The first prints the entry, the lines of its request and the audit window; exit code 1 means it was not found.

## 5. Share what you found

Copy or download the lines with masking on (the default): the download of **Download range** is masked like the page
and compressed. Never paste unmasked lines into a ticket: they can hold customers' e-mail addresses and IP addresses.
