-- Upgrades the DFS cluster tables made by the ezpostgresqlcluster extension (integer sizes and times, scope of 20
-- characters, indexes on name and mtime only) to kernel/sql/postgresql/cluster_dfs_schema.sql. Safe to run again (a
-- second run only rebuilds the two name indexes). PostgreSQL 13 and later.
-- Run it on the cluster database while no web server writes to the cluster (the type changes rewrite the tables):
--   psql -v ON_ERROR_STOP=1 -h <host> -U <user> -d <cluster database> -f kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql

ALTER TABLE ezdfsfile ALTER COLUMN size TYPE bigint, ALTER COLUMN mtime TYPE bigint, ALTER COLUMN expired TYPE smallint, ALTER COLUMN scope TYPE character varying(25);
ALTER TABLE ezdfsfile ADD COLUMN IF NOT EXISTS status smallint NOT NULL DEFAULT 0;
DROP INDEX IF EXISTS ezdfsfile_name;
CREATE INDEX IF NOT EXISTS ezdfsfile_name ON ezdfsfile (name text_pattern_ops);
CREATE INDEX IF NOT EXISTS ezdfsfile_name_trunk ON ezdfsfile (name_trunk);
CREATE INDEX IF NOT EXISTS ezdfsfile_mtime ON ezdfsfile (mtime);
CREATE INDEX IF NOT EXISTS ezdfsfile_expired_name ON ezdfsfile (expired, name text_pattern_ops);

ALTER TABLE ezdfsfile_cache ALTER COLUMN size TYPE bigint, ALTER COLUMN mtime TYPE bigint, ALTER COLUMN expired TYPE smallint, ALTER COLUMN scope TYPE character varying(25);
DROP INDEX IF EXISTS ezdfsfile_cache_name;
CREATE INDEX IF NOT EXISTS ezdfsfile_cache_name ON ezdfsfile_cache (name text_pattern_ops);
CREATE INDEX IF NOT EXISTS ezdfsfile_cache_name_trunk ON ezdfsfile_cache (name_trunk);
CREATE INDEX IF NOT EXISTS ezdfsfile_cache_mtime ON ezdfsfile_cache (mtime);
CREATE INDEX IF NOT EXISTS ezdfsfile_cache_expired_name ON ezdfsfile_cache (expired, name text_pattern_ops);
