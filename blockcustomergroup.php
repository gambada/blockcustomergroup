<?php

/*	Block Customer Group
*	Add a block in the registration form where the customer can select his group.
*
*	@author			Daniele Gambaletta
*	@version		1.0.0
*	@PS version		1.6 recommended
*	@license   		MIT
*/

if (!defined('_PS_VERSION_'))
    exit;

include_once(dirname(__FILE__) . '/classes/BlockCG_SQL.php');

class BlockCustomerGroup extends Module
{
    const __MY_MAIL_DELIMITER__ = ',';

    public function __construct()
    {
        $this->name = 'blockcustomergroup';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Daniele Gambaletta';

        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Block Customer Group');
        $this->description = $this->l('Add a block in the registration form where the customer can select his group.');
    }


    // Install hooks and table
    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        Configuration::updateValue('INPUT_TYPE', 1);
        Configuration::updateValue('SHOW_REDUCTION', 0);
        Configuration::updateValue('DEFAULT', null);
        Configuration::updateValue('EMAIL_LIST', strval(Configuration::get('PS_SHOP_EMAIL')));

        return (
            parent::install() &&
            BlockCG_SQL::installTable() &&
            BlockCG_SQL::insertRows() &&
            $this->registerHook('createAccountForm') &&
            $this->registerHook('actionCustomerAccountAdd')
        );
    }


    // Uninstall hooks, cache and table
    public function uninstall()
    {
        $this->_clearCache('blockcustomergroup.tpl');

        return (
            parent::uninstall() &&
            Configuration::deleteByName('blockcustomergroup') &&
            Configuration::deleteByName('SHOW_REDUCTION') &&
            Configuration::deleteByName('DEFAULT') &&
            Configuration::deleteByName('INPUT_TYPE') &&
            Configuration::deleteByName('EMAIL_LIST') &&
            BlockCG_SQL::dropTable()
        );
    }


    // Update table with checkbox value
    private function _updateData()
    {
        $types = BlockCG_SQL::getTypes();

        $default = Tools::getValue('DEFAULT');
        $groups = Tools::getValue('GROUPS');

        Configuration::updateValue('INPUT_TYPE', Tools::getValue('INPUT_TYPE'));
        Configuration::updateValue('SHOW_REDUCTION', Tools::getValue('SHOW_REDUCTION'));

        foreach ($types as $key => $type) {
            if ($groups && in_array((int)$type['id_reg_group'], $groups) !== false) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'registration_group` SET `selected` = 1 WHERE `id_reg_group` = ' . (int)$type['id_reg_group'] . ';'
                );
            } else {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'registration_group` SET `selected` = 0 WHERE `id_reg_group` = ' . (int)$type['id_reg_group'] . ';'
                );
            }
        }

        if ($groups && !$default) {
            Configuration::updateValue('DEFAULT', array_values($groups)[0]);
        } elseif ($groups && $default) {
            if (!in_array($default, $groups)) {
                Configuration::updateValue('DEFAULT', array_values($groups)[0]);
            } else {
                Configuration::updateValue('DEFAULT', Tools::getValue('DEFAULT'));
            }
        } elseif (!$groups && $default) {
            Configuration::updateValue('DEFAULT', null);
        } else {
            Configuration::updateValue('DEFAULT', Tools::getValue('DEFAULT'));
        }

        ppp(Configuration::get('DEFAULT'));

        if (!empty(Tools::getValue('EMAIL_LIST'))) {

            $emails = explode(self::__MY_MAIL_DELIMITER__, Tools::getValue('EMAIL_LIST'));

            if (!($emails = self::_checkEmail($emails))) {
                return $this->displayError($this->l('Email(s) written incorrectly, not saved!'));
            }
            $emails = implode(",\n", $emails);

            Configuration::updateValue('EMAIL_LIST', $emails);

        } else {
            Configuration::updateValue('EMAIL_LIST', Configuration::get('PS_SHOP_EMAIL'));
        }

        return $this->displayConfirmation($this->l('Configurations set correctly'));
    }


    private function _checkEmail($emails)
    {
        foreach ($emails as &$email) {
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return $emails;
    }


    // Get selected groups
    private function _getData()
    {
        $types = BlockCG_SQL::getTypes();

        foreach ($types as $key => $type) {
            $this->fields_value[(int)$type['id_reg_group']] = BlockCG_SQL::getSelected((int)$type['id_reg_group']);
        }

        $this->fields_value['DEFAULT'] = Configuration::get('DEFAULT');
        $this->fields_value['SHOW_REDUCTION'] = Configuration::get('SHOW_REDUCTION');
        $this->fields_value['INPUT_TYPE'] = Configuration::get('INPUT_TYPE');
        $this->fields_value['EMAIL_LIST'] = Configuration::get('EMAIL_LIST');
    }


    public function getContent()
    {
        $output = '';
        BlockCG_SQL::updateTable();

        if (Tools::isSubmit('submit' . $this->name)) {
            $output = $this->_updateData();
        }

        $this->_clearCache('blockcustomergroup.tpl');

        return $output . $this->renderForm();
    }


    public function renderForm()
    {
        $groups = BlockCG_SQL::getGroups();
        $default = BlockCG_SQL::getSelectedGroups();

        foreach ($groups as $key => $group)
            $groups[$key]['reduction'] = $group['reduction'] . '%';

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Registration Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(

                array(
                    'type' => 'table_group_list',
                    'name' => 'GROUPS',
                    'label' => $this->l('Select group(s):'),
                    'hint' => $this->l('Select group(s) you want to be seen in the registration form'),
                    'desc' => $this->l('If nothing selected the block will be not displayed'),
                    'required' => false,
                    'class' => 't',
                    'values' => $groups,
                ),

                array(
                    'type' => 'select',
                    'name' => 'DEFAULT',
                    'label' => $this->l('Select default group:'),
                    'hint' => $this->l('Select which group you want see as first in the registration form'),
                    'desc' => $this->l('That you see is the selected one'),
                    'required' => false,
                    'class' => 't',
                    'options' => array(
                        'query' => $default,
                        'id' => 'id_reg_group',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'name' => 'SHOW_REDUCTION',
                    'label' => $this->l('Reduction visible:'),
                    'hint' => $this->l('Chose if customer can see reduction percentage of group(s)'),
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                        ),
                    ),
                ),

                array(
                    'type' => 'switch_input_type',
                    'name' => 'INPUT_TYPE',
                    'label' => $this->l('Select Type:'),
                    'hint' => $this->l('Chose selection type in the registration form'),
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('SELECT'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('RADIO'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ),
        );


        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Mailing'),
                'icon' => 'icon-envelope'
            ),
            'input' => array(


                array(
                    'type' => 'textarea',
                    'name' => 'EMAIL_LIST',
                    'label' => $this->l('Send to these email addresses:'),
                    'hint' => $this->l('Write every email you want to be advised about new customer registration'),
                    'desc' => $this->l('One email address per line, separated by comma ( , ). If void, the email will be send to default shop mail'),
                    'required' => false,
                    'class' => 't',
                    'values' => $groups,
                ),

            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ),
        );


        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->submit_action = 'submit' . $this->name;

        // Load current value
        $this->_getData();
        $helper->fields_value = $this->fields_value;

        return $helper->generateForm($fields_form);
    }


    private function _moveToTop(&$array, $key)
    {
        $temp = array($key => $array[$key]);
        unset($array[$key]);
        $array = $temp + $array;
    }


    private function _getKeySubArray($array, $value)
    {
        foreach ($array as $key => $elem) {
            if (in_array($value, $elem)) {
                return $key;
            }
        }
    }


    public function hookCreateAccountForm($params)
    {
        if (!$this->active)
            return false;

        if (!BlockCG_SQL::checkSelectedGroups())
            return false;

        $groups = BlockCG_SQL::getSelectedGroups();
        $default = Configuration::get('DEFAULT');

        $this->_moveToTop($groups, $this->_getKeySubArray($groups, $default));

        if (!$this->isCached('blockcustomergroup.tpl', $this->getCacheId())) {
            $this->context->smarty->assign('groups', $groups);
            $this->context->smarty->assign('module_message', $this->l('Select your group.'));
            $this->context->smarty->assign('show_reduction', Configuration::get('SHOW_REDUCTION'));
            $this->context->smarty->assign('type', Configuration::get('INPUT_TYPE'));

        }

        return $this->display(__FILE__, 'blockcustomergroup.tpl', $this->getCacheId());
    }


    public function hookActionCustomerAccountAdd($params)
    {

        if (!$this->active)
            return false;

        $postVars = $params['_POST'];

        if (empty($postVars))
            return false;


        if ((int)Configuration::get('PS_REGISTRATION_PROCESS_INPUT_TYPE') == 0) {
            // ACCOUNT

            // Mail template
            $email_template = 'new_reg';

            // Filling-in vars for email
            $email_template_vars = array(
                '{shopname}' => $this->context->shop->name, // in use
                '{firstname}' => $postVars['firstname'], // in use
                '{lastname}' => $postVars['lastname'], // in use
                '{email}' => $postVars['email'], // in use
                '{newsletter}' => ($postVars['newsletter'] == 1 ? $this->l('Yes') : $this->l('No')),
                '{birthday}' => $postVars['months'] . '/' . $postVars['days'] . '/' . $postVars['years'],
                '{dni}' => $postVars['dni'], // in use
                // Get group value
                '{customer_group}' => $postVars['customer_group']  // in use

            );
        } else {
            // ACCOUNT + ADDRESS

            // Mail template
            $email_template = 'new_reg_advanced';

            // Filling-in vars for email
            $email_template_vars = array(
                '{shopname}' => $this->context->shop->name, // in use
                '{firstname}' => $postVars['firstname'], // in use
                '{lastname}' => $postVars['lastname'], // in use
                '{email}' => $postVars['email'], // in use
                '{newsletter}' => ($postVars['newsletter'] == 1 ? $this->l('Yes') : $this->l('No')),
                '{birthday}' => $postVars['months'] . '/' . $postVars['days'] . '/' . $postVars['years'],
                '{dni}' => $postVars['dni'], // in use
                '{address1}' => $postVars['address1'], // in use
                '{address2}' => $postVars['address2'],
                '{postcode}' => $postVars['postcode'],
                '{city}' => $postVars['city'],
                '{country}' => Country::getNameById(intval(Context::getContext()->cookie->id_lang), intval($postVars['id_country'])),
                '{state}' => State::getNameById(intval($postVars['id_state'])),
                '{phone}' => $postVars['phone'],
                '{phone_mobile}' => $postVars['phone_mobile'],
                '{company}' => $postVars['company'], // in use
                '{vat_number}' => $postVars['vat_number'], // in use
                '{other}' => $postVars['other'],
                // Get group value
                '{customer_group}' => $postVars['customer_group']  // in use
            );
        }

        $email_obj = $this->l('NEW REGISTRATION');

        $emails = explode(',', Configuration::get('EMAIL_LIST'));

        foreach ($emails as $single_email) {
            Mail::Send(
                Configuration::get('PS_LANG_DEFAULT'),
                $email_template,    // Mail template name
                Mail::l(
                    $email_obj,    // Mail object
                    Configuration::get('PS_LANG_DEFAULT')
                ),
                $email_template_vars, // Mail template vars
                $single_email,
                $this->context->shop->name,
                NULL,
                $this->context->shop->name,
                NULL,
                NULL,
                dirname(__FILE__) . '/mails/'
            );
        }
    }
}
