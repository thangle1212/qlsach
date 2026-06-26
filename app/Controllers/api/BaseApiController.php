<?php

require_once __DIR__ . '/../BaseController.php';

abstract class BaseApiController extends BaseController
{
    protected function checkAuth()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $rewriteUrl = $_GET['url'] ?? '';
        $pathFromUri = parse_url($requestUri, PHP_URL_PATH) ?? '';
        $requestPath = trim($rewriteUrl !== '' ? $rewriteUrl : $pathFromUri, '/');
        $requestPath = ltrim(str_replace('/qlsach', '', $requestPath), '/');
        $requestPath = preg_replace('#^index\.php#', '', $requestPath);
        $requestPath = ltrim($requestPath, '/');

        $allowedApiRoutes = [
            'api/login',
            'api/logout'
        ];

        if (in_array($requestPath, $allowedApiRoutes, true)) {
            return;
        }

        parent::checkAuth();
    }
}
