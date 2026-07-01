-- =============================================================
-- FIX: Corriger les image_path qui contiennent host.docker.internal
-- Ces URLs ne sont pas accessibles depuis le navigateur du client.
-- 
-- AVANT: http://host.docker.internal:8000/storage/support-images/xxx.png
-- APRÈS: /storage/support-images/xxx.png
-- =============================================================

-- 1. Messages table
UPDATE messages 
SET image_path = CONCAT('/storage/', SUBSTRING_INDEX(image_path, '/storage/', -1))
WHERE image_path LIKE '%host.docker.internal%';

-- 2. AI Responses table (si la colonne image_url existe)
UPDATE ai_responses
SET image_url = CONCAT('/storage/', SUBSTRING_INDEX(image_url, '/storage/', -1))
WHERE image_url LIKE '%host.docker.internal%';

-- 3. Tickets table (si la colonne image_url existe)
UPDATE tickets
SET image_url = CONCAT('/storage/', SUBSTRING_INDEX(image_url, '/storage/', -1))
WHERE image_url LIKE '%host.docker.internal%';