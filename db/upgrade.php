<?php

/**
 * Define upgrade steps to be performed to upgrade the plugin from the old version to the current one.
 *
 * @param int $oldversion Version number the plugin is being upgraded from.
 */
function xmldb_h5plib_course_builder_upgrade($oldversion)
{
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024011001) {

                // Define table h5plib_course_builder to be created.
                $table = new xmldb_table('h5plib_course_builder');

                // Adding fields to table h5plib_course_builder.
                $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
                $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
                $table->add_field('presentationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

                // Adding keys to table h5plib_course_builder.
                $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
                $table->add_key('course_builder-user-foreign-key', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
                $table->add_key('course_builder-presentation-foreign-key', XMLDB_KEY_FOREIGN, ['presentationid'], 'hvp', ['id']);

                // Conditionally launch create table for h5plib_course_builder.
                if (!$dbman->table_exists($table)) {
                    $dbman->create_table($table);
                }

                // course_builder savepoint reached.
                upgrade_plugin_savepoint(true, 2024011001, 'h5plib', 'course_builder');
    }

    if ($oldversion < 2024013101) {

        // Define table h5plib_course_builder_template to be created.
        $table = new xmldb_table('h5plib_course_builder_template');

        // Adding fields to table h5plib_course_builder_template.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        // Adding keys to table h5plib_course_builder_template.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for h5plib_course_builder_template.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // course_builder savepoint reached.
        upgrade_plugin_savepoint(true, 2024013101, 'h5plib', 'course_builder');
    }

    if ($oldversion < 2024013102) {

        // Define table h5plib_course_builder to be renamed to h5plib_course_builder_pres.
        $table = new xmldb_table('h5plib_course_builder');

        // Launch rename table for h5plib_course_builder_pres.
        $dbman->rename_table($table, 'h5plib_course_builder_pres');

        // course_builder savepoint reached.
        upgrade_plugin_savepoint(true, 2024013102, 'h5plib', 'course_builder');
    }

    if ($oldversion < 2024020101) {

        // Define field presentationid to be added to h5plib_course_builder_template.
        $table = new xmldb_table('h5plib_course_builder_template');
        $field = new xmldb_field('presentationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field presentationid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('json_content', XMLDB_TYPE_TEXT, null, null, null, null, null, 'presentationid');

        // Conditionally launch add field json_content.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // course_builder savepoint reached.
        upgrade_plugin_savepoint(true, 2024020101, 'h5plib', 'course_builder');
    }

    if ($oldversion < 2024020501) {

        // Define field timecreated to be added to h5plib_course_builder_template.
        $table = new xmldb_table('h5plib_course_builder_template');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'json_content');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timemodified to be added to h5plib_course_builder_template.
        $table = new xmldb_table('h5plib_course_builder_template');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'timecreated');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // course_builder savepoint reached.
        upgrade_plugin_savepoint(true, 2024020501, 'h5plib', 'course_builder');

    }

    if ($oldversion < 2024020701) {

        // Define key presentationid_foreign_key (foreign) to be added to h5plib_course_builder_template.
        $table = new xmldb_table('h5plib_course_builder_template');
        $key = new xmldb_key('presentationid_foreign_key', XMLDB_KEY_FOREIGN, ['presentationid'], 'hvp', ['id']);

        // Launch add key presentationid_foreign_key.
        $dbman->add_key($table, $key);

        // course_builder savepoint reached.
        upgrade_plugin_savepoint(true, 2024020701, 'h5plib', 'course_builder');
    }

    if ($oldversion < 2024020702) {

        // Define field shared to be added to h5plib_course_builder_pres.
        $table = new xmldb_table('h5plib_course_builder_pres');
        $field = new xmldb_field('shared', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'presentationid');

        // Conditionally launch add field shared.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // course_builder savepoint reached.
        upgrade_plugin_savepoint(true, 2024020702, 'h5plib', 'course_builder');
    }

    return true;
}