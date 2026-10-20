---
title: "Lesson 7.4: Several web nodes and clustering"
audience: trainer, hosting, admin
category: training
order: 74
summary: Learners decide what must be shared and what stays local when several web nodes serve one site, explain the DFS cluster file handler and session handlers, and plan cron, deploys, maintenance and logs for a cluster. 45 minutes.
keywords: hosting and operations, several web nodes, cluster, load balancer, eZDFSFileHandler, NFS, ezdfsfile, index_cluster.php, sessions, ezpSessionHandlerDB, sticky sessions, cron on one node, var not shared
type: help
---
# Lesson 7.4: Several web nodes and clustering

When one machine is not enough, or a plan promises that a node can fail without taking the site down, several web
nodes serve one site behind a load balancer. That changes where files, sessions and caches live and how cron, deploys
and logs work. This lesson is a design exercise built on the hosting pages; most training labs have no cluster to
practise on.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 7: Performance and scaling](README.md) |
| Duration | 45 minutes (20 presenting, 25 design exercise) |
| Level | Advanced |
| Audience | System administrators and solution architects |
| Prerequisites | [Lesson 7.3](valkey-cache.md); [Module 3](../module-03-databases/README.md) |

## Learning objectives

After this lesson, learners can:

- list what is shared between nodes (database, uploaded files, sessions) and what stays local (caches, logs);
- explain why SQLite cannot be the database of a multi-node site;
- describe the DFS cluster: an NFS export, the `ezdfsfile` metadata table, local cache copies, and var directories
  that are never shared;
- choose between database sessions and sticky sessions;
- plan cron on one node, deploys on every node, maintenance and logs for a cluster.

## Trainer's notes

### How to present it

Source: [Several web nodes](../../../hosting/operations/several-web-nodes.md) and
[Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md).

1. **First question** (2 minutes). A single machine with enough CPU and memory is the simpler choice for most sites.
   Use several nodes when one machine is not enough or when a node must be able to fail.
2. **Shared and local** (5 minutes). Show the table of *Several web nodes*: database shared; `var/storage` shared through
   the DFS handler and an NFS mount; settings and code identical on each node; sessions shared or users kept on one
   node; caches local and kept in step; logs local; cron on one node.
3. **The database** (2 minutes). Every supported engine except SQLite supports several web servers on one database;
   SQLite's WAL needs every process on one machine.
4. **The DFS cluster** (5 minutes). `file.ini [ClusteringSettings] FileHandler=eZDFSFileHandler`; reference copies on
   an NFS export mounted at the same path on every node; metadata in `ezdfsfile`; cache files copied locally. The var
   directories "should in NO CASE be shared among instances". Cluster settings belong in the global override.
5. **Sessions** (3 minutes). `[Session] Handler=ezpSessionHandlerDB` puts sessions in the database for every node; or
   a load balancer keeps each user on one node by the session cookie, and a failed node signs its users out.
6. **Operations** (3 minutes). One crontab on one node; deploy on every node; a shared pause directory; each node keeps
   its own `var/log`, so look on the right node or collect logs centrally.

### Common questions

**Can I just put `var/` on NFS for every node?** No. The DFS handler shares only what must be shared and keeps each
node's var directory in step.

**Which databases can hold the cluster's metadata table?** MySQL and MariaDB through the kernel's backend; PostgreSQL
through the package `ezpostgresqlcluster`; Oracle through the `ezoracle` extension.

**Do the caches need Valkey?** Not necessarily; the cluster handler keeps caches in step. A shared cache backend such
as the Valkey extension is another option (lesson 7.3).

**What about the load balancer's health check?** See lesson 5.1: a node in maintenance or with a stopped engine must be
taken out.

### Pitfalls

- **Teaching clustering as the default.** It adds a mount and a database to run, back up and monitor.
- **Cron on every node.** Script locks are local to a node; two nodes run the same job twice.
- **Promising a design without a test.** Every design must name a test on staging before go-live.

## Hands-on exercise

A design exercise in groups of three. No cluster is needed.

1. Read the scenario: a customer's site must stay up when one of its web servers fails; it runs on MariaDB today and has
   many editors.
2. Draw the architecture: load balancer, two web nodes, database server, NFS export.
3. Fill in the table *What must be shared, what stays local* from
   [Several web nodes](../../../hosting/operations/several-web-nodes.md#what-must-be-shared-what-stays-local) with your
   concrete choices.
4. Choose the file handler and write the global override file name it goes in.
5. Choose the session handler and write one sentence on what happens to signed-in users when a node fails.
6. Decide which node runs the crontab and where the pause directory lives.
7. Write the deploy procedure in four lines: on which nodes, in which order, and when the response cache is cleared.
8. Name where an error reference is looked up when the customer cannot say which node answered.
9. Name the load balancer health check you would use and why.
10. Present the design in three minutes; the other groups ask one question each.

## Expected result

- Each design shares the database, `var/storage` through the DFS handler, and sessions (database or sticky), and keeps
  caches and logs local.
- The file handler is `eZDFSFileHandler`, configured in `settings/override/file.ini.append.php`.
- The crontab runs on one node; the pause directory is on shared storage.
- The deploy runs on every node and clears rendered-output caches after the new code runs.
- The trainer checks each design against the checklist of *Several web nodes*.

## Quiz

1. Why can SQLite not be the database of a site with two web nodes?
2. What must never be shared between the nodes of a DFS cluster?
   A. the NFS export B. the database C. the var directories D. the code release
3. Where is the metadata of clustered files kept?
4. Name the two ways to make sign-in work across nodes.
5. On how many nodes does the crontab run, and why?

<details>
<summary>Answers</summary>

1. WAL uses shared memory, so every process must be on one machine; a network file system is not a substitute.
2. **C.** The var directories "should in NO CASE be shared among instances"
   ([Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md#how-the-dfs-cluster-works)).
3. In the database table `ezdfsfile`.
4. Sessions in the database (`ezpSessionHandlerDB`), or a load balancer that keeps each user on one node.
5. One node; script locks are local to a node, so several nodes would run the same jobs twice.

</details>

## Further reading

- [Several web nodes](../../../hosting/operations/several-web-nodes.md)
- [Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md)
- [Sessions](../../../../guides/sessions.md): the session handlers.
- [Kubernetes](../../../hosting/models/kubernetes.md) and
  [Containers, Docker and Podman](../../../hosting/models/containers-docker-and-podman.md): nodes as containers.

## Related pages

- [Module 7: Performance and scaling](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
