-- Script de inicialización de la base de datos
-- Crea extensiones necesarias y configura la base de datos

-- Crear extensión pgvector para embeddings de IA
CREATE EXTENSION IF NOT EXISTS vector;

-- Crear extensión para UUID si no existe
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Crear extensión para búsqueda de texto completo
CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- Crear extensión para funciones JSON avanzadas
CREATE EXTENSION IF NOT EXISTS btree_gin;

-- Configurar parámetros de performance
ALTER SYSTEM SET shared_buffers = '256MB';
ALTER SYSTEM SET effective_cache_size = '1GB';
ALTER SYSTEM SET maintenance_work_mem = '64MB';
ALTER SYSTEM SET checkpoint_completion_target = 0.9;
ALTER SYSTEM SET wal_buffers = '16MB';
ALTER SYSTEM SET default_statistics_target = 100;

-- Crear usuario adicional si es necesario (opcional)
-- CREATE USER noticias_app WITH PASSWORD 'noticias123';
-- GRANT ALL PRIVILEGES ON DATABASE noticias TO noticias_app;

-- Nota: Las tablas serán creadas por Laravel migraciones