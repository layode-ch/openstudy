<?php

use OpenStudy\HTTPStatus;
use OpenStudy\Models\BaseModel;
use OpenStudy\Schemas\SchemaException;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/vendor/autoload.php';
// Source - https://stackoverflow.com/a
// Posted by Fancy John, modified by community. See post 'Timeline' for change history
// Retrieved 2025-11-27, License - CC BY-SA 4.0

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ERROR | E_PARSE);

$app = AppFactory::create();
// Add global exception handler
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setDefaultErrorHandler(function (Request $request, Throwable $exception) {
    if ($exception instanceof SchemaException) {
        sendErrors($exception->errors, $exception->httpStatus);
    }
    sendErrors([$exception->getFile() => [
		$exception->getLine(),
		$exception->getMessage()
	]], 500);
});

// Routes
require_once __DIR__."/routes.php";

$app->run();

function sendData(array|BaseModel $data, int|HTTPStatus $httpStatus = HTTPStatus::OK): void {
    if ($httpStatus instanceof HTTPStatus)
        $httpStatus = $httpStatus->value;
    http_response_code($httpStatus);
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($data);
    die();
}

function sendErrors(array $errors, int|HTTPStatus $httpStatus = HTTPStatus::SERVER_ERROR) {
    sendData(["errors" => $errors], $httpStatus);
}