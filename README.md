# Ohs Core

Hubleto app with the shared core of the Ohs apps (occupational health service, Slovakia):
- code lists: hazard catalogue with exam intervals per category, clinics with their medical staff, cars;
- the five roles (administrator, manager, hygienist, nurse, medical staff) and what each may see and change;
- shared services of the other Ohs apps (access scope, record write guard, translations, protected files, demo data).

Namespace: `Hubleto\App\External\WaiBlue\OhsCore`. The other Ohs apps need this one.

## Install into a Hubleto project

```bash
composer config repositories.wai-blue/hubleto-ohs-core vcs <repository-url>
composer require wai-blue/hubleto-ohs-core:dev-main
php hubleto app install "Hubleto\App\External\WaiBlue\OhsCore"
npm run build
```

Fake demo data: `generateDemoData: true` in the init yaml (demo users are listed in `install/README.md`).
