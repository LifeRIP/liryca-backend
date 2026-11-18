<?php

it('serves the Swagger interface and its separate OpenAPI document', function () {
    config(['l5-swagger.defaults.generate_always' => true]);
    $this->get('/docs')->assertOk()->assertSee('SwaggerUIBundle');
    $this->get('/docs/api-docs.json')->assertOk()->assertJsonPath('openapi', '3.0.0');
    $this->get('/docs/api-docs.json/asset/swagger-ui.css')->assertOk();
});
