<?php

require __DIR__ . '/../app/db.php';

$database = db();
ensure_database($database, true);

echo "Local file database initialized successfully.\n";
echo "Data file: database/data.json\n";
echo "SQL reference: database/schema.sql\n";
echo "Admin: admin / admin123\n";
