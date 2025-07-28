<?php

namespace AndreasElia\PostmanGenerator;

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

class ExportPostman extends Command
{
    /** @var string */
    protected $signature = 'export:postman-test {--bearer= : The bearer token to use on your endpoints}';

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

        if (is_subclass_of(TripRequest::class, \Illuminate\Foundation\Http\FormRequest::class)) {

            try {
                // Extract rules without executing any code
                $rules = $this->extractRulesFromClass(TripRequest::class);
                print_r($rules);

                if (!empty($rules)) {
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
            } catch (Exception $e) {
                throw $e;
                $this->warn("Skipping rules for {$route->uri()}: " . $e->getMessage());
            }
        }
        return;

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
                                        $rules = $this->extractRulesFromClass($className);

                                        if (!empty($rules)) {
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
                                                print_r($formdata);
                                            }

                                            $requestData['request']['body'] = [
                                                'mode' => 'formdata',
                                                'formdata' => $formdata,
                                            ];
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

    /**
     * Extract rules array from FormRequest class without executing code
     */
    protected function extractRulesFromClass(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $source = file_get_contents($reflection->getFileName());

        // Find the rules() method
        if (!preg_match('/function\s+rules\s*\([^)]*\)\s*:\s*array\s*{([^}]*)}/s', $source, $matches)) {
            return [];
        }

        $methodBody = $matches[1];

        // Find the return statement
        if (!preg_match('/return\s*(\[[\s\S]*?\])\s*;/', $methodBody, $returnMatch)) {
            return [];
        }

        $arrayString = $returnMatch[1];

        // Parse array elements (supports both string and array values)
        $pattern = '/([\'"])(.*?)\1\s*=>\s*(?:(\[[^\]]*\])|([\'"])(.*?)\4)/';
        preg_match_all($pattern, $arrayString, $matches, PREG_SET_ORDER);
        print_r($matches);
        $rules = [];
        foreach ($matches as $match) {
            $key = $match[2]; // Extracted key (e.g., "first_name")

            if (!empty($match[3])) {
                // Handle array values (e.g., ['required', 'string'])
                $value = trim($match[3], '[]');
                $value = implode('|', array_map('trim', explode(',', $value)));
            } else {
                // Handle string values (e.g., 'required')
                try {
                    $value = $match[5];
                } catch (Exception $e) {
                    throw $e;
                    $value = [];
                }
            }

            $rules[$key] = $value;
        }

        return $rules;
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
}
