# Hubleto Ohs Core

External Hubleto app with the shared core of the Ohs apps for an occupational health service. The other Ohs apps need it.

## Brief overview

Keeps the cars code list of an occupational health service (the hazard catalogue and the clinics are the apps Ohs Hazards
and Ohs Clinics). Defines the five roles (administrator, manager, hygienist, nurse, medical staff) and the apps each role
gets. Also has the shared services of the other Ohs apps: translations, protected files, the home page and dashboard,
demo data and the install config of the whole Ohs set.

Namespace `Hubleto\App\External\WaiBlue\OhsCore`, URL slug `ohs/core`. Requires: Auth, Settings, Customers.

## Installation

  1. `composer require wai-blue/hubleto-ohs-core`
  2. `php hubleto app install Hubleto\App\External\WaiBlue\OhsCore`
  3. `npm install && npm run build`

To install the whole Ohs set into a new Hubleto project, follow `install/README.md`.

## What is where

| Area | Where |
|---|---|
| Cars | `ohs/core/cars` |
| Roles and the apps of each role | `Services/Roles.php`, `Services/RoleApps.php` |
| Home page, dashboard and search | `HubletoOverrides/` |
| Protected file download | `Services/ProtectedFile.php`, `Components/FC/ProtectedFileInput.tsx` |
| Demo data | `Services/DemoData.php` (`generateDemoData: true` in the init yaml) |
| Install config of the Ohs set | `install/` |
| Settings | `ohs/core/settings` |
