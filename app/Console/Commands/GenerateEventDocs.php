<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use ReflectionProperty;

class GenerateEventDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:events {--output=markdown : The output format (markdown, json, or html)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate documentation for application events.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = $this->discoverEvents();
        $documentation = $this->generateDocumentation($events);

        $outputFormat = $this->option('output');

        if ($outputFormat === 'json') {
            $this->generateJsonOutput($documentation);
            $this->info('Event documentation generated in JSON format.');
        } elseif ($outputFormat === 'html') {
            $this->generateHtmlOutput($documentation);
            $this->info('Event documentation generated in HTML format.');
        } else {
            $this->generateMarkdownOutput($documentation);
            $this->info('Event documentation generated in Markdown format.');
        }

        return 0;
    }

    protected function discoverEvents()
    {
        $eventFiles = File::files(app_path('Events'));
        $events = [];

        foreach ($eventFiles as $file) {
            $className = 'App\\Events\\' . $file->getFilenameWithoutExtension();
            if (class_exists($className)) {
                $events[] = $className;
            }
        }

        return $events;
    }

    protected function generateDocumentation(array $events)
    {
        $documentation = [];

        foreach ($events as $eventClass) {
            $reflection = new ReflectionClass($eventClass);
            $constructor = $reflection->getConstructor();
            $properties = [];

            if ($constructor) {
                foreach ($constructor->getParameters() as $param) {
                    $propReflection = $reflection->getProperty($param->getName());
                    $properties[$param->getName()] = $this->getPropertyType($propReflection);
                }
            }

            $channels = [];
            if ($reflection->implementsInterface(ShouldBroadcast::class)) {
                $broadcastOnMethod = $reflection->getMethod('broadcastOn');
                $startLine = $broadcastOnMethod->getStartLine() - 1;
                $endLine = $broadcastOnMethod->getEndLine();
                $length = $endLine - $startLine;
                $source = file($broadcastOnMethod->getFileName());
                $methodCode = implode("", array_slice($source, $startLine, $length));

                if (preg_match_all('/BroadCastChannelEnum::([A-Z_]+)/', $methodCode, $matches)) {
                    foreach ($matches[1] as $enumCaseName) {
                        $channelEnumValue = null;
                        foreach (\App\Enums\channels\BroadCastChannelEnum::cases() as $case) {
                            if ($case->name === $enumCaseName) {
                                $channelEnumValue = $case->value;
                                break;
                            }
                        }

                        if ($channelEnumValue) {
                            $channels[] = $channelEnumValue;
                        }
                    }
                }
                
                if (empty($channels)) {
                    $channels[] = 'Unable to determine dynamically';
                }
            }

            $broadcastWith = [];
            if ($reflection->implementsInterface(ShouldBroadcast::class) && $reflection->hasMethod('broadcastWith')) {
                $broadcastWith = $this->parseArrayStructureFromMethod($reflection->getMethod('broadcastWith'));
            }

            $documentation[$eventClass] = [
                'payload' => $properties,
                'listeners' => $this->findEventListeners($eventClass),
                'channels' => $channels,
                'dispatched_from' => $this->findEventDispatchLocations($eventClass),
                'broadcast_with' => $broadcastWith,
            ];
        }

        return $documentation;
    }

    protected function getPropertyType(ReflectionProperty $reflectionProperty)
    {
        // Check for a native type hint first
        if ($reflectionProperty->hasType()) {
            return $reflectionProperty->getType()->getName();
        }

        // Fallback to docblock
        $docComment = $reflectionProperty->getDocComment();
        if ($docComment && preg_match('/@var\s+([^\s]+)/', $docComment, $matches)) {
            return $matches[1];
        }

        return 'mixed';
    }

    protected function findEventListeners(string $eventClass)
    {
        $listeners = [];
        $listenerFiles = File::files(app_path('Listeners'));

        foreach ($listenerFiles as $file) {
            $className = 'App\\Listeners\\' . $file->getFilenameWithoutExtension();
            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            $handleMethod = $reflection->getMethod('handle');
            $parameters = $handleMethod->getParameters();

            if (count($parameters) > 0) {
                $eventType = $parameters[0]->getType();
                if ($eventType && $eventType->getName() === $eventClass) {
                    $listeners[] = $className;
                }
            }
        }

        return $listeners;
    }

    protected function generateMarkdownOutput(array $documentation)
    {
        $markdown = "# Application Events\n\n";

        foreach ($documentation as $event => $details) {
            $markdown .= "## `{$event}`\n\n";
            $markdown .= "**Payload:**\n\n";

            if (empty($details['payload'])) {
                $markdown .= "- None\n\n";
            } else {
                foreach ($details['payload'] as $property => $type) {
                    $markdown .= "- `{$property}`: `{$type}`\n";
                }
                $markdown .= "\n";
            }

            $markdown .= "**Listeners:**\n\n";

            if (empty($details['listeners'])) {
                $markdown .= "- None\n\n";
            } else {
                foreach ($details['listeners'] as $listener) {
                    $markdown .= "- `{$listener}`\n";
                }
                $markdown .= "\n";
            }

            if (!empty($details['channels'])) {
                $markdown .= "**Broadcast Channels:**\n\n";
                foreach ($details['channels'] as $channel) {
                    $markdown .= "- `{$channel}`\n";
                }
                $markdown .= "\n";
            }

            if (!empty($details['dispatched_from'])) {
                $markdown .= "**Dispatched From:**\n\n";
                foreach ($details['dispatched_from'] as $location) {
                    $markdown .= "- `{$location}`\n";
                }
                $markdown .= "\n";
            }

            if (!empty($details['broadcast_with'])) {
                $markdown .= "**Broadcast Data:**\n\n";
                $markdown .= $this->formatArrayAsMarkdown($details['broadcast_with']);
                $markdown .= "\n";
            }
        }

        Storage::disk('local')->put('EVENTS.md', $markdown);
    }

    protected function generateJsonOutput(array $documentation)
    {
        Storage::disk('local')->put('events.json', json_encode($documentation, JSON_PRETTY_PRINT));
    }

    protected function generateHtmlOutput(array $documentation)
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Events</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #eaecef; padding-bottom: 10px; }
        h2 { color: #2c3e50; margin-top: 40px; border-bottom: 1px solid #eaecef; padding-bottom: 8px; }
        code { background-color: #e8e8e8; padding: 3px 6px; border-radius: 4px; font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace; }
        .event-block { margin-bottom: 40px; }
        .property-list, .listener-list, .channel-list { list-style-type: none; padding-left: 0; }
        .property-list li, .listener-list li, .channel-list li { background: #f6f8fa; border: 1px solid #d1d5da; padding: 10px; margin-bottom: 8px; border-radius: 6px; }
        strong { color: #24292e; }
        details { margin-left: 20px; }
        summary { cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Application Events</h1>
HTML;

        foreach ($documentation as $event => $details) {
            $html .= "<div class='event-block'>";
            $html .= "<h2><code>{$event}</code></h2>";

            $html .= "<strong>Payload:</strong>";
            if (empty($details['payload'])) {
                $html .= "<p>None</p>";
            } else {
                $html .= "<ul class='property-list'>";
                foreach ($details['payload'] as $property => $type) {
                    $html .= "<li><code>{$property}</code>: <code>{$type}</code></li>";
                }
                $html .= "</ul>";
            }

            $html .= "<strong>Listeners:</strong>";
            if (empty($details['listeners'])) {
                $html .= "<p>None</p>";
            } else {
                $html .= "<ul class='listener-list'>";
                foreach ($details['listeners'] as $listener) {
                    $html .= "<li><code>{$listener}</code></li>";
                }
                $html .= "</ul>";
            }

            if (!empty($details['channels'])) {
                $html .= "<strong>Broadcast Channels:</strong>";
                $html .= "<ul class='channel-list'>";
                foreach ($details['channels'] as $channel) {
                    $html .= "<li><code>{$channel}</code></li>";
                }
                $html .= "</ul>";
            }

            if (!empty($details['dispatched_from'])) {
                $html .= "<strong>Dispatched From:</strong>";
                $html .= "<ul class='channel-list'>";
                foreach ($details['dispatched_from'] as $location) {
                    $html .= "<li><code>{$location}</code></li>";
                }
                $html .= "</ul>";
            }

            if (!empty($details['broadcast_with'])) {
                $html .= "<strong>Broadcast Data:</strong>";
                $html .= $this->formatArrayAsHtml($details['broadcast_with']);
            }
            $html .= "</div>";
        }

        $html .= <<<HTML
    </div>
</body>
</html>
HTML;

        Storage::disk('local')->put('events.html', $html);
    }

    protected function findEventDispatchLocations(string $eventClass)
    {
        $locations = [];
        $files = File::allFiles(app_path());
        $eventClassName = class_basename($eventClass);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $contents = $file->getContents();
            if (strpos($contents, $eventClassName) === false) {
                continue;
            }

            if (preg_match_all("/(event|Event::dispatch)\s*\(\s*new\s+{$eventClassName}\s*\(/", $contents, $matches, PREG_OFFSET_CAPTURE)) {
                $lines = explode("\n", $contents);
                foreach ($matches[0] as $match) {
                    $lineNumber = substr_count(substr($contents, 0, $match[1]), "\n") + 1;
                    $methodName = $this->findMethodNameForLine($lines, $lineNumber);
                    $locations[] = "{$file->getRelativePathname()}:{$lineNumber} (in method `{$methodName}`)";
                }
            }
        }

        return $locations;
    }

    private function findMethodNameForLine(array $lines, int $lineNumber)
    {
        for ($i = $lineNumber - 1; $i >= 0; $i--) {
            if (preg_match('/function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(/', $lines[$i], $matches)) {
                return $matches[1];
            }
        }
        return 'unknown';
    }

    private function formatArrayAsMarkdown(array $data, int $level = 0)
    {
        $markdown = '';
        $indent = str_repeat('  ', $level);

        foreach ($data as $key => $type) {
            if (is_array($type)) {
                $markdown .= "{$indent}- `{$key}`:\n";
                $markdown .= $this->formatArrayAsMarkdown($type, $level + 1);
            } else {
                $markdown .= "{$indent}- `{$key}`: `{$type}`\n";
            }
        }

        return $markdown;
    }

    private function formatArrayAsHtml(array $data)
    {
        $html = "<ul class='channel-list'>";
        foreach ($data as $key => $type) {
            if (is_array($type)) {
                $html .= "<li><details><summary><code>{$key}</code></summary>" . $this->formatArrayAsHtml($type) . "</details></li>";
            } else {
                $html .= "<li><code>{$key}</code>: <code>{$type}</code></li>";
            }
        }
        $html .= "</ul>";
        return $html;
    }

    private function parseArrayStructureFromMethod(\ReflectionMethod $method)
    {
        $startLine = $method->getStartLine() - 1;
        $endLine = $method->getEndLine();
        $length = $endLine - $startLine;
        $source = file($method->getFileName());
        $methodCode = implode("", array_slice($source, $startLine, $length));

        if (preg_match('/return\s*\[(.*)\];/s', $methodCode, $match)) {
            $content = $match[1];
            preg_match_all("/'([^']*)'\s*=>/s", $content, $keyMatches, PREG_OFFSET_CAPTURE);
            
            $structure = [];
            for ($i = 0; $i < count($keyMatches[1]); $i++) {
                $key = $keyMatches[1][$i][0];
                $startPos = $keyMatches[0][$i][1] + strlen($keyMatches[0][$i][0]);
                $endPos = ($i + 1 < count($keyMatches[1])) ? $keyMatches[0][$i+1][1] : strlen($content);
                
                $valueContent = substr($content, $startPos, $endPos - $startPos);
                $valueContent = rtrim(trim($valueContent), ',');

                if (preg_match('/new\s+([a-zA-Z0-9_\\\\]+Resource)/', $valueContent, $resourceMatch)) {
                    $resourceClass = $resourceMatch[1];
                    $fullResourceClass = "App\\Http\\Resources\\{$resourceClass}";
                    if (class_exists($fullResourceClass)) {
                        $structure[$key] = $this->parseResource($fullResourceClass);
                    } else {
                        $structure[$key] = $resourceClass;
                    }
                } elseif (strpos(trim($valueContent), '$this->') === 0) {
                    $structure[$key] = 'property';
                } elseif (strpos(trim($valueContent), '[') === 0) {
                    $structure[$key] = 'array';
                } else {
                    $structure[$key] = 'unknown';
                }
            }
            return $structure;
        }

        return [];
    }

    private function parseResource(string $resourceClass)
    {
        static $visited = [];
        if (in_array($resourceClass, $visited)) {
            return "[recursive: {$resourceClass}]";
        }
        $visited[] = $resourceClass;

        try {
            $reflection = new ReflectionClass($resourceClass);
            if ($reflection->hasMethod('toArray')) {
                return $this->parseArrayStructureFromMethod($reflection->getMethod('toArray'));
            }
        } finally {
            array_pop($visited);
        }

        return "[{$resourceClass}]";
    }
}
