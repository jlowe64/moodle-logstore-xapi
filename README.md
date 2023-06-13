# [Moodle Logstore xAPI](https://moodle.org/plugins/view/logstore_xapi)
> Emits [xAPI](https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md) statements using the [Moodle](https://moodle.org/) Logstore.

Moodle Logstore xAPI is a plugin that emits events from the Moodle Logstore as xAPI statements, allowing a modular, interoperable, performing and secure way of logging user 
interactions and learning experiences.
Moodle Logstore xAPI processes and stores the xAPI statements derived from Moodle logs into a Learning Record Store (LRS), where data from other platforms can be integrated as 
well in the same format, thus maximising interoperability. The LRS is the central repository of any xAPI ecosystem, receiving, storing, and providing data on learning experiences, 
achievements, and performance from a range of systems.
Moodle Logstore xAPI also augments the statements with information about the user roles and course content, which is key 
for contextualising the data when they were generated. Moodle Logstore xAPI provides options to anonymise the logs, and can be used both live and on historical data.

To avoid blocking page responses, the plugin operates in the background by default (this parameter can be changed in the settings) via Cron tasks. While this makes the process 
less real-time, it prevents Moodle's performance from fluctuating based on the performance of the LRS. The endpoint, key/username, and password to the LRS must be specified in 
the configuration page after installation. The plugin is currently compatible with Moodle versions 3.9 to 4.1.

## Documentation
- Install the plugin using [our zip installation guide](/docs/install-with-zip.md).
- Process events before the plugin was installed using [our historical events guide](/docs/historical-events.md).
- Ask questions via the [Github issue tracker](https://github.com/xAPI-vle/moodle-logstore_xapi/issues).
- Report bugs and suggest features with the [Github issue tracker](https://github.com/xAPI-vle/moodle-logstore_xapi/issues).
- View the supported events in [our `get_event_function_map` function](/src/transformer/get_event_function_map.php).
- Change existing statements for the supported events using [our change statements guide](/docs/change-statements.md).
- Create statements using [our new statements guide](/docs/new-statements.md).
