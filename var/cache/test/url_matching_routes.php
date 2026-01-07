<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/reset-password/request' => [[['_route' => '_api_/reset-password/request_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\ApiResource\\User\\ResetPassword', '_api_operation_name' => '_api_/reset-password/request_post', '_format' => null], null, ['POST' => 0], null, false, false, null]],
        '/api/reset-password/confirm' => [[['_route' => '_api_/reset-password/confirm_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\ApiResource\\User\\ResetPassword', '_api_operation_name' => '_api_/reset-password/confirm_post', '_format' => null], null, ['POST' => 0], null, false, false, null]],
        '/api/change-password' => [[['_route' => '_api_/change-password_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\ApiResource\\User\\ResetPassword', '_api_operation_name' => '_api_/change-password_post', '_format' => null], null, ['POST' => 0], null, false, false, null]],
        '/api/verify' => [[['_route' => '_api_/verify_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\ApiResource\\User\\VerifyEmail', '_api_operation_name' => '_api_/verify_post', '_format' => null], null, ['POST' => 0], null, false, false, null]],
        '/api/resend-verification' => [[['_route' => '_api_/resend-verification_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\ApiResource\\User\\VerifyEmail', '_api_operation_name' => '_api_/resend-verification_post', '_format' => null], null, ['POST' => 0], null, false, false, null]],
        '/api/users/register' => [[['_route' => '_api_/users/register_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/register_post', '_format' => null], null, ['POST' => 0], null, false, false, null]],
        '/api/user/profile' => [[['_route' => '_api_/user/profile_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/user/profile_get', '_format' => null], null, ['GET' => 0], null, false, false, null]],
        '/api/profile' => [[['_route' => '_api_/profile_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/profile_put', '_format' => null], null, ['PUT' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'api_login'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api(?'
                    .'|/(?'
                        .'|docs(?:\\.([^/]++))?(*:37)'
                        .'|\\.well\\-known/genid/([^/]++)(*:72)'
                        .'|validation_errors/([^/]++)(*:105)'
                    .')'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:142)'
                    .'|/(?'
                        .'|contexts/([^.]+)(?:\\.(jsonld))?(*:185)'
                        .'|errors/(\\d+)(?:\\.([^/]++))?(*:220)'
                        .'|validation_errors/([^/]++)(?'
                            .'|(*:257)'
                        .')'
                        .'|\\.well\\-known/genid/([^/\\.]++)(?:\\.([^/]++))?(*:311)'
                        .'|areas(?'
                            .'|(?:\\.([^/]++))?(*:342)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:379)'
                            .')'
                            .'|(?:\\.([^/]++))?(*:403)'
                            .'|/(?'
                                .'|([^/]++)/postcodes(*:433)'
                                .'|([^/\\.]++)(?:\\.([^/]++))?(*:466)'
                            .')'
                        .')'
                        .'|postcodes(?'
                            .'|(?:\\.([^/]++))?(*:503)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:537)'
                            .'|(?:\\.([^/]++))?(*:560)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:597)'
                            .')'
                        .')'
                        .'|time_slots(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:646)'
                            .'|(?:\\.([^/]++))?(*:669)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:706)'
                            .')'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        37 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => null, '_api_respond' => true], ['_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        72 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => true], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        105 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        142 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => null, '_api_respond' => true, 'index' => 'index'], ['index', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        185 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => true], ['shortName', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        220 => [[['_route' => '_api_errors', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\State\\ApiResource\\Error', '_api_operation_name' => '_api_errors', '_format' => null], ['status', '_format'], ['GET' => 0], null, false, true, null]],
        257 => [
            [['_route' => '_api_validation_errors_problem', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_problem', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_hydra', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_hydra', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_jsonapi', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_jsonapi', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_xml', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_xml', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
        ],
        311 => [[['_route' => '_api_/.well-known/genid/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_stateless' => true, '_api_resource_class' => 'App\\ApiResource\\User\\VerifyEmail', '_api_operation_name' => '_api_/.well-known/genid/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        342 => [[['_route' => '_api_/areas{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Area', '_api_operation_name' => '_api_/areas{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        379 => [
            [['_route' => '_api_/areas/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Area', '_api_operation_name' => '_api_/areas/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => '_api_/areas/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Area', '_api_operation_name' => '_api_/areas/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null],
        ],
        403 => [[['_route' => '_api_/areas{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Area', '_api_operation_name' => '_api_/areas{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        433 => [[['_route' => '_api_/areas/{id}/postcodes_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Area', '_api_operation_name' => '_api_/areas/{id}/postcodes_get_collection', '_format' => null], ['id'], ['GET' => 0], null, false, false, null]],
        466 => [[['_route' => '_api_/areas/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Area', '_api_operation_name' => '_api_/areas/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null]],
        503 => [[['_route' => '_api_/postcodes{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Postcode', '_api_operation_name' => '_api_/postcodes{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        537 => [[['_route' => '_api_/postcodes/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Postcode', '_api_operation_name' => '_api_/postcodes/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        560 => [[['_route' => '_api_/postcodes{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Postcode', '_api_operation_name' => '_api_/postcodes{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        597 => [
            [['_route' => '_api_/postcodes/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Postcode', '_api_operation_name' => '_api_/postcodes/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => '_api_/postcodes/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Postcode', '_api_operation_name' => '_api_/postcodes/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        646 => [[['_route' => '_api_/time_slots/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\TimeSlot', '_api_operation_name' => '_api_/time_slots/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        669 => [[['_route' => '_api_/time_slots{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\TimeSlot', '_api_operation_name' => '_api_/time_slots{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        706 => [
            [['_route' => '_api_/time_slots/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\TimeSlot', '_api_operation_name' => '_api_/time_slots/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/time_slots/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\TimeSlot', '_api_operation_name' => '_api_/time_slots/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
