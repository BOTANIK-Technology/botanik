# Botanik 1.1

## Stages of deploy for development:

1. Run the `composer install` command
2. Create a database with the **"botanik"** name
3. Create **.env** file in the root directory of the project
4. Copy all rules form the **.env.example** file and paste to the **.env**
5. Update the `DB_DATABASE=botanik`, `DB_USERNAME={username}`, `DB_PASSWORD={password}` fields in the **.env**
6. Replace the default mailer rules from **.env** with rules from **Mailer** README section  
7. Generate APP secret key by `php artisan key:generate`
8. Run the migrations by `php artisan migrate --path=/database/migrations/root`

###### Login data:

* Url: _https://{domain}/a-level/login_
* Login: _root-ukrlogika@gess.com_
* Password: _v2kLZ1CEL7aYceXAXh_

###### IDE helper:

1. `php artisan ide-helper:generate`
2. `php artisan ide-helper:meta`
3. `php artisan ide-helper:models`
4. `php artisan ide-helper:models "App\Models\User"`

###### Mailer:

* `MAIL_MAILER=smtp`
* `MAIL_HOST=smtp.gmail.com`
* `MAIL_PORT=587`
* `MAIL_USERNAME=botanik.devs@gmail.com`
* `MAIL_PASSWORD=Gfb3u!11kfb!7^bf|_/f3f3yb`
* `MAIL_ENCRYPTION=tls`
* `MAIL_FROM_ADDRESS=botanik.devs@gmail.com`
* `MAIL_FROM_NAME="${APP_NAME}"`

###### File system:
* `FILESYSTEM_DRIVER=public`
