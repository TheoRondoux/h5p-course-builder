<br />
<div align="center">
    <div class="images">
        <a href="https://github.com/TheoRondoux/h5p-course-builder">
            <img src="medias/img/course_builder_logo.png" alt="Logo" width="400">
        </a>
    </div>
</div>

## Introduction ## 
Course Builder is a plugin that allows Junia teachers to create content for their courses in a simplified way.

## Dependencies ##

To work completly, this plugin needs another Moodle plugin called "[Interactive Content](https://moodle.org/plugins/mod_hvp)".
Please install it before installing Course Builder.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/h5p/h5plib/course_builder

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2024 - Théo Rondoux, Godfred Akwasi Boahend, Vincent Dumas, Pauline Jaspart

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
