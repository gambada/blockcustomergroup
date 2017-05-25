# ![alt text](logo.png) Block Customer Group Module - Prestashop
Add a block in the registration form where the customer can select his group.
The admin can choose which group the customer can select, from module configuration page. This module doesn't modify default database tables, just inform the merchant (with an e-mail) that a new customer has registered in his shop.

## Requirements
Prestashop 1.6.x recommended

## Installation

#### Method 1
1. compress the `blockcustomergroup` folder in zip or tarball format
2. go in the admin form of your site
3. select `modules`
4. search for `Block Customer Group`
5. click the `Add` icon in top right corner
6. select the compressed module
7. upload and install it!

#### Method 2
1. rename the folder in `blockcustomergroup`
2. add it in the folder `your-site\prestashop\modules`
3. go in the admin form of your site
4. select `modules`
5. search for `Block Customer Group`
6. install it!

## Guide
#### Module
Once you added all groups in Prestashop: 
 1. go to the module `Configuration`
 2. select group(s)
 3. select default group
 4. choose if in the registration form can be seen the discount percentage
 5. select the type of visualization (select / radio)

#### Prestashop

If the module doesn't send the e-mail:
 1. Module folder `blockcustomergroup`
 2. mails
 3. create your country folder (example: Italy -> "it" folder)
 4. insert your translated mail template 

---
to modify the registration form, from `ACCOUNT ONLY` to `ACCOUNT + ADDRESS`:
 1. `PREFERENCES`
 2. `Customers`
 3. `Registration process type` change in `Standard`
 
---

to add the DNI field in the registration form:
 1. `LOCALIZATION`  
 2. `Countries`
 3. select and edit your country
 4. enable `Do you need a tax identification number?`

## Known issues
If you have [ValidateCustomer](https://www.prestashop.com/forums/topic/219050-free-module-validate-customer) module, you need to install BlockCostumerGroup before that, otherwise it will not send e-mails!

## Contributing
1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request!

## Changelog
1.0.0

## Credits
Daniele Gambaletta

## Clarifications
- This is my first prestashop module
- English isn’t my first language, so please excuse any mistakes.

## License
This module is available under the MIT license. See the [LICENSE](LICENSE.md) file for more info.
