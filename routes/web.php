<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'Transaction Service',
        'version' => 'v1',
        'docs'    => '/api/documentation',
        'graphql' => '/graphql-playground',
    ]);
});

Route::get('/graphql-playground', function () {
    return response()->make('
<!DOCTYPE html>
<html>
<head>
    <title>GraphQL Playground</title>
    <link rel="stylesheet" href="https://unpkg.com/graphql-playground-react/build/static/css/index.css"/>
    <script src="https://unpkg.com/graphql-playground-react/build/static/js/middleware.js"></script>
</head>
<body>
<div id="root"></div>
<script>
    window.addEventListener("load", function(event) {
        GraphQLPlayground.init(document.getElementById("root"), {
            endpoint: "/graphql",
            headers: {"X-IAE-KEY": "102022430015"}
        });
    });
</script>
</body>
</html>
    ', 200, ["Content-Type" => "text/html"]);
});
