-- Upgrades the DFS cluster table the ezoracle extension made (its eZDFSFileHandlerOracleBackend: one table ezdfsfile
-- without name_trunk, the cache files in it too) to the tables of the kernel's expDFSFileHandlerOracleBackend
-- (kernel/sql/oracle/cluster_dfs_schema.sql). Run once, with the web servers stopped or the site in maintenance:
--   sqlplus <user>/<password>@<connect string> @kernel/sql/oracle/cluster_dfs_schema_upgrade.sql
-- The columns filesize, mtime, expired (CHAR(1) there, compared as numbers here) and status stay as they are.

ALTER TABLE ezdfsfile ADD (name_trunk VARCHAR2(4000));
UPDATE ezdfsfile SET name_trunk = name;
ALTER TABLE ezdfsfile MODIFY (name_trunk NOT NULL);
CREATE INDEX ezdfsfile_name_trunk ON ezdfsfile (name_trunk);
CREATE INDEX ezdfsfile_expired_scope ON ezdfsfile (expired, scope);

-- the cache files: the kernel backend keeps them in a table of their own, so the rows of the old table are dropped
-- (caches are generated again; their files left on the DFS are removed by bin/php/dfscleanup.php)
DELETE FROM ezdfsfile WHERE name LIKE '%/cache/%' AND name NOT LIKE '%/storage/%';
COMMIT;

CREATE TABLE ezdfsfile_cache (
  name VARCHAR2(4000) NOT NULL,
  name_trunk VARCHAR2(4000) NOT NULL,
  name_hash VARCHAR2(34) NOT NULL,
  datatype VARCHAR2(255) DEFAULT 'application/octet-stream',
  scope VARCHAR2(25),
  filesize NUMBER(19) DEFAULT 0 NOT NULL,
  mtime NUMBER(19) DEFAULT 0 NOT NULL,
  expired NUMBER(1) DEFAULT 0 NOT NULL,
  CONSTRAINT ezdfsfile_cache_pk PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_cache_name ON ezdfsfile_cache (name);
CREATE INDEX ezdfsfile_cache_name_trunk ON ezdfsfile_cache (name_trunk);
CREATE INDEX ezdfsfile_cache_mtime ON ezdfsfile_cache (mtime);
CREATE INDEX ezdfsfile_cache_expired_scope ON ezdfsfile_cache (expired, scope);
