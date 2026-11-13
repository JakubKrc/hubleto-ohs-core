# Installing the Ohs CRM (Hubleto 2)

Needs PHP 8.2 or newer, composer, node and npm, and a MySQL or MariaDB database. The Ohs repositories are private: composer needs a GitHub account with access to them (SSH key or token).
Until `wai-blue/hubleto-ohs-hazards` and `wai-blue/hubleto-ohs-clinics` exist, these two apps come from the public repos
`JakubKrc/hubleto-ohs-hazards` and `JakubKrc/hubleto-ohs-clinics` (the yaml already points there; use them in the loops below too).

Everything Ohs needs is in one file, `ohs.example.yaml` in this folder: the Ohs packages for composer,
and the apps and settings for `php hubleto init`.

1. Create a Hubleto project:

   ```bash
   composer create-project hubleto/install my-project --stability=dev
   cd my-project
   ```

2. Put `ohs.example.yaml` (from this folder on GitHub, or from a clone of this repository) into the project root
   as `ohs.yaml`, and fill in the URL, database and admin values.

3. Add the Ohs packages and install:

   ```bash
   php hubleto prepare-composer ohs.yaml   # adds the 10 Ohs packages to composer.json
   composer update
   php hubleto init ohs.yaml               # drops and recreates the database named in the yaml
   ```

   Run `prepare-composer` only once per project: a second run doubles the entries and breaks `composer.json`.

4. Build the frontend:

   ```bash
   npm install && npm run build
   ```

5. Point the web server to the project folder (the template's `.htaccess` sends every route to `index.php`).
   Under Apache, the web server user needs write access to `log`, `upload` and `secure`.

6. Optional: hide the Hubleto apps you do not need (for example Tasks, Worksheets) in Settings > Sidebar (order 0).

7. Optional, FAKE demo data: set `generateDemoData: true` in the yaml before `php hubleto init`.
   Besides Hubleto's own demo data, it adds demo clients with branches, employees, exams, audits, documents
   and events, and one demo user per Ohs role. As with Hubleto's demo users, the password is the login name:

   | Role | Login | Password |
   |---|---|---|
   | Hygienist | `hygienik1@example.com`, `hygienik2@example.com` | `hygienik1`, `hygienik2` |
   | Nurse | `sestra@example.com` | `sestra` |
   | Medical staff | `lekar@example.com` | `lekar` |
   | Manager | `manazer@example.com` | `manazer` |

   The passwords are public: on a server reachable from the internet, change them after install.

Updates later: `composer update "wai-blue/*"`, then `npm run build`.

## Developing the apps

As in the developer guide ("External apps"): clone the apps into any folder and let composer link them into `vendor/`,
so changes show at once. Before step 3, change each repository in the yaml's `composer:` part from `vcs` to `path`:

```bash
mkdir -p /var/www/html/ohs-apps && cd /var/www/html/ohs-apps
for app in core hazards clinics clients branches employees medical-exams audits documents events; do
  git clone git@github.com:wai-blue/hubleto-ohs-$app.git
done
```

```yaml
composer:
  repositories:
    wai-blue/hubleto-ohs-core: { type: path, url: /var/www/html/ohs-apps/hubleto-ohs-core }
    # ... the same for the other 9
```

Commit and push in the cloned folders. Files copied into `src/apps` do not work: that folder is for custom apps,
and Hubleto finds external apps only as composer packages.

## Without prepare-composer

The same as step 3, by hand:

```bash
for app in core hazards clinics clients branches employees medical-exams audits documents events; do
  composer config repositories.wai-blue/hubleto-ohs-$app \
    "{\"type\": \"vcs\", \"url\": \"https://github.com/wai-blue/hubleto-ohs-$app\", \"no-api\": true}"
done
composer require wai-blue/hubleto-ohs-core:dev-main wai-blue/hubleto-ohs-hazards:dev-main \
  wai-blue/hubleto-ohs-clinics:dev-main wai-blue/hubleto-ohs-clients:dev-main \
  wai-blue/hubleto-ohs-branches:dev-main wai-blue/hubleto-ohs-employees:dev-main \
  wai-blue/hubleto-ohs-medical-exams:dev-main wai-blue/hubleto-ohs-audits:dev-main \
  wai-blue/hubleto-ohs-documents:dev-main wai-blue/hubleto-ohs-events:dev-main
php hubleto init ohs.yaml
```

`no-api` makes composer clone with plain git and your git credentials, without GitHub API calls, so it avoids the GitHub API rate limit.
