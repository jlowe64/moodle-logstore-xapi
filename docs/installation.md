1. [Get the requirements](#requirements).
2. [Download the plugin](#downloading).
3. [Install the plugin](#installing).
4. [Configure the plugin](#configuring)

## Requirements
You obviously need to have [Moodle](https://moodle.org/) installed first and you need to be logged in as an Admin.

## Downloading
#### Downloading via Zip (for users)
1. [Download the plugin](https://github.com/jlowe64/moodle-logstore-xapi/releases) as a zip or tar.
2. Go to "http://www.example.com/admin/tool/installaddon/index.php".
3. Drag and drop your download.
4. Click "Install the plugin from the ZIP file".

#### Downloading via Git (for developers)
1. Run `cd admin/tool/log/store/` from the root directory of Moodle.
2. Run `git clone git@github.com:jlowe64/moodle-logstore-xapi.git xapi`.

## Installing
1. Go to "http://www.example.com/admin/index.php".
2. Follow through the upgrade and enter your LRS details when required.
3. Go to "http://www.example.com/admin/settings.php?section=managelogging".
4. Enable the "Logstore xAPI" plugin.

## Configuring
1. Go to "http://www.example.com/admin/settings.php?section=logsettingxapi".
2. Modify your LRS details.
3. Click "Save changes".
