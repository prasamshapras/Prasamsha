<?php
require "db.php";

$res = $conn->query("SELECT image, mime_type FROM portfolio_profile WHERE id = 1 LIMIT 1");

if ($res && ($row = $res->fetch_assoc()) && !empty($row["image"])) {
  header("Content-Type: " . $row["mime_type"]);
  echo $row["image"];
  exit();
}

// Fallback SVG (no image uploaded yet)
header("Content-Type: image/svg+xml");
echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="320" height="400">
  <rect width="100%" height="100%" fill="#0b1020"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
        fill="#ffffff" font-size="14" font-family="Segoe UI, Arial">
    Upload photo from Dashboard
  </text>
</svg>
SVG;
