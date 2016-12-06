<?php

/*
*
*	Block Customer Group
*	Add a block in the registration form where the customer can select his group.
*
*	@author			Daniele Gambaletta
*	@version		1.0.0
*	@PS version		1.6 recommended
*	@license   		http://creativecommons.org/licenses/by-sa/4.0/
*
*/

class BlockCG_SQL
{

    // Install new table
    public static function installTable()
    {
        /*
        CREATE TABLE IF NOT EXISTS `ps_registration_group` (
            `id_reg_group` INT(10) NOT NULL,
            `selected` BOOL NOT NULL DEFAULT 0,
        PRIMARY KEY (`id_reg_group`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        */
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'registration_group` (
					`id_reg_group` INT(10) NOT NULL,
					`selected` BOOL NOT NULL DEFAULT 0,
				PRIMARY KEY (`id_reg_group`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;'
        );
    }


    // Delete table
    public static function dropTable()
    {
        // DROP TABLE IF EXISTS `ps_registration_group`;
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'registration_group`;'
        );
    }


    //Update table not considering rows with existing id
    public static function insertRows()
    {
        /*
        INSERT IGNORE INTO `ps_registration_group` (`id_reg_group`)
            SELECT `id_group` FROM `ps_group`;
        */
        return Db::getInstance()->execute(
            'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'registration_group` (`id_reg_group`)
					SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group`;'
        );
    }


    //Delete rows not in the ps_group table
    public static function deleteRows()
    {
        /*
        DELETE FROM `ps_registration_group`
        WHERE `id_reg_group` NOT IN
            (SELECT `id_group` FROM `ps_group`);
        */
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'registration_group`
					WHERE `id_reg_group` NOT IN 
					(SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group`);'
        );
    }

    // Update ps_registration_group table
    public static function updateTable()
    {
        return (
            self::insertRows() &&
            self::deleteRows()
        );
    }


    public static function setSelectedGroups($value, $id)
    {
        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'registration_group` SET `selected` = ' . $value . ' WHERE `id_reg_group` = ' . $id . ';'
        );
    }


    // Get selected groups from table
    public static function getSelectedGroups()
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

        $query = 'SELECT ' . _DB_PREFIX_ . 'registration_group.id_reg_group, ' . _DB_PREFIX_ . 'group_lang.name, ' . _DB_PREFIX_ . 'group.reduction
					FROM ' . _DB_PREFIX_ . 'registration_group
					INNER JOIN ' . _DB_PREFIX_ . 'group_lang
						ON ' . _DB_PREFIX_ . 'group_lang.id_group = ' . _DB_PREFIX_ . 'registration_group.id_reg_group
						AND ' . _DB_PREFIX_ . 'group_lang.id_lang = ' . $id_lang_default . '
					INNER JOIN ' . _DB_PREFIX_ . 'group
						ON ' . _DB_PREFIX_ . 'group.id_group = ' . _DB_PREFIX_ . 'registration_group.id_reg_group
					WHERE ' . _DB_PREFIX_ . 'registration_group.selected > 0;';

        return Db::getInstance()->ExecuteS($query);
    }


    // Check if there aren't selected groups
    public static function checkSelectedGroups()
    {
        $check = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'registration_group` WHERE `selected` = 1;';

        if (Db::getInstance()->getValue($check) == 0)
            return false;

        return true;
    }


    // Get all groups
    public static function getGroups()
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

        return Db::getInstance()->ExecuteS(
            'SELECT ' . _DB_PREFIX_ . 'group_lang.id_group, ' . _DB_PREFIX_ . 'group_lang.name, ' . _DB_PREFIX_ . 'group.reduction, ' . _DB_PREFIX_ . 'group.show_prices
					FROM ' . _DB_PREFIX_ . 'group_lang
					INNER JOIN ' . _DB_PREFIX_ . 'group
						ON ' . _DB_PREFIX_ . 'group_lang.id_group = ' . _DB_PREFIX_ . 'group.id_group
                        AND ' . _DB_PREFIX_ . 'group_lang.id_lang = ' . $id_lang_default . ';'
        );
    }


    // Get groups id
    public static function getTypes()
    {
        return Db::getInstance()->ExecuteS(
            'SELECT `id_reg_group`
						FROM `' . _DB_PREFIX_ . 'registration_group`;'
        );
    }


    // Get if the group is selected
    public static function getSelected($id)
    {
        return Db::getInstance()->getValue(
            'SELECT `selected`
						FROM `' . _DB_PREFIX_ . 'registration_group`
						WHERE `id_reg_group` = ' . $id . ';'
        );
    }

}