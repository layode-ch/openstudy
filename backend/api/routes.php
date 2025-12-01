<?php
use OpenStudy\Controllers AS OC;

$app->get("/swagger", [OC\OpenApiController::class, "swagger"]);