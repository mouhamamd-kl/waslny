<?php

namespace App\Console\Commands;

use App\Http\Requests\CarManufacturerRequest;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\RiderCompleteProfileRequest;
use App\Http\Requests\TripRequest;
use App\Http\Requests\TripStatusRequest;
use App\Http\Requests\TwoFactorCodeRequest;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\ReflectionMethod;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class MyExportPostmanTest extends Command
{
    /** @var string */
    protected $signature = 'export:my-postman-test {--bearer= : [DEPRECATED] The bearer token to use on your endpoints}';

    /** @var string */
    protected $description = 'Automatically generate a Postman collection for your API routes';

    /** @var \Illuminate\Routing\Router */
    protected $router;

    /** @var array */
    protected $routes;

    public function __construct(Router $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    public function handle(): void
    {

        // if (is_subclass_of(TripRequest::class, \Illuminate\Foundation\Http\FormRequest::class)) {

        //     try {
        //         // Extract rules without executing any code
        //         $rules = $this->extractRulesFromClass2(TripRequest::class);

        //         if (!empty($rules)) {
        //             $formdata = [];
        //             foreach ($rules as $key => $rule) {
        //                 $isFileInput = false;
        //                 $ruleString = $this->ruleToString($rule);

        //                 if (stripos($ruleString, 'file') !== false || stripos($ruleString, 'image') !== false) {
        //                     $isFileInput = true;
        //                 }

        //                 $formdata[] = [
        //                     'key' => $key,
        //                     'value' => '',
        //                     'type' => $isFileInput ? 'file' : 'text',
        //                     'description' => $ruleString,
        //                 ];
        //             }

        //             $requestData['request']['body'] = [
        //                 'mode' => 'formdata',
        //                 'formdata' => $formdata,
        //             ];
        //             print_r($requestData);
        //         }
        //     } catch (Exception $e) {
        //         throw $e;
        //         $this->warn("Skipping rules for {$route->uri()}: " . $e->getMessage());
        //     }
        // }
        // return;

        $authGuards = [];
        foreach ($this->router->getRoutes() as $route) {
            foreach ($route->middleware() as $middleware) {
                if (is_string($middleware) && str_starts_with($middleware, 'auth:')) {
                    $guard = substr($middleware, 5);
                    if (!in_array($guard, $authGuards)) {
                        $authGuards[] = $guard;
                    }
                }
            }
        }

        $bearerTokens = [];
        if ($this->option('bearer')) {
            $this->warn('The --bearer option is deprecated. You will be prompted for tokens for each guard.');
        }

        foreach ($authGuards as $guard) {
            $token = $this->ask("Enter bearer token for guard '{$guard}'");
            if ($token) {
                $bearerTokens[$guard] = $token;
            }
        }

        $this->routes = [
            'variable' => [
                [
                    'key' => 'base_url',
                    'value' => config('api-postman.base_url'),
                ],
            ],
            'info' => [
                'name' => $filename = date('Y_m_d_His') . '_postman',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => [],
        ];

        $structured = config('api-postman.structured');

        // if ($bearer) {
        //     $this->routes['variable'][] = [
        //         'key' => 'token',
        //         'value' => $bearer,
        //     ];
        // }

        foreach ($this->router->getRoutes() as $route) {
            $middleware = $route->middleware();


            foreach ($route->methods as $method) {
                if ($method == 'HEAD' || !in_array('api', $middleware)) {
                    continue;
                }

                if (str_starts_with($route->getName(), 'no-export.')) {
                    continue;
                }

                $routeHeaders = config('api-postman.headers');
                $auth = null;

                $routeGuard = null;
                foreach ($middleware as $m) {
                    if (is_string($m) && str_starts_with($m, 'auth:')) {
                        $routeGuard = substr($m, 5);
                        break;
                    }
                }

                if ($routeGuard && !empty($bearerTokens[$routeGuard])) {
                    $auth = ['value' => $bearerTokens[$routeGuard]];
                }

                $request = $this->makeItem($route, $method, $routeHeaders, $auth);

                if (! $structured) {
                    $this->routes['item'][] = $request;
                }

                if ($structured) {
                    $routeNameStr = $route->action['as'] ?? '';
                    $routeNames = array_filter(explode('.', $routeNameStr));

                    if (empty($routeNames)) {
                        $this->routes['item'][] = $request;
                        continue;
                    }

                    $destination = end($routeNames);

                    $this->ensurePath($this->routes, $routeNames, $request, $destination);
                }
            }
        }

        Storage::put($exportName = "$filename.json", json_encode($this->routes));
        Storage::disk('local')->put("$filename.json", json_encode($this->routes));

        $this->info("Postman Collection Exported: $exportName");
    }

    protected function ensurePath(array &$routes, array $segments, array $request, string $destination): void
    {
        $parent = &$routes;

        foreach ($segments as $segment) {
            $matched = false;

            foreach ($parent['item'] as &$item) {
                if ($item['name'] === $segment) {
                    $parent = &$item;

                    if ($segment === $destination) {
                        $parent['item'][] = $request;
                    }

                    $matched = true;

                    break;
                }
            }

            unset($item);

            if (! $matched) {
                $item = [
                    'name' => $segment,
                    'item' => $segment === $destination ? [$request] : [],
                ];

                $parent['item'][] = &$item;
                $parent = &$item;
            }

            unset($item);
        }
    }

    public function makeItem($route, $method, $routeHeaders, $auth)
    {
        $requestData = [
            'name' => $route->uri(),
            'request' => [

                'method' => strtoupper($method),
                'header' => $routeHeaders,
                'url' => [
                    'raw' => '{{base_url}}/' . $route->uri(),
                    'host' => '{{base_url}}/' . $route->uri(),
                ],
            ],
        ];

        if ($auth) {
            $requestData['request']['auth'] = [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => $auth['value'], // Using variable for security
                        'type' => 'string'
                    ]
                ]
            ];
        }


        $action = $route->getAction('uses');

        if (is_string($action)) {
            try {
                if (strpos($action, '@') !== false) {
                    [$controller, $methodName] = explode('@', $action);

                    if (class_exists($controller) && method_exists($controller, $methodName)) {
                        $reflection = new ReflectionMethod($controller, $methodName);
                        $parameters = $reflection->getParameters();

                        $queryParams = $this->extractInputsFromMethodBody($reflection);
                        if (!empty($queryParams)) {
                            $requestData['request']['url']['query'] = $queryParams;
                            // Also update the raw URL
                            $rawQueryParams = [];
                            foreach ($queryParams as $param) {
                                $rawQueryParams[$param['key']] = $param['value'];
                            }
                            $queryString = http_build_query($rawQueryParams);
                            if (!empty($queryString)) {
                                $requestData['request']['url']['raw'] .= '?' . $queryString;
                            }
                        }

                        foreach ($parameters as $parameter) {
                            $type = $parameter->getType();
                            if ($type && !$type->isBuiltin()) {
                                $className = $type->getName();

                                // Check if it's a FormRequest
                                if (is_subclass_of($className, \Illuminate\Foundation\Http\FormRequest::class)) {

                                    try {
                                        // Extract rules without executing any code
                                        $rules = $this->extractRulesFromClass2($className);

                                        if (!empty($rules)) {
                                            $isJsonRequest = false;
                                            foreach ($rules as $key => $rule) {
                                                if (str_contains($key, '.*')) {
                                                    $isJsonRequest = true;
                                                    break;
                                                }
                                            }

                                            if ($isJsonRequest) {
                                                $rawJson = [];
                                                foreach ($rules as $key => $rule) {
                                                    $this->buildNestedArray($rawJson, $key, $this->getSampleValueForRule($rule));
                                                }

                                                // The above creates a structure with numeric keys for arrays,
                                                // let's clean it up to be a single object in the array.
                                                foreach ($rawJson as $key => $value) {
                                                    if (is_array($value) && isset($value[0])) {
                                                        $rawJson[$key] = [$value[0]];
                                                    }
                                                }


                                                $requestData['request']['body'] = [
                                                    'mode' => 'raw',
                                                    'raw' => json_encode($rawJson, JSON_PRETTY_PRINT),
                                                    'options' => [
                                                        'raw' => [
                                                            'language' => 'json'
                                                        ]
                                                    ]
                                                ];

                                                print_r($requestData);
                                            } else {
                                                $formdata = [];
                                                foreach ($rules as $key => $rule) {
                                                    $isFileInput = false;
                                                    $ruleString = $this->ruleToString($rule);

                                                    if (
                                                        stripos($ruleString, 'file') !== false ||
                                                        stripos($ruleString, 'image') !== false ||
                                                        str_contains($key, 'photo') ||
                                                        str_contains($key, 'image') ||
                                                        str_contains($key, 'file') ||
                                                        str_contains($key, 'document')
                                                    ) {
                                                        $isFileInput = true;
                                                    }

                                                    $formdata[] = [
                                                        'key' => $key,
                                                        'value' => '',
                                                        'type' => $isFileInput ? 'file' : 'text',
                                                        'description' => $ruleString,
                                                    ];
                                                }

                                                $requestData['request']['body'] = [
                                                    'mode' => 'formdata',
                                                    'formdata' => $formdata,
                                                ];
                                            }
                                        }
                                    } catch (Exception $e) {
                                        throw $e;
                                        $this->warn("Skipping rules for {$route->uri()}: " . $e->getMessage());
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                throw $e;
                $this->warn("Error processing route {$route->uri()}: " . $e->getMessage());
            }
        }

        return $requestData;
    }

    function extractInputsFromMethodBody(\ReflectionMethod $reflectionMethod): array
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $nodeFinder = new NodeFinder();

        try {
            $ast = $parser->parse(file_get_contents($reflectionMethod->getFileName()));
            $classNode = $nodeFinder->findFirstInstanceOf($ast, \PhpParser\Node\Stmt\Class_::class);
            if (!$classNode) return [];

            $methodNode = $nodeFinder->findFirst($classNode->stmts, function ($node) use ($reflectionMethod) {
                return $node instanceof ClassMethod
                    && $node->name->toString() === $reflectionMethod->getName();
            });

            if (!$methodNode || !$methodNode->stmts) return [];

            $inputs = [];
            $nodeFinder->find($methodNode->stmts, function ($node) use (&$inputs) {
                if (
                    $node instanceof \PhpParser\Node\Expr\MethodCall &&
                    $node->var instanceof \PhpParser\Node\Expr\Variable &&
                    $node->var->name === 'request' &&
                    $node->name->toString() === 'input' &&
                    isset($node->args[0])
                ) {
                    $keyNode = $node->args[0]->value;
                    if ($keyNode instanceof \PhpParser\Node\Scalar\String_) {
                        $key = $keyNode->value;
                        $value = '';
                        $description = 'No default value';

                        if (isset($node->args[1])) {
                            $value = $this->nodeToValue($node->args[1]->value);
                            $description = 'Default: ' . (is_array($value) ? json_encode($value) : $value);
                        }

                        $inputs[] = [
                            'key' => $key,
                            'value' => (string)$value,
                            'description' => $description,
                        ];
                    }
                }
            });

            return $inputs;
        } catch (\Throwable $e) {
            // Log or handle error if parsing fails
            return [];
        }
    }

    private function nodeToValue(\PhpParser\Node\Expr $node)
    {
        if ($node instanceof \PhpParser\Node\Scalar\String_) {
            return $node->value;
        }
        if ($node instanceof \PhpParser\Node\Scalar\LNumber) {
            return $node->value;
        }
        if ($node instanceof \PhpParser\Node\Scalar\DNumber) {
            return $node->value;
        }
        if ($node instanceof \PhpParser\Node\Expr\ConstFetch) {
            $name = $node->name->toLowerString();
            if ($name === 'true') return true;
            if ($name === 'false') return false;
            if ($name === 'null') return null;
        }
        if ($node instanceof \PhpParser\Node\Expr\Array_) {
            $arr = [];
            foreach ($node->items as $item) {
                if ($item && $item->value) {
                    if ($item->key) {
                        $arr[$this->nodeToValue($item->key)] = $this->nodeToValue($item->value);
                    } else {
                        $arr[] = $this->nodeToValue($item->value);
                    }
                }
            }
            return $arr;
        }
        // For other complex expressions, return a placeholder string
        $printer = new \PhpParser\PrettyPrinter\Standard();
        return $printer->prettyPrintExpr($node);
    }

    function getRulesFunctionReturn(string $className): string
    {
        $reflection = new \ReflectionClass($className);
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $ast = $parser->parse(file_get_contents($reflection->getFileName()));

        $nodeFinder = new NodeFinder();
        $rulesMethod = $nodeFinder->findFirst($ast, function ($node) {
            return $node instanceof ClassMethod
                && $node->name->toString() === 'rules';
        });

        if (!$rulesMethod) return '[]';

        $returnStmt = $nodeFinder->findFirst($rulesMethod->stmts, function ($node) {
            return $node instanceof Return_;
        });
        if (!$returnStmt || !$returnStmt->expr) return '[]';

        // Convert all values to strings in the AST
        $this->stringifyValues($returnStmt->expr);

        $printer = new \PhpParser\PrettyPrinter\Standard();
        return $printer->prettyPrintExpr($returnStmt->expr);
    }

    private function stringifyValues(\PhpParser\Node\Expr $node): void
    {
        if ($node instanceof \PhpParser\Node\Expr\Array_) {
            foreach ($node->items as $item) {
                if ($item && $item->value) {
                    $this->convertNodeToString($item->value);
                }
            }
        }
    }

    private function convertNodeToString(\PhpParser\Node\Expr &$node): void
    {
        if ($node instanceof \PhpParser\Node\Expr\Array_) {
            $this->stringifyValues($node);
        } elseif ($node instanceof \PhpParser\Node\Scalar\String_) {
            // Already a string, do nothing
        } elseif ($node instanceof \PhpParser\Node\Expr\New_) {
            $node = new \PhpParser\Node\Scalar\String_('new ' . $node->class->toString());
        } elseif ($node instanceof \PhpParser\Node\Expr\StaticCall) {
            $node = new \PhpParser\Node\Scalar\String_($node->class->toString() . '::' . $node->name->toString());
        } elseif ($node instanceof \PhpParser\Node\Expr\Closure) {
            $node = new \PhpParser\Node\Scalar\String_('');
        } elseif ($node instanceof \PhpParser\Node\Expr\MethodCall) {
            $node = new \PhpParser\Node\Scalar\String_('');
        } else {
            $node = new \PhpParser\Node\Scalar\String_($this->nodeToString($node));
        }
    }

    private function nodeToString(\PhpParser\Node\Expr $node): string
    {
        $printer = new \PhpParser\PrettyPrinter\Standard();
        return $printer->prettyPrintExpr($node);
    }

    function extractRulesFromClass2(string $className): array
    {
        $arrayString = $this->getRulesFunctionReturn($className);

        if (empty($arrayString)) {
            return [];
        }

        // Remove all comments
        $cleanedArrayString = preg_replace([
            '/\/\/.*?(\r\n|\n|$)/m',    // Single-line comments
            '/\/\*.*?\*\//s',           // Multi-line comments
        ], '', $arrayString);
        // Remove blank lines and extra whitespace
        $cleanedArrayString = preg_replace('/^\s*[\r\n]/m', '', $cleanedArrayString);
        $cleanedArrayString = trim($cleanedArrayString);

        // Convert the array string to actual PHP array
        try {
            $rulesArray = eval("return $cleanedArrayString;");
            return is_array($rulesArray) ? $rulesArray : [];
        } catch (\Throwable $e) {
            // Handle evaluation errors
            error_log("Failed to parse rules array: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Convert rule to string representation
     */
    protected function ruleToString($rule): string
    {
        if (is_string($rule)) {
            return $rule;
        }

        if (is_array($rule)) {
            return implode('|', array_map([$this, 'ruleToString'], $rule));
        }

        if (is_object($rule)) {
            return get_class($rule);
        }

        return (string) $rule;
    }

    protected function getSampleValueForRule($rule)
    {
        $ruleString = $this->ruleToString($rule);

        if (str_contains($ruleString, 'string')) {
            return 'string';
        }
        if (str_contains($ruleString, 'integer')) {
            return 1;
        }
        if (str_contains($ruleString, 'numeric')) {
            return 123.45;
        }
        if (str_contains($ruleString, 'boolean')) {
            return true;
        }
        if (str_contains($ruleString, 'date')) {
            return '2024-01-01';
        }
        if (str_contains($ruleString, 'datetime')) {
            return '2024-01-01 12:00:00';
        }
        if (str_contains($ruleString, 'Point')) {
            return [
                'type' => 'Point',
                'coordinates' => [0, 0]
            ];
        }

        return '';
    }

    function buildNestedArray(&$arr, $key, $value)
    {
        $keys = explode('.', str_replace('.*.', '.*.', $key));
        $current = &$arr;
        foreach ($keys as $i => $k) {
            if ($k === '*') {
                $k = 0;
            }
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        $current = $value;
    }
}
