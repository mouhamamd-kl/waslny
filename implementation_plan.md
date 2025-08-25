# Implementation Plan

[Overview]
This plan outlines the steps to enhance the `GenerateEventDocs` command to provide more comprehensive event documentation, including details about event listeners. The goal is to improve the existing command to automatically generate documentation that shows which events are fired, what data they carry, which channels they are broadcast on, and which listeners handle them.

[Types]
No new types, interfaces, or data structures will be introduced. The existing array structures for documentation will be augmented with a new key for listeners.

[Files]
This implementation will modify one existing file.
- **Modified File:** `app/Console/Commands/GenerateEventDocs.php` will be updated to include listener discovery and improved data extraction.

[Functions]
Several functions within `GenerateEventDocs.php` will be modified, and new helper functions will be added.
- **New Function:** `findEventListeners($eventClass)` will be created to scan the `app/Listeners` directory and identify listeners for a specific event class.
- **Modified Function:** `generateDocumentation($events)` will be updated to call `findEventListeners` and include the listener information in the documentation array for each event.
- **Modified Function:** `getPropertyType($reflectionProperty)` will be enhanced to read native PHP type hints in addition to docblock annotations.
- **Modified Function:** `generateMarkdownOutput($documentation)` will be updated to include a "Listeners" section in the generated Markdown file.
- **Modified Function:** `generateJsonOutput($documentation)` will be updated to include the list of listeners in the JSON output.

[Classes]
No new classes will be created.
- **Modified Class:** `App\Console\Commands\GenerateEventDocs` will be modified as described in the "Files" and "Functions" sections.

[Dependencies]
There are no changes to project dependencies.

[Testing]
Manual testing will be sufficient for this command-line tool.
- Run `php artisan docs:events` to generate the Markdown documentation.
- Run `php artisan docs:events --output=json` to generate the JSON documentation.
- Verify that the generated `EVENTS.md` and `events.json` files contain the correct event, payload, channel, and listener information.

[Implementation Order]
The implementation will proceed in the following logical sequence.
1.  Implement the `findEventListeners` method in `GenerateEventDocs.php`.
2.  Update the `generateDocumentation` method to incorporate the listener information.
3.  Enhance the `getPropertyType` method to support native type hints.
4.  Update the `generateMarkdownOutput` method to display the listener information.
5.  Update the `generateJsonOutput` method to include the listener information.
6.  Manually test both output formats to ensure correctness.
