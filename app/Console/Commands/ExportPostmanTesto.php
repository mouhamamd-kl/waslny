<?php

namespace App\Console\Commands;

use App\Http\Requests\CarManufactureRequest;
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

class ExportPostmanTesto extends Command
{
    /** @var string */
    protected $signature = 'export:postman-testo {--bearer= : The bearer token to use on your endpoints}';

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

        $bearer = $this->option('bearer') ?? false;

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
                if ($method == 'HEAD' || empty($middleware) || $middleware[0] !== 'api') {
                    continue;
                }

                $routeHeaders = config('api-postman.headers');
                $auth = null;
                if ($bearer &&  $middleware) {
                    $auth['value'] =
                        $bearer;
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

                                                    if (stripos($ruleString, 'file') !== false || stripos($ruleString, 'image') !== false) {
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
    // protected function extractRulesFromClass2(string $className): array
    // {
    //     $reflection = new \ReflectionClass($className);
    //     $source = file_get_contents($reflection->getFileName());

    //     // Find the rules() method
    //     if (!preg_match('/function\s+rules\s*\([^)]*\)\s*:\s*array\s*{([^}]*)}/s', $source, $matches)) {
    //         return [];
    //     }

    //     $methodBody = $matches[1];

    //     // Find the return statement
    //     if (!preg_match('/return\s*(\[[\s\S]*?\])\s*;/', $methodBody, $returnMatch)) {
    //         return [];
    //     }

    //     $arrayString = $returnMatch[1];

    //     // Remove all comments (both // and /** */ styles)
    //     $cleanedArrayString = preg_replace([
    //         '/\/\/.*?(\r\n|\n|$)/m',    // Single-line comments
    //         '/\/\*.*?\*\//s',           // Multi-line comments
    //     ], '', $arrayString);

    //     // Remove blank lines and extra whitespace
    //     $cleanedArrayString = preg_replace('/^\s*[\r\n]/m', '', $cleanedArrayString);
    //     $cleanedArrayString = trim($cleanedArrayString);

    //     print_r($cleanedArrayString);

    //     return [];
    // }

    // protected function extractRulesFromClass(string $className): array
    // {
    //     $reflection = new \ReflectionClass($className);
    //     $source = file_get_contents($reflection->getFileName());

    //     // Find the rules() method
    //     if (!preg_match('/function\s+rules\s*\([^)]*\)\s*:\s*array\s*{([^}]*)}/s', $source, $matches)) {
    //         return [];
    //     }

    //     $methodBody = $matches[1];

    //     // Find the return statement
    //     if (!preg_match('/return\s*(\[[\s\S]*?\])\s*;/', $methodBody, $returnMatch)) {
    //         return [];
    //     }

    //     $arrayString = $returnMatch[1];


    //     // Parse array elements (supports both string and array values)
    //     $pattern = '/([\'"])(.*?)\1\s*=>\s*(?:(\[[^\]]*\])|([\'"])(.*?)\4)/';
    //     preg_match_all($pattern, $arrayString, $matches, PREG_SET_ORDER);
    //     print_r($matches);
    //     $rules = [];
    //     foreach ($matches as $match) {
    //         $key = $match[2]; // Extracted key (e.g., "first_name")

    //         if (!empty($match[3])) {
    //             // Handle array values (e.g., ['required', 'string'])
    //             $value = trim($match[3], '[]');
    //             $value = implode('|', array_map('trim', explode(',', $value)));
    //         } else {
    //             // Handle string values (e.g., 'required')
    //             try {
    //                 $value = $match[5];
    //             } catch (Exception $e) {
    //                 throw $e;
    //                 $value = [];
    //             }
    //         }

    //         $rules[$key] = $value;
    //     }

    //     return $rules;
    // }


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
