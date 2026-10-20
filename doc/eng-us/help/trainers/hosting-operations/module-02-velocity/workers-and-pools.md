---
title: "Lesson 2.3: Workers and pools"
audience: trainer, hosting, admin
category: training
order: 23
summary: Learners size a Velocity worker pool, switch between a fixed and a dynamic pool, judge memory by PSS instead of RSS, and explain when a worker is replaced. 45 minutes.
keywords: hosting and operations, Velocity, workers, pool, SpareWorkers, IdleWorkerTimeout, Instances, zygote, PSS, RSS, persistent workers, KeepGlobals, maxRequests, requestTimeout
type: help
---
# Lesson 2.3: Workers and pools

The worker pool decides how many pages a Velocity site can render at once and how much memory it holds. Hosts that
copy a number from somewhere else, or judge memory by RSS, either overload a machine or waste it. This lesson teaches
the settings, the measured costs, and the method the documentation gives for finding the right number.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 2: Velocity](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate to advanced |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 2.2](keeping-velocity-running.md) |

## Learning objectives

After this lesson, learners can:

- set `Workers`, `SpareWorkers` and `IdleWorkerTimeout` for a fixed or a dynamic pool;
- explain why memory is judged by PSS or private memory, never by RSS;
- name four reasons a worker is replaced by a fresh fork;
- explain what persistent workers keep between requests and why `KeepGlobals` is not for request data;
- describe the method for finding the pool size of a hosting plan.

## Trainer's notes

### How to present it

Source: [Velocity workers and pools](../../../hosting/servers/velocity-workers-and-pools.md) and
[Chapter 8, section 8.3.6](../../../../install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote).

1. **The settings** (4 minutes). `Workers` (shipped 4, "explicit, always"), `SpareWorkers` (0 is a fixed pool),
   `IdleWorkerTimeout` (60 s), `Instances` (servers sharing the ports), `Zygote`, `ForkPerRequest` (`disabled`:
   persistent workers).
2. **What a worker costs** (4 minutes). Measured on 12 cores with PHP 8.5: about 10 to 11 MB of its own memory for a
   full Exponential, with a shared part of about 47 MB paid once. RSS counts every shared page once per worker and
   overstates a pool many times. Another measurement in the sources, on a test installation without warm-up, found
   far more per worker; tell learners to measure their own sites.
3. **Fixed or dynamic** (3 minutes). A fixed pool of 590 held 7,094 MB on one installation, the dynamic pool 626 MB.
   For a plan: `Workers` is the ceiling sold, `SpareWorkers` the floor the customer always has. Chapter 8's start for
   production: `Workers=64`, `SpareWorkers=8`, `IdleWorkerTimeout=60`.
4. **Replacement** (3 minutes). After `maxRequests` (1000), when the heap passes `workerMemoryCeiling`, with
   unbalanced output buffers, or when a request runs past `requestTimeout` (30 s, the client gets 504). A `POST` is
   never run twice.
5. **Persistent workers** (3 minutes). Measured on a signed-in administration page: 256 to 284 ms with a fresh worker
   per request, 73 to 80 ms with persistent workers. Four kernel registries are kept on purpose; keep nothing else,
   or one visitor's request leaks into the next.
6. **Finding the number** (3 minutes). Measure with `exp:benchmark --cold`, raise `Workers` step by step, tune to the
   throughput cliff, then use a dynamic pool (Module 7 practises it).

### Common questions

**What happens when every worker is busy?** The request waits in the parent; it is refused (`503`) only when
`maxConnections` (1024) is reached.

**Do more instances render pages faster?** No. Instances help cached pages, which one server answers bounded by one
core; rendered pages are bounded by the workers. Divide `Workers` by `Instances` to keep the same memory.

**What is the zygote?** A process forked before the first connection is accepted; later workers are forked from it
and inherit no visitor connections. It needs the `sockets` extension.

**Do event streams matter?** Each open stream holds one worker for its whole life; `StreamShare` and `MaxStreams` cap
them. Keep the cap well below `Workers`.

### Pitfalls

- **Monitoring that sums RSS.** It reports memory the machine does not use. Show the control panel's Worker Memory
  card, which shows PSS.
- **Copying the course's numbers.** Figures describe one site's templates on one machine. The exercise uses small
  numbers on purpose.
- **Editing `settings/velocity.ini`.** Use `config set`; the shipped file is replaced by updates.

## Hands-on exercise

Use your own training installation, served by Velocity with the `qbix` engine.

1. Show the current pool settings:

   ```bash
   php bin/php/console exp:velocity config list ServerSettings
   ```

2. Note `Workers`, `SpareWorkers`, `IdleWorkerTimeout` and `ForkPerRequest`.
3. Set a small dynamic pool:

   ```bash
   php bin/php/console exp:velocity config set ServerSettings Workers 8
   php bin/php/console exp:velocity config set ServerSettings SpareWorkers 2
   ```

4. Restart: `php bin/php/console exp:velocity restart`.
5. Open the dashboard address that `exp:velocity status` lists (`/Q/dashboard`) in a browser on the same machine and
   find the workers and their memory.
6. Generate a little load with ten parallel requests (replace the address):

   ```bash
   for i in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null http://127.0.0.1:8088/ & done; wait
   ```

7. Look at the dashboard again and note the number of workers.
8. Wait more than 60 seconds and look once more.
9. Put the settings back with `config unset` for both values and restart.

## Expected result

- Step 5 shows two idle workers right after the restart (the spare workers).
- Step 7 may show more workers, up to eight, if requests overlapped; step 8 shows extra workers retired after
  `IdleWorkerTimeout`.
- Step 9 leaves the shipped values; `config list` shows no `*` for the two settings.
- The trainer checks by asking learners to read the per-worker memory figure from the dashboard and say whether it is
  PSS.

## Quiz

1. Which setting turns a fixed pool into a dynamic pool?
   A. `Workers` B. `SpareWorkers` C. `Instances` D. `ForkPerRequest`
2. Why must a monitoring system not sum RSS for Velocity's workers?
3. A request runs for 45 seconds with the default engine settings. What does the client get?
4. What is `KeepGlobals` for, and what must never be added to it?
5. True or false: four instances render uncached pages four times faster than one.

<details>
<summary>Answers</summary>

1. **B.** Above 0, the pool keeps that many idle workers and grows up to `Workers`.
2. RSS counts every shared copy-on-write page once per worker and overstates the pool many times; judge PSS or private
   memory ([Velocity workers and pools](../../../hosting/servers/velocity-workers-and-pools.md#what-a-worker-costs)).
3. A `504`; the request is killed after `requestTimeout` (30 seconds).
4. For a global filled once by `include_once`; never a request-scoped global, which would leak one visitor's request
   into the next.
5. **False.** Rendered pages are bounded by the workers; instances help cached pages.

</details>

## Further reading

- [Velocity workers and pools](../../../hosting/servers/velocity-workers-and-pools.md): settings, costs and the
  sizing method.
- [Velocity worker pool specification](../../../../specifications/6.0/velocity-worker-pool.md): the measurements.
- [Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md): planning a machine.
- [Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md): measuring before you
  decide.

## Related pages

- [Module 2: Velocity](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
