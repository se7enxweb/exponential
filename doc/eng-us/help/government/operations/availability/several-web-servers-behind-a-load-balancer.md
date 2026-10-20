---
title: Several web servers behind a load balancer
audience: government, hosting, admin
category: public-sector
order: 30
summary: For IT teams in public bodies that put an Exponential site on several web servers behind a central load balancer or reverse proxy. What is shared and what stays local, trusted proxies, cron on one node, and deploys, cache clears and maintenance on every node.
keywords: load balancer, reverse proxy, several web servers, web nodes, TrustedProxies, X-Forwarded-For, X-Forwarded-Proto, ClientIpByCustomHTTPHeader, cron on one node, deploy every node, central proxy, government network
type: help
---
# Several web servers behind a load balancer

This page is for infrastructure teams in public bodies that run an Exponential site on more than one web server, often
behind a load balancer that a central network team operates. It answers what must be shared between the servers,
what stays local, how to make Exponential trust the balancer, and what changes for cron, deploys, cache clears and
maintenance. The full hosting page is [Several web nodes](../../../hosting/operations/several-web-nodes.md).

## Contents

1. [Shared and local](#shared-and-local)
2. [Trusting the load balancer](#trusting-the-load-balancer)
3. [Cron on one node](#cron-on-one-node)
4. [Deploys and cache clears on every node](#deploys-and-cache-clears-on-every-node)
5. [Maintenance and logs](#maintenance-and-logs)
6. [Working with a central network team](#working-with-a-central-network-team)
7. [Checklist](#checklist)

## Shared and local

| Part of the site | Shared or local | How |
|---|---|---|
| Database | shared | a database server all nodes reach ([Database high availability](database-high-availability.md)) |
| Uploaded files | shared | the DFS cluster and an NFS mount ([Shared storage and the DFS cluster](shared-storage-and-the-dfs-cluster.md)) |
| Code, settings, extensions, designs | identical on every node | the same deployment on each |
| Sessions | shared, or one user kept on one node | [Sessions across servers](sessions-across-servers.md) |
| Caches | local per node, kept in step | the cluster handler, or a shared cache backend |
| Logs | local per node | collect them centrally if you need one view |
| Cronjobs | one node | the crontab on one node only |

Do not put the whole `var/` directory on a network share for all nodes: the cluster documentation says the var
directories "should in NO CASE be shared among instances".

## Trusting the load balancer

Behind a balancer every request arrives from the balancer's address. Exponential believes forwarded headers only from
proxies you list ([Trusted proxies](../../../../bc/6.0/trusted-proxies.md)):

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=192.0.2.10
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

The empty line replaces the shipped list (`127.0.0.1` and `::1`); repeat those if a local proxy still needs them. The
address is a documentation example; use your balancer's. The balancer must set `X-Forwarded-Proto: https` when it
terminates TLS and pass the original host. Velocity has its own list, `Q.webserver.proxy.trusted` in the engine
configuration, because it decides before the kernel runs.

Why it matters for a public body: without it every visitor appears with the balancer's address in the audit trail and
the request logs, so an investigation cannot tell sign-ins apart, and HTTPS pages may generate `http` links.

## Cron on one node

Install the crontab on one node only. Each script takes a lock, but that lock is a file in the node's own cache
directory, so it does not stop another node from running the same job. To pause cronjob parts for all nodes, point
`cronjob.ini [PauseSettings] Directory` at a shared directory
([Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md)). Document which node runs cron, and how another
node takes over if it fails.

## Deploys and cache clears on every node

Run the deploy on each node, so all of them run the new code and rebuild their caches:

```bash
./console exp:velocity deploy --allow-root-user
```

Take a node out of the balancer while it is deployed if the change must not be served half way. The caches in front
of PHP (the HTTP cache and Velocity's response cache) are local to each node; clear them on every node after a
change. With the DFS cluster, the kernel's own cache files and their expiry timestamps are cluster files, so a clear
on one node reaches the others ([Several web nodes](../../../hosting/operations/several-web-nodes.md)).

## Maintenance and logs

- The maintenance state is the file `var/maintenance.json` of an installation. With a `var/` directory per node,
  switch it on and off on every node.
- Each node writes its own `var/log`. An error reference a visitor reports may be in another node's log; collect logs
  centrally or know how to search every node.
- Check each node directly as well as the balanced address ([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md)).

## Working with a central network team

Give the network team a short, written specification:

| Item | Value to agree |
|---|---|
| Health check URL per node | for example the front page and `/user/login` of the administration |
| Headers the balancer sets | `X-Forwarded-For`, `X-Forwarded-Proto`, original `Host` |
| Session affinity | needed or not ([Sessions across servers](sessions-across-servers.md)) |
| Balancer addresses | for `TrustedProxies[]` |
| How a node is drained for a deploy | the procedure and who does it |

## Checklist

- [ ] Database server, shared storage and session handling are in place.
- [ ] `TrustedProxies[]` lists the balancer; `ClientIpByCustomHTTPHeader` is set.
- [ ] Velocity's own trusted proxy list is set when Velocity serves the nodes.
- [ ] The crontab runs on one node; the pause directory is shared.
- [ ] Deploy, cache clearing and maintenance runbooks name every node.
- [ ] The network team has the written specification.

## Related pages

- [Availability](README.md) index of this folder
- [High availability overview](high-availability-overview.md)
- [Varnish and reverse proxies](../../../hosting/performance/varnish-and-reverse-proxies.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
